<?php

//INICIA A SESSÃO
session_start();

//RECEBE OS DADOS DO CADASTRO
$nome                    = trim(strip_tags(mb_convert_case(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING), MB_CASE_TITLE, 'UTF-8')));
$sobrenome               = trim(strip_tags(mb_convert_case(filter_input(INPUT_POST, "sobrenome", FILTER_SANITIZE_STRING), MB_CASE_TITLE, 'UTF-8')));  
$cpf                     = trim(strip_tags(filter_input(INPUT_POST, "cpf", FILTER_SANITIZE_STRING)));  
$celular                 = trim(strip_tags(filter_input(INPUT_POST, "celular", FILTER_SANITIZE_STRING))); 
$email                   = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));   
$senha                   = trim(strip_tags(filter_input(INPUT_POST, "senha", FILTER_SANITIZE_STRING)));  
$identificador_seguranca = trim(strip_tags(filter_input(INPUT_POST, "identificador_seguranca", FILTER_SANITIZE_STRING)));  
$codigo                  = trim(strip_tags(filter_input(INPUT_POST, "codigo", FILTER_SANITIZE_NUMBER_INT)));  
unset($_SESSION['RETORNO']); 

include_once '../../../bd/conecta.php';

$busca_loja = mysqli_query($conn, "SELECT opcao_validar_email_cadastro FROM loja WHERE id = 1");
$loja       = mysqli_query($busca_loja);

