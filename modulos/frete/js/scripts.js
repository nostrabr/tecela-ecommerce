var buscando_frete = false;

function calculaFreteIndividual(id_produto,quantidade,cep,destino){
    
    if(!buscando_frete){
        
        var site = $("#site").val();

        $.ajax({
            url: site+"modulos/frete/melhor-envio/consulta-frete-produto.php",
            type: "POST",
            dataType: "json",
            data: {"id-produto": id_produto, "quantidade": quantidade, "cep": cep},
            success: function (retorno){
                if(retorno[0].status == 'ERRO'){
                    mensagemAviso('erro', retorno[0].descricao, 3000);
                } else {
                    if(destino == '#produto-resultado-frete'){
                        fechaLoaderElemento('#produto-resultado-frete');
                        var resultado_fretes = '<ul><li>Frete para:</li><li class="mb-1">'+retorno[0].cidade+'/'+retorno[0].uf+'</li><li>Valores:</li><li><table><tbody>'
                        var fretes = retorno[0].fretes;            
                        for(var i = 0; i < fretes.length; i++){
                            var prazo = fretes[i].prazo;
                            if(prazo != ''){ if(prazo == 1){ prazo = "1 dia"; } else { prazo = fretes[i].prazo+" dias"; } } else { prazo = ''; }
                            resultado_fretes += '<tr><td class="pr-2">'+fretes[i].empresa+' '+fretes[i].nome+'</td><td class="pr-2">'+fretes[i].preco+'</td><td>'+prazo+'</td></tr>'
                        }
                        resultado_fretes += '</tbody></table></li></ul>';
                    } else {
                        fechaLoader();
                    }
                    $(destino).html(resultado_fretes);
                }
                buscando_frete = false;
            },
            beforeSend: function() {
                if(destino == '#produto-resultado-frete'){
                    $("#produto-resultado-frete").empty();
                    abreLoaderElemento('#produto-resultado-frete','gif-terra.gif','100px');
                } else {
                    abreLoader();
                }
                buscando_frete = true;
            }
        });

    }

}

function calculaFreteCarrinho(cep,destino,destino_total){
    
    if(!buscando_frete){
        
        var site = $("#site").val();

        $.ajax({
            url: site+"modulos/frete/melhor-envio/consulta-frete-carrinho.php",
            type: "POST",
            dataType: "json",
            data: {"cep": cep},
            success: function (retorno){
                if(retorno[0].status == 'ERRO'){
                    mensagemAviso('erro', retorno[0].descricao, 3000);
                } else {  
                    var resultado_fretes = '';                  
                    if(destino == '#carrinho-resultado-frete'){
                        resultado_fretes = retorno[0].menor_valor_companhia+" "+retorno[0].menor_valor;
                        var fretes = retorno[0].fretes;
                        for(var i = 0; i < fretes.length; i++){
                            if(fretes[i].nome == 'Motoboy' | fretes[i].nome == 'Retirar'){
                                resultado_fretes += '<br>'+fretes[i].nome+" "+fretes[i].preco_brl;
                            }                            
                        }
                    } else if(destino == '#carrinho-frete-resultados'){
                        resultado_fretes = '<div class="col-12 mb-2">Destino: '+cep+' - '+retorno[0].cidade+'/'+retorno[0].uf+'</div>';
                        var fretes = retorno[0].fretes;
                        for(var i = 0; i < fretes.length; i++){
                            var prazo         = fretes[i].prazo;
                            var data_entrega  = fretes[i].data_entrega;
                            if(prazo != ''){ if(prazo == 1){ prazo = "1 dia"; } else { prazo = fretes[i].prazo+" dias"; } } else { prazo = ''; }
                            if(data_entrega != ''){ data_entrega = 'Previsão para: <b>'+data_entrega+'</b>'; } else { data_entrega = ''; }
                            if(i === 0){ var destino_ativo = 'carrinho-frete-resultado-ativo'; } else { var destino_ativo = ''; }
                            if(fretes[i].empresa != ''){ var empresa_servico = fretes[i].empresa+' - '+fretes[i].nome; } else { var empresa_servico = fretes[i].nome; }
                            resultado_fretes += '<div class="col-12"><div class="carrinho-frete-resultado '+destino_ativo+'" preco="'+fretes[i].preco+'" tipo="'+fretes[i].nome+'" title="Selecionar esta opção de frete"><ul><li>'+empresa_servico+'</li><li style="font-size: 10px;">'+data_entrega+'</li><span></ul>'+fretes[i].preco_brl+'</span></div></div>'
                        }             
                    }
                    $(destino).html(resultado_fretes);
                    $(destino_total).html(retorno[0].valor_total);
                    if(destino == '#carrinho-frete-resultados'){ carrinhoFreteResultados(); }
                    if($("#erro_pagamento_pagseguro").val() == 'S'){
                        setTimeout(() => {
                            fechaLoader();
                        }, 5000);
                    } else {
                        fechaLoader();
                    }
                }
                buscando_frete = false;
                if (typeof myFunction === clicaTipoFrete()) { clicaTipoFrete(); }
            },
            beforeSend: function() {
                abreLoader();
                buscando_frete = true;
            }
        });

    }

}

function carrinhoFreteResultados(){               
    $('.carrinho-frete-resultado').click(function(){
        $('.carrinho-frete-resultado').each(function(){
            $(this).removeClass('carrinho-frete-resultado-ativo');
        });
        $(this).addClass('carrinho-frete-resultado-ativo');
        var valor_frete = $('.carrinho-frete-resultado-ativo').attr('preco');  
        $("#carrinho-frete-resumo-valor-total-valor-frete").html(parseFloat(valor_frete).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}));    
        $("#carrinho-frete-resumo-valor-total-valor-total").html((parseFloat(valor_frete)+parseFloat($("#valor-produtos").val())).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}))
        $("#tipo-frete").val($('.carrinho-frete-resultado-ativo').attr('tipo'));
        $("#valor-frete").val(valor_frete);
        if(!$("#modo_whatsapp").val()){
            aplicaCupom();
        }
    });
    var valor_frete = $('.carrinho-frete-resultado-ativo').attr('preco');
    $("#carrinho-frete-resumo-valor-total-valor-frete").html(parseFloat(valor_frete).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}));    
    $("#carrinho-frete-resumo-valor-total-valor-total").html((parseFloat(valor_frete)+parseFloat($("#valor-produtos").val())).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'})); 
    $("#tipo-frete").val($('.carrinho-frete-resultado-ativo').attr('tipo'));  
    $("#valor-frete").val(valor_frete);
}
