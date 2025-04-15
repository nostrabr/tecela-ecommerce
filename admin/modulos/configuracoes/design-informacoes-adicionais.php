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
    $busca_informacoes_adicionais = mysqli_query($conn, 'SELECT * FROM informacao_adicional WHERE id = 1 OR id = 2 OR id = 3 OR id = 4'); 
}

?>

<!--SECTION INFORMAÇÕES ADICIONAIS-->
<section id="informacoes-adicionais">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Informações Adicionais</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes-design.php';">VOLTAR</button>
            </div>
        </div>
        
        <!-- ROW DA TABELA -->
        <div class="row">
            <div class="col-12">   
                <table id="admin-lista-cinco" class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">ÍCONE</th>
                            <th scope="col">TÍTULO</th>
                            <th scope="col" class="text-right">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>      
                        <?php while($informacoes_adicionais = mysqli_fetch_array($busca_informacoes_adicionais)){ ?>
                            <tr class="cursor-pointer" id="informacoes-adicionais-<?= $informacoes_adicionais['identificador'] ?>" title="Editar" onclick="javascript: editaInformacaoAdicional('<?= $informacoes_adicionais['identificador'] ?>');">
                                <td class="tabela-imagem-miniatura text-capitalize"><img src="<?= $loja['site'] ?>imagens/informacoes-adicionais/pequena/<?= $informacoes_adicionais['imagem'] ?>"></td>
                                <td class="text-uppercase"><?= $informacoes_adicionais['titulo'] ?></td>
                                <?php if($informacoes_adicionais['status'] == 1){ ?>  
                                    <td class="text-right" id="status-<?= $informacoes_adicionais['identificador'] ?>">
                                        <a class="botao-status" href="javascript: trocaStatusInformacaoAdicional('<?= $informacoes_adicionais['identificador'] ?>',<?= $informacoes_adicionais['status'] ?>)" title="Desativar"><img class="status-ativado" src="<?= $loja['site'] ?>imagens/status-ativo.png" alt="Ativo"></a><span class="d-none">ligado on ativo 1</span>
                                    </td>
                                <?php } else if($informacoes_adicionais['status'] == 0){  ?>     
                                    <td class="text-right" id="status-<?= $informacoes_adicionais['identificador'] ?>">
                                        <a class="botao-status" href="javascript: trocaStatusInformacaoAdicional('<?= $informacoes_adicionais['identificador'] ?>',<?= $informacoes_adicionais['status'] ?>)" title="Ativar"><img class="status-desativado" src="<?= $loja['site'] ?>imagens/status-inativo.png" alt="Inativo"></a><span class="d-none">desligado off inativo 1</span>
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