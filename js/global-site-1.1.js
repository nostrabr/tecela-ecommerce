//MASCARAS
var options = {
    onKeyPress: function (cpf, ev, el, op) {
        var masks = ['000.000.000-000', '00.000.000/0000-00'],
            mask = (cpf.length > 14) ? masks[1] : masks[0];
        el.mask(mask, op);
    }
}

$(document).ready(function(){
    if($('#cpf-cnpj').length > 0){
        var cpf_cnpj = $('#cpf-cnpj').val();
        if(cpf_cnpj.length > 14){
            $('#cpf-cnpj').mask('00.000.000/0000-00', options);
        } else {
            $('#cpf-cnpj').mask('000.000.000-00#', options);
        }
    }
})

$("#cep").mask("00.000-000");
$("#telefone").mask("(00) 0000-0000");
$("#celular").mask("(00) 00000-0000");
$("#whatsapp").mask("(00) 00000-0000");
$("#validade").mask("00/00/0000");
$("#validade-cartao").mask("00/00");
$("#nascimento").mask("00/00/0000");
$("#cpf").mask("000.000.000-00");
$("#numero-cartao").mask("9999-9999-9999-9999");
$("#cvv").mask("999");
$("#preco").maskMoney({
  prefix: "R$ ",
  decimal: ",",
  thousands: "."
});

//CIDADE E ESTADO
function buscaCidades(uf, cidade) {
    if(uf != ''){
        var urlData = "&uf="+uf;  
        $.ajax({
            type: "POST",
            url: $("#site").val()+"php/busca-cidade-estado.php",
            data: urlData,
            success: function (data) {
              $('#cidade').html(data);
              if(cidade != ''){                  
                $("#cidade").val(cidade).select();
              }
            }
        });    
    }
}

function validaCpfCnpj(cpf_cnpj, campo){

    cpf_cnpj = cpf_cnpj.replace(/[^\d]+/g,"");

    var retorno = true;

    if(cpf_cnpj.length === 11){ 
  
      if(cpf_cnpj == "00000000000"	|| cpf_cnpj == "11111111111"	|| cpf_cnpj == "22222222222" || cpf_cnpj == "33333333333" || cpf_cnpj == "44444444444" || cpf_cnpj == "55555555555" || cpf_cnpj == "66666666666"	|| cpf_cnpj == "77777777777" || cpf_cnpj == "88888888888" || cpf_cnpj == "99999999999" )
        retorno = false;
  
      var soma = 0;
      var resto;
          
      for (var i = 1; i <= 9; i++) 
        soma = soma + parseInt(cpf_cnpj.substring(i-1, i)) * (11 - i);
        
      resto = (soma * 10) % 11;
              
      if ((resto == 10) || (resto == 11))  
        resto = 0;
  
      if (resto != parseInt(cpf_cnpj.substring(9, 10))) 
        retorno = false;
      
      soma = 0;
            
      for (var i = 1; i <= 10; i++) 
        soma = soma + parseInt(cpf_cnpj.substring(i-1, i)) * (12 - i);
            
      resto = (soma * 10) % 11;
            
      if ((resto == 10) || (resto == 11))  
        resto = 0;
            
      if (resto != parseInt(cpf_cnpj.substring(10, 11))) 
        retorno = false;
  
    } else if(cpf_cnpj.length === 14) {
          
      if (cpf_cnpj == "00000000000000" || cpf_cnpj == "11111111111111" || cpf_cnpj == "22222222222222" || cpf_cnpj == "33333333333333" || cpf_cnpj == "44444444444444" || cpf_cnpj == "55555555555555" || cpf_cnpj == "66666666666666" || cpf_cnpj == "77777777777777" || cpf_cnpj == "88888888888888" || cpf_cnpj == "99999999999999")        
        retorno = false;
  
      var tamanho = cpf_cnpj.length - 2;
      var numeros = cpf_cnpj.substring(0,tamanho);
      var digitos = cpf_cnpj.substring(tamanho);
      var soma = 0;
      var pos = tamanho - 7;
          
      for (var i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) 
         pos = 9;
      }
  
      var resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
      
      if (resultado != digitos.charAt(0)) 
        retorno = false;
  
      tamanho = tamanho + 1;
      numeros = cpf_cnpj.substring(0,tamanho);
      soma = 0;
      pos = tamanho - 7;
  
      for (var i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) 
          pos = 9;
      }
  
      resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
      
      if (resultado != digitos.charAt(1)) 
        retorno = false;
  
    } else {
        retorno = false;
    }

    if(!retorno){
        mensagemAviso('erro', 'CPF ou CNPJ Inválido!', 2000);
        $("#"+campo).val('');
        $("#"+campo).focus();
    }
  
}

var buscandoEndereco = false;
var tecla;

document.addEventListener("keydown", function(event) {
  tecla = event.which;
});

function buscaEndereco(cep){

  if(cep.length === 10 & !buscandoEndereco & tecla != 9){
    
    buscandoEndereco = true;

    var urlData = "&cep="+cep;

    $.ajax({
      type: "POST",
      url: $("#site").val()+"php/busca-endereco.php",
      data: urlData,
      dataType : "json",
      success: function (endereco) {   
        buscandoEndereco = false;
        if(endereco[0] === "SESSÃO INVÁLIDA") {
            window.location.href = 'logout'
        } else if(endereco[0] === "CEP NÃO ENCONTRADO") {
          mensagemAviso('erro', 'Endereço não encontrado! Preencha manualmente.', 3000);
        } else {
          buscaCidades(endereco[0].uf, endereco[0].municipio);
          fechaLoader();        
          $("#rua").val(endereco[0].logradouro[0]);
          $("#bairro").val(endereco[0].bairro[0]);
          $("#estado").val(endereco[0].uf).select();
          $("#numero").focus();
        }
      },
      beforeSend: function() {
        abreLoader();
        $("#rua").val("");
        $("#bairro").val("");
        $("#numero").val("");
        $("#complemento").val("");
        $("#referencia").val("");
        $("#estado").val("").select();
        $("#cidade").empty();
      }
    });

  }

}

