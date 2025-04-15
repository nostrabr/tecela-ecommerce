
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

            <div class="col"><a id="categorias-desktop-btn-vertudo" href="javascript: abreFechaCategoriasEscondidas();">Ver tudo <i id="categorias-desktop-chevron-vertudo" class="fas fa-angle-down"></i></a></div>

            <?php 
            
            while($categoria = mysqli_fetch_array($categorias)){ 
                
                $busca_subcategorias = mysqli_query($conn, "SELECT id, nome FROM categoria WHERE pai = '".$categoria['id']."' ORDER BY nivel, ordem");

                if(mysqli_num_rows($busca_subcategorias) > 0){
                    $tem_subcategoria = true;
                } else {
                    $tem_subcategoria = false;
                }
                
            ?>
                
                <div id="categorias-desktop-categoria-<?= $categoria['id'] ?>" class="col categorias-desktop-categoria" id-categoria='<?= $categoria['id'] ?>'><a href="<?= $loja['site'] ?>categoria/<?= urlCategoria($categoria['nome']).'/'.$categoria['id'] ?>" alt="<?= $categoria['nome'] ?>"><?= $categoria['nome'] ?> <?php if($tem_subcategoria){ ?><i class="fas fa-caret-down"></i><?php } ?></a></div>

                <?php 

                    if($tem_subcategoria){ ?>
                        
                        <div id="categorias-desktop-subcategoria-<?= $categoria['id'] ?>" class="categorias-desktop-subcategoria">
                            <ul>
                                <?php while($subcategoria = mysqli_fetch_array($busca_subcategorias)){ ?>
                                    <li><a href="<?= $loja['site'].'categoria/'.urlCategoria($subcategoria['nome']).'/'.$subcategoria['id'] ?>"><?= $subcategoria['nome'] ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>

                    <?php }

                ?>

            <?php } ?>

            <div id="categorias-desktop-categoria-0" class="col categorias-desktop-categoria"><a href="<?= $loja['site'] ?>categoria/todas/0" alt="Todas Categorias">TODAS</a></div>

        </div>

    </div>

</section>

<section id="categorias-desktop-escondidas">

    <div class="container">

        <div class="row">

            <div class="categorias-desktop-escondidas-container">

            <?php 

                $categorias = mysqli_query($conn, "SELECT id, nome FROM categoria WHERE nivel = 1 ORDER BY nivel, ordem ASC");
                $contador   = 0;

                while($categoria = mysqli_fetch_array($categorias)){ $contador++;                    

                    ?>

                    <ul class="col"><li class="categorias-desktop-escondidas-categoria"><a href="<?= $loja['site'].'categoria/'.urlCategoria($categoria['nome']).'/'.$categoria['id'] ?>"><?= $categoria['nome'] ?></a></li><?php

                        $busca_subcategorias = mysqli_query($conn, "SELECT id, nome FROM categoria WHERE pai = '".$categoria['id']."' ORDER BY nivel, ordem");
                        if(mysqli_num_rows($busca_subcategorias) > 0){ ?>
                            
                            <?php while($subcategoria = mysqli_fetch_array($busca_subcategorias)){ ?>
                                <li class="categorias-desktop-escondidas-subcategoria"><a href="<?= $loja['site'].'categoria/'.urlCategoria($subcategoria['nome']).'/'.$subcategoria['id'] ?>"><?= $subcategoria['nome'] ?></a></li>
                            <?php } ?>

                        <?php } ?>

                    </ul>

                <?php } ?>

                <ul class="col">
                    <li class="categorias-desktop-escondidas-categoria"><a href="<?= $loja['site'].'categoria/todas/0' ?>">VER TODOS PRODUTOS</a></li>
                </ul>

            </div>

        </div>

    </div>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="<?= $loja['site'] ?>modulos/categorias-desktop/js/scripts.js"></script>

<?php } } ?>