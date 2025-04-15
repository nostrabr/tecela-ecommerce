if($("#carrinho-login").length == 0 & $("#carrinho-pagamento-formas-asaas").length == 0){
    abreLoader();
}   

function buscaFrete(cep,destino,destino_total) {
    if(cep.length == 10){
        calculaFreteCarrinho(cep,destino,destino_total);
    }
}

function copiarTexto(id,msg){
    let textoCopiado = document.getElementById(id);
    textoCopiado.select();
    textoCopiado.setSelectionRange(0, 99999)
    document.execCommand("copy");
    $("#"+id).blur();
    mensagemAviso('sucesso', msg, 1000);
}

$(document).ready(function(){
    if($("#endereco").length > 0){   
        buscaFrete($("#endereco").val(),"#carrinho-resultado-frete",'#carrinho-resumo-valor-total');
        $("#carrinho-botoes-btn-avancar").focus();
    } 
    if($("#carrinho").length > 0){
        var cep_global = localStorage.getItem("cep-global");
        if($("#cep").length > 0){
            if($("#cep").val() != '' & $("#cep").val().length == 10){
                buscaFrete($("#cep").val(),"#carrinho-resultado-frete",'#carrinho-resumo-valor-total');
            } else {
                if(cep_global != null & $("#busca-automatica-cep").val() == 1){       
                    $("#cep").val(cep_global);
                    buscaFrete(cep_global,"#carrinho-resultado-frete",'#carrinho-resumo-valor-total');
                } else {
                    fechaLoader();
                }
            }
        } else {
            if($("#modo_whatsapp").val()){
                fechaLoader();
            }
        }
    }
    if($("#carrinho-vazio").length > 0){
        fechaLoader();
    }
    if($("#carrinho-frete").length > 0){        
        if($(".carrinho-frete-endereco").length > 0){
            var cep = $("#cep").val();
            buscaFrete(cep,"#carrinho-frete-resultados",'#carrinho-frete-resumo-valor-total');
            if($("#erro_pagamento_pagseguro").val() == 'S'){
                mensagemAviso('erro', 'Ops! O PagSeguro teve problemas em processar o seu boleto. Tente novamente.', 5000);
            }
            $(".carrinho-frete-endereco").each(function(){
                if($(this).attr('cep') == cep){
                    $(this).addClass('carrinho-frete-endereco-ativo');
                    $(this).find('.row').append('<div class="carrinho-frete-endereco-label-ativo">SELECIONADO</div>');
                    return false;
                }
            });  
            if(!$("#modo_whatsapp").val()){
                aplicaCupom();
            }
        } else {
            fechaLoader();
        }
    }
    if($("#carrinho-confirmacao").length > 0){        
        fechaLoader();
    }
});

$("#endereco").change(function(){
    buscaFrete($("#endereco").val(),"#carrinho-resultado-frete",'#carrinho-resumo-valor-total');
});

$("#cep").keyup(function(){
    buscaFrete($(this).val(),"#carrinho-resultado-frete",'#carrinho-resumo-valor-total');
    if($(this).val().length == 10){
        localStorage.setItem("cep-global",$(this).val());
    }
});

function alteraQuantidade(identificador,quantidade){

    var quantidade = parseInt(quantidade);
    var quantidade_max = $("#carrinho-produto-quantidade-"+identificador).attr("max");
    
    if(quantidade <= 0){
        $("#carrinho-produto-quantidade-"+identificador).val(1);
    } else {

        if(quantidade > quantidade_max)
            quantidade = quantidade_max;

        $.ajax({
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
        });
        
    }

}

function excluiProduto(identificador){
    
    $.ajax({
        url: "modulos/carrinho/php/excluir.php",
        type: "POST",
        dataType: "json",
        data: {"identificador": identificador},
        success: function (retorno){
            if(retorno[0].status == 'ERRO'){
                fechaLoader();
                mensagemAviso('erro', 'Erro ao alterar quantidade. Se o problema persistir, contate o administrador do sistema.', 3000);
            } else {    
                location.reload();
            }
        }
    });

}

