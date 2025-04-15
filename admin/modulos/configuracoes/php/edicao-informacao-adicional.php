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

        function reduz_imagem($target, $name, $largura, $pasta){
            $extensao = mb_strtolower(pathinfo($target, PATHINFO_EXTENSION));  
            if(strcasecmp($extensao,'jpg') == 0 | strcasecmp($extensao,'jpeg') == 0){
                $img = imagecreatefromjpeg($target);
            } else if(strcasecmp($extensao,'png') == 0){
                $img = imagecreatefrompng($target);
            } else if(strcasecmp($extensao,'gif') == 0){
                $img = imagecreatefromgif($target);
            }
            $x = imagesx($img);
            $y = imagesy($img);
            $altura = ($largura*$y) / $x;
            $nova_imagem = imagecreatetruecolor($largura, $altura);
            imagealphablending( $nova_imagem, false );
            imagesavealpha( $nova_imagem, true );
            imagecopyresampled($nova_imagem, $img, 0, 0, 0, 0, $largura, $altura, $x, $y);    
            if(strcasecmp($extensao,'jpg') == 0 | strcasecmp($extensao,'jpeg') == 0){
                imagejpeg($nova_imagem, $pasta.'/'.$name);
            } else if(strcasecmp($extensao,'png') == 0){
                imagepng($nova_imagem, $pasta.'/'.$name, 9);
            } else if(strcasecmp($extensao,'gif') == 0){
                imagegif($nova_imagem, $pasta.'/'.$name);
            }
            imagedestroy($img);
            imagedestroy($nova_imagem);
        }

        //RECEBE OS DADOS DO FORM
        $titulo                        = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
        $descricao                     = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);  
        $identificador_usuario_session = filter_var($_SESSION['identificador']);
        $nivel_usuario                 = filter_var($_SESSION['nivel']);

        //CONFIRMA SE VEIO TUDO PREENCHIDO
        if(!empty($titulo) & $nivel_usuario != 'U'){

            include_once '../../../../bd/conecta.php';

            //RECEBE OS DADOS DO FORM
            $id               = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
            $nome_imagem_form = filter_input(INPUT_POST, 'arquivo', FILTER_SANITIZE_STRING);

            //VERIFICA SE A IMAGEM OU ORDEM FOI ATUALIZADA
            $consulta_informacao_adicional = mysqli_query($conn, "SELECT imagem FROM informacao_adicional WHERE id = '$id'");
            $informacao_adicional_banco    = mysqli_fetch_array($consulta_informacao_adicional);

            //ALTERA OS DADOS
            mysqli_query($conn, "UPDATE informacao_adicional SET titulo = '$titulo', descricao = '$descricao' WHERE id = '$id'");     

            //SE A IMAGEM FOI, TRATA
            if($informacao_adicional_banco["imagem"] !== $nome_imagem_form){
                
                //DELETA AS IMAGENS VELHAS
                unlink("../../../../imagens/informacoes-adicionais/original/".$informacao_adicional_banco["imagem"]);
                unlink("../../../../imagens/informacoes-adicionais/pequena/".$informacao_adicional_banco["imagem"]);
                
                //RETIRA A EXTENSÃO DA IMAGEM RECEBIDA
                $extensao = mb_strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));  
                    
                //RENOMEIA
                $nome_imagem = md5(time()).'.'.$extensao;
                
                //DIRETÓRIO DE IMAGENS DE INFORMAÇÕES ADICIONAIS
                $diretorio = "../../../../imagens/informacoes-adicionais/original/";
                
                //MOVE A IMAGEM PARA O DIRETÓRIO
                move_uploaded_file($_FILES['imagem']['tmp_name'], $diretorio.$nome_imagem);
                
                //GERA UMA MINIATURA
                reduz_imagem("../../../../imagens/informacoes-adicionais/original/".$nome_imagem, $nome_imagem, 64, "../../../../imagens/informacoes-adicionais/pequena/");
                
                mysqli_query($conn, "UPDATE informacao_adicional SET imagem = '$nome_imagem' WHERE id = '$id'");            
                
            } 
            
            include_once '../../../../bd/desconecta.php';

            //REDIRECIONA PARA A TELA DE USUÁRIOS
            echo "<script>location.href='../../../configuracoes-design-informacoes-adicionais.php';</script>";

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
