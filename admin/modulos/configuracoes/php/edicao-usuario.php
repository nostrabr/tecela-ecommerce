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
        $identificador_usuario         = trim(strip_tags(filter_input(INPUT_POST, "identificador", FILTER_SANITIZE_STRING)));
        $nome                          = trim(strip_tags(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING)));
        $email                         = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL)));
        $usuario                       = trim(strip_tags(filter_input(INPUT_POST, "usuario", FILTER_SANITIZE_STRING)));
        $senha                         = trim(strip_tags(filter_input(INPUT_POST, "senha", FILTER_SANITIZE_STRING)));
        $nivel                         = trim(strip_tags(filter_input(INPUT_POST, "nivel", FILTER_SANITIZE_STRING)));
        $nivel_usuario                 = filter_var($_SESSION['nivel']);
        $identificador_usuario_session = filter_var($_SESSION['identificador']);
        unset($_SESSION['RETORNO']); 

        //CONFIRMA SE VEIO TUDO PREENCHIDO
        if(!empty($identificador_usuario) & !empty($nome) & !empty($email) & !empty($usuario) & !empty($nivel)){

            include_once '../../../../bd/conecta.php';

            //VERIFICA SE O E-MAIL E O LOGIN NÃO SÃO REPETIDOS
            $busca_usuario = mysqli_query($conn, "SELECT * FROM usuario WHERE (email = '$email' OR login = '$usuario') AND identificador != '$identificador_usuario'");
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
                echo "<script>location.href='../../../configuracoes-edita-usuarios.php?id=".$identificador_usuario."';</script>";

            } else {

                //SE FOR ADMINISTRADOR, GARANTE QUE VAI SER NÍVEL USUÁRIO
                if($nivel_usuario == 'A' & $identificador_usuario != $identificador_usuario_session){ $nivel = 'U'; }

                //VERIFICA SE VEIO UMA NOVA SENHA
                if(isset($senha) & $senha != ''){

                    //CRIPTOGRAFA A SENHA DO FORM
                    $senha = md5($senha);

                    //UPDATE REGISTRO
                    mysqli_query($conn, "UPDATE usuario SET nome = '$nome', email = '$email', login = '$usuario', senha = '$senha', nivel = '$nivel' WHERE identificador = '$identificador_usuario'");

                } else {

                    //UPDATE REGISTRO
                    mysqli_query($conn, "UPDATE usuario SET nome = '$nome', email = '$email', login = '$usuario', nivel = '$nivel' WHERE identificador = '$identificador_usuario'");

                }

                //ALTERA O NOME NA SESSION PARA O CASO DE SER A MESMA DO USUÁRIO ATUAL
                if($identificador_usuario == $identificador_usuario_session){
                    $_SESSION['nome'] = $nome;
                }

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
