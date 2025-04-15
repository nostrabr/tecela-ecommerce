<!--CSS-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">

<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE TENTAR ACESSAR COMO USUÁRIO OU ADMINISTRADOR, DESLOGA DO SISTEMA
if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
}

?>

<!--SECTION BANNERS-->
<section id="banners">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Barra de Categorias</div>    
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes-design.php';">VOLTAR</button>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">                                   
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input text-uppercase" id="barra-categorias-mobile" name="barra-categorias-mobile" <?php if($loja['design_barra_categorias_mobile'] == 1){ echo 'checked'; } ?> >
                    <label class="custom-control-label" for="barra-categorias-mobile">Barra de categorias mobile</label>
                    <small>Com este modo ativado, uma barra de categorias para tablets e celulares será apresentada no topo do site.</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">                                   
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input text-uppercase" id="barra-categorias-desktop" name="barra-categorias-desktop" <?php if($loja['design_barra_categorias_desktop'] == 1){ echo 'checked'; } ?> >
                    <label class="custom-control-label" for="barra-categorias-desktop">Barra de categorias desktop</label>
                    <small>Com este modo ativado, uma barra de categorias para monitores será apresentada no topo do site.</small>
                </div>
            </div>
        </div>

    </div>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="modulos/configuracoes/js/scripts.js"></script>