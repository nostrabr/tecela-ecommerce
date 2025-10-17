<?php
// Conecta com o banco e busca as vagas
require_once __DIR__ . '/../../bd/conecta.php';

$vagas = [];
try {
    $sql = "SELECT titulo, requisitos FROM vagas ORDER BY data_criacao DESC";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Decodifica o JSON dos requisitos
            $requisitos_array = json_decode($row['requisitos'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $requisitos_array = [$row['requisitos']]; // Fallback se não for JSON válido
            }
            $vagas[] = [
                'titulo' => $row['titulo'],
                'requisitos' => $requisitos_array
            ];
        }
    }
    $conn->close();
} catch (Exception $e) {
    // Em caso de erro, continua com array vazio
    $vagas = [];
}
?>

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
                    <?php if (!empty($vagas)): ?>
                        <?php foreach ($vagas as $vaga): ?>
                            <div class="swiper-slide vaga-card">
                                <h4 class="text-white fw-semibold mb-4"><?= htmlspecialchars($vaga['titulo']) ?></h4>
                                <?php foreach ($vaga['requisitos'] as $requisito): ?>
                                    <p class="mb-2 text-white">- <?= htmlspecialchars($requisito) ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback caso não haja vagas no banco -->
                        <div class="swiper-slide vaga-card">
                            <h4 class="text-white fw-semibold mb-4">Nenhuma vaga disponível</h4>
                            <p class="mb-2 text-white">- No momento não temos vagas abertas;</p>
                            <p class="mb-2 text-white">- Acompanhe nosso site para futuras oportunidades;</p>
                            <p class="mb-2 text-white">- Envie seu currículo para análise.</p>
                        </div>
                    <?php endif; ?>
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
        
        // Configuração dinâmica baseada no número de vagas
        let desktopConfig;
        if (slideCount === 0) {
            // Não inicializa o swiper se não há slides
            return;
        } else if (slideCount === 1) {
            desktopConfig = { slidesPerView: 1, spaceBetween: 24, centeredSlides: true };
        } else if (slideCount === 2) {
            desktopConfig = { slidesPerView: 2, spaceBetween: 24, centeredSlides: true, centeredSlidesBounds: true };
        } else {
            desktopConfig = { slidesPerView: 3, spaceBetween: 24 };
        }

        const swiperVagas = new Swiper('#swiper-vagas', {
            slidesPerView: 1,
            spaceBetween: 24,
            autoplay: slideCount > 1 ? {
                delay: 3000,
                disableOnInteraction: false,
            } : false,
            loop: slideCount > 1,
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