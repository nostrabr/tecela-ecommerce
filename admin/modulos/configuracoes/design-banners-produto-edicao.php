<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$identificador_banner  = filter_input(INPUT_GET,"id",FILTER_SANITIZE_STRING);
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE TENTAR ACESSAR COMO USUÁRIO OU ADMINISTRADOR, DESLOGA DO SISTEMA
if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
} else {
    $busca_banner = mysqli_query($conn, 'SELECT * FROM banner_produto WHERE identificador = "'.$identificador_banner.'"'); 
    $banner       = mysqli_fetch_array($busca_banner);
}

?>

<!--SECTION EDIÇÃO BANNER-->
<section id="banner-produto-edicao">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Banners Produto - Edição</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes-design-banners-produto.php';">VOLTAR</button>
            </div>
        </div>
        
        <!--FORM DE EDIÇÃO DE BANNER-->
        <form enctype="multipart/form-data" action="modulos/configuracoes/php/edicao-banner-produto.php" method="POST">
            
            <input type="hidden" name="id" value="<?= $banner["id"] ?>">
            
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="imagem">Banner (RECOMENDADO 1920x400px)</label>
                        <input type="file" name="imagem" id="imagem" class="imagem form-control-file" accept="image/png, image/jpeg" onchange="javascript: inputFileChange();">
                        <input type="text" name="arquivo" id="arquivo" class="arquivo" placeholder="Selecionar arquivo" readonly="readonly" value="<?= $banner["imagem"] ?>">
                        <input type="button" id="btn-escolher" class="btn-escolher" value="ESCOLHER" onclick="javascript: inputFileEscolher();">
                    </div>            
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="link">Link</label>
                        <input type="url" name="link" id="link" class="form-control" maxlength="300" value="<?= $banner["link"] ?>">
                    </div>            
                </div>
            </div>
            
            <div class="row d-none">
                <div class="col-12">
                    <div class="form-group">
                        <label for="ordem">ORDEM</label>
                        <select id="ordem" name="ordem" class="form-control">
                            <?php
                            
                            //CONSULTA A QUANTIDADE DE BANNERS CADASTRADOS
                            $consulta_qtde_ordem = mysqli_query($conn, "SELECT COUNT(id) AS total_ordem FROM banner_produto"); 
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