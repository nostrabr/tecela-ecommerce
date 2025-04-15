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
        $nivel_usuario = filter_var($_SESSION['nivel']);
        
        //CONFIRMA SE VEIO TUDO PREENCHIDO
        if($nivel_usuario == 'S'){           

            include_once '../../../../bd/conecta.php';

            //UPDATE REGISTRO
            mysqli_query($conn, "
                UPDATE frete SET 
                melhor_envio                    = 0, 
                melhor_envio_redirect_uri       = '', 
                melhor_envio_nome_aplicacao     = '', 
                melhor_envio_email_aplicacao    = '', 
                melhor_envio_client_id          = '', 
                melhor_envio_client_secret      = '',
                melhor_envio_coleta             = '', 
                melhor_envio_aviso_recebimento  = '', 
                melhor_envio_maos_proprias      = '', 
                melhor_envio_servicos           = '', 
                melhor_envio_token              = '', 
                melhor_envio_refresh_token      = ''
                WHERE id = 1
            ");

            mysqli_query($conn, "TRUNCATE TABLE frete_transportadora");
            mysqli_query($conn, "TRUNCATE TABLE frete_transportadora_servico");


            include_once '../../../../bd/desconecta.php';
            
            echo "OK";

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
