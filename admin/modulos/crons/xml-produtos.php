<?php

//CONECTA AO BANCO
include '../../../bd/conecta.php';

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

//BUSCA OS DADOS DA LOJA
$busca_loja = mysqli_query($conn, "SELECT nome, site, loja_roupa FROM loja WHERE id = 1");
$loja       = mysqli_fetch_array($busca_loja);   

//BUSCA OS PRODUTOS CADASTRADOS ATIVOS
$produtos = mysqli_query($conn, "
    SELECT p.id AS produto_id, p.sku AS produto_sku, p.mpn AS produto_mpn, p.gtin AS produto_gtin, p.idade AS produto_idade, p.genero AS produto_genero, p.nome AS produto_nome, p.categoria_google, p.preco AS produto_preco, p.estoque AS produto_estoque, p.descricao AS produto_descricao, m.nome AS marca_nome, c.nome AS categoria_nome, 
    (SELECT ppp.porcentagem FROM promocao AS ppp WHERE p.id = ppp.id_produto AND p.promocao = 1 AND ppp.status = 1 ORDER BY ppp.data_cadastro DESC LIMIT 1) AS produto_promocao,
    (SELECT ppc.porcentagem FROM promocao AS ppc WHERE p.id_categoria = ppc.id_categoria AND c.promocao = 1 AND ppc.status = 1 ORDER BY ppc.data_cadastro DESC LIMIT 1) AS categoria_promocao 
    FROM produto AS p 
    LEFT JOIN marca AS m ON m.id = p.id_marca 
    LEFT JOIN categoria AS c ON c.id = p.id_categoria 
    WHERE p.status = 1 
    ORDER BY p.id
");

//ESTANCIA VARIÁVEIS
$feed_products = [];

//LOOP NOS PRODUTOS
while ($produto = mysqli_fetch_array($produtos)){

    //BUSCA IMAGENS DO PRODUTO
    $imagens_produto = mysqli_query($conn, "SELECT imagem, capa FROM produto_imagem WHERE id_produto = ".$produto['produto_id']." ORDER BY ordem ASC");
            
    //ARRAY DE IMAGENS DO PRODUTO
    $array_imagens_produto = array();

    //PERCORRE AS IMAGENS
    if(mysqli_num_rows($imagens_produto) > 0){

        //PREENCHE O ARRAY DE IMAGENS DO PRODUTO E SETA A IMAGEM DE CAPA
        while($imagem_produto = mysqli_fetch_array($imagens_produto)){
    
            if($imagem_produto['capa'] == 1){                
                $imagem_capa = $loja['site'].'imagens/produtos/grande/'.$imagem_produto['imagem'];
            } else {
                $array_imagens_produto[] = $loja['site'].'imagens/produtos/grande/'.$imagem_produto['imagem'];
            }
        }

    //SE NÃO TIVER NENHUMA IMAGEM, PEGA O ARQUIVO SEM FOTO
    } else {
        $imagem_capa = $loja['site'].'imagens/produto_sem_foto.png';
    }

    //VERIFICA SE TEM MARCA
    $marca = $produto['marca_nome'];

    //GERA LINK
    $link = $loja['site'].'produto/'.urlProduto($produto['categoria_nome']).'/'.urlProduto($produto['produto_nome']).'/'.$produto['produto_id'];
    
    //PADRONIZA PREÇO
    $preco = number_format($produto['produto_preco'], 2, ',', '.').' BRL';

    //GERA GTIN
    $gtin = $produto['produto_gtin'];

    //GERA MPN
    $mpn = $produto['produto_mpn'];

    //RETIRA PULOS DE LINHA DA DESCRICAO    
    if(base64_encode(base64_decode($produto['produto_descricao'], true)) === $produto['produto_descricao']){ 
        $produto_descricao = base64_decode($produto['produto_descricao']); 
    } else { 
        $produto_descricao = $produto['produto_descricao']; 
    }
    $descricao = str_replace('"','&quot;',preg_replace('/( )+/', ' ', str_replace("<br>","",str_replace("<br/>","",str_replace("<br />","",$produto_descricao)))));
                   
    //VERIFICA SE O PRODUTO POSSUI VARIAÇÕES
    $busca_variacoes_produto = mysqli_query($conn, "SELECT * FROM produto_variacao WHERE status = 1 AND id_produto = ".$produto['produto_id']);
    $n_variacoes             = mysqli_num_rows($busca_variacoes_produto);
    $tem_variacoes           = false;    
    if($n_variacoes > 0){ $tem_variacoes = true; }

    if($tem_variacoes & $loja['loja_roupa'] == 1){

        $contador_variacoes = 0;

        while($variacao = mysqli_fetch_array($busca_variacoes_produto)){
            
            $contador_variacoes++;
                     
            //CRIA OS ARRAYS DE INFORMAÇÃO PRA ENVIAR PARA O GOOGLE
            $gf_product = [];
            $gf_product_adicional_images = [];              

            //IMAGENS ADICIONAIS
            $total_imagems_adicionais = count($array_imagens_produto);
            if($total_imagems_adicionais > 0){
                for($i=0; $i < $total_imagems_adicionais; $i++){
                    array_push($gf_product_adicional_images,$array_imagens_produto[$i]);
                }
                $gf_product['g:additional_image_link'] = $gf_product_adicional_images;
            }

            //ATRIBUTOS            
            $gf_product['g:id']                      = $produto['produto_sku'].$contador_variacoes;
            $gf_product['g:item_group_id']           = $produto['produto_sku'];
            $gf_product['g:sku']                     = $produto['produto_sku'];
            $gf_product['g:gtin']                    = $gtin;
            $gf_product['g:mpn']                     = $mpn;
            $gf_product['g:title']                   = $produto['produto_nome'];
            $gf_product['g:description']             = $descricao;
            $gf_product['g:link']                    = $link;
            $gf_product['g:image_link']              = $imagem_capa;
            $gf_product['g:price']                   = $preco;
                
            //SE O PRODUTO ESTIVER EM PROMOÇÃO   
            if($produto['produto_promocao'] != '' | $produto['categoria_promocao'] != ''){
                if($produto['produto_promocao'] >= $produto['categoria_promocao']){ $porcentagem_desconto = $produto['produto_promocao'] ;
                } else { $porcentagem_desconto = $produto['categoria_promocao']; }                       
                $produto_desconto    = $produto['produto_preco']*$porcentagem_desconto / 100;   
                $produto_preco_venda = $produto['produto_preco']-$produto_desconto;     
                $gf_product['g:is_on_sale'] = true;  
                $gf_product['g:sale_price'] = number_format($produto_preco_venda, 2, ',', '.').' BRL';
                //$gf_product['g:sale_price_effective_date'] = $product['sale_startdate']." ".$product['sale_enddate'];
            } else {
                $gf_product['g:is_on_sale']                = false;  
            }        

            $gf_product['g:google_product_category'] = $produto['categoria_google'];
            $gf_product['g:brand']                   = $marca;
            $gf_product['g:condition']               = 'NEW';           

            //SE NÃO POSSUI NEM GTIN NEM MPN MARCA COMO NÃO IDENTIFICADO
            if (($gf_product['g:gtin'] == "") && ($gf_product['g:mpn'] == "")){ $gf_product['g:identifier_exists'] = "no"; };

            //PADRONIZA ESTOQUE
            if($variacao['estoque'] > 0){ 
                $estoque = "in_stock"; 
            } else { 
                $estoque = "out_of_stock"; 
            }  

            $gf_product['is_clothing']    = true;
            $gf_product['g:availability'] = $estoque;
                            
            $gf_product['g:age_group']    = $produto['produto_idade'];
            $gf_product['g:gender']       = $produto['produto_genero'];  
                               
            if($variacao['id_caracteristica_primaria'] != '' & $variacao['id_caracteristica_secundaria'] != ''){        

                $busca_caracteristica_primaria   = mysqli_query($conn, "SELECT nome, textura, cor FROM caracteristica WHERE id = ".$variacao['id_caracteristica_primaria']);        
                $busca_caracteristica_secundaria = mysqli_query($conn, "SELECT nome, textura, cor FROM caracteristica WHERE id = ".$variacao['id_caracteristica_secundaria']);
                $caracteristica_primaria         = mysqli_fetch_array($busca_caracteristica_primaria);
                $caracteristica_secundaria       = mysqli_fetch_array($busca_caracteristica_secundaria);
                                
                if($caracteristica_primaria['textura'] != '' | $caracteristica_primaria['cor'] != ''){
                    $gf_product['g:color'] = $caracteristica_primaria['nome'];
                } else {
                    $gf_product['g:size']  = $caracteristica_primaria['nome']; 
                }           

                if($caracteristica_secundaria['textura'] != '' | $caracteristica_secundaria['cor'] != ''){
                    $gf_product['g:color'] = $caracteristica_secundaria['nome'];
                } else {
                    $gf_product['g:size']  = $caracteristica_secundaria['nome']; 
                }       
                                 
            } else if($variacao['id_caracteristica_primaria'] != '' & $variacao['id_caracteristica_secundaria'] == ''){    
                                    
                $busca_caracteristica_primaria = mysqli_query($conn, "SELECT nome, textura, cor FROM caracteristica WHERE id = ".$variacao['id_caracteristica_primaria']);         
                $caracteristica_primaria       = mysqli_fetch_array($busca_caracteristica_primaria);  
                
                if($caracteristica_primaria['textura'] != '' | $caracteristica_primaria['cor'] != ''){
                    $gf_product['g:color'] = $caracteristica_primaria['nome'];
                } else {
                    $gf_product['g:size']  = $caracteristica_primaria['nome']; 
                }                        
                
            }   


            $feed_products[] = $gf_product;

        }

    } else {
                     
        //CRIA OS ARRAYS DE INFORMAÇÃO PRA ENVIAR PARA O GOOGLE
        $gf_product = [];
        $gf_product_adicional_images = [];              

        //IMAGENS ADICIONAIS
        $total_imagems_adicionais = count($array_imagens_produto);
        if($total_imagems_adicionais > 0){
            for($i=0; $i < $total_imagems_adicionais; $i++){
                array_push($gf_product_adicional_images,$array_imagens_produto[$i]);
            }
            $gf_product['g:additional_image_link'] = $gf_product_adicional_images;
        }
         
        //ATRIBUTOS            
        $gf_product['g:id']                      = $produto['produto_id'];
        $gf_product['g:item_group_id']           = $produto['produto_sku'];
        $gf_product['g:sku']                     = $produto['produto_sku'];
        $gf_product['g:gtin']                    = $gtin;
        $gf_product['g:mpn']                     = $mpn;
        $gf_product['g:title']                   = $produto['produto_nome'];
        $gf_product['g:description']             = $descricao;
        $gf_product['g:link']                    = $link;
        $gf_product['g:image_link']              = $imagem_capa;
        $gf_product['g:price']                   = $preco;
                
        //SE O PRODUTO ESTIVER EM PROMOÇÃO   
        if($produto['produto_promocao'] != '' | $produto['categoria_promocao'] != ''){
            if($produto['produto_promocao'] >= $produto['categoria_promocao']){ $porcentagem_desconto = $produto['produto_promocao'] ;
            } else { $porcentagem_desconto = $produto['categoria_promocao']; }                       
            $produto_desconto    = $produto['produto_preco']*$porcentagem_desconto / 100;   
            $produto_preco_venda = $produto['produto_preco']-$produto_desconto;     
            $gf_product['g:is_on_sale'] = true;              
            $gf_product['g:sale_price'] = number_format($produto_preco_venda, 2, ',', '.').' BRL';
            //$gf_product['g:sale_price_effective_date'] = $product['sale_startdate']." ".$product['sale_enddate'];
        } else {
            $gf_product['g:is_on_sale']                = false;  
        }   

        $gf_product['g:google_product_category'] = $produto['categoria_google'];
        $gf_product['g:brand']                   = $marca;
        $gf_product['g:condition']               = 'NEW';      
       
        //SE NÃO POSSUI NEM GTIN NEM MPN MARCA COMO NÃO IDENTIFICADO
        if (($gf_product['g:gtin'] == "") && ($gf_product['g:mpn'] == "")){ $gf_product['g:identifier_exists'] = "no"; };

        //SE FOR LOJA DE ROUPAS
        if($loja['loja_roupa'] == 1){
            $gf_product['is_clothing'] = true;
            $gf_product['g:age_group'] = $produto['produto_idade'];
            $gf_product['g:gender']    = $produto['produto_genero'];  
        } else {
            $gf_product['is_clothing'] = false;
        }
        
        //PADRONIZA ESTOQUE
        if($produto['produto_estoque'] > 0){ 
            $estoque = "in_stock"; 
        } else { 
            $estoque = "out_of_stock"; 
        }  

        $gf_product['g:availability'] = $estoque;

        $feed_products[] = $gf_product;

    }

}

$doc = new DOMDocument('1.0', 'UTF-8');

$xmlRoot = $doc->createElement("rss");
$xmlRoot = $doc->appendChild($xmlRoot);
$xmlRoot->setAttribute('version', '2.0');
$xmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:g', "http://base.google.com/ns/1.0");

$channelNode = $xmlRoot->appendChild($doc->createElement('channel'));
$channelNode->appendChild($doc->createElement('title', $loja['nome']));
$channelNode->appendChild($doc->createElement('link' , $loja['site']));

foreach ($feed_products as $product) {

    $itemNode = $channelNode->appendChild($doc->createElement('item'));

    foreach($product as $key=>$value) {

        if ($value != "") {

            if (is_array($product[$key])) {

                foreach($product[$key] as $key2=>$value2){   
                    $itemNode->appendChild($doc->createElement('g:additional_image_link'))->appendChild($doc->createTextNode($value2));
                }

            } else {

               $itemNode->appendChild($doc->createElement($key))->appendChild($doc->createTextNode($value));
           
            }

        } else {

            $itemNode->appendChild($doc->createElement($key));

        }

    }

}

$doc->formatOutput = true;
$doc->save('produtos.xml');