$(".miniatura").hover(function(){

    $(".miniatura").removeClass("miniatura-ativa");
    $(this).addClass("miniatura-ativa");
    
    var imagem = $(this).attr("url_imagem");
    
    $("#imagem-grande").attr("src", imagem);
    
});

$("#galeria").click(function(){

    var miniatura_ativa = $(".miniatura-ativa").attr("index");

    $(".carousel-indicator").each(function(){
        if($(this).attr("index") == miniatura_ativa){
            $(this).addClass("active");
        } else {
            $(this).removeClass("active");
        }
    });

    $(".carousel-item").each(function(){
        if($(this).attr("index") == miniatura_ativa){
            $(this).addClass("active");
        } else {
            $(this).removeClass("active");
        }
    });
    $("#modal-imagens-produto").modal("show");

});

$(".produto #miniaturas .ultima-miniatura").click(function(){

    $(".carousel-indicator").each(function(){
        if($(this).attr("index") == 5){
            $(this).addClass("active");
        } else {
            $(this).removeClass("active");
        }
    });
    
    $(".carousel-item").each(function(){
        if($(this).attr("index") == 5){
            $(this).addClass("active");
        } else {
            $(this).removeClass("active");
        }
    });

    $("#modal-imagens-produto").modal("show");

});

$(".produto .imagens-seta-esquerda").click(function(){

    var miniatura_ativa = $(".miniatura-ativa").attr("index");    
    var total_miniaturas = parseInt($("#total_miniaturas").val()-1);    

    if(miniatura_ativa != 0){
        var prev_miniatura = parseInt(miniatura_ativa)-1;
        $(".miniatura").each(function(){
            if($(this).attr("index") == prev_miniatura){
                $(this).addClass("miniatura-ativa");    
                var imagem = $(this).attr("url_imagem");
                $("#imagem-grande").attr("src", imagem);
            } else {
                $(this).removeClass("miniatura-ativa");
            }
        });
    } else {
        next_miniatura = total_miniaturas;
        $(".miniatura").each(function(){
            if($(this).attr("index") == next_miniatura){
                $(this).addClass("miniatura-ativa");   
                var imagem = $(this).attr("url_imagem");
                $("#imagem-grande").attr("src", imagem);
            } else {
                $(this).removeClass("miniatura-ativa");
            }
        });
    }

});

$(".produto .imagens-seta-direita").click(function(){

    var miniatura_ativa = $(".miniatura-ativa").attr("index");
    var next_miniatura = parseInt(miniatura_ativa)+1;
    var total_miniaturas = parseInt($("#total_miniaturas").val()-1); 
       
    if(miniatura_ativa < total_miniaturas){
        $(".miniatura").each(function(){
            if($(this).attr("index") == next_miniatura){
                $(this).addClass("miniatura-ativa");     
                var imagem = $(this).attr("url_imagem");
                $("#imagem-grande").attr("src", imagem);
            } else {
                $(this).removeClass("miniatura-ativa");
            }
        });
    } else {
        next_miniatura = 0;
        $(".miniatura").each(function(){
            if($(this).attr("index") == next_miniatura){
                $(this).addClass("miniatura-ativa");  
                var imagem = $(this).attr("url_imagem");
                $("#imagem-grande").attr("src", imagem);
            } else {
                $(this).removeClass("miniatura-ativa");
            }
        });
    }

});

$(".caracteristica").click(function(){

    if(!$(this).hasClass('caracteristica-sem-estoque')){
        $("#produto-quantidade-input").removeAttr('max');
    }
        
    if($(this).hasClass('caracteristica-1')){
        $(".caracteristica-2").each(function(){
            $(this).removeClass("caracteristica-ativa");
        });
    }

    if(!$(this).hasClass('caracteristica-sem-estoque')){
        var numero_atributo = $(this).attr("numero-atributo");
        $(".caracteristica-"+numero_atributo).each(function(){
            $(this).removeClass("caracteristica-ativa");
        });
        $(this).addClass("caracteristica-ativa");        
    }
    
    if($(this).attr('numero-atributo') == 1){

        $("#produto-quantidade-input").val(1);
        var id_atributo = $(this).attr('id-caracteristica');
        var id_produto = $("#produto-id").val();
                
        $.ajax({
            url: "../../../modulos/produto/php/busca-estoque.php",
            type: "POST",
            dataType: "json",
            data: {"id-atributo": id_atributo, "id-produto": id_produto},
            success: function (variacoes){   
                if(variacoes[0].status == 'ERRO'){
                    mensagemAviso('erro', 'Erro ao buscar variantes do produto. Se o problema persistir, contate o administrador do sistema.', 3000);
                } else {     
                    
                    var contador_caracteristicas_secundarias = 0;

                    for(var i = 0; i < variacoes.length; i++){
                        $(".caracteristica-2").each(function(e,x){
                            contador_caracteristicas_secundarias++;
                            if($(this).attr("id-caracteristica") == variacoes[i]['secundaria']){
                                $(this).css("order",variacoes[i].ordem);
                                if(variacoes[i]['estoque'] <= 0){
                                    $(this).addClass("caracteristica-sem-estoque").attr("estoque",variacoes[i]['estoque']);
                                } else {
                                    $(this).removeClass("caracteristica-sem-estoque").attr("estoque",variacoes[i]['estoque']);
                                }
                                if(variacoes[i]['status_v'] == 0){
                                    $(this).addClass("caracteristica-desativada").attr("estoque",0);
                                } else {
                                    $(this).removeClass("caracteristica-desativada");
                                }
                            }
                        });
                    }

                    if(contador_caracteristicas_secundarias == 0){
                        $("#produto-quantidade-input").attr("max",variacoes[0]['estoque']).val(1);
                    }

                    $(".produto-atributo-2").css('display','block');
                    fechaLoader();
                                                
                }
            },
            beforeSend: function() {
                abreLoader();
            }
        });
    } else {   
        if(!$(this).hasClass('caracteristica-sem-estoque')){
            $("#produto-quantidade-input").attr("max",$(this).attr('estoque')).val(1);
        }
    }

});

