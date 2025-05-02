<style>
    #container-mapa{
        position: relative;
        width: 100%;
        height: 70vh;
    }

    #infos{
        position: absolute;
        top: 0;
        left: 0;
        width: 40%;
        height: 60%;
        background-color: #1C4A50;
        padding-left: 40px;
        padding-top: 40px;
        padding-right: 30px;
        border-bottom-right-radius: 75%;
        z-index: 2;
    }

    #mapa{
        position: absolute;
        width: 100%;
        height: 100%;
        z-index: 1;
    }

    #acessar-mapa{
        color: white !important;
        background-color: #DC582A;
        padding: 12px 25px;
        border-radius: 10px;
        opacity: 1 !important;
    }

    @media(min-width:1500px){
        #container-mapa{
            height: 40vh;
        }

        #infos{
            width: 30%;
            height: 60%;
            padding-left: 100px;
            padding-top: 40px;
            padding-right: 30px;
            border-bottom-right-radius: 65%;
        }
    }

    @media(max-width:992px){
        #container-mapa{
            margin-left: -15px;
            margin-right: -15px;
            height: 80vh;
            width: 110%;
        }
        #infos{
            width: 100%;
            height: auto;
            padding-left: 30px;
            padding-top: 60px;
            padding-right: 30px;
            padding-bottom: 60px;
            border-bottom-right-radius: 0%;
        }
    }
</style>


<section id="container-mapa">
    <div id="infos">
        <h2 class="text-white mb-3"><img style="width: 30px;" src='<?= $loja['site']?>imagens/maps.png'> Onde estamos</h2>
        <p class="mb-4 text-white">R. Padre Valentim Rumpel, 905 - Martini, Não-Me-Toque - RS, 99470-000</p>
        <a href="" target="_blank" id="acessar-mapa">Acesse o mapa</a>
    </div>

    <div id="mapa">
        <iframe class="w-100 h-100" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3507.4877368979237!2d-52.82382462489951!3d-28.46485465983732!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94fd49cc8765f025%3A0xacd078521e3fc77d!2zVGVjZWzDow!5e0!3m2!1spt-BR!2sbr!4v1746208146840!5m2!1spt-BR!2sbr" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</section>