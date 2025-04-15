<!--CSS-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
                  
<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE FOR USUÁRIO, DESLOGA
if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
}

//BUSCA TODASAS MARCAS CADASTRADAS
$marcas = mysqli_query($conn, 'SELECT identificador, nome, logo, (SELECT nome FROM usuario WHERE marca.cadastrado_por = usuario.identificador) AS por, (SELECT COUNT(id) FROM produto WHERE produto.id_marca = marca.id) AS produtos FROM marca ORDER BY nome'); 

?>

<!--SECTION MARCAS-->
<section id="marcas">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-7">    
                <div id="admin-titulo-pagina">Marcas</div>
            </div>
            <div class="col-5 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'marcas-cadastra.php';">NOVA MARCA</button>
            </div>
        </div>

        <!-- ROW DA TABELA -->
        <div class="row">
            <div class="col-12">   
                <table id="admin-lista" class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col" class="d-none d-md-table-cell">LOGO</th>
                            <th scope="col">NOME</th>
                            <th scope="col" class="d-none d-md-table-cell">CADASTRADO POR</th>
                            <th scope="col" class="text-right">PRODUTOS</th>
                        </tr>
                    </thead>
                    <tbody>      
                        <?php while($marca = mysqli_fetch_array($marcas)){ ?>  
                            <tr class="cursor-pointer" title="Editar" onclick="javascript: edita('<?= $marca['identificador'] ?>');">
                                <td class="tabela-imagem-miniatura text-capitalize d-none d-md-table-cell"><?php if($marca['logo'] != ''){ ?><img src="<?= $loja['site'] ?>imagens/marcas/pequena/<?= $marca['logo'] ?>" alt="<?= $marca['nome'] ?>"><?php } else { echo $marca['nome']; } ?></td>
                                <td class="text-capitalize align-middle"><?= $marca['nome'] ?></td>
                                <td class="text-capitalize align-middle d-none d-md-table-cell"><?= $marca['por'] ?></td>
                                <td class="text-right align-middle"><?= $marca['produtos'] ?></td>
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
<script type="text/javascript" src="modulos/marcas/js/scripts.js"></script>