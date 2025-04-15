abreLoader();

var acessando              = true;
var array_produtos         = [];
var filtro_estoque         = $("#filtro-estoque").hasClass("produto-filtro-ativo");
var filtro_promocao        = $("#filtro-promocao").hasClass("produto-filtro-ativo");

function filtroCategoria(){
    
    var categorias_ativas   = [];
    var categorias_inativas = [];
            
    $(".produto-filtro-categoria").each(function(){
        if($(this).hasClass('produto-filtro-ativo')){
            categorias_ativas.push($(this).attr("categoria"));
        } else {
            categorias_inativas.push($(this).attr("categoria"));
        }
    });

    if(categorias_ativas.length > 0){
        $(".produto").each(function(){
            if(categorias_ativas.includes($(this).attr("categoria"))){
                $(this).removeClass('categoria-filtrada');
            }
            if(categorias_inativas.includes($(this).attr("categoria"))){
                $(this).addClass('categoria-filtrada');
            }
        });
    } else {
        $(".produto").each(function(){
            $(this).removeClass('categoria-filtrada');
        });
    }    
    
    if(!acessando) adicionaVariavelUrl();

}

function filtroPreco(){   
    
    abreLoader();

    var preco_inicial = $("#input-preco-inicial").val(); 
    var preco_final = $("#input-preco-final").val();

    if(preco_inicial == ''){ preco_inicial = 0; }
    if(preco_final == ''){ preco_final = 9999999; }

    $(".produto").each(function(){
        var preco_produto = $(this).attr('preco');
        if(preco_produto != ''){
            if(parseFloat(preco_produto) >= parseFloat(preco_inicial) & parseFloat(preco_produto) <= parseFloat(preco_final)){
                $(this).removeClass('preco-filtrado');
            } else {
                $(this).addClass('preco-filtrado');
            }
        }
    });

    if(!acessando) adicionaVariavelUrl();

}

function filtroMarca(){
    
    var marcas_ativas   = [];
    var marcas_inativas = [];
    
    $(".produto-filtro-marca").each(function(){
        if($(this).hasClass('produto-filtro-ativo')){
            marcas_ativas.push($(this).attr("marca"));
        } else {
            marcas_inativas.push($(this).attr("marca"));
        }
    });

    if(marcas_ativas.length > 0){
        $(".produto").each(function(){
            if(marcas_ativas.includes($(this).attr("marca"))){
                $(this).removeClass('marca-filtrada');
            }
            if(marcas_inativas.includes($(this).attr("marca"))){
                $(this).addClass('marca-filtrada');
            }
        });
    } else {
        $(".produto").each(function(){
            $(this).removeClass('marca-filtrada');
        });
    }

    if(!acessando) adicionaVariavelUrl();

}

function filtroCaracteristica(){
            
    var caracteristicas_ativas = [];
    var produtos_visiveis      = [];
        
    $(".produto-filtro-caracteristica").each(function(){
        if($(this).hasClass('produto-filtro-ativo')){
            caracteristicas_ativas.push($(this).attr("caracteristica"));
        }
    });

    if(caracteristicas_ativas.length > 0){
        $(".produto").each(function(){
            var produto_caracteristicas = $(this).attr("caracteristicas").split(",");
            for(var pc=0; pc < produto_caracteristicas.length; pc++){
                if(caracteristicas_ativas.includes(produto_caracteristicas[pc])){
                    produtos_visiveis.push(parseInt($(this).attr("produto")));                    
                }
            }   
        });
        produtos_visiveis = produtos_visiveis.filter(function(este, i) {
            return produtos_visiveis.indexOf(este) === i;
        });
        $(".produto").each(function(){
            if(produtos_visiveis.includes(parseInt($(this).attr("produto")))){
                $(this).removeClass("caracteristica-filtrada");
            } else {
                $(this).addClass("caracteristica-filtrada");
            }
        });
    } else {
        $(".produto").each(function(){
            $(this).removeClass('caracteristica-filtrada');
        });
    }

    if(!acessando) adicionaVariavelUrl();

}

