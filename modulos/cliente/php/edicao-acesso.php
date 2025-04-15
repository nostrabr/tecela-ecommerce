<?php

//INICIA A SESSÃO
session_start();

//RECEBE OS DADOS DO CADASTRO 
$email                   = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));   
$senha                   = trim(strip_tags(filter_input(INPUT_POST, "senha", FILTER_SANITIZE_STRING)));  
$identificador_seguranca = trim(strip_tags(filter_input(INPUT_POST, "identificador_seguranca", FILTER_SANITIZE_STRING)));  
$codigo                  = trim(strip_tags(filter_input(INPUT_POST, "codigo", FILTER_SANITIZE_NUMBER_INT)));  
$identificador_cliente   = filter_var($_SESSION['identificador']);
unset($_SESSION['RETORNO']); 

if(!empty($email) & !empty($identificador_seguranca) & !empty($codigo) & !empty($identificador_cliente)){

    include_once '../../../bd/conecta.php';

    //VALIDA O CÓDIGO DE SEGURANÇA
    $busca_verificador_seguranca = mysqli_query($conn, "SELECT id FROM verificacao_seguranca WHERE identificador = '$identificador_seguranca' AND codigo = '$codigo'");

    //SE ESTIVER CERTO, PROSSEGUE
    if(mysqli_num_rows($busca_verificador_seguranca) > 0){
        
        //SE VEIO A SENHA PARA ALTERAR TAMBÈM
        if($senha != ''){

            $senha = md5($senha);
            mysqli_query($conn, "UPDATE cliente SET email = '$email', senha = '$senha' WHERE identificador = '$identificador_cliente'");

        //SENÃO SÓ ALTERA O E-MAIL
        } else {            

            mysqli_query($conn, "UPDATE cliente SET email = '$email' WHERE identificador = '$identificador_cliente'");

        }

        if(!mysqli_error($conn)){

            
            //PREENCHE A SESSION DE RETORNO COM ERRO
            $_SESSION['RETORNO'] = array(
                'ERRO'   => false,
                'STATUS' => 'SUCESSO-EDICAO'
            );  
                
            //REDIRECIONA PARA A TELA DE ACESSO
            echo "<script>location.href='../../../cliente-acesso';</script>";

        } else {

            //PREENCHE A SESSION DE RETORNO COM ERRO
            $_SESSION['RETORNO'] = array(
                'ERRO'   => 'ERRO-EDICAO',
                'STATUS' => 'ERRO'
            );  
            
            ?>

            <form id="form-edicao-confirmacao-retorno" style="display: none;" action="../../../cliente-acesso-confirmacao" method="POST">     
                <input type="email" name="email" maxlength="50" value="<?= $email ?>" required>
                <input type="password" name="senha" id="senha" maxlength="32" minlength="8" value="<?= $senha ?>">
                <input type="text" name="identificador_seguranca" maxlength="32" value="<?= $identificador_seguranca  ?>" required>
            </form>

            <?php
                
            //REDIRECIONA PARA A TELA DE CADASTRO
            echo "<script>document.getElementById('form-edicao-confirmacao-retorno').submit();</script>";

        }
        
    //SE ESTIVER ERRADO, VOLTA COM ERRO
    } else {
        
        //PREENCHE A SESSION DE RETORNO COM ERRO
        $_SESSION['RETORNO'] = array(
            'ERRO'   => 'ERRO-CODIGO',
            'STATUS' => 'ERRO'
        );   
        
        ?>

        <form id="form-edicao-confirmacao-retorno" style="display: none;" action="../../../cliente-acesso-confirmacao" method="POST">     
            <input type="email" name="email" maxlength="50" value="<?= $email ?>" required>
            <input type="password" name="senha" id="senha" maxlength="32" minlength="8" value="<?= $senha ?>" required>
            <input type="text" name="identificador_seguranca" maxlength="32" value="<?= $identificador_seguranca  ?>" required>
        </form>

        <?php
            
        //REDIRECIONA PARA A TELA DE EDIÇÃO
        echo "<script>document.getElementById('form-edicao-confirmacao-retorno').submit();</script>";
        
    }

    include_once '../../../bd/desconecta.php';
    
} else {    
        
    //REDIRECIONA PARA A TELA DE LOGIN
    echo "<script>location.href='/';</script>";

}
