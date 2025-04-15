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
        $identificador_categoria = trim(strip_tags(filter_input(INPUT_POST, "identificador", FILTER_SANITIZE_STRING)));        
        $nivel_usuario           = filter_var($_SESSION['nivel']);

        //CONFIRMA SE VEIO TUDO PREENCHIDO E SE NÃO É USUÁRIO
        if(!empty($identificador_categoria)){

            include_once '../../../../bd/conecta.php';

            //BUSCA OS DADOS DA CATEGORIA A SER DELETADA
            $busca_categoria = mysqli_query($conn, "SELECT id, pai FROM categoria WHERE identificador = '$identificador_categoria'");
            if(mysqli_num_rows($busca_categoria) > 0){

                $sql_delete = "identificador = '".$identificador_categoria."'";
                $categoria_pai = mysqli_fetch_array($busca_categoria);
                $id_categoria  = $categoria_pai['id'];
                $id_pai        = $categoria_pai['pai'];

                //BUSCA AS FILHAS DA CATEGORIA PARA DELETAR E TROCAR O PRODUTO PARA A CATEGORIA PAI
                $categorias = mysqli_query($conn, "SELECT id, pai FROM (SELECT * FROM categoria ORDER BY pai, id) categorias_sorted, (SELECT @pv := '$id_categoria') initialisation WHERE find_in_set(pai, @pv) AND length(@pv := concat(@pv, ',', id))");
                while($categoria = mysqli_fetch_array($categorias)){
                    $sql_delete .= " OR id = ".$categoria['id'];
                    mysqli_query($conn, "UPDATE produto SET id_categoria = '$id_pai' WHERE id_categoria = ".$categoria['id']);
                }                
                
                mysqli_query($conn, "UPDATE produto SET id_categoria = '$id_pai' WHERE id_categoria = ".$id_categoria);
                mysqli_query($conn, "DELETE FROM categoria WHERE ".$sql_delete); 
    
                echo "OK";

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