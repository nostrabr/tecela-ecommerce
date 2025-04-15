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
} else {
    $banners = mysqli_query($conn, 'SELECT * FROM banner_produto ORDER BY ordem ASC'); 
}

?>

<!--SECTION BANNERS-->
<section id="banners">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-4">    
                <div id="admin-titulo-pagina">Banners Produto</div>    
            </div>
            <div class="col-8 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes-design-banners-produto-cadastra.php';">NOVO BANNER</button>
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes-design.php';">VOLTAR</button>
            </div>
        </div>

        <!-- ROW DA TABELA -->
        <div class="row">
            <div class="col-12">   
                <table id="admin-lista-seis" class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">PREVIEW</th>
                            <th scope="col" class="text-right">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>      
                        <?php while($banner = mysqli_fetch_array($banners)){ ?>
                            <tr class="cursor-pointer" id="banner-<?= $banner['identificador'] ?>" title="Editar" onclick="javascript: editaBannerProduto('<?= $banner['identificador'] ?>');">                                
                                <td class="tabela-imagem-miniatura text-capitalize"><img src="<?= $loja['site'] ?>imagens/banners-produto/pequena/<?= $banner['imagem'] ?>"></td>
                                <?php if($banner['status'] == 1){ ?>  
                                    <td class="text-right" id="status-<?= $banner['identificador'] ?>">
                                        <a class="botao-status" href="javascript: trocaStatusBannerProduto('<?= $banner['identificador'] ?>',<?= $banner['status'] ?>,'<?= $banner["id"] ?>','<?= $banner["ordem"] ?>')" title="Desativar"><img class="status-ativado" src="<?= $loja['site'] ?>imagens/status-ativo.png" alt="Ativo"></a><span class="d-none">ligado on ativo 1</span>
                                        <a href="javascript: excluiBannerProduto('<?= $banner["id"] ?>','<?= $banner["ordem"] ?>');" title="Excluir" class="botao-excluir"><img class="acao-excluir" src="<?= $loja['site'] ?>imagens/acao-excluir.png"></a>
                                    </td>
                                <?php } else if($banner['status'] == 0){  ?>     
                                    <td class="text-right" id="status-<?= $banner['identificador'] ?>">
                                        <a class="botao-status" href="javascript: trocaStatusBannerProduto('<?= $banner['identificador'] ?>',<?= $banner['status'] ?>,'<?= $banner["id"] ?>','<?= $banner["ordem"] ?>')" title="Ativar"><img class="status-desativado" src="<?= $loja['site'] ?>imagens/status-inativo.png" alt="Inativo"></a><span class="d-none">desligado off inativo 1</span>
                                        <a href="javascript: excluiBannerProduto('<?= $banner["id"] ?>','<?= $banner["ordem"] ?>');" title="Excluir" class="botao-excluir"><img class="acao-excluir" src="<?= $loja['site'] ?>imagens/acao-excluir.png"></a>
                                    </td>
                                <?php } ?>  
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="modulos/configuracoes/js/scripts.js"></script>