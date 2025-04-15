<?php 

$busca_total_n_lidas_site    = mysqli_query($conn, "SELECT count(id) AS total FROM avaliacao WHERE tipo = 'EXPERIENCIA-COMPRA' AND lida = 0 AND status = 1");
$total_n_lidas_site          = mysqli_fetch_array($busca_total_n_lidas_site);
$total_n_lidas_site          = $total_n_lidas_site['total'];

$busca_media_site            = mysqli_query($conn, "SELECT AVG(nota) AS media FROM avaliacao WHERE tipo = 'EXPERIENCIA-COMPRA' AND status = 1");
$total_media_site            = mysqli_fetch_array($busca_media_site);
$total_media_site            = $total_media_site['media'];

$busca_total_n_lidas_produto = mysqli_query($conn, "SELECT count(id) AS total FROM avaliacao WHERE tipo = 'PRODUTO' AND lida = 0 AND status = 1");
$total_n_lidas_produto       = mysqli_fetch_array($busca_total_n_lidas_produto);
$total_n_lidas_produto       = $total_n_lidas_produto['total'];

$busca_avaliacoes_compra     = mysqli_query($conn, "
    SELECT a.*, c.nome AS cliente_nome, c.sobrenome AS cliente_sobrenome 
    FROM avaliacao AS a
    LEFT JOIN pedido AS p ON p.id = a.id_pedido
    LEFT JOIN cliente AS c ON c.id = p.id_cliente
    WHERE a.tipo = 'EXPERIENCIA-COMPRA' AND a.status = 1 
    ORDER BY a.data_cadastro DESC
");

$busca_avaliacoes_produto    = mysqli_query($conn, "
    SELECT a.*, p.nome AS nome_produto, c.nome AS cliente_nome, c.sobrenome AS cliente_sobrenome 
    FROM avaliacao AS a 
    LEFT JOIN produto AS p ON p.id = a.id_produto 
    LEFT JOIN pedido AS pd ON pd.id = a.id_pedido
    LEFT JOIN cliente AS c ON c.id = pd.id_cliente
    WHERE a.tipo = 'PRODUTO' AND a.status = 1 
    ORDER BY a.data_cadastro DESC
");

//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){
    if($_SESSION['RETORNO']['STATUS'] == 'REPLICA'){
        echo "<script>mensagemAviso('sucesso', 'Réplica enviada com sucesso.', 5000);</script>";
    }
}

?>

<!--CSS-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="modulos/avaliacoes/css/style.css">

<!--SECTION AVALIAÇÕES-->
<section id="avaliacoes">
  
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">    
                <div id="admin-titulo-pagina">AVALIAÇÕES</div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">                                   
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input text-uppercase" id="mostrar-avaliacoes" name="mostrar-avaliacoes" <?php if($loja['opcao_mostrar_avaliacoes'] == 1){ echo 'checked'; } ?> >
                    <label class="custom-control-label" for="mostrar-avaliacoes">Exibir avaliações</label>
                    <small>Com este módulo ativado, as notas e comentários feitos aos produtos serão exibidos na tela do produto e uma tela de avaliações da loja com acesso pelos menus é liberada.</small>
                </div>
            </div>
        </div>

		<!-- ABAS -->
		<ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="tab-site" data-toggle="tab" href="#conteudo-tab-site" role="tab" aria-controls="conteudo-tab-site" aria-selected="true">
                    <ul class="d-inline-flex">
                        <li>Site</li>
                        <?php if($total_n_lidas_site > 0){ ?>
                            <li class="n-nao-lidas">
                                <?= $total_n_lidas_site ?>
                            </li>
                        <?php } ?>
                    </ul>                    
                </a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="tab-produto" data-toggle="tab" href="#conteudo-tab-produto" role="tab" aria-controls="conteudo-tab-produto" aria-selected="false">
                    <ul class="d-inline-flex">
                        <li>Produto</li>
                        <?php if($total_n_lidas_produto > 0){ ?>
                            <li class="n-nao-lidas">
                                <?= $total_n_lidas_produto ?>
                            </li>
                        <?php } ?>
                    </ul>   
                </a>
			</li>
		</ul>
		
		<div class="tab-content">

			<div class="tab-pane active" id="conteudo-tab-site" role="tabpanel" aria-labelledby="tab-site">

				<!-- ROW DO TÍTULO -->
				<div class="row">
					<div class="col-6">    
						<div id="admin-titulo-pagina">
                            <ul class="mb-0">
                                <?php if($total_media_site > 0){ ?>
                                    <li class="text-capitalize avaliacao-loja avaliacao-loja-geral" title="<?= number_format($total_media_site,2,'.','') ?>">  
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
                                    </li>
                                <?php } ?>
                            </ul>                                      
                        </div>
					</div>	
				</div>

                <!-- ROW DA TABELA -->
                <div class="row">
                    <div class="col-12">   
                        <table id="admin-lista" class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">NOTA</th>
                                    <th scope="col" class="d-none d-lg-table-cell">DATA</th>
                                    <th scope="col" class="d-none d-lg-table-cell">CLIENTE</th>
                                    <th scope="col" class="d-none d-lg-table-cell">COMENTÁRIO</th>
                                    <th scope="col" class="d-none d-lg-table-cell">RÉPLICA</th>
                                    <th scope="col" class="text-right">AÇÕES</th>
                                </tr>
                            </thead>
                            <tbody>      
                                <?php while($avaliacao_compra = mysqli_fetch_array($busca_avaliacoes_compra)){ ?> 
                                    <tr id="avaliacao-<?= $avaliacao_compra['identificador'] ?>" class="cursor-pointer <?php if($avaliacao_compra['lida'] == 0){ echo 'comentario-nao-lido'; } ?>" title="Visualizar" onclick="javascript: visualiza('<?= $avaliacao_compra['nota'] ?>','<?= date('d/m/Y H:i', strtotime($avaliacao_compra['data_cadastro']))?>','<?= preg_replace( "/\r|\n/", " ", addslashes(str_replace('"','\'',$avaliacao_compra['comentario']))) ?>','<?= $avaliacao_compra['lida'] ?>','<?= $avaliacao_compra['identificador'] ?>','','<?= addslashes(str_replace('"','\'',$avaliacao_compra['replica'])) ?>','<?= date('d/m/Y H:i', strtotime($avaliacao_compra['data_replica'])) ?>','<?= $avaliacao_compra['cliente_nome'].' '.$avaliacao_compra['cliente_sobrenome'] ?>');">
                                        <td class="text-capitalize avaliacao-loja">   
                                            <span class="d-none">Nota <?= $avaliacao_compra['nota'] ?></span>                                         
                                            <ul>
                                                <li><img class="estrela <?php if($avaliacao_compra['nota'] >= 1){ echo 'img-dourada'; } ?>" estrela="1" id="estrela-1" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito ruim" alt="1 estrela"></li>
                                                <li><img class="estrela <?php if($avaliacao_compra['nota'] >= 2){ echo 'img-dourada'; } ?>" estrela="2" id="estrela-2" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Ruim" alt="2 estrelas"></li>
                                                <li><img class="estrela <?php if($avaliacao_compra['nota'] >= 3){ echo 'img-dourada'; } ?>" estrela="3" id="estrela-3" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Regular" alt="3 estrelas"></li>
                                                <li><img class="estrela <?php if($avaliacao_compra['nota'] >= 4){ echo 'img-dourada'; } ?>" estrela="4" id="estrela-4" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Boa" alt="4 estrelas"></li>
                                                <li><img class="estrela <?php if($avaliacao_compra['nota'] >= 5){ echo 'img-dourada'; } ?>" estrela="5" id="estrela-5" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito boa" alt="5 estrelas"></li>
                                            </ul>
                                        </td> 
                                        <td class="text-capitalize d-none d-lg-table-cell"><?= date('d/m/Y', strtotime($avaliacao_compra['data_cadastro'])) ?></td>    
                                        <td class="d-none d-lg-table-cell"><?= $avaliacao_compra['cliente_nome'].' '.$avaliacao_compra['cliente_sobrenome'] ?></td>
                                        <td class="d-none d-lg-table-cell"><?= mb_strimwidth($avaliacao_compra['comentario'], 0, 30, "...") ?></td>
                                        <td class="d-none d-lg-table-cell"><?= mb_strimwidth($avaliacao_compra['replica'], 0, 30, "...") ?></td>
                                        <td class="text-right avaliacao-acoes">
                                            <?php if($avaliacao_compra['mostrar_avaliacao'] == 1){ ?>
                                                <span class="text-left align-middle mr-1" id="status-<?= $avaliacao_compra['identificador'] ?>"><a class="botao-status" href="javascript: trocaStatus('<?= $avaliacao_compra['identificador'] ?>',<?= $avaliacao_compra['mostrar_avaliacao'] ?>)" title="Desativar"><img class="status-ativado" src="<?= $loja['site'] ?>imagens/status-ativo.png" alt="Ativo"></a><span class="d-none">ligado on ativo 1</span></span>
                                            <?php } else { ?>
                                                <span class="text-left align-middle mr-1" id="status-<?= $avaliacao_compra['identificador'] ?>"><a class="botao-status" href="javascript: trocaStatus('<?= $avaliacao_compra['identificador'] ?>',<?= $avaliacao_compra['mostrar_avaliacao'] ?>)" title="Ativar"><img class="status-desativado" src="<?= $loja['site'] ?>imagens/status-inativo.png" alt="Inativo"></a><span class="d-none">desligado off inativo 0</span></span>
                                            <?php } ?>
                                            <?php if($avaliacao_compra['lida'] == 0){ ?>
                                                <img class="img-email-nao-lido" src="<?= $loja['site'] ?>imagens/email-nao-lido.png" title="Não lido">
                                            <?php } else { ?>
                                                <img class="img-email-lido" src="<?= $loja['site'] ?>imagens/email-lido.png" title="Lido">
                                            <?php } ?>
                                            <?php if($avaliacao_compra['replica'] == ''){ ?>
                                                <a class="botao-replica align-middle ml-1" href="javascript: replica('<?= $avaliacao_compra['identificador'] ?>','<?= $avaliacao_compra['replica'] ?>');" title="Réplica"><img class="img-replica" src="<?= $loja['site'] ?>imagens/comente.png" ></a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
				
            </div>
    
            <div class="tab-pane" id="conteudo-tab-produto" role="tabpanel" aria-labelledby="tab-produto">

                <!-- ROW DA TABELA -->
                <div class="row">
                    <div class="col-12">   
                        <table id="admin-lista-dois" class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">NOTA</th>
                                    <th scope="col" class="d-none d-lg-table-cell">DATA</th>
                                    <th scope="col" class="d-none d-lg-table-cell">CLIENTE</th>
                                    <th scope="col" class="d-none d-lg-table-cell">PRODUTO</th>
                                    <th scope="col" class="d-none d-lg-table-cell">COMENTÁRIO</th>
                                    <th scope="col" class="d-none d-lg-table-cell">RÉPLICA</th>
                                    <th scope="col" class="text-right">AÇÕES</th>
                                </tr>
                            </thead>
                            <tbody>      
                                <?php while($avaliacao_produto = mysqli_fetch_array($busca_avaliacoes_produto)){ ?> 
                                    <tr id="avaliacao-<?= $avaliacao_produto['identificador'] ?>" class="cursor-pointer <?php if($avaliacao_produto['lida'] == 0){ echo 'comentario-nao-lido'; } ?>" title="Visualizar" onclick="javascript: visualiza('<?= $avaliacao_produto['nota'] ?>','<?= date('d/m/Y H:i', strtotime($avaliacao_produto['data_cadastro']))?>','<?= preg_replace( "/\r|\n/", " ", addslashes(str_replace('"','\'',$avaliacao_produto['comentario']))) ?>','<?= $avaliacao_produto['lida'] ?>','<?= $avaliacao_produto['identificador'] ?>','<?= $avaliacao_produto['nome_produto'] ?>','<?= addslashes(str_replace('"','\'',$avaliacao_produto['replica'])) ?>','<?= date('d/m/Y H:i', strtotime($avaliacao_produto['data_replica'])) ?>','<?= $avaliacao_produto['cliente_nome'].' '.$avaliacao_produto['cliente_sobrenome'] ?>');">
                                        <td class="text-capitalize avaliacao-loja">   
                                            <span class="d-none">Nota <?= $avaliacao_produto['nota'] ?></span>                                         
                                            <ul>
                                                <li><img class="estrela <?php if($avaliacao_produto['nota'] >= 1){ echo 'img-dourada'; } ?>" estrela="1" id="estrela-1" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito ruim" alt="1 estrela"></li>
                                                <li><img class="estrela <?php if($avaliacao_produto['nota'] >= 2){ echo 'img-dourada'; } ?>" estrela="2" id="estrela-2" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Ruim" alt="2 estrelas"></li>
                                                <li><img class="estrela <?php if($avaliacao_produto['nota'] >= 3){ echo 'img-dourada'; } ?>" estrela="3" id="estrela-3" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Regular" alt="3 estrelas"></li>
                                                <li><img class="estrela <?php if($avaliacao_produto['nota'] >= 4){ echo 'img-dourada'; } ?>" estrela="4" id="estrela-4" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Boa" alt="4 estrelas"></li>
                                                <li><img class="estrela <?php if($avaliacao_produto['nota'] >= 5){ echo 'img-dourada'; } ?>" estrela="5" id="estrela-5" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito boa" alt="5 estrelas"></li>
                                            </ul>
                                        </td> 
                                        <td class="text-capitalize d-none d-lg-table-cell"><?= date('d/m/Y', strtotime($avaliacao_produto['data_cadastro'])) ?></td>    
                                        <td class="d-none d-lg-table-cell"><?= $avaliacao_produto['cliente_nome'].' '.$avaliacao_produto['cliente_sobrenome'] ?></td>
                                        <td class="text-capitalize d-none d-lg-table-cell"><?= mb_strimwidth($avaliacao_produto['nome_produto'], 0, 15, "...") ?></td>    
                                        <td class="d-none d-lg-table-cell"><?= mb_strimwidth($avaliacao_produto['comentario'], 0, 15, "...") ?></td>
                                        <td class="d-none d-lg-table-cell"><?= mb_strimwidth($avaliacao_produto['replica'], 0, 15, "...") ?></td>
                                        <td class="text-right avaliacao-acoes">
                                            <?php if($avaliacao_produto['mostrar_avaliacao'] == 1){ ?>
                                                <span class="text-left align-middle mr-1" id="status-<?= $avaliacao_produto['identificador'] ?>"><a class="botao-status" href="javascript: trocaStatus('<?= $avaliacao_produto['identificador'] ?>',<?= $avaliacao_produto['mostrar_avaliacao'] ?>)" title="Desativar"><img class="status-ativado" src="<?= $loja['site'] ?>imagens/status-ativo.png" alt="Ativo"></a><span class="d-none">ligado on ativo 1</span></span>
                                            <?php } else { ?>
                                                <span class="text-left align-middle mr-1" id="status-<?= $avaliacao_produto['identificador'] ?>"><a class="botao-status" href="javascript: trocaStatus('<?= $avaliacao_produto['identificador'] ?>',<?= $avaliacao_produto['mostrar_avaliacao'] ?>)" title="Ativar"><img class="status-desativado" src="<?= $loja['site'] ?>imagens/status-inativo.png" alt="Inativo"></a><span class="d-none">desligado off inativo 0</span></span>
                                            <?php } ?>
                                            <?php if($avaliacao_produto['lida'] == 0){ ?>
                                                <img class="img-email-nao-lido" src="<?= $loja['site'] ?>imagens/email-nao-lido.png" title="Não lido">
                                             <?php } else { ?>
                                                <img class="img-email-lido" src="<?= $loja['site'] ?>imagens/email-lido.png" title="Lido">
                                            <?php } ?>
                                            <?php if($avaliacao_produto['replica'] == ''){ ?>
                                                <a class="botao-replica align-middle ml-1" href="javascript: replica('<?= $avaliacao_produto['identificador'] ?>','<?= $avaliacao_produto['replica'] ?>');" title="Réplica"><img class="img-replica" src="<?= $loja['site'] ?>imagens/comente.png" ></a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

    </div>

</section>

<!-- MODAL VISUALIZAÇÃO DE COMENTÁRIO -->
<div class="modal fade" id="modal-visualizacao-comentario" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <ul class="mb-0">
                <li><h5 class="modal-title mb-1" id="exampleModalLongTitle">Avaliação</h5></li>
            </ul>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div id="visualizacao-comentario-nota" class="col-12"></div>
                <div id="visualizacao-comentario-data" class="col-12 mt-2"></div>
                <div id="visualizacao-comentario-cliente" class="col-12 mt-3"></div>
                <div id="visualizacao-comentario-produto" class="col-12"></div>
                <div id="visualizacao-comentario-comentario" class="col-12"></div>
                <div id="visualizacao-comentario-replica-container"class="col-12 mt-2">
                    <ul>
                        <li id="visualizacao-comentario-replica-titulo">Réplica:</li>
                        <li id="visualizacao-comentario-replica"></li>
                    </ul>
                </div>
            </div>
        </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL RÉPLICA -->
<div class="modal fade" id="modal-replica" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <ul class="mb-0">
                <li><h5 class="modal-title mb-1" id="exampleModalLongTitle">Réplica</h5></li>
            </ul>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form action="modulos/avaliacoes/php/replica.php" method="POST">
            <input type="hidden" name="visualizacao-comentario-identificador" id="visualizacao-comentario-identificador">
            <div class="modal-body">                
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="replica">Réplica</label>
                            <textarea name="replica" id="replica" rows="5" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-dark">Editar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php /* LIMPA A SESSION DE RETORNO */ unset($_SESSION['RETORNO']); ?>

<!--SCRIPTS-->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="modulos/avaliacoes/js/scripts.js"></script>