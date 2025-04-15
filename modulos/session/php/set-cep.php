<?php

//INICIA A SESSÃO
session_start();

//RECEBE OS DADOS
$cep = trim(strip_tags(filter_input(INPUT_POST, "cep", FILTER_SANITIZE_STRING)));
unset($_SESSION['CEP']); 

if(!empty($cep)){

    include_once '../../../bd/conecta.php';

    //SETA A SESSION CEP COM O VALOR INFORMADO
    $_SESSION['CEP'] = $cep;

    include_once '../../../bd/desconecta.php';
    
} else {    
        
    //REDIRECIONA PARA A TELA DE LOGIN
    echo "<script>location.href='/';</script>";

}
