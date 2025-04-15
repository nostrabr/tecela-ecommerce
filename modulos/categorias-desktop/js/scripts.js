$(".categorias-desktop-categoria").mouseover(function(){
    $(".categorias-desktop-subcategoria").each(function(){
        $(this).hide();
    });
    var id_categoria                    = $(this).attr('id-categoria');
    var categoria_left_position         = $("#categorias-desktop-categoria-"+id_categoria).position().left;
    var categoria_size                  = $("#categorias-desktop-categoria-"+id_categoria).width() / 3;
    var posicao_container_subcategorias = categoria_left_position+categoria_size;
    $("#categorias-desktop-subcategoria-"+id_categoria).css('left',posicao_container_subcategorias+'px').show(100);
});

$("#categorias-desktop").mouseleave(function(){
    $(".categorias-desktop-subcategoria").hide();
});

function abreFechaCategoriasEscondidas(){    
    if($("#categorias-desktop-escondidas").css('display') == 'none'){
        $("#categorias-desktop-chevron-vertudo").css("transform","rotate(-180deg)");
        $("#categorias-desktop-escondidas").slideDown('fast').animate({ duration: '200' });
    } else {
        $("#categorias-desktop-chevron-vertudo").css("transform","rotate(0deg)");
        $("#categorias-desktop-escondidas").slideUp('fast').animate({ duration: '200' });
    }
}