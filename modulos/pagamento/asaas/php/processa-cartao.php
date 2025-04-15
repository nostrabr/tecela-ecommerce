<?php 

//INICIA A SESSION
session_start();

//CONECTA AO BANCO
include_once '../../../../bd/conecta.php';

//RECEBE OS DADOS
$tipo_frete             = trim(strip_tags(filter_input(INPUT_POST, 'tipo-frete', FILTER_SANITIZE_STRING)));
$endereco               = trim(strip_tags(filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING)));
$identificador_cupom    = trim(strip_tags(filter_input(INPUT_POST, 'cupom', FILTER_SANITIZE_STRING)));
$numero_cartao          = trim(strip_tags(filter_input(INPUT_POST, 'numero', FILTER_SANITIZE_STRING)));
$nome                   = trim(strip_tags(mb_convert_case(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING), MB_CASE_TITLE, 'UTF-8')));
$validade               = trim(strip_tags(filter_input(INPUT_POST, 'validade', FILTER_SANITIZE_STRING)));
$cvv                    = trim(strip_tags(filter_input(INPUT_POST, 'cvv', FILTER_SANITIZE_NUMBER_INT)));
$cpf                    = trim(strip_tags(filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING)));
$parcelas               = trim(strip_tags(filter_input(INPUT_POST, 'parcelas', FILTER_SANITIZE_NUMBER_INT)));
$identificador_carrinho = filter_var($_SESSION['visitante']);
$identificador_cliente  = filter_var($_SESSION['identificador']);

