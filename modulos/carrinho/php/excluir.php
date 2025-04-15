<?php

//INICIA A SESSÃO
session_start();
 
$identificador = trim(strip_tags(filter_input(INPUT_POST, "identificador", FILTER_SANITIZE_STRING)));   
$visitante     = filter_var($_SESSION['visitante']);

if(mb_strlen($identificador) == 32 & mb_strlen($visitante) == 32){

    include_once '../../../bd/conecta.php';

    //BUSCA O PRODUTO
    $busca_produto = mysqli_query($conn, "
        SELECT *
        FROM carrinho_produto AS cp
        INNER JOIN carrinho AS c ON c.identificador = '$visitante'
        WHERE cp.identificador = '$identificador' 
    ");
    
    //SE ESTÁ TUDO CERTO
    if(mysqli_num_rows($busca_produto) > 0){

        mysqli_query($conn, "UPDATE carrinho_produto SET status = 0 WHERE identificador = '$identificador'");

        include_once '../../../bd/desconecta.php';

        //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
        $dados[] = array(
            "status" => "SUCESSO"
        );
        echo json_encode($dados);

    } else {

        //VERIFICA SE VEIO DO AJAX
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    
            //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
            $dados[] = array(
                "status" => "ERRO"
            );
            echo json_encode($dados);
            
        } else {
            
            //REDIRECIONA PARA A TELA DE LOGIN
            echo "<script>location.href='/';</script>";
            
        }
    
    }
    
} else {

    //VERIFICA SE VEIO DO AJAX
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                
        //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
        $dados[] = array(
            "status" => "ERRO"
        );
        echo json_encode($dados);
        
    } else {
        
        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='/';</script>";
        
    }

}
