<style>
    #container-nosso-alcance{
        background-image: url('<?= $loja['site']; ?>imagens/banner-nosso-alcance.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        margin-left: -15px;
        margin-right: -15px;
    }

    .container-nosso-alcance{
        width: 70%;
    }

    .title-alcance{
        font-size: 65px;
        color: white !important;
        font-weight: bold !important;
        margin-bottom: 0px !important;
    }
    .sub-alcance{
        font-size: 17px;
        color: white !important;
    }

    @media(min-width:1500px) {
        .container-nosso-alcance{
            width: 60%;
        }
        .title-alcance{
            font-size: 75px;
        }
        .sub-alcance{
            font-size: 18px;
        }
    }
    
    @media(max-width:992px) {
        .container-nosso-alcance{
            width: 100%;
        }
    }
</style>


<section id="container-nosso-alcance" class="py-5 d-flex flex-column align-items-center">
    <img style="width: 120px;" src='<?= $loja['site'] ?>imagens/nosso-alcance-logo.png'>

    <div class="container-nosso-alcance row mt-5">
        <div class="mb-5 mb-lg-0 col-12 col-lg-3 px-4 text-center">
            <h2 class="title-alcance">18</h2>
            <p class="sub-alcance">Anos de História</p>
        </div>
        <div class="mb-5 mb-lg-0 col-12 col-lg-3 px-4 text-center">
            <h2 class="title-alcance">+800</h2>
            <p class="sub-alcance">Clientes em <br> todo Brasil</p>
        </div>
        <div class="mb-5 mb-lg-0 col-12 col-lg-3 px-4 text-center">
            <h2 class="title-alcance">+100</h2>
            <p class="sub-alcance">Produtos em <br> Linha de Fabricação</p>
        </div>
        <div class="mb-lg-0 col-12 col-lg-3 px-4 text-center">
            <h2 class="title-alcance">+60</h2>
            <p class="sub-alcance">Colaboradores <br> Diretos</p>
        </div>
    </div>
</section>