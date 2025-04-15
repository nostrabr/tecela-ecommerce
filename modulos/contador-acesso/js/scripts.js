//CONTA A VISITA
$.ajax({
    url: $("#site").val()+"modulos/contador-acesso/php/conta-acesso-visita.php",
    type: "POST",
    data: {'width': screen.width, 'height': screen.height}
});

//CONTA O ACESSO A UM PRODUTO
if($("#produto").length > 0){
    var id_produto = $("#produto-id").val();
    $.ajax({
        url: $("#site").val()+"modulos/contador-acesso/php/conta-acesso-pagina-produto.php",
        type: "POST",
        data: {"id-produto": id_produto}
    });
}

//CLICK NO BOTÃO DO WHATSAPP DO PRODUTO EM ESPECÍFICO
$(".produto-container-valor-esgotado").click(function(){
    var id_produto = $(this).closest('.produto-container').parent('div').attr('onclick');
    id_produto = id_produto.substring(id_produto.lastIndexOf('/')+1).replace(/[^0-9]/g,'');
    $.ajax({
        url: $("#site").val()+"modulos/contador-acesso/php/conta-acesso-whatsapp-produto.php",
        type: "POST",
        data: {"id-produto": id_produto}
    });
});

$("#produto-consultar-whatsapp").click(function(){
    var id_produto = $("#produto-id").val();
    $.ajax({
        url: $("#site").val()+"modulos/contador-acesso/php/conta-acesso-whatsapp-produto.php",
        type: "POST",
        data: {"id-produto": id_produto}
    });
});

//CLICK NO WHATSAPP FLUTUANTE
$("#whatsapp-flutuante-link").click(function(){
    $.ajax({
        url: $("#site").val()+"modulos/contador-acesso/php/conta-acesso-whatsapp-flutuante.php",
        type: "POST"
    });
});

//CLICK NO WHATSAPP DA PÁGINA DE CONTATO
$("#contato-btn-whatsapp").click(function(){
    $.ajax({
        url: $("#site").val()+"modulos/contador-acesso/php/conta-acesso-whatsapp-contato.php",
        type: "POST"
    });
});

//CLICK NO WHATSAPP DO FOOTER
$("#footer-btn-whats").click(function(){
    $.ajax({
        url: $("#site").val()+"modulos/contador-acesso/php/conta-acesso-whatsapp-footer.php",
        type: "POST"
    });
});