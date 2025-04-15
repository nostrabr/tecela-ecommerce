<?php 

session_start();

$session_visitante   = filter_var($_SESSION['visitante']);

if(mb_strlen($session_visitante) == 32){

    //CONECTA AO BANCO
    include_once '../bd/conecta.php';
    
    //PARAMETROS
    $produto_id                 = filter_input(INPUT_POST,"product_id");
    $produto_valor_sem_desconto = filter_input(INPUT_POST,"product_discount");
    $produto_valor              = filter_input(INPUT_POST,"product_value");
    $produto_quantidade         = filter_input(INPUT_POST,"product_qtty");
    $produto_variante           = filter_input(INPUT_POST,"product_variant");
    $produto_desconto           = 0;

    if($produto_valor_sem_desconto != ''){
        $produto_desconto = $produto_valor_sem_desconto-$produto_valor;
    } else {
        $produto_valor_sem_desconto = $produto_valor;
    }

    //BUSCA O PRODUTO
    $busca_produto = mysqli_query($conn, "
        SELECT p.id AS produto_id, p.nome AS produto_nome, p.id_categoria AS produto_categoria_id,
        (SELECT m.nome FROM marca AS m WHERE m.id = p.id_marca) AS produto_marca
        FROM produto AS p
        WHERE p.id = '".$produto_id."'
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

    $retorno_item[] = array(
        "item_id"           => $produto['produto_id'],
        "item_name"         => mb_convert_case($produto['produto_nome'], MB_CASE_TITLE, 'UTF-8'),
        "discount"          => $produto_desconto,
        "index"             => 1,
        "item_brand"        => mb_convert_case($produto['produto_marca'], MB_CASE_TITLE, 'UTF-8'),
        "item_category"     => $categoria1,
        "item_category2"    => $categoria2,
        "item_category3"    => $categoria3,
        "item_category4"    => $categoria4,
        "item_category5"    => $categoria5,
        "item_variant"      => $produto_variante,
        "price"             => floatval($produto_valor_sem_desconto),
        "quantity"          => intval($produto_quantidade)
    );

    $retorno = array(
        "currency" => "BRL", 
        "value"    => floatval($produto_valor*$produto_quantidade), 
        "items"    => $retorno_item
    );

    echo json_encode($retorno, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    include_once '../bd/desconecta.php';

}