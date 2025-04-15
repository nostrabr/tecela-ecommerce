<?php 

if($loja['opcao_mostrar_avaliacoes'] == 1){

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

$busca_avaliacoes_geral     = mysqli_query($conn, "SELECT COUNT(id) AS total FROM avaliacao WHERE status = 1 AND mostrar_avaliacao = 1");
$avaliacao_geral            = mysqli_fetch_array($busca_avaliacoes_geral);
$n_avaliacoes_geral         = $avaliacao_geral['total']; 

$busca_avaliacoes_loja      = mysqli_query($conn, "SELECT COUNT(id) AS total FROM avaliacao WHERE tipo = 'EXPERIENCIA-COMPRA' AND status = 1 AND mostrar_avaliacao = 1");
$avaliacao_loja             = mysqli_fetch_array($busca_avaliacoes_loja);
$n_avaliacoes_loja          = $avaliacao_loja['total']; 

$busca_avaliacoes_produtos  = mysqli_query($conn, "SELECT COUNT(id) AS total FROM avaliacao WHERE tipo = 'PRODUTO' AND status = 1 AND mostrar_avaliacao = 1");
$avaliacao_produtos         = mysqli_fetch_array($busca_avaliacoes_produtos);
$n_avaliacoes_produtos      = $avaliacao_produtos['total']; 

$busca_avaliacoes_loja = mysqli_query($conn, "
    SELECT a.*, c.nome AS nome_cliente, c.sobrenome AS sobrenome_cliente
    FROM avaliacao AS a
    LEFT JOIN pedido AS p ON p.id = a.id_pedido
    LEFT JOIN cliente AS c ON c.id = p.id_cliente
    WHERE a.tipo = 'EXPERIENCIA-COMPRA' AND a.status = 1 AND a.mostrar_avaliacao = 1 
    ORDER BY a.id DESC
    LIMIT 5
");

$busca_avaliacoes_produtos = mysqli_query($conn, "
    SELECT a.*, c.nome AS nome_cliente, c.sobrenome AS sobrenome_cliente, pd.nome AS produto_nome, pd.id AS produto_id, pc.nome AS produto_categoria
    FROM avaliacao AS a
    LEFT JOIN pedido AS p ON p.id = a.id_pedido
    LEFT JOIN cliente AS c ON c.id = p.id_cliente
    LEFT JOIN produto AS pd ON pd.id = a.id_produto
    LEFT JOIN categoria AS pc ON pc.id = pd.id_categoria
    WHERE a.tipo = 'PRODUTO' AND a.status = 1 AND a.mostrar_avaliacao = 1 
    ORDER BY a.id DESC
    LIMIT 5
");

if(mysqli_num_rows($busca_avaliacoes_loja) > 0){

$busca_media_geral    = mysqli_query($conn, "SELECT AVG(nota) AS media FROM avaliacao WHERE status = 1 AND mostrar_avaliacao = 1");
$total_media_geral    = mysqli_fetch_array($busca_media_geral);
$total_media_geral    = $total_media_geral['media'];

$busca_media_site     = mysqli_query($conn, "SELECT AVG(nota) AS media FROM avaliacao WHERE tipo = 'EXPERIENCIA-COMPRA' AND status = 1 AND mostrar_avaliacao = 1");
$total_media_site     = mysqli_fetch_array($busca_media_site);
$total_media_site     = $total_media_site['media'];

$busca_media_produtos = mysqli_query($conn, "SELECT AVG(nota) AS media FROM avaliacao WHERE tipo = 'PRODUTO' AND status = 1 AND mostrar_avaliacao = 1");
$total_media_produtos = mysqli_fetch_array($busca_media_produtos);
$total_media_produtos = $total_media_produtos['media'];

?>

<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/avaliacao/css/style.css">

<!--AVALIAÇÕES DO SITE-->
<section id="site-avaliacoes">

    <h2 class="d-none">Avaliações do site</h2>
    
    <div class="row">
        <div class="col-12">
            <div class="titulo-section text-left">AVALIAÇÃO GERAL</div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <li class="text-capitalize avaliacao-loja avaliacao-loja-geral" title="<?= number_format($total_media_geral,2,'.','') ?>">  
                <?php 
                    $media_quebrada = explode('.',number_format($total_media_geral,2,'.',''));
                    $media_quebrada = '0.'.$media_quebrada[1];
                    $media_quebrada = 1-$media_quebrada;
                ?>
                <ul>
                    <li><img style="<?php if($total_media_geral > 0 AND $total_media_geral <= 1){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_geral >= 1){ echo 'img-dourada'; } ?>" estrela="1" id="estrela-1" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="1 estrela"></li>
                    <li><img style="<?php if($total_media_geral > 1 AND $total_media_geral <= 2){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_geral >= 2){ echo 'img-dourada'; } ?>" estrela="2" id="estrela-2" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="2 estrelas"></li>
                    <li><img style="<?php if($total_media_geral > 2 AND $total_media_geral <= 3){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_geral >= 3){ echo 'img-dourada'; } ?>" estrela="3" id="estrela-3" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="3 estrelas"></li>
                    <li><img style="<?php if($total_media_geral > 3 AND $total_media_geral <= 4){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_geral >= 4){ echo 'img-dourada'; } ?>" estrela="4" id="estrela-4" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="4 estrelas"></li>
                    <li><img style="<?php if($total_media_geral > 4 AND $total_media_geral <= 5){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_geral >= 5){ echo 'img-dourada'; } ?>" estrela="5" id="estrela-5" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="5 estrelas"></li>
                    <li class="nota-extenso d-none d-md-block ml-3"><b><?= number_format($total_media_geral,1,',','') ?> de 5,0</b></li>
                </ul>
                <div class="nota-extenso d-block d-md-none"><b><?= number_format($total_media_geral,1,',','') ?> de 5,0</b></div>
            </li>
            <li class="legenda-quantidade-avaliacoes">NOTA MÉDIA BASEADA EM <?= $n_avaliacoes_geral ?> AVALIAÇÕES DOS NOSSOS CLIENTES</li>
        </div>
    </div>

    <hr>

    <div class="row">

        <div class="col-12 col-md-6">

            <div class="row">
                <div class="col-12">
                    <div class="titulo-section text-left">AVALIAÇÃO DA LOJA</div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <li class="text-capitalize avaliacao-loja avaliacao-loja-categoria" title="<?= number_format($total_media_site,2,'.','') ?>">  
                        <?php 
                            $media_quebrada = explode('.',number_format($total_media_site,2,'.',''));
                            $media_quebrada = '0.'.$media_quebrada[1];
                            $media_quebrada = 1-$media_quebrada;
                        ?>
                        <ul>
                            <li><img style="<?php if($total_media_site > 0 AND $total_media_site <= 1){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_site >= 1){ echo 'img-dourada'; } ?>" estrela="1" id="estrela-1" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="1 estrela"></li>
                            <li><img style="<?php if($total_media_site > 1 AND $total_media_site <= 2){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_site >= 2){ echo 'img-dourada'; } ?>" estrela="2" id="estrela-2" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="2 estrelas"></li>
                            <li><img style="<?php if($total_media_site > 2 AND $total_media_site <= 3){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_site >= 3){ echo 'img-dourada'; } ?>" estrela="3" id="estrela-3" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="3 estrelas"></li>
                            <li><img style="<?php if($total_media_site > 3 AND $total_media_site <= 4){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_site >= 4){ echo 'img-dourada'; } ?>" estrela="4" id="estrela-4" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="4 estrelas"></li>
                            <li><img style="<?php if($total_media_site > 4 AND $total_media_site <= 5){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_site >= 5){ echo 'img-dourada'; } ?>" estrela="5" id="estrela-5" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="5 estrelas"></li>
                        </ul>
                        <div class="nota-extenso-pequena"><b><?= number_format($total_media_site,1,',','') ?> de 5,0</b></div>
                        
                    </li>
                </div>
            </div>

            <hr>

            <div id="container-avaliacoes-loja">

                <?php $n_avaliacoes = mysqli_num_rows($busca_avaliacoes_loja); ?>
                <?php while($avaliacao_site = mysqli_fetch_array($busca_avaliacoes_loja)){ ?>
                    <div id="<?= $avaliacao_site['id'] ?>" class="row">
                        <div class="col-12">
                            <div class="site-avaliacoes-avaliacao">
                                <ul>
                                    <li class="site-avaliacoes-avaliacao-estrelas avaliacao-loja">
                                        <span class="d-none">Nota <?= $avaliacao_site['nota'] ?></span>                                         
                                        <ul>
                                            <?php if($avaliacao_site['nota'] >= 1){ ?><li><img class="estrela estrela-preta" estrela="1" id="estrela-1" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito ruim" alt="1 estrela"></li><?php } ?>
                                            <?php if($avaliacao_site['nota'] >= 2){ ?><li><img class="estrela estrela-preta" estrela="2" id="estrela-2" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Ruim" alt="2 estrelas"></li><?php } ?>
                                            <?php if($avaliacao_site['nota'] >= 3){ ?><li><img class="estrela estrela-preta" estrela="3" id="estrela-3" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Regular" alt="3 estrelas"></li><?php } ?>
                                            <?php if($avaliacao_site['nota'] >= 4){ ?><li><img class="estrela estrela-preta" estrela="4" id="estrela-4" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Boa" alt="4 estrelas"></li><?php } ?>
                                            <?php if($avaliacao_site['nota'] >= 5){ ?><li><img class="estrela estrela-preta" estrela="5" id="estrela-5" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito boa" alt="5 estrelas"></li><?php } ?>
                                        </ul>
                                    </li>
                                    <li class="site-avaliacoes-avaliacao-cliente"><?= $avaliacao_site['nome_cliente'].' '.substr($avaliacao_site['sobrenome_cliente'], 0, 1).'.' ?></li>
                                    <li class="site-avaliacoes-avaliacao-data"><i>Data: <?= date('d/m/Y', strtotime($avaliacao_site['data_cadastro'])) ?></i></li>
                                    <li class="site-avaliacoes-avaliacao-comentario"><?= $avaliacao_site['comentario'] ?></li>
                                    <?php if($avaliacao_site['replica'] != ''){ ?>
                                        <li class="site-avaliacoes-avaliacao-replica-titulo"><?= $loja['nome'] ?> respondeu:</li>
                                        <li class="site-avaliacoes-avaliacao-replica"><?= $avaliacao_site['replica'] ?></li>
                                        <li class="site-avaliacoes-avaliacao-replica-data"><?= date('d/m/Y H:i', strtotime($avaliacao_site['data_replica'])) ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php $avaliacao_site_id = $avaliacao_site['id']; ?>
                <?php } ?>

            </div>

            <?php 
                $busca_avaliacoes_loja_aux = mysqli_query($conn, "
                    SELECT a.id
                    FROM avaliacao AS a
                    WHERE a.tipo = 'EXPERIENCIA-COMPRA' AND a.status = 1 AND a.mostrar_avaliacao = 1 AND a.id < $avaliacao_site_id
                    ORDER BY a.id DESC
                ");                
            ?>
            <?php if(mysqli_num_rows($busca_avaliacoes_loja_aux) > 0){ ?>
                <div class="row">
                    <div class="col-12">
                        <a id="btn-carrega-mais-site" href="javascript: carregarMais('L',<?= $avaliacao_site_id ?>);" class="btn btn-carregar-mais">CARREGAR MAIS</a>
                    </div>
                </div>
            <?php } ?>

        </div>

        <?php if(mysqli_num_rows($busca_avaliacoes_produtos) > 0){ ?>
            
            <div class="col-12 col-md-6">

                <div class="row">
                    <div class="col-12">
                        <div id="titulo-avaliacao-produtos" class="titulo-section text-left">AVALIAÇÃO DOS PRODUTOS</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <li class="text-capitalize avaliacao-loja avaliacao-loja-categoria" title="<?= number_format($total_media_produtos,2,'.','') ?>">  
                            <?php 
                                $media_quebrada = explode('.',number_format($total_media_produtos,2,'.',''));
                                $media_quebrada = '0.'.$media_quebrada[1];
                                $media_quebrada = 1-$media_quebrada;
                            ?>
                            <ul>
                                <li><img style="<?php if($total_media_produtos > 0 AND $total_media_produtos <= 1){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_produtos >= 1){ echo 'img-dourada'; } ?>" estrela="1" id="estrela-1" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="1 estrela"></li>
                                <li><img style="<?php if($total_media_produtos > 1 AND $total_media_produtos <= 2){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_produtos >= 2){ echo 'img-dourada'; } ?>" estrela="2" id="estrela-2" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="2 estrelas"></li>
                                <li><img style="<?php if($total_media_produtos > 2 AND $total_media_produtos <= 3){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_produtos >= 3){ echo 'img-dourada'; } ?>" estrela="3" id="estrela-3" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="3 estrelas"></li>
                                <li><img style="<?php if($total_media_produtos > 3 AND $total_media_produtos <= 4){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_produtos >= 4){ echo 'img-dourada'; } ?>" estrela="4" id="estrela-4" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="4 estrelas"></li>
                                <li><img style="<?php if($total_media_produtos > 4 AND $total_media_produtos <= 5){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_produtos >= 5){ echo 'img-dourada'; } ?>" estrela="5" id="estrela-5" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="5 estrelas"></li>
                            </ul>
                            <div class="nota-extenso-pequena"><b><?= number_format($total_media_produtos,1,',','') ?> de 5,0</b></div>
                            
                        </li>
                    </div>
                </div>

                <hr>

                <div id="container-avaliacoes-produto">

                    <?php $n_avaliacoes = mysqli_num_rows($busca_avaliacoes_produtos); ?>
                    <?php while($avaliacao_produto = mysqli_fetch_array($busca_avaliacoes_produtos)){ ?>
                        <div id="<?= $avaliacao_produto['id'] ?>" class="row">
                            <div class="col-12">
                                <div class="site-avaliacoes-avaliacao">
                                    <ul>
                                        <li class="site-avaliacoes-avaliacao-estrelas avaliacao-loja">
                                            <span class="d-none">Nota <?= $avaliacao_site['nota'] ?></span>                                         
                                            <ul>
                                                <?php if($avaliacao_produto['nota'] >= 1){ ?><li><img class="estrela estrela-preta" estrela="1" id="estrela-1" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito ruim" alt="1 estrela"></li><?php } ?>
                                                <?php if($avaliacao_produto['nota'] >= 2){ ?><li><img class="estrela estrela-preta" estrela="2" id="estrela-2" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Ruim" alt="2 estrelas"></li><?php } ?>
                                                <?php if($avaliacao_produto['nota'] >= 3){ ?><li><img class="estrela estrela-preta" estrela="3" id="estrela-3" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Regular" alt="3 estrelas"></li><?php } ?>
                                                <?php if($avaliacao_produto['nota'] >= 4){ ?><li><img class="estrela estrela-preta" estrela="4" id="estrela-4" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Boa" alt="4 estrelas"></li><?php } ?>
                                                <?php if($avaliacao_produto['nota'] >= 5){ ?><li><img class="estrela estrela-preta" estrela="5" id="estrela-5" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito boa" alt="5 estrelas"></li><?php } ?>
                                            </ul>
                                        </li>
                                        <li class="site-avaliacoes-avaliacao-cliente"><?= $avaliacao_produto['nome_cliente'].' '.substr($avaliacao_produto['sobrenome_cliente'], 0, 1).'.' ?></li>
                                        <li class="site-avaliacoes-avaliacao-produto">Produto: <a href="<?= $loja['site'] ?>produto/<?= urlProduto($avaliacao_produto['produto_categoria']) ?>/<?= urlProduto($avaliacao_produto['produto_nome']) ?>/<?= $avaliacao_produto['produto_id'] ?>" target="_blank"><?= $avaliacao_produto['produto_nome'] ?></a></li>
                                        <li class="site-avaliacoes-avaliacao-data"><i>Data: <?= date('d/m/Y', strtotime($avaliacao_produto['data_cadastro'])) ?></i></li>
                                        <li class="site-avaliacoes-avaliacao-comentario"><?= $avaliacao_produto['comentario'] ?></li>
                                        <?php if($avaliacao_produto['replica'] != ''){ ?>
                                            <li class="site-avaliacoes-avaliacao-replica-titulo"><?= $loja['nome'] ?> respondeu:</li>
                                            <li class="site-avaliacoes-avaliacao-replica"><?= $avaliacao_produto['replica'] ?></li>
                                            <li class="site-avaliacoes-avaliacao-replica-data"><?= date('d/m/Y H:i', strtotime($avaliacao_produto['data_replica'])) ?></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php $avaliacao_produtos_id = $avaliacao_produto['id']; ?>
                    <?php } ?>

                </div>

                <?php 
                    $busca_avaliacoes_produtos_aux = mysqli_query($conn, "
                        SELECT a.id
                        FROM avaliacao AS a
                        WHERE a.tipo = 'PRODUTO' AND a.status = 1 AND a.mostrar_avaliacao = 1 AND a.id < $avaliacao_produtos_id
                        ORDER BY a.id DESC
                    ");  
                ?>
                <?php if(mysqli_num_rows($busca_avaliacoes_produtos_aux) > 0){ ?>
                    <div class="row">
                        <div class="col-12">
                            <a id="btn-carrega-mais-produtos" href="javascript: carregarMais('P',<?= $avaliacao_produtos_id ?>);" class="btn btn-carregar-mais">CARREGAR MAIS</a>
                        </div>
                    </div>
                <?php } ?>

            </div>

        <?php } ?>

    </div>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="<?= $loja['site'] ?>modulos/avaliacao/js/scripts.js"></script>

<?php } else { ?>

<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/avaliacao/css/style.css">

<section id="site-avaliacoes">

    <div class="row">
        <div class="col-12">Esta loja ainda não possui avaliações. : /</div>
    </div>

</section>

<?php } } ?>