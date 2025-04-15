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
        $ordem                         = filter_input(INPUT_POST, 'ordem', FILTER_SANITIZE_STRING); 
        $titulo                        = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);  
        $link                          = filter_input(INPUT_POST, 'link', FILTER_SANITIZE_URL);  
        $identificador_usuario_session = filter_var($_SESSION['identificador']);
        $nivel_usuario                 = filter_var($_SESSION['nivel']);

        //CONFIRMA SE VEIO TUDO PREENCHIDO
        if(!empty($ordem) & $nivel_usuario != 'U'){

            include_once '../../../../bd/conecta.php';

            //TRATA A IMAGEM
            //RETIRA A EXTENSÃO DA IMAGEM RECEBIDA
            $extensao        = mb_strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));  

            //RENOMEIA
            $nome_imagem = md5(time().'-secundaria').'.'.$extensao;

            //DIRETÓRIO DE IMAGENS DE BANNERS
            $diretorio = "../../../../imagens/banners-secundarios/original/";

            //MOVE A IMAGEM PARA O DIRETÓRIO
            move_uploaded_file($_FILES['imagem']['tmp_name'], $diretorio.$nome_imagem);

            //GERA UMA MINIATURA
            reduz_imagem("../../../../imagens/banners-secundarios/original/".$nome_imagem, $nome_imagem, 100, "../../../../imagens/banners-secundarios/pequena/");

            //TRATA A ORDEM
            //CONSULTA A ORDEM ATUAL
            $consulta_qtde_ordem = mysqli_query($conn, "SELECT COUNT(ordem) AS total_ordem FROM banner_secundario"); 
            $qtde_ordens = mysqli_fetch_array($consulta_qtde_ordem); 

            //GERA UM IDENTIFICADOR
            $identificador = md5(date('Y-m-d H:m:i').'-secundaria'.$ordem.$nome_imagem);

            //SE A QUE VEIO É MAIOR, ADICIONA
            if($ordem > $qtde_ordens["total_ordem"]){    
                
                //INSERE NO BANCO
                mysqli_query($conn, "INSERT INTO banner_secundario (identificador, ordem, imagem, titulo, link, cadastrado_por) VALUES ('$identificador','$ordem','$nome_imagem','$titulo','$link','$identificador_usuario_session')");    

            //SENÃO, REORDENA    
            } else {
            
                $ordem_novo_banner = $ordem;
                
                $nova_ordem = $qtde_ordens['total_ordem'] + 1;
                $velha_ordem = $qtde_ordens['total_ordem'];
                
                while($velha_ordem >= $ordem){
                    
                    $busca_id = mysqli_query($conn, "SELECT id FROM banner_secundario WHERE ordem = '$velha_ordem'");
                    $velha_ordem_id = mysqli_fetch_array($busca_id);
                    $id_ordem = $velha_ordem_id["id"];
                    
                    mysqli_query($conn, "UPDATE banner_secundario SET ordem = '$nova_ordem' WHERE id = '$id_ordem'");
                    
                    $nova_ordem--;
                    $velha_ordem--;
                    
                }
                    
                //INSERE NO BANCO
                mysqli_query($conn, "INSERT INTO banner_secundario (identificador, ordem, imagem, titulo, link, cadastrado_por) VALUES ('$identificador','$ordem_novo_banner','$nome_imagem','$titulo','$link','$identificador_usuario_session')");
                
            }
            
            include_once '../../../../bd/desconecta.php';

            //REDIRECIONA PARA A TELA DE USUÁRIOS
            echo "<script>location.href='../../../configuracoes-design-banners-secundarios.php';</script>";

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
