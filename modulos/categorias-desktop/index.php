
<?php

if($loja['design_barra_categorias_desktop'] == 1){

$categorias = mysqli_query($conn, "SELECT id, nome FROM categoria ORDER BY nivel, ordem ASC LIMIT 4");
$n_categorias     = mysqli_num_rows($categorias);

if($n_categorias > 0){

//FUNÇÃO QUE ACERTA O NOME DA CATEGORIA PARA URL
function urlCategoria($nome){    
    $caracteres_proibidos_url = array('(',')','.',',');
    $caracteres_por_espaco    = array(' - ');
    $caracteres_por_hifen     = array(' ','/','#39;','#34;');
    return mb_strtolower(str_replace($caracteres_proibidos_url,'', str_replace($caracteres_por_hifen,'-', str_replace($caracteres_por_espaco,' ', preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim(preg_replace('/(\'|")/', "-", $nome))))))));
}

?>

<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/categorias-desktop/css/style-1.1.css">

<!--CATEGORIAS DESKTOP-->

<section id="categorias-desktop" class="d-none d-xl-block">

    <div class="container">

        <div class="row">

            <div class="col categorias-desktop-categoria"><a href="<?= $loja['site'] ?>">Home</a></div>
            <div class="col categorias-desktop-categoria"><a href="<?= $loja['site'] ?>">Quem Somos</a></div>
            <div class="col categorias-desktop-categoria"><a href="<?= $loja['site'] ?>">Segmento</a></div>
            <div class="col categorias-desktop-categoria"><a href="<?= $loja['site'] ?>">Produtos</a></div>
            <div class="col categorias-desktop-categoria"><a href="<?= $loja['site'] ?>">Contato</a></div>
            <div class="col categorias-desktop-categoria"><a href="<?= $loja['site'] ?>">Trabalhe Conosco</a></div>

        </div>

    </div>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="<?= $loja['site'] ?>modulos/categorias-desktop/js/scripts.js"></script>

<?php } } ?>