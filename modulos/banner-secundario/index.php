<?php

$busca_banners_secundarios = mysqli_query($conn, "SELECT imagem, titulo, link FROM banner_secundario WHERE status = 1 ORDER BY ordem ASC");
while($banner_secundario = mysqli_fetch_array($busca_banners_secundarios)){
    $array_banners_secundarios[] = array(
        "imagem" => $banner_secundario["imagem"],
        "titulo" => $banner_secundario["titulo"],
        "link"   => $banner_secundario["link"]
    );
}
$n_banners_secundarios = mysqli_num_rows($busca_banners_secundarios);

if($n_banners_secundarios > 0){

?>

<!--CSS-->
<link rel="stylesheet" href="modulos/banner-secundario/css/style.css">

<!--BANNERS SECUNDÁRIOS-->
<section id="banner-secundario">
    
    <div class="row">

        <?php for($i=0;$i<$n_banners_secundarios;$i++){ ?>
            <div class="col-12 col-xl">
                <div class="banner-secundario-item <?php if(($i+1) == $n_banners_secundarios){ echo 'mb-0'; } ?>" style="">
                    <img class="lozad img-fluid" src="imagens/banners-secundarios/original/<?= $array_banners_secundarios[$i]["imagem"] ?>" data-src="imagens/fundo-produto.jpg" alt="">
                    <div class="banner-secundario-item-capa <?php if($array_banners_secundarios[$i]["titulo"] == ''){ echo "banner-secundario-item-sem-titulo"; } ?> " onclick="javascript: window.location.href = '<?= $array_banners_secundarios[$i]['link'] ?>';">
                        <?= $array_banners_secundarios[$i]["titulo"] ?>                                   
                    </div>
                </div>
            </div>
        <?php } ?>

    </div>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="modulos/banner-secundario/js/scripts.js"></script>

<?php } ?>