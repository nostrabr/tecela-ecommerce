<?php

header('Access-Control-Allow-Origin: *');

include_once '../bd/conecta.php';

$array_retorno = array();

$carrinho = trim(strip_tags(filter_input(INPUT_POST, "carrinho", FILTER_SANITIZE_STRING)));

$busca_filial = mysqli_query($conn,"
    SELECT f.*
    FROM pedido_filial AS pf
    LEFT JOIN filiais AS f ON pf.filial = f.identificador
    LEFT JOIN carrinho AS c ON pf.carrinho = c.identificador
    LEFT JOIN pedido AS p ON p.id_carrinho = c.id
    LEFT JOIN pagamento_pagseguro AS pp ON pp.id_pedido = p.id
    WHERE c.identificador = '$carrinho' AND pp.tipo_frete = 'Retirar'
    ORDER BY pf.id DESC 
    LIMIT 1
");

if(mysqli_num_rows($busca_filial) > 0){
    $filial = mysqli_fetch_array($busca_filial);
    $array_retorno[] = array(
        'nome' => $filial['nome'],
        'email' => $filial['email'],
        'telefone' => $filial['telefone'],
        'endereco' => $filial['endereco'],
        'cidade' => $filial['cidade'],
        'estado' => $filial['estado']
    );
}

echo json_encode($array_retorno);

include_once '../bd/desconecta.php';