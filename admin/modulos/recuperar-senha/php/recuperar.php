<?php 

//CONFIGURA O CHARSET PARA NÃO DAR PROBLEMA COM ACENTUAÇÃO
header('Content-Type: text/html; charset=UTF-8');
setlocale(LC_ALL,'pt_BR.UTF8');
mb_internal_encoding('UTF8'); 
mb_regex_encoding('UTF8');

//ESTANCIA AS CLASSES DO PHPMAILER
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function geraHash($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = true){

    $lmin = 'abcdefghijklmnopqrstuvwxyz';
    $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $num = '1234567890';
    $simb = '!@#$%*-';
    $retorno = '';
    $caracteres = '';

    $caracteres .= $lmin;

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

//RECEBE O E-MAIL
$email = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING)));

if(!empty($email)){

    //CONECTA AO BANCO
    include_once '../../../../bd/conecta.php';

    //BUSCA O USUÁRIO PELO E-MAIL
    $busca_usuario  = mysqli_query($conn, "SELECT id, nome, email FROM usuario WHERE email = '$email'");
    $usuario        = mysqli_fetch_array($busca_usuario);
    $n_resultados   = mysqli_num_rows($busca_usuario);

    //SE NÃO ENCONTROU NADA, RETORNA ERRO 2
    if($n_resultados === 0){

        $dados[] = array(
            "status" => "ERRO-2"
        );
        echo json_encode($dados);

    //SE ENCONTROU
    } else if($n_resultados === 1) {
        
        //BUSCA AS CONFIGURAÇÕES DE ENVIO DE E-MAIL E DADOS DA LOJA
        $busca_dados_loja = mysqli_query($conn, "SELECT nome, site, email, email_sistema, email_sistema_senha, email_sistema_host, email_sistema_porta, email_cabecalho, email_rodape FROM loja WHERE id = 1");
        $loja             = mysqli_fetch_array($busca_dados_loja);

        //GERA UMA NOVA SENHA
        $nova_senha     = geraHash(20, true, true, true);
        $nova_senha_md5 = md5($nova_senha);

        //ALTERA NO BANCO
        mysqli_query($conn,"UPDATE usuario SET senha = '".$nova_senha_md5."' WHERE id = '".$usuario["id"]."'");

        //CORPO DO E-MAIL
        $corpo_email = '
        <table width="100%" border="0" cellspacing="0" cellpadding="50" style="margin:0px; padding: 0px;">
            <tbody>
                <tr>
                    <td height="0" valign="top" style="padding: 0px;">
                        <table width="100%" border="0" align="center" cellpadding="50">
                            <tbody>
                                <tr><td style="padding: 20px 0 0 0;">Olá '.$usuario['nome'].'.</td></tr>
                                <tr><td style="padding: 0px;">Você solicitou uma nova senha de acesso ao admin da loja.</td></tr>
                                <tr><td style="padding: 0px;">Nova senha: '.$nova_senha.'</td></tr>   
                                <tr><td style="padding: 20px 0 0 0;">Obs: É aconselhável que a senha seja alterada novamente, para uma melhor memorização e segurança.</tr>   
                                <tr><td style="padding: 0px;">Para alerá-la, basta acessar o sistema com a nova senha e ir nas configurações do usuário.</tr>               
                                <tr><td style="padding: 20px 0 0 0;">Atenciosamente,</tr>               
                                <tr><td style="padding: 0 0 20px 0;">Equipe <b>'.$loja['nome'].'</></td></tr>                
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        ';        

        require '../PHPMailer/src/Exception.php';
        require '../PHPMailer/src/PHPMailer.php';
        require '../PHPMailer/src/SMTP.php';

        try {

            $mail = new PHPMailer(true);   

            //CONFIGURAÇÕES DO SERVER
            $mail->isSMTP();                    
            $mail->isHTML(true);                                 
            $mail->SMTPDebug = 0;       
            $mail->SMTPAuth = true;                              
            $mail->SMTPSecure = 'ssl';                      
            $mail->Host = $loja['email_sistema_host'];         
            $mail->Port = $loja['email_sistema_porta'];     
            $mail->Username = $loja['email_sistema'];                
            $mail->Password = $loja['email_sistema_senha'];                             

            //RECIPIENTES
            $mail->setFrom($loja['email_sistema'], $loja['nome']);
            $mail->addAddress($email);

            //CONTEÚDO                                                   
            $mail->Subject = "Nova senha";
            $mail->Body    = $corpo_email;
            $mail->CharSet = 'UTF-8';

            $mail->send();
            
            $dados[] = array(
                "status" => "OK"
            );
            echo json_encode($dados);

        } catch (Exception $e) {

            $dados[] = array(
                "status" => "erro-email",
                "erro" => $mail->ErrorInfo
            );
            echo json_encode($dados);

        }     

    //SE ENCONTROU MAIS DE UM, RETORNA ERRO 3
    } else {

        $dados[] = array(
            "status" => "ERRO-3"
        );
        echo json_encode($dados);

    }

    //DESCONECTA DO BANCO
    include_once '../../../../bd/desconecta.php';

} else {    
    $dados[] = array(
        "status" => "ERRO-1"
    );
    echo json_encode($dados);
}