<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE TENTAR ACESSAR COMO USUÁRIO OU ADMINISTRADOR, DESLOGA DO SISTEMA
if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
}

?>

<!--SECTION DESIGN CONTATO-->
<section id="design-contato">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Contato</div>    
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes-design.php';">VOLTAR</button>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">                                   
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input text-uppercase" id="whatsapp-flutuante" name="whatsapp-flutuante" <?php if($loja['design_whatsapp_flutuante'] == 1){ echo 'checked'; } ?> >
                    <label class="custom-control-label" for="whatsapp-flutuante">Whatsapp Flutuante</label>
                    <small>Com este modo ativado, um link flutuante de contato ao Whatsapp da loja é disponibilizado.</small>
                </div>
            </div>
        </div>

    </div>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="modulos/configuracoes/js/scripts.js"></script>