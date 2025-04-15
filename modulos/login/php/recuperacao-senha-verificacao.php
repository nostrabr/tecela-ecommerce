<?php

//INICIA A SESSÃO
session_start();

//RECEBE OS DADOS DO FORM
$email                   = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));   
$codigo                  = trim(strip_tags(filter_input(INPUT_POST, "codigo", FILTER_SANITIZE_EMAIL)));  
$identificador_seguranca = trim(strip_tags(filter_input(INPUT_POST, "identificador_seguranca", FILTER_SANITIZE_STRING)));
unset($_SESSION['RECUPERACAO-SENHA']);
unset($_SESSION['RETORNO']);

if(!empty($email) & !empty($identificador_seguranca) & !empty($codigo)){

    include_once '../../../bd/conecta.php';

    //BUSCA O CLIENTE
    $busca_cliente = mysqli_query($conn, "SELECT identificador FROM cliente WHERE email = '$email'");
    $cliente       = mysqli_fetch_array($busca_cliente);
        
    //VALIDA O CÓDIGO DE SEGURANÇA
    $busca_verificador_seguranca = mysqli_query($conn, "SELECT id FROM verificacao_seguranca WHERE identificador = '$identificador_seguranca' AND codigo = '$codigo'");
    
    //SE ESTIVER CERTO, PROSSEGUE
    if(mysqli_num_rows($busca_verificador_seguranca) > 0){
        
        //PREENCHE A SESSION DE CONFIRMAÇÃO DE TROCA
        $_SESSION['RECUPERACAO-SENHA'] = array(
            'STATUS'        => 'OK',
            'IDENTIFICADOR' => $cliente['identificador']
        );   
        
        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='../../../login-alterar-senha';</script>";

    } else {

        //PREENCHE A SESSION DE RETORNO COM ERRO
        $_SESSION['RETORNO'] = array(
            'ERRO'   => 'ERRO-CODIGO',
            'STATUS' => 'ERRO'
        );   
        
        ?>

        <form id="form-recuperacao-senha-confirmacao-codigo-retorno" style="display: none;" action="../../../login-recuperacao-senha-confirmacao.php" method="POST">     
            <input type="email" name="email" maxlength="50" value="<?= $email ?>" required>
            <input type="text" name="identificador_seguranca" maxlength="32" value="<?= $identificador_seguranca  ?>" required>
        </form>

        <?php
            
        //REDIRECIONA PARA A TELA DE EDIÇÃO
        echo "<script>document.getElementById('form-recuperacao-senha-confirmacao-codigo-retorno').submit();</script>";
        
    }
    
    include_once '../../../bd/desconecta.php';

} else {

    //VERIFICA SE VEIO DO AJAX
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                
        //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
        $dados[] = array(
            "status" => "ERRO"
        );
        echo json_encode($dados);
        
    } else {
        
        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='../../../index.php';</script>";
        
    }

}

