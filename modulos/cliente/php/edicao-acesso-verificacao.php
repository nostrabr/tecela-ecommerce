<?php

//INICIA A SESSÃO
session_start();

//RECEBE OS DADOS DO CADASTRO 
$email             = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));  
$senha             = trim(strip_tags(filter_input(INPUT_POST, "senha", FILTER_SANITIZE_STRING)));  
$email_confirmacao = trim(strip_tags(filter_input(INPUT_POST, "email-confirmacao", FILTER_SANITIZE_EMAIL)));  
$senha_confirmacao = trim(strip_tags(filter_input(INPUT_POST, "senha-confirmacao", FILTER_SANITIZE_STRING)));  
$identificador     = filter_var($_SESSION['identificador']);
$troca_senha       = false;
$troca_email       = false;
$senhas_conferem   = false;
$emails_conferem   = false;
$form_action       = '';
unset($_SESSION['RETORNO']); 

if(!empty($identificador)){

    include_once '../../../bd/conecta.php';
    
    //VALIDA E-MAIL REPETIDO
    $busca_email_repetido = mysqli_query($conn, "SELECT email FROM cliente WHERE email = '$email' AND identificador != '$identificador'");

    //SE JÁ EXISTE
    if(mysqli_num_rows($busca_email_repetido) > 0){

        //PREENCHE A SESSION DE RETORNO COM ERRO
        $_SESSION['RETORNO'] = array(
            'ERRO'   => 'ERRO-EMAIL-REPETIDO',
            'STATUS' => 'ERRO'
        ); 

        ?>
            <form id="form-edicao-confirmacao" style="display: none;" action="../../../cliente-acesso-verificacao" method="POST">     
                <input type="email" name="email" maxlength="50" value="<?= $email ?>" required>
                <input type="password" name="senha" id="senha" maxlength="32" minlength="8" value="<?= $senha ?>">
            </form>
        <?php
        
        //REDIRECIONA PARA A TELA DE CADASTRO
        echo "<script>document.getElementById('form-edicao-confirmacao').submit();</script>";

    } else {

        //VALIDA O CÓDIGO DO CLIENTE
        $busca_cliente = mysqli_query($conn, "SELECT email FROM cliente WHERE identificador = '$identificador'");

        //SE ESTIVER CERTO, PROSSEGUE
        if(mysqli_num_rows($busca_cliente) > 0){   

            $cliente = mysqli_fetch_array($busca_cliente);

            //SE TROCA O E-MAIL, VERIFICA
            if(isset($_POST['email'])){
                $troca_email = true;
                if($email === $email_confirmacao){
                    $emails_conferem = true;
                }
            } else {
                $email = $cliente['email'];
            }   

            //SE É PARA ALTERAR A SENHA, VERIFICA
            if(isset($_POST['senha'])){
                $troca_senha = true;
                if($senha === $senha_confirmacao){
                    $senhas_conferem = true;
                }
            }

            //SE TUDO CERTO, PROSSEGUE PRO ENVIO DO E-MAIL DE VERIFICAÇÃO
            if(($troca_email & $troca_senha & $emails_conferem & $senhas_conferem) | ($troca_email & !$troca_senha & $emails_conferem) | (!$troca_email & $troca_senha & $senhas_conferem)){
                                
                ?>
                    <form id="form-edicao-confirmacao" style="display: none;" action="../../../modulos/envio-email/index.php" method="POST">        
                        <input type="text" name="tipo-envio" value="formulario-edicao-acesso-cliente-verificacao" required>   
                        <input type="email" name="email" maxlength="50" value="<?= $email ?>" required>
                        <?php if($troca_senha){ ?><input type="password" name="senha" id="senha" maxlength="32" minlength="8" value="<?= $senha ?>"><?php } ?>
                    </form>
                <?php
                
            } else {
                
                //PREENCHE A SESSION DE RETORNO COM ERRO
                $_SESSION['RETORNO'] = array(
                    'ERRO'   => 'ERRO-DADOS-NAO-CONFEREM',
                    'STATUS' => 'ERRO'
                ); 

                ?>
                    <form id="form-edicao-confirmacao" style="display: none;" action="../../../cliente-acesso-verificacao" method="POST">     
                        <input type="email" name="email" maxlength="50" value="<?= $email ?>" required>
                        <input type="password" name="senha" id="senha" maxlength="32" minlength="8" value="<?= $senha ?>">
                    </form>
                <?php

            }
            
            //REDIRECIONA PARA A TELA DE CADASTRO
            echo "<script>document.getElementById('form-edicao-confirmacao').submit();</script>";


        //SE ESTIVER ERRADO, VOLTA COM ERRO
        } else {       
            
            //REDIRECIONA PARA O LOGOUT
            echo "<script>location.href='../../../logout';</script>";
            
        }

    }

    include_once '../../../bd/desconecta.php';
    
} else {    
        
    //REDIRECIONA PARA A TELA DE LOGIN
    echo "<script>location.href='/';</script>";

}
