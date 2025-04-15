if($("#produtos-novos .produto-container").length >= 5){
    $('.slider-produtos-novos').slick({
        arrows: true,
        dots: false,
        infinite: true,
        speed: 200,
        slidesToShow: 5,
        slidesToScroll: 5,
        prevArrow: '<button class="slick-prev slick-arrow" aria-label="Previous" type="button"><i class="fas fa-chevron-left"></i></button>',
        nextArrow: '<button class="slick-next slick-arrow" aria-label="Previous" type="button"><i class="fas fa-chevron-right"></i></button>',
        responsive: [        
            {
                breakpoint: 1400,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll: 4,
                    infinite: true,
                    dots: false
                }
            },
            {
                breakpoint: 1199,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                    infinite: true,
                    dots: false
                }
            },
            {
            breakpoint: 767,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    infinite: true,
                    dots: false,
                    arrows: true
                }
            }
        ]
    });
}