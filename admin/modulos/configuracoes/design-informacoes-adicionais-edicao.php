<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$identificador_informacao_adicional  = filter_input(INPUT_GET,"id",FILTER_SANITIZE_STRING);
$nivel_usuario                       = filter_var($_SESSION['nivel']);
$identificador_usuario               = filter_var($_SESSION['identificador']);

//SE TENTAR ACESSAR COMO USUÁRIO OU ADMINISTRADOR, DESLOGA DO SISTEMA
if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
} else {
    $busca_informacoes_adicionais = mysqli_query($conn, 'SELECT * FROM informacao_adicional WHERE identificador = "'.$identificador_informacao_adicional.'"'); 
    $informacoes_adicionais       = mysqli_fetch_array($busca_informacoes_adicionais);
}

?>

<!--SECTION INFORMAÇÕES ADICIONAIS-->
<section id="informacoes-adicionais-edicao">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Informações Adicionais - Edição</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes-design-informacoes-adicionais.php';">VOLTAR</button>
            </div>
        </div>
        
        <!--FORM DE EDIÇÃO DE INFORMAÇÕES ADICIONAIS-->
        <form enctype="multipart/form-data" action="modulos/configuracoes/php/edicao-informacao-adicional.php" method="POST">

            <input type="hidden" name="id" value="<?= $informacoes_adicionais["id"] ?>">

            <div class="row">

                <div class="col-12">                    
                    <div class="form-group">
                        <label for="imagem">Ícone (RECOMENDADO 64x64px) <span class="campo-obrigatorio">*</span></label>
                        <input type="file" name="imagem" id="imagem" class="imagem form-control-file" accept="image/png, image/jpeg" onchange="javascript: inputFileChange();">
                        <input type="text" name="arquivo" id="arquivo" class="arquivo" placeholder="Selecionar arquivo" readonly="readonly" value="<?= $informacoes_adicionais["imagem"] ?>">
                        <input type="button" id="btn-escolher" class="btn-escolher" value="ESCOLHER" onclick="javascript: inputFileEscolher();">
                    </div>     
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="titulo">Título <span class="campo-obrigatorio">*</span></label>
                        <input type="text" name="titulo" id="titulo" class="form-control text-uppercase" maxlength="50" value="<?= $informacoes_adicionais["titulo"] ?>" required>
                    </div>            
                </div>
                
                <div class="col-12">
                    <div class="form-group">
                        <label for="descricao">Descrição <span class="campo-obrigatorio">*</span></label>
                        <textarea name="descricao" id="descricao" class="form-control" rows="3" required><?= $informacoes_adicionais["descricao"] ?></textarea>
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