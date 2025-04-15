<!--CSS-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="modulos/pedidos/css/style.css">
                  
<?php 

if(isset($_SESSION['RETORNO'])){
    if($_SESSION['RETORNO']['ERRO']){
        echo "<script>mensagemAviso('erro', '".$_SESSION['RETORNO']['status']."', 10000);</script>";
    } else {
        echo "<script>mensagemAviso('sucesso', '".$_SESSION['RETORNO']['status']."', 10000);</script>";
    }
    unset($_SESSION['RETORNO']);
}

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//PEGA O IDENTIFICADOR DO PEDIDO NA URL
$identificador_pedido  = FILTER_INPUT(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

//BUSCA OS DADOS DO PEDIDO
$busca_pedido  = mysqli_query($conn, "
    SELECT p.id, p.identificador AS identificador_pedido, p.id_carrinho, p.id_cliente, p.codigo, p.endereco, p.data_cadastro, ps.nome AS nome_status, ps.cor AS cor_status
    FROM pedido AS p 
    LEFT JOIN pedido_status AS ps ON p.status = ps.id_status
    WHERE p.identificador = '$identificador_pedido'
");
$pedido = mysqli_fetch_array($busca_pedido);

//BUSCA OS DADOS DO CLIENTE
$busca_cliente = mysqli_query($conn, "SELECT * FROM cliente WHERE id = ".$pedido['id_cliente']);
$cliente       = mysqli_fetch_array($busca_cliente);

//BUSCA OS DADOS DO PAGAMENTO
$busca_pagamento = mysqli_query($conn, "SELECT * FROM pagamento_pagseguro WHERE id_pedido = ".$pedido['id']);
$pagamento       = mysqli_fetch_array($busca_pagamento);

//BUSCA O CARRINHO DO PEDIDO
$carrinho = mysqli_query($conn, "
    SELECT c.identificador AS carrinho_identificador, cp.identificador AS carrinho_produto_identificador, cp.id_produto AS produto_id, cp.quantidade AS produto_quantidade, cp.ids_caracteristicas AS produto_caracteristicas, cp.preco AS produto_preco, p.nome AS produto_nome,
    (SELECT pi.imagem FROM produto_imagem AS pi WHERE p.id = pi.id_produto AND pi.capa = 1) AS produto_imagem
    FROM carrinho AS c
    INNER JOIN carrinho_produto AS cp ON c.id = cp.id_carrinho
    INNER JOIN produto AS p ON p.id = cp.id_produto
    WHERE cp.status = 1 AND c.id = '".$pedido['id_carrinho']."'
");

$n_itens         = mysqli_num_rows($carrinho);
$contador_itens  = 0;

//VERIFICA SE FOI USADO CUPOM
$busca_cupom = mysqli_query($conn, "
    SELECT c.nome, c.valor, c.tipo
    FROM cupom AS c
    RIGHT JOIN cupom_uso AS cu ON cu.id_cupom = c.id
    WHERE cu.id_pedido = ".$pedido['id']
); 

?>

<!--SECTION PEDIDO-->
<section id="pedido">

    <div class="container-fluid">

        <div id="logo-para-impressao" class="row">
            <div class="col-12"> 
                <img src="<?= $loja['site'] ?>imagens/logo-admin.png">
            </div>
        </div>

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div id="pedido-titulo" class="col-4">    
                <div id="admin-titulo-pagina">Pedido</div>
            </div>
            <div class="col-8 text-right">
                <a href="https://wa.me/55<?= preg_replace("/[^0-9]/", "",$cliente['celular']) ?>" class="acao-whats mr-2" title="Contatar pelo whats" target="_blank"><img src="<?= $loja['site'] ?>imagens/acao-whats.png"></a> 
                <button id="btn-imprimir-pedido" type="button" class="btn btn-dark btn-top-right" onclick="javascript: imprimirPedido();">IMPRIMIR</button>
                <button type="button" class="btn btn-dark btn-top-right ml-0 ml-md-2" onclick="javascript: window.location.href = 'pedidos.php';">VOLTAR</button>
            </div>
        </div>
    
        <div class="row">

            <div class="col-12 col-xl-8">

                <div id="cliente-pedidos-resumo">
                
                    <div class="cliente-pedidos-resumo-status"> 
                        <div class="cliente-pedidos-resumo-informacao">
                            <span>Codigo:</span>
                            <span class="codigo-pedido"><?= $pedido['codigo'] ?></span>
                        </div>  
                        <div class="cliente-pedidos-resumo-informacao">
                            <span>Data:</span>
                            <span><?= date('d/m/Y H:i', strtotime($pedido['data_cadastro'])) ?></span>
                        </div>  
                    </div>
                    
                    <div class="cliente-pedidos-resumo-status"> 
                        <div class="cliente-pedidos-resumo-informacao">
                            <span>Status:</span>
                            <span style="color: <?= $pedido['cor_status'] ?>;"><?= $pedido['nome_status'] ?></span>
                        </div>  
                    </div>
                    
                    <div class="cliente-pedidos-resumo-valores">                          
                        <div class="cliente-pedidos-resumo-informacao">
                            <span>Tipo frete:</span>
                            <span><?= $pagamento['tipo_frete'] ?></span>
                        </div>    
                    </div>

                    <div class="cliente-pedidos-resumo-valores">        
                        <div class="cliente-pedidos-resumo-informacao">
                            <span>Produtos:</span>
                            <span>R$ <?= number_format($pagamento['valor_produtos'],2,',','.') ?></span>
                        </div>          
                        <div class="cliente-pedidos-resumo-informacao <?php if($pagamento['valor_desconto'] == 0){ echo 'd-none'; } ?>">
                            <span>Desconto:</span>
                            <span>R$ <?= number_format($pagamento['valor_desconto'],2,',','.') ?></span>
                        </div>           
                        <div class="cliente-pedidos-resumo-informacao">
                            <span>Frete:</span>
                            <span>R$ <?= number_format($pagamento['valor_frete'],2,',','.') ?></span>
                        </div>        
                        <div class="cliente-pedidos-resumo-informacao <?php if($pagamento['valor_juros'] == 0){ echo 'd-none'; } ?>">
                            <span>Juros:</span>
                            <span>R$ <?= number_format($pagamento['valor_juros'],2,',','.') ?></span>
                        </div>        
                        <div class="cliente-pedidos-resumo-informacao">
                            <span>Total:</span>
                            <span>R$ <?= number_format(($pagamento['parcelas']*$pagamento['valor_parcela']),2,',','.') ?></span>
                        </div>     
                    </div>
                    
                    <?php if(mysqli_num_rows($busca_cupom) > 0){ ?>
                        <?php $cupom = mysqli_fetch_array($busca_cupom); ?>
                        <div class="cliente-pedidos-resumo-valores">   
                            <div class="cliente-pedidos-resumo-informacao">
                                <span>Cupom:</span>
                                <span><?= $cupom['nome'] ?></span>
                            </div>   
                            <div class="cliente-pedidos-resumo-informacao">
                                <span>Tipo do desconto:</span>
                                <?php if($cupom['tipo'] == 'P'){ ?>
                                    <span><?= $cupom['valor'].'%' ?></span>
                                <?php } else if($cupom['tipo'] == 'V'){ ?>
                                    <span><?= 'R$ '.number_format($cupom['valor'],2,',','.') ?></span>
                                <?php } ?>
                            </div>   
                        </div>
                    <?php } ?>
                    
                    <div class="cliente-pedidos-resumo-forma-pagamento"> 
                        <div class="cliente-pedidos-resumo-informacao">
                            <span>Forma:</span>
                            <span>
                                <?php 
                                if($pagamento['asaas'] == 0){
                                    if($pagamento['tipo'] == 'BOLETO'){ 
                                        echo '<a href="'.$pagamento['boleto'] .'" target="_blank" class="link-underline" title="Link do boleto">Boleto</a>'; 
                                    } else if ($pagamento['tipo'] == 'CARTAO'){ 
                                        echo 'Cartão de Crédito';
                                    } else if ($pagamento['tipo'] == 'PIX'){ 
                                        echo 'PIX'; 
                                    } 
                                } else {
                                    if($pagamento['tipo'] == 'BOLETO'){ 
                                        echo '<a href="'.$pagamento['asaas_link_fatura'] .'" target="_blank" class="link-underline" title="Link da fatura">Boleto</a>'; 
                                    } else if ($pagamento['tipo'] == 'CARTAO'){ 
                                        if($pagamento['asaas_erro'] == ''){
                                            echo '<a href="'.$pagamento['asaas_link_fatura'] .'" target="_blank" class="link-underline" title="Link da fatura">Cartão de Crédito</a>';
                                        } else {
                                            echo 'Cartão de Crédito';
                                        }
                                    } else if ($pagamento['tipo'] == 'PIX'){ 
                                        echo '<a href="'.$pagamento['asaas_link_fatura'] .'" target="_blank" class="link-underline" title="Link da fatura">PIX</a>'; 
                                    } 
                                }
                                ?>
                            </span>
                        </div>   
                        <?php if($pagamento['parcelas'] != 1){ ?>
                            <div class="cliente-pedidos-resumo-informacao">
                                <span>Parcelas:</span>
                                <span><?= $pagamento['parcelas'].'x' ?> R$ <?= number_format($pagamento['valor_parcela'],2,',','.') ?></span>
                            </div>   
                        <?php } else { ?>
                            <div class="cliente-pedidos-resumo-informacao">
                                <span>Parcelas:</span>
                                <span>À Vista</span>
                            </div>  
                        <?php } ?>   
                        <?php if ($pagamento['asaas'] == 1 & $pagamento['tipo'] == 'CARTAO' & $pagamento['asaas_erro'] != ''){ ?>
                            <div class="cliente-pedidos-resumo-informacao mt-2">
                                <span>Erro: <?= $pagamento['asaas_erro'] ?></span>
                            </div>  
                        <?php } ?>
                        <?php if($pagamento['asaas'] == 0){ ?>                         
                            <?php if($pagamento['tipo'] == 'PIX' & $pagamento['comprovante_pagamento'] != ''){ ?> 
                                <div class="cliente-pedidos-resumo-informacao">
                                    <span>Comprovante:</span>
                                    <span><a href="<?= $loja['site'].'imagens/pix/comprovantes/'.$pagamento['comprovante_pagamento'] ?>" target="_blank">Visualizar</a></span>
                                </div> 
                                <div class="cliente-pedidos-resumo-informacao">
                                    <?php 
                                        $busca_usuario = mysqli_query($conn, "SELECT nome FROM usuario WHERE identificador = '".$pagamento['comprovante_pagamento_por']."'"); 
                                        $usuario       = mysqli_fetch_array($busca_usuario);
                                    ?>
                                    <span>Adicionado por:</span>
                                    <span><?= $usuario['nome'] ?></span>
                                </div> 
                            <?php } else if($pagamento['tipo'] == 'PIX' & $pagamento['comprovante_pagamento'] == ''){ ?> 
                                <a href="javascript: modalComprovantePix('<?= $pedido['identificador_pedido'] ?>');" class="acao-upload-comprovante" title="Marcar como pago"><img src="<?= $loja['site'] ?>imagens/acao-upload.png"> Adicionar comprovante</a> 
                            <?php } ?>
                        <?php } ?>
                    </div>

                    <div class="cliente-pedidos-resumo-entrega"> 
                        <div class="cliente-pedidos-resumo-informacao">
                            <ul class="m-0">
                                <li>Cliente:</li>
                                <?php if(strlen($cliente['cpf']) == 18){ ?>
                                    <li><?= $cliente['nome'].' ('.$cliente['sobrenome'].')' ?></li>
                                <?php } else { ?>
                                    <li><?= $cliente['nome'].' '.$cliente['sobrenome'] ?></li>
                                <?php } ?>
                                <li><?= $cliente['cpf']?></li>
                                <li><?= $cliente['celular']?></li>
                                <li><?= $cliente['email']?></li>
                            </ul>                          
                        </div>  
                    </div>
                    
                    <?php if($pagamento['tipo_frete'] != 'Retirar'){ ?>
                        <div class="cliente-pedidos-resumo-entrega"> 
                            <div class="cliente-pedidos-resumo-informacao">
                                Entrega em: <br /><?= $pedido['endereco'] ?>
                            </div>  
                        </div>
                    <?php } ?>

                </div>    

            </div>

            <div class="col-12 col-xl-4 mt-4 mt-xl-0">

                <?php while($produto = mysqli_fetch_array($carrinho)){ $contador_itens++; ?>
                    <?php
                        $carrinho_identificador = $produto['carrinho_identificador'];
                        if($produto['produto_imagem'] == ''){  $produto_imagem = $loja['site'].'imagens/produto_sem_foto.png';
                        } else { $produto_imagem = $loja['site'].'imagens/produtos/media/'.$produto['produto_imagem']; }
                    ?>
                    <div class="row cliente-pedidos-produto">
                        <div class="cliente-pedidos-produto-imagem-container col-4">
                            <div class="cliente-pedidos-produto-imagem" style="background-image: url('<?= $produto_imagem  ?>')"></div>
                        </div>
                        <div class="col-8">
                            <div class="cliente-pedidos-produto-texto">
                                <ul>
                                    <li class="cliente-pedidos-produto-texto-nome"><?= $produto['produto_nome'] ?></li>
                                    <?php 
                                        $caracteristicas = explode(',',$produto['produto_caracteristicas']);
                                        $n_caracteristicas = count($caracteristicas);
                                        if($n_caracteristicas > 0){
                                            $sql_caracteristicas = '';
                                            for($i = 0; $i < $n_caracteristicas; $i++){
                                                if($i == 0){
                                                    $sql_caracteristicas .= "pc.id = ".$caracteristicas[$i];
                                                } else {
                                                    $sql_caracteristicas .= " OR pc.id = ".$caracteristicas[$i];
                                                }
                                            }
                                            $busca_caracteristicas = mysqli_query($conn, "
                                                SELECT a.nome AS atributo_nome, c.nome AS caracteristica_nome 
                                                FROM produto_caracteristica AS pc
                                                INNER JOIN atributo AS a ON pc.id_atributo = a.id
                                                INNER JOIN caracteristica AS c ON pc.id_caracteristica = c.id
                                                WHERE ".$sql_caracteristicas
                                            );
                                            while($caracteristica = mysqli_fetch_array($busca_caracteristicas)){
                                                ?><li class="cliente-pedidos-produto-texto-caracteristicas text-uppercase"><?= $caracteristica['atributo_nome'].": ".$caracteristica['caracteristica_nome'] ?></li><?php
                                            }
                                        }
                                    ?>
                                    <li class="cliente-pedidos-produto-texto-preco">R$ <?= number_format(($produto['produto_preco']),2,',','.') ?></li>
                                    <li class="cliente-pedidos-produto-texto-quantidade">Quantidade: <?= $produto['produto_quantidade'] ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php if($contador_itens != $n_itens){ ?>
                        <div class="row cliente-pedidos-separador"><div class="col-12"><hr></div></div>     
                    <?php } ?>   
                <?php } ?>   

            </div>

        </div>

    </div>
        
    <input type="hidden" id="carrinho" value="<?= $carrinho_identificador ?>">

</section>

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