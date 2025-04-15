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
        $home_titulo                     = trim(strip_tags(filter_input(INPUT_POST, "home-titulo", FILTER_SANITIZE_STRING)));
        $home_descricao                  = trim(strip_tags(filter_input(INPUT_POST, "home-descricao", FILTER_SANITIZE_STRING)));
        $home_palavras_chave             = trim(strip_tags(filter_input(INPUT_POST, "home-palavras-chave", FILTER_SANITIZE_STRING)));
        $produtos_titulo                 = trim(strip_tags(filter_input(INPUT_POST, "produtos-titulo", FILTER_SANITIZE_STRING)));
        $produtos_descricao              = trim(strip_tags(filter_input(INPUT_POST, "produtos-descricao", FILTER_SANITIZE_STRING)));
        $produtos_palavras_chave         = trim(strip_tags(filter_input(INPUT_POST, "produtos-palavras-chave", FILTER_SANITIZE_STRING)));
        $contato_titulo                  = trim(strip_tags(filter_input(INPUT_POST, "contato-titulo", FILTER_SANITIZE_STRING)));
        $contato_descricao               = trim(strip_tags(filter_input(INPUT_POST, "contato-descricao", FILTER_SANITIZE_STRING)));
        $contato_palavras_chave          = trim(strip_tags(filter_input(INPUT_POST, "contato-palavras-chave", FILTER_SANITIZE_STRING)));
        $localizacao_titulo              = trim(strip_tags(filter_input(INPUT_POST, "localizacao-titulo", FILTER_SANITIZE_STRING)));
        $localizacao_descricao           = trim(strip_tags(filter_input(INPUT_POST, "localizacao-descricao", FILTER_SANITIZE_STRING)));
        $localizacao_palavras_chave      = trim(strip_tags(filter_input(INPUT_POST, "localizacao-palavras-chave", FILTER_SANITIZE_STRING)));
        $sobre_titulo                    = trim(strip_tags(filter_input(INPUT_POST, "sobre-titulo", FILTER_SANITIZE_STRING)));
        $sobre_descricao                 = trim(strip_tags(filter_input(INPUT_POST, "sobre-descricao", FILTER_SANITIZE_STRING)));
        $sobre_palavras_chave            = trim(strip_tags(filter_input(INPUT_POST, "sobre-palavras-chave", FILTER_SANITIZE_STRING)));
        $cadastro_cliente_titulo         = trim(strip_tags(filter_input(INPUT_POST, "cadastro-cliente-titulo", FILTER_SANITIZE_STRING)));
        $cadastro_cliente_descricao      = trim(strip_tags(filter_input(INPUT_POST, "cadastro-cliente-descricao", FILTER_SANITIZE_STRING)));
        $cadastro_cliente_palavras_chave = trim(strip_tags(filter_input(INPUT_POST, "cadastro-cliente-palavras-chave", FILTER_SANITIZE_STRING)));
        $nivel_usuario                   = filter_var($_SESSION['nivel']);

        //CONFIRMA SE VEIO TUDO PREENCHIDO
        if($nivel_usuario != 'U'){

            include_once '../../../../bd/conecta.php';

            //UPDATE HOME
            mysqli_query($conn, "UPDATE seo SET titulo = '$home_titulo', descricao = '$home_descricao', palavras_chave = '$home_palavras_chave' WHERE id = 1");
            
            //UPDATE HOME
            mysqli_query($conn, "UPDATE seo SET titulo = '$produtos_titulo', descricao = '$produtos_descricao', palavras_chave = '$produtos_palavras_chave' WHERE id = 2");
            
            //UPDATE HOME
            mysqli_query($conn, "UPDATE seo SET titulo = '$contato_titulo', descricao = '$contato_descricao', palavras_chave = '$contato_palavras_chave' WHERE id = 3");
            
            //UPDATE HOME
            mysqli_query($conn, "UPDATE seo SET titulo = '$localizacao_titulo', descricao = '$localizacao_descricao', palavras_chave = '$localizacao_palavras_chave' WHERE id = 4");
            
            //UPDATE HOME
            mysqli_query($conn, "UPDATE seo SET titulo = '$sobre_titulo', descricao = '$sobre_descricao', palavras_chave = '$sobre_palavras_chave' WHERE id = 5");
            
            //UPDATE HOME
            mysqli_query($conn, "UPDATE seo SET titulo = '$cadastro_cliente_titulo', descricao = '$cadastro_cliente_descricao', palavras_chave = '$cadastro_cliente_palavras_chave' WHERE id = 6");

            include_once '../../../../bd/desconecta.php';

            //REDIRECIONA PARA A TELA DE USUÁRIOS
            echo "<script>location.href='../../../configuracoes.php';</script>";
        
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
