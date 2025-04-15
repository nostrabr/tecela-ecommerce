<?php

//CONFIGURA O CHARSET PARA NÃO DAR PROBLEMA COM ACENTUAÇÃO
header('Content-Type: text/html; charset=UTF-8');
setlocale(LC_ALL,'pt_BR.UTF8');
mb_internal_encoding('UTF8'); 
mb_regex_encoding('UTF8');

//INICIA A SESSION
session_start();
        
//ESTANCIA AS CLASSES DO PHPMAILER
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//RECEBE O FORMATO DE ENVIO DE E-MAIL E INSTANCIA AS VARIÁVEIS
$tipo_envio         = trim(strip_tags(filter_input(INPUT_POST, "tipo-envio", FILTER_SANITIZE_STRING)));
$envia_para_cliente = true;
$envia_para_loja    = false;
$tudo_certo         = false;
unset($_SESSION['RETORNO']); 

//CONFIRMA SE VEIO PREENCHIDO
if(!empty($tipo_envio)){  

    //FUNÇÃO PRA GERAR CÓDIGOS ALEATÓRIOS
    function geraHash($tamanho = 8, $minusculas = true, $maiusculas = true, $numeros = true, $simbolos = true){

        $lmin = 'abcdefghijklmnopqrstuvwxyz';
        $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num = '1234567890';
        $simb = '!@#$%*-';
        $retorno = '';
        $caracteres = '';
    
        if ($minusculas) $caracteres .= $lmin;
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

    include '../../bd/conecta.php';

    //BUSCA AS CONFIGURAÇÕES DE ENVIO DE E-MAIL E DADOS DA LOJA
    $busca_dados_loja = mysqli_query($conn, "SELECT * FROM loja WHERE id = 1");
    $loja             = mysqli_fetch_array($busca_dados_loja);
    
    $busca_cidade     = mysqli_query($conn, "SELECT nome FROM cidade WHERE id = ".$loja['cidade']);
    $cidade           = mysqli_fetch_array($busca_cidade);

    $busca_estado     = mysqli_query($conn, "SELECT sigla FROM estado WHERE id = ".$loja['estado']);
    $estado           = mysqli_fetch_array($busca_estado);

    include '../../bd/desconecta.php';

    $endereco_loja = $loja['rua'].', '.$loja['numero'];
    if($loja['complemento'] != ''){ $endereco_loja .= ' - '.$loja['complemento']; }
    $endereco_loja .= ' - '.$loja['bairro'].' - '.$cidade['nome'].'/'.$estado['sigla'];

    //SE VIER DO FORMULÁRIO DE ENVIO DO SITE
    if($tipo_envio == 'formulario-contato'){

        //PEGA OS DADOS DO FORMULÁRIO
        $nome             = trim(strip_tags(mb_convert_case(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING), MB_CASE_TITLE, 'UTF-8')));
        $email            = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING)));
        $telefone         = trim(strip_tags(filter_input(INPUT_POST, "telefone", FILTER_SANITIZE_STRING)));
        $mensagem         = trim(strip_tags(filter_input(INPUT_POST, "mensagem", FILTER_SANITIZE_STRING)));

        $tudo_certo       = true;
        $envia_para_loja  = true;
        $assunto_loja     = 'Novo contato pelo formulário do e-commerce';
        $assunto_cliente  = 'Contato recebido';   
        $pagina_retorno   = '../../contato';
        
        //VARIÁVEIS QUE VEM COMO TEXTO DA CONFIGURAÇÂO NA ADMINISTRAÇÃO E SÃO ALTERADAS PELAS VARIÁVEIS PHP
        $variaveis_email = array('{cliente_nome}','{cliente_email}','{cliente_telefone}','{mensagem_contato}','{loja_endereco}','{loja_telefone}','{loja_whatsapp}','{loja_email}','{loja_nome}','{loja_site}');
        $variaveis_troca = array($nome, $email, $telefone, $mensagem, $endereco_loja, $loja['telefone'], $loja['whatsapp'], $loja['email'], $loja['nome'], $loja['site']);

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
                                        '.str_replace($variaveis_email, $variaveis_troca, $loja['email_contato']).'
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

        $corpo_email_loja = '
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
                                <tr><td style="padding: 20px 0 0 0;">'.$nome.' entrou em contato através do formulário do site.</td></tr>
                                <tr><td style="padding: 0px;">Dados do contato:</td></tr>
                                <tr><td style="padding: 0px;">E-mail: '.$email.'</td></tr>
                                <tr><td style="padding: 0px;">Telefone: '.$telefone.'</td></tr>
                                <tr><td style="padding: 0px;">Mensagem:</td></tr>
                                <tr><td style="padding: 0px;">'.$mensagem.'</td></tr>
                                <tr><td style="padding: 0 0 20px 0;"></td></tr>   
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
    
    //SE VIER DO FORMULÁRIO DE CADASTRO DE CLIENTE PARA ENVIO DE E-MAIL COM CÓDIGO 
    } else if($tipo_envio == 'formulario-cadastro-cliente'){

        //PEGA OS DADOS DO FORMULÁRIO
        $nome      = trim(strip_tags(mb_convert_case(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING), MB_CASE_TITLE, 'UTF-8')));
        $sobrenome = trim(strip_tags(mb_convert_case(filter_input(INPUT_POST, "sobrenome", FILTER_SANITIZE_STRING), MB_CASE_TITLE, 'UTF-8')));   
        $cpf       = trim(strip_tags(filter_input(INPUT_POST, "cpf", FILTER_SANITIZE_STRING)));  
        $email     = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));   

        $tudo_certo       = true;
        $envia_para_loja  = true;
        $assunto_loja     = 'Novo cliente';
        $assunto_cliente  = 'Cadastro confirmado';   
        if(isset($_SESSION['RETORNO-CADASTRO'])){
            $pagina_retorno   = '../../'.$_SESSION['RETORNO-CADASTRO'];
            unset($_SESSION['RETORNO-CADASTRO']); 
        } else {
            $pagina_retorno   = '../../login';
        }

        //VARIÁVEIS QUE VEM COMO TEXTO DA CONFIGURAÇÂO NA ADMINISTRAÇÃO E SÃO ALTERADAS PELAS VARIÁVEIS PHP
        $variaveis_email = array('{cliente_nome}','{cliente_sobrenome}','{cliente_cpf}','{cliente_email}','{loja_endereco}','{loja_telefone}','{loja_whatsapp}','{loja_email}','{loja_nome}','{loja_site}');
        $variaveis_troca = array($nome, $sobrenome, $cpf, $email, $endereco_loja, $loja['telefone'], $loja['whatsapp'], $loja['email'], $loja['nome'], $loja['site']);

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
                                        '.str_replace($variaveis_email, $variaveis_troca, $loja['email_cadastro_cliente']).'
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
        
        if(strlen($cpf) == 18){
            $nome_cliente = $nome.' ('.$sobrenome.')';
        } else {
            $nome_cliente = $nome.' '.$sobrenome;
        }

        $corpo_email_loja = '
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
                                <tr><td style="padding: 20px 0 0 0;">'.$nome_cliente.' acabou de se cadastrar na loja.</td></tr>
                                <tr><td style="padding: 0px;">Dados:</td></tr>
                                <tr><td style="padding: 0px;">CPF/CNPJ: '.$cpf.'</td></tr>
                                <tr><td style="padding: 0px;">E-mail: '.$email.'</td></tr>
                                <tr><td style="padding: 0 0 20px 0;"></td></tr>   
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
    
    //SE VIER DO FORMULÁRIO DE CADASTRO DE CLIENTE PARA ENVIO DE E-MAIL COM CÓDIGO 
    } else if($tipo_envio == 'formulario-cadastro-cliente-verificacao'){

        //PEGA OS DADOS DO FORMULÁRIO
        $nome      = trim(strip_tags(mb_convert_case(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING), MB_CASE_TITLE, 'UTF-8')));
        $sobrenome = trim(strip_tags(mb_convert_case(filter_input(INPUT_POST, "sobrenome", FILTER_SANITIZE_STRING), MB_CASE_TITLE, 'UTF-8')));   
        $cpf       = trim(strip_tags(filter_input(INPUT_POST, "cpf", FILTER_SANITIZE_STRING)));   
        $celular   = trim(strip_tags(filter_input(INPUT_POST, "celular", FILTER_SANITIZE_STRING)));  
        $email     = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));   
        $senha     = trim(strip_tags(filter_input(INPUT_POST, "senha", FILTER_SANITIZE_STRING)));  

        $tudo_certo              = true;
        $envia_para_loja         = false;
        $assunto_loja            = '';
        $assunto_cliente         = 'Código de confirmação de e-mail';           
        $codigo_confirmaca_email = geraHash(6,false,false,true,false); 
        $pagina_retorno          = 'formulario-cadastro-cliente-verificacao';

        include '../../bd/conecta.php';

        //GERA UM IDENTIFICADOR PARA O REGISTRO DA VERIFICACAO DE SEGURANCA
        $identificador_verificacao_seguranca = md5(date('Y-m-d H:i:s').$codigo_confirmaca_email.$cpf.$email);

        //INSERE NO BANCO PARA CONSULTA POSTERIOR
        mysqli_query($conn, "INSERT INTO verificacao_seguranca (identificador, codigo, email) VALUES ('$identificador_verificacao_seguranca','$codigo_confirmaca_email','$email')");

        include '../../bd/desconecta.php';
        
        //VARIÁVEIS QUE VEM COMO TEXTO DA CONFIGURAÇÂO NA ADMINISTRAÇÃO E SÃO ALTERADAS PELAS VARIÁVEIS PHP
        $variaveis_email = array('{loja_endereco}','{loja_telefone}','{loja_whatsapp}','{loja_email}','{loja_nome}','{loja_site}');
        $variaveis_troca = array($endereco_loja, $loja['telefone'], $loja['whatsapp'], $loja['email'], $loja['nome'], $loja['site']);

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
                                <tr><td style="padding: 20px 0 0 0;">Olá '.$nome.'.</td></tr>
                                <tr><td style="padding: 0px;">Seu código para confirmação do e-mail do cadastro: '.$codigo_confirmaca_email.'</td></tr>     
                                <tr><td style="padding: 20px 0 20px 0;">Muito obrigado e até logo!</td></tr>                                       
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
                                <tr><td style="padding: 0px;">'.str_replace($variaveis_email, $variaveis_troca, $loja['email_rodape']).'</td></tr>               
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        ';             

        $corpo_email_loja = '';  

    //SE VIER DO FORMULÁRIO DE EDIÇÃO DE DADOS DE ACESSO DO CLIENTE
    } else if($tipo_envio == 'formulario-edicao-acesso-cliente-verificacao'){

        //PEGA OS DADOS DO FORMULÁRIO
        $email = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));   
        $senha = trim(strip_tags(filter_input(INPUT_POST, "senha", FILTER_SANITIZE_STRING)));  

        $tudo_certo              = true;
        $envia_para_loja         = false;
        $assunto_loja            = '';
        $assunto_cliente         = 'Código de confirmação de alteração cadastral';           
        $codigo_confirmaca_email = geraHash(6,false,false,true,false); 
        $pagina_retorno          = 'formulario-edicao-acesso-cliente-verificacao';

        include '../../bd/conecta.php';

        //GERA UM IDENTIFICADOR PARA O REGISTRO DA VERIFICACAO DE SEGURANCA
        $identificador_verificacao_seguranca = md5(date('Y-m-d H:i:s').$codigo_confirmaca_email.$email.$senha);

        //INSERE NO BANCO PARA CONSULTA POSTERIOR
        mysqli_query($conn, "INSERT INTO verificacao_seguranca (identificador, codigo, email) VALUES ('$identificador_verificacao_seguranca','$codigo_confirmaca_email','$email')");

        include '../../bd/desconecta.php';
        
        //VARIÁVEIS QUE VEM COMO TEXTO DA CONFIGURAÇÂO NA ADMINISTRAÇÃO E SÃO ALTERADAS PELAS VARIÁVEIS PHP
        $variaveis_email = array('{loja_endereco}','{loja_telefone}','{loja_whatsapp}','{loja_email}','{loja_nome}','{loja_site}');
        $variaveis_troca = array($endereco_loja, $loja['telefone'], $loja['whatsapp'], $loja['email'], $loja['nome'], $loja['site']);

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
                                <tr><td style="padding: 20px 0 0 0;">Olá!</td></tr>
                                <tr><td style="padding: 0px;">Seu código para confirmação da alteração dos dados de acesso do seu cadastro: '.$codigo_confirmaca_email.'</td></tr>   
                                <tr><td style="padding: 20px 0 0 0;">Atenciosamente,</tr>     
                                <tr><td style="padding: 0 0 20px 0;">Equipe <b>'.$loja['nome'].'</></td></tr>                
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

        $corpo_email_loja = '';  

    //SE VIER DO FORMULÁRIO DE RECUPERAÇÃO DE SENHA
    } else if($tipo_envio == 'formulario-recuperacao-senha-confirmacao'){

        //PEGA OS DADOS DO FORMULÁRIO
        $email = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));   

        $tudo_certo              = true;
        $envia_para_loja         = false;
        $assunto_loja            = '';
        $assunto_cliente         = 'Código de confirmação para alteração de senha';           
        $codigo_confirmaca_email = geraHash(6,false,false,true,false); 
        $pagina_retorno          = 'formulario-recuperacao-senha-confirmacao';

        include '../../bd/conecta.php';

        //GERA UM IDENTIFICADOR PARA O REGISTRO DA VERIFICACAO DE SEGURANCA
        $identificador_verificacao_seguranca = md5(date('Y-m-d H:i:s').$codigo_confirmaca_email.$email);

        //INSERE NO BANCO PARA CONSULTA POSTERIOR
        mysqli_query($conn, "INSERT INTO verificacao_seguranca (identificador, codigo, email) VALUES ('$identificador_verificacao_seguranca','$codigo_confirmaca_email','$email')");

        include '../../bd/desconecta.php';
        
        //VARIÁVEIS QUE VEM COMO TEXTO DA CONFIGURAÇÂO NA ADMINISTRAÇÃO E SÃO ALTERADAS PELAS VARIÁVEIS PHP
        $variaveis_email = array('{loja_endereco}','{loja_telefone}','{loja_whatsapp}','{loja_email}','{loja_nome}','{loja_site}');
        $variaveis_troca = array($endereco_loja, $loja['telefone'], $loja['whatsapp'], $loja['email'], $loja['nome'], $loja['site']);

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
                                <tr><td style="padding: 20px 0 0 0;">Olá!</td></tr>
                                <tr><td style="padding: 0px;">Seu código para confirmar e-mail e alterar a senha: '.$codigo_confirmaca_email.'</td></tr>
                                <tr><td style="padding: 20px 0 0 0;">Atenciosamente,</tr>     
                                <tr><td style="padding: 0 0 20px 0;">Equipe <b>'.$loja['nome'].'</></td></tr>                
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

        $corpo_email_loja = '';  

    //SE VIER DO FORMULÁRIO DE PROCESSAMENTO DO PAGAMENTO VIA PAGSEGURO NA MODALIDADE BOLETO
    } else if($tipo_envio == 'formulario-pagamento-pagseguro-boleto'){

        //PEGA OS DADOS DO FORMULÁRIO
        $email           = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));   
        $codigo_pedido   = trim(strip_tags(filter_input(INPUT_POST, "pedido", FILTER_SANITIZE_STRING)));   
        $boleto          = trim(strip_tags(filter_input(INPUT_POST, "boleto", FILTER_SANITIZE_STRING)));  
        $cliente         = trim(strip_tags(filter_input(INPUT_POST, "cliente", FILTER_SANITIZE_STRING)));  

        $tudo_certo      = true;
        $envia_para_loja = true;
        $assunto_loja    = 'Novo pedido';
        $assunto_cliente = 'Pedido recebido';       
        $pagina_retorno  = '../../carrinho-confirmacao';
        
        //VARIÁVEIS QUE VEM COMO TEXTO DA CONFIGURAÇÂO NA ADMINISTRAÇÃO E SÃO ALTERADAS PELAS VARIÁVEIS PHP
        $variaveis_email = array('{cliente_nome}','{cliente_email}','{pedido_codigo}','{boleto_link}','{loja_endereco}','{loja_telefone}','{loja_whatsapp}','{loja_email}','{loja_nome}','{loja_site}');
        $variaveis_troca = array($cliente, $email, $codigo_pedido, $boleto, $endereco_loja, $loja['telefone'], $loja['whatsapp'], $loja['email'], $loja['nome'], $loja['site']);

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
                                        '.str_replace($variaveis_email, $variaveis_troca, $loja['email_pedido_boleto']).'
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

        $corpo_email_loja = '
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
                                <tr><td style="padding: 20px 0 0 0;">Novo pedido registrado</td></tr>
                                <tr><td style="padding: 0px;">Código de referência: '.$codigo_pedido.'</td></tr>
                                <tr><td style="padding: 0px;">Cliente: '.$cliente.'</td></tr>
                                <tr><td style="padding: 0px;">Forma de pagamento: Boleto PagSeguro</td></tr>
                                <tr><td style="padding: 20px 0 0 0;">Link do pedido: <a href="'.$loja['site'].'pedido/'.$codigo_pedido.'">'.$loja['site'].'pedido/'.$codigo_pedido.'</a></td></tr>
                                <tr><td style="padding: 0 0 20px 0;"></td></tr>   
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

    //SE VIER DO FORMULÁRIO DE PROCESSAMENTO DO PAGAMENTO VIA PAGSEGURO NA MODALIDADE CARTÃO
    } else if($tipo_envio == 'formulario-pagamento-pagseguro-cartao'){

        //PEGA OS DADOS DO FORMULÁRIO
        $email           = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));   
        $codigo_pedido   = trim(strip_tags(filter_input(INPUT_POST, "pedido", FILTER_SANITIZE_STRING)));  
        $cliente         = trim(strip_tags(filter_input(INPUT_POST, "cliente", FILTER_SANITIZE_STRING)));  

        $tudo_certo      = true;
        $envia_para_loja = true;
        $assunto_loja    = 'Novo pedido';
        $assunto_cliente = 'Pedido recebido';       
        $pagina_retorno  = '../../carrinho-confirmacao';
        
        //VARIÁVEIS QUE VEM COMO TEXTO DA CONFIGURAÇÂO NA ADMINISTRAÇÃO E SÃO ALTERADAS PELAS VARIÁVEIS PHP
        $variaveis_email = array('{cliente_nome}','{cliente_email}','{pedido_codigo}','{loja_endereco}','{loja_telefone}','{loja_whatsapp}','{loja_email}','{loja_nome}','{loja_site}');
        $variaveis_troca = array($cliente, $email, $codigo_pedido, $endereco_loja, $loja['telefone'], $loja['whatsapp'], $loja['email'], $loja['nome'], $loja['site']);

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
                                        '.str_replace($variaveis_email, $variaveis_troca, $loja['email_pedido_cartao']).'
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

        $corpo_email_loja = '
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
                                <tr><td style="padding: 20px 0 0 0;">Novo pedido registrado</td></tr>
                                <tr><td style="padding: 0px;">Código de referência: '.$codigo_pedido.'</td></tr>
                                <tr><td style="padding: 0px;">Cliente: '.$cliente.'</td></tr>
                                <tr><td style="padding: 0px;">Forma de pagamento: Cartão de Crédito PagSeguro</td></tr>
                                <tr><td style="padding: 20px 0 0 0;">Link do pedido: <a href="'.$loja['site'].'pedido/'.$codigo_pedido.'">'.$loja['site'].'pedido/'.$codigo_pedido.'</a></td></tr>
                                <tr><td style="padding: 0 0 20px 0;"></td></tr>   
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

    //SE VIER DO FORMULÁRIO DE PROCESSAMENTO DO PAGAMENTO NA MODALIDADE PIX MANUAL
    } else if($tipo_envio == 'formulario-pagamento-pix'){

        //PEGA OS DADOS DO FORMULÁRIO
        $email           = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));   
        $codigo_pedido   = trim(strip_tags(filter_input(INPUT_POST, "pedido", FILTER_SANITIZE_STRING)));  
        $cliente         = trim(strip_tags(filter_input(INPUT_POST, "cliente", FILTER_SANITIZE_STRING)));  

        $tudo_certo      = true;
        $envia_para_loja = true;
        $assunto_loja    = 'Novo pedido';
        $assunto_cliente = 'Pedido recebido';       
        $pagina_retorno  = '../../carrinho-confirmacao';

        include '../../bd/conecta.php';

        $busca_pagamento = mysqli_query($conn, "SELECT pix_chave FROM pagamento WHERE id = 1");
        $pagamento       = mysqli_fetch_array($busca_pagamento);

        include '../../bd/desconecta.php';

        //VARIÁVEIS QUE VEM COMO TEXTO DA CONFIGURAÇÂO NA ADMINISTRAÇÃO E SÃO ALTERADAS PELAS VARIÁVEIS PHP
        $variaveis_email = array('{cliente_nome}','{cliente_email}','{pedido_codigo}','{chave_pix}','{loja_endereco}','{loja_telefone}','{loja_whatsapp}','{loja_email}','{loja_nome}','{loja_site}');
        $variaveis_troca = array($cliente, $email, $codigo_pedido, $pagamento['pix_chave'], $endereco_loja, $loja['telefone'], $loja['whatsapp'], $loja['email'], $loja['nome'], $loja['site']);

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
                                        '.str_replace($variaveis_email, $variaveis_troca, $loja['email_pedido_pix']).'
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

        $corpo_email_loja = '
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
                                <tr><td style="padding: 20px 0 0 0;">Novo pedido registrado</td></tr>
                                <tr><td style="padding: 0px;">Código de referência: '.$codigo_pedido.'</td></tr>
                                <tr><td style="padding: 0px;">Cliente: '.$cliente.'</td></tr>
                                <tr><td style="padding: 0px;">Forma de pagamento: PIX</td></tr>
                                <tr><td style="padding: 20px 0 0 0;">Link do pedido: <a href="'.$loja['site'].'pedido/'.$codigo_pedido.'">'.$loja['site'].'pedido/'.$codigo_pedido.'</a></td></tr>
                                <tr><td style="padding: 0 0 20px 0;"></td></tr>   
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

    //SE VIER DO FORMULÁRIO DE PROCESSAMENTO DO PAGAMENTO VIA ASAAS NA MODALIDADE PIX
    } else if($tipo_envio == 'formulario-pagamento-pix-asaas'){

        //PEGA OS DADOS DO FORMULÁRIO
        $email           = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));   
        $codigo_pedido   = trim(strip_tags(filter_input(INPUT_POST, "pedido", FILTER_SANITIZE_STRING)));  
        $cliente         = trim(strip_tags(filter_input(INPUT_POST, "cliente", FILTER_SANITIZE_STRING)));  
        $qrcode_imagem   = trim(strip_tags(filter_input(INPUT_POST, "qrcode_imagem")));  
        $qrcode_chave    = trim(strip_tags(filter_input(INPUT_POST, "qrcode_chave")));  
        $pedido_url      = trim(strip_tags(filter_input(INPUT_POST, "pedido_url")));  

        $tudo_certo      = true;
        $envia_para_loja = true;
        $assunto_loja    = 'Novo pedido';
        $assunto_cliente = 'Pedido recebido';       
        $pagina_retorno  = '../../carrinho-confirmacao';
        
        //VARIÁVEIS QUE VEM COMO TEXTO DA CONFIGURAÇÂO NA ADMINISTRAÇÃO E SÃO ALTERADAS PELAS VARIÁVEIS PHP
        $variaveis_email = array('{cliente_nome}','{cliente_email}','{pedido_codigo}','{loja_endereco}','{loja_telefone}','{loja_whatsapp}','{loja_email}','{loja_nome}','{loja_site}','{chave_pix}','{pedido_url}');
        $variaveis_troca = array($cliente, $email, $codigo_pedido, $endereco_loja, $loja['telefone'], $loja['whatsapp'], $loja['email'], $loja['nome'], $loja['site'], $qrcode_chave, $pedido_url);

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
                                        '.str_replace($variaveis_email, $variaveis_troca, $loja['email_pedido_pix']).'
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

        $corpo_email_loja = '
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
                                <tr><td style="padding: 20px 0 0 0;">Novo pedido registrado</td></tr>
                                <tr><td style="padding: 0px;">Código de referência: '.$codigo_pedido.'</td></tr>
                                <tr><td style="padding: 0px;">Cliente: '.$cliente.'</td></tr>
                                <tr><td style="padding: 0px;">Forma de pagamento: PIX Asaas</td></tr>
                                <tr><td style="padding: 20px 0 0 0;">Link do pedido: <a href="'.$loja['site'].'pedido/'.$codigo_pedido.'">'.$loja['site'].'pedido/'.$codigo_pedido.'</a></td></tr>
                                <tr><td style="padding: 0 0 20px 0;"></td></tr>   
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
        
    //SE VIER DO FORMULÁRIO DE PROCESSAMENTO DO PAGAMENTO VIA ASAAS NA MODALIDADE BOLETO
    } else if($tipo_envio == 'formulario-pagamento-boleto-asaas'){

        //PEGA OS DADOS DO FORMULÁRIO
        $email           = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));   
        $codigo_pedido   = trim(strip_tags(filter_input(INPUT_POST, "pedido", FILTER_SANITIZE_STRING)));  
        $cliente         = trim(strip_tags(filter_input(INPUT_POST, "cliente", FILTER_SANITIZE_STRING)));  
        $asaas_boleto    = trim(strip_tags(filter_input(INPUT_POST, "asaas_boleto")));  
        $qrcode_imagem   = trim(strip_tags(filter_input(INPUT_POST, "qrcode_imagem")));  
        $qrcode_chave    = trim(strip_tags(filter_input(INPUT_POST, "qrcode_chave")));  
        $pedido_url      = trim(strip_tags(filter_input(INPUT_POST, "pedido_url")));  

        $tudo_certo      = true;
        $envia_para_loja = true;
        $assunto_loja    = 'Novo pedido';
        $assunto_cliente = 'Pedido recebido';       
        $pagina_retorno  = '../../carrinho-confirmacao';
        
        $variaveis_email = array('{cliente_nome}','{cliente_email}','{pedido_codigo}','{boleto_link}','{loja_endereco}','{loja_telefone}','{loja_whatsapp}','{loja_email}','{loja_nome}','{loja_site}');
        $variaveis_troca = array($cliente, $email, $codigo_pedido, $pedido_url, $endereco_loja, $loja['telefone'], $loja['whatsapp'], $loja['email'], $loja['nome'], $loja['site']);

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
                                        '.str_replace($variaveis_email, $variaveis_troca, $loja['email_pedido_boleto']).'
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

        $corpo_email_loja = '
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
                                <tr><td style="padding: 20px 0 0 0;">Novo pedido registrado</td></tr>
                                <tr><td style="padding: 0px;">Código de referência: '.$codigo_pedido.'</td></tr>
                                <tr><td style="padding: 0px;">Cliente: '.$cliente.'</td></tr>
                                <tr><td style="padding: 0px;">Forma de pagamento: Boleto Asaas</td></tr>
                                <tr><td style="padding: 20px 0 0 0;">Link do pedido: <a href="'.$loja['site'].'pedido/'.$codigo_pedido.'">'.$loja['site'].'pedido/'.$codigo_pedido.'</a></td></tr>
                                <tr><td style="padding: 0 0 20px 0;"></td></tr>   
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
        
    //SE VIER DO FORMULÁRIO DE PROCESSAMENTO DO PAGAMENTO VIA ASAAS NA MODALIDADE CARTÃO
    } else if($tipo_envio == 'formulario-pagamento-cc-asaas'){

        //PEGA OS DADOS DO FORMULÁRIO
        $email           = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));   
        $codigo_pedido   = trim(strip_tags(filter_input(INPUT_POST, "pedido", FILTER_SANITIZE_STRING)));  
        $cliente         = trim(strip_tags(filter_input(INPUT_POST, "cliente", FILTER_SANITIZE_STRING)));  

        $tudo_certo      = true;
        $envia_para_loja = true;
        $assunto_loja    = 'Novo pedido';
        $assunto_cliente = 'Pedido recebido';       
        $pagina_retorno  = '../../carrinho-confirmacao';
        
        //VARIÁVEIS QUE VEM COMO TEXTO DA CONFIGURAÇÂO NA ADMINISTRAÇÃO E SÃO ALTERADAS PELAS VARIÁVEIS PHP
        $variaveis_email = array('{cliente_nome}','{cliente_email}','{pedido_codigo}','{loja_endereco}','{loja_telefone}','{loja_whatsapp}','{loja_email}','{loja_nome}','{loja_site}','{pedido_url}');
        $variaveis_troca = array($cliente, $email, $codigo_pedido, $endereco_loja, $loja['telefone'], $loja['whatsapp'], $loja['email'], $loja['nome'], $loja['site'],$pedido_url);

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
                                        '.str_replace($variaveis_email, $variaveis_troca, $loja['email_pedido_cartao']).'
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

        $corpo_email_loja = '
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
                                <tr><td style="padding: 20px 0 0 0;">Novo pedido registrado</td></tr>
                                <tr><td style="padding: 0px;">Código de referência: '.$codigo_pedido.'</td></tr>
                                <tr><td style="padding: 0px;">Cliente: '.$cliente.'</td></tr>
                                <tr><td style="padding: 0px;">Forma de pagamento: Cartão de Crédito Asaas</td></tr>
                                <tr><td style="padding: 20px 0 0 0;">Link do pedido: <a href="'.$loja['site'].'pedido/'.$codigo_pedido.'">'.$loja['site'].'pedido/'.$codigo_pedido.'</a></td></tr>
                                <tr><td style="padding: 0 0 20px 0;"></td></tr>   
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

    //SE VIER DO FORMULÁRIO DE PROCESSAMENTO DO PAGAMENTO VIA ASAAS NA MODALIDADE CARTÃO E NÃO FOI AUTORIZADO
    } else if($tipo_envio == 'formulario-pagamento-cc-asaas-nao-autorizado'){

        //PEGA OS DADOS DO FORMULÁRIO
        $email              = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));   
        $codigo_pedido      = trim(strip_tags(filter_input(INPUT_POST, "pedido", FILTER_SANITIZE_STRING)));  
        $cliente            = trim(strip_tags(filter_input(INPUT_POST, "cliente", FILTER_SANITIZE_STRING)));  
        $erro               = trim(strip_tags(filter_input(INPUT_POST, "erro", FILTER_SANITIZE_STRING)));  

        $tudo_certo         = true;        
        $envia_para_cliente = false;
        $envia_para_loja    = true;
        $assunto_loja       = 'Novo pedido - Não aprovado';
        $pagina_retorno     = '../../carrinho-confirmacao/erro';
        
        //VARIÁVEIS QUE VEM COMO TEXTO DA CONFIGURAÇÂO NA ADMINISTRAÇÃO E SÃO ALTERADAS PELAS VARIÁVEIS PHP
        $variaveis_email = array('{cliente_nome}','{cliente_email}','{pedido_codigo}','{loja_endereco}','{loja_telefone}','{loja_whatsapp}','{loja_email}','{loja_nome}','{loja_site}','{pedido_url}');
        $variaveis_troca = array($cliente, $email, $codigo_pedido, $endereco_loja, $loja['telefone'], $loja['whatsapp'], $loja['email'], $loja['nome'], $loja['site'],$pedido_url);

        $corpo_email_loja = '
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
                                <tr><td style="padding: 20px 0 0 0;">Novo pedido registrado</td></tr>
                                <tr><td style="padding: 0px;">Código de referência: '.$codigo_pedido.'</td></tr>
                                <tr><td style="padding: 0px;">Cliente: '.$cliente.'</td></tr>
                                <tr><td style="padding: 0px;">Forma de pagamento: Cartão de Crédito Asaas (Não aprovado)</td></tr>
                                <tr><td style="padding: 0px;">Erro: '.$erro.'</td></tr>
                                <tr><td style="padding: 20px 0 0 0;">Link do pedido: <a href="'.$loja['site'].'pedido/'.$codigo_pedido.'">'.$loja['site'].'pedido/'.$codigo_pedido.'</a></td></tr>
                                <tr><td style="padding: 0 0 20px 0;"></td></tr>   
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

    //SE NÃO EXISTIR O TIPO DE ENVIO, REDIRECIONA PARA A INDEX
    } else if($tipo_envio == 'formulario-orcamento-whatsapp'){

        //PEGA OS DADOS DO FORMULÁRIO
        $email            = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));   
        $codigo_orcamento = trim(strip_tags(filter_input(INPUT_POST, "orcamento", FILTER_SANITIZE_STRING)));  
        $cliente          = trim(strip_tags(filter_input(INPUT_POST, "cliente", FILTER_SANITIZE_STRING)));  
        $url_whatsapp     = trim(strip_tags(filter_input(INPUT_POST, "url-whatsapp")));  

        $tudo_certo       = true;
        $envia_para_loja  = true;
        $assunto_loja     = 'Novo orçamento';
        $assunto_cliente  = 'Orçamento recebido';  
        $pagina_retorno   = 'orcamento-whatsapp';

        //VARIÁVEIS QUE VEM COMO TEXTO DA CONFIGURAÇÂO NA ADMINISTRAÇÃO E SÃO ALTERADAS PELAS VARIÁVEIS PHP
        $variaveis_email = array('{cliente_nome}','{cliente_email}','{orcamento_codigo}','{loja_endereco}','{loja_telefone}','{loja_whatsapp}','{loja_email}','{loja_nome}','{loja_site}');
        $variaveis_troca = array($cliente, $email, $codigo_orcamento, $endereco_loja, $loja['telefone'], $loja['whatsapp'], $loja['email'], $loja['nome'], $loja['site']);

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
                                        '.str_replace($variaveis_email, $variaveis_troca, $loja['email_pedido_orcamento']).'
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

        $corpo_email_loja = '
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
                                <tr><td style="padding: 20px 0 0 0;">Novo orçamento registrado</td></tr>
                                <tr><td style="padding: 0px;">Código de referência: '.$codigo_orcamento.'</td></tr>
                                <tr><td style="padding: 0px;">Cliente: '.$cliente.'</td></tr>
                                <tr><td style="padding: 0 0 20px 0;"></td></tr>   
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

    //AVISA A LOJA DE UMA NOVA AVALIAÇÃO
    } else if($tipo_envio == 'nova-avaliacao'){

        //PEGA OS DADOS DO FORMULÁRIO
        $identificador_pedido = trim(strip_tags(filter_input(INPUT_POST, "identificador-pedido", FILTER_SANITIZE_STRING))); 

        $tudo_certo       = true;
        $envia_para_loja  = false;
        $assunto_cliente  = 'Avaliação recebida';  
        $pagina_retorno   = 'avaliacao';
        $email            = $loja['email'];

        //VARIÁVEIS QUE VEM COMO TEXTO DA CONFIGURAÇÂO NA ADMINISTRAÇÃO E SÃO ALTERADAS PELAS VARIÁVEIS PHP
        $variaveis_email = array('{loja_endereco}','{loja_telefone}','{loja_whatsapp}','{loja_email}','{loja_nome}','{loja_site}');
        $variaveis_troca = array($endereco_loja, $loja['telefone'], $loja['whatsapp'], $loja['email'], $loja['nome'], $loja['site']);

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
                                    <td height="0" valign="top" style="padding: 0px;">
                                        <table width="100%" border="0" align="center" cellpadding="50">
                                            <tbody>
                                                <tr><td style="padding: 20px 0 0 0;">Nova avaliação registrada</td></tr>
                                                <tr><td style="padding: 0px;">A loja acabou de receber uma nova avaliação. Para verificar as avaliações registradas acesse o painel do administrador na opção "Avaliações".</td></tr>
                                                <tr><td style="padding: 0 0 20px 0;"></td></tr>   
                                            </tbody>
                                        </table>
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

    //AVISA A LOJA DE UMA NOVA AVALIAÇÃO
    } else if($tipo_envio == 'nova-avaliacao-simples'){

        $tudo_certo       = true;
        $envia_para_loja  = false;
        $assunto_cliente  = 'Avaliação recebida';  
        $pagina_retorno   = '';
        $email            = $loja['email'];

        //VARIÁVEIS QUE VEM COMO TEXTO DA CONFIGURAÇÂO NA ADMINISTRAÇÃO E SÃO ALTERADAS PELAS VARIÁVEIS PHP
        $variaveis_email = array('{loja_endereco}','{loja_telefone}','{loja_whatsapp}','{loja_email}','{loja_nome}','{loja_site}');
        $variaveis_troca = array($endereco_loja, $loja['telefone'], $loja['whatsapp'], $loja['email'], $loja['nome'], $loja['site']);

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
                                    <td height="0" valign="top" style="padding: 0px;">
                                        <table width="100%" border="0" align="center" cellpadding="50">
                                            <tbody>
                                                <tr><td style="padding: 20px 0 0 0;">Nova avaliação registrada</td></tr>
                                                <tr><td style="padding: 0px;">A loja acabou de receber uma nova avaliação. Para verificar as avaliações registradas acesse o painel do administrador na opção "Avaliações".</td></tr>
                                                <tr><td style="padding: 0 0 20px 0;"></td></tr>   
                                            </tbody>
                                        </table>
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

    //SE NÃO EXISTIR O TIPO DE ENVIO, REDIRECIONA PARA A INDEX
    } else {

        //REDIRECIONA PARA A INDEX
        echo "<script>location.href='/';</script>";

    }
    
    //SE ESTIVER TUDO CERTO COM O ENVIO
    if($tudo_certo){

        require 'PHPMailer/src/Exception.php';
        require 'PHPMailer/src/PHPMailer.php';
        require 'PHPMailer/src/SMTP.php';

        if($envia_para_cliente){
            
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

        }

        if($envia_para_loja){

            try {

                $mail_loja = new PHPMailer(true);   
    
                //CONFIGURAÇÕES DO SERVER
                if($loja['email_issmtp'] == 1){
                    $mail_loja->isSMTP(); 
                }                          
                $mail_loja->isHTML(true);                                 
                $mail_loja->SMTPDebug = 0;       
                $mail_loja->SMTPAuth = true;                              
                $mail_loja->SMTPSecure = 'ssl';                
                $mail_loja->Host = $loja['email_sistema_host'];         
                $mail_loja->Port = $loja['email_sistema_porta'];     
                $mail_loja->Username = $loja['email_sistema'];                
                $mail_loja->Password = $loja['email_sistema_senha'];                                    
    
                //RECIPIENTES
                $mail_loja->addReplyTo($email);
                $mail_loja->setFrom($loja['email_sistema'], $loja['nome']);
                $mail_loja->addAddress($loja['email']);

                //SE TIVER E-MAILS ADICIONAIS
                if($loja['email_adicional'] != ''){
                    $emails_adicionais = explode(',',$loja['email_adicional']);
                    for($e = 0; $e < count($emails_adicionais); $e++){
                        if(trim($emails_adicionais[$e]) != ''){
                            $mail_loja->AddCC(trim($emails_adicionais[$e]));
                        }
                    }
                }
    
                //CONTEÚDO                                                   
                $mail_loja->Subject = $assunto_loja;
                $mail_loja->Body    = $corpo_email_loja;
                $mail_loja->CharSet = 'UTF-8';
    
                $mail_loja->send(); 
                
                $status_envio_loja = 'EMAIL-ENVIADO';
                
            } catch (Exception $e) {
    
                $status_envio_loja = 'ERRO-ENVIO-EMAIL'.$mail->ErrorInfo;
    
            } 

        } else {
            $status_envio_loja = 'SEM-ENVIO-LOJA';
        }
        
        if($pagina_retorno === 'formulario-cadastro-cliente-verificacao'){

            ?>

            <form id="form-cadastro-confirmacao" style="display: none;" action="../../cliente-cadastro-confirmacao" method="POST">                   
                <input type="text" name="nome" maxlength="50" value="<?= $nome ?>" required>
                <input type="text" name="sobrenome" maxlength="50" value="<?= $sobrenome ?>" required>
                <input type="text" name="cpf" id="cpf-cnpj" maxlength="18" value="<?= $cpf ?>" required>
                <input type="text" name="celular" id="celular" value="<?= $celular ?>" required>
                <input type="email" name="email" maxlength="50" value="<?= $email ?>" required>
                <input type="password" name="senha" id="senha" maxlength="32" minlength="12" value="<?= $senha ?>" required>
                <input type="checkbox" class="custom-control-input" name="aceite-termos" checked required>
                <input type="text" name="identificador_seguranca" maxlength="32" value="<?= $identificador_verificacao_seguranca  ?>" required>
            </form>

            <?php
            
            //REDIRECIONA PARA A TELA DE CADASTRO
            echo "<script>document.getElementById('form-cadastro-confirmacao').submit();</script>";

        } else if($pagina_retorno === 'formulario-edicao-acesso-cliente-verificacao'){

            ?>

                <form id="form-edicao-confirmacao" style="display: none;" action="../../cliente-acesso-confirmacao" method="POST">    
                    <input type="email" name="email" maxlength="50" value="<?= $email ?>" required>
                    <input type="password" name="senha" id="senha" maxlength="32" minlength="12" value="<?= $senha ?>">
                    <input type="text" name="identificador_seguranca" maxlength="32" value="<?= $identificador_verificacao_seguranca  ?>" required>
                </form>

            <?php
            
            //REDIRECIONA PARA A TELA DE CADASTRO
            echo "<script>document.getElementById('form-edicao-confirmacao').submit();</script>";

        } else if($pagina_retorno === 'formulario-recuperacao-senha-confirmacao'){

            ?>

                <form id="form-recuperacao-senha-confirmacao" style="display: none;" action="../../login-recuperacao-senha-confirmacao" method="POST">    
                    <input type="email" name="email" maxlength="50" value="<?= $email ?>" required>
                    <input type="text" name="identificador_seguranca" maxlength="32" value="<?= $identificador_verificacao_seguranca  ?>" required>
                </form>

            <?php
            
            //REDIRECIONA PARA A TELA DE CADASTRO
            echo "<script>document.getElementById('form-recuperacao-senha-confirmacao').submit();</script>";

        } else if($pagina_retorno === '../../carrinho-confirmacao'){

            ?>

                <form id="form-pagamento-pagseguro" style="display: none;" action="<?= $pagina_retorno ?>" method="POST">
                    <input type="hidden" name="status" value="SUCESSO">
                    <input type="hidden" name="mensagem" value="">
                    <input type="hidden" name="codigo" value="<?= $codigo_pedido ?>">
                </form>

            <?php
            
            //REDIRECIONA PARA A TELA DE CADASTRO
            echo "<script>document.getElementById('form-pagamento-pagseguro').submit();</script>";

        } else if($pagina_retorno === '../../carrinho-confirmacao/erro'){

            ?>

                <form id="form-pagamento-pagseguro" style="display: none;" action="../../carrinho-confirmacao" method="POST">
                    <input type="hidden" name="status" value="ERRO">
                    <input type="hidden" name="mensagem" value="<?= $erro ?>">
                </form>

            <?php
            
            //REDIRECIONA PARA A TELA DE CADASTRO
            echo "<script>document.getElementById('form-pagamento-pagseguro').submit();</script>";

        } else if($pagina_retorno === 'orcamento-whatsapp'){

            //REDIRECIONA PARA A TELA DE CADASTRO
            echo "<script> window.location.href = '".$url_whatsapp."';</script>";

        } else if($pagina_retorno === 'avaliacao'){

            //REDIRECIONA PARA A TELA DE CADASTRO
            echo "<script> window.location.href = '../../avaliacao/".$identificador_pedido."';</script>";

        } else if($pagina_retorno === ''){
            
            //NÃO FAZ NADA

        } else {   

            //PREENCHE A SESSION DE RETORNO COM ERRO
            $_SESSION['RETORNO'] = array(
                'ERRO'              => false,
                'STATUS'            => 'CADASTRADO-SUCESSO',
                'STATUS-EMAIL'      => $status_envio,
                'STATUS-EMAIL-LOJA' => $status_envio_loja
            );      
            
            //REDIRECIONA PARA A INDEX
            echo "<script>location.href='".$pagina_retorno."';</script>";

        }
        
    } else {

        //REDIRECIONA PARA A INDEX
        echo "<script>location.href='/';</script>";

    }

} else {

    //REDIRECIONA PARA A INDEX
    echo "<script>location.href='/';</script>";

}