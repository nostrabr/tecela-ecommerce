<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$identificador_marca = filter_input(INPUT_GET,"id",FILTER_SANITIZE_STRING);
$nivel_usuario       = filter_var($_SESSION['nivel']);

//SE FOR USUÁRIO, DESLOGA
if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
}

//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){

    if($_SESSION['RETORNO']['ERRO'] == 'ERRO-NOME-REPETIDO'){
        echo "<script>mensagemAviso('erro', 'Já existe uma marca cadastrada com esse nome.', 3000);</script>";
    }
    
    $nome = $_SESSION['RETORNO']['nome'];
    $logo = $_SESSION['RETORNO']['logo'];

} else {

    //BUSCA MARCA
    $busca_marca = mysqli_query($conn, 'SELECT nome, logo FROM marca WHERE identificador = "'.$identificador_marca.'"'); 

    //VERIFICA SE ENCONTROU O USUÁRIO
    if(mysqli_num_rows($busca_marca) == 0){
        echo "<script>location.href='marcas.php';</script>";
    } else {

        $marca = mysqli_fetch_array($busca_marca);
        $nome  = $marca['nome'];
        $logo  = $marca['logo'];

    }
    
}

?>

<!--SECTION MARCAS-->
<section id="marcas-edita">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Marcas - Edição</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'marcas.php';">VOLTAR</button>
            </div>
        </div>

        <!-- FORM DE EDIÇÃO -->
        <form action="modulos/marcas/php/edicao.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="identificador" value="<?= $identificador_marca ?>">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="nome">Nome <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control text-capitalize" name="nome" id="nome" maxlength="50" value="<?= $nome ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="imagem">Logo <span class="campo-obrigatorio">*</span></label>
                        <input type="file" name="imagem" id="imagem" class="form-control-file imagem" accept=".png, .jpg, .gif, .jpeg" onchange="javascript: inputFileChange();">
                        <input type="text" name="arquivo" id="arquivo" class="arquivo" placeholder="Selecionar arquivo" value="<?= $logo ?>" readonly="readonly">
                        <input type="button" class="btn-escolher" value="ESCOLHER" onclick="javascript: inputFileEscolher();">
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