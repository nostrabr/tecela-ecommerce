<?php

//INICIA A SESSION SE JÁ NÃO FOI INICIADA
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//SE VEIO LOGADO DO ADMIN
if(isset($_SESSION["plataforma"])) {
    if($_SESSION["plataforma"] === 'ADMIN') {
        echo "<script>window.location.href='logout.php';</script>";
    }
}

//GERA UM HASH PARA O VISITANTE CASO NÃO TENHA SIDO SETADO
if(!isset($_SESSION["visitante"])) {

    //SE TEM SETADO O COOKIE DO VISITANTE, ATRIBUI
    if(isset($_COOKIE['visitante'])){   
        $_SESSION['visitante'] = $_COOKIE['visitante'];
        setcookie("visitante", $_COOKIE['visitante'], time()+(3600*24*30*12*5), "/");

    //SENÃO CRIA UM NOVO
    } else {    
        $visitante =  md5(date("Y-m-d H:i:s").filter_input(INPUT_SERVER, "REMOTE_ADDR", FILTER_DEFAULT).filter_input(INPUT_SERVER, "HTTP_USER_AGENT", FILTER_DEFAULT));
        setcookie("visitante", $visitante, time()+(3600*24*30*12*5), "/");
        $_SESSION['visitante'] = $visitante;        
    }

} else {
    //SE ESTÁ SETADO, MAS ESTÁ DIFERENTE DO COOKIE, ATRIBUI O DO COOKIE E DA UM REFRESH DA DATA DE EXPIRAÇÃO
    if(isset($_COOKIE['visitante']) & $_SESSION["visitante"] != $_COOKIE['visitante']){ $_SESSION['visitante'] = $_COOKIE['visitante']; }
    if(isset($_COOKIE['visitante'])){ setcookie("visitante", $_COOKIE['visitante'], time()+(3600*24*30*12*5), "/"); }
}

//VERIFICA SE TEM PROMOÇÃO VENCIDA E DESATIVA
$busca_produtos_promocao = mysqli_query($conn, "SELECT produto.id AS id_produto, promocao.id AS id_promocao, validade, promocao.status FROM produto INNER JOIN promocao ON produto.id = promocao.id_produto WHERE promocao = 1 AND validade < DATE(NOW())");
while($produtos_promocao = mysqli_fetch_array($busca_produtos_promocao)){
	if((strtotime($produtos_promocao['validade']) < strtotime(date('Y-m-d'))) & $produtos_promocao['status'] == 1){
		//ALTERA O STATUS DA PROMOÇÃO NO PRODUTO
		mysqli_query($conn, "UPDATE produto SET promocao = 0 WHERE id = ".$produtos_promocao["id_produto"]);
		//ENCERRA A PROMOÇÃO
		mysqli_query($conn, "UPDATE promocao SET data_desativacao = NOW(), status = 0 WHERE id = ".$produtos_promocao['id_promocao']);
	}
}

//VERIFICA SE TEM PROMOÇÃO VENCIDA E DESATIVA
$busca_categorias_promocao = mysqli_query($conn, "SELECT categoria.id AS id_categoria, promocao.id AS id_promocao, validade, promocao.status FROM categoria INNER JOIN promocao ON categoria.id = promocao.id_categoria WHERE promocao = 1 AND validade < DATE(NOW())");
while($categorias_promocao = mysqli_fetch_array($busca_categorias_promocao)){
	if((strtotime($categorias_promocao['validade']) < strtotime(date('Y-m-d'))) & $categorias_promocao['status'] == 1){
        //ALTERA O STATUS DA PROMOÇÃO NA CATEGORIA
		mysqli_query($conn, "UPDATE categoria SET promocao = 0 WHERE id = ".$categorias_promocao["id_categoria"]);
		//ENCERRA A PROMOÇÃO
		mysqli_query($conn, "UPDATE promocao SET data_desativacao = NOW(), status = 0 WHERE id = ".$categorias_promocao['id_promocao']);
	}
}

