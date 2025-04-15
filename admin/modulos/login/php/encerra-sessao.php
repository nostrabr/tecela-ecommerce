<?php

//INICIA A SESSÃO
session_start();

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
header('location: index.php');