<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
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

        //RECEBE OS DADOS DO FORM
        $codigo = trim(filter_input(INPUT_POST, "codigo", FILTER_SANITIZE_STRING));  

        //CONFIRMA SE VEIO TUDO PREENCHIDO 
        if(!empty($codigo)){
            
            include_once '../../../../bd/conecta.php';

            $orders = array();
 
            $busca_frete_pacote = mysqli_query($conn, "
                SELECT pfp.melhor_envio_id_etiqueta 
                FROM pedido_frete_pacote AS pfp
                LEFT JOIN pedido_frete AS pf ON pf.id = pfp.id_pedido_frete
                LEFT JOIN pedido AS p ON p.id = pf.id_pedido
                WHERE p.codigo = '$codigo' AND pfp.melhor_envio_id_etiqueta IS NOT NULL
            ");
            
            if(mysqli_num_rows($busca_frete_pacote) > 0){

                while($pacote = mysqli_fetch_array($busca_frete_pacote)){
                    array_push($orders, $pacote['melhor_envio_id_etiqueta']);
                }

                include '../../frete/melhor-envio/pre-visualizar-etiqueta.php';  
                
            } else {

                $retorno = array(
                    "error" => "Este pedido ainda não foi processado pelo melhor envio. Para processá-lo é necessária a compra das etiquetas."
                );

                echo json_encode($retorno);

            }

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
