<?php

//INICIA A SESSÃO
session_start();

if(isset($_SESSION["DONO"])){

    //GERA O TOKEN
    $token_usuario = md5('a5db19398b1f6d6ccb96c410b7c93755'.$_SERVER['REMOTE_ADDR']);
    
    //SE FOR DIFERENTE
    if($_SESSION["DONO"] !== $token_usuario){
    
        //LIMPA TODAS AS VARIÁVEIS DA SESSÃO
        $_SESSION = array();

        //APAGA OS COOKIES DA SESSÃO
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        //DESTROI A SESSÃO
        session_destroy();

        //REDIRECIONA PARA A TELA DE LOGIN
        header('location:login.php');
        
    }
    
} else {

    //LIMPA TODAS AS VARIÁVEIS DA SESSÃO
    $_SESSION = array();

    //APAGA OS COOKIES DA SESSÃO
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    //DESTROI A SESSÃO
    session_destroy();

    //REDIRECIONA PARA A TELA DE LOGIN
    header('location:login.php');

}
