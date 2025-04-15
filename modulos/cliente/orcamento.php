<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/cliente/css/style.css">

<?php 

//PEGA IDENTIFICADOR DO CLIENTE
$identificador_cliente   = filter_var($_SESSION['identificador']);
$identificador_orcamento = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

//BUSCA OS DADOS DO CLIENTE
$busca_cliente = mysqli_query($conn, "SELECT id FROM cliente WHERE identificador = '$identificador_cliente'");

//BUSCA OS DADOS DO ORÇAMENTO
$busca_orcamento = mysqli_query($conn, "
    SELECT *
    FROM orcamento
    WHERE identificador = '$identificador_orcamento'
");

//VERIFICA SE ENCONTROU O CLIENTE E O ORÇAMENTO, SENÃO MANDA PRO LOGIN
if(mysqli_num_rows($busca_cliente) > 0 & mysqli_num_rows($busca_orcamento) > 0 ){

//FETCH
$cliente   = mysqli_fetch_array($busca_cliente);
$orcamento = mysqli_fetch_array($busca_orcamento);

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

<!--CLIENTE PEDIDOS-->
<section id="cliente-pedidos" class="cliente"> 

    <h1 class="d-none">Dados de orçamento</h1>

    <!--MENU DE OPÇÕES-->
    <div class="row mb-4">
    
        <div class="col-12">

            <div id="cliente-menu">
            
                <ul>
                    <li id="cliente-menu-btn-cliente-dados" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-dados';">Cadastro</li>
                    <li id="cliente-menu-btn-cliente-acesso" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-acesso';">Acesso</li>
                    <li id="cliente-menu-btn-cliente-enderecos" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-enderecos';">Endereços</li>
                    <?php if(!$modo_whatsapp_simples){ ?>
                        <?php if($modo_whatsapp){ ?>
                            <li id="cliente-menu-btn-cliente-orcamentos" class="menu-cliente-ativo" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-orcamentos';">Orçamentos</li>
                        <?php } else { ?>
                            <li id="cliente-menu-btn-cliente-pedidos" class="menu-cliente-ativo" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-pedidos';">Pedidos</li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            
            </div>

        </div>

    </div>

    <div class="row">
        <div class="col-12 col-xl-8">
            <h2 class="subtitulo-pagina-central-h2">Resumo do orçamento</h2>
            <p class="subtitulo-pagina-central-p">Código: <b class="codigo-pedido"><?= $orcamento['codigo'] ?></b> - Em <?= date('d/m/Y H:i', strtotime($orcamento['data_cadastro'])) ?></p>  
        </div>
        <div class="col-4 d-none d-xl-block">
            <h2 class="subtitulo-pagina-central-h2">Produtos</h2>
            <p class="subtitulo-pagina-central-p"></p>  
        </div>
    </div>    
    
    <div class="row">

        <div class="col-12 col-xl-8">

            <div id="cliente-pedidos-resumo">

                <!--
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
                -->
                  
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
                    if($produto['produto_imagem'] == ''){  $produto_imagem = 'imagens/produto_sem_foto.png';
                    } else { $produto_imagem = 'imagens/produtos/media/'.$produto['produto_imagem']; }
                ?>
                <div class="row cliente-pedidos-produto">
                    <div class="col-4">
                        <div class="cliente-pedidos-produto-imagem" style="background-image: url('<?= $loja['site'].$produto_imagem ?>')"></div>
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

    <div id="cliente-pedidos-botoes" class="row">
        <div class="col-12 mt-4">
            <ul>
                <li><a id="cliente-pedidos-btn-voltar" href="<?= $loja['site'] ?>cliente-pedidos" class="btn-escuro">Voltar</a> </li>
                <li><a id="cliente-pedidos-btn-ajuda" href="<?= $loja['site'] ?>contato" class="btn-escuro">Preciso de ajuda</a></li>
            </ul>    
        </div>
    </div>

</section>

<?php } else { ?>

<script> window.location.href = '<?= $loja['site'] ?>cliente-pedidos'; </script>

<?php } ?>