<!--CSS-->
<link rel="stylesheet" href="modulos/politicas/css/style.css">

<?php

    //BUSCA A POLÍTICA
    $politicas = mysqli_query($conn, "SELECT troca_devolucao FROM politicas WHERE id = 1");
    $politica  = mysqli_fetch_array($politicas);

?>

<!--POLÍTICA DE ENTREGA-->
<section id="politica-troca-devolucao" class="politicas">
    
    <ul>
        <li><h1>Política de Troca e Devolução</h1></li>
        <li><?= $politica['troca_devolucao'] ?></li>
    </ul>    

</section>