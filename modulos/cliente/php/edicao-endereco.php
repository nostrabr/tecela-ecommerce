<?php

//INICIA A SESSÃO
session_start();

//RECEBE OS DADOS DO CADASTRO
$identificador_endereco = trim(strip_tags(filter_input(INPUT_POST, "identificador", FILTER_SANITIZE_STRING)));  
$nome                   = trim(strip_tags(mb_convert_case(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING), MB_CASE_TITLE, 'UTF-8')));
$cep                    = trim(strip_tags(filter_input(INPUT_POST, "cep", FILTER_SANITIZE_STRING)));  
$logradouro             = trim(strip_tags(mb_convert_case(filter_input(INPUT_POST, "rua", FILTER_SANITIZE_STRING), MB_CASE_TITLE, 'UTF-8')));  
$numero                 = trim(strip_tags(mb_convert_case(filter_input(INPUT_POST, "numero", FILTER_SANITIZE_STRING), MB_CASE_TITLE, 'UTF-8')));  
$bairro                 = trim(strip_tags(mb_convert_case(filter_input(INPUT_POST, "bairro", FILTER_SANITIZE_STRING), MB_CASE_TITLE, 'UTF-8')));  
$complemento            = trim(strip_tags(mb_convert_case(filter_input(INPUT_POST, "complemento", FILTER_SANITIZE_STRING), MB_CASE_TITLE, 'UTF-8')));  
$cidade                 = trim(strip_tags(filter_input(INPUT_POST, "cidade", FILTER_SANITIZE_NUMBER_INT)));  
$estado                 = trim(strip_tags(filter_input(INPUT_POST, "estado", FILTER_SANITIZE_NUMBER_INT)));  
$referencia             = trim(strip_tags(mb_convert_case(filter_input(INPUT_POST, "referencia", FILTER_SANITIZE_STRING), MB_CASE_TITLE, 'UTF-8')));  
$identificador_cliente  = filter_var($_SESSION['identificador']);
unset($_SESSION['RETORNO']); 

if(!empty($identificador_endereco) & !empty($nome) & !empty($cep) & !empty($logradouro) & !empty($numero) & !empty($bairro) & !empty($cidade) & !empty($estado) & !empty($identificador_cliente)){

    include_once '../../../bd/conecta.php';

    //VALIDA O CLIENTE
    $busca_cliente = mysqli_query($conn, "SELECT id FROM cliente WHERE identificador = '$identificador_cliente'");

    //SE ESTIVER CERTO, PROSSEGUE
    if(mysqli_num_rows($busca_cliente) > 0){

        //ALTERA O ENDEREÇO DO CLIENTE
        mysqli_query($conn, "UPDATE cliente_endereco SET nome = '$nome', cep = '$cep', logradouro = '$logradouro', numero = '$numero', complemento = '$complemento', bairro = '$bairro', cidade = '$cidade', estado = '$estado', referencia = '$referencia' WHERE identificador = '$identificador_endereco'");
      
        //VERIFICA SE CADASTROU CORRETAMENTE
        if(mysqli_error($conn)){ 

            //PREENCHE A SESSION DE RETORNO COM ERRO
            $_SESSION['RETORNO'] = array(
                'ERRO'   => 'ERRO-EDICAO',
                'STATUS' => 'ERRO'
            );  

        } else {            

            //PREENCHE A SESSION DE RETORNO COM SUCESSO
            $_SESSION['RETORNO'] = array(
                'ERRO'   => false,
                'STATUS' => 'SUCESSO-EDICAO'
            );  

        }             
    
        //SE ESTÁ SETADA A SESSION DE RETORNO DO FRETE RETORNA PARA A TELA DO FRETE DO CARRINHO, SENÃO PROS ENDEREÇOS
        if(isset($_SESSION["RETORNO-FRETE"])){
            $retorno_frete = $_SESSION["RETORNO-FRETE"];
            unset($_SESSION["RETORNO-FRETE"]);
            echo "<script>location.href='../../../".$retorno_frete."';</script>";
        } else {
            echo "<script>location.href='../../../cliente-enderecos';</script>";
        }

        
    //SE ESTIVER ERRADO, REDIRECIONA PARA O LOGOUT
    } else {
    
        echo "<script>location.href='../../../logout';</script>";

    }

    include_once '../../../bd/desconecta.php';
    
} else {    
        
    //REDIRECIONA PARA A TELA DE LOGIN
    echo "<script>location.href='/';</script>";

}
