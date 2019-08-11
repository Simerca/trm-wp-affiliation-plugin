<div class="grille">

    <div class="colonne-12 texte-droite">
    
        <a class="btn btn-info"> Nouvelle campagne </a>

    </div>

    <div class="colonne-12">
    
        <table class="table">
        
            <thead>
                <th>ID</th>
                <th>Nom de la campagne</th>
                <th>Gain</th>
                <th>Nombre d'utilisateur touché</th>
                <th>Du</th>
                <th>Au</th>
            </thead>

            <tbody>
            <?php

                
                $args = array(
                    'post_type'=>'campagne_affiliation',
                    'post_author'=>get_current_user_id()
                );
                $userCampagnes = get_posts($args);
                var_dump($userCampagnes);
                foreach($userCampagnes as $userCampagne){

                    $fields = get_fields($userCampagne->ID);
                    echo "<pre>";
                    var_dump($fields);
                    echo "</pre>";
                    $html = '<tr>';
                    $html .= '<td>'.$userCampagne->ID.'</td>';
                    $html .= '<td>'.$userCampagne->post_title.'</td>';
                    $html .= '<td></td>';
                    $html .= '<td>'.count($fields['utilisateur_affilie']).'</td>';
                    $html .= '<td>'.$fields['date_start'].'</td>';
                    $html .= '<td>'.$fields['date_end'].'</td>';
                    $html .= '</tr>';
                    echo $html;

                }

            ?>
            </tbody>

        </table>

    </div> 

</div>