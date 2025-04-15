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

        //FUNÇÃO QUE INVERTE A DATA
        function inverteData($data){
            $formata_data = explode("/",$data);
            return $formata_data[2]."-".$formata_data[1]."-".$formata_data[0];
        }   

        //RECEBE OS DADOS DO FORM
        $identificador_cliente = trim(strip_tags(filter_input(INPUT_POST, "identificador", FILTER_SANITIZE_STRING)));
        $nome                  = trim(strip_tags(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING)));    
        $sobrenome             = trim(strip_tags(filter_input(INPUT_POST, "sobrenome", FILTER_SANITIZE_STRING)));  
        $nascimento            = trim(strip_tags(filter_input(INPUT_POST, "nascimento")));  
        $cpf                   = trim(strip_tags(filter_input(INPUT_POST, "cpf", FILTER_SANITIZE_STRING)));    
        $email                 = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));    
        $senha                 = trim(strip_tags(filter_input(INPUT_POST, "senha", FILTER_SANITIZE_STRING)));    
        $telefone              = trim(strip_tags(filter_input(INPUT_POST, "telefone", FILTER_SANITIZE_STRING)));  
        $celular               = trim(strip_tags(filter_input(INPUT_POST, "celular", FILTER_SANITIZE_STRING)));     
        $nivel_usuario         = filter_var($_SESSION['nivel']);
        unset($_SESSION['RETORNO']); 
        
        //CONFIRMA SE VEIO TUDO PREENCHIDO E NÃO É NÍVEL USUÁRIO
        if(!empty($identificador_cliente) & !empty($nome) & !empty($sobrenome) & !empty($cpf) & !empty($email) & !empty($celular) & $nivel_usuario != 'U'){
            
            include_once '../../../../bd/conecta.php';

            //VERIFICA SE O E-MAIL NÃO É REPETIDO
            $busca_cliente = mysqli_query($conn, "SELECT id FROM cliente WHERE email = '$email' AND identificador != '$identificador_cliente'");

            if(mysqli_num_rows($busca_cliente) > 0){                

                //PREENCHE A SESSION DE RETORNO COM ERRO
                $_SESSION['RETORNO'] = array(
                    'ERRO' => 'ERRO-EMAIL-REPETIDO'
                );

                //REDIRECIONA PARA A TELA DE USUÁRIOS
                echo "<script>location.href='../../../clientes-edita.php?id=".$identificador_cliente."';</script>";

            } else {

                //INVERTE A DATA DE NASCIMENTO
                if($nascimento != ''){
                    $nascimento = inverteData($nascimento);
                }

                //SE A SENHA VEIO DIFERENTE DE BRANCO TROCA NO BANCO
                if($senha != ''){
                    
                    //CRIPTOGRAFA A SENHA
                    $senha = md5($senha);
                    
                    //ALTERA CLIENTE
                    //mysqli_query($conn, "UPDATE cliente SET nome = '$nome', sobrenome = '$sobrenome', nascimento = '$nascimento', cpf = '$cpf', email = '$email', senha = '$senha', telefone = '$telefone', celular = '$celular', cep = '$cep', rua = '$rua', numero = '$numero', bairro = '$bairro', complemento = '$complemento', referencia = '$referencia', cidade = '$cidade', estado = '$estado' WHERE identificador = '$identificador_cliente'");
                    mysqli_query($conn, "UPDATE cliente SET nome = '$nome', sobrenome = '$sobrenome', nascimento = '$nascimento', cpf = '$cpf', email = '$email', senha = '$senha', telefone = '$telefone', celular = '$celular' WHERE identificador = '$identificador_cliente'");

                } else {

                    //ALTERA CLIENTE
                    //mysqli_query($conn, "UPDATE cliente SET nome = '$nome', sobrenome = '$sobrenome', nascimento = '$nascimento', cpf = '$cpf', email = '$email', telefone = '$telefone', celular = '$celular', cep = '$cep', rua = '$rua', numero = '$numero', bairro = '$bairro', complemento = '$complemento', referencia = '$referencia', cidade = '$cidade', estado = '$estado' WHERE identificador = '$identificador_cliente'");
                    mysqli_query($conn, "UPDATE cliente SET nome = '$nome', sobrenome = '$sobrenome', nascimento = '$nascimento', cpf = '$cpf', email = '$email', telefone = '$telefone', celular = '$celular' WHERE identificador = '$identificador_cliente'");

                }
                    
                include_once '../../../../bd/desconecta.php';

                //REDIRECIONA PARA A TELA DE USUÁRIOS
                echo "<script>location.href='../../../clientes.php';</script>";
                     
            }
        
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
