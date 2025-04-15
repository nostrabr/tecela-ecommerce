<!--CSS-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="modulos/orcamentos/css/style.css">
                  
<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//BUSCA TODAS OS ORÇAMENTOS CADASTRADOS
$orcamentos = mysqli_query($conn, 'SELECT o.codigo, o.identificador, o.data_cadastro, c.nome AS nome_cliente, c.sobrenome AS sobrenome_cliente
FROM orcamento AS o 
INNER JOIN cliente AS c ON o.id_cliente = c.id
ORDER BY o.data_cadastro DESC'); 

?>

<!--SECTION ORÇAMENTOS-->
<section id="pedidos">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-6">    
                <div id="admin-titulo-pagina">Orçamentos</div>
            </div>
        </div>

        <!-- ROW DA TABELA -->
        <div class="row">
            <div class="col-12">   
                <table id="admin-lista" class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">CÓDIGO</th>
                            <th scope="col">DATA</th>
                            <th scope="col" class="d-none d-md-table-cell">CLIENTE</th>  
                        </tr>
                    </thead>
                    <tbody>      
                        <?php while($orcamento = mysqli_fetch_array($orcamentos)){ ?>  
                            <tr class="cursor-pointer" title="Editar" onclick="javascript: edita('<?= $orcamento['identificador'] ?>');">
                                <td class="text-capitalize codigo-pedido"><?= $orcamento['codigo'] ?></td>
                                <td class="text-capitalize"><?= date('d/m/Y H:i', strtotime($orcamento['data_cadastro'])) ?></td>
                                <td class="text-capitalize d-none d-md-table-cell"><?= $orcamento['nome_cliente'].' '.$orcamento['sobrenome_cliente']  ?></td> 
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
<script type="text/javascript" src="modulos/orcamentos/js/scripts.js"></script>