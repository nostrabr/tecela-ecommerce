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
        $nome                           = trim(strip_tags(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING)));
        $cpf_cnpj                       = trim(strip_tags(filter_input(INPUT_POST, "cpf-cnpj", FILTER_SANITIZE_STRING)));
        $telefone                       = trim(strip_tags(filter_input(INPUT_POST, "telefone", FILTER_SANITIZE_STRING)));
        $whatsapp                       = trim(strip_tags(filter_input(INPUT_POST, "whatsapp", FILTER_SANITIZE_STRING)));
        $site                           = trim(strip_tags(filter_input(INPUT_POST, "site", FILTER_SANITIZE_URL)));
        $email                          = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));
        $google_maps                    = $_POST['google-maps'];
        $rua                            = trim(strip_tags(filter_input(INPUT_POST, "rua", FILTER_SANITIZE_STRING)));
        $numero                         = trim(strip_tags(filter_input(INPUT_POST, "numero", FILTER_SANITIZE_STRING)));
        $bairro                         = trim(strip_tags(filter_input(INPUT_POST, "bairro", FILTER_SANITIZE_STRING)));
        $cep                            = trim(strip_tags(filter_input(INPUT_POST, "cep", FILTER_SANITIZE_STRING)));
        $complemento                    = trim(strip_tags(filter_input(INPUT_POST, "complemento", FILTER_SANITIZE_STRING)));
        $estado                         = trim(strip_tags(filter_input(INPUT_POST, "estado", FILTER_SANITIZE_NUMBER_INT)));
        $cidade                         = trim(strip_tags(filter_input(INPUT_POST, "cidade", FILTER_SANITIZE_NUMBER_INT)));
        $exibir_endereco                = trim(strip_tags(filter_input(INPUT_POST, "exibir-endereco")));
        $validar_email_cadastro         = trim(strip_tags(filter_input(INPUT_POST, "opcao-validar-email-cadastro")));
        $site_manutencao                = trim(strip_tags(filter_input(INPUT_POST, "site-manutencao")));
        $mensagem_cookies               = trim(strip_tags(filter_input(INPUT_POST, "mensagem-cookies")));
        $facebook                       = trim(strip_tags(filter_input(INPUT_POST, "facebook", FILTER_SANITIZE_URL)));
        $instagram                      = trim(strip_tags(filter_input(INPUT_POST, "instagram", FILTER_SANITIZE_URL)));
        $twiter                         = trim(strip_tags(filter_input(INPUT_POST, "twiter", FILTER_SANITIZE_URL)));
        $youtube                        = trim(strip_tags(filter_input(INPUT_POST, "youtube", FILTER_SANITIZE_URL)));
        $pinterest                      = trim(strip_tags(filter_input(INPUT_POST, "pinterest", FILTER_SANITIZE_URL)));
        $tiktok                         = trim(strip_tags(filter_input(INPUT_POST, "tiktok", FILTER_SANITIZE_URL)));
        $google_recaptcha_chave_site    = trim(strip_tags(filter_input(INPUT_POST, "google-recaptcha-chave-site", FILTER_SANITIZE_STRING)));
        $google_recaptcha_chave_secreta = trim(strip_tags(filter_input(INPUT_POST, "google-recaptcha-chave-secreta", FILTER_SANITIZE_STRING)));
        $pagina_sobre                   = filter_input(INPUT_POST, "summernote");
        $nivel_usuario                  = filter_var($_SESSION['nivel']);

        //CONFIRMA SE VEIO TUDO PREENCHIDO
        if(!empty($nome) & !empty($cpf_cnpj) & !empty($site) & !empty($email) & !empty($rua) & !empty($numero) & !empty($bairro) & !empty($cep) & !empty($estado) & !empty($cidade)){

            include_once '../../../../bd/conecta.php';

            //VERIFICA SE É OU NÃO PARA EXIBIR O ENDEREÇO NO SITE
            if($exibir_endereco == 'on'){
                $exibir_endereco = 1;
            } else {
                $exibir_endereco = 0;
            }

            //VERIFICA SE É OU NÃO PARA EXIBIR O SITE EM MANUTENÇÃO
            if($site_manutencao == 'on'){
                $site_manutencao = 1;
            } else {
                $site_manutencao = 0;
            }

            //VERIFICA SE É OU NÃO PARA EXIBIR O AVISO SOBRE COOKIES
            if($mensagem_cookies == 'on'){
                $mensagem_cookies = 1;
            } else {
                $mensagem_cookies = 0;
            }

            //VERIFICA SE É OU NÃO PARA VALIDADE O CADASTRO DO CLIENTE COM UM CÓDIGO POR E-MAIL
            if($validar_email_cadastro == 'on'){
                $validar_email_cadastro = 1;
            } else {
                $validar_email_cadastro = 0;
            }

            //VERIFICA O ÚLTIMO CARACTER DO SITE E SE NÃO FOR UMA BARRA, INCLUI
            $ultima_letra_site = substr($site, -1);
            if($ultima_letra_site != '/'){
                $site .= '/';
            }

            //SE FOR NÍVEL SUPER, VEM ALGUNS DADOS QUE NÃO VEM PRO RESTANTE
            if($nivel_usuario == 'S'){

                //RECEBE O MODO WHATSAPP
                $modo_whatsapp         = trim(strip_tags(filter_input(INPUT_POST, "modo-whatsapp")));
                $modo_whatsapp_simples = trim(strip_tags(filter_input(INPUT_POST, "modo-whatsapp-simples")));
                $modo_whatsapp_preco   = trim(strip_tags(filter_input(INPUT_POST, "modo-whatsapp-preco")));

                //MUDA O FORMATO PRO BANCO
                if($modo_whatsapp == 'on'){
                    $modo_whatsapp = 1;
                } else {
                    $modo_whatsapp = 0;
                }

                //MUDA O FORMATO PRO BANCO
                if($modo_whatsapp_simples == 'on'){
                    $modo_whatsapp_simples = 1;
                } else {
                    $modo_whatsapp_simples = 0;
                }

                //MUDA O FORMATO PRO BANCO
                if($modo_whatsapp_preco == 'on'){
                    $modo_whatsapp_preco = 1;
                } else {
                    $modo_whatsapp_preco = 0;
                }

                //UPDATE REGISTRO
                mysqli_query($conn, "UPDATE loja SET nome = '$nome', cpf_cnpj = '$cpf_cnpj', telefone = '$telefone', whatsapp = '$whatsapp', site = '$site', email = '$email', google_maps = '$google_maps', rua = '$rua', numero = '$numero', bairro = '$bairro', cep = '$cep', complemento = '$complemento', estado = '$estado', cidade = '$cidade', facebook = '$facebook', instagram = '$instagram', twiter = '$twiter', youtube = '$youtube', pinterest = '$pinterest', tiktok = '$tiktok', pagina_sobre = '$pagina_sobre', exibir_endereco = '$exibir_endereco', opcao_validar_email_cadastro = '$validar_email_cadastro', modo_whatsapp = '$modo_whatsapp', modo_whatsapp_simples = '$modo_whatsapp_simples', modo_whatsapp_preco = '$modo_whatsapp_preco', recaptcha = '$google_recaptcha_chave_site', recaptcha_secret = '$google_recaptcha_chave_secreta', site_manutencao = '$site_manutencao', opcao_mensagem_cookies = '$mensagem_cookies' WHERE id = 1");

            } else {

                //UPDATE REGISTRO
                mysqli_query($conn, "UPDATE loja SET nome = '$nome', cpf_cnpj = '$cpf_cnpj', telefone = '$telefone', whatsapp = '$whatsapp', site = '$site', email = '$email', google_maps = '$google_maps', rua = '$rua', numero = '$numero', bairro = '$bairro', cep = '$cep', complemento = '$complemento', estado = '$estado', cidade = '$cidade', facebook = '$facebook', instagram = '$instagram', twiter = '$twiter', youtube = '$youtube', pinterest = '$pinterest', tiktok = '$tiktok', pagina_sobre = '$pagina_sobre', exibir_endereco = '$exibir_endereco', opcao_validar_email_cadastro = '$validar_email_cadastro', recaptcha = '$google_recaptcha_chave_site', recaptcha_secret = '$google_recaptcha_chave_secreta', site_manutencao = '$site_manutencao', opcao_mensagem_cookies = '$mensagem_cookies' WHERE id = 1");

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
