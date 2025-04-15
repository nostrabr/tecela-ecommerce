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
        $identificador_cliente         = trim(strip_tags(filter_input(INPUT_POST, "identificador", FILTER_SANITIZE_STRING)));
        $assunto                       = trim(strip_tags(filter_input(INPUT_POST, "assunto", FILTER_SANITIZE_STRING)));
        $corpo_email                   = filter_input(INPUT_POST, "summernote");      
        $identificador_usuario_session = filter_var($_SESSION['identificador']);    
        $nivel_usuario                 = filter_var($_SESSION['nivel']);
        unset($_SESSION['RETORNO']); 
        
        //CONFIRMA SE VEIO TUDO PREENCHIDO E NÃO É NÍVEL USUÁRIO
        if(!empty($identificador_cliente) & !empty($corpo_email) & !empty($assunto) & $nivel_usuario != 'U'){
            
            include '../../../../bd/conecta.php'; 

            //BUSCA OS DADOS DO CLIENTE
            $busca_cliente = mysqli_query($conn, "SELECT id, nome, email FROM cliente WHERE identificador = '$identificador_cliente'");
            $cliente       = mysqli_fetch_array($busca_cliente);
            $email_envio   = $cliente['email'];

            include_once '../../envio-email/index.php';    

            if($status_envio == 'EMAIL-ENVIADO'){         
                
                //GERA UM CÓDIGO IDENTIFICADOR
                $identificador_email = md5(date('Y-m-d H:i:s').$assunto.$email_envio);
                
                //INSERE NA LISTA DE E-MAIL ENVIADOS
                mysqli_query($conn, "INSERT INTO email (identificador, id_cliente, email, assunto, corpo_email, enviado_por) VALUES ('$identificador_email','".$cliente['id']."','$email_envio','$assunto','$corpo_email','$identificador_usuario_session')");

                //PREENCHE A SESSION DE RETORNO COM ERRO
                $_SESSION['RETORNO'] = array(
                    'ERRO' => ''
                );  

                include '../../../../bd/desconecta.php';    

                //REDIRECIONA PARA A TELA DE USUÁRIOS
                echo "<script>location.href='../../../clientes-email.php?id=".$identificador_cliente."';</script>";

            } else if($status_envio == 'ERRO-ENVIO-EMAIL'){                            

                //PREENCHE A SESSION DE RETORNO COM ERRO
                $_SESSION['RETORNO'] = array(
                    'ERRO' => 'ERRO-EMAIL'
                );  

                include '../../../../bd/desconecta.php';    

                echo "<script>location.href='../../../clientes-email.php?id=".$identificador_cliente."';</script>";

            }
        
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
