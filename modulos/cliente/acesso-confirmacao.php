<!--CSS-->
<link rel="stylesheet" href="modulos/cliente/css/style.css">

<?php 

//RECEBE OS DADOS DO CADASTRO
$email                   = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));   
$senha                   = trim(strip_tags(filter_input(INPUT_POST, "senha", FILTER_SANITIZE_STRING)));  
$identificador_seguranca = trim(strip_tags(filter_input(INPUT_POST, "identificador_seguranca", FILTER_SANITIZE_STRING)));  

//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){
    if($_SESSION['RETORNO']['ERRO'] == 'ERRO-CODIGO'){
        echo "<script>mensagemAviso('erro', 'Código de confirmação incorreto.', 3000);</script>";
    } else if($_SESSION['RETORNO']['ERRO'] == 'ERRO-EDICAO'){
        echo "<script>mensagemAviso('erro', 'Ocorreu um erro ao tentar te cadastrar. Se o problema persistir contate o administrador do sistema.', 3000);</script>";
    }
}

?>

<!--CONFIRMAÇÃO DE CADASTRO DE CLIENTE-->
<section id="cliente-cadastro-confirmacao" class="cliente">

    <form action="modulos/cliente/php/edicao-acesso.php" method="POST">
    
        <input type="email" name="email" maxlength="50" class="d-none" value="<?= $email ?>" required>
        <input type="password" name="senha" id="senha" maxlength="32" minlength="8" class="d-none" value="<?= $senha ?>">
        <input type="text" class="d-none" name="identificador_seguranca" value="<?= $identificador_seguranca ?>" required>

        <div class="row">

            <div class="col-12 col-xl-4 offset-xl-4">
                
                <h1 class="subtitulo-pagina-central-h1">Código de confirmação <span class="d-none">de acesso</span></h1>
                <p class="subtitulo-pagina-central-p">Enviamos um e-mail para <?= $email ?> com um código de confirmação para concluírmos a edição dos seus dados. Caso não o encontre, confira sua pasta de spam.</p>
          
                <div class="form-group">
                    <label for="codigo">Código <span class="campo-obrigatorio">*</span></label>
                    <input type="text" name="codigo" id="codigo" maxlength="6" minlength="6" class="form-control" required>
                </div>      
                <div class="form-group mt-3">
                    <input id="cliente-cadastro-confirmacao-btn-continuar" type="submit" class="btn-escuro" value="Continuar">
                </div>

            </div>

        </div>

    </form>

</section>

<?php /* LIMPA A SESSION DE RETORNO */ unset($_SESSION['RETORNO']); ?>
