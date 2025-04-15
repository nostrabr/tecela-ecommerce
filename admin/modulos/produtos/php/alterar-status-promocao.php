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

        //FUNÇÃO QUE INVERTE A DATA
        function inverteData($data){
            $formata_data = explode("/",$data);
            return $formata_data[2]."-".$formata_data[1]."-".$formata_data[0];
        }   

        //RECEBE OS DADOS DO FORM
        $identificador_produto = trim(strip_tags(filter_input(INPUT_POST, "identificador", FILTER_SANITIZE_STRING)));  
        $status                = trim(strip_tags(filter_input(INPUT_POST, "status", FILTER_SANITIZE_NUMBER_FLOAT)));        
        $nivel_usuario         = filter_var($_SESSION['nivel']);

        //CONFIRMA SE VEIO TUDO PREENCHIDO E SE NÃO É USUÁRIO
        if(!empty($identificador_produto)){

            include_once '../../../../bd/conecta.php';

            //BUSCA DADOS DO PRODUTO
            $busca_produto = mysqli_query($conn, "SELECT id, preco FROM produto WHERE identificador = '$identificador_produto'");
            $produto       = mysqli_fetch_array($busca_produto);
            
            if($status == 1){

                $validade      = inverteData(trim(strip_tags(filter_input(INPUT_POST, "validade", FILTER_SANITIZE_STRING))));  
                $tipo_desconto = trim(strip_tags(filter_input(INPUT_POST, "tipo-desconto", FILTER_SANITIZE_STRING)));  
                
                //CONFIRMA SE VEIO TUDO PREENCHIDO E SE NÃO É USUÁRIO
                if(!empty($validade) & mb_strlen($tipo_desconto) == 1){

                    if($tipo_desconto == 'V'){
                        $porcentagem = str_replace(',','.',trim(strip_tags(filter_input(INPUT_POST, "porcentagem", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND))));  
                        $porcentagem = number_format(($porcentagem*100)/$produto['preco'],4,'.','');
                    } else {                        
                        $porcentagem = trim(strip_tags(filter_input(INPUT_POST, "porcentagem", FILTER_SANITIZE_NUMBER_FLOAT)));  
                    }

                    //GERA UM IDENTIFICADOR PARA A PROMOÇÃO
                    $identificador_promocao = md5(date('Y-m-d H:i:s').$identificador_produto.$porcentagem.$validade);

                    //INSERE A PROMOÇÃO
                    mysqli_query($conn, "INSERT INTO promocao (identificador, id_produto, tipo, porcentagem, validade) VALUES ('$identificador_promocao','".$produto['id']."','$tipo_desconto','$porcentagem','$validade')");
                    
                    //ALTERA O STATUS DA PROMOÇÃO NO PRODUTO
                    mysqli_query($conn, "UPDATE produto SET promocao = 1 WHERE identificador = '$identificador_produto'");
                    
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

            } else if($status == 0){

                //ALTERA O STATUS DA PROMOÇÃO NO PRODUTO
                mysqli_query($conn, "UPDATE produto SET promocao = 0 WHERE identificador = '$identificador_produto'");

                //BUSCA A ULTIMA PROMOÇÃO DO PRODUTO
                $busca_promocao = mysqli_query($conn, "SELECT id FROM promocao WHERE id_produto = ".$produto['id']." ORDER BY id DESC LIMIT 1");
                $promocao       = mysqli_fetch_array($busca_promocao);

                //ENCERRA A PROMOÇÃO
                mysqli_query($conn, "UPDATE promocao SET data_desativacao = NOW(), status = 0 WHERE id = ".$promocao['id']);

            //SE ESTIVER ALTERANDO
            } else if($status == 2){

                $validade      = inverteData(trim(strip_tags(filter_input(INPUT_POST, "validade", FILTER_SANITIZE_STRING))));  
                $tipo_desconto = trim(strip_tags(filter_input(INPUT_POST, "tipo-desconto", FILTER_SANITIZE_STRING)));  
                
                //CONFIRMA SE VEIO TUDO PREENCHIDO E SE NÃO É USUÁRIO
                if(!empty($validade) & mb_strlen($tipo_desconto) == 1){
                
                    //BUSCA A ULTIMA PROMOÇÃO DO PRODUTO
                    $busca_promocao = mysqli_query($conn, "SELECT id FROM promocao WHERE id_produto = ".$produto['id']." ORDER BY id DESC LIMIT 1");
                    $promocao       = mysqli_fetch_array($busca_promocao);
    
                    //ENCERRA A PROMOÇÃO
                    mysqli_query($conn, "UPDATE promocao SET data_desativacao = NOW(), status = 0 WHERE id = ".$promocao['id']);

                    if($tipo_desconto == 'V'){
                        $porcentagem = str_replace(',','.',trim(strip_tags(filter_input(INPUT_POST, "porcentagem", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND))));  
                        $porcentagem = number_format(($porcentagem*100)/$produto['preco'],4,'.','');
                    } else {                        
                        $porcentagem = trim(strip_tags(filter_input(INPUT_POST, "porcentagem", FILTER_SANITIZE_NUMBER_FLOAT)));  
                    }

                    //GERA UM IDENTIFICADOR PARA A PROMOÇÃO
                    $identificador_promocao = md5(date('Y-m-d H:i:s').$identificador_produto.$porcentagem.$validade);
                
                    //CADASTRA A NOVA PRA MANTER HISTÓRICO
                    mysqli_query($conn, "INSERT INTO promocao (identificador, id_produto, tipo, porcentagem, validade) VALUES ('$identificador_promocao','".$produto['id']."','$tipo_desconto','$porcentagem','$validade')");
                                        
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
