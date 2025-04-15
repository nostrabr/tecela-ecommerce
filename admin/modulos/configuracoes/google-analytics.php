<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE TENTAR ACESSAR COMO USUÁRIO OU ADMINISTRADOR, DESLOGA DO SISTEMA
if($nivel_usuario == 'U' | $nivel_usuario == 'A'){
    echo "<script>location.href='logout.php';</script>";
} else if($nivel_usuario == 'M' | $nivel_usuario == 'S'){
    $busca_google_analytics = mysqli_query($conn, 'SELECT google_analytics FROM loja WHERE id = 1'); 
    $google_analytics       = mysqli_fetch_array($busca_google_analytics);
}

?>

<!--SECTION CONFIGURAÇÕES-->
<section id="configuracoes-google-analytics">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Configuração do Google Analytics</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes.php';">VOLTAR</button>
            </div>
        </div>
        
        <!-- FORM DE EDIÇÃO -->
        <form action="modulos/configuracoes/php/edicao-google-analytics.php" method="POST">
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="google-analytics">Código</label>
                        <input type="text" class="form-control" name="google-analytics" id="google-analytics" value="<?= $google_analytics['google_analytics'] ?>">
                        <small>Adicionar somente o código do Google Analytics. As tags de 'script' são adicionadas automaticamente.</small>
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