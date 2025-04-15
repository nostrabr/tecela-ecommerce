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
            $dados[] = array(
                "status" => "SESSAO INVALIDA"
            );
            echo json_encode($dados);
            
        } else {
            
            //REDIRECIONA PARA A TELA DE LOGIN
            echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";
            
        }

    } else {

        $nivel_usuario = filter_var($_SESSION['nivel']);

        if($nivel_usuario != ''){

            include_once '../../../../bd/conecta.php';

            $busca_atributos = mysqli_query($conn, "SELECT id, nome FROM atributo WHERE status = 1");
            
            while($atributo = mysqli_fetch_array($busca_atributos)){
                $dados[] = array(
                    "status"       => "OK",
                    "id"           => $atributo['id'],
                    "nome"         => $atributo['nome']
                );
            }                      
            
            echo json_encode($dados);

            include_once '../../../../bd/desconecta.php';
        
        } else {
            
            //VERIFICA SE VEIO DO AJAX
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                
                //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
                $dados[] = array(
                    "status" => "SESSAO INVALIDA"
                );
                echo json_encode($dados);
                
            } else {
                
                //REDIRECIONA PARA A TELA DE LOGIN
                echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";
                
            }
            
        }

    }
    
} else {
    
    //VERIFICA SE VEIO DO AJAX
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

        $dados[] = array(
            "status" => "SESSAO INVALIDA"
        );
        echo json_encode($dados);

    } else {

        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";

    }
        
}
