<!-- CSS -->
<link rel="stylesheet" href="modulos/atributos/css/style.css">

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
        echo "<script>mensagemAviso('erro', 'Já existe um atributo cadastrado com esse nome.', 3000);</script>";
    } else if($_SESSION['RETORNO']['ERRO'] == 'ERRO-SEM-CARACTERISTICAS'){
        echo "<script>mensagemAviso('erro', 'É preciso cadastrar características para este atributo.', 3000);</script>";
    }
}

?>

<!--SECTION ATRIBUTOS-->
<section id="atributos-cadastra">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Atributos de produtos - Cadastro</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'atributos.php';">VOLTAR</button>
            </div>
        </div>

        <!-- FORM DE CADASTRO -->
        <form action="modulos/atributos/php/cadastro.php" method="POST" enctype="multipart/form-data">

            <div class="row admin-subtitulo"><div class="col-12">Dados do atributo</div></div>

            <div class="row">
                <div class="col-12 col-md-8">
                    <div class="form-group">
                        <label for="nome">Nome <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control text-capitalize" name="nome" id="nome" maxlength="50" value="<?php if(isset($_SESSION['RETORNO']['nome'])){ echo $_SESSION['RETORNO']['nome']; } ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="visualizacao">Visualização <span class="campo-obrigatorio">*</span></label>
                        <select name="visualizacao" id="visualizacao" class="form-control" onchange="javascript: verificaVisualizacao();" required>
                            <option value="S" <?php if(isset($_SESSION['RETORNO']['visualizacao'])){ if($_SESSION['RETORNO']['visualizacao'] == 'R'){ echo 'selected'; }} ?>>Nome</option>
                            <!--<option value="L" <?php if(isset($_SESSION['RETORNO']['visualizacao'])){ if($_SESSION['RETORNO']['visualizacao'] == 'S'){ echo 'selected'; }} ?>>Lista</option>-->
                            <option value="C" <?php if(isset($_SESSION['RETORNO']['visualizacao'])){ if($_SESSION['RETORNO']['visualizacao'] == 'C'){ echo 'selected'; }} ?>>Cor</option>
                            <option value="T" <?php if(isset($_SESSION['RETORNO']['visualizacao'])){ if($_SESSION['RETORNO']['visualizacao'] == 'T'){ echo 'selected'; }} ?>>Textura</option>
                        </select>
                    </div>
                </div>
            </div>
                        
            <hr>            

            <div class="row admin-subtitulo"><div class="col-12">Características deste atributo</div></div>

            <input type="hidden" id="n_caracteristicas" name="n_caracteristicas" value="0">

            <div id="caracteristicas"></div>

            <div id="row-btn-adicionar" class="row">
                <div class="col-12 d-flex align-items-center">
                    <a class="acao-add-remove-caracteristica-produto" href="javascript: addCaracteristica();" title="Adicionar atributo">
                        <img class="img-add-remove-caracteristica" src="<?= $loja['site'] ?>imagens/adicionar.png" alt="Adicionar característica">Adicionar atributo
                    </a>
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

<!-- SCRIPTS -->
<script type="text/javascript" src="modulos/atributos/js/scripts.js"></script>
