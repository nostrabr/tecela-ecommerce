<?php

//INICIA A SESSÃO
session_start();

//RECEBE OS DADOS DO FORM
$senha                 = trim(strip_tags(filter_input(INPUT_POST, "senha", FILTER_SANITIZE_STRING))); 
$senha_confirmacao     = trim(strip_tags(filter_input(INPUT_POST, "senha-confirmacao", FILTER_SANITIZE_STRING))); 
$identificador_cliente = $_SESSION['RECUPERACAO-SENHA']['IDENTIFICADOR'];
unset($_SESSION['RETORNO']); 

if(!empty($senha) & !empty($senha_confirmacao) & !empty($identificador_cliente)){

    include_once '../../../bd/conecta.php';

    //BUSCA O CLIENTE
    $busca_cliente = mysqli_query($conn, "SELECT id FROM cliente WHERE identificador = '$identificador_cliente'");

    if(mysqli_num_rows($busca_cliente) > 0){

        if($senha === $senha_confirmacao){

            //CRIPTOGRAFA A SENHA
            $senha = md5($senha);

            //ALTERA A SENHA NO BANCO
            mysqli_query($conn, "UPDATE cliente SET senha = '$senha' WHERE identificador = '$identificador_cliente'");
            
            //REMOVE SESSÃO DE RECUPERACÃO
            unset($_SESSION['RECUPERACAO-SENHA']); 

            //PREENCHE A SESSION DE RETORNO COM ERRO
            $_SESSION['RETORNO'] = array(
                'ERRO'   => false,
                'STATUS' => 'SENHA-SUCESSO'
            ); 
            
            //REDIRECIONA PARA A TELA DE LOGIN
            echo "<script>location.href='../../../login';</script>";

        } else {

            //PREENCHE A SESSION DE RETORNO COM ERRO
            $_SESSION['RETORNO'] = array(
                'ERRO'   => 'SENHAS-NAO-CONFEREM',
                'STATUS' => 'ERRO'
            ); 
            
            //REDIRECIONA PARA A TELA DE SENHA
            echo "<script>window.history.back();</script>";

        }
        
    } else {
        //REDIRECIONA PARA A INDEX
        echo "<script>location.href='/';</script>";
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

