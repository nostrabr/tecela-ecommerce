<!--CSS-->
<script>abreLoader();</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.3/css/select.dataTables.min.css">
<link rel="stylesheet" href="modulos/envios/css/style.css">
                  
<?php 

//VERIFICA SE ESTÁ ATIVO O MODO WHATS E WHATS SIMPLES PARA REDIRECIONAR PARA O LUGAR CERTO
if(!$modo_envios){
    echo "<script>location.href='logout.php';</script>";
}

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
$trintadias            = date('Y-m-d', strtotime("-30 day", strtotime(date('Y-m-d'))));

//BUSCA TODAS OS PEDIDOS CADASTRADOS
$pedidos_enviar   = mysqli_query($conn, '
    SELECT DISTINCT p.codigo, p.identificador, p.data_cadastro, pfc.melhor_envio_id_etiqueta, pfc.melhor_envio_codigo_envio, c.nome AS cliente_nome, c.sobrenome AS cliente_sobrenome, cid.nome AS cidade_nome, est.sigla AS estado_uf
    FROM pedido AS p 
    LEFT JOIN pedido_frete AS pf ON p.id = pf.id_pedido
    LEFT JOIN pedido_frete_pacote AS pfc ON pf.id = pfc.id_pedido_frete
    LEFT JOIN cliente AS c ON c.id = p.id_cliente
    LEFT JOIN cliente_endereco AS ce ON ce.id = p.id_endereco
    LEFT JOIN cidade AS cid ON ce.cidade = cid.id
    LEFT JOIN estado AS est ON ce.estado = est.id
    WHERE (p.status = 3 OR p.status = 4) AND (pfc.status = 0) AND (p.data_cadastro > "'.$trintadias.'")
    ORDER BY p.data_cadastro DESC
');

$pedidos_enviados = mysqli_query($conn, '
    SELECT DISTINCT p.codigo, p.identificador, p.data_cadastro, pfc.melhor_envio_id_etiqueta, pfc.melhor_envio_codigo_envio, c.nome AS cliente_nome, c.sobrenome AS cliente_sobrenome, cid.nome AS cidade_nome, est.sigla AS estado_uf
    FROM pedido AS p 
    LEFT JOIN pedido_frete AS pf ON p.id = pf.id_pedido
    LEFT JOIN pedido_frete_pacote AS pfc ON pf.id = pfc.id_pedido_frete
    LEFT JOIN cliente AS c ON c.id = p.id_cliente
    LEFT JOIN cliente_endereco AS ce ON ce.id = p.id_endereco
    LEFT JOIN cidade AS cid ON ce.cidade = cid.id
    LEFT JOIN estado AS est ON ce.estado = est.id
    WHERE (p.status = 3 OR p.status = 4) AND (pfc.status = 2 OR pfc.status = 3)
    ORDER BY p.data_cadastro DESC
');

?>

<!--SECTION ENVIOS-->
<section id="envios">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Envios</div>
            </div>
            <div class="col-4 text-right">
                <a id="menu-carrinho-btn-carrinho" href="javascript: abrirCarrinhoEtiquetas(1);">
                    <img id="menu-carrinho-img-cesta" src="<?= $loja['site'] ?>imagens/shopping-basket.png" alt="Carrinho">      
                </a>
                <span id="menu-carrinho-quantidade"></span>
            </div>
        </div>

        <!-- ABAS -->
        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tab-melhor-envio" data-toggle="tab" href="#conteudo-tab-melhor-envio" role="tab" aria-controls="conteudo-tab-melhor-envio" aria-selected="true">Melhor Envio</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-enviar" data-toggle="tab" href="#conteudo-tab-enviar" role="tab" aria-controls="conteudo-tab-enviar" aria-selected="true">Gerar</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-enviados" data-toggle="tab" href="#conteudo-tab-enviados" role="tab" aria-controls="conteudo-tab-enviados" aria-selected="false">Gerados</a>
            </li>
        </ul>

        <div class="tab-content">

            <div class="tab-pane active" id="conteudo-tab-melhor-envio" role="tabpanel" aria-labelledby="tab-melhor-envio">

                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="envios-saldo-melhor-envio-completo"></div>
                    </div>
                    <div class="col-12 col-md-6 text-left text-md-right mt-2 mt-md-0">
                        <ul>
                            <li><a href="javascript: abreModalSaldo();" class="btn btn-sm btn-dark">ADICIONAR SALDO</a></li>
                        </ul>                        
                    </div>
                </div>      
                
                <div id="envios-opcoes">
                    <div class="row">
                        <div class="col-12">
                            <h5>Etiquetas</h5>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="mb-0" for="imprimir-etiqueta">Pesquisar</label>
                                <input type="text" class="form-control" id="pesquisar-etiqueta" name="pesquisar-etiqueta" placeholder="Código do pedido">
                                <button class="btn btn-dark btn-acao-pesquisar" onclick="javascript: pesquisarEtiquetas($('#pesquisar-etiqueta').val())"><img src="<?= $loja['site'] ?>imagens/acao-pesquisar.png"></button>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="mb-0" for="imprimir-etiqueta">Imprimir</label>
                                <input type="text" class="form-control" id="imprimir-etiqueta" name="imprimir-etiqueta" placeholder="Código do pedido">
                                <button class="btn btn-dark btn-acao-pesquisar" onclick="javascript: preVisualizarEtiqueta($('#imprimir-etiqueta').val())"><img src="<?= $loja['site'] ?>imagens/acao-pesquisar.png"></button>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="mb-0" for="imprimir-etiqueta">Cancelar</label>
                                <input type="text" class="form-control" id="cancelar-etiqueta" name="cancelar-etiqueta" placeholder="Código do pedido">
                                <button class="btn btn-dark btn-acao-pesquisar" onclick="javascript: cancelarEtiqueta($('#cancelar-etiqueta').val())"><img src="<?= $loja['site'] ?>imagens/acao-pesquisar.png"></button>
                            </div>
                        </div>
                    </div>   
                </div>       

            </div>

            <div class="tab-pane" id="conteudo-tab-enviar" role="tabpanel" aria-labelledby="tab-enviar">

                <div class="row">
                    <div class="col-12">                           
                        <select class="custom-select custom-select-sm" name="envio-acao" id="envio-acao">
                            <option value="" disabled selected>Ações</option>
                            <option value="add-carrinho">Adicionar ao carrinho</option>
                        </select>
                        <table id="admin-lista" class="table table-hover w-100">
                            <thead>
                                <tr>
                                    <th scope="col">                       
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="selectAll custom-control-input text-uppercase" id="selectAll" name="selectAll" value="all">
                                            <label class="custom-control-label" for="selectAll"></label>
                                        </div>
                                    </th>
                                    <th scope="col" class="d-none d-lg-table-cell">DATA</th>
                                    <th scope="col">PEDIDO</th>
                                    <th scope="col">CLIENTE</th>
                                    <th scope="col" class="d-none d-lg-table-cell">DESTINO</th>
                                </tr>
                            </thead>
                            <tbody>      
                                <?php while($pedido = mysqli_fetch_array($pedidos_enviar)){ ?>  
                                    <tr id="<?= $pedido['identificador'] ?>" class="cursor-pointer">
                                        <td></td>
                                        <td class="text-capitalize d-none d-lg-table-cell"><?= date('d/m/Y', strtotime($pedido['data_cadastro'])) ?></td>
                                        <td class="text-capitalize codigo-pedido"><?= $pedido['codigo'] ?></td>    
                                        <td class="text-capitalize"><?= $pedido['cliente_nome'].' '.$pedido['cliente_sobrenome'] ?></td>
                                        <td class="text-capitalize d-none d-lg-table-cell"><?= $pedido['cidade_nome'].'/'.$pedido['estado_uf'] ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>            

            <div class="tab-pane" id="conteudo-tab-enviados" role="tabpanel" aria-labelledby="tab-enviados">

                <div class="row">
                    <div class="col-12">   
                        <table id="admin-lista-dois" class="table table-hover w-100">
                            <thead>
                                <tr>
                                    <th scope="col" class="d-none d-lg-table-cell">DATA</th>
                                    <th scope="col">PEDIDO</th>
                                    <th scope="col" class="d-none d-lg-table-cell">ETIQUETA</th>
                                    <th scope="col">CLIENTE</th>
                                    <th scope="col" class="d-none d-lg-table-cell">DESTINO</th>
                                    <th scope="col" class="text-right">AÇÕES</th>
                                </tr>
                            </thead>
                            <tbody>      
                                <?php while($pedido = mysqli_fetch_array($pedidos_enviados)){ ?>  
                                    <tr>
                                        <td class="text-capitalize d-none d-lg-table-cell"><?= date('d/m/Y', strtotime($pedido['data_cadastro'])) ?></td>
                                        <td class="text-uppercase codigo-pedido"><?= $pedido['codigo'] ?></td>    
                                        <td class="text-uppercase codigo-pedido d-none d-lg-table-cell"><?= $pedido['melhor_envio_codigo_envio'] ?></td>    
                                        <td class="text-capitalize"><?= $pedido['cliente_nome'].' '.$pedido['cliente_sobrenome'] ?></td>
                                        <td class="text-capitalize d-none d-lg-table-cell"><?= $pedido['cidade_nome'].'/'.$pedido['estado_uf'] ?></td>
                                        <td class="text-right">
                                            <a href="javascript: preVisualizarEtiqueta('<?= $pedido['codigo'] ?>')"><img class="img-visualizar" title="Visualizar" src="<?= $loja['site'] ?>imagens/acao-visualizar.png"></a>
                                            <a href="javascript: rastrearEtiqueta('<?= $pedido['melhor_envio_id_etiqueta'] ?>')"><img class="img-rastrear" src="<?= $loja['site'] ?>imagens/acao-rastreamento.png" alt="Rastrear" title="Rastrear etiqueta"></a>
                                            <a href="javascript: cancelarEtiqueta('<?= $pedido['codigo'] ?>');"><img class="img-cancelar" title="Cancelar" src="<?= $loja['site'] ?>imagens/acao-cancelar.png"></a>
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

    <input type="hidden" id="saldo-melhor-envio">
    <input type="hidden" id="reservado-melhor-envio">
    <input type="hidden" id="debito-melhor-envio">

</section>

<!-- MODAL CARRINHO -->
<div class="modal fade" id="modal-carrinho" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <ul class="mb-0">
                    <li><h5 class="modal-title" id="exampleModalLongTitle">Carrinho de etiquetas</h5></li>
                </ul>   
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="modal-carrinho-comprar-etiquetas" action="modulos/envios/php/comprar-fretes.php" method="POST" target="_blank">
                    <input type="hidden" id="ids-etiquetas-compra" name="ids-etiquetas-compra">
                    <table id="lista-etiquetas" class="table table-hover w-100">
                        <thead>
                            <tr>
                                <th scope="col">                       
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="selectAll2 custom-control-input text-uppercase" id="selectAll2" name="selectAll2" value="all">
                                        <label class="custom-control-label" for="selectAll2"></label>
                                    </div>
                                </th>
                                <th class="d-none"></th>
                                <th scope="col">PEDIDO</th>
                                <th scope="col" class="d-none d-lg-table-cell">ETIQUETA</th>
                                <th scope="col" class="d-none d-lg-table-cell">PARA</th>
                                <th scope="col" class="d-none d-lg-table-cell">PREÇO</th>
                                <th scope="col" class="text-right">AÇÕES</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <ul>
                    <li class="envios-saldo-melhor-envio-simples"></li>
                    <li class="modal-carrinho-total-selecionado"><div class="saldo-zerado">Total selecionado: R$ 0,00</div></li>
                </ul>
                <button id="modal-carrinho-btn-comprar" type="button" class="btn btn-dark" onclick="javascript: $('#modal-carrinho-comprar-etiquetas').submit();" disabled = "disabled">Comprar etiquetas selecionadas</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CARRINHO -->
<div class="modal fade" id="modal-visualizar-etiqueta" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <ul class="mb-0">
                    <li><h5 class="modal-title" id="exampleModalLongTitle">Etiqueta</h5></li>
                </ul>   
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<!-- MODAL CANCELAR ETIQUETAS -->
<div class="modal fade" id="modal-cancelar-etiqueta" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <ul class="mb-0">
                    <li><h5 class="modal-title" id="exampleModalLongTitle">Cancelar etiqueta</h5></li>
                    <li>
                        Após a compra da etiqueta, a mesma tem um período de tempo onde pode ser cancelada.<br>
                        Se for possível o cancelamento, o valor será estornado em 12 horas.
                    </li>
                </ul>   
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-cancelamento-etiqueta" action="modulos/envios/php/cancelar-etiqueta.php" method="POST">
                    <input type="hidden" name="codigo-pedido" id="codigo-pedido" required>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="descricao-cancelamento">Descrição do cancelamento <span class="campo-obrigatorio">*</span></label>
                                <textarea class="form-control" name="descricao-cancelamento" id="descricao-cancelamento" rows="5" required></textarea>
                                <small>Para cancelar uma etiqueta é necessária uma explicação.</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="senha">Senha <span class="campo-obrigatorio">*</span></label>
                                <input type="password" class="form-control" name="senha" id="senha" required>
                                <small>Por segurança digite a sua senha de acesso ao sistema</small>
                            </div>
                        </div>
                        <div class="col-12">                            
                            <button type="submit" class="btn btn-dark">Confirmar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL INSERÇÃO DE SALDO -->
<div class="modal fade" id="modal-inserir-saldo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <ul class="mb-0">
                    <li><h5 class="modal-title" id="exampleModalLongTitle">Inserir saldo</h5></li>
                    <li><div class="envios-saldo-melhor-envio-simples"></div></li>
                </ul>   
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-insercao-saldo-melhor-envio" action="modulos/envios/php/inserir-saldo.php" method="POST">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="valor">Valor <span class="campo-obrigatorio">*</span></label>
                                <input type="text" class="form-control" name="valor" id="valor" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="gateway">Gatway <span class="campo-obrigatorio">*</span></label>
                                <select type="text" class="form-control" name="gateway" id="gateway" required>
                                    <?php
                                    $busca_frete = mysqli_query($conn, "SELECT melhor_envio_ambiente FROM frete WHERE id = 1");
                                    $frete       = mysqli_fetch_array($busca_frete);
                                    if($frete['melhor_envio_ambiente'] == 'S'){ ?>
                                        <option value="pagseguro" selected>PagSeguro</option>
                                    <?php } else { ?>
                                        <option value="pagseguro" selected>PagSeguro</option>
                                        <option value="paypal">PayPal</option>
                                        <option value="mercado-pago">Mercado Pago</option>
                                        <option value="moip">Moip</option>
                                        <option value="picpay">PicPay</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="senha">Senha <span class="campo-obrigatorio">*</span></label>
                                <input type="password" class="form-control" name="senha" id="senha" required>
                                <small>Por segurança digite a sua senha de acesso ao sistema</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-dark">Continuar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!--SCRIPTS-->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
<script type="text/javascript" src="modulos/envios/js/scripts.js"></script>