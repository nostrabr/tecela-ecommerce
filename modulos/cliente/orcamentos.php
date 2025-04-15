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

//BUSCA A LISTA DE ORÇAMENTOS DO CLIENTE
$orcamentos = mysqli_query($conn, "
    SELECT *
    FROM orcamento
    WHERE id_cliente = ".$cliente['id']."
    ORDER BY id DESC
");

?>

<!--CLIENTE ORÇAMENTOS-->
<section id="cliente-pedidos" class="cliente">

    <h1 class="d-none">Dados de orçamentos</h1>

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

    <?php if(mysqli_num_rows($orcamentos) > 0){ ?>

        <div class="cliente-pedidos-cabecalho d-none d-xl-block">
            <div class="row">       
                <div class="col-12 col-xl-2">Data</div>  
                <div class="col-12 col-xl-5">Código</div>    
            </div>
        </div>

        <?php while($orcamento = mysqli_fetch_array($orcamentos)){ ?>
            <div class="cliente-pedidos-pedido" title="Ver orçamento" onclick="javascript: window.location.href = 'cliente-orcamento/<?= $orcamento['identificador'] ?>';">
                <div class="row">       
                    <div class="col-12 col-xl-2">
                        <?= date('d/m/Y H:i', strtotime($orcamento['data_cadastro'])) ?>
                    </div>  
                    <div class="col-12 col-xl-5 codigo-pedido">
                        <?= $orcamento['codigo'] ?>
                    </div>    
                </div>
            </div>
        <?php } ?>

    <?php } else { ?>

        <div class="row">       
            <div class="col-12">   
                Você não solicitou nenhum orçamento ainda. :/
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