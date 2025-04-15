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
}

?>

<!--SECTION CONFIGURAÇÕES-->
<section id="configuracoes-nota-fiscal">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Configurações de nota fiscal</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes.php';">VOLTAR</button>
            </div>
        </div>
        
        <!-- FORM DE EDIÇÃO -->
        <form enctype="multipart/form-data" action="modulos/configuracoes/php/edicao-nota-fiscal.php" method="POST">      
                                   
            <div class="row mb-3">                            
                <div class="col-12">                        
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input text-uppercase" id="asaas-nf" name="asaas-nf" <?php if($pagamento['asaas_status_nf'] == 1){ echo 'checked'; } ?>>
                        <label class="custom-control-label" for="asaas-nf">Ativar Notas Fiscais</label>
                        <small>Com esta opção ativada as notas fiscais são geradas automaticamente após a confirmação do pagamento do pedido</small>
                    </div>
                </div>   
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="asaas-producao" name="asaas-ambiente" value="P" class="custom-control-input" <?php if($pagamento['asaas_ambiente_nf'] == 'P'){ echo 'checked'; } ?>>
                        <label class="custom-control-label" for="asaas-producao">Produção</label>
                    </div>
                        <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="asaas-sandbox" name="asaas-ambiente" value="S" class="custom-control-input" <?php if($pagamento['asaas_ambiente_nf'] == 'S'){ echo 'checked'; } ?>>
                        <label class="custom-control-label" for="asaas-sandbox">Sandbox</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="asaas-token">Token <span class="campo-obrigatorio">*</span></label>
                        <input type="password" class="form-control" name="asaas-token" id="asaas-token" value="<?= $pagamento['asaas_token_nf'] ?>" required>
                    </div>
                </div>
            </div>    

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="asaas-emails">E-mails</label>
                        <input type="text" class="form-control" name="asaas-emails" id="asaas-emails" value="<?= $pagamento['asaas_nf_emails'] ?>">
                        <small>Separe por ponto e virgula e-mails que vão receber as notas fiscais da loja</small>
                        <small>Para não enviar e-mails deixe o campo em branco</small>
                    </div>
                </div>
            </div>   

            <hr>            

            <div class="row admin-subtitulo"><div class="col-12">Impostos</div></div>

            <div class="row">
                <div class="col-12 col-lg-3">
                    <div class="form-group">
                        <label for="asaas-deducao">Deduções</label>
                        <input type="text" class="form-control mascara-double" name="asaas-deducao" id="asaas-deducao" value="<?= number_format($pagamento['asaas_nf_deducoes'],2,',','.') ?>">
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    <div class="form-group">
                        <label for="asaas-reter-iss">Reter ISS</label>
                        <select class="form-control" name="asaas-reter-iss" id="asaas-reter-iss">
                            <option value="0" <?php if($pagamento['asaas_nf_reter_iss'] == 0){ echo 'selected'; } ?>>Não</option>
                            <option value="1" <?php if($pagamento['asaas_nf_reter_iss'] == 1){ echo 'selected'; } ?>>Sim</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    <div class="form-group">
                        <label for="asaas-iss">ISS</label>
                        <input type="text" class="form-control mascara-double" name="asaas-iss" id="asaas-iss" value="<?= number_format($pagamento['asaas_nf_iss'],2,',','.') ?>">
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    <div class="form-group">
                        <label for="asaas-cofins">COFINS</label>
                        <input type="text" class="form-control mascara-double" name="asaas-cofins" id="asaas-cofins" value="<?= number_format($pagamento['asaas_nf_cofins'],2,',','.') ?>">
                    </div>
                </div>
            </div>        

            <div class="row">
                <div class="col-12 col-lg-3">
                    <div class="form-group">
                        <label for="asaas-csll">CSLL</label>
                        <input type="text" class="form-control mascara-double" name="asaas-csll" id="asaas-csll" value="<?= number_format($pagamento['asaas_nf_csll'],2,',','.') ?>">
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    <div class="form-group">
                        <label for="asaas-inss">INSS</label>
                        <input type="text" class="form-control mascara-double" name="asaas-inss" id="asaas-inss" value="<?= number_format($pagamento['asaas_nf_inss'],2,',','.') ?>">
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    <div class="form-group">
                        <label for="asaas-ir">IR</label>
                        <input type="text" class="form-control mascara-double" name="asaas-ir" id="asaas-ir" value="<?= number_format($pagamento['asaas_nf_ir'],2,',','.') ?>">
                    </div>
                </div>
                <div class="col-12 col-lg-3">
                    <div class="form-group">
                        <label for="asaas-pis">PIS</label>
                        <input type="text" class="form-control mascara-double" name="asaas-pis" id="asaas-pis" value="<?= number_format($pagamento['asaas_nf_pis'],2,',','.') ?>">
                    </div>
                </div>
            </div>   
            
            <hr>            

            <div class="row admin-subtitulo"><div class="col-12">Serviço municipal</div></div>

            <div class="row">
                <div class="col-12 col-lg-4">
                    <div class="form-group">
                        <label for="asaas-id-serv-municipal">Identificador único do serviço municipal</label>
                        <input type="text" class="form-control" name="asaas-id-serv-municipal" id="asaas-id-serv-municipal" value="<?= $pagamento['asaas_id_serv_municipal'] ?>">
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="form-group">
                        <label for="asaas-cod-serv-municipal">Código de serviço municipal</label>
                        <input type="text" class="form-control" name="asaas-cod-serv-municipal" id="asaas-cod-serv-municipal" value="<?= $pagamento['asaas_cod_serv_municipal'] ?>">
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="form-group">
                        <label for="asaas-name-serv-municipal">Nome do serviço municipal</label>
                        <input type="text" class="form-control" name="asaas-name-serv-municipal" id="asaas-name-serv-municipal" value="<?= $pagamento['asaas_nome_serv_municipal'] ?>">
                        <small>Se não for informado, será utilizado o campo 'Código de serviço municipal' como nome para identificação.</small>
                    </div>
                </div>
            </div>        

            <!--

            <hr>            

            <div class="row admin-subtitulo"><div class="col-12">Informações fiscais</div></div>

            <div class="row">
                <div class="col-12 col-lg-4">
                    <div class="form-group">
                        <label for="email">Email para notificações de notas fiscais <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control" name="email" id="email" value="<?= $if_email ?>" required>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="form-group">
                        <label for="municipalInscription">Inscrição municipal da empresa</label>
                        <input type="text" class="form-control" name="municipalInscription" id="municipalInscription" value="<?= $if_municipalInscription ?>">
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="form-group">
                        <label for="stateInscription">Inscrição estadual da empresa</label>
                        <input type="text" class="form-control" name="stateInscription" id="stateInscription" value="<?= $if_stateInscription ?>">
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="form-group">
                        <label for="simplesNacional">Optante pelo simples nacional <span class="campo-obrigatorio">*</span></label>
                        <select class="form-control" name="simplesNacional" id="simplesNacional" required>
                            <option value="1" <?php if($if_simplesNacional){ echo 'selected'; } ?>>Sim</option>
                            <option value="0" <?php if(!$if_simplesNacional){ echo 'selected'; } ?>>Não</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="form-group">
                        <label for="culturalProjectsPromoter">Classificada como incentivador cultural</label>
                        <select class="form-control" name="culturalProjectsPromoter" id="culturalProjectsPromoter">
                            <option value="1" <?php if($if_culturalProjectsPromoter){ echo 'selected'; } ?>>Sim</option>
                            <option value="0" <?php if(!$if_culturalProjectsPromoter){ echo 'selected'; } ?>>Não</option>
                        </select>
                    </div>
                </div> 
                <div class="col-12 col-lg-4">
                    <div class="form-group">
                        <label for="cnae">Código CNAE</label>
                        <input type="text" class="form-control" name="cnae" id="cnae" value="<?= $if_cnae ?>">
                    </div>
                </div> 
                <div class="col-12">
                    <div class="form-group">
                        <label for="specialTaxRegime">Identificador do regime especial de tributação</label>
                        <input type="text" class="form-control" name="specialTaxRegime" id="specialTaxRegime" value="<?= $if_specialTaxRegime ?>">
                        <small>Empresas do simples nacional geralmente optam pelo Microempresa Municipal</small>
                    </div>
                </div> 
                <div class="col-12">
                    <div class="form-group">
                        <label for="serviceListItem">Item da lista de serviço, conforme <a href="http://www.planalto.gov.br/ccivil_03/leis/LCP/Lcp116.htm" target="_blank">http://www.planalto.gov.br/ccivil_03/leis/LCP/Lcp116.htm</a></label>
                        <textarea class="form-control" name="serviceListItem" id="serviceListItem" rows="3"><?= $if_serviceListItem ?></textarea>
                    </div>
                </div> 
                <div class="col-12">
                    <div class="form-group">
                        <label for="rpsSerie">Número de Série cadastrado para a empresa</label>
                        <input type="text" class="form-control" name="rpsSerie" id="rpsSerie" value="<?= $if_rpsSerie ?>">
                        <small>Número de Série utilizado pela sua empresa para emissão de notas fiscais. Na maioria das cidades o número de série utilizado é '1' ou 'E'</small>
                    </div>
                </div> 
                <div class="col-12">
                    <div class="form-group">
                        <label for="rpsNumber">Número de RPS utilizado na última nota fiscal emitida para a sua empresa</label>
                        <input type="text" class="form-control" name="rpsNumber" id="rpsNumber" value="<?= $if_rpsNumber ?>">
                        <small>Número do RPS utilizado na última nota fiscal emitida pela sua empresa. Se a sua última NF emitida tem RPS igual a '100', esse campo deve ser preenchido com '101'. Se você nunca emitiu notas fiscais pelo site da sua prefeitura, informe '1' nesse campo</small>
                    </div>
                </div> 
                <div class="col-12">
                    <div class="form-group">
                        <label for="loteNumber">Número do Lote utilizado na última nota fiscal emitida pela sua empresa</label>
                        <input type="text" class="form-control" name="loteNumber" id="loteNumber" value="<?= $if_loteNumber ?>">
                        <small>Número do Lote utilizado na última nota fiscal emitida pela sua empresa. Se o último lote utilizado na sua prefeitura for '25', esse campo deve ser preenchido com '26'. Informe esse campo apenas se sua prefeitura exigir a utilização de lotes</small>
                    </div>
                </div>  
                <div class="col-12">
                    <div class="form-group">
                        <label for="username">Usuário para acesso ao site da prefeitura da sua cidade</label>
                        <input type="text" class="form-control" name="username" id="username" value="<?= $if_username ?>">
                    </div>
                </div>  
                <div class="col-12">
                    <div class="form-group">
                        <label for="password">Senha para acesso ao site da prefeitura</label>
                        <input type="password" class="form-control" name="password" id="password" value="<?= $if_username ?>">
                    </div>
                </div>  
                <div class="col-12">
                    <div class="form-group">
                        <label for="accessToken">Token de acesso ao site da prefeitura</label>
                        <input type="password" class="form-control" name="accessToken" id="accessToken" value="<?= $if_accessToken ?>">
                        <small>Caso o acesso ao site da sua prefeitura seja através por Token</small>
                    </div>
                </div>  
                <div class="col-12">
                    <div class="form-group">
                        <label for="certificateFile">Arquivo (.pfx ou .p12) do certificado digital da empresa</label>
                        <input type="file" class="form-control" name="certificateFile" id="certificateFile" value="<?= $if_certificateFile ?>">
                        <small>Caso o acesso ao site da sua prefeitura através de certificado digital</small>
                    </div>
                </div> 
                <div class="col-12">
                    <div class="form-group">
                        <label for="certificatePassword">Senha do certificado digital enviado</label>
                        <input type="password" class="form-control" name="certificatePassword" id="certificatePassword" value="<?= $if_certificatePassword ?>">
                        <small>Caso o acesso ao site da sua prefeitura através de certificado digital</small>
                    </div>
                </div>   

                -->

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