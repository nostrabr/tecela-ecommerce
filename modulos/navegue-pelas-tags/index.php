<style>
    #container-navegue-pelas-tags{
        margin-left: -15px;
        margin-right: -15px;
        padding: 70px 5%;
        background-color: white;
    }

    #tags-home{
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
    }

    .tag-item{
        color: #DC582A;
        border: 1px solid #DC582A;
        border-radius: 10px;
        cursor: pointer;
        padding: 15px 40px;
    }
    .tag-item:hover{
        background-color: #DC582A;
        color: white;
        border: 1px solid #DC582A;
        border-radius: 10px;
        padding: 15px 40px;
    }

    #container-banner-solucoes{
        width: 100%;
        overflow: hidden;
    }
    #container-banner-solucoes img{
        width: 100%;
    }

    @media(max-width:992px){
        .tag-item{
            width: 100%;
            text-align: center;
        }

        #container-banner-solucoes{
            width: 110%;
            margin-left: -15px;
        }

        #container-navegue-pelas-tags{
            padding: 70px 5% 0px 5%;
        }
    }
</style>


<section id="container-navegue-pelas-tags">
        <div class="nossas-linhas-titulo mb-5">
            <div class="text-center mb-4"><img style="width: 50px;" src='<?= $loja['site']?>imagens/ico-lancamento.png'></div>
            <h2 style="font-size: 1.4em;" class="text-center">Navegue</h2>
            <h3 style="font-size: 2em;" class="text-center">Pelas Tags</h3>
        </div>

        <div id="tags-home">
            <div class="tag-item">
                CAMISETAS
            </div>
            <div class="tag-item">
                MOLETOM
            </div>
            <div class="tag-item">
                POLO
            </div>
            <div class="tag-item">
                SUÉTER
            </div>
        </div>


        <div id="container-banner-solucoes" class="mt-5 mb-0 mb-lg-5">
            <a href="" style="opacity: 1;"><img class="d-none d-lg-block" src='<?= $loja['site']?>imagens/banner-solucoes-desktop.png'></a>
            <a href="" style="opacity: 1;"><img class="d-block d-lg-none" src='<?= $loja['site']?>imagens/banner-solucoes-mobile.png'></a>
        </div>
</section>