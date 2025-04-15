$(".banner-secundario-item-capa").hover(function(){
    $(this).css('background-color','rgba(255,255,255,0.0)');
    $(this).parent('.banner-secundario-item').find('img').css('object-position','left');
});
$(".banner-secundario-item-capa").mouseleave(function(){
    $(this).css('background-color','rgba(255,255,255,0.4)');
    $(this).parent('.banner-secundario-item').find('img').css('object-position','center');
});