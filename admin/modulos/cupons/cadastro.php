<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario = filter_var($_SESSION['nivel']);

//SE FOR USUÁRIO, DESLOGA
if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
}

//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){
    if($_SESSION['RETORNO']['ERRO'] == 'ERRO-NOME-REPETIDO'){
        echo "<script>mensagemAviso('erro', 'Já existe um cupom cadastrado com esse nome.', 3000);</script>";
    }
}

?>

<!--SECTION CUPONS-->
<section id="cupons-cadastra">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Cupons - Cadastro</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'cupons.php';">VOLTAR</button>
            </div>
        </div>

        <!-- FORM DE CADASTRO -->
        <form action="modulos/cupons/php/cadastro.php" method="POST">
            <div class="row">
                <div class="col-12 col-md-8">
                    <div class="form-group">
                        <label for="nome">Nome <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control text-uppercase" name="nome" id="nome" maxlength="20" value="<?php if(isset($_SESSION['RETORNO']['nome'])){ echo $_SESSION['RETORNO']['nome']; } ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="validade">Validade <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control" name="validade" id="validade" maxlength="10" minlength="10" value="<?php if(isset($_SESSION['RETORNO']['validade'])){ echo $_SESSION['RETORNO']['validade']; } ?>" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="quantidade">Quantidade <span class="campo-obrigatorio">*</span></label>
                        <input type="number" class="form-control" name="quantidade" id="quantidade" min="1" value="<?php if(isset($_SESSION['RETORNO']['quantidade'])){ echo $_SESSION['RETORNO']['quantidade']; } ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="valor">Valor <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control" name="valor" id="valor" value="<?php if(isset($_SESSION['RETORNO']['valor'])){ echo $_SESSION['RETORNO']['valor']; } ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="tipo">Tipo <span class="campo-obrigatorio">*</span></label>
                        <select class="form-control" name="tipo" id="tipo" required>
                            <option value="" selected>Selecione</option>
                            <option value="V" <?php if(isset($_SESSION['RETORNO']['tipo'])){ if($_SESSION['RETORNO']['tipo'] == 'V'){ echo 'selected'; }} ?>>Valor</option>
                            <option value="P" <?php if(isset($_SESSION['RETORNO']['tipo'])){ if($_SESSION['RETORNO']['tipo'] == 'P'){ echo 'selected'; }} ?>>Porcentagem</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 text-center text-md-right">
                    <div class="form-group">
                        <button type="submit" class="btn btn-dark btn-bottom">CADASTRAR</button>
                    </div>
                </div>
            </div>
        </form>

    </div>

</section>

<?php /* LIMPA A SESSION DE RETORNO */ unset($_SESSION['RETORNO']); ?>