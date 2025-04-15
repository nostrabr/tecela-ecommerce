<?php

//INICIA A SESSÃO
session_start();

//VALIDA A SESSÃO
if(isset($_SESSION["DONO"])){
    
    //GERA O TOKEN
    $token_usuario = md5('18f80a949b97de988368995777c5aaea'.$_SERVER['REMOTE_ADDR']);
    
    //SE FOR DIFERENTE
    if($_SESSION["DONO"] !== $token_usuario){

        //VERIFICA SE VEIO DO AJAX
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            
            //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
            echo "SESSAO INVALIDA";
            
        } else {
            
            //REDIRECIONA PARA A TELA DE LOGIN
            echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";
            
        }

    } else {

        //RECEBE OS DADOS DO FORM
        $ativar_pagamento                 = trim(strip_tags(filter_input(INPUT_POST, "ativar-pagamento")));
        $ambiente                         = trim(strip_tags(filter_input(INPUT_POST, "ambiente", FILTER_SANITIZE_STRING)));
        $email                            = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));
        $site                             = trim(strip_tags(filter_input(INPUT_POST, "site", FILTER_SANITIZE_URL)));
        $token                            = trim(strip_tags(filter_input(INPUT_POST, "token", FILTER_SANITIZE_STRING)));
        $parcelas                         = trim(strip_tags(filter_input(INPUT_POST, "parcelas", FILTER_SANITIZE_NUMBER_INT)));
        $pix                              = trim(strip_tags(filter_input(INPUT_POST, "pix")));
        $asaas_ambiente                   = trim(strip_tags(filter_input(INPUT_POST, "asaas-ambiente", FILTER_SANITIZE_STRING)));
        $asaas_token                      = trim(strip_tags(filter_input(INPUT_POST, "asaas-token", FILTER_SANITIZE_STRING)));
        $asaas_pix                        = trim(strip_tags(filter_input(INPUT_POST, "asaas-pix")));
        $asaas_boleto                     = trim(strip_tags(filter_input(INPUT_POST, "asaas-boleto")));
        $asaas_cc                         = trim(strip_tags(filter_input(INPUT_POST, "asaas-cartao-credito")));
        $asaas_parcelas                   = trim(strip_tags(filter_input(INPUT_POST, "asaas-parcelas", FILTER_SANITIZE_NUMBER_INT)));
        $asaas_parcelas_juros             = trim(strip_tags(filter_input(INPUT_POST, "asaas-parcelas-juros", FILTER_SANITIZE_NUMBER_INT)));
        $asaas_parcelas_juros_porcentagem = trim(strip_tags(filter_input(INPUT_POST, "asaas-parcelas-juros-porcentagem", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND)));
        $asaas_juros_tipo                 = trim(strip_tags(filter_input(INPUT_POST, "asaas-juros-tipo", FILTER_SANITIZE_NUMBER_INT)));

        //CONFIRMA SE VEIO TUDO PREENCHIDO
        if(!empty($ativar_pagamento)){

            include_once '../../../../bd/conecta.php';

            if($ativar_pagamento == 'asaas'){
                $pagseguro_status = 0;
                $asaas_status     = 1;
            } else if($ativar_pagamento == 'pagseguro') {
                $pagseguro_status = 1;
                $asaas_status     = 0;
            } else {
                $pagseguro_status = 0;
                $asaas_status     = 0;
            }

            if($asaas_pix == 'on'){
                $asaas_pix = 1;
            } else{
                $asaas_pix = 0;
            }

            if($asaas_boleto == 'on'){
                $asaas_boleto = 1;
            } else{
                $asaas_boleto = 0;
            }

            if($asaas_cc == 'on'){
                $asaas_cc = 1;
            } else{
                $asaas_cc = 0;
            }

            if($parcelas == '' | $parcelas < 1){
                $parcelas = 1;
            }

            if($asaas_parcelas == '' | $asaas_parcelas < 1){
                $asaas_parcelas = 1;
            }
            
            if($asaas_parcelas_juros == '' | $asaas_parcelas_juros < 0){
                $asaas_parcelas_juros = 0;
            }

            if($asaas_parcelas_juros_porcentagem == '' | $asaas_parcelas_juros_porcentagem < 0){
                $asaas_parcelas_juros_porcentagem = 0;
            }

            if($asaas_juros_tipo != '1' & $asaas_juros_tipo != '2'){
                $asaas_juros_tipo = 1;
            }
            
            $asaas_parcelas_juros_porcentagem = str_replace(',','.',$asaas_parcelas_juros_porcentagem);

            if($pix == 'on'){

                $pix = 1;
                $pix_chave        = trim(strip_tags(filter_input(INPUT_POST, "chave-pix", FILTER_SANITIZE_STRING)));
                $nome_qrcode_form = trim(strip_tags(filter_input(INPUT_POST, 'arquivo', FILTER_SANITIZE_STRING)));
                
                $busca_qrcode = mysqli_query($conn, "SELECT pix_qrcode FROM pagamento WHERE id = 1");
                $qrcode       = mysqli_fetch_array($busca_qrcode);

                if($qrcode["pix_qrcode"] !== $nome_qrcode_form){
                    $pix_qrcode = '';
                    $extensao    = mb_strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));  
                    if($extensao == 'png' | $extensao == 'jpg' | $extensao == 'jpeg'){
                        $pix_qrcode = md5(time()).'.'.$extensao;
                        $diretorio  = "../../../../imagens/pix/";
                        move_uploaded_file($_FILES['imagem']['tmp_name'], $diretorio.$pix_qrcode);
                    }
                } else {
                    $pix_qrcode = $qrcode["pix_qrcode"];
                }

            } else {
                $pix = 0;
                $pix_chave  = '';
                $pix_qrcode = '';
            }

            //UPDATE REGISTRO
            mysqli_query($conn, "UPDATE pagamento SET pagseguro_status = '$pagseguro_status', ambiente = '$ambiente', email = '$email', site = '$site', token = '$token', parcelas = '$parcelas', pix = '$pix', pix_chave = '$pix_chave', pix_qrcode = '$pix_qrcode', asaas_status = '$asaas_status', asaas_ambiente = '$asaas_ambiente', asaas_token = '$asaas_token', asaas_pix = '$asaas_pix', asaas_boleto = '$asaas_boleto', asaas_cc = '$asaas_cc', asaas_parcelas = '$asaas_parcelas', asaas_parcelas_juros = '$asaas_parcelas_juros', asaas_parcelas_juros_porcentagem = '$asaas_parcelas_juros_porcentagem', asaas_juros_tipo = '$asaas_juros_tipo' WHERE id = 1");

            /***************************************************/
            /* CONSULTA E CONFIGURA O WEBHOOK DO ASAAS VIA API */
            /***************************************************/

            if($asaas_status == 1){

                $busca_loja  = mysqli_query($conn, "SELECT site, email FROM loja WHERE id = 1");
                $loja        = mysqli_fetch_array($busca_loja);

                $webhook_url_retorno = $loja['site'].'modulos/pagamento/asaas/php/retorno.php';
                $webhook_email       = $loja['email'];

                $url_asaas = '';

                if($asaas_ambiente == 'S'){
                    $url_asaas = 'https://sandbox.asaas.com/api/v3/';
                } else if($asaas_ambiente == 'P') {
                    $url_asaas = 'https://api.asaas.com/v3/';
                }          
            
                $url_asaas = $url_asaas."webhook";
                
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $url_asaas);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);

                curl_setopt($ch, CURLOPT_POST, TRUE);

                curl_setopt($ch, CURLOPT_POSTFIELDS, "{
                \"url\": \"$webhook_url_retorno\",
                \"email\": \"$webhook_email\",
                \"interrupted\": false,
                \"enabled\": true,
                \"apiVersion\": 3,
                \"authToken\": \"\"
                }");

                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                "access_token: $asaas_token"
                ));

                $response = curl_exec($ch);
                curl_close($ch);

            }
            
            /**********************/
            /* FAIXAS DE DESCONTO */
            /**********************/

            $n_faixas_desconto = trim(strip_tags(filter_input(INPUT_POST, "n-faixas-desconto", FILTER_SANITIZE_NUMBER_INT)));

            if($n_faixas_desconto > 0){

                //INSERE AS CARACTERISTICAS                        
                $i = 1;
                $x = 1;

                while($i <= $n_faixas_desconto){                            
                    if(isset($_POST["faixa-desconto-tipo-pagamento-".$x])){
                        $cadastra = true;
                        $faixa_desconto_de             = str_replace(',','.',trim(strip_tags(filter_input(INPUT_POST, "faixa-desconto-de-".$x, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND))));
                        $faixa_desconto_ate            = str_replace(',','.',trim(strip_tags(filter_input(INPUT_POST, "faixa-desconto-ate-".$x, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND))));
                        $faixa_desconto_porcentagem    = trim(strip_tags(filter_input(INPUT_POST, "faixa-desconto-porcentagem-".$x, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND)));
                        $faixa_desconto_valor          = trim(strip_tags(filter_input(INPUT_POST, "faixa-desconto-valor-".$x, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND)));
                        $faixa_desconto_tipo_pagamento = trim(strip_tags(filter_input(INPUT_POST, "faixa-desconto-tipo-pagamento-".$x, FILTER_SANITIZE_NUMBER_INT)));
                        if($faixa_desconto_tipo_pagamento == 1){ $faixa_desconto_tipo_pagamento = 'PIX';
                        } else if($faixa_desconto_tipo_pagamento == 2) { $faixa_desconto_tipo_pagamento = 'BOLETO';
                        } else if($faixa_desconto_tipo_pagamento == 3){ $faixa_desconto_tipo_pagamento = 'CARTAO';
                        } else { $cadastra = false; }
                        if($cadastra){
                            if($faixa_desconto_ate == 0 | $faixa_desconto_ate == ''){ $faixa_desconto_ate = 999999; }
                            if(isset($_POST["faixa-desconto-identificador-".$x])){
                                $identificador_faixa_desconto = trim(strip_tags(filter_input(INPUT_POST, "faixa-desconto-identificador-".$x, FILTER_SANITIZE_STRING)));
                                mysqli_query($conn, "UPDATE pagamento_faixa_desconto SET tipo = '$faixa_desconto_tipo_pagamento', de = '$faixa_desconto_de', ate = '$faixa_desconto_ate', porcentagem_desconto = '$faixa_desconto_porcentagem', valor_desconto = '$faixa_desconto_valor' WHERE identificador = '$identificador_faixa_desconto'");
                            } else {
                                $identificador_faixa_desconto  = md5(date('Y-m-d H:i:s').$faixa_desconto_tipo_pagamento.$faixa_desconto_de.$faixa_desconto_ate.$faixa_desconto_porcentagem.$x);
                                mysqli_query($conn, "INSERT INTO pagamento_faixa_desconto (identificador, tipo, de, ate, porcentagem_desconto, valor_desconto) VALUES ('$identificador_faixa_desconto','$faixa_desconto_tipo_pagamento','$faixa_desconto_de','$faixa_desconto_ate','$faixa_desconto_porcentagem','$faixa_desconto_valor')");
                            }
                            $i++; $x++;
                        }
                    } else {
                        $x++;
                    }
                }

            }

            include_once '../../../../bd/desconecta.php';

            //REDIRECIONA PARA A TELA DE USUÁRIOS
            echo "<script>location.href='../../../configuracoes.php';</script>";
        
        } else {
            
            //REDIRECIONA PARA A TELA DE LOGIN
            echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";
            
        }
        
    }
    
} else {
    
    //VERIFICA SE VEIO DO AJAX
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

        //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
        echo "SESSAO INVALIDA";

    } else {

        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";

    }
        
}
