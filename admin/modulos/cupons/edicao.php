<?php 

//PEGA OS DADOS
$identificador_cupom = filter_input(INPUT_GET,"id",FILTER_SANITIZE_STRING);
$nivel_usuario       = filter_var($_SESSION['nivel']);

//SE FOR USUÁRIO, DESLOGA
if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
}

//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){

    if($_SESSION['RETORNO']['ERRO'] == 'ERRO-NOME-REPETIDO'){
        echo "<script>mensagemAviso('erro', 'Já existe um cupom cadastrado com esse nome.', 3000);</script>";
    }    

    $nome       = $_SESSION['RETORNO']['nome'];
    $quantidade = $_SESSION['RETORNO']['quantidade'];
    $validade   = $_SESSION['RETORNO']['validade'];
    $valor      = $_SESSION['RETORNO']['valor'];
    $tipo       = $_SESSION['RETORNO']['tipo'];

} else {

    //BUSCA CUPOM
    $busca_cupom = mysqli_query($conn, 'SELECT nome, quantidade, validade, valor, tipo FROM cupom WHERE identificador = "'.$identificador_cupom.'"'); 

    //VERIFICA SE ENCONTROU O USUÁRIO
    if(mysqli_num_rows($busca_cupom) == 0){
        echo "<script>location.href='cupons.php';</script>";
    } else {

        $cupom      = mysqli_fetch_array($busca_cupom);
        $nome       = $cupom['nome'];
        $quantidade = $cupom['quantidade'];
        $valor      = $cupom['valor'];
        $tipo       = $cupom['tipo'];
        $validade   = date('d/m/Y', strtotime($cupom['validade']));

    }
    
}

?>

<!--SECTION CUPONS-->
<section id="cupons-edita">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Cupons - Edição</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'cupons.php';">VOLTAR</button>
            </div>
        </div>

        <!-- FORM DE EDIÇÃO -->
        <form action="modulos/cupons/php/edicao.php" method="POST">            
            <input type="hidden" name="identificador" value="<?= $identificador_cupom ?>">
            <div class="row">
                <div class="col-12 col-md-8">
                    <div class="form-group">
                        <label for="nome">Nome <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control text-uppercase" name="nome" id="nome" maxlength="20" value="<?= $nome ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="validade">Validade <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control" name="validade" id="validade" maxlength="10" minlength="10" value="<?= $validade ?>" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="quantidade">Quantidade <span class="campo-obrigatorio">*</span></label>
                        <input type="number" class="form-control" name="quantidade" id="quantidade" min="1" value="<?= $quantidade ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="valor">Valor <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control" name="valor" id="valor" value="<?= number_format($valor,2,',','.') ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="tipo">Tipo <span class="campo-obrigatorio">*</span></label>
                        <select class="form-control" name="tipo" id="tipo" required>
                            <option value="V" <?php if($tipo == 'V'){ echo 'selected'; } ?>>Valor</option>
                            <option value="P" <?php if($tipo == 'P'){ echo 'selected'; } ?>>Porcentagem</option>
                        </select>
                    </div>
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

<?php /* LIMPA A SESSION DE RETORNO */ unset($_SESSION['RETORNO']); ?>