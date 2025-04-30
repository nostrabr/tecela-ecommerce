<style>
    #container-img-sobre-home{
        width: 100%;
        border-radius: 20px;
        height: 250px;
        overflow: hidden;
    }
    #container-img-sobre-home img{
        width: 100%;
    }

    #container-quem-somos-home{
        width: 90%;
        margin: 50px auto;
    }

    @media(min-width:1500px) {
        #container-img-sobre-home{
            height: 400px;
        }

        #title-quem-somos-home{
            font-size: 35px;
        }
        #sub-quem-somos-home{
            font-size: 22px;
        }
        #btn-quem-somos-home{
            font-size: 22px;
        }
    }
    
    @media(max-width:992px) {
        #container-img-sobre-home{
            height: auto;
        }

        #container-quem-somos-home{
            margin: 30px auto;
        }

        #title-quem-somos-home{
            text-align: center;
        }
        #sub-quem-somos-home{
            text-align: center;
        }
        #btn-quem-somos-home{
            display: block;
            width: 70%;
            text-align: center;
            margin: 0 auto;
        }
    }
</style>


<section class="py-5">
    <div id="container-quem-somos-home">
        <div class="row">
            <div class="col-12 col-lg-6 mb-5 mb-lg-0 px-0">
                <div id="container-img-sobre-home">
                    <img src='<?= $loja['site']?>imagens/quem-somos-home.png' alt="Quem somos">
                </div>
            </div>
            <div class="pl-0 pl-lg-5 col-12 col-lg-6">
                <h3 id="title-quem-somos-home" class="mb-3" style="color: #DC582A; font-weight: bold;">Quem Somos</h3>
                <p id="sub-quem-somos-home" style="color: #1C4A50;" class="mb-5">Fundada em 2002, a empresa Tecelã tem por objetivo a <strong>confecção de uniformes profissionais</strong>, está situada no município de Não-Me-Toque no norte do estado do Rio Grande do Sul e conta também com uma filial na cidade de Mormaço, implantada no ano de 2006...</p>
                <a href="" id="btn-quem-somos-home" class="rounded py-3 px-5 text-white" style="background-color: #1C4A50;">Conheça mais</a>
            </div>
        </div>
    </div>
</section>