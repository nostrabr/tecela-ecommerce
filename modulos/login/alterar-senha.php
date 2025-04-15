<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/login/css/style.css">

<?php

//SE JÁ ESTÁ LOGANDO, MANDA PRO CADASTRO
if(isset($_SESSION['DONO'])){
    echo '<script>window.location.href = "cliente-dados";</script>';
}

//SE JÁ ESTÁ LOGANDO, MANDA PRO CADASTRO
if(isset($_SESSION['RECUPERACAO-SENHA'])){

if($_SESSION['RECUPERACAO-SENHA']['STATUS'] == 'OK'){

//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){
    if($_SESSION['RETORNO']['ERRO'] == 'SENHAS-NAO-CONFEREM'){
        echo "<script>mensagemAviso('erro', 'Senhas não conferem. Tente novamente.', 3000);</script>";
    }
}

?>

<!--LOGIN - RECUPERAR SENHA-->
<section id="login-recuperar-senha">

    <div class="row">

        <div class="col-12 col-xl-4 offset-xl-4">
                
            <h1 class="subtitulo-pagina-central-h1">Nova senha</h1>
            <p class="subtitulo-pagina-central-p">Entre com a nova senha para prosseguir.</p>          

            <form action="modulos/login/php/alterar-senha.php" method="POST">
                <input type="hidden" name="identificador" value="<?php echo $_SESSION['RECUPERACAO-SENHA']['IDENTIFICADOR']; ?>" required>
                <div class="form-group">
                    <label for="senha">Nova senha <span class="campo-obrigatorio">*</span></label>
                    <input type="password" name="senha" id="senha" maxlength="32" minlength="8" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="senha-confirmacao">Confirmar nova senha <span class="campo-obrigatorio">*</span></label>
                    <input type="password" name="senha-confirmacao" id="senha-confirmacao" maxlength="32" minlength="8"  class="form-control" required>
                </div>
                <div class="form-group">
                    <input id="login-alterar-senha-btn-finalizar" class="btn-escuro" type="submit" value="Finalizar">
                </div>
            </form>

        </div>

    </div>
        
</section>

<?php } else { echo '<script>window.location.href = "/";</script>'; } } else { echo '<script>window.location.href = "/";</script>'; } ?>

<?php /* LIMPA A SESSION DE RETORNO */  unset($_SESSION['RETORNO']); ?>