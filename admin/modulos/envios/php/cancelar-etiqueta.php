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
        $codigo                 = trim(filter_input(INPUT_POST, "codigo-pedido", FILTER_SANITIZE_STRING));  
        $descricao_cancelamento = trim(filter_input(INPUT_POST, "descricao-cancelamento", FILTER_SANITIZE_STRING));  
        $senha                  = trim(filter_input(INPUT_POST, "senha", FILTER_SANITIZE_STRING));  
        unset($_SESSION['RETORNO']); 
        
        //CONFIRMA SE VEIO TUDO PREENCHIDO E SE NÃO É USUÁRIO
        if(!empty($codigo) & !empty($descricao_cancelamento) & !empty($senha)){
            
            include_once '../../../../bd/conecta.php';         
                        
            //CRIPTOGRAFA A SENHA
            $senha_usuario         = md5($senha);
            $identificador_usuario = $_SESSION['identificador'];

            $valida_usuario = mysqli_query($conn, "SELECT id FROM usuario WHERE identificador = '$identificador_usuario' AND senha = '$senha_usuario'");

            if(mysqli_num_rows($valida_usuario) > 0){                
 
                $busca_frete_pacote = mysqli_query($conn, "
                    SELECT pfp.melhor_envio_id_etiqueta 
                    FROM pedido_frete_pacote AS pfp
                    LEFT JOIN pedido_frete AS pf ON pf.id = pfp.id_pedido_frete
                    LEFT JOIN pedido AS p ON p.id = pf.id_pedido
                    WHERE p.codigo = '$codigo' AND pfp.melhor_envio_id_etiqueta IS NOT NULL
                ");
                
                if(mysqli_num_rows($busca_frete_pacote) > 0){

                    while($pacote = mysqli_fetch_array($busca_frete_pacote)){
                        $order_id = $pacote['melhor_envio_id_etiqueta'];
                        include '../../frete/melhor-envio/cancelar-etiqueta.php'; 
                        mysqli_query($conn, "UPDATE pedido_frete_pacote SET melhor_envio_id_etiqueta = NULL, melhor_envio_codigo_envio = NULL, status = 0 WHERE melhor_envio_id_etiqueta = '$order_id'");
                        $_SESSION['RETORNO'] = array(
                            'ERRO'   => false,
                            'status' => 'Etiqueta cancelada com sucesso!'
                        );
                    }
                    
                } 

            } else {

                //SENHA INVÁLIDA
                //PREENCHE A SESSION DE RETORNO COM ERRO
                $_SESSION['RETORNO'] = array(
                    'ERRO'    => true,
                    'status'  => 'Senha inválida.'
                );

            }        

            echo "<script>location.href='../../../envios.php';</script>";     

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
