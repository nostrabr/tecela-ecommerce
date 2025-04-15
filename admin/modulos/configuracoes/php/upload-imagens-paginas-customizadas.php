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
        
        include_once '../../../../bd/conecta.php';

        $busca_loja = mysqli_query($conn, "SELECT site FROM loja WHERE id = 1");
        $loja       = mysqli_fetch_array($busca_loja);

        $return_value = "";

        if ($_FILES['file']['name']) {

            if (!$_FILES['file']['error']) {

                $name = md5(date("Y-m-d H:i:s"));        
                $ext = mb_strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
                $filename = $name . '.' . $ext;
                $destination = '../../../../imagens/paginas-customizadas/'.$filename;
                $location = $_FILES['file']['tmp_name'];
                move_uploaded_file($location, $destination);                
                $return_value = $loja['site'].'/imagens/paginas-customizadas/'.$filename;

            } else {

                $return_value = 'ERRO UPLOAD IMAGEM';

            }

        }

        echo $return_value;

        include_once '../../../../bd/desconecta.php';

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