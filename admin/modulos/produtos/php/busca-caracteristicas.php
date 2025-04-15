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

        $id_atributo = trim(strip_tags(filter_input(INPUT_POST, "atributo", FILTER_SANITIZE_NUMBER_INT)));   
        $nivel_usuario = filter_var($_SESSION['nivel']);

        if(!empty($id_atributo)){

            include_once '../../../../bd/conecta.php';

            $busca_caracteristicas = mysqli_query($conn, "SELECT id, nome FROM caracteristica WHERE id_atributo = '$id_atributo' AND status = 1 ORDER BY nome ASC");
            
            while($caracteristica = mysqli_fetch_array($busca_caracteristicas)){
                $dados[] = array(
                    "status"       => "OK",
                    "id"           => $caracteristica['id'],
                    "nome"         => $caracteristica['nome']
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
