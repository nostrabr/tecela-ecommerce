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
            
            $result = array(); 
            $storeFolder = "../../../../imagens/produtos/original/";  

            $identificador_produto = filter_input(INPUT_GET, "id", FILTER_SANITIZE_STRING);

            $imagens = mysqli_query($conn, "SELECT produto_imagem.id AS id, produto_imagem.imagem AS imagem, produto_imagem.capa AS capa, produto_imagem.ordem AS ordem FROM produto_imagem INNER JOIN produto ON produto.id = produto_imagem.id_produto AND produto.identificador = '$identificador_produto' ORDER BY produto_imagem.ordem ASC");
            while($imagem = mysqli_fetch_array($imagens)){
                $obj['id']    = $imagem['id'];
                $obj['name']  = $imagem['imagem'];
                $obj['capa']  = $imagem['capa'];
                $obj['ordem'] = $imagem['ordem'];
                $obj['size']  = filesize($storeFolder.$imagem['imagem']);
                $result[]     = $obj;
            }
            
            header('Content-type: text/json');              
            header('Content-type: application/json');
            echo json_encode($result);                    
            
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