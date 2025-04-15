<?php

session_start();

$visitante = filter_var($_SESSION['visitante']);

if(mb_strlen($visitante) == 32){
   
    include_once '../../../bd/conecta.php';
       
    mysqli_query($conn, "INSERT INTO visita (tipo, visitante) VALUES ('WHATSAPP-FLUTUANTE','$visitante')");
       
    include_once '../../../bd/desconecta.php';    

}