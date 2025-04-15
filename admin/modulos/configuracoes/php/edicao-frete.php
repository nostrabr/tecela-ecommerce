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
        $cep            = trim(strip_tags(filter_input(INPUT_POST, "cep", FILTER_SANITIZE_STRING)));
        $prazo_minimo   = trim(strip_tags(filter_input(INPUT_POST, "prazo-minimo", FILTER_SANITIZE_NUMBER_INT)));
        $nivel_usuario  = filter_var($_SESSION['nivel']);   
        $array_servicos = $_POST["servicos"];
        $total_servicos = count($array_servicos);        
        
        //CONFIRMA SE VEIO TUDO PREENCHIDO
        if(!empty($cep) & $nivel_usuario != 'U' & $total_servicos > 0 & $prazo_minimo >= 0){            

            $aviso_recebimento = trim(strip_tags(filter_input(INPUT_POST, "aviso-recebimento", FILTER_SANITIZE_STRING)));
            $maos_proprias     = trim(strip_tags(filter_input(INPUT_POST, "maos-proprias", FILTER_SANITIZE_STRING)));     
            $coleta            = trim(strip_tags(filter_input(INPUT_POST, "coleta", FILTER_SANITIZE_STRING)));    
            $frete_retirar     = trim(strip_tags(filter_input(INPUT_POST, "frete-retirar", FILTER_SANITIZE_STRING)));        
            $frete_gratis      = trim(strip_tags(filter_input(INPUT_POST, "frete-gratis", FILTER_SANITIZE_STRING)));     
            $frete_motoboy     = trim(strip_tags(filter_input(INPUT_POST, "frete-motoboy", FILTER_SANITIZE_STRING)));    
            $tw                = trim(strip_tags(filter_input(INPUT_POST, "tw", FILTER_SANITIZE_STRING)));              

            //TRATA OS SERVICOS
            $servicos = implode(',',$array_servicos);

            //TRATA A FUNÇÃO AVISO DE RECEBIMENTO
            if($aviso_recebimento == 'on'){
                $aviso_recebimento = 1;
            } else {
                $aviso_recebimento = 0;
            }

            //TRATA A FUNÇÃO MÃOS PRÓPRIAS
            if($maos_proprias == 'on'){
                $maos_proprias = 1;
            } else {
                $maos_proprias = 0;
            }

            //TRATA A FUNÇÃO COLETA
            if($coleta == 'on'){
                $coleta = 1;
            } else {
                $coleta = 0;
            }

            //TRATA A FUNÇÃO FRETE RETIRAR
            if($frete_retirar == 'on'){
                $frete_retirar = 1;
                $array_cidades         = $_POST["frete-retirar-cidades"]; 
                $frete_retirar_cidades = implode(',',$array_cidades);
            } else {
                $frete_retirar = 0;
            }

            //TRATA A FUNÇÃO FRETE GRÁTIS
            if($frete_gratis == 'on'){             

                $array_estados            = $_POST["frete-gratis-estados"];   

                //SE NÃO FORAM SELECIONADOS ESTADOS NÃO ATIVA A FUNÇÃO
                if(count($array_estados) > 0){    
                    $frete_gratis_minimo  = trim(str_replace(',','.',str_replace('R$','',strip_tags(filter_input(INPUT_POST, "frete-gratis-minimo", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND)))));  
                    $frete_gratis         = 1;
                    $frete_gratis_estados = implode(',',$array_estados);
                } else {
                    $frete_gratis         = 0;
                    $frete_gratis_minimo  = 0;
                    $frete_gratis_estados = '';
                }

            } else {
                $frete_gratis         = 0;
                $frete_gratis_minimo  = 0;
                $frete_gratis_estados = '';
            }
        
            if($frete_motoboy == 'on'){
                $frete_motoboy         = 1;
                $frete_motoboy_minimo  = trim(str_replace(',','.',str_replace('R$','',strip_tags(filter_input(INPUT_POST, "frete-motoboy-minimo", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND)))));
                $frete_motoboy_entrega = trim(str_replace(',','.',str_replace('R$','',strip_tags(filter_input(INPUT_POST, "frete-motoboy-entrega", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND)))));
                $frete_motoboy_prazo   = trim(strip_tags(filter_input(INPUT_POST, "frete-motoboy-prazo", FILTER_SANITIZE_NUMBER_INT)));
                $array_cidades         = $_POST["frete-motoboy-cidades"];   
                $frete_motoboy_cidades = implode(',',$array_cidades);
            } else {
                $frete_motoboy         = 0;
                $frete_motoboy_minimo  = 0;
                $frete_motoboy_entrega = 0;
                $frete_motoboy_prazo   = 0;
                $frete_motoboy_cidades = '';
            }
        
            //TRATA A FUNÇÃO FRETE RETIRAR
            if($tw == 'on'){
                $tw                    = 1;
                $frete_tw_dominio      = trim(strip_tags(filter_input(INPUT_POST, "frete-tw-dominio", FILTER_SANITIZE_STRING)));
                $frete_tw_login        = trim(strip_tags(filter_input(INPUT_POST, "frete-tw-login", FILTER_SANITIZE_STRING)));
                $frete_tw_senha        = trim(strip_tags(filter_input(INPUT_POST, "frete-tw-senha", FILTER_SANITIZE_STRING)));
                $frete_tw_cnpj_pagador = preg_replace('/[^0-9]/', '', trim(strip_tags(filter_input(INPUT_POST, "frete-tw-cnpj-pagador", FILTER_SANITIZE_STRING))));
            } else {
                $tw                    = 0;
                $frete_tw_dominio      = null;
                $frete_tw_login        = null;
                $frete_tw_senha        = null;
                $frete_tw_cnpj_pagador = null;
            }

            include_once '../../../../bd/conecta.php';
            
            //UPDATE REGISTRO
            mysqli_query($conn, "
                UPDATE frete SET 
                cep                            = '$cep', 
                prazo_minimo                   = '$prazo_minimo',
                tw                             = '$tw', 
                frete_retirar                  = '$frete_retirar', 
                frete_gratis                   = '$frete_gratis', 
                frete_motoboy                  = '$frete_motoboy', 
                frete_retirar_cidades          = '$frete_retirar_cidades', 
                frete_gratis_valor_minimo      = '$frete_gratis_minimo', 
                frete_gratis_estados           = '$frete_gratis_estados', 
                frete_motoboy_valor_minimo     = '$frete_motoboy_minimo', 
                frete_motoboy_valor_entrega    = '$frete_motoboy_entrega', 
                frete_motoboy_prazo            = '$frete_motoboy_prazo', 
                frete_motoboy_cidades          = '$frete_motoboy_cidades', 
                melhor_envio_coleta            = '$coleta', 
                melhor_envio_aviso_recebimento = '$aviso_recebimento', 
                melhor_envio_maos_proprias     = '$maos_proprias', 
                melhor_envio_servicos          = '$servicos', 
                tw_dominio                     = '$frete_tw_dominio', 
                tw_login                       = '$frete_tw_login', 
                tw_senha                       = '$frete_tw_senha', 
                tw_cnpj_pagador                = '$frete_tw_cnpj_pagador'
                WHERE id = 1
            ");
            
            //SE FOR NÍVEL SUPER, VEM ALGUNS DADOS QUE NÃO VEM PRO RESTANTE
            if($nivel_usuario == 'S'){

                //RECEBE O MODO ENVIOS
                $modo_envios        = trim(strip_tags(filter_input(INPUT_POST, "modo-envios")));

                //MUDA O FORMATO PRO BANCO
                if($modo_envios == 'on'){
                    $modo_envios = 1;
                } else {
                    $modo_envios = 0;
                }

                //UPDATE LOJA
                mysqli_query($conn, "UPDATE loja SET modo_envios = '$modo_envios' WHERE id = 1");

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
