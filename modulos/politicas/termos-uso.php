<!--CSS-->
<link rel="stylesheet" href="modulos/politicas/css/style.css">

<?php

    //BUSCA A POLÍTICA
    $politicas = mysqli_query($conn, "SELECT termos_uso FROM politicas WHERE id = 1");
    $politica  = mysqli_fetch_array($politicas);

?>

<!--POLÍTICA TERMOS DE USO-->
<section id="politica-termos-uso" class="politicas">
    
    <ul>
        <li><h1>Termos de Uso</h1></li>
        <li><?= $politica['termos_uso'] ?></li>
    </ul>    

</section>