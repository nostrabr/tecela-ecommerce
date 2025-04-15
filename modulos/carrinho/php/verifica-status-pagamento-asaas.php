<?php

//INICIA A SESSÃO
session_start();

$identificador = trim(strip_tags(filter_input(INPUT_POST, "id")));    
$visitante     = filter_var($_SESSION['visitante']);

if(mb_strlen($identificador) == 32 & mb_strlen($visitante) == 32){

    include_once '../../../bd/conecta.php';

    $busca_pedido = mysqli_query($conn, "SELECT status FROM pedido WHERE identificador = '$identificador'");
    $pedido       = mysqli_fetch_array($busca_pedido);

    include_once '../../../bd/desconecta.php';
    
    if($pedido['status'] == 3 | $pedido['status'] == 4){
        echo "CONFIRMADO";
    } else {
        echo "PENDENTE";
    }
    
} else {

    //VERIFICA SE VEIO DO AJAX
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                
        //RETORNA PRO AJAX
        echo "ERRO";
        
    } else {
        
        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='/';</script>";
        
    }

}