$(function($) {
  $(document).on('keypress', 'input.only-number', function(e) {
    var $this = $(this);
    var key = (window.event)?event.keyCode:e.which;
    var dataAcceptDot = $this.data('accept-dot');
    var dataAcceptComma = $this.data('accept-comma');
    var acceptDot = (typeof dataAcceptDot !== 'undefined' && (dataAcceptDot == true || dataAcceptDot == 1)?true:false);
    var acceptComma = (typeof dataAcceptComma !== 'undefined' && (dataAcceptComma == true || dataAcceptComma == 1)?true:false);

		if((key > 47 && key < 58)
      || (key == 46 && acceptDot)
      || (key == 44 && acceptComma)) {
    	return true;
  	} else {
 			return (key == 8 || key == 0)?true:false;
 		}
  });
});

$(document).on('submit','form',function(){
  abreLoader();
});

function capitalize(value) {
  var textArray = value.split(' ')
  var capitalizedText = ''
  for (var i = 0; i < textArray.length; i++) {
    capitalizedText += textArray[i].charAt(0).toUpperCase() + textArray[i].slice(1) + ' '
  }
  return capitalizedText.trim()
}

$(document).ready(function(){  

  /********************************/
  /* EVENTOS DO PIXEL DO FACEBOOK */
  /********************************/

  if (typeof fbq !== 'undefined') {

    /* VIEW CONTENT */
    var url  = window.location.href; 
    var url_fixa_geral   = url.split("/")[url.split("/").length -1];
    var url_fixa_produto = url.split("/")[url.split("/").length -4];

    if(url_fixa_geral == 'contato' | url_fixa_geral == 'localizacao' | url_fixa_geral == 'sobre' | url_fixa_geral == 'politica-comercial' | url_fixa_geral == 'politica-entrega' | url_fixa_geral == 'politica-troca-devolucao' | url_fixa_geral == 'politica-privacidade-seguranca' | url_fixa_geral == 'politica-termos-uso' | url_fixa_produto == 'produto'){
      fbq('track', 'ViewContent');
    }
    if(url_fixa_geral == 'localizacao'){
      fbq('track', 'FindLocation');
    }

    /* ADD TO CART */
    $("#produto-btn-adicionar-carrinho").click(function(){
      fbq('track', 'AddToCart');
    });
    $("#produto-btn-comprar").click(function(){
      fbq('track', 'AddToCart');
    });

    /* PESQUISA */
    if($("#filtro-pesquisa").length > 0){
      if($("#filtro-pesquisa").val() != ''){
        fbq('track', 'Search', {Search: $("#filtro-pesquisa").val(), value: 0.00, currency: 'BRL'});
      }
    }

    /* CONTATO */
    $(".produto-container-valor-esgotado").click(function(){
      var url_produto = $(".produto-container-valor-esgotado").closest('.produto-container').parent('div').attr('onclick').replace('javascript: window.location.href = ','').replace("'",'').replace("'",'');
      var produto     = url_produto.split("/")[url_produto.split("/").length -1];
      fbq('track', 'Contact', {canal: 'Whatsapp', motivo: 'Interesse em produto esgotado', id_produto: produto});
    });
    $("#produto-btn-consultar-whatsapp").click(function(){
      var url     = window.location.href; 
      var produto = url.split("/")[url.split("/").length -1];
      fbq('track', 'Contact', {canal: 'Whatsapp', motivo: 'Interesse em produto esgotado', id_produto: produto});
    })
    $("#footer-btn-whats").click(function(){
      fbq('track', 'Contact', {canal: 'Whatsapp'});
    });
    $("#contato-btn-whatsapp").click(function(){
      fbq('track', 'Contact', {canal: 'Whatsapp'});
    });
    $("#contato-btn-telefone").click(function(){
      fbq('track', 'Contact', {canal: 'Telefone'});
    });
    $("#footer-btn-email").click(function(){
      fbq('track', 'Contact', {canal: 'E-mail'});
      fbq('track', 'Lead');
    });
    $("[name='contato-btn-enviar-formulario']").click(function(){
      var form_nome     = $("#nome").val();
      var form_email    = $("#email").val();
      var form_celular  = $("#celular").val();
      var form_mensagem = $("#mensagem").val();
      fbq('track', 'Contact', {canal: 'Form', nome: form_nome, email: form_email, telefone: form_celular, mensagem: form_mensagem});
      fbq('track', 'Lead');
    });

    /* CADASTRO */
    if($("#registro-cadastro-realizado").length > 0){
      if($("#registro-cadastro-realizado").val() != ''){
        fbq('track', 'CompleteRegistration', {identificador: $("#registro-cadastro-realizado").val(), value: 0.00, currency: 'BRL'});
      }
    }

    /* FINALIZAR PEDIDO */
    $("#carrinho-frete-btn-finalizar").click(function(){
      fbq('track', 'InitiateCheckout');
    });

    /* PEDIDO CONCLUIDO COM SUCESSO */  
    if($("#carrinho-confirmacao-pedido-confirmado").length > 0){
      if($("#carrinho-confirmacao-pedido-confirmado").val() == 'confirmado'){
        fbq('track', 'Purchase', {value: $("#carrinho-confirmacao-valor-pedido").val(), currency: 'BRL'});
      }
    }
      
  }

});

$(".produto-container-valor-esgotado").click(function(e){
  e.stopPropagation();
});