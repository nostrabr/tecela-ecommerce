<?php

//RECEBE OS DADOS DO FORM
$order = trim(filter_input(INPUT_POST, "etiqueta", FILTER_SANITIZE_STRING));  

//CONFIRMA SE VEIO TUDO PREENCHIDO 
if(!empty($order)){
    
    include_once '../../../bd/conecta.php';
    include '../../../admin/modulos/frete/melhor-envio/rastrear-etiqueta.php';              
    include_once '../../../bd/desconecta.php';

} else {

    //REDIRECIONA PARA A TELA DE LOGIN
    echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";
        
}