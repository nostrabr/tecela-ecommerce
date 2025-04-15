<?php

$code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRING);

if(!empty($code)){

    include_once '../../../../bd/conecta.php';

    $busca_loja               = mysqli_query($conn, "SELECT site FROM loja WHERE id = 1");
    $loja                     = mysqli_fetch_array($busca_loja);

    $busca_dados_melhor_envio = mysqli_query($conn, "SELECT * FROM frete WHERE id = 1");
    $melhor_envio             = mysqli_fetch_array($busca_dados_melhor_envio);

    if($melhor_envio['melhor_envio_ambiente'] == 'S'){
        $url = "https://sandbox.melhorenvio.com.br/oauth/token";
    } else {
        $url = "https://melhorenvio.com.br/oauth/token";
    }

    $client_id                = $melhor_envio['melhor_envio_client_id'];
    $client_secret            = $melhor_envio['melhor_envio_client_secret'];
    $redirect_uri             = $melhor_envio['melhor_envio_redirect_uri'];
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
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => array('grant_type' => 'authorization_code','client_id' => $client_id,'client_secret' => $client_secret,'redirect_uri' => $redirect_uri,'code' => $code),
        CURLOPT_HTTPHEADER => array(
            "Accept: application/json",
            "User-Agent: ".$nome_aplicacao." (".$email_aplicacao.")"
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $json = json_decode($response, true);

    $token         = $json["access_token"];
    $refresh_token = $json["refresh_token"];
    
    mysqli_query($conn, "UPDATE frete SET melhor_envio = 1, melhor_envio_token = '$token', melhor_envio_refresh_token = '$refresh_token' WHERE id = 1");
    
    include_once '../../../../bd/desconecta.php';

    echo "<script>location.href='".$loja['site'].'admin/modulos/frete/melhor-envio/listar-transportadoras.php'."';</script>";

} else {        

    include_once '../../../../bd/conecta.php';

    $busca_loja = mysqli_query($conn, "SELECT site FROM loja WHERE id = 1");
    $loja       = mysqli_fetch_array($busca_loja);
    
    include_once '../../../../bd/desconecta.php';

    echo "Código não encontrado. Você será redirecionado para uma nova tentativa...";

    sleep(10);

    echo "<script>location.href='".$loja['site'].'admin/configuracoes-frete.php'."';</script>";        

}