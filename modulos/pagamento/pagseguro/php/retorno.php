<?php

//ESTANCIA AS CLASSES DO PHPMAILER
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include_once '../../../../bd/conecta.php';

//INCLUI A CONFIGURAÇÃO DO PAG SEGURO
include './configuracao.php';

//RECEBE A NOTIFICAÇÃO DO PAGSEGURO
$notificationCode = preg_replace('/[^[:alnum:]-]/','',filter_input(INPUT_POST, 'notificationCode', FILTER_SANITIZE_STRING));

//MONTA A URL
$url = URL_PAGSEGURO . "transactions/notifications/".$notificationCode."?email=".EMAIL_PAGSEGURO."&token=".TOKEN_PAGSEGURO;

//INICIA O CURL
$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_URL, $url);
$xml = curl_exec($curl);
curl_close($curl);
$xml = simplexml_load_string($xml);

//PEGA OS DADOS DO XML
$codigo_pedido = $xml->reference;
$status        = $xml->status;

//SE RETORNOU
if($codigo_pedido && $status){
    
    //BUSCA O PEDIDO PELA REFERENCIA(CÓDIGO DO PEDIDO)
    $busca_pedido = mysqli_query($conn, "
    SELECT p.id AS id, p.identificador AS pedido_identificador, p.codigo AS pedido_codigo, p.id_carrinho AS id_carrinho, p.id_endereco AS id_endereco, p.data_cadastro AS data_pedido, c.nome AS cliente_nome, c.sobrenome AS cliente_sobrenome, c.email AS cliente_email, c.cpf AS cliente_cpf, c.telefone AS cliente_telefone, c.celular AS cliente_celular
    FROM pedido AS p
    INNER JOIN cliente AS c ON c.id = p.id_cliente
    WHERE p.codigo = '$codigo_pedido'
    ");
    $pedido      = mysqli_fetch_array($busca_pedido);
    
    //SE ENCONTROU O PEDIDO, ATUALIZA O STATUS
    if(mysqli_num_rows($busca_pedido) > 0){
        
        mysqli_query($conn, "UPDATE pedido SET status = '$status' WHERE id = ".$pedido['id']);       

        //SE O STATUS FOR DE PAGO, ENVIA E-MAIL AVISANDO O CLIENTE
        if($status == 3){ 
            
            //DIMINUI O ESTOQUE DOS PRODUTOS
            $produtos_carrinho = mysqli_query($conn, "SELECT * FROM carrinho_produto WHERE status = 1 AND id_carrinho = ".$pedido['id_carrinho']);
            while($produto_carrinho = mysqli_fetch_array($produtos_carrinho)){
                $caracteristicas = explode(',',$produto_carrinho['ids_caracteristicas']);                    
                if($caracteristicas[0] != '' & $caracteristicas[1] != ''){                        
                    $busca_caracteristica_primaria   = mysqli_query($conn, "SELECT id_caracteristica FROM produto_caracteristica WHERE id = ".$caracteristicas[0]);        
                    $busca_caracteristica_secundaria = mysqli_query($conn, "SELECT id_caracteristica FROM produto_caracteristica WHERE id = ".$caracteristicas[1]);
                    $caracteristica_primaria         = mysqli_fetch_array($busca_caracteristica_primaria);
                    $caracteristica_secundaria       = mysqli_fetch_array($busca_caracteristica_secundaria);                        
                    mysqli_query($conn, "UPDATE produto_variacao SET estoque = estoque - '".$produto_carrinho['quantidade']."' WHERE id_produto = '".$produto_carrinho['id_produto']."' AND id_caracteristica_primaria = '".$caracteristica_primaria['id_caracteristica']."' AND id_caracteristica_secundaria = '".$caracteristica_secundaria['id_caracteristica']."'");
                    mysqli_query($conn, "UPDATE produto SET estoque = estoque - '".$produto_carrinho['quantidade']."' WHERE id = '".$produto_carrinho['id_produto']."'");                    
                } else if($caracteristicas[0] != '' & $caracteristicas[1] == ''){                        
                    $busca_caracteristica_primaria   = mysqli_query($conn, "SELECT id_caracteristica FROM produto_caracteristica WHERE id = ".$caracteristicas[0]);    
                    $caracteristica_primaria         = mysqli_fetch_array($busca_caracteristica_primaria);                        
                    mysqli_query($conn, "UPDATE produto_variacao SET estoque = estoque - '".$produto_carrinho['quantidade']."' WHERE id_produto = '".$produto_carrinho['id_produto']."' AND id_caracteristica_primaria = '".$caracteristica_primaria['id_caracteristica']."' AND id_caracteristica_secundaria = ''");
                    mysqli_query($conn, "UPDATE produto SET estoque = estoque - '".$produto_carrinho['quantidade']."' WHERE id = '".$produto_carrinho['id_produto']."'");
                } else if($caracteristicas[0] == '' & $caracteristicas[1] == ''){                        
                    mysqli_query($conn, "UPDATE produto SET estoque = estoque - '".$produto_carrinho['quantidade']."' WHERE id = '".$produto_carrinho['id_produto']."'");
                }
            }

            //BUSCA OS DADOS DA LOJA
            $busca_loja = mysqli_query($conn, "SELECT * FROM loja WHERE id = 1");
            $loja       = mysqli_fetch_array($busca_loja);

            /**********************/
            /* PARA A LOJA ODEZZA */
            /**********************/
            //INTEGRAÇÃO COM O ERP. AQUI GERA O XML DO PEDIDO PARA RETORNO.
            $nomes_loja = explode(' ',mb_strtolower($loja['nome']));
            if(in_array('odezza',$nomes_loja)){
                include '../../../../xml/gera-xml-pedido.php';
            }
            
            $endereco_loja = $loja['rua'].', '.$loja['numero'];
            if($loja['complemento'] != ''){ $endereco_loja .= ' - '.$loja['complemento']; }
            $endereco_loja .= ' - '.$loja['bairro'].' - '.$cidade['nome'].'/'.$estado['sigla'];

            //ENVIA E-MAIL DE CONFIRMAÇÃO
            $email           = $pedido['cliente_email'];
            $assunto_cliente = 'Pagamento confirmado';    
            
            //VARIÁVEIS QUE VEM COMO TEXTO DA CONFIGURAÇÂO NA ADMINISTRAÇÃO E SÃO ALTERADAS PELAS VARIÁVEIS PHP
            $variaveis_email = array('{cliente_nome}','{cliente_email}','{pedido_codigo}','{loja_endereco}','{loja_telefone}','{loja_whatsapp}','{loja_email}','{loja_nome}','{loja_site}');
            $variaveis_troca = array($pedido['cliente_nome'],$pedido['cliente_email'],$pedido['pedido_codigo'],$endereco_loja,$loja['telefone'],$loja['whatsapp'],$loja['email'],$loja['nome'],$loja['site']);

            $corpo_email = '
            <table width="100%" border="0" cellspacing="0" cellpadding="50" style="margin:0px; padding: 0px;">
                <tbody>
                    <tr>
                        <td height="0" valign="top" style="padding: 0px;">
                            <table width="100%" border="0" align="center" cellpadding="50">
                                <tbody>
                                    <tr><td style="padding: 0px;">'.str_replace($variaveis_email, $variaveis_troca, $loja['email_cabecalho']).'</td></tr>               
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="0" valign="top" style="padding: 0px;">
                            <table width="100%" border="0" align="center" cellpadding="50">
                                <tbody>
                                    <tr>
                                        <td style="padding: 20px 0px;">
                                            '.str_replace($variaveis_email, $variaveis_troca, $loja['email_pedido_confirmacao']).'
                                        </td>
                                    </tr>                
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="0" valign="top" style="padding: 0px;">
                            <table width="100%" border="0" align="center" cellpadding="50">
                                <tbody>
                                    <tr><td style="padding: 0px;">'.str_replace($variaveis_email, $variaveis_troca, $loja['email_rodape']).'</td></tr>               
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            ';       

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
                $mail->Subject = $assunto_cliente;
                $mail->Body    = $corpo_email;
                $mail->CharSet = 'UTF-8';

                $mail->send(); 
                
                $status_envio = 'EMAIL-ENVIADO';

            } catch (Exception $e) {

                $status_envio = 'ERRO-ENVIO-EMAIL'.$mail->ErrorInfo;

            } 
                                        
            //CASO TENHA O RD INSTALADO, ENVIA O EVENTO DE CONFIRMACAO DO PEDIDO
            if(!empty($loja['rd_station'])){
                $pedido_identificador = $pedido['pedido_identificador'];   
                $evento_status        = 'confirmed_payment';
                include '../../../../php/rd-station-fechamento-pedido-include.php';
            }
        
        }

    }

}

include_once '../../../../bd/desconecta.php';