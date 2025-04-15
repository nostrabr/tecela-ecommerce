<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

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
                SELECT SUM(pp.valor_produtos) AS total_produtos, SUM(pp.valor_desconto) AS total_desconto, SUM(pp.valor_juros) AS total_juros, SUM(pp.valor_frete) AS total_frete
                FROM pedido AS p
                LEFT JOIN pagamento_pagseguro AS pp ON p.id = pp.id_pedido
                WHERE p.data_cadastro BETWEEN '$data_inicio 00:00:00' AND '$data_fim 23:59:59' AND (p.status = 3 OR p.status = 4)
            ");
            $pedidos = mysqli_fetch_array($busca_pedidos);

            $total = $pedidos['total_produtos'] + $pedidos['total_juros'] + $pedidos['total_frete'] - $pedidos['total_desconto'];

            echo 'R$ '.number_format($total,2,',','.');
                
            include_once '../../../../bd/desconecta.php';

        } else {
            echo $data_inicio." - ".$data_fim;
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
