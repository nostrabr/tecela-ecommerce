<style>
    #container-trabalhe-conosco-home{
        padding: 50px 5% !important;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        margin-left: -15px;
        margin-right: -15px;
    }

    #faca-parte-equipe{
        background-color: white;
        border-radius: 10px;
        border: 1px solid #DC582A;
        color: #1C4A50;
        padding: 10px 25px;
    }

    @media(min-width:1500px){
        #container-trabalhe-conosco-home{
            padding: 90px 5% !important;
        }

        #container-trabalhe-conosco-home h2{
            font-size: 35px;
            font-weight: normal;
        }

        #faca-parte-equipe{
            font-size: 22px;
        }
    }

    @media(max-width:992px){
        #container-trabalhe-conosco-home h2{
            font-size: 22px;
            font-weight: normal;
        }
    }
</style>


<section id="container-trabalhe-conosco-home" style="background-image: url('<?= $loja['site']; ?>imagens/trabalhe-conosco-home.png');">
    <h2 class="text-white mb-4"><strong>Trabalhe conosco</strong> e <br> construa uma carreira <br> sólida em nossa <strong>equipe</strong>!</h2>
    <a href="" id="faca-parte-equipe">Faça parte da equipe</a>
</section>