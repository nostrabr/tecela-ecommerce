<style>
    #container-banner-trabalhe-conosco{
        background-image: url('<?= $loja['site'] ?>imagens/banner-trabalhe-conosco.png');
        background-size: cover;
        background-position: center;
        width: 100%;
        height: auto;
    }

    #container-vagas{
        width: 85%;
        margin: 0 auto;
    }

    @media(min-width:1500px) {
        #container-vagas{
            width: 70%;
            margin: 0 auto;
        }
    }
    
    @media(max-width:992px) {
        #container-vagas{
            width: 90%;
            margin: 0 auto;
        }
    }

    /* Swiper overrides to work on dark banner */
    .swiper {
        overflow: visible; /* allow pagination to sit outside flow if needed */
    }
    .swiper-pagination {
        position: static !important; /* place pagination below slides */
        margin-top: 16px;
        text-align: center;
    }
    .swiper-pagination-bullet {
        background: rgba(255,255,255,0.7);
        opacity: 1;
    }
    .swiper-pagination-bullet-active {
        background: #fff;
    }
    /* Slide cards */
    .vaga-card {
        border-radius: 8px;
        padding: 0 16px;
    }
</style>

<!-- Swiper CSS via CDN -->
<link rel="stylesheet" href="https://unpkg.com/swiper@9/swiper-bundle.min.css" />


<div id="container-banner-trabalhe-conosco" class="py-5">
    <div class="py-5 my-3">
        <h2 class="text-white text-center mb-5 px-4 px-lg-0">Vagas <strong>Disponíveis</strong>:</h2>

        <!-- Slider container -->
        <div id="container-vagas">
            <div class="swiper" id="swiper-vagas">
                <div class="swiper-wrapper">
                    <!-- Slide 1 -->
                    <div class="swiper-slide vaga-card">
                        <h4 class="text-white fw-semibold mb-4">Auxiliar de Vendas</h4>
                        <p class="mb-2 text-white">- Possuir experiência na área de vendas;</p>
                        <p class="mb-2 text-white">- Disponibilidade de horários;</p>
                        <p class="mb-2 text-white">- Possuir CNH B.</p>
                    </div>
                    <!-- Slide 2 -->
                    <div class="swiper-slide vaga-card">
                        <h4 class="text-white fw-semibold mb-4">Técnico Instalador</h4>
                        <p class="mb-2 text-white">- Ser ágil e ter atenção a detalhes;</p>
                        <p class="mb-2 text-white">- Disponibilidade de horários;</p>
                        <p class="mb-2 text-white">- Possuir CNH B.</p>
                    </div>
                    <!-- Slide 3 -->
                    <div class="swiper-slide vaga-card">
                        <h4 class="text-white fw-semibold mb-4">Costureira</h4>
                        <p class="mb-2 text-white">- Possuir experiência na área de vendas;</p>
                        <p class="mb-2 text-white">- Disponibilidade de horários;</p>
                        <p class="mb-2 text-white">- Possuir CNH B.</p>
                    </div>
                </div>
                <!-- If we need pagination -->
                <!-- <div class="swiper-pagination"></div> -->
            </div>
        </div>
    </div>
</div>

<!-- Swiper JS via CDN -->
<script src="https://unpkg.com/swiper@9/swiper-bundle.min.js"></script>
<script>
    // Init Swiper with autoplay and draggable
    (function() {
        const slideCount = document.querySelectorAll('#swiper-vagas .swiper-slide').length;
        const desktopConfig = (slideCount === 2)
            ? { slidesPerView: 2, spaceBetween: 24, centeredSlides: true, centeredSlidesBounds: true }
            : { slidesPerView: 3, spaceBetween: 24 };

        const swiperVagas = new Swiper('#swiper-vagas', {
            slidesPerView: 1,
            spaceBetween: 24,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            loop: true,
            grabCursor: true,
            centerInsufficientSlides: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                992: desktopConfig
            }
        });
    })();
</script>