<?php

//BUSCA AS CONFIGURAÇÕES DE FRETE
$busca_dados_frete        = mysqli_query($conn, "SELECT * FROM frete WHERE id = 1");
$frete                    = mysqli_fetch_array($busca_dados_frete);

if($frete['melhor_envio_ambiente'] == 'S'){
    $url = "https://sandbox.melhorenvio.com.br/api/v2/me/shipment/calculate";
} else {
    $url = "https://melhorenvio.com.br/api/v2/me/shipment/calculate";
}

//BUSCA O ID DO CARRINHO PELO IDENTIFICADOR
$busca_carrinho           = mysqli_query($conn, "SELECT id FROM carrinho WHERE identificador = '".$identificador_carrinho ."'");
$carrinho                 = mysqli_fetch_array($busca_carrinho);

//BUSCA OS PRODUTOS NO CARRINHO
$produtos_carrinho  = mysqli_query($conn, "
    SELECT cp.quantidade AS produto_quantidade, p.id AS produto_id, p.nome AS produto_nome, p.sku AS produto_sku, p.altura AS produto_altura, p.largura AS produto_largura, p.comprimento AS produto_comprimento, p.peso AS produto_peso, p.preco AS produto_preco
    FROM carrinho_produto AS cp 
    INNER JOIN produto AS p ON cp.id_produto = p.id 
    WHERE cp.status = 1 AND id_carrinho = ".$carrinho['id']
);

//RECOLHE TODAS AS VARIÁVEIS
$cep_remetente            = str_replace('-','',str_replace('.','',$frete['cep']));
$cep_destinatario         = str_replace('-','',str_replace('.','',$cep_destinatario));
$nome_aplicacao           = $frete['melhor_envio_nome_aplicacao'];
$email_aplicacao          = $frete['melhor_envio_email_aplicacao'];
$token                    = $frete['melhor_envio_token'];

//MONTA A VARIAVEL COM OS PRODUTOS PARA O MELHOR ENVIO
$produtos_melhor_envio    = "";

//VERIFICA A QUANTIDADE TOTAL DE PRODUTOS PARA NÃO COLOCAR A VIRGULA NO ÚLTIMO OBJETO DE PRODUTO
$total_produtos           = mysqli_num_rows($produtos_carrinho);
$contador_produtos        = 0; 
$valor_total_produtos     = 0;     
$qtde_total_produtos      = 0;   
$peso_total_produtos      = 0;  
$volume_total_produtos    = 0; 

//PERCORRE O CARRINHO E PREENCHE NA VARIÁVEL DE PARAMENTROS DO MELHOR ENVIO
while($produto = mysqli_fetch_array($produtos_carrinho)){

    $contador_produtos++;

    //ATRIBUI AS VARIÁVEIS E TRANSFORMA SE PRECISO
    $produto_sku           = $produto['produto_sku'];
    $produto_largura       = $produto['produto_largura'];
    $produto_altura        = $produto['produto_altura'];
    $produto_comprimento   = $produto['produto_comprimento'];
    $produto_peso          = $produto['produto_peso']/1000;
    $produto_preco         = $produto['produto_preco'];
    $produto_quantidade    = $produto['produto_quantidade'];
    $volume_produto        = ($produto_largura*$produto_altura*$produto_comprimento)/1000000;
    $valor_total_produtos  += ($produto_preco*$produto_quantidade);
    $qtde_total_produtos   += $produto_quantidade;
    $peso_total_produtos   += $produto_peso*$produto_quantidade;  
    $volume_total_produtos += $volume_produto*$produto_quantidade; 

    //MONTA O OBJETO DO PRODUTO
    $produtos_melhor_envio .= "
        {
        \n \"id\": \"$produto_sku\",
        \n \"width\": $produto_largura,
        \n \"height\": $produto_altura,
        \n \"length\": $produto_comprimento,
        \n \"weight\": ".$produto_peso.",
        \n \"insurance_value\": $produto_preco,
        \n \"quantity\": $produto_quantidade\n        
        }
    ";

    //SE NÃO FOR O ÚLTIMO COLOCA UMA VIRGULA PARA RECEBER O PRÓXIMO PRODUTO
    if($contador_produtos != $total_produtos){
        $produtos_melhor_envio .= ", \n";
    }

}

