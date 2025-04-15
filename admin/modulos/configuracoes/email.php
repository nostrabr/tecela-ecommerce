<!--CSS-->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE TENTAR ACESSAR COMO USUÁRIO OU ADMINISTRADOR, DESLOGA DO SISTEMA
if($nivel_usuario == 'U' | $nivel_usuario == 'A'){
    echo "<script>location.href='logout.php';</script>";
} else if($nivel_usuario == 'M' | $nivel_usuario == 'S'){
    $busca_loja = mysqli_query($conn, 'SELECT * FROM loja WHERE id = 1'); 
    $loja       = mysqli_fetch_array($busca_loja);
    $busca_pagamento = mysqli_query($conn, 'SELECT * FROM pagamento WHERE id = 1'); 
    $pagamento       = mysqli_fetch_array($busca_pagamento);
}

?>

<!--SECTION CONFIGURAÇÕES-->
<section id="configuracoes-email">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Configuração de e-mail</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes.php';">VOLTAR</button>
            </div>
        </div>
        
        <!-- FORM DE EDIÇÃO -->
        <form action="modulos/configuracoes/php/edicao-email.php" method="POST">
            <div class="row <?php if($nivel_usuario != 'S'){ echo 'd-none'; } ?>">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="email">E-mail <span class="campo-obrigatorio">*</span></label>
                        <input type="email" class="form-control text-lowercase" name="email" id="email" maxlength="50" value="<?= $loja['email_sistema'] ?>">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="senha">Senha <span class="campo-obrigatorio">*</span></label>
                        <input type="password" class="form-control" name="senha" id="senha" maxlength="50" value="<?= $loja['email_sistema_senha'] ?>">
                    </div>
                </div>
            </div>
            <div class="row <?php if($nivel_usuario != 'S'){ echo 'd-none'; } ?>">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="host">Host <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control text-lowercase" name="host" id="host" maxlength="50" value="<?= $loja['email_sistema_host'] ?>">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="porta">Porta <span class="campo-obrigatorio">*</span></label>
                        <input type="number" class="form-control" name="porta" id="porta" value="<?= $loja['email_sistema_porta'] ?>">
                    </div>
                </div>
            </div>        
            <div class="row <?php if($nivel_usuario != 'S'){ echo 'd-none'; } ?>">                            
                <div class="col-12 mb-3">                        
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input text-uppercase" id="issmtp" name="issmtp" <?php if($loja['email_issmtp'] == 1){ echo 'checked'; } ?> >
                        <label class="custom-control-label" for="issmtp">isSMTP</label>
                    </div>
                </div>   
            </div>
            <div class="row">
                <div class="col-12">
                    <label for="email-adicional">E-mails adicionais</label>
                    <input type="text" class="form-control" name="email-adicional" id="email-adicional" value="<?= $loja['email_adicional'] ?>">
                    <small>Cadastre outros e-mails que irão receber os envios do sistema separados por vírgula. Ex: estoque@gmail.com, auxiliar@hotmail.com</small>
                </div>
            </div>   
            <div class="row mt-3">
                <div class="col-12">
                    <label for="summernote">Cabeçalho</label>
                    <textarea id="summernote" class="summernote" name="summernote"><?= $loja['email_cabecalho'] ?></textarea> 
                    <small>Variáveis aceitas: {loja_endereco}, {loja_telefone}, {loja_whatsapp}, {loja_email}, {loja_nome}, {loja_site}</small>
                </div>
            </div>    
            <div class="row mt-3">
                <div class="col-12">
                    <label for="summernote2">Contato</label>
                    <textarea id="summernote2" class="summernote2" name="summernote2"><?= $loja['email_contato'] ?></textarea>
                    <small>Variáveis aceitas: {cliente_nome}, {cliente_email}, {cliente_telefone}, {mensagem_contato}, {loja_endereco}, {loja_telefone}, {loja_whatsapp}, {loja_email}, {loja_nome}, {loja_site}</small>
                </div>
            </div>      
            <div class="row mt-3">
                <div class="col-12">
                    <label for="summernote3">Cadastro de cliente</label>
                    <textarea id="summernote3" class="summernote3" name="summernote3"><?= $loja['email_cadastro_cliente'] ?></textarea>
                    <small>Variáveis aceitas: {cliente_nome}, {cliente_sobrenome}, {cliente_cpf}, {cliente_email}, {loja_endereco}, {loja_telefone}, {loja_whatsapp}, {loja_email}, {loja_nome}, {loja_site}</small>
                </div>
            </div>    
            <div class="row mt-3 <?php if($modo_whatsapp){ echo 'd-none'; } ?>">
                <div class="col-12">
                    <label for="summernote4">Pedido por boleto</label>
                    <textarea id="summernote4" class="summernote4" name="summernote4"><?= $loja['email_pedido_boleto'] ?></textarea>
                    <small>Variáveis aceitas: {cliente_nome}, {cliente_email}, {pedido_codigo}, {boleto_link}, {loja_endereco}, {loja_telefone}, {loja_whatsapp}, {loja_email}, {loja_nome}, {loja_site}</small>
                </div>
            </div>    
            <div class="row mt-3 <?php if($modo_whatsapp){ echo 'd-none'; } ?>">
                <div class="col-12">
                    <label for="summernote5">Pedido por cartão</label>
                    <textarea id="summernote5" class="summernote5" name="summernote5"><?= $loja['email_pedido_cartao'] ?></textarea>
                    <small>Variáveis aceitas: {cliente_nome}, {cliente_email}, {pedido_codigo}, {loja_endereco}, {loja_telefone}, {loja_whatsapp}, {loja_email}, {loja_nome}, {loja_site}</small>
                </div>
            </div>      
            <div class="row mt-3 <?php if($modo_whatsapp | ($pagamento['pix'] == 0 & $pagamento['asaas_pix'] == 0)){ echo 'd-none'; } ?>">
                <div class="col-12">
                    <label for="summernote9">Pedido por pix</label>
                    <textarea id="summernote9" class="summernote9" name="summernote9"><?= $loja['email_pedido_pix'] ?></textarea>
                    <small>Variáveis aceitas: {cliente_nome}, {cliente_email}, {pedido_codigo}, {chave_pix}, {loja_endereco}, {loja_telefone}, {loja_whatsapp}, {loja_email}, {loja_nome}, {loja_site}</small>
                    <small>Com o Asaas ativo: {pedido_url}</small>
                </div>
            </div>      
            <div class="row mt-3 <?php if(!$modo_whatsapp){ echo 'd-none'; } else { if($modo_whatsapp_simples){ echo 'd-none'; } } ?>">
                <div class="col-12">
                    <label for="summernote6">Pedido de orçamento</label>
                    <textarea id="summernote6" class="summernote6" name="summernote6"><?= $loja['email_pedido_orcamento'] ?></textarea>
                    <small>Variáveis aceitas: {cliente_nome}, {cliente_email}, {orcamento_codigo}, {loja_endereco}, {loja_telefone}, {loja_whatsapp}, {loja_email}, {loja_nome}, {loja_site}</small>
                </div>
            </div>    
            <div class="row mt-3 <?php if($modo_whatsapp){ echo 'd-none'; } ?>">
                <div class="col-12">
                    <label for="summernote7">Confirmação de pagamento de pedido</label>
                    <textarea id="summernote7" class="summernote7" name="summernote7"><?= $loja['email_pedido_confirmacao'] ?></textarea>
                    <small>Variáveis aceitas: {cliente_nome}, {cliente_email}, {pedido_codigo}, {loja_endereco}, {loja_telefone}, {loja_whatsapp}, {loja_email}, {loja_nome}, {loja_site}</small>
                </div>
            </div>    
            <div class="row mt-3">
                <div class="col-12">
                    <label for="summernote12">Autorização para retirada de pedido</label>
                    <textarea id="summernote12" class="summernote12" name="summernote12"><?= $loja['email_pedido_confirmacao_retirada'] ?></textarea>
                    <small>Variáveis aceitas: {cliente_nome}</small>
                </div>
            </div>    
            <div class="row mt-3 <?php if($modo_whatsapp){ echo 'd-none'; } ?>">
                <div class="col-12">
                    <label for="summernote10">Pedido enviado</label>
                    <textarea id="summernote10" class="summernote10" name="summernote10"><?= $loja['email_pedido_enviado'] ?></textarea>
                    <small>Variáveis aceitas: {cliente_nome}, {cliente_email}, {pedido_codigo}, {rastreamento}, {loja_endereco}, {loja_telefone}, {loja_whatsapp}, {loja_email}, {loja_nome}, {loja_site}</small>
                </div>
            </div>   
            <div class="row mt-3">
                <div class="col-12">
                    <label for="summernote8">Rodapé</label>
                    <textarea id="summernote8" class="summernote8" name="summernote8"><?= $loja['email_rodape'] ?></textarea>
                    <small>Variáveis aceitas: {loja_endereco}, {loja_telefone}, {loja_whatsapp}, {loja_email}, {loja_nome}, {loja_site}</small>
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
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script type="text/javascript" src="modulos/configuracoes/js/scripts.js"></script>