<?php 

//PEGA O ID DA CATEGORIA DA URL
$categoria_id   = trim(strip_tags(filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT)));
$categoria_nome = trim(strip_tags(filter_input(INPUT_GET, "cat", FILTER_SANITIZE_STRING)));

//SE VEIO O ID DA CATEGORIA
if($categoria_id != '' & $categoria_nome != '') {
        
//FUNÇÃO QUE ACERTA O NOME DO PRODUTO OU CATEGORIA PARA URL
function urlProduto($nome){
    if($nome != ''){
        $caracteres_proibidos_url = array('(',')','.',',','+','%','$','@','!','#','*','[',']','{','}','?',';',':','|','<','>','=','ª','º','°','§','¹','²','³','£','¢','¬');
        $caracteres_por_espaco    = array(' - ');
        $caracteres_por_hifen     = array(' ','/','#39;','#34;');
        return mb_strtolower(str_replace('--','-',str_replace($caracteres_proibidos_url,'', str_replace($caracteres_por_hifen,'-', str_replace($caracteres_por_espaco,' ', preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim(preg_replace('/(\'|")/', "-", $nome)))))))));
    } else {
        return "categoria";
    }
}

//FUNÇÃO QUE ACERTA O ATRIBUTO ALT PARA A IMAGEM DO PRODUTO
function altProduto($nome){    
    return preg_replace("/&([a-z])[a-z]+;/i", "$1", preg_replace('/(\'|")/', "", preg_replace('/( )+/', ' ', str_replace('-',' ',$nome))));
}


/****************************************/
/* VERIFICA SE VIERAM OS FILTROS NA URL */
/****************************************/

//ORGANIZA A ORDEM
$filtro_ordenacao       = 'relevancia';
if(isset($_GET['ordenacao'])){ $filtro_ordenacao = trim(strip_tags(filter_input(INPUT_GET, "ordenacao", FILTER_SANITIZE_STRING))); }
if($filtro_ordenacao == 'menor-preco'){ $sql_ordenacao = 'p.preco ASC';
} else if($filtro_ordenacao == 'maior-preco'){ $sql_ordenacao = 'p.preco DESC';
} else if($filtro_ordenacao == 'nome'){ $sql_ordenacao = 'p.nome ASC';
} else { $sql_ordenacao = 'p.relevancia DESC'; }

//ORGANIZA O PREÇO
$filtro_preco_minimo    = 0;
$filtro_preco_maximo    = 9999999;
$sql_preco              = '';
if(!$modo_whatsapp){
    if(isset($_GET['menor-preco'])){ $filtro_preco_minimo = trim(strip_tags(filter_input(INPUT_GET, "menor-preco", FILTER_SANITIZE_NUMBER_INT))); }
    if(isset($_GET['maior-preco'])){ $filtro_preco_maximo = trim(strip_tags(filter_input(INPUT_GET, "maior-preco", FILTER_SANITIZE_NUMBER_INT))); }
    $sql_preco = 'AND p.preco BETWEEN '.$filtro_preco_minimo.' AND '.$filtro_preco_maximo;
} else {
    if($loja['modo_whatsapp_preco'] == 1){
        if(isset($_GET['menor-preco'])){ $filtro_preco_minimo = trim(strip_tags(filter_input(INPUT_GET, "menor-preco", FILTER_SANITIZE_NUMBER_INT))); }
        if(isset($_GET['maior-preco'])){ $filtro_preco_maximo = trim(strip_tags(filter_input(INPUT_GET, "maior-preco", FILTER_SANITIZE_NUMBER_INT))); }
        $sql_preco = 'AND p.preco BETWEEN '.$filtro_preco_minimo.' AND '.$filtro_preco_maximo;
    }
}

//ORGANIZA AS MARCAS
$filtro_marcas          = 'T';
$array_filtro_marcas    = array('T');
$sql_marcas             = '';
if(isset($_GET['marcas'])){ $filtro_marcas = trim(strip_tags(filter_input(INPUT_GET, "marcas", FILTER_SANITIZE_STRING))); }
if($filtro_marcas != 'T'){ 
    $array_filtro_marcas = explode('-',$filtro_marcas); 
    $sql_marcas         .= 'AND (';
    foreach($array_filtro_marcas as $marca => $m){
        $sql_marcas .= 'p.id_marca = '.$m.' OR ';
    }
    $sql_marcas         = substr($sql_marcas,0,-4);
    $sql_marcas         .= ')';
}

//ORGANIZA AS CARACTERISTICAS
$filtro_caracteristicas       = 'T';
$array_filtro_caracteristicas = array('T');
$sql_caracteristicas          = '';
if(isset($_GET['caracteristicas'])){ $filtro_caracteristicas = trim(strip_tags(filter_input(INPUT_GET, "caracteristicas", FILTER_SANITIZE_STRING))); }
if($filtro_caracteristicas != 'T'){ 
    $array_filtro_caracteristicas = explode('-',$filtro_caracteristicas); 
    $sql_caracteristicas .= 'INNER JOIN produto_caracteristica AS pcc2 ON pcc2.id_produto = p.id AND (';
    foreach($array_filtro_caracteristicas as $caracteristica => $c){
        $sql_caracteristicas .= 'pcc2.id_caracteristica = '.$c.' OR ';
    }
    $sql_caracteristicas      = substr($sql_caracteristicas,0,-4);
    $sql_caracteristicas      .= ') AND pcc2.status = 1';
}

//ORGANIZA OS FILTROS PARA LOJAS DE ROUPA (GENERO E IDADE) - ESTÃO JUNTOS POR QUE O HTACCESS NÃO ACEITA MAIS DE 9 PARAMETROS
if($loja['loja_roupa'] == 1){
    $filtro_genero_idade     = 'T';
    $array_filtro_genero     = array();
    $array_filtro_idade      = array();
    $array_filtro_genero_aux = array('male','female','unisex');
    $array_filtro_idade_aux  = array('newborn','infant','toddler','kids','adult');
    $sql_genero              = '';
    $sql_idade               = '';
    if(isset($_GET['genero-idade'])){ $filtro_genero_idade = trim(strip_tags(filter_input(INPUT_GET, "genero-idade", FILTER_SANITIZE_STRING))); }
    if($filtro_genero_idade != 'T'){         
        $array_filtro_genero_idade = explode('-',$filtro_genero_idade);
        foreach($array_filtro_genero_idade AS $afgi => $gi){
            if(in_array($gi,$array_filtro_genero_aux)){ array_push($array_filtro_genero, $gi); }
            if(in_array($gi,$array_filtro_idade_aux)){ array_push($array_filtro_idade, $gi); }
        }
        if(count($array_filtro_genero) == 0){ 
            array_push($array_filtro_genero, 'T'); 
        } else {
            $sql_genero .= 'AND (';
            foreach($array_filtro_genero as $genero => $g){ $sql_genero .= 'p.genero = "'.$g.'" OR '; }
            $sql_genero = substr($sql_genero,0,-4);
            $sql_genero .= ')';
        }
        if(count($array_filtro_idade) == 0){ 
            array_push($array_filtro_idade, 'T'); 
        } else {
            $sql_idade .= 'AND (';
            foreach($array_filtro_idade as $idade => $i){ $sql_idade .= 'p.idade = "'.$i.'" OR '; }
            $sql_idade = substr($sql_idade,0,-4);
            $sql_idade .= ')';
        }
    } else {
        if(count($array_filtro_genero) == 0){ array_push($array_filtro_genero, 'T'); }
        if(count($array_filtro_idade) == 0){ array_push($array_filtro_idade, 'T'); }
    }
} else {
    $filtro_genero_idade     = 'T';
    $array_filtro_genero     = array('T');
    $array_filtro_idade      = array('T');
    $sql_genero = '';
    $sql_idade  = '';
}

