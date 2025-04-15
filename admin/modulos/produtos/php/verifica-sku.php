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
        $sku  = trim(strip_tags(filter_input(INPUT_POST, "sku", FILTER_SANITIZE_STRING))); 
        $tela = trim(strip_tags(filter_input(INPUT_POST, "tela", FILTER_SANITIZE_STRING))); 

        if(!empty($sku) & !empty($tela)){

            include_once '../../../../bd/conecta.php';

            //BUSCA DADOS DO PRODUTO
            if($tela == 'E'){
                $identificador = trim(strip_tags(filter_input(INPUT_POST, "identificador", FILTER_SANITIZE_STRING))); 
                $busca_produto = mysqli_query($conn, "SELECT id FROM produto WHERE sku = '$sku' AND identificador != '$identificador'");
            } else {
                $busca_produto = mysqli_query($conn, "SELECT id FROM produto WHERE sku = '$sku'");
            }

            if(mysqli_num_rows($busca_produto) > 0){    
                echo "NOT-OK";
            } else {
                echo "OK";
            }

            include_once '../../../../bd/desconecta.php';
                
            
        } else {
            
            echo "SESSAO INVALIDA";
                
        }
        
    }
    
} else {
    
    //VERIFICA SE VEIO DO AJAX
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

        echo "SESSAO INVALIDA";

    } else {

        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";

    }
        
}