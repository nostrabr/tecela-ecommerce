<?php

//RECEBE O IDENTIFICADOR DO PEDIDO
$identificador_pedido = filter_input(INPUT_GET, "id", FILTER_SANITIZE_STRING);

//SE NÃO FOR UM IDENTIFICADOR VÁLIDO, MANDA PRA HOME
if(mb_strlen($identificador_pedido) != 32){
    echo "<script> window.location.href = '".$loja['site']."'; </script>";
}

//BUSCA PEDIDO
$busca_pedido = mysqli_query($conn, "SELECT id, codigo FROM pedido WHERE identificador = '$identificador_pedido'");

//SE NÃO FOR UM PEDIDO VÁLIDO, MANDA PRA HOME
if(mysqli_num_rows($busca_pedido) == 0){
    echo "<script> window.location.href = '".$loja['site']."'; </script>";
} else {
    $pedido = mysqli_fetch_array($busca_pedido);
}

//FUNÇÃO QUE ACERTA O NOME DO PRODUTO OU CATEGORIA PARA URL
function urlProduto($nome){
    if($nome != ''){
        $caracteres_proibidos_url = array('(',')','.',',','+','%','$','@','!','#','*','[',']','{','}','?',';',':','|','<','>','=','ª','º','°','§','¹','²','³','£','¢','¬');
        $caracteres_por_espaco    = array(' - ');
        $caracteres_por_hifen     = array(' ','/','#39;','#34;');
        return mb_strtolower(str_replace('--','-',str_replace($caracteres_proibidos_url,'', str_replace($caracteres_por_hifen,'-', str_replace($caracteres_por_espaco,' ', preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim(preg_replace('/(\'|")/', "-", $nome)))))))));
    } else {
        return "categoria";
    }
}

?>

<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/avaliacao/css/style.css">

