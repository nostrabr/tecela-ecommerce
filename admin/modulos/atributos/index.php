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

//BUSCA OS ATRIBUTOS CADASTRADOS
$atributos = mysqli_query($conn, 'SELECT identificador, nome, visualizacao FROM atributo WHERE status = 1 ORDER BY nome ASC'); 

?>

<!--SECTION ATRIBUTOS-->
<section id="atributos">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-7">    
                <div id="admin-titulo-pagina">Atributos de produtos</div>
            </div>
            <div class="col-5 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'atributos-cadastra.php';">NOVO ATRIBUTO</button>
            </div>
        </div>

        <!-- ROW DA TABELA -->
        <div class="row">
            <div class="col-12">   
                <table id="admin-lista" class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">NOME</th>
                            <th scope="col">VISUALIZAÇÃO</th>
                            <th scope="col" class="text-right">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>      
                        <?php while($atributo = mysqli_fetch_array($atributos)){ ?> 
                            <tr id="atributo-<?= $atributo['identificador'] ?>" class="cursor-pointer" title="Editar" onclick="javascript: edita('<?= $atributo['identificador'] ?>');">
                                <td class="text-capitalize"><?= $atributo['nome'] ?></td> 
                                <td class="text-capitalize"><?php if($atributo['visualizacao'] == 'S'){ echo 'Nome'; } else if($atributo['visualizacao'] == 'L'){ echo 'Lista'; } else if($atributo['visualizacao'] == 'C'){ echo 'Cor'; } else if($atributo['visualizacao'] == 'T'){ echo 'Textura'; } ?></td> 
                                <td class="text-right"><a class="botao-excluir" href="javascript: exclui('<?= $atributo['identificador'] ?>','<?= $atributo['nome'] ?>')" title="Excluir"><img class="acao-excluir" src="<?= $loja['site'] ?>imagens/acao-excluir.png" alt="Excluir"></a></td>
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
<script type="text/javascript" src="modulos/atributos/js/scripts.js"></script>