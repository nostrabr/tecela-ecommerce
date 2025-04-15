<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$identificador_banner  = filter_input(INPUT_GET,"id",FILTER_SANITIZE_STRING);
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE TENTAR ACESSAR COMO USUÁRIO OU ADMINISTRADOR, DESLOGA DO SISTEMA
if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
} else {
    $busca_banner = mysqli_query($conn, 'SELECT * FROM banner_secundario WHERE identificador = "'.$identificador_banner.'"'); 
    $banner       = mysqli_fetch_array($busca_banner);
}

?>

<!--SECTION EDIÇÃO BANNER-->
<section id="banner-secundario-edicao">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Banners Secundários - Edição</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes-design-banners-secundarios.php';">VOLTAR</button>
            </div>
        </div>
        
        <!--FORM DE EDIÇÃO DE BANNER-->
        <form enctype="multipart/form-data" action="modulos/configuracoes/php/edicao-banner-secundario.php" method="POST">
            
            <input type="hidden" name="id" value="<?= $banner["id"] ?>">
            
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="imagem">Banner desktop (RECOMENDADO 500x300px)</label>
                        <input type="file" name="imagem" id="imagem" class="imagem form-control-file" accept="image/png, image/jpeg" onchange="javascript: inputFileChange();">
                        <input type="text" name="arquivo" id="arquivo" class="arquivo" placeholder="Selecionar arquivo" readonly="readonly" value="<?= $banner["imagem"] ?>">
                        <input type="button" id="btn-escolher" class="btn-escolher" value="ESCOLHER" onclick="javascript: inputFileEscolher();">
                    </div>            
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="titulo">Título</label>
                        <input type="text" name="titulo" id="titulo" class="form-control text-uppercase" maxlength="50" value="<?= $banner["titulo"] ?>">
                    </div>            
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="link">Link</label>
                        <input type="url" name="link" id="link" class="form-control text-lowercase" maxlength="300" value="<?= $banner["link"] ?>" required>
                        <small>É necessário incluir o protocolo. Ex: http:// ou https://ecommerce.com....</small>
                    </div>            
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="ordem">ORDEM</label>
                        <select id="ordem" name="ordem" class="form-control">
                            <?php
                            
                            //CONSULTA A QUANTIDADE DE BANNERS CADASTRADOS
                            $consulta_qtde_ordem = mysqli_query($conn, "SELECT COUNT(id) AS total_ordem FROM banner_secundario"); 
                            $qtde_ordens = mysqli_fetch_array($consulta_qtde_ordem);
                            $total_ordens = $qtde_ordens["total_ordem"];
                            $contador = 1;
                            
                            while($contador <= $total_ordens){
                                                                
                                if($contador == $banner["ordem"]){
                                    ?><option value="<?= $contador ?>" selected><?= $contador ?></option><?php
                                } else {
                                    ?><option value="<?= $contador ?>"><?= $contador ?></option><?php
                                }
                                
                                $contador++;
                                
                            }
                            
                            ?>
                        </select>
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