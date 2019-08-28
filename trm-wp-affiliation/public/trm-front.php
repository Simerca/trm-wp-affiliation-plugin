<?php

function loadDashboardFront(){
    
    if(!is_admin()){
        // Chargement du front page utilisateur
        require(plugin_dir_path( dirname( __FILE__ ) ) . '/views/front/dashboard.php');
    }
    
}
add_shortcode('trm_front_dashboard','loadDashboardFront');

function loadWishList(){
    
    if(!is_admin()){
        // Chargement du front page utilisateur
        require(plugin_dir_path( dirname( __FILE__ ) ) . '/views/front/wishlist.php');
    }
    
}
add_shortcode('trm_wishlist_affiliation','loadWishList');