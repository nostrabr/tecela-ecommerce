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
$busca_loja = mysqli_query($conn, "SELECT nome, site FROM loja WHERE id = 1");
$loja       = mysqli_fetch_array($busca_loja);   

//DATA ATUAL
$datetime = new DateTime(date('Y-m-d H:i:s'));
$date = $datetime->format(DateTime::ATOM);

//CABEÇALHO DO XML
$xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
    
//INCREMENTA O XML
$xml .= '
<url>
    <loc>'.$loja['site'].'</loc>
    <lastmod>'.$date.'</lastmod>
    <changefreq>weekly</changefreq>
    <priority>1.00</priority>
</url>';

$xml .= '
<url>
    <loc>'.$loja['site'].'promocao</loc>
    <lastmod>'.$date.'</lastmod>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
</url>';

$xml .= '
<url>
    <loc>'.$loja['site'].'vistos-recentemente</loc>
    <lastmod>'.$date.'</lastmod>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
</url>';

$xml .= '
<url>
    <loc>'.$loja['site'].'mais-vendidos</loc>
    <lastmod>'.$date.'</lastmod>
    <changefreq>daily</changefreq>
    <priority>1.00</priority>
</url>';

$xml .= '
<url>
    <loc>'.$loja['site'].'contato</loc>
    <lastmod>'.$date.'</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.85</priority>
</url>';

$xml .= '
<url>
    <loc>'.$loja['site'].'localizacao</loc>
    <lastmod>'.$date.'</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.85</priority>
</url>';

$xml .= '
<url>
    <loc>'.$loja['site'].'sobre</loc>
    <lastmod>'.$date.'</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.85</priority>
</url>';

//BUSCA AS CATEGORIAS
$categorias = mysqli_query($conn, "SELECT id, nome, data_cadastro FROM categoria ORDER BY nivel, ordem");

//ADICIONA AS CATEGORIAS AO XML
while($categoria = mysqli_fetch_array($categorias)){

    //GERA OS LINKS
    $link_categoria = $loja['site'].'categoria/'.urlProduto($categoria['nome']).'/'.$categoria['id'];
    $data_edicao    = new DateTime($categoria['data_cadastro']);
    $data_edicao    = $data_edicao->format(DateTime::ATOM);
    
    $xml .= '
    <url>
        <loc>'.$link_categoria.'</loc>
        <lastmod>'.$data_edicao.'</lastmod>
        <changefreq>weekly</changefreq>
        <priority>1</priority>
    </url>';
    
}

//BUSCA AS PRODUTOS CADASTRADOS ATIVOS
$produtos = mysqli_query($conn, "
    SELECT p.id AS produto_id, p.nome AS produto_nome, p.data_edicao AS produto_edicao, c.nome AS produto_categoria,
    (SELECT imagem FROM produto_imagem AS pi WHERE capa = 1 AND pi.id_produto = p.id) AS produto_imagem
    FROM produto AS p 
    LEFT JOIN categoria AS c ON c.id = p.id_categoria
    WHERE p.status = 1 
    ORDER BY p.id
");

//ADICIONA OS PRODUTOS AO XML
while($produto = mysqli_fetch_array($produtos)){

//GERA OS LINKS
$link_produto = $loja['site'].'produto/'.urlProduto($produto['produto_categoria']).'/'.urlProduto($produto['produto_nome']).'/'.$produto['produto_id'];
$link_imagem  = $loja['site'].'imagens/produtos/grande/'.$produto['produto_imagem'];
$data_edicao  = new DateTime($produto['produto_edicao']);
$data_edicao  = $data_edicao->format(DateTime::ATOM);

$xml .= '
<url>
    <loc>'.$link_produto.'</loc>
    <lastmod>'.$data_edicao.'</lastmod>
    <changefreq>daily</changefreq>
    <image:image>
        <image:loc>'.$link_imagem.'</image:loc>
    </image:image>
    <priority>1</priority>
</url>';

}

$busca_paginas_customizadas = mysqli_query($conn, "SELECT * FROM pagina_customizada WHERE status = 1");
while($pagina_customizada = mysqli_fetch_array($busca_paginas_customizadas)){

    $xml .= '
    <url>
        <loc>'.$loja['site'].'pagina.php?id='.$pagina_customizada['identificador'].'</loc>
        <lastmod>'.$date.'</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.85</priority>
    </url>';

}

$xml .= '</urlset>';

// Abre o arquivo ou tenta cria-lo se ele não exixtir
$arquivo = fopen('../../../sitemap.xml', 'w');
fwrite($arquivo, $xml);
fclose($arquivo);

/*
if (fwrite($arquivo, $xml)) {
    echo "Arquivo sitemap.xml criado com sucesso";
} else {
    echo "Não foi possível criar o arquivo. Verifique as permissões do diretório.";
}*/

// Compactar arquivo sitemap para GZIP
/*
$data = implode("", file("sitemap.xml"));
$gzdata = gzencode($data, 9);
$fp = fopen("sitemap.xml.gz", "w");
fwrite($fp, $gzdata);
fclose($fp);*/

//https://aristides.dev/criar-sitemap-automaticamente-em-php/