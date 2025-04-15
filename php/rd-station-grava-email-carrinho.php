<?php 

session_start();

$session_visitante = filter_var($_SESSION['visitante']);
$email             = filter_input(INPUT_POST,'email');

if(mb_strlen($session_visitante) == 32 & !empty($email)){
    
    //CONECTA AO BANCO
    include_once '../bd/conecta.php';

    mysqli_query($conn, "UPDATE carrinho SET email_cliente = '$email' WHERE identificador = '$session_visitante'");

    //DESCONECTA DO BANCO
    include_once '../bd/desconecta.php';    
        
}