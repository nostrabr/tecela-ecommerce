function abreLoader(){
    $("#loader .mensagens .img-erro").removeAttr("style");
    $("#loader .mensagens .img-aviso").removeAttr("style");
    $("#loader .mensagens .img-sucesso").removeAttr("style");
    $("#loader .mensagens .texto").empty();
    $("#loader .wrapper").show();
    $("#loader").show();
}
function fechaLoader(){
    $("#loader").hide();
    $("#loader .mensagens .img-erro").removeAttr("style");
    $("#loader .mensagens .img-aviso").removeAttr("style");
    $("#loader .mensagens .img-sucesso").removeAttr("style");
    $("#loader .mensagens .texto").empty();
    $("#loader .wrapper").show();
}
function abreLoaderElemento(elemento,gif,max_width){
    $(elemento).append('<img class="elemento-temporario" style="max-width: '+max_width+';" src="'+$('#site').val()+'imagens/'+gif+'">');
}
function fechaLoaderElemento(elemento){
    $(elemento+' .elemento-temporario').remove();
}
function mensagemAviso(tipo, texto, tempo){    
    $("#loader .wrapper").hide();
    if(tipo == "erro"){
        $("#loader .mensagens .img-erro").css("display","block");
    } else if(tipo == "aviso"){
        $("#loader .mensagens .img-aviso").css("display","block");
    } else if(tipo == "sucesso"){
        $("#loader .mensagens .img-sucesso").css("display","block");
    }
    $("#loader .mensagens .texto").html(texto);
    $("#loader").show();
    setTimeout(() => {
        fechaLoader();
    }, tempo);
}
$("#loader").click(function(){    
    if($("#loader .mensagens .texto").html() != ''){
        fechaLoader();
    }
});