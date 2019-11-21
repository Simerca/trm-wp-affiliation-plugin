<?php

function loadDashboardFront(){
    
    if(!is_admin()){

        ob_start();
        // Chargement du front page utilisateur
        include(plugin_dir_path( dirname( __FILE__ ) ) . '/views/front/dashboard.php');

        $content = ob_get_clean();

        return $content;
    }
    
}
add_shortcode('trm_front_dashboard','loadDashboardFront');

function loadWishList(){
    
    if(!is_admin()){

        ob_start();
        // Chargement du front page utilisateur
        include(plugin_dir_path( dirname( __FILE__ ) ) . '/views/front/wishlist.php');

        $content = ob_get_clean();
        return $content;
    }
    
}
add_shortcode('trm_wishlist_affiliation','loadWishList');