<!--CSS-->
<link rel="stylesheet" href="modulos/politicas/css/style.css">

<?php

    //BUSCA A POLÍTICA
    $politicas = mysqli_query($conn, "SELECT comercial FROM politicas WHERE id = 1");
    $politica  = mysqli_fetch_array($politicas);

?>

<!--POLÍTICA COMERCIAL-->
<section id="politica-comercial" class="politicas">
    
    <ul>
        <li><h1>Política Comercial</h1></li>
        <li><?= $politica['comercial'] ?></li>
    </ul>    

</section>