
<?php

global $wp;
$current_url = home_url(add_query_arg(array(), $wp->request));

?>

<!-- The text field -->

<h4><?php echo __('Espace affiliation','trm_wp_affiliation'); ?></h4>

<div class="col-12 my-2">
<label id="zoneAlert"></label>
<input type="text" value="<?php echo $current_url.'?affiliate='.get_current_user_id(); ?>" class="input form-control" id="myInput">

<!-- The button used to copy the text -->
<button type="button" class="button btn-lg" onclick="myFunction()"><?php echo __('Copier le lien d\'affiliation','trm_wp_affiliation'); ?></button>
<script>

function myFunction() {
  /* Get the text field */
  var copyText = document.getElementById("myInput");
  var zoneAlert = document.getElementById("zoneAlert");


  /* Select the text field */
  copyText.select();

  /* Copy the text inside the text field */
  document.execCommand("copy");

    zoneAlert.innerHTML = '<i class="text-success">Lien copié !</i>';
  
}

</script>
</div>