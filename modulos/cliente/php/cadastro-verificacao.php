<?php

//INICIA A SESSÃO
session_start();

//RECEBE OS DADOS DO CADASTRO
$nome      = trim(strip_tags(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING)));   
$sobrenome = trim(strip_tags(filter_input(INPUT_POST, "sobrenome", FILTER_SANITIZE_STRING)));   
$cpf       = trim(strip_tags(filter_input(INPUT_POST, "cpf", FILTER_SANITIZE_STRING)));   
$celular   = trim(strip_tags(filter_input(INPUT_POST, "celular", FILTER_SANITIZE_STRING)));  
$email     = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));   
$senha     = trim(strip_tags(filter_input(INPUT_POST, "senha", FILTER_SANITIZE_STRING)));  
unset($_SESSION['RETORNO']); 

if(!empty($nome) & !empty($sobrenome) & !empty($cpf) & !empty($celular) & !empty($email) & !empty($senha)){

    include_once '../../../bd/conecta.php';
    
    //VERIFICA SE E-MAIL OU CPF JÁ NÃO ESTÃO CADASTRADOS
    $busca_clientes = mysqli_query($conn, "SELECT id FROM cliente WHERE cpf = '$cpf' OR email = '$email'");

    //SE JÁ TEM CADASTRO COM ALGUM DOS CAMPOS
    if(mysqli_num_rows($busca_clientes) > 0){

        //PREENCHE A SESSION DE RETORNO COM ERRO
        $_SESSION['RETORNO'] = array('ERRO' => 'ERRO-REPETIDO');   
        
        //REDIRECIONA PARA A TELA DE CADASTRO
        echo "<script>window.history.back();</script>";

    } else {

        ?>

        <form id="form-cadastro-confirmacao" style="display: none;" action="../../../modulos/envio-email/index.php" method="POST">        
            <input type="text" name="tipo-envio" value="formulario-cadastro-cliente-verificacao" required>     
            <input type="text" name="nome" maxlength="50" value="<?= $nome ?>" required>
            <input type="text" name="sobrenome" maxlength="50" value="<?= $sobrenome ?>" required>
            <input type="text" name="cpf" id="cpf-cnpj" maxlength="18" value="<?= $cpf ?>" required>
            <input type="text" name="celular" id="celular" value="<?= $celular ?>" required>
            <input type="email" name="email" maxlength="50" value="<?= $email ?>" required>
            <input type="password" name="senha" id="senha" maxlength="32" minlength="8" value="<?= $senha ?>" required>
            <input type="checkbox" name="aceite-termos" checked required>
        </form>

        <?php
        
        //REDIRECIONA PARA A TELA DE CADASTRO
        echo "<script>document.getElementById('form-cadastro-confirmacao').submit();</script>";

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
        echo "<script>location.href='/';</script>";
        
    }

}