function filtroEstoque(){
    if(filtro_estoque){
        $(".produto").each(function(){
            if($(this).attr("estoque") == 0){
                $(this).addClass('produto-sem-estoque');
            }
        });
    } else {
        $(".produto").each(function(){
            if($(this).attr("estoque") == 0){
                $(this).removeClass('produto-sem-estoque');
            }
        });
    }

    if(!acessando) adicionaVariavelUrl();

}

function filtroPromocao(){
    if(filtro_promocao){
        $(".produto").each(function(){
            if($(this).attr("promocao") == 1){
                $(this).removeClass('produto-sem-promocao');
            }
        });
    } else {
        $(".produto").each(function(){
            if($(this).attr("promocao") == 1){
                $(this).addClass('produto-sem-promocao');
            }
        });
    }    
    
    if(!acessando) adicionaVariavelUrl();

}

$(".produto-filtro").change(function(){   
    var tipo_filtro = $(this).attr('tipo-filtro');
    if(tipo_filtro == 'preco'){ filtroPreco(); }
});

$(".produto-filtro").click(function(){
    var tipo_filtro = $(this).attr('tipo-filtro');

    if(tipo_filtro != 'preco')
        abreLoader();
        
    if(tipo_filtro != 'preco'){
        if($(this).hasClass('produto-filtro-ativo')){ $(this).removeClass('produto-filtro-ativo');
        } else { $(this).addClass('produto-filtro-ativo'); }    
    }

    if(tipo_filtro == 'categoria'){ filtroCategoria();
    } else if(tipo_filtro == 'marca'){ filtroMarca();
    } else if(tipo_filtro == 'caracteristica'){ filtroCaracteristica();
    } else if(tipo_filtro == 'estoque'){
        if(filtro_estoque){ filtro_estoque = false;
        } else { filtro_estoque = true; }
        filtroEstoque();
    } else if(tipo_filtro == 'promocao'){
        if(filtro_promocao){ filtro_promocao = false;
        } else { filtro_promocao = true; }
        filtroPromocao();
    }

});

function recolheHtmlProduto(){

    array_produtos = [];

    $(".produto").each(function(){

        var relevancia = $(this).attr('relevancia');
        var preco      = $(this).attr('preco');
        var nome       = $(this).attr('nome');
        var promocao   = $(this).attr('promocao');
        var html       = $(this).get(0).outerHTML;
        
        var produto = new Object();
        produto.relevancia = relevancia;
        produto.preco      = preco;
        produto.nome       = nome;
        produto.promocao   = promocao;
        produto.html       = html;

        array_produtos.push(produto);

    });
    
}

function compareValues(key, order = 'asc') {

    return function innerSort(a, b) {
        if (!a.hasOwnProperty(key) || !b.hasOwnProperty(key)) {
            return 0;
        }

        if(key == 'preco'){
            var varA = parseFloat(a[key]); 
            var varB = parseFloat(b[key]); 
        } else if(key == 'relevancia') {
            var varA = parseInt(a[key]); 
            var varB = parseInt(b[key]); 
        } else if(key == 'promocao') {
            var varA = parseInt(a[key]); 
            var varB = parseInt(b[key]); 
        } else if(key == 'nome') {
            var varA = (typeof a[key] === 'string')
            ? a[key].toUpperCase() : a[key];
            var varB = (typeof b[key] === 'string')
            ? b[key].toUpperCase() : b[key];
        }

        let comparison = 0;
        if (varA > varB) {
            comparison = 1;
        } else if (varA < varB) {
            comparison = -1;
        }
        return (
            (order === 'desc') ? (comparison * -1) : comparison
        );
    };
}

function filtroOrdenacao(){

    recolheHtmlProduto();
    $("#produtos-produtos .row").empty();
    
    var ordenar_por = $("#select-ordenar-por").val();

    if(ordenar_por == 'nome'){
        array_produtos.sort(compareValues('nome', 'asc'));
    } else if(ordenar_por == 'menor-preco'){
        array_produtos.sort(compareValues('preco', 'asc'));
    } else if(ordenar_por == 'maior-preco'){
        array_produtos.sort(compareValues('preco', 'desc'));
    } else if(ordenar_por == 'menor-relevancia'){
        array_produtos.sort(compareValues('relevancia', 'asc'));
    } else if(ordenar_por == 'maior-relevancia'){
        array_produtos.sort(compareValues('relevancia', 'desc'));
    } else if(ordenar_por == 'promocao'){
        array_produtos.sort(compareValues('promocao', 'desc'));
    }

    for(var i = 0; i<array_produtos.length; i++){
        $("#produtos-produtos .row").append(array_produtos[i]['html']);
    }

    if(!acessando) adicionaVariavelUrl();

}

