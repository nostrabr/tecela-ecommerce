<?php 

//INICIA A SESSION
session_start();

//CONECTA AO BANCO
include_once '../../../../bd/conecta.php';

//INCLUIR A CONFIGURAÇÃO
include './configuracao.php';

//RECEBE OS DADOS
$tipo_frete             = trim(strip_tags(filter_input(INPUT_POST, 'tipo-frete', FILTER_SANITIZE_STRING)));
$endereco               = trim(strip_tags(filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING)));
$identificador_cupom    = trim(strip_tags(filter_input(INPUT_POST, 'cupom', FILTER_SANITIZE_STRING)));
$hash_comprador         = trim(strip_tags(filter_input(INPUT_POST, 'hash-comprador-cartao', FILTER_SANITIZE_STRING)));
$token_cartao           = trim(strip_tags(filter_input(INPUT_POST, 'token-cartao', FILTER_SANITIZE_STRING)));
$numero_cartao          = trim(strip_tags(filter_input(INPUT_POST, 'numero', FILTER_SANITIZE_STRING)));
$nome                   = trim(strip_tags(mb_convert_case(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING), MB_CASE_TITLE, 'UTF-8')));
$validade               = trim(strip_tags(filter_input(INPUT_POST, 'validade', FILTER_SANITIZE_STRING)));
$cvv                    = trim(strip_tags(filter_input(INPUT_POST, 'cvv', FILTER_SANITIZE_NUMBER_INT)));
$cpf                    = trim(strip_tags(filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING)));
$nascimento             = trim(strip_tags(filter_input(INPUT_POST, 'nascimento', FILTER_SANITIZE_STRING)));
$parcelas               = trim(strip_tags(filter_input(INPUT_POST, 'parcelas', FILTER_SANITIZE_NUMBER_INT)));
$valor_parcela          = trim(strip_tags(filter_input(INPUT_POST, 'valor-parcela')));
$identificador_carrinho = filter_var($_SESSION['visitante']);
$identificador_cliente  = filter_var($_SESSION['identificador']);

