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

//BUSCA OS CLIENTES CADASTRADOS
$clientes = mysqli_query($conn, 'SELECT identificador, nome, sobrenome, cpf, email, data_cadastro, newsletter FROM cliente ORDER BY data_cadastro DESC'); 

?>

<!--SECTION CLIENTES-->
<section id="clientes">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-6">    
                <div id="admin-titulo-pagina">CLIENTES</div>
            </div>     
            <?php if($nivel_usuario != 'U'){ ?>
                <div class="col-6 text-right">
                    <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'clientes-emails.php';">CAIXA DE SAÍDA</button>
                </div>
            <?php } ?>
        </div>

        <!-- ROW DA TABELA -->
        <div class="row">
            <div class="col-12">   
                <table id="admin-lista" class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">NOME</th>
                            <th scope="col" class="d-none d-lg-table-cell">CPF/CNPJ</th>
                            <th scope="col" class="d-none d-lg-table-cell">E-MAIL</th>
                            <th scope="col" class="d-none d-lg-table-cell">CADASTRADO</th>
                            <?php if($nivel_usuario != 'U'){ ?>
                                <th scope="col" class="text-right">AÇÕES</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>      
                        <?php while($cliente = mysqli_fetch_array($clientes)){ ?> 
                            <tr <?php if($nivel_usuario != 'U'){ ?> id="cliente-<?= $cliente['identificador'] ?>" class="cursor-pointer" title="Editar" onclick="javascript: edita('<?= $cliente['identificador'] ?>');"<?php } ?>>
                                <?php if(strlen($cliente['cpf']) == 18){ ?>
                                    <td class="text-capitalize"><?= $cliente['nome'] ?></td> 
                                <?php } else { ?>
                                    <td class="text-capitalize"><?= $cliente['nome'].' '.$cliente['sobrenome'] ?></td> 
                                <?php } ?>
                                <td class="text-lowercase d-none d-md-table-cell"><?= $cliente['cpf'] ?></td> 
                                <td class="text-lowercase d-none d-md-table-cell"><?= $cliente['email'] ?></td> 
                                <td class="text-capitalize d-none d-md-table-cell"><?= date('d/m/Y', strtotime($cliente['data_cadastro'])) ?></td>                                 
                                <?php if($nivel_usuario != 'U'){ ?>
                                    <td class="text-right"><?php if($cliente['newsletter'] == 1){ ?><a class="botao-email" href="clientes-email.php?id=<?= $cliente['identificador'] ?>" title=" Enviar e-mail"><img class="acao-email" src="<?= $loja['site'] ?>imagens/acao-email.png" alt="E-mail"></a><?php } else { ?><img class="acao-email-desativada" src="<?= $loja['site'] ?>imagens/acao-email.png" alt="E-mail"><?php } ?></td>
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
<script type="text/javascript" src="modulos/clientes/js/scripts.js"></script>