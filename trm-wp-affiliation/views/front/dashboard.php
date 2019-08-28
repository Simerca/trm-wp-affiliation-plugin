<div class="grille">

    <div class="colonne-12 texte-droite">
    
        <a class="btn btn-info"> Nouvelle campagne </a>

    </div>

    <div class="colonne-12">
    
        <table class="table">
        
            <thead>
                <th>ID</th>
                <th>Commande</th>
                <th>Gain</th>
                <th>Payé ?</th>
                <th>Date</th>
            </thead>

            <tbody>
            <?php

                
                $args = array(
                    'post_type'=>'commande_affilie',
                    'meta_query'=>array(
                        array(
                            'key'=>'partenaire',
                            'value'=>get_current_user_id()
                        )
                    )
                );
                $userCampagnes = get_posts($args);

                foreach($userCampagnes as $userCampagne){

                    $fields = get_post_meta($userCampagne->ID);

                    $html = '<tr>';
                    $html .= '<td>'.$userCampagne->ID.'</td>';
                    $html .= '<td>'.$userCampagne->post_title.'</td>';
                    $html .= '<td>'.$fields['montant_affiliation'][0].' €</td>';
                    if(isset($fields['affiliation_paye'])){
                        if($fields['affiliation_paye'] == 1){
                            $html .= '<td><i class="text-success">Payé</i></td>';
                        }else{
                            $html .= '<td><i>Non payé</i></td>';
                        }
                    }else{
                        $html .= '<td></td>';
                    }
                    $html .= '<td>'.$userCampagne->post_date.'</td>';
                    $html .= '</tr>';
                    echo $html;

                }

            ?>
            </tbody>

        </table>

    </div> 

</div>