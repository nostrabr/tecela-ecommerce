<!--CSS-->
<link rel="stylesheet" href="modulos/login/css/style.css">

<?php

//SE JÁ ESTÁ LOGANDO, MANDA PRO CADASTRO
if(isset($_SESSION['DONO'])){
    echo '<script>window.location.href = "cliente-dados";</script>';
}

//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){
    if($_SESSION['RETORNO']['ERRO'] == 'ERRO-INEXISTENTE'){
        echo "<script>mensagemAviso('erro', 'E-mail inválido', 3000);</script>";
    }
}

?>

<!--LOGIN - RECUPERAR SENHA-->
<section id="login-recuperar-senha">

    <div class="row">

        <div class="col-12 col-xl-4 offset-xl-4">
            
            <h1 class="subtitulo-pagina-central-h1">E-mail de confirmação</h1>
            <p class="subtitulo-pagina-central-p">Digite o e-mail cadastrado na loja e lhe enviaremos um código de confirmação para continuar.</p>     

            <form action="modulos/login/php/recuperacao-senha-confirmacao.php" method="POST">
                <input type="hidden" name="acao" value="area-cliente" required>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" name="email" id="email" class="form-control text-lowercase" required>
                </div>
                <div class="form-group">
                    <input id="login-recuperar-senha-btn-continuar" class="btn-escuro" type="submit" value="Continuar">
                </div>
            </form>

        </div>

    </div>
        
</section>

<?php /* LIMPA A SESSION DE RETORNO */ unset($_SESSION['RETORNO']); ?>