//INSTACIA VARIÁVEIS
var global_numero_cartao;
var global_bandeira_cartao;
var global_mes_validade;
var global_ano_validade;
var global_cvv;
var global_cartao_ok    = false;
var global_validade_ok  = false;
var global_cvv_ok       = false;
var global_endereco     = $('#endereco-pagseguro').val();
var global_valor_compra = $("#valor-compra").val();
var num_cart_qtde       = 0;
var erros_cartao        = 0;


//GERA O ID DA SESSÃO AO CARREGAR A PÁGINA
geraIdSessao();

//VERIFICA A BANDEIRA DO CARTÃO CONFORME DIGITA
$("#numero-cartao").bind("keyup",function(e){

    if(num_cart_qtde != 16){

        //RECUPERA O NÚMERO DO CARTÃO QUE ESTÁ SENDO DIGITADO
        var numero_cartao = formataNumeroCartao($(this).val());
        num_cart_qtde = numero_cartao.length;

        if(num_cart_qtde >= 6){
        
            PagSeguroDirectPayment.getBrand({

                cardBin: numero_cartao,
                success: function(retorno){
                    
                    //PEGA A BANDEIRA E MOSTRA NA TELA
                    var bandeira_cartao = retorno.brand.name;
                    $("#bandeira-cartao").html("<img src='https://stc.pagseguro.uol.com.br/public/img/payment-methods-flags/42x20/"+bandeira_cartao+".png'>");
                    
                    //AO TERMINAR DE DIGITAR O CARTÃO BUSCA AS PARCELAS
                    if(num_cart_qtde == 16){
                        global_numero_cartao = numero_cartao;
                        global_bandeira_cartao = bandeira_cartao;
                        global_cartao_ok = true;
                        buscaParcelas();
                    } else{
                        global_numero_cartao = "";
                        global_bandeira_cartao = "";
                        global_cartao_ok = false;
                        $("#parcelas").empty();
                    }

                },
                error: function(retorno){    
                    erros_cartao++;
                    console.log("ERRO: ERRO NO NÚMERO DO CARTÃO");
                    global_numero_cartao = "";
                    global_bandeira_cartao = "";
                    global_cartao_ok = false;
                    $("#bandeira-cartao").empty();
                    $("#mensagem-numero-cartao").show();
                    $("#parcelas").empty();
                }

            });

        } else if(num_cart_qtde == 0){
            global_numero_cartao = "";
            global_bandeira_cartao = "";
            global_cartao_ok = false;
            $("#bandeira-cartao").empty();
            $("#mensagem-numero-cartao").hide();
            $("#parcelas").empty();
        } else {
            global_numero_cartao = "";
            global_bandeira_cartao = "";
            global_cartao_ok = false;
            $("#bandeira-cartao").empty();
            $("#mensagem-numero-cartao").hide();
            $("#parcelas").empty();
        }

    } else {
        num_cart_qtde = $("#numero-cartao").length;
        if(erros_cartao >= 5){
            location.reload();
        }
    }

});

//VALIDADE CONFORME DIGITA
$("#validade-cartao").on("keyup",function(){

    //RECUPERA A VALIDADE QUE ESTÁ SENDO DIGITADA
    var data_validade = $(this).val();
    var validade_qtde = data_validade.length;
    var data_validade = data_validade.split("/");
    
    if(validade_qtde == 7){
        global_mes_validade = data_validade[0];
        global_ano_validade = data_validade[1];
        global_validade_ok = true;
    } else {
        global_mes_validade = "";
        global_ano_validade = "";
        global_validade_ok = false;
    }

});

//CVV CONFORME DIGITA
$("#cvv").on("keyup",function(){

    //RECUPERA O NÚMERO DO CVV QUE ESTÁ SENDO DIGITADO
    var cvv = $(this).val();
    var cvv_qtde = cvv.length;
    
    if(cvv_qtde == 3){
        global_cvv = cvv;
        global_cvv_ok = true;
    } else {
        global_cvv = "";
        global_cvv_ok = false;
    }

});

$("#parcelas").change(function(){
    var n_parcelas                = parseInt($(this).val());
    var valor_parcela             = parseFloat($(this).find('option:selected').attr('data-valor-parcela'));
    var valor_compra              = parseFloat(global_valor_compra);
    var juros                     = (valor_parcela*n_parcelas)-valor_compra;
    var juros_rs                  = parseFloat(juros).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
    var valor_compra_com_juros    = valor_compra+juros;
    var valor_compra_com_juros_rs = parseFloat(valor_compra_com_juros).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
    $("#carrinho-frete-resumo-valor-total-juros").css("display","flex");
    $("#carrinho-frete-resumo-valor-total-valor-juros").html(juros_rs);  
    $("#carrinho-frete-resumo-valor-total-valor-total").html(valor_compra_com_juros_rs);
    $("#valor-parcela").val(valor_parcela);
}); 

//EVENTOS PRA DISPARAR A FUNÇÃO DE BUSCA DO TOKEN AO SAIR DO CAMPO CARTÃO, VALIDADE E CVV
$("#numero-cartao").blur(function(){ verificaPreenchimentoCartao(); });
$("#validade-cartao").blur(function(){ verificaPreenchimentoCartao(); });
$("#cvv").blur(function(){ verificaPreenchimentoCartao(); });

/**********************/
/* FUNÇÕES AUXILIARES */
/**********************/

