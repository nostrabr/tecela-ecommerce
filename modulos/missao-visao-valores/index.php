<style>
    #container-missao-visao-valores{
        width: 70%;
        margin: 30px auto;
    }

    .img-mvs{
        height: 120px;
    }
    .title-mvs{
        font-weight: bold;
        color: #DC582A;
    }
    .texto-mvs{
        text-align: center;
        color: #fff !important;
    }

    @media(min-width:1500px){
        .texto-mvs{
            width: 60%;
        }
        #container-missao-visao-valores{
            width: 60%;
        }
    }
    @media(max-width:992px){
        #container-missao-visao-valores{
            width: 100%;
        }
    }
</style>



<section style="margin-left: -15px; margin-right: -15px; background-color: #1C4A50;" class="py-5 px-4">
    <div class="row" id="container-missao-visao-valores">
        <div class="d-flex flex-column align-items-center mb-5 mb-lg-0 px-2 col-12 col-lg-4">
            <img class="img-mvs" src='<?= $loja['site'] ?>imagens/missao.png'>
            <h5 class="title-mvs mt-4 my-4">Missão</h5>
            <p class="texto-mvs">Criar, desenvolver, produzir e comercializar uniformes, com excelência de qualidade proporcionando total conforto e bem estar para seus usuários.</p>
        </div>

        <div class="d-flex flex-column align-items-center mb-5 mb-lg-0 px-2 col-12 col-lg-4">
            <img class="img-mvs" src='<?= $loja['site'] ?>imagens/visao.png'>  
            <h5 class="title-mvs mt-4 my-4">Visão</h5>
            <p class="texto-mvs">Tornar-se referência em atendimento, inovação e qualidade no seguimento de uniformes profissionais.</p>
        </div>

        <div class="d-flex flex-column align-items-center mb-lg-0 px-2 col-12 col-lg-4">
            <img class="img-mvs" src='<?= $loja['site'] ?>imagens/valores.png'>
            <h5 class="title-mvs mt-4 my-4">Valores</h5>
            <p class="texto-mvs">- Satisfação do cliente;</p>
            <p class="texto-mvs">- Responsabilidade social;</p>
            <p class="texto-mvs">- Ética profissional;</p>
            <p class="texto-mvs">- Valorização e respeito <br> as pessoas;</p>
        </div>
    </div>
</section>