//BUSCA OS DADOS DA LOJA
$busca_loja = mysqli_query($conn, "
    SELECT l.*, c.nome AS nome_cidade, e.sigla AS sigla_estado 
    FROM loja AS l 
    LEFT JOIN cidade AS c ON l.cidade = c.id 
    LEFT JOIN estado AS e ON l.estado = e.id 
    WHERE l.id = 1
");
$loja       = mysqli_fetch_array($busca_loja);

//VERIFICA SE O MODO WHATSAPP ESTÁ ATIVADO E ATRIBUI À VARIÁVEL
if($loja['modo_whatsapp'] == 0){ 
    $modo_whatsapp = false;
    $modo_whatsapp_simples = false;
} else {
    $modo_whatsapp = true;
    if($loja['modo_whatsapp_simples'] == 0){ 
        $modo_whatsapp_simples = false;
    } else {
        $modo_whatsapp_simples = true;
    }
}

//BUSCA A CONFIGURAÇÃO DO FRETE
$busca_frete = mysqli_query($conn, "SELECT melhor_envio FROM frete WHERE id = 1");
$frete       = mysqli_fetch_array($busca_frete);

//CONFIGURA A VARIÁVEL FRETE_ATIVADO
if($frete['melhor_envio'] == 1){
    $frete_ativado = true;
} else {
    $frete_ativado = false;
}

//BUSCA AS CONFIGURAÇÕES DE PAGAMENTO
$busca_pagamento = mysqli_query($conn, "SELECT * FROM pagamento WHERE id = 1");
$pagamento       = mysqli_fetch_array($busca_pagamento);

//BUSCA O SEO DAS PÁGINAS E COLOCA EM UM ARRAY PARA DISTRIBUIR
$busca_seo = mysqli_query($conn, "SELECT titulo, descricao, palavras_chave FROM seo ORDER BY id ASC");

//ESTANCIA O ARRAY
$array_seo = [];

//PREENCHE O ARRAY
while($seo = mysqli_fetch_array($busca_seo)){
    $array_seo[] = array(
        "titulo"         => $seo["titulo"],
        "descricao"      => $seo["descricao"],
        "palavras_chave" => $seo["palavras_chave"]
    );
}

//ESTANCIA A VARIÁVEL SOBRE DO SITE
$sobre_site = $array_seo[0]['descricao'];

?>

<!DOCTYPE html>

<html lang = "pt-br">
    
<head>

    <?php if($loja['site'] == 'https://lojapadrao.conectashop.com/' | $loja['site'] == 'https://catalogo.nostrabr.com/' | $loja['site'] == 'https://windowshow.conectashop.com/'){ ?>
        <meta name="robots" content="noindex">
        <meta name="googlebot" content="noindex">    
    <?php } ?>

    <?php //SE A LOJA ESTIVER EM MANUTENÇÃO, REDIRECIONA PARA A PÁGINA DE MANUTENÇÃO
    if($loja['site_manutencao'] == 1){ echo "<script>window.location.href = '".$loja['site']."manutencao';</script>"; } ?>

    <?php if($loja['facebook_pixel'] != ''){ ?>
        <!-- FACEBOOK PIXEL -->
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '<?= $loja['facebook_pixel'] ?>');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?= $loja['facebook_pixel'] ?>&ev=PageView&noscript=1" /></noscript>
    <?php } ?>

    <?php if($loja['google_analytics'] != ''){ ?>
        <!--GOOGLE ANALYTICS-->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $loja['google_analytics'] ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?= $loja['google_analytics'] ?>');
        </script>
    <?php } ?>
    
    <?php if($loja['google_tag_manager_script'] != ''){ ?>
        <!--GOOGLE TAG MANAGER SCRIPT-->
        <script>
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','<?= $loja['google_tag_manager_script'] ?>');
        </script>
    <?php } ?>
    
    <!--METATAGS-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--METATAGS DE PRODUTO-->
    <?php

    //PEGA O NOME DA PÁGINA ATUAL
    $pagina = basename($_SERVER['SCRIPT_NAME']); 

    //VERIFICA A PÁGINA PARA MOLDAR O SEO
    if($pagina == 'index.php'){ ?>
    
        <title><?= $array_seo[0]['titulo'] ?></title>
        <meta name="description" content="<?= $array_seo[0]['descricao'] ?>">
        <meta name="keywords" content="<?= $array_seo[0]['palavras_chave'] ?>">
        <meta property="og:title" content="<?= $array_seo[0]['titulo']  ?>">
        <meta property="og:description" content="<?= $array_seo[0]['descricao'] ?>">

    <?php } else if($pagina == 'produtos.php'){ ?>
    
        <title><?= $array_seo[1]['titulo'] ?></title>
        <meta name="description" content="<?= $array_seo[1]['descricao'] ?>">
        <meta name="keywords" content="<?= $array_seo[1]['palavras_chave'] ?>">
        <meta property="og:title" content="<?= $array_seo[1]['titulo']  ?>">
        <meta property="og:description" content="<?= $array_seo[1]['descricao'] ?>">

    <?php } else if($pagina == 'contato.php'){ ?>
    
        <title><?= $array_seo[2]['titulo'] ?></title>
        <meta name="description" content="<?= $array_seo[2]['descricao'] ?>">
        <meta name="keywords" content="<?= $array_seo[2]['palavras_chave'] ?>">
        <meta property="og:title" content="<?= $array_seo[2]['titulo']  ?>">
        <meta property="og:description" content="<?= $array_seo[2]['descricao'] ?>">

    <?php } else if($pagina == 'localizacao.php'){ ?>
    
        <title><?= $array_seo[3]['titulo'] ?></title>
        <meta name="description" content="<?= $array_seo[3]['descricao'] ?>">
        <meta name="keywords" content="<?= $array_seo[3]['palavras_chave'] ?>">
        <meta property="og:title" content="<?= $array_seo[3]['titulo']  ?>">
        <meta property="og:description" content="<?= $array_seo[3]['descricao'] ?>">

    <?php } else if($pagina == 'sobre.php'){ ?>
    
        <title><?= $array_seo[4]['titulo'] ?></title>
        <meta name="description" content="<?= $array_seo[4]['descricao'] ?>">
        <meta name="keywords" content="<?= $array_seo[4]['palavras_chave'] ?>">
        <meta property="og:title" content="<?= $array_seo[4]['titulo']  ?>">
        <meta property="og:description" content="<?= $array_seo[4]['descricao'] ?>">

    <?php } else if($pagina == 'produto.php'){

        //RECEBE O ID DO PRODUTO
        $id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);    
        
        //BUSCA OS DADOS DO PRODUTO
        $busca_produto = mysqli_query($conn, "SELECT p.identificador, p.nome, p.id_categoria, p.descricao, p.estoque, p.preco, p.categoria_google, p.sku, p.palavras_chave, p.atributo_primario, p.atributo_secundario, m.nome AS nome_marca FROM produto AS p LEFT JOIN categoria AS c ON p.id_categoria = c.id LEFT JOIN marca AS m ON p.id_marca = m.id WHERE p.id = $id AND status = 1");
        
        //SE O PRODUTO ESTIVER DESATIVADO MANDA PRA INDEX
        if(mysqli_num_rows($busca_produto) === 0){
            echo '<script>window.location.href = "'.$loja['site'].'";</script>';
        } else {
        
            $produto       = mysqli_fetch_array($busca_produto);

            $id_produto_url        = $id;
            $identificador_produto = $produto['identificador'];
            $id_categoria_url      = $produto['id_categoria'];

            //BUSCA AS IMAGENS DO PRODUTO
            $busca_produto_imagens = mysqli_query($conn, "SELECT capa, imagem FROM produto_imagem WHERE id_produto = $id ORDER BY capa DESC, ordem ASC");

            //PADRONIZA O ESTOQUE    
            if($produto['estoque'] > 0){
                $descricao_estoque            = "in stock";
                $descricao_estoque_secundario = "https://schema.org/InStock";
            } else {
                $descricao_estoque            = "out of stock";
                $descricao_estoque_secundario = "https://schema.org/OutOfStock";
            }      

            //VERIFICA O ÚLTIMO CARACTER DAS PALAVRAS CHAVE E SE FOR VIRGULA, RETIRA
            $link_sem_barra = substr($loja['site'],0,-1);
            $link_produto = $link_sem_barra.$_SERVER["REQUEST_URI"];

            ?>

            <title><?= $produto['nome'] ?></title>
            <meta name="description" content="<?= str_replace('<br />','',$produto['descricao']) ?>">
            <meta name="keywords" content="<?= $loja['palavras_chave'].', '.$produto['palavras_chave'] ?>">
            <meta property="og:title" content="<?= $produto['nome'] ?>">
            <meta property="og:description" content="<?= str_replace('<br />','',$produto['descricao']) ?>">
            <meta property="og:url" content="<?= $link_produto ?>">
            <?php while($produto_imagem = mysqli_fetch_array($busca_produto_imagens)){ ?>
                <?php if($produto_imagem['capa'] == 1){ ?>
                    <meta property="og:image" content="<?= $loja['site'] ?>imagens/produtos/grande/<?= $produto_imagem['imagem'] ?>">     
                <?php } else { ?>     
                    <meta property="additional_image_link" content="<?= $loja['site'] ?>imagens/produtos/grande/<?= $produto_imagem['imagem'] ?>"> 
                <?php } ?>                      
            <?php } ?>       
            <meta property="product:brand" content="<?= $produto['nome_marca'] ?>">
            <meta property="product:availability" content="<?= $descricao_estoque ?>">
            <meta property="product:condition" content="new">
            <meta property="product:price:amount" content="<?= $produto['preco'] ?>">
            <meta property="product:category" content="<?= $produto['categoria_google'] ?>">
            <meta property="product:price:currency" content="BRL">
            <meta property="product:retailer_item_id" content="<?= $produto['sku'] ?>">
            <meta property="product:item_group_id" content="<?= $produto['sku'] ?>">  
                        
        <?php } ?>

    <?php } else if($pagina == 'pagina.php'){

        //RECEBE O ID DA PÁGINA CUSTOMIZADA
        $id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_STRING);    

        //BUSCA OS DADOS DA PÁGINA CUSTOMIZADA
        $busca_pagina_customizada = mysqli_query($conn, "SELECT * FROM pagina_customizada WHERE identificador = '$id' AND status = 1");

        //SE A PÁGINA ESTIVER DESATIVADA MANDA PRA INDEX
        if(mysqli_num_rows($busca_pagina_customizada) === 0){       
            echo '<script>window.location.href = "'.$loja['site'].'";</script>';
        } else {

            $pagina_customizada = mysqli_fetch_array($busca_pagina_customizada);

            ?>

            <title><?= $pagina_customizada['titulo'] ?></title>
            <meta name="description" content="<?= str_replace('<br />','',$pagina_customizada['descricao']) ?>">
            <meta name="keywords" content="<?= $loja['palavras_chave'].', '.$pagina_customizada['palavras_chave'] ?>">
            <meta property="og:title" content="<?= $pagina_customizada['titulo'] ?>">
            <meta property="og:description" content="<?= str_replace('<br />','',$pagina_customizada['descricao']) ?>">
            
        <?php } ?>

    <?php } else if($pagina == 'pedido.php'){ ?>

        <meta name="robots" content="noindex">
        <meta name="googlebot" content="noindex">   

        <?php 
            
        //RECEBE O ID DO PEDIDO
        $id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_STRING);    

        //BUSCA OS DADOS DO PEDIDO
        $busca_pedido_pagina = mysqli_query($conn, "SELECT * FROM pedido WHERE codigo = '$id'");

        if(mysqli_num_rows($busca_pedido_pagina) === 0){      
            echo '<script>window.location.href = "'.$loja['site'].'";</script>';
        } else {

            $pedido_pagina = mysqli_fetch_array($busca_pedido_pagina);

            ?>
    
            <title>Pedido <?= $pedido_pagina['codigo'] ?></title>
            <meta name="description" content="Dados do pedido <?= $pedido_pagina['codigo'] ?>">
            <meta name="keywords" content="pedido">
            <meta property="og:title" content="Pedido <?= $pedido_pagina['codigo'] ?>">
            <meta property="og:description" content="Dados do pedido <?= $pedido_pagina['codigo'] ?>">
            
        <?php } ?>

    <?php } else if($pagina == 'carrinho-confirmacao.php'){ ?>
    
        <title>Confirmação de compra - <?= $loja['nome'] ?></title>
        <meta name="description" content="Página de confirmação de compra.">
        <meta name="keywords" content="confirmação, compra, finalizada">
        <meta property="og:title" content="Confirmação de compra - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Página de confirmação de compra.">

    <?php } else if($pagina == 'carrinho-frete.php'){ ?>
    
        <title>Carrinho frete - <?= $loja['nome'] ?></title>
        <meta name="description" content="Página de seleção de endereço para cálculo do frete do carrinho.">
        <meta name="keywords" content="carrinho, endereço, frete, cálculo">
        <meta property="og:title" content="Carrinho frete - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Página de seleção de endereço para cálculo do frete do carrinho.">

    <?php } else if($pagina == 'carrinho-login.php'){ ?>
    
        <title>Carrinho login - <?= $loja['nome'] ?></title>
        <meta name="description" content="Página de login do carrinho.">
        <meta name="keywords" content="carrinho, login">
        <meta property="og:title" content="Carrinho login - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Página de login do carrinho.">

    <?php } else if($pagina == 'carrinho-pagamento.php'){ ?>
    
        <title>Carrinho pagamento - <?= $loja['nome'] ?></title>
        <meta name="description" content="Página de seleção da forma de pagamento do carrinho.">
        <meta name="keywords" content="carrinho, pagamento, boleto, cartão">
        <meta property="og:title" content="Carrinho pagamento - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Página de seleção da forma de pagamento do carrinho.">

    <?php } else if($pagina == 'carrinho.php'){ ?>
    
        <title>Carrinho - <?= $loja['nome'] ?></title>
        <meta name="description" content="Página do carrinho com a lista de produtos para conferência.">
        <meta name="keywords" content="carrinho, lista, produtos">
        <meta property="og:title" content="Carrinho - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Página do carrinho com a lista de produtos a ser adquirida.">

    <?php } else if($pagina == 'cliente-acesso-confirmacao.php'){ ?>
    
        <title>Área do cliente - código de confirmação de acesso - <?= $loja['nome'] ?></title>
        <meta name="description" content="Código de confirmação para alterar dados de acesso da área do cliente.">
        <meta name="keywords" content="código, confirmação, alterar, dados, acesso, area do cliente">
        <meta property="og:title" content="Área do cliente - código de confirmação de acesso - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Código de confirmação para alterar dados de acesso da área do cliente.">

    <?php } else if($pagina == 'cliente-acesso-verificacao.php'){ ?>
        
        <title>Área do cliente - confirmar dados - <?= $loja['nome'] ?></title>
        <meta name="description" content="Página para confirmar alteração dos dados de acesso.">
        <meta name="keywords" content="confirmar, dados, acesso, alteração">
        <meta property="og:title" content="Área do cliente - confirmar dados - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Página para confirmar alteração dos dados de acesso.">

    <?php } else if($pagina == 'cliente-acesso.php'){ ?>
    
        <title>Área do cliente - acesso - <?= $loja['nome'] ?></title>
        <meta name="description" content="Dados de acesso do cliente.">
        <meta name="keywords" content="dados, acesso, cliente">
        <meta property="og:title" content="Área do cliente - acesso - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Dados de acesso do cliente.">

    <?php } else if($pagina == 'cliente-cadastro-confirmacao.php'){ ?>
    
        <title>Cliente - código de confirmação de cadastro - <?= $loja['nome'] ?></title>
        <meta name="description" content="Código de confirmação para alterar os dados do cadastro da área do cliente.">
        <meta name="keywords" content="código, confirmação, alterar, dados, cadastro, área do cliente">
        <meta property="og:title" content="Cliente - código de confirmação de cadastro - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Código de confirmação para alterar os dados do cadastro da área do cliente.">

    <?php } else if($pagina == 'cliente-cadastro.php'){ ?>
    
        <title><?= $array_seo[5]['titulo'] ?></title>
        <meta name="description" content="<?= $array_seo[5]['descricao'] ?>">
        <meta name="keywords" content="<?= $array_seo[5]['palavras_chave'] ?>">
        <meta property="og:title" content="<?= $array_seo[5]['titulo'] ?>">
        <meta property="og:description" content="<?= $array_seo[5]['descricao'] ?>">

    <?php } else if($pagina == 'cliente-dados.php'){ ?>
    
        <title>Área do cliente - dados pessoais - <?= $loja['nome'] ?></title>
        <meta name="description" content="Dados pessoais do cliente.">
        <meta name="keywords" content="dados, pessoais, cliente">
        <meta property="og:title" content="Área do cliente - dados pessoais - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Dados pessoais do cliente.">

    <?php } else if($pagina == 'cliente-enderecos-cadastro.php'){ ?>
    
        <title>Área do cliente - cadastro de endereço - <?= $loja['nome'] ?></title>
        <meta name="description" content="Cadastro de endereço do cliente.">
        <meta name="keywords" content="cadastro, endereço, cliente">
        <meta property="og:title" content="Área do cliente - cadastro de endereço - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Cadastro de endereço do cliente.">

    <?php } else if($pagina == 'cliente-enderecos-edicao.php'){ ?>
    
        <title>Área do cliente - edição de endereço - <?= $loja['nome'] ?></title>
        <meta name="description" content="Edição de endereço do cliente.">
        <meta name="keywords" content="edição, endereço, cliente">
        <meta property="og:title" content=" - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Edição de endereço do cliente.">

    <?php } else if($pagina == 'cliente-enderecos.php'){ ?>
    
        <title>Área do cliente - endereços - <?= $loja['nome'] ?></title>
        <meta name="description" content="Lista de endereços do cliente.">
        <meta name="keywords" content="lista, endereços, cliente">
        <meta property="og:title" content="Área do cliente - edição de endereço - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Lista de endereços do cliente.">

    <?php } else if($pagina == 'cliente-pedido.php'){ ?>
    
        <title>Área do cliente - pedido - <?= $loja['nome'] ?></title>
        <meta name="description" content="Pedido do cliente">
        <meta name="keywords" content="pedido, cliente">
        <meta property="og:title" content="Área do cliente - pedido - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Pedido do cliente">

    <?php } else if($pagina == 'cliente-pedidos.php'){ ?>
    
        <title>Área do cliente - pedidos - <?= $loja['nome'] ?></title>
        <meta name="description" content="Lista de pedidos do cliente">
        <meta name="keywords" content="lista, pedidos, cliente">
        <meta property="og:title" content="Área do cliente - pedidos - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Lista de pedidos do cliente">

    <?php } else if($pagina == 'cliente-orcamento.php'){ ?>
    
        <title>Área do cliente - orçamento - <?= $loja['nome'] ?></title>
        <meta name="description" content="Orçamento do cliente">
        <meta name="keywords" content="orçamento, cliente">
        <meta property="og:title" content="Área do cliente - orçamento - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Orçamento do cliente">

    <?php } else if($pagina == 'cliente-orcamentos.php'){ ?>
    
        <title>Área do cliente - orçamentos - <?= $loja['nome'] ?></title>
        <meta name="description" content="Lista de orçamentos do cliente">
        <meta name="keywords" content="lista, orçamentos, cliente">
        <meta property="og:title" content="Área do cliente - orçamentos - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Lista de orçamentos do cliente">

    <?php } else if($pagina == 'login-alterar-senha.php'){ ?>
    
        <title>Login - alterar senha - <?= $loja['nome'] ?></title>
        <meta name="description" content="Página para alterar a senha do cliente.">
        <meta name="keywords" content="alterar, senha, cliente">
        <meta property="og:title" content="Login - alterar senha - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Página para alterar a senha do cliente.">

    <?php } else if($pagina == 'login-recuperacao-senha-confirmacao.php'){ ?>
    
        <title>Recuperar senha - código de confirmação - <?= $loja['nome'] ?></title>
        <meta name="description" content="Código de confirmação para alterar a senha do acesso à área do cliente.">
        <meta name="keywords" content="recuperar, senha, código, confirmação, alterar">
        <meta property="og:title" content="Recuperar senha - código de confirmação - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Código de confirmação para alterar a senha do acesso à área do cliente.">

    <?php } else if($pagina == 'login-recuperacao-senha.php'){ ?>
    
        <title>Recuperar senha - confirmar e-mail - <?= $loja['nome'] ?></title>
        <meta name="description" content="Confirmação do e-mail para recuperar senha.">
        <meta name="keywords" content="confirmação, e-mail, recuperar, senha">
        <meta property="og:title" content="Recuperar senha - confirmar e-mail - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Confirmação do e-mail para recuperar senha.">

    <?php } else if($pagina == 'login.php'){ ?>
    
        <title>Login - <?= $loja['nome'] ?></title>
        <meta name="description" content="Página de login para acesso à área do cliente.">
        <meta name="keywords" content="login, acesso, área do cliente">
        <meta property="og:title" content="Login - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Página de login para acesso à área do cliente.">

    <?php } else if($pagina == 'politica-comercial.php'){ ?>
    
        <title>Politica comercial - <?= $loja['nome'] ?></title>
        <meta name="description" content="Página da política comercial da loja.">
        <meta name="keywords" content="política comercial, loja">
        <meta property="og:title" content="Politica comercial - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Página da política comercial da loja.">

    <?php } else if($pagina == 'politica-entrega.php'){ ?>
    
        <title>Política de entrega - <?= $loja['nome'] ?></title>
        <meta name="description" content="Página da política de entrega da loja.">
        <meta name="keywords" content="política de entrega, loja">
        <meta property="og:title" content="Política de entrega - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Página da política de entrega da loja.">

    <?php } else if($pagina == 'politica-privacidade-seguranca.php'){ ?>
    
        <title>Política de privacidade e segurança - <?= $loja['nome'] ?></title>
        <meta name="description" content="Página da política de privacidade e segurança da loja.">
        <meta name="keywords" content="política de privacidade e segurança, loja">
        <meta property="og:title" content="Política de privacidade e segurança - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Página da política de privacidade e segurança da loja.">

    <?php } else if($pagina == 'politica-termos-uso.php'){ ?>
    
        <title>Termos de uso - <?= $loja['nome'] ?></title>
        <meta name="description" content="Página dos termos de uso da loja.">
        <meta name="keywords" content="termos de uso, loja">
        <meta property="og:title" content="Termos de uso - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Página dos termos de uso da loja.">

    <?php } else if($pagina == 'politica-troca-devolucao.php'){ ?>
    
        <title>Política de troca e devolução - <?= $loja['nome'] ?></title>
        <meta name="description" content="Página da política de troca e devolução da loja.">
        <meta name="keywords" content="política de troca e devolução, loja">
        <meta property="og:title" content="Política de troca e devolução - <?= $loja['nome'] ?>">
        <meta property="og:description" content="Página da política de troca e devolução da loja.">

    <?php } else { ?>
        
        <title><?= $loja['nome'] ?></title>
        <meta name="description" content="">
        <meta name="keywords" content="<?= $loja['palavras_chave'] ?>">
        <meta property="og:title" content="<?= $loja['nome'] ?>">
        <meta property="og:description" content="">
    
    <?php } ?>

    <!--METATAG DE INCORPORAÇÃO-->
    <meta property="og:image" content="<?= $loja['site'].'imagens/thumb.png' ?>">
    <meta property="og:image:width" content="310">
    <meta property="og:image:height" content="310">
    <meta property="og:url" content="<?= $loja['site'] ?>">
    <meta property="og:site_name" content="<?= $loja['nome'] ?>">
    <meta property="og:type" content="website">
    
    <!--ICONS-->
    <link rel="shortcut icon" href="<?= $loja['site'] ?>imagens/favicon.png" type="image/x-icon">

    <!--FONTS-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/92360c3dda.js" crossorigin="anonymous"></script>
    
    <!--CSS-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= $loja['site'] ?>css/global-site.css">    
    <link rel="stylesheet" href="<?= $loja['site'] ?>css/<?= $loja['custom_css'] ?>">
    
    <!--SCRIPTS-->
    <!--<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/lozad/dist/lozad.min.js"></script>
    <script type="text/javascript"> document.addEventListener('DOMContentLoaded', function(){ lozad().observe(); }); </script>-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js" integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js" integrity="sha512-Rdk63VC+1UYzGSgd3u2iadi0joUrcwX0IWp2rTh6KXFoAmgOjRS99Vynz1lJPT8dLjvo6JZOqpAHJyfCEZ5KoA==" crossorigin="anonymous"></script>
    <script type="text/javascript" src="<?= $loja['site'] ?>modulos/header/js/scripts.js"></script>

