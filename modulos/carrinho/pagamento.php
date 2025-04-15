<?php

//SE NÃO ESTÁ LOGADO, VOLTA PRA INDEX
if(!isset($_SESSION['nome'])){

    ?><script> window.location.href = '/'; </script><?php

} else {

//RECEBE O IDENTIFICADOR DO ENDEREÇO E O TIPO DO FRETE
$identificador_endereco = filter_input(INPUT_POST, "endereco", FILTER_SANITIZE_STRING);
$tipo_frete             = filter_input(INPUT_POST, "frete", FILTER_SANITIZE_STRING);
$valor_frete            = filter_input(INPUT_POST, "valor-frete", FILTER_SANITIZE_STRING);
$valor_desconto         = filter_input(INPUT_POST, "valor-desconto", FILTER_SANITIZE_STRING);
$cupom_desconto         = filter_input(INPUT_POST, "cupom-desconto", FILTER_SANITIZE_STRING);

//BUSCA ENDEREÇO
$busca_endereco = mysqli_query($conn, "
    SELECT ce.identificador AS endereco_identificador, ce.padrao AS endereco_padrao, ce.nome AS endereco_nome, ce.cep AS endereco_cep, ce.logradouro AS endereco_logradouro, ce.numero AS endereco_numero, ce.complemento AS endereco_complemento, ce.bairro AS endereco_bairro, ce.referencia AS endereco_referencia, cd.nome AS endereco_cidade, e.sigla AS endereco_estado
    FROM cliente_endereco AS ce
    INNER JOIN cidade AS cd ON ce.cidade = cd.id
    INNER JOIN estado AS e ON ce.estado = e.id
    INNER JOIN cliente AS c ON c.id = ce.id_cliente
    WHERE ce.identificador = '$identificador_endereco' AND ce.status = 1
    ORDER BY ce.padrao DESC
");

//VERIFICA SE VEIO ENCONTROU O ENDEREÇO, SENÃO RETORNA PARA O FRETE
if(mysqli_num_rows($busca_endereco) == 0){

    ?><script> window.location.href = 'carrinho-frete'; </script><?php

} else {

//ENDEREÇO
$endereco = mysqli_fetch_array($busca_endereco);

//BUSCA A QUANTIDADE DE PARCELAS ACEITAS PELA LOJA
$busca_pagamento = mysqli_query($conn, "SELECT * FROM pagamento WHERE id = 1");
$pagamento       = mysqli_fetch_array($busca_pagamento);
    
//BUSCA O CARRINHO DO VISITANTE
$session_visitante = filter_var($_SESSION['visitante']);
$carrinho          = mysqli_query($conn, "
    SELECT cp.identificador AS carrinho_produto_identificador, cp.id_produto AS produto_id, cp.quantidade AS produto_quantidade, cp.ids_caracteristicas AS produto_caracteristicas, cp.preco AS produto_preco, p.nome AS produto_nome,
    (SELECT pi.imagem FROM produto_imagem AS pi WHERE p.id = pi.id_produto AND pi.capa = 1) AS produto_imagem
    FROM carrinho AS c
    INNER JOIN carrinho_produto AS cp ON c.id = cp.id_carrinho
    INNER JOIN produto AS p ON p.id = cp.id_produto
    WHERE cp.status = 1 AND c.identificador = '".$session_visitante."'
");

$n_itens = mysqli_num_rows($carrinho);

if($n_itens == 0){
    
    ?><script> window.location.href = '/'; </script><?php

} else {

$valor_produtos = 0;
$produtos       = '';
$quantidades    = '';

?>

<?php if($modo_whatsapp){ ?>
    
    <form id="carrinho-pagamento-form-whatsapp" action="modulos/orcamento/whatsapp/php/processa.php" method="POST">  
        <input type="hidden" name="tipo-frete" value="<?= $tipo_frete ?>"> 
        <input type="hidden" name="endereco" value="<?= $identificador_endereco ?>"> 
    </form>

    <?php echo "<script>document.getElementById('carrinho-pagamento-form-whatsapp').submit();</script>"; ?>

<?php } else { ?>

<!--CSS-->
<link rel="stylesheet" href="modulos/carrinho/css/style.css">

<!--CARRINHO-->
<section id="carrinho-pagamento" class="carrinho">
    
    <h1 class="d-none">Carrinho pagamento</h1>

    <div id="carrinho-mapa">
        <ul>
            <li class="carrinho-mapa-imagem"><img class="carrinho-mapa-ativo" src="<?= $loja['site'] ?>imagens/carrinho-carrinho.png" title="Resumo do carrinho"></li>
            <li class="carrinho-mapa-separador"><hr class="carrinho-mapa-ativo-hr"></li>
            <li class="carrinho-mapa-imagem"><img class="carrinho-mapa-ativo" src="<?= $loja['site'] ?>imagens/carrinho-login.png" title="Cadastro/Login"></li>
            <li class="carrinho-mapa-separador"><hr class="carrinho-mapa-ativo-hr"></li>
            <li class="carrinho-mapa-imagem"><img class="carrinho-mapa-ativo" src="<?= $loja['site'] ?>imagens/carrinho-frete.png" title="Frete"></li>
            <li class="carrinho-mapa-separador"><hr class="carrinho-mapa-ativo-hr"></li>
            <li class="carrinho-mapa-imagem"><img class="carrinho-mapa-ativo" src="<?= $loja['site'] ?>imagens/carrinho-pagamento.png" title="Pagamento"></li>
        </ul>
    </div>

    <div class="row">

        <div class="col-12 col-xl-4 mt-5 mt-xl-0 order-2">
            
            <h2 class="d-none">Resumo da compra</h2>
            <h3 class="d-none">Produtos</h3>

            <div class="row carrinho-separador"><div class="col-12"><hr class="mt-0"></div></div>
            <?php while($produto = mysqli_fetch_array($carrinho)){ $valor_produtos += $produto['produto_preco']*$produto['produto_quantidade']; ?>
                <?php
                    if($produto['produto_imagem'] == ''){  $produto_imagem = 'imagens/produto_sem_foto.png';
                    } else { $produto_imagem = 'imagens/produtos/media/'.$produto['produto_imagem']; }
                ?>
                <div class="row carrinho-produto">
                    <div class="col-4">
                        <div class="carrinho-produto-imagem" style="background-image: url('<?= $produto_imagem  ?>')"></div>
                    </div>
                    <div class="col-8">
                        <div class="carrinho-produto-texto">
                            <ul>
                                <li class="carrinho-produto-texto-nome"><?= $produto['produto_nome'] ?></li>
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
                                            ?><li class="carrinho-produto-texto-caracteristicas text-uppercase"><?= $caracteristica['atributo_nome'].": ".$caracteristica['caracteristica_nome'] ?></li><?php
                                        }
                                    }
                                ?>
                                <li class="carrinho-produto-texto-preco">R$ <?= number_format(($produto['produto_quantidade']*$produto['produto_preco']),2,',','.') ?></li>
                                <li class="carrinho-produto-texto-quantidade">Quantidade: <?= $produto['produto_quantidade'] ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row carrinho-separador"><div class="col-12"><hr></div></div> 
                <?php 
                    $produtos .= $produto['produto_id'].',';
                    $quantidades .= $produto['produto_quantidade'].',';
                ?>      
            <?php } ?>     
            
            <h3 class="d-none">Valores</h3>

            <div id="carrinho-frete-resumo-valor-total-produtos">
                <span>Produtos:</span>
                <span id="carrinho-frete-resumo-valor-totall-produtos-valor">R$ <?= number_format($valor_produtos,'2',',','.') ?></span>
            </div> 

            <?php if($valor_desconto != ''){ ?>

                <div id="carrinho-frete-resumo-valor-total-desconto" class="d-flex">
                    <span id="carrinho-frete-resumo-valor-total-tipo-desconto">Desconto do cupom:</span>
                    <span id="carrinho-frete-resumo-valor-total-valor-desconto">R$ <?= number_format($valor_desconto,'2',',','.') ?></span>
                </div>        
                   
            <?php } else {

                $desconto_pix          = '';
                $desconto_boleto       = '';
                $desconto_cartao       = '';
                $desconto_pix_tipo     = '';
                $desconto_boleto_tipo  = '';
                $desconto_cartao_tipo  = '';
                $desconto_pix_valor    = '';
                $desconto_boleto_valor = '';
                $desconto_cartao_valor = '';
                $tipo_desconto         = '';
                
                if($pagamento['pix'] == 1){
                    $faixas_desconto = mysqli_query($conn, "SELECT * FROM pagamento_faixa_desconto WHERE status = 1 AND tipo = 'PIX' AND $valor_produtos BETWEEN de AND ate ORDER BY porcentagem_desconto, valor_desconto DESC LIMIT 1");
                    if(mysqli_num_rows($faixas_desconto) > 0){
                        $faixa_desconto = mysqli_fetch_array($faixas_desconto);
                        if($faixa_desconto['porcentagem_desconto'] != 0){
                            $desconto_pix_valor = $faixa_desconto['porcentagem_desconto'].'%';
                            $desconto_pix       = $faixa_desconto['porcentagem_desconto'];
                            $desconto_pix_tipo  = 'P';
                        } else if($faixa_desconto['valor_desconto'] != 0) {
                            $desconto_pix_valor = 'R$ '.number_format($faixa_desconto['valor_desconto'],'2',',','.');
                            $desconto_pix       = $faixa_desconto['valor_desconto'];
                            $desconto_pix_tipo  = 'V';
                        }   
                    }
                } 

                $faixas_desconto = mysqli_query($conn, "SELECT * FROM pagamento_faixa_desconto WHERE status = 1 AND tipo = 'BOLETO' AND $valor_produtos BETWEEN de AND ate ORDER BY porcentagem_desconto, valor_desconto DESC LIMIT 1");
                if(mysqli_num_rows($faixas_desconto) > 0){
                    $faixa_desconto = mysqli_fetch_array($faixas_desconto);
                    if($faixa_desconto['porcentagem_desconto'] != 0){
                        $desconto_boleto_valor = $faixa_desconto['porcentagem_desconto'].'%';
                        $desconto_boleto       = $faixa_desconto['porcentagem_desconto'];
                        $desconto_boleto_tipo  = 'P';
                    } else if($faixa_desconto['valor_desconto'] != 0) {
                        $desconto_boleto_valor = 'R$ '.number_format($faixa_desconto['valor_desconto'],'2',',','.');
                        $desconto_boleto       = $faixa_desconto['valor_desconto'];
                        $desconto_boleto_tipo  = 'V';
                    }   
                }

                $faixas_desconto = mysqli_query($conn, "SELECT * FROM pagamento_faixa_desconto WHERE status = 1 AND tipo = 'CARTAO' AND $valor_produtos BETWEEN de AND ate ORDER BY porcentagem_desconto, valor_desconto DESC LIMIT 1");
                if(mysqli_num_rows($faixas_desconto) > 0){
                    $faixa_desconto = mysqli_fetch_array($faixas_desconto);
                    if($faixa_desconto['porcentagem_desconto'] != 0){
                        $desconto_cartao_valor = $faixa_desconto['porcentagem_desconto'].'%';
                        $desconto_cartao       = $faixa_desconto['porcentagem_desconto'];
                        $desconto_cartao_tipo  = 'P';
                    } else if($faixa_desconto['valor_desconto'] != 0) {
                        $desconto_cartao_valor = 'R$ '.number_format($faixa_desconto['valor_desconto'],'2',',','.');
                        $desconto_cartao       = $faixa_desconto['valor_desconto'];
                        $desconto_cartao_tipo  = 'V';
                    }   
                }

                if($pagamento['pix'] == 1 & $desconto_pix != ''){
                    $tipo_desconto = 'Desconto no Pix:';
                    if($desconto_pix_tipo == 'P'){
                        $valor_desconto = $valor_produtos*$desconto_pix/100;
                    } else {
                        $valor_desconto = $desconto_pix;
                    }
                } 
                
                if($pagamento['pix'] == 0 & $desconto_boleto != ''){
                    $tipo_desconto = 'Desconto no Boleto:';
                    if($desconto_boleto_tipo == 'P'){
                        $valor_desconto = $valor_produtos*$desconto_boleto/100;
                    } else {
                        $valor_desconto = $desconto_boleto;
                    }
                }

                if($valor_desconto != ''){
                    $valor_desconto_final = 'R$ '.number_format($valor_desconto,'2',',','.');
                }

                ?>

                <div id="carrinho-frete-resumo-valor-total-desconto" class="d-flex">
                    <span id="carrinho-frete-resumo-valor-total-tipo-desconto"><?= $tipo_desconto ?></span>
                    <span id="carrinho-frete-resumo-valor-total-valor-desconto"><?= $valor_desconto_final ?></span>
                </div>   

                <input type="hidden" id="desconto-pix"    value="<?= $desconto_pix ?>" tipo="<?= $desconto_pix_tipo ?>">
                <input type="hidden" id="desconto-cartao" value="<?= $desconto_cartao ?>" tipo="<?= $desconto_cartao_tipo ?>">
                <input type="hidden" id="desconto-boleto" value="<?= $desconto_boleto ?>" tipo="<?= $desconto_boleto_tipo ?>">

            <?php } ?>

            <div id="carrinho-frete-resumo-valor-total-frete">
                <span>Frete:</span>
                <span id="carrinho-frete-resumo-valor-total-valor-frete">R$ <?= number_format($valor_frete,'2',',','.') ?></span>
            </div>           
            <div id="carrinho-frete-resumo-valor-total-juros">
                <span>Juros:</span>
                <span id="carrinho-frete-resumo-valor-total-valor-juros"></span>
            </div>        
            <div id="carrinho-frete-resumo-valor-total-total">
                <span>Total:</span>
                <?php 
                    if($valor_desconto != ''){
                        $valor_total = $valor_produtos-$valor_desconto;
                        if($valor_total < 0){
                            $valor_total = 0;
                        }
                        $valor_total = $valor_total+$valor_frete;
                    } else {
                        $valor_total = $valor_produtos+$valor_frete;
                    }
                ?>
                <span id="carrinho-frete-resumo-valor-total-valor-total">R$ <?= number_format($valor_total,'2',',','.') ?></span>
            </div>

        </div>

        <div class="col-12 col-xl-8 order-1">

            <div id="carrinho-pagamento-formas">
            
                <h2 class="subtitulo-pagina-central-h2">Como você prefere pagar?</h2>
                <p class="subtitulo-pagina-central-p">Selecione a forma de pagamento, preencha os dados e clique em finalizar pedido.</p>   

                <?php if($pagamento['pagseguro_status'] == 1){ ?> 

                    <div id="carrinho-pagamento-formas-pagseguro">
                
                        <?php if($pagamento['pix'] == 1){ ?> 

                            <div class="row">        
                                <div class="col-12">   
                                    <div class="carrinho-pagamento-forma-pagamento carrinho-pagamento-forma-pagamento-ativo" tipo="pix">
                                        <ul>
                                            <li>Pix</li>
                                            <li><b>Rápido e prático</b>. Transfira e envie o comprovante pelo WhatsApp.</li>
                                            <?php if($desconto_pix != ''){ ?>
                                                <li class="desconto"><b>DESCONTO: <?= $desconto_pix_valor ?></b></li>
                                            <?php } ?>
                                        </ul>                           
                                    </div>
                                    <div class="d-none" id="carrinho-pagamento-forma-pagamento-boleto">
                                        <form id="carrinho-pagamento-form-pix" action="modulos/pagamento/pix/php/processa.php" method="POST">  
                                            <input type="hidden" name="tipo-frete" value="<?= $tipo_frete ?>"> 
                                            <input type="hidden" name="cupom" value="<?= $cupom_desconto ?>">  
                                            <input type="hidden" name="endereco" value="<?= $identificador_endereco ?>"> 
                                        </form>
                                    </div>
                                </div>
                            </div>                

                        <?php } ?>
                    
                        <div class="row">        
                            <div class="col-12">   
                                <div class="carrinho-pagamento-forma-pagamento <?php if($pagamento['pix'] == 0){ ?>carrinho-pagamento-forma-pagamento-ativo<?php } ?>" tipo="boleto">
                                    <ul>
                                        <li>Boleto</li>
                                        <li>Será aprovado em 1 ou 2 dias úteis.</li>
                                        <?php if($desconto_boleto != ''){ ?>
                                            <li class="desconto"><b>DESCONTO: <?= $desconto_boleto_valor ?></b></li>
                                        <?php } ?>
                                    </ul>                           
                                </div>
                                <div class="d-none" id="carrinho-pagamento-forma-pagamento-boleto">
                                    <form id="carrinho-pagamento-form-boleto" action="modulos/pagamento/pagseguro/php/processa-boleto.php" method="POST">  
                                        <input type="hidden" name="tipo-frete" value="<?= $tipo_frete ?>"> 
                                        <input type="hidden" name="cupom" value="<?= $cupom_desconto ?>">  
                                        <input type="hidden" name="endereco" value="<?= $identificador_endereco ?>"> 
                                        <input type="hidden" name="hash-comprador-boleto" id="hash-comprador-boleto">
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="row">        
                            <div class="col-12"> 
                                <div class="carrinho-pagamento-forma-pagamento carrinho-pagamento-forma-pagamento-cartao" tipo="cartao">
                                    <ul>
                                        <li>Cartão de crédito</li>
                                        <?php if($pagamento['parcelas'] > 1){ ?>
                                            <li>Parcele em até <?= $pagamento['parcelas'] ?>x</li>
                                        <?php } else { ?>
                                            <li>A vista</li>
                                        <?php } ?>
                                        <?php if($desconto_cartao != ''){ ?>
                                            <li class="desconto"><b>DESCONTO: <?= $desconto_cartao_valor ?></b></li>
                                        <?php } ?>
                                    </ul>  

                                    <div class="mt-3" id="carrinho-pagamento-forma-pagamento-cartao">

                                        <form id="carrinho-pagamento-form-cartao" action="modulos/pagamento/pagseguro/php/processa-cartao.php" method="POST">     

                                            <div class="row">
                                            
                                                <input type="hidden" name="tipo-frete" value="<?= $tipo_frete ?>"> 
                                                <input type="hidden" name="cupom" value="<?= $cupom_desconto ?>">  
                                                <input type="hidden" name="endereco" value="<?= $identificador_endereco ?>">
                                                <input type="hidden" name="token-cartao" id="token-cartao">
                                                <input type="hidden" name="hash-comprador-cartao" id="hash-comprador-cartao">
                                                <input type="hidden" name="valor-parcela" id="valor-parcela" value="">

                                                <div class="col-12">
                                                    <h2 class="subtitulo-pagina-central-h2">Dados do cartão</h2>
                                                    <p class="subtitulo-pagina-central-p">A nossa loja não armazena nenhum dado do cartão por motivos de segurança.<br>Para continuar preencha os campos abaixo e clique em Finalizar Pedido.</p> 
                                                </div>
                                                
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="numero-cartao">Número do cartão <span class="campo-obrigatorio">*</span></label>
                                                        <input type="text" name="numero" maxlength="19" id="numero-cartao" class="form-control" required>
                                                        <small id="mensagem-numero-cartao">Cartão inválido!</small>
                                                        <span id="bandeira-cartao"></span>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="nome">Nome <span class="campo-obrigatorio">*</span></label>
                                                        <input type="text" name="nome" maxlength="50" id="nome" class="form-control text-capitalize" required>
                                                        <small>Deve ser preenchido igual o do cartão</small>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-12 col-xl-6">
                                                    <div class="form-group">
                                                        <label for="validade-cartao">Validade <span class="campo-obrigatorio">*</span></label>
                                                        <input type="text" name="validade" maxlength="5" id="validade-cartao" class="form-control" required>
                                                        <small>Formato: MM/YY</small>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-12 col-xl-6">
                                                    <div class="form-group">
                                                        <label for="cvv">CVV <span class="campo-obrigatorio">*</span></label>
                                                        <input type="number" name="cvv" maxlength="3" id="cvv" class="form-control" required>
                                                        <small>Número de segurança de 3 dígitos do cartão</small>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-12 col-xl-6">
                                                    <div class="form-group">
                                                        <label for="cpf">CPF <span class="campo-obrigatorio">*</span></label>
                                                        <input type="text" name="cpf" maxlength="14" id="cpf" onblur="javascript: validaCpfCnpj(this.value, this.id);" class="form-control" required>
                                                        <small>Do títular do cartão</small>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-12 col-xl-6">
                                                    <div class="form-group">
                                                        <label for="nascimento">Data Nascimento <span class="campo-obrigatorio">*</span></label>
                                                        <input type="text" name="nascimento" maxlength="10" id="nascimento" class="form-control" required>
                                                        <small>Do títular do cartão</small>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="parcelas">Parcelas <span class="campo-obrigatorio">*</span></label>
                                                        <select type="text" name="parcelas" id="parcelas" class="form-control" onchange="javascript: calculaDescontoTipoPagamento(); calculaValorParcelas();" required>
                                                            <option value="" disabled>Preencha os dados do cartão</option>
                                                        </select>                            
                                                    </div>
                                                </div>

                                            </div>
                                                
                                        </form>

                                    </div>

                                </div>
                            </div>
                        </div>
                    
                        <div class="row">
                            <div class="col-12 mt-4">
                                <a id="carrinho-frete-btn-finalizar" href="javascript: finalizarPedido();" class="btn-escuro">Finalizar pedido</a>            
                            </div>
                        </div>

                    </div>
                    
                <?php } else if($pagamento['asaas_status'] == 1){ ?> 
                    
                    <div id="carrinho-pagamento-formas-asaas">

                        <?php if($pagamento['asaas_pix'] == 1){ ?> 
                            <div class="row">        
                                <div class="col-12">   
                                    <div class="carrinho-pagamento-forma-pagamento carrinho-pagamento-forma-pagamento-ativo" tipo="pix">
                                        <ul>
                                            <li>Pix</li>
                                            <li><b>Rápido e prático</b>.</li>
                                            <?php if($desconto_pix != ''){ ?>
                                                <li class="desconto"><b>DESCONTO: <?= $desconto_pix_valor ?></b></li>
                                            <?php } ?>
                                        </ul>                           
                                    </div>
                                    <div class="d-none" id="carrinho-pagamento-forma-pagamento-boleto">
                                        <form id="carrinho-pagamento-form-pix" action="modulos/pagamento/asaas/php/processa-pix.php" method="POST">  
                                            <input type="hidden" name="tipo-frete" value="<?= $tipo_frete ?>"> 
                                            <input type="hidden" name="cupom" value="<?= $cupom_desconto ?>">  
                                            <input type="hidden" name="endereco" value="<?= $identificador_endereco ?>"> 
                                        </form>
                                    </div>
                                </div>
                            </div> 
                        <?php } ?>

                        <?php if($pagamento['asaas_boleto'] == 1){ ?> 
                            <div class="row">        
                                <div class="col-12">   
                                    <div class="carrinho-pagamento-forma-pagamento <?php if($pagamento['asaas_pix'] == 0){ ?>carrinho-pagamento-forma-pagamento-ativo<?php } ?>" tipo="boleto">
                                        <ul>
                                            <li>Boleto</li>
                                            <li>Será aprovado em 1 ou 2 dias úteis.</li>
                                            <?php if($desconto_boleto != ''){ ?>
                                                <li class="desconto"><b>DESCONTO: <?= $desconto_boleto_valor ?></b></li>
                                            <?php } ?>
                                        </ul>                           
                                    </div>
                                    <div class="d-none" id="carrinho-pagamento-forma-pagamento-boleto">
                                        <form id="carrinho-pagamento-form-boleto" action="modulos/pagamento/asaas/php/processa-boleto.php" method="POST">  
                                            <input type="hidden" name="tipo-frete" value="<?= $tipo_frete ?>"> 
                                            <input type="hidden" name="cupom" value="<?= $cupom_desconto ?>">  
                                            <input type="hidden" name="endereco" value="<?= $identificador_endereco ?>"> 
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if($pagamento['asaas_cc'] == 1){ ?>
                            <?php                
                                if($cupom_desconto != ''){
                                    $valor_total_cartao = $valor_produtos+$valor_frete-$valor_desconto;
                                } else {
                                    if($desconto_cartao != ''){
                                        if($desconto_cartao_tipo == 'P'){                                        
                                            $desconto_cartao = $valor_produtos*$desconto_cartao/100;
                                        }                                    
                                        $valor_total_cartao = $valor_produtos-$desconto_cartao;
                                        if($valor_total_cartao < 0){
                                            $valor_total_cartao = 0;
                                        }
                                        $valor_total_cartao = $valor_total_cartao+$valor_frete;
                                    } else {
                                        $valor_total_cartao = $valor_produtos+$valor_frete;
                                    }                                        
                                }
                            ?>
                            <div class="row">        
                                <div class="col-12"> 
                                    <div class="carrinho-pagamento-forma-pagamento carrinho-pagamento-forma-pagamento-cartao <?php if($pagamento['asaas_pix'] == 0 & $pagamento['asaas_boleto'] == 0){ ?>carrinho-pagamento-forma-pagamento-ativo<?php } ?>" tipo="cartao">
                                        <ul>
                                            <li>Cartão de crédito</li>
                                            <?php if($pagamento['asaas_parcelas'] > 1){ ?>
                                                <li>Parcele em até <?= $pagamento['asaas_parcelas'] ?>x<br>Valor mínimo da parcela: R$ 5,00</li>
                                            <?php } else { ?>
                                                <li>A vista</li>
                                            <?php } ?>
                                            <?php if($desconto_cartao != ''){ ?>
                                                <li class="desconto"><b>DESCONTO: <?= $desconto_cartao_valor ?></b></li>
                                            <?php } ?>
                                        </ul>  

                                        <div class="mt-3" id="carrinho-pagamento-forma-pagamento-cartao" <?php if($pagamento['asaas_pix'] == 0 & $pagamento['asaas_boleto'] == 0){ ?>style="display: block;"<?php } ?>>

                                            <form id="carrinho-pagamento-form-cartao" action="modulos/pagamento/asaas/php/processa-cartao.php" method="POST">     

                                                <div class="row">
                                                
                                                    <input type="hidden" name="tipo-frete" value="<?= $tipo_frete ?>"> 
                                                    <input type="hidden" name="cupom" value="<?= $cupom_desconto ?>">  
                                                    <input type="hidden" name="endereco" value="<?= $identificador_endereco ?>">

                                                    <div class="col-12">
                                                        <h2 class="subtitulo-pagina-central-h2">Dados do cartão</h2>
                                                        <p class="subtitulo-pagina-central-p">A nossa loja não armazena nenhum dado do cartão por motivos de segurança.<br>Para continuar preencha os campos abaixo e clique em Finalizar Pedido.</p> 
                                                    </div>
                                                    
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="numero-cartao">Número do cartão <span class="campo-obrigatorio">*</span></label>
                                                            <input type="text" name="numero" maxlength="19" id="numero-cartao" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="nome">Nome <span class="campo-obrigatorio">*</span></label>
                                                            <input type="text" name="nome" maxlength="50" id="nome" class="form-control text-capitalize" required>
                                                            <small>Deve ser preenchido igual o do cartão</small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-12 col-xl-6">
                                                        <div class="form-group">
                                                            <label for="validade-cartao">Validade <span class="campo-obrigatorio">*</span></label>
                                                            <input type="text" name="validade" maxlength="5" id="validade-cartao" class="form-control" required>
                                                            <small>Formato: MM/YY</small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-12 col-xl-6">
                                                        <div class="form-group">
                                                            <label for="cvv">CVV <span class="campo-obrigatorio">*</span></label>
                                                            <input type="number" name="cvv" maxlength="3" id="cvv" class="form-control" required>
                                                            <small>Número de segurança de 3 dígitos do cartão</small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="cpf">CPF <span class="campo-obrigatorio">*</span></label>
                                                            <input type="text" name="cpf" maxlength="14" id="cpf" onblur="javascript: validaCpfCnpj(this.value, this.id);" class="form-control" required>
                                                            <small>Do títular do cartão</small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-12 <?php if($pagamento['asaas_parcelas'] <= 1){ echo 'd-none'; } ?>">
                                                        <div class="form-group">
                                                            <label for="parcelas">Parcelas <span class="campo-obrigatorio">*</span></label>
                                                            <select type="text" name="parcelas" id="parcelas" class="form-control" onchange="javascript: calculaDescontoTipoPagamento(); calculaValorParcelas();" required>
                                                                <?php

                                                                for($i=1;$i<=$pagamento['asaas_parcelas'];$i++){ ?>

                                                                    <?php 

                                                                    $tem_juros = false;
                                                                    if($pagamento['asaas_parcelas_juros'] == 0){
                                                                        $valor_parcela = $valor_total_cartao/$i;
                                                                    } else {
                                                                        if($pagamento['asaas_parcelas_juros'] > 0 & $i < $pagamento['asaas_parcelas_juros']){      
                                                                            $valor_parcela = $valor_total_cartao/$i;
                                                                        } else {
                                                                            $tem_juros = true;
                                                                            if($pagamento['asaas_juros_tipo'] == 1){
                                                                                $valor_parcela = $valor_total_cartao/$i+($valor_total_cartao/$i*$pagamento['asaas_parcelas_juros_porcentagem']/100);
                                                                            } else {                                                                        
                                                                                $mont = $valor_total_cartao * pow( (1 + ($pagamento['asaas_parcelas_juros_porcentagem'] / 100)) , $i );
                                                                                $valor_parcela = ($mont / $i);
                                                                            }
                                                                        }
                                                                    }
                                                                    
                                                                    //PARCELA MINIMA NO ASAAS É DE R$ 5,00
                                                                    if($valor_parcela >= 5){ ?>          
                                                                        <option value="<?= $i ?>" data-valor-parcela="<?= $valor_parcela ?>"><?= $i."x de R$ ".number_format($valor_parcela,2,',','.') ?><?php if(!$tem_juros){ echo ' sem juros'; } ?></option>
                                                                <?php } } ?>
                                                            </select>                            
                                                        </div>
                                                    </div>
                                                </div>                                            
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    
                        <?php if($pagamento['asaas_pix'] == 1 | $pagamento['asaas_boleto'] == 1 | $pagamento['asaas_cc'] == 1){ ?> 
                            <div class="row">
                                <div class="col-12 mt-4">
                                    <a id="carrinho-frete-btn-finalizar" href="javascript: finalizarPedidoAsaas();" class="btn-escuro">Finalizar pedido</a>            
                                </div>
                            </div>
                        <?php } ?>

                    </div>

                <?php } ?>

            </div>

        </div>

    </div>

    <input type="hidden" id="valor-produtos" value="<?= $valor_produtos ?>">
    <input type="hidden" id="valor-frete" value="<?= $valor_frete ?>">
    <input type="hidden" id="valor-compra" value="<?= $valor_total ?>">
    <input type="hidden" id="total-parcelas" value="<?= $pagamento['parcelas'] ?>">
    <input type="hidden" id="valor-desconto" value="<?= $valor_desconto ?>">
    <input type="hidden" id="cupom-desconto" value="<?= $cupom_desconto ?>">

</section>

<!--SCRIPTS-->
<?php if($pagamento['pagseguro_status'] == 1){ ?> 

    <input type="hidden" id="endereco-pagseguro" value="<?= URL ?>">
    
    <script type="text/javascript" src="<?php echo SCRIPT_PAGSEGURO; ?>"></script>
    <script type="text/javascript" src="modulos/pagamento/pagseguro/js/scripts-1.2.js"></script>
    <script type="text/javascript" src="modulos/carrinho/js/scripts-1.1.js"></script>

<?php } else if($pagamento['asaas_status'] == 1){ ?> 

    <script type="text/javascript" src="modulos/carrinho/js/scripts-1.1.js"></script>

<?php } ?> 

<?php } } } } ?>