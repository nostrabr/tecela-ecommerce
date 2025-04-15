<?php

//INICIA A SESSION
session_start();

include_once '../bd/conecta.php';

$filial = trim(strip_tags(filter_input(INPUT_POST, "filial", FILTER_SANITIZE_STRING)));

if(!empty($filial)){

    $identificador_carrinho = filter_var($_SESSION['visitante']);

    mysqli_query($conn, "INSERT INTO pedido_filial (filial,carrinho) VALUES ('$filial','$identificador_carrinho')");

}

include_once '../bd/desconecta.php';