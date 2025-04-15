<!--CSS-->
<link rel="stylesheet" href="modulos/menu/css/style.css">

<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario = filter_var($_SESSION['nivel']);

?>

<!--SECTION MENU CABEÇALHO-->
<section id="menu-cabecalho">

    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-8">
                <a href="<?= $loja['site'] ?>" target="_blank"><img src="<?= $loja['site'] ?>imagens/logo-admin.png" alt="<?= $loja['nome'] ?>"></a>
            </div>
            <div class="col-4 text-right">
                <div id="menu-icone" class="d-block fechado"></div>   
                <div id="menu-icone-hamburguer" class="d-flex">
                    <input id="menu-hamburguer" type="checkbox">
                    <label id="menu-hamburguer-label" for="menu-hamburguer">
                        <span id="hamburguer"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>

</section>

<!--SECTION MENU-->
<section id="menu">

    <div class="menu-opcoes">
        <ul>                
            <?php if($nivel_usuario == 'U'){ ?>
                <li onclick="javascript: window.location.href = 'dashboard.php';" class="menu-opcao" data-active="dashboard">Dashboard</li>
                <?php if($modo_whatsapp){ ?>
                    <?php if(!$modo_whatsapp_simples){ ?>
                        <li onclick="javascript: window.location.href = 'orcamentos.php';" class="menu-opcao" data-active="orcamentos">Orçamentos</li>
                    <?php } ?>
                <?php } else { ?>
                    <li onclick="javascript: window.location.href = 'pedidos.php';" class="menu-opcao" data-active="pedidos">Pedidos</li>
                    <?php if($modo_envios){ ?>
                        <li onclick="javascript: window.location.href = 'envios.php';" class="menu-opcao" data-active="envios">Envios</li>
                    <?php } ?>
                <?php } ?>
                <li onclick="javascript: window.location.href = 'produtos.php';" class="menu-opcao" data-active="produtos">Produtos</li>
                <li onclick="javascript: window.location.href = 'logout.php';" class="menu-opcao opcao-logout">SAIR</li>
            <?php } else if($nivel_usuario == 'A' | $nivel_usuario == 'M' | $nivel_usuario == 'S'){ ?>
                <li onclick="javascript: window.location.href = 'dashboard.php';" class="menu-opcao" data-active="dashboard">Dashboard</li>
                <?php if($modo_whatsapp){ ?>
                    <?php if(!$modo_whatsapp_simples){ ?>
                        <li onclick="javascript: window.location.href = 'orcamentos.php';" class="menu-opcao" data-active="orcamentos">Orçamentos</li>
                    <?php } ?>
                <?php } else { ?>
                        <li onclick="javascript: window.location.href = 'pedidos.php';" class="menu-opcao" data-active="pedidos">Pedidos</li>
                    <?php if($modo_envios){ ?>
                        <li onclick="javascript: window.location.href = 'envios.php';" class="menu-opcao" data-active="envios">Envios</li>
                    <?php } ?>
                <?php } ?>
                <li onclick="javascript: window.location.href = 'clientes.php';" class="menu-opcao" data-active="clientes">Clientes</li>
                <li onclick="javascript: window.location.href = 'produtos.php';" class="menu-opcao" data-active="produtos">Produtos</li>
                <li onclick="javascript: window.location.href = 'atributos.php';" class="menu-opcao" data-active="atributos">Atributos</li>
                <li onclick="javascript: window.location.href = 'marcas.php';" class="menu-opcao" data-active="marcas">Marcas</li>
                <li onclick="javascript: window.location.href = 'categorias.php';" class="menu-opcao" data-active="categorias">Categorias e Tags</li>                
                <?php if(!$modo_whatsapp){ ?>
                    <li onclick="javascript: window.location.href = 'cupons.php';" class="menu-opcao" data-active="cupons">Cupons</li>
                <?php } ?>
                <?php if(!$modo_whatsapp){ ?>
                    <li onclick="javascript: window.location.href = 'avaliacoes.php';" class="menu-opcao" data-active="avaliacoes">Avaliações</li>
                <?php } ?>
                <li onclick="javascript: window.location.href = 'configuracoes.php';" class="menu-opcao" data-active="configuracoes">Configurações</li>
                <li onclick="javascript: window.location.href = 'logout.php';" class="menu-opcao opcao-logout">SAIR</li>
            <?php } ?>
        </ul>
    </div>

</section>

<!--SECTION MENU-->
<section id="menu-rodape">
    <ul>
        <li><?= filter_var($_SESSION['nome']) ?></li>
        <li>PAINEL ADMINISTRATIVO</li>
    </ul>        
</section>

<!--SCRIPTS-->
<script type="text/javascript" src="modulos/menu/js/scripts.js"></script>