<?php 

//FUNÇÃO QUE ACERTA O NOME DO PRODUTO OU CATEGORIA PARA URL
function urlProdutoPromocao($nome){    
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
function altProdutoPromocao($nome){    
    return preg_replace("/&([a-z])[a-z]+;/i", "$1", preg_replace('/(\'|")/', "", preg_replace('/( )+/', ' ', str_replace('-',' ',$nome))));
}

//BUSCA OS PRODUTOS
$busca_produtos = mysqli_query($conn,"
    SELECT p.id AS produto_id, p.nome AS produto_nome, p.preco AS produto_preco, p.descricao AS produto_descricao, p.estoque AS produto_estoque, pc.nome AS produto_categoria, 
    (SELECT ppp.porcentagem FROM promocao AS ppp WHERE p.id = ppp.id_produto AND p.promocao = 1 AND ppp.status = 1 ORDER BY ppp.data_cadastro DESC LIMIT 1) AS produto_promocao,
    (SELECT ppc.porcentagem FROM promocao AS ppc WHERE p.id_categoria = ppc.id_categoria AND pc.promocao = 1 AND ppc.status = 1 ORDER BY ppc.data_cadastro DESC LIMIT 1) AS categoria_promocao,
    (SELECT pi.imagem FROM produto_imagem AS pi WHERE pi.id_produto = p.id AND pi.capa = 1 ) AS produto_capa
    FROM produto AS p
    LEFT JOIN categoria AS pc ON pc.id = p.id_categoria
    WHERE status = 1
    ORDER BY field(p.estoque,0), p.relevancia DESC
");

if(mysqli_num_rows($busca_produtos) > 0){

$array_produtos_promocao = array();

while($produto = mysqli_fetch_array($busca_produtos)){

    if($produto['produto_promocao'] != '' | $produto['categoria_promocao'] != ''){
        $array_produtos_promocao[] = array(
            "produto_id"         => $produto['produto_id'],
            "produto_nome"       => $produto['produto_nome'],
            "produto_preco"      => $produto['produto_preco'],
            "produto_descricao"  => $produto['produto_descricao'],
            "produto_estoque"    => $produto['produto_estoque'], 
            "produto_categoria"  => $produto['produto_categoria'],
            "produto_promocao"   => $produto['produto_promocao'],
            "categoria_promocao" => $produto['categoria_promocao'],
            "produto_capa"       => $produto['produto_capa']
        );
    }

}

$total_produtos_promocao = count($array_produtos_promocao);

if($total_produtos_promocao > 4){

?>

<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/produtos-promocao/css/style.css">

<!--PRODUTOS-->
<section id="produtos-promocao">

    <div id="produtos-promocao-produtos">

        <h2 class="d-none">Produtos em promoção</h2>
        
        <div class="titulo-section">
            <span>PROMOÇÃO</span>
            <a class="d-none d-md-block" href="<?= $loja['site'] ?>promocao">Ver todos ></a>
        </div>

        <div class="row">

        <?php 
        
            $contador_produtos = 0;

            if($total_produtos_promocao < 5){
                $produtos_impar = !($total_produtos_promocao % 2) ? "par" : "impar";
            } else {
                $produtos_impar = "par";
            }
            
            for($i = 0; $i < $total_produtos_promocao; $i++){ 

                if($i >= 5){
                    break;
                }
                
                $contador_produtos++;

                if($loja['modo_whatsapp'] == 0){

                    if($array_produtos_promocao[$i]['produto_preco'] != 0){

                        if($array_produtos_promocao[$i]['produto_promocao'] >= $array_produtos_promocao[$i]['categoria_promocao']){
                            $porcentagem_desconto = $array_produtos_promocao[$i]['produto_promocao'];
                        } else {
                            $porcentagem_desconto = $array_produtos_promocao[$i]['categoria_promocao'];
                        }
                        $tem_promocao        = true;
                        $produto_preco       = $array_produtos_promocao[$i]['produto_preco'];
                        $produto_desconto    = $array_produtos_promocao[$i]['produto_preco'] * $porcentagem_desconto / 100;
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

                        if($array_produtos_promocao[$i]['produto_estoque'] <= 0){
                            $em_estoque = false;
                            $produto_preco_final = '<a href="https://wa.me/55'.preg_replace("/[^0-9]/", "", $loja['whatsapp']).'?text='.urlencode("Olá! Vi o produto ".$array_produtos_promocao[$i]['produto_nome']." no site e gostaria de mais informações.").'" target="_blank" class="produto-container-valor-esgotado">WhatsApp</a>';
                        } else {
                            $em_estoque = true;
                        }

                    } else {

                        $em_estoque   = true;
                        $tem_promocao = false;
                        $produto_preco_final = '<a href="https://wa.me/55'.preg_replace("/[^0-9]/", "", $loja['whatsapp']).'?text='.urlencode("Olá! Vi o produto ".$array_produtos_promocao[$i]['produto_nome']." no site e gostaria de mais informações.").'" target="_blank" class="produto-container-valor-esgotado">WhatsApp</a>';
                    
                    }

                } else {

                    if($loja['modo_whatsapp_preco'] == 1){

                        if($array_produtos_promocao[$i]['produto_preco'] != 0){

                            if($array_produtos_promocao[$i]['produto_promocao'] >= $array_produtos_promocao[$i]['categoria_promocao']){
                                $porcentagem_desconto = $array_produtos_promocao[$i]['produto_promocao'];
                            } else {
                                $porcentagem_desconto = $array_produtos_promocao[$i]['categoria_promocao'];
                            }
                            $tem_promocao        = true;
                            $produto_preco       = $array_produtos_promocao[$i]['produto_preco'];
                            $produto_desconto    = $array_produtos_promocao[$i]['produto_preco'] * $porcentagem_desconto / 100;
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
                            $produto_preco_final .= '<br><a href="https://wa.me/55'.preg_replace("/[^0-9]/", "", $loja['whatsapp']).'?text='.urlencode("Olá! Vi o produto ".$array_produtos_promocao[$i]['produto_nome']." no site e gostaria de fazer um pedido.").'" target="_blank" class="produto-container-valor-esgotado">Pedir pelo whats</a>';
                                
                            if($array_produtos_promocao[$i]['produto_estoque'] <= 0){
                                $em_estoque = false;
                                $produto_preco_final = '<a href="https://wa.me/55'.preg_replace("/[^0-9]/", "", $loja['whatsapp']).'?text='.urlencode("Olá! Vi o produto ".$array_produtos_promocao[$i]['produto_nome']." no site e gostaria de mais informações.").'" target="_blank" class="produto-container-valor-esgotado">WhatsApp</a>';
                            } else {
                                $em_estoque = true;
                            }
    
                        } else {
    
                            $tem_promocao = false;
                            if($array_produtos_promocao[$i]['produto_estoque'] <= 0){
                                $em_estoque = false;
                                $produto_preco_final = '<a href="https://wa.me/55'.preg_replace("/[^0-9]/", "", $loja['whatsapp']).'?text='.urlencode("Olá! Vi o produto ".$array_produtos_promocao[$i]['produto_nome']." no site e gostaria de mais informações.").'" target="_blank" class="produto-container-valor-esgotado">WhatsApp</a>';
                            } else {
                                $em_estoque = true;
                                $produto_preco_final = '<a href="https://wa.me/55'.preg_replace("/[^0-9]/", "", $loja['whatsapp']).'?text='.urlencode("Olá! Vi o produto ".$array_produtos_promocao[$i]['produto_nome']." no site e gostaria de fazer um pedido.").'" target="_blank" class="produto-container-valor-esgotado">Pedir pelo whats</a>';
                            }
                            
                        }

                    } else {

                        $em_estoque   = true;
                        $tem_promocao = false;
                        $produto_preco_final = '<a href="https://wa.me/55'.preg_replace("/[^0-9]/", "", $loja['whatsapp']).'?text='.urlencode("Olá! Vi o produto ".$array_produtos_promocao[$i]['produto_nome']." no site e gostaria de mais informações.").'" target="_blank" class="produto-container-valor-esgotado">WhatsApp</a>';
                    
                    }

                }
                
                ?>

                <div class="col-6 col-md-6 col-xl-15">

                    <div class="produto-link" onclick="javascript: window.location.href = '<?= $loja['site'] ?>produto/<?= urlProdutoPromocao($array_produtos_promocao[$i]['produto_categoria']) ?>/<?= urlProdutoPromocao($array_produtos_promocao[$i]['produto_nome']) ?>/<?= $array_produtos_promocao[$i]['produto_id'] ?>'" data-produto-id="<?= $array_produtos_promocao[$i]['produto_id'] ?>" data-produto-lista="Promoção" data-produto-lista-id="produtos-promocao">
                        <div class="produto-container produto-container-<?= $contador_produtos ?><?php if(!$em_estoque){ echo ' produto-container-esgotado'; } else { ?> <?php if($tem_promocao){ echo ' produto-container-promocao'; } } ?>">
                            <ul>
                                <li class="produto-container-imagem">
                                    <img class="lozad" src="<?php if($array_produtos_promocao[$i]['produto_capa'] != ''){ ?><?= $loja['site'] ?>imagens/produtos/media/<?= $array_produtos_promocao[$i]['produto_capa'] ?><?php } else { ?><?= $loja['site'] ?>imagens/produto_sem_foto.png<?php } ?>" data-src="<?php if($array_produtos_promocao[$i]['produto_capa'] != ''){ ?><?= $loja['site'] ?>imagens/fundo-produto.jpg<?php } else { ?><?= $loja['site'] ?>imagens/produto_sem_foto.png<?php } ?>" alt="<?= altProdutoPromocao($array_produtos_promocao[$i]['produto_nome']) ?>" title="<?= $array_produtos_promocao[$i]['produto_nome'] ?>">
                                    <?php if($em_estoque){ ?> 
                                        <?php if($tem_promocao){ ?><span class="produto-container-promocao">#Promoção</span><?php } ?>
                                    <?php } else { ?>
                                        <span class="produto-container-esgotado">#Esgotado</span>
                                    <?php } ?>
                                </li>
                                <li class="produto-container-categoria"><h3><?= $array_produtos_promocao[$i]['produto_categoria'] ?></h3></li>
                                <li class="produto-container-nome"><h4><?= $array_produtos_promocao[$i]['produto_nome'] ?></h4></li>
                                <li class="produto-container-valor"><?= $produto_preco_final ?></li>                                
                                <?php if(base64_encode(base64_decode($array_produtos_promocao[$i]['produto_descricao'], true)) === $array_produtos_promocao[$i]['produto_descricao']){ $produto_descricao = base64_decode($array_produtos_promocao[$i]['produto_descricao']); } else { $produto_descricao = $array_produtos_promocao[$i]['produto_descricao']; } ?>
                                <li class="produto-container-descricao"><?= str_replace('<br />', '', $produto_descricao) ?></li>
                            </ul>                        
                        </div>  
                    </div>                                  

                </div>

            <?php } ?>

        </div>  

        <div class="btn-ver-todos d-block d-md-none">
            <a href="<?= $loja['site'] ?>promocao">
                <ul>
                    <li>Ver todos</li>
                    <li><i class="fas fa-chevron-right"></i></li>
                </ul>                
            </a>
        </div>

    </div>

</section>

<?php } } ?>