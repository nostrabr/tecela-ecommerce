<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!empty($pagina)){

    $busca_dados_melhor_envio = mysqli_query($conn, "SELECT melhor_envio_nome_aplicacao, melhor_envio_email_aplicacao, melhor_envio_token, melhor_envio_ambiente FROM frete WHERE id = 1");
    $melhor_envio             = mysqli_fetch_array($busca_dados_melhor_envio);

    if($melhor_envio['melhor_envio_ambiente'] == 'S'){
        $url = "https://sandbox.melhorenvio.com.br/api/v2/me/cart?page=".$pagina;
    } else {
        $url = "https://melhorenvio.com.br/api/v2/me/cart";
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

    $json  = json_decode($response, true);

    if($json['total'] > 0){

        $data               = $json["data"];
        $select_pedidos     = '';
        $array_protocolos   = array();
        $array_pedidos      = array();
        foreach ($data as $key => $d) { array_push($array_protocolos,$d['protocol']); }
        $json_protocolos    = json_encode($array_protocolos);
                
        foreach ($array_protocolos as $key => $p) { 
            $select_pedidos .= 'pfp.melhor_envio_codigo_envio = "'.$p.'" OR ';
        }

        $select_pedidos = substr($select_pedidos, 0, -3);

        $busca_pedidos = mysqli_query($conn, "
            SELECT pfp.melhor_envio_codigo_envio, p.codigo
            FROM pedido_frete_pacote AS pfp 
            LEFT JOIN pedido_frete AS pf ON pfp.id_pedido_frete = pf.id
            LEFT JOIN pedido AS p ON pf.id_pedido = p.id
            WHERE $select_pedidos
        ");
        $array_protocolos   = array();
        while($pedido = mysqli_fetch_array($busca_pedidos)){
            array_push($array_pedidos, $pedido['codigo']);
            array_push($array_protocolos, $pedido['melhor_envio_codigo_envio']);
        }
        $array_total = array(
            "total"      => $json['total'],
            "protocolos" => $array_protocolos,
            "pedidos"    => $array_pedidos,
            "response"   => json_decode($response, true),
        );

    } else {
        
        $array_total = array(
            "total"  => 0
        );

    }

    echo json_encode($array_total);

} else {

    //REDIRECIONA PARA A TELA DE LOGIN
    echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";
        
}