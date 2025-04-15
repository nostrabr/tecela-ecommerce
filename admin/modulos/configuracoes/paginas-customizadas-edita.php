<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$identificador_pagina  = filter_input(INPUT_GET,"id",FILTER_SANITIZE_STRING);
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE TENTAR ACESSAR COMO USUÁRIO OU ADMINISTRADOR, DESLOGA DO SISTEMA
if($nivel_usuario == 'U' | $nivel_usuario == 'A'){
    echo "<script>location.href='logout.php';</script>";
} else {
    
    $busca_pagina_customizada = mysqli_query($conn, 'SELECT * FROM pagina_customizada WHERE identificador = "'.$identificador_pagina.'"'); 
    
    //VERIFICA SE ENCONTROU A PÁGINA
    if(mysqli_num_rows($busca_pagina_customizada) == 0){
        echo "<script>location.href='configuracoes-paginas-customizadas.php';</script>";
    } else {
        $pagina_customizada = mysqli_fetch_array($busca_pagina_customizada);
    }

}

?>

<!--CSS-->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<link rel="stylesheet" href="modulos/configuracoes/css/style.css">

<!--SECTION CONFIGURAÇÕES-->
<section id="configuracoes-paginas-customizadas-edita">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Páginas customizadas - Edição</div>
            </div>
            <div class="col-4 text-right">  
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes-paginas-customizadas.php';">VOLTAR</button>
            </div>
        </div>

        <!-- FORM DE CADASTRO -->
        <form id="form-pagina-customizada" action="modulos/configuracoes/php/edicao-pagina-customizada.php" method="POST">

            <input type="hidden" name="identificador" value="<?= $identificador_pagina ?>">

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="titulo">Titulo <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control" name="titulo" id="titulo" value="<?= $pagina_customizada['titulo'] ?>" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="descricao">Descrição</label>
                        <textarea class="form-control" name="descricao" id="descricao" rows="2"><?= $pagina_customizada['descricao'] ?></textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="palavras-chave">Palavras chave</label>
                        <textarea class="form-control" name="palavras-chave" id="palavras-chave" rows="1"><?= $pagina_customizada['palavras_chave'] ?></textarea>
                        <small>Separar as palavras com virgula. Ex: loja, roupas, femininas</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="summernote">Conteúdo</label>
                        <input type="hidden" name="codigo-fonte" id="codigo-fonte">
                        <textarea id="summernote11" class="summernote11" name="summernote"><?= $pagina_customizada['conteudo'] ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="row admin-subtitulo mt-3">
                <div class="col-12">ONDE MOSTRAR</div>
            </div>
            
            <div class="row">
                <div class="col-12"><label class="mb-0">Atalhos</label></div>  
                <div class="col-12 mb-2"><small>Onde vai o link da página customizada</small></div>
                <div class="form-group mb-1 col-12">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="mostrar-cabecalho" id="mostrar-cabecalho" <?php if($pagina_customizada['mostrar_cabecalho'] == 1){ echo 'checked'; } ?>>
                        <label class="custom-control-label" for="mostrar-cabecalho">Cabeçalho</label>
                    </div>
                </div>
                <div class="form-group mb-1 col-12">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="mostrar-rodape" id="mostrar-rodape" <?php if($pagina_customizada['mostrar_rodape'] == 1){ echo 'checked'; } ?>>
                        <label class="custom-control-label" for="mostrar-rodape">Rodapé</label>
                    </div>
                </div>
                <div class="form-group mb-1 col-12">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="mostrar-menu-mobile" id="mostrar-menu-mobile" <?php if($pagina_customizada['mostrar_menu_mobile'] == 1){ echo 'checked'; } ?>>
                        <label class="custom-control-label" for="mostrar-menu-mobile">Menu mobile</label>
                    </div>
                </div>
            </div>

            <div class="row mt-3">     
                <div class="col-12"><label class="mb-0">Atribuir à uma categoria</label></div>     
                <div class="col-12 mb-2"><small>Ao atribuir à uma categoria o conteúdo da página será exibido antes dos produtos na página da categoria selecionada.</small></div>                              
                <div class="col-12">
                    <div class="form-group">
                        <label for="categoria">Categoria</label>
                        <div class="text-capitalize" id="arvore-categorias"></div>
                        <input type="hidden" id="categoria" name="categoria" value="<?= $pagina_customizada['categoria'] ?>">
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

<!--SCRIPTS-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.js" integrity="sha512-Hyk+1XSRfagqzuSHE8M856g295mX1i5rfSV5yRugcYFlvQiE3BKgg5oFRfX45s7I8qzMYFa8gbFy9xMFbX7Lqw==" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script type="text/javascript" src="modulos/configuracoes/js/scripts.js"></script>