//ORGANIZA AS TAGS
$filtro_tags       = 'T';
$array_filtro_tags = array('T');
$sql_tags          = '';
if(isset($_GET['tags'])){ $filtro_tags = end(explode('/',trim(strip_tags(filter_input(INPUT_GET, "tags", FILTER_SANITIZE_STRING))))); }
if($filtro_tags != 'T'){ 
    $array_filtro_tags = explode('-',$filtro_tags); 
    $sql_tags .= 'INNER JOIN produto_tag AS pt ON pt.id_produto = p.id AND (';
    foreach($array_filtro_tags as $tag => $t){
        $sql_tags .= 'pt.id_tag = '.$t.' OR ';
    }
    $sql_tags      = substr($sql_tags,0,-4);
    $sql_tags      .= ')';
}




/**************************************************************************/
/* BUSCA A HIERARQUIA DAS SUBCATEGORIAS DA CATEGORIA QUE VEIO SELECIONADA */
/**************************************************************************/

if($categoria_id != 0){
    
    $sql_produtos_sem_categoria = '';

    //BUSCA A HIERARQUIA DE CATEGORIAS
    $busca_hierarquia = mysqli_query($conn, "
        SELECT t1.id AS lev1, t1.nome AS nome1, t2.id as lev2, t2.nome AS nome2, t3.id as lev3, t3.nome AS nome3, t4.id as lev4, t4.nome AS nome4, t5.id as lev5, t5.nome AS nome5, t6.id as lev6, t6.nome AS nome6, t7.id as lev7, t7.nome AS nome7, t8.id as lev8, t8.nome AS nome8, t9.id as lev9, t9.nome AS nome9, t10.id as lev10, t10.nome AS nome10
        FROM categoria AS t1
        LEFT JOIN categoria AS t2 ON t2.pai = t1.id
        LEFT JOIN categoria AS t3 ON t3.pai = t2.id
        LEFT JOIN categoria AS t4 ON t4.pai = t3.id
        LEFT JOIN categoria AS t5 ON t5.pai = t4.id
        LEFT JOIN categoria AS t6 ON t6.pai = t5.id
        LEFT JOIN categoria AS t7 ON t7.pai = t6.id
        LEFT JOIN categoria AS t8 ON t8.pai = t7.id
        LEFT JOIN categoria AS t9 ON t9.pai = t8.id
        LEFT JOIN categoria AS t10 ON t10.pai = t9.id
        WHERE t1.id = $categoria_id
    ");

} else {

    $sql_produtos_sem_categoria = 'OR (p.id_categoria = 0 AND p.status = 1)';

    $busca_categorias_lvl_1 = mysqli_query($conn, "SELECT id FROM categoria WHERE nivel = 1");
    while($categoria_lvl_1 = mysqli_fetch_array($busca_categorias_lvl_1)){
        $sql_categorias_lvl_1 .= 't1.id = '.$categoria_lvl_1['id'].' OR ';
    }
    $sql_categorias_lvl_1 = substr($sql_categorias_lvl_1,0,-4);

    //BUSCA A HIERARQUIA DE CATEGORIAS
    $busca_hierarquia = mysqli_query($conn, "
        SELECT t1.id AS lev1, t1.nome AS nome1, t2.id as lev2, t2.nome AS nome2, t3.id as lev3, t3.nome AS nome3, t4.id as lev4, t4.nome AS nome4, t5.id as lev5, t5.nome AS nome5, t6.id as lev6, t6.nome AS nome6, t7.id as lev7, t7.nome AS nome7, t8.id as lev8, t8.nome AS nome8, t9.id as lev9, t9.nome AS nome9, t10.id as lev10, t10.nome AS nome10
        FROM categoria AS t1
        LEFT JOIN categoria AS t2 ON t2.pai = t1.id
        LEFT JOIN categoria AS t3 ON t3.pai = t2.id
        LEFT JOIN categoria AS t4 ON t4.pai = t3.id
        LEFT JOIN categoria AS t5 ON t5.pai = t4.id
        LEFT JOIN categoria AS t6 ON t6.pai = t5.id
        LEFT JOIN categoria AS t7 ON t7.pai = t6.id
        LEFT JOIN categoria AS t8 ON t8.pai = t7.id
        LEFT JOIN categoria AS t9 ON t9.pai = t8.id
        LEFT JOIN categoria AS t10 ON t10.pai = t9.id
        WHERE $sql_categorias_lvl_1
    ");
    
}

$titulo_pagina                     = '';
$array_categorias                  = array();
$array_categorias_filhas           = array();
$array_categorias_filhas_distintos = array();

while($categorias = mysqli_fetch_array($busca_hierarquia)){    
    if($categoria_id == 0){
        if($categorias['lev1']){ array_push($array_categorias, 'pc.id = '.$categorias['lev1']); $array_categorias_filhas[] = array('id' => $categorias['lev1'], 'nome' => $categorias['nome1'], 'nivel' => 1); }
    } else {
        if($categorias['lev1']){ array_push($array_categorias, 'pc.id = '.$categorias['lev1']); $titulo_pagina = $categorias['nome1']; }
    }
    if($categorias['lev2']){ array_push($array_categorias, 'pc.id = '.$categorias['lev2']); $array_categorias_filhas[] = array('id' => $categorias['lev2'], 'nome' => $categorias['nome2'], 'nivel' => 2); }
    if($categorias['lev3']){ array_push($array_categorias, 'pc.id = '.$categorias['lev3']); $array_categorias_filhas[] = array('id' => $categorias['lev3'], 'nome' => $categorias['nome3'], 'nivel' => 3); }
    if($categorias['lev4']){ array_push($array_categorias, 'pc.id = '.$categorias['lev4']); $array_categorias_filhas[] = array('id' => $categorias['lev4'], 'nome' => $categorias['nome4'], 'nivel' => 4); }
    if($categorias['lev5']){ array_push($array_categorias, 'pc.id = '.$categorias['lev5']); $array_categorias_filhas[] = array('id' => $categorias['lev5'], 'nome' => $categorias['nome5'], 'nivel' => 5); }
    if($categorias['lev6']){ array_push($array_categorias, 'pc.id = '.$categorias['lev6']); $array_categorias_filhas[] = array('id' => $categorias['lev6'], 'nome' => $categorias['nome6'], 'nivel' => 6); }
    if($categorias['lev7']){ array_push($array_categorias, 'pc.id = '.$categorias['lev7']); $array_categorias_filhas[] = array('id' => $categorias['lev7'], 'nome' => $categorias['nome7'], 'nivel' => 7); }
    if($categorias['lev8']){ array_push($array_categorias, 'pc.id = '.$categorias['lev8']); $array_categorias_filhas[] = array('id' => $categorias['lev8'], 'nome' => $categorias['nome8'], 'nivel' => 8); }
    if($categorias['lev9']){ array_push($array_categorias, 'pc.id = '.$categorias['lev9']); $array_categorias_filhas[] = array('id' => $categorias['lev9'], 'nome' => $categorias['nome9'], 'nivel' => 9); }
    if($categorias['lev10']){ array_push($array_categorias, 'pc.id = '.$categorias['lev10']); $array_categorias_filhas[] = array('id' => $categorias['lev10'], 'nome' => $categorias['nome10'], 'nivel' => 10); }
}

$array_categorias = array_unique($array_categorias);
$sql_categorias   = ' AND ('.implode($array_categorias,' OR ').') ';

foreach($array_categorias_filhas as $k => $v){
    if(!in_array($v['id'],$array_categorias_filhas_distintos)){
        array_push($array_categorias_filhas_distintos,$v['id']);
    } else {
        unset($array_categorias_filhas[$k]);
    }
}


/****************************************************************************************/
/* BUSCA TODOS OS PRODUTOS DE TODAS AS PÁGINAS, E ORGANIZA AS QUANTIDADES POR ATRIBUTOS */
/****************************************************************************************/

//SQL PARA MOSTRAR QUAIS SÂO OS PRODUTOS DE MENOR E MAIOR PRECO
$busca_produtos_valores = mysqli_query($conn,"
    SELECT MIN(p.preco) AS minimo, MAX(p.preco) AS maximo
    FROM produto AS p
    LEFT JOIN categoria AS pc ON pc.id = p.id_categoria
    LEFT JOIN marca AS pm ON pm.id = p.id_marca
    WHERE p.status = 1 $sql_categorias $sql_produtos_sem_categoria
    ORDER BY $sql_ordenacao, p.estoque DESC
");
$valores_produtos   = mysqli_fetch_array($busca_produtos_valores);
$preco_minimo_geral = floor($valores_produtos['minimo']);
$preco_maximo_geral = ceil($valores_produtos['maximo']);

$busca_produtos = mysqli_query($conn,"
    SELECT DISTINCT(p.id) AS produto_id, p.nome AS produto_nome, p.genero AS produto_genero, p.idade AS produto_idade, p.preco AS produto_preco, p.descricao AS produto_descricao, p.estoque AS produto_estoque, p.relevancia AS produto_relevancia, pc.nome AS produto_categoria, pc.id AS produto_categoria_id, pm.id AS produto_marca_id,
    (SELECT ppp.porcentagem FROM promocao AS ppp WHERE p.id = ppp.id_produto AND p.promocao = 1 AND ppp.status = 1 ORDER BY ppp.data_cadastro DESC LIMIT 1) AS produto_promocao,
    (SELECT ppc.porcentagem FROM promocao AS ppc WHERE p.id_categoria = ppc.id_categoria AND pc.promocao = 1 AND ppc.status = 1 ORDER BY ppc.data_cadastro DESC LIMIT 1) AS categoria_promocao,
    (SELECT pi.imagem FROM produto_imagem AS pi WHERE pi.id_produto = p.id AND pi.capa = 1 ) AS produto_capa,
    (SELECT GROUP_CONCAT(DISTINCT(pca.id_atributo)) FROM produto_caracteristica AS pca WHERE pca.id_produto = p.id AND pca.status = 1 ORDER BY pca.id_atributo) AS produto_atributos,
    (SELECT GROUP_CONCAT(pcc.id_caracteristica) FROM produto_caracteristica AS pcc WHERE pcc.id_produto = p.id AND pcc.status = 1 ORDER BY pcc.id_caracteristica) AS produto_caracteristicas,
    (SELECT GROUP_CONCAT(pt2.id_tag) FROM produto_tag AS pt2 WHERE pt2.id_produto = p.id) AS produto_tags
    FROM produto AS p
    LEFT JOIN categoria AS pc ON pc.id = p.id_categoria
    LEFT JOIN marca AS pm ON pm.id = p.id_marca
    $sql_caracteristicas
    $sql_tags
    WHERE p.status = 1 $sql_categorias $sql_preco $sql_marcas $sql_genero $sql_idade $sql_produtos_sem_categoria
    ORDER BY field(p.estoque,0), $sql_ordenacao
");

//INSTANCIA OS ARRAYS DE TOTAIS
$total_produtos        = mysqli_num_rows($busca_produtos);
$array_atributos       = array();
$array_categorias      = array();
$array_valores         = array();
$array_marcas          = array();
$array_tags            = array();
$array_caracteristicas = array();
$array_generos         = array();
$array_idades          = array();

//PREENCHE OS ARRAYS
while($produto = mysqli_fetch_array($busca_produtos)){ 
    if(!$modo_whatsapp){
        if($produto['produto_preco'] != 0){
            if($produto['produto_promocao'] != '' | $produto['categoria_promocao'] != ''){
                if($produto['produto_promocao'] >= $produto['categoria_promocao']){ $porcentagem_desconto = $produto['produto_promocao'];
                } else { $porcentagem_desconto = $produto['categoria_promocao']; }
                $produto_preco             = $produto['produto_preco'];
                $produto_desconto          = $produto['produto_preco'] * $porcentagem_desconto / 100;
                $produto_preco_venda       = $produto_preco - $produto_desconto;
                $produto_preco_venda_busca = $produto_preco_venda;
            } else { $produto_preco_venda_busca = $produto['produto_preco']; }
            if($produto['produto_estoque'] <= 0){ $produto_preco_venda_busca = ''; }    
        } else { $produto_preco_venda_busca = ''; }
    } else {     
        if($loja['modo_whatsapp_preco'] == 1){
            if($produto['produto_preco'] != 0){
                if($produto['produto_promocao'] != '' | $produto['categoria_promocao'] != ''){
                    if($produto['produto_promocao'] >= $produto['categoria_promocao']){ $porcentagem_desconto = $produto['produto_promocao'];
                    } else {  $porcentagem_desconto = $produto['categoria_promocao']; }
                    $produto_preco             = $produto['produto_preco'];
                    $produto_desconto          = $produto['produto_preco'] * $porcentagem_desconto / 100;
                    $produto_preco_venda       = $produto_preco - $produto_desconto;
                    $produto_preco_venda_busca = $produto_preco_venda;
                } else { $produto_preco_venda_busca = $produto['produto_preco']; }                        
                if($produto['produto_estoque'] <= 0){  $produto_preco_venda_busca = ''; }
            } else { $produto_preco_venda_busca = ''; }
        } else { $produto_preco_venda_busca = ''; }   
    }
    if($produto_preco_venda_busca != ''){ array_push($array_valores, $produto_preco_venda_busca); }
    array_push($array_categorias, $produto['produto_categoria_id']);
    array_push($array_marcas, $produto['produto_marca_id']);
    if($produto['produto_atributos'] != ''){
        $produto_atributos = explode(',',$produto['produto_atributos']);
        for($a = 0; $a < count($produto_atributos); $a++){ array_push($array_atributos, $produto_atributos[$a]); }
    }
    if($produto['produto_caracteristicas'] != ''){
        $produto_caracteristicas = explode(',',$produto['produto_caracteristicas']);
        for($p = 0; $p < count($produto_caracteristicas); $p++){ array_push($array_caracteristicas, $produto_caracteristicas[$p]); }
    }
    if($produto['produto_tags'] != ''){
        $produto_tags = explode(',',$produto['produto_tags']);
        for($p = 0; $p < count($produto_tags); $p++){ array_push($array_tags, $produto_tags[$p]); }
    }
    array_push($array_generos, $produto['produto_genero']);
    array_push($array_idades, $produto['produto_idade']);   
}

//PARA O FILTRO DE PREÇOS
$preco_minimo = floor(min($array_valores));
$preco_maximo = ceil(max($array_valores));
/*if($filtro_preco_minimo == 0){ $filtro_preco_minimo = $preco_minimo; }*/
if($filtro_preco_maximo == 9999999){ $filtro_preco_maximo = $preco_maximo; }




/**********************************************/
/* BUSCA OS PRODUTOS PRA EXIBIR COM PÁGINAÇÃO */
/**********************************************/

$produtos_por_pagina = 36;
$total_paginas       = ceil($total_produtos/$produtos_por_pagina);
if(isset($_GET['pagina'])){ $pagina = trim(strip_tags(filter_input(INPUT_GET, "pagina", FILTER_SANITIZE_NUMBER_INT)));    
} else { $pagina = 1; }
$primeiro_produto    = ($produtos_por_pagina*$pagina)-$produtos_por_pagina;

$busca_produtos = mysqli_query($conn,"
    SELECT DISTINCT(p.id) AS produto_id, p.nome AS produto_nome, p.genero AS produto_genero, p.idade AS produto_idade, p.preco AS produto_preco, p.descricao AS produto_descricao, p.estoque AS produto_estoque, p.relevancia AS produto_relevancia, pc.nome AS produto_categoria, pc.id AS produto_categoria_id, pm.id AS produto_marca_id,
    (SELECT ppp.porcentagem FROM promocao AS ppp WHERE p.id = ppp.id_produto AND p.promocao = 1 AND ppp.status = 1 ORDER BY ppp.data_cadastro DESC LIMIT 1) AS produto_promocao,
    (SELECT ppc.porcentagem FROM promocao AS ppc WHERE p.id_categoria = ppc.id_categoria AND pc.promocao = 1 AND ppc.status = 1 ORDER BY ppc.data_cadastro DESC LIMIT 1) AS categoria_promocao,
    (SELECT pi.imagem FROM produto_imagem AS pi WHERE pi.id_produto = p.id AND pi.capa = 1 ) AS produto_capa,
    (SELECT GROUP_CONCAT(DISTINCT(pca.id_atributo)) FROM produto_caracteristica AS pca WHERE pca.id_produto = p.id AND pca.status = 1 ORDER BY pca.id_atributo) AS produto_atributos,
    (SELECT GROUP_CONCAT(pcc.id_caracteristica) FROM produto_caracteristica AS pcc WHERE pcc.id_produto = p.id AND pcc.status = 1 ORDER BY pcc.id_caracteristica) AS produto_caracteristicas,
    (SELECT GROUP_CONCAT(pt2.id_tag) FROM produto_tag AS pt2 WHERE pt2.id_produto = p.id) AS produto_tags
    FROM produto AS p
    LEFT JOIN categoria AS pc ON pc.id = p.id_categoria
    LEFT JOIN marca AS pm ON pm.id = p.id_marca
    $sql_caracteristicas
    $sql_tags
    WHERE p.status = 1 $sql_categorias $sql_preco $sql_marcas $sql_genero $sql_idade $sql_produtos_sem_categoria
    ORDER BY field(p.estoque,0), $sql_ordenacao
    LIMIT $primeiro_produto, $produtos_por_pagina
");



/***********************************************************/
/* VERIFICA SE TEM PÁGINA CUSTOMIZADA ATRELADA À CATEGORIA */
/***********************************************************/

$tem_pagina_customizada = false;
$busca_pagina_customizada = mysqli_query($conn, "SELECT * FROM pagina_customizada WHERE categoria = '$categoria_id' AND status = 1 ORDER BY id DESC LIMIT 1");
if(mysqli_num_rows($busca_pagina_customizada)){
    $pagina_customizada = mysqli_fetch_array($busca_pagina_customizada);
    $tem_pagina_customizada = true;
}

?>

<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/produtos/css/nouislider.min.css">
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/produtos/css/style-1.1.css">

<?php if($tem_pagina_customizada){ ?>
    
    <!--CSS-->
    <link rel="stylesheet" href="<?= $loja['site'] ?>modulos/paginas-customizadas/css/style.css">

    <!--PÁGINAS CUSTOMIZADAS-->
    <section id="paginas-customizadas">
        <?= $pagina_customizada['conteudo'] ?>
    </section>

<?php } ?>

<!--PRODUTOS-->
<section id="produtos">
              
<?php if(mysqli_num_rows($busca_produtos) > 0){ ?>

    <div class="row">

        <h2 class="d-none">Filtros</h2>         

        <div id="produtos-filtros" class="d-none d-lg-block col-3">
                    
            <h1 id="titulo-pagina"><?= $titulo_pagina ?></h1>
            <p id="total-produtos-encontrados-geral"><?= $total_produtos ?> resultados</p>

            <!--ORDENAÇÃO-->
            <ul>
                <label for="select-ordenar-por" class="produtos-filtros-titulo">ORDENAR POR</label>
                <li class="produtos-caracteristicas">
                    <select id="select-ordenar-por" title="Selecione a forma de ordenação dos produtos listados" onchange="javascript: window.location.href = '<?= $loja['site'].'categoria/'.$categoria_nome.'/'.$categoria_id.'/1/'.$filtro_preco_minimo.'/'.$filtro_preco_maximo.'/'.$filtro_marcas.'/'.$filtro_caracteristicas.'/'.$filtro_genero_idade.'/' ?>'+this.value+'/<?= $filtro_tags ?>';">   
                        <option value="nome" <?php if($filtro_ordenacao == 'nome'){ echo 'selected'; } ?>>Nome</option>  
                        <option value="relevancia" <?php if($filtro_ordenacao == 'relevancia'){ echo 'selected'; } ?>>Mais relevantes</option> 
                        <?php if(!$modo_whatsapp){ ?>
                            <option value="menor-preco" <?php if($filtro_ordenacao == 'menor-preco'){ echo 'selected'; } ?>>Menor preço</option>   
                            <option value="maior-preco" <?php if($filtro_ordenacao == 'maior-preco'){ echo 'selected'; } ?>>Maior preço</option> 
                        <?php } else { ?>
                            <?php if($loja['modo_whatsapp_preco'] == 1){ ?>
                                <option value="menor-preco" <?php if($filtro_ordenacao == 'menor-preco'){ echo 'selected'; } ?>>Menor preço</option>   
                                <option value="maior-preco" <?php if($filtro_ordenacao == 'maior-preco'){ echo 'selected'; } ?>>Maior preço</option> 
                            <?php } ?>
                        <?php } ?>
                    </select>
                </li>
            </ul>

            <ul>

                <div class="container-filtro-preco">
                    <li class="produtos-filtros-titulo">Preço</li>
                    <li class="produtos-caracteristicas">
                        <ul class="produtos-caracteristicas-caixas-selecao">
                            <li><input type="text" id="amount" readonly></li>
                            <li><div id="slider-range" minimo-geral="<?= $preco_minimo_geral  ?>" minimo-selecionado="<?= $filtro_preco_minimo ?>" maximo-geral="<?= $preco_maximo_geral ?>" maximo-selecionado="<?= $filtro_preco_maximo ?>" url="<?= $loja['site'].'categoria/'.$categoria_nome.'/'.$categoria_id.'/1' ?>" marcas="<?= $filtro_marcas ?>" caracteristicas="<?= $filtro_caracteristicas ?>" genero-idade="<?= $filtro_genero_idade ?>" tags="<?= $filtro_tags ?>"></div></li>
                        </ul>
                    </li> 
                </div>

                <?php 

                //SUBCATEGORIAS
                if(count($array_categorias_filhas) > 0){ 
                    $total_resultados_categorias = 0;
                    foreach($array_categorias_filhas as $k => $v){ 
                        $counts = array_count_values($array_categorias);
                        $total_counts = $counts[$v['id']];
                        if($total_counts > $total_resultados_categorias){
                            $total_resultados_categorias = $total_counts;
                        }
                    }
                    if($total_resultados_categorias > 0){
                    ?>
                    <li class="produtos-filtros-titulo"><?php if($categoria_id == 0){ echo 'Categorias'; } else { echo 'Subcategorias'; } ?></li>
                    <li class="produtos-caracteristicas">
                        <ul class="produtos-caracteristicas-caixas-selecao">
                            <?php $contador_categorias = 0; foreach($array_categorias_filhas as $k => $v){ 
                                $contador_categorias++;
                                $counts = array_count_values($array_categorias);
                                $total_counts = $counts[$v['id']];
                                if($categoria_id == 0){
                                    if($v['nivel'] == 2){ $categoria_nivel_dots = '- '; } else 
                                    if($v['nivel'] == 3){ $categoria_nivel_dots = '-- '; } else 
                                    if($v['nivel'] == 4){ $categoria_nivel_dots = '--- '; } else 
                                    if($v['nivel'] == 5){ $categoria_nivel_dots = '---- '; } else 
                                    if($v['nivel'] == 6){ $categoria_nivel_dots = '----- '; } else 
                                    if($v['nivel'] == 7){ $categoria_nivel_dots = '------ '; } else 
                                    if($v['nivel'] == 8){ $categoria_nivel_dots = '------- '; } else 
                                    if($v['nivel'] == 9){ $categoria_nivel_dots = '-------- '; } else 
                                    if($v['nivel'] == 10){ $categoria_nivel_dots = '--------- '; } else { $categoria_nivel_dots = ''; }
                                } else {
                                    $categoria_nivel_dots = '';
                                }
                                if($total_counts > 0 | $v['nivel'] == 1){
                                ?>
                                <li class="produto-filtro produtos-caracteristica-caixa-selecao"><a <?php if($v['nivel'] == 1){ echo 'style="font-weight: 600;"'; } ?> href="<?= $loja['site'].'categoria/'.urlProduto($v['nome']).'/'.$v['id'].'/1/'.$filtro_preco_minimo.'/'.$filtro_preco_maximo.'/'.$filtro_marcas.'/'.$filtro_caracteristicas.'/'.$filtro_genero_idade.'/'.$filtro_ordenacao.'/'.$filtro_tags ?>"><?= $categoria_nivel_dots.$v['nome'] ?><span class="total-resultados-por-filtro"><?php if($total_counts > 0){ echo '('.$total_counts.')'; } ?></span></a></li>
                            <?php } } ?>
                        </ul>
                    </li> 
                <?php } }

                //BUSCA AS TAGS E SE TIVER ALGUMA LISTA
                $tags = mysqli_query($conn, "SELECT * FROM tag ORDER BY nome ASC");

                if(count($array_tags) > 0){ ?>
                    <li class="produtos-filtros-titulo">Tags</li>
                    <li class="produtos-caracteristicas">
                        <ul class="produtos-caracteristicas-caixas-selecao">
                            <?php while($tag = mysqli_fetch_array($tags)){ 
                                $counts = array_count_values($array_tags);
                                $total_counts = $counts[$tag['id']];
                                if($total_counts > 0){
                                ?>                    
                                <li class="produto-filtro produtos-caracteristica-caixa-selecao custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" value="<?= $tag['id'] ?>" name="checkbox-tags" id="checkbox-tags-<?= $tag['id'] ?>" url="<?= $loja['site'].'categoria/'.$categoria_nome.'/'.$categoria_id.'/1/'.$filtro_preco_minimo.'/'.$filtro_preco_maximo.'/'.$filtro_marcas ?>" caracteristicas="<?= $filtro_caracteristicas ?>" genero-idade="<?= $filtro_genero_idade ?>" <?php if(in_array($tag['id'],$array_filtro_tags)){ echo 'checked'; } ?>>
                                    <label class="custom-control-label" for="checkbox-tags-<?= $tag['id'] ?>"><?= $tag['nome'] ?><span class="total-resultados-por-filtro">(<?= $total_counts ?>)</span></label>
                                </li>
                            <?php }} ?>
                        </ul>
                    </li>                
                <?php } 

                //BUSCA AS MARCAS E SE TIVER ALGUMA LISTA
                $marcas = mysqli_query($conn, "SELECT id, identificador, nome FROM marca ORDER BY nome ASC");
                
                if(mysqli_num_rows($marcas)){ ?>
                    <li class="produtos-filtros-titulo">Marcas</li>
                    <li class="produtos-caracteristicas">
                        <ul class="produtos-caracteristicas-caixas-selecao">
                            <?php while($marca = mysqli_fetch_array($marcas)){ 
                                $counts = array_count_values($array_marcas);
                                $total_counts = $counts[$marca['id']];
                                if($total_counts > 0){
                                ?>                    
                                <li class="produto-filtro produtos-caracteristica-caixa-selecao custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" value="<?= $marca['id'] ?>" name="checkbox-marcas" id="checkbox-marcas-<?= $marca['id'] ?>" url="<?= $loja['site'].'categoria/'.$categoria_nome.'/'.$categoria_id.'/1/'.$filtro_preco_minimo.'/'.$filtro_preco_maximo ?>" caracteristicas="<?= $filtro_caracteristicas ?>" genero-idade="<?= $filtro_genero_idade ?>" tags="<?= $filtro_tags ?>" <?php if(in_array($marca['id'],$array_filtro_marcas)){ echo 'checked'; } ?>>
                                    <label class="custom-control-label" for="checkbox-marcas-<?= $marca['id'] ?>"><?= $marca['nome'] ?><span class="total-resultados-por-filtro">(<?= $total_counts ?>)</span></label>
                                </li>
                            <?php }} ?>
                        </ul>
                    </li>                
                <?php } 

                //BUSCA OS DIFERENTES ATRIBUTOS DO PRODUTO
                $atributos = mysqli_query($conn,"
                    SELECT pc.id_atributo AS atributo_id, a.nome AS atributo_nome, a.visualizacao AS atributo_visualizacao
                    FROM produto_caracteristica AS pc
                    INNER JOIN atributo AS a ON pc.id_atributo = a.id
                    WHERE a.status = 1
                    GROUP BY id_atributo
                ");

                if(mysqli_num_rows($atributos) > 0){

                    //CRIA AS SESSÕES DE ATRIBUTOS
                    while($atributo = mysqli_fetch_array($atributos)){
                        
                        $counts = array_count_values($array_atributos);
                        $total_counts = $counts[$atributo['atributo_id']];

                        if($total_counts > 0){

                            //IMPRIME O TÍTULO DO ATRIBUTO
                            echo'<li class="produtos-filtros-titulo">'.$atributo['atributo_nome'].'</li>';

                            //BUSCA AS CARACTERÍSTICAS DO PRODUTO
                            $caracteristicas = mysqli_query($conn,"
                                SELECT c.id AS caracteristica_id, c.nome AS caracteristica_nome, c.textura AS caracteristica_textura, c.cor AS caracteristica_cor
                                FROM caracteristica AS c 
                                WHERE c.id_atributo = ".$atributo['atributo_id']." AND c.status = 1
                                ORDER BY c.nome ASC
                            ");

                            ?>
                            <li class="produtos-caracteristicas">
                                
                                <ul class="produtos-caracteristicas-caixas-selecao">
                                    
                                    <?php

                                    //LISTA AS CARACTERISTICAS CADASTRADAS DE CADA ATRIBUTO
                                    while($caracteristica = mysqli_fetch_array($caracteristicas)){
                                    
                                        $counts = array_count_values($array_caracteristicas);
                                        $total_counts = $counts[$caracteristica['caracteristica_id']];

                                        if($total_counts > 0){

                                            if($atributo['atributo_visualizacao'] == 'T'){ ?>
                                                <li class="produto-filtro produtos-caracteristica-caixa-selecao custom-control custom-checkbox">
                                                    <input class="custom-control-input" type="checkbox" value="<?= $caracteristica['caracteristica_id'] ?>" name="checkbox-caracteristicas" id="checkbox-caracteristicas-<?= $caracteristica['caracteristica_id'] ?>" genero-idade="<?= $filtro_genero_idade ?>" tags="<?= $filtro_tags ?>" url="<?= $loja['site'].'categoria/'.$categoria_nome.'/'.$categoria_id.'/1/'.$filtro_preco_minimo.'/'.$filtro_preco_maximo.'/'.$filtro_marcas ?>" <?php if(in_array($caracteristica['caracteristica_id'],$array_filtro_caracteristicas)){ echo 'checked'; } ?>>
                                                    <label class="custom-control-label" for="checkbox-caracteristicas-<?= $caracteristica['caracteristica_id'] ?>"><span class="produtos-caracteristica-textura" style="background-image: url('<?= $loja['site'] ?>imagens/texturas/<?= $caracteristica['caracteristica_textura'] ?>');"></span><?= $caracteristica['caracteristica_nome'] ?><span class="total-resultados-por-filtro">(<?= $total_counts ?>)</span></label>
                                                </li>           
                                            <?php } else if($atributo['atributo_visualizacao'] == 'C'){ ?>
                                                
                                                <li class="produto-filtro produtos-caracteristica-caixa-selecao custom-control custom-checkbox">
                                                    <input class="custom-control-input" type="checkbox" value="<?= $caracteristica['caracteristica_id'] ?>" name="checkbox-caracteristicas" id="checkbox-caracteristicas-<?= $caracteristica['caracteristica_id'] ?>" genero-idade="<?= $filtro_genero_idade ?>" tags="<?= $filtro_tags ?>" url="<?= $loja['site'].'categoria/'.$categoria_nome.'/'.$categoria_id.'/1/'.$filtro_preco_minimo.'/'.$filtro_preco_maximo.'/'.$filtro_marcas ?>" <?php if(in_array($caracteristica['caracteristica_id'],$array_filtro_caracteristicas)){ echo 'checked'; } ?>>
                                                    <label class="custom-control-label" for="checkbox-caracteristicas-<?= $caracteristica['caracteristica_id'] ?>"><span class="produtos-caracteristica-cor" style="background: <?= $caracteristica['caracteristica_cor'] ?>;"></span><?= $caracteristica['caracteristica_nome'] ?><span class="total-resultados-por-filtro">(<?= $total_counts ?>)</span></label>
                                                </li>

                                            <?php } else if($atributo['atributo_visualizacao'] == 'L'){ ?>

                                                <li iclass="produto-filtro produtos-caracteristica-caixa-selecao custom-control custom-checkbox">
                                                    <input class="custom-control-input" type="checkbox" value="<?= $caracteristica['caracteristica_id'] ?>" name="checkbox-caracteristicas" id="checkbox-caracteristicas-<?= $caracteristica['caracteristica_id'] ?>" genero-idade="<?= $filtro_genero_idade ?>" tags="<?= $filtro_tags ?>" url="<?= $loja['site'].'categoria/'.$categoria_nome.'/'.$categoria_id.'/1/'.$filtro_preco_minimo.'/'.$filtro_preco_maximo.'/'.$filtro_marcas ?>" <?php if(in_array($caracteristica['caracteristica_id'],$array_filtro_caracteristicas)){ echo 'checked'; } ?>>
                                                    <label class="custom-control-label" for="checkbox-caracteristicas-<?= $caracteristica['caracteristica_id'] ?>"><?= $caracteristica['caracteristica_nome'] ?><span class="total-resultados-por-filtro">(<?= $total_counts ?>)</span></label>
                                                </li>

                                            <?php } else if($atributo['atributo_visualizacao'] == 'S'){ ?>

                                                <li class="produto-filtro produtos-caracteristica-caixa-selecao custom-control custom-checkbox">
                                                    <input class="custom-control-input" type="checkbox" value="<?= $caracteristica['caracteristica_id'] ?>" name="checkbox-caracteristicas" id="checkbox-caracteristicas-<?= $caracteristica['caracteristica_id'] ?>" genero-idade="<?= $filtro_genero_idade ?>" tags="<?= $filtro_tags ?>" url="<?= $loja['site'].'categoria/'.$categoria_nome.'/'.$categoria_id.'/1/'.$filtro_preco_minimo.'/'.$filtro_preco_maximo.'/'.$filtro_marcas ?>" <?php if(in_array($caracteristica['caracteristica_id'],$array_filtro_caracteristicas)){ echo 'checked'; } ?>>
                                                    <label class="custom-control-label" for="checkbox-caracteristicas-<?= $caracteristica['caracteristica_id'] ?>"><?= $caracteristica['caracteristica_nome'] ?><span class="total-resultados-por-filtro">(<?= $total_counts ?>)</span></label>
                                                </li>

                                            <?php }

                                        }

                                    }
                                
                                    ?>

                                </ul>
                                
                            </li>
                        
                        <?php
                        
                        }
                        
                    }

                }

                //SE FOR LOJA DE ROUPA
                if($loja['loja_roupa'] == 1){ ?>
                    
                    <?php 
                        $counts = array_count_values($array_generos);
                        $total_counts_male   = $counts['male'];
                        $total_counts_female = $counts['female'];
                        $total_counts_unisex = $counts['unisex'];
                    ?>
                    <?php if($total_counts_male > 0 | $total_counts_female > 0 | $total_counts_unisex > 0){ ?>
                        <li class="produtos-filtros-titulo">Gênero</li>
                        <li class="produtos-caracteristicas">
                            <ul class="produtos-caracteristicas-caixas-selecao">
                                <?php if($total_counts_male > 0){ ?>
                                    <li class="produto-filtro produtos-caracteristica-caixa-selecao custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" value="male" name="checkbox-genero" id="checkbox-genero-male" url="<?= $loja['site'].'categoria/'.$categoria_nome.'/'.$categoria_id.'/1/'.$filtro_preco_minimo.'/'.$filtro_preco_maximo.'/'.$filtro_marcas.'/'.$filtro_caracteristicas ?>" tags="<?= $filtro_tags ?>" <?php if(in_array('male',$array_filtro_genero)){ echo 'checked'; } ?>>
                                        <label class="custom-control-label" for="checkbox-genero-male">Masculino<span class="total-resultados-por-filtro">(<?= $total_counts_male ?>)</span></label>
                                    </li>
                                <?php } ?>
                                <?php if($total_counts_female > 0){ ?>
                                    <li class="produto-filtro produtos-caracteristica-caixa-selecao custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" value="female" name="checkbox-genero" id="checkbox-genero-female" url="<?= $loja['site'].'categoria/'.$categoria_nome.'/'.$categoria_id.'/1/'.$filtro_preco_minimo.'/'.$filtro_preco_maximo.'/'.$filtro_marcas.'/'.$filtro_caracteristicas ?>" tags="<?= $filtro_tags ?>" <?php if(in_array('female',$array_filtro_genero)){ echo 'checked'; } ?>>
                                        <label class="custom-control-label" for="checkbox-genero-female">Feminino<span class="total-resultados-por-filtro">(<?= $total_counts_female ?>)</span></label>
                                    </li>
                                <?php } ?>
                                <?php if($total_counts_unisex > 0){ ?>
                                    <li class="produto-filtro produtos-caracteristica-caixa-selecao custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" value="unisex" name="checkbox-genero" id="checkbox-genero-unisex" url="<?= $loja['site'].'categoria/'.$categoria_nome.'/'.$categoria_id.'/1/'.$filtro_preco_minimo.'/'.$filtro_preco_maximo.'/'.$filtro_marcas.'/'.$filtro_caracteristicas ?>" tags="<?= $filtro_tags ?>" <?php if(in_array('unisex',$array_filtro_genero)){ echo 'checked'; } ?>>
                                        <label class="custom-control-label" for="checkbox-genero-unisex">Unisex<span class="total-resultados-por-filtro">(<?= $total_counts_unisex ?>)</span></label>
                                    </li>
                                <?php } ?>
                            </ul>
                        </li>              
                    <?php } ?>       
                                        
                    <?php 
                        $counts = array_count_values($array_idades);
                        $total_counts_newborn = $counts['newborn'];
                        $total_counts_infant  = $counts['infant'];
                        $total_counts_toddler = $counts['toddler'];
                        $total_counts_kids    = $counts['kids'];
                        $total_counts_adult   = $counts['adult'];
                    ?>
                    <?php if($total_counts_newborn > 0 | $total_counts_infant > 0 | $total_counts_toddler > 0 | $total_counts_kids > 0 | $total_counts_adult > 0){ ?>
                        <li class="produtos-filtros-titulo">Idade</li>
                        <li class="produtos-caracteristicas">
                            <ul class="produtos-caracteristicas-caixas-selecao">
                                <?php if($total_counts_newborn > 0){ ?>
                                    <li class="produto-filtro produtos-caracteristica-caixa-selecao custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" value="newborn" name="checkbox-idade" id="checkbox-idade-newborn" url="<?= $loja['site'].'categoria/'.$categoria_nome.'/'.$categoria_id.'/1/'.$filtro_preco_minimo.'/'.$filtro_preco_maximo.'/'.$filtro_marcas.'/'.$filtro_caracteristicas ?>" tags="<?= $filtro_tags ?>" <?php if(in_array('newborn',$array_filtro_idade)){ echo 'checked'; } ?>>
                                        <label class="custom-control-label" for="checkbox-idade-newborn">Recém nascido<span class="total-resultados-por-filtro">(<?= $total_counts_newborn ?>)</span></label>
                                    </li>
                                <?php } ?> 
                                <?php if($total_counts_infant > 0){ ?>
                                    <li class="produto-filtro produtos-caracteristica-caixa-selecao custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" value="infant" name="checkbox-idade" id="checkbox-idade-infant" url="<?= $loja['site'].'categoria/'.$categoria_nome.'/'.$categoria_id.'/1/'.$filtro_preco_minimo.'/'.$filtro_preco_maximo.'/'.$filtro_marcas.'/'.$filtro_caracteristicas ?>" tags="<?= $filtro_tags ?>" <?php if(in_array('infant',$array_filtro_idade)){ echo 'checked'; } ?>>
                                        <label class="custom-control-label" for="checkbox-idade-infant">3 a 12 mêses<span class="total-resultados-por-filtro">(<?= $total_counts_infant ?>)</span></label>
                                    </li>
                                <?php } ?> 
                                <?php if($total_counts_toddler > 0){ ?>
                                    <li class="produto-filtro produtos-caracteristica-caixa-selecao custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" value="toddler" name="checkbox-idade" id="checkbox-idade-toddler" url="<?= $loja['site'].'categoria/'.$categoria_nome.'/'.$categoria_id.'/1/'.$filtro_preco_minimo.'/'.$filtro_preco_maximo.'/'.$filtro_marcas.'/'.$filtro_caracteristicas ?>" tags="<?= $filtro_tags ?>" <?php if(in_array('toddler',$array_filtro_idade)){ echo 'checked'; } ?>>
                                        <label class="custom-control-label" for="checkbox-idade-toddler">1 a 5 anos<span class="total-resultados-por-filtro">(<?= $total_counts_toddler ?>)</span></label>
                                    </li>
                                <?php } ?> 
                                <?php if($total_counts_kids > 0){ ?>
                                    <li class="produto-filtro produtos-caracteristica-caixa-selecao custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" value="kids" name="checkbox-idade" id="checkbox-idade-kids" url="<?= $loja['site'].'categoria/'.$categoria_nome.'/'.$categoria_id.'/1/'.$filtro_preco_minimo.'/'.$filtro_preco_maximo.'/'.$filtro_marcas.'/'.$filtro_caracteristicas ?>" tags="<?= $filtro_tags ?>" <?php if(in_array('kids',$array_filtro_idade)){ echo 'checked'; } ?>>
                                        <label class="custom-control-label" for="checkbox-idade-kids">Infantil<span class="total-resultados-por-filtro">(<?= $total_counts_kids ?>)</span></label>
                                    </li>
                                <?php } ?> 
                                <?php if($total_counts_adult > 0){ ?>
                                    <li class="produto-filtro produtos-caracteristica-caixa-selecao custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" value="adult" name="checkbox-idade" id="checkbox-idade-adult" url="<?= $loja['site'].'categoria/'.$categoria_nome.'/'.$categoria_id.'/1/'.$filtro_preco_minimo.'/'.$filtro_preco_maximo.'/'.$filtro_marcas.'/'.$filtro_caracteristicas ?>" tags="<?= $filtro_tags ?>" <?php if(in_array('adult',$array_filtro_idade)){ echo 'checked'; } ?>>
                                        <label class="custom-control-label" for="checkbox-idade-adult">Adulto<span class="total-resultados-por-filtro">(<?= $total_counts_adult ?>)</span></label>
                                    </li>
                                <?php } ?> 
                            </ul>
                        </li>            
                    <?php } ?>  

                <?php } ?>     
 
            </ul>

        </div>

        <h2 class="d-none">Lista de produtos</h2>

        <!--PRODUTOS-->
        <div id="produtos-produtos" class="col-12 col-lg-9">

            <div class="row">

                <?php 
                
                while($produto = mysqli_fetch_array($busca_produtos)){ 

                    if(!$modo_whatsapp){

                        if($produto['produto_preco'] != 0){

                            //TRATA O PREÇO CASO TENHA PROMOÇÃO NO PRODUTO
                            if($produto['produto_promocao'] != '' | $produto['categoria_promocao'] != ''){
                                if($produto['produto_promocao'] >= $produto['categoria_promocao']){
                                    $porcentagem_desconto = $produto['produto_promocao'];
                                } else {
                                    $porcentagem_desconto = $produto['categoria_promocao'];
                                }
                                $tem_promocao              = true;
                                $produto_preco             = $produto['produto_preco'];
                                $produto_desconto          = $produto['produto_preco'] * $porcentagem_desconto / 100;
                                $produto_preco_venda       = $produto_preco - $produto_desconto;
                                $produto_preco_venda_busca = $produto_preco_venda;
                                $valor_parcela             = $produto_preco_venda/$pagamento['parcelas'];
                                $produto_preco             = 'R$ '.number_format($produto_preco,2,",",".");
                                $produto_preco_venda       = 'R$ '.number_format($produto_preco_venda,2,",",".");
                                $valor_parcela             = 'R$ '.number_format($valor_parcela,2,",",".");
                                $produto_preco_final       = '<span class="produto-container-valor-original">'.$produto_preco.'</span><span class="produto-container-valor-final">'.$produto_preco_venda.'</span>';
                                if($loja['design_exibir_parcelamento'] == 1){
                                    $parcelamento   = $pagamento['parcelas'].'x de '.$valor_parcela.' SEM JUROS';
                                    $produto_preco_final .= '<span class="produto-container-parcelamento">'.$parcelamento.'</span>';
                                }
                            } else {
                                $tem_promocao              = false;
                                $valor_parcela             = $produto['produto_preco']/$pagamento['parcelas'];
                                $produto_preco_venda       = 'R$ '.number_format($produto['produto_preco'],2,",",".");
                                $produto_preco_venda_busca = $produto['produto_preco'];
                                $valor_parcela             = 'R$ '.number_format($valor_parcela,2,",",".");
                                $produto_preco_final       = '<span class="produto-container-valor-final">'.$produto_preco_venda.'</span>';
                                if($loja['design_exibir_parcelamento'] == 1){
                                    $parcelamento   = $pagamento['parcelas'].'x de '.$valor_parcela.' SEM JUROS';
                                    $produto_preco_final .= '<span class="produto-container-parcelamento">'.$parcelamento.'</span>';
                                }
                            }

                            if($produto['produto_estoque'] <= 0){
                                $em_estoque                = false;
                                $produto_preco_venda_busca = '';
                                $produto_preco_final       = '<a href="https://wa.me/55'.preg_replace("/[^0-9]/", "", $loja['whatsapp']).'?text='.urlencode("Olá! Vi o produto ".$produto['produto_nome']." no site e gostaria de mais informações.").'" target="_blank" class="produto-container-valor-esgotado">WhatsApp</a>';
                            } else {
                                $em_estoque = true;
                            }
                    
                        } else {

                            $em_estoque   = true;
                            $tem_promocao = false;
                            $produto_preco_venda_busca = '';
                            $produto_preco_final = '<a href="https://wa.me/55'.preg_replace("/[^0-9]/", "", $loja['whatsapp']).'?text='.urlencode("Olá! Vi o produto ".$produto['produto_nome']." no site e gostaria de mais informações.").'" target="_blank" class="produto-container-valor-esgotado">WhatsApp</a>';
                        
                        }
    
                    } else {
                        
                        $produto_preco_venda_busca = '';
                        
                        if($loja['modo_whatsapp_preco'] == 1){

                            if($produto['produto_preco'] != 0){
    
                                //TRATA O PREÇO CASO TENHA PROMOÇÃO NO PRODUTO
                                if($produto['produto_promocao'] != '' | $produto['categoria_promocao'] != ''){
                                    if($produto['produto_promocao'] >= $produto['categoria_promocao']){
                                        $porcentagem_desconto = $produto['produto_promocao'];
                                    } else {
                                        $porcentagem_desconto = $produto['categoria_promocao'];
                                    }
                                    $tem_promocao        = true;
                                    $produto_preco       = $produto['produto_preco'];
                                    $produto_desconto    = $produto['produto_preco'] * $porcentagem_desconto / 100;
                                    $produto_preco_venda = $produto_preco - $produto_desconto;
                                    $valor_parcela       = $produto_preco_venda/$pagamento['parcelas'];
                                    $produto_preco       = 'R$ '.number_format($produto_preco,2,",",".");
                                    $produto_preco_venda = 'R$ '.number_format($produto_preco_venda,2,",",".");
                                    $valor_parcela       = 'R$ '.number_format($valor_parcela,2,",",".");
                                    $produto_preco_final = '<span class="produto-container-valor-original">'.$produto_preco.'</span><span class="produto-container-valor-final">'.$produto_preco_venda.'</span>';
                                    if($loja['design_exibir_parcelamento'] == 1){
                                        $parcelamento   = $pagamento['parcelas'].'x de '.$valor_parcela.' SEM JUROS';
                                        $produto_preco_final .= '<span class="produto-container-parcelamento">'.$parcelamento.'</span>';
                                    }
                                    $produto_preco_final .= '<br><a href="https://wa.me/55'.preg_replace("/[^0-9]/", "", $loja['whatsapp']).'?text='.urlencode("Olá! Vi o produto ".$produto['produto_nome']." no site e gostaria de fazer um pedido.").'" target="_blank" class="produto-container-valor-esgotado">Pedir pelo whats</a>';
                                } else {
                                    $tem_promocao        = false;
                                    $valor_parcela       = $produto['produto_preco']/$pagamento['parcelas'];
                                    $produto_preco_venda = 'R$ '.number_format($produto['produto_preco'],2,",",".");
                                    $valor_parcela       = 'R$ '.number_format($valor_parcela,2,",",".");
                                    $produto_preco_final = '<span class="produto-container-valor-final">'.$produto_preco_venda.'</span>';
                                    if($loja['design_exibir_parcelamento'] == 1){
                                        $parcelamento   = $pagamento['parcelas'].'x de '.$valor_parcela.' SEM JUROS';
                                        $produto_preco_final .= '<span class="produto-container-parcelamento">'.$parcelamento.'</span>';
                                    }
                                    $produto_preco_final .= '<br><a href="https://wa.me/55'.preg_replace("/[^0-9]/", "", $loja['whatsapp']).'?text='.urlencode("Olá! Vi o produto ".$produto['produto_nome']." no site e gostaria de fazer um pedido.").'" target="_blank" class="produto-container-valor-esgotado">Pedir pelo whats</a>';
                                }
                                    
                                if($produto['produto_estoque'] <= 0){
                                    $em_estoque = false;
                                    $produto_preco_final = '';
                                } else {
                                    $em_estoque = true;
                                }
        
                            } else {
        
                                $tem_promocao = false;
                                if($produto['produto_estoque'] <= 0){
                                    $em_estoque = false;
                                    $produto_preco_final = '';
                                } else {
                                    $em_estoque = true;
                                    $produto_preco_final = '<a href="https://wa.me/55'.preg_replace("/[^0-9]/", "", $loja['whatsapp']).'?text='.urlencode("Olá! Vi o produto ".$produto['produto_nome']." no site e gostaria de fazer um pedido.").'" target="_blank" class="produto-container-valor-esgotado">Pedir pelo whats</a>';
                                }
                                
                            }
    
                        } else {
    
                            $em_estoque   = true;
                            $tem_promocao = false;
                            $produto_preco_final = '<a href="https://wa.me/55'.preg_replace("/[^0-9]/", "", $loja['whatsapp']).'?text='.urlencode("Olá! Vi o produto ".$produto['produto_nome']." no site e gostaria de mais informações.").'" target="_blank" class="produto-container-valor-esgotado">WhatsApp</a>';
                        
                        }

                    }
                    
                    ?>
                    <div id="produto-<?= $produto['produto_id'] ?>" produto="<?= $produto['produto_id'] ?>" class="produto col-12 col-md-6 col-xl-4 col-sxl-4" nome="<?= $produto['produto_nome'] ?>" relevancia="<?= $produto['produto_relevancia'] ?>" categoria="<?= $produto['produto_categoria_id'] ?>" marca="<?= $produto['produto_marca_id'] ?>" atributos="<?= $produto['produto_atributos'] ?>" caracteristicas="<?= $produto['produto_caracteristicas'] ?>" promocao="<?php if($tem_promocao){ echo '1'; } else { echo '0'; } ?>" preco="<?= $produto_preco_venda_busca ?>" estoque="<?php if($em_estoque){ echo '1'; } else { echo '0'; } ?>">

                        <div class="produto-link" onclick="javascript: window.location.href = '<?= $loja['site'] ?>produto/<?= urlProduto($produto['produto_categoria']) ?>/<?= urlProduto($produto['produto_nome']) ?>/<?= $produto['produto_id'] ?>'" data-produto-id="<?= $produto['produto_id'] ?>" data-produto-lista="Produtos" data-produto-lista-id="produtos">
                            <div class="produto-container <?php if(!$em_estoque){ echo 'produto-container-esgotado'; } else { ?> <?php if($tem_promocao){ echo 'produto-container-promocao'; } } ?>">
                                <ul>
                                    <li class="produto-container-imagem">
                                    <img class="lozad" src="<?php if($produto['produto_capa'] != ''){ ?><?= $loja['site'] ?>imagens/produtos/media/<?= $produto['produto_capa'] ?><?php } else { ?><?= $loja['site'] ?>imagens/produto_sem_foto.png<?php } ?>" data-src="<?php if($produto['produto_capa'] != ''){ ?><?= $loja['site'] ?>imagens/fundo-produto.jpg<?php } else { ?><?= $loja['site'] ?>imagens/produto_sem_foto.png<?php } ?>" alt="<?= altProduto($produto['produto_nome']) ?>" title="<?= $produto['produto_nome'] ?>">
                                    <?php if($em_estoque){ ?> 
                                            <?php if($tem_promocao){ ?><span class="produto-container-promocao">#Promoção</span><?php } ?>
                                        <?php } else { ?>
                                            <span class="produto-container-esgotado">#Esgotado</span>
                                        <?php } ?>
                                    </li>
                                    <li class="produto-container-categoria"><h3><?= $produto['produto_categoria'] ?></h3></li>
                                    <li class="produto-container-nome"><h4><?= $produto['produto_nome'] ?></h4></li>
                                    <li class="produto-container-valor"><?= $produto_preco_final ?></li>
                                    <?php if(base64_encode(base64_decode($produto['produto_descricao'], true)) === $produto['produto_descricao']){ $produto_descricao = base64_decode($produto['produto_descricao']); } else { $produto_descricao = $produto['produto_descricao']; } ?>
                                    <li class="produto-container-descricao"><?= str_replace('<br />', '', $produto_descricao) ?></li>
                                </ul>                        
                            </div>  
                        </div>                                  

                    </div>
                <?php } ?>
            
            </div>  

            <?php 
            
            //MONTA A PAGINAÇÃO
            if($total_paginas > 1){ $url_pagina = $loja['site'].'categoria/'.$categoria_nome.'/'.$categoria_id; ?>
                <input type="hidden" id="pagina-atual" value="<?= $pagina ?>">
                <input type="hidden" id="total-paginas" value="<?= $total_paginas ?>">
                <input type="hidden" id="url-pagina" value="<?= $url_pagina ?>">
                <input type="hidden" id="complemento-pagina" value="<?= '/'.$filtro_preco_minimo.'/'.$filtro_preco_maximo.'/'.$filtro_marcas.'/'.$filtro_caracteristicas.'/'.$filtro_genero_idade.'/'.$filtro_ordenacao ?>">
                <nav aria-label="Page navigations">
                    <ul class="pagination justify-content-center mt-3 mb-4 paginacao-produtos">
                        <?php for($pag = 1; $pag <= $total_paginas; $pag++){ ?>
                            <li class="page-item"><?= $pag ?></li>
                        <?php } ?>
                    </ul>
                </nav>
            <?php } ?>

        </div>

    <div>

    <div id="btn-filtros-mobile" class="d-block d-lg-none">
        <ul>
            <li><img src="<?= $loja['site'].'imagens/chevron-duplo.png' ?>" alt="Filtrar"></li>
            <li>FILTRAR</li>
        </ul>
    </div>    

    <div id="filtros-mobile"></div>
    
<?php } else { ?>

    <ul class="row pt-5 pb-4 text-center">
        <li class="col-12"><h3>Nenhum produto encontrado</h3></li>
    </ul>
    <ul class="row pb-5 justify-content-center">
        <li class="col-6 col-md-3 col-lg-2"><a class="btn-escuro" href="javascript: window.history.back();">Voltar</a></li>
    </ul>

<?php } } ?>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="<?= $loja['site'] ?>modulos/produtos/js/nouislider.min.js"></script>
<script type="text/javascript" src="<?= $loja['site'] ?>modulos/produtos/js/jquery.twbsPagination.min.js"></script>
<script type="text/javascript" src="<?= $loja['site'] ?>modulos/produtos/js/scripts-1.1.js"></script>