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
        $url_redirecionamento = trim(strip_tags(filter_input(INPUT_POST, "url-redirecionamento", FILTER_SANITIZE_URL))); 
        $nome                 = trim(strip_tags(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING)));  
        $email                = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));  
        $client_id            = trim(strip_tags(filter_input(INPUT_POST, "client-id", FILTER_SANITIZE_STRING)));  
        $client_secret        = trim(strip_tags(filter_input(INPUT_POST, "client-secret", FILTER_SANITIZE_STRING)));  
        $ambiente             = trim(strip_tags(filter_input(INPUT_POST, "ambiente", FILTER_SANITIZE_STRING)));  
        
        //CONFIRMA SE VEIO TUDO PREENCHIDO
        if(!empty($url_redirecionamento) & !empty($nome) & !empty($email) & !empty($client_id) & !empty($client_secret) & !empty($ambiente)){           

            include_once '../../../../bd/conecta.php';

            //UPDATE REGISTRO
            mysqli_query($conn, "
                UPDATE frete SET 
                melhor_envio_redirect_uri    = '$url_redirecionamento', 
                melhor_envio_nome_aplicacao  = '$nome', 
                melhor_envio_email_aplicacao = '$email', 
                melhor_envio_client_id       = '$client_id', 
                melhor_envio_client_secret   = '$client_secret', 
                melhor_envio_ambiente        = '$ambiente'
                WHERE id = 1
            ");

            include_once '../../../../bd/desconecta.php';

            //REDIRECIONA PARA A TELA DE USUÁRIOS
            if($ambiente == 'S'){
                echo "<script>location.href='https://sandbox.melhorenvio.com.br/oauth/authorize?client_id=".$client_id."&redirect_uri=".$url_redirecionamento."&response_type=code&scope=cart-read cart-write companies-read companies-write coupons-read coupons-write notifications-read orders-read products-read products-write purchases-read shipping-calculate shipping-cancel shipping-checkout shipping-companies shipping-generate shipping-preview shipping-print shipping-share shipping-tracking ecommerce-shipping transactions-read users-read users-write';</script>";
            } else if($ambiente == 'P'){
                echo "<script>location.href='https://melhorenvio.com.br/oauth/authorize?client_id=".$client_id."&redirect_uri=".$url_redirecionamento."&response_type=code&scope=cart-read cart-write companies-read companies-write coupons-read coupons-write notifications-read orders-read products-read products-write purchases-read shipping-calculate shipping-cancel shipping-checkout shipping-companies shipping-generate shipping-preview shipping-print shipping-share shipping-tracking ecommerce-shipping transactions-read users-read users-write';</script>";
            }

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
