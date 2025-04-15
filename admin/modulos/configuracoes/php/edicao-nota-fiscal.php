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
        $ativar_asaas_nf            = trim(strip_tags(filter_input(INPUT_POST, "asaas-nf")));
        $asaas_ambiente             = trim(strip_tags(filter_input(INPUT_POST, "asaas-ambiente")));      
        $asaas_token                = trim(strip_tags(filter_input(INPUT_POST, "asaas-token")));     
        $asaas_emails               = trim(strip_tags(filter_input(INPUT_POST, "asaas-emails")));         
        $asaas_deducao              = trim(strip_tags(filter_input(INPUT_POST, "asaas-deducao", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND)));
        $asaas_reter_iss            = trim(strip_tags(filter_input(INPUT_POST, "asaas-reter-iss", FILTER_SANITIZE_NUMBER_INT)));
        $asaas_iss                  = trim(strip_tags(filter_input(INPUT_POST, "asaas-iss", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND)));
        $asaas_cofins               = trim(strip_tags(filter_input(INPUT_POST, "asaas-cofins", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND)));
        $asaas_csll                 = trim(strip_tags(filter_input(INPUT_POST, "asaas-csll", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND)));
        $asaas_inss                 = trim(strip_tags(filter_input(INPUT_POST, "asaas-inss", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND)));
        $asaas_ir                   = trim(strip_tags(filter_input(INPUT_POST, "asaas-ir", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND)));
        $asaas_pis                  = trim(strip_tags(filter_input(INPUT_POST, "asaas-pis", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND)));
        $asaas_id_serv_municipal    = trim(strip_tags(filter_input(INPUT_POST, "asaas-id-serv-municipal")));     
        $asaas_cod_serv_municipal   = trim(strip_tags(filter_input(INPUT_POST, "asaas-cod-serv-municipal")));     
        $asaas_nome_serv_municipal  = trim(strip_tags(filter_input(INPUT_POST, "asaas-name-serv-municipal")));     

        //CONFIRMA SE VEIO TUDO PREENCHIDO
        if(!empty($asaas_ambiente)){

            include_once '../../../../bd/conecta.php';

            if($ativar_asaas_nf == 'on'){
                $ativar_asaas_nf = 1;
            } else{
                $ativar_asaas_nf = 0;
            }

            if($asaas_deducao == '' | $asaas_deducao < 0){
                $asaas_deducao = 0;
            } else {
                $asaas_deducao = str_replace(',','.',$asaas_deducao);
            }

            if($asaas_reter_iss != 0 & $asaas_reter_iss != 1){
                $asaas_reter_iss = 0;
            }

            if($asaas_iss == '' | $asaas_iss < 0){
                $asaas_iss = 0;
            } else {
                $asaas_iss = str_replace(',','.',$asaas_iss);
            }

            if($asaas_cofins == '' | $asaas_cofins < 0){
                $asaas_cofins = 0;
            } else {
                $asaas_cofins = str_replace(',','.',$asaas_cofins);
            }

            if($asaas_csll == '' | $asaas_csll < 0){
                $asaas_csll = 0;
            } else {
                $asaas_csll = str_replace(',','.',$asaas_csll);
            }

            if($asaas_inss == '' | $asaas_inss < 0){
                $asaas_inss = 0;
            } else {
                $asaas_inss = str_replace(',','.',$asaas_inss);
            }

            if($asaas_ir == '' | $asaas_ir < 0){
                $asaas_ir = 0;
            } else {
                $asaas_ir = str_replace(',','.',$asaas_ir);
            }

            if($asaas_pis == '' | $asaas_pis < 0){
                $asaas_pis = 0;
            } else {
                $asaas_pis = str_replace(',','.',$asaas_pis);
            }

            //UPDATE REGISTRO
            mysqli_query($conn, "UPDATE pagamento SET asaas_status_nf = '$ativar_asaas_nf', asaas_ambiente_nf = '$asaas_ambiente', asaas_token_nf = '$asaas_token', asaas_nf_emails = '$asaas_emails', asaas_nf_deducoes = '$asaas_deducao', asaas_nf_reter_iss = '$asaas_reter_iss', asaas_nf_iss = '$asaas_iss', asaas_nf_cofins = '$asaas_cofins', asaas_nf_csll = '$asaas_csll', asaas_nf_inss = '$asaas_inss', asaas_nf_ir = '$asaas_ir', asaas_nf_pis = '$asaas_pis', asaas_id_serv_municipal = '$asaas_id_serv_municipal', asaas_cod_serv_municipal = '$asaas_cod_serv_municipal', asaas_nome_serv_municipal = '$asaas_nome_serv_municipal' WHERE id = 1");

            /***************************************************/
            /* CONSULTA E CONFIGURA O WEBHOOK DO ASAAS VIA API */
            /***************************************************/
            
            $busca_loja  = mysqli_query($conn, "SELECT site, email FROM loja WHERE id = 1");
            $loja        = mysqli_fetch_array($busca_loja);

            $webhook_url_retorno = $loja['site'].'modulos/nota-fiscal/asaas/php/retorno.php';
            $webhook_email       = $loja['email'];

            $url_asaas = '';

            if($asaas_ambiente == 'S'){
                $url_asaas = 'https://sandbox.asaas.com/api/v3/';
            } else if($asaas_ambiente == 'P') {
                $url_asaas = 'https://api.asaas.com/v3/';
            }          
        
            $url_asaas_wh = $url_asaas."webhook/invoice";
            
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url_asaas_wh);
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
