<?php

setlocale(LC_MONETARY, 'pt_BR');

session_start();

//BUSCA O IDENTIFICADOR DO CARRINHO NA SESSION
$identificador_carrinho = filter_var($_SESSION['visitante']);
$id_produto             = filter_input(INPUT_POST, 'id-produto', FILTER_SANITIZE_NUMBER_INT);
$quantidade             = filter_input(INPUT_POST, 'quantidade', FILTER_SANITIZE_NUMBER_INT);
$cep_destinatario_aux   = filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_STRING);
$cep_destinatario       = str_replace('-','',str_replace('.','',$cep_destinatario_aux));

if(mb_strlen($cep_destinatario) == 8 & !empty($id_produto) & !empty($quantidade) & mb_strlen($identificador_carrinho) == 32){
  
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

    //BUSCA OS PRODUTO
    $busca_produto            = mysqli_query($conn, "SELECT p.id AS produto_id, p.nome AS produto_nome, p.sku AS produto_sku, p.altura AS produto_altura, p.largura AS produto_largura, p.comprimento AS produto_comprimento, p.peso AS produto_peso, p.preco AS produto_preco FROM produto AS p WHERE p.id = '".$id_produto."'");
    $produto                  = mysqli_fetch_array($busca_produto);

    //RECOLHE TODAS AS VARIÁVEIS
    $cep_remetente            = str_replace('-','',str_replace('.','',$frete['cep']));
    $nome_aplicacao           = $frete['melhor_envio_nome_aplicacao'];
    $email_aplicacao          = $frete['melhor_envio_email_aplicacao'];
    $token                    = $frete['melhor_envio_token'];

    //ATRIBUI AS VARIÁVEIS E TRANSFORMA SE PRECISO
    $produto_sku              = $produto['produto_sku'];
    $produto_largura          = $produto['produto_largura'];
    $produto_altura           = $produto['produto_altura'];
    $produto_comprimento      = $produto['produto_comprimento'];
    $produto_peso             = $produto['produto_peso']/1000;
    $produto_preco            = $produto['produto_preco'];
    $produto_quantidade       = $quantidade;

    //MONTA O OBJETO DO PRODUTO
    $produtos_melhor_envio = "
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

    $response = '';

    while($response == ''){

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

    } 

    curl_close($curl);

    $formas_envio = json_decode($response);

    $maior_prazo = 1;

    //INCREMENTA O ARRAY DE FRETES COM AS FORMAS DE ENTREGA DO MELHOR ENVIO
    foreach ($formas_envio as $key => $forma_envio) {
        if($forma_envio->error == '' & $forma_envio->company->name != null){
            $prazo_final = $forma_envio->custom_delivery_time+$frete['prazo_minimo'];
            $fretes[] = array(
                'empresa' => $forma_envio->company->name,
                'nome'    => $forma_envio->name,
                'preco'   => money_format('%(#10n', $forma_envio->custom_price),
                'prazo'   => $prazo_final
            );
            if($prazo_final > $maior_prazo){
                $maior_prazo = $prazo_final;
            }
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
        if($produto_preco >= $frete_gratis_valor_minimo){
            $frete_gratis_valor_status = true;
        }

        //SE ATENDE TODOS REQUISITOS, INCREMENTA NOS FRETES
        if($frete_gratis_estados_status & $frete_gratis_valor_status){     

            $fretes[] = array(
                'empresa' =>  '',
                'nome'    => 'Grátis',
                'preco'   => money_format('%(#10n', 0),
                'prazo'   => $maior_prazo
            );

        }

    }

    //SE ESTÁ ATIVO O FRETE TW    
    if($frete['tw'] == 1){
        include_once '../tw/consulta-frete-produto.php';
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
        if($produto_preco >= $frete_motoboy_valor_minimo){
            $frete_motoboy_valor_status = true;
        }

        //SE ATENDE TODOS REQUISITOS, INCREMENTA NOS FRETES
        if($frete_motoboy_cidades_status & $frete_motoboy_valor_status){                                
            $fretes[] = array(
                'empresa' =>  '',
                'nome'    => 'Motoboy',
                'preco'   => money_format('%(#10n', $frete_motoboy_valor_entrega),
                'prazo'   => $frete_motoboy_prazo
            );
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
                } else {
                    $prazo_final  = '';
                }

                $fretes[] = array(
                    'empresa' => '',
                    'nome'    => 'Retirar',
                    'preco'   => money_format('%(#10n', 0),
                    'prazo'   => $prazo_final
                );

            }

        } else {

            if($frete['prazo_minimo'] > 0){
                $prazo_final  = $frete['prazo_minimo'];
            } else {
                $prazo_final  = '';
            }

            $fretes[] = array(
                'empresa' => '',
                'nome'    => 'Retirar',
                'preco'   => money_format('%(#10n', 0),
                'prazo'   => $prazo_final
            );

        }

    }

    //VERIFICA SE ENCONTROU ALGUM FRETE PRA REGIÃO DO CLIENTE
    if(count($fretes) > 0){

        $retorno[] = array(
            'status'    => 'OK',
            'descricao' => 'Fretes encontrados.',
            'cidade'    => $cep_cidade,
            'uf'        => $cep_uf,
            'fretes'    => $fretes
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
            'status' => "ERRO"
        );
        echo json_encode($dados);
        
    } else {
        
        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='/';</script>";
        
    }

}