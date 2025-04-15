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
        $identificador_atributo = trim(strip_tags(filter_input(INPUT_POST, "identificador", FILTER_SANITIZE_STRING)));

        //CONFIRMA SE VEIO TUDO PREENCHIDO
        if(!empty($identificador_atributo)){
            
            include_once '../../../../bd/conecta.php';

            //BUSCA DADOS DA CARACTERISTICA
            $busca_atributo = mysqli_query($conn, "SELECT id, visualizacao FROM atributo WHERE identificador = '$identificador_atributo'");

            if(mysqli_num_rows($busca_atributo) > 0){

                $atributo = mysqli_fetch_array($busca_atributo);

                if($atributo['visualizacao'] ==  'T'){
                    
                    $caracteristicas = mysqli_query($conn, "SELECT textura FROM caracteristica WHERE id_atributo = ".$atributo['id']);
                    while($caracteristica = mysqli_fetch_array($caracteristicas)){
                        unlink("../../../../imagens/texturas/".$caracteristica['textura']);
                    }
                        
                }

                mysqli_query($conn, "UPDATE atributo SET status = 0 WHERE identificador = '$identificador_atributo'"); 
                mysqli_query($conn, "UPDATE caracteristica SET status = 0 WHERE id_atributo = '".$atributo['id']."'"); 
                mysqli_query($conn, "UPDATE produto_caracteristica SET status = 0 WHERE id_atributo = '".$atributo['id'] ."'"); 

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
