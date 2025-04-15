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

        session_write_close();
        
        $data_inicio = filter_input(INPUT_POST, 'data-inicio', FILTER_SANITIZE_STRING);  
        $data_fim    = filter_input(INPUT_POST, 'data-fim', FILTER_SANITIZE_STRING);  

        if(mb_strlen($data_inicio) == 10 & mb_strlen($data_fim) == 10){

            include_once '../../../../bd/conecta.php';

            $array_produtos = [];
            
            $busca_produtos = mysqli_query($conn, "
                SELECT cp.id_produto, p.nome, SUM(cp.quantidade) AS total
                FROM carrinho_produto AS cp
                RIGHT JOIN carrinho AS c ON c.id = cp.id_carrinho
                LEFT JOIN produto AS p ON p.id = cp.id_produto
                LEFT JOIN pedido AS pd ON pd.id_carrinho = c.id
                WHERE c.status = 1 AND cp.status = 1 AND c.data_cadastro BETWEEN '$data_inicio 00:00:00' AND '$data_fim 23:59:59' AND (pd.status = 3 OR pd.status = 4)
                GROUP BY cp.id_produto 
                ORDER BY total DESC
                LIMIT 10
            ");

            while($produto = mysqli_fetch_array($busca_produtos)){

                $array_produtos[] = array(
                    "nome"  =>  mb_convert_case($produto['nome'], MB_CASE_TITLE, 'UTF-8'),
                    "total" => $produto['total']
                );

            }     

            echo json_encode($array_produtos);
                
            include_once '../../../../bd/desconecta.php';

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
