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
        $google_tag_manager_script   = $_POST['google-tag-manager-script'];
        $google_tag_manager_noscript = $_POST['google-tag-manager-noscript'];

        if(isset($google_tag_manager_script) & isset($google_tag_manager_noscript)){

            include_once '../../../../bd/conecta.php';

            //UPDATE REGISTRO
            mysqli_query($conn, "UPDATE loja SET google_tag_manager_script = '$google_tag_manager_script', google_tag_manager_noscript = '$google_tag_manager_noscript' WHERE id = 1");

            include_once '../../../../bd/desconecta.php';

            //REDIRECIONA PARA A TELA DE USUÁRIOS
            echo "<script>location.href='../../../configuracoes.php';</script>";

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
