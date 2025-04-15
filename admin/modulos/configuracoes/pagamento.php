<!--CSS-->
<link rel="stylesheet" href="modulos/configuracoes/css/style.css">

<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE TENTAR ACESSAR COMO USUÁRIO OU ADMINISTRADOR, DESLOGA DO SISTEMA
if($nivel_usuario == 'U' | $nivel_usuario == 'A'){
    echo "<script>location.href='logout.php';</script>";
} else if($nivel_usuario == 'M' | $nivel_usuario == 'S'){
    
    $busca_pagamento = mysqli_query($conn, 'SELECT * FROM pagamento WHERE id = 1'); 
    $pagamento       = mysqli_fetch_array($busca_pagamento);

    $faixas_desconto = mysqli_query($conn, 'SELECT * FROM pagamento_faixa_desconto WHERE status = 1 ORDER BY id ASC');
    $total_faixas_desconto = mysqli_num_rows($faixas_desconto);
    $contador_faixas_desconto = 0;

}

?>

<!--SECTION CONFIGURAÇÕES-->
<section id="configuracoes-pagamento">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Configurações de pagamento</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes.php';">VOLTAR</button>
            </div>
        </div>
        
        <!-- FORM DE EDIÇÃO -->
        <form enctype="multipart/form-data" action="modulos/configuracoes/php/edicao-pagamento.php" method="POST"> 
                                        
            <!-- ABAS -->
            <ul class="nav nav-tabs mb-4" id="tab-payment" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-pagamento" data-name="pagamento" data-toggle="tab" href="#conteudo-tab-pagamento" role="tab" aria-controls="conteudo-tab-pagamento" aria-selected="true">Pagamento</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-faixas-desconto" data-name="faixas-desconto" data-toggle="tab" href="#conteudo-tab-faixas-desconto" role="tab" aria-controls="conteudo-tab-faixas-desconto" aria-selected="false">Faixas de desconto</a>
                </li>
            </ul>  

            <div class="tab-content">   
    
                <div class="tab-pane active" id="conteudo-tab-pagamento" role="tabpanel" aria-labelledby="tab-pagamento">  
                        
                    <div class="row">
                        <div class="col-12">
                            <label>PAGAMENTO ATIVO</label>
                        </div>
                    </div>
                
                    <div class="row">                            
                        <div class="col-12">                        
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input text-uppercase" id="ativar-asaas" name="ativar-pagamento" value="asaas" <?php if($pagamento['asaas_status'] == 1){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="ativar-asaas">Asaas</label>
                            </div>                       
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input text-uppercase" id="ativar-pagseguro" name="ativar-pagamento" value="pagseguro" <?php if($pagamento['pagseguro_status'] == 1){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="ativar-pagseguro">PagSeguro</label>
                            </div>            
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input text-uppercase" id="ativar-nenhum" name="ativar-pagamento" value="nenhum" <?php if($pagamento['asaas_status'] == 0 & $pagamento['pagseguro_status'] == 0){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="ativar-nenhum">Nenhum</label>
                            </div>
                        </div>   
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">                    
                            <small>Deixe selecionado o sistema de pagamento que deseja oferecer aos clientes</small>
                        </div>
                    </div>
                            
                    <!-- ABAS -->
                    <ul class="nav nav-tabs mb-4" id="tab-payment" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link <?php if($pagamento['asaas_status'] == 1 | ($pagamento['asaas_status'] == 0 & $pagamento['pagseguro_status'] == 0)){ echo 'active'; } ?>" id="tab-asaas" data-name="asaas" data-toggle="tab" href="#conteudo-tab-asaas" role="tab" aria-controls="conteudo-tab-asaas" aria-selected="true">Asaas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php if($pagamento['pagseguro_status'] == 1){ echo 'active'; } ?>" id="tab-pagseguro" data-name="pagseguro" data-toggle="tab" href="#conteudo-tab-pagseguro" role="tab" aria-controls="conteudo-tab-pagseguro" aria-selected="false">Pag Seguro</a>
                        </li>
                    </ul>
                
                    <div class="tab-content">

                        <div class="tab-pane <?php if($pagamento['asaas_status'] == 1 | ($pagamento['asaas_status'] == 0 & $pagamento['pagseguro_status'] == 0)){ echo 'active'; } ?>" id="conteudo-tab-asaas" role="tabpanel" aria-labelledby="tab-asaas">  
                            
                            <div class="row">
                                <div class="col-12">          
                                    <div class="alert alert-success text-center <?php if($pagamento['asaas_status'] == 1){ echo 'd-block'; } else { echo 'd-none'; } ?>" role="alert">
                                        <b>ATIVADO</b>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">         
                                    <div class="alert alert-danger text-center <?php if($pagamento['asaas_status'] == 1){ echo 'd-none'; } else { echo 'd-block'; } ?>" role="alert">
                                        <b>DESATIVADO</b>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">    
                                    <label>Ambiente <span class="campo-obrigatorio">*</span></label>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="asaas-producao" name="asaas-ambiente" value="P" class="custom-control-input" <?php if($pagamento['asaas_ambiente'] == 'P'){ echo 'checked'; } ?>>
                                        <label class="custom-control-label" for="asaas-producao">Produção</label>
                                    </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="asaas-sandbox" name="asaas-ambiente" value="S" class="custom-control-input" <?php if($pagamento['asaas_ambiente'] == 'S'){ echo 'checked'; } ?>>
                                        <label class="custom-control-label" for="asaas-sandbox">Sandbox</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="token">Token <span class="campo-obrigatorio">*</span></label>
                                        <input type="password" class="form-control" name="asaas-token" id="asaas-token" value="<?= $pagamento['asaas_token'] ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="asaas-parcelas">Máximo de Parcelas <span class="campo-obrigatorio">*</span></label>
                                        <input type="number" class="form-control" name="asaas-parcelas" id="asaas-parcelas" value="<?= $pagamento['asaas_parcelas'] ?>">
                                        <small>Número máximo de parcelas</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="asaas-parcelas-juros">Juros a partir de <span class="campo-obrigatorio">*</span></label>
                                        <input type="number" class="form-control" name="asaas-parcelas-juros" id="asaas-parcelas-juros" value="<?= $pagamento['asaas_parcelas_juros'] ?>">
                                        <small>Preencha com 0 caso não queira juros</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="asaas-parcelas-juros-porcentagem">% de juros <span class="campo-obrigatorio">*</span></label>
                                        <input type="text" class="form-control" name="asaas-parcelas-juros-porcentagem" id="valor" value="<?= number_format($pagamento['asaas_parcelas_juros_porcentagem'],2,',','.') ?>">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="asaas-juros-tipo">Tipo do juros <span class="campo-obrigatorio">*</span></label>
                                        <select class="form-control" name="asaas-juros-tipo" id="asaas-juros-tipo">
                                            <option value="1" <?php if($pagamento['asaas_juros_tipo'] == 1){ echo 'selected'; } ?>>Simples</option>
                                            <option value="2" <?php if($pagamento['asaas_juros_tipo'] == 2){ echo 'selected'; } ?>>Composto</option>
                                        </select>                                        
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">    
                                    <label>Serviços <span class="campo-obrigatorio">*</span></label>
                                </div>
                            </div>

                            <div class="row mb-5">
                                <div class="col-12">                                               
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input" id="asaas-pix" name="asaas-pix" <?php if($pagamento['asaas_pix'] == 1){ echo 'checked'; } ?>>
                                        <label class="custom-control-label" for="asaas-pix">Pix</label>
                                    </div>                                               
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input" id="asaas-boleto" name="asaas-boleto" <?php if($pagamento['asaas_boleto'] == 1){ echo 'checked'; } ?>>
                                        <label class="custom-control-label" for="asaas-boleto">Boleto</label>
                                    </div>                                               
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input" id="asaas-cartao-credito" name="asaas-cartao-credito" <?php if($pagamento['asaas_cc'] == 1){ echo 'checked'; } ?>>
                                        <label class="custom-control-label" for="asaas-cartao-credito">Cartão de crédito</label>
                                    </div>        
                                    <small>Selecione os serviços que deseja disponibilizar aos clientes</small>      
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane <?php if($pagamento['pagseguro_status'] == 1){ echo 'active'; } ?>" id="conteudo-tab-pagseguro" role="tabpanel" aria-labelledby="tab-pagseguro">
                
                            <div class="row">
                                <div class="col-12">          
                                    <div class="alert alert-success text-center <?php if($pagamento['pagseguro_status'] == 1){ echo 'd-block'; } else { echo 'd-none'; } ?>" role="alert">
                                        <b>ATIVADO</b>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">         
                                    <div class="alert alert-danger text-center <?php if($pagamento['pagseguro_status'] == 1){ echo 'd-none'; } else { echo 'd-block'; } ?>" role="alert">
                                        <b>DESATIVADO</b>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">    
                                    <label>Ambiente <span class="campo-obrigatorio">*</span></label>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="producao" name="ambiente" value="P" class="custom-control-input" <?php if($pagamento['ambiente'] == 'P'){ echo 'checked'; } ?>>
                                        <label class="custom-control-label" for="producao">Produção</label>
                                    </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="sandbox" name="ambiente" value="S" class="custom-control-input" <?php if($pagamento['ambiente'] == 'S'){ echo 'checked'; } ?>>
                                        <label class="custom-control-label" for="sandbox">Sandbox</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="token">Token <span class="campo-obrigatorio">*</span></label>
                                        <input type="password" class="form-control" name="token" id="token" maxlength="100" value="<?= $pagamento['token'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="email">E-mail <span class="campo-obrigatorio">*</span></label>
                                        <input type="email" class="form-control" name="email" id="email" maxlength="50" value="<?= $pagamento['email'] ?>">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="site">Site <span class="campo-obrigatorio">*</span></label>
                                        <input type="url" class="form-control" name="site" id="site" maxlength="100" value="<?= $pagamento['site'] ?>">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="parcelas">Nº Parcelas <span class="campo-obrigatorio">*</span></label>
                                        <input type="number" class="form-control" name="parcelas" id="parcelas" value="<?= $pagamento['parcelas'] ?>">
                                        <small>Número máximo de parcelas</small>
                                    </div>
                                </div>
                            </div>

                            <hr>            

                            <div class="row admin-subtitulo">
                                <div class="col-12">
                                    PIX Manual                        
                                    <small>Com o pagamento por PIX Manual ativado o cliente pode fazer uma transferência e enviar o comprovante pelo WhatsApp.</small>
                                </div>
                            </div>

                            <div class="row mb-3">                            
                                <div class="col-12">                        
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input text-uppercase" id="pix" name="pix" <?php if($pagamento['pix'] == 1){ echo 'checked'; } ?>>
                                        <label class="custom-control-label" for="pix">Ativar Pix</label>
                                    </div>
                                </div>   
                            </div>

                            <div id="dados-pix" <?php if($pagamento['pix'] == 0){ echo 'class="dados-pix-desativado"'; } ?>>
                            
                                <div class="row">   
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="chave-pix">Chave PIX <span class="campo-obrigatorio">*</span></label>
                                            <input type="text" class="form-control" name="chave-pix" id="chave-pix" maxlength="200" value="<?= $pagamento['pix_chave'] ?>">
                                        </div>
                                    </div>  
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="imagem">QRCode</label>
                                            <input type="file" name="imagem" id="imagem" class="imagem form-control-file" accept="image/png, image/jpeg" onchange="javascript: inputFileChange();">
                                            <input type="text" name="arquivo" id="arquivo" class="arquivo" placeholder="Selecionar arquivo" readonly="readonly" value="<?= $pagamento['pix_qrcode'] ?>">
                                            <input type="button" id="btn-escolher" class="btn-escolher" value="ESCOLHER" onclick="javascript: inputFileEscolher();">
                                        </div>            
                                    </div>  
                                </div>

                            </div>
                            
                        </div>

                    </div>
                    
                    <div class="tab-pane <?php if($pagamento['asaas_status'] == 0 & $pagamento['pagseguro_status'] == 0){ echo 'active'; } ?>" id="conteudo-tab-nenhum" role="tabpanel" aria-labelledby="tab-nenhum">
                    </div>
                    
                </div>

                <div class="tab-pane" id="conteudo-tab-faixas-desconto" role="tabpanel" aria-labelledby="tab-faixas-desconto">  
                    
                    <div class="row admin-subtitulo mb-3">
                        <div class="col-12">
                            FAIXAS DE DESCONTO                        
                            <small>Você pode configurar faixas de preço com desconto para tipos de pagamento.</small>
                            <small>Ex: Compras entre R$ 100,00 e R$ 199,00 tem 3% de desconto no PIX.</small>
                            <small>Ex: Compras acima de R$ 200,00 tem 5% de desconto no PIX.</small>
                            <small><b>OBS: Para o modelo 'acima de' deixe o segundo campo de valor (Até) em branco.</b></small>
                            <small><b>OBS: As faixas de desconto não são acumulativas. No caso do cliente usar um cupom, elas não serão oferecidas.</b></small>
                        </div>
                    </div>

                    <div>

                        <div id="faixas-desconto">

                            <input type="hidden" id="n-faixas-desconto" name="n-faixas-desconto" value="<?= $total_faixas_desconto ?>">

                            <?php while($faixa_desconto = mysqli_fetch_array($faixas_desconto)){ ?>

                                <?php $contador_faixas_desconto++ ?>

                                <div class="row mt-3 mt-lg-0" id="faixa-desconto-<?= $contador_faixas_desconto ?>">
                                    <input type="hidden" name="faixa-desconto-identificador-<?= $contador_faixas_desconto ?>" value="<?= $faixa_desconto['identificador'] ?>">
                                    <div class="col-12 col-lg-3">
                                        <div class="row">
                                            <div class="col-2 col-sm-1 col-lg-2">
                                                <a href="javascript: removeFaixaDescontoPagamento(<?= $contador_faixas_desconto ?>,true,'<?= $faixa_desconto['identificador'] ?>')"><img class="faixas-desconto-botao <?php if($contador_faixas_desconto == 1){ echo 'remove-faixa-desconto-primeiro'; } ?> " src="<?= $loja['site'] ?>imagens/remover.png"></a>
                                            </div>
                                            <div class="col-10 col-sm-11 col-lg-10">
                                                <div class="form-group">
                                                    <?php if($contador_faixas_desconto == 1){ ?><label class="d-none d-lg-block" for="faixa-desconto-tipo-pagamento">Tipo</label><?php } ?>
                                                    <select name="faixa-desconto-tipo-pagamento-<?= $contador_faixas_desconto ?>" id="faixa-desconto-tipo-pagamento-<?= $contador_faixas_desconto ?>" class="form-control" required>
                                                        <option value="1" <?php if($faixa_desconto['tipo'] == 'PIX'){ echo 'selected'; } ?>>PIX</option>
                                                        <option value="2" <?php if($faixa_desconto['tipo'] == 'BOLETO'){ echo 'selected'; } ?>>Boleto</option>
                                                        <option value="3" <?php if($faixa_desconto['tipo'] == 'CARTAO'){ echo 'selected'; } ?>>Cartão</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-lg-3">
                                        <div class="form-group">
                                            <?php if($contador_faixas_desconto == 1){ ?><label class="d-none d-lg-block" for="faixa-desconto-de">De</label><?php } ?>
                                            <input type="text" value="<?= 'R$ '.number_format($faixa_desconto['de'], 2, ',', '.') ?>" name="faixa-desconto-de-<?= $contador_faixas_desconto ?>" id="faixa-desconto-de-<?= $contador_faixas_desconto ?>" class="form-control faixa-desconto-de" placeholder="De" required>
                                        </div>
                                    </div>
                                    <div class="col-6 col-lg-3">
                                        <div class="form-group">
                                            <?php if($contador_faixas_desconto == 1){ ?><label class="d-none d-lg-block" for="faixa-desconto-ate">Até</label><?php } ?>
                                            <input type="text" value="<?php if($faixa_desconto['ate'] != 0 & $faixa_desconto['ate'] != 999999){ echo 'R$ '.number_format($faixa_desconto['ate'], 2, ',', '.'); } ?>" name="faixa-desconto-ate-<?= $contador_faixas_desconto ?>" id="faixa-desconto-ate-<?= $contador_faixas_desconto ?>" class="form-control faixa-desconto-ate" placeholder="Até">
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-3">
                                        <div class="row">
                                            <div class="col-5 pr-0">
                                                <div class="form-group">
                                                    <?php if($contador_faixas_desconto == 1){ ?><label class="d-none d-lg-block" for="faixa-desconto-porcentagem">%</label><?php } ?>
                                                    <input type="text" value="<?php if($faixa_desconto['porcentagem_desconto'] != 0){ echo number_format($faixa_desconto['porcentagem_desconto'], 2, ',', '.'); } ?>" name="faixa-desconto-porcentagem-<?= $contador_faixas_desconto ?>" id="faixa-desconto-porcentagem-<?= $contador_faixas_desconto ?>" class="form-control faixa-desconto-porcentagem" placeholder="%">
                                                </div>
                                            </div>
                                            <div class="col-2 p-0 d-flex align-items-center justify-content-center <?php if($contador_faixas_desconto == 1){ echo 'valor-ou-porcentagem-primeiro'; } ?>">ou</div>
                                            <div class="col-5 pl-0">    
                                                <div class="form-group">
                                                    <?php if($contador_faixas_desconto == 1){ ?><label class="d-none d-lg-block" for="faixa-desconto-valor">Valor</label><?php } ?>
                                                    <input type="text" value="<?php if($faixa_desconto['valor_desconto'] != 0){ echo number_format($faixa_desconto['valor_desconto'], 2, ',', '.'); } ?>" name="faixa-desconto-valor-<?= $contador_faixas_desconto ?>" id="faixa-desconto-valor-<?= $contador_faixas_desconto ?>" class="form-control faixa-desconto-porcentagem" placeholder="Valor">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>

                        </div>

                        <div class="row mt-2">
                            <div class="col-12">
                                <a href="javascript: addFaixaDescontoPagamento();" id="btn-add-faixa-desconto"><img class="faixas-desconto-botao" src="<?= $loja['site'] ?>imagens/adicionar.png">Adicionar faixa de desconto</a>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <div class="row mt-3">
                <div class="col-12 text-center text-md-right">
                    <div class="form-group">
                        <button type="submit" class="btn btn-dark btn-bottom">SALVAR</button>
                    </div>
                </div>
            </div>

        </form>

    </div>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="modulos/configuracoes/js/scripts.js"></script>