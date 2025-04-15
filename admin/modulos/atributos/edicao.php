<!-- CSS -->
<link rel="stylesheet" href="modulos/atributos/css/style.css">

<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$identificador_atributo = filter_input(INPUT_GET,"id",FILTER_SANITIZE_STRING);
$nivel_usuario          = filter_var($_SESSION['nivel']);

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
    
    $nome              = $_SESSION['RETORNO']['nome'];
    $visualizacao      = $_SESSION['RETORNO']['visualizacao'];
    $n_caracteristicas = $_SESSION['RETORNO']['n_caracteristicas'];

} else {

    //BUSCA ATRIBUTO
    $busca_atributo = mysqli_query($conn, 'SELECT id, identificador, nome, visualizacao, (SELECT COUNT(id) FROM caracteristica WHERE atributo.id = caracteristica.id_atributo AND caracteristica.status = 1) AS n_caracteristicas FROM atributo WHERE identificador = "'.$identificador_atributo.'"'); 

    //VERIFICA SE ENCONTROU O USUÁRIO
    if(mysqli_num_rows($busca_atributo) == 0){
        echo "<script>location.href='atributos.php';</script>";
    } else {

        $atributo          = mysqli_fetch_array($busca_atributo);
        $nome              = $atributo['nome'];
        $visualizacao      = $atributo['visualizacao'];
        $n_caracteristicas = $atributo['n_caracteristicas'];
        
        //BUSCA AS CARACTERÍSTICAS DO ATRIBUTO
        $caracteristicas = mysqli_query($conn, 'SELECT identificador, nome, textura, cor FROM caracteristica WHERE status = 1 AND id_atributo = '.$atributo['id'].' ORDER BY id'); 

    }
    
}

?>

<!--SECTION ATRIBUTOS-->
<section id="atributos-edita">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Atributos de produtos - Edição</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'atributos.php';">VOLTAR</button>
            </div>
        </div>

        <!-- FORM DE CADASTRO -->
        <form action="modulos/atributos/php/edicao.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="identificador" value="<?= $identificador_atributo ?>">    

            <div class="row admin-subtitulo"><div class="col-12">Dados do atributo</div></div>

            <div class="row">
                <div class="col-12 col-md-8">
                    <div class="form-group">
                        <label for="nome">Nome <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control text-capitalize" name="nome" id="nome" maxlength="50" value="<?= $nome ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="visualizacao">Visualização <span class="campo-obrigatorio">*</span></label>
                        <select name="visualizacao" id="visualizacao" class="form-control" onchange="javascript: verificaVisualizacao();" required>
                            <option value="S" <?php if($visualizacao == 'S'){ echo 'selected'; } ?>>Nome</option>
                            <!--<option value="L" <?php if($visualizacao == 'L'){ echo 'selected'; } ?>>Lista</option>-->
                            <option value="C" <?php if($visualizacao == 'C'){ echo 'selected'; } ?>>Cor</option>
                            <option value="T" <?php if($visualizacao == 'T'){ echo 'selected'; } ?>>Textura</option>
                        </select>
                    </div>
                </div>
            </div>
                        
            <hr>            

            <div class="row admin-subtitulo"><div class="col-12">Características deste atributo</div></div>

            <input type="hidden" id="n_caracteristicas" name="n_caracteristicas" value="<?= $n_caracteristicas ?>">

            <div id="caracteristicas">
                <?php $contador_caracteristicas = 0; while($caracteristica = mysqli_fetch_array($caracteristicas)){ $contador_caracteristicas++; ?>                    
                    <div class="row" id="container-caracteristica-<?= $contador_caracteristicas ?>">
                        <input type="hidden" name="identificador-caracteristica-<?= $contador_caracteristicas ?>" value="<?= $caracteristica['identificador'] ?>">    
                        <div class="col-1 d-flex align-items-center col-add-remove-caracteristica">
                            <a class="acao-add-remove-caracteristica-produto" href="javascript: removeCaracteristica(<?= $contador_caracteristicas ?>, true, '<?= $caracteristica['identificador'] ?>')"><img class="img-add-remove-caracteristica" src="<?= $loja['site'] ?>imagens/remover.png" alt="Remover característica"></a>
                        </div>
                        <div class="col caracteristica-nome">
                            <div class="form-group">
                                <input type="text" class="form-control text-capitalize" name="caracteristica-<?= $contador_caracteristicas ?>" id="caracteristica-<?= $contador_caracteristicas ?>" value="<?= $caracteristica['nome'] ?>" placeholder="Nome" maxlength="50" required>
                            </div>
                        </div>
                        <div class="col-3 caracteristica-cor">
                            <div class="form-group">
                                <input type="color" class="form-control" name="cor-<?= $contador_caracteristicas ?>" id="cor-<?= $contador_caracteristicas ?>" placeholder="Cor" value="<?= $caracteristica['cor'] ?>">
                            </div>
                        </div>
                        <div class="col-6 caracteristica-textura">
                            <div class="form-group">
                                <input type="file" name="imagem-<?= $contador_caracteristicas ?>" id="imagem-<?= $contador_caracteristicas ?>" class="form-control-file imagem" accept=".png, .jpg, .gif, .jpeg" onchange="javascript: inputFileChange(<?= $contador_caracteristicas ?>);">
                                <input type="text" name="arquivo-<?= $contador_caracteristicas ?>" class="arquivo" id="arquivo-<?= $contador_caracteristicas ?>" value="<?= $caracteristica['textura'] ?>" placeholder="Textura" readonly="readonly">
                                <input type="button" class="btn-escolher" value="ESCOLHER" onclick="javascript: inputFileEscolher(<?= $contador_caracteristicas ?>);">
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div> 

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
                        <button type="submit" class="btn btn-dark btn-bottom">SALVAR</button>
                    </div>
                </div>
            </div>

        </form>

    </div>

</section>

<?php /* LIMPA A SESSION DE RETORNO */ unset($_SESSION['RETORNO']); ?>

<!-- SCRIPTS -->
<script type="text/javascript" src="modulos/atributos/js/scripts.js"></script>
