<?php

include_once '../../../../bd/conecta.php';

//INCLUI A CONFIGURAÇÃO DO PAG SEGURO
include './configuracao.php';

include_once '../../../../bd/desconecta.php';

//MONTA A URL
$url = URL_PAGSEGURO."sessions?email=".EMAIL_PAGSEGURO."&token=".TOKEN_PAGSEGURO;

//BUSCA PELA A URL O ID DA SESSÃO DO PAG SEGURO SETANDO AS OPÇÕES
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/xml", "Content-Length: 0", "charset=UTF-8"));
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$retorno = curl_exec($curl);
curl_close($curl);

$xml = simplexml_load_string($retorno);

echo $xml->id;