</head>

<body id="body-site">   

<!--VARIÁVEIS PARA USO DO JS-->
<input type="hidden" id="site" value="<?= $loja['site'] ?>">
<input type="hidden" id="site-nome" value="<?= $loja['nome'] ?>">
<input type="hidden" id="site-telefone" value="<?= $loja['telefone'] ?>">
<input type="hidden" id="site-whatsapp" value="<?= $loja['whatsapp'] ?>">
<input type="hidden" id="site-endereco-rua" value="<?= $loja['rua'] ?>">
<input type="hidden" id="site-endereco-numero" value="<?= $loja['numero'] ?>">
<input type="hidden" id="site-endereco-cidade" value="<?= $loja['nome_cidade'] ?>">
<input type="hidden" id="site-endereco-uf" value="<?= $loja['sigla_estado'] ?>">
<input type="hidden" id="site-endereco-cep" value="<?= $loja['cep'] ?>">
<input type="hidden" id="site-facebook" value="<?= $loja['facebook'] ?>">
<input type="hidden" id="site-instagram" value="<?= $loja['instagram'] ?>">

<!--JSON GERADO DINAMICAMENTE COM AS INFORMAÇÕES DA LOJA PARA OS MOTORES DE BUSCA-->
<script id="json-informacoes-loja" type="application/ld+json"></script>

