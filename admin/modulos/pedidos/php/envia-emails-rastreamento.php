<?php

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

        /**********************************************/
        /* ENVIA OS E-MAILS DE PESQUISA DE SATISFAÇÃO */
        /**********************************************/

        //BUSCA OS PEDIDOS QUE NÃO TEM AVALIAÇÃO
        $busca_avaliacoes_pendentes = mysqli_query($conn, "SELECT id_pedido FROM avaliacao WHERE status = 0 AND envio_email = 0 GROUP BY id_pedido");

        while($avaliacao_pendente = mysqli_fetch_array($busca_avaliacoes_pendentes)){

            $envia_email  = false;
            $busca_pedido = mysqli_query($conn, "
                SELECT p.codigo, p.identificador, p.data_cadastro, p.id_cliente, pfc.melhor_envio_id_etiqueta, pfc.melhor_envio_codigo_envio, p.id AS id_pedido, pp.tipo_frete AS tipo_frete
                FROM pedido AS p 
                LEFT JOIN pedido_frete AS pf ON p.id = pf.id_pedido
                LEFT JOIN pedido_frete_pacote AS pfc ON pf.id = pfc.id_pedido_frete
                LEFT JOIN pagamento_pagseguro AS pp ON pp.id_pedido = p.id
                WHERE p.id = ".$avaliacao_pendente['id_pedido']." AND (p.status = 3 OR p.status = 4)
                ORDER BY p.data_cadastro ASC
            ");

            $pedido     = mysqli_fetch_array($busca_pedido);
            $order      = $pedido['melhor_envio_id_etiqueta'];
            $tipo_frete = $pedido['tipo_frete'];

            if($order != ''){
            
                include '../../frete/melhor-envio/rastrear-etiqueta.php';  
                if($status_rastreamento == 'delivered'){                          
                    $envia_email = true;
                }            
                    
            } else {
                
                if($tipo_frete == 'Retirar' | $tipo_frete == 'Motoboy'){
                    $sete_dias = date('Y-m-d', strtotime("-7 day", time()));
                    if(strtotime($pedido['data_cadastro']) < strtotime($sete_dias)){ $envia_email = true; }
                } else if($tipo_frete == 'TW'){
                    $quinze_dias = date('Y-m-d', strtotime("-15 day", time()));
                    if(strtotime($pedido['data_cadastro']) < strtotime($quinze_dias)){ $envia_email = true; }
                }

            }

            if($envia_email){
                
                //BUSCA OS DADOS DO CLIENTE
                $busca_cliente = mysqli_query($conn, "SELECT id, nome, email FROM cliente WHERE id = ".$pedido['id_cliente']);
                $cliente       = mysqli_fetch_array($busca_cliente);
                $email_envio   = $cliente['email'];
                $assunto       = 'E aí, o que achou do seu produto?';   
            
                //VARIÁVEIS QUE VEM COMO TEXTO DA CONFIGURAÇÂO NA ADMINISTRAÇÃO E SÃO ALTERADAS PELAS VARIÁVEIS PHP
                $variaveis_email = array('{cliente_nome}','{cliente_email}','{pedido_codigo}','{rastreamento}','{loja_endereco}','{loja_telefone}','{loja_whatsapp}','{loja_email}','{loja_nome}','{loja_site}');
                $variaveis_troca = array($cliente['nome'], $cliente['email'], $pedido['codigo'], $rastreamento, $endereco_loja, $loja['telefone'], $loja['whatsapp'], $loja['email'], $loja ['nome'], $loja['site']);

                $corpo_email = 'Olá '.$cliente['nome'].'!<br>Agora que você já recebeu seu produto, poderia nos ajudar e nos contar brevemente o que achou dele?<br>Vai ser bem rapidinho e vai nos ajudar muito. : )<br><a href="'.$loja['site'].'avaliacao/'.$pedido['identificador'].'">Clique aqui</a> para prosseguir.';

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
                                                '.str_replace($variaveis_email, $variaveis_troca, $corpo_email).'
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

                mysqli_query($conn, "UPDATE avaliacao SET envio_email = 1 WHERE id_pedido = '".$pedido['id_pedido']."'");
                
            }

        }
        
        include_once '../../../../bd/desconecta.php';

    }
    
} else {

    //REDIRECIONA PARA A TELA DE LOGIN
    echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";
        
}
