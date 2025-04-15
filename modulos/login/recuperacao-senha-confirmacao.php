<!--CSS-->
<link rel="stylesheet" href="modulos/login/css/style.css">

<?php

//SE JÁ ESTÁ LOGANDO, MANDA PRO CADASTRO
if(isset($_SESSION['DONO'])){
    echo '<script>window.location.href = "cliente-dados";</script>';
}

//RECEBE OS DADOS DO CADASTRO
$email                   = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));   
$identificador_seguranca = trim(strip_tags(filter_input(INPUT_POST, "identificador_seguranca", FILTER_SANITIZE_STRING)));  

//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){
    if($_SESSION['RETORNO']['ERRO'] == 'ERRO-CODIGO'){
        echo "<script>mensagemAviso('erro', 'Código de confirmação incorreto.', 3000);</script>";
    } else if($_SESSION['RETORNO']['ERRO'] == 'ERRO-CADASTRO'){
        echo "<script>mensagemAviso('erro', 'Ocorreu um erro ao tentar te cadastrar. Se o problema persistir contate o administrador do sistema.', 3000);</script>";
    }
}

?>

<!--LOGIN - RECUPERAR SENHA-->
<section id="login-recuperar-senha-confirmacao">

    <form action="modulos/login/php/recuperacao-senha-verificacao.php" method="POST">
        
        <input type="email" name="email" maxlength="50" class="d-none" value="<?= $email ?>" required>
        <input type="text" class="d-none" name="identificador_seguranca" value="<?= $identificador_seguranca ?>" required>

        <div class="row">

            <div class="col-12 col-xl-4 offset-xl-4">
                
                <h1 class="subtitulo-pagina-central-h1">Código de confirmação</h1>
                <p class="subtitulo-pagina-central-p">Enviamos um e-mail para <?= $email ?> com um código para confirmarmos sua identidade e prosseguir com a alteração da sua senha. Caso não o encontre, confira sua pasta de spam.</p>     

                <div class="form-group">
                    <label for="codigo">Código <span class="campo-obrigatorio">*</span></label>
                    <input type="text" name="codigo" id="codigo" maxlength="6" minlength="6" class="form-control" required>
                </div>   
                <div class="form-group mt-3">
                    <input id="login-recuperar-senha-confirmacao-btn-continuar" type="submit" class="btn-escuro" value="Continuar">
                </div>

            </div>

        </div>

    </form>
        
</section>

<?php /* LIMPA A SESSION DE RETORNO */ unset($_SESSION['RETORNO']); ?>