<?php 

    //INCLUI O AVISO DE COOKIES
    include_once 'modulos/aviso-cookies/index.php';

    //SE FOR A PÁGINA DE PRODUTO, INSERE METATAGS PARA MOSTRAR O PRODUTO NAS PESQUISAS DO GOOGLE
    if($pagina == 'produto.php'){     
    
        //BUSCA AS IMAGENS DO PRODUTO
        $busca_produto_imagens = mysqli_query($conn, "SELECT capa, imagem FROM produto_imagem WHERE id_produto = $id ORDER BY capa DESC, ordem ASC");
            
    ?>
    <div itemtype="http://schema.org/Product" itemscope>
        <meta itemprop="mpn" content="<?= 'MPN'.$produto['sku'] ?>" />
        <meta itemprop="name" content="<?= $produto['nome'] ?>" />        
        <?php while($produto_imagem = mysqli_fetch_array($busca_produto_imagens)){ ?>
            <link itemprop="image" href="<?= $loja['site'] ?>imagens/produtos/grande/<?= $produto_imagem['imagem'] ?>" />      
        <?php } ?>             
        <meta itemprop="description" content="<?= str_replace('<br />','',$produto['descricao']) ?>" />
        <div itemprop="offers" itemtype="http://schema.org/Offer" itemscope>
            <link itemprop="url" href="<?= $link_produto ?>" />
            <meta itemprop="availability" content="<?= $descricao_estoque_secundario ?>" />
            <meta itemprop="priceCurrency" content="BRL" />
            <meta itemprop="itemCondition" content="new" />
            <meta itemprop="price" content="<?= $produto['preco'] ?>" />
        </div>
        <div itemprop="aggregateRating" itemtype="http://schema.org/AggregateRating" itemscope>
            <meta itemprop="reviewCount" content="5" />
            <meta itemprop="ratingValue" content="5" />
        </div>
        <meta itemprop="sku" content="<?= $produto['sku'] ?>" />
        <div itemprop="brand" itemtype="http://schema.org/Brand" itemscope>
            <meta itemprop="name" content="<?= $produto['nome_marca'] ?>" />
        </div>
    </div>

<?php } ?>

<?php if($loja['google_tag_manager_script'] != ''){ ?>
    <!--GOOGLE TAG MANAGER NO SCRIPT-->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=<?= $loja['google_tag_manager_script'] ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
<?php } ?>

<!-- ABRE A DIV CONTAINER E FECHA NO FOOTER -->
<div class="container">