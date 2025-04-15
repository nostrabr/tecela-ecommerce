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
        $nome                          = trim(strip_tags(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING)));
        $email                         = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));
        $usuario                       = trim(strip_tags(filter_input(INPUT_POST, "usuario", FILTER_SANITIZE_STRING)));
        $senha                         = trim(strip_tags(filter_input(INPUT_POST, "senha", FILTER_SANITIZE_STRING)));
        $nivel                         = trim(strip_tags(filter_input(INPUT_POST, "nivel", FILTER_SANITIZE_STRING)));
        $nivel_usuario                 = filter_var($_SESSION['nivel']);
        $identificador_usuario_session = filter_var($_SESSION['identificador']);
        unset($_SESSION['RETORNO']); 

        //CONFIRMA SE VEIO TUDO PREENCHIDO
        if(!empty($nome) & !empty($email) & !empty($usuario) & !empty($senha) & !empty($nivel)){

            include_once '../../../../bd/conecta.php';

            //VERIFICA SE O E-MAIL E O LOGIN NÃO SÃO REPETIDOS
            $busca_usuario = mysqli_query($conn, "SELECT id FROM usuario WHERE email = '$email' OR login = '$usuario'");
            if(mysqli_num_rows($busca_usuario) > 0){                

                //PREENCHE A SESSION DE RETORNO COM ERRO
                $_SESSION['RETORNO'] = array(
                    'ERRO'    => 'ERRO-EMAIL-LOGIN',
                    'nome'    => $nome,
                    'email'   => $email,
                    'usuario' => $usuario,
                    'nivel'   => $nivel
                );

                //REDIRECIONA PARA A TELA DE USUÁRIOS
                echo "<script>location.href='../../../configuracoes-cadastra-usuarios.php';</script>";

            } else {

                //CRIPTOGRAFA A SENHA
                $senha = md5($senha);

                //SE FOR ADMINISTRADOR, GARANTE QUE VAI SER NÍVEL USUÁRIO
                if($nivel_usuario == 'A'){ $nivel = 'U'; }

                //GERA UM CÓDIGO IDENTIFICADOR
                $identificador_usuario = md5(date('Y-m-d H:i:s').$nome.$email.$usuario.$senha.$nivel);

                //INSERE NO BANCO
                mysqli_query($conn, "INSERT INTO usuario (identificador, nome, email, login, senha, nivel, cadastrado_por) VALUES ('$identificador_usuario','$nome','$email','$usuario','$senha','$nivel','$identificador_usuario_session')");

                include_once '../../../../bd/desconecta.php';

                //REDIRECIONA PARA A TELA DE USUÁRIOS
                echo "<script>location.href='../../../configuracoes-usuarios.php';</script>";

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
