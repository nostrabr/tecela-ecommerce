<?php

//DESABILITA OS ERROS
error_reporting(0);

//INICIA A SESSÃO
session_start();

//RECEBE OS DADOS DO FORM
$email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING);
$senha = filter_input(INPUT_POST, "senha", FILTER_SANITIZE_STRING);
$acao  = filter_input(INPUT_POST, "acao", FILTER_SANITIZE_STRING);

//CONECTA AO BANCO
include_once '../../../bd/conecta.php';

//CONSULTA E-MAIL
$consulta_cliente   = mysqli_query($conn, "SELECT * FROM cliente WHERE email = '$email'");
$cliente            = mysqli_fetch_array($consulta_cliente);
unset($_SESSION['RETORNO']); 

//SE ENCONTROU O CLIENTE, VERIFICA A SENHA
if(mysqli_num_rows($consulta_cliente) > 0){

    //SE OS DADOS ESTÃO CORRETOS E USUÁRIO ESTIVER ATIVO INICIA A SESSÃO, SENÂO LIMPA A SESSÃO
    if($cliente["senha"] === md5($senha)){
        
        if($cliente["status"] == 1){

            //ATRIBUI O ID DO CLIENTE AO CARRINHO
            $session_visitante = filter_var($_SESSION['visitante']);
            mysqli_query($conn, "UPDATE carrinho SET id_cliente = '".$cliente['id']."' WHERE identificador = '".$session_visitante."'");
            
            //DESCONECTA DO BANCO
            include_once '../../../bd/desconecta.php';
            
            //GERA UM NOME PRA SESSION
            session_name(md5('a5db19398b1f6d6ccb96c410b7c93755'.$_SERVER['REMOTE_ADDR']));
            
            $_SESSION['DONO']           = md5('a5db19398b1f6d6ccb96c410b7c93755'.$_SERVER['REMOTE_ADDR']);
            $_SESSION['identificador']  = $cliente["identificador"];
            $_SESSION['nome']           = $cliente["nome"];
            $_SESSION['plataforma']     = 'SITE';

            if($acao == 'area-cliente'){  
                header('location:../../../cliente-dados?gtag=login-cliente');
            } else if($acao == 'carrinho'){  
                header('location:../../../carrinho-frete?gtag=login-carrinho');
            } else {  
                header('location:../../../login');
            }

        } else {
            
            //LIMPA A SESSÃO
            unset($_SESSION['DONO']); 
            unset($_SESSION['identificador']);
            unset($_SESSION['nome']); 
            unset($_SESSION['plataforma']); 

            if($acao == 'area-cliente'){  
                $_SESSION['RETORNO'] = array('ERRO' => 'ERRO-STATUS');
                header('location:../../../login');        
            } else if($acao == 'carrinho'){  
                $_SESSION['RETORNO'] = array('ERRO' => 'ERRO-STATUS');   
                header('location:../../../carrinho-login');
            } else {  
                header('location:../../../login');
            }

        }
        
    } else {
        
        //LIMPA A SESSÃO
        unset($_SESSION['DONO']); 
        unset($_SESSION['identificador']);
        unset($_SESSION['nome']);     
        unset($_SESSION['plataforma']); 

        if($acao == 'area-cliente'){  
            $_SESSION['RETORNO'] = array('ERRO' => 'ERRO'); 
            header('location:../../../login');
        } else if($acao == 'carrinho'){  
            $_SESSION['RETORNO'] = array('ERRO' => 'ERRO'); 
            header('location:../../../carrinho-login');
        } else {  
            header('location:../../../login');
        }

    }

} else {
    
    //LIMPA A SESSÃO
    unset($_SESSION['DONO']); 
    unset($_SESSION['identificador']);
    unset($_SESSION['nome']);      
    unset($_SESSION['plataforma']); 
    
    if($acao == 'area-cliente'){          
        $_SESSION['RETORNO'] = array('ERRO' => 'ERRO');
        header('location:../../../login');
    } else if($acao == 'carrinho'){                 
        $_SESSION['RETORNO'] = array('ERRO' => 'ERRO');
        header('location:../../../carrinho-login');
    } else {  
        header('location:../../../login');
    }
    
}

