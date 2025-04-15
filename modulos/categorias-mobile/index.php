
<?php

if($loja['design_barra_categorias_mobile'] == 1){

$categorias = mysqli_query($conn, "SELECT id, nome, imagem FROM categoria ORDER BY nivel, ordem ASC LIMIT 14");
$n_categorias     = mysqli_num_rows($categorias);

function removePalavrasPequenas($nome){

    $nome_resumido = '';
    $nome          = explode(' ',$nome);
    $n_nomes       = count($nome);

    for($i = 0; $i < $n_nomes; $i++){
        if(mb_strlen($nome[$i]) > 2){
            $nome_resumido .= $nome[$i].'<br>';
        }
    }

    return trim($nome_resumido);
}

if($n_categorias > 0){

//FUNÇÃO QUE ACERTA O NOME DA CATEGORIA PARA URL
function urlCategoriaMobile($nome){    
    $caracteres_proibidos_url = array('(',')','.',',');
    $caracteres_por_espaco    = array(' - ');
    $caracteres_por_hifen     = array(' ','/','#39;','#34;');
    return mb_strtolower(str_replace($caracteres_proibidos_url,'', str_replace($caracteres_por_hifen,'-', str_replace($caracteres_por_espaco,' ', preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim(preg_replace('/(\'|")/', "-", $nome))))))));
}

?>

<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/categorias-mobile/css/style.css">

<!--CATEGORIAS MOBILE-->
<section id="categorias-mobile" class="d-block d-xl-none">
    
    <ul id="categorias-mobile-ul">

        <?php
        
        $contador_categorias = 0;

        while($categoria = mysqli_fetch_array($categorias)){ 

            $contador_categorias++;
            $displays = '';

            if($contador_categorias > 5){
                $displays = 'd-none d-md-block';
            }

            if($contador_categorias > 8){
                $displays = 'd-none d-sm-block';
            }

            if($contador_categorias > 10){
                $displays = 'd-none d-lg-block';
            }
            
            ?>

            <?php $nome_resumido = substr($categoria['nome'], 0, 2); ?>
            <?php $nome_resumido = substr($categoria['nome'], 0, 2); ?>
            
            <li class="categoria-mobile <?= $displays ?>">
                <a href="<?= $loja['site'] ?>categoria/<?= urlCategoriaMobile($categoria['nome']) ?>/<?= $categoria['id'] ?>" alt="<?= $nome_resumido ?>">
                    <ul>
                        <?php if($categoria['imagem'] != ''){ ?>
                            <li><img src="<?= $loja['site'].'imagens/categorias/'.$categoria['imagem'] ?>" alt="<?= $categoria['nome'] ?>"></li>
                        <?php } else { ?> 
                            <li><?= $nome_resumido ?></li>
                        <?php } ?> 
                        <li><?= removePalavrasPequenas($categoria['nome']) ?></li>
                    </ul>
                </a>
            </li>

        <?php } ?>
        
        <li class="categoria-mobile">
            <a id="categoria-mobile-todas" href="<?= $loja['site'] ?>categoria/todas/0">
                <ul>
                    <li>+</li>
                    <li>Todos<br>Produtos</li>
                </ul>
            </a>
        </li>

    </ul>

</section>

<?php } } ?>