//RETIRA OS HÍFENS DO NÚMERO DO CARTÃO
function formataNumeroCartao(numero_cartao){
    return numero_cartao.replace("-","").replace("-","").replace("-","");
}

//FUNÇÃO QUE TRANSFORMA NÚMERO COM PONTO (500.00) PARA O MODELO VIGENTE NO BRASIL (R$ 500,00)
function converteValorR$(valor){
    return valor.toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
}

//VERIFICA SE TODOS OS DADOS DO CARTÃO ESTÃO PREENCHIDOS E GERA O TOKEN DO CARTÃO
function verificaPreenchimentoCartao(){
    if(global_cartao_ok && global_validade_ok && global_cvv_ok)
        buscaTokenCartao();
    else
        $("#token-cartao").val("");
}

/************************/
/* FUNÇÕES DO PAGSEGURO */
/************************/

//GERA O ID DE SESSÂO OBRIGATÓRIO PARA INICIAR O PROCESSO
function geraIdSessao(){    
                
    $.ajax({
        url: global_endereco + 'php/session.php',
        type: 'POST',
        success: function (idSessao) {
            console.log("SUCESSO: ID GERADO");
            PagSeguroDirectPayment.setSessionId(idSessao);
            fechaLoader();
        },
        error: function(retorno){
            mensagemAviso('erro', 'Erro ao gerar sessão do PagSeguro. Se o problema persistir contate o administrador do sistema.', 3000);
            setTimeout(function(){
                window.location.href = 'carrinho-frete';
            }, 3000);
        }
    });
    
}

//BUSCA O VALOR E QUANTIDADE DE PARCELAS
function buscaParcelas(){

    if(global_cartao_ok){
    
        $("#carrinho-frete-resumo-valor-total-juros").hide();
        $("#carrinho-frete-resumo-valor-total-valor-juros").empty();

        abreLoader();
        var options_parcelas = '';
        var total_parcelas   = parseInt($("#total-parcelas").val());
        
        PagSeguroDirectPayment.getInstallments({

            amount: global_valor_compra,
            brand: global_bandeira_cartao,
            success: function(retorno){
                $.each(retorno.installments, function(ia, obja){
                    $.each(obja, function(ib, objb){
                        if(parseInt(objb.quantity) <= total_parcelas){
                            options_parcelas += "<option value='"+objb.quantity+"' data-valor-parcela='"+objb.installmentAmount+"'>"+objb.quantity+" de "+converteValorR$(objb.installmentAmount)+"</option>";
                        }
                        if(objb.quantity == 1){
                            $("#valor-parcela").val(objb.installmentAmount);
                        }
                    });
                });
                $("#parcelas").empty();
                $("#parcelas").html(options_parcelas);
                console.log("SUCESSO: PARCELAS ENCONTRADAS");
                fechaLoader();
            },
            error: function(retorno){
                console.log("ERRO: GERAR PARCELAMENTO");
                mensagemAviso('erro', 'Erro ao buscar parcelamento. Vamos ter que tentar novamente.', 3000);
                setTimeout(function(){
                    location.reload();
                }, 3000);
            }

        });

    }

}

//BUSCAR O TOKEN DO CARTÃO
function buscaTokenCartao(){
    
    abreLoader();

    PagSeguroDirectPayment.createCardToken({

        cardNumber: global_numero_cartao,
        brand: global_bandeira_cartao,
        cvv: global_cvv,
        expirationMonth: global_mes_validade,
        expirationYear: global_ano_validade,
        success: function(retorno){
            console.log("SUCESSO: TOKEN DO CARTÃO GERADO");
            $("#token-cartao").val(retorno.card.token);
            fechaLoader();
        },
        error: function(retorno){
            $("#token_cartao").val("");
            console.log("ERRO: GERAR TOKEN CARTÃO");
            mensagemAviso('erro', 'Erro do Pagseguro. Vamos ter que tentar novamente.', 3000);
            setTimeout(function(){
                location.reload();
            }, 3000);
        }

    });

}

//FUNÇÃO QUE BUSCA O HASH DO COPMPRADOR
function buscaHashComprador(funcao){

    var tudo_certo = false

    if(funcao == "cartao" && global_cartao_ok && global_validade_ok && global_cvv_ok && $("#cpf").val().length === 14 && $("#nascimento").val().length === 10 &&  $("#parcelas").val() != '')
        tudo_certo = true;
    else if(funcao == "boleto")
        tudo_certo = true;

    if(tudo_certo){

        abreLoader();

        PagSeguroDirectPayment.onSenderHashReady(function(retorno){
            
            if(retorno.status == 'success'){

                console.log("SUCESSO: HASH DO COMPRADOR GERADO");
                
                if(funcao == "cartao"){
                    $("#hash-comprador-cartao").val(retorno.senderHash); 
                    $("#carrinho-pagamento-form-cartao").submit();
                } else if(funcao == "boleto") {            
                    $("#hash-comprador-boleto").val(retorno.senderHash);     
                    $("#carrinho-pagamento-form-boleto").submit();
                }

            } else {

                $("#hash-comprador").val("");
                console.log("ERRO: HASH COMPRADOR. - "+retorno.message);
                mensagemAviso('erro', 'Erro do Pagseguro. Vamos ter que tentar novamente.', 3000);
                setTimeout(function(){
                    location.reload();
                }, 3000);
                return false;

            }
            
        });

    } else {
        mensagemAviso('erro', 'Preencha todos os dados do cartão corretamente.', 3000);
    }

}