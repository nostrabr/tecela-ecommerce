<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/footer/css/style.css">

<!--FOOTER-->
<footer>

    <div class="container">

        <div class="row">
            <div id="footer-logo" class="col-12">
                <img id="footer-logo-img" src="<?= $loja['site'] ?>imagens/logo-rodape.png" alt="<?= $loja['nome'] ?>">           
            </div>
        </div>

        <div class="row">

            <div id="footer-sobre-loja" class="col-12 col-xl-3">
                <ul>
                    <li class="footer-titulo">SOBRE NÓS</li>
                    <li class="footer-segundo-li"><a id="footer-btn-text-sobre" href="<?= $loja['site'] ?>sobre" title="Leia mais.."><?= $sobre_site ?></a></li>
                </ul>            
            </div>

            <div id="footer-acesso" class="col-12 col-xl-3">
                <ul>
                    <li class="footer-titulo">ACESSO</li>
                    <li class="footer-segundo-li"><a id="footer-btn-home" href="<?= $loja['site'] ?>">Home</a></li>
                    <li><a id="footer-btn-contato" href="<?= $loja['site'] ?>contato">Contato</a></li>
                    <?php if($loja['exibir_endereco'] == 1){ ?>
                        <li><a id="footer-btn-localizacao" href="<?= $loja['site'] ?>localizacao">Localização</a></li>
                    <?php } ?>
                    <li><a id="footer-btn-sobre" href="<?= $loja['site'] ?>sobre">Sobre</a></li>
                    <?php if($loja['opcao_mostrar_avaliacoes'] == 1){ ?>
                        <li><a id="footer-btn-cadastro" href="<?= $loja['site'] ?>avaliacoes">Avaliações</a></li>
                    <?php } ?>                   
                    <?php 
                        $busca_paginas_customizadas_rodape = mysqli_query($conn, "SELECT * FROM pagina_customizada WHERE status = 1 AND mostrar_rodape = 1");
                        while($pagina_customizada_rodape = mysqli_fetch_array($busca_paginas_customizadas_rodape)){ ?>
                            <li><a href="<?= $loja['site'] ?>pagina/<?= $pagina_customizada_rodape['identificador'] ?>"><?= $pagina_customizada_rodape['titulo'] ?></a></li>
                        <?php }
                    ?>     
                    <?php 
                        if(isset($_SESSION['nome'])){
                            $nome_cliente = filter_var($_SESSION['nome']);
                            ?><li><a id="footer-btn-cadastro" href="<?= $loja['site'] ?>cliente-dados">Meu Cadastro</a></li><?php
                            ?><li><a id="footer-btn-pedidos" href="<?= $loja['site'] ?>cliente-pedidos">Meus Pedidos</a></li><?php
                        } else {
                            ?><li><a id="footer-btn-login" href="<?= $loja['site'] ?>login">LOGIN</a></li><?php
                            ?><li><a id="footer-btn-cadastro" href="<?= $loja['site'] ?>cliente-cadastro">CADASTRO</a></li><?php
                        }                    
                    ?>                 
                </ul>
            </div>

            <?php
            
            $busca_politicas = mysqli_query($conn, "SELECT * FROM politicas"); 
            $politica        = mysqli_fetch_array($busca_politicas);
            
            if($politica['comercial'] != '' | $politica['entrega'] != '' | $politica['troca_devolucao'] != '' | $politica['privacidade_seguranca'] != '' | $politica['termos_uso'] != ''){

                $primeira_linha = true;
            ?>

            <div id="footer-politicas" class="col-12 col-xl-3">
                <ul>
                    <li class="footer-titulo">NOSSAS POLÍTICAS</li>
                    <?php if($politica['comercial'] != ''){ ?><li <?php if($primeira_linha){ $primeira_linha = false; ?>class="footer-segundo-li"<?php } ?>><a id="footer-btn-politica-comercial" href="<?= $loja['site'] ?>politica-comercial">Comercial</a></li><?php } ?>
                    <?php if($politica['entrega'] != ''){ ?><li <?php if($primeira_linha){ $primeira_linha = false; ?>class="footer-segundo-li"<?php } ?>><a id="footer-btn-politica-entrega" href="<?= $loja['site'] ?>politica-entrega">Entrega</a></li><?php } ?>
                    <?php if($politica['troca_devolucao'] != ''){ ?><li <?php if($primeira_linha){ $primeira_linha = false; ?>class="footer-segundo-li"<?php } ?>><a id="footer-btn-politica-troca-devolucao" href="<?= $loja['site'] ?>politica-troca-devolucao">Troca e devolução</a></li><?php } ?>
                    <?php if($politica['privacidade_seguranca'] != ''){ ?><li <?php if($primeira_linha){ $primeira_linha = false; ?>class="footer-segundo-li"<?php } ?>><a id="footer-btn-politica-privacidade-seguranca" href="<?= $loja['site'] ?>politica-privacidade-seguranca">Privacidade e segurança</a></li><?php } ?>
                    <?php if($politica['termos_uso'] != ''){ ?><li <?php if($primeira_linha){ $primeira_linha = false; ?>class="footer-segundo-li"<?php } ?>><a id="footer-btn-politica-termos-uso" href="<?= $loja['site'] ?>politica-termos-uso">Termos de uso</a></li><?php } ?>
                </ul>
            </div>

            <?php } ?>

            <div id="footer-redes-sociais" class="col-12 col-xl-3">
                <ul>
                    <li class="footer-titulo">CONTATO</li>
                    <ul id="footer-redes-sociais-ul">
                        <li class="footer-segundo-li"><a class="mr-1" id="footer-btn-whats" href="https://wa.me/55<?= preg_replace("/[^0-9]/", "", $loja['whatsapp']) ?>?text=Atendimento%20online%20%7C%20Ol%C3%A1%20gostaria%20de%20mais%20informa%C3%A7%C3%B5es..." target="_blank" title="Chamar no Whats"><img src="<?= $loja['site'] ?>imagens/whatsapp-claro.png" alt="WhatsApp"> <?= $loja['whatsapp'] ?></a></li><br>
                        <li class="footer-segundo-li"><a class="mr-1" id="footer-btn-email" href="mailto:<?= $loja['email'] ?>" title="Enviar e-mail"><img src="<?= $loja['site'] ?>imagens/email-claro.png" alt="E-mail"></a></li>
                        <?php if($loja['facebook'] != ''){ ?><li class="footer-segundo-li"><a id="footer-btn-facebook" class="mr-1" href="<?= $loja['facebook'] ?>" target="_blank" title="Visitar Facebook"><img src="<?= $loja['site'] ?>imagens/facebook-claro.png" alt="Facebook"></a></li><?php } ?>
                        <?php if($loja['instagram'] != ''){ ?><li class="footer-segundo-li"><a id="footer-btn-instagram" class="mr-1" href="<?= $loja['instagram'] ?>" target="_blank" title="Visitar Instagram"><img src="<?= $loja['site'] ?>imagens/instagram-claro.png" alt="Instagram"></a></li><?php } ?>
                        <?php if($loja['twiter'] != ''){ ?><li class="footer-segundo-li"><a id="footer-btn-twitter" class="mr-1" href="<?= $loja['twiter'] ?>" target="_blank" title="Visitar Twitter"><img src="<?= $loja['site'] ?>imagens/twitter-claro.png" alt="Twitter"></a></li><?php } ?>
                        <?php if($loja['youtube'] != ''){ ?><li class="footer-segundo-li"><a id="footer-btn-youtube" class="mr-1" href="<?= $loja['youtube'] ?>" target="_blank" title="Visitar YouTube"><img src="<?= $loja['site'] ?>imagens/youtube-claro.png" alt="YouTube"></a></li><?php } ?>
                        <?php if($loja['pinterest'] != ''){ ?><li class="footer-segundo-li"><a id="footer-btn-pinterest" class="mr-1" href="<?= $loja['pinterest'] ?>" target="_blank" title="Visitar Pinterest"><img src="<?= $loja['site'] ?>imagens/pinterest-claro.png" alt="Pinterest"></a></li><?php } ?>
                        <?php if($loja['tiktok'] != ''){ ?><li class="footer-segundo-li"><a id="footer-btn-tiktok" href="<?= $loja['tiktok'] ?>" target="_blank" title="Visitar TikTok"><img src="<?= $loja['site'] ?>imagens/tiktok-claro.png" alt="TikTok"></a></li><?php } ?>
                        <?php if($loja['exibir_endereco'] == 1){
                
                            //GUARDA O ENDEREÇO POR EXTENSO DO LOCAL DA ENTREGA
                            $endereco_extenso = $loja['rua'].', '.$loja['numero'];
                            if($loja['complemento'] != ''){ $endereco_extenso .= ' - '.$loja['complemento']; }
                            $endereco_extenso .= ' - '.$loja['bairro'];
                            $endereco_extenso .= ' - '.$loja['nome_cidade'].'/'.$loja['sigla_estado'];
                            $endereco_extenso .= ' - CEP: '.$loja['cep'];

                        ?>
                        <li id="footer-endereco"><a href="<?= $loja['site'] ?>localizacao"><?= $endereco_extenso ?></a></li>
                        <?php } ?>
                    </ul>
                </ul>
            </div>

        </div>
        
    </div>

