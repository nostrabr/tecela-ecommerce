<?php

include_once '../../../bd/conecta.php';     

function urlCategoria($nome){
    $caracteres_proibidos_url = array('(',')','.',',');
    $caracteres_por_espaco    = array(' - ');
    $caracteres_por_hifen     = array(' ','/','#39;','#34;');
    return mb_strtolower(str_replace($caracteres_proibidos_url,'', str_replace($caracteres_por_hifen,'-', str_replace($caracteres_por_espaco,' ', preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim(preg_replace('/(\'|")/', "-", $nome))))))));
}

$data = array();

$busca_loja = mysqli_query($conn, "SELECT site FROM loja WHERE id = 1");
$loja       = mysqli_fetch_array($busca_loja);
    
$res = mysqli_query($conn, "SELECT * FROM categoria");

//iterate on results row and create new index array of data
while( $row = mysqli_fetch_assoc($res) ) { 
    $tmp = array();
    $tmp['id']        = $row['id'];
    $tmp['label']     = $row['nome'];
    $tmp['parent_id'] = $row['pai'];
    $tmp['link']      = $loja['site'].'categoria/'.urlCategoria($row['nome']).'/'.$row['id'];
    array_push($data, $tmp); 
}

$tmp = array();
$tmp['id']        = '';
$tmp['label']     = 'Todos produtos';
$tmp['link']      = $loja['site'].'categoria/todas/0';
array_push($data, $tmp); 

$tmp = array();
$tmp['id']        = '';
$tmp['label']     = 'Promoção';
$tmp['link']      = $loja['site'].'promocao';
array_push($data, $tmp); 

$tmp = array();
$tmp['id']        = '';
$tmp['label']     = 'Vistos recentemente';
$tmp['link']      = $loja['site'].'vistos-recentemente';
array_push($data, $tmp); 

$tmp = array();
$tmp['id']        = '';
$tmp['label']     = 'Mais vendidos';
$tmp['link']      = $loja['site'].'mais-vendidos';
array_push($data, $tmp); 

$busca_paginas_customizadas_menu_mobile = mysqli_query($conn, "SELECT * FROM pagina_customizada WHERE status = 1 AND mostrar_menu_mobile = 1");
while($pagina_customizada_menu_mobile = mysqli_fetch_array($busca_paginas_customizadas_menu_mobile)){
    $tmp = array();
    $tmp['id']        = '';
    $tmp['label']     = $pagina_customizada_menu_mobile['titulo'];
    $tmp['link']      = $loja['site'].'pagina/'.$pagina_customizada_menu_mobile['identificador'];
    array_push($data, $tmp); 
}

$itemsByReference = array();

// Build array of item references:
foreach($data as $key => &$item) {
    $itemsByReference[$item['id']] = &$item;
    // Children array:
    $itemsByReference[$item['id']]['children'] = array();
}

// Set items as children of the relevant parent item.
foreach($data as $key => &$item)  {
    if($item['parent_id'] && isset($itemsByReference[$item['parent_id']])) {
        $itemsByReference [$item['parent_id']]['children'][] = &$item;
    }
}

// Remove items that were added to parents elsewhere:
foreach($data as $key => &$item) {
    if(empty($item['children'])) {
        unset($item['children']);
    }
    if($item['parent_id'] && isset($itemsByReference[$item['parent_id']])) {
        unset($data[$key]);
    }
}

//REINDEXA
$data = array_values($data);

// Encode:
echo json_encode($data);

include_once '../../../bd/desconecta.php'; 