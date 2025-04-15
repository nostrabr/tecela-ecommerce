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

        $identificador = trim(strip_tags(filter_input(INPUT_POST, "identificador", FILTER_SANITIZE_STRING)));
        $nivel_usuario = filter_var($_SESSION['nivel']);

        if(!empty($identificador) & mb_strlen($identificador) == 32 & $nivel_usuario != 'U'){

            include_once '../../../../bd/conecta.php';

            $busca_pagina_customizada = mysqli_query($conn, "SELECT status FROM pagina_customizada WHERE identificador = '".$identificador."'");
            $pagina_customizada       = mysqli_fetch_array($busca_pagina_customizada);

            if ($pagina_customizada["status"] == 1) {
                $sql = mysqli_query($conn, "UPDATE pagina_customizada SET status = 0 WHERE identificador = '".$identificador."'");
            } else {
                $sql = mysqli_query($conn, "UPDATE pagina_customizada SET status = 1 WHERE identificador = '".$identificador."'");
            }

            //VALIDA SQL
            if (!$sql) {
                echo 'ERRO BANCO';
            } else {
                echo 'OK';
            } 

            include_once '../../../../bd/desconecta.php';
        
        } else {
            
            //REDIRECIONA PARA A TELA DE LOGIN
            echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";
            
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
