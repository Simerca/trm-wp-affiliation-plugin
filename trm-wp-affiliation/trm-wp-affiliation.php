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

if(isset($_GET['wishlist'])){

	$id = $_GET['wishlist'];
	// addToWishList($id);

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
			include plugin_dir_path( __FILE__ ) . 'views/front/button-wishlist.php';
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

function addToWishList(){

	if(isset($_GET['m_wishlist'])){

	$id = $_GET['m_wishlist'];

	$args = array(
		'post_type'=>'wishlist',
		'meta_query'=>array(
			array(
				'key'=>'utilisateur_wishlist',
				'value'=>get_current_user_id()
			)
		)
	);

	$wishlists = get_posts($args);
	
	if($wishlists){

		foreach($wishlists as $wishlist){
			

			$fields = get_fields($wishlist->ID);
			

			// echo "<pre>";
			// var_dump($wishlist->ID);
			// var_dump($fields);
			// echo "</pre>";

			$itemExist = false;
			if(isset($fields['produits']) && is_array($fields['produits'])){
			foreach($fields['produits'] as $product){

				// echo "<pre>";
				// var_dump($product);
				// var_dump($id);
				// echo "</pre>";
				if($id == $product){

					$itemExist = true;

				}

			}
			}	
			if(!$itemExist){
				
				// echo "<pre>";
				// var_dump($itemExist);
				add_row('produits', $id, $wishlist->ID);
				// echo "</pre>";

			}

		}

	}else{

		$my_post = array(
			'post_title'    => wp_strip_all_tags( date('d-m-Y H:i'). '#'. get_current_user_id() ) ,
			'post_content'  => '',
			'post_status'   => 'publish',
			'post_author'   => 1,
			'post_type' => 'wishlist'
		);
		
		// Insert the post into the database
		$post = wp_insert_post( $my_post );

		update_field('produits', $id, $post);
		update_field('utilisateur_wishlist', get_current_user_id(), $post);


	}

	}

}

function delToWishList(){

	if(isset($_GET['delwishlist'])){

	$id = $_GET['delwishlist'];

	$args = array(
		'post_type'=>'wishlist',
		'meta_query'=>array(
			array(
				'key'=>'utilisateur_wishlist',
				'value'=>get_current_user_id()
			)
		)
	);

	$wishlists = get_posts($args);

	foreach($wishlists as $wishlist){
		

		$fields = get_fields($wishlist->ID);

		// echo "<pre>";
		// var_dump($wishlist->ID);
		// var_dump($fields);
		// echo "</pre>";

		$itemExist = false;
		foreach($fields as $product){


			if($id == $product[0]){

				$itemExist = true;
				// echo "<pre>";
				// var_dump($itemExist);
				// var_dump($id);
				// var_dump($wishlist->ID);
				// var_dump(delete_row('produits', 80, 178));
				// echo "</pre>";

			}

		}

	}

	}

}

add_action('init', 'addToWishList');
add_action('init', 'delToWishList');
add_action('init', 'debugMyPlugin');

function debugMyPlugin(){

	if(isset($_GET['debug'])){
	
		//Start Debug

		// Si Utilisateur Affilié
		$_POST ['affiliate'] = '1';
	if ( isset( $_POST ['affiliate'] ) &&  '' != $_POST ['affiliate'] ) {

		add_post_meta( $order_id, '_affiliate',  sanitize_text_field( $_POST ['affiliate'] ) );

		// Recuperation de l'utilisateur qui commande
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

		// Definition du partenaire
		$affiliateur = get_userdata($_POST['affiliate']);

		//Obtain cashback for Products 
		$order = wc_get_order(304932);
		$totalCashback = array();
		$totalAffiliation = 0;
		$campagnesAffiliation = array();

		// Recuperation des items de la commande.
		foreach ($order->get_items() as $item_key => $item ){

			$item_id = $item->get_product_id();

			echo "<pre>";
			var_dump($item_id);
			echo "</pre>";

			$terms = get_the_terms( $item_id, 'product_cat'  );

			$brands = get_the_terms( $item_id, 'product_brand' );

			//Args Produit
			$args = array(
				'post_type' => 'cashback',
				'orderby'          => 'ID',
				'order'            => 'DESC',
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

				$itemBrand = false;
				foreach($brands as $brand){
					
					$itemBrand = $brand->term_id;
				}
				
				//Args Brands
				$args = array(
					'post_type' => 'cashback',
					'orderby'          => 'ID',
					'order'            => 'DESC',
					'meta_query' => array(
						array(
						'key' => 'marque',
						'value' => $itemBrand,
						'compare' => 'LIKE',
						)
					)
				);
				$cashbacks = get_posts($args);

				

			}

			if(count($cashbacks) == 0){

				$itemCat = false;
				foreach($terms as $term){
					$itemCat = $term->term_id;
				}
				
				//Args Categorie
				$args = array(
					'post_type' => 'cashback',
					'orderby'          => 'ID',
					'order'            => 'DESC',
					'meta_query' => array(
						array(
						'key' => 'groupe_de_produit',
						'value' => $itemCat,
						'compare' => 'LIKE',
						)
					)
				);
				$cashbacks = get_posts($args);


			}

			// Main Query

			if($cashbacks){

				foreach($cashbacks as $cashback){

					$meta = get_post_meta($cashback->ID, '', true);
					$amountCashback = 0;
					if(isset( $meta['cashback'][0])){
						$totalCashback[] = $meta['cashback'][0];
						$amountCashback = $meta['cashback'][0];
					}

					//Total affiliation
					$line_subtotal  = $item->get_subtotal(); 
						$ammount = round(($line_subtotal * $amountCashback) / 100,2);

					$args = array(
						'post_type' => 'campagne_affiliation',
						'orderby'          => 'ID',
						'order'            => 'DESC',
						'meta_query' => array(
							array(
								'key' => 'cashback',
								'value' => $cashback->ID,
								'compare' => 'LIKE',
							)
						)
					);
					$campagnes = get_posts($args);

					if($campagnes){
						
						foreach($campagnes as $campagne){
							$meta = get_fields($campagne->ID, '', true);
							echo "<pre>";
							var_dump($meta);
							echo "</pre>";
							if($meta['utilisateur_affilie'] == "" || in_array($affiliateur->ID, $meta['utilisateur_affilie'])){	
								$campagnesAffiliation[] = $campagne->ID;
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
										'montant_affiliation'=>$ammount
									)
								);
								
								// Insert the post into the database
								wp_insert_post( $my_post );
								break;
							}
						}

					}
			
				}

			}

			// Main Query

		}

	}

		//Start Debug
	
	}
}

