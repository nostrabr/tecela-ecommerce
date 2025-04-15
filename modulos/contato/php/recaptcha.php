<?php 

session_start();

require_once "recaptchalib.php";

$nome      = trim(strip_tags(mb_convert_case(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING), MB_CASE_TITLE, 'UTF-8')));
$email     = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));
$telefone  = trim(strip_tags(filter_input(INPUT_POST, "telefone", FILTER_SANITIZE_STRING)));
$mensagem  = trim(strip_tags(filter_input(INPUT_POST, "mensagem", FILTER_SANITIZE_STRING)));
$recaptcha = trim(strip_tags(filter_input(INPUT_POST, "g-recaptcha-response", FILTER_SANITIZE_STRING)));
$visitante = filter_var($_SESSION['visitante']);
unset($_SESSION['RETORNO']); 

if(!empty($nome) & !empty($email) & !empty($telefone) & !empty($mensagem) & !empty($recaptcha) & !empty($visitante)){

    include_once '../../../bd/conecta.php';

    $busca_loja = mysqli_query($conn, "SELECT recaptcha_secret FROM loja WHERE id = 1");
    $loja       = mysqli_fetch_array($busca_loja);

    $secret     = $loja['recaptcha_secret'];
    $response   = null;
    $reCaptcha  = new ReCaptcha($secret);
    $response   = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"],$recaptcha);
    
    if ($response != null && $response->success) {

        ?>            
            <form style="display: none;" id="form-envio-email" action="../../envio-email/index.php" method="POST">  
                <input type="hidden" name="tipo-envio" value="formulario-contato" required>
                <input type="text" name="nome" id="nome" value="<?= $nome ?>" required>
                <input type="email" name="email" id="email" value="<?= $email ?>" required>
                <input type="text" name="telefone" id="celular" value="<?= $telefone ?>" required>
                <textarea name="mensagem" id="mensagem" required><?= $mensagem ?></textarea>
            </form>
        <?php

        //REDIRECIONA PARA A TELA DE CADASTRO
        echo "<script>document.getElementById('form-envio-email').submit();</script>";
        
    } else {

        //PREENCHE A SESSION DE RETORNO COM SUCESSO
        $_SESSION['RETORNO'] = array(
            'ERRO'         => true,
            'STATUS-EMAIL' => 'ERRO-CAPTCHA'
        ); 

        echo "<script>location.href='../../../contato';</script>";

    }

    include_once '../../../bd/desconecta.php';

} else {    

    //PREENCHE A SESSION DE RETORNO COM SUCESSO
    $_SESSION['RETORNO'] = array(
        'ERRO'         => true,
        'STATUS-EMAIL' => 'ERRO-CAMPOS-BRANCO'
    ); 

    echo "<script>location.href='../../../contato';</script>";

}