<?php

if(mb_strlen($identificador_carrinho) == 32){
    
    function get_tag($txt,$tag){
        $offset = 0;
        $start_tag = "<".$tag;
        $end_tag = "</".$tag.">";
        $arr = array();
        do{
            $pos = strpos($txt,$start_tag,$offset); 
            if($pos){
                $str_pos = strpos($txt,">",$pos)+1;
                $end_pos = strpos($txt,$end_tag,$str_pos);  
                $len = $end_pos - $str_pos;
                $f_text = substr($txt,$str_pos,$len);
    
    
        $arr[] = $f_text;
            $offset = $end_pos;
        }
    } while($pos);
        return $arr;
    }
    
    $xml = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:sswinfbr.sswCotacao"><soapenv:Header/><soapenv:Body>';

    $xml .= 
    '<urn:cotar soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
        <dominio>'.$frete['tw_dominio'].'</dominio>
        <login>'.$frete['tw_login'].'</login>
        <senha>'.$frete['tw_senha'].'</senha>
        <cnpjPagador>'.$frete['tw_cnpj_pagador'].'</cnpjPagador>
        <cepOrigem>'.$cep_remetente.'</cepOrigem>
        <cepDestino>'.$cep_destinatario.'</cepDestino>
        <valorNF>'.$valor_total_produtos.'</valorNF>
        <quantidade>'.$qtde_total_produtos.'</quantidade>
        <peso>'.$peso_total_produtos.'</peso>
        <volume>'.$volume_total_produtos.'</volume>
        <mercadoria>001</mercadoria>
        <cnpjDestinatario></cnpjDestinatario>
    </urn:cotar>';

    $xml .= '</soapenv:Body></soapenv:Envelope>';

    $url = 'https://ssw.inf.br/ws/sswCotacao/index.php?wsdl';

    $soap_do = curl_init(); 
    curl_setopt($soap_do, CURLOPT_URL,            $url );   
    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10); 
    curl_setopt($soap_do, CURLOPT_TIMEOUT,        10); 
    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);  
    curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false); 
    curl_setopt($soap_do, CURLOPT_POST,           true ); 
    curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $xml); 
    curl_setopt($soap_do, CURLOPT_HTTPHEADER,     array('Content-Type: text/xml; charset=utf-8', 'Content-Length: '.strlen($xml))); 
    curl_setopt($soap_do, CURLOPT_USERPWD, $frete['tw_login'].":".$frete['tw_senha']);

    $response = curl_exec($soap_do);

    $soap = simplexml_load_string($response);
    $soap->registerXPathNamespace('ns1', 'urn:sswinfbr.sswCotacao');
    $resposta = (string) $soap->xpath('//ns1:cotarResponse/return')[0];

    $erro = get_tag($resposta,'erro')[0];

    $frete_tw = 0;

    //SE NÃO RETORNOU ERRO, ADICIONA O FRETE A LISTA DE FRETES
    if($erro == 0){
        $prazo_final = get_tag($resposta,'prazo')[0]+$frete['prazo_minimo'];
        $fretes[] = array(
            'empresa'      =>  '',
            'nome'         => 'TW',
            'preco'        => get_tag($resposta,'totalFrete')[0],
            'preco_brl'    => money_format('%(#10n', get_tag($resposta,'totalFrete')[0]),
            'prazo'        => $prazo_final,
            'data_entrega' => date('d/m/Y', strtotime('+'.$prazo_final.' day'))
        );
        $frete_tw = number_format(get_tag($resposta,'totalFrete')[0],2);
    }

}