<?php 

session_start();

$session_visitante = filter_var($_SESSION['visitante']);

if(mb_strlen($session_visitante) == 32){
    
    //CONECTA AO BANCO
    include_once '../bd/conecta.php';

    $data_aux_inicial = date('Y-m-d H:00', strtotime('-3 months', strtotime(date('Y-m-d H:i:s'))));
    $data_aux_final   = date('Y-m-d H:i:s');

    //BUSCA PEDIDOS PAGOS
    $pedidos = mysqli_query($conn, "
        SELECT p.* 
        FROM pedido AS p
        WHERE 
            (p.status = 3 OR p.status = 4) AND
            p.ga_event_purchase = 0 AND
            p.data_cadastro BETWEEN '$data_aux_inicial' AND '$data_aux_final'
        ORDER BY p.id ASC
    ");

    while($pedido = mysqli_fetch_array($pedidos)){ 
                
        $preco_total    = 0;
        $retorno_itens  = array();
        $contador_itens = 0;

        //BUSCA O CUPOM CASO TENHA
        $tem_cupom  = false;
        $cupom_nome = '';
        if($identificador_cupom != ''){
            $busca_cupom_uso = mysqli_query($conn, "SELECT id_cupom FROM cupom_uso WHERE id_pedido = '".$pedido['id']."'");
            if(mysqli_num_rows($busca_cupom_uso) > 0){
                $cupom_uso   = mysqli_fetch_array($busca_cupom_uso);
                $busca_cupom = mysqli_query($conn, "SELECT nome FROM cupom WHERE id = '".$cupom_uso['id_cupom']."'");
                $cupom       = mysqli_fetch_array($busca_cupom);
                $cupom_nome  = $cupom['nome'];
                $tem_cupom   = true;
            }
        }

        //BUSCA OS PRODUTOS DO CARRINHO
        $carrinho = mysqli_query($conn, "
            SELECT cp.identificador AS carrinho_produto_identificador, cp.id_produto AS produto_id, cp.quantidade AS produto_quantidade, cp.ids_caracteristicas AS produto_caracteristicas, cp.preco AS produto_preco, p.nome AS produto_nome, p.id_categoria AS produto_categoria_id,
            (SELECT m.nome FROM marca AS m WHERE m.id = p.id_marca) AS produto_marca
            FROM carrinho AS c
            INNER JOIN carrinho_produto AS cp ON c.id = cp.id_carrinho
            INNER JOIN produto AS p ON p.id = cp.id_produto
            WHERE cp.status = 1 AND c.id = '".$pedido['id_carrinho']."'
        ");

        $busca_pagamento = mysqli_query($conn, "SELECT valor_frete FROM pagamento_pagseguro WHERE id_pedido = ".$pedido['id']);
        $pagamento       = mysqli_fetch_array($busca_pagamento);

        while($produto = mysqli_fetch_array($carrinho)){ 
        
            $preco_total += $produto['produto_preco']*$produto['produto_quantidade'];

            $contador_itens++;

            $array_categorias = array();
            $categoria1 = '';
            $categoria2 = '';
            $categoria3 = '';
            $categoria4 = '';
            $categoria5 = '';
            
            $busca_nivel_categorias = mysqli_query($conn, "SELECT id, nivel, pai, nome FROM categoria WHERE id = ".$produto['produto_categoria_id']."");
            $nivel_categoria        = mysqli_fetch_array($busca_nivel_categorias);   
            $nivel_maximo           = $nivel_categoria['nivel'];  
            $ultima_categoria       = $nivel_categoria['nome'];

            for($c=0;$c < $nivel_maximo;$c++){

                $array_categorias[] = array(
                    "id"   => $nivel_categoria['id'],
                    "nome" => $nivel_categoria["nome"]
                ); ;

                $busca_categoria_pai = mysqli_query($conn, "SELECT id, nivel, pai, nome FROM categoria WHERE id = '".$nivel_categoria['pai']."'");
                $nivel_categoria     = mysqli_fetch_array($busca_categoria_pai);  

            }

            $array_categorias = array_reverse($array_categorias);
            $n_categorias     = count($array_categorias);
            
            for($c=0;$c < $n_categorias;$c++){
                switch($c){
                    case 0:
                        $categoria1 = mb_convert_case($array_categorias[$c]['nome'], MB_CASE_TITLE, 'UTF-8');
                        break;
                    case 1:
                        $categoria2 = mb_convert_case($array_categorias[$c]['nome'], MB_CASE_TITLE, 'UTF-8');
                        break;
                    case 2:
                        $categoria3 = mb_convert_case($array_categorias[$c]['nome'], MB_CASE_TITLE, 'UTF-8');
                        break;
                    case 3:
                        $categoria4 = mb_convert_case($array_categorias[$c]['nome'], MB_CASE_TITLE, 'UTF-8');
                        break;
                    case 4:
                        $categoria5 = mb_convert_case($array_categorias[$c]['nome'], MB_CASE_TITLE, 'UTF-8');
                        break;
                    default:
                        break;

                }
            }

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

            $valor_desconto = '';
            if($tem_cupom){
                if($cupom['tipo'] == 'V'){            
                    $valor_desconto = number_format(($produto['produto_preco']*$produto['produto_quantidade'])*$cupom['valor']/$preco_total,2,'.','');
                } else if($cupom['tipo'] == 'P'){
                    $valor_desconto = number_format(($produto['produto_preco']*$produto['produto_quantidade'])*$cupom['valor']/100,2,'.','');
                }
                $valor_desconto = explode('.',$valor_desconto);
                if($valor_desconto[1] == "00"){
                    $valor_desconto = $valor_desconto[0];
                } else {
                    $valor_desconto = $valor_desconto[0].".".$valor_desconto[1];
                }
            }

            $retorno_itens[] = array(
                "item_id"           => $produto['produto_id'],
                "item_name"         => mb_convert_case($produto['produto_nome'], MB_CASE_TITLE, 'UTF-8'),
                "coupon"            => $cupom_nome,
                "discount"          => floatval($valor_desconto),
                "index"             => $contador_itens,
                "item_brand"        => mb_convert_case($produto['produto_marca'], MB_CASE_TITLE, 'UTF-8'),
                "item_category"     => $categoria1,
                "item_category2"    => $categoria2,
                "item_category3"    => $categoria3,
                "item_category4"    => $categoria4,
                "item_category5"    => $categoria5,
                "item_list_name"    => "Carrinho",
                "item_variant"      => $caracteristicas_nomes,
                "price"             => floatval($produto['produto_preco']),
                "quantity"          => intval($produto['produto_quantidade'])
            );

        }
        
        $retorno = array(
            "transaction_id" => $pedido['id'],
            "currency"       => "BRL", 
            "value"          => floatval($preco_total), 
            "coupon"         => $cupom_nome,
            "shipping"       => floatval($pagamento['valor_frete']),
            "items"          => $retorno_itens
        );

        mysqli_query($conn, "UPDATE pedido SET ga_event_purchase = 1 WHERE id = ".$pedido['id']);

        echo json_encode($retorno, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    }

    //DESCONECTA DO BANCO
    include_once '../bd/desconecta.php';    
        
}