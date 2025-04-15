<!--CSS-->
<link rel="stylesheet" href="modulos/politicas/css/style.css">

<?php

    //BUSCA A POLÍTICA
    $politicas = mysqli_query($conn, "SELECT privacidade_seguranca FROM politicas WHERE id = 1");
    $politica  = mysqli_fetch_array($politicas);

?>

<!--POLÍTICA DE ENTREGA-->
<section id="politica-privacidade-seguranca" class="politicas">
    
    <ul>
        <li><h1>Política de Privacidade e Segurança</h1></li>
        <li><?= $politica['privacidade_seguranca'] ?></li>
    </ul>    

</section>