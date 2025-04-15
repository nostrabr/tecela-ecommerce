<!--CSS-->
<link rel="stylesheet" href="modulos/carrinho/css/style.css">

<?php 

//SE JÁ ESTÁ LOGANDO, MANDA PRO FRETE       
if(isset($_SESSION['nome'])){

    ?><script> window.location.href = 'carrinho-frete'; </script><?php

} else {

//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){
    if($_SESSION['RETORNO']['ERRO'] == 'ERRO-STATUS'){
        echo "<script>mensagemAviso('erro', 'Cliente desativado. Para maiores informações contate o administrador do sistema.', 3000);</script>";
    } else if($_SESSION['RETORNO']['ERRO'] == 'ERRO'){
        echo "<script>mensagemAviso('erro', 'Dados de acesso incorretos.', 3000);</script>";
    }
}

?>

<!--CARRINHO-->
<section id="carrinho-login" class="carrinho">

    <h1 class="d-none">Carrinho login</h1>

    <div class="row">

        <div class="col-12 col-xl-4 offset-xl-4">

            <div id="carrinho-mapa">
                <ul>
                    <li class="carrinho-mapa-imagem"><img class="carrinho-mapa-ativo" src="<?= $loja['site'] ?>imagens/carrinho-carrinho.png" title="Resumo do carrinho"></li>
                    <li class="carrinho-mapa-separador"><hr class="carrinho-mapa-ativo-hr"></li>
                    <li class="carrinho-mapa-imagem"><img class="carrinho-mapa-ativo" src="<?= $loja['site'] ?>imagens/carrinho-login.png" title="Cadastro/Login"></li>
                    <li class="carrinho-mapa-separador"><hr></li>
                    <li class="carrinho-mapa-imagem"><img src="<?= $loja['site'] ?>imagens/carrinho-frete.png" title="Frete"></li>
                    <li class="carrinho-mapa-separador"><hr></li>
                    <li class="carrinho-mapa-imagem"><img src="<?= $loja['site'] ?>imagens/carrinho-pagamento.png" title="Pagamento"></li>
                </ul>
            </div>
            
            <h2 class="subtitulo-pagina-central-h2">Login</h2>
            <p class="subtitulo-pagina-central-p">Acesse com seus dados para continuar</p>

            <form action="modulos/login/php/processa-login.php" method="POST">
                <input type="hidden" name="acao" value="carrinho" required>
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
                    
            <input id="login-btn-novo" class="btn-escuro" type="button" value="Novo cadastro" onclick="javascript: proximoPassoCadastro();">

        </div>

    </div>

</section>

<?php /* LIMPA A SESSION DE RETORNO */ unset($_SESSION['RETORNO']); ?>

<!--SCRIPTS-->
<script type="text/javascript" src="modulos/carrinho/js/scripts-1.1.js"></script>

<?php } ?>