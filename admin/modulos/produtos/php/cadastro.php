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

        function acertaOrietacaoImagem(string $directory){
            if(file_exists($directory)){
                $destination_extension = strtolower(pathinfo($directory, PATHINFO_EXTENSION));
                if(in_array($destination_extension, ["jpg","jpeg","png","gif"])){
                    if(function_exists('exif_read_data')){
                        $exif = @exif_read_data($directory);
                        if(!empty($exif) && isset($exif['Orientation'])){
                            $orientation = $exif['Orientation'];
                            switch ($orientation){
                                case 2:
                                    $flip = 1;
                                    $deg = 0;
                                    break;
                                case 3:
                                    $flip = 0;
                                    $deg = 180;
                                    break;
                                case 4:
                                    $flip = 2;
                                    $deg = 0;
                                    break;
                                case 5:
                                    $flip = 2;
                                    $deg = -90;
                                    break;
                                case 6:
                                    $flip = 0;
                                    $deg = -90;
                                    break;
                                case 7:
                                    $flip = 1;
                                    $deg = -90;
                                    break;
                                case 8:
                                    $flip = 0;
                                    $deg = 90;
                                    break;
                                default:
                                    $flip = 0;
                                    $deg = 0;
                            }
                            $img = imagecreatefromjpeg($directory);
                            if($deg !== 1 && $img !== null){
                                if($flip !== 0){ imageflip($img,$flip); }
                                $img = imagerotate($img, $deg, 0);
                                imagejpeg($img, $directory);
                            }
                        }
                    }
                }
            }
        }

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
            if($x < $y){ 
                $altura = $largura; 
                $largura = ($altura*$x) / $y;
            } else {
                $altura = ($largura*$y) / $x;
            }
            $nova_imagem = imagecreatetruecolor($largura, $altura);
            $background  = imagecolorallocate($nova_imagem, 0, 0, 0);
            imagecolortransparent($nova_imagem, $background);
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

        function compressImage($source, $destination, $quality) { 
    
            // Get image info 
            $imgInfo = getimagesize($source); 
            $mime = $imgInfo['mime']; 
             
            // Create a new image from file 
            switch($mime){ 
                case 'image/jpeg': 
                    $image = imagecreatefromjpeg($source); 
                    break; 
                case 'image/png': 
                    $image = imagecreatefrompng($source); 
                    break; 
                case 'image/gif': 
                    $image = imagecreatefromgif($source); 
                    break; 
                default: 
                    $image = imagecreatefromjpeg($source); 
            } 
             
            // Save image 
            imagejpeg($image, $destination, $quality); 
             
            // Return compressed image 
            return $destination; 
        
        } 

        function soNumero($str) {
            return preg_replace("/[^0-9]/", "", $str);
        }

        //RECEBE OS DADOS OBRIGATÓRIOS DO FORM
        $nome                          = trim(strip_tags(mb_convert_case(filter_input(INPUT_POST, "nome", FILTER_SANITIZE_STRING), MB_CASE_TITLE, 'UTF-8')));   
        $relevancia                    = trim(strip_tags(filter_input(INPUT_POST, "relevancia", FILTER_SANITIZE_NUMBER_INT)));  
        $marca                         = trim(strip_tags(filter_input(INPUT_POST, "marca", FILTER_SANITIZE_NUMBER_INT)));  
        $categoria                     = trim(strip_tags(filter_input(INPUT_POST, "categoria", FILTER_SANITIZE_NUMBER_INT))); 
        $categoria_google              = trim(strip_tags(filter_input(INPUT_POST, "categoria_google", FILTER_SANITIZE_NUMBER_INT)));  
        $identificador_usuario_session = filter_var($_SESSION['identificador']);
        $nivel_usuario                 = filter_var($_SESSION['nivel']);
        unset($_SESSION['RETORNO']); 

        //CONFIRMA SE OS CAMPOS OBRIGATÓRIOS VIERAM PREENCHIDOS
        if(!empty($nome) & !empty($marca)){

            include_once '../../../../bd/conecta.php'; 

            //BUSCA OS DADOS DA LOJA
            $busca_loja          = mysqli_query($conn, "SELECT loja_roupa FROM loja WHERE id = 1");
            $loja                = mysqli_fetch_array($busca_loja);
            
            //RECEBE OS DADOS NÃO OBRIGATÓRIOS DO FORM
            $preco                       = trim(strip_tags(filter_input(INPUT_POST, "preco", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND)));  
            $peso                        = trim(strip_tags(filter_input(INPUT_POST, "peso", FILTER_SANITIZE_NUMBER_INT)));   
            $altura                      = trim(strip_tags(filter_input(INPUT_POST, "altura", FILTER_SANITIZE_NUMBER_INT)));   
            $largura                     = trim(strip_tags(filter_input(INPUT_POST, "largura", FILTER_SANITIZE_NUMBER_INT)));   
            $comprimento                 = trim(strip_tags(filter_input(INPUT_POST, "comprimento", FILTER_SANITIZE_NUMBER_INT)));   
            $estoque                     = trim(strip_tags(filter_input(INPUT_POST, "estoque", FILTER_SANITIZE_NUMBER_INT)));      
            $atributo_primario           = trim(strip_tags(filter_input(INPUT_POST, "atributo-primario", FILTER_SANITIZE_NUMBER_INT)));      
            $atributo_secundario         = trim(strip_tags(filter_input(INPUT_POST, "atributo-secundario", FILTER_SANITIZE_NUMBER_INT)));       
            $palavras_chave              = trim(strip_tags(filter_input(INPUT_POST, "palavras_chave", FILTER_SANITIZE_STRING)));  
            $descricao                   = base64_encode(nl2br(trim(strip_tags(filter_input(INPUT_POST, "descricao", FILTER_SANITIZE_STRING))))); 
            $variacoes                   = trim(strip_tags(filter_input(INPUT_POST, "ids-variacoes", FILTER_SANITIZE_STRING)));   
            $sku                         = trim(strip_tags(filter_input(INPUT_POST, "sku", FILTER_SANITIZE_STRING)));   
            $mpn                         = trim(strip_tags(filter_input(INPUT_POST, "mpn", FILTER_SANITIZE_STRING)));   
            $gtin                        = trim(strip_tags(filter_input(INPUT_POST, "gtin", FILTER_SANITIZE_STRING)));    
            
            //VERIFICA O ÚLTIMO CARACTER DAS PALAVRAS CHAVE E SE FOR VIRGULA, RETIRA
            $ultima_letra_palavras_chave = substr($palavras_chave, -1);
            if($ultima_letra_palavras_chave == ','){
                $palavras_chave = substr($palavras_chave,0,-1);
            }

            //TRATA O PREÇO TROCANDO A VIRGULA PELO PONTO
            $preco = str_replace(',','.',$preco);

            //GERA UM CÓDIGO IDENTIFICADOR
            $identificador_produto = md5(date('Y-m-d H:i:s').$nome.$marca.$categoria.$sku);

            //BUSCA O ÚLTIMO ID CADASTRADO
            $busca_ultimo_produto = mysqli_query($conn, "SELECT MAX(id) AS id_ultimo_produto FROM produto");
            $ultimo_produto       = mysqli_fetch_array($busca_ultimo_produto);
            
            //SE FOR LOJA DE ROUPA PEGA OS DADOS ESPECÍFICOS
            if($loja['loja_roupa'] == 1){

                $genero = trim(strip_tags(filter_input(INPUT_POST, "genero", FILTER_SANITIZE_STRING)));  
                $idade  = trim(strip_tags(filter_input(INPUT_POST, "idade", FILTER_SANITIZE_STRING)));  

                //CADASTRA O PRODUTO
                mysqli_query($conn, "INSERT INTO produto (identificador, id_marca, id_categoria, nome, categoria_google, sku, gtin, genero, idade, mpn, preco, altura, largura, comprimento, peso, descricao, palavras_chave, estoque, relevancia, atributo_primario, atributo_secundario, cadastrado_por) VALUES ('$identificador_produto','$marca','$categoria','$nome','$categoria_google','$sku','$gtin','$genero','$idade','$mpn','$preco','$altura','$largura','$comprimento','$peso','$descricao','$palavras_chave','$estoque','$relevancia','$atributo_primario','$atributo_secundario','$identificador_usuario_session')");
                 
            } else {
   
                //CADASTRA O PRODUTO
                mysqli_query($conn, "INSERT INTO produto (identificador, id_marca, id_categoria, nome, categoria_google, sku, gtin, mpn, preco, altura, largura, comprimento, peso, descricao, palavras_chave, estoque, relevancia, atributo_primario, atributo_secundario, cadastrado_por) VALUES ('$identificador_produto','$marca','$categoria','$nome','$categoria_google','$sku','$gtin','$mpn','$preco','$altura','$largura','$comprimento','$peso','$descricao','$palavras_chave','$estoque','$relevancia','$atributo_primario','$atributo_secundario','$identificador_usuario_session')");
                            
            }

            //PEGA O ID DO PRODUTO RECÉM CADASTRADO
            $id_produto = mysqli_insert_id($conn);     
            
            //CADASTRA AS TAGS PARA O PRODUTO           
            $tags         = [];
            if(isset($_POST['tags'])){
                $tags     = $_POST['tags']; 
            }    
            $n_tags = count($tags);
            if($n_tags > 0){                    
                for($i = 0; $i < $n_tags; $i++){
                    mysqli_query($conn, "INSERT INTO produto_tag (id_produto, id_tag) VALUES ('$id_produto','$tags[$i]')");
                }
            }

            //SE TEM VARIAÇÕES, CADASTRA
            if($variacoes != ''){
                
                //CADASTRA AS CARACTERISTICAS DO PRODUTO
                if(!empty($atributo_primario)){                
                    $caracteristicas_primarias   = $_POST['caracteristicas-primarias'];   
                    $n_caracteristicas_primarias = count($caracteristicas_primarias);
                    if($n_caracteristicas_primarias > 0){                    
                        for($i = 0; $i < $n_caracteristicas_primarias; $i++){
                            mysqli_query($conn, "INSERT INTO produto_caracteristica (id_produto, id_atributo, id_caracteristica) VALUES ('$id_produto','$atributo_primario','$caracteristicas_primarias[$i]')");
                        }
                    }
                }

                if(!empty($atributo_secundario)){
                    $caracteristicas_secundarias = $_POST['caracteristicas-secundarias']; 
                    $n_caracteristicas_secundarias = count($caracteristicas_secundarias);
                    if($n_caracteristicas_secundarias > 0){                  
                        for($i = 0; $i < $n_caracteristicas_secundarias; $i++){
                            mysqli_query($conn, "INSERT INTO produto_caracteristica (id_produto, id_atributo, id_caracteristica) VALUES ('$id_produto','$atributo_secundario','$caracteristicas_secundarias[$i]')");
                        }
                    }                
                }
                
                $variacoes     = explode(',',$variacoes);
                $estoque_total = 0;
                $n_variacoes   = count($variacoes);

                //PERCORRE TODAS AS VARIAÇÕES CRIADAS
                for($i = 0; $i < $n_variacoes; $i++){

                    //BUSCA O ESTOQUE E O STATUS
                    $estoque_variante = trim(strip_tags(filter_input(INPUT_POST, "variacao-".$variacoes[$i], FILTER_SANITIZE_NUMBER_INT)));
                    $status_variante  = trim(strip_tags(filter_input(INPUT_POST, "variacao-status-input-".$variacoes[$i], FILTER_SANITIZE_NUMBER_INT)));
                    $ordem_variante   = trim(strip_tags(filter_input(INPUT_POST, "variacao-ordem-".$variacoes[$i], FILTER_SANITIZE_NUMBER_INT)));

                    //SEPARA A VARIAÇÃO
                    $variantes  = explode('-',$variacoes[$i]);
                    $variante_1 = $variantes[0];
                    $variante_2 = $variantes[1];

                    //INSERE A VARIAÇÃO NO BANCO
                    mysqli_query($conn, "INSERT INTO produto_variacao (id_produto, id_caracteristica_primaria, id_caracteristica_secundaria, estoque, ordem, status) VALUES ('$id_produto','$variante_1','$variante_2','$estoque_variante','$ordem_variante','$status_variante')");
                        
                    //INCREMENTA O ESTOQUE TOTAL
                    $estoque_total += $estoque_variante;

                }

                //SE O ESTOQUE TOTAL QUE É A SOMA DOS ESTOQUES INDIVIDUAIS DE CADA VARIAÇÃO DO PRODUTO FOR DIFERENTE DO DIGITADO COMO VALOR TOTAL, TROCA
                if($estoque_total != $estoque){
                    mysqli_query($conn, "UPDATE produto SET estoque = '$estoque_total' WHERE id = '$id_produto'");
                }

            }
            
            //GERA UM CONTADOR DE ARQUIVOS
            $contador = 0;

            //INSTANCIA O DIRETÓRIO TEMPORÁRIO
            $diretorio = "../arquivos/temp/".$identificador_usuario_session."/";
            $pasta = dir("../arquivos/temp/".$identificador_usuario_session); 

            //SE VIERAM IMAGENS, CADASTRA
            if(isset($_POST['imagem'])){
        
                //RECEBE AS IMAGENS E INSTANCIA OS ARRAYS
                $imagens      = $_POST['imagem'];
                $n_imagens    = count($imagens);
                $array_ordens = [];
                $array_capas  = [];
                $array_nomes  = [];

                //SEPARA A ORDEM, CAPA E NOME DA IMAGEM EM DIFERENTES ARRAYS
                for($j = 0; $j < $n_imagens; $j++){
                    $ordem_capa_nome = explode(']-[',$imagens[$j]);
                    array_push($array_ordens, soNumero($ordem_capa_nome[0]));
                    array_push($array_capas, soNumero($ordem_capa_nome[1]));
                    array_push($array_nomes, substr($ordem_capa_nome[2],0,-1));
                }

                //LISTA OS ARQUIVOS
                while($arquivo = $pasta -> read()){
                    
                    //SE NÃO FOR O DIRETÓRIO . ou ..
                    if(($arquivo != '.') && ($arquivo != '..')){        
                        
                        //INCREMENTA O CONTADOR
                        $contador++;
                        
                        //PEGA A EXTENSÃO DA IMAGEM
                        $extensao = mb_strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));

                        //SE A EXTENSÃO FOR VÁLIDA
                        if($extensao == 'png' | $extensao == 'jpg' | $extensao == 'jpeg' | $extensao == 'gif'){
                           
                            //PROCURA A IMAGEM NO ARRAY DE NOMES
                            $key = array_search($arquivo, $array_nomes);

                            //SE ENCONTROU
                            if(false !== $key){
                        
                                //CRIA UM NOME E UM IDENTIFICADOR PARA A IMAGEM
                                $identificador_imagem = md5($contador.time().$identificador_usuario_session);    
                                $nome_imagem          = str_replace(' ','_',preg_replace('/( )+/', ' ',  preg_replace('/[^a-zA-Z0-9\s]/', '', str_replace('+',' ',str_replace('-',' ',$nome))))).'_'.$contador.'_'.$identificador_imagem.'.'.$extensao;      

                                //ACERTA A ORIENTAÇÃO DA IMAGEM
                                acertaOrietacaoImagem($diretorio.$arquivo);   

                                //MOVE A IMAGEM RENOMEADA PARA A NOVA PASTA
                                copy($diretorio.$arquivo, "../../../../imagens/produtos/original/".$nome_imagem);
                                
                                //REDUZ A IMAGEM E SEPARA EM PASTAS
                                reduz_imagem("../../../../imagens/produtos/original/".$nome_imagem, $nome_imagem, 100, "../../../../imagens/produtos/pequena/");
                                reduz_imagem("../../../../imagens/produtos/original/".$nome_imagem, $nome_imagem, 400, "../../../../imagens/produtos/media/");
                                reduz_imagem("../../../../imagens/produtos/original/".$nome_imagem, $nome_imagem, 1000, "../../../../imagens/produtos/grande/");
                                /*compressImage("../../../../imagens/produtos/pequena/".$nome_imagem, "../../../../imagens/produtos/pequena/".$nome_imagem, 75);
                                compressImage("../../../../imagens/produtos/media/".$nome_imagem, "../../../../imagens/produtos/media/".$nome_imagem, 75);*/
                                
                                //INSERE NO BANCO
                                mysqli_query($conn, "INSERT INTO produto_imagem (identificador, id_produto, imagem, capa, ordem) VALUES ('$identificador_imagem','$id_produto','$nome_imagem','".$array_capas[$key]."','".$array_ordens[$key]."')");

                            }

                        }
                        
                    }
                    
                }
                
            }

            include_once '../../../../bd/desconecta.php';
                    
            //REDIRECIONA PARA A TELA DE PRODUTOS
            echo "<script>location.href='../../../produtos.php?acao=e';</script>";

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
