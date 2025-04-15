/*$.ajax({
    url: "modulos/carrinho/php/alterar-quantidade.php",
    type: "POST",
    dataType: "json",
    data: {"quantidade": quantidade, "identificador": identificador},
    success: function (retorno){
        if(retorno[0].status == 'ERRO'){
            fechaLoader();
            mensagemAviso('erro', 'Erro ao alterar quantidade. Se o problema persistir, contate o administrador do sistema.', 3000);
        } else {    
            location.reload();
        }
    },
    beforeSend: function() { 
        abreLoader();
    }
});*/

function coloreEstrela(pergunta, estrela){
    if(estrela == 1){
        $("#avaliacao-loja-"+pergunta+" .estrela-1").addClass("img-dourada");
        $("#avaliacao-loja-"+pergunta+" .estrela-2, #avaliacao-loja-"+pergunta+" .estrela-3, #avaliacao-loja-"+pergunta+" .estrela-4, #avaliacao-loja-"+pergunta+" .estrela-5").removeClass("img-dourada");
    } else if(estrela == 2){
        $("#avaliacao-loja-"+pergunta+" .estrela-1, #avaliacao-loja-"+pergunta+" .estrela-2").addClass("img-dourada");
        $("#avaliacao-loja-"+pergunta+" .estrela-3, #avaliacao-loja-"+pergunta+" .estrela-4, #avaliacao-loja-"+pergunta+" .estrela-5").removeClass("img-dourada");
    } else if(estrela == 3){
        $("#avaliacao-loja-"+pergunta+" .estrela-1, #avaliacao-loja-"+pergunta+" .estrela-2, #avaliacao-loja-"+pergunta+" .estrela-3").addClass("img-dourada");
        $("#avaliacao-loja-"+pergunta+" .estrela-4, #avaliacao-loja-"+pergunta+" .estrela-5").removeClass("img-dourada");
    } else if(estrela == 4){
        $("#avaliacao-loja-"+pergunta+" .estrela-1, #avaliacao-loja-"+pergunta+" .estrela-2, #avaliacao-loja-"+pergunta+" .estrela-3, #avaliacao-loja-"+pergunta+" .estrela-4").addClass("img-dourada");
        $("#avaliacao-loja-"+pergunta+" .estrela-5").removeClass("img-dourada");
    } else if(estrela == 5){
        $("#avaliacao-loja-"+pergunta+" .estrela-1, #avaliacao-loja-"+pergunta+" .estrela-2, #avaliacao-loja-"+pergunta+" .estrela-3, #avaliacao-loja-"+pergunta+" .estrela-4, #avaliacao-loja-"+pergunta+" .estrela-5").addClass("img-dourada");
    } else {
        $("#avaliacao-loja-"+pergunta+" .estrela-1, #avaliacao-loja-"+pergunta+" .estrela-2, #avaliacao-loja-"+pergunta+" .estrela-3, #avaliacao-loja-"+pergunta+" .estrela-4, #avaliacao-loja-"+pergunta+" .estrela-5").removeClass("img-dourada");
    }
    $("#nota-"+pergunta).val(estrela);
}

var total_caracteres = 0;
$(document).on("input", "#observacoes-loja", function () {
    total_caracteres = $(this).val().length;    
    $("#observacoes-caracteres").html(total_caracteres);
});

function carregarMais(tipo, id){
    $.ajax({
        url: "modulos/avaliacao/php/busca-avaliacoes.php",
        type: "POST",
        dataType: "json",
        data: {"tipo": tipo, "id": id},
        success: function (retorno){
            if(retorno[0].status == 'ERRO'){
                fechaLoader();
            } else if(retorno[0].status == 'SUCESSO') {   
                if(tipo == 'L'){
                    $("#btn-carrega-mais-site").attr('href',"javascript: carregarMais('L',"+retorno[0].ultimo+");");
                    $("#container-avaliacoes-loja").append(retorno[0].html);
                    if(!retorno[0].tem){
                        $("#btn-carrega-mais-site").remove();
                    }
                } else if(tipo == 'P'){
                    $("#btn-carrega-mais-produtos").attr('href',"javascript: carregarMais('P',"+retorno[0].ultimo+");");
                    $("#container-avaliacoes-produto").append(retorno[0].html);
                    if(!retorno[0].tem){
                        $("#btn-carrega-mais-produtos").remove();
                    }
                }
                fechaLoader();
            } else if(retorno[0].status == 'ACABOU') {   
                if(tipo == 'L'){
                    $("#btn-carrega-mais-site").remove();
                } else if(tipo == 'P'){
                    $("#btn-carrega-mais-produtos").remove();
                }
                fechaLoader();
            }
        },
        beforeSend: function() { 
            abreLoader();
        }
    });
}

function carregarMaisProduto(id_produto, id){
    $.ajax({
        url: $("#site").val()+"modulos/avaliacao/php/busca-avaliacoes-produto.php",
        type: "POST",
        dataType: "json",
        data: {"id_produto": id_produto, "id": id},
        success: function (retorno){
            if(retorno[0].status == 'ERRO'){
                fechaLoader();
            } else if(retorno[0].status == 'SUCESSO') {   
                $("#btn-carrega-mais-produtos").attr('href',"javascript: carregarMaisProduto("+id_produto+","+retorno[0].ultimo+");");
                $("#container-avaliacoes-produto").append(retorno[0].html);
                if(!retorno[0].tem){
                    $("#btn-carrega-mais-produtos").remove();
                }
                fechaLoader();
            } else if(retorno[0].status == 'ACABOU') {   
                $("#btn-carrega-mais-produtos").remove();
                fechaLoader();
            }
        },
        beforeSend: function() { 
            abreLoader();
        }
    });
}