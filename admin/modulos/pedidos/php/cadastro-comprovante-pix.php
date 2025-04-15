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
        $identificador_pedido = trim(strip_tags(filter_input(INPUT_POST, "identificador-pedido-pix", FILTER_SANITIZE_STRING)));  
        $senha_usuario        = trim(strip_tags(filter_input(INPUT_POST, "senha-pix", FILTER_SANITIZE_STRING)));  

        //CONFIRMA SE VEIO TUDO PREENCHIDO E SE NÃO É USUÁRIO
        if(!empty($identificador_pedido) & !empty($senha_usuario)){

            include_once '../../../../bd/conecta.php';

            //CRIPTOGRAFA A SENHA
            $senha_usuario = md5($senha_usuario);
            $identificador_usuario = $_SESSION['identificador'];

            $valida_usuario = mysqli_query($conn, "SELECT id FROM usuario WHERE identificador = '$identificador_usuario' AND senha = '$senha_usuario'");

            if(mysqli_num_rows($valida_usuario) > 0){

                //RETIRA A EXTENSÃO DO ARQUIVO RECEBIDO
                $extensao = mb_strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));  

                if($extensao == 'png' | $extensao == 'jpg' | $extensao == 'jpeg' | $extensao == 'pdf'){

                    //RENOMEIA
                    $nome_arquivo = md5(time().'comprovante-pix'.$_SESSION['identificador']).'.'.$extensao;

                    //DIRETÓRIO DE COMPROVANTES DE PAGAMENTO POR PIX
                    $diretorio = "../../../../imagens/pix/comprovantes/";

                    //MOVE A IMAGEM PARA O DIRETÓRIO
                    move_uploaded_file($_FILES['imagem']['tmp_name'], $diretorio.$nome_arquivo);
                    
                    $busca_pedido = mysqli_query($conn, "SELECT id, codigo, id_cliente, id_carrinho FROM pedido WHERE identificador = '$identificador_pedido'");
                    $pedido       = mysqli_fetch_array($busca_pedido);
                    $id_pedido    = $pedido['id'];

                    mysqli_query($conn, "UPDATE pedido SET status = 3 WHERE identificador = '$identificador_pedido'");
                    mysqli_query($conn, "UPDATE pagamento_pagseguro SET comprovante_pagamento = '$nome_arquivo', comprovante_pagamento_por = '$identificador_usuario' WHERE id_pedido = '$id_pedido'");
                    
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

                    /***********************************************************/
                    /* PREPARA O ENVIO DO E-MAIL DE CONFIRMAÇÃO PARA O CLIENTE */
                    /***********************************************************/
                    
                    //BUSCA AS CONFIGURAÇÕES DE ENVIO DE E-MAIL E DADOS DA LOJA
                    $busca_dados_loja = mysqli_query($conn, "SELECT * FROM loja WHERE id = 1");
                    $loja             = mysqli_fetch_array($busca_dados_loja);
                    
                    $busca_cidade     = mysqli_query($conn, "SELECT nome FROM cidade WHERE id = ".$loja['cidade']);
                    $cidade           = mysqli_fetch_array($busca_cidade);

                    $busca_estado     = mysqli_query($conn, "SELECT sigla FROM estado WHERE id = ".$loja['estado']);
                    $estado           = mysqli_fetch_array($busca_estado);
                    
                    //BUSCA OS DADOS DO CLIENTE
                    $busca_cliente = mysqli_query($conn, "SELECT id, nome, email FROM cliente WHERE id = ".$pedido['id_cliente']);
                    $cliente       = mysqli_fetch_array($busca_cliente);
                    $email_envio   = $cliente['email'];
                    $assunto       = 'Pagamento confirmado';
                    
                    $endereco_loja = $loja['rua'].', '.$loja['numero'];
                    if($loja['complemento'] != ''){ $endereco_loja .= ' - '.$loja['complemento']; }
                    $endereco_loja .= ' - '.$loja['bairro'].' - '.$cidade['nome'].'/'.$estado['sigla'];
                    
                    //VARIÁVEIS QUE VEM COMO TEXTO DA CONFIGURAÇÂO NA ADMINISTRAÇÃO E SÃO ALTERADAS PELAS VARIÁVEIS PHP
                    $variaveis_email = array('{cliente_nome}','{cliente_email}','{pedido_codigo}','{loja_endereco}','{loja_telefone}','{loja_whatsapp}','{loja_email}','{loja_nome}','{loja_site}');
                    $variaveis_troca = array($cliente['nome'], $cliente['email'], $pedido['codigo'], $endereco_loja, $loja['telefone'], $loja['whatsapp'], $loja['email'], $loja ['nome'], $loja['site']);

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

                    //INCLUI O ENVIO DE E-MAIL
                    include_once '../../envio-email/index.php';  

                    //PREENCHE A SESSION DE RETORNO COM ERRO
                    $_SESSION['RETORNO'] = array(
                        'ERRO'    => false,
                        'status'  => 'SUCESSO'
                    );

                    echo "<script>location.href='../../../pedidos-visualiza.php?id=".$identificador_pedido."';</script>";

                } else {

                    //ARQUIVO INVÁLIDO
                    //PREENCHE A SESSION DE RETORNO COM ERRO
                    $_SESSION['RETORNO'] = array(
                        'ERRO'    => true,
                        'status'  => 'Tipo de arquivo inválido ('.$extensao.').'
                    );
                    
                    echo "<script>location.href='../../../pedidos-visualiza.php?id=".$identificador_pedido."';</script>";

                }

            } else {

                //SENHA INVÁLIDA
                //PREENCHE A SESSION DE RETORNO COM ERRO
                $_SESSION['RETORNO'] = array(
                    'ERRO'    => true,
                    'status'  => 'Senha inválida.'
                );
                
                echo "<script>location.href='../../../pedidos-visualiza.php?id=".$identificador_pedido."';</script>";

            }

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