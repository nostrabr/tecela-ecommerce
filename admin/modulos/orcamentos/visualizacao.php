<!--CSS-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="modulos/orcamentos/css/style.css">
                  
<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//PEGA O IDENTIFICADOR DO ORÇAMENTO NA URL
$identificador_orcamento = FILTER_INPUT(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

//BUSCA OS DADOS DO ORÇAMENTO
$busca_orcamento  = mysqli_query($conn, "
    SELECT o.id, o.id_carrinho, o.id_cliente, o.codigo, o.endereco, o.data_cadastro, o.tipo_frete, o.valor_frete
    FROM orcamento AS o 
    WHERE o.identificador = '$identificador_orcamento'
");
$orcamento = mysqli_fetch_array($busca_orcamento);

//BUSCA OS DADOS DO CLIENTE
$busca_cliente = mysqli_query($conn, "SELECT id FROM cliente WHERE id = ".$orcamento['id_cliente']);

//BUSCA O CARRINHO DO PEDIDO
$carrinho = mysqli_query($conn, "
    SELECT cp.identificador AS carrinho_produto_identificador, cp.id_produto AS produto_id, cp.quantidade AS produto_quantidade, cp.ids_caracteristicas AS produto_caracteristicas, cp.preco AS produto_preco, p.nome AS produto_nome,
    (SELECT pi.imagem FROM produto_imagem AS pi WHERE p.id = pi.id_produto AND pi.capa = 1) AS produto_imagem
    FROM carrinho AS c
    INNER JOIN carrinho_produto AS cp ON c.id = cp.id_carrinho
    INNER JOIN produto AS p ON p.id = cp.id_produto
    WHERE cp.status = 1 AND c.id = '".$orcamento['id_carrinho']."'
");

$n_itens         = mysqli_num_rows($carrinho);
$contador_itens  = 0;

?>

<!--SECTION PEDIDO-->
<section id="pedido">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Orçamento</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'orcamentos.php';">VOLTAR</button>
            </div>
        </div>
    
        <div class="row">

            <div class="col-12 col-xl-8">

                <div id="cliente-pedidos-resumo">
                
                    <div class="cliente-pedidos-resumo-status"> 
                        <div class="cliente-pedidos-resumo-informacao">
                            <span>Codigo:</span>
                            <span class="codigo-pedido"><?= $orcamento['codigo'] ?></span>
                        </div>  
                        <div class="cliente-pedidos-resumo-informacao">
                            <span>Data:</span>
                            <span><?= date('d/m/Y H:i', strtotime($orcamento['data_cadastro'])) ?></span>
                        </div>  
                    </div>
                                        
                    <div class="cliente-pedidos-resumo-valores">                          
                        <div class="cliente-pedidos-resumo-informacao">
                            <span>Tipo frete:</span>
                            <span><?= $orcamento['tipo_frete'] ?></span>
                        </div>      
                    </div>

                    <div class="cliente-pedidos-resumo-valores">   
                        <div class="cliente-pedidos-resumo-informacao">
                            <span>Frete:</span>
                            <span>R$ <?= number_format($orcamento['valor_frete'],2,',','.') ?></span>
                        </div>         
                        <div class="cliente-pedidos-resumo-informacao">
                            <span>Tipo:</span>
                            <span><?= mb_strtoupper($orcamento['tipo_frete']) ?></span>
                        </div>      
                    </div>
                    
                    <div class="cliente-pedidos-resumo-entrega"> 
                        <div class="cliente-pedidos-resumo-informacao">
                            Entrega em: <br /><?= str_replace('%0A','<br/>',$orcamento['endereco']) ?>
                        </div>  
                    </div>

                </div>    

            </div>

            <div class="col-12 col-xl-4 mt-4 mt-xl-0">

                <?php while($produto = mysqli_fetch_array($carrinho)){ $contador_itens++; ?>
                    <?php
                        if($produto['produto_imagem'] == ''){  $produto_imagem = $loja['site'].'imagens/produto_sem_foto.png';
                        } else { $produto_imagem = $loja['site'].'imagens/produtos/media/'.$produto['produto_imagem']; }
                    ?>
                    <div class="row cliente-pedidos-produto">
                        <div class="col-4">
                            <div class="cliente-pedidos-produto-imagem" style="background-image: url('<?= $produto_imagem  ?>')"></div>
                        </div>
                        <div class="col-8">
                            <div class="cliente-pedidos-produto-texto">
                                <ul>
                                    <li class="cliente-pedidos-produto-texto-nome"><?= $produto['produto_nome'] ?></li>
                                    <?php 
                                        $caracteristicas = explode(',',$produto['produto_caracteristicas']);
                                        $n_caracteristicas = count($caracteristicas);
                                        if($n_caracteristicas > 0){
                                            $sql_caracteristicas = '';
                                            for($i = 0; $i < $n_caracteristicas; $i++){
                                                if($i == 0){
                                                    $sql_caracteristicas .= "pc.id = ".$caracteristicas[$i];
                                                } else {
                                                    $sql_caracteristicas .= " OR pc.id = ".$caracteristicas[$i];
                                                }
                                            }
                                            $busca_caracteristicas = mysqli_query($conn, "
                                                SELECT a.nome AS atributo_nome, c.nome AS caracteristica_nome 
                                                FROM produto_caracteristica AS pc
                                                INNER JOIN atributo AS a ON pc.id_atributo = a.id
                                                INNER JOIN caracteristica AS c ON pc.id_caracteristica = c.id
                                                WHERE ".$sql_caracteristicas
                                            );
                                            while($caracteristica = mysqli_fetch_array($busca_caracteristicas)){
                                                ?><li class="cliente-pedidos-produto-texto-caracteristicas text-uppercase"><?= $caracteristica['atributo_nome'].": ".$caracteristica['caracteristica_nome'] ?></li><?php
                                            }
                                        }
                                    ?>
                                    <li class="cliente-pedidos-produto-texto-quantidade">Quantidade: <?= $produto['produto_quantidade'] ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php if($contador_itens != $n_itens){ ?>
                        <div class="row cliente-pedidos-separador"><div class="col-12"><hr></div></div>     
                    <?php } ?>   
                <?php } ?>   

            </div>

        </div>
        

    </div>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="modulos/orcamentos/js/scripts.js"></script>