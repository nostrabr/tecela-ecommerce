<?php

include_once '../../../../bd/conecta.php';

$busca_loja               = mysqli_query($conn, "SELECT site FROM loja WHERE id = 1");
$loja                     = mysqli_fetch_array($busca_loja);

$busca_dados_melhor_envio = mysqli_query($conn, "SELECT melhor_envio_nome_aplicacao, melhor_envio_email_aplicacao, melhor_envio_token, melhor_envio_ambiente FROM frete WHERE id = 1");
$melhor_envio             = mysqli_fetch_array($busca_dados_melhor_envio);

if($melhor_envio['melhor_envio_ambiente'] == 'S'){
    $url = "https://sandbox.melhorenvio.com.br/api/v2/me/shipment/app-settings";
} else {
    $url = "https://melhorenvio.com.br/api/v2/me/shipment/app-settings";
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
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "Accept: application/json",
        "Authorization: Bearer $token",
        "User-Agent: ".$nome_aplicacao." (".$email_aplicacao.")"
    ),
));

$response = curl_exec($curl);

curl_close($curl);

$settings = json_decode($response);

foreach ($settings as $key => $setting) {

    $coleta            = $setting->collect;
    $aviso_recebimento = $setting->receipt;
    $maos_proprias     = $setting->own_hand;
    $servicos          = implode(',',$setting->services);

    mysqli_query($conn, "UPDATE frete SET melhor_envio_coleta = '$coleta', melhor_envio_aviso_recebimento = '$aviso_recebimento', melhor_envio_maos_proprias = '$maos_proprias', melhor_envio_servicos = '$servicos' WHERE id = 1");

}

include_once '../../../../bd/conecta.php';

echo "<script>location.href='".$loja['site'].'admin/configuracoes-frete.php'."';</script>";