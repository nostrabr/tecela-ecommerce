<!--CSS-->
<link rel="stylesheet" href="modulos/cliente/css/style.css">

<?php 

//RECEBE OS DADOS DO CADASTRO
$nome                    = trim(strip_tags(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING)));   
$sobrenome               = trim(strip_tags(filter_input(INPUT_POST, "sobrenome", FILTER_SANITIZE_STRING)));   
$cpf                     = trim(strip_tags(filter_input(INPUT_POST, "cpf", FILTER_SANITIZE_STRING)));   
$celular                 = trim(strip_tags(filter_input(INPUT_POST, "celular", FILTER_SANITIZE_STRING)));  
$email                   = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));   
$senha                   = trim(strip_tags(filter_input(INPUT_POST, "senha", FILTER_SANITIZE_STRING)));  
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

<!--CONFIRMAÇÃO DE CADASTRO DE CLIENTE-->
<section id="cliente-cadastro-confirmacao" class="cliente">

    <form action="modulos/cliente/php/cadastro.php" method="POST">
    
        <input type="text" name="nome" maxlength="50" class="d-none" value="<?= $nome ?>" required>
        <input type="text" name="sobrenome" maxlength="50" class="d-none" value="<?= $sobrenome ?>" required>
        <input type="text" name="cpf" id="cpf-cnpj" maxlength="18" class="d-none" value="<?= $cpf ?>" required>
        <input type="text" name="celular" id="celular" class="d-none" value="<?= $celular ?>" required>
        <input type="email" name="email" maxlength="50" class="d-none" value="<?= $email ?>" required>
        <input type="password" name="senha" id="senha" maxlength="32" minlength="8" class="d-none" value="<?= $senha ?>" required>
        <input type="checkbox" class="d-none" name="aceite-termos" checked required>
        <input type="text" class="d-none" name="identificador_seguranca" value="<?= $identificador_seguranca ?>" required>

        <div class="row">

            <div class="col-12 col-xl-4 offset-xl-4">
                
                <h1 class="subtitulo-pagina-central-h1">Código de confirmação <span class="d-none">de cadastro</span></h1>
                <p class="subtitulo-pagina-central-p">Enviamos um e-mail para <?= $email ?> com um código de confirmação para finalizarmos seu cadastro. Caso não o encontre, confira sua pasta de spam.</p>
          
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
