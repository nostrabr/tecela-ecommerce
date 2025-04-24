<!--TÍTULO DA INDEX-->
<h1 class="d-none">Home</h1>

<?php

$busca_banners = mysqli_query($conn, "SELECT imagem_desktop, imagem_mobile, link FROM banner WHERE status = 1 ORDER BY ordem ASC");
while($banner = mysqli_fetch_array($busca_banners)){
    $array_banners[] = array(
        "imagem_desktop" => $banner["imagem_desktop"],
        "imagem_mobile"  => $banner["imagem_mobile"],
        "link"           => $banner["link"]
    );
}
$n_banners = mysqli_num_rows($busca_banners);

if($n_banners > 0){

?>

<!--CSS-->
<link rel="stylesheet" href="modulos/banner/css/style.css">

<!--BANNER-->

<section id="banner" class="d-none d-sm-block">
        
    <div id="carrosel-lg" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <?php for($i=0;$i<$n_banners;$i++){ ?>
                <li class="<?php if($i == 0){ echo ' active'; } ?>" data-target="#carrosel-lg" data-slide-to="<?= $i ?>"></li>
            <?php } ?>
        </ol>
        <div class="carousel-inner">
            <?php for($i=0;$i<$n_banners;$i++){ ?>
                <img id="banner-desktop-<?= $array_banners[$i]["imagem_desktop"] ?>" class="carousel-item <?php if($i == 0){ echo 'active'; } ?> <?php if($array_banners[$i]['link'] != ''){ echo 'cursor-pointer'; } ?>" src="<?= $loja['site'] ?>imagens/banners/original/<?= $array_banners[$i]["imagem_desktop"] ?>" <?php if($array_banners[$i]['link'] != ''){ ?> onclick="javascript: window.location.href = '<?= $array_banners[$i]['link'] ?>';" <?php } ?> alt="<?= $array_banners[$i]['link'] ?>">
            <?php } ?>
        </div>
        <a class="carousel-control-prev" href="#carrosel-lg" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carrosel-lg" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

</section>

<?php if($loja['design_banner_principal'] == 0){ ?>
    
    <section id="banner-mobile" class="d-block d-sm-none">

        <div id="carrosel-mobile" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <?php for($i=0;$i<$n_banners;$i++){ ?>
                    <li class="<?php if($i == 0){ echo ' active'; } ?>" data-target="#carrosel-mobile" data-slide-to="<?= $i ?>"></li>
                <?php } ?>
            </ol>
            <div class="carousel-inner">
                <?php for($i=0;$i<$n_banners;$i++){ ?>
                    <img id="banner-mobile-<?= $array_banners[$i]["imagem_mobile"] ?>" class="carousel-item <?php if($i == 0){ echo 'active'; } ?> <?php if($array_banners[$i]['link'] != ''){ echo 'cursor-pointer'; } ?>" src="<?= $loja['site'] ?>imagens/banners/original/<?= $array_banners[$i]["imagem_mobile"] ?>" <?php if($array_banners[$i]['link'] != ''){ ?> onclick="javascript: window.location.href = '<?= $array_banners[$i]['link'] ?>';" <?php } ?> alt="<?= $array_banners[$i]['link'] ?>">
                <?php } ?>
            </div>
            <a class="carousel-control-prev" href="#carrosel-mobile" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carrosel-mobile" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>  

    </section>

<?php } else { ?>
        
    <link rel="stylesheet" href="<?= $loja['site'] ?>css/slick.css">
    <link rel="stylesheet" href="<?= $loja['site'] ?>css/slick-theme.css">

    <section id="banner-modo-banners-iguais" class="d-block d-sm-none">

        <div class="slider slider-modo-banners-iguais">
            <?php for($i=0;$i<$n_banners;$i++){ ?>
                <img style="border-radius: 0 !important;" id="banner-mobile-<?= $array_banners[$i]["imagem_desktop"] ?>" class="carousel-item <?php if($i == 0){ echo 'active'; } ?> <?php if($array_banners[$i]['link'] != ''){ echo 'cursor-pointer'; } ?>" src="<?= $loja['site'] ?>imagens/banners/original/<?= $array_banners[$i]["imagem_mobile"] ?>" <?php if($array_banners[$i]['link'] != ''){ ?> onclick="javascript: window.location.href = '<?= $array_banners[$i]['link'] ?>';" <?php } ?> alt="<?= $array_banners[$i]['link'] ?>">
            <?php } ?>
        </div>

    </section>
        
    <script type="text/javascript" src="<?= $loja['site'] ?>js/slick.js"></script>  

<?php } ?>

<script type="text/javascript" src="modulos/banner/js/scripts.js"></script>  

<?php } ?>