<?php 

$identificador_email = filter_input(INPUT_GET,"id",FILTER_SANITIZE_STRING);
$nivel_usuario       = filter_var($_SESSION['nivel']);

//SE FOR USUÁRIO, DESLOGA
if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
}

//BUSCA E-MAIL
$busca_email = mysqli_query($conn, 'SELECT email, assunto, data_envio, corpo_email, (SELECT nome FROM usuario WHERE identificador = email.enviado_por) AS por, (SELECT nome FROM cliente WHERE id = email.id_cliente) AS cliente FROM email WHERE identificador = "'.$identificador_email.'"'); 

//VERIFICA SE ENCONTROU O E-MAIL
if(mysqli_num_rows($busca_email) == 0){
    echo "<script>location.href='clientes-emails.php';</script>";
} else {
    $email = mysqli_fetch_array($busca_email);
}

?>

<!--SECTION CLIENTES-->
<section id="clientes-detalhes-email">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Clientes - E-mail</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'clientes-emails.php';">VOLTAR</button>
            </div>
        </div> 

        <!-- ROW DOS DETALHES -->
        <div class="row">
            <div class="col-12">
                <ul>
                    <li>De: <?= $email['por'] ?></li>
                    <li>Para: <?= $email['cliente'] ?> (<?= $email['email'] ?>)</li>
                    <li>Envio: <?= date('d/m/Y H:i', strtotime($email['data_envio'])) ?></li>
                    <li class="text-capitalize mt-2 mb-2">Assunto: <?= $email['assunto'] ?></li>
                    <li class="mb-2">E-mail:</li>
                </ul>
            </div>
            <div class="col-12">
                <?= $email['corpo_email'] ?>
            </div>
        </div>

    </div>

</section>
