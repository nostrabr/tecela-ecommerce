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

        //RECEBE OS DADOS DO FORM
        $identificador_produto = trim(strip_tags(filter_input(INPUT_POST, "identificador", FILTER_SANITIZE_STRING))); 

        //CONFIRMA SE VEIO TUDO PREENCHIDO E SE NÃO É USUÁRIO
        if(!empty($identificador_produto)){

            include_once '../../../../bd/conecta.php';

            //BUSCA DADOS DO PRODUTO
            $busca_produto = mysqli_query($conn, "SELECT id, preco FROM produto WHERE identificador = '$identificador_produto'");
            $produto       = mysqli_fetch_array($busca_produto);

            //BUSCA A ULTIMA PROMOÇÃO DO PRODUTO
            $busca_promocao = mysqli_query($conn, "SELECT * FROM promocao WHERE id_produto = ".$produto['id']." ORDER BY id DESC LIMIT 1");
            $promocao       = mysqli_fetch_array($busca_promocao);

            if($promocao['tipo'] == 'V'){
                $valor_promocao = number_format($promocao['porcentagem']*$produto['preco']/100,2,',','');
            } else {
                $valor_promocao = $promocao['porcentagem'];
            }

            $retorno[] = array(
                "status"   => "OK",
                "valor"    => $valor_promocao,
                "tipo"     => $promocao['tipo'],
                "validade" => date('d/m/Y', strtotime($promocao['validade']))
            );

            echo json_encode($retorno);

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
