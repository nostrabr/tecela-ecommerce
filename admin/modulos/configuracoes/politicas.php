<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE TENTAR ACESSAR COMO USUÁRIO OU ADMINISTRADOR, DESLOGA DO SISTEMA
if($nivel_usuario == 'U' | $nivel_usuario == 'A'){
    echo "<script>location.href='logout.php';</script>";
} else if($nivel_usuario == 'M' | $nivel_usuario == 'S'){
    $busca_politicas = mysqli_query($conn, 'SELECT * FROM politicas WHERE id = 1'); 
    $politica        = mysqli_fetch_array($busca_politicas);
}

?>

<!--SECTION CONFIGURAÇÕES-->
<section id="configuracoes-frete">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Configuração das Políticas</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes.php';">VOLTAR</button>
            </div>
        </div>
        
        <!-- FORM DE EDIÇÃO -->
        <form action="modulos/configuracoes/php/edicao-politicas.php" method="POST">
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="comercial">Política Comercial</label>
                        <textarea type="text" class="form-control" name="comercial" id="comercial" rows="10"><?= str_replace("<br>","",str_replace("<br/>","",str_replace("<br />","",$politica['comercial']))) ?></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="entrega">Política de Entrega</label>
                        <textarea type="text" class="form-control" name="entrega" id="entrega" rows="10"><?= str_replace("<br>","",str_replace("<br/>","",str_replace("<br />","",$politica['entrega']))) ?></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="troca-devolucao">Política de Troca e Devolução</label>
                        <textarea type="text" class="form-control" name="troca-devolucao" rows="10" id="troca-devolucao"><?= str_replace("<br>","",str_replace("<br/>","",str_replace("<br />","",$politica['troca_devolucao']))) ?></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="privacidade-seguranca">Política de Privacidade e Segurança</label>
                        <textarea type="text" class="form-control" name="privacidade-seguranca" rows="10" id="privacidade-seguranca"><?= str_replace("<br>","",str_replace("<br/>","",str_replace("<br />","",$politica['privacidade_seguranca']))) ?></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="termos-uso">Termos de Uso</label>
                        <textarea type="text" class="form-control" name="termos-uso" rows="10" id="termos-uso"><?= str_replace("<br>","",str_replace("<br/>","",str_replace("<br />","",$politica['termos_uso']))) ?></textarea>
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