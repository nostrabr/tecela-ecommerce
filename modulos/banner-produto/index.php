<?php

$busca_banners_produto = mysqli_query($conn, "SELECT imagem, link FROM banner_produto WHERE status = 1 ORDER BY rand() LIMIT 1");
$n_banners = mysqli_num_rows($busca_banners_produto);

if($n_banners > 0){

$banner_produto = mysqli_fetch_array($busca_banners_produto);

?>

<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/banner-produto/css/style.css">

<!--BANNER PRODUTO-->
<section id="banner-produto">

    <div id="banner-produto-container">
        <?php if($banner_produto['link'] != ''){ ?> 
            <a href="<?= $banner_produto['link'] ?>" target="_blank"><img class="lozad img-fluid" src="<?= $loja['site'].'imagens/banners-produto/original/'.$banner_produto['imagem'] ?>" data-src="<?= $loja['site'].'imagens/banners-produto/pequena/'.$banner_produto['imagem'] ?>" alt="<?= $banner_produto['link'] ?>"></a>
        <?php } else { ?>
            <img class="img-fluid" src="<?= $loja['site'].'imagens/banners-produto/original/'.$banner_produto['imagem'] ?>" alt="<?= $banner_produto['link'] ?>">
        <?php } ?>
    </div>

</section>

<?php } ?>