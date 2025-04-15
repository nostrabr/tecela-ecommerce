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
        $identificador = trim(strip_tags(filter_input(INPUT_POST, "visualizacao-comentario-identificador", FILTER_SANITIZE_STRING)));  
        $replica       = trim(strip_tags(filter_input(INPUT_POST, "replica", FILTER_SANITIZE_STRING)));  
        $nivel_usuario         = filter_var($_SESSION['nivel']);
        unset($_SESSION['RETORNO']); 
        
        //CONFIRMA SE VEIO TUDO PREENCHIDO E NÃO É NÍVEL USUÁRIO
        if(!empty($identificador) & $nivel_usuario != 'U'){
            
            include_once '../../../../bd/conecta.php';

            mysqli_query($conn, "UPDATE avaliacao SET replica = '$replica', data_replica = NOW() WHERE identificador = '$identificador'");

            //BUSCA AS CONFIGURAÇÕES DE ENVIO DE E-MAIL E DADOS DA LOJA
            $busca_dados_loja = mysqli_query($conn, "SELECT * FROM loja WHERE id = 1");
            $loja             = mysqli_fetch_array($busca_dados_loja);

            //BUSCA OS DADOS DO CLIENTE
            $busca_cliente = mysqli_query($conn, "
                SELECT c.nome AS nome_cliente, c.email AS email_cliente, p.codigo AS codigo_pedido, p.identificador AS identificador_pedido
                FROM avaliacao AS a
                LEFT JOIN pedido AS p ON p.id = a.id_pedido
                LEFT JOIN cliente AS c ON c.id = p.id_cliente
                WHERE a.identificador = '$identificador'
            ");
            $cliente       = mysqli_fetch_array($busca_cliente);
            $email_envio   = $cliente['email_cliente'];
            $assunto       = 'Avaliação replicada'; 

            //VARIÁVEIS QUE VEM COMO TEXTO DA CONFIGURAÇÂO NA ADMINISTRAÇÃO E SÃO ALTERADAS PELAS VARIÁVEIS PHP
            $variaveis_email = array('{cliente_nome}','{cliente_email}','{pedido_codigo}','{loja_telefone}','{loja_whatsapp}','{loja_email}','{loja_nome}','{loja_site}');
            $variaveis_troca = array($cliente['nome_cliente'], $cliente['email_cliente'], $cliente['codigo_pedido'], $loja['telefone'], $loja['whatsapp'], $loja['email'], $loja ['nome'], $loja['site']);

            $corpo_email = 'Olá '.$cliente['nome_cliente'].'!<br>A sua avaliação em nosso site recebeu um comentário.<br>Caso queira visualizá-lo acesse o link abaixo:<br><a href="'.$loja['site'].'avaliacao-replica/'.$cliente['identificador_pedido'].'">Clique aqui</a> para prosseguir.';

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

            include_once '../../envio-email/index.php';   
            
            include_once '../../../../bd/desconecta.php';
            
            //PREENCHE A SESSION DE RETORNO COM ERRO
            $_SESSION['RETORNO'] = array(
                'STATUS' => 'REPLICA'
            );

            echo "<script>location.href='../../../avaliacoes.php';</script>";
        
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
