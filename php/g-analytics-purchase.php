<?php 

session_start();

$session_visitante = trim(strip_tags(filter_input(INPUT_POST, "carrinho", FILTER_SANITIZE_STRING)));

if(mb_strlen($session_visitante) == 32){

    include_once '../bd/conecta.php';
        
    //BUSCA NOME DA LOJA
    $busca_loja = mysqli_query($conn, "SELECT nome FROM loja WHERE id = 1");
    $loja       = mysqli_fetch_array($busca_loja);

    //BUSCA O CARRINHO DO VISITANTE
    $carrinho    = mysqli_query($conn, "
        SELECT c.id AS id_carrinho, cp.identificador AS carrinho_produto_identificador, cp.id_produto AS produto_id, cp.quantidade AS produto_quantidade, cp.ids_caracteristicas AS produto_caracteristicas, cp.preco AS produto_preco, p.nome AS produto_nome, p.sku AS produto_sku,
        (SELECT m.nome FROM marca AS m WHERE m.id = p.id_marca) AS produto_marca,
        (SELECT c.nome FROM categoria AS c WHERE c.id = p.id_categoria) AS produto_categoria
        FROM carrinho AS c
        INNER JOIN carrinho_produto AS cp ON c.id = cp.id_carrinho
        INNER JOIN produto AS p ON p.id = cp.id_produto
        WHERE cp.status = 1 AND c.identificador = '".$session_visitante."'
    ");

    $n_itens = mysqli_num_rows($carrinho);

    if($n_itens > 0){

        $preco_total     = 0;
        $retorno_itens   = array();
        $contador_itens  = 0;
        
        while($produto = mysqli_fetch_array($carrinho)){ 

            $id_carrinho    = $produto['id_carrinho'];
            
            $contador_itens++;
            $preco_total += $produto['produto_preco']*$produto['produto_quantidade'];

            $caracteristicas_nomes = '';
            $caracteristicas       = explode(',',$produto['produto_caracteristicas']);
            $caracteristicas       = array_filter($caracteristicas);
            
            $n_caracteristicas = count($caracteristicas);
            if($n_caracteristicas > 0){                  
                $sql_caracteristicas = '';
                for($i = 0; $i < $n_caracteristicas; $i++){
                    if($i == 0){
                        $sql_caracteristicas .= "pc.id = ".$caracteristicas[$i];
                    } else {
                        $sql_caracteristicas .= " OR pc.id = ".$caracteristicas[$i];
                    }
                }
                $busca_caracteristicas = mysqli_query($conn, "
                    SELECT a.nome AS atributo_nome, c.nome AS caracteristica_nome, pc.id_caracteristica AS caracteristica_id 
                    FROM produto_caracteristica AS pc
                    INNER JOIN atributo AS a ON pc.id_atributo = a.id
                    INNER JOIN caracteristica AS c ON pc.id_caracteristica = c.id
                    WHERE ".$sql_caracteristicas
                );
                while($caracteristica = mysqli_fetch_array($busca_caracteristicas)){
                    $caracteristicas_nomes .= $caracteristica['caracteristica_nome'].'/';
                }
                $caracteristicas_nomes = substr($caracteristicas_nomes,0,-1);
            }

            $retorno_itens[] = array(        
                'sku'           => $produto['produto_sku'],
                'id'            => $produto['produto_id'],
                'name'          => mb_convert_case($produto['produto_nome'], MB_CASE_TITLE, 'UTF-8'),
                'list_name'     => "Pedido",
                'brand'         => mb_convert_case($produto['produto_marca'], MB_CASE_TITLE, 'UTF-8'),
                'category'      => mb_convert_case($produto['produto_categoria'], MB_CASE_TITLE, 'UTF-8'),
                'variant'       => $caracteristicas_nomes,
                'list_position' => $contador_itens,
                'quantity'      => intval($produto['produto_quantidade']),
                'price'         => floatval(number_format($produto['produto_preco'],2,'.',''))
            );
            
            mysqli_query($conn, "UPDATE produto SET relevancia = relevancia + 1 WHERE id = '".$produto['produto_id']."'");     

        }      

        //BUSCA PEDIDO
        $busca_pedido    = mysqli_query($conn, "SELECT id, codigo FROM pedido WHERE id_carrinho = $id_carrinho");
        $pedido          = mysqli_fetch_array($busca_pedido);

        //BUSCA VALORES 
        $busca_pagamento = mysqli_query($conn, "SELECT valor_frete FROM pagamento_pagseguro WHERE id_pedido = ".$pedido['id']);
        $pagamento       = mysqli_fetch_array($busca_pagamento);
        
        //VERIFICA SE FOI USADO CUPOM
        $busca_cupom_uso = mysqli_query($conn, "SELECT id_cupom FROM cupom_uso WHERE id_pedido = ".$pedido['id']);

        if(mysqli_num_rows($busca_cupom_uso) > 0){
            $cupom_uso   = mysqli_fetch_array($busca_cupom_uso);
            $busca_cupom = mysqli_query($conn, "SELECT nome FROM cupom WHERE id = ".$cupom_uso['id_cupom']);
            $cupom_aux   = mysqli_fetch_array($busca_cupom);
            $cupom       = $cupom_aux['nome'];
        } else {
            $cupom = '';
        }

        $retorno[] = array( 
            'affiliation'    => $loja['nome'],
            'coupon'         => $cupom,
            'currency'       => "BRL", 
            'shipping'       => floatval(number_format($pagamento['valor_frete'],2,'.','')), 
            'transaction_id' => $pedido['codigo'],
            'value'          => floatval(number_format($preco_total+$pagamento['valor_frete'],2,'.','')), 
            'items'          => $retorno_itens
        );

        echo json_encode($retorno);

    }

    include_once '../bd/desconecta.php';

}