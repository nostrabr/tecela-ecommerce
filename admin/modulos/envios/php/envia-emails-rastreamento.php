<?php

ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

//INICIA A SESSÃO
session_start();

//VALIDA A SESSÃO
if(isset($_SESSION["DONO"])){
    
    //GERA O TOKEN
    $token_usuario = md5('18f80a949b97de988368995777c5aaea'.$_SERVER['REMOTE_ADDR']);
    
    //SE FOR DIFERENTE
    if($_SESSION["DONO"] !== $token_usuario){
            
        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";

    } else {        
            
        include_once '../../../../bd/conecta.php';
        
        //BUSCA AS CONFIGURAÇÕES DE ENVIO DE E-MAIL E DADOS DA LOJA
        $busca_dados_loja = mysqli_query($conn, "SELECT * FROM loja WHERE id = 1");
        $loja             = mysqli_fetch_array($busca_dados_loja);
            
        $busca_cidade     = mysqli_query($conn, "SELECT nome FROM cidade WHERE id = ".$loja['cidade']);
        $cidade           = mysqli_fetch_array($busca_cidade);

        $busca_estado     = mysqli_query($conn, "SELECT sigla FROM estado WHERE id = ".$loja['estado']);
        $estado           = mysqli_fetch_array($busca_estado);

        $endereco_loja = $loja['rua'].', '.$loja['numero'];
        if($loja['complemento'] != ''){ $endereco_loja .= ' - '.$loja['complemento']; }
        $endereco_loja .= ' - '.$loja['bairro'].' - '.$cidade['nome'].'/'.$estado['sigla'];

        /************************************/
        /* ENVIA OS E-MAILS DE RASTREAMENTO */
        /************************************/

        //BUSCA OS PEDIDOS QUE NÃO FORAM ENVIADOS
        $busca_pedidos = mysqli_query($conn, "
            SELECT p.codigo, p.identificador, p.data_cadastro, p.id_cliente, pfc.melhor_envio_id_etiqueta, pfc.melhor_envio_codigo_envio
            FROM pedido AS p 
            LEFT JOIN pedido_frete AS pf ON p.id = pf.id_pedido
            LEFT JOIN pedido_frete_pacote AS pfc ON pf.id = pfc.id_pedido_frete
            WHERE pfc.status = 2
            ORDER BY p.data_cadastro ASC
        ");

        while($pedido = mysqli_fetch_array($busca_pedidos)){

            $order = $pedido['melhor_envio_id_etiqueta'];
            
            include '../../frete/melhor-envio/rastrear-etiqueta.php';   

            //BUSCA OS DADOS DO CLIENTE
            $busca_cliente = mysqli_query($conn, "SELECT id, nome, email FROM cliente WHERE id = ".$pedido['id_cliente']);
            $cliente       = mysqli_fetch_array($busca_cliente);
            $email_envio   = $cliente['email'];
            $assunto       = 'Rastreamento';            
            
            //VARIÁVEIS QUE VEM COMO TEXTO DA CONFIGURAÇÂO NA ADMINISTRAÇÃO E SÃO ALTERADAS PELAS VARIÁVEIS PHP
            $variaveis_email = array('{cliente_nome}','{cliente_email}','{pedido_codigo}','{rastreamento}','{loja_endereco}','{loja_telefone}','{loja_whatsapp}','{loja_email}','{loja_nome}','{loja_site}');
            $variaveis_troca = array($cliente['nome'], $cliente['email'], $pedido['codigo'], $rastreamento, $endereco_loja, $loja['telefone'], $loja['whatsapp'], $loja['email'], $loja ['nome'], $loja['site']);

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
                                            '.str_replace($variaveis_email, $variaveis_troca, $loja['email_pedido_enviado']).'
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

            //INCLUI O ENVIO DE E-MAIL
            include '../../envio-email/index.php';  

            mysqli_query($conn, "UPDATE pedido_frete_pacote SET status = 3 WHERE melhor_envio_id_etiqueta = '$order' AND melhor_envio_codigo_envio = '".$pedido['melhor_envio_codigo_envio']."'");

        }

        
        include_once '../../../../bd/desconecta.php';

    }
    
} else {

    //REDIRECIONA PARA A TELA DE LOGIN
    echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";
        
}
