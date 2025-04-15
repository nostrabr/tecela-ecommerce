<?php

//INICIA A SESSÃO
session_start();

$nota        = trim(strip_tags(filter_input(INPUT_POST, "nota", FILTER_SANITIZE_NUMBER_INT)));   
$carrinho    = trim(strip_tags(filter_input(INPUT_POST, "carrinho")));   
$observacoes = trim(strip_tags(filter_input(INPUT_POST, "observacoes", FILTER_SANITIZE_STRING)));   
$visitante   = filter_var($_SESSION['visitante']);

if(!empty($nota) & mb_strlen($carrinho) == 32 & mb_strlen($visitante) == 32){

    include_once '../../../bd/conecta.php';

    $busca_pedido = mysqli_query($conn, "
        SELECT p.id 
        FROM pedido AS p 
        INNER JOIN carrinho AS c ON c.id = p.id_carrinho
        WHERE c.identificador = '$carrinho'
    ");
    $pedido = mysqli_fetch_array($busca_pedido);
    $id_pedido = $pedido['id'];

    mysqli_query($conn, "UPDATE avaliacao SET nota = '$nota', comentario = '$observacoes', status = 1 WHERE id_pedido = '$id_pedido' AND tipo = 'EXPERIENCIA-COMPRA'");
    
    //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
    $dados[] = array(
        "status" => "OK"
    );
    echo json_encode($dados);

    include_once '../../../bd/desconecta.php';
    
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