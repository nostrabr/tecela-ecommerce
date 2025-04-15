<?php

include_once '../../../bd/conecta.php';

$busca_dados_melhor_envio = mysqli_query($conn, "SELECT melhor_envio_client_id, melhor_envio_client_secret, melhor_envio_redirect_uri, melhor_envio_nome_aplicacao, melhor_envio_email_aplicacao, melhor_envio_refresh_token, melhor_envio_ambiente FROM frete WHERE id = 1");
$melhor_envio             = mysqli_fetch_array($busca_dados_melhor_envio);

if($melhor_envio['melhor_envio_ambiente'] == 'S'){
  $url = "https://sandbox.melhorenvio.com.br/oauth/token";
} else {
  $url = "https://melhorenvio.com.br/oauth/token";
}

$client_id       = $melhor_envio['melhor_envio_client_id'];
$client_secret   = $melhor_envio['melhor_envio_client_secret'];
$redirect_uri    = $melhor_envio['melhor_envio_redirect_uri'];
$nome_aplicacao  = $melhor_envio['melhor_envio_nome_aplicacao'];
$email_aplicacao = $melhor_envio['melhor_envio_email_aplicacao'];
$refresh_token   = $melhor_envio['melhor_envio_refresh_token'];

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
  CURLOPT_POSTFIELDS => array('grant_type' => 'refresh_token','refresh_token' => $refresh_token,'client_id' => $client_id,'client_secret' => $client_secret),
  CURLOPT_HTTPHEADER => array(
    "Accept: application/json",
    "User-Agent: ".$nome_aplicacao." (".$email_aplicacao.")"
  ),
));

$response = curl_exec($curl);

curl_close($curl);

$json = json_decode($response, true);

$novo_token         = $json["access_token"];
$novo_refresh_token = $json["refresh_token"];

mysqli_query($conn, "UPDATE frete SET melhor_envio_token = '$novo_token', melhor_envio_refresh_token = '$novo_refresh_token' WHERE id = 1");

include_once '../../../bd/desconecta.php';