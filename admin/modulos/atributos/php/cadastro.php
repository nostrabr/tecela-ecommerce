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
        $nome                          = trim(strip_tags(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING)));
        $visualizacao                  = trim(strip_tags(filter_input(INPUT_POST, "visualizacao", FILTER_SANITIZE_STRING)));
        $n_caracteristicas             = trim(strip_tags(filter_input(INPUT_POST, "n_caracteristicas", FILTER_SANITIZE_NUMBER_INT)));
        $identificador_usuario_session = filter_var($_SESSION['identificador']);
        unset($_SESSION['RETORNO']); 

        //CONFIRMA SE VEIO TUDO PREENCHIDO
        if(!empty($nome) & !empty($visualizacao)){
            
            include_once '../../../../bd/conecta.php';

            //VERIFICA SE O NOME NÃO É REPETIDO
            $busca_atributo = mysqli_query($conn, "SELECT id FROM atributo WHERE nome = '$nome' AND status = 1");
            if(mysqli_num_rows($busca_atributo) > 0){                

                //PREENCHE A SESSION DE RETORNO COM ERRO
                $_SESSION['RETORNO'] = array(
                    'ERRO'         => 'ERRO-NOME-REPETIDO',
                    'nome'         => $nome,
                    'visualizacao' => $visualizacao
                );

                //REDIRECIONA PARA A TELA DE USUÁRIOS
                echo "<script>location.href='../../../atributos-cadastra.php';</script>";

            } else {

                if($n_caracteristicas == 0){

                    //PREENCHE A SESSION DE RETORNO COM ERRO
                    $_SESSION['RETORNO'] = array(
                        'ERRO'         => 'ERRO-SEM-CARACTERISTICAS',
                        'nome'         => $nome,
                        'visualizacao' => $visualizacao
                    );

                    //REDIRECIONA PARA A TELA DE USUÁRIOS
                    echo "<script>location.href='../../../atributos-cadastra.php';</script>";

                } else {

                    //GERA UM CÓDIGO IDENTIFICADOR
                    $identificador_atributo = md5(date('Y-m-d H:i:s').$nome.$visualizacao.$n_caracteristicas);

                    //INSERE O ATRIBUTO NO BANCO
                    mysqli_query($conn, "INSERT INTO atributo (identificador, nome, visualizacao) VALUES ('$identificador_atributo','$nome','$visualizacao')");

                    $id_atributo = mysqli_insert_id($conn);

                    //INSERE AS CARACTERISTICAS                        
                    $i = 1;
                    $x = 1;

                    //SE FOR DO TIPO CAIXAS DE SELEÇÃO OU LISTA
                    if($visualizacao == 'S' | $visualizacao == 'L'){
                        while($i <= $n_caracteristicas){                            
                            if(isset($_POST["caracteristica-".$x])){
                                $caracteristica               = mb_strtoupper(trim(strip_tags(filter_input(INPUT_POST, "caracteristica-".$x, FILTER_SANITIZE_STRING))));
                                $identificador_caracteristica = md5(date('Y-m-d H:i:s').$id_atributo.$caracteristica);
                                mysqli_query($conn, "INSERT INTO caracteristica (identificador, id_atributo, nome) VALUES ('$identificador_caracteristica','$id_atributo','$caracteristica')");
                                $i++; $x++;
                            } else {
                                $x++;
                            }
                        }
                    } else if($visualizacao == 'C') {
                        while($i <= $n_caracteristicas){                            
                            if(isset($_POST["caracteristica-".$x])){
                                $caracteristica               = mb_strtoupper(trim(strip_tags(filter_input(INPUT_POST, "caracteristica-".$x, FILTER_SANITIZE_STRING))));
                                $cor                          = trim(strip_tags(filter_input(INPUT_POST, "cor-".$x, FILTER_SANITIZE_STRING)));
                                $identificador_caracteristica = md5(date('Y-m-d H:i:s').$id_atributo.$caracteristica);
                                mysqli_query($conn, "INSERT INTO caracteristica (identificador, id_atributo, nome, cor) VALUES ('$identificador_caracteristica','$id_atributo','$caracteristica', '$cor')");
                                $i++; $x++;
                            } else {
                                $x++;
                            }
                        }
                    } else if($visualizacao == 'T') {
                        while($i <= $n_caracteristicas){                            
                            if(isset($_POST["caracteristica-".$x])){
                                $caracteristica               = mb_strtoupper(trim(strip_tags(filter_input(INPUT_POST, "caracteristica-".$x, FILTER_SANITIZE_STRING))));
                                $textura                      = filter_input(INPUT_POST, "arquivo-".$x, FILTER_SANITIZE_STRING);
                                if($textura != ''){                         
                                    $extensao = mb_strtolower(pathinfo($_FILES['imagem-'.$x]['name'], PATHINFO_EXTENSION));    
                                    if($extensao == 'png' | $extensao == 'jpg' | $extensao == 'jpeg' | $extensao == 'gif'){   
                                        $imagem_textura = md5(time().$caracteristica).'.'.$extensao;
                                        move_uploaded_file($_FILES['imagem-'.$x]['tmp_name'], "../../../../imagens/texturas/".$imagem_textura);
                                        reduz_imagem("../../../../imagens/texturas/".$imagem_textura, $imagem_textura, 400, "../../../../imagens/texturas/");
                                    } else {
                                        $imagem_textura = '';
                                    }
                                } else {
                                    $imagem_textura = '';
                                }
                                $identificador_caracteristica = md5(date('Y-m-d H:i:s').$id_atributo.$caracteristica);
                                mysqli_query($conn, "INSERT INTO caracteristica (identificador, id_atributo, nome, textura) VALUES ('$identificador_caracteristica','$id_atributo','$caracteristica', '$imagem_textura')");
                                $i++; $x++;
                            } else {
                                $x++;
                            }
                        }
                    }

                    include_once '../../../../bd/desconecta.php';

                    //REDIRECIONA PARA A TELA DE USUÁRIOS
                    echo "<script>location.href='../../../atributos.php';</script>";
                        
                }

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
