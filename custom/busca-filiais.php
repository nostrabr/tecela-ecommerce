<?php

include_once '../bd/conecta.php';

$array_filiais = array();

$filiais = mysqli_query($conn,"SELECT * FROM filiais");

while ($filial = mysqli_fetch_array($filiais)) {
    
    $array_filiais[] = array(
        'identificador' => $filial['identificador'],
        'nome'          => $filial['nome'],
        'endereco'      => $filial['endereco'],
        'cidade'        => $filial['cidade'],
        'estado'        => $filial['estado']
    );

}

echo json_encode($array_filiais);

include_once '../bd/desconecta.php';