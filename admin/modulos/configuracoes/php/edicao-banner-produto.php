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
        $ordem_form                    = filter_input(INPUT_POST, 'ordem', FILTER_SANITIZE_STRING);
        $link                          = filter_input(INPUT_POST, 'link', FILTER_SANITIZE_URL);  
        $identificador_usuario_session = filter_var($_SESSION['identificador']);
        $nivel_usuario                 = filter_var($_SESSION['nivel']);

        //CONFIRMA SE VEIO TUDO PREENCHIDO
        if(!empty($ordem_form) & $nivel_usuario != 'U'){

            include_once '../../../../bd/conecta.php';

            //RECEBE OS DADOS DO FORM
            $id               = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
            $nome_imagem_form = filter_input(INPUT_POST, 'arquivo', FILTER_SANITIZE_STRING);

            //VERIFICA SE A IMAGEM OU ORDEM FOI ATUALIZADA
            $consulta_banner  = mysqli_query($conn, "SELECT imagem, ordem FROM banner_produto WHERE id = '$id'");
            $banner_banco     = mysqli_fetch_array($consulta_banner);

            //ALTERA O LINK
            mysqli_query($conn, "UPDATE banner_produto SET link = '$link' WHERE id = '$id'");     

            //SE A IMAGEM FOI, TRATA
            if($banner_banco["imagem"] !== $nome_imagem_form){
                
                //DELETA AS IMAGENS VELHAS
                unlink("../../../../imagens/banners-produto/original/".$banner_banco["imagem"]);
                unlink("../../../../imagens/banners-produto/pequena/".$banner_banco["imagem"]);
                
                //RETIRA A EXTENSÃO DA IMAGEM RECEBIDA
                $extensao = mb_strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));  
                    
                //RENOMEIA
                $nome_imagem = md5(time()).'.'.$extensao;
                
                //DIRETÓRIO DE IMAGENS DE BANNERS
                $diretorio = "../../../../imagens/banners-produto/original/";
                
                //MOVE A IMAGEM PARA O DIRETÓRIO
                move_uploaded_file($_FILES['imagem']['tmp_name'], $diretorio.$nome_imagem);
                
                //GERA UMA MINIATURA
                reduz_imagem("../../../../imagens/banners-produto/original/".$nome_imagem, $nome_imagem, 100, "../../../../imagens/banners-produto/pequena/");
                
                mysqli_query($conn, "UPDATE banner_produto SET imagem = '$nome_imagem' WHERE id = '$id'");            
                
            } 

            //SE A ORDEM FOI, REORDENA
            if($banner_banco["ordem"] !== $ordem_form){
                
                //CONSULTA A ORDEM ATUAL
                $consulta_qtde_ordem = mysqli_query($conn, "SELECT COUNT(id) AS total_ordem FROM banner_produto"); 
                $qtde_ordens = mysqli_fetch_array($consulta_qtde_ordem);
                
                $nova_ordem = $ordem_form;
                $ordem_antiga = $banner_banco["ordem"];
                $ordem_final = $qtde_ordens["total_ordem"];
                
                if($nova_ordem > $ordem_antiga){
                    
                    $contador = $ordem_antiga;
                    $fim_contagem = $nova_ordem;
                    
                    $ordem_temporaria = $ordem_final + 1;
                    
                    mysqli_query($conn, "UPDATE banner_produto SET ordem = '$ordem_temporaria' WHERE id = '$id'"); 
                    
                    while($contador < $fim_contagem){
                        
                        $proximo_da_ordem = $contador + 1;
                        
                        $busca_id = mysqli_query($conn, "SELECT id FROM banner_produto WHERE ordem = '$proximo_da_ordem'");
                        $banner_id = mysqli_fetch_array($busca_id);
                        $id_proximo = $banner_id['id'];
                        
                        mysqli_query($conn, "UPDATE banner_produto SET ordem = '$contador' WHERE id = '$id_proximo'"); 
                        
                        $contador++;
                        
                    }
                    
                    mysqli_query($conn, "UPDATE banner_produto SET ordem = '$nova_ordem' WHERE id = '$id'"); 
                    
                } else {
                    
                    $contador = $ordem_antiga;
                    $fim_contagem = $nova_ordem;
                    
                    $ordem_temporaria = $ordem_final + 1;
                    
                    mysqli_query($conn, "UPDATE banner_produto SET ordem = '$ordem_temporaria' WHERE id = '$id'"); 
                    
                    while($contador > $fim_contagem){
                        
                        $proximo_da_ordem = $contador - 1;
                        
                        $busca_id = mysqli_query($conn, "SELECT id FROM banner_produto WHERE ordem = '$proximo_da_ordem'");
                        $banner_id = mysqli_fetch_array($busca_id);
                        $id_proximo = $banner_id['id'];
                        
                        mysqli_query($conn, "UPDATE banner_produto SET ordem = '$contador' WHERE id = '$id_proximo'"); 
                        
                        $contador--;
                        
                    }
                    
                    mysqli_query($conn, "UPDATE banner_produto SET ordem = '$nova_ordem' WHERE id = '$id'"); 
                    
                }
                
            }
            
            include_once '../../../../bd/desconecta.php';

            //REDIRECIONA PARA A TELA DE USUÁRIOS
            echo "<script>location.href='../../../configuracoes-design-banners-produto.php';</script>";

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