function aplicaCupom(){

    var cupom = $("#carrinho-frete-input-cupom").val();

    if(cupom != ''){
    
        $.ajax({    
            url: "modulos/carrinho/php/aplicar-cupom.php",
            type: "POST",
            dataType: "json",
            data: {"cupom": cupom},
            success: function (retorno){
                fechaLoader();
                if(retorno[0].status == 'ERRO'){
                    $("#carrinho-frete-small-cupom-aplicado").hide();
                    mensagemAviso('erro', retorno[0].mensagem, 3000);
                } else {    

                    var valor_produtos = parseFloat($("#valor-produtos").val());
                    var valor_frete    = parseFloat($("#valor-frete").val());
                    var valor_compra   = parseFloat(valor_produtos+valor_frete);
                    var valor_desconto = parseFloat(retorno[0].valor);
                    var resultado      = 0;
                    var desconto       = 0;

                    if(retorno[0].tipo == 'V'){
                        desconto  = valor_desconto;
                        resultado = valor_produtos-desconto;
                    } else if(retorno[0].tipo == 'P'){
                        desconto  = valor_produtos*valor_desconto/100;
                        resultado = valor_produtos-desconto;
                    }                   

                    if(resultado < 0) {
                        desconto  = valor_produtos-1;
                        resultado = 1; 
                    }
                    resultado            = resultado+valor_frete;

                    var desconto_final    = desconto.toFixed(2);
                    var valor_final       = resultado.toFixed(2);
                    var desconto_final_rs = parseFloat(desconto_final).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
                    var valor_final_rs    = parseFloat(valor_final).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});

                    $("#cupom-desconto").val(retorno[0].identificador);
                    $("#valor-desconto").val(desconto_final);
                    $("#carrinho-frete-resumo-valor-total-desconto").css('display','flex');
                    $("#carrinho-frete-resumo-valor-total-valor-desconto").html(desconto_final_rs);
                    $("#carrinho-frete-resumo-valor-total-valor-total").html(valor_final_rs);
                    $("#carrinho-frete-small-cupom-aplicado").show();

                }
            },
            beforeSend: function() { 
                abreLoader();
            }
        });

    }

}

function proximoPassoCarrinho(){
    if($("#endereco").length > 0){  
        $.ajax({
            url: "modulos/session/php/set-cep.php",
            type: "POST",
            data: {"cep": $("#endereco").val()},
            success: function (retorno){
                if($("#logado").val() == 'true'){
                    window.location.href = 'carrinho-frete';
                }
            }
        });
    } else if($("#cep").length > 0){  
        $.ajax({
            url: "modulos/session/php/set-cep.php",
            type: "POST",
            data: {"cep": $("#cep").val()},
            success: function (retorno){
                if($("#logado").val() == 'true'){
                    window.location.href = 'carrinho-frete';
                } else if ($("#logado").val() == 'false'){
                    window.location.href = 'carrinho-login';
                }
            }
        });
    } else if($("#modo_whatsapp").val()){
        if($("#logado").val() == 'true'){
            window.location.href = 'carrinho-frete';
        } else if ($("#logado").val() == 'false'){
            window.location.href = 'carrinho-login';
        }
    }
}

function proximoPassoPagamento(){    
    if($("#endereco").val() != '' & $("#frete").val() != '' & $("#valor-frete").val() != ''){
        $("#form-carrinho-frete").submit();
    } else {
        mensagemAviso('erro', 'Selecione o endereço de entrega e uma opção de envio para prosseguir.', 3000);
    } 
}

function proximoPassoCadastro(){    
    $.ajax({
        url: "modulos/session/php/set-retorno-cadastro.php",
        type: "POST",
        data: {"retorno": "carrinho-login"},
        success: function (){
            window.location.href = 'cliente-cadastro';
        }
    });
}

function setaRetornoFrete(funcao, identificador){    
    $.ajax({
        url: "modulos/session/php/set-retorno-frete.php",
        type: "POST",
        data: {"retorno": "carrinho-frete"},
        success: function (data){
            if(funcao == 'cadastro'){
                if($("#enderecos").val() == 0){
                    $.ajax({
                        url: "modulos/session/php/set-cep.php",
                        type: "POST",
                        data: {"cep": $("#cep").val()},
                        success: function (retorno){
                            window.location.href = 'cliente-enderecos-cadastro';
                        }
                    });
                } else {
                    window.location.href = 'cliente-enderecos-cadastro';
                }
            } else if(funcao == 'edicao'){
                window.location.href = 'cliente-enderecos-edicao/'+identificador;
            }
        }
    });
}

