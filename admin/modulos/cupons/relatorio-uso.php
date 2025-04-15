<!--CSS-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
                  
<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE FOR MASTER OU SUPER BUSCA TODOS
if($nivel_usuario == 'M' | $nivel_usuario == 'S'){
    $cupons = mysqli_query($conn, 'SELECT DATE(data_uso) AS data_uso, (SELECT nome FROM cupom WHERE cupom.id = cupom_uso.id_cupom) AS nome, (SELECT nome FROM cliente WHERE cupom_uso.id_cliente = cliente.id) AS cliente FROM cupom_uso ORDER BY id DESC'); 

//SE FOR ADMINISTRADOR BUSCA SÓ OS USUÁRIOS CADASTRADOS POR ELE
} else if($nivel_usuario == 'A'){    
    $cupons = mysqli_query($conn, 'SELECT DATE(data_uso) AS data_uso, (SELECT nome FROM cupom WHERE cupom.id = cupom_uso.id_cupom) AS nome, (SELECT nome FROM cliente WHERE cupom_uso.id_cliente = cliente.id) AS cliente FROM cupom_uso LEFT JOIN cupom ON cupom_uso.id_cupom = cupom.id WHERE cupom.cadastrado_por = "'.$identificador_usuario.'" ORDER BY cupom_uso.id DESC'); 

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
            <div class="col-8">    
                <div id="admin-titulo-pagina"><span class="text-nowrap">Cupons de desconto -</span> Relatório de Uso</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'cupons.php';">VOLTAR</button>
            </div>
        </div>

        <!-- ROW DA TABELA -->
        <div class="row">
            <div class="col-12">   
                <table id="admin-lista-dois" class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">CUPOM</th>
                            <th scope="col" class="d-none d-md-table-cell">CLIENTE</th> 
                            <th scope="col" class="text-right">DATA</th>   
                        </tr>
                    </thead>
                    <tbody>      
                        <?php while($cupom = mysqli_fetch_array($cupons)){ ?>    
                            <tr>
                                <td class="text-uppercase"><?= $cupom['nome'] ?></td>
                                <td class="text-capitalize d-none d-md-table-cell"><?= $cupom['cliente'] ?></td>
                                <td class="text-right"><?= date('d/m/Y', strtotime($cupom['data_uso'])) ?></td>
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