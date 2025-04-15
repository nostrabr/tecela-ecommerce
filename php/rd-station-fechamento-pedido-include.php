<?php 
 
if(mb_strlen($pedido_identificador) == 32 & !empty($evento_status)){

    $busca_loja = mysqli_query($conn, "SELECT rd_station_client_id, rd_station_client_secret, rd_station_access_token, rd_station_refresh_token FROM loja WHERE id = 1");
    $loja       = mysqli_fetch_array($busca_loja);
    
    $GLOBALS['conn']                     = $conn;
    $GLOBALS['rd_station_client_id']     = $loja['rd_station_client_id'];
    $GLOBALS['rd_station_client_secret'] = $loja['rd_station_client_secret'];
    $GLOBALS['rd_station_access_token']  = $loja['rd_station_access_token'];
    $GLOBALS['rd_station_refresh_token'] = $loja['rd_station_refresh_token'];

    function refreshToken(){

        $curl = curl_init();

        curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.rd.services/auth/token",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\"client_id\":\"".$GLOBALS['rd_station_client_id']."\",\"client_secret\":\"".$GLOBALS['rd_station_client_secret']."\",\"refresh_token\":\"".$GLOBALS['rd_station_refresh_token']."\"}",
        CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "content-type: application/json"
        ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $array_response = json_decode($response, true);

        mysqli_query($GLOBALS['conn'], "UPDATE loja SET rd_station_access_token = '".$array_response['access_token']."', rd_station_refresh_token = '".$array_response['refresh_token']."' FROM loja WHERE id = 1");

        $GLOBALS['rd_station_access_token']  = $array_response['access_token'];
        $GLOBALS['rd_station_refresh_token'] = $array_response['refresh_token'];
        
    }

    function postLead($json_postfields){

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.rd.services/platform/events",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $json_postfields,
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "content-type: application/json",
                "authorization: Bearer ".$GLOBALS['rd_station_access_token']."" 
            ],
        ]);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return json_decode($response, true);

    }

    //BUSCA OS DADOS DO PEDIDO
    $busca_pedido = mysqli_query($conn, "
        SELECT p.*
        FROM pedido AS p
        WHERE p.identificador = '$pedido_identificador' 
    ");
    $pedido = mysqli_fetch_array($busca_pedido);

    //BUSCA OS DADOS DO CLIENTE
    $busca_cliente = mysqli_query($conn, "
        SELECT c.*
        FROM cliente AS c
        WHERE c.id = '".$pedido['id_cliente']."' 
    ");
    $cliente = mysqli_fetch_array($busca_cliente);
    
    //BUSCA OS DADOS DO PAGAMENTO
    $busca_pagamento = mysqli_query($conn, "
        SELECT p.*
        FROM pagamento_pagseguro AS p
        WHERE p.id_pedido = '".$pedido['id']."' 
    ");
    $pagamento = mysqli_fetch_array($busca_pagamento);

    //CALCULA O NÚMERO DE ITENS
    $busca_carrinho = mysqli_query($conn, "
        SELECT c.id, cp.quantidade
        FROM carrinho AS c
        INNER JOIN carrinho_produto AS cp ON c.id = cp.id_carrinho
        WHERE cp.status = 1 AND c.id = '".$pedido['id_carrinho']."'
    ");
    
    $n_itens = 0;
    while($carrinho = mysqli_fetch_array($busca_carrinho)){
        $id_carrinho = $carrinho['id'];
        $n_itens += $carrinho['quantidade'];
    }  

    $preco_total = number_format($pagamento['parcelas']*$pagamento['valor_parcela'],2,'.','');

    //DEFINE O TIPO DO PAGAMNETO
    if($pagamento['tipo'] == 'CARTAO'){
        $pagamento_tipo = 'Credit Card';
    } else if($pagamento['tipo'] == 'BOLETO'){
        $pagamento_tipo = 'Invoice';
    } else {        
        $pagamento_tipo = 'Others';
    }

    //VERIFICA SE O EVENTO CONFIRMED JÁ NÃO FOI DISPARADO
    $busca_evento_confirmed = mysqli_query($conn, "SELECT id FROM pedido_rd_station_events WHERE id_carrinho = '$id_carrinho' AND id_pedido = '".$pedido['id']."' AND payment_pending = 1 AND payment_confirmed = 1");

    if(mysqli_num_rows($busca_evento_confirmed) == 0){

        $evento_confirmed = mysqli_fetch_array($busca_evento_confirmed);

        //SALVA NO BANCO O EVENTO DO RD COMO STATUS DO CONFIRMED ENVIADO
        mysqli_query($conn, "UPDATE pedido_rd_station_events SET payment_confirmed = 1, payment_confirmed_data = NOW() WHERE id_carrinho = '$id_carrinho' AND id_pedido = '".$pedido['id']."' AND payment_pending = 1");
            
        //MONTA O JSON DO PEDIDO
        $json_pedido = '{
            "event_type": "ORDER_PLACED",
            "event_family":"CDP",
            "payload": {
                "name": "'.$cliente['nome'].' '.$cliente['sobrenome'].'",
                "email": "'.$cliente['email'].'",
                "cf_order_id": "'.$pedido['id'].'",
                "cf_order_total_items": '.$n_itens.',
                "cf_order_status": "'.$evento_status.'",
                "cf_order_payment_method": "'.$pagamento_tipo.'",
                "cf_order_payment_amount": '.$preco_total.'
            }
        }';
        
        //ENVIA LEAD DO PEDIDO
        $response = postLead($json_pedido);

        //CASO DE ERRO DE TOKEN, ATUALIZA O TOKEN REFAZ O POST
        if($response['error'] == 'invalid_token'){
            refreshToken();
            postLead($json_pedido);
        }
        
    }

}