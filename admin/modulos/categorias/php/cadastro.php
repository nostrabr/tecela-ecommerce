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

            //CONSULTA A ORDEM ATUAL
            $busca_categoria = mysqli_query($conn, "SELECT MAX(ordem) AS ordem FROM categoria WHERE nivel = 1");
            $categoria       = mysqli_fetch_array($busca_categoria);

            if(mysqli_num_rows($busca_categoria) > 0){
                $ordem = intval($categoria['ordem'])+1;
            } else {
                $ordem = 1;
            }

            //INSERT NO BANCO
            mysqli_query($conn, "INSERT INTO categoria (identificador, nome, pai, nivel, ordem) VALUES ('$identificador','$nome',0,1,'$ordem')");

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
