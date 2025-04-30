<?php 

//FUNÇÃO QUE ACERTA O NOME DO PRODUTO OU CATEGORIA PARA URL
function urlProdutoNovo($nome){
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
function altProdutoNovo($nome){    
    return preg_replace("/&([a-z])[a-z]+;/i", "$1", preg_replace('/(\'|")/', "", preg_replace('/( )+/', ' ', str_replace('-',' ',$nome))));
}

//BUSCA OS ÚLTIMOS 5 PRODUTOS CADASTRADOS NO SISTEMA
$busca_produtos = mysqli_query($conn,"
    SELECT p.id AS produto_id, p.nome AS produto_nome, p.preco AS produto_preco, p.descricao AS produto_descricao, p.estoque AS produto_estoque, pc.nome AS produto_categoria, 
    (SELECT ppp.porcentagem FROM promocao AS ppp WHERE p.id = ppp.id_produto AND p.promocao = 1 AND ppp.status = 1 ORDER BY ppp.data_cadastro DESC LIMIT 1) AS produto_promocao,
    (SELECT ppc.porcentagem FROM promocao AS ppc WHERE p.id_categoria = ppc.id_categoria AND pc.promocao = 1 AND ppc.status = 1 ORDER BY ppc.data_cadastro DESC LIMIT 1) AS categoria_promocao,
    (SELECT pi.imagem FROM produto_imagem AS pi WHERE pi.id_produto = p.id AND pi.capa = 1 ) AS produto_capa
    FROM produto AS p
    LEFT JOIN categoria AS pc ON pc.id = p.id_categoria
    WHERE p.status = 1    
    ORDER BY field(p.estoque,0), p.data_cadastro DESC
    LIMIT 20
");

if(mysqli_num_rows($busca_produtos) > 0){

?>

<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>css/slick.css">
<link rel="stylesheet" href="<?= $loja['site'] ?>css/slick-theme.css">
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/produtos-novos/css/style.css">

<!--PRODUTOS-->
<section id="produtos-novos" class="py-5" style="background-image: url('<?= $loja['site'] ?>imagens/bg-produto-home.png');">

    <div id="produtos-novos-produtos">
    
        <h2 class="d-none">Produtos lançamento</h2>

        <div class="text-center"><img style="width: 50px; margin-bottom: -25px;" src='<?= $loja['site']?>imagens/ico-lancamento.png'></div>
        <div class="titulo-section mt-0 text-white">LANÇAMENTOS</div>

        <div class="row slider slider-produtos-novos">

            <?php 
            
            while($produto = mysqli_fetch_array($busca_produtos)){ 
                
                if($loja['modo_whatsapp'] == 0){

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
                        }

                        if($produto['produto_estoque'] <= 0){
                            $em_estoque = false;
                            $produto_preco_final = '<a href="https://wa.me/55'.preg_replace("/[^0-9]/", "", $loja['whatsapp']).'?text='.urlencode("Olá! Vi o produto ".$produto['produto_nome']." no site e gostaria de mais informações.").'" target="_blank" class="produto-container-valor-esgotado">WhatsApp</a>';
                        } else {
                            $em_estoque = true;
                        }

                    } else {

                        $em_estoque   = true;
                        $tem_promocao = false;
                        $produto_preco_final = '<a href="https://wa.me/55'.preg_replace("/[^0-9]/", "", $loja['whatsapp']).'?text='.urlencode("Olá! Vi o produto ".$produto['produto_nome']." no site e gostaria de mais informações.").'" target="_blank" class="produto-container-valor-esgotado">WhatsApp</a>';
                    
                    }

                } else {

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
                <div class="col-12 col-md-6 col-xl-15">

                    <div class="produto-link" onclick="javascript: window.location.href = '<?= $loja['site'] ?>produto/<?= urlProdutoNovo($produto['produto_categoria']) ?>/<?= urlProdutoNovo($produto['produto_nome']) ?>/<?= $produto['produto_id'] ?>'" data-produto-id="<?= $produto['produto_id'] ?>" data-produto-lista="Lançamentos" data-produto-lista-id="produtos-novos">
                        <div class="produto-container <?php if(!$em_estoque){ echo 'produto-container-esgotado'; } else { ?> <?php if($tem_promocao){ echo 'produto-container-promocao'; } } ?>">
                            <ul>
                                <li class="produto-container-imagem">
                                    <img class="lozad" src="<?php if($produto['produto_capa'] != ''){ ?><?= $loja['site'] ?>imagens/produtos/media/<?= $produto['produto_capa'] ?><?php } else { ?><?= $loja['site'] ?>imagens/produto_sem_foto.png<?php } ?>" data-src="<?php if($produto['produto_capa'] != ''){ ?><?= $loja['site'] ?>imagens/fundo-produto.jpg<?php } else { ?><?= $loja['site'] ?>imagens/produto_sem_foto.png<?php } ?>" alt="<?= altProdutoNovo($produto['produto_nome']) ?>" title="<?= $produto['produto_nome'] ?>">
                                    <?php if($em_estoque){ ?> 
                                        <?php if($tem_promocao){ ?><span class="produto-container-promocao">#Promoção</span><?php } ?>
                                    <?php } else { ?>
                                        <span class="produto-container-esgotado">#Esgotado</span>
                                    <?php } ?>
                                </li>
                                <li class="produto-container-categoria"><h3 style="color: #1C4A50; font-weight: bold;">Linha: <span style="color: #DC582A;"><?= $produto['produto_categoria'] ?></span></h3></li>
                                <li class="produto-container-nome"><h4 style="color: #1C4A50;"><?= $produto['produto_nome'] ?></h4></li>
                                <li class="produto-container-valor"><button id="ver-produto">Acessar produto</button></li>
                                <?php if(base64_encode(base64_decode($produto['produto_descricao'], true)) === $produto['produto_descricao']){ $produto_descricao = base64_decode($produto['produto_descricao']); } else { $produto_descricao = $produto['produto_descricao']; } ?>
                                <li class="produto-container-descricao" style="color: #1C4A50;"><?= str_replace('<br />', '', $produto_descricao) ?></li>
                            </ul>      
                        </div>  
                    </div>                          

                </div>
            <?php } ?>

        </div>

    </div>

</section>

<script type="text/javascript" src="<?= $loja['site'] ?>js/slick.min.js"></script>  
<script type="text/javascript" src="modulos/produtos-novos/js/scripts.js"></script>  

<?php } ?>