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
        $identificador_caracteristica = trim(strip_tags(filter_input(INPUT_POST, "identificador", FILTER_SANITIZE_STRING)));

        //CONFIRMA SE VEIO TUDO PREENCHIDO
        if(!empty($identificador_caracteristica)){
            
            include_once '../../../../bd/conecta.php';

            //BUSCA DADOS DA CARACTERISTICA
            $busca_caracteristica = mysqli_query($conn, "SELECT id, textura FROM caracteristica WHERE identificador = '$identificador_caracteristica'");

            if(mysqli_num_rows($busca_caracteristica) > 0){

                $caracteristica = mysqli_fetch_array($busca_caracteristica);

                if($caracteristica['textura'] != null){                    
                    unlink("../../../../imagens/texturas/".$caracteristica['textura']);
                }

                mysqli_query($conn, "UPDATE caracteristica SET status = 0 WHERE identificador = '$identificador_caracteristica'"); 
                mysqli_query($conn, "UPDATE produto_caracteristica SET status = 0 WHERE id_caracteristica = '".$caracteristica['id'] ."'"); 

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
