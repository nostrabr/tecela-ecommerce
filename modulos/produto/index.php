<?php 

//FUNÇÃO QUE ACERTA O ATRIBUTO ALT PARA A IMAGEM DO PRODUTO
function altProduto($nome){    
    return preg_replace("/&([a-z])[a-z]+;/i", "$1", preg_replace('/(\'|")/', "", preg_replace('/( )+/', ' ', str_replace('-',' ',$nome))));
}

//FUNÇÃO QUE ACERTA O NOME DA CATEGORIA PARA URL
function urlCategoriaProduto($nome){    
    $caracteres_proibidos_url = array('(',')','.',',');
    $caracteres_por_espaco    = array(' - ');
    $caracteres_por_hifen     = array(' ','/','#39;','#34;');
    return mb_strtolower(str_replace($caracteres_proibidos_url,'', str_replace($caracteres_por_hifen,'-', str_replace($caracteres_por_espaco,' ', preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim(preg_replace('/(\'|")/', "-", $nome))))))));
}


?>

<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/produto/css/style.css">

<!--PRODUTO-->
<section id="produto">

    <input id="produto-id" type="hidden" value="<?= $id_produto_url ?>">
    <input id="busca-automatica-cep" type="hidden" value="<?= $loja['opcao_cep_automatico'] ?>">

    <!-- CATEGORIAS LIGADAS AO PRODUTO -->
    <div id="produto-breadcrumbs">
    
        <?php

            //BUSCA AS CATEGORIAS PAI DO PRODUTO E CRIA OS BREADCRUMBS
            $busca_nivel_categorias = mysqli_query($conn, "SELECT id, nivel, pai, nome FROM categoria WHERE id = $id_categoria_url");
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
                echo '<a href="'.$loja['site'].'categoria/'.urlCategoriaProduto($array_categorias[$c]['nome']).'/'.$array_categorias[$c]['id'].'">'.$array_categorias[$c]['nome'].'</a>'.' / ';
            }

            echo $produto['nome'];

        ?>

    </div>

    <div class="row position-relative">

        <?php
                                
        //BUSCA AS IMAGENS DO PRODUTO
        $busca_imagens_produto = mysqli_query($conn, "SELECT capa, imagem FROM produto_imagem WHERE id_produto = $id_produto_url ORDER BY capa DESC, ordem ASC");

        //ORGANIZA NUM ARRAY
        if (mysqli_num_rows($busca_imagens_produto) > 0) {
            while($produto_imagem = mysqli_fetch_array($busca_imagens_produto)){$array_imagens_produto[] = array("PRODUTO_IMAGEM" => 'imagens/produtos/grande/'.$produto_imagem['imagem'], "PRODUTO_IMAGEM_MEDIA" => 'imagens/produtos/media/'.$produto_imagem['imagem']);}
            $n_imagens           = count($array_imagens_produto);
            $n_imagens_miniatura = count($array_imagens_produto);
            if($n_imagens >= 6){ $n_imagens_miniatura = 6; }            
        } else {
            $n_imagens           = 1;
            $n_imagens_miniatura = 1;
            $array_imagens_produto[] = array("PRODUTO_IMAGEM" => 'imagens/produto_sem_foto.png', "PRODUTO_IMAGEM_MEDIA" => 'imagens/produto_sem_foto.png');
        }

        ?>

        <input id="total_miniaturas" type="hidden" value="<?= $n_imagens ?>">

        <!--IMAGENS EM MINIATURA-->
        <div class="produto col-1 d-none d-xl-block">
            <div id="miniaturas">
                <ul>
                    <?php for($i=0;$i<$n_imagens;$i++){ ?>
                        <li class="miniatura<?php if($i == 0){ echo " miniatura-ativa"; } ?> <?php if($i >= $n_imagens_miniatura){ echo " d-none"; } ?> <?php if($i == 5){ echo " ultima-miniatura"; } ?>" index="<?= $i ?>" url_imagem="<?= $loja['site'].$array_imagens_produto[$i]["PRODUTO_IMAGEM"] ?>" style="background-image: url('<?= $loja['site'] ?><?= $array_imagens_produto[$i]["PRODUTO_IMAGEM_MEDIA"] ?>')">
                            <?php if($i == 5){ echo strval($n_imagens-$n_imagens_miniatura).'+'; } ?>
                        </li>
                    <?php } ?> 
                </ul>
            </div>
        </div>

        <!--CONTAINER DA IMAGEM GRANDE-->
        <div class="produto col-12 col-xl-5 pl-xl-0">
            <?php if($n_imagens > 1){ ?><div class="imagens-seta-esquerda"><img src="<?= $loja['site'] ?>imagens/seta.png" alt="Anterior"></div><?php } ?>
            <div id="galeria">
                <div id="imagem-grande-container">
                    <img id="imagem-grande" class="lozad" src="<?= $loja['site'].$array_imagens_produto[0]["PRODUTO_IMAGEM"] ?>" data-src="<?= $loja['site']."/imagens/fundo-produto.jpg" ?>" alt="<?= altProduto($produto['nome']) ?>" title="<?= $produto['nome'] ?>">
                </div>
            </div>
            <?php if($n_imagens > 1){ ?><div class="imagens-seta-direita"><img src="<?= $loja['site'] ?>imagens/seta.png" alt="Próxima"></div><?php } ?>
        </div>                    

        <div id="produto-zoom-container" class="col-lg-6 col-md-6 col-sm-12 col-12"><div id="produto-zoom"></div></div>
        
        <?php

            //BUSCA PROMOÇÃO E ESTOQUE
            $busca_produto_promocao_estoque = mysqli_query($conn,"
                SELECT p.preco AS produto_preco, p.estoque AS produto_estoque,
                (SELECT ppp.porcentagem FROM promocao AS ppp WHERE p.id = ppp.id_produto AND p.promocao = 1 AND ppp.status = 1 ORDER BY ppp.data_cadastro DESC LIMIT 1) AS produto_promocao,
                (SELECT ppc.porcentagem FROM promocao AS ppc WHERE p.id_categoria = ppc.id_categoria AND pc.promocao = 1 AND ppc.status = 1 ORDER BY ppc.data_cadastro DESC LIMIT 1) AS categoria_promocao
                FROM produto AS p
                LEFT JOIN categoria AS pc ON pc.id = p.id_categoria
                WHERE p.id = '$id_produto_url'
            ");

            $produto_promocao_estoque = mysqli_fetch_array($busca_produto_promocao_estoque);

            //TRATA O PREÇO CASO TENHA PROMOÇÃO NO PRODUTO
            if($produto_promocao_estoque['produto_promocao'] != '' | $produto_promocao_estoque['categoria_promocao'] != ''){
                if($produto_promocao_estoque['produto_promocao'] >= $produto_promocao_estoque['categoria_promocao']){
                    $porcentagem_desconto = $produto_promocao_estoque['produto_promocao'];
                } else {
                    $porcentagem_desconto = $produto_promocao_estoque['categoria_promocao'];
                }
                $tem_promocao        = true;
                $produto_preco       = $produto_promocao_estoque['produto_preco'];
                $produto_desconto    = $produto_promocao_estoque['produto_preco'] * $porcentagem_desconto / 100;
                $produto_preco_venda = $produto_preco - $produto_desconto;
                $produto_preco       = 'R$ '.number_format($produto_preco,2,",",".");
                $produto_preco_venda = 'R$ '.number_format($produto_preco_venda,2,",",".");
                $produto_preco_final = '<span class="produto-container-valor-original">'.$produto_preco.'</span><br><span class="produto-container-valor-final">'.$produto_preco_venda.'</span>';
            } else {
                $tem_promocao        = false;
                $produto_preco_venda = 'R$ '.number_format($produto_promocao_estoque['produto_preco'],2,",",".");
                $produto_preco_final = '<span class="produto-container-valor-final">'.$produto_preco_venda.'</span>';
            }

            //VERIFICA SE O TEM EM ESTOQUE
            if($produto_promocao_estoque['produto_estoque'] <= 0){
                $em_estoque = false;
                $produto_preco_final = 'Esgotado';
            } else {
                $em_estoque = true;
                $produto_estoque = $produto_promocao_estoque['produto_estoque'];
            }

            //VERIFICA SE O PRODUTO ESTÁ COM O VALOR ZERADO E ATRIBUI À VARIÁVEL
            if($produto_promocao_estoque['produto_preco'] == 0){ 
                $produto_zerado = true;
                $produto_preco_final = '';
            } else {
                $produto_zerado = false;
            }
            
        ?>

        <?php if($modo_whatsapp){ ?>

            <!--CONTAINER DE INFORMAÇÔES SOBRE O PRODUTO-->
            <div class="col-12 col-xl-6">

                <ul>
                    <li id="ultima-categoria"><?= $ultima_categoria ?></li>
                    <li id="produto-nome"><h1><?= $produto['nome'] ?></h1></li>
                    <li id="produto-marca" class="mb-2">Marca: <?= $produto['nome_marca'] ?></li>
                    <?php if($produto['sku'] != ''){ ?>
                        <li id="produto-sku" class="mb-2">Sku: <?= $produto['sku'] ?></li>
                    <?php } ?>
                    <?php if($loja['modo_whatsapp_preco'] == 1){ ?>
                        <li id="produto-preco"><?= $produto_preco_final ?></li>
                    <?php } ?>
                    <?php 

                        //BUSCA OS DIFERENTES ATRIBUTOS DO PRODUTO
                        $busca_produto_atributos = mysqli_query($conn,"
                            SELECT pc.id_atributo AS atributo_id, a.nome AS atributo_nome, a.visualizacao AS atributo_visualizacao
                            FROM produto_caracteristica AS pc
                            INNER JOIN atributo AS a ON pc.id_atributo = a.id
                            WHERE id_produto = $id_produto_url AND a.status = 1
                            GROUP BY id_atributo
                        ");
                        $total_atributos = 0;

                        //VERIFICA SE TEM CARACTERISTICA SECUNDÁRIA
                        $busca_total_caracteristicas_secundarias = mysqli_query($conn, "SELECT count(id) AS total FROM produto_variacao WHERE status != 2 AND id_caracteristica_secundaria != '' AND id_produto = ".$id_produto_url);
                        $total_caracteristicas_secundarias       = mysqli_fetch_array($busca_total_caracteristicas_secundarias);
                        $n_caracteristicas_secundarias           = $total_caracteristicas_secundarias['total'];

                        if(mysqli_num_rows($busca_produto_atributos) > 0){

                            //CRIA UM ARRAY PARA REORDENAR E COLOCAR O ATRIBUTO PRIMARIO POR PRIMEIRO
                            while($produto_atributo = mysqli_fetch_array($busca_produto_atributos)){
                                $array_atributos[] = array(
                                    'atributo_id'           => $produto_atributo['atributo_id'],
                                    'atributo_nome'         => $produto_atributo['atributo_nome'],
                                    'atributo_visualizacao' => $produto_atributo['atributo_visualizacao']
                                );
                            }
                            $n_atributos = count($array_atributos);
                            
                            //REORDENA CASO PRECISE
                            if($array_atributos[0]['atributo_id'] != $produto['atributo_primario']){
                                
                                $array_atributos_auxiliar[] = array(
                                    'atributo_id'           => $array_atributos[1]['atributo_id'],
                                    'atributo_nome'         => $array_atributos[1]['atributo_nome'],
                                    'atributo_visualizacao' => $array_atributos[1]['atributo_visualizacao']
                                );
                            
                                $array_atributos_auxiliar[] = array(
                                    'atributo_id'           => $array_atributos[0]['atributo_id'],
                                    'atributo_nome'         => $array_atributos[0]['atributo_nome'],
                                    'atributo_visualizacao' => $array_atributos[0]['atributo_visualizacao']
                                );
                                
                                $array_atributos = $array_atributos_auxiliar;

                            }

                            //CRIA AS SESSÕES DE ATRIBUTOS
                            for($i = 0; $i < $n_atributos; $i++){

                                //BUSCA AS CARACTERÍSTICAS DO PRODUTO
                                $busca_produto_caracteristicas = mysqli_query($conn,"
                                    SELECT pc.id AS produto_caracteristica_id, pc.id_caracteristica AS produto_caracteristica_id_caracteristica, c.nome AS caracteristica_nome, c.textura AS caracteristica_textura, c.cor AS caracteristica_cor
                                    FROM produto_caracteristica AS pc
                                    INNER JOIN caracteristica AS c ON pc.id_caracteristica = c.id
                                    WHERE pc.id_produto = $id_produto_url AND pc.id_atributo = ".$array_atributos[$i]['atributo_id']." AND pc.status = 1
                                    ORDER BY c.nome ASC
                                ");  

                                if(mysqli_num_rows($busca_produto_caracteristicas) > 0){
                                    
                                    $total_atributos++;
                                    
                                    //IMPRIME O TÍTULO DO ATRIBUTO
                                    echo'<li class="produto-atributo produto-atributo-'.$total_atributos.'">'.$array_atributos[$i]['atributo_nome'].'</li>';

                                    ?><li class="produto-caracteristicas produto-atributo-<?= $total_atributos ?>"><?php

                                    if($loja['design_variacao_extenso'] == 0){
                                        if($array_atributos[$i]['atributo_visualizacao'] == 'T'){ echo '<ul class="produto-caracteristicas-texturas d-flex">'; }
                                        if($array_atributos[$i]['atributo_visualizacao'] == 'C'){ echo '<ul class="produto-caracteristicas-cores d-flex">'; }
                                        if($array_atributos[$i]['atributo_visualizacao'] == 'S'){ echo '<ul class="produto-caracteristicas-caixas-selecao d-flex">'; }
                                    } else {
                                        echo '<ul class="produto-caracteristicas-caixas-selecao d-flex">';
                                    }

                                        //LISTA AS CARACTERISTICAS CADASTRADAS DE CADA ATRIBUTO
                                        while($produto_caracteristica = mysqli_fetch_array($busca_produto_caracteristicas)){

                                            if($n_caracteristicas_secundarias == 0){
                                                
                                                $busca_variacao_primaria   = mysqli_query($conn, "SELECT estoque, status, ordem FROM produto_variacao WHERE status != 2 AND id_produto = $id_produto_url AND id_caracteristica_primaria = ".$produto_caracteristica['produto_caracteristica_id_caracteristica']);
                                                $variacao_primaria         = mysqli_fetch_array($busca_variacao_primaria);

                                                if($variacao_primaria['estoque'] == 0){
                                                    $variacao_primaria_estoque = 'caracteristica-desativada';
                                                } else {
                                                    $variacao_primaria_estoque = '';
                                                }
                                                if($variacao_primaria['status'] == 0){
                                                    $variacao_primaria_status = 'caracteristica-desativada';
                                                } else {
                                                    $variacao_primaria_status = '';
                                                }

                                                $variacao_ordem = $variacao_primaria['ordem'];

                                            } else {
                                                
                                                $busca_ordem_variacao   = mysqli_query($conn, "SELECT ordem FROM produto_variacao WHERE status != 2 AND id_produto = $id_produto_url AND id_caracteristica_primaria = ".$produto_caracteristica['produto_caracteristica_id_caracteristica']." ORDER BY ordem ASC");
                                                $ordem_variacao         = mysqli_fetch_array($busca_ordem_variacao);

                                                $variacao_ordem = $ordem_variacao['ordem'];

                                            }
                                            
                                            if($loja['design_variacao_extenso'] == 0){

                                                //SE FOR DO TIPO TEXTURA
                                                if($array_atributos[$i]['atributo_visualizacao'] == 'T'){
                                                    ?><li class="caracteristica produto-caracteristica-textura caracteristica-<?= $total_atributos ?> <?= $variacao_primaria_estoque ?> <?= $variacao_primaria_status ?>" id-caracteristica="<?= $produto_caracteristica['produto_caracteristica_id_caracteristica'] ?>" value="<?= $produto_caracteristica['produto_caracteristica_id'] ?>" numero-atributo="<?= $total_atributos ?>" style="background-image: url('<?= $loja['site'] ?>imagens/texturas/<?= $produto_caracteristica['caracteristica_textura'] ?>'); order: <?= $variacao_ordem ?>;" title="<?= $produto_caracteristica['caracteristica_nome'] ?>"></li><?php                           

                                                //SE FOR DO TIPO COR
                                                } else if($array_atributos[$i]['atributo_visualizacao'] == 'C'){ 
                                                    ?><li class="caracteristica produto-caracteristica-cor caracteristica-<?= $total_atributos ?> <?= $variacao_primaria_estoque ?> <?= $variacao_primaria_status ?>" id-caracteristica="<?= $produto_caracteristica['produto_caracteristica_id_caracteristica'] ?>" value="<?= $produto_caracteristica['produto_caracteristica_id'] ?>" numero-atributo="<?= $total_atributos ?>" style="background-color: <?= $produto_caracteristica['caracteristica_cor'] ?>; order: <?= $variacao_ordem ?>;" title="<?= $produto_caracteristica['caracteristica_nome'] ?>"></li><?php  

                                                //SE FOR DO TIPO CAIXA DE SELEÇÃO
                                                } else if($array_atributos[$i]['atributo_visualizacao'] == 'S'){
                                                    ?><li class="caracteristica produto-caracteristica-caixa-selecao caracteristica-<?= $total_atributos ?> <?= $variacao_primaria_estoque ?> <?= $variacao_primaria_status ?>" id-caracteristica="<?= $produto_caracteristica['produto_caracteristica_id_caracteristica'] ?>" value="<?= $produto_caracteristica['produto_caracteristica_id'] ?>" numero-atributo="<?= $total_atributos ?>" style="order: <?= $variacao_ordem ?>;" title="<?= $produto_caracteristica['caracteristica_nome'] ?>"><?= $produto_caracteristica['caracteristica_nome'] ?></li><?php  

                                                }

                                            } else {
                                                ?><li class="caracteristica produto-caracteristica-caixa-selecao caracteristica-<?= $total_atributos ?> <?= $variacao_primaria_estoque ?> <?= $variacao_primaria_status ?>" id-caracteristica="<?= $produto_caracteristica['produto_caracteristica_id_caracteristica'] ?>" value="<?= $produto_caracteristica['produto_caracteristica_id'] ?>" numero-atributo="<?= $total_atributos ?>" style="order: <?= $variacao_ordem ?>;"  title="<?= $produto_caracteristica['caracteristica_nome'] ?>"><?= $produto_caracteristica['caracteristica_nome'] ?></li><?php  
                                            }

                                        }
                                        
                                    ?></ul><?php 
                                    
                                    ?></li><?php

                                }
                                
                            }

                        }
                        
                    ?>

                    <?php if($loja['modo_whatsapp_preco'] == 1){ ?>
                        <?php if($em_estoque & !$produto_zerado){ ?>
                            <li id="produto-quantidade">
                                <ul>
                                    <label for="produto-quantidade-input" class="produto-atributo">QUANTIDADE</label>
                                    <li><input id="produto-quantidade-input" class="form-control" type="number" value="1" min="1" max="<?= $produto_estoque ?>"></li>
                                </ul>
                            </li>
                        <?php } ?>
                        <?php if($em_estoque){ ?>
                            <li id="produto-consultar-whatsapp"><a id="produto-btn-consultar-whatsapp" class="btn-escuro" href="https://wa.me/55<?= preg_replace("/[^0-9]/", "", $loja['whatsapp']) ?>?text=<?= urlencode("Olá! Vi o produto ".$produto['nome']." no site e gostaria de fazer um pedido.") ?>" target="_blank">Pedir pelo Whatsapp</a></li>
                        <?php } ?>
                        <?php if($em_estoque & !$produto_zerado){ ?>
                            <li id="produto-adicionar-carrinho"><a id="produto-btn-adicionar-carrinho" class="btn-claro" href="javascript: adicionarCarrinho('adicionar');">Adicionar ao pedido</a></li>    
                        <?php } ?>
                    <?php } else { ?>
                        <li id="produto-quantidade">
                            <ul>
                                <label for="produto-quantidade-input" class="produto-atributo">QUANTIDADE</label>
                                <li><input id="produto-quantidade-input" class="form-control" type="number" value="1" min="1" max="<?= $produto_estoque ?>"></li>
                            </ul>
                        </li>
                        <li id="produto-consultar-whatsapp"><a id="produto-btn-consultar-whatsapp" class="btn-escuro" href="https://wa.me/55<?= preg_replace("/[^0-9]/", "", $loja['whatsapp']) ?>?text=<?= urlencode("Olá! Vi o produto ".$produto['nome']." no site e gostaria de mais informações.") ?>" target="_blank">Cotar pelo Whatsapp</a></li>
                        <li id="produto-adicionar-carrinho"><a id="produto-btn-adicionar-carrinho" class="btn-claro" href="javascript: adicionarCarrinho('adicionar');">Adicionar ao orçamento</a></li>    
                    <?php } ?>
                    
                    <?php $tags = mysqli_query($conn, "SELECT t.nome, t.id FROM tag AS t INNER JOIN produto_tag AS pt ON t.id = pt.id_tag WHERE pt.id_produto = '$id_produto_url'"); ?>
                    <?php if(mysqli_num_rows($tags) > 0){ ?>
                    <li id="produto-tags">
                        <ul>
                            <li><h2><b>Tags:</b></h2></li>
                            <li>
                                <?php while($tag = mysqli_fetch_array($tags)){ ?>
                                    <a href="<?= $loja['site'] ?>categoria/todas/0/1/3/9999999/T/T/T/relevancia/<?= $tag['id'] ?>" class="produto-tag"><?= $tag['nome'] ?></a>
                                <?php } ?>
                            </li>
                        </ul>
                    </li>
                    <?php } ?>

                    <li id="produto-descricao">
                        <ul>
                            <li><h2><b>Descrição:</b></h2></li>
                            <?php if(base64_encode(base64_decode($produto['descricao'], true)) === $produto['descricao']){ $produto_descricao = base64_decode($produto['descricao']); } else { $produto_descricao = $produto['descricao']; } ?>
                            <li><p><?= $produto_descricao ?></p></li>
                        </ul>
                    </li>
                    <input id="total-atributos" type="hidden" value="<?= $total_atributos ?>">
                    <input id="identificador" type="hidden" value="<?= $identificador_produto ?>">
                </ul>

            </div>

        
        <?php } else { ?>

            <!--CONTAINER DE INFORMAÇÔES SOBRE O PRODUTO-->
            <div class="col-12 col-xl-6">

                <ul>
                    <li id="ultima-categoria"><?= $ultima_categoria ?></li>
                    <li id="produto-nome"><h1><?= $produto['nome'] ?></h1></li>
                    <li id="produto-marca">Marca: <?= $produto['nome_marca'] ?></li>
                    <?php if($produto['sku'] != ''){ ?>
                        <li id="produto-sku">Sku: <?= $produto['sku'] ?></li>
                    <?php } ?>
                    <li id="produto-preco"><?= $produto_preco_final ?></li>

                    <div class="<?php if(!$em_estoque){ echo 'd-none'; } ?>">

                        <?php 

                            //BUSCA OS DIFERENTES ATRIBUTOS DO PRODUTO
                            $busca_produto_atributos = mysqli_query($conn,"
                                SELECT pc.id_atributo AS atributo_id, a.nome AS atributo_nome, a.visualizacao AS atributo_visualizacao
                                FROM produto_caracteristica AS pc
                                INNER JOIN atributo AS a ON pc.id_atributo = a.id
                                WHERE id_produto = $id_produto_url AND a.status = 1
                                GROUP BY id_atributo
                            ");
                            $total_atributos = 0;

                            //VERIFICA SE TEM CARACTERISTICA SECUNDÁRIA
                            $busca_total_caracteristicas_secundarias = mysqli_query($conn, "SELECT count(id) AS total FROM produto_variacao WHERE status != 2 AND id_caracteristica_secundaria != '' AND id_produto = ".$id_produto_url);
                            $total_caracteristicas_secundarias       = mysqli_fetch_array($busca_total_caracteristicas_secundarias);
                            $n_caracteristicas_secundarias           = $total_caracteristicas_secundarias['total'];

                            if(mysqli_num_rows($busca_produto_atributos) > 0){

                                //CRIA UM ARRAY PARA REORDENAR E COLOCAR O ATRIBUTO PRIMARIO POR PRIMEIRO
                                while($produto_atributo = mysqli_fetch_array($busca_produto_atributos)){
                                    $array_atributos[] = array(
                                        'atributo_id'           => $produto_atributo['atributo_id'],
                                        'atributo_nome'         => $produto_atributo['atributo_nome'],
                                        'atributo_visualizacao' => $produto_atributo['atributo_visualizacao']
                                    );
                                }
                                $n_atributos = count($array_atributos);
                                
                                //REORDENA CASO PRECISE
                                if($array_atributos[0]['atributo_id'] != $produto['atributo_primario']){
                                    
                                    $array_atributos_auxiliar[] = array(
                                        'atributo_id'           => $array_atributos[1]['atributo_id'],
                                        'atributo_nome'         => $array_atributos[1]['atributo_nome'],
                                        'atributo_visualizacao' => $array_atributos[1]['atributo_visualizacao']
                                    );
                                
                                    $array_atributos_auxiliar[] = array(
                                        'atributo_id'           => $array_atributos[0]['atributo_id'],
                                        'atributo_nome'         => $array_atributos[0]['atributo_nome'],
                                        'atributo_visualizacao' => $array_atributos[0]['atributo_visualizacao']
                                    );
                                    
                                    $array_atributos = $array_atributos_auxiliar;

                                }

                                //CRIA AS SESSÕES DE ATRIBUTOS
                                for($i = 0; $i < $n_atributos; $i++){

                                    //BUSCA AS CARACTERÍSTICAS DO PRODUTO
                                    $busca_produto_caracteristicas = mysqli_query($conn,"
                                        SELECT  pc.id AS produto_caracteristica_id, pc.id_caracteristica AS produto_caracteristica_id_caracteristica, c.nome AS caracteristica_nome, c.textura AS caracteristica_textura, c.cor AS caracteristica_cor
                                        FROM produto_caracteristica AS pc
                                        INNER JOIN caracteristica AS c ON pc.id_caracteristica = c.id
                                        WHERE pc.id_produto = $id_produto_url AND pc.id_atributo = ".$array_atributos[$i]['atributo_id']." AND pc.status = 1
                                        ORDER BY c.nome ASC
                                    ");  

                                    if(mysqli_num_rows($busca_produto_caracteristicas) > 0){
                                        
                                        $total_atributos++;
                                        
                                        //IMPRIME O TÍTULO DO ATRIBUTO
                                        echo'<li class="produto-atributo produto-atributo-'.$total_atributos.'">'.$array_atributos[$i]['atributo_nome'].'</li>';

                                        ?><li class="produto-caracteristicas produto-atributo-<?= $total_atributos ?>"><?php

                                        if($loja['design_variacao_extenso'] == 0){
                                            if($array_atributos[$i]['atributo_visualizacao'] == 'T'){ echo '<ul class="produto-caracteristicas-texturas d-flex">'; }
                                            if($array_atributos[$i]['atributo_visualizacao'] == 'C'){ echo '<ul class="produto-caracteristicas-cores d-flex">'; }
                                            if($array_atributos[$i]['atributo_visualizacao'] == 'S'){ echo '<ul class="produto-caracteristicas-caixas-selecao d-flex">'; }
                                        } else {
                                            echo '<ul class="produto-caracteristicas-caixas-selecao d-flex">';
                                        }
                                        
                                        //LISTA AS CARACTERISTICAS CADASTRADAS DE CADA ATRIBUTO
                                        while($produto_caracteristica = mysqli_fetch_array($busca_produto_caracteristicas)){
                                                
                                            if($n_caracteristicas_secundarias == 0){
                                                
                                                $busca_variacao_primaria   = mysqli_query($conn, "SELECT estoque, status, ordem FROM produto_variacao WHERE status != 2 AND id_produto = $id_produto_url AND id_caracteristica_primaria = ".$produto_caracteristica['produto_caracteristica_id_caracteristica']." ORDER BY ordem ASC");
                                                $variacao_primaria         = mysqli_fetch_array($busca_variacao_primaria);

                                                if($variacao_primaria['estoque'] == 0){
                                                    $variacao_primaria_estoque = 'caracteristica-sem-estoque';
                                                } else {
                                                    $variacao_primaria_estoque = '';
                                                }
                                                if($variacao_primaria['status'] == 0){
                                                    $variacao_primaria_status = 'caracteristica-desativada';
                                                } else {
                                                    $variacao_primaria_status = '';
                                                }

                                                $variacao_ordem = $variacao_primaria['ordem'];

                                            } else {
                                                
                                                $busca_ordem_variacao   = mysqli_query($conn, "SELECT estoque, status, ordem FROM produto_variacao WHERE status != 2 AND id_produto = $id_produto_url AND id_caracteristica_primaria = ".$produto_caracteristica['produto_caracteristica_id_caracteristica']." ORDER BY ordem ASC");
                                                
                                                $variacao_primaria_estoque = 'caracteristica-sem-estoque';
                                                $variacao_primaria_status = 'caracteristica-desativada';
                                                
                                                while($ordem_variacao = mysqli_fetch_array($busca_ordem_variacao)){
                                                    if($ordem_variacao['estoque'] > 0){
                                                        $variacao_primaria_estoque = '';
                                                    }
                                                    if($ordem_variacao['status'] == 1){
                                                        $variacao_primaria_status = '';
                                                    }

                                                }
                                                $variacao_ordem = $ordem_variacao['ordem'];

                                            }
                                            
                                            if($loja['design_variacao_extenso'] == 0){

                                                //SE FOR DO TIPO TEXTURA
                                                if($array_atributos[$i]['atributo_visualizacao'] == 'T'){
                                                    ?><li class="caracteristica produto-caracteristica-textura caracteristica-<?= $total_atributos ?> <?= $variacao_primaria_estoque ?> <?= $variacao_primaria_status ?>" id-caracteristica="<?= $produto_caracteristica['produto_caracteristica_id_caracteristica'] ?>" value="<?= $produto_caracteristica['produto_caracteristica_id'] ?>" numero-atributo="<?= $total_atributos ?>" style="background-image: url('<?= $loja['site'] ?>imagens/texturas/<?= $produto_caracteristica['caracteristica_textura'] ?>'); order: <?= $variacao_ordem ?>;" title="<?= $produto_caracteristica['caracteristica_nome'] ?>"></li><?php                           

                                                //SE FOR DO TIPO COR
                                                } else if($array_atributos[$i]['atributo_visualizacao'] == 'C'){ 
                                                    ?><li class="caracteristica produto-caracteristica-cor caracteristica-<?= $total_atributos ?> <?= $variacao_primaria_estoque ?> <?= $variacao_primaria_status ?>" id-caracteristica="<?= $produto_caracteristica['produto_caracteristica_id_caracteristica'] ?>" value="<?= $produto_caracteristica['produto_caracteristica_id'] ?>" numero-atributo="<?= $total_atributos ?>" style="background-color: <?= $produto_caracteristica['caracteristica_cor'] ?>; order: <?= $variacao_ordem ?>;" title="<?= $produto_caracteristica['caracteristica_nome'] ?>"></li><?php  

                                                //SE FOR DO TIPO CAIXA DE SELEÇÃO
                                                } else if($array_atributos[$i]['atributo_visualizacao'] == 'S'){
                                                    ?><li class="caracteristica produto-caracteristica-caixa-selecao caracteristica-<?= $total_atributos ?> <?= $variacao_primaria_estoque ?> <?= $variacao_primaria_status ?>" id-caracteristica="<?= $produto_caracteristica['produto_caracteristica_id_caracteristica'] ?>" value="<?= $produto_caracteristica['produto_caracteristica_id'] ?>" numero-atributo="<?= $total_atributos ?>" style="order: <?= $variacao_ordem ?>;"  title="<?= $produto_caracteristica['caracteristica_nome'] ?>"><?= $produto_caracteristica['caracteristica_nome'] ?></li><?php  

                                                }

                                            } else {
                                                ?><li class="caracteristica produto-caracteristica-caixa-selecao caracteristica-<?= $total_atributos ?> <?= $variacao_primaria_estoque ?> <?= $variacao_primaria_status ?>" id-caracteristica="<?= $produto_caracteristica['produto_caracteristica_id_caracteristica'] ?>" value="<?= $produto_caracteristica['produto_caracteristica_id'] ?>" numero-atributo="<?= $total_atributos ?>" style="order: <?= $variacao_ordem ?>;"  title="<?= $produto_caracteristica['caracteristica_nome'] ?>"><?= $produto_caracteristica['caracteristica_nome'] ?></li><?php  
                                            }

                                        }

                                        ?></ul><?php 
                                        
                                        ?></li><?php

                                    }
                                    
                                }

                            }

                        ?>

                    </div>

                    <li id="produto-quantidade" class="<?php if(!$em_estoque | $produto_zerado){ echo 'd-none'; } ?>">
                        <ul>
                            <label for="produto-quantidade-input" class="produto-atributo">QUANTIDADE</label>
                            <li><input id="produto-quantidade-input" class="form-control" type="number" value="1" min="1" max="<?= $produto_estoque ?>"></li>
                        </ul>
                    </li>
                    
                    <?php if((!$em_estoque & $produto_zerado) | (!$em_estoque & !$produto_zerado)){ ?>
                        <li id="produto-consultar-whatsapp"><a id="produto-btn-consultar-whatsapp" class="btn-escuro" href="https://wa.me/55<?= preg_replace("/[^0-9]/", "", $loja['whatsapp']) ?>?text=<?= urlencode("Olá! Gostaria de ser avisado quando o produto ".$produto['nome']." estiver disponível.") ?>" target="_blank">Avise-se me quando disponível</a></li>
                    <?php } else if($em_estoque & $produto_zerado){ ?>
                        <li id="produto-consultar-whatsapp"><a id="produto-btn-consultar-whatsapp" class="btn-escuro" href="https://wa.me/55<?= preg_replace("/[^0-9]/", "", $loja['whatsapp']) ?>?text=<?= urlencode("Olá! Vi o produto ".$produto['nome']." no site e gostaria de mais informações.") ?>" target="_blank">Cotar pelo Whatsapp</a></li>
                    <?php } ?>

                    <?php if($em_estoque & !$produto_zerado){ ?>
                        <li id="produto-comprar"><a id="produto-btn-comprar" class="btn-escuro" href="javascript: adicionarCarrinho('comprar');">Comprar</a></li>
                        <li id="produto-adicionar-carrinho"><a id="produto-btn-adicionar-carrinho" class="btn-claro" href="javascript: adicionarCarrinho('adicionar');">Adicionar ao carrinho</a></li>    
                        <?php if($frete_ativado){ ?>
                            <li id="container-calculo-frete">
                                <label for="cep" class="d-none">Calcular frete</label>
                                <input type="text" id="cep" placeholder="Calcular frete.." title="Simule o frete para sua região">
                                <a id="btn-buscar-fretes" href="javascript: buscaFrete();">Buscar</a>
                                <small id="nao-sei-cep" class="mb-2"><a href="https://buscacepinter.correios.com.br/app/endereco/index.php?t" target="_blank">Não sei meu CEP</a></small>
                                <div id="produto-resultado-frete"></div>
                            </li>
                        <?php } ?>
                    <?php } ?>

                    <?php $tags = mysqli_query($conn, "SELECT t.nome, t.id FROM tag AS t INNER JOIN produto_tag AS pt ON t.id = pt.id_tag WHERE pt.id_produto = '$id_produto_url'"); ?>
                    <?php if(mysqli_num_rows($tags) > 0){ ?>
                    <li id="produto-tags">
                        <ul>
                            <li><h2><b>Tags:</b></h2></li>
                            <li>
                                <?php while($tag = mysqli_fetch_array($tags)){ ?>
                                    <a href="<?= $loja['site'] ?>categoria/todas/0/1/3/9999999/T/T/T/relevancia/<?= $tag['id'] ?>" class="produto-tag"><?= $tag['nome'] ?></a>
                                <?php } ?>
                            </li>
                        </ul>
                    </li>
                    <?php } ?>

                    <li id="produto-descricao">
                        <ul>
                            <li><h2><b>Descrição:</b></h2></li>
                            <?php if(base64_encode(base64_decode($produto['descricao'], true)) === $produto['descricao']){ $produto_descricao = base64_decode($produto['descricao']); } else { $produto_descricao = $produto['descricao']; } ?>
                            <li><p><?= $produto_descricao ?></p></li>
                        </ul>
                    </li>
                    <input id="total-atributos" type="hidden" value="<?= $total_atributos ?>">
                    <input id="identificador" type="hidden" value="<?= $identificador_produto ?>">
                </ul>

            </div>

        <?php } ?>

    </div>

</section>

<!-- Modal -->
<div class="modal fade" id="modal-imagens-produto" tabindex="-1" role="dialog" aria-labelledby="modal-imagens-produto" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <div id="carouselExampleControls" class="carousel slide" data-interval="false">    
            <ol class="carousel-indicators">        
                <?php for($i=0;$i<$n_imagens;$i++){ ?>  
                    <li data-target="#carouselExampleControls" class="carousel-indicator" data-slide-to="<?= $i ?>" index="<?= $i ?>"></li>
                <?php } ?> 
            </ol>
            <div class="carousel-inner">        
                <?php for($i=0;$i<$n_imagens;$i++){ ?>            
                    <div class="carousel-item" index="<?= $i ?>">
                        <img class="d-block w-100" src="<?= $loja['site'] ?><?= $array_imagens_produto[$i]["PRODUTO_IMAGEM"] ?>" alt="<?= altProduto($produto['nome']) ?>" title="<?= $produto['nome'] ?>">
                    </div>
                <?php } ?> 
            </div>
            <?php if($n_imagens > 1){ ?> 
                <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                    <div id="carousel-control-prev-container">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </div>
                </a>
                <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                    <div id="carousel-control-next-container">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </div>
                </a>
            <?php }?> 
        </div>
      </div>
    </div>
  </div>
</div>  

<!--SCRIPTS-->
<script type="text/javascript" src="<?= $loja['site'] ?>modulos/frete/js/scripts.js"></script>
<script type="text/javascript" src="<?= $loja['site'] ?>modulos/produto/js/scripts-1.2.js"></script>