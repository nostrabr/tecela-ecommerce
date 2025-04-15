<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/cliente/css/style.css">

<?php 

//PEGA IDENTIFICADOR DO CLIENTE
$identificador_cliente = filter_var($_SESSION['identificador']);
$identificador_pedido  = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

//BUSCA OS DADOS DO CLIENTE
$busca_cliente = mysqli_query($conn, "SELECT id FROM cliente WHERE identificador = '$identificador_cliente'");

//BUSCA OS DADOS DO PEDIDO
$busca_pedido  = mysqli_query($conn, "
    SELECT p.id, p.id_carrinho, p.codigo, p.endereco, p.data_cadastro, ps.id_status AS id_status, ps.nome AS nome_status, ps.cor AS cor_status
    FROM pedido AS p 
    LEFT JOIN pedido_status AS ps ON p.status = ps.id_status
    WHERE p.identificador = '$identificador_pedido'
");

//VERIFICA SE ENCONTROU O CLIENTE E O PEDIDO, SENÃO MANDA PRO LOGIN
if(mysqli_num_rows($busca_cliente) > 0 & mysqli_num_rows($busca_pedido) > 0 ){

//FETCH
$cliente = mysqli_fetch_array($busca_cliente);
$pedido  = mysqli_fetch_array($busca_pedido);

//BUSCA O CARRINHO DO PEDIDO
$carrinho = mysqli_query($conn, "
    SELECT cp.identificador AS carrinho_produto_identificador, cp.id_produto AS produto_id, cp.quantidade AS produto_quantidade, cp.ids_caracteristicas AS produto_caracteristicas, cp.preco AS produto_preco, p.nome AS produto_nome,
    (SELECT pi.imagem FROM produto_imagem AS pi WHERE p.id = pi.id_produto AND pi.capa = 1) AS produto_imagem
    FROM carrinho AS c
    INNER JOIN carrinho_produto AS cp ON c.id = cp.id_carrinho
    INNER JOIN produto AS p ON p.id = cp.id_produto
    WHERE cp.status = 1 AND c.id = '".$pedido['id_carrinho']."'
");

$n_itens         = mysqli_num_rows($carrinho);
$contador_itens  = 0;

//BUSCA OS DADOS DA CONFIGURAÇÃO DO PAGAMENTO
$busca_configuracao_pagamento = mysqli_query($conn, "SELECT * FROM pagamento WHERE id = 1");
$configuracao_pagamento       = mysqli_fetch_array($busca_configuracao_pagamento);

//BUSCA OS DADOS DO PAGAMENTO
$busca_pagamento = mysqli_query($conn, "SELECT * FROM pagamento_pagseguro WHERE id_pedido = ".$pedido['id']);
$pagamento       = mysqli_fetch_array($busca_pagamento);

$busca_frete     = mysqli_query($conn, "
    SELECT pfc.melhor_envio_id_etiqueta, pfc.melhor_envio_codigo_envio
    FROM pedido AS p 
    LEFT JOIN pedido_frete AS pf ON p.id = pf.id_pedido
    LEFT JOIN pedido_frete_pacote AS pfc ON pf.id = pfc.id_pedido_frete
    WHERE (p.status = 3 OR p.status = 4) AND pfc.status = 2 AND p.codigo = '".$pedido['codigo']."'
");

?>

<!--CLIENTE PEDIDOS-->
<section id="cliente-pedidos" class="cliente"> 

    <h1 class="d-none">Dados de pedido</h1>

    <!--MENU DE OPÇÕES-->
    <div class="row mb-4">
    
        <div class="col-12">

            <div id="cliente-menu">
            
                <ul>
                    <li id="cliente-menu-btn-cliente-dados" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-dados';">Cadastro</li>
                    <li id="cliente-menu-btn-cliente-acesso" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-acesso';">Acesso</li>
                    <li id="cliente-menu-btn-cliente-enderecos" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-enderecos';">Endereços</li>
                    <?php if(!$modo_whatsapp_simples){ ?>
                        <?php if($modo_whatsapp){ ?>
                            <li id="cliente-menu-btn-cliente-orcamentos" class="menu-cliente-ativo" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-orcamentos';">Orçamentos</li>
                        <?php } else { ?>
                            <li id="cliente-menu-btn-cliente-pedidos" class="menu-cliente-ativo" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-pedidos';">Pedidos</li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            
            </div>

        </div>

    </div>

    <div class="row">
        <div class="col-12 col-xl-8">
            <h2 class="subtitulo-pagina-central-h2">Resumo do pedido</h2>
            <p class="subtitulo-pagina-central-p">Código: <b class="codigo-pedido"><?= $pedido['codigo'] ?></b> - Em <?= date('d/m/Y H:i', strtotime($pedido['data_cadastro'])) ?></p>  
        </div>
        <div class="col-4 d-none d-xl-block">
            <h2 class="subtitulo-pagina-central-h2">Produtos</h2>
            <p class="subtitulo-pagina-central-p"></p>  
        </div>
    </div>    
    
    <div class="row">

        <div class="col-12 col-xl-8">

            <div id="cliente-pedidos-resumo">
            
                  
                <div class="cliente-pedidos-resumo-status"> 
                    <div class="cliente-pedidos-resumo-informacao">
                        <span>Status:</span>
                        <span style="color: <?= $pedido['cor_status'] ?>;"><?= $pedido['nome_status'] ?></span>
                    </div>  
                </div>

                <?php if($pedido['id_status'] == 1 & $pagamento['tipo'] == 'BOLETO'){ ?>

                    <?php 
                    
                    if($pagamento['asaas'] == 1){
                        $link_boleto = $pagamento['asaas_link_fatura'];
                    } else {
                        $link_boleto = $pagamento['boleto'];
                    }

                    ?>
                    
                    <div class="cliente-pedidos-resumo-status"> 
                        <div class="cliente-pedidos-resumo-informacao">
                            <span>Boleto:</span>
                            <span><a id="cliente-pedidos-btn-boleto" href="<?= $link_boleto ?>" target="_blank">ABRIR</a></span>
                        </div>  
                    </div>
                <?php } ?>

                <?php if(mysqli_num_rows($busca_frete) > 0){ ?>
                    <div class="cliente-pedidos-resumo-status cliente-pedidos-resumo-status-rastreamento">                         
                        <div class="cliente-pedidos-resumo-informacao">
                            <span>Rastreamento:</span>
                            <span>
                                <?php while($frete = mysqli_fetch_array($busca_frete)){ ?>
                                    <a href="javascript: rastrearEtiqueta('<?= $frete['melhor_envio_id_etiqueta'] ?>')"><img class="img-rastrear mr-1" src="<?= $loja['site'] ?>imagens/acao-rastreamento.png" alt="Rastrear" title="Rastrear etiqueta"><?= $frete['melhor_envio_codigo_envio'] ?></a>
                                <?php } ?>
                            </span>  
                        </div> 
                    </div>
                <?php } ?>

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
                    <div class="cliente-pedidos-resumo-informacao">
                        <span>Tipo:</span>
                        <span><?= $pagamento['tipo_frete'] ?></span>
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
                  
                <div class="cliente-pedidos-resumo-forma-pagamento"> 
                    <div class="cliente-pedidos-resumo-informacao">
                        <span>Forma:</span>
                        <span><?php if($pagamento['tipo'] == 'BOLETO'){ echo 'Boleto'; } else if ($pagamento['tipo'] == 'CARTAO'){ echo 'Cartão de Crédito'; } else if ($pagamento['tipo'] == 'PIX'){ echo 'PIX'; } ?></span>
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
                    <?php if($pagamento['tipo'] == 'PIX' & $pedido['id_status'] == 1 & ($configuracao_pagamento['pix'] == 1 | $pagamento['asaas'] == 1)){ ?>
                        <?php if($pagamento['asaas'] == 1){ ?>
                            <div class="cliente-pedidos-resumo-informacao">
                                <span>Chave PIX:</span>
                            </div>  
                            <div class="cliente-pedidos-resumo-informacao">
                                <textarea id="txt-copy" class="textarea-chave-pix"><?= $pagamento['asaas_pix_chave'] ?></textarea>
                            </div>  
                            <div class="cliente-pedidos-resumo-informacao">
                                <button id="btn-copy" onclick="copiarTexto()">Copiar Chave Pix</button>
                            </div>
                            <div class="cliente-pedidos-resumo-informacao">
                                <span>QRCode:</span>
                            </div>
                            <div class="cliente-pedidos-resumo-informacao">
                                <span><img id="cliente-pedido-img-qrcode-pix-asaas" src="data:image/png;base64,<?= $pagamento['asaas_pix_imagem'] ?>"></span>
                            </div> 
                            <div class="cliente-pedidos-resumo-informacao">
                                <span>Link para pagamento:</span>
                            </div>
                            <div class="cliente-pedidos-resumo-informacao">
                                <span><a href="<?= $pagamento['asaas_link_fatura'] ?>" target="_blank" class="link-pagamento-asaas"><i><?= $pagamento['asaas_link_fatura'] ?></i></a></span>
                            </div> 
                        <?php } else if($configuracao_pagamento['pix'] == 1){ ?>
                            <div class="cliente-pedidos-resumo-informacao">
                                <span>Chave PIX:</span>
                                <span><?= $configuracao_pagamento['pix_chave'] ?></span>
                            </div>  
                            <?php if($configuracao_pagamento['pix_qrcode'] != ''){ ?>
                                <div class="cliente-pedidos-resumo-informacao">
                                    <span><img id="cliente-pedido-img-qrcode-pix" src="<?= $loja['site'].'imagens/pix/'.$configuracao_pagamento['pix_qrcode'] ?>"></span>
                                </div>  
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>    
                </div>

                  
                <div class="cliente-pedidos-resumo-entrega"> 
                    <div class="cliente-pedidos-resumo-informacao">
                        Entrega em: <br /><?= $pedido['endereco'] ?>
                    </div>  
                </div>

            </div>    

        </div>

        <div class="col-12 col-xl-4 mt-4 mt-xl-0">

            <?php while($produto = mysqli_fetch_array($carrinho)){ $contador_itens++; ?>
                <?php
                    if($produto['produto_imagem'] == ''){  $produto_imagem = 'imagens/produto_sem_foto.png';
                    } else { $produto_imagem = 'imagens/produtos/media/'.$produto['produto_imagem']; }
                ?>
                <div class="row cliente-pedidos-produto">
                    <div class="col-4">
                        <div class="cliente-pedidos-produto-imagem" style="background-image: url('<?= $loja['site'].$produto_imagem ?>')"></div>
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

    <div id="cliente-pedidos-botoes" class="row">
        <div class="col-12 mt-4">
            <ul>
                <li><a id="cliente-pedidos-btn-voltar" href="<?= $loja['site'] ?>cliente-pedidos" class="btn-escuro">Voltar</a> </li>
                <li><a id="cliente-pedidos-btn-ajuda" href="<?= $loja['site'] ?>contato" class="btn-escuro">Preciso de ajuda</a></li>
            </ul>    
        </div>
    </div>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="<?= $loja['site'] ?>modulos/cliente/js/scripts.js"></script>

<?php } else { ?>

<script> window.location.href = '<?= $loja['site'] ?>cliente-pedidos'; </script>

<?php } ?>