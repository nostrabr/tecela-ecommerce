<?php

include_once '../../../../bd/conecta.php';

$busca_loja               = mysqli_query($conn, "SELECT site FROM loja WHERE id = 1");
$loja                     = mysqli_fetch_array($busca_loja);

$busca_dados_melhor_envio = mysqli_query($conn, "SELECT melhor_envio_nome_aplicacao, melhor_envio_email_aplicacao, melhor_envio_ambiente FROM frete WHERE id = 1");
$melhor_envio             = mysqli_fetch_array($busca_dados_melhor_envio);

if($melhor_envio['melhor_envio_ambiente'] == 'S'){
    $url = "https://sandbox.melhorenvio.com.br/api/v2/me/shipment/companies";
} else {
    $url = "https://melhorenvio.com.br/api/v2/me/shipment/companies";
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

$transportadoras = json_decode($response);

foreach ($transportadoras as $key => $transportadora) {
    $transportadora_id   = $transportadora->id;
    $transportadora_nome = $transportadora->name;
    mysqli_query($conn, "INSERT IGNORE INTO frete_transportadora (melhor_envio_id, melhor_envio_nome) VALUES ('$transportadora_id','$transportadora_nome')");
    foreach ($transportadora->services as $key => $servico) {
        $servico_id   = $servico->id;
        $servico_nome = $servico->name;
        mysqli_query($conn, "INSERT IGNORE INTO frete_transportadora_servico (melhor_envio_id_transportadora, melhor_envio_id_servico, melhor_envio_nome_servico) VALUES ('$transportadora_id','$servico_id','$servico_nome')");
    }
}

include_once '../../../../bd/conecta.php';

echo "<script>location.href='".$loja['site'].'admin/modulos/frete/melhor-envio/listar-informacoes-aplicativo.php'."';</script>";
