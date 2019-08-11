<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://weebedia.com
 * @since             1.0.0
 * @package           Trm_Wp_Affiliation
 *
 * @wordpress-plugin
 * Plugin Name:       TRM WP Affiliation
 * Plugin URI:        https://github.com/Simerca/trm-wp-affiliation
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Ayrton LECOUTRE
 * Author URI:        https://weebedia.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       trm-wp-affiliation
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TRM_WP_AFFILIATION_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-trm-wp-affiliation-activator.php
 */
function activate_trm_wp_affiliation() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-trm-wp-affiliation-activator.php';
	Trm_Wp_Affiliation_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-trm-wp-affiliation-deactivator.php
 */
function deactivate_trm_wp_affiliation() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-trm-wp-affiliation-deactivator.php';
	Trm_Wp_Affiliation_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_trm_wp_affiliation' );
register_deactivation_hook( __FILE__, 'deactivate_trm_wp_affiliation' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-trm-wp-affiliation.php';
require plugin_dir_path( __FILE__ ) . 'public/trm-front.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_trm_wp_affiliation() {

	$plugin = new Trm_Wp_Affiliation();
	$plugin->run();

}
run_trm_wp_affiliation();

session_start();
if(isset($_GET['affiliate'])){

	$_SESSION['affiliate'] = $_GET['affiliate'];
	// var_dump($_SESSION['affiliate']);

}

if(isset($_SESSION['affiliate'])){

	// var_dump($_SESSION['affiliate']);
}

add_action( 'woocommerce_after_add_to_cart_button', 'add_content_after_addtocart_button_func' );
function add_content_after_addtocart_button_func(){

	if( is_user_logged_in() ) {
		$user = wp_get_current_user();
		$roles = ( array ) $user->roles;
		if(in_array('partenaires', $roles) || in_array('administrator', $roles)){
			include plugin_dir_path( __FILE__ ) . 'views/front/button-affiliation.php';
		}
	}

}

add_action( 'woocommerce_after_checkout_billing_form', 'display_extra_fields_after_billing_address' , 10, 1 );
function display_extra_fields_after_billing_address () {
	//if(isset($_SESSION['affiliate'])){
		$output = '<br>';
		$output .= '<input type="hidden" name="affiliate" class="add_delivery_date" value="'.$_SESSION['affiliate'].'">';
		echo $output;
	//}
}


// add_action('init', 'debugMyPlugin');

function debugMyPlugin(){

	$args = array(
		'post_type' => 'campagne_affiliation',
		'meta_query' => array(
			'relation'=>'AND',
			array(
				'key' => 'cashback',
				'value' => 71,
				'compare' => 'LIKE',
			),
			array(
				'key'=>'date_start',
				'value'=>date('Y-m-d',time()),
				'compare'=>'<=',
				'type'=>'DATE'
			),
			array(
				'key'=>'date_end',
				'value'=>date('Y-m-d',time()),
				'compare'=>'>=',
				'type'=>'DATE'
			)
		)
	);
	$campagnes = get_posts($args);

	// echo "<pre>";
	// var_dump($campagnes);
	// echo "</pre>";

}

