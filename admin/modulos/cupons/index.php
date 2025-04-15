<!--CSS-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
                  
<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE FOR MASTER OU SUPER BUSCA TODOS
if($nivel_usuario == 'M' | $nivel_usuario == 'S'){
    $cupons = mysqli_query($conn, 'SELECT identificador, nome, quantidade, DATE(validade) AS validade, status, (SELECT nome FROM usuario WHERE cupom.cadastrado_por = usuario.identificador) AS por, (SELECT COUNT(id) FROM cupom_uso WHERE cupom.id = cupom_uso.id_cupom) AS utilizados FROM cupom ORDER BY validade DESC'); 

//SE FOR ADMINISTRADOR BUSCA SÓ OS USUÁRIOS CADASTRADOS POR ELE
} else if($nivel_usuario == 'A'){    
    $cupons = mysqli_query($conn, 'SELECT identificador, nome, quantidade, DATE(validade) AS validade, status, (SELECT nome FROM usuario WHERE cupom.cadastrado_por = usuario.identificador) AS por, (SELECT COUNT(id) FROM cupom_uso WHERE cupom.id = cupom_uso.id_cupom) AS utilizados FROM cupom WHERE cadastrado_por = "'.$identificador_usuario.'" ORDER BY validade DESC'); 

//SE TENTAR ACESSAR COMO USUÁRIO, DESLOGA DO SISTEMA
} else if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
}

?>

<!--SECTION CUPONS-->
<section id="cupons">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-6">    
                <div id="admin-titulo-pagina">Cupons de desconto</div>
            </div>
            <div class="col-6 text-right">
                <button type="button" class="btn btn-dark btn-top-right mb-1 mb-md-0" onclick="javascript: window.location.href = 'cupons-relatorio-uso.php';">RELATÓRIO DE USO</button>
                <button type="button" class="btn btn-dark btn-top-right ml-0 ml-md-2" onclick="javascript: window.location.href = 'cupons-cadastra.php';">NOVO CUPOM</button>
            </div>
        </div>

        <!-- ROW DA TABELA -->
        <div class="row">
            <div class="col-12">   
                <table id="admin-lista" class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">NOME</th>
                            <th scope="col" class="d-none d-lg-table-cell">CADASTRADO POR</th>
                            <th scope="col" class="d-none d-md-table-cell">VALIDADE</th>
                            <th scope="col" class="d-none d-md-table-cell">QUANTIDADE</th>
                            <th scope="col" class="d-none d-md-table-cell">UTILIZADOS</th>
                            <th scope="col" class="text-right">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>      
                        <?php while($cupom = mysqli_fetch_array($cupons)){ ?>          
                            <?php 
                                if((strtotime($cupom['validade']) < strtotime(date('Y-m-d')))){
                                    $cupom_valido = false;
                                } else if($cupom['quantidade'] <= $cupom['utilizados']){
                                    $cupom_valido = false;
                                } else { 
                                    $cupom_valido = true;  
                                }
                            ?>   
                            <tr class="cursor-pointer <?php if(!$cupom_valido){ echo 'tabela-linha-desativada'; } ?>" id="cupom-<?= $cupom['identificador'] ?>" title="Editar" onclick="javascript: edita('<?= $cupom['identificador'] ?>');">
                                <td class="text-uppercase"><?= $cupom['nome'] ?></td>
                                <td class="text-capitalize d-none d-lg-table-cell"><?= $cupom['por'] ?></td>
                                <td class="d-none d-md-table-cell"><?= date('d/m/Y', strtotime($cupom['validade'])) ?></td>
                                <td class="d-none d-md-table-cell"><?= $cupom['quantidade'] ?></td>
                                <td class="d-none d-md-table-cell"><?= $cupom['utilizados'] ?></td>
                                <?php if($cupom_valido){ ?>
                                    <?php if($cupom['status'] == 1){ ?>  
                                        <td class="text-right" id="status-<?= $cupom['identificador'] ?>"><a class="botao-status" href="javascript: trocaStatus('<?= $cupom['identificador'] ?>',<?= $cupom['status'] ?>)" title="Desativar"><img class="status-ativado" src="<?= $loja['site'] ?>imagens/status-ativo.png" alt="Ativo"></a><span class="d-none">ligado on ativo 1</span></td>
                                    <?php } else if($cupom['status'] == 0){ ?>     
                                        <td class="text-right" id="status-<?= $cupom['identificador'] ?>"><a class="botao-status" href="javascript: trocaStatus('<?= $cupom['identificador'] ?>',<?= $cupom['status'] ?>)" title="Ativar"><img class="status-desativado" src="<?= $loja['site'] ?>imagens/status-inativo.png" alt="Inativo"></a><span class="d-none">desligado off inativo 0</span></td>
                                    <?php } ?>   
                                <?php } else { ?>  
                                    <td class="text-right"><img class="status-desativado" src="<?= $loja['site'] ?>imagens/status-inativo.png" alt="Inativo"></a><span class="d-none">desligado off inativo 0</span></td>
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
<script type="text/javascript" src="modulos/cupons/js/scripts.js"></script>