<!--AVALIAÇÃO-->
<section id="avaliacao-replicas">
    
    <h2 class="subtitulo-pagina-central-h2">Réplicas de avaliação referente ao pedido <?= $pedido['codigo'] ?></h2>    
    <p class="subtitulo-pagina-central-p">Abaixo você poderá ver a réplica da nossa loja às suas avaliações</p>

    <?php 

    //BUSCA AVALIAÇÔES
    $avaliacoes = mysqli_query($conn, "
        SELECT * 
        FROM avaliacao 
        WHERE id_pedido = ".$pedido['id']." AND replica != ''
        ORDER BY id DESC
    ");

    //BUSCA AVALIAÇÕES DO PRODUTO
    $busca_avaliacoes_produtos = mysqli_query($conn, "
        SELECT a.*, pd.nome AS produto_nome, pd.id AS produto_id, c.nome AS produto_categoria
        FROM avaliacao AS a
        LEFT JOIN produto AS pd ON pd.id = a.id_produto
        LEFT JOIN categoria AS c ON c.id = pd.id_categoria
        WHERE a.tipo = 'PRODUTO' AND a.id_pedido = ".$pedido['id']." AND replica != '' 
        ORDER BY a.id DESC
    ");

    ?>

    <?php $n_avaliacoes = mysqli_num_rows($busca_avaliacoes_produtos); ?>
    <?php if($n_avaliacoes > 0){ ?>
        <div class="titulo-avaliacao-replicas">Produtos</div>
    <?php } ?>
    <?php while($avaliacao_produto = mysqli_fetch_array($busca_avaliacoes_produtos)){ ?>
        <div class="row">
            <div class="col-12">
                <div class="site-avaliacoes-avaliacao">
                    <ul>
                        <li class="site-avaliacoes-avaliacao-estrelas avaliacao-loja-replica">
                            <span class="d-none">Nota <?= $avaliacao_site['nota'] ?></span>                                         
                            <ul>
                                <li><img class="estrela <?php if($avaliacao_produto['nota'] >= 1){ echo 'img-dourada'; } ?>" estrela="1" id="estrela-1" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito ruim" alt="1 estrela"></li>
                                <li><img class="estrela <?php if($avaliacao_produto['nota'] >= 2){ echo 'img-dourada'; } ?>" estrela="2" id="estrela-2" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Ruim" alt="2 estrelas"></li>
                                <li><img class="estrela <?php if($avaliacao_produto['nota'] >= 3){ echo 'img-dourada'; } ?>" estrela="3" id="estrela-3" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Regular" alt="3 estrelas"></li>
                                <li><img class="estrela <?php if($avaliacao_produto['nota'] >= 4){ echo 'img-dourada'; } ?>" estrela="4" id="estrela-4" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Boa" alt="4 estrelas"></li>
                                <li><img class="estrela <?php if($avaliacao_produto['nota'] >= 5){ echo 'img-dourada'; } ?>" estrela="5" id="estrela-5" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito boa" alt="5 estrelas"></li>
                            </ul>
                        </li>
                        <li class="avaliacao-produto">Produto: <a href="<?= $loja['site'] ?>produto/<?= urlProduto($avaliacao_produto['produto_categoria']) ?>/<?= urlProduto($avaliacao_produto['produto_nome']) ?>/<?= $avaliacao_produto['produto_id'] ?>" target="_blank"><?= $avaliacao_produto['produto_nome'] ?></a></li>
                        <li class="avaliacao-data"><i>Data: <?= date('d/m/Y', strtotime($avaliacao_produto['data_cadastro'])) ?></i></li>
                        <li class="avaliacao-comentario"><?= $avaliacao_produto['comentario'] ?></li>
                        <?php if($avaliacao_produto['replica'] != ''){ ?>
                            <li class="avaliacao-replica-titulo"><?= $loja['nome'] ?> respondeu:</li>
                            <li class="avaliacao-replica"><?= $avaliacao_produto['replica'] ?></li>
                            <li class="avaliacao-replica-data"><?= date('d/m/Y H:i', strtotime($avaliacao_produto['data_replica'])) ?></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php $avaliacao_produtos_id = $avaliacao_produto['id']; ?>
    <?php } ?>

    <?php 

    $busca_avaliacoes_loja = mysqli_query($conn, "
        SELECT a.*
        FROM avaliacao AS a
        LEFT JOIN pedido AS p ON p.id = a.id_pedido
        WHERE a.tipo = 'EXPERIENCIA-COMPRA' AND a.id_pedido = ".$pedido['id']." AND replica != '' 
        ORDER BY a.id DESC
        LIMIT 5
    ");

    ?>
    
    <?php $n_avaliacoes = mysqli_num_rows($busca_avaliacoes_loja); ?>
    <?php if($n_avaliacoes > 0){ ?>
        <div class="titulo-avaliacao-replicas">Loja</div>
    <?php } ?>
    <?php while($avaliacao_site = mysqli_fetch_array($busca_avaliacoes_loja)){ ?>
        <div class="row">
            <div class="col-12">
                <div class="site-avaliacoes-avaliacao">
                    <ul>
                        <li class="site-avaliacoes-avaliacao-estrelas avaliacao-loja-replica">
                            <span class="d-none">Nota <?= $avaliacao_site['nota'] ?></span>                                         
                            <ul>
                                <li><img class="estrela <?php if($avaliacao_site['nota'] >= 1){ echo 'img-dourada'; } ?>" estrela="1" id="estrela-1" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito ruim" alt="1 estrela"></li>
                                <li><img class="estrela <?php if($avaliacao_site['nota'] >= 2){ echo 'img-dourada'; } ?>" estrela="2" id="estrela-2" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Ruim" alt="2 estrelas"></li>
                                <li><img class="estrela <?php if($avaliacao_site['nota'] >= 3){ echo 'img-dourada'; } ?>" estrela="3" id="estrela-3" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Regular" alt="3 estrelas"></li>
                                <li><img class="estrela <?php if($avaliacao_site['nota'] >= 4){ echo 'img-dourada'; } ?>" estrela="4" id="estrela-4" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Boa" alt="4 estrelas"></li>
                                <li><img class="estrela <?php if($avaliacao_site['nota'] >= 5){ echo 'img-dourada'; } ?>" estrela="5" id="estrela-5" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito boa" alt="5 estrelas"></li>
                            </ul>
                        </li>
                        <li class="avaliacao-data"><i>Data: <?= date('d/m/Y', strtotime($avaliacao_site['data_cadastro'])) ?></i></li>
                        <li class="avaliacao-comentario"><?= $avaliacao_site['comentario'] ?></li>
                        <?php if($avaliacao_site['replica'] != ''){ ?>
                            <li class="avaliacao-replica-titulo"><?= $loja['nome'] ?> respondeu:</li>
                            <li class="avaliacao-replica"><?= $avaliacao_site['replica'] ?></li>
                            <li class="avaliacao-replica-data"><?= date('d/m/Y H:i', strtotime($avaliacao_site['data_replica'])) ?></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php } ?>
    
</section>

<!--SCRIPTS-->
<script type="text/javascript" src="<?= $loja['site'] ?>modulos/avaliacao/js/scripts.js"></script>
