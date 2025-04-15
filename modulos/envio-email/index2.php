<?php

//CONFIGURA O CHARSET PARA NÃO DAR PROBLEMA COM ACENTUAÇÃO
header('Content-Type: text/html; charset=UTF-8');
setlocale(LC_ALL,'pt_BR.UTF8');
mb_internal_encoding('UTF8'); 
mb_regex_encoding('UTF8');

ini_set('display_errors', 1);
error_reporting(E_ALL);

//INICIA A SESSION
session_start();
        
//ESTANCIA AS CLASSES DO PHPMAILER
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../../bd/conecta.php';

//BUSCA AS CONFIGURAÇÕES DE ENVIO DE E-MAIL E DADOS DA LOJA
$busca_dados_loja = mysqli_query($conn, "SELECT * FROM loja WHERE id = 1");
$loja             = mysqli_fetch_array($busca_dados_loja);

include '../../bd/desconecta.php';

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

try {

    $mail = new PHPMailer(true);   

    //CONFIGURAÇÕES DO SERVER
    if($loja['email_issmtp'] == 1){
        $mail->isSMTP(); 
    }                   
    $mail->isHTML(true);                                 
    $mail->SMTPDebug = 4;       
    $mail->SMTPAuth = true;                              
    $mail->SMTPSecure = 'ssl';                
    $mail->Host = 'smtppro.zoho.com';         
    $mail->Port = 465;     
    $mail->Username = 'contato@lojaroos.com.br';                
    $mail->Password = '@Mvp258080';                                    

    //RECIPIENTES
    $mail->setFrom('contato@lojaroos.com.br', 'Roos');
    $mail->addAddress('rafaelrmattei@gmail.com');

    //CONTEÚDO                                                   
    $mail->Subject = 'Teste';
    $mail->Body    = 'Teste';
    $mail->CharSet = 'UTF-8';

    $mail->send();
    
    echo 'EMAIL-ENVIADO';
    
} catch (Exception $e) {

    echo $e;

} 