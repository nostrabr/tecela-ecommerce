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
        $identificador_marca           = trim(strip_tags(filter_input(INPUT_POST, "identificador", FILTER_SANITIZE_STRING)));
        $nome                          = trim(strip_tags(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING)));     
        $logo                          = filter_input(INPUT_POST, 'arquivo', FILTER_SANITIZE_STRING);  
        unset($_SESSION['RETORNO']); 

        //CONFIRMA SE VEIO TUDO PREENCHIDO
        if(!empty($identificador_marca) & !empty($nome)){

            include_once '../../../../bd/conecta.php';

            //VERIFICA SE O NOME NÃO É REPETIDO
            $busca_marca = mysqli_query($conn, "SELECT id FROM marca WHERE nome = '$nome' AND identificador != '$identificador_marca'");
            if(mysqli_num_rows($busca_marca) > 0){                

                //PREENCHE A SESSION DE RETORNO COM ERRO
                $_SESSION['RETORNO'] = array(
                    'ERRO'       => 'ERRO-NOME-REPETIDO',
                    'nome'       => $nome,
                    'logo'       => $logo,
                );

                //REDIRECIONA PARA A TELA DE USUÁRIOS
                echo "<script>location.href='../../../marcas-edita.php?id=".$identificador_marca."';</script>";

            } else {

                //BUSCA OS DADOS DA MARCA
                $busca_marca = mysqli_query($conn, "SELECT logo FROM marca WHERE identificador = '$identificador_marca'");
                $marca       = mysqli_fetch_array($busca_marca);

                //INSTANCIA O NOME DA LOGO
                $nome_imagem_logo = '';

                //TRATA A LOGO CASO TENHA SIDO ALTERADA
                if($logo != $marca['logo']){
                    
                    //RETIRA A EXTENSÃO DA IMAGEM RECEBIDA
                    $extensao = mb_strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));                          

                    //SE A EXTENSÃO FOR VÁLIDA
                    if($extensao == 'png' | $extensao == 'jpg' | $extensao == 'jpeg' | $extensao == 'gif'){
                        
                        if($marca['logo'] != ''){
                            unlink("../../../../imagens/marcas/original/".$marca["logo"]);
                            unlink("../../../../imagens/marcas/pequena/".$marca["logo"]);
                            unlink("../../../../imagens/marcas/media/".$marca["logo"]);
                            unlink("../../../../imagens/marcas/grande/".$marca["logo"]);
                        }

                        //RENOMEIA
                        $nome_imagem_logo = md5(time()).'.'.$extensao;
                    
                        //MOVE A IMAGEM PARA O DIRETÓRIO DE IMAGENS COM O TAMANHO ORIGINAL
                        move_uploaded_file($_FILES['imagem']['tmp_name'], "../../../../imagens/marcas/original/".$nome_imagem_logo);
                        
                        //REDUZ E COLOCA E SEPARA EM PASTAS
                        reduz_imagem("../../../../imagens/marcas/original/".$nome_imagem_logo, $nome_imagem_logo, 100, "../../../../imagens/marcas/pequena/");
                        reduz_imagem("../../../../imagens/marcas/original/".$nome_imagem_logo, $nome_imagem_logo, 500, "../../../../imagens/marcas/media/");
                        reduz_imagem("../../../../imagens/marcas/original/".$nome_imagem_logo, $nome_imagem_logo, 1000, "../../../../imagens/marcas/grande/");

                    }

                } else {
                    $nome_imagem_logo = $marca['logo'];
                }

                //UPDATE NO BANCO
                mysqli_query($conn, "UPDATE marca SET nome = '$nome', logo = '$nome_imagem_logo' WHERE identificador = '$identificador_marca'");

                include_once '../../../../bd/desconecta.php';

                //REDIRECIONA PARA A TELA DE USUÁRIOS
                echo "<script>location.href='../../../marcas.php';</script>";

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
