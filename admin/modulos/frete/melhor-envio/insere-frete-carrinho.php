<?php

if(mb_strlen($identificador_pedido) == 32){
    
    //BUSCA OS DADOS DA LOJA
    $busca_loja                 = mysqli_query($conn, "SELECT l.nome AS loja_nome, l.whatsapp AS loja_whatsapp, l.email AS loja_email, l.cpf_cnpj AS loja_cpf_cnpj, l.rua AS loja_rua, l.numero AS loja_numero, l.complemento AS loja_complemento, l.bairro AS loja_bairro, l.cep AS loja_cep, c.nome AS loja_cidade FROM loja AS l INNER JOIN cidade as c ON l.cidade = c.id WHERE l.id = 1");
    $loja                       = mysqli_fetch_array($busca_loja);

    //BUSCA OS DADOS DE FRETE DA LOJA
    $busca_dados_melhor_envio   = mysqli_query($conn, "SELECT melhor_envio_token, melhor_envio_nome_aplicacao, melhor_envio_email_aplicacao, melhor_envio_ambiente FROM frete WHERE id = 1");
    $melhor_envio               = mysqli_fetch_array($busca_dados_melhor_envio);
                
    //BUSCA O PEDIDO
    $busca_pedido               = mysqli_query($conn, "SELECT id, id_cliente, id_carrinho, codigo FROM pedido WHERE identificador = '".$identificador_pedido."'");
    $pedido                     = mysqli_fetch_array($busca_pedido);

    //BUSCA A COTAÇÃO DE FRETE DO PEDIDO
    $busca_cotacao_frete        = mysqli_query($conn, "SELECT id, id_frete_transportadora_servico, id_cliente_endereco, funcao_aviso_recebimento, funcao_maos_proprias, funcao_coleta FROM pedido_frete WHERE id_pedido = ".$pedido['id']);
    $cotacao_frete              = mysqli_fetch_array($busca_cotacao_frete);
    
    //BUSCA OS PACOTES DO PEDIDO DA COTAÇÃO DO FRETE
    $busca_cotacao_frete_pacote = mysqli_query($conn, "SELECT id, altura, largura, comprimento, peso, valor_seguro FROM pedido_frete_pacote WHERE id_pedido_frete = ".$cotacao_frete['id']);

    //BUSCA OS DADOS DO CLIENTE
    $busca_cliente              = mysqli_query($conn, "SELECT nome, sobrenome, celular, email, cpf FROM cliente WHERE id = ".$pedido['id_cliente']);
    $cliente                    = mysqli_fetch_array($busca_cliente);

    //BUSCA ENDERECO DO CLIENTE NA COTAÇÃO DE FRETE DO PEDIDO
    $busca_endereco_cliente     = mysqli_query($conn, "SELECT ce.logradouro AS cliente_rua, ce.numero AS cliente_numero, ce.complemento AS cliente_complemento, ce.bairro AS cliente_bairro, ce.cep AS cliente_cep, c.nome AS cliente_cidade, e.sigla AS cliente_uf FROM cliente_endereco AS ce INNER JOIN cidade AS c ON ce.cidade = c.id INNER JOIN estado AS e ON ce.estado = e.id WHERE ce.id = ".$cotacao_frete['id_cliente_endereco']);
    $endereco_cliente           = mysqli_fetch_array($busca_endereco_cliente);
                
    //VARIÁVEIS
    $loja_nome                       = $loja['loja_nome'];
    $loja_telefone                   = preg_replace("/[^a-zA-Z0-9]/", "", $loja['loja_whatsapp']);
    $loja_email                      = $loja['loja_email'];
    $loja_cpf_cnpj                   = $loja['loja_cpf_cnpj'];
    $loja_rua                        = $loja['loja_rua'];
    $loja_numero                     = $loja['loja_numero'];
    $loja_complemento                = $loja['loja_complemento'];
    $loja_bairro                     = $loja['loja_bairro'];
    $loja_cidade                     = $loja['loja_cidade'];
    $loja_cep                        = preg_replace("/[^a-zA-Z0-9]/", "", $loja['loja_cep']);
    $cliente_nome                    = $cliente['nome'].' '.$cliente['sobrenome'];
    $cliente_telefone                = preg_replace("/[^a-zA-Z0-9]/", "", $cliente['celular']);
    $cliente_email                   = $cliente['email'];
    $cliente_cpf                     = preg_replace("/[^a-zA-Z0-9]/", "", $cliente['cpf']);
    if(strlen($cliente_cpf) == 11){
        $cliente_cnpj = '';
    } else if(strlen($cliente_cpf) == 14) {
        $cliente_cnpj = $cliente_cpf;
        $cliente_cpf = '';
    }
    $cliente_rua                     = $endereco_cliente['cliente_rua'];
    $cliente_numero                  = $endereco_cliente['cliente_numero'];
    $cliente_complemento             = $endereco_cliente['cliente_complemento'];
    $cliente_bairro                  = $endereco_cliente['cliente_bairro'];
    $cliente_cidade                  = $endereco_cliente['cliente_cidade'];
    $cliente_uf                      = $endereco_cliente['cliente_uf'];
    $cliente_cep                     = preg_replace("/[^a-zA-Z0-9]/", "", $endereco_cliente['cliente_cep']);
    $token_aplicacao                 = $melhor_envio['melhor_envio_token'];
    $nome_aplicacao                  = $melhor_envio['melhor_envio_nome_aplicacao'];
    $email_aplicacao                 = $melhor_envio['melhor_envio_email_aplicacao'];
    $id_frete_transportadora_servico = $cotacao_frete['id_frete_transportadora_servico'];

    if($melhor_envio['melhor_envio_ambiente'] == 'S'){
        $url = "https://sandbox.melhorenvio.com.br/api/v2/me/cart";
    } else {
        $url = "https://melhorenvio.com.br/api/v2/me/cart";
    }

    //VERIFICA SE É CPF OU CNPJ DA LOJA
    if(count($loja_cpf_cnpj) == 14){
        $loja_cpf = $loja_cpf_cnpj;
        $loja_cnpj = '';
    } else {
        $loja_cpf = '';
        $loja_cnpj = $loja_cpf_cnpj;
    }
                    
    while($pacote = mysqli_fetch_array($busca_cotacao_frete_pacote)){

        $$dados_melhor_envio = "";

        //PREENCHE A VARIÁVEL DE DADOS DO MELHOR ENVIO COM OS DADOS DO REMETENTE E DESTINATÁRIO
        $dados_melhor_envio = "
        {
            \n \"service\": $id_frete_transportadora_servico,
            \n \"from\": {
                \n \"name\": \"$loja_nome\",
                \n \"phone\": \"$loja_telefone\",
                \n \"email\": \"$loja_email\",
                \n \"document\": \"$loja_cpf\",
                \n \"company_document\": \"$loja_cnpj\",
                \n \"state_register\": \"\",
                \n \"address\": \"$loja_rua\",
                \n \"complement\": \"$loja_complemento\",
                \n \"number\": \"$loja_numero\",
                \n \"district\": \"$loja_bairro\",
                \n \"city\": \"$loja_cidade\",
                \n \"country_id\": \"BR\",
                \n \"postal_code\": \"$loja_cep\",
                \n \"note\": \"\"
            \n },
            \n \"to\": {
                \n \"name\": \"$cliente_nome\",
                \n \"phone\": \"$cliente_telefone\",
                \n \"email\": \"$cliente_email\",
                \n \"document\": \"$cliente_cpf\",
                \n \"company_document\": \"$cliente_cnpj\",
                \n \"state_register\": \"\",
                \n \"address\": \"$cliente_rua\",
                \n \"complement\": \"$cliente_complemento\",
                \n \"number\": \"$cliente_numero\",
                \n \"district\": \"$cliente_bairro\",
                \n \"city\": \"$cliente_cidade\",
                \n \"state_abbr\": \"$cliente_uf\",
                \n \"country_id\": \"BR\",
                \n \"postal_code\": \"$cliente_cep\",
                \n \"note\": \"\"
            \n },
        ";

        //PREENCHE OS PRODUTOS
        $dados_melhor_envio .= "\n \"products\": [";
        
        //BUSCA OS PRODUTOS
        $busca_produtos = mysqli_query($conn, "SELECT pfcp.produto_quantidade AS produto_quantidade, p.nome AS produto_nome, (SELECT cp.preco FROM carrinho_produto AS cp WHERE cp.id_carrinho = ".$pedido['id_carrinho']." AND cp.id_produto = p.id LIMIT 1) AS produto_preco FROM pedido_frete_pacote_produto AS pfcp LEFT JOIN produto AS p ON p.sku = pfcp.produto_sku WHERE pfcp.id_pedido_frete_pacote = ".$pacote['id']);
        
        $total_produtos    = mysqli_num_rows($busca_produtos);
        $contador_produtos = 0;

        while($produto = mysqli_fetch_array($busca_produtos)){
            
            $contador_produtos++;
                
            $produto_nome       = $produto['produto_nome'];
            $produto_quantidade = $produto['produto_quantidade'];
            $produto_preco      = $produto['produto_preco'];

            $dados_melhor_envio .= "
                \n {
                    \n \"name\": \"$produto_nome\",
                    \n \"quantity\": $produto_quantidade,
                    \n \"unitary_value\": $produto_preco
                \n }
            ";

            if($contador_produtos != $total_produtos){
                $dados_melhor_envio .= ", \n";
            }

        }
                
        $dados_melhor_envio .= "\n ], ";

        //PREENCHE OS PACOTES
        $dados_melhor_envio .= "\n \"volumes\": [";

        $pacote_altura      = $pacote['altura'];
        $pacote_largura     = $pacote['largura'];
        $pacote_comprimento = $pacote['comprimento'];
        $pacote_peso        = $pacote['peso'];
        $total_seguro       = $pacote['valor_seguro'];

        $dados_melhor_envio .= "
            \n {
                \n \"height\": $pacote_altura,
                \n \"width\": $pacote_largura,
                \n \"length\": $pacote_comprimento,
                \n \"weight\": $pacote_peso
            \n }
        ";
                
        $dados_melhor_envio .= " \n ], ";

        //PREECHE AS OPÇÕES
        $funcao_aviso_recebimento = $cotacao_frete['funcao_aviso_recebimento'];
        $funcao_maos_proprias     = $cotacao_frete['funcao_maos_proprias'];
        $funcao_coleta            = $cotacao_frete['funcao_coleta'];
        $pedido_codigo            = $pedido['codigo'];

        if($funcao_aviso_recebimento == 1){
            $funcao_aviso_recebimento = "true";
        } else {
            $funcao_aviso_recebimento = "false";
        }

        if($funcao_maos_proprias == 1){
            $funcao_maos_proprias = "true";
        } else {
            $funcao_maos_proprias = "false";
        }

        if($funcao_coleta == 1){
            $funcao_coleta = "true";
        } else {
            $funcao_coleta = "false";
        }
        
        $dados_melhor_envio .= "
            \n \"options\": {
                \n \"insurance_value\": $total_seguro,
                \n \"receipt\": $funcao_aviso_recebimento,
                \n \"own_hand\": $funcao_maos_proprias,
                \n \"reverse\": $funcao_coleta,
                \n \"non_commercial\": true,
                \n \"invoice\": {
                    \n \"key\": \" \"\n        
                },
                \n \"platform\": \"Conecta Shop\",
                \n \"tags\": [
                    \n {
                        \n \"tag\": \"$pedido_codigo\",
                        \n \"url\": \"null\"
                    \n }
                \n ]
            \n }
        \n }
        ";
        
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
            CURLOPT_POSTFIELDS => $dados_melhor_envio,
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Content-Type: application/json",
                "Authorization: Bearer ".$token_aplicacao."",
                "User-Agent: ".$nome_aplicacao." (".$email_aplicacao.")"
            ),
        ));

        $response = curl_exec($curl);
        
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($httpCode >= 200 && $httpCode < 300) {

            $etiqueta = json_decode($response);
    
            //PEGA AS INFORMAÇÕES
            $etiqueta_id        = $etiqueta->id;
            $etiqueta_protocolo = $etiqueta->protocol;
    
            //ALTERA O PACOTE NO BANCO COM O ID E CÓDIGO DA ETIQUETA GERADOS
            mysqli_query($conn, "UPDATE pedido_frete_pacote SET melhor_envio_id_etiqueta = '$etiqueta_id', melhor_envio_codigo_envio = '$etiqueta_protocolo', status = 1 WHERE id = ".$pacote['id']);
            
        } else {

            $responseData = json_decode($response, true);
        
            if ($responseData && isset($responseData['error'])) {
                echo "Erro ao adicionar ao carrinho para o pedido ".$pedido_codigo.": " . $responseData['error'] . "\n";
            } else {
                echo "Erro ao adicionar ao carrinho para o pedido ".$pedido_codigo.": Detalhes do erro indisponíveis.\n";
            }

        }

    }

} else {

    //REDIRECIONA PARA A TELA DE LOGIN
    echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";
        
}