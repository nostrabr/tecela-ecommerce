<!--CSS-->
<link rel="stylesheet" href="modulos/cliente/css/style.css">

<?php

//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){
    if($_SESSION['RETORNO']['ERRO'] == 'ERRO-REPETIDO'){
        echo "<script>mensagemAviso('erro', 'CPF ou E-MAIL já cadastrado em nosso site.', 3000);</script>";
    } else if($_SESSION['RETORNO']['ERRO'] == 'ERRO-CAPTCHA'){
        echo "<script>mensagemAviso('erro', 'Erro ao processar reCAPTCHA. Tente novamente.', 3000);</script>";
    }
}

?>

<!--CADASTRO DE CLIENTE-->
<section id="cliente-cadastro" class="cliente">

    <?php if($loja['opcao_validar_email_cadastro'] == 1){ ?>   
        <form action="<?php if($loja['recaptcha'] != ''){ ?>modulos/cliente/php/recaptcha-verificacao.php<?php } else { ?>modulos/cliente/php/cadastro-verificacao.php<?php } ?>" method="POST">
    <?php } else if($loja['opcao_validar_email_cadastro'] == 0){ ?>
        <form action="<?php if($loja['recaptcha'] != ''){ ?>modulos/cliente/php/recaptcha.php<?php } else { ?>modulos/cliente/php/cadastro.php<?php } ?>" method="POST">
    <?php } ?>

        <div class="row">

            <div class="col-12 col-xl-4 offset-xl-4">
                
                <h1 class="subtitulo-pagina-central-h1">Cadastro</h1>
                <p class="subtitulo-pagina-central-p">Para se cadastrar, digite seus dados nos campos abaixo.</p>
 
                <div class="form-group">
                    <label for="cpf">CPF/CNPJ <span class="campo-obrigatorio">*</span></label>
                    <input type="text" name="cpf" id="cpf-cnpj" maxlength="18" class="form-control" onblur="javascript: validaCpfCnpj(this.value, this.id); trocaLabelNomes(this.value);" required>
                </div>          
                <div class="form-group">
                    <label for="nome"><span id="label-cliente-nome">Nome</span> <span class="campo-obrigatorio">*</span></label>
                    <input type="text" name="nome" id="nome" maxlength="50" class="form-control text-capitalize" required>
                </div>                
                <div class="form-group">
                    <label for="sobrenome"><span id="label-cliente-sobrenome">Sobrenome</span> <span class="campo-obrigatorio">*</span></label>
                    <input type="text" name="sobrenome" id="sobrenome" maxlength="50" class="form-control text-capitalize" required>
                </div>            
                <div class="form-group">
                    <label for="celular">Celular <span class="campo-obrigatorio">*</span></label>
                    <input type="text" name="celular" id="celular" class="form-control" required>
                </div>                         
                <div class="form-group">
                    <label for="email">E-mail <span class="campo-obrigatorio">*</span></label>
                    <input type="email" name="email" id="email" maxlength="50" class="form-control text-lowercase" required>
                </div>                
                <div class="form-group">
                    <label for="senha">Senha <span class="campo-obrigatorio">*</span></label>
                    <input type="password" name="senha" id="senha" maxlength="32" minlength="8" class="form-control" required>
                </div>     
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="cliente-cadastro-checkbox-aceite-termos" name="aceite-termos" required>
                    <label class="custom-control-label" id="cliente-cadastro-label-aceite-termos" for="cliente-cadastro-checkbox-aceite-termos">Aceito os <a href="politica-termos-uso" target="_blank">Termos e condições</a> e autorizo o uso de meus dados de acordo com a <a href="politica-privacidade-seguranca" target="_blank">Política de privacidade</a>.</label>
                </div>     
                <?php if($loja['recaptcha'] != ''){ ?>
                    <div class="form-group mt-2">
                        <div class="g-recaptcha" data-sitekey="<?= $loja['recaptcha'] ?>"></div>
                    </div>
                <?php } ?>    
                <div class="form-group mt-3">
                    <input id="cliente-cadastro-btn-continuar" type="submit" class="btn-escuro" value="Continuar">
                </div>
            </div>

        </div>

    </form>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="modulos/cliente/js/scripts.js"></script>

<?php if($loja['recaptcha'] != ''){ ?>
    <script src='https://www.google.com/recaptcha/api.js?hl=pt-BR'></script>
<?php } ?>

<?php /* LIMPA A SESSION DE RETORNO */ unset($_SESSION['RETORNO']); ?>