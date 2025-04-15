//MARCA A PÁGINA ATUAL COMO ATIVA
var url  = window.location.href; 
var url_fixa = url.split("/")[url.split("/").length -1];
var url_variavel = url_fixa.split("?")[url_fixa.split("?").length -2];

$(window).bind("scroll", function(){

    if(url_fixa != 'carrinho' & url_fixa != 'carrinho-login' & url_fixa != 'carrinho-frete' & url_fixa != 'carrinho-pagamento' & url_fixa != 'carrinho-confirmacao'){

        var scroll = $(this).scrollTop();

        if(scroll >= 100){
            $("#menu #menu-carrinho-lista").addClass("carrinho-suspenso");
        } else {
            $("#menu #menu-carrinho-lista").removeClass("carrinho-suspenso");
        }

    }

});


function organizaPesquisa(pesquisa){    
    var pesquisa_auxiliar = pesquisa.split(' ');
    var palavras_pesquisa = [];
    for(var i = 0; i < pesquisa_auxiliar.length; i++){
        var palavra = pesquisa_auxiliar[i].normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/([^0-9a-zA-Z])/g, '');
        if(palavra.length > 2){
            palavras_pesquisa.push(palavra);
        }
    }
    return palavras_pesquisa.join('-');    
}

function abreFechaformPesquisa(){

    var status_form = $("#status-form-pesquisa-produto").val();

    if(status_form == 'fechado'){
        $("#status-form-pesquisa-produto").val('aberto');
        $("#menu #menu-opcoes li:last-of-type").css("margin-top","-5px");
        $("#form-pesquisar-produto").css("display","inline");
        $("#menu-opcoes-input-pesquisar").focus();
    } else if(status_form == 'aberto'){
        if($("#menu-opcoes-input-pesquisar").val() != ''){
            $("#form-pesquisar-produto").submit();
        } else {
            $("#menu #menu-opcoes li:last-of-type").css("margin-top","-3px");
            $("#form-pesquisar-produto").css("display","none");
        }
        $("#status-form-pesquisa-produto").val('fechado');
    }
}

document.addEventListener("click", function(e){ 
    if(e.target.id != "menu-opcoes-input-pesquisar" & e.target.id != "menu-opcoes-img-pesquisar" & $("#menu-opcoes-input-pesquisar").css("display") != "none"){
        $("#status-form-pesquisa-produto").val('fechado');
        $("#menu #menu-opcoes li:last-of-type").css("margin-top","-3px");
        $("#form-pesquisar-produto").css("display","none");
    }
});

function submitFormPesquisaMobile(){
    if($("#menu-mobile-opcoes-input-pesquisar").val() != ''){
        $('#form-mobile-pesquisar-produto').submit();
    }
}

//ABRE E FECHA O MENU MOBILE NEW
$("#menu-icone").click(function(e){
    $(window).scrollTop(0);
    if($("#menu-icone").hasClass("fechado")){
        $("#body-site").css("overflow","hidden");
        $("#menu-hamburguer").attr("checked",true);
        $("#menu-mobile-new").slideDown('fast').animate(
            { duration: '300' }
        );
        $("#menu-icone").removeClass("fechado");
        $("#menu-icone").addClass("aberto");
    } else if($("#menu-icone").hasClass("aberto")) {
        $("#body-site").css("overflow","unset");
        $("#menu-hamburguer").attr("checked",false);
        $("#menu-mobile-new").slideUp('fast').animate(
            { duration: '300' }
        );
        $("#menu-icone").removeClass("aberto");
        $("#menu-icone").addClass("fechado");
    }
});

function buscaCategorias(){
    $.ajax({
      url: $("#site").val()+"modulos/menu/php/busca-categorias.php",
      type: "POST",
      dataType: "json",
      success: function (categorias){
        $('#arvore-categorias').tree({
          data: categorias,
          selectable: false,
          animationSpeed: 100,
          closedIcon:'+',
          openedIcon:'-',
          buttonLeft:false,
          buttonRight:true
        }); 
        $('#arvore-categorias').on('tree.click', function(event) {
            var node = event.node;
            $("#menu-icone").trigger("click");
            window.location.href = node.link;
        });
      }
    }); 
}

buscaCategorias();