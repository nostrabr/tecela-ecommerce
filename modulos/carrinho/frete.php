<?php

if(isset($_SESSION['pagseguro_boleto_erro'])){
    ?><input type="hidden" id="erro_pagamento_pagseguro" value="S"><?php
    unset($_SESSION['pagseguro_boleto_erro']);
} else {    
    ?><input type="hidden" id="erro_pagamento_pagseguro" value="N"><?php
}

//SE NÃO ESTÁ LOGADO, VOLTA PRA INDEX
if(!isset($_SESSION['nome'])){

    ?><script> window.location.href = '/'; </script><?php

} else {

//BUSCA ENDEREÇOS
$identificador_cliente = filter_var($_SESSION['identificador']);
$enderecos = mysqli_query($conn, "
    SELECT ce.identificador AS endereco_identificador, ce.padrao AS endereco_padrao, ce.nome AS endereco_nome, ce.cep AS endereco_cep, ce.logradouro AS endereco_logradouro, ce.numero AS endereco_numero, ce.complemento AS endereco_complemento, ce.bairro AS endereco_bairro, ce.referencia AS endereco_referencia, cd.nome AS endereco_cidade, e.sigla AS endereco_estado
    FROM cliente_endereco AS ce
    INNER JOIN cidade AS cd ON ce.cidade = cd.id
    INNER JOIN estado AS e ON ce.estado = e.id
    INNER JOIN cliente AS c ON c.id = ce.id_cliente
    WHERE c.identificador = '$identificador_cliente' AND ce.status = 1
    ORDER BY ce.padrao DESC
");
$n_enderecos = mysqli_num_rows($enderecos);
    
//BUSCA O CARRINHO DO VISITANTE
$session_visitante = filter_var($_SESSION['visitante']);
$carrinho    = mysqli_query($conn, "
    SELECT cp.identificador AS carrinho_produto_identificador, cp.id_produto AS produto_id, cp.quantidade AS produto_quantidade, cp.ids_caracteristicas AS produto_caracteristicas, cp.preco AS produto_preco, p.nome AS produto_nome,
    (SELECT pi.imagem FROM produto_imagem AS pi WHERE p.id = pi.id_produto AND pi.capa = 1) AS produto_imagem
    FROM carrinho AS c
    INNER JOIN carrinho_produto AS cp ON c.id = cp.id_carrinho
    INNER JOIN produto AS p ON p.id = cp.id_produto
    WHERE cp.status = 1 AND c.identificador = '".$session_visitante."'
");

$n_itens         = mysqli_num_rows($carrinho);

if($n_itens == 0){
    
    ?><script> window.location.href = '/'; </script><?php

} else {

$preco_total     = 0;
$produtos        = '';
$quantidades     = '';
$endereco_padrao = '';

//VERIFICA SE TEM ALGUM CEP SELECIONADO EM SESSION
if(isset($_SESSION["CEP"])){
    $cep = $_SESSION['CEP'];
} else {
    $cep = '';
}
unset($_SESSION['CEP']);

?>

<!--CSS-->
<link rel="stylesheet" href="modulos/carrinho/css/style.css">

<!--CARRINHO-->
<section id="carrinho-frete" class="carrinho">
    
    <h1 class="d-none">Carrinho frete</h1>

    <div id="carrinho-mapa">
        <ul>
            <li class="carrinho-mapa-imagem"><img class="carrinho-mapa-ativo" src="<?= $loja['site'] ?>imagens/carrinho-carrinho.png" title="Resumo do carrinho"></li>
            <li class="carrinho-mapa-separador"><hr class="carrinho-mapa-ativo-hr"></li>
            <li class="carrinho-mapa-imagem"><img class="carrinho-mapa-ativo" src="<?= $loja['site'] ?>imagens/carrinho-login.png" title="Cadastro/Login"></li>
            <li class="carrinho-mapa-separador"><hr class="carrinho-mapa-ativo-hr"></li>
            <li class="carrinho-mapa-imagem"><img class="carrinho-mapa-ativo" src="<?= $loja['site'] ?>imagens/carrinho-frete.png" title="Frete"></li>
            <li class="carrinho-mapa-separador"><hr></li>
            <li class="carrinho-mapa-imagem"><img src="<?= $loja['site'] ?>imagens/carrinho-pagamento.png" title="Pagamento"></li>
        </ul>
    </div>

    <div class="row">

        <div class="col-12 col-xl-8">

            <div id="carrinho-frete-endereco-entrega">

                <h2 class="subtitulo-pagina-central-h2">Como você quer receber sua compra?</h2>
                <?php if(mysqli_num_rows($enderecos) > 0){ ?>
                    <p class="subtitulo-pagina-central-p">Selecione o endereço de entrega e a opção de envio</p>    
                <?php } else { ?>
                    <p class="subtitulo-pagina-central-p">Cadastre seu endereço e selecione a opção de envio</p> 
                <?php } ?>
                                       
                <?php if(mysqli_num_rows($enderecos) > 0){ ?>
                    <h3 class="subtitulo-pagina-central-h3">Endereço de entrega</h3>
                <?php } ?>

                <?php if(mysqli_num_rows($enderecos) > 0){ ?>
                        
                    <?php while($endereco = mysqli_fetch_array($enderecos)){ ?>
                        <?php if($cep == '' & $endereco['endereco_padrao'] == 1){ $cep = $endereco['endereco_cep']; } ?>
                        <?php if($endereco['endereco_padrao'] == 1){ $endereco_padrao = $endereco['endereco_identificador']; } ?>
                        <div class="row">        
                            <div class="col-12">            
                                <div id="carrinho-frete-endereco-<?= $endereco['endereco_identificador'] ?>" identificador="<?= $endereco['endereco_identificador'] ?>" cep="<?= $endereco['endereco_cep'] ?>" class="carrinho-frete-endereco" onclick="javascript: selecionarEndereco('<?= $endereco['endereco_identificador'] ?>');" title="Selecionar este endereço">
                                    <div class="row">  
                                        <div class="carrinho-frete-endereco-btn-editar"><a href="javascript: setaRetornoFrete('edicao','<?= $endereco['endereco_identificador'] ?>');" title="Editar endereço"><img src="imagens/acao-editar.png" alt="Editar"></a></div>     
                                        <div class="col-12 carrinho-frete-endereco-nome text-uppercase"><?= $endereco['endereco_nome'] ?></div>
                                        <div class="col-12 carrinho-frete-endereco-dados text-capitalize"><?= $endereco['endereco_logradouro'].', '.$endereco['endereco_numero'] ?><?php if($endereco['endereco_complemento'] != ''){ echo ' - '.$endereco['endereco_complemento']; } ?><?= ' - '.$endereco['endereco_bairro'] ?><?= ' - CEP: '.$endereco['endereco_cep'] ?></div>
                                        <div class="col-12 carrinho-frete-endereco-cid-est text-capitalize"><?= $endereco['endereco_cidade'].' - '.$endereco['endereco_estado'] ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php } ?>

                <?php } ?>

                <div class="row">
                    <div class="col-12">                    
                        <a href="javascript: setaRetornoFrete('cadastro');" class="btn-claro mb-2">Novo endereço</a>                        
                    </div>
                </div>
                    
                <?php if(mysqli_num_rows($enderecos) > 0){ ?>
                    
                    <h3 id="subtitulo-pagina-central-opcoes-envio" class="subtitulo-pagina-central-h3">Opções de envio</h3>

                    <div class="row">
                        <div class="col-12">
                            <div id="carrinho-frete-resultados" class="row"><div class="col-12"><small>Selecione ou cadastre um endereço para calcular o frete.</small></div></div>
                        </div>
                    </div>

                <?php } ?>            

            </div>   

            <?php if(!$modo_whatsapp){ ?>

                <h2 class="subtitulo-pagina-central-h2 mt-3">Possui cupom de desconto?</h2>
                <p class="subtitulo-pagina-central-p">Informe no campo abaixo para recalcularmos o valor da sua compra.</p>  

                <div class="row">
                    <div class="col-8 col-xl-4">
                        <div class="form-group">
                            <label for="carrinho-frete-input-cupom" class="d-none">Cupom</label>
                            <input id="carrinho-frete-input-cupom" type="text" placeholder="Cupom" class="form-control text-uppercase" title="Informe o cupom de desconto aqui caso possua">  
                            <small id="carrinho-frete-small-cupom-aplicado">Desconto aplicado</small>
                        </div>      
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <input id="carrinho-frete-btn-aplicar-cupom" type="button" value="Aplicar" class="btn-claro" onclick="javascript: aplicaCupom();">
                        </div>      
                    </div>
                </div>

            <?php } ?>

            <?php if(mysqli_num_rows($enderecos) > 0){ ?>
                <div class="row">
                    <div class="col-12 mt-4">
                        <a id="carrinho-frete-btn-continuar" href="javascript: proximoPassoPagamento();" class="btn-escuro">Continuar</a>     
                    </div>
                </div>
            <?php } ?>

        </div>

        <div class="col-12 col-xl-4 mt-5 mt-xl-0">
            
            <h2 class="d-none">Resumo da compra</h2>
            <h3 class="d-none">Produtos</h3>

            <div class="row carrinho-separador"><div class="col-12"><hr class="mt-0"></div></div>
            <?php while($produto = mysqli_fetch_array($carrinho)){ $preco_total += $produto['produto_preco']*$produto['produto_quantidade']; ?>
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
                                <?php if(!$modo_whatsapp){ ?>
                                    <li class="carrinho-produto-texto-preco">R$ <?= number_format(($produto['produto_quantidade']*$produto['produto_preco']),2,',','.') ?></li>
                                <?php } ?>      
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
            
            <?php if(!$modo_whatsapp){ ?>

                <h3 class="d-none">Valores</h3>

                <div id="carrinho-frete-resumo-valor-total-produtos">
                    <span>Produtos:</span>
                    <span id="carrinho-frete-resumo-valor-totall-produtos-valor">R$ <?= number_format($preco_total,'2',',','.') ?></span>
                </div>      
                <div id="carrinho-frete-resumo-valor-total-desconto">
                    <span>Desconto:</span>
                    <span id="carrinho-frete-resumo-valor-total-valor-desconto"></span>
                </div>              
                <div id="carrinho-frete-resumo-valor-total-frete">
                    <span>Frete:</span>
                    <span id="carrinho-frete-resumo-valor-total-valor-frete"></span>
                </div>         
                <div id="carrinho-frete-resumo-valor-total-total">
                    <span>Total:</span>
                    <span id="carrinho-frete-resumo-valor-total-valor-total"></span>
                </div>  

            <?php } ?> 

        </div>

    </div>

    <form id="form-carrinho-frete" action="carrinho-pagamento"  method="POST">
        <input type="hidden" id="endereco" name="endereco" value="<?= $endereco_padrao ?>" minlength="32">
        <input type="hidden" id="tipo-frete" name="frete" value="">
        <input type="hidden" id="valor-produtos" name="valor-produtos" value="<?= $preco_total ?>">
        <input type="hidden" id="valor-frete"  name="valor-frete" value="0">
        <input type="hidden" id="valor-desconto" name="valor-desconto" value="">
        <input type="hidden" id="cupom-desconto" name="cupom-desconto" value="">
        <input type="hidden" id="cep" value="<?= $cep ?>">
        <input type="hidden" id="produtos" value="<?= $produtos ?>"> 
        <input type="hidden" id="quantidades" value="<?= $quantidades ?>">
        <input type="hidden" id="enderecos" value="<?= $n_enderecos ?>">
        <input type="hidden" id="modo_whatsapp" value="<?= $modo_whatsapp ?>">
    </form>

</section>

<?php /* LIMPA A SESSION DE RETORNO */ unset($_SESSION['RETORNO']); ?>

<!--SCRIPTS-->
<script type="text/javascript" src="modulos/frete/js/scripts.js"></script>
<script type="text/javascript" src="modulos/carrinho/js/scripts-1.1.js"></script>

<?php } } ?>