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
        $codigo = trim(filter_input(INPUT_POST, "codigo",FILTER_SANITIZE_STRING));  

        //CONFIRMA SE VEIO TUDO PREENCHIDO E SE NÃO É USUÁRIO
        if(!empty($codigo)){
            
            include_once '../../../../bd/conecta.php';    
                  
            $busca_frete_pacote = mysqli_query($conn, "
                SELECT pfp.melhor_envio_id_etiqueta 
                FROM pedido_frete_pacote AS pfp
                LEFT JOIN pedido_frete AS pf ON pf.id = pfp.id_pedido_frete
                LEFT JOIN pedido AS p ON p.id = pf.id_pedido
                WHERE p.codigo = '$codigo'
            ");

            while($frete_pacote = mysqli_fetch_array($busca_frete_pacote)){                
                $order_id = $frete_pacote['melhor_envio_id_etiqueta'];              
                include '../../frete/melhor-envio/remover-item-carrinho.php';   
                mysqli_query($conn, "UPDATE pedido_frete_pacote SET melhor_envio_id_etiqueta = NULL, melhor_envio_codigo_envio = NULL, status = 0 WHERE melhor_envio_id_etiqueta = '$order_id'");
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
