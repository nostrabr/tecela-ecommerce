<?php

setlocale(LC_MONETARY, 'pt_BR');

session_start();

//BUSCA O IDENTIFICADOR DO CARRINHO NA SESSION
$identificador_carrinho = filter_var($_SESSION['visitante']);
$cep_destinatario_aux   = filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_STRING);
$cep_destinatario       = str_replace('-','',str_replace('.','',$cep_destinatario_aux));

if(mb_strlen($cep_destinatario) == 8 & mb_strlen($identificador_carrinho) == 32){

    //BUSCA A LOCALIDADE PELO CEP
    $localizacao = simplexml_load_file("http://cep.republicavirtual.com.br/web_cep.php?formato=xml&cep=".$cep_destinatario);

    //SE ENCONTROU PROSSEGUE
    if($localizacao->resultado == 1 | $localizacao->resultado == 2){

        //ATRIBUI AS VARIAVEIS DE CIDADE E ESTADO
        $cep_uf     = strval($localizacao->uf);
        $cep_cidade = strval($localizacao->cidade);

    } else {
        $cep_uf     = '';
        $cep_cidade = $cep_destinatario_aux;
    }

    include_once '../../../bd/conecta.php';

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

    $maior_prazo           = 1;
    $menor_valor           = 999999;
    $menor_valor_companhia = '';

    foreach ($formas_envio as $key => $forma_envio) {
        if($forma_envio->error == '' & $forma_envio->company->name != null){
            $prazo_final = $forma_envio->custom_delivery_time+$frete['prazo_minimo'];
            $fretes[] = array(
                'empresa'      => $forma_envio->company->name,
                'nome'         => $forma_envio->name,
                'preco'        => $forma_envio->custom_price,
                'preco_brl'    => money_format('%(#10n', $forma_envio->custom_price),
                'prazo'        => $prazo_final,
                'data_entrega' => date('d/m/Y', strtotime('+'.$prazo_final.' day'))
            );
            if($prazo_final > $maior_prazo){
                $maior_prazo = $prazo_final;
            }
            if($forma_envio->custom_price < $menor_valor){
                $menor_valor = $forma_envio->custom_price;
                $menor_valor_companhia = $forma_envio->company->name;
            }
        }
    }

    //SE ESTÁ ATIVO O FRETE TW    
    if($frete['tw'] == 1){

        include_once '../tw/consulta-frete-carrinho.php';
        
        if($frete_tw != 0 & ($frete_tw < $menor_valor)){
            $menor_valor = $frete_tw;
            $menor_valor_companhia = 'TW';
        }

    }

    //SE ESTÁ ATIVO O FRETE GRATIS
    if($frete['frete_gratis'] == 1){

        //VARIÁVEIS
        $frete_gratis_valor_minimo   = $frete['frete_gratis_valor_minimo'];
        $frete_gratis_estados        = explode(',',$frete['frete_gratis_estados']);
        $frete_gratis_valor_status   = false;
        $frete_gratis_estados_status = false;            

        //BUSCA O ID DO ESTADO DO CEP DO CLIENTE
        $busca_uf = mysqli_query($conn, "SELECT id FROM estado WHERE sigla = '$cep_uf'");
        $uf       = mysqli_fetch_array($busca_uf);
        
        //VERIFICA SE O ESTADO DO CEP DO CLIENTE É ATENDIDO PARA FRETE GRÁTIS
        if(in_array($uf['id'],$frete_gratis_estados)){
            $frete_gratis_estados_status = true;
        }

        //VERIFICA SE O VALOR PRO PRODUTO É MAIOR OU IGUAL AO VALOR MÍNIMO
        if($valor_total_produtos >= $frete_gratis_valor_minimo){
            $frete_gratis_valor_status = true;
        }

        //SE ATENDE TODOS REQUISITOS, INCREMENTA NOS FRETES
        if($frete_gratis_estados_status & $frete_gratis_valor_status){     

            $fretes[] = array(
                'empresa'      =>  '',
                'nome'         => 'Grátis',
                'preco'        => 0,
                'preco_brl'    => money_format('%(#10n', 0),
                'prazo'        => $maior_prazo,
                'data_entrega' => date('d/m/Y', strtotime('+'.$maior_prazo.' day'))
            );

            $menor_valor = 0;
            $menor_valor_companhia = 'Grátis';

        }

    }

    //SE ESTÁ ATIVO O FRETE GRATIS
    if($frete['frete_motoboy'] == 1){

        //VARIÁVEIS
        $frete_motoboy_valor_minimo   = $frete['frete_motoboy_valor_minimo'];
        $frete_motoboy_valor_entrega  = $frete['frete_motoboy_valor_entrega'];
        $frete_motoboy_prazo          = $frete['frete_motoboy_prazo']+$frete['prazo_minimo'];
        $frete_motoboy_cidades        = explode(',',$frete['frete_motoboy_cidades']);
        $frete_motoboy_valor_status   = false;
        $frete_motoboy_cidades_status = false;            

        //BUSCA A CIDADE DO CEP DO CLIENTE
        $busca_cidade = mysqli_query($conn, "SELECT id FROM cidade WHERE nome = '$cep_cidade'");
        $cidade       = mysqli_fetch_array($busca_cidade);
        
        //VERIFICA SE A CIDADE DO CEP DO CLIENTE É ATENDIDO PARA FRETE MOTOBOY
        if(in_array($cidade['id'],$frete_motoboy_cidades)){
            $frete_motoboy_cidades_status = true;
        }

        //VERIFICA SE O VALOR PRO PRODUTO É MAIOR OU IGUAL AO VALOR MÍNIMO
        if($valor_total_produtos >= $frete_motoboy_valor_minimo){
            $frete_motoboy_valor_status = true;
        }

        //SE ATENDE TODOS REQUISITOS, INCREMENTA NOS FRETES
        if($frete_motoboy_cidades_status & $frete_motoboy_valor_status){                                
            $fretes[] = array(
                'empresa'      =>  '',
                'nome'         => 'Motoboy',
                'preco'        => $frete_motoboy_valor_entrega,
                'preco_brl'    => money_format('%(#10n', $frete_motoboy_valor_entrega),
                'prazo'        => $frete_motoboy_prazo,
                'data_entrega' => date('d/m/Y', strtotime('+'.$frete_motoboy_prazo.' day'))
            );
            
            /*
            if($frete_motoboy_valor_entrega < $menor_valor){
                $menor_valor = $frete_motoboy_valor_entrega;
                $menor_valor_companhia = 'Motoboy';
            }*/

        }

    }

    //SE ESTÁ ATIVO O FRETE RETIRAR, INCREMENTA
    if($frete['frete_retirar'] == 1){
        
        if($frete['frete_retirar_cidades'] != ''){
                
            $frete_retirar_cidades = explode(',',$frete['frete_retirar_cidades']);
            $frete_retirar_cidades_status = false;   
            
            //BUSCA A CIDADE DO CEP DO CLIENTE
            $busca_cidade = mysqli_query($conn, "SELECT id FROM cidade WHERE nome = '$cep_cidade'");
            $cidade       = mysqli_fetch_array($busca_cidade);

            if(in_array($cidade['id'],$frete_retirar_cidades)){
                $frete_retirar_cidades_status = true;
            }

            //SE ATENDE TODOS REQUISITOS, INCREMENTA NOS FRETES
            if($frete_retirar_cidades_status){   

                if($frete['prazo_minimo'] > 0){
                    $prazo_final  = $frete['prazo_minimo'];
                    $data_entrega = date('d/m/Y', strtotime('+'.$prazo_final.' day'));
                } else {
                    $prazo_final  = '';
                    $data_entrega = '';
                }
                $fretes[] = array(
                    'empresa'      => '',
                    'nome'         => 'Retirar',
                    'preco'        => 0,
                    'preco_brl'    => money_format('%(#10n', 0),
                    'prazo'        => $prazo_final,
                    'data_entrega' => $data_entrega
                );

            }

        } else {

            if($frete['prazo_minimo'] > 0){
                $prazo_final  = $frete['prazo_minimo'];
                $data_entrega = date('d/m/Y', strtotime('+'.$prazo_final.' day'));
            } else {
                $prazo_final  = '';
                $data_entrega = '';
            }
            $fretes[] = array(
                'empresa'      => '',
                'nome'         => 'Retirar',
                'preco'        => 0,
                'preco_brl'    => money_format('%(#10n', 0),
                'prazo'        => $prazo_final,
                'data_entrega' => $data_entrega
            );
            
        }

    }    

    //VERIFICA SE ENCONTROU ALGUM FRETE PRA REGIÃO DO CLIENTE
    if(count($fretes) > 0){

        $retorno[] = array(
            'status'                => 'OK',
            'descricao'             => 'Fretes encontrados.',
            'cidade'                => $cep_cidade,
            'uf'                    => $cep_uf,
            'menor_valor'           => money_format('%(#10n', $menor_valor),
            'menor_valor_companhia' => $menor_valor_companhia,
            'valor_total'           => money_format('%(#10n', ($menor_valor+$valor_total_produtos)),
            'fretes'                => $fretes
        );

    } else {

        $retorno[] = array(
            'status'   => 'ERRO',
            'descricao' => 'Não foram encontradas formas de entrega para sua localidade.'
        );

    }
        
    echo json_encode($retorno);

    include_once '../../../bd/desconecta.php';

} else {

    //VERIFICA SE VEIO DO AJAX
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                
        //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
        $dados[] = array(
            "status" => "ERRO"
        );
        echo json_encode($dados);
        
    } else {
        
        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='/';</script>";
        
    }

}