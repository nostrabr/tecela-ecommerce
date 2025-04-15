<?php 
 
$pedido_identificador = filter_input(INPUT_POST,'pedido');   

if(mb_strlen($pedido_identificador) == 32){
    
    //CONECTA AO BANCO
    include_once '../bd/conecta.php';  
    $GLOBALS['conn'] = $conn;

    $busca_loja = mysqli_query($conn, "SELECT rd_station_client_id, rd_station_client_secret, rd_station_access_token, rd_station_refresh_token FROM loja WHERE id = 1");
    $loja       = mysqli_fetch_array($busca_loja);
    
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

        echo $response;

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
    
    //VERIFICA SE O EVENTO JÁ NÃO FOI DISPARADO
    $busca_evento_pending = mysqli_query($conn, "SELECT id FROM pedido_rd_station_events WHERE id_pedido = '".$pedido['id']."' AND payment_pending = 1");

    if(mysqli_num_rows($busca_evento_pending) == 0){

        //VERIFICA SE O CLIENTE JÁ NÃO ABANDONOU O CARRINHO
        $busca_evento_abandono_carrinho = mysqli_query($conn, "SELECT id FROM pedido_rd_station_events WHERE id_carrinho = '$id_carrinho' AND payment_pending = 0");

        if(mysqli_num_rows($busca_evento_abandono_carrinho) > 0){

            //ALTERA O EVENTO DO ABANDONO E SALVA O STATUS PENDING 
            $evento_abandono_carrinho = mysqli_fetch_array($busca_evento_abandono_carrinho);
            mysqli_query($conn, "UPDATE pedido_rd_station_events SET id_pedido = '".$pedido['id']."', payment_pending = 1, payment_pending_data = NOW() WHERE id = '".$evento_abandono_carrinho['id']."'");
             
        } else {

            //SALVA NO BANCO O EVENTO DO RD COM O STATUS DO PENDING ENVIADO
            $idenficador_rd_station_event = md5(time().$id_carrinho.$pedido['id'].'rdstationpendingpayment');
            mysqli_query($conn, "INSERT INTO pedido_rd_station_events (identifier,id_carrinho,id_pedido,payment_pending,payment_pending_data) VALUES ('$idenficador_rd_station_event','$id_carrinho','".$pedido['id']."',1,NOW())");
                 
        }

        //MONTA O JSON DO PEDIDO
        $json_pedido = '{
            "event_type": "ORDER_PLACED",
            "event_family":"CDP",
            "payload": {
                "name": "'.$cliente['nome'].' '.$cliente['sobrenome'].'",
                "email": "'.$cliente['email'].'",
                "cf_order_id": "'.$pedido['id'].'",
                "cf_order_total_items": '.$n_itens.',
                "cf_order_status": "pending_payment",
                "cf_order_payment_method": "'.$pagamento_tipo.'",
                "cf_order_payment_amount": 0
            }
        }';
        
        //ENVIA LEAD DO PEDIDO
        $response = postLead($json_pedido);

        //CASO DE ERRO DE TOKEN, ATUALIZA O TOKEN REFAZ O POST
        if($response['error'] == 'invalid_token'){
            refreshToken();
            postLead($json_pedido);
        }

        //BUSCA OS PRODUTOS
        $busca_produtos = mysqli_query($conn, "
            SELECT p.id, p.sku
            FROM carrinho AS c
            INNER JOIN carrinho_produto AS cp ON c.id = cp.id_carrinho
            INNER JOIN produto AS p ON p.id = cp.id_produto
            WHERE cp.status = 1 AND c.id = '".$pedido['id_carrinho']."'
        ");
        
        while($produto = mysqli_fetch_array($busca_produtos)){ 

            $json_produto = '{
                "event_type": "ORDER_PLACED_ITEM",
                "event_family":"CDP",
                "payload": {
                    "name": "'.$cliente['nome'].' '.$cliente['sobrenome'].'",
                    "email": "'.$cliente['email'].'",
                    "cf_order_id": "'.$pedido['id'].'",
                    "cf_order_product_id": "'.$produto['id'].'",
                    "cf_order_product_sku": "'.$produto['sku'].'"
                }
            }';        
        
            //ENVIA LEAD DO PRODUTO
            $response = postLead($json_produto);

            //CASO DE ERRO DE TOKEN, ATUALIZA O TOKEN REFAZ O POST
            if($response['error'] == 'invalid_token'){
                refreshToken();
                postLead($json_produto);
            }

        } 

    }

    //DESCONECTA DO BANCO
    include_once '../bd/desconecta.php';   

}