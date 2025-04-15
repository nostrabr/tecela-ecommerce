<?php

if(!empty($order_id) & !empty($descricao_cancelamento)){

    $busca_dados_melhor_envio = mysqli_query($conn, "SELECT melhor_envio_nome_aplicacao, melhor_envio_email_aplicacao, melhor_envio_token, melhor_envio_ambiente FROM frete WHERE id = 1");
    $melhor_envio             = mysqli_fetch_array($busca_dados_melhor_envio);

    if($melhor_envio['melhor_envio_ambiente'] == 'S'){
        $url = "https://sandbox.melhorenvio.com.br/api/v2/me/shipment/cancel";
    } else {
        $url = "https://melhorenvio.com.br/api/v2/me/shipment/cancel";
    }

    $token                    = $melhor_envio['melhor_envio_token'];
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
        CURLOPT_POSTFIELDS =>"{\n \"order\": {\n \"id\": \"$order_id\",\n \"reason_id\": \"2\",\n \"description\": \"$descricao_cancelamento\"\n }\n}",
        CURLOPT_HTTPHEADER => array(
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: Bearer $token",
            "User-Agent: ".$nome_aplicacao." (".$email_aplicacao.")"
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

} else {

    //REDIRECIONA PARA A TELA DE LOGIN
    echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";

}