$("#select-ordenar-por").change(function(){
    filtroOrdenacao();
});

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

    } else {
        filtroEstoque();
        filtroPromocao();
        filtroPreco();
        filtroMarca();
        filtroCaracteristica();
        filtroCategoria();
        filtroOrdenacao();
        adicionaVariavelUrl();
        acessando = false;
        fechaLoader();
    }

    $(".produto-container-valor-esgotado").click(function(e){
        e.stopPropagation();
    });

});

function adicionaVariavelUrl(){

    var promocao               = '';
    var estoque                = '';
    var preco_minimo           = '&preco-minimo='+$("#input-preco-inicial").val();
    var preco_maximo           = '&preco-maximo='+$("#input-preco-final").val();
    var marcas                 = '';
    var marcas_titulo          = '&marcas=';
    var caracteristicas        = '';
    var caracteristicas_titulo = '&caracteristicas=';
    var categorias             = '';
    var categorias_titulo      = '&categorias=';
    var ordenacao              = '&ordenacao='+$("#select-ordenar-por").val();
    var pesquisa               = '&pesquisa='+$("#filtro-pesquisa").val();

    if($("#filtro-promocao").hasClass("produto-filtro-ativo")){ promocao = '?promocao=s';
    } else { promocao = '?promocao=n'; }

    if($("#filtro-estoque").hasClass("produto-filtro-ativo")){ estoque = '&estoque=s';
    } else { estoque = '&estoque=n'; }
    
    $(".produto-filtro-marca").each(function(){
        if($(this).hasClass('produto-filtro-ativo')){
            marcas += $(this).attr("marca")+',';
        }
    });
    marcas = marcas.slice(0, -1);
        
    $(".produto-filtro-caracteristica").each(function(){
        if($(this).hasClass('produto-filtro-ativo')){
            caracteristicas += $(this).attr("caracteristica")+',';
        }
    });
    caracteristicas = caracteristicas.slice(0, -1);

    $(".produto-filtro-categoria").each(function(){
        if($(this).hasClass('produto-filtro-ativo')){
            categorias += $(this).attr("categoria")+',';
        }
    });
    categorias = categorias.slice(0, -1);

    var stateObj = { foo: "bar" };
    history.pushState(stateObj, "produto", "produtos.php"+promocao+estoque+preco_minimo+preco_maximo+caracteristicas_titulo+caracteristicas+categorias_titulo+categorias+ordenacao+pesquisa+marcas_titulo+marcas);

    fechaLoader();

    $(".produto-container-valor-esgotado").click(function(e){
        e.stopPropagation();
    });

}


$("#produtos #produtos-filtros-titulo").click(function(){

    if($("#produtos #produtos-filtros").css('display') == 'none'){
        $("#produtos #produtos-filtros").slideDown();
        $("#produtos #produtos-filtros-titulo img").css('transform',"rotate(90deg)");
    } else {
        $("#produtos #produtos-filtros").slideUp();
        $("#produtos #produtos-filtros-titulo img").css('transform',"rotate(-90deg)");
    }

});

/*
function checkboxCategorias(categorias,checked,categoria){
    if(checked){
        $(".produto-filtro-categoria").each(function(){         
            if(categorias.includes(parseInt($(this).attr("categoria")))){
                $(this).prop("checked",true);
            }
        });
    } else {
        $(".produto-filtro-categoria").each(function(){       
            var categorias_aux1 = [];  
            var categorias_aux2 = $(this).val().split(',');
            for (var i = 0; i < categorias_aux2.length; i++) {
                categorias_aux1.push(parseInt(categorias_aux2[i]));
            }
            if(categorias_aux1.includes(parseInt(categoria))){
                $(this).prop("checked",false);
            }
        });
    }
}*/