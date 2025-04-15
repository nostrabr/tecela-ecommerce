<?php

//SQL PAGSEGURO
$busca_pagamento = mysqli_query($conn, "SELECT * FROM pagamento WHERE id = 1");
$pagamento       = mysqli_fetch_array($busca_pagamento);

$email = $pagamento['email'];
$token = $pagamento['token'];
$site  = $pagamento['site'];
$ultimo_caracter = substr(trim($site), -1);
if($ultimo_caracter != '/'){
    $site = $site.'/';
}

//SE SANDBOX = TRUE, AMBIENTE DE TESTES ATIVADO - SE = FALSE, AMBIENTE DE PRODUÇÃO
if($pagamento['ambiente'] == 'S'){
    $sandbox = true;
} else {    
    $sandbox = false;
}

//OBRIGATÓRIO POSSUIR SSL
define("URL", $site."modulos/pagamento/pagseguro/");

//VERIFICA SE É PRODUÇÃO OU TESTES E CHAMA AS RESPECTIVAS URLS
if($sandbox){
    define("MOEDA_PAGAMENTO", "BRL");
    define("EMAIL_SUPORTE", $email);
    define("EMAIL_PAGSEGURO", $email);
    define("TOKEN_PAGSEGURO", $token);
    define("URL_PAGSEGURO", "https://ws.sandbox.pagseguro.uol.com.br/v2/");
    define("SCRIPT_PAGSEGURO", "https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js");
    define("URL_NOTIFICACAO", URL."php/retorno.php");
} else {
    define("MOEDA_PAGAMENTO", "BRL");
    define("EMAIL_SUPORTE", $email);
    define("EMAIL_PAGSEGURO", $email);
    define("TOKEN_PAGSEGURO", $token);
    define("URL_PAGSEGURO", "https://ws.pagseguro.uol.com.br/v2/");
    define("SCRIPT_PAGSEGURO", "https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js");
    define("URL_NOTIFICACAO", URL."php/retorno.php");
}

