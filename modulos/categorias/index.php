<?php 

if($loja['design_sessao_categorias'] == 1){

$categorias   = mysqli_query($conn, "SELECT nome, id, imagem FROM categoria ORDER BY nivel, ordem, nome LIMIT 11"); 
$n_categorias = mysqli_num_rows($categorias);

if($n_categorias > 0){

//FUNÇÃO QUE ACERTA O NOME DA CATEGORIA PARA URL
function urlSessaoCategoria($nome){    
    $caracteres_proibidos_url = array('(',')','.',',');
    $caracteres_por_espaco    = array(' - ');
    $caracteres_por_hifen     = array(' ','/','#39;','#34;');
    return mb_strtolower(str_replace($caracteres_proibidos_url,'', str_replace($caracteres_por_hifen,'-', str_replace($caracteres_por_espaco,' ', preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim(preg_replace('/(\'|")/', "-", $nome))))))));
}

?>

<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/categorias/css/style.css">

<section id="categorias">

    <div id="categorias-categorias">

        <h2 class="d-none">Navegue pelas categorias</h2>
        
        <div class="titulo-section">
            <span>NAVEGUE PELAS CATEGORIAS</span>
        </div>

        <div class="row">
            <?php while($categoria = mysqli_fetch_array($categorias)){ ?>
                <div class="col-12 col-md-6 col-lg-3">
                    <a class="categorias-categoria" href="<?= $loja['site'] ?>categoria/<?= urlSessaoCategoria($categoria['nome']).'/'.$categoria['id'] ?>" alt="<?= $categoria['nome'] ?>">
                        <?php if($categoria['imagem'] != ''){ ?><img class="mr-2" src="<?= $loja['site'] ?>imagens/categorias/<?= $categoria['imagem'] ?>" alt="<?= $categoria['imagem'] ?>"><?php } ?>
                        <?= $categoria['nome'] ?>
                    </a>
                </div>
            <?php } ?>
            <div class="col-12 col-md-6 col-lg-3">
                <a class="categorias-categoria" href="<?= $loja['site'] ?>categoria/todas/0" alt="Todos Produtos">TODAS</a>
            </div>
        </div>

    </div>

</section>

<?php } } ?>