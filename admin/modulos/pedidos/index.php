<!--CSS-->
<script>abreLoader();</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="modulos/pedidos/css/style.css">
                  
<?php 

//VERIFICA SE ESTÁ ATIVO O MODO WHATS E WHATS SIMPLES PARA REDIRECIONAR PARA O LUGAR CERTO
if($modo_whatsapp){
    if($modo_whatsapp_simples){
        echo "<script>location.href='produtos.php';</script>";
    } else {
        echo "<script>location.href='orcamentos.php';</script>";
    }
}

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//VERIFICA SE TEM PIX VENCIDO E CANCELA
$pix_vencidos = date('Y-m-d', strtotime("-7 day", time()));
$busca_pagamentos_pix = mysqli_query($conn, "
    SELECT p.id AS pedido_id, p.status AS pedido_status
    FROM pagamento_pagseguro AS pp
    LEFT JOIN pedido AS p ON pp.id_pedido = p.id
    WHERE pp.tipo = 'PIX' AND p.status = 1 AND pp.data_cadastro <= DATE('$pix_vencidos') AND pp.asaas = 0
");
while($pagamentos_pix = mysqli_fetch_array($busca_pagamentos_pix)){
    mysqli_query($conn, "UPDATE pedido SET status = 7 WHERE id = ".$pagamentos_pix['pedido_id']);
}
$pix_pagos = date('Y-m-d');
$busca_pagamentos_pix = mysqli_query($conn, "
    SELECT p.id AS pedido_id, p.status AS pedido_status
    FROM pagamento_pagseguro AS pp
    LEFT JOIN pedido AS p ON pp.id_pedido = p.id
    WHERE pp.tipo = 'PIX' AND p.status = 3 AND pp.data_cadastro <= DATE('$pix_pagos') AND pp.asaas = 0
");
while($pagamentos_pix = mysqli_fetch_array($busca_pagamentos_pix)){
    mysqli_query($conn, "UPDATE pedido SET status = 4 WHERE id = ".$pagamentos_pix['pedido_id']);
}

//BUSCA TODAS OS PEDIDOS CADASTRADOS
$pedidos = mysqli_query($conn, '
    SELECT p.codigo, p.identificador, p.data_cadastro, p.status, c.identificador AS identificador_cliente, c.cpf AS cpf_cliente, c.nome AS nome_cliente, c.sobrenome AS sobrenome_cliente, c.celular AS celular_cliente, ps.nome AS nome_status, ps.cor AS cor_status, pp.tipo AS forma_pagamento, pp.asaas
    FROM pedido AS p 
    LEFT JOIN pedido_status AS ps ON p.status = ps.id_status
    LEFT JOIN pagamento_pagseguro AS pp ON p.id = pp.id_pedido
    INNER JOIN cliente AS c ON p.id_cliente = c.id
    WHERE p.status != 0
    ORDER BY p.data_cadastro DESC
'); 

$busca_pagamento = mysqli_query($conn, "SELECT pagseguro_status, asaas_status FROM pagamento WHERE id = 1");
$pagamento       = mysqli_fetch_array($busca_pagamento);

?>

<!--SECTION PEDIDOS-->
<section id="pedidos">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-6">    
                <div id="admin-titulo-pagina">Pedidos</div>
            </div>
            <?php if($pagamento['pagseguro_status'] == 1 & $pagamento['asaas_status'] == 0){ ?>
                <div class="col-6 text-right">    
                    <button type="button" class="btn btn-dark btn-top-right" data-toggle="modal" data-target="#modal-status">ENTENDER STATUS</button>
                </div>
            <?php } ?>
        </div>

        <!-- ROW DA TABELA -->
        <div class="row">
            <div class="col-12">   
                <table id="admin-lista" class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">CÓDIGO</th>
                            <th scope="col">DATA</th>
                            <th scope="col" class="d-none d-md-table-cell">CLIENTE</th>  
                            <th scope="col" class="d-none d-md-table-cell">PAGAMENTO</th>  
                            <th scope="col" class="d-none d-md-table-cell text-right">STATUS</th>
                            <th scope="col" class="d-none d-md-table-cell text-right">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>      
                        <?php while($pedido = mysqli_fetch_array($pedidos)){ ?>  
                            <tr class="cursor-pointer" title="Editar" onclick="javascript: edita('<?= $pedido['identificador'] ?>');">
                                <td class="text-capitalize codigo-pedido"><?= $pedido['codigo'] ?></td>
                                <td class="text-capitalize"><?= date('d/m/Y', strtotime($pedido['data_cadastro'])) ?></td>
                                <?php if(strlen($pedido['cpf_cliente']) == 18){ ?>
                                    <td class="text-capitalize d-none d-md-table-cell"><?= $pedido['nome_cliente']  ?></td> 
                                <?php } else { ?>
                                    <td class="text-capitalize d-none d-md-table-cell"><?= $pedido['nome_cliente'].' '.$pedido['sobrenome_cliente']  ?></td> 
                                <?php } ?>
                                <td class="text-uppercase d-none d-md-table-cell"><?= $pedido['forma_pagamento'] ?></td> 
                                <td class="text-capitalize d-none d-md-table-cell text-right pedido-status" style='color: <?= $pedido['cor_status'] ?>;'><?= $pedido['nome_status'] ?></td>
                                <td class="d-none d-md-table-cell text-right">
                                    <?php if($pedido['status'] == 1 & $pedido['forma_pagamento'] == 'PIX' & $pedido['asaas'] == 0){ ?> 
                                        <a href="javascript: modalComprovantePix('<?= $pedido['identificador'] ?>');" class="acao-upload-comprovante" title="Marcar como pago"><img src="<?= $loja['site'] ?>imagens/acao-upload.png"></a> 
                                    <?php } ?>
                                    <a class="botao-email" href="clientes-email.php?id=<?= $pedido['identificador_cliente'] ?>&acao=confirmacao-retirada" title="Enviar e-mail"><img class="acao-email" src="<?= $loja['site'] ?>imagens/acao-email.png" alt="E-mail"></a>
                                    <a href="https://wa.me/55<?= preg_replace("/[^0-9]/", "",$pedido['celular_cliente']) ?>" class="acao-whats" title="Contatar pelo whats" target="_blank"><img src="<?= $loja['site'] ?>imagens/acao-whats.png"></a> 
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</section>
	
<!-- MODAL STATUS -->
<div class="modal fade" id="modal-status" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Status</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <?php $busca_status = mysqli_query($conn, "SELECT * FROM pedido_status"); ?>
            <?php while($status = mysqli_fetch_array($busca_status)){ ?>
                <div class="mb-4">
                    <ul>
                        <li class="mb-2" style="color: <?= $status['cor'] ?>;"><b><?= $status['nome'] ?></b></li>
                        <li><?= $status['descricao'] ?></li>
                    </ul>
                </div>
            <?php } ?>
        </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL COMPROVANTE PIX -->
<div class="modal fade" id="modal-comprovante-pix" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <ul class="mb-0">
                <li><h5 class="modal-title mb-1" id="exampleModalLongTitle">Upload do comprovante</h5></li>
                <li><p class="modal-subtitle">Para alterar o status de pagamento de uma compra por PIX é necessário, por segurança, o upload do comprovante de pagamento enviado pelo cliente.</p></li>
            </ul>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="form-cadastro-comprovante-pix" enctype="multipart/form-data" action="modulos/pedidos/php/cadastro-comprovante-pix.php" method="POST">
                <div class="row">
                    <div class="col-12">
                        <input type="hidden" name="identificador-pedido-pix" id="identificador-pedido-pix" value="">
                        <div class="form-group">
                            <label for="imagem">Arquivo <span class="campo-obrigatorio">*</span></label>
                            <input type="file" name="imagem" id="imagem" class="imagem form-control-file" accept="image/png, image/jpeg, application/pdf" onchange="javascript: inputFileChange();">
                            <input type="text" name="arquivo" id="arquivo" class="arquivo" placeholder="Selecionar arquivo" readonly="readonly">
                            <input type="button" id="btn-escolher" class="btn-escolher" value="ESCOLHER" onclick="javascript: inputFileEscolher();">
                            <small>Formatos aceitos: PNG, JPG E PDF</small>
                        </div>
                        <div class="form-group">
                            <label for="senha-pix">Senha <span class="campo-obrigatorio">*</span></label>
                            <input type="password" name="senha-pix" id="senha-pix" class="form-control" required>
                            <small>Por segurança digite a sua senha de acesso ao sistema</small>
                        </div>
                    </div>
                </div>
            </form>
        </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Fechar</button>
				<button type="button" class="btn btn-dark btn-add-rastreamento" onclick="javascript: $('#form-cadastro-comprovante-pix').submit();">Adicionar</button>
            </div>
        </div>
    </div>
</div>

<!--SCRIPTS-->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="modulos/pedidos/js/scripts.js"></script>