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
        $identificador_pedido = trim(strip_tags(filter_input(INPUT_POST, "identificador-pedido", FILTER_SANITIZE_STRING)));  
        $codigo_rastreamento  = trim(strip_tags(filter_input(INPUT_POST, "codigo-rastreamento", FILTER_SANITIZE_STRING)));  

        //CONFIRMA SE VEIO TUDO PREENCHIDO E SE NÃO É USUÁRIO
        if(!empty($identificador_pedido) & !empty($codigo_rastreamento)){

            include_once '../../../../bd/conecta.php';

            //BUSCA OS DADOS DA LOJA
            $busca_loja   = mysqli_query($conn, "SELECT nome, email_cabecalho, email_rodape FROM loja WHERE id = 1");
            $loja         = mysqli_fetch_array($busca_loja);

            //BUSCA O ID DO CLIENTE NO PEDIDO
            $busca_pedido = mysqli_query($conn, "SELECT id, id_cliente FROM pedido WHERE identificador = '$identificador_pedido'");
            $pedido       = mysqli_fetch_array($busca_pedido);

            //BUSCA O TIPO DE PAGAMENTO
            $busca_pedido_pagamento = mysqli_query($conn, "SELECT tipo FROM pagamento_pagseguro WHERE id_pedido = ".$pedido['id']);
            $pedido_pagamento       = mysqli_fetch_array($busca_pedido_pagamento);

            //SE FOR PIX
            if($pedido_pagamento['tipo'] == 'PIX'){

                //ADICIONA O CÓDIGO DE RASTREAMENTO NO PEDIDO E ALTERA O STATUS PARA DISPONÍVEL
                mysqli_query($conn, "UPDATE pedido SET rastreamento = '$codigo_rastreamento', status = 4 WHERE identificador = '$identificador_pedido'");

            } else {
            
                //ADICIONA O CÓDIGO DE RASTREAMENTO NO PEDIDO
                mysqli_query($conn, "UPDATE pedido SET rastreamento = '$codigo_rastreamento' WHERE identificador = '$identificador_pedido'");

            }
            
            //BUSCA OS DADOS DO CLIENTE
            $busca_cliente = mysqli_query($conn, "SELECT id, nome, email FROM cliente WHERE id = ".$pedido['id_cliente']);
            $cliente       = mysqli_fetch_array($busca_cliente);
            $email_envio   = $cliente['email'];
            $assunto       = 'Pedido despachado';

            $corpo_email = '
            <table width="100%" border="0" cellspacing="0" cellpadding="50" style="margin:0px; padding: 0px;">
                <tbody>
                    <tr>
                        <td height="0" valign="top" style="padding: 0px;">
                            <table width="100%" border="0" align="center" cellpadding="50">
                                <tbody>
                                    <tr><td style="padding: 0px;">'.$loja['email_cabecalho'].'</td></tr>               
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="0" valign="top" style="padding: 0px;">
                            <table width="100%" border="0" align="center" cellpadding="50">
                                <tbody>
                                    <tr><td style="padding: 20px 0 0 0;">Olá '.$cliente['nome'].'.</td></tr>
                                    <tr><td style="padding: 0px;">Boa notícia! Seu pedido já foi enviado e seu código para rastreamento é o <b>'.$codigo_rastreamento.'</b>.</td></tr>                                       
                                    <tr><td style="padding: 0px;">Atenciosamente,</tr>     
                                    <tr><td style="padding: 0 0 20px 0;">Equipe <b>'.$loja['nome'].'</></td></tr>                
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="0" valign="top" style="padding: 0px;">
                            <table width="100%" border="0" align="center" cellpadding="50">
                                <tbody>
                                    <tr><td style="padding: 0px;">'.$loja['email_rodape'].'</td></tr>               
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            ';

            //INCLUI O ENVIO DE E-MAIL
            include_once '../../envio-email/index.php';  

            include_once '../../../../bd/desconecta.php';                
            
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