add_action( 'woocommerce_checkout_update_order_meta', 'add_order_delivery_date_to_order' , 10, 1);
function add_order_delivery_date_to_order ( $order_id ) {

	// Si Utilisateur Affilié
	if ( isset( $_POST ['affiliate'] ) &&  '' != $_POST ['affiliate'] ) {

		add_post_meta( $order_id, '_affiliate',  sanitize_text_field( $_POST ['affiliate'] ) );

		// Recuperation de l'utilisateur qui commande
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

		// Definition du partenaire
		$affiliateur = get_userdata($_POST['affiliate']);

		//Obtain cashback for Products 
		$order = wc_get_order($order_id);
		$totalCashback = array();
		$totalAffiliation = 0;
		$campagnesAffiliation = array();

		// Recuperation des items de la commande.
		foreach ($order->get_items() as $item_key => $item ){

			$item_id = $item->get_product_id();

			echo "<pre>";
			var_dump($item_id);
			echo "</pre>";

			$terms = get_the_terms( $item_id, 'product_cat'  );

			$brands = get_the_terms( $item_id, 'product_brand' );

			//Args Produit
			$args = array(
				'post_type' => 'cashback',
				'orderby'          => 'ID',
				'order'            => 'DESC',
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

				$itemBrand = false;
				foreach($brands as $brand){
					
					$itemBrand = $brand->term_id;
				}
				
				//Args Brands
				$args = array(
					'post_type' => 'cashback',
					'orderby'          => 'ID',
					'order'            => 'DESC',
					'meta_query' => array(
						array(
						'key' => 'marque',
						'value' => $itemBrand,
						'compare' => 'LIKE',
						)
					)
				);
				$cashbacks = get_posts($args);

				

			}

			if(count($cashbacks) == 0){

				$itemCat = false;
				foreach($terms as $term){
					$itemCat = $term->term_id;
				}
				
				//Args Categorie
				$args = array(
					'post_type' => 'cashback',
					'orderby'          => 'ID',
					'order'            => 'DESC',
					'meta_query' => array(
						array(
						'key' => 'groupe_de_produit',
						'value' => $itemCat,
						'compare' => 'LIKE',
						)
					)
				);
				$cashbacks = get_posts($args);


			}

			// Main Query

			if($cashbacks){

				foreach($cashbacks as $cashback){

					$meta = get_post_meta($cashback->ID, '', true);
					$amountCashback = 0;
					if(isset( $meta['cashback'][0])){
						$totalCashback[] = $meta['cashback'][0];
						$amountCashback = $meta['cashback'][0];
					}

					//Total affiliation
					$line_subtotal  = $item->get_subtotal(); 
						$ammount = round(($line_subtotal * $amountCashback) / 100,2);

					$args = array(
						'post_type' => 'campagne_affiliation',
						'orderby'          => 'ID',
						'order'            => 'DESC',
						'meta_query' => array(
							array(
								'key' => 'cashback',
								'value' => $cashback->ID,
								'compare' => 'LIKE',
							)
						)
					);
					$campagnes = get_posts($args);

					if($campagnes){
						
						foreach($campagnes as $campagne){
							$meta = get_fields($campagne->ID, '', true);
							if($meta['utilisateur_affilie'] == 0 || in_array($affiliateur->ID, $meta['utilisateur_affilie'])){	
								$campagnesAffiliation[] = $campagne->ID;
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
										'montant_affiliation'=>$ammount
									)
								);
								
								// Insert the post into the database
								wp_insert_post( $my_post );
								break;
							}
						}

					}
			
				}

			}

			// Main Query

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