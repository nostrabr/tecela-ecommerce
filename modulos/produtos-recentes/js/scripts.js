
$(document).ready(function(){

    var produtos = localStorage.getItem("produtos-vistos-recentemente");

    if(produtos != '' & produtos != 'null'){

        $.ajax({
            url: "modulos/produtos-recentes/php/busca-produtos.php",
            type: "POST",
            data: {"produtos": produtos},
            success: function (retorno){                  
                if(retorno != ''){
                    $("#produtos-recentes .row").html(retorno);
                    $("#produtos-recentes").show();     
                    $(".produto-container-valor-esgotado").click(function(e){
                        e.stopPropagation();
                    });
                } else {
                    $("#produtos-recentes").hide();
                }                
            }
        });

    } else {
        $("#produtos-recentes").hide();
    }

});