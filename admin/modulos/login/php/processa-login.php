<?php

//DESABILITA OS ERROS
error_reporting(0);

//INICIA A SESSÃO
session_start();

//RECEBE OS DADOS DO FORM
$login = filter_input(INPUT_POST, "login", FILTER_SANITIZE_STRING);
$senha = filter_input(INPUT_POST, "senha", FILTER_SANITIZE_STRING);

//CONECTA AO BANCO
include_once '../../../../bd/conecta.php';

//CONSULTA LOGIN
$consulta_usuario   = mysqli_query($conn, "SELECT * FROM usuario WHERE login = '$login'");
$usuario            = mysqli_fetch_array($consulta_usuario);

//SE ENCONTROU O USUÁRIO, VERIFICA A SENHA
if(mysqli_num_rows($consulta_usuario) > 0){

    //SE OS DADOS ESTÃO CORRETOS E USUÁRIO ESTIVER ATIVO INICIA A SESSÃO, SENÂO LIMPA A SESSÃO
    if($usuario["senha"] === md5($senha)){
        
        if($usuario["status"] == 1){
            
            //GERA UM NOME PRA SESSION
            session_name(md5('18f80a949b97de988368995777c5aaea'.$_SERVER['REMOTE_ADDR']));
            
            $_SESSION['DONO']           = md5('18f80a949b97de988368995777c5aaea'.$_SERVER['REMOTE_ADDR']);
            $_SESSION['identificador']  = $usuario["identificador"];
            $_SESSION['nivel']          = $usuario["nivel"];
            $_SESSION['nome']           = $usuario["nome"];
            $_SESSION['plataforma']     = 'ADMIN';

            echo "OK";
            
        } else {
            
            unset($_SESSION['DONO']); 
            unset($_SESSION['identificador']);
            unset($_SESSION['nivel']);
            unset($_SESSION['nome']);
            unset($_SESSION['plataforma']); 
            echo "NOT OK";

        }
        
    } else {
        
        unset($_SESSION['DONO']); 
        unset($_SESSION['identificador']);
        unset($_SESSION['nivel']);
        unset($_SESSION['nome']);
        unset($_SESSION['plataforma']); 
        echo "NOT OK";
        
    }

} else {
    
    unset($_SESSION['DONO']); 
    unset($_SESSION['identificador']);
    unset($_SESSION['nivel']);
    unset($_SESSION['nome']);
    unset($_SESSION['plataforma']); 
    echo "NOT OK";
    
}

//DESCONECTA DO BANCO
include_once '../../../../bd/desconecta.php';

