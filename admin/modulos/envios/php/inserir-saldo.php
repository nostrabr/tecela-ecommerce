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
        $valor_creditar = trim(filter_input(INPUT_POST, "valor", FILTER_SANITIZE_STRING));  
        $gateway        = trim(filter_input(INPUT_POST, "gateway", FILTER_SANITIZE_STRING));  
        $senha          = trim(filter_input(INPUT_POST, "senha", FILTER_SANITIZE_STRING));  

        //CONFIRMA SE VEIO TUDO PREENCHIDO E SE NÃO É USUÁRIO
        if(!empty($valor_creditar) & !empty($gateway)){

            $valor_creditar = str_replace(".", "", $valor_creditar);
            $valor_creditar = str_replace(",", ".", $valor_creditar);
            $valor_creditar = number_format($valor_creditar, 2, '.', '');
            
            include_once '../../../../bd/conecta.php';         
                        
            //CRIPTOGRAFA A SENHA
            $senha_usuario = md5($senha);
            $identificador_usuario = $_SESSION['identificador'];

            $valida_usuario = mysqli_query($conn, "SELECT id FROM usuario WHERE identificador = '$identificador_usuario' AND senha = '$senha_usuario'");

            if(mysqli_num_rows($valida_usuario) > 0){
            
                include '../../frete/melhor-envio/inserir-saldo.php';   

                $json = json_decode($response, true);

                if(isset($json["error"])){

                    $_SESSION['RETORNO'] = array(
                        'ERRO'    => true,
                        'status'  => $json["error"]
                    );
    
                    echo "<script>location.href='../../../envios.php';</script>";

                } else {
                    header('Location:'.$json["redirect"]);
                }                

            } else {

                //SENHA INVÁLIDA
                //PREENCHE A SESSION DE RETORNO COM ERRO
                $_SESSION['RETORNO'] = array(
                    'ERRO'    => true,
                    'status'  => 'Senha inválida.'
                );

                echo "<script>location.href='../../../envios.php';</script>";

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
