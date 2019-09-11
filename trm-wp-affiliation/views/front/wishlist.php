<div class="grille">

    <div class="colonne-12">

        <div class="grille">

            <?php 

            if(!isset($_GET['w_user'])){

                if(is_user_logged_in()){
                    echo '<div class="colonne-12">';
                    echo '<h3>Lien vers ma wishlist</h3>';
                    echo '<code>'.home_url().'/wishlist-partenaire/?w_user='.get_current_user_id().'&affiliate='.get_current_user_id().'</code>';
                    echo '</div>';
                }

            }
            
            if(isset($_GET['w_user'])){

                $user = get_user_by('id',$_GET['w_user']);

                echo "Wishlist de ".$user->display_name;

                $args = array(
                    'post_type'=>'wishlist',
                    'meta_query'=>array(
                        array(
                            'key'=>'utilisateur_wishlist',
                            'value'=>$_GET['w_user']
                        )
                    )
                );
            }else{
                $args = array(
                    'post_type'=>'wishlist',
                    'meta_query'=>array(
                        array(
                            'key'=>'utilisateur_wishlist',
                            'value'=>get_current_user_id()
                        )
                    )
                );
            }


            $wishlist = get_posts($args);

            if($wishlist){

            foreach($wishlist as $item){

                $fields = get_post_meta($item->ID);

                $acf =  get_fields( $item->ID );

                // var_dump($acf);
                
                foreach($acf['produits'] as $product){

                    $product = get_post($product);

                    echo '<div class="colonne-12 border my-2 bg-light">';

                    echo '<div class="colonne-5 my-2">';
                    echo '<h3><a href="'.get_permalink($product->ID).'">'.$product->post_title.'</a></h3>';

                    echo '</div>';

                    if( is_user_logged_in() ) {
                        $user = wp_get_current_user();
                        $roles = ( array ) $user->roles;
                        if(in_array('partenaires', $roles) || in_array('administrator', $roles)){
                            ?>

                            <?php

                            global $wp;
                            $current_url = get_permalink($product->ID);

                            ?>

                            <!-- The text field -->

                            <div class="colonne-5 my-2 float-right">
                            <label id="zoneAlert<?php echo $product->ID ?>"></label>
                            
                            <input type="text" value="<?php echo $current_url.'?affiliate='.get_current_user_id(); ?>" class="input" id="<?php echo $product->ID ?>">

                            <!-- The button used to copy the text -->
                            <button type="button" class="button" onclick="myFunction(<?php echo $product->ID ?>)">Copier le lien d'affiliation</button>
                            
                            </div>

                            <?php
                        }
                    }

                    echo '</div>';
                
                }

            }

            }

            ?>

        </div>

    </div>

</div>

                        <script>

                            function myFunction(id) {
                            /* Get the text field */
                            var copyText = document.getElementById(id);
                            var zoneAlert = document.getElementById("zoneAlert"+id);


                            /* Select the text field */
                            copyText.select();

                            /* Copy the text inside the text field */
                            document.execCommand("copy");

                                zoneAlert.innerHTML = '<i class="text-success">Lien copié !</i>';
                            
                            }

                            </script>