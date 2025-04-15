<?php

//INICIA A SESSÃO
session_start();

//RECEBE OS DADOS DO FORM
$email = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));   
unset($_SESSION['RETORNO']); 

if(!empty($email)){

    include_once '../../../bd/conecta.php';
    
    //VERIFICA SE E-MAIL EXISTE
    $busca_clientes = mysqli_query($conn, "SELECT id FROM cliente WHERE email = '$email'");

    //SE JÁ TEM CADASTRO COM ALGUM DOS CAMPOS
    if(mysqli_num_rows($busca_clientes) == 0){

        //PREENCHE A SESSION DE RETORNO COM ERRO
        $_SESSION['RETORNO'] = array('ERRO' => 'ERRO-INEXISTENTE');   
        
        //REDIRECIONA PARA A TELA DE CADASTRO
        echo "<script>window.history.back();</script>";

    } else {

        ?>
        
        <form id="form-recuperacao-senha-confirmacao" style="display: none;" action="../../../modulos/envio-email/index.php" method="POST">        
            <input type="text" name="tipo-envio" value="formulario-recuperacao-senha-confirmacao" required>     
            <input type="email" name="email" maxlength="50" value="<?= $email ?>" required>
        </form>

        <?php
        
        //REDIRECIONA PARA A TELA DE CADASTRO
        echo "<script>document.getElementById('form-recuperacao-senha-confirmacao').submit();</script>";

    }
    
    include_once '../../../bd/desconecta.php';

} else {

    //VERIFICA SE VEIO DO AJAX
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                
        //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
        $dados[] = array(
            "status" => "ERRO"
        );
        echo json_encode($dados);
        
    } else {
        
        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='../../../home';</script>";
        
    }

}

