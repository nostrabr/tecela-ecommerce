<?php 

session_start();

$session_visitante   = filter_var($_SESSION['visitante']);

if(mb_strlen($session_visitante) == 32){

    //CONECTA AO BANCO
    include_once '../bd/conecta.php';
    
    //PARAMETROS
    $carrinho_produto_id  = filter_input(INPUT_POST,"cart_product_id");

    //BUSCA O PRODUTO
    $busca_produto = mysqli_query($conn, "
        SELECT p.id AS produto_id, cp.quantidade AS produto_quantidade, cp.ids_caracteristicas AS produto_caracteristicas, cp.preco AS produto_preco, p.nome AS produto_nome, p.id_categoria AS produto_categoria_id,
        (SELECT m.nome FROM marca AS m WHERE m.id = p.id_marca) AS produto_marca
        FROM produto AS p
        INNER JOIN carrinho_produto AS cp ON cp.id_produto = p.id
        WHERE cp.identificador = '".$carrinho_produto_id."'
    ");
    $produto = mysqli_fetch_array($busca_produto);

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

    $retorno_item[] = array(
        "item_id" => $produto['produto_id'],
        "item_name" => mb_convert_case($produto['produto_nome'], MB_CASE_TITLE, 'UTF-8'),
        "index" => 1,
        "item_brand" => mb_convert_case($produto['produto_marca'], MB_CASE_TITLE, 'UTF-8'),
        "item_category" => $categoria1,
        "item_category2" => $categoria2,
        "item_category3" => $categoria3,
        "item_category4" => $categoria4,
        "item_category5" => $categoria5,
        "item_variant" => $caracteristicas_nomes,
        "price" => floatval($produto['produto_preco']),
        "quantity" => intval($produto['produto_quantidade'])
    );

    $retorno = array(
        "currency" => "BRL", 
        "value" => floatval($produto['produto_preco']*$produto['produto_quantidade']), 
        "items" => $retorno_item
    );

    echo json_encode($retorno, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    include_once '../bd/desconecta.php';

}