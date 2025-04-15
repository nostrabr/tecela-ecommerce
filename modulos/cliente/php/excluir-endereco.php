<?php

//INICIA A SESSÃO
session_start();

//RECEBE OS DADOS DO CADASTRO
$identificador = trim(strip_tags(filter_input(INPUT_POST, "identificador", FILTER_SANITIZE_STRING)));   

if(!empty($identificador)){

    include_once '../../../bd/conecta.php';
    
    //VERIFICA SE O ENDEREÇO EXISTE
    $busca_endereco = mysqli_query($conn, "SELECT id_cliente, padrao FROM cliente_endereco WHERE identificador = '$identificador'");

    //SE ENCONTROU
    if(mysqli_num_rows($busca_endereco) > 0){
        
        $endereco = mysqli_fetch_array($busca_endereco);
        
        //MUDA O STATUS PARA 0 QUE É O STATUS DE EXCLUÍDO
        mysqli_query($conn, "UPDATE cliente_endereco SET status = 0 WHERE identificador = '$identificador'");

        //VERIFICA SE ERA O ENDEREÇO PADRÃO, E ALTERA PARA OUTRO
        if($endereco['padrao'] == 1){
            //MUDA O STATUS PARA 0 QUE É O STATUS DE EXCLUÍDO
            mysqli_query($conn, "UPDATE cliente_endereco SET padrao = 1 WHERE status = 1 AND id_cliente = ".$endereco['id_cliente']." LIMIT 1");
        }

        //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
        $dados[] = array(
            "status" => "SUCESSO"
        );
        echo json_encode($dados);

    } else {
        
        //RETORNA ERRO PARA O AJAX
        $dados[] = array(
            "status" => "ERRO"
        );
        echo json_encode($dados);

    }
    
    include_once '../../../bd/desconecta.php';

} else {

    //VERIFICA SE VEIO DO AJAX
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                
        //RETORNA ERRO PARA O AJAX
        $dados[] = array(
            "status" => "ERRO"
        );
        echo json_encode($dados);
        
    } else {
        
        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='/';</script>";
        
    }

}

