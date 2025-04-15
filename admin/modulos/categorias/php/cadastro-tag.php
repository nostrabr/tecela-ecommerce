<?php

//INICIA A SESSÃO
session_start();

//VALIDA A SESSÃO
if(isset($_SESSION["DONO"])){
    
    //GERA O TOKEN
    $token_usuario = md5('18f80a949b97de988368995777c5aaea'.$_SERVER['REMOTE_ADDR']);
    
    //SE FOR DIFERENTE
    if($_SESSION["DONO"] !== $token_usuario){

        //VERIFICA SE VEIO DO AJAX
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            
            //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
            echo "SESSAO INVALIDA";
            
        } else {
            
            //REDIRECIONA PARA A TELA DE LOGIN
            echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";
            
        }

    } else {

        //RECEBE OS DADOS DO FORM
        $nome          = trim(strip_tags(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING)));        
        $nivel_usuario = filter_var($_SESSION['nivel']);

        //CONFIRMA SE VEIO TUDO PREENCHIDO E SE NÃO É USUÁRIO
        if(!empty($nome)){

            include_once '../../../../bd/conecta.php';

            //GERA O CÓDIGO IDENTIFICADOR
            $identificador = md5(date('Y-m-d H:i:s').$nome);

            //INSERT NO BANCO
            mysqli_query($conn, "INSERT INTO tag (identificador, nome) VALUES ('$identificador','$nome')");

            echo "OK";

            include_once '../../../../bd/desconecta.php';
                
        } else {
    
            //VERIFICA SE VEIO DO AJAX
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        
                //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
                echo "SESSAO INVALIDA";
        
            } else {
        
                //REDIRECIONA PARA A TELA DE LOGIN
                echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";
        
            }
                
        }

    }
    
} else {
    
    //VERIFICA SE VEIO DO AJAX
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

        //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
        echo "SESSAO INVALIDA";

    } else {

        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";

    }
        
}