</footer>

<div id="rodape-informacoes">

    <!--RODAPÉ-->
    <div id="rodape" class="<?php if($modo_whatsapp){ echo "mb-0 mb-sm-5"; } ?>">
        <div class="container">
            <div class="row">
                <div class="col-12 col-xl-8 order-2 order-xl-1">Copyright © <?= date('Y').' - '.$loja['nome'].' - '.$loja['cpf_cnpj'] ?></div>    
                <div class="col-12 col-xl-4 order-1 order-xl-2 text-left text-xl-right mb-2 mb-xl-0">   
                        <?php 
                            if($loja['opcao_mostrar_avaliacoes'] == 1){?>
                            <?php if($total_media_site > 0){ ?>
                                <div class="text-capitalize avaliacao-loja avaliacao-loja-geral" title="<?= number_format($total_media_site,2,'.','') ?>" onclick="javascript: window.open('<?= $loja['site'] ?>avaliacoes','_blank');">  
                                    <?php 
                                        $media_quebrada = explode('.',number_format($total_media_site,2,'.',''));
                                        $media_quebrada = '0.'.$media_quebrada[1];
                                        $media_quebrada = 1-$media_quebrada;
                                    ?>
                                    <ul>
                                        <li><img style="<?php if($total_media_site > 0 AND $total_media_site <= 1){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_site >= 1){ echo 'img-dourada'; } ?>" estrela="1" id="estrela-1" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="1 estrela"></li>
                                        <li><img style="<?php if($total_media_site > 1 AND $total_media_site <= 2){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_site >= 2){ echo 'img-dourada'; } ?>" estrela="2" id="estrela-2" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="2 estrelas"></li>
                                        <li><img style="<?php if($total_media_site > 2 AND $total_media_site <= 3){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_site >= 3){ echo 'img-dourada'; } ?>" estrela="3" id="estrela-3" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="3 estrelas"></li>
                                        <li><img style="<?php if($total_media_site > 3 AND $total_media_site <= 4){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_site >= 4){ echo 'img-dourada'; } ?>" estrela="4" id="estrela-4" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="4 estrelas"></li>
                                        <li><img style="<?php if($total_media_site > 4 AND $total_media_site <= 5){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_site >= 5){ echo 'img-dourada'; } ?>" estrela="5" id="estrela-5" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="5 estrelas"></li>
                                    </ul>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
            </div>
        </div>
    </div>

    <?php if(!$modo_whatsapp){ ?>

        <!--INFORMAÇÕES IMPORTANTES-->
        <div id="informacoes-importantes">
            <div class="container">
                <div class="row">            
                    <div class="col-12 col-xl-4">
                        <ul id="meios-pagamento">
                            <li><h2>Meios de pagamento</h2></li>
                            <li><img src="<?= $loja['site'] ?>imagens/visa.png" alt="Visa"></li>
                            <li><img src="<?= $loja['site'] ?>imagens/mastercard.png" alt="Mastercard"></li>
                            <li><img src="<?= $loja['site'] ?>imagens/elo.png" alt="Elo"></li>
                            <li><img src="<?= $loja['site'] ?>imagens/amex.png" alt="Amex"></li>
                            <li><img src="<?= $loja['site'] ?>imagens/boleto.png" alt="Boleto"></li>
                        </ul>
                    </div>     
                    <div class="col-12 col-xl-4">
                        <ul id="meios-entrega">
                            <li><h2>Entrega em todo o Brasil</h2></li>
                            <li><img src="<?= $loja['site'] ?>imagens/correios.png" alt="Correios"></li>
                            <li><img src="<?= $loja['site'] ?>imagens/sedex.png" alt="Sedex"></li>
                            <li><img src="<?= $loja['site'] ?>imagens/pac.png" alt="Pac"></li>
                            <li><img src="<?= $loja['site'] ?>imagens/motoboy.png" alt="Motoboy"></li>
                        </ul>
                    </div>     
                    <div class="col-12 col-xl-4">
                        <ul id="meios-seguranca">
                            <li><h2>Tecnologia e segurança</h2></li>
                            <?php if($pagamento['pagseguro_status'] == 1){ ?>
                                <li><img src="<?= $loja['site'] ?>imagens/pagseguro.png" alt="Pagseguro"></li>
                            <?php } else if($pagamento['asaas_status'] == 1){ ?>
                                <li class="d-none"><img src="<?= $loja['site'] ?>imagens/asaas.png" alt="Asaas"></li>
                            <?php } ?>
                            <li><img src="<?= $loja['site'] ?>imagens/ssl.png" alt="SSL"></li>
                            <li><a class="d-flex" href="https://transparencyreport.google.com/safe-browsing/search?url=<?= $loja['site'] ?>&hl=pt-PT" target="_blank" alt="Google Safe Browsing"><img src="<?= $loja['site'] ?>imagens/google-safe-browsing.png" alt="Google Safe Browsing"></a></li>
                            <li><a class="d-flex" href="https://conectashop.com" target="_blank" alt="https://conectashop.com"><img src="<?= $loja['site'] ?>imagens/conectashop.png" alt="ConectaShop.com"></a></li>
                        </ul>
                    </div>     
                </div>
            </div>
        </div>

    <?php } else { ?>
        
        <!--INFORMAÇÕES IMPORTANTES-->
        <div id="informacoes-importantes">
            <div class="container">
                <div class="row">            
                    <div class="col-12 col-xl-4">
                        <ul id="meios-seguranca">
                            <li><h2>Tecnologia e segurança</h2></li>
                            <li><img src="<?= $loja['site'] ?>imagens/ssl.png" alt="SSL"></li>
                            <li><a class="d-flex" href="https://transparencyreport.google.com/safe-browsing/search?url=<?= $loja['site'] ?>&hl=pt-PT" target="_blank" alt="Google Safe Browsing"><img src="<?= $loja['site'] ?>imagens/google-safe-browsing.png" alt="Google Safe Browsing"></a></li>
                            <li><a class="d-flex" href="https://conectashop.com" target="_blank" alt="https://conectashop.com"><img src="<?= $loja['site'] ?>imagens/conectashop.png" alt="ConectaShop.com"></a></li>
                        </ul>
                    </div>     
                </div>
            </div>
        </div>

    <?php } ?>

    </div>

</div>

<?php if(!empty($loja['rd_station'])){ ?>
    <script type="text/javascript" async src="<?= $loja['rd_station'] ?>"></script>
    <script type='text/javascript' defer='defer'>
        function setEmailCarrinho(email){
            $.ajax({
                type: "POST",
                data: {'email': email},
                url: $("#site").val()+"php/rd-station-grava-email-carrinho.php"
            });  
        }
        function getEmailFromRdStation(){
            if($(".rdstation-popup-js-floating-button").length > 0){
                clearTimeout(rdStationIntervalAux);
                $(".rdstation-popup-js-floating-button").click(function(){
                    $(".rdstation-popup-js-form-identifier input[name=email]").keyup(function(){
                        setEmailCarrinho($(this).val());
                    });
                })
            }
        }
        const rdStationIntervalAux = setInterval(function () {
            getEmailFromRdStation()
        }, 500);
        $("input[name=email]").keyup(function(){
            setEmailCarrinho($(this).val());
        })
    </script>
<?php } ?>

</body>  

</html> 

<!--CSS CUSTOM-->
<link rel="stylesheet" href="<?= $loja['site'] ?>css/<?= $loja['custom_css'] ?>">

<!-- SCRIPTS -->
<script type="text/javascript" src="<?= $loja['site'] ?>modulos/contador-acesso/js/scripts.js"></script>
<?php if(!empty($loja['rd_station'])){ ?>
    <script src="<?= $loja['site'] ?>js/rd-station-events.js"></script>
<?php } ?>
<script src="<?= $loja['site'] ?>js/ga-events.js"></script>
<script src="<?= $loja['site'] ?>js/global-site-1.1.js"></script>
<script src="<?= $loja['site'] ?>js/<?= $loja['custom_js'] ?>"></script>