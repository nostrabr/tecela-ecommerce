<?php

//RECEBE OS DADOS 
$status   = trim(strip_tags(filter_input(INPUT_POST, "status", FILTER_SANITIZE_STRING)));   
$mensagem = trim(strip_tags(filter_input(INPUT_POST, "mensagem", FILTER_SANITIZE_STRING)));   

if(isset($_SESSION['nome']) & !empty($status)){    

?>

<!--CSS-->
<link rel="stylesheet" href="modulos/carrinho/css/style.css">

<!--CARRINHO-->
<section id="carrinho-confirmacao" class="carrinho">
    
    <h1 class="d-none">Carrinho confirmação</h1>

    <?php 

    $busca_pagamento = mysqli_query($conn, "SELECT pagseguro_status, asaas_status FROM pagamento WHERE id = 1");
    $pagamento       = mysqli_fetch_array($busca_pagamento);
            
    $codigo_pedido = trim(strip_tags(filter_input(INPUT_POST, "codigo", FILTER_SANITIZE_STRING)));   

    $busca_pedido = mysqli_query($conn, "
        SELECT pp.tipo, pp.boleto, pp.valor_parcela, pp.parcelas, c.identificador AS carrinho_identificador, p.id AS id_pedido, p.identificador AS pedido_identificador, pp.asaas_link_fatura, pp.asaas_pix_chave, pp.asaas_pix_imagem, pp.asaas_boleto
        FROM pagamento_pagseguro AS pp 
        INNER JOIN pedido AS p ON p.id = pp.id_pedido 
        LEFT JOIN carrinho AS c ON p.id_carrinho = c.id
        WHERE p.codigo = '".$codigo_pedido."'
    ");
    $pedido       = mysqli_fetch_array($busca_pedido);
    
    ?><input id="carrinho-confirmacao-identificador" type="hidden" value="<?= $pedido['carrinho_identificador'] ?>"><?php
    ?><input id="pedido-confirmacao-identificador" type="hidden" value="<?= $pedido['pedido_identificador'] ?>"><?php

    //SE NÃO PROCESSOU O PAGAMENTO MOSTRA O ERRO
    if($status === 'ERRO'){

        ?>
        <input id="carrinho-confirmacao-pedido-nao-confirmado" type="hidden" value="nao-confirmado">
        <h2 class="subtitulo-pagina-central-h2">Erro ao processar pagamento,</h2>
        <p><?= $mensagem ?></p>   
        <p>Se o problema persistir contate o administrador do sistema.</p>   
        <p class="mb-0">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-lg-3">
                        <a id="btn-email" href="<?= $loja['site'] ?>carrinho-pagamento">TENTAR NOVAMENTE</a>
                    </div>
                </div>
            </div>
        </p>   
        <?php

    } else if($status === 'SUCESSO'){

        ?>

        <input id="carrinho-confirmacao-pedido-confirmado" type="hidden" value="confirmado">
        <input id="carrinho-confirmacao-valor-pedido" type="hidden" value="<?= $pedido['valor_parcela']*$pedido['parcelas'] ?>">
        <h2 class="subtitulo-pagina-central-h2">Pedido realizado com sucesso.</h2>
        
        <?php if($pedido['tipo'] != 'PIX'){  ?>

            <?php if($pagamento['pagseguro_status'] == 1){ ?>

                <p>Assim que o pagamento for confirmado, lhe enviaremos um e-mail avisando.</p>                   
                <p>Para acompanhar seus pedidos acesse a <a href="cliente-pedidos">ÁREA DO CLIENTE</a> em 'Pedidos' e procure pelo código de referência.</p>
                <p class="<?php if($pedido['tipo'] == 'CARTAO'){ echo 'mb-0'; } ?>"><b>CÓDIGO DE REFERÊNCIA: <span class="codigo-pedido"><?= $codigo_pedido ?></span></b></p>
                <?php if($pedido['tipo'] == 'BOLETO'){  ?>
                    <p>Boleto para pagamento: <a href="<?= $pedido['boleto'] ?>" target="_blank">ABRIR</a></p>
                    <iframe src="<?= $pedido['boleto'] ?>" title="iframe example 1" width="100%" height="500"></iframe>
                <?php } ?>

            <?php } else if($pagamento['asaas_status'] == 1){ ?>  
                
                <?php if($pedido['tipo'] == 'CARTAO'){  ?>       
                    
                    <div class="pagamento-confirmado">
                        <ul>
                            <li><img src="<?= $loja['site'] ?>imagens/pagamento-confirmado.gif" alt="Pedido Confirmado"></li>
                            <li>Pagamento confirmado!</li>
                        </ul>                    
                    </div>

                    <div class="pagamento-nao-confirmado">
                        <p>Estamos confirmando seu pagamento...</p> 
                        <p>Assim que o pagamento for confirmado, lhe enviaremos também um e-mail avisando.</p> 
                        <p>Para acompanhar seus pedidos acesse a <a href="cliente-pedidos">ÁREA DO CLIENTE</a> em 'Pedidos' e procure pelo código de referência.</p>
                        <p><b>CÓDIGO DE REFERÊNCIA: <span class="codigo-pedido"><?= $codigo_pedido ?></span></b></p>
                        <p>Muito obrigado!</p>       
                    </div>

                <?php } ?>

                <?php if($pedido['tipo'] == 'BOLETO'){  ?>
                    
                    <div class="pagamento-confirmado">
                        <ul>
                            <li><img src="<?= $loja['site'] ?>imagens/pagamento-confirmado.gif" alt="Pedido Confirmado"></li>
                            <li>Pagamento confirmado!</li>
                        </ul>                    
                    </div>
                    
                    <div class="pagamento-nao-confirmado">

                        <p>Assim que o pagamento for confirmado, lhe enviaremos um e-mail avisando.</p>      

                        <p class="mb-0">Complete o pedido utilizando a linha digitável do boleto abaixo ou pelo link do boleto.</p>    
                        <p class="mb-2">TOTAL: <span><b><?= 'R$ '.number_format($pedido['valor_parcela'],2,",",".") ?></b></span></p> 

                        <p class="mb-0"><b>LINK:</b></p>
                        <p class="mb-2"><a href="<?= $pedido['asaas_link_fatura'] ?>" target="_blank" class="link-pagamento-asaas"><i><?= $pedido['asaas_link_fatura'] ?></i></a></p>

                        <?php 

                        $boleto_asaas = json_decode($pedido['asaas_boleto'],true);
                        $boleto_asaas_linha_digitavel = $boleto_asaas['identificationField'];

                        ?>

                        <p class="mb-0"><b>LINHA DIGITÁVEL:</b> <textarea id="textarea-linha-digitavel" type="text" class="txt-copy" value=""><?= $boleto_asaas_linha_digitavel ?></textarea></p>
                        <p class="mb-5"><button class="btn-copy" onclick="copiarTexto('textarea-linha-digitavel','Linha copiada!')">Copiar Linha Digitável</button></p>

                        <div id="asaas-pix-do-boleto">

                            <p class="mb-0">Se preferir, pague pelo PIX (Aprova na hora):</p>    

                            <p class="mb-0"><img id="img-pix-asaas" class="img-pix-asaas-sm" src="data:image/png;base64,<?= $pedido['asaas_pix_imagem'] ?>"></p>

                            <p class="mb-0">CHAVE: <textarea id="textarea-chave-pix" type="text" class="txt-copy"><?= $pedido['asaas_pix_chave'] ?></textarea></p>
                            <p class="mb-5"><button class="btn-copy" onclick="copiarTexto('textarea-chave-pix','Chave copiada!')">Copiar Chave Pix</button></p>

                        </div>

                        <p>
                            <b>Enfrentanto problemas?</b><br>Acesse o link de pagamento direto do Asaas, ou entre em contato pelos canais abaixo.
                            <p class="mb-3">LINK DE PAGAMENTO: <a href="<?= $pedido['asaas_link_fatura'] ?>" target="_blank" class="link-pagamento-asaas"><i><?= $pedido['asaas_link_fatura'] ?></i></a></p>
                            <ul>
                                <li class="d-inline-flex col-12 col-md-4 col-xl-2"><a id="btn-whatsapp" href="https://wa.me/55<?= preg_replace("/[^0-9]/", "", $loja['whatsapp']) ?>" target="_blank">LINK DO WHATSAPP</a></li>
                                <li class="d-inline-flex col-12 col-md-4 col-xl-2 ml-0 ml-md-2"><a id="btn-email" href="mailto:<?= $loja['email'] ?>" target="_blank">LINK DO E-MAIL</a></li>
                                <li class="d-inline-flex col-12 col-md-4 col-xl-2 ml-0 ml-md-2"><a id="btn-ajuda" href="<?= $loja['site'].'contato' ?>" target="_blank">PRECISO DE AJUDA</a></li>
                            </ul>         
                        </p>        
                                    
                        <p>Para acompanhar seus pedidos acesse a <a href="cliente-pedidos">ÁREA DO CLIENTE</a> em 'Pedidos' e procure pelo código de referência.</p>
                        <p><b>CÓDIGO DE REFERÊNCIA: <span class="codigo-pedido"><?= $codigo_pedido ?></span></b></p>   

                        <p>Muito obrigado!</p>  

                    </div>

                <?php } ?>

            <?php } ?>

        <?php } else { ?>

            <?php if($pagamento['pagseguro_status'] == 1){ ?>
                
                <?php               

                    //BUSCA OS DADOS DO PIX
                    $busca_pix  = mysqli_query($conn, "SELECT pix_chave, pix_qrcode FROM pagamento WHERE id = 1");
                    $pix        = mysqli_fetch_array($busca_pix);

                ?>

                <p class="mb-0">Complete o pedido utilizando a chave PIX abaixo para realizar a transferência e nos envie o comprovante.</p>    
                <p class="mb-0">TOTAL: <span><b><?= 'R$ '.number_format($pedido['valor_parcela'],2,",",".") ?></b></span></p> 
                <p>CHAVE: <span><b><?= $pix['pix_chave'] ?></b></span></p>

                <?php if($pix['pix_qrcode'] != ''){ ?>
                    <p><img id="carrinho-confirmacao-qrcode-pix" src="../../../imagens/pix/<?= $pix['pix_qrcode'] ?>" class="img-fluid"></p>
                <?php } ?>

                <p>
                    <b>Utilize um dos meios abaixo para enviar o comprovante:</b>
                    <ul>
                        <li class="d-inline-flex col-12 col-md-4 col-xl-2"><a id="btn-whatsapp" href="https://wa.me/55<?= preg_replace("/[^0-9]/", "", $loja['whatsapp']) ?>" target="_blank">LINK DO WHATSAPP</a></li>
                        <li class="d-inline-flex col-12 col-md-4 col-xl-2 ml-0 ml-md-2"><a id="btn-email" href="mailto:<?= $loja['email'] ?>" target="_blank">LINK DO E-MAIL</a></li>
                        <li class="d-inline-flex col-12 col-md-4 col-xl-2 ml-0 ml-md-2"><a id="btn-ajuda" href="<?= $loja['site'].'contato' ?>" target="_blank">PRECISO DE AJUDA</a></li>
                    </ul>         
                </p>        

                <p>Assim que o pagamento for confirmado, lhe enviaremos um e-mail avisando.</p>                   
                <p>Para acompanhar seus pedidos acesse a <a href="cliente-pedidos">ÁREA DO CLIENTE</a> em 'Pedidos' e procure pelo código de referência.</p>
                <p><b>CÓDIGO DE REFERÊNCIA: <span class="codigo-pedido"><?= $codigo_pedido ?></span></b></p>   

                <p>Muito obrigado!</p>  

            <?php } else if($pagamento['asaas_status'] == 1){ ?>

                <div class="pagamento-confirmado">
                    <ul>
                        <li><img src="<?= $loja['site'] ?>imagens/pagamento-confirmado.gif" alt="Pedido Confirmado"></li>
                        <li>Pagamento confirmado!</li>
                    </ul>                    
                </div>

                <div class="pagamento-nao-confirmado">

                    <p class="mb-0">Complete o pedido utilizando a chave PIX abaixo.</p>    
                    <p class="mb-3">TOTAL: <span><b><?= 'R$ '.number_format($pedido['valor_parcela'],2,",",".") ?></b></span></p> 

                    <p class="mb-0">CHAVE: <textarea id="textarea-chave-pix" type="text" class="txt-copy"><?= $pedido['asaas_pix_chave'] ?></textarea></p>
                    <p><button class="btn-copy" onclick="copiarTexto('textarea-chave-pix','Chave copiada!')">Copiar Chave Pix</button></p>

                    <p><img id="img-pix-asaas" src="data:image/png;base64,<?= $pedido['asaas_pix_imagem'] ?>" class="img-fluid"></p>

                    <p>
                        <b>Enfrentanto problemas?</b><br>Acesse o link de pagamento direto do Asaas, ou entre em contato pelos canais abaixo.
                        <p class="mb-3">LINK DE PAGAMENTO: <a href="<?= $pedido['asaas_link_fatura'] ?>" target="_blank" class="link-pagamento-asaas"><i><?= $pedido['asaas_link_fatura'] ?></i></a></p>
                        <ul>
                            <li class="d-inline-flex col-12 col-md-4 col-xl-2"><a id="btn-whatsapp" href="https://wa.me/55<?= preg_replace("/[^0-9]/", "", $loja['whatsapp']) ?>" target="_blank">LINK DO WHATSAPP</a></li>
                            <li class="d-inline-flex col-12 col-md-4 col-xl-2 ml-0 ml-md-2"><a id="btn-email" href="mailto:<?= $loja['email'] ?>" target="_blank">LINK DO E-MAIL</a></li>
                            <li class="d-inline-flex col-12 col-md-4 col-xl-2 ml-0 ml-md-2"><a id="btn-ajuda" href="<?= $loja['site'].'contato' ?>" target="_blank">PRECISO DE AJUDA</a></li>
                        </ul>         
                    </p>        

                                
                    <p>Para acompanhar seus pedidos acesse a <a href="cliente-pedidos">ÁREA DO CLIENTE</a> em 'Pedidos' e procure pelo código de referência.</p>
                    <p><b>CÓDIGO DE REFERÊNCIA: <span class="codigo-pedido"><?= $codigo_pedido ?></span></b></p>   

                    <p>Muito obrigado!</p>  

                </div>

            <?php } ?>

        <?php } ?> 

        <?php

    } else {
        ?><script> window.location.href = '/'; </script><?php
    }
    
    //VERIFICA SE JÁ NÂO CADASTROU A AVALIAÇÃO PARA SER RESPONDIDA
    $busca_avaliacao = mysqli_query($conn, "SELECT id FROM avaliacao WHERE id_pedido = '".$pedido['id_pedido']."' AND tipo = 'EXPERIENCIA-COMPRA'");
    
    //SE NÃO, CADASTRA
    if(mysqli_num_rows($busca_avaliacao) == 0){

        //GERA UM IDENTIFICADOR
        $identificador_avaliacao = md5(time().$pedido['carrinho_identificador'].$pedido['id_pedido'].'EXPERIENCIA-COMPRA');
        
        //CADASTRA COM STATUS 0
        mysqli_query($conn, "INSERT INTO avaliacao (identificador, id_pedido, tipo) VALUES ('$identificador_avaliacao','".$pedido['id_pedido']."','EXPERIENCIA-COMPRA')");

        //CADASTRA A PESQUISA DOS PRODUTOS
        $busca_carrinho = mysqli_query($conn, "
            SELECT cp.id_produto
            FROM carrinho_produto AS cp
            LEFT JOIN carrinho AS c ON c.id = cp.id_carrinho 
            LEFT JOIN pedido AS p ON p.id_carrinho = c.id
            WHERE p.codigo = '".$codigo_pedido."' AND cp.status = 1
        ");
        while($carrinho = mysqli_fetch_array($busca_carrinho)){
            $identificador_avaliacao_produto = md5(time().$pedido['carrinho_identificador'].$pedido['id_pedido'].$carrinho['id_produto']);
            mysqli_query($conn, "INSERT INTO avaliacao (identificador, id_pedido, id_produto, tipo) VALUES ('$identificador_avaliacao_produto','".$pedido['id_pedido']."','".$carrinho['id_produto']."','PRODUTO')");
        }

    }

    //SE NÃO FOI RESPONDIDA AINDA
    $busca_avaliacao = mysqli_query($conn, "SELECT id FROM avaliacao WHERE id_pedido = '".$pedido['id_pedido']."' AND status = 1 AND tipo = 'EXPERIENCIA-COMPRA'");

    //OFERECE PARA RESPONDER
    if(mysqli_num_rows($busca_avaliacao) == 0){
        
    ?>
    
        <div id="avaliacao-loja">
            <h2>Conte-nos como foi sua experiência em nossa loja:</h2>
            <ul>
                <li><img class="estrela" estrela="1" id="estrela-1" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito ruim" alt="1 estrela"></li>
                <li><img class="estrela" estrela="2" id="estrela-2" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Ruim" alt="2 estrelas"></li>
                <li><img class="estrela" estrela="3" id="estrela-3" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Regular" alt="3 estrelas"></li>
                <li><img class="estrela" estrela="4" id="estrela-4" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Boa" alt="4 estrelas"></li>
                <li><img class="estrela" estrela="5" id="estrela-5" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito boa" alt="5 estrelas"></li>
            </ul>
            <div class="form-group mt-3">
                <textarea name="observacoes" id="observacoes" class="form-control" cols="30" rows="5" placeholder="Alguma observação para que possamos melhorar?"></textarea>
                <small><b>MÍNIMO DE CARACTERES: 30 - TOTAL: <span id="observacoes-caracteres">0</span></b></small>
            </div>
            <div class="form-group mt-3">               
                <input class="btn-escuro" type="button" value="Enviar" onclick="javascript: enviaPesquisaSatisfacao();">
            </div>
        </div>

    <?php } ?>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="modulos/carrinho/js/scripts-1.1.js"></script>

<?php

} else {

    ?><script> window.location.href = '/'; </script><?php

} 