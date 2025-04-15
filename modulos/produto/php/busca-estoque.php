<?php

//INICIA A SESSÃO
session_start();

$id_atributo = trim(strip_tags(filter_input(INPUT_POST, "id-atributo", FILTER_SANITIZE_NUMBER_INT)));   
$id_produto  = trim(strip_tags(filter_input(INPUT_POST, "id-produto", FILTER_SANITIZE_NUMBER_INT)));  
$visitante   = filter_var($_SESSION['visitante']);

if(!empty($id_atributo) & !empty($id_produto) & mb_strlen($visitante) == 32){

    include_once '../../../bd/conecta.php';
    
    //BUSCA AS VARIANTES DO PRODUTO
    $variantes = mysqli_query($conn, "SELECT * FROM produto_variacao WHERE status != 2 AND id_caracteristica_primaria = $id_atributo AND id_produto = $id_produto");
    
    //SE O PRODUTO EXISTE, PROSSEGUE
    if(mysqli_num_rows($variantes) > 0){

        while($variante = mysqli_fetch_array($variantes)){           

            $dados[] = array(
                "status"     => "SUCESSO",
                "primaria"   => $variante['id_caracteristica_primaria'],
                "secundaria" => $variante['id_caracteristica_secundaria'],
                "estoque"    => $variante['estoque'],
                "status_v"   => $variante['status'],
                "ordem"      => $variante['ordem']
            ); 

        }

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