$(".carrinho-frete-endereco-btn-editar").click(function(e){
    e.stopPropagation();
});

function selecionarEndereco(identificador){
    
    $('.carrinho-frete-endereco').each(function(){
        $(this).removeClass('carrinho-frete-endereco-ativo');
        $(this).find(".carrinho-frete-endereco-label-ativo").remove();
    });
    $("#carrinho-frete-endereco-"+identificador).addClass('carrinho-frete-endereco-ativo');
    $("#carrinho-frete-endereco-"+identificador+" .row").append('<div class="carrinho-frete-endereco-label-ativo">SELECIONADO</div>');
    var cep = $("#carrinho-frete-endereco-"+identificador).attr('cep');
    $("#cep").val(cep);
    buscaFrete(cep,"#carrinho-frete-resultados",'#carrinho-frete-resumo-valor-total');
    $("#endereco").val(identificador);
    if(!$("#modo_whatsapp").val()){
        aplicaCupom();
    }

}

function calculaDescontoTipoPagamento(){
    
    if($("#desconto-pix").length > 0){

        var tipo = $('.carrinho-pagamento-forma-pagamento-ativo').attr('tipo');
        var valor = '';
        var tipo_calculo = '';
        var tipo_desconto = '';
        if(tipo == 'pix'){
            tipo_desconto = 'Desconto no Pix:';
            valor         = $("#desconto-pix").val();
            tipo_calculo  = $("#desconto-pix").attr('tipo');
        } else if(tipo == 'boleto'){
            tipo_desconto = 'Desconto no Boleto:';
            valor         = $("#desconto-boleto").val();
            tipo_calculo  = $("#desconto-boleto").attr('tipo');
        } else if(tipo == 'cartao'){
            tipo_desconto = 'Desconto no Cartão:';
            valor         = $("#desconto-cartao").val();
            tipo_calculo  = $("#desconto-cartao").attr('tipo');
        } 

        if(tipo_desconto != ''){
            var valor_desconto;
            var valor_total;
            var valor_produtos = parseFloat($("#valor-produtos").val());
            var valor_frete    = parseFloat($("#valor-frete").val());
            if(tipo_calculo == 'V'){
                $("#valor-desconto").val(valor);
                valor_desconto = parseFloat(valor).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
                valor_total    = valor_produtos - valor;
                if(valor_total < 0){ valor_total = 0; }
                valor_total    = valor_total + valor_frete;
                $("#valor-compra").val(valor_total.toFixed(2));
                valor_total    = parseFloat(valor_total).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
                $("#carrinho-frete-resumo-valor-total-valor-desconto").html(valor_desconto);
                $("#carrinho-frete-resumo-valor-total-tipo-desconto").html(tipo_desconto);
                $("#carrinho-frete-resumo-valor-total-valor-total").html(valor_total);
                $("#valor-desconto").val();
            } else if(tipo_calculo == 'P') {
                valor_desconto = valor_produtos * valor / 100;
                valor_total    = valor_produtos - valor_desconto;
                $("#valor-desconto").val(valor_desconto);
                valor_desconto = parseFloat(valor_desconto).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
                if(valor_total < 0){ valor_total = 0; }
                valor_total    = valor_total + valor_frete;
                $("#valor-compra").val(valor_total.toFixed(2));
                valor_total    = parseFloat(valor_total).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
                $("#carrinho-frete-resumo-valor-total-valor-desconto").html(valor_desconto);
                $("#carrinho-frete-resumo-valor-total-tipo-desconto").html(tipo_desconto);
                $("#carrinho-frete-resumo-valor-total-valor-total").html(valor_total);
            } else {           
                valor_total    = valor_produtos + valor_frete;
                $("#valor-compra").val(valor_total.toFixed(2));
                valor_total    = parseFloat(valor_total).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
                $("#carrinho-frete-resumo-valor-total-valor-desconto").html('');
                $("#carrinho-frete-resumo-valor-total-tipo-desconto").html('');
                $("#carrinho-frete-resumo-valor-total-valor-total").html(valor_total);
                $("#valor-desconto").val('');
            }
            
        }

    }
    
}

