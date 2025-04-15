function mostraTags(){
    $(".tags-tag").each(function(){
        $(this).parent('.d-none').removeClass('d-none');
    });
    $("#btn-mostra-todas-tags").parent('.col-12').hide();
}