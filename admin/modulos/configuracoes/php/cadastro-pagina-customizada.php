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
        $titulo                        = trim(strip_tags(filter_input(INPUT_POST, "titulo", FILTER_SANITIZE_STRING)));
        $descricao                     = trim(strip_tags(filter_input(INPUT_POST, "descricao", FILTER_SANITIZE_STRING)));
        $palavras_chave                = trim(strip_tags(filter_input(INPUT_POST, "palavras-chave", FILTER_SANITIZE_STRING)));
        $mostrar_cabecalho             = trim(strip_tags(filter_input(INPUT_POST, "mostrar-cabecalho", FILTER_SANITIZE_STRING)));        
        $mostrar_rodape                = trim(strip_tags(filter_input(INPUT_POST, "mostrar-rodape", FILTER_SANITIZE_STRING)));     
        $mostrar_menu_mobile           = trim(strip_tags(filter_input(INPUT_POST, "mostrar-menu-mobile", FILTER_SANITIZE_STRING)));  
        $categoria                     = trim(strip_tags(filter_input(INPUT_POST, "categoria", FILTER_SANITIZE_NUMBER_INT)));  
        $conteudo                      = filter_input(INPUT_POST, "summernote");
        $codigo_fonte_conteudo         = $_POST['codigo-fonte'];
        $identificador_usuario_session = filter_var($_SESSION['identificador']);
        $nivel_usuario                 = filter_var($_SESSION['nivel']);

        //CONFIRMA SE VEIO TUDO PREENCHIDO
        if(!empty($titulo) & $nivel_usuario != 'U'){

            if($mostrar_cabecalho == 'on'){
                $mostrar_cabecalho = 1;
            } else {
                $mostrar_cabecalho = 0;
            }

            if($mostrar_rodape == 'on'){
                $mostrar_rodape = 1;
            } else {
                $mostrar_rodape = 0;
            }

            if($mostrar_menu_mobile == 'on'){
                $mostrar_menu_mobile = 1;
            } else {
                $mostrar_menu_mobile = 0;
            }

            $identificador = md5(date('Y-m-d H:m:i').$titulo.$descricao.$palavras_chave.$mostrar_cabecalho.$mostrar_menu_mobile.$mostrar_rodape);

            include_once '../../../../bd/conecta.php';

            mysqli_query($conn, "INSERT INTO pagina_customizada (identificador,titulo,descricao,palavras_chave,conteudo,mostrar_cabecalho,mostrar_rodape,mostrar_menu_mobile,categoria) VALUES ('$identificador','$titulo','$descricao','$palavras_chave','$codigo_fonte_conteudo','$mostrar_cabecalho','$mostrar_rodape','$mostrar_menu_mobile','$categoria')");
            
            include_once '../../../../bd/desconecta.php';

            //REDIRECIONA PARA A TELA DE USUÁRIOS
            echo "<script>location.href='../../../configuracoes-paginas-customizadas.php';</script>";

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
