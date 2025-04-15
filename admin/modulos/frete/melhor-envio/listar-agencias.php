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

        include_once '../../../../bd/conecta.php';

        $busca_dados_melhor_envio = mysqli_query($conn, "SELECT melhor_envio_nome_aplicacao, melhor_envio_email_aplicacao, melhor_envio_ambiente FROM frete WHERE id = 1");
        $melhor_envio             = mysqli_fetch_array($busca_dados_melhor_envio);

        if($melhor_envio['melhor_envio_ambiente'] == 'S'){
            $url = "https://sandbox.melhorenvio.com.br/api/v2/me/shipment/agencies";
        } else {
            $url = "https://melhorenvio.com.br/api/v2/me/shipment/agencies";
        }

        $nome_aplicacao           = $melhor_envio['melhor_envio_nome_aplicacao'];
        $email_aplicacao          = $melhor_envio['melhor_envio_email_aplicacao'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "User-Agent: ".$nome_aplicacao." (".$email_aplicacao.")"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

        include_once '../../../../bd/conecta.php';

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