//VERIFICA SE TODOS OS DADOS OBRIGATÓRIOS VIERAM PREENCHIDOS
if(!empty($tipo_frete) & mb_strlen($endereco) == 32 & !empty($identificador_carrinho) & !empty($identificador_cliente) & mb_strlen($numero_cartao) == 19 & !empty($nome) & mb_strlen($validade) == 5 & mb_strlen($cvv) == 3 & mb_strlen($cpf) == 14 & !empty($parcelas)){

    //BUSCA CARRINHO
    $busca_carrinho = mysqli_query($conn, "SELECT id FROM carrinho WHERE identificador = '$identificador_carrinho'");
    $carrinho       = mysqli_fetch_array($busca_carrinho);

    if(mysqli_num_rows($busca_carrinho) > 0){

        //BUSCA PRODUTOS DO CARRINHO
        $busca_produtos_carrinho = mysqli_query($conn, "SELECT cp.*, p.nome FROM carrinho_produto AS cp INNER JOIN produto AS p ON cp.id_produto = p.id WHERE cp.status = 1 AND id_carrinho = ".$carrinho['id']);

        //BUSCA OS DADOS DA LOJA
        $busca_loja              = mysqli_query($conn, "SELECT * FROM loja WHERE id = 1");
        $loja                    = mysqli_fetch_array($busca_loja);

        //BUSCA CLIENTE
        $busca_cliente           = mysqli_query($conn, "SELECT * FROM cliente WHERE identificador = '$identificador_cliente'");
        $cliente                 = mysqli_fetch_array($busca_cliente);
        
        //BUSCA CLIENTE
        $busca_endereco          = mysqli_query($conn, "SELECT ce.*, ce.id AS id_endereco, cd.nome AS nome_cidade, e.sigla AS sigla_estado FROM cliente_endereco AS ce INNER JOIN cidade AS cd ON ce.cidade = cd.id INNER JOIN estado AS e ON ce.estado = e.id WHERE ce.identificador = '$endereco' AND ce.id_cliente = ".$cliente['id']);
        $endereco                = mysqli_fetch_array($busca_endereco);

        //SE O CUPOM FOI SETADO
        if($identificador_cupom != ''){

            //BUSCA CUPOM
            $busca_cupom         = mysqli_query($conn, "SELECT * FROM cupom WHERE identificador = '".$identificador_cupom."'");
            $cupom               = mysqli_fetch_array($busca_cupom);

            //SE ENCONTROU
            if(mysqli_num_rows($busca_cupom) > 0){
                $tem_cupom       = true;
            } else {
                $tem_cupom       = true;
            }

        } else {
            $tem_cupom           = false;
        }
        
        if(mysqli_num_rows($busca_produtos_carrinho) > 0 & mysqli_num_rows($busca_cliente) > 0 & mysqli_num_rows($busca_endereco) > 0){
            
            //FUNÇÃO PRA GERAR CÓDIGOS ALEATÓRIOS
            function geraHash($tamanho = 8, $minusculas = true, $maiusculas = true, $numeros = true, $simbolos = true){

                $lmin = 'abcdefghijklmnopqrstuvwxyz';
                $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $num = '1234567890';
                $simb = '!@#$%*-';
                $retorno = '';
                $caracteres = '';

                if ($minusculas) $caracteres .= $lmin;
                if ($maiusculas) $caracteres .= $lmai;
                if ($numeros) $caracteres .= $num;
                if ($simbolos) $caracteres .= $simb;

                $len = strlen($caracteres);
                for ($n = 1; $n <= $tamanho; $n++) {
                    $rand = mt_rand(1, $len);
                    $retorno .= $caracteres[$rand-1];
                }

                return $retorno;

            }

            //GERA UM IDENTIFICADOR PARA O PEDIDO
            $identificador_pedido = md5(date('Y-m-d H:i:s').$cliente['id'].$carrinho['id']);

            //GUARDA O ENDEREÇO POR EXTENSO DO LOCAL DA ENTREGA
            $endereco_extenso = $endereco['logradouro'].', '.$endereco['numero'];
            if($endereco['complemento'] != ''){ $endereco_extenso .= ' - '.$endereco['complemento']; }
            $endereco_extenso .= ' - '.$endereco['bairro'].'<br />';
            $endereco_extenso .= $endereco['nome_cidade'].'/'.$endereco['sigla_estado'];
            $endereco_extenso .= ' - '.$endereco['cep'].'<br />';
            if($endereco['referencia'] != ''){ $endereco_extenso .= '<br />'.$endereco['referencia']; }    

            //SE DEU TUDO CERTO, GERA UM PEDIDO
            mysqli_query($conn, "INSERT INTO pedido (identificador, id_cliente, id_carrinho, id_endereco, endereco) VALUES ('$identificador_pedido','".$cliente['id']."','".$carrinho['id']."','".$endereco['id_endereco']."','$endereco_extenso')");     
            $id_pedido = mysqli_insert_id($conn);         

            //GERA UM CÓDIGO DE REFERÊNCIA PARA O PEDIDO
            $codigo_pedido = geraHash(10,false,true,true,false);
            $codigo_pedido = $codigo_pedido.str_pad($id_pedido, 5,'0',STR_PAD_LEFT);

            //ALTERA O CÓDIGO NO PEDIDO
            mysqli_query($conn, "UPDATE pedido SET codigo = '$codigo_pedido' WHERE id = ".$id_pedido);

            //ESTANCIA VARIÁVEIS PARA BUSCAR O FRETE E JÁ CALCULA O VALOR TOTAL DA COMPRA
            $id_endereco       = $endereco['id_endereco'];
            $cep_destinatario  = $endereco['cep'];
            $frete_tipo_frete  = $tipo_frete;
            $contador_produtos = 1;
            $valor_total       = 0;
            while($produtos_carrinho = mysqli_fetch_array($busca_produtos_carrinho)){
                $valor_total = $valor_total + ($produtos_carrinho['quantidade']*$produtos_carrinho['preco']);
                $contador_produtos++;
            }
            
            //BUSCA O VALOR DO FRETE
            include_once '../../../frete/melhor-envio/consulta-frete-carrinho-include.php';
            
            //CALCULA O DESCONTO CASO TENHA
            if($tem_cupom){
                if($cupom['tipo'] == 'V'){
                    $valor_desconto                         = $cupom['valor'];
                } else if($cupom['tipo'] == 'P'){
                    $valor_desconto                         = $valor_total*$cupom['valor']/100;
                }
                if($valor_desconto > $valor_total){
                    $valor_desconto                         = $valor_total-1;
                }
                $valor_desconto = number_format(($valor_desconto*-1),2,'.','');   
            } else {
                            
                $faixas_desconto = mysqli_query($conn, "SELECT * FROM pagamento_faixa_desconto WHERE status = 1 AND tipo = 'CARTAO' AND $valor_total BETWEEN de AND ate ORDER BY porcentagem_desconto, valor_desconto DESC LIMIT 1");
                if(mysqli_num_rows($faixas_desconto) > 0){
                    $faixa_desconto = mysqli_fetch_array($faixas_desconto);
                    if($faixa_desconto['porcentagem_desconto'] != 0){
                        $valor_desconto       = $valor_total*$faixa_desconto['porcentagem_desconto']/100;
                    } else if($faixa_desconto['valor_desconto'] != 0) {
                        $valor_desconto       = $faixa_desconto['valor_desconto'];
                    }                       
                    if($valor_desconto > $valor_total){
                        $valor_desconto = $valor_total-1;
                    }
                    $valor_desconto = number_format(($valor_desconto*-1),2,'.','');  
                } else {
                    $valor_desconto = '0.00';
                }

            }
             
            //CALCULA O VALOR FINAL
            $valor_final = $valor_total+$valor_desconto+$valor_frete;
            
            //CADASTRA OU EDITA O CLIENTE NO ASAAS            
            $busca_pagamento  = mysqli_query($conn, "SELECT asaas_ambiente, asaas_token, asaas_parcelas, asaas_parcelas_juros, asaas_parcelas_juros_porcentagem, asaas_juros_tipo FROM pagamento WHERE id = 1");
            $pagamento         = mysqli_fetch_array($busca_pagamento);

            if($pagamento['asaas_parcelas'] >= $parcelas & $parcelas > 0){

                $asaas_token       = $pagamento['asaas_token'];         
                $url_asaas         = '';
                $url_asaas_cliente = '';
                $url_asaas_pedido  = '';
                $url_asaas_qrcode  = '';
                $client_id_asaas   = '';

                if($pagamento['asaas_ambiente'] == 'S'){
                    $url_asaas = 'https://sandbox.asaas.com/api/v3/';
                } else if($pagamento['asaas_ambiente'] == 'P') {
                    $url_asaas = 'https://api.asaas.com/v3/';
                }          

                if($cliente['asaas_id'] != ''){        
                    $url_asaas_cliente = $url_asaas."customers/".$cliente['asaas_id'];
                } else {        
                    $url_asaas_cliente = $url_asaas."customers";
                }

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $url_asaas_cliente);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);

                curl_setopt($ch, CURLOPT_POST, TRUE);

                curl_setopt($ch, CURLOPT_POSTFIELDS, "{
                \"name\": \"".$cliente['nome']." ".$cliente['sobrenome']."\",
                \"email\": \"".$cliente['email']."\",
                \"phone\": \"".preg_replace("/[^0-9]/", "",$cliente['telefone'])."\",
                \"mobilePhone\": \"".preg_replace("/[^0-9]/", "",$cliente['celular'])."\",
                \"cpfCnpj\": \"".preg_replace("/[^0-9]/", "",$cliente['cpf'])."\",
                \"postalCode\": \"".preg_replace("/[^0-9]/", "",$endereco['cep'])."\",
                \"address\": \"".$endereco['logradouro']."\",
                \"addressNumber\": \"".$endereco['numero']."\",
                \"complement\": \"".$endereco['complemento']."\",
                \"province\": \"".$endereco['bairro']."\",
                \"externalReference\": \"".$cliente['identificador']."\",
                \"notificationDisabled\": true,
                \"additionalEmails\": \"\",
                \"municipalInscription\": \"\",
                \"stateInscription\": \"\",
                \"observations\": \"\"
                }");

                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                "access_token: $asaas_token"
                ));

                $response = curl_exec($ch);
                curl_close($ch);
                
                $cliente_retorno = json_decode($response,true);
                
                if($cliente['asaas_id'] != ''){        
                    $client_id_asaas = $cliente['asaas_id'];
                } else {      
                    $client_id_asaas = $cliente_retorno['id'];
                    mysqli_query($conn, "UPDATE cliente SET asaas_id = '$client_id_asaas' WHERE identificador = '$identificador_cliente'");
                }

                if($parcelas <= $pagamento['asaas_parcelas']){

                    if($pagamento['asaas_parcelas_juros'] > 0 & $parcelas < $pagamento['asaas_parcelas_juros']){      
                        $valor_parcela = $valor_final/$parcelas;
                    } else {
                        if($pagamento['asaas_juros_tipo'] == 1){
                            $valor_parcela = $valor_final/$parcelas+($valor_final/$parcelas*$pagamento['asaas_parcelas_juros_porcentagem']/100);
                        } else {                                                                        
                            $mont = $valor_final * pow( (1 + ($pagamento['asaas_parcelas_juros_porcentagem'] / 100)) , $parcelas );
                            $valor_parcela = ($mont / $parcelas);
                        }
                    }

                    $valor_parcela = number_format($valor_parcela,2,'.','');
                                        
                    //CADASTRA O PEDIDO NO ASAAS            
                    $url_asaas_pedido = $url_asaas."payments";

                    //SEPARA A VALIDADE
                    $validade = explode('/',$validade);

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, $url_asaas_pedido);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_HEADER, FALSE);

                    curl_setopt($ch, CURLOPT_POST, TRUE);

                    curl_setopt($ch, CURLOPT_POSTFIELDS, "{
                    \"customer\": \"$client_id_asaas\",
                    \"billingType\": \"CREDIT_CARD\",
                    \"installmentCount\": $parcelas,
                    \"installmentValue\": $valor_parcela,
                    \"dueDate\": \"".date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-d'))))."\",
                    \"description\": \"PEDIDO: ".$codigo_pedido." - ".$loja['site']."pedido/".$codigo_pedido."\",
                    \"externalReference\": \"$id_pedido\",
                    \"postalService\": false,
                    \"creditCard\": {
                      \"holderName\": \"".strtolower($nome)."\",
                      \"number\": \"".preg_replace("/[^0-9]/", "",$numero_cartao)."\",
                      \"expiryMonth\": \"".$validade[0]."\",
                      \"expiryYear\": \"20".$validade[1]."\",
                      \"ccv\": \"".$cvv."\"
                    },
                    \"creditCardHolderInfo\": {
                      \"name\": \"".$nome."\",
                      \"email\": \"".$cliente['email']."\",
                      \"cpfCnpj\": \"".preg_replace("/[^0-9]/", "",$cliente['cpf'])."\",
                      \"postalCode\": \"".str_replace(".", "",$endereco['cep'])."\",
                      \"addressNumber\": \"".$endereco['numero']."\",
                      \"addressComplement\": null,
                      \"phone\": \"".preg_replace("/[^0-9]/", "",$cliente['telefone'])."\",
                      \"mobilePhone\": \"".preg_replace("/[^0-9]/", "",$cliente['celular'])."\"
                    }
                    }");

                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    "Content-Type: application/json",
                    "access_token: $asaas_token"
                    ));

                    $response = curl_exec($ch);
                    curl_close($ch);

                    $pedido_retorno = json_decode($response,true);
                    
                    $pedido_id_asaas           = $pedido_retorno['id'];
                    $pedido_url                = $pedido_retorno['invoiceUrl'];
                    $pedido_url_recibo         = $pedido_retorno['transactionReceiptUrl'];
                    $pedido_status             = $pedido_retorno['status'];
                    $pedido_errors             = $pedido_retorno['errors'];
                    $pedido_errors_description = $pedido_retorno['errors'][0]['description'];
                    
                    if(!isset($pedido_errors)){    

                        //SE RETORNAR O PEDIDO
                        if(!empty($pedido_id_asaas)){
                                                
                            //INSERE O USO DO CUPOM NO BANCO CASO TENHA
                            if($tem_cupom){
                                mysqli_query($conn, "INSERT INTO cupom_uso (id_cupom, id_cliente, id_pedido) VALUES ('".$cupom['id']."','".$cliente['id']."','$id_pedido')");
                            }
                            
                            //DEIXA O VALOR DO DESCONTO POSITIVO
                            if($valor_desconto != '0.00'){ $valor_desconto = $valor_desconto*-1; }

                            //CALCULA O VALOR DO JUROS
                            $valor_juros = round(($parcelas*$valor_parcela)-($valor_total+$valor_frete)+$valor_desconto,2);

                            if($valor_juros < 0.04){
                                $valor_juros = 0;
                            }

                            //GERAL UM IDENTIFICADOR DO PAGAMENTO PAGSEGUO
                            $identificador_pagamento_cc = md5(date('Y-m-d H:i:s').$id_pedido.$codigo_pedido);
                            
                            //INSERE NO BANCO O HASH DE REFERENCIA DO PAGSEGURO E O LINK DO BOLETO
                            mysqli_query($conn, "INSERT INTO pagamento_pagseguro (identificador, id_pedido, codigo, tipo, parcelas, valor_parcela, valor_produtos, valor_desconto, valor_juros, valor_frete, tipo_frete, asaas, asaas_link_fatura, comprovante_pagamento, comprovante_pagamento_por) VALUES ('$identificador_pagamento_cc','$id_pedido','$pedido_id_asaas','CARTAO','$parcelas','$valor_parcela','$valor_total','$valor_desconto','$valor_juros','$valor_frete','$tipo_frete',1,'$pedido_url','$pedido_url_recibo','ASAAS')");
                            
                            //ALTERA O STATUS DO CARRINHO
                            mysqli_query($conn, "UPDATE carrinho SET status = 1 WHERE id = ".$carrinho['id']);
                                                        
                            //ALTERA O STATUS DO PEDIDO PARA AGUARDANDO PAGAMENTO
                            mysqli_query($conn, "UPDATE pedido SET status = 1 WHERE id = ".$id_pedido);

                            //ALTERA A SESSION DO VISITANTE PARA NÃO GERAR CARRINHOS IGUAIS                
                            $visitante = md5(date("Y-m-d H:i:s").filter_input(INPUT_SERVER, "REMOTE_ADDR", FILTER_DEFAULT).filter_input(INPUT_SERVER, "HTTP_USER_AGENT", FILTER_DEFAULT));
                            setcookie("visitante", $visitante, time()+(3600*24*30*12*5), "/");
                            $_SESSION['visitante'] = $visitante;
                            
                            //PREENCHE AS VARIÁVEIS DO FORM QUE VAI PRA TELA DE CONFIRMAÇÃO
                            $status_processo   = "SUCESSO";
                            $mensagem_processo = "";

                        } else {                            
                            
                            //DEIXA O VALOR DO DESCONTO POSITIVO
                            if($valor_desconto != '0.00'){ $valor_desconto = $valor_desconto*-1; }

                            //CALCULA O VALOR DO JUROS
                            $valor_juros = round(($parcelas*$valor_parcela)-($valor_total+$valor_frete)+$valor_desconto,2);

                            if($valor_juros < 0.04){
                                $valor_juros = 0;
                            }

                            //GERAL UM IDENTIFICADOR DO PAGAMENTO PAGSEGUO
                            $identificador_pagamento_cc = md5(date('Y-m-d H:i:s').$id_pedido.$codigo_pedido);
                            
                            //INSERE NO BANCO O HASH DE REFERENCIA DO PAGSEGURO E O LINK DO BOLETO
                            mysqli_query($conn, "INSERT INTO pagamento_pagseguro (identificador, id_pedido, codigo, tipo, parcelas, valor_parcela, valor_produtos, valor_desconto, valor_juros, valor_frete, tipo_frete, asaas, asaas_link_fatura, asaas_erro, comprovante_pagamento, comprovante_pagamento_por) VALUES ('$identificador_pagamento_cc','$id_pedido','$pedido_id_asaas','CARTAO','$parcelas','$valor_parcela','$valor_total','$valor_desconto','$valor_juros','$valor_frete','$tipo_frete',1,'$pedido_url','$pedido_errors_description','$pedido_url_recibo','ASAAS')");
                            
                            //ALTERA O STATUS DO PEDIDO PARA AGUARDANDO PAGAMENTO
                            mysqli_query($conn, "UPDATE pedido SET status = 10 WHERE id = ".$id_pedido);
                        
                            //PREENCHE AS VARIÁVEIS DO FORM QUE VAI PRA TELA DE CONFIRMAÇÃO
                            $status_processo   = "ERRO PARCIAL";
                            $mensagem_processo = "Erro ao processar dados 6.";
                
                        }

                    } else {
                                                        
                        //DEIXA O VALOR DO DESCONTO POSITIVO
                        if($valor_desconto != '0.00'){ $valor_desconto = $valor_desconto*-1; }

                        //CALCULA O VALOR DO JUROS
                        $valor_juros = round(($parcelas*$valor_parcela)-($valor_total+$valor_frete)+$valor_desconto,2);

                        if($valor_juros < 0.04){
                            $valor_juros = 0;
                        }

                        //GERAL UM IDENTIFICADOR DO PAGAMENTO PAGSEGUO
                        $identificador_pagamento_cc = md5(date('Y-m-d H:i:s').$id_pedido.$codigo_pedido);
                        
                        //INSERE NO BANCO O HASH DE REFERENCIA DO PAGSEGURO E O LINK DO BOLETO
                        mysqli_query($conn, "INSERT INTO pagamento_pagseguro (identificador, id_pedido, codigo, tipo, parcelas, valor_parcela, valor_produtos, valor_desconto, valor_juros, valor_frete, tipo_frete, asaas, asaas_link_fatura, asaas_erro, comprovante_pagamento, comprovante_pagamento_por) VALUES ('$identificador_pagamento_cc','$id_pedido','$pedido_id_asaas','CARTAO','$parcelas','$valor_parcela','$valor_total','$valor_desconto','$valor_juros','$valor_frete','$tipo_frete',1,'$pedido_url','$pedido_errors_description','$pedido_url_recibo','ASAAS')");
                                                
                        //ALTERA O STATUS DO PEDIDO PARA NÃO APROVADA
                        mysqli_query($conn, "UPDATE pedido SET status = 10 WHERE id = ".$id_pedido);
                        
                        //PREENCHE AS VARIÁVEIS DO FORM QUE VAI PRA TELA DE CONFIRMAÇÃO
                        $status_processo   = "ERRO PARCIAL";
                        $mensagem_processo = 'PEDIDO NÃO APROVADO: '.$pedido_errors_description;
            
                    }

                } else {
                
                    //PREENCHE AS VARIÁVEIS DO FORM QUE VAI PRA TELA DE CONFIRMAÇÃO
                    $status_processo   = "ERRO";
                    $mensagem_processo = "Erro ao processar dados 5.";
        
                }

            } else {
                
                //PREENCHE AS VARIÁVEIS DO FORM QUE VAI PRA TELA DE CONFIRMAÇÃO
                $status_processo   = "ERRO";
                $mensagem_processo = "Erro ao processar dados 4.";

            }

        } else {
            
            //PREENCHE AS VARIÁVEIS DO FORM QUE VAI PRA TELA DE CONFIRMAÇÃO
            $status_processo   = "ERRO";
            $mensagem_processo = "Erro ao processar dados 3.";

        }

    } else {
            
        //PREENCHE AS VARIÁVEIS DO FORM QUE VAI PRA TELA DE CONFIRMAÇÃO
        $status_processo   = "ERRO";
        $mensagem_processo = "Erro ao processar dados 2.";
      
    }
    
} else {
            
    //PREENCHE AS VARIÁVEIS DO FORM QUE VAI PRA TELA DE CONFIRMAÇÃO
    $status_processo   = "ERRO";
    $mensagem_processo = "Erro ao processar dados 1.";

}

