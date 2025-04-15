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

//BUSCA OS E-MAILS CADASTRADOS
$emails = mysqli_query($conn, 'SELECT identificador, email, assunto, data_envio, (SELECT nome FROM usuario WHERE identificador = email.enviado_por) AS por, (SELECT nome FROM cliente WHERE id = email.id_cliente) AS cliente FROM email ORDER BY data_envio DESC'); 

?>

<!--SECTION CLIENTES-->
<section id="clientes-lista-emails">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Clientes - Caixa de Saída</div>
            </div>     
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'clientes.php';">VOLTAR</button>
            </div>
        </div>

        <!-- ROW DA TABELA -->
        <div class="row">
            <div class="col-12">   
                <table id="admin-lista-dois" class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">CLIENTE</th>
                            <th scope="col" class="d-none d-lg-table-cell">E-MAIL</th>
                            <th scope="col" class="d-none d-lg-table-cell">ASSUNTO</th>
                            <th scope="col" class="d-none d-lg-table-cell">POR</th>
                            <th scope="col" class="text-right">ENVIADO</th>
                        </tr>
                    </thead>
                    <tbody>      
                        <?php while($email = mysqli_fetch_array($emails)){ ?> 
                            <tr id="email-<?= $email['identificador'] ?>" class="cursor-pointer" title="Visualizar" onclick="javascript: visualiza('<?= $email['identificador'] ?>');">
                                <td class="text-capitalize"><?= $email['cliente'] ?></td> 
                                <td class="text-lowercase d-none d-md-table-cell"><?= $email['email'] ?></td> 
                                <td class="text-capitalize d-none d-md-table-cell"><?= $email['assunto'] ?></td>      
                                <td class="text-capitalize d-none d-md-table-cell"><?= $email['por'] ?></td>  
                                <td class="text-right"><?= date('d/m/Y H:i', strtotime($email['data_envio'])) ?></td>
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
<script type="text/javascript" src="modulos/clientes/js/scripts.js"></script>