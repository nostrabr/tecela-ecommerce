<!--CSS-->
<link rel="stylesheet" href="modulos/cliente/css/style.css">

<?php 

//PEGA IDENTIFICADOR DO CLIENTE
$identificador = filter_var($_SESSION['identificador']);

//BUSCA OS DADOS DO CLIENTE
$busca_cliente = mysqli_query($conn, "SELECT id FROM cliente WHERE identificador = '$identificador'");

//VERIFICA SE ENCONTROU O CLIENTE, SENÃO MANDA PRO LOGIN
if(mysqli_num_rows($busca_cliente) > 0){

//FETCH
$cliente = mysqli_fetch_array($busca_cliente);

//BUSCA A LISTA DE PEDIDOS DO CLIENTE
$pedidos = mysqli_query($conn, "
    SELECT p.*, ps.nome AS nome_status, ps.cor AS cor_status
    FROM pedido AS p
    LEFT JOIN pedido_status AS ps ON p.status = ps.id_status
    WHERE p.status != 0 AND p.id_cliente = ".$cliente['id']."
    ORDER BY id DESC
");

?>

<!--CLIENTE PEDIDOS-->
<section id="cliente-pedidos" class="cliente">

    <h1 class="d-none">Dados de pedidos</h1>

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

    <?php if(mysqli_num_rows($pedidos) > 0){ ?>

        <div class="cliente-pedidos-cabecalho d-none d-xl-block">
            <div class="row">       
                <div class="col-12 col-xl-2">Data</div>  
                <div class="col-12 col-xl-5">Código</div>   
                <div class="col-12 col-xl-5 text-right">Status</div>    
            </div>
        </div>

        <?php while($pedido = mysqli_fetch_array($pedidos)){ ?>
            <div class="cliente-pedidos-pedido" title="Ver pedido" onclick="javascript: window.location.href = 'cliente-pedido/<?= $pedido['identificador'] ?>';">
                <div class="row">       
                    <div class="col-12 col-xl-2">
                        <?= date('d/m/Y H:i', strtotime($pedido['data_cadastro'])) ?>
                    </div>  
                    <div class="col-12 col-xl-5 codigo-pedido">
                        <?= $pedido['codigo'] ?>
                    </div>    
                    <div class="col-12 col-xl-5 text-left text-xl-right" style="color: <?= $pedido['cor_status'] ?>;">
                        <?= $pedido['nome_status'] ?>
                    </div>     
                </div>
            </div>
        <?php } ?>

    <?php } else { ?>

        <div class="row">       
            <div class="col-12">   
                Você não fez nenhum pedido ainda. :/ 
            </div>      
            <div class="col-12">   
                <a id="cliente-pedidos-btn-produtos" class="btn-escuro" href="<?= $loja['site'] ?>">Ver produtos</a>
            </div>
        </div>

    <?php } ?>

</section>

<?php } else { ?>

<script> window.location.href = 'login'; </script>

<?php } ?>