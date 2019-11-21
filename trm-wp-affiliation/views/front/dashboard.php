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

?>
<div class="grille">

    <div class="colonne-12 texte-droite">

        <h3><?php echo __('Total de vos gains dans le temps','trm-wp-affiliation'); ?></h3>

        <?php 

            $title = array();
            $value = array();
            $gain = 0;
            foreach($userCampagnes as $campagne){
                $gain += $fields['montant_affiliation'][0];
                $fields = get_post_meta($campagne->ID);
                $title[] = date('d M Y H:i', strtotime($campagne->post_date));
                $value[] = $gain;
            }

            echo do_shortcode('[wpcharts type="linechart" legend="true" titles="'.implode(',',$title).'" values="'.implode(',',$value).'"]');

        

        ?>

    </div>

    <div class="colonne-12">
    
        <table class="table" id="datatables">
        
            <thead>
                <th>ID</th>
                <th>Commande</th>
                <th>Campagne</th>
                <th>Gain</th>
                <th>Payé ?</th>
                <th>Date</th>
            </thead>

            <tbody>
            <?php

                
                

                foreach($userCampagnes as $userCampagne){

                    $fields = get_post_meta($userCampagne->ID);

                    $campagnes = unserialize($fields['campagne_daffiliation'][0]);

                    

                    $outputCampagne = array();
                    foreach($campagnes as $campagne){
                        $detailCampagne =  get_post($campagne);
                        if(!in_array($detailCampagne->post_title,$outputCampagne)){
                            $outputCampagne[] = $detailCampagne->post_title;
                        }
                    }

                    $html = '<tr>';
                    $html .= '<td>'.$userCampagne->ID.'</td>';
                    $html .= '<td>'.$userCampagne->post_title.'</td>';
                    $html .= '<td>'.implode (", ", $outputCampagne).'</td>';
                    $html .= '<td>'.$fields['montant_affiliation'][0].' €</td>';
                    if(isset($fields['affiliation_paye'])){
                        if($fields['affiliation_paye'] == 1){
                            $html .= '<td><i class="text-success">Payé</i></td>';
                        }else{
                            $html .= '<td><i>En attente</i></td>';
                        }
                    }else{
                        $html .= '<td>En attente</td>';
                    }
                    $html .= '<td>'.date('d M Y H:i', strtotime($userCampagne->post_date)).'</td>';
                    $html .= '</tr>';
                    echo $html;

                }

            ?>
            </tbody>

        </table>

    </div> 

</div>

<!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css"> -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">

<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>

<script>

    jQuery(document).ready(function(){

        jQuery('#datatables').DataTable({
            "language": {
            "sProcessing": "Traitement en cours ...",
            "sLengthMenu": "Afficher _MENU_ lignes",
            "sZeroRecords": "Aucun résultat trouvé",
            "sEmptyTable": "Aucune donnée disponible",
            "sInfo": "Lignes _START_ à _END_ sur _TOTAL_",
            "sInfoEmpty": "Aucune ligne affichée",
            "sInfoFiltered": "(Filtrer un maximum de_MAX_)",
            "sInfoPostFix": "",
            "sSearch": "Chercher:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Chargement...",
            "oPaginate": {
            "sFirst": "Premier", "sLast": "Dernier", "sNext": "Suivant", "sPrevious": "Précédent"
            },
            "oAria": {
            "sSortAscending": ": Trier par ordre croissant", "sSortDescending": ": Trier par ordre décroissant"
            }
        }
        });

    });

</script>