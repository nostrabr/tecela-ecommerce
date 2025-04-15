<!--CSS-->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

<?php 

$identificador_cliente = filter_input(INPUT_GET,"id",FILTER_SANITIZE_STRING);
$nivel_usuario         = filter_var($_SESSION['nivel']);
$acao                  = filter_input(INPUT_GET,"acao",FILTER_SANITIZE_STRING);

//SE FOR USUÁRIO, DESLOGA
if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
}

//BUSCA CLIENTE
$busca_cliente = mysqli_query($conn, 'SELECT nome, email FROM cliente WHERE identificador = "'.$identificador_cliente.'"'); 

//VERIFICA SE ENCONTROU O CLIENTE
if(mysqli_num_rows($busca_cliente) == 0){
    echo "<script>location.href='clientes.php';</script>";
} else {

    $cliente = mysqli_fetch_array($busca_cliente);
    
    //BUSCA LOJA
    $busca_loja = mysqli_query($conn, 'SELECT email_cabecalho, email_rodape, email_pedido_confirmacao_retirada FROM loja WHERE id = 1'); 
    $loja       = mysqli_fetch_array($busca_loja);
    

    //VERIFICA SE VEIO ACÃO DE E-MAIL
    $assunto     = '';
    $corpo_email = '';
    if($acao === 'confirmacao-retirada'){      
        $variaveis_email = array('{cliente_nome}');
        $variaveis_troca = array($cliente['nome']);
        $assunto         = 'Autorização de retirada';
        $corpo_email     = str_replace($variaveis_email, $variaveis_troca, $loja['email_pedido_confirmacao_retirada']);
    }

}

//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){
    if($_SESSION['RETORNO']['ERRO'] == 'ERRO-EMAIL'){
        echo "<script>mensagemAviso('erro', 'Erro ao enviar e-mail! Se o problema persistir contate o administrador do sistema.', 3000);</script>";
    } else {
        echo "<script>mensagemAviso('sucesso', 'E-mail enviado com sucesso!', 2000); setTimeout(function(){ location.href='clientes-emails.php'; },2000);</script>";
    }
}

?>

<!--SECTION CLIENTES-->
<section id="clientes-envio-email">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Clientes - Envio de e-mail</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'clientes.php';">VOLTAR</button>
            </div>
            <div class="col-12 admin-subtitulo-pagina">
                <p class="text-capitalize">Para: <?= $cliente['nome'] ?></p>
                <p>E-mail: <?= $cliente['email'] ?></p>
            </div>
        </div>

        <!-- FORM DE ENVIO -->
        <form action="modulos/clientes/php/email.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="identificador" value="<?= $identificador_cliente ?>">    

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="assunto">Assunto <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control text-capitalize" name="assunto" id="assunto" maxlength="100" value="<?= $assunto ?>" required>
                    </div>
                </div>
            </div>

            <label for="summernote">E-mail <span class="campo-obrigatorio">*</span></label>
            <textarea id="summernote" class="summernote" name="summernote" required>
                <?= $loja['email_cabecalho'] ?>  
                <?= $corpo_email ?>
                <?= $loja['email_rodape'] ?>
            </textarea>

            <div class="row mt-3">
                <div class="col-12 text-center text-md-right">
                    <div class="form-group">
                        <button type="submit" class="btn btn-dark btn-bottom">ENVIAR</button>
                    </div>
                </div>
            </div>

        </form>

    </div>

</section>

<?php /* LIMPA A SESSION DE RETORNO */ unset($_SESSION['RETORNO']); ?>

<!--SCRIPTS-->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script type="text/javascript" src="modulos/clientes/js/scripts.js"></script>