function calculaValorParcelas(){
    if($("#parcelas").val() != null){
        var n_parcelas                = parseInt($("#parcelas").val());
        var valor_parcela             = parseFloat($("#parcelas").find('option:selected').attr('data-valor-parcela'));
        var valor_compra              = parseFloat($("#valor-compra").val());   
        var valor_compra_rs           = parseFloat(valor_compra).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});  
        var juros                     = (valor_parcela*n_parcelas)-valor_compra;
        if(juros < 0.03){ juros = 0; }
        var juros_rs                  = parseFloat(juros).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
        var valor_compra_com_juros    = valor_compra+juros;
        var valor_compra_com_juros_rs = parseFloat(valor_compra_com_juros).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
        if(juros > 0){
            $("#carrinho-frete-resumo-valor-total-juros").css("display","flex");
            $("#carrinho-frete-resumo-valor-total-valor-juros").html(juros_rs);  
        } else {
            $("#carrinho-frete-resumo-valor-total-juros").css("display","none");
            $("#carrinho-frete-resumo-valor-total-valor-juros").html('');  
        }
        $("#carrinho-frete-resumo-valor-total-valor-total").html(valor_compra_com_juros_rs);
        $("#valor-parcela").val(valor_parcela);

        if(!$('.carrinho-pagamento-forma-pagamento-ativo').hasClass("carrinho-pagamento-forma-pagamento-cartao")){
            $("#carrinho-frete-resumo-valor-total-juros").css("display","none");
            $("#carrinho-frete-resumo-valor-total-valor-juros").html('');  
            $("#carrinho-frete-resumo-valor-total-valor-total").html(valor_compra_rs);
        }
    }
}


/*************/
/* PAGSEGURO */
/*************/            

if($("#carrinho-pagamento-formas-pagseguro").length > 0){    

    $('.carrinho-pagamento-forma-pagamento').click(function(){
        
        $('.carrinho-pagamento-forma-pagamento').each(function(){
            $(this).removeClass('carrinho-pagamento-forma-pagamento-ativo');
        });
        $(this).addClass('carrinho-pagamento-forma-pagamento-ativo');

        if($(this).hasClass("carrinho-pagamento-forma-pagamento-cartao")){
            $("#carrinho-pagamento-forma-pagamento-cartao").slideDown();
        } else {
            $("#carrinho-pagamento-forma-pagamento-cartao").slideUp();
            $("#carrinho-frete-resumo-valor-total-juros").hide();
            $("#carrinho-frete-resumo-valor-total-valor-juros").empty();
            $("#carrinho-frete-resumo-valor-total-valor-total").html(parseFloat($("#valor-compra").val()).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}));
            $("#numero-cartao").val('');
            $("#nome").val('');
            $("#validade-cartao").val('');
            $("#cvv").val('');
            $("#cpf").val('');
            $("#nascimento").val('');
            $("#token-cartao").val('');
            $("#valor-parcela").val('');
            $("#parcelas").empty().append('<option value="" disabled>Preencha os dados do cartão</option>');
        }    

        calculaDescontoTipoPagamento();
        calculaValorParcelas();

    });

    function finalizarPedido(){

        var funcao = $('.carrinho-pagamento-forma-pagamento-ativo').attr("tipo");
        if(funcao == 'pix'){  
            $("#carrinho-pagamento-form-pix").submit();
        } else {
            buscaHashComprador(funcao);
        }

    }

}


/*********/
/* ASAAS */
/*********/

