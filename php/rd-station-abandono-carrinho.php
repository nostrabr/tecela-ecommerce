<?php 

session_start();

$session_visitante = filter_var($_SESSION['visitante']);

if(mb_strlen($session_visitante) == 32){
    
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

    $data_aux_inicial = date('Y-m-d H:00', strtotime('-3 months', strtotime(date('Y-m-d H:i:s'))));
    $data_aux_final   = date('Y-m-d H:00', strtotime('-1 hours', strtotime(date('Y-m-d H:i:s'))));

    //BUSCA CARRINHOS ABANDONADOS
    $carrinhos_abandonados = mysqli_query($conn, "
        SELECT c.id AS carrinho_id, c.id_cliente AS carrinho_id_cliente, c.data_cadastro AS carrinho_data_cadastro, c.email_cliente AS carrinho_email_cliente
        FROM carrinho AS c
        WHERE 
            c.email_cliente != '' AND
            c.status = 0 AND 
            c.data_cadastro BETWEEN '$data_aux_inicial' AND '$data_aux_final' AND 
            (SELECT COUNT(cp.id) FROM carrinho_produto AS cp WHERE c.id = cp.id_carrinho AND cp.status = 1) > 0 AND 
            (SELECT COUNT(pdse.id) FROM pedido_rd_station_events AS pdse WHERE c.id = pdse.id_carrinho) = 0
        ORDER BY c.id ASC
    ");

    while($carrinho_abandonado = mysqli_fetch_array($carrinhos_abandonados)){ 

        //BUSCA OS DADOS DO CLIENTE CASO TENHA O ID
        if($carrinho_abandonado['carrinho_id_cliente'] != ''){
            $busca_cliente = mysqli_query($conn, "
                SELECT c.*
                FROM cliente AS c
                WHERE c.id = '".$carrinho_abandonado['carrinho_id_cliente']."' 
            ");
            $cliente = mysqli_fetch_array($busca_cliente);
            $cliente_nome  = $cliente['nome'].' '.$cliente['sobrenome'];
            $cliente_email = $cliente['email'];
        } else {
            $cliente_nome  = '';
            $cliente_email = $carrinho_abandonado['carrinho_email_cliente'];
        }

        if($cliente_email != ''){           

            $identificador_rd_station_event = md5(time().$carrinho_abandonado['carrinho_id'].'rdstationpendingpayment');
            mysqli_query($conn, "INSERT INTO pedido_rd_station_events (identifier, id_carrinho, carrinho_abandonado, carrinho_abandonado_data) VALUES ('$identificador_rd_station_event','".$carrinho_abandonado['carrinho_id']."',1,NOW())");
                    
            $carrinho_id   = $carrinho_abandonado['carrinho_id'];        

            //CALCULA O NÚMERO DE ITENS
            $busca_carrinho = mysqli_query($conn, "
                SELECT cp.quantidade
                FROM carrinho AS c
                INNER JOIN carrinho_produto AS cp ON c.id = cp.id_carrinho
                WHERE cp.status = 1 AND c.id = '".$carrinho_id."'
            ");
            
            $n_itens = 0;
            while($carrinho = mysqli_fetch_array($busca_carrinho)){
                $n_itens += $carrinho['quantidade'];
            }  

            $json_carrinho = '{
                "event_type": "CART_ABANDONED",
                "event_family":"CDP",
                "payload": {
                    "name": "'.$cliente_nome.'",
                    "email": "'.$cliente_email.'",
                    "cf_cart_id": "'.$carrinho_id.'",
                    "cf_cart_total_items": '.$n_itens.',
                    "cf_cart_status": "in_progress"
                }
            }';
                
            //ENVIA LEAD DO PEDIDO
            $response = postLead($json_carrinho);

            //CASO DE ERRO DE TOKEN, ATUALIZA O TOKEN REFAZ O POST
            if($response['error'] == 'invalid_token'){
                refreshToken();
                postLead($json_carrinho);
            }

            //BUSCA OS PRODUTOS
            $busca_produtos = mysqli_query($conn, "
                SELECT p.id, p.sku
                FROM carrinho AS c
                INNER JOIN carrinho_produto AS cp ON c.id = cp.id_carrinho
                INNER JOIN produto AS p ON p.id = cp.id_produto
                WHERE cp.status = 1 AND c.id = '".$carrinho_id."'
            ");
            
            while($produto = mysqli_fetch_array($busca_produtos)){ 

                $json_produto = '{
                    "event_type": "CART_ABANDONED_ITEM",
                    "event_family":"CDP",
                    "payload": {
                        "name": "'.$cliente_nome.'",
                        "email": "'.$cliente_email.'",
                        "cf_cart_id": "'.$carrinho_id.'",
                        "cf_cart_product_id": "'.$produto['id'].'",
                        "cf_cart_product_sku": "'.$produto['sku'].'"
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
        
    }

    //DESCONECTA DO BANCO
    include_once '../bd/desconecta.php';    
        
}