if($('.slider-modo-banners-iguais').length > 0){
    $('.slider-modo-banners-iguais').slick({
        arrows:false,
        dots: true,
        infinite: true,
        speed: 200
    });
}

$(document).ready(function(){
    if($("#carrosel-lg .carousel-item").length > 1){
        $("#carrosel-lg .carousel-control-prev").css('visibility','visible');
        $("#carrosel-lg .carousel-control-next").css('visibility','visible');
        $("#carrosel-lg .carousel-indicators").css('visibility','visible');
        $("#carrosel-mobile .carousel-control-prev").css('visibility','visible');
        $("#carrosel-mobile .carousel-control-next").css('visibility','visible');
        $("#carrosel-mobile .carousel-indicators").css('visibility','visible');
    }
});