if($("#carrinho-pagamento-formas-asaas").length > 0){    

    $('.carrinho-pagamento-forma-pagamento').click(function(){
        
        $('.carrinho-pagamento-forma-pagamento').each(function(){
            $(this).removeClass('carrinho-pagamento-forma-pagamento-ativo');
        });
        $(this).addClass('carrinho-pagamento-forma-pagamento-ativo');

        if($(this).hasClass("carrinho-pagamento-forma-pagamento-cartao")){
            $("#carrinho-pagamento-forma-pagamento-cartao").slideDown();
        } else {
            $("#carrinho-pagamento-forma-pagamento-cartao").slideUp();
            $("#carrinho-frete-resumo-valor-total-juros").hide();
            $("#carrinho-frete-resumo-valor-total-valor-juros").empty();
            $("#carrinho-frete-resumo-valor-total-valor-total").html(parseFloat($("#valor-compra").val()).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}));
        }    

        calculaDescontoTipoPagamento();
        calculaValorParcelas();

    });

    function finalizarPedidoAsaas(){
        var funcao = $('.carrinho-pagamento-forma-pagamento-ativo').attr("tipo");
        if(funcao == 'pix'){  
            $("#carrinho-pagamento-form-pix").submit();
        } else if(funcao == 'boleto'){  
            $("#carrinho-pagamento-form-boleto").submit();
        } else if(funcao == 'cartao'){  
            if($("#numero-cartao").val().length === 19 && $("#nome").val() != '' && $("#cpf").val().length === 14 && $("#validade-cartao").val().length === 5 && $("#cvv").val().length === 3 && $("#parcelas").val() != ''){
                $("#carrinho-pagamento-form-cartao").submit();
            } else {
                
                if($("#numero-cartao").val().length !== 19){
                    $("#numero-cartao").css("border-color",'firebrick');
                } else {
                    $("#numero-cartao").removeAttr("style");
                }
                
                if($("#nome").val() == ''){
                    $("#nome").css("border-color",'firebrick');
                } else {
                    $("#nome").removeAttr("style");
                }
                
                if($("#cpf").val().length !== 14){
                    $("#cpf").css("border-color",'firebrick');
                } else {
                    $("#cpf").removeAttr("style");
                }
                
                if($("#validade-cartao").val().length !== 5){
                    $("#validade-cartao").css("border-color",'firebrick');
                } else {
                    $("#validade-cartao").removeAttr("style");
                }

                if($("#cvv").val().length !== 3){
                    $("#cvv").css("border-color",'firebrick');
                } else {
                    $("#cvv").removeAttr("style");
                }

                if($("#parcelas").val() == ''){
                    $("#parcelas").css("border-color",'firebrick');
                } else {
                    $("#parcelas").removeAttr("style");
                }

                mensagemAviso('erro', 'Preencha todos os campos do cartão corretamente.', 3000);
            }
        }
    }    

}
    
function textAreaAdjust(el){
    el.style.height = (el.scrollHeight > el.clientHeight) ? (el.scrollHeight)+"px" : "40px";
}

if($('#textarea-chave-pix').length > 0){
    textAreaAdjust(document.getElementById('textarea-chave-pix'));
}

if($(".pagamento-confirmado").length > 0){

    var retorno_status = '';

    function verificaPagamentoAsaas(){
        $.ajax({
            url: "modulos/carrinho/php/verifica-status-pagamento-asaas.php",
            type: "POST",
            data: {"id": $("#pedido-confirmacao-identificador").val()},
            success: function (retorno){
                if(retorno == 'ERRO'){
                    window.location.href = "/";
                } else {    
                    retorno_status = retorno;
                }
            }
        });
    }

    function toggleDivsConfirmacao(){
        $(".subtitulo-pagina-central-h2").hide(400);
        $(".pagamento-nao-confirmado").slideUp(400);
        setTimeout(() => {
            $(".pagamento-confirmado").slideDown(1000);
        }, 500);
    }

    var verificaPagamentoInterval = setInterval(() => {

        verificaPagamentoAsaas();
        
        setTimeout(() => {
            if(retorno_status == 'CONFIRMADO'){
                toggleDivsConfirmacao();
                clearInterval(verificaPagamentoInterval);
            }
        }, 1);

    }, 3000);

}

