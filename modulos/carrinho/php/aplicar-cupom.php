<?php

//INICIA A SESSÃO
session_start();
 
$nome_cupom            = mb_strtoupper(trim(strip_tags(filter_input(INPUT_POST, "cupom", FILTER_SANITIZE_STRING))));   
$identificador_cliente = $_SESSION['identificador'];

if(!empty($nome_cupom) & mb_strlen($identificador_cliente) == 32){

    include_once '../../../bd/conecta.php';

    //PEGA A DATA DE HOJE
    $hoje = date('Y-m-d');

    //BUSCA O CUPOM
    $busca_cupom = mysqli_query($conn, "SELECT * FROM cupom WHERE nome = '".$nome_cupom."' AND status = 1 AND validade >= '".$hoje."'");
    $cupom       = mysqli_fetch_array($busca_cupom);

    //SE ENCONTROU O CUPOM, PROSSEGUE
    if(mysqli_num_rows($busca_cupom) > 0){ 
        
        $reutilizando = true;
        $em_estoque   = false;
        
        //BUSCA O ID DO CLIENTE
        $busca_cliente = mysqli_query($conn, "SELECT id FROM cliente WHERE identificador = '$identificador_cliente'");
        $cliente       = mysqli_fetch_array($busca_cliente);

        //BUSCA SE O CUPOM JÁ FOI USADO PELO CLIENTE
        $busca_cupom_usado = mysqli_query($conn, "SELECT id FROM cupom_uso WHERE id_cupom = ".$cupom['id']." AND id_cliente = ".$cliente["id"]);
        $cupom_usado       = mysqli_fetch_array($busca_cupom_usado);     
        
        //SE NÃO FOI USADO PELO CLIENTE, PROSSEGUE
        if(mysqli_num_rows($busca_cupom_usado) == 0){
            $reutilizando = false;
        }               

        //BUSCA A QUANTIDADE DE CUPONS
        $busca_cupons_usados = mysqli_query($conn, "SELECT COUNT(id) AS total FROM cupom_uso WHERE id_cupom = ".$cupom['id']);
        $cupons_usados       = mysqli_fetch_array($busca_cupons_usados);

        //SE AINDA TIVEREM 
        if($cupom['quantidade'] > $cupons_usados['total']){
            $em_estoque = true;
        }

        //SE TEM DISPONÍVEIS E NÃO ESTÁ SENDO REUTILIZADO PELO CLIENTE RETORNA OK
        if($em_estoque & !$reutilizando){

            //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
            $dados[] = array(
                "status"        => "SUCESSO",
                "identificador" => $cupom['identificador'],
                "valor"         => $cupom['valor'],
                "tipo"          => $cupom['tipo']
            );
            echo json_encode($dados);
            
        //SENÃO, RETORNA CUPOM INVÁLIDO
        } else {

            //VERIFICA SE VEIO DO AJAX
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        
                //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
                $dados[] = array(
                    "status"   => "ERRO",
                    'mensagem' => "Cupom inválido"
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
                "status"   => "ERRO",
                'mensagem' => "Cupom inválido"
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
            "status"   => "ERRO",
            "mensagem" => "Erro ao tentar aplicar cupom. Se o problema persistir, contate o administrador do sistema."
        );
        echo json_encode($dados);
        
    } else {
        
        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='/';</script>";
        
    }

}