//VERIFICA AS OPÇÕES DE ENVIO CADASTRADAS E MONTA A VARIAVEL PARA INCREMENTEAR NO ENVIO
if($frete['melhor_envio_aviso_recebimento'] == 1){
    $melhor_envio_aviso_recebimento = 'true';
} else {
    $melhor_envio_aviso_recebimento = 'false';
}
if($frete['melhor_envio_maos_proprias'] == 1){
    $melhor_envio_maos_proprias = 'true';
} else {
    $melhor_envio_maos_proprias = 'false';
}
$opcoes_melhor_envio = "
    {
        \n \"receipt\": $melhor_envio_aviso_recebimento,
        \n \"own_hand\": $melhor_envio_maos_proprias\n        
    }
";

//VERIFICA AS OPÇÕES DE SERVIÇOS CADASTRADOS
if($frete['melhor_envio_servicos'] != ''){
    $servicos_melhor_envio = ", \"services\": \"".$frete['melhor_envio_servicos']."\"";
} else {
    $servicos_melhor_envio = '';
}
        
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
    CURLOPT_POSTFIELDS => "{\n \"from\": {\n \"postal_code\": \"$cep_remetente\"\n },\n \"to\": {\n \"postal_code\": \"$cep_destinatario\"\n },\n \"products\": [\n $produtos_melhor_envio ]\n, \"options\": \n $opcoes_melhor_envio  $servicos_melhor_envio }",
    CURLOPT_HTTPHEADER => array(
    "Accept: application/json",
    "Content-Type: application/json",
    "Authorization: Bearer $token",
    "User-Agent: ".$nome_aplicacao." (".$email_aplicacao.")"
    ),
));

$response = curl_exec($curl);

curl_close($curl);

$formas_envio = json_decode($response);