//FUNÇÃO DO BOTÃO DE QUANTIDADE
$(document).ready(function () {
    jQuery('<div class="quantity-nav"><button class="quantity-button quantity-up">&uarr;</button><button class="quantity-button quantity-down">&darr;</button></div>').insertAfter('.quantity input');
    jQuery('.quantity').each(function () {
      var spinner = jQuery(this),
          input = spinner.find('input[type="number"]'),
          btnUp = spinner.find('.quantity-up'),
          btnDown = spinner.find('.quantity-down'),
          min = input.attr('min'),
          max = input.attr('max');
  
      btnUp.click(function () {
        var oldValue = parseFloat(input.val());
        if (oldValue >= max) {
          var newVal = oldValue;
        } else {
          var newVal = oldValue + 1;
        }
        spinner.find("input").val(newVal);
        spinner.find("input").trigger("change");
      });
  
      btnDown.click(function () {
        var oldValue = parseFloat(input.val());
        if (oldValue <= min) {
          var newVal = oldValue;
        } else {
          var newVal = oldValue - 1;
        }
        spinner.find("input").val(newVal);
        spinner.find("input").trigger("change");
      });
  
    });
});


/*************/
/* AVALIAÇÃO */
/*************/

$(".estrela").mouseover(function(){
    var estrela = $(this).attr("estrela");
    if(estrela == 1){
        $("#estrela-1").addClass("img-dourada");
        $("#estrela-2, #estrela-3, #estrela-4, #estrela-5").removeClass("img-dourada");
    } else if(estrela == 2){
        $("#estrela-1, #estrela-2").addClass("img-dourada");
        $("#estrela-3, #estrela-4, #estrela-5").removeClass("img-dourada");
    } else if(estrela == 3){
        $("#estrela-1, #estrela-2, #estrela-3").addClass("img-dourada");
        $("#estrela-4, #estrela-5").removeClass("img-dourada");
    } else if(estrela == 4){
        $("#estrela-1, #estrela-2, #estrela-3, #estrela-4").addClass("img-dourada");
        $("#estrela-5").removeClass("img-dourada");
    } else if(estrela == 5){
        $("#estrela-1, #estrela-2, #estrela-3, #estrela-4, #estrela-5").addClass("img-dourada");
    } else {
        $("#estrela-1, #estrela-2, #estrela-3, #estrela-4, #estrela-5").removeClass("img-dourada");
    }
});

function enviaPesquisaSatisfacao(){
    
    if($("#estrela-5").hasClass('img-dourada')){
        nota = 5;
    } else if($("#estrela-4").hasClass('img-dourada')){
        nota = 4;
    } else if($("#estrela-3").hasClass('img-dourada')){
        nota = 3;
    } else if($("#estrela-2").hasClass('img-dourada')){
        nota = 2;
    } else if($("#estrela-1").hasClass('img-dourada')){
        nota = 1;
    } else {
        var nota = '';
    }

    if(nota != ''){
        if(total_caracteres >= 30){
            $.ajax({
                url: "modulos/carrinho/php/cadastra-pesquisa-satisfacao.php",
                type: "POST",
                dataType: "json",
                data: {"nota": nota, "carrinho": $("#carrinho-confirmacao-identificador").val(), "observacoes": $("#observacoes").val()},
                success: function (retorno){
                    if(retorno[0].status == 'ERRO'){
                        fechaLoader();
                    } else {    
                        $.ajax({
                            url: "modulos/envio-email/index.php",
                            type: "POST",
                            data: {"tipo-envio": "nova-avaliacao-simples"},
                            success: function (retorno){                                    
                                $("#avaliacao-loja").remove();
                                fechaLoader();
                                setTimeout(() => {
                                    alert("Muito obrigado pela sua avaliação.");
                                }, 0);
                            }
                        });
                    }
                },
                beforeSend: function() { 
                    abreLoader();
                }
            });
        } else {
            alert('Deixe uma observação de pelo menos trinta caracteres.');
        }
    } else {
        alert('Selecione uma nota entre 1 e 5. 1 para muito ruim e 5 para muito bom.');
    }
}

var total_caracteres = 0;
$(document).on("input", "#observacoes", function () {
    total_caracteres = $(this).val().length;    
    $("#observacoes-caracteres").html(total_caracteres);
});