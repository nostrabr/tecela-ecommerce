<?php

session_start();

$id_produto  = trim(strip_tags(filter_input(INPUT_POST, "id-produto", FILTER_SANITIZE_NUMBER_INT)));  
$visitante   = filter_var($_SESSION['visitante']);

if(!empty($id_produto) & mb_strlen($visitante) == 32){

    include_once '../../../bd/conecta.php';

    mysqli_query($conn, "INSERT INTO visita (tipo, id_produto, visitante) VALUES ('PRODUTO','$id_produto','$visitante')");      
    mysqli_query($conn, "UPDATE produto SET relevancia = relevancia + 1 WHERE id = '$id_produto'");     
       
    include_once '../../../bd/desconecta.php';    

}