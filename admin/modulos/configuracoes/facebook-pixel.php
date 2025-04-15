<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE TENTAR ACESSAR COMO USUÁRIO OU ADMINISTRADOR, DESLOGA DO SISTEMA
if($nivel_usuario == 'U' | $nivel_usuario == 'A'){
    echo "<script>location.href='logout.php';</script>";
} else if($nivel_usuario == 'M' | $nivel_usuario == 'S'){
    $busca_facebook_pixel = mysqli_query($conn, 'SELECT facebook_pixel FROM loja WHERE id = 1'); 
    $facebook_pixel       = mysqli_fetch_array($busca_facebook_pixel);
}

?>

<!--SECTION CONFIGURAÇÕES-->
<section id="configuracoes-facebook-pixel">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Configuração do Facebook Pixel</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes.php';">VOLTAR</button>
            </div>
        </div>
        
        <!-- FORM DE EDIÇÃO -->
        <form action="modulos/configuracoes/php/edicao-facebook-pixel.php" method="POST">
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="facebook-pixel">Código</label>
                        <input type="text" class="form-control" name="facebook-pixel" id="facebook-pixel" value="<?= $facebook_pixel['facebook_pixel'] ?>">
                        <small>Adicionar somente o código do Facebook Pixel. As tags de 'script' são adicionadas automaticamente.</small>
                        <small>Para remover basta deixar o campo em branco.</small>
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