function validaAtributos(){

    var total_atributos = parseInt($("#total-atributos").val());
    var array_atributos = [];

    if(total_atributos > 0){

        for(var i=1; i<=total_atributos; i++){        
            $(".caracteristica-"+i).each(function(){            
                if($(this).hasClass('caracteristica-ativa')){
                    array_atributos.push(i);
                }
            });       
        }
        
        if(total_atributos == array_atributos.length){
            return true;
        } else {
            return false;
        }

    } else {
        return true;
    }

}

function validaAtributosSelect(){

    var lista_sem_selecao = false;

    $(".produto-caracteristicas-lista").each(function(){
        if($(this).val() == null){
            lista_sem_selecao = true;
        }
    });

    if(lista_sem_selecao){
        return false;
    } else {
        return true;
    }

}

function validaQuantidade(){

    var quantidade_selecionada = parseInt($("#produto-quantidade-input").val());
    var quantidade_maxima      = parseInt($("#produto-quantidade-input").attr('max'));
    if(quantidade_selecionada <= 0){
        return false;
    } else if(quantidade_selecionada > quantidade_maxima) {
        return false;
    }
    return true;
    
}

function adicionarCarrinho(funcao){

    if(validaAtributos() & validaAtributosSelect() & validaQuantidade()){

        var quantidade    = $("#produto-quantidade-input").val();
        var identificador = $("#identificador").val();   
        var atributos     = [];     
        
        $(".caracteristica").each(function(){
            if($(this).hasClass('caracteristica-ativa')){
                atributos.push($(this).attr("value"));
            }
        });        

        $(".produto-caracteristicas-lista").each(function(){
            atributos.push($(this).val());
        });
        
        $.ajax({
            url: "../../../modulos/carrinho/php/adicionar.php",
            type: "POST",
            dataType: "json",
            data: {"quantidade": quantidade, "atributos": JSON.stringify(atributos), "identificador": identificador},
            success: function (retorno){
                if(retorno[0].status == 'ERRO'){
                    mensagemAviso('erro', 'Erro ao adicionar produto ao carrinho. Se o problema persistir, contate o administrador do sistema.', 3000);
                } else {                
                    $('.circle-loader').toggleClass('load-complete');
                    $('.checkmark').toggle();
                    if(funcao == 'adicionar'){     
                        setTimeout(function(){
                            $("#menu-carrinho-quantidade").html(retorno[0].itens);
                            $("#menu-carrinho-valor").html(retorno[0].preco);   
                            $('.circle-loader').toggleClass('load-complete');
                            $('.checkmark').toggle();   
                            fechaLoader();
                        }, 1000);                     
                    } else if(funcao == 'comprar'){  
                        window.location.href = '../../../carrinho';  
                    }
                }
            },
            beforeSend: function() {
                abreLoader();
            }
        });

    } else {
        if(!validaAtributos()){
            mensagemAviso('erro', 'Selecione o modelo do produto para adiciona-lo ao carrinho.', 3000);
        } else if(!validaAtributosSelect()){
            mensagemAviso('erro', 'Selecione o modelo do produto para adiciona-lo ao carrinho.', 3000);
        } else if(!validaQuantidade()){
            mensagemAviso('erro', 'A quantidade selecionada não consta em estoque. Selecione uma quantidade menor ou igual a '+$("#produto-quantidade-input").attr('max'), 3000);
        }
    }

}

function buscaFrete(){
    var cep = $("#cep").val();
    if(cep.length == 10){        
        localStorage.setItem("cep-global",cep);
        var id_produto = $("#produto-id").val();
        var quantidade = $("#produto-quantidade-input").val();
        if(quantidade > 0){
            calculaFreteIndividual(id_produto,quantidade,cep,'#produto-resultado-frete');
        }
    }
}

/*
$("#cep").keyup(function(){
    buscaFrete();
});
*/
$("#produto-quantidade-input").change(function(){
    buscaFrete();
});

$(document).ready(function(){

    var id_produto          = $("#produto-id").val();
    var vistos_recentemente = localStorage.getItem("produtos-vistos-recentemente");

    if(vistos_recentemente != '' & vistos_recentemente != null){            
        localStorage.setItem("produtos-vistos-recentemente", vistos_recentemente+','+id_produto);
    } else {
        localStorage.setItem("produtos-vistos-recentemente", id_produto);
    }

    if($("[numero-atributo=1]").length == 1){
        $("[numero-atributo=1]").trigger("click");
    }

    var cep_global = localStorage.getItem("cep-global");

    if(cep_global != ''){            
        $("#cep").val(cep_global);
        buscaFrete();
    }

});