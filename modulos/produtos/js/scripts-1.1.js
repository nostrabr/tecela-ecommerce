if(localStorage.getItem('primeiro-click-menu-filtros') == 'S'){
    $("#btn-filtros-mobile").addClass('btn-filtros-mobile-reduzido');
}
abreLoader();

$(document).ready(function(){

    if($(".produtos-vistos-recentemente").length > 0){
        
        var produtos = localStorage.getItem("produtos-vistos-recentemente");
        if(produtos != '' & produtos != 'null'){
            $.ajax({
                url: "modulos/produtos/php/busca-produtos-recentes.php",
                type: "POST",
                data: {"produtos": produtos},
                success: function (retorno){                  
                    if(retorno != ''){
                        $(".produtos-vistos-recentemente .row").html(retorno);
                    }       
                    fechaLoader();        
                }
            });
        }

    }
    
    $(".produto-container-valor-esgotado").click(function(e){
        e.stopPropagation();
    });
    
    $("#filtros-mobile").html($("#produtos-filtros").html());
    $("#filtros-mobile #slider-range").attr("id","slider-range-mobile");
    $("#filtros-mobile #amount").attr("id","amount-mobile");

    if($(".produto").length > 0){

        var slider_range         = document.getElementById('slider-range');
        var slider_range_mobile  = document.getElementById('slider-range-mobile');
        valor_minimo_geral       = parseFloat(Math.floor($("#slider-range").attr("minimo-geral"))).toFixed(0);
        valor_maximo_geral       = parseFloat(Math.ceil($("#slider-range").attr("maximo-geral"))).toFixed(0);
        valor_minimo_selecionado = parseFloat(Math.floor($("#slider-range").attr("minimo-selecionado"))).toFixed(0);
        valor_maximo_selecionado = parseFloat(Math.ceil($("#slider-range").attr("maximo-selecionado"))).toFixed(0);
        
        if(valor_minimo_geral != 0 | valor_maximo_geral != 0){

            if(valor_minimo_geral == valor_maximo_geral){
                valor_maximo_geral = valor_maximo_geral+1;
            }

            if(valor_minimo_selecionado != valor_maximo_selecionado){

                noUiSlider.create(slider_range, {
                    start: [valor_minimo_selecionado, valor_maximo_selecionado],
                    connect: true,
                    range: {
                        'min': parseFloat(valor_minimo_geral),
                        'max': parseFloat(valor_maximo_geral)
                    }
                });

                noUiSlider.create(slider_range_mobile, {
                    start: [valor_minimo_selecionado, valor_maximo_selecionado],
                    connect: true,
                    range: {
                        'min': parseFloat(valor_minimo_geral),
                        'max': parseFloat(valor_maximo_geral)
                    }
                });

                slider_range.noUiSlider.on('update', function (values, handle) {
                    $("#amount").val(parseFloat(values[0]).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'})+" - "+parseFloat(values[1]).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}));
                });
                
                slider_range_mobile.noUiSlider.on('update', function (values, handle) {
                    $("#amount-mobile").val(parseFloat(values[0]).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'})+" - "+parseFloat(values[1]).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}));
                });
                
                slider_range.noUiSlider.on('change', function (values, handle) {
                    var novo_minimo = Math.floor(values[0]);
                    var novo_maximo = Math.ceil(values[1]);
                    var url_destino = $(('#slider-range')).attr('url')+'/'+novo_minimo+'/'+novo_maximo+'/'+$("#slider-range").attr('marcas')+'/'+$("#slider-range").attr('caracteristicas')+'/'+$("#slider-range").attr('genero-idade')+'/'+$("#select-ordenar-por").val()+'/'+$("#slider-range").attr('tags');
                    window.location.href = url_destino;
                });

                slider_range_mobile.noUiSlider.on('change', function (values, handle) {
                    var novo_minimo = Math.floor(values[0]);
                    var novo_maximo = Math.ceil(values[1]);
                    var url_destino = $(('#slider-range-mobile')).attr('url')+'/'+novo_minimo+'/'+novo_maximo+'/'+$("#slider-range-mobile").attr('marcas')+'/'+$("#slider-range-mobile").attr('caracteristicas')+'/'+$("#slider-range-mobile").attr('genero-idade')+'/'+$("#select-ordenar-por").val()+'/'+$("#slider-range-mobile").attr('tags');
                    window.location.href = url_destino;
                });

            } else {
                $(".container-filtro-preco").hide();
            }

        } else {
            $(".container-filtro-preco").hide();
        }

    }

    $("#produtos-filtros input[name='checkbox-tags']").click(function(){
        var tags = '';
        $("#produtos-filtros input[name='checkbox-tags']:checked").each(function(){
            tags += $(this).val()+'-';
        });
        tags = tags.slice(0, -1);
        if(tags == ''){ tags = 'T'; }
        var url_destino = $(this).attr('url')+'/'+$(this).attr('caracteristicas')+'/'+$(this).attr('genero-idade')+'/'+$("#select-ordenar-por").val()+'/'+tags;
        window.location.href = url_destino;
    });
    
    $("#filtros-mobile input[name='checkbox-tags']").click(function(){
        var tags = '';
        $("#filtros-mobile input[name='checkbox-tags']:checked").each(function(){
            tags += $(this).val()+'-';
        });
        tags = tags.slice(0, -1);
        if(tags == ''){ tags = 'T'; }
        var url_destino = $(this).attr('url')+'/'+$(this).attr('caracteristicas')+'/'+$(this).attr('genero-idade')+'/'+$("#select-ordenar-por").val()+'/'+tags;
        window.location.href = url_destino;
    });   

    $("#produtos-filtros input[name='checkbox-marcas']").click(function(){
        var marcas = '';
        $("#produtos-filtros input[name='checkbox-marcas']:checked").each(function(){
            marcas += $(this).val()+'-';
        });
        marcas = marcas.slice(0, -1);
        if(marcas == ''){ marcas = 'T'; }
        var url_destino = $(this).attr('url')+'/'+marcas+'/'+$(this).attr('caracteristicas')+'/'+$(this).attr('genero-idade')+'/'+$("#select-ordenar-por").val()+'/'+$(this).attr('tags');
        window.location.href = url_destino;
    });
    
    $("#filtros-mobile input[name='checkbox-marcas']").click(function(){
        var marcas = '';
        $("#filtros-mobile input[name='checkbox-marcas']:checked").each(function(){
            marcas += $(this).val()+'-';
        });
        marcas = marcas.slice(0, -1);
        if(marcas == ''){ marcas = 'T'; }
        var url_destino = $(this).attr('url')+'/'+marcas+'/'+$(this).attr('caracteristicas')+'/'+$(this).attr('genero-idade')+'/'+$("#select-ordenar-por").val()+'/'+$(this).attr('tags');
        window.location.href = url_destino;
    });    

    $("#produtos-filtros input[name='checkbox-caracteristicas']").click(function(){
        var caracteristicas = '';
        $("#produtos-filtros input[name='checkbox-caracteristicas']:checked").each(function(){
            caracteristicas += $(this).val()+'-';
        });
        caracteristicas = caracteristicas.slice(0, -1);
        if(caracteristicas == ''){ caracteristicas = 'T'; }
        var url_destino = $(this).attr('url')+'/'+caracteristicas+'/'+$(this).attr('genero-idade')+'/'+$("#select-ordenar-por").val()+'/'+$(this).attr('tags');
        window.location.href = url_destino;
    });

    $("#produtos-mobile input[name='checkbox-caracteristicas']").click(function(){
        var caracteristicas = '';
        $("#produtos-mobile input[name='checkbox-caracteristicas']:checked").each(function(){
            caracteristicas += $(this).val()+'-';
        });
        caracteristicas = caracteristicas.slice(0, -1);
        if(caracteristicas == ''){ caracteristicas = 'T'; }
        var url_destino = $(this).attr('url')+'/'+caracteristicas+'/'+$(this).attr('genero-idade')+'/'+$("#select-ordenar-por").val()+'/'+$(this).attr('tags');
        window.location.href = url_destino;
    });  

    $("#produtos-filtros input[name='checkbox-genero']").click(function(){
        var generos_idades = '';
        $("#produtos-filtros input[name='checkbox-genero']:checked").each(function(){
            generos_idades += $(this).val()+'-';
        });
        $("#produtos-filtros input[name='checkbox-idade']:checked").each(function(){
            generos_idades += $(this).val()+'-';
        });
        generos_idades = generos_idades.slice(0, -1);
        if(generos_idades == ''){ generos_idades = 'T'; }
        var url_destino = $(this).attr('url')+'/'+generos_idades+'/'+$("#select-ordenar-por").val()+'/'+$(this).attr('tags');
        window.location.href = url_destino;
    });

    $("#produtos-filtros input[name='checkbox-idade']").click(function(){
        var generos_idades = '';
        $("#produtos-filtros input[name='checkbox-genero']:checked").each(function(){
            generos_idades += $(this).val()+'-';
        });
        $("#produtos-filtros input[name='checkbox-idade']:checked").each(function(){
            generos_idades += $(this).val()+'-';
        });
        generos_idades = generos_idades.slice(0, -1);
        if(generos_idades == ''){ generos_idades = 'T'; }
        var url_destino = $(this).attr('url')+'/'+generos_idades+'/'+$("#select-ordenar-por").val()+'/'+$(this).attr('tags');
        window.location.href = url_destino;
    });

    if($(".paginacao-produtos").length > 0){

        var pagina_atual       = $("#pagina-atual").val();
        var total_paginas      = $("#total-paginas").val();
        var url_pagina         = $("#url-pagina").val();
        var complemento_pagina = $("#complemento-pagina").val();

        $('.paginacao-produtos').twbsPagination({
            startPage: parseInt(pagina_atual),
            totalPages: total_paginas,
            visiblePages: 3,
            first: '',
            last: '',
            prev: '«',
            next: '»',
            initiateStartPageClick: false,
            onPageClick: function (event, page) {
                window.location.href = url_pagina+'/'+page+complemento_pagina;
            }
        });
    }

    fechaLoader();

});


$("#btn-filtros-mobile").click(function(){

    if(!localStorage.getItem('primeiro-click-menu-filtros')){
        localStorage.setItem('primeiro-click-menu-filtros', 'S');
    }

    if(!$(this).hasClass('btn-filtros-mobile-fechar')){
        $("#body-site").css("overflow","hidden");
        $("#filtros-mobile").slideDown('fast').animate(
            { duration: '300' }
        );
        $("#btn-filtros-mobile").addClass('btn-filtros-mobile-fechar');    
    } else {
        $("#body-site").css("overflow","unset");
        $("#filtros-mobile").slideUp('fast').animate(
            { duration: '150' }
        );
        $("#btn-filtros-mobile").addClass('btn-filtros-mobile-reduzido').removeClass('btn-filtros-mobile-fechar');
    }

}); 
