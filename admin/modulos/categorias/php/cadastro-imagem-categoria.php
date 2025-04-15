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
        $identificador_categoria = trim(strip_tags(filter_input(INPUT_POST, "identificador-categoria", FILTER_SANITIZE_STRING)));    
        $nivel_usuario           = filter_var($_SESSION['nivel']);

        //CONFIRMA SE VEIO TUDO PREENCHIDO E SE NÃO É USUÁRIO
        if(!empty($identificador_categoria)){

            include_once '../../../../bd/conecta.php';

                if($_FILES['imagem']['name'] != ''){
                //TRATA A IMAGEM
                //RETIRA A EXTENSÃO DA IMAGEM RECEBIDA
                $extensao    = mb_strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));  
                
                //RENOMEIA
                $nome_imagem = md5(time()).'.'.$extensao;

                //DIRETÓRIO DE IMAGENS DE BANNERS
                $diretorio = "../../../../imagens/categorias/";

                //MOVE A IMAGEM PARA O DIRETÓRIO
                move_uploaded_file($_FILES['imagem']['tmp_name'], $diretorio.$nome_imagem);
                
                //VERIFICA SE JÁ TEM IMAGEM PRA REMOVER A ANTIGA
                $busca_categoria = mysqli_query($conn, "SELECT imagem FROM categoria WHERE identificador = '$identificador_categoria'");
                $categoria       = mysqli_fetch_array($busca_categoria);

                if($categoria['imagem'] != ''){                
                    unlink($diretorio.$categoria['imagem']);
                }

                //INSERT NO BANCO
                mysqli_query($conn, "UPDATE categoria SET imagem = '$nome_imagem' WHERE identificador = '$identificador_categoria'");

            } else {
                mysqli_query($conn, "UPDATE categoria SET imagem = '' WHERE identificador = '$identificador_categoria'");
            }

            include_once '../../../../bd/desconecta.php';
            
            //REDIRECIONA PARA A TELA DE USUÁRIOS
            echo "<script>location.href='../../../categorias.php';</script>";
                
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
