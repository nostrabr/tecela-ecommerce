<?php

//INICIA A SESSÃO
session_start();

//RECEBE OS DADOS
$retorno = trim(strip_tags(filter_input(INPUT_POST, "retorno", FILTER_SANITIZE_STRING)));
unset($_SESSION['RETORNO-FRETE']); 

if(!empty($retorno)){

    include_once '../../../bd/conecta.php';

    //SETA A SESSION DE RETORNO DO CADASTRO
    $_SESSION['RETORNO-FRETE'] = $retorno;

    include_once '../../../bd/desconecta.php';
    
} else {    
        
    //REDIRECIONA PARA A TELA DE LOGIN
    echo "<script>location.href='/';</script>";

}
