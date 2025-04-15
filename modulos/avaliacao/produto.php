<?php 

if($loja['opcao_mostrar_avaliacoes'] == 1){

$busca_avaliacoes_produto = mysqli_query($conn, "
    SELECT a.*, c.nome AS nome_cliente, c.sobrenome AS sobrenome_cliente, pd.nome AS produto_nome
    FROM avaliacao AS a
    LEFT JOIN pedido AS p ON p.id = a.id_pedido
    LEFT JOIN cliente AS c ON c.id = p.id_cliente
    LEFT JOIN produto AS pd ON pd.id = a.id_produto
    WHERE a.tipo = 'PRODUTO' AND a.id_produto = '$id_produto_url' AND a.status = 1 AND a.mostrar_avaliacao = 1 
    ORDER BY a.id DESC
    LIMIT 3
");

if(mysqli_num_rows($busca_avaliacoes_produto) > 0){
    
$busca_media_produto = mysqli_query($conn, "SELECT AVG(nota) AS media FROM avaliacao WHERE tipo = 'PRODUTO' AND status = 1 AND mostrar_avaliacao = 1 AND id_produto = ".$id_produto_url);
$total_media_produto = mysqli_fetch_array($busca_media_produto);
$total_media_produto = $total_media_produto['media'];

?>

<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/avaliacao/css/style.css">

<!--AVALIAÇÕES DO PRODUTO-->
<section id="produto-avaliacoes">

    <h2 class="d-none">Avaliações do produto</h2>

    <div class="row">
        <div class="col-12">
            <div class="titulo-section">AVALIAÇÕES</div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <li class="text-capitalize avaliacao-loja avaliacao-loja-geral" title="<?= number_format($total_media_produto,2,'.','') ?>">  
                <?php 
                    $media_quebrada = explode('.',number_format($total_media_produto,2,'.',''));
                    $media_quebrada = '0.'.$media_quebrada[1];
                    $media_quebrada = 1-$media_quebrada;
                ?>
                <ul>
                    <li><img style="<?php if($total_media_produto > 0 AND $total_media_produto <= 1){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_produto >= 1){ echo 'img-dourada'; } ?>" estrela="1" id="estrela-1" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="1 estrela"></li>
                    <li><img style="<?php if($total_media_produto > 1 AND $total_media_produto <= 2){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_produto >= 2){ echo 'img-dourada'; } ?>" estrela="2" id="estrela-2" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="2 estrelas"></li>
                    <li><img style="<?php if($total_media_produto > 2 AND $total_media_produto <= 3){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_produto >= 3){ echo 'img-dourada'; } ?>" estrela="3" id="estrela-3" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="3 estrelas"></li>
                    <li><img style="<?php if($total_media_produto > 3 AND $total_media_produto <= 4){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_produto >= 4){ echo 'img-dourada'; } ?>" estrela="4" id="estrela-4" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="4 estrelas"></li>
                    <li><img style="<?php if($total_media_produto > 4 AND $total_media_produto <= 5){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_produto >= 5){ echo 'img-dourada'; } ?>" estrela="5" id="estrela-5" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="5 estrelas"></li>
                </ul>
            </li>
        </div>
    </div>
    
    <div id="container-avaliacoes-produto">

        <?php $n_avaliacoes = mysqli_num_rows($busca_avaliacoes_produto); ?>
        <?php while($avaliacao_produto = mysqli_fetch_array($busca_avaliacoes_produto)){  ?>
            <div class="row">
                <div class="col-12">
                    <div class="produto-avaliacoes-avaliacao">
                        <ul>
                            <li class="produto-avaliacoes-avaliacao-estrelas avaliacao-loja">
                                <span class="d-none">Nota <?= $avaliacao_produto['nota'] ?></span>                                         
                                <ul>
                                    <li><img class="estrela <?php if($avaliacao_produto['nota'] >= 1){ echo 'img-dourada'; } ?>" estrela="1" id="estrela-1" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito ruim" alt="1 estrela"></li>
                                    <li><img class="estrela <?php if($avaliacao_produto['nota'] >= 2){ echo 'img-dourada'; } ?>" estrela="2" id="estrela-2" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Ruim" alt="2 estrelas"></li>
                                    <li><img class="estrela <?php if($avaliacao_produto['nota'] >= 3){ echo 'img-dourada'; } ?>" estrela="3" id="estrela-3" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Regular" alt="3 estrelas"></li>
                                    <li><img class="estrela <?php if($avaliacao_produto['nota'] >= 4){ echo 'img-dourada'; } ?>" estrela="4" id="estrela-4" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Boa" alt="4 estrelas"></li>
                                    <li><img class="estrela <?php if($avaliacao_produto['nota'] >= 5){ echo 'img-dourada'; } ?>" estrela="5" id="estrela-5" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito boa" alt="5 estrelas"></li>
                                </ul>
                            </li>
                            <li class="produto-avaliacoes-avaliacao-cliente"><?= $avaliacao_produto['nome_cliente'].' '.substr($avaliacao_produto['sobrenome_cliente'], 0, 1).'.' ?></li>
                            <li class="produto-avaliacoes-avaliacao-data"><i>Data: <?= date('d/m/Y', strtotime($avaliacao_produto['data_cadastro'])) ?></i></li>
                            <li class="produto-avaliacoes-avaliacao-comentario"><?= $avaliacao_produto['comentario'] ?></li>
                            <?php if($avaliacao_produto['replica'] != ''){ ?>
                                <li class="produto-avaliacoes-avaliacao-replica-titulo"><?= $loja['nome'] ?> respondeu:</li>
                                <li class="produto-avaliacoes-avaliacao-replica"><?= $avaliacao_produto['replica'] ?></li>
                                <li class="produto-avaliacoes-avaliacao-replica-data"><?= date('d/m/Y H:i', strtotime($avaliacao_produto['data_replica'])) ?></li>
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
            WHERE a.tipo = 'PRODUTO' AND a.status = 1 AND a.mostrar_avaliacao = 1 AND a.id < $avaliacao_produtos_id AND a.id_produto = $id_produto_url
            ORDER BY a.id DESC
        ");  
    ?>
    <?php if(mysqli_num_rows($busca_avaliacoes_produtos_aux) > 0){ ?>
        <div class="row">
            <div class="col-12">
                <a id="btn-carrega-mais-produtos" href="javascript: carregarMaisProduto(<?= $id_produto_url ?>,<?= $avaliacao_produtos_id ?>);" class="btn btn-carregar-mais">CARREGAR MAIS</a>
            </div>
        </div>
    <?php } ?>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="<?= $loja['site'] ?>modulos/avaliacao/js/scripts.js"></script>

<?php } } ?>