add_action( 'woocommerce_checkout_update_order_meta', 'add_order_delivery_date_to_order' , 10, 1);
function add_order_delivery_date_to_order ( $order_id ) {
	if ( isset( $_POST ['affiliate'] ) &&  '' != $_POST ['affiliate'] ) {
		add_post_meta( $order_id, '_affiliate',  sanitize_text_field( $_POST ['affiliate'] ) );

		$current_user = get_current_user_id();
		if($current_user == 0){
			$user_name = $_POST['billing']['email'];
			$user_email = $_POST['billing']['email'];
			$user_id = username_exists( $user_name );
			if ( !$user_id and email_exists($user_email) == false ) {
				$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
				$user_id = wp_create_user( $user_name, $random_password, $user_email );
				$current_user = $user_id;
			} else {
				$random_password = __('User already exists.  Password inherited.');
				$billinguser = get_userdata($_POST['billing']['email']);
				$current_user = $billinguser->ID;
			}
		}

		$affiliateur = get_userdata($_POST['affiliate']);

		//Obtain cashback for Products 
		$order = wc_get_order($order_id);
		$totalCashback = array();
		$totalAffiliation = 0;
		$campagnesAffiliation = array();
		foreach ($order->get_items() as $item_key => $item ){

			$item_id = $item->get_id();

			$args = array(
				'post_type' => 'cashback',
				'meta_query' => array(
					array(
					'key' => 'produit',
					'value' => $item_id,
					'compare' => 'LIKE',
					)
				)
			);
			$cashbacks = get_posts($args);

			if(count($cashbacks) == 0){

				$terms = get_terms(  'product_cat', 80 );

				$formatedTerms = array();

				$metaQuerys = array();
				foreach($terms as $term){

					$formatedTerms[] = $term->term_taxonomy_id;

					$metaQuery = array(
						'key'=>'groupe_de_produit',
						'value'=> intval( $term->term_taxonomy_id ) ,
						'compare'=>'LIKE'
					);

					$metaQuerys = $metaQuery;

				}

				$args = array(
					'post_type' => 'cashback',
					'meta_query' => array(
						'relation' => 'OR',
						$metaQuerys
					)
				);
				$cashbacks = get_posts($args);
				if(!is_admin()){

					echo "<pre>";
					var_dump($cashbacks);
					echo "</pre>";
				}

			}

			if(count($cashbacks) != 0){

				foreach($cashbacks as $cashback){
					
					$meta = get_post_meta($cashback->ID, '', true);
					$amountCashback = 0;
					if(isset( $meta['cashback'][0])){
						$totalCashback[] = $meta['cashback'][0];
						$amountCashback = $meta['cashback'][0];
					}

					//Total affiliation

					$line_subtotal  = $item->get_subtotal(); 

						$ammount = ($line_subtotal * $amountCashback) / 100;

						$totalAffiliation += $ammount;

					$args = array(
						'post_type' => 'campagne_affiliation',
						'meta_query' => array(
							'relation'=>'AND',
							array(
								'key' => 'cashback',
								'value' => $cashback->ID,
								'compare' => 'LIKE',
							),
							array(
								'key'=>'date_start',
								'value'=>date('Y-m-d',time()),
								'compare'=>'<=',
								'type'=>'DATE'
							),
							array(
								'key'=>'date_end',
								'value'=>date('Y-m-d',time()),
								'compare'=>'>=',
								'type'=>'DATE'
							)
						)
					);
					$campagnes = get_posts($args);
					
					if($campagnes){
						
						foreach($campagnes as $campagne){
							$campagnesAffiliation[] = $campagne->ID;
						}

					}

				}
				
			}

		}
		if(count($campagnesAffiliation) != 0){
			// Create post object
			$my_post = array(
				'post_title'    => wp_strip_all_tags( date('d-m-Y H:i').' #'.$order_id ),
				'post_content'  => '',
				'post_status'   => 'publish',
				'post_author'   => 1,
				'post_type' => 'commande_affilie',
				'meta_input'=>array(
					'utilisateur'=>$current_user,
					'campagne_daffiliation'=>$campagnesAffiliation,
					'partenaire'=>$affiliateur->ID,
					'commande'=>$order_id,
					'montant_affiliation'=>$totalAffiliation
				)
			);
			
			// Insert the post into the database
			wp_insert_post( $my_post );
		}
	}
}

add_action( 'woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );
function my_custom_checkout_field_display_admin_order_meta( $order ){
	$order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
	$userdata = get_userdata( get_post_meta( $order_id, '_affiliate', true ));
	if($userdata){
		echo '<p><strong>'.__('Utilisateur affilié').':</strong> ' . $userdata->display_name . '</p>';
	}
}