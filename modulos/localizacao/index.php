<?php

if($loja['exibir_endereco'] == 1){

?>

<!--CSS-->
<link rel="stylesheet" href="modulos/localizacao/css/style.css">

<!--LOCALIZAÇÃO-->
<section id="localizacao">

    <h1 class="d-none">Localização</h1>

    <?= $loja['google_maps'] ?>

</section>

<?php } else { ?>

    <script> window.location.href = '/'; </script>

<?php } ?>