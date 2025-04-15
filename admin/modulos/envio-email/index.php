<?php

//CONFIGURA O CHARSET PARA NÃO DAR PROBLEMA COM ACENTUAÇÃO
header('Content-Type: text/html; charset=UTF-8');
setlocale(LC_ALL,'pt_BR.UTF8');
mb_internal_encoding('UTF8'); 
mb_regex_encoding('UTF8');
        
//ESTANCIA AS CLASSES DO PHPMAILER
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//VERIFICA SE A SESSION JÁ NÃO ESTÁ ATIVA E ATIVA
if (session_status() !== PHP_SESSION_ACTIVE){
    session_start();
}

//VALIDA A SESSÃO
if(isset($_SESSION["DONO"])){
    
    //GERA O TOKEN
    $token_usuario = md5('18f80a949b97de988368995777c5aaea'.$_SERVER['REMOTE_ADDR']);
    
    //SE FOR DIFERENTE
    if($_SESSION["DONO"] !== $token_usuario){
            
        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='../../modulos/login/php/encerra-sessao.php';</script>";

    } else {

        $busca_configuracao_email = mysqli_query($conn, "SELECT nome, email_sistema, email_sistema_senha, email_sistema_host, email_sistema_porta, email_issmtp FROM loja WHERE id = 1");
        $configuracao_email       = mysqli_fetch_array($busca_configuracao_email);
        
        require_once '../../envio-email/PHPMailer/src/Exception.php';
        require_once '../../envio-email/PHPMailer/src/PHPMailer.php';
        require_once '../../envio-email/PHPMailer/src/SMTP.php';

        try {

            $mail = new PHPMailer(true);   

            //CONFIGURAÇÕES DO SERVER
            if($loja['email_issmtp'] == 1){
                $mail->isSMTP(); 
            }                               
            $mail->isHTML(true);                                 
            $mail->SMTPDebug = 0;       
            $mail->SMTPAuth = true;                              
            $mail->SMTPSecure = 'ssl';                
            $mail->Host = $configuracao_email['email_sistema_host'];         
            $mail->Port = $configuracao_email['email_sistema_porta'];     
            $mail->Username = $configuracao_email['email_sistema'];                
            $mail->Password = $configuracao_email['email_sistema_senha'];                                    

            //RECIPIENTES
            $mail->setFrom($configuracao_email['email_sistema'], $configuracao_email['nome']);
            $mail->addAddress($email_envio);

            //CONTEÚDO                                                   
            $mail->Subject = $assunto;
            $mail->Body    = $corpo_email;
            $mail->CharSet = 'UTF-8';

            $mail->send(); 
            
            $status_envio = 'EMAIL-ENVIADO';

        } catch (Exception $e) {

            $status_envio = 'ERRO-ENVIO-EMAIL'.$mail->ErrorInfo;

        } 

    }

} else {

    //REDIRECIONA PARA A TELA DE LOGIN
    echo "<script>location.href='../../modulos/login/php/encerra-sessao.php';</script>";
        
}