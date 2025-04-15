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
            
            $busca_pedidos = mysqli_query($conn, "
                SELECT COUNT(p.id) AS total, ps.nome, ps.cor
                FROM pedido AS p
                LEFT JOIN pedido_status AS ps ON p.status = ps.id_status
                WHERE p.data_cadastro BETWEEN '$data_inicio 00:00:00' AND '$data_fim 23:59:59' AND p.status > 0
                GROUP BY p.status
                ORDER BY total DESC
            ");
                        
            while($pedido = mysqli_fetch_array($busca_pedidos)){
                $array_pedidos[] = array(
                    "nome"  => $pedido['nome'], 
                    "total" => intval($pedido['total']),
                    "cor"   => $pedido['cor']
                );
            } 

            echo json_encode($array_pedidos);
                
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
