<?php

//INICIA A SESSÃO
session_start();

$quantidade            = trim(strip_tags(filter_input(INPUT_POST, "quantidade", FILTER_SANITIZE_NUMBER_INT)));   
$atributos             = trim(strip_tags(filter_input(INPUT_POST, "atributos")));   
$identificador_produto = trim(strip_tags(filter_input(INPUT_POST, "identificador", FILTER_SANITIZE_STRING)));   
$visitante             = filter_var($_SESSION['visitante']);

if(!empty($quantidade) & $quantidade > 0 & mb_strlen($identificador_produto) == 32 & mb_strlen($visitante) == 32){

    include_once '../../../bd/conecta.php';
    
    //BUSCA O PRODUTO
    $busca_produto = mysqli_query($conn, "
        SELECT p.id AS produto_id, p.preco AS produto_preco,
        (SELECT ppp.porcentagem FROM promocao AS ppp WHERE p.id = ppp.id_produto AND p.promocao = 1 AND ppp.status = 1 ORDER BY ppp.data_cadastro DESC LIMIT 1) AS produto_promocao,
        (SELECT ppc.porcentagem FROM promocao AS ppc WHERE p.id_categoria = ppc.id_categoria AND pc.promocao = 1 AND ppc.status = 1 ORDER BY ppc.data_cadastro DESC LIMIT 1) AS categoria_promocao 
        FROM produto AS p 
        LEFT JOIN categoria AS pc ON pc.id = p.id_categoria
        WHERE p.identificador = '".$identificador_produto."'
    ");
    
    //SE O PRODUTO EXISTE, PROSSEGUE
    if(mysqli_num_rows($busca_produto) > 0){

        //PEGA O ID DO PRODUTO
        $produto       = mysqli_fetch_array($busca_produto);
        $id_produto    = $produto['produto_id'];

        //TRATA O PREÇO CASO TENHA PROMOÇÃO NO PRODUTO
        if($produto['produto_promocao'] != '' | $produto['categoria_promocao'] != ''){
            if($produto['produto_promocao'] >= $produto['categoria_promocao']){
                $porcentagem_desconto = $produto['produto_promocao'];
            } else {
                $porcentagem_desconto = $produto['categoria_promocao'];
            }
            $produto_preco       = $produto['produto_preco'];
            $produto_desconto    = $produto['produto_preco'] * $porcentagem_desconto / 100;
            $preco_produto = $produto_preco - $produto_desconto;
        } else {
            $preco_produto = $produto['produto_preco'];
        }
      
        //BUSCA O CARRINHO
        $busca_carrinho = mysqli_query($conn, "SELECT id FROM carrinho WHERE identificador = '".$visitante."'");

        //SE JÁ EXISTIR
        if(mysqli_num_rows($busca_carrinho) > 0){

            //BUSCA O ID DO CARRINHO
            $carrinho = mysqli_fetch_array($busca_carrinho);
            $id_carrinho = $carrinho['id'];

        //SE NÃO EXISTIR
        } else {

            //GERA UM CARRINHO COMO IDENTIFICADOR A SESSÃO DO VISITANTE E PEGA O ID
            mysqli_query($conn, "INSERT INTO carrinho (identificador) VALUES ('$visitante')");
            $id_carrinho = mysqli_insert_id($conn);

        }

        //RETIRA CARACTERES INDESEJADOS DOS ATRIBUTOS
        $array_caracteres = array('[',']','"');
        $atributos        = str_replace($array_caracteres,'',$atributos);

        //GERA UM IDENTIFICADOR
        $identificador    = md5(date('Y-m-d H:i:s').$id_carrinho.$id_produto.$atributos.$quantidade.$preco_produto);

        //INSERE O PRODUTO NMO CARRINHO
        mysqli_query($conn, "INSERT INTO carrinho_produto (identificador, id_carrinho, id_produto, ids_caracteristicas, quantidade, preco) VALUES ('$identificador','$id_carrinho','$id_produto','$atributos','$quantidade','$preco_produto')");
        mysqli_query($conn, "UPDATE produto SET relevancia = relevancia + 1 WHERE id = '$id_produto'");     
    
        $total_produtos_carrinho = 0;
        $valor_total_carrinho    = '0.00';

        //BUSCA OS  VALORES DO CARRINHO
        $busca_carrinho = mysqli_query($conn, "SELECT id FROM carrinho WHERE identificador = '".$_SESSION['visitante']."'");
        if(mysqli_num_rows($busca_carrinho) > 0){
            $carrinho                   = mysqli_fetch_array($busca_carrinho);
            $itens_carrinho = mysqli_query($conn, "SELECT quantidade, preco FROM carrinho_produto WHERE status = 1 AND id_carrinho = ".$carrinho['id']);
            while($item_carrinho = mysqli_fetch_array($itens_carrinho)){
                $total_produtos_carrinho += $item_carrinho['quantidade'];
                $valor_total_carrinho    += $item_carrinho['preco']*$item_carrinho['quantidade'];
            }
        }        
        $valor_total_carrinho = number_format($valor_total_carrinho, 2, ',', '.');

        include_once '../../../bd/desconecta.php';

        //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
        $dados[] = array(
            "status" => "SUCESSO",
            "id"     => $id_carrinho,
            "itens"  => $total_produtos_carrinho,
            "preco"  => $valor_total_carrinho
        );
        echo json_encode($dados);

    } else {

        //VERIFICA SE VEIO DO AJAX
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    
            //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
            $dados[] = array(
                "status" => "ERRO"
            );
            echo json_encode($dados);
            
        } else {
            
            //REDIRECIONA PARA A TELA DE LOGIN
            echo "<script>location.href='/';</script>";
            
        }
    
    }
    
} else {

    //VERIFICA SE VEIO DO AJAX
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                
        //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
        $dados[] = array(
            "status" => "ERRO"
        );
        echo json_encode($dados);
        
    } else {
        
        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='/';</script>";
        
    }

}