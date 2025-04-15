<!--CSS-->
<link rel="stylesheet" href="modulos/carrinho/css/style.css">

<?php

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

$n_itens        = mysqli_num_rows($carrinho);
$preco_total    = 0;
$produtos       = '';
$quantidades    = '';

//SE O MODO WHATSAPP SIMPLES ESTIVER ATIVADO
if($modo_whatsapp_simples){
    //ESTANCIA A VARIÁVEL DO TEXTO QUE VAI PRO WHATS
    if($loja['modo_whatsapp_preco'] == 0){
        $texto_whatsapp = 'Olá!%0AGostaria de um orçamento para a lista de produtos abaixo:%0A%0A';
    } else {
        $texto_whatsapp = 'Olá!%0AGostaria de fazer um pedido. Segue a lista:%0A%0A';
    }
}

?>

<!--CARRINHO-->
<section id="carrinho" class="carrinho">

    <h1 class="d-none">Carrinho</h1>

    <?php if($n_itens > 0){ ?>
               
        <?php if(!$modo_whatsapp){ ?>
            <div id="carrinho-mapa">
                <ul>
                    <li class="carrinho-mapa-imagem"><img class="carrinho-mapa-ativo" src="<?= $loja['site'] ?>imagens/carrinho-carrinho.png" title="Resumo do carrinho"></li>
                    <li class="carrinho-mapa-separador"><hr></li>
                    <li class="carrinho-mapa-imagem"><img src="<?= $loja['site'] ?>imagens/carrinho-login.png" title="Cadastro/Login"></li>
                    <li class="carrinho-mapa-separador"><hr></li>
                    <li class="carrinho-mapa-imagem"><img src="<?= $loja['site'] ?>imagens/carrinho-frete.png" title="Frete"></li>
                    <li class="carrinho-mapa-separador"><hr></li>
                    <li class="carrinho-mapa-imagem"><img src="<?= $loja['site'] ?>imagens/carrinho-pagamento.png" title="Pagamento"></li>
                </ul>
            </div>
        <?php } ?>

        <div id="carrinho-titulos" class="row d-none d-lg-flex">
            <div class="<?php if(!$modo_whatsapp){ echo "col-8";} else { echo "col-10"; } ?>">Produto</div>
            <div class="col-1">Qtde</div>
            <?php if(!$modo_whatsapp){ ?>
                <div class="col-2 text-center">Total</div>
            <?php } ?>
            <div class="col-1 text-right">Excluir</div>
        </div>
        <div class="row carrinho-separador"><div class="col-12"><hr></div></div>
        <?php while($produto = mysqli_fetch_array($carrinho)){ $preco_total += $produto['produto_preco']*$produto['produto_quantidade']; ?>
            <?php            

                if($produto['produto_imagem'] == ''){  $produto_imagem = 'imagens/produto_sem_foto.png';
                } else { $produto_imagem = 'imagens/produtos/media/'.$produto['produto_imagem']; }

            ?>
            <div class="row carrinho-produto">
                <div class="col-4 col-lg-1">
                    <div class="carrinho-produto-imagem" style="background-image: url('<?= $produto_imagem ?>')"></div>
                </div>
                <div  class="<?php if(!$modo_whatsapp){ echo "col-8 col-lg-7"; } else { if($loja['modo_whatsapp_preco'] == 1){ echo "col-8 col-lg-7"; } else { echo "col-8 col-lg-9"; } } ?>">
                    <div class="carrinho-produto-texto">
                        <ul>
                            <li class="carrinho-produto-texto-nome"><?= $produto['produto_nome'] ?></li>
                            <?php if(!$modo_whatsapp){ ?>
                                <li class="carrinho-produto-texto-preco-unitario">R$ <?= number_format($produto['produto_preco'],2,',','.') ?> UN</li>
                            <?php } else {  ?>                            
                                <?php if($loja['modo_whatsapp_preco'] == 1){ ?>
                                    <li class="carrinho-produto-texto-preco-unitario">R$ <?= number_format($produto['produto_preco'],2,',','.') ?> UN</li>
                                <?php } ?>
                            <?php } ?>
                            <?php 
                                $estoque_variacao             = '';
                                $id_caracteristica_primaria   = '';
                                $id_caracteristica_secundaria = '';
                                $caracteristicas              = explode(',',$produto['produto_caracteristicas']);
                                $n_caracteristicas            = count($caracteristicas);
                                if($n_caracteristicas > 0){                                    
                                    $produto_caracteristica_orcamento = '';
                                    $sql_caracteristicas = '';
                                    for($i = 0; $i < $n_caracteristicas; $i++){
                                        if($i == 0){
                                            $sql_caracteristicas .= "pc.id = ".$caracteristicas[$i];
                                        } else {
                                            $sql_caracteristicas .= " OR pc.id = ".$caracteristicas[$i];
                                        }
                                    }
                                    $busca_caracteristicas = mysqli_query($conn, "
                                        SELECT a.nome AS atributo_nome, c.nome AS caracteristica_nome, pc.id_caracteristica AS caracteristica_id 
                                        FROM produto_caracteristica AS pc
                                        INNER JOIN atributo AS a ON pc.id_atributo = a.id
                                        INNER JOIN caracteristica AS c ON pc.id_caracteristica = c.id
                                        WHERE ".$sql_caracteristicas
                                    );
                                    $contador_aux = 0;
                                    while($caracteristica = mysqli_fetch_array($busca_caracteristicas)){
                                        if($contador_aux == 0){
                                            $id_caracteristica_primaria   = $caracteristica['caracteristica_id'];
                                        } else if($contador_aux == 1){
                                            $id_caracteristica_secundaria   = $caracteristica['caracteristica_id'];
                                        }                                        
                                        $produto_caracteristica_orcamento .= ' - '.$caracteristica['atributo_nome'].": ".$caracteristica['caracteristica_nome'];
                                        $contador_aux++;
                                        ?><li class="carrinho-produto-texto-caracteristicas text-uppercase"><?= $caracteristica['atributo_nome'].": ".$caracteristica['caracteristica_nome'] ?></li><?php
                                    }
                                    
                                    if($id_caracteristica_primaria == '' & $id_caracteristica_secundaria == ''){

                                        //BUSCA O ESTOQUE DO PRODUTO
                                        $busca_estoque = mysqli_query($conn, "SELECT estoque FROM produto WHERE id = ".$produto['produto_id']);
                                        $estoque       = mysqli_fetch_array($busca_estoque);

                                        $estoque_variacao = $estoque['estoque'];

                                    } else {

                                        //BUSCA O ESTOQUE MÁXIMO DA VARIAÇÃO
                                        $busca_estoque = mysqli_query($conn, "SELECT estoque FROM produto_variacao WHERE status != 2 AND id_caracteristica_primaria = '$id_caracteristica_primaria' AND id_caracteristica_secundaria = '$id_caracteristica_secundaria' AND id_produto = ".$produto['produto_id']);
                                        $estoque       = mysqli_fetch_array($busca_estoque);

                                        $estoque_variacao = $estoque['estoque'];

                                    }
                                    
                                } else {
                                    $produto_caracteristica_orcamento = '';
                                }

                                //SE O MODO WHATSAPP SIMPLES ESTIVER ATIVADO INCREMENTA O TEXTO
                                if($modo_whatsapp_simples){ 
                                    if($loja['modo_whatsapp_preco'] == 1){ 
                                        $texto_whatsapp .= $produto['produto_quantidade'].'x - '.$produto['produto_nome'].mb_strtoupper($produto_caracteristica_orcamento).' - R$ '.number_format($produto['produto_preco'],2,',','.').' UN - R$ '.number_format(($produto['produto_preco']*$produto['produto_quantidade']),2,',','.').'%0A'; 
                                    } else {
                                        $texto_whatsapp .= $produto['produto_quantidade'].'x - '.$produto['produto_nome'].mb_strtoupper($produto_caracteristica_orcamento).'%0A'; 
                                    }
                                }  

                            ?>
                        </ul>
                    </div>
                </div>
                <div class="col-3 col-lg-1 order-2 order-lg-1">
                    <div class="carrinho-produto-quantidade">
                        <div class="form-group mb-0">
                            <label for="carrinho-produto-quantidade-<?= $produto['carrinho_produto_identificador'] ?>" class="d-none">Quantidade</label>
                            <div class="quantity">
                                <input id="carrinho-produto-quantidade-<?= $produto['carrinho_produto_identificador'] ?>" type="number" class="form-control border-0" value="<?= $produto['produto_quantidade'] ?>" min="1" max="<?= $estoque_variacao ?>" onchange="javascript: alteraQuantidade('<?= $produto['carrinho_produto_identificador'] ?>',$(this).val());" >
                            </div>
                        </div>
                    </div>
                </div>
                <?php if(!$modo_whatsapp){ ?>
                    <div class="col-6 col-lg-2 order-1 order-lg-2">
                        <div class="carrinho-produto-preco-total">
                            R$ <?= number_format(($produto['produto_quantidade']*$produto['produto_preco']),2,',','.') ?>
                        </div>
                    </div>
                <?php } else { ?>                    
                    <?php if($loja['modo_whatsapp_preco'] == 1){ ?>
                        <div class="col-6 col-lg-2 order-1 order-lg-2">
                            <div class="carrinho-produto-preco-total">
                                R$ <?= number_format(($produto['produto_quantidade']*$produto['produto_preco']),2,',','.') ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="col-6 d-lg-none"></div>
                    <?php } ?>
                <?php } ?>
                <div class="col-3 col-lg-1 order-3 order-lg-3">
                    <div class="carrinho-produto-texto-btn-excluir" onclick="javascript: excluiProduto('<?= $produto['carrinho_produto_identificador'] ?>');" data-id-carrinho-produto="<?= $produto['carrinho_produto_identificador'] ?>">
                        <img src="imagens/acao-excluir.png" alt="Excluir">
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

            <div class="row carrinho-frete">
                <?php $tem_endereco = false; if(isset($_SESSION['nome'])){ ?>
                    <div class="col-12 col-lg-9 col-xl-10">            
                        <div class="carrinho-frete-container">
                            <?php 

                                //RECUPERA O ID DO CLIENTE
                                $identificador_cliente = $_SESSION['identificador'];

                                //BUSCA O ENDEREÇO PADRÃO DO CLIENTE
                                $busca_endereco = mysqli_query($conn, "
                                    SELECT ce.identificador AS endereco_identificador, ce.nome AS endereco_nome, ce.cep AS endereco_cep, ce.logradouro AS endereco_logradouro, ce.numero AS endereco_numero, cd.nome AS endereco_cidade
                                    FROM cliente_endereco AS ce
                                    INNER JOIN cidade AS cd ON ce.cidade = cd.id
                                    INNER JOIN cliente AS c ON c.id = ce.id_cliente
                                    WHERE c.identificador = '$identificador_cliente' AND ce.status = 1
                                    ORDER BY ce.padrao DESC
                                ");

                                if(mysqli_num_rows($busca_endereco) > 0){

                                    ?>
                                    <div class="form-group">
                                        <select name="endereco" id="endereco" class="form-control"><?php
                                            while($endereco = mysqli_fetch_array($busca_endereco)){
                                                ?><option value="<?= $endereco['endereco_cep'] ?>"><?= $endereco['endereco_nome']." - ".$endereco['endereco_logradouro'].", ".$endereco['endereco_numero']." - ".$endereco['endereco_cidade'] ?></option><?php
                                            }
                                        ?>
                                        </select>
                                    </div><?php

                                    $tem_endereco = true;

                                }
                            ?>  
                        </div>           
                    </div>        
                    <?php if($tem_endereco){ ?>
                        <div class="col-12 col-lg-3 col-xl-2">
                            <div id="carrinho-resultado-frete"></div>
                        </div>   
                    <?php } ?>                    
                <?php } ?>   
            </div>     

            <div class="row carrinho-resumo">
                <div class="col-12 col-lg-9 col-xl-10">       
                    <div class="carrinho-resumo-valor-total"><?php if(isset($_SESSION['nome']) & $tem_endereco){ ?>Total com frete: <?php } else { ?>Total: <?php } ?></div>                
                </div>
                <div class="col-12 col-lg-3 col-xl-2">
                    <div class="carrinho-resumo-valor-total" id="carrinho-resumo-valor-total"><?php if(!isset($_SESSION['nome']) | !$tem_endereco){ ?>R$ <?= number_format($preco_total,2,',','.') ?><?php } ?></div>                
                </div>
            </div>               
            
            <?php if(!$tem_endereco){ ?>
                <div class="row <?php if(!$frete_ativado){ echo 'd-none'; } ?>">
                    <div class="col-12">    
                        <div class="carrinho-frete-simulacao">
                            <ul>
                                <li>
                                    <label for="cep" class="d-none">Cep</label>
                                    <input type="text" id="cep" placeholder="Simular frete.." title="Digite o cep para simular o frete">
                                </li>
                                <li><div id="carrinho-resultado-frete"></div></li>
                            </ul>                            
                        </div>
                    </div>   
                </div>   
            <?php } ?>  
            <small class="<?php if(!$frete_ativado){ echo 'd-none'; } ?>" id="small-mais-opcoes-envio">Clique em avançar para ver mais opções de envio</small>        

        <?php } else { ?>
            
            <?php if($loja['modo_whatsapp_preco'] == 1){ ?>

                <div class="row carrinho-resumo">
                    <div class="col-12 col-lg-9 col-xl-10">       
                        <div class="carrinho-resumo-valor-total">Total: </div>                
                    </div>
                    <div class="col-12 col-lg-3 col-xl-2">
                        <div class="carrinho-resumo-valor-total" id="carrinho-resumo-valor-total">R$ <?= number_format($preco_total,2,',','.') ?></div>                
                    </div>
                </div>

                <?php $texto_whatsapp .= 'TOTAL: R$ '.number_format($preco_total,2,',','.').'%0A'; ?>
                        
            <?php } ?>

        <?php } ?>
        
        <?php if($modo_whatsapp_simples){ ?>
            <div class="row carrinho-botoes">
                <div class="col-12">
                    <div class="carrinho-botoes-container">
                        <?php if($loja['modo_whatsapp_preco'] == 0){ ?>
                            <a id="carrinho-botoes-btn-avancar" href="https://wa.me/55<?= preg_replace("/[^0-9]/", "", $loja['whatsapp']) ?>?text=<?= $texto_whatsapp ?>" target="_blank" class="btn-escuro">Solicitar orçamento</a>
                        <?php } else { ?>
                            <a id="carrinho-botoes-btn-avancar" href="https://wa.me/55<?= preg_replace("/[^0-9]/", "", $loja['whatsapp']) ?>?text=<?= $texto_whatsapp ?>" target="_blank" class="btn-escuro">Fazer pedido</a>
                        <?php } ?>
                    </div>                
                </div>
            </div>
        <?php } else { ?>
            <?php if($frete_ativado){ ?>           
                <div class="row carrinho-botoes">
                    <div class="col-12">
                        <div class="carrinho-botoes-container">
                            <ul>
                                <li class="d-none d-md-inline-flex mr-4 align-bottom"><a id="carrinho-botoes-btn-mais-produtos" href="<?= $loja['site'] ?>">Continuar comprando</a></li>
                                <li class="d-block d-md-inline-flex"><a id="carrinho-botoes-btn-avancar" href="javascript: proximoPassoCarrinho();" class="btn-escuro">Avançar</a></li>
                                <li class="d-block d-md-none pt-3"><a id="carrinho-botoes-btn-mais-produtos-mobile" href="<?= $loja['site'] ?>">Continuar comprando</a></li>
                            </ul>                        
                        </div>                
                    </div>
                </div>
            <?php } ?>
        <?php } ?>

    <?php } else { ?>
        <div id="carrinho-vazio" class="row">
            <div class="col-12 col-xl-4 offset-xl-4">
                <ul>
                    <li><h3>O seu carrinho está vazio</h3></li>
                    <li id="carrinho-vazio-btn-container"><a id="carrinho-vazio-btn-escolher" class="btn-claro" href="<?= $loja['site'] ?>">Escolher um produto</a></li>
                </ul>  
            </div>  
        </div>    
    <?php } ?>

    <input type="hidden" id="produtos" value="<?= $produtos ?>"> 
    <input type="hidden" id="quantidades" value="<?= $quantidades ?>">
    <input type="hidden" id="total" value="<?= $preco_total ?>">
    <input type="hidden" id="logado" value="<?php if(isset($_SESSION['nome'])){ echo 'true'; } else { echo 'false'; } ?>">
    <input type="hidden" id="modo_whatsapp" value="<?= $modo_whatsapp ?>">
    <input id="busca-automatica-cep" type="hidden" value="<?= $loja['opcao_cep_automatico'] ?>">

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="modulos/frete/js/scripts.js"></script>
<script type="text/javascript" src="modulos/carrinho/js/scripts-1.1.js"></script>