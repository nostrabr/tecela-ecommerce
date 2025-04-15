<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE TENTAR ACESSAR COMO USUÁRIO OU ADMINISTRADOR, DESLOGA DO SISTEMA
if($nivel_usuario == 'U' | $nivel_usuario == 'A'){
    echo "<script>location.href='logout.php';</script>";
} else if($nivel_usuario == 'M' | $nivel_usuario == 'S'){
    $busca_paginas = mysqli_query($conn, 'SELECT * FROM pagina_customizada WHERE status != 2 ORDER BY titulo ASC');     
}

?>

<!--CSS-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">

<!--SECTION CONFIGURAÇÕES-->
<section id="configuracoes-paginas-customizadas">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-7">    
                <div id="admin-titulo-pagina">Páginas customizadas</div>
            </div>
            <div class="col-5 text-right">  
                <button type="button" class="btn btn-dark btn-top-right mb-1 mb-md-0" onclick="javascript: window.location.href = 'configuracoes-paginas-customizadas-cadastra.php';">NOVA PÁGINA</button>
                <button type="button" class="btn btn-dark btn-top-right ml-0 ml-md-1" onclick="javascript: window.location.href = 'configuracoes.php';">VOLTAR</button>
            </div>
        </div>

        <!-- ROW DA TABELA -->
        <div class="row">
            <div class="col-12">   
                <table id="admin-lista-sete" class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">TÍTULO</th>
                            <th scope="col" class="text-right">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>      
                        <?php while($pagina = mysqli_fetch_array($busca_paginas)){ ?>
                            <tr class="cursor-pointer" id="pagina-customizada-<?= $pagina['identificador'] ?>" title="Editar" onclick="javascript: editaPaginaCustomizada('<?= $pagina['identificador'] ?>');">                                 
                                <td class="text-capitalize"><?= $pagina['titulo'] ?></td>                                
                                <td class="text-right" id="status-<?= $pagina['identificador'] ?>">
                                    <?php if($pagina['status'] == 1){ ?>  
                                        <a class="botao-status" href="javascript: trocaStatusPaginaCustomizada('<?= $pagina['identificador'] ?>',<?= $pagina['status'] ?>,'<?= $pagina["titulo"] ?>')" title="Desativar"><img class="status-ativado" src="<?= $loja['site'] ?>imagens/status-ativo.png" alt="Ativo"></a><span class="d-none">ligado on ativo 1</span>
                                    <?php } else if($pagina['status'] == 0){  ?>     
                                        <a class="botao-status" href="javascript: trocaStatusPaginaCustomizada('<?= $pagina['identificador'] ?>',<?= $pagina['status'] ?>,'<?= $pagina["titulo"] ?>')" title="Ativar"><img class="status-desativado" src="<?= $loja['site'] ?>imagens/status-inativo.png" alt="Inativo"></a><span class="d-none">desligado off inativo 1</span>
                                    <?php } ?> 
                                    <a href="javascript: duplicaPaginaCustomizada('<?= $pagina["identificador"] ?>','<?= $pagina["titulo"] ?>');" title="Duplicar" class="botao-duplicar"><img class="acao-duplicar" src="<?= $loja['site'] ?>imagens/acao-duplicar.png"></a>
                                    <a href="javascript: excluiPaginaCustomizada('<?= $pagina["identificador"] ?>','<?= $pagina["titulo"] ?>');" title="Excluir" class="botao-excluir"><img class="acao-excluir" src="<?= $loja['site'] ?>imagens/acao-excluir.png"></a>
                                </td>
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