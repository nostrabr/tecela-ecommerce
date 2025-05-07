<!--CSS-->
<style>
    #texto-sobre{
        width: 70%;
        margin-right: auto;
        margin-left: auto;
    }

    @media(max-width:992px){
        #texto-sobre{
            width: 100%;
        }
    }
</style>

<!--SOBRE-->
<section class="py-5 bg-white px-4" style="margin-left: -15px; margin-right: -15px;">

    <h2 class="d-none">Sobre</h2>

    <div class="w-100 text-center mb-5">
        <img style="width: 250px;" src='<?= $loja['site'] ?>imagens/logo-tecela-nome.png'>
    </div>

    <div class="text-center" id="texto-sobre"><?= $loja['pagina_sobre'] ?></div>

</section>
