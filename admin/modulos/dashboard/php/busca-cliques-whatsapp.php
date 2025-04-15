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

        session_write_close();
        
        $data_inicio = filter_input(INPUT_POST, 'data-inicio', FILTER_SANITIZE_STRING);  
        $data_fim    = filter_input(INPUT_POST, 'data-fim', FILTER_SANITIZE_STRING);  

        if(mb_strlen($data_inicio) == 10 & mb_strlen($data_fim) == 10){

            include_once '../../../../bd/conecta.php';

            $busca_whatsapp_flutuante = mysqli_query($conn, "
                SELECT COUNT(id) AS total
                FROM visita
                WHERE tipo = 'WHATSAPP-FLUTUANTE' AND data_cadastro BETWEEN '$data_inicio 00:00:00' AND '$data_fim 23:59:59'
            ");
            $whatsapp_flutuante = mysqli_fetch_array($busca_whatsapp_flutuante);

            $busca_whatsapp_contato = mysqli_query($conn, "
                SELECT COUNT(id) AS total
                FROM visita
                WHERE tipo = 'WHATSAPP-CONTATO' AND data_cadastro BETWEEN '$data_inicio 00:00:00' AND '$data_fim 23:59:59'
            ");
            $whatsapp_contato = mysqli_fetch_array($busca_whatsapp_contato);

            $busca_whatsapp_rodape = mysqli_query($conn, "
                SELECT COUNT(id) AS total
                FROM visita
                WHERE tipo = 'WHATSAPP-FOOTER' AND data_cadastro BETWEEN '$data_inicio 00:00:00' AND '$data_fim 23:59:59'
            ");
            $whatsapp_rodape = mysqli_fetch_array($busca_whatsapp_rodape);

            $array_whatsapp = [];

            $array_whatsapp[] = array(
                "flutuante" => $whatsapp_flutuante['total'],
                "contato"   => $whatsapp_contato['total'],
                "rodape"    => $whatsapp_rodape['total'],
                "total"     => $whatsapp_flutuante['total']+$whatsapp_contato['total']+$whatsapp_rodape['total']
            );

            echo json_encode($array_whatsapp);
                
            include_once '../../../../bd/desconecta.php';

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