if($loja['opcao_validar_email_cadastro'] == 1){

    if(!empty($nome) & !empty($sobrenome) & !empty($cpf) & !empty($celular) & !empty($email) & !empty($senha) & !empty($identificador_seguranca) & !empty($codigo)){

        //VALIDA O CÓDIGO DE SEGURANÇA
        $busca_verificador_seguranca = mysqli_query($conn, "SELECT id FROM verificacao_seguranca WHERE identificador = '$identificador_seguranca' AND codigo = '$codigo'");

        //SE ESTIVER CERTO, PROSSEGUE
        if(mysqli_num_rows($busca_verificador_seguranca) > 0){
            
            //CRIPTOGRAFA A SENHA
            $senha = md5($senha);

            //GERA UM IDENTIFICADOR PARA O CLIENTE
            $identificador_cliente = md5(date('Y-m-d H:i:s').$nome.$sobrenome.$cpf.$identificador_seguranca.$codigo);
            
            //INSERE NO BANCO
            mysqli_query($conn, "INSERT INTO cliente (identificador, nome, sobrenome, email, celular, senha, cpf) VALUES ('$identificador_cliente','$nome','$sobrenome','$email','$celular','$senha','$cpf')");

            if(!mysqli_error($conn)){
                
                $id_cliente = mysqli_insert_id($conn);     
                
                //ATRIBUI O ID DO CLIENTE AO CARRINHO
                $session_visitante = filter_var($_SESSION['visitante']);
                mysqli_query($conn, "UPDATE carrinho SET id_cliente = '".$id_cliente."' WHERE identificador = '".$session_visitante."'");

                ?>

                <form id="form-cadastro-confirmacao" style="display: none;" action="../../../modulos/envio-email/index.php" method="POST">            
                    <input type="text" name="tipo-envio" value="formulario-cadastro-cliente" required>                  
                    <input type="text" name="nome" maxlength="50" value="<?= $nome ?>" required>
                    <input type="text" name="sobrenome" maxlength="50" value="<?= $sobrenome ?>" required>
                    <input type="text" name="cpf" id="cpf-cnpj" maxlength="18" value="<?= $cpf ?>" required>
                    <input type="text" name="celular" id="celular" value="<?= $celular ?>" required>
                    <input type="email" name="email" maxlength="50" value="<?= $email ?>" required>
                </form>

                <?php
                    
                //REDIRECIONA PARA A TELA DE CADASTRO
                echo "<script>document.getElementById('form-cadastro-confirmacao').submit();</script>";

            } else {

                //PREENCHE A SESSION DE RETORNO COM ERRO
                $_SESSION['RETORNO'] = array('ERRO' => 'ERRO-CADASTRO');   
                
                ?>

                <form id="form-cadastro-confirmacao-retorno" style="display: none;" action="../../../cliente-cadastro-confirmacao" method="POST">                   
                    <input type="text" name="nome" maxlength="50" value="<?= $nome ?>" required>
                    <input type="text" name="sobrenome" maxlength="50" value="<?= $sobrenome ?>" required>
                    <input type="text" name="cpf" id="cpf-cnpj" maxlength="18" value="<?= $cpf ?>" required>
                    <input type="email" name="email" maxlength="50" value="<?= $email ?>" required>
                    <input type="text" name="celular" id="celular" value="<?= $celular ?>" required>
                    <input type="password" name="senha" id="senha" maxlength="32" minlength="8" value="<?= $senha ?>" required>
                    <input type="checkbox" class="custom-control-input" name="aceite-termos" checked required>
                    <input type="text" name="identificador_seguranca" maxlength="32" value="<?= $identificador_seguranca  ?>" required>
                </form>

                <?php
                    
                //REDIRECIONA PARA A TELA DE CADASTRO
                echo "<script>document.getElementById('form-cadastro-confirmacao-retorno').submit();</script>";

            }
            
        //SE ESTIVER ERRADO, VOLTA COM ERRO
        } else {
            
            //PREENCHE A SESSION DE RETORNO COM ERRO
            $_SESSION['RETORNO'] = array('ERRO' => 'ERRO-CODIGO');   
            
            ?>

            <form id="form-cadastro-confirmacao-retorno" style="display: none;" action="../../../cliente-cadastro-confirmacao" method="POST">                   
                <input type="text" name="nome" maxlength="50" value="<?= $nome ?>" required>
                <input type="text" name="sobrenome" maxlength="50" value="<?= $sobrenome ?>" required>
                <input type="text" name="cpf" id="cpf" maxlength="14" value="<?= $cpf ?>" required>
                <input type="email" name="email" maxlength="50" value="<?= $email ?>" required>
                <input type="text" name="celular" id="celular" value="<?= $celular ?>" required>
                <input type="password" name="senha" id="senha" maxlength="32" minlength="8" value="<?= $senha ?>" required>
                <input type="checkbox" class="custom-control-input" name="aceite-termos" checked required>
                <input type="text" name="identificador_seguranca" maxlength="32" value="<?= $identificador_seguranca  ?>" required>
            </form>

            <?php
                
            //REDIRECIONA PARA A TELA DE CADASTRO
            echo "<script>document.getElementById('form-cadastro-confirmacao-retorno').submit();</script>";
            
        }

        include_once '../../../bd/desconecta.php';
        
    } else {    
            
        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='/';</script>";

    }

} else {

    if(!empty($nome) & !empty($sobrenome) & !empty($cpf) & !empty($celular) & !empty($email) & !empty($senha)){
               
        //VERIFICA SE E-MAIL OU CPF JÁ NÃO ESTÃO CADASTRADOS
        $busca_clientes = mysqli_query($conn, "SELECT id FROM cliente WHERE cpf = '$cpf' OR email = '$email'");

        //SE JÁ TEM CADASTRO COM ALGUM DOS CAMPOS
        if(mysqli_num_rows($busca_clientes) > 0){

            //PREENCHE A SESSION DE RETORNO COM ERRO
            $_SESSION['RETORNO'] = array('ERRO' => 'ERRO-REPETIDO');   
            
            //REDIRECIONA PARA A TELA DE CADASTRO
            echo "<script>window.history.back();</script>";

        } else {

            //CRIPTOGRAFA A SENHA
            $senha = md5($senha);

            //GERA UM IDENTIFICADOR PARA O CLIENTE
            $identificador_cliente = md5(date('Y-m-d H:i:s').$nome.$sobrenome.$cpf.$identificador_seguranca.$codigo);

            //INSERE NO BANCO
            mysqli_query($conn, "INSERT INTO cliente (identificador, nome, sobrenome, email, celular, senha, cpf) VALUES ('$identificador_cliente','$nome','$sobrenome','$email','$celular','$senha','$cpf')");

            if(!mysqli_error($conn)){
                
                $id_cliente = mysqli_insert_id($conn);     
            
                //ATRIBUI O ID DO CLIENTE AO CARRINHO
                $session_visitante = filter_var($_SESSION['visitante']);
                mysqli_query($conn, "UPDATE carrinho SET id_cliente = '".$id_cliente."' WHERE identificador = '".$session_visitante."'");

                //LOGA O CLIENTE
                session_name(md5('a5db19398b1f6d6ccb96c410b7c93755'.$_SERVER['REMOTE_ADDR']));                
                $_SESSION['DONO']           = md5('a5db19398b1f6d6ccb96c410b7c93755'.$_SERVER['REMOTE_ADDR']);
                $_SESSION['identificador']  = $identificador_cliente;
                $_SESSION['nome']           = $nome;
                $_SESSION['plataforma']     = 'SITE';

                ?>

                <form id="form-cadastro-confirmacao" style="display: none;" action="../../../modulos/envio-email/index.php" method="POST">            
                    <input type="text" name="tipo-envio" value="formulario-cadastro-cliente" required>                  
                    <input type="text" name="nome" maxlength="50" value="<?= $nome ?>" required>
                    <input type="text" name="sobrenome" maxlength="50" value="<?= $sobrenome ?>" required>
                    <input type="text" name="cpf" id="cpf" maxlength="14" value="<?= $cpf ?>" required>
                    <input type="text" name="celular" id="celular" value="<?= $celular ?>" required>
                    <input type="email" name="email" maxlength="50" value="<?= $email ?>" required>
                </form>

                <?php
                    
                //REDIRECIONA PARA A TELA DE CADASTRO
                echo "<script>document.getElementById('form-cadastro-confirmacao').submit();</script>";

            } else {

                //PREENCHE A SESSION DE RETORNO COM ERRO
                $_SESSION['RETORNO'] = array('ERRO' => 'ERRO-CADASTRO');   
                
                //REDIRECIONA PARA A TELA DE CADASTRO
                echo "<script>window.history.back();</script>";

            }

        }

        include_once '../../../bd/desconecta.php';
        
    } else {    
            
        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='/';</script>";

    }

}
