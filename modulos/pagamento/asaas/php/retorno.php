<?php 

$json = file_get_contents('php://input');

//ESTANCIA AS CLASSES DO PHPMAILER
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(isset($json)){
    
    $array_resposta        = json_decode($json, true);
    $status                = $array_resposta['event'];
    $codigo_pedido_externo = $array_resposta['payment']['id'];

    if(!empty($status) & !empty($codigo_pedido_externo)){
    
        include_once '../../../../bd/conecta.php';

        //BUSCA O PEDIDO PELA REFERENCIA(CÓDIGO DO PEDIDO)
        $busca_pedido = mysqli_query($conn, "
            SELECT p.id AS id, p.identificador AS pedido_identificador, p.codigo AS pedido_codigo, p.id_carrinho AS id_carrinho, p.id_endereco AS id_endereco, p.data_cadastro AS data_pedido, p.status AS status_pedido, c.nome AS cliente_nome, c.sobrenome AS cliente_sobrenome, c.email AS cliente_email, c.cpf AS cliente_cpf, c.telefone AS cliente_telefone, c.celular AS cliente_celular
            FROM pedido AS p
            INNER JOIN cliente AS c ON c.id = p.id_cliente
            INNER JOIN pagamento_pagseguro AS pp ON p.id = pp.id_pedido
            WHERE pp.codigo = '$codigo_pedido_externo'
        ");
        $pedido = mysqli_fetch_array($busca_pedido);
        
        //SE ENCONTROU O PEDIDO, ATUALIZA O STATUS
        if(mysqli_num_rows($busca_pedido) > 0){
            
            //BUSCA OS DADOS DA LOJA
            $busca_loja = mysqli_query($conn, "SELECT * FROM loja WHERE id = 1");
            $loja       = mysqli_fetch_array($busca_loja);
            
            //SE O STATUS FOR DE PAGO, ALTERA NO BANCO, GRAVA O COMPROVANTE E ENVIA E-MAIL AVISANDO O CLIENTE
            if($status == "PAYMENT_CONFIRMED" | $status == "PAYMENT_RECEIVED"){ 
                
                $comprovante_pagamento = $array_resposta['payment']['transactionReceiptUrl'];

                if($pedido['status_pedido'] != 3){
                
                    if($status == "PAYMENT_CONFIRMED"){
                        mysqli_query($conn, "UPDATE pedido SET status = 3 WHERE id = ".$pedido['id']); 
                    } else if($status == "PAYMENT_RECEIVED"){
                        mysqli_query($conn, "UPDATE pedido SET status = 3 WHERE id = ".$pedido['id']); 
                        //mysqli_query($conn, "UPDATE pedido SET status = 4 WHERE id = ".$pedido['id']); 
                    }

                    mysqli_query($conn, "UPDATE pagamento_pagseguro SET comprovante_pagamento = '$comprovante_pagamento', comprovante_pagamento_por = 'ASAAS' WHERE codigo = '$codigo_pedido_externo'"); 

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

            } else if($status == "PAYMENT_AWAITING_RISK_ANALYSIS"){ 

                mysqli_query($conn, "UPDATE pedido SET status = 2 WHERE id = ".$pedido['id']); 

            } else if($status == "PAYMENT_APPROVED_BY_RISK_ANALYSIS"){ 

                mysqli_query($conn, "UPDATE pedido SET status = 3 WHERE id = ".$pedido['id']); 

            } else if($status == "PAYMENT_REPROVED_BY_RISK_ANALYSIS"){ 

                mysqli_query($conn, "UPDATE pedido SET status = 7 WHERE id = ".$pedido['id']); 

            } else if($status == "PAYMENT_OVERDUE"){ 

                mysqli_query($conn, "UPDATE pedido SET status = 7 WHERE id = ".$pedido['id']); 

            } else if($status == "PAYMENT_REFUNDED"){ 

                mysqli_query($conn, "UPDATE pedido SET status = 6 WHERE id = ".$pedido['id']); 

            } else if($status == "PAYMENT_RECEIVED_IN_CASH_UNDONE"){ 

                mysqli_query($conn, "UPDATE pedido SET status = 1 WHERE id = ".$pedido['id']); 

            } else if($status == "PAYMENT_CHARGEBACK_REQUESTED"){ 

                mysqli_query($conn, "UPDATE pedido SET status = 2 WHERE id = ".$pedido['id']); 

            } else if($status == "PAYMENT_CHARGEBACK_DISPUTE"){ 

                mysqli_query($conn, "UPDATE pedido SET status = 2 WHERE id = ".$pedido['id']); 

            } else if($status == "PAYMENT_AWAITING_CHARGEBACK_REVERSAL"){ 

                mysqli_query($conn, "UPDATE pedido SET status = 6 WHERE id = ".$pedido['id']); 

            }             

        }

        include_once '../../../../bd/desconecta.php';

    }


} else {
    echo '<script>window.location.href = "https://conectashop.com";</script>';
}