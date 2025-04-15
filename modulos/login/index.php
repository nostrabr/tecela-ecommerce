<!--CSS-->
<link rel="stylesheet" href="modulos/login/css/style.css">

<?php

//SE JÁ ESTÁ LOGANDO, MANDA PRO CADASTRO
if(isset($_SESSION['DONO'])){
    echo '<script>window.location.href = "cliente-dados";</script>';
}

//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){
    if($_SESSION['RETORNO']['ERRO'] == 'ERRO-STATUS'){
        echo "<script>mensagemAviso('erro', 'Cliente desativado. Para maiores informações contate o administrador do sistema.', 3000);</script>";
    } else if($_SESSION['RETORNO']['ERRO'] == 'ERRO'){
        echo "<script>mensagemAviso('erro', 'Dados de acesso incorretos.', 3000);</script>";
    } else if($_SESSION['RETORNO']['STATUS'] == 'CADASTRADO-SUCESSO'){
        echo "<script>mensagemAviso('sucesso', 'Cadastro realizado com sucesso.', 3000);</script>";
        $busca_ultimo_cliente = mysqli_query($conn, "SELECT identificador FROM cliente ORDER BY id DESC LIMIT 1");
        $ultimo_cliente       = mysqli_fetch_array($busca_ultimo_cliente);
        ?><input type="hidden" id="registro-cadastro-realizado" value="<?= $ultimo_cliente['identificador'] ?>"><?php
    } else if($_SESSION['RETORNO']['STATUS'] == 'SENHA-SUCESSO'){
        echo "<script>mensagemAviso('sucesso', 'Senha alterada com sucesso.', 3000);</script>";
    }
}

?>


<!--LOGIN-->
<section id="login">

    <div class="row">

        <div class="col-12 col-xl-4 offset-xl-4">
            
            <h1 class="subtitulo-pagina-central-h1">Login</h1>
            <p class="subtitulo-pagina-central-p">Acesse com seus dados para continuar</p>

            <form action="modulos/login/php/processa-login.php" method="POST">
                <input type="hidden" name="acao" value="area-cliente" required>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" name="email" id="email" class="form-control text-lowercase" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" name="senha" id="senha" class="form-control" required>
                </div>
                <div class="form-group">
                    <a href="login-recuperacao-senha">Não sei minha senha</a>
                </div>
                <div class="form-group">
                    <input id="login-btn-entrar" class="btn-escuro" type="submit" value="Entrar">
                </div>
            </form>

        </div>

    </div>

    <div class="row mt-3">

        <div class="col-12 col-xl-4 offset-xl-4">
            
            <h2 class="subtitulo-pagina-central-h2">Não possui cadastro?</h2>
            <p class="subtitulo-pagina-central-p">Acesse pelo link abaixo e cadastre-se.</p>
            <input id="login-btn-novo" class="btn-escuro" type="button" value="Novo cadastro" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-cadastro';">

        </div>

    </div>
        
</section>

<?php /* LIMPA A SESSION DE RETORNO */ unset($_SESSION['RETORNO']); ?>