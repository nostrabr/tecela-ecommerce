<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE TENTAR ACESSAR COMO USUÁRIO OU ADMINISTRADOR, DESLOGA DO SISTEMA
if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
}

?>

<!--SECTION CADASTRO BANNER-->
<section id="banner-produto-cadastro">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Banners Produto - Cadastro</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes-design-banners-produto.php';">VOLTAR</button>
            </div>
        </div>
        
        <!--FORM DE CADASTRO-->
        <form enctype="multipart/form-data" action="modulos/configuracoes/php/cadastro-banner-produto.php" method="POST">
            
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="imagem">Banner (RECOMENDADO 1920x400px)<span class="campo-obrigatorio">*</span></label>
                        <input type="file" name="imagem" id="imagem" class="imagem form-control-file" accept="image/png, image/jpeg" onchange="javascript: inputFileChange();">
                        <input type="text" name="arquivo" id="arquivo" class="arquivo" placeholder="Selecionar arquivo" readonly="readonly">
                        <input type="button" id="btn-escolher" class="btn-escolher" value="ESCOLHER" onclick="javascript: inputFileEscolher();">
                    </div>            
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="link">Link</label>
                        <input type="url" name="link" id="link" class="form-control" maxlength="300">
                    </div>            
                </div>
            </div>
            
            <?php 
            
            //CONSULTA SE POSSUI ALGUM BANNER DE PRODUTO
            $consulta_ordem = mysqli_query($conn, "SELECT ordem FROM banner_produto ORDER BY ordem"); 
            
            //SE SIM, LISTA A ORDEM
            if(mysqli_num_rows($consulta_ordem) > 0){
                
            ?>
            
                <div class="row d-none">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="ordem">Ordem <span class="campo-obrigatorio">*</span></label>
                            <select id="ordem" name="ordem" class="form-control" required>
                                
                                <?php
                                
                                //LISTA AS EXISTENTES
                                while($ordens = mysqli_fetch_array($consulta_ordem)){
                                    ?><option value="<?= $ordens["ordem"] ?>"><?= $ordens["ordem"] ?></option><?php
                                }
                                
                                //CRIA UMA NOVA POR ÚLTIMO SETADA
                                $consulta_qtde_ordem = mysqli_query($conn, "SELECT COUNT(ordem) AS total_ordem FROM banner_produto"); 
                                $qtde_ordens = mysqli_fetch_array($consulta_qtde_ordem);                            
                                ?><option value="<?= $qtde_ordens["total_ordem"]+1 ?>" selected><?= $qtde_ordens["total_ordem"]+1 ?></option><?php
                                
                                ?>
                            </select>
                        </div>            
                    </div>
                </div>
            
            <?php 
            
            //SE INEXISTENTE, GERA COMO PRIMEIRA
            } else { 
                
                ?><input type="hidden" id="ordem" name="ordem" value="1"><?php 
            
            } 
            
            ?>
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