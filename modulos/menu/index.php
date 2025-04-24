<!--CSS-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.css" integrity="sha512-A81ejcgve91dAWmCGseS60zjrAdohm7PTcAjjiDWtw3Tcj91PNMa1gJ/ImrhG+DbT5V+JQ5r26KT5+kgdVTb5w==" crossorigin="anonymous" />
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/menu/css/jqtree.css">
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/menu/css/style-1.1.css">

<!--MENU-->

<header>

    <div id="fundo-header"></div>
    <div id="fundo-menu" class="d-none d-xl-block"></div>

    <section id="menu" <?php if($pagina == 'index.php'){ ?> class="menu-com-sombra" <?php } ?>>

        <div class="row h-100 d-flex align-items-center">

            <div id="menu-menu" class="col-3 d-block d-xl-none">
                <div id="menu-icone" class="d-block d-xl-none fechado"></div>   
                <div id="menu-icone-hamburguer" class="d-flex d-xl-none">
                    <input id="menu-hamburguer" type="checkbox" title="Abre e fecha menu">
                    <label id="menu-hamburguer-label" for="menu-hamburguer">
                        <span id="hamburguer"><span class="d-none">Abre e fecha menu</span></span>
                    </label>
                </div>
            </div>

            <div id="menu-logo" class="col-6 col-xl-2 text-center text-xl-left">
                <a id="menu-logo-desktop" href="<?= $loja['site'] ?>"><img src="<?= $loja['site'] ?>imagens/logo.png" alt="<?= $loja['nome'] ?>"></a>
            </div>

            <?php if($loja['design_menu_links_pesquisar'] == 1){ ?>     
                <div id="menu-opcoes" class="d-none d-xl-block col text-center">               
                    <form id="form-pesquisar-produto" class="form-pesquisar-produto-sigle d-block" action="<?= $loja['site'].'produtos-pesquisa.php' ?>" onsubmit="document.location = '<?= $loja['site'] ?>pesquisa/'+organizaPesquisa(this.pesquisa.value); return false;" method="GET">
                        <label for="menu-opcoes-input-pesquisar" class="d-none">Pesquisa</label>
                        <input id="menu-opcoes-input-pesquisar" name="pesquisa" type="text" placeholder="Buscar produto..." title="Descreva com palavras chave o produto que procuras" required>
                        <a id="menu-opcoes-btn-pesquisar" href="javascript: $('#form-pesquisar-produto').submit();" title="Pesquisar" class="<?php if($pagina == 'pesquisa.php'){ echo 'menu-ativo'; } ?>">Buscar</a>
                    </form>   
                </div>
            <?php } else { ?>
                <div id="menu-opcoes" class="d-none d-xl-block col">    
                    <ul class="d-inline-flex h-100">
                        <li class="ml-2 mr-2"><a id="menu-opcoes-btn-home" href="<?= $loja['site'] ?>" class="<?php if($pagina == 'index.php'){ echo 'menu-ativo'; } ?>">HOME</a></li>
                        <li class="ml-2 mr-2"><a id="menu-opcoes-btn-contato" href="<?= $loja['site'] ?>contato" class="<?php if($pagina == 'contato.php'){ echo 'menu-ativo'; } ?>">CONTATO</a></li>
                        <?php if($loja['exibir_endereco'] == 1){ ?>
                            <li class="ml-2 mr-2"><a id="menu-opcoes-btn-localizacao" href="<?= $loja['site'] ?>localizacao" class="<?php if($pagina == 'localizacao.php'){ echo 'menu-ativo'; } ?>">LOCALIZAÇÃO</a></li>
                        <?php } ?>
                        <li class="ml-2 mr-2"><a id="menu-opcoes-btn-sobre" href="<?= $loja['site'] ?>sobre" class="<?php if($pagina == 'sobre.php'){ echo 'menu-ativo'; } ?>">SOBRE</a></li>
                        <?php if($loja['opcao_mostrar_avaliacoes'] == 1){ ?>
                            <li class="ml-2 mr-2"><a id="menu-opcoes-btn-avaliacoes" href="<?= $loja['site'] ?>avaliacoes" class="<?php if($pagina == 'avaliacoes.php'){ echo 'menu-ativo'; } ?>">AVALIAÇÕES</a></li>
                        <?php } ?>
                        <?php 
                            $busca_paginas_customizadas_cabecalho = mysqli_query($conn, "SELECT * FROM pagina_customizada WHERE status = 1 AND mostrar_cabecalho = 1");
                            while($pagina_customizada_cabecalho = mysqli_fetch_array($busca_paginas_customizadas_cabecalho)){ ?>
                                <li class="ml-2 mr-2"><a href="<?= $loja['site'] ?>pagina/<?= $pagina_customizada_cabecalho['identificador'] ?>" class="text-uppercase <?php if($_SERVER['QUERY_STRING'] == 'id='.$pagina_customizada_cabecalho['identificador']){ echo 'menu-ativo'; } ?>"><?= $pagina_customizada_cabecalho['titulo'] ?></a></li>
                            <?php }
                        ?>
                        <li class="ml-2 mr-2">
                            <form id="form-pesquisar-produto" action="<?= $loja['site'].'produtos-pesquisa.php' ?>" onsubmit="document.location = '<?= $loja['site'] ?>pesquisa/'+organizaPesquisa(this.pesquisa.value); return false;" method="GET">
                                <label for="menu-opcoes-input-pesquisar" class="d-none">Pesquisa</label>
                                <input id="menu-opcoes-input-pesquisar" name="pesquisa" type="text" placeholder="Buscar produto..." title="Descreva com palavras chave o produto que procuras" required>
                            </form>
                            <input type="hidden" id="status-form-pesquisa-produto" value="fechado">
                            <a id="menu-opcoes-btn-pesquisar" href="javascript: abreFechaformPesquisa();" title="Pesquisar" class="<?php if($pagina == 'pesquisa.php'){ echo 'menu-ativo'; } ?>"><img id="menu-opcoes-img-pesquisar" src="<?= $loja['site'] ?>imagens/search.png" alt="Pesquisar"></a>
                        </li>
                    </ul>
                </div>
            <?php } ?>

            <div id="menu-carrinho" class="col-3 col-xl-3 d-flex align-items-center justify-content-end h-100">
                <ul id="menu-carrinho-lista" class="d-inline-flex h-100 align-items-center">  
                    <div class="d-none d-xl-block">
                        <?php 
                            if(isset($_SESSION['nome'])){
                                $nome_cliente = filter_var($_SESSION['nome']);
                                ?>
                                    <li><a id="menu-carrinho-btn-cliente" href="<?= $loja['site'] ?>cliente-dados" class="<?php if($pagina == 'cliente-dados.php' | $pagina == 'cliente-acesso.php' | $pagina == 'cliente-acesso-verificacao.php' | $pagina == 'cliente-acesso-confirmacao.php' | $pagina == 'cliente-enderecos.php' | $pagina == 'cliente-enderecos-cadastro.php' | $pagina == 'cliente-enderecos-edicao.php' | $pagina == 'cliente-pedidos.php' | $pagina == 'cliente-pedido.php'){ echo 'menu-ativo'; } ?> text-capitalize">ÁREA DO CLIENTE</a></li>
                                    <li><a id="menu-carrinho-btn-cliente-sair" href="<?= $loja['site'] ?>logout">Sair</a></li>  
                                <?php
                            } else {
                                ?>
                                    <li><a id="menu-carrinho-btn-login" href="<?= $loja['site'] ?>login" class="<?php if($pagina == 'login.php' | $pagina == 'login-recuperacao-senha.php' | $pagina == 'login-recuperacao-senha-confirmacao.php' | $pagina == 'login-alterar-senha.php'){ echo 'menu-ativo'; } ?>">LOGIN</a></li>  
                                    <li><a id="menu-carrinho-btn-cliente-novo" href="<?= $loja['site'] ?>cliente-cadastro" class="<?php if($pagina == 'cliente-cadastro.php'){ echo 'menu-ativo'; } ?>">Novo</a></li>  
                                <?php
                            }                    
                        ?>                    
                    </div>
                    <div class="d-block d-xl-none">
                        <?php 
                            if(isset($_SESSION['nome'])){
                                $nome_cliente = filter_var($_SESSION['nome']);
                                ?><li><a id="menu-carrinho-btn-cliente" href="<?= $loja['site'] ?>cliente-dados"><img id="menu-carrinho-img-usuario" src="<?= $loja['site'] ?>imagens/usuario.png" alt="Usuário"></a></li><?php
                            } else {
                                ?><li><a id="menu-carrinho-btn-cliente-novo" href="<?= $loja['site'] ?>login"><img id="menu-carrinho-img-usuario" src="<?= $loja['site'] ?>imagens/usuario.png" alt="Usuário"></a></li><?php
                            }                    
                        ?>                    
                    </div>
                    <li id="menu-carrinho-separador" class="ml-4 d-none d-xl-block"><hr></li>
                    <li>
                        <?php
                                
                            $total_produtos_carrinho = 0;
                            $valor_total_carrinho    = '0.00';
                            $session_visitante       = filter_var($_SESSION['visitante']);

                            //BUSCA OS  VALORES DO CARRINHO
                            $busca_carrinho = mysqli_query($conn, "SELECT id FROM carrinho WHERE identificador = '".$session_visitante."'");
                            if(mysqli_num_rows($busca_carrinho) > 0){
                                $carrinho                   = mysqli_fetch_array($busca_carrinho);
                                $itens_carrinho = mysqli_query($conn, "SELECT quantidade, preco FROM carrinho_produto WHERE status = 1 AND id_carrinho = ".$carrinho['id']);
                                while($item_carrinho = mysqli_fetch_array($itens_carrinho)){
                                    $total_produtos_carrinho += $item_carrinho['quantidade'];
                                    $valor_total_carrinho    += $item_carrinho['preco']*$item_carrinho['quantidade'];
                                }
                            }
                                
                        ?>    
                        <a id="menu-carrinho-btn-carrinho" href="<?= $loja['site'] ?>carrinho" class="<?php if($modo_whatsapp){ echo "d-block "; } ?><?php if($pagina == 'carrinho.php' | $pagina == 'carrinho-login.php' | $pagina == 'carrinho-frete.php' | $pagina == 'carrinho-pagamento.php' | $pagina == 'carrinho-confirmacao.php'){ echo 'menu-ativo'; } ?>">
                            <?php if($loja['modo_whatsapp'] == 0){ ?>
                                <span class="d-none d-xl-block mr-1">R$</span>
                                <span id="menu-carrinho-valor" class="mr-2 d-none d-xl-block"><?= number_format($valor_total_carrinho, 2, ',', '.') ?></span> 
                            <?php } ?>
                            <img id="menu-carrinho-img-cesta" src="<?= $loja['site'] ?>imagens/shopping-basket.png" alt="Carrinho">      
                        </a>
                        <span id="menu-carrinho-quantidade"><?= $total_produtos_carrinho ?></span>
                    </li>
                </ul>            
            </div>
            
        </div>

    </section>

    <section id="menu-mobile-new">
    
        <div class="container">
            <ul>                
                <?php 
                    if(isset($_SESSION['nome'])){
                        $nome_cliente = filter_var($_SESSION['nome']);
                        ?><li class="mb-2"><a class="menu-mobile-titulo" href="<?= $loja['site'] ?>cliente-dados">MEU CADASTRO</a></li><?php
                        ?><li class="mb-2"><a class="menu-mobile-titulo" href="<?= $loja['site'] ?>cliente-pedidos">MEUS PEDIDOS</a></li><?php
                    } else {
                        ?><li class="mb-2"><a class="menu-mobile-titulo" href="<?= $loja['site'] ?>login">LOGIN</a></li><?php
                        ?><li class="mb-2"><a class="menu-mobile-titulo" href="<?= $loja['site'] ?>cliente-cadastro">CADASTRO</a></li><?php
                    }                    
                ?>   

                <li class="mb-1 mt-5"><a class="menu-mobile-titulo" href="<?= $loja['site'] ?>login">Home</a></li>
                <li class="mb-1 mt-5"><a class="menu-mobile-titulo" href="<?= $loja['site'] ?>login">Quem Somos</a></li>
                <li class="mb-1 mt-5"><a class="menu-mobile-titulo" href="<?= $loja['site'] ?>login">Segmento</a></li>
                <li class="mb-1 mt-5"><a class="menu-mobile-titulo" href="<?= $loja['site'] ?>login">Produtos</a></li>
                <li class="mb-1 mt-5"><a class="menu-mobile-titulo" href="<?= $loja['site'] ?>login">Contato</a></li>
                <li class="mb-1 mt-5"><a class="menu-mobile-titulo" href="<?= $loja['site'] ?>login">Trabalhe Conosco</a></li>
                
            </ul>
           
        </div>

    </section>

    <!--MENU MOBILE - DESATIVADO
    <section id="menu-mobile">
            
        <div id="menu-mobile-opcoes">
            <ul id="menu-mobile-opcoes-lista">
                <li class="ml-2 mr-2"><a id="menu-mobile-opcoes-btn-home" href="<?= $loja['site'] ?>" class="<?php if($pagina == 'index.php'){ echo 'menu-ativo'; } ?>">HOME</a></li>
                <li class="ml-2 mr-2"><a id="menu-mobile-opcoes-btn-contato" href="<?= $loja['site'] ?>contato" class="<?php if($pagina == 'contato.php'){ echo 'menu-ativo'; } ?>">CONTATO</a></li>
                <?php if($loja['exibir_endereco'] == 1){ ?>
                    <li class="ml-2 mr-2"><a id="menu-mobile-opcoes-btn-localizacao" href="<?= $loja['site'] ?>localizacao" class="<?php if($pagina == 'localizacao.php'){ echo 'menu-ativo'; } ?>">LOCALIZAÇÃO</a></li>
                <?php } ?>
                <li class="ml-2 mr-2"><a id="menu-mobile-opcoes-btn-sobre" href="<?= $loja['site'] ?>sobre" class="<?php if($pagina == 'sobre.php'){ echo 'menu-ativo'; } ?>">SOBRE</a></li>
                <?php
                    if(isset($_SESSION['nome'])){
                        $nome_cliente = filter_var($_SESSION['nome']);
                        ?>
                            <li class="ml-2 mr-2"><a id="menu-mobile-opcoes-btn-cliente" href="<?= $loja['site'] ?>cliente-dados" class="<?php if($pagina == 'cliente-dados.php' | $pagina == 'cliente-acesso.php' | $pagina == 'cliente-acesso-verificacao.php' | $pagina == 'cliente-acesso-confirmacao.php' | $pagina == 'cliente-enderecos.php' | $pagina == 'cliente-enderecos-cadastro.php' | $pagina == 'cliente-enderecos-edicao.php' | $pagina == 'cliente-pedidos.php' | $pagina == 'cliente-pedido.php'){ echo 'menu-ativo'; } ?> text-uppercase">ÁREA DO CLIENTE</a></li>
                            <li class="ml-2 mr-2"><a id="menu-mobile-opcoes-btn-logout" href="<?= $loja['site'] ?>logout" class="text-uppercase">SAIR</a></li>
                        <?php
                    } else {
                        ?>
                            <li class="ml-2 mr-2"><a id="menu-mobile-opcoes-btn-login" href="<?= $loja['site'] ?>login" class="<?php if($pagina == 'login.php' | $pagina == 'login-recuperacao-senha.php' | $pagina == 'login-recuperacao-senha-confirmacao.php' | $pagina == 'login-alterar-senha.php'){ echo 'menu-ativo'; } ?>">LOGIN</a></li>
                            <li class="ml-2 mr-2"><a id="menu-mobile-opcoes-btn-novo" href="<?= $loja['site'] ?>cliente-cadastro" class="<?php if($pagina == 'cliente-cadastro.php'){ echo 'menu-ativo'; } ?>">NOVO CADASTRO</a></li>
                        <?php
                    } 
                ?>
            </ul>
        </div>
        
    </section>
    -->  
            
    <section id="menu-mobile-busca" class="d-block d-xl-none">
        <form id="form-mobile-pesquisar-produto" action="<?= $loja['site'].'produtos-pesquisa.php' ?>" onsubmit="document.location = '<?= $loja['site'] ?>pesquisa/'+organizaPesquisa(this.pesquisa.value); return false;" method="GET">
            <label for="menu-mobile-opcoes-input-pesquisar" class="d-none">Pesquisar</label>
            <input id="menu-mobile-opcoes-input-pesquisar" name="pesquisa" type="text" placeholder="Buscar produto..." title="Descreva com palavras chave o produto que procuras" required>
            <a id="menu-mobile-opcoes-btn-pesquisar" title="Pesquisar" onclick="javascript: submitFormPesquisaMobile();" class="<?php if($pagina == 'pesquisa.php'){ echo 'menu-ativo'; } ?>"><img id="menu-mobile-opcoes-img-pesquisar" src="<?= $loja['site'] ?>imagens/search.png" alt="Pesquisar"></a>
        </form>
    </section>

</header>

<!--SCRIPTS-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.js" integrity="sha512-Hyk+1XSRfagqzuSHE8M856g295mX1i5rfSV5yRugcYFlvQiE3BKgg5oFRfX45s7I8qzMYFa8gbFy9xMFbX7Lqw==" crossorigin="anonymous"></script>
<script type="text/javascript" src="<?= $loja['site'] ?>modulos/menu/js/tree.jquery.js"></script>
<script type="text/javascript" src="<?= $loja['site'] ?>modulos/menu/js/scripts-1.1.js"></script>