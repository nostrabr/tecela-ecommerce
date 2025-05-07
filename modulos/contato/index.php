<style>
    #container-contato{
        background-image: url('<?= $loja['site']; ?>imagens/banner-contato.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .title-contato{
        color: #fff;
        font-size: 35px;
        width: 500px;
        font-weight: 600;
    }

    .container-contatos-infos{
        width: 1000px;
    }

    @media(max-width:992px){
        #container-contato{
            margin-right: -15px;
            margin-left: -15px;
            padding-left: 30px !important;
            padding-right: 30px !important;
        }
        .title-contato{
            font-size: 21px;
            width: 90% !important;
        }
    }
</style>


<section class="py-5 px-2 px-lg-5" id="container-contato">
    <div class="container-contato-infos">
        <h2 class="mb-5 title-contato"><strong>Soluções</strong> em uniformes e vestuário para fortalecer a imagem da sua <strong>empresa</strong>.</h2>

        <div class="row container-contatos-infos">
            <div class="order-1 order-lg-1 d-flex align-items-start col-12 col-lg-3 mb-5">
                <img style="width: 30px;" src='<?= $loja['site'] ?>imagens/contato-wpp.png'>
                <div class="d-flex flex-column ml-3">
                    <a href="" class="text-white mb-2">(54) 99132-4215</a>
                </div>
            </div>

            <div class="order-2 order-lg-2 d-flex align-items-start col-12 col-lg-4 mb-5">
                <img style="width: 30px;" src='<?= $loja['site'] ?>imagens/contato-email.png'>
                <div class="d-flex flex-column ml-3">
                    <a href="" class="text-white mb-2">tecela@tecela.com.br</a>
                    <a href="" class="text-white">comercial@tecela.com.br</a>
                </div>
            </div>

            <div class="order-4 order-lg-3 col-12 col-lg-4 mb-0 mb-lg-5">
                <h6 class="fw-bold text-white mb-4">Nos acompanhe nas redes</h6>
                <div class="d-flex">
                    <a href=""><img style="height: 30px;" class="mr-4" src='<?= $loja['site'] ?>imagens/contato-insta.png'></a>
                    <a href=""><img style="height: 30px;" src='<?= $loja['site'] ?>imagens/contato-fb.png'></a>
                </div>
            </div>

            <div class="order-3 order-lg-4 d-flex align-items-start col-12 col-lg-4 mb-5">
                <img style="width: 30px;" src='<?= $loja['site'] ?>imagens/contato-fone.png'>
                <div class="d-flex flex-column ml-3">
                    <a href="" class="text-white mb-2">(54) 3332-1355</a>
                    <a href="" class="text-white mb-2">(54) 3332-4525</a>
                    <a href="" class="text-white mb-2">(54) 3332-5522</a>
                </div>
            </div>
        </div>
    </div>
</section>