//VERIFICA SE TODOS OS DADOS OBRIGATÓRIOS VIERAM PREENCHIDOS
if(!empty($tipo_frete) & mb_strlen($endereco) == 32 & !empty($hash_comprador) & !empty($identificador_carrinho) & !empty($identificador_cliente) & !empty($token_cartao) & mb_strlen($numero_cartao) == 19 & !empty($nome) & mb_strlen($validade) == 5 & mb_strlen($cvv) == 3 & mb_strlen($cpf) == 14 & mb_strlen($nascimento) == 10 & !empty($parcelas) & !empty($valor_parcela)){

    //BUSCA CARRINHO
    $busca_carrinho = mysqli_query($conn, "SELECT id FROM carrinho WHERE identificador = '$identificador_carrinho'");
    $carrinho       = mysqli_fetch_array($busca_carrinho);

    if(mysqli_num_rows($busca_carrinho) > 0){

        //BUSCA PRODUTOS DO CARRINHO
        $busca_produtos_carrinho = mysqli_query($conn, "SELECT cp.*, p.nome FROM carrinho_produto AS cp INNER JOIN produto AS p ON cp.id_produto = p.id WHERE cp.status = 1 AND id_carrinho = ".$carrinho['id']);

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
            
            //PREENCHE O ARRAY DE DADOS PARA O PAGSEGURO
            $pag_seguro['paymentMode']                      = 'DEFAULT';
            $pag_seguro["email"]                            = EMAIL_PAGSEGURO;
            $pag_seguro["token"]                            = TOKEN_PAGSEGURO;
            $pag_seguro['receiverEmail']                    = EMAIL_SUPORTE;
            $pag_seguro['currency']                         = MOEDA_PAGAMENTO;
            $pag_seguro['notificationURL']                  = URL_NOTIFICACAO;
            
            //TIPO DE PAGAMENTO
            $pag_seguro['paymentMethod']                    = 'creditCard';
            $pag_seguro['senderHash']                       = $hash_comprador;                                    
            $pag_seguro['creditCardToken']                  = $token_cartao;

            //TRATA O CELULAR
            $telefone                                       = preg_replace('/\D/', '', $cliente['celular']);
            $telefone_ddd                                   = substr($telefone, 0, 2);
            $telefone_numero                                = substr($telefone, 2);            

            //DADOS DO PEDIDO
            $pag_seguro['reference']                        = $codigo_pedido;                                                   
            $pag_seguro['senderName']                       = $cliente['nome']." ".$cliente['sobrenome']; 
            if(strlen($cliente['cpf']) == 18){
                $pag_seguro['senderCNPJ']                   = str_replace(array('.','-','/'), '', $cliente['cpf']); 
            } else {
                $pag_seguro['senderCPF']                    = str_replace(array('.','-'), '', $cliente['cpf']); 
            }                                           
            $pag_seguro['senderAreaCode']                   = $telefone_ddd;                                                             
            $pag_seguro['senderPhone']                      = $telefone_numero;                                      
            
            if($pagamento['ambiente'] == 'S'){
                $pag_seguro['senderEmail']                  = 'teste@sandbox.pagseguro.com.br';
            } else {                                                  
                $pag_seguro['senderEmail']                  = $cliente['email'];   
            }       

            //TRATA O TIPO DE FRETE PRO PAGSEGURO
            if(mb_strtolower($tipo_frete) == 'pac'){
                $tipo_frete_pagseguro                       = 1;
            } else if(mb_strtolower($tipo_frete)== 'sedex') {
                $tipo_frete_pagseguro                       = 2;
            } else {
                $tipo_frete_pagseguro                       = 3;
            }        

            //ESTANCIA VARIÁVEIS PARA BUSCAR O FRETE E JÁ CALCULA O VALOR TOTAL DA COMPRA
            $id_endereco       = $endereco['id_endereco'];
            $cep_destinatario  = $endereco['cep'];
            $frete_tipo_frete  = $tipo_frete;
            $contador_produtos = 1;
            $valor_total       = 0;
            while($produtos_carrinho = mysqli_fetch_array($busca_produtos_carrinho)){
                $pag_seguro["itemDescription{$contador_produtos}"] = mb_strtoupper($produtos_carrinho['nome']);
                $pag_seguro["itemId{$contador_produtos}"]          = $produtos_carrinho['id_produto'];
                $pag_seguro["itemQuantity{$contador_produtos}"]    = $produtos_carrinho['quantidade'];
                $pag_seguro["itemAmount{$contador_produtos}"]      = number_format($produtos_carrinho['preco'],2,'.','');
                $valor_total                                       = $valor_total + ($produtos_carrinho['quantidade']*$produtos_carrinho['preco']);
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

            //TAXA OU DESCONTO
            $pag_seguro['extraAmount']                      = $valor_desconto;

            //PARCELAS
            $pag_seguro['installmentQuantity']              = $parcelas;
            $pag_seguro['installmentValue']                 = number_format($valor_parcela,2,'.','');
            
            //DADOS DO CARTÃO DE CRÉDITO
            $pag_seguro['creditCardHolderName']             = $nome;
            $pag_seguro['creditCardHolderCPF']              = str_replace(array('.','-'), '', $cpf);  
            $pag_seguro['creditCardHolderBirthDate']        = $nascimento;
            $pag_seguro['creditCardHolderAreaCode']         = $telefone_ddd;                                                           
            $pag_seguro['creditCardHolderPhone']            = $telefone_numero;   
            
            $pag_seguro['billingAddressStreet']             = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($endereco['logradouro'])));
            $pag_seguro['billingAddressNumber']             = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($endereco['numero'])));
            $pag_seguro['billingAddressComplement']         = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($endereco['complemento'])));
            $pag_seguro['billingAddressDistrict']           = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($endereco['bairro'])));
            $pag_seguro['billingAddressPostalCode']         = str_replace(array('.','-'), '', $endereco['cep']);
            $pag_seguro['billingAddressCity']               = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($endereco['nome_cidade'])));
            $pag_seguro['billingAddressState']              = mb_strtoupper($endereco['sigla_estado']);
            $pag_seguro['billingAddressCountry']            = 'BRL';

            //SEM CALCULO DE FRETE
            $pag_seguro['shippingType']                     = $tipo_frete_pagseguro;   
            $pag_seguro['shippingCost']                     = $valor_frete;
            $pag_seguro['shippingAddressStreet']            = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($endereco['logradouro'])));
            $pag_seguro['shippingAddressNumber']            = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($endereco['numero'])));
            $pag_seguro['shippingAddressComplement']        = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($endereco['complemento'])));
            $pag_seguro['shippingAddressDistrict']          = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($endereco['bairro'])));
            $pag_seguro['shippingAddressPostalCode']        = str_replace(array('.','-'), '', $endereco['cep']);
            $pag_seguro['shippingAddressCity']              = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($endereco['nome_cidade'])));
            $pag_seguro['shippingAddressState']             = mb_strtoupper($endereco['sigla_estado']);
            $pag_seguro['shippingAddressCountry']           = 'BRL'; 

            //ENVIA PARA O PAGSEGURO
            $buildQuery = http_build_query($pag_seguro);
            $url = URL_PAGSEGURO . "transactions";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, Array("Content-Type: application/x-www-form-urlencoded; charset=UTF-8"));
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $buildQuery);
            $retorno = curl_exec($curl);
            curl_close($curl);
            $xml = simplexml_load_string($retorno);
            
            //RETORNO PRO AJAX  
            if($xml->error){    
    
                //ALTERA O STATUS DO PEDIDO PARA ERRO
                mysqli_query($conn, "UPDATE pedido SET status = $xml->error->code WHERE id = ".$id_pedido);                

                //PREENCHE AS VARIÁVEIS DO FORM QUE VAI PRA TELA DE CONFIRMAÇÃO
                $status_processo   = "ERRO";
                $mensagem_processo = "Erro ao processar pagamento pelo PagSeguro. Erro ".$xml->error->code.": ".$xml->error->message;

            } else {   
                
                //INSERE O USO DO CUPOM NO BANCO CASO TENHA
                if($tem_cupom){
                    mysqli_query($conn, "INSERT INTO cupom_uso (id_cupom, id_cliente, id_pedido) VALUES ('".$cupom['id']."','".$cliente['id']."','$id_pedido')");
                }

                //GERAL UM IDENTIFICADOR DO PAGAMENTO PAGSEGUO
                $identificador_pagamento_pagseguro = md5(date('Y-m-d H:i:s').$id_pedido.$hash_comprador.$xml->code);
                
                //DEIXA O VALOR DO DESCONTO POSITIVO
                if($valor_desconto != '0.00'){ $valor_desconto = $valor_desconto*-1; }

                //CALCULA O VALOR DO JUROS
                $valor_juros = round(($parcelas*$valor_parcela)-($valor_total+$valor_frete)+$valor_desconto,2);
                    
                //INSERE NO BANCO O HASH DE REFERENCIA DO PAGSEGURO E O LINK DO BOLETO
                mysqli_query($conn, "INSERT INTO pagamento_pagseguro (identificador, id_pedido, hash, codigo, tipo, parcelas, valor_parcela, valor_produtos, valor_desconto, valor_juros, valor_frete, tipo_frete) VALUES ('$identificador_pagamento_pagseguro','$id_pedido','$hash_comprador','$xml->code','CARTAO','$parcelas','$valor_parcela','$valor_total','$valor_desconto','$valor_juros','$valor_frete','$tipo_frete')");

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
    <form id="form-pagamento-pagseguro" style="display: none;" action="../../../envio-email/index.php" method="POST">
        <input type="hidden" name="tipo-envio" value="formulario-pagamento-pagseguro-cartao">
        <input type="hidden" name="pedido" value="<?= $codigo_pedido ?>">
        <input type="hidden" name="cliente" value="<?= $cliente['nome'] ?>">
        <input type="hidden" name="email" value="<?= $cliente['email'] ?>">
    </form>
    <?php

//SE GEROU ERRO, VAI DIRETO PARA A CONFIRMAÇÃO EXIBIR O PROBLEMA
} else if($status_processo == 'ERRO'){   

    ?>
    <form id="form-pagamento-pagseguro" style="display: none;" action="../../../../carrinho-confirmacao" method="POST">
        <input type="hidden" name="status" value="<?= $status_processo ?>">
        <input type="hidden" name="mensagem" value="<?= $mensagem_processo ?>">
    </form>
    <?php

}

//SUBMIT NO FORM DE CONTINUAÇÃO
echo "<script>document.getElementById('form-pagamento-pagseguro').submit();</script>";