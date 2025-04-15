//LISTAS
$(document).ready(function(){
    $("#admin-lista_length, #admin-lista-dois_length").closest(".col-sm-12").removeClass("col-sm-12 col-md-6").addClass("col-3");
    $("#admin-lista_filter, #admin-lista-dois_filter").closest(".col-sm-12").removeClass("col-sm-12 col-md-6").addClass("col-9");
    $("#admin-lista_filter, #admin-lista-dois_filter").addClass("text-right");
    $("#admin-lista_filter input, #admin-lista-dois_filter input").attr("placeholder", "Pesquisar...");
    $("#admin-lista_length, #admin-lista-tres_length").closest(".col-sm-12").removeClass("col-sm-12 col-md-6").addClass("col-3");
    $("#admin-lista_filter, #admin-lista-tres_filter").closest(".col-sm-12").removeClass("col-sm-12 col-md-6").addClass("col-9");
    $("#admin-lista_filter, #admin-lista-tres_filter").addClass("text-right");
    $("#admin-lista_filter input, #admin-lista-tres_filter input").attr("placeholder", "Pesquisar...");
    $("#admin-lista_length, #admin-lista-quatro_length").closest(".col-sm-12").removeClass("col-sm-12 col-md-6").addClass("col-3");
    $("#admin-lista_filter, #admin-lista-quatro_filter").closest(".col-sm-12").removeClass("col-sm-12 col-md-6").addClass("col-9");
    $("#admin-lista_filter, #admin-lista-quatro_filter").addClass("text-right");
    $("#admin-lista_filter input, #admin-lista-quatro_filter input").attr("placeholder", "Pesquisar...");
    $("#admin-lista_length, #admin-lista-cinco_length").closest(".col-sm-12").removeClass("col-sm-12 col-md-6").addClass("col-3");
    $("#admin-lista_filter, #admin-lista-cinco_filter").closest(".col-sm-12").removeClass("col-sm-12 col-md-6").addClass("col-9");
    $("#admin-lista_filter, #admin-lista-cinco_filter").addClass("text-right");
    $("#admin-lista_filter input, #admin-lista-cinco_filter input").attr("placeholder", "Pesquisar...");
});

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
    if($('#whatsapp').length > 0){
      if($('#whatsapp').val().length == 15){
        $('#whatsapp').mask('(00) 00000-0009');
      } else {
        $('#whatsapp').mask('(00) 0000-00009');
      }
    } 
})

$("#cep").mask("00.000-000");
$("#validade").mask("00/00/0000");
$("#nascimento").mask("00/00/0000");
$("#cpf").mask("000.000.000-00");
$(".cnpj").mask("00.000.000/0000-00");
$("#preco").maskMoney({
  prefix: "R$ ",
  decimal: ",",
  thousands: "."
});
$("#valor").maskMoney({
  decimal: ",",
  thousands: "."
});
$(".mascara-double").maskMoney({
  decimal: ",",
  thousands: "."
});
$(".valor-com-prefixo").maskMoney({
  prefix: "R$ ",
  decimal: ",",
  thousands: "."
});
$("#telefone").mask("(00) 0000-0000");
$("#celular").mask("(00) 00000-0000");
$('#celular').blur(function(event) {
   if($(this).val().length == 15){ 
      $('#celular').mask('(00) 00000-0009');
   } else {
      $('#celular').mask('(00) 0000-00009');
   }
});
$('#whatsapp').mask('(00) 0000-00009');
$('#whatsapp').blur(function(event) {
   if($(this).val().length == 15){
      $('#whatsapp').mask('(00) 00000-0009');
   } else {
      $('#whatsapp').mask('(00) 0000-00009');
   }
});

//CIDADE E ESTADO
function buscaCidades(uf, cidade) {
    if(uf != ''){
        var urlData = "&uf="+uf;  
        $.ajax({
            type: "POST",
            url: $("#site").val()+"/php/busca-cidade-estado.php",
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

//FUNÇÕES PARA O CAMPO FILE
function inputFileEscolher(n){
    if(n != undefined)
        $('#imagem-'+n).trigger('click');
    else 
        $('#imagem').trigger('click');
}
function inputFileChange(n){    
    if(n != undefined){
        var fileName = $('#imagem-'+n)[0].files[0].name;
        $('#arquivo-'+n).val(fileName);
        $('#arquivo-'+n).focus();
    } else {
        var fileName = $('#imagem')[0].files[0].name;
        $('#arquivo').val(fileName);
        $('#arquivo').focus();
    }
}

//FUNÇÕES PARA O CAMPO FILE MOBILE
function inputFileEscolherMobile(n){
  if(n != undefined)
      $('#imagem-mobile-'+n).trigger('click');
  else 
      $('#imagem-mobile').trigger('click');
}
function inputFileChangeMobile(n){    
  if(n != undefined){
      var fileName = $('#imagem-mobile-'+n)[0].files[0].name;
      $('#arquivo-mobile-'+n).val(fileName);
      $('#arquivo-mobile-'+n).focus();
  } else {
      var fileName = $('#imagem-mobile')[0].files[0].name;
      $('#arquivo-mobile').val(fileName);
      $('#arquivo-mobile').focus();
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
      url: "../php/busca-endereco.php",
      data: urlData,
      dataType : "json",
      success: function (endereco) {   
        buscandoEndereco = false;
        if(endereco[0] === "CEP NÃO ENCONTRADO") {
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
