<!--CSS-->
<link rel="stylesheet" href="modulos/configuracoes/css/style.css">

<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
}

?>

<!--SECTION CONFIGURAÇÕES-->
<section id="configuracoes">

    <div class="container-fluid">

        <div class="row">

            <?php if($nivel_usuario == 'M' | $nivel_usuario == 'S'){ ?>
                <div class="col-12 col-md-4 col-xl-3" onclick="javascript: window.location.href = 'configuracoes-loja.php'">                
                    <div class="opcao">
                        <ul>
                            <li><img class="img-fluid" src="<?= $loja['site'] ?>imagens/loja.png" alt="Loja"></li>
                            <li class="nome-configuracao mt-3">Loja</li>
                        </ul>
                    </div>
                </div>
                <div class="col-12 col-md-4 col-xl-3" onclick="javascript: window.location.href = 'configuracoes-paginas-customizadas.php'">                
                    <div class="opcao">
                        <ul>
                            <li><img class="img-fluid" src="<?= $loja['site'] ?>imagens/paginas-customizadas.png" alt="Loja"></li>
                            <li class="nome-configuracao mt-3">Páginas<br>Customizadas</li>
                        </ul>
                    </div>
                </div>
                <div class="col-12 col-md-4 col-xl-3" onclick="javascript: window.location.href = 'configuracoes-design.php'">                
                    <div class="opcao">
                        <ul>
                            <li><img class="img-fluid" src="<?= $loja['site'] ?>imagens/design.png" alt="Design"></li>
                            <li class="nome-configuracao mt-3">Design</li>
                        </ul>
                    </div>
                </div>
                <div class="col-12 col-md-4 col-xl-3" onclick="javascript: window.location.href = 'configuracoes-seo.php'">                
                    <div class="opcao">
                        <ul>
                            <li><img class="img-fluid" src="<?= $loja['site'] ?>imagens/seo.png" alt="SEO"></li>
                            <li class="nome-configuracao mt-3">SEO</li>
                        </ul>
                    </div>
                </div>
                <?php if(!$modo_whatsapp_simples){ ?>
                    <div class="col-12 col-md-4 col-xl-3" onclick="javascript: window.location.href = 'configuracoes-frete.php'">                
                        <div class="opcao">
                            <ul>
                                <li><img class="img-fluid" src="<?= $loja['site'] ?>imagens/frete.png" alt="Frete"></li>
                                <li class="nome-configuracao mt-3">Frete</li>
                            </ul>
                        </div>
                    </div>
                <?php } ?>
                <div class="col-12 col-md-4 col-xl-3" onclick="javascript: window.location.href = 'configuracoes-politicas.php'">                
                    <div class="opcao">
                        <ul>
                            <li><img class="img-fluid" src="<?= $loja['site'] ?>imagens/politicas.png" alt="Politicas"></li>
                            <li class="nome-configuracao mt-3">Políticas</li>
                        </ul>
                    </div>
                </div>
                <?php if(!$modo_whatsapp){ ?>
                    <div class="col-12 col-md-4 col-xl-3" onclick="javascript: window.location.href = 'configuracoes-pagamento.php'">                
                        <div class="opcao">
                            <ul>
                                <li><img class="img-fluid" src="<?= $loja['site'] ?>imagens/pagamento.png" alt="Pagamento"></li>
                                <li class="nome-configuracao mt-3">Pagamento</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 col-xl-3 d-none" onclick="javascript: window.location.href = 'configuracoes-nota-fiscal.php'">                
                        <div class="opcao">
                            <ul>
                                <li><img class="img-fluid" src="<?= $loja['site'] ?>imagens/nota-fiscal.png" alt="Nota Fiscal"></li>
                                <li class="nome-configuracao mt-3">Nota Fiscal</li>
                            </ul>
                        </div>
                    </div>
                <?php } ?>
                <div class="col-12 col-md-4 col-xl-3" onclick="javascript: window.location.href = 'configuracoes-email.php'">                
                    <div class="opcao">
                        <ul>
                            <li><img class="img-fluid" src="<?= $loja['site'] ?>imagens/email.png" alt="E-mail"></li>
                            <li class="nome-configuracao mt-3">Envio de<br>e-mail</li>
                        </ul>
                    </div>
                </div>
                <div class="col-12 col-md-4 col-xl-3" onclick="javascript: window.location.href = 'configuracoes-facebook-pixel.php'">                
                    <div class="opcao">
                        <ul>
                            <li><img class="img-fluid" src="<?= $loja['site'] ?>imagens/facebook-pixel.png" alt="Facebook Pixel"></li>
                            <li class="nome-configuracao mt-3">Facebook Pixel</li>
                        </ul>
                    </div>
                </div>
                <div class="col-12 col-md-4 col-xl-3" onclick="javascript: window.location.href = 'configuracoes-google-tag-manager.php'">                
                    <div class="opcao">
                        <ul>
                            <li><img class="img-fluid" src="<?= $loja['site'] ?>imagens/google-tag-manager.png" alt="Google Tag Manager"></li>
                            <li class="nome-configuracao mt-3">Google Tag Manager</li>
                        </ul>
                    </div>
                </div>
                <div class="col-12 col-md-4 col-xl-3" onclick="javascript: window.location.href = 'configuracoes-google-analytics.php'">                
                    <div class="opcao">
                        <ul>
                            <li><img class="img-fluid" src="<?= $loja['site'] ?>imagens/google-analytics.png" alt="Google Analytics"></li>
                            <li class="nome-configuracao mt-3">Google Analytics</li>
                        </ul>
                    </div>
                </div>
            <?php } else if($nivel_usuario == 'A'){ ?>            
                <div class="col-12 col-md-4 col-xl-3" onclick="javascript: window.location.href = 'configuracoes-design.php'">                
                    <div class="opcao">
                        <ul>
                            <li><img class="img-fluid" src="<?= $loja['site'] ?>imagens/design.png" alt="Design"></li>
                            <li class="nome-configuracao mt-3">Design</li>
                        </ul>
                    </div>
                </div>
                <div class="col-12 col-md-4 col-xl-3" onclick="javascript: window.location.href = 'configuracoes-seo.php'">                
                    <div class="opcao">
                        <ul>
                            <li><img class="img-fluid" src="<?= $loja['site'] ?>imagens/seo.png" alt="SEO"></li>
                            <li class="nome-configuracao mt-3">SEO</li>
                        </ul>
                    </div>
                </div>
            <?php } ?>

            <div class="col-12 col-md-4 col-xl-3 mb-3" onclick="javascript: window.location.href = 'configuracoes-usuarios.php'">                
                <div class="opcao">
                    <ul>
                        <li><img class="img-fluid" src="<?= $loja['site'] ?>imagens/usuarios.png" alt="Usuários"></li>
                        <li class="nome-configuracao mt-3">Usuários</li>
                    </ul>
                </div>
            </div>

        </div>

    </div>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="modulos/configuracoes/js/scripts.js"></script>