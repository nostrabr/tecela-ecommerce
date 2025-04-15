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
        $email                 = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));
        $senha                 = trim(strip_tags(filter_input(INPUT_POST, "senha", FILTER_SANITIZE_STRING)));
        $host                  = trim(strip_tags(filter_input(INPUT_POST, "host", FILTER_SANITIZE_STRING)));
        $porta                 = trim(strip_tags(filter_input(INPUT_POST, "porta", FILTER_SANITIZE_STRING)));
        $issmtp                = trim(strip_tags(filter_input(INPUT_POST, "issmtp")));
        $email_adicional       = trim(strip_tags(filter_input(INPUT_POST, "email-adicional", FILTER_SANITIZE_STRING)));
        $cabecalho             = filter_input(INPUT_POST, "summernote");
        $contato               = filter_input(INPUT_POST, "summernote2");
        $cadastro_cliente      = filter_input(INPUT_POST, "summernote3");
        $pedido_boleto         = filter_input(INPUT_POST, "summernote4");
        $pedido_cartao         = filter_input(INPUT_POST, "summernote5");
        $pedido_orcamento      = filter_input(INPUT_POST, "summernote6");
        $confirmacao_pagamento = filter_input(INPUT_POST, "summernote7");
        $rodape                = filter_input(INPUT_POST, "summernote8");
        $pedido_pix            = filter_input(INPUT_POST, "summernote9");
        $pedido_enviado        = filter_input(INPUT_POST, "summernote10");
        $confirmacao_retirada  = filter_input(INPUT_POST, "summernote12");
        
        //CONFIRMA SE VEIO TUDO PREENCHIDO
        if(!empty($email) & !empty($senha) & !empty($host) & !empty($porta)){

            include_once '../../../../bd/conecta.php';

            //VERIFICA SE É OU NÃO SMTP
            if($issmtp == 'on'){
                $issmtp = 1;
            } else {
                $issmtp = 0;
            }

            //UPDATE REGISTRO
            mysqli_query($conn, "
                UPDATE loja SET 
                email_sistema = '$email', 
                email_sistema_senha = '$senha', 
                email_sistema_host = '$host', 
                email_sistema_porta = '$porta', 
                email_issmtp = '$issmtp', 
                email_adicional = '$email_adicional', 
                email_cabecalho = '$cabecalho', 
                email_contato = '$contato', 
                email_cadastro_cliente = '$cadastro_cliente', 
                email_pedido_boleto = '$pedido_boleto', 
                email_pedido_cartao = '$pedido_cartao', 
                email_pedido_pix = '$pedido_pix', 
                email_pedido_orcamento = '$pedido_orcamento', 
                email_pedido_confirmacao = '$confirmacao_pagamento', 
                email_pedido_confirmacao_retirada = '$confirmacao_retirada',
                email_pedido_enviado = '$pedido_enviado', 
                email_rodape = '$rodape' 
                WHERE id = 1
            ");

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
