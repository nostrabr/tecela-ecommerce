//MARCA A PÁGINA ATUAL COMO ATIVA
var url  = window.location.href; 
var url_fixa = url.split("/")[url.split("/").length -1];
var url_variavel = url_fixa.split("?")[url_fixa.split("?").length -2];

if(url_variavel != undefined){
    var pagina = url_variavel.replace('.php','');
    if(pagina.indexOf("-") != -1){
        pagina = pagina.substr(0,pagina.indexOf("-"));
    }
    $("li[data-active='"+pagina+"']").addClass('ativo');
} else {
    var pagina = url_fixa.replace('.php','');
    if(pagina.indexOf("-") != -1){
        pagina = pagina.substr(0,pagina.indexOf("-"));
    }
    $("li[data-active='"+pagina+"']").addClass('ativo');
}

//ABRE E FECHA O MENU MOBILE
$("#menu-icone").click(function(e){
    if($("#menu-icone").hasClass("fechado")){
        $("#menu-hamburguer").attr("checked",true);
        $("#menu").css('left','0px').css('width','100vw');
        $("#menu-rodape").css('left','25px');
        $("#menu-icone").removeClass("fechado");
        $("#menu-icone").addClass("aberto");
    } else if($("#menu-icone").hasClass("aberto")) {
        $("#menu-hamburguer").attr("checked",false);
        $("#menu").removeAttr("style");
        $("#menu-rodape").removeAttr("style");
        $("#menu-icone").removeClass("aberto");
        $("#menu-icone").addClass("fechado");
    }
});