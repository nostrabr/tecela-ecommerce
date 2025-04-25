<!--CSS-->

<style>
    #footer-new{
        border-top: 10px solid #DC582A;
    }
    .logo-footer{
        width: 250px;
    }

    #rodape-footer{
        width: 90%;
     }

    ._sub-footer{
        font-size: 18px;
        font-weight: 600;
    }

    #body-site footer a#acessar-mapa{
        border-radius: 40px !important;
        font-size: 14px;
        background-color: white !important;
        color: #1C4A50 !important;
        padding: 10px 35px;
    }

    #division{
        width: 90%;
        height: 1px;
        background-color: white;
        margin-top: 70px;
        margin-bottom: 70px;
        margin-left: auto;
        margin-right: auto;
    }

    ._container-items-footer{
        margin-left: auto;
        margin-right: auto;
        width: 90%;
    }

    ._title-items-footer{
        font-size: 17px;
        font-weight: bold;
    }

    #body-site footer a._link-footer {
        color: white !important;
        font-weight: 500;
    }

    ._font-item-footer{
        font-size: 14px;
    }

    @media(min-width:1500px){
        #division{
            width: 70%;
        }

        ._container-items-footer{
            width: 70%;
        }

        #rodape-footer{
            width: 70%;
        }
    }

    @media(max-width:992px){
        .logo-footer{
            width: 170px !important;
        }

        #division{
            width: 100%;
        }

        ._container-items-footer{
            width: 100%;
        }

        #rodape-footer{
            width: 100%;
        }
    }
</style>

<!--FOOTER-->
<footer id="footer-new" class="mt-5 px-4 px-lg-0">

    <div class="w-100 text-start text-lg-center"><img src='<?= $loja['site'] ?>imagens/logo.png' alt='Logo' class='logo-footer'></div>

    <div class="d-flex justify-content-start justify-content-lg-center mt-5"><div class="text-start text-lg-center text-white _sub-footer" style="width: 250px;">Vestir bem a sua empresa é a nossa missão.</div></div>

    <div class="mt-5 w-100 d-flex justify-content-start justify-content-lg-center">
        <a href="<?= $loja['instagram'] ?>" target="_blank" class="mr-4"><img style="height: 30px;" src='<?= $loja['site'] ?>imagens/insta-white.png'></a>
        <a href="<?= $loja['facebook'] ?>" target="_blank"><img style="height: 30px;" src='<?= $loja['site'] ?>imagens/fb-white.png'></a>
    </div>

    <div id="division"></div>

    <div class="row _container-items-footer text-white">
        <div class="col-12 col-lg-3 mb-5 mb-lg-0">
            <h5 class="_title-items-footer mb-4">Horário de atendimento</h5>

            <h6 class="_font-item-footer mb-1" style="font-weight: bold;">Segunda a Sexta-Feira</h6>
            <p class="_font-item-footer mb-0 text-white">Manhã: 08:00h - 12:00h</p>
            <p class="_font-item-footer text-white">Tarde: 13:30h - 17:30h</p>
        </div>

        <div class="col-12 col-lg-3 mb-5 mb-lg-0">
            <h5 class="_title-items-footer mb-4">Contato</h5>

            <div class="d-flex justify-content-start align-items-center mb-3">
                <img style="width: 30px;" class="mr-3" src='<?= $loja['site']; ?>imagens/wpp-orange.png'>
                <p class="text-white _font-item-footer align-self-center my-0">(54) 99132-4215</p>
            </div>

            <div class="d-flex justify-content-start align-items-center mb-3">
                <img style="width: 30px;" class="mr-3 align-self-center" src='<?= $loja['site']; ?>imagens/phone-orange.png'>
                <div>
                    <p class="text-white _font-item-footer my-2">(54) 99132-4215</p>
                    <p class="text-white _font-item-footer my-2">(54) 99132-4215</p>
                    <p class="text-white _font-item-footer my-2">(54) 99132-4215</p>
                </div>
            </div>

            <div class="d-flex justify-content-start align-items-center mb-3">
                <img style="width: 30px;" class="mr-3 align-self-center" src='<?= $loja['site']; ?>imagens/email-orange.png'>
                <div>
                    <p class="text-white _font-item-footer my-2">tecela@tecela.com.br</p>
                    <p class="text-white _font-item-footer my-2">comercial@tecela.com.br</p>
                </div>
            </div>

        </div>

        <div class="col-12 col-lg-3 mb-5 mb-lg-0">
            <h5 class="_title-items-footer mb-4">Onde Estamos</h5>

            <p class="text-white mb-5 _font-item-footer">Rua Padre Valentim Rumpel, N 905, Não-Me-Toque – RS <br> CEP: 99470-000</p>

            <a href="" target="_blank" id="acessar-mapa">Acessar mapa</a>
        </div>

        <div class="col-12 col-lg-3">
            <h5 class="_title-items-footer mb-4 mb-lg">Navegação</h5>

            <div class="d-flex flex-column align-items-start">
                <a href="" class="_link-footer _font-item-footer mb-3">Home</a>
                <a href="" class="_link-footer _font-item-footer mb-3">Quem Somos</a>
                <a href="" class="_link-footer _font-item-footer mb-3">Produtos</a>
                <a href="" class="_link-footer _font-item-footer mb-3">Contato</a>
            </div>
        </div>
    </div>
</footer>

<div class="bg-white py-4 px-4 px-lg-0">
    <div id="rodape-footer" class="mr-auto ml-auto d-flex justify-content-between align-items-center flex-column flex-lg-row">
        <span class="mb-4 mb-lg-0 text-center text-lg-start" style="color: #1C4A50; font-weight: 500;"><?= date('Y'); ?> © Tecelã - Todos os direitos reservados.</span>

        <img style="width: 80px;" src='<?= $loja['site'] ?>imagens/nostra-logo.png'>
    </div>
</div>

<!--FOOTER-->


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