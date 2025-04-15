<!--CSS-->
<link rel="stylesheet" href="modulos/politicas/css/style.css">

<?php

    //BUSCA A POLÍTICA
    $politicas = mysqli_query($conn, "SELECT entrega FROM politicas WHERE id = 1");
    $politica  = mysqli_fetch_array($politicas);

?>

<!--POLÍTICA DE ENTREGA-->
<section id="politica-entrega" class="politicas">
    
    <ul>
        <li><h1>Política de Entrega</h1></li>
        <li><?= $politica['entrega'] ?></li>
    </ul>    

</section>