include_once '../../../../bd/desconecta.php';

//SE NÃO GEROU NENHUM ERRO PASSA PELO ENVIO DE E-MAIL E DEPOIS VAI PRA CONFIRMAÇÃO
if($status_processo == 'SUCESSO'){ 

    ?> 
    <form id="form-pagamento-cc-asaas" style="display: none;" action="../../../envio-email/index.php" method="POST">
        <input type="hidden" name="tipo-envio" value="formulario-pagamento-cc-asaas">
        <input type="hidden" name="pedido" value="<?= $codigo_pedido ?>">
        <input type="hidden" name="cliente" value="<?= $cliente['nome'] ?>">
        <input type="hidden" name="email" value="<?= $cliente['email'] ?>">
    </form>
    <?php

//SE GEROU ERRO, VAI DIRETO PARA A CONFIRMAÇÃO EXIBIR O PROBLEMA
} else if($status_processo == 'ERRO PARCIAL'){   

    ?> 
    <form id="form-pagamento-cc-asaas" style="display: none;" action="../../../envio-email/index.php" method="POST">
        <input type="hidden" name="tipo-envio" value="formulario-pagamento-cc-asaas-nao-autorizado">
        <input type="hidden" name="pedido" value="<?= $codigo_pedido ?>">
        <input type="hidden" name="cliente" value="<?= $cliente['nome'] ?>">
        <input type="hidden" name="email" value="<?= $cliente['email'] ?>">
        <input type="hidden" name="erro" value="<?= $mensagem_processo ?>">
    </form>
    <?php

} else if($status_processo == 'ERRO'){   

    ?>
    <form id="form-pagamento-cc-asaas" style="display: none;" action="../../../../carrinho-confirmacao" method="POST">
        <input type="hidden" name="status" value="<?= $status_processo ?>">
        <input type="hidden" name="mensagem" value="<?= $mensagem_processo ?>">
    </form>
    <?php

}

//SUBMIT NO FORM DE CONTINUAÇÃO
echo "<script>document.getElementById('form-pagamento-cc-asaas').submit();</script>";