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
        $comercial             = nl2br(trim(strip_tags(filter_input(INPUT_POST, "comercial", FILTER_SANITIZE_STRING))));
        $entrega               = nl2br(trim(strip_tags(filter_input(INPUT_POST, "entrega", FILTER_SANITIZE_STRING))));
        $troca_devolucao       = nl2br(trim(strip_tags(filter_input(INPUT_POST, "troca-devolucao", FILTER_SANITIZE_STRING))));
        $privacidade_seguranca = nl2br(trim(strip_tags(filter_input(INPUT_POST, "privacidade-seguranca", FILTER_SANITIZE_STRING))));
        $termos_uso            = nl2br(trim(strip_tags(filter_input(INPUT_POST, "termos-uso", FILTER_SANITIZE_STRING))));

        include_once '../../../../bd/conecta.php';

        //UPDATE REGISTRO
        mysqli_query($conn, "UPDATE politicas SET comercial = '$comercial', entrega = '$entrega', troca_devolucao = '$troca_devolucao', privacidade_seguranca = '$privacidade_seguranca', termos_uso = '$termos_uso' WHERE id = 1");

        include_once '../../../../bd/desconecta.php';

        //REDIRECIONA PARA A TELA DE USUÁRIOS
        echo "<script>location.href='../../../configuracoes.php';</script>";

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
