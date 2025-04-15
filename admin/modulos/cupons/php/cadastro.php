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
        $nome                          = mb_strtoupper(trim(strip_tags(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING))));
        $quantidade                    = trim(strip_tags(filter_input(INPUT_POST, "quantidade", FILTER_SANITIZE_NUMBER_INT)));
        $validade                      = trim(strip_tags(filter_input(INPUT_POST, "validade", FILTER_SANITIZE_STRING)));
        $valor                         = trim(strip_tags(filter_input(INPUT_POST, "valor", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND)));  
        $tipo                          = trim(strip_tags(filter_input(INPUT_POST, "tipo", FILTER_SANITIZE_STRING)));
        $identificador_usuario_session = filter_var($_SESSION['identificador']);
        unset($_SESSION['RETORNO']); 

        //CONFIRMA SE VEIO TUDO PREENCHIDO
        if(!empty($nome) & !empty($quantidade) & !empty($validade) & !empty($valor) & !empty($tipo)){

            include_once '../../../../bd/conecta.php';

            //VERIFICA SE O NOME NÃO É REPETIDO
            $busca_cupom = mysqli_query($conn, "SELECT id FROM cupom WHERE nome = '$nome'");
            if(mysqli_num_rows($busca_cupom) > 0){                

                //PREENCHE A SESSION DE RETORNO COM ERRO
                $_SESSION['RETORNO'] = array(
                    'ERRO'       => 'ERRO-NOME-REPETIDO',
                    'nome'       => $nome,
                    'quantidade' => $quantidade,
                    'validade'   => $validade,
                    'valor'      => $valor,
                    'tipo'       => $tipo
                );

                //REDIRECIONA PARA A TELA DE USUÁRIOS
                echo "<script>location.href='../../../cupons-cadastra.php';</script>";

            } else {

                //TRATA O VALOR TROCANDO A VIRGULA PELO PONTO
                $valor = str_replace(',','.',$valor);

                //MOTIFICA O FORMADO DA DATA
                $validade = inverteData($validade);

                //GERA UM CÓDIGO IDENTIFICADOR
                $identificador_cupom = md5(date('Y-m-d H:i:s').$nome.$quantidade.$validade);

                //INSERE NO BANCO
                mysqli_query($conn, "INSERT INTO cupom (identificador, nome, quantidade, validade, valor, tipo, cadastrado_por) VALUES ('$identificador_cupom','$nome','$quantidade','$validade','$valor','$tipo','$identificador_usuario_session')");

                include_once '../../../../bd/desconecta.php';

                //REDIRECIONA PARA A TELA DE USUÁRIOS
                echo "<script>location.href='../../../cupons.php';</script>";

            }
        
        } else {
            
            //REDIRECIONA PARA A TELA DE LOGIN
            echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";
            
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