//VERIFICA SE O FRETE É DO MELHOR ENVIO E CADASTRA AS INFORMAÇÕES NECESSÁRIAS PARA GERAR ETIQUETA E RETORNA O VALOR DO FRETE
foreach ($formas_envio as $key => $forma_envio) {
    if(!isset($forma_envio->error)){
        if(mb_strtolower($frete_tipo_frete) == mb_strtolower($forma_envio->name)){

            $id_servico                          = $forma_envio->id;
            $id_transportadora                   = $forma_envio->company->id;
            $custom_delivery_range_min           = $forma_envio->custom_delivery_range->min;
            $custom_delivery_range_max           = $forma_envio->custom_delivery_range->max;

            if($forma_envio->additional_services->receipt == false){
                $servico_adicional_aviso_recebimento = 0;
            } else {                
                $servico_adicional_aviso_recebimento = 1;
            }

            if($forma_envio->additional_services->own_hand == false){
                $servico_adicional_maos_proprias = 0;
            } else {                
                $servico_adicional_maos_proprias = 1;
            }

            if($forma_envio->additional_services->collect == false){
                $servico_adicional_coleta = 0;
            } else {                
                $servico_adicional_coleta = 1;
            }

            //GERA UM IDENTIFICADOR PARA O PEDIDO_FRETE
            $identificador_pedido_frete = md5(date('Y-m-d H:i:s').$id_pedido.$frete_tipo_frete.$id_endereco);

            //INSERE A COTAÇÃO DE FRETE NO BANCO
            mysqli_query($conn, "INSERT INTO pedido_frete (identificador, id_pedido, id_cliente_endereco, id_frete_transportadora, id_frete_transportadora_servico, nome_servico, preco, desconto, tempo_entrega_min, tempo_entrega_max, funcao_aviso_recebimento, funcao_maos_proprias, funcao_coleta) VALUES ('$identificador_pedido_frete','$id_pedido','$id_endereco','$id_transportadora','$id_servico','$forma_envio->name','$forma_envio->custom_price','$forma_envio->discount','$custom_delivery_range_min','$custom_delivery_range_max','$servico_adicional_aviso_recebimento','$servico_adicional_maos_proprias','$servico_adicional_coleta')");

            //BUSCA O ID DO PEDIDO FRETE
            $id_pedido_frete = mysqli_insert_id($conn);
            
            //GERA UM CONTADOR DE PACOTES
            $contador_pacotes = 0;

            //INSERE OS PACOTES DO PEDIDO FRETE
            foreach ($forma_envio->packages as $key => $package) {

                //SE TEM MAIS DE UM PACOTE O PRECO E O DESCONTO NÃO VEM DENTRO DO PACOTE E PRECISA SER PEGO DA FORMA DE ENVIO
                if(isset($package->price)){
                    $pacote_preco        = $package->price;
                    $pacote_desconto     = $package->discount;
                    $pacote_formato      = $package->format;
                    $pacote_altura       = $package->dimensions->height;
                    $pacote_largura      = $package->dimensions->width;
                    $pacote_comprimento  = $package->dimensions->length;
                    $pacote_peso         = $package->weight;
                    $pacote_valor_seguro = $package->insurance_value;
                } else {
                    $pacote_preco        = $forma_envio->custom_price;
                    $pacote_desconto     = $forma_envio->discount;
                    $pacote_formato      = $package->format;
                    $pacote_altura       = $package->dimensions->height;
                    $pacote_largura      = $package->dimensions->width;
                    $pacote_comprimento  = $package->dimensions->length;
                    $pacote_peso         = $package->weight;
                    $pacote_valor_seguro = $package->insurance_value;
                }
                
                //INCREMENTA O CONTADOR PARA NÃO GERAR IDENTIFICADORES REPETIDOS
                $contador_pacotes++;

                //GERAL UM IDENTIFICADOR
                $identificador_pedido_frete_pacote = md5(date('Y-m-d H:i:s').$id_pedido_frete.$contador_pacotes);

                //INSERE OS PACOTE NO BANCO
                mysqli_query($conn, "INSERT INTO pedido_frete_pacote (identificador, id_pedido_frete, preco, desconto, formato, altura, largura, comprimento, peso, valor_seguro) VALUES ('$identificador_pedido_frete_pacote','$id_pedido_frete','$pacote_preco','$pacote_desconto','$pacote_formato','$pacote_altura','$pacote_largura','$pacote_comprimento','$pacote_peso','$pacote_valor_seguro')");
            
                //BUSCA O ID DO PEDIDO FRETE PACOTE
                $id_pedido_frete_pacote = mysqli_insert_id($conn);
                
                //ZERA O CONTADOR DE PRODUTOS DO PACOTE
                $contador_pacotes_produtos = 0;

                //INSERE OS PRODUTOS DO PACOTE NO BANCO                
                foreach ($package->products as $key3 => $product) {

                    //INCREMENTA O CONTADOR DE PRODUTOS DO PACOTE
                    $contador_pacotes_produtos++;
                    
                    //GERAL UM IDENTIFICADOR
                    $identificador_pedido_frete_pacote_produto = md5(date('Y-m-d H:i:s').$id_pedido_frete.$contador_pacotes.$contador_pacotes_produtos);

                    mysqli_query($conn, "INSERT INTO pedido_frete_pacote_produto (identificador, id_pedido_frete_pacote, produto_sku, produto_quantidade) VALUES ('$identificador_pedido_frete_pacote_produto','$id_pedido_frete_pacote','$product->id','$product->quantity')");
                
                }

            }            

            //PEGA O VALOR DO FRETE
            $valor_frete = $forma_envio->custom_price;

        }

    }

}

//SE ESTÁ ATIVO O FRETE TW    
if($frete_tipo_frete == 'TW'){
    include_once '../../../frete/tw/consulta-frete-carrinho-include.php';
}
 
//SE FOR O FRETE GRÁTIS RETORNA ZERO SEMPRE
if($frete_tipo_frete == 'Grátis'){
    $valor_frete = '0.00';
}

//SE FOR O FRETE MOTOBOY CONSULTA NO BANCO E RETORNA
if($frete_tipo_frete == 'Motoboy'){
    $valor_frete = $frete['frete_motoboy_valor_entrega'];
}

//SE FOR O FRETE RETIRAR RETORNA ZERO SEMPRE
if($frete_tipo_frete == 'Retirar'){
    $valor_frete = '0.00';
}

$valor_frete = number_format($valor_frete, 2, '.', '');