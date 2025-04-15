var buscando_total_carrinho = true;
var buscando_saldo_melhor_envio = true;
var DT2 = $('#lista-etiquetas').DataTable();

$(document).ready(function () {

  totalCarrinho();
  saldoMelhorEnvio();
  enviaEmailsRastreamento();

  if($("#admin-lista").length != 0){
    jQuery.extend(jQuery.fn.dataTableExt.oSort, {
      "date-br-pre": function ( a ) {
          if (a == null || a == "") {
              return 0;
          }
          var brDatea = a.split('/');
          return (brDatea[2] + brDatea[1] + brDatea[0]) * 1;
      },
      "date-br-asc": function ( a, b ) {
          return ((a < b) ? -1 : ((a > b) ? 1 : 0));
      },
      "date-br-desc": function ( a, b ) {
          return ((a < b) ? 1 : ((a > b) ? -1 : 0));
      }
    });
    var DT1 = $('#admin-lista').DataTable({
      "order": [[ 1, 'desc' ]],
      "lengthMenu": [[10, 50, 500, -1], [10, 50, 500, "Tudo"]],
      "pagingType": "numbers",
      "columns": [
        {"orderable": false},
        {"orderable": true, "type": 'date-br'},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
      ],
      columnDefs: [{
        width: 40,
        orderable: false,
        className: 'select-checkbox',
        targets: 0
      }],
      select: {
          style:    'os',
          selector: 'td:first-child'
      },
      'select': 'multi',
      "language": {
        "sEmptyTable": "Nenhum registro encontrado",
        "sInfo": " _START_ até _END_ de _TOTAL_ ",
        "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
        "sInfoFiltered": "(Filtrados de _MAX_ registros)",
        "sInfoPostFix": "",
        "sInfoThousands": ".",
        "sLengthMenu": "_MENU_",
        "sLoadingRecords": "Carregando...",
        "sProcessing": "Processando...",
        "sZeroRecords": "Nenhum registro encontrado",
        "sSearch": "",
        "oPaginate": {
            "sNext": "Próximo",
            "sPrevious": "Anterior",
            "sFirst": "Primeiro",
            "sLast": "Último"
        },
        "oAria": {
            "sSortAscending": ": Ordenar colunas de forma ascendente",
            "sSortDescending": ": Ordenar colunas de forma descendente"
        },
        "select": {
            "rows": {
                "_": "Selecionado %d linhas",
                "0": "Nenhuma linha selecionada",
                "1": "Selecionado 1 linha"
            }
        }
      }
    });

    $(".selectAll").on("click",function(e) {
        if($(this).is(":checked")) {
            DT1.rows().select();        
        } else {
            DT1.rows().deselect(); 
        }
    });
    
  }

  if($("#admin-lista-dois").length != 0){
    jQuery.extend(jQuery.fn.dataTableExt.oSort, {
      "date-br-pre": function ( a ) {
          if (a == null || a == "") {
              return 0;
          }
          var brDatea = a.split('/');
          return (brDatea[2] + brDatea[1] + brDatea[0]) * 1;
      },
      "date-br-asc": function ( a, b ) {
          return ((a < b) ? -1 : ((a > b) ? 1 : 0));
      },
      "date-br-desc": function ( a, b ) {
          return ((a < b) ? 1 : ((a > b) ? -1 : 0));
      }
    });
    var DT3 = $('#admin-lista-dois').DataTable({
      "order": [[ 0, 'desc' ]],
      "lengthMenu": [[10, 50, 500, -1], [10, 50, 500, "Tudo"]],
      "pagingType": "numbers",
      "columns": [
        {"orderable": true, "type": 'date-br'},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": false}
      ],
      "language": {
        "sEmptyTable": "Nenhum registro encontrado",
        "sInfo": " _START_ até _END_ de _TOTAL_ ",
        "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
        "sInfoFiltered": "(Filtrados de _MAX_ registros)",
        "sInfoPostFix": "",
        "sInfoThousands": ".",
        "sLengthMenu": "_MENU_",
        "sLoadingRecords": "Carregando...",
        "sProcessing": "Processando...",
        "sZeroRecords": "Nenhum registro encontrado",
        "sSearch": "",
        "oPaginate": {
            "sNext": "Próximo",
            "sPrevious": "Anterior",
            "sFirst": "Primeiro",
            "sLast": "Último"
        },
        "oAria": {
            "sSortAscending": ": Ordenar colunas de forma ascendente",
            "sSortDescending": ": Ordenar colunas de forma descendente"
        },
        "select": {
            "rows": {
                "_": "Selecionado %d linhas",
                "0": "Nenhuma linha selecionada",
                "1": "Selecionado 1 linha"
            }
        }
      }
    });   
    
  }  

});

function enviaEmailsRastreamento(){
  $.ajax({
    url: "modulos/envios/php/envia-emails-rastreamento.php"
  });
}

function preVisualizarEtiqueta(codigo){
  $.ajax({
    url: "modulos/envios/php/pre-visualizar-etiqueta.php",
    type: "POST",
    dataType: "json",
    data: {"codigo": codigo},
    success: function (data){  
      fechaLoader();
      if(data.error){ 
        mensagemAviso('erro', data.error, 5000) 
      } else {
        window.open(data.url, '_blank');
      }
    },
    beforeSend: function() {
      abreLoader();
    }
  });
}

function cancelarEtiqueta(codigo){  
  $.ajax({
    url: "modulos/envios/php/cancelar-etiqueta-verificar.php",
    type: "POST",
    dataType: "json",
    data: {"codigo": codigo},
    success: function (data){  
      fechaLoader();
      if(data.error){ 
        mensagemAviso('erro', data.error, 5000);
      } else {
        if(data.response[data.order_id].cancellable){
          $("#modal-cancelar-etiqueta #codigo-pedido").val(codigo);
          $("#modal-cancelar-etiqueta").modal("show");   
        } else {          
          mensagemAviso('erro', 'Esta etiqueta não pode mais ser cancelada.', 5000);
        }
      }
    },
    beforeSend: function() {
      abreLoader();
    }
  });
}

$('#modal-cancelar-etiqueta').on('hidden.bs.modal', function () {
  $("#modal-cancelar-etiqueta #id-etiqueta").val('');
});

function rastrearEtiqueta(etiqueta){
  $.ajax({
    url: "modulos/envios/php/rastrear-etiqueta.php",
    type: "POST",
    dataType: "json",
    data: {"etiqueta": etiqueta},
    success: function (data){ 
      window.open('https://www.melhorrastreio.com.br/rastreio/'+data[etiqueta].melhorenvio_tracking, '_blank');
      fechaLoader()
    },
    beforeSend: function() {
      abreLoader();
    }
  }); 
}

function buscaPedidosSelecionados(){
  var pedidos = [];
  $("tr.selected").each(function(){
    pedidos.push($(this).attr("id"));
    $(this).remove();
  });
  return pedidos;
}

$("#envio-acao").change(function(){
  var acao = $(this).val();
  var pedidos = buscaPedidosSelecionados();
  if(pedidos.length > 0){
    if(acao == 'add-carrinho'){
      $.ajax({
        url: "modulos/envios/php/adiciona-carrinho.php",
        type: "POST",
        data: {"pedidos": JSON.stringify(pedidos)},
        success: function (data){  
          if(data != ''){
            mensagemAviso('erro',data,5000);
            setTimeout(() => {              
              abrirCarrinhoEtiquetas(1);
              totalCarrinho();
            }, 5000);
          } else {
            abrirCarrinhoEtiquetas(1);
            totalCarrinho();
          }
        },
        beforeSend: function() {
          abreLoader();
          $("#envio-acao option").each(function(){
            $(this).attr('selected',false);
          });
          $("#envio-acao option:first").attr('selected',true);
        }
      }); 
    }
  } else {
    alert("Selecione ao menos um pedido.")
  }
});

var etiqueta_removida_carrinho = false;

function removeEtiquetaCarrinho(codigo){
  $.ajax({
    url: "modulos/envios/php/remover-item-carrinho.php",
    type: "POST",
    data: {"codigo": codigo},
    success: function (data){  
      etiqueta_removida_carrinho = true;
      totalCarrinho();
      abrirCarrinhoEtiquetas(1);
    },
    beforeSend: function() {
      abreLoader();
    }
  }); 
}

$('#modal-carrinho').on('hidden.bs.modal', function () {
  if(etiqueta_removida_carrinho){
    abreLoader();
    location.reload();
  }
});

function abrirCarrinhoEtiquetas(pagina){
  $.ajax({
    url: "modulos/envios/php/busca-carrinho.php",
    type: "POST",
    dataType: "json",
    data: {"pagina": pagina},
    success: function (data){ 
      if(data.total == 0){
        fechaLoader(); 
        mensagemAviso('aviso','Carrinho vazio!',3000);
        $("#modal-carrinho").modal("hide");   
      } else {    
        if(pagina == 1){
          $('#lista-etiquetas').DataTable().destroy();
          $("#lista-etiquetas tbody").empty();
        }
        for(var i = 0; i < data.response.data.length; i++){
          $("#lista-etiquetas tbody").append(
            '<tr id="'+data.response.data[i].id+'" pedido="'+data.pedidos[i]+'" class="cursor-pointer">'+
              '<td></td>'+
              '<td class="d-none">'+data.response.data[i].price+'</td>'+
              '<td class="codigo-pedido">'+data.pedidos[i]+'</td>'+
              '<td class="codigo-pedido d-none d-lg-table-cell">'+data.response.data[i].protocol+'</td>'+
              '<td class="d-none d-lg-table-cell">'+data.response.data[i].to.name+'</td>'+
              '<td class="d-none d-lg-table-cell">'+data.response.data[i].price.toLocaleString('pt-BR', {minimumFractionDigits: 2 , style: 'currency', currency: 'BRL'})+'</td>'+
              '<td class="text-right">'+
                '<a href="javascript: pesquisarEtiqueta(\''+data.response.data[i].id+'\');"><img class="img-visualizar" title="Detalhes" src="'+$("#nome_site").val()+'imagens/acao-visualizar.png"></a>'+
                '<a class="ml-2" href="javascript: removeEtiquetaCarrinho(\''+data.pedidos[i]+'\');"><img class="img-excluir" title="Remover" src="'+$("#nome_site").val()+'imagens/acao-excluir.png"></a>'+
              '</td>'+
            '</tr>'
          );
        }
        if(data.response.last_page != pagina){
          abrirCarrinhoEtiquetas(pagina+1)
        } else {
          atualizaDataTableEtiquetas();
          fechaLoader();
          $("#modal-carrinho").modal("show");   
        }
      }   
    },
    beforeSend: function() {
      abreLoader();
    }
  }); 
}

$("#modal-carrinho-btn-comprar").click(function(){
  setTimeout(() => { location.reload(); }, 0);
});

function pesquisarEtiqueta(id){
  $.ajax({
    url: "modulos/envios/php/pesquisar-etiqueta.php",
    type: "POST",
    data: {"id": id},
    dataType: "json",
    success: function (data){  

      var embalagens = '<li class="mb-3"><ul class="d-inline-flex">';
      for(var i = 0; i < data[0].volumes.length; i++){
        embalagens += '<li class="mr-3">Formato: '+data[0].volumes[i].format+'<br>Peso: '+data[0].volumes[i].weight+'kg<br>Altura: '+data[0].volumes[i].height+"cm<br>Largura: "+data[0].volumes[i].width+"cm<br>Comprimento: "+data[0].volumes[i].length+'cm<br> Diâmetro: '+data[0].volumes[i].diameter+'cm</li>';   
      }

      embalagens += '</ul></li>';

      var endereco_complemento = '';
      if(data[0].to.complement != '' | data[0].to.complement != null){
        endereco_complemento = ' - '+data[0].to.complement;
      }
      
      $("#modal-visualizar-etiqueta .modal-body").empty();

      $("#modal-visualizar-etiqueta .modal-body").html(
        '<div>'+
          '<ul>'+
            '<li class="mt-2 mb-4"><img src="https://melhorenvio.com.br'+data[0].service.company.picture+'"></li>'+
            '<li class="mb-2">ETIQUETA: <b class="codigo-pedido">'+data[0].protocol+'</b></li><li>ENVIO POR: '+data[0].service.company.name+'</li>'+
            '<li>MODALIDADE: '+data[0].service.name+'</li>'+
            '<li>PREÇO: '+data[0].price.toLocaleString('pt-BR', {minimumFractionDigits: 2 , style: 'currency', currency: 'BRL'})+'</li>'+
            '<li>VALOR SEGURADO: '+data[0].insurance_value.toLocaleString('pt-BR', {minimumFractionDigits: 2 , style: 'currency', currency: 'BRL'})+'</li>'+
            '<li class="mb-2">PRAZO: '+data[0].delivery_min+' - '+data[0].delivery_max+' dias úteis</li>'+
            '<li>DESTINO</li>'+
            '<li>Nome: '+data[0].to.name+'</li>'+
            '<li>Documento: '+data[0].to.document+'</li>'+
            '<li>Endereço: '+data[0].to.address+', '+data[0].to.location_number+endereco_complemento+' - '+data[0].to.city+'/'+data[0].to.state_abbr+'</li>'+
            '<li class="mb-2">Cep: '+data[0].to.postal_code+'</li>'+
            '<li>EMBALAGEM</li>'+
            embalagens+
          '</ul>'+
        '</div>'
      ); 
      fechaLoader();   
      $("#modal-visualizar-etiqueta").modal('show');    
    },
    beforeSend: function() {
      abreLoader();
    }
  }); 
}

function pesquisarEtiquetas(codigo){
  $.ajax({
    url: "modulos/envios/php/pesquisar-etiquetas.php",
    type: "POST",
    data: {"codigo": codigo},
    dataType: "json",
    success: function (data){  
      if(data[0].pesquisas == 0){
        fechaLoader(); 
        mensagemAviso('erro', 'Este pedido ainda não foi gerado.', 3000);
      } else {
          
        var endereco_complemento = '';
        if(data[0].pesquisas[0][0].to.complement != '' | data[0].pesquisas[0][0].to.complement != null){
          endereco_complemento = ' - '+data[0].pesquisas[0][0].to.complement;
        }
        
        $("#modal-visualizar-etiqueta .modal-body").empty();
        $("#modal-visualizar-etiqueta .modal-body").append(          
          '<div>'+
            '<ul>'+
              '<li class="mt-2 mb-4"><img src="https://melhorenvio.com.br'+data[0].pesquisas[0][0].service.company.picture+'"></li>'+
              '<li>ENVIO POR: '+data[0].pesquisas[0][0].service.company.name+'</li>'+
              '<li>MODALIDADE: '+data[0].pesquisas[0][0].service.name+'</li>'+
            '</ul>'+
          '</div>'+
          '<div>'+
            '<ul>'+
              '<li>DESTINO</li>'+
              '<li>Nome: '+data[0].pesquisas[0][0].to.name+'</li>'+
              '<li>Documento: '+data[0].pesquisas[0][0].to.document+'</li>'+
              '<li>Endereço: '+data[0].pesquisas[0][0].to.address+', '+data[0].pesquisas[0][0].to.location_number+endereco_complemento+' - '+data[0].pesquisas[0][0].to.city+'/'+data[0].pesquisas[0][0].to.state_abbr+'</li>'+
              '<li class="mb-2">Cep: '+data[0].pesquisas[0][0].to.postal_code+'</li>'+
            '</ul>'+
          '</div>'
        );

        for(var i = 0; i < data[0].pesquisas.length; i++){

          var embalagens = '<li class="mb-3"><ul class="d-inline-flex">';
          for(var j = 0; j < data[0].pesquisas[i][0].volumes.length; j++){
            embalagens += '<li class="mr-3">Formato: '+data[0].pesquisas[i][0].volumes[j].format+'<br>Peso: '+data[0].pesquisas[i][0].volumes[j].weight+'kg<br>Altura: '+data[0].pesquisas[i][0].volumes[j].height+"cm<br>Largura: "+data[0].pesquisas[i][0].volumes[j].width+"cm<br>Comprimento: "+data[0].pesquisas[i][0].volumes[j].length+'cm<br> Diâmetro: '+data[0].pesquisas[i][0].volumes[j].diameter+'cm</li>';   
          }
          embalagens += '</ul></li>';

          var informacoes_complementares = '';
          if(data[0].pesquisas[i][0].paid_at != null){ informacoes_complementares += '<li>PAGO: '+data[0].pesquisas[i][0].paid_at+'</li>'; }
          if(data[0].pesquisas[i][0].posted_at != null){ informacoes_complementares += '<li>POSTADO: '+data[0].pesquisas[i][0].posted_at+'</li>'; }
          if(data[0].pesquisas[i][0].receipt){ informacoes_complementares += '<li>RECEBIDO: '+data[0].pesquisas[i][0].receipt+'</li>'; }
          if(data[0].pesquisas[i][0].receipt_code != null){ informacoes_complementares += '<li>RECEBIDO CÓDIGO: '+data[0].pesquisas[i][0].receipt_code+'</li>'; }

          $("#modal-visualizar-etiqueta .modal-body").append(
            '<div>'+
              '<ul>'+
                '<li>ETIQUETA: <b class="codigo-pedido">'+data[0].pesquisas[i][0].protocol+'</b></li>'+
                '<li>STATUS: <b class="codigo-pedido">'+data[0].pesquisas[i][0].status+'</b></li>'+
                informacoes_complementares+
                '<li>PREÇO: '+data[0].pesquisas[i][0].price.toLocaleString('pt-BR', {minimumFractionDigits: 2 , style: 'currency', currency: 'BRL'})+'</li>'+
                '<li>VALOR SEGURADO: '+data[0].pesquisas[i][0].insurance_value.toLocaleString('pt-BR', {minimumFractionDigits: 2 , style: 'currency', currency: 'BRL'})+'</li>'+
                '<li class="mb-2">PRAZO: '+data[0].pesquisas[i][0].delivery_min+' - '+data[0].pesquisas[i][0].delivery_max+' dias úteis</li>'+                
                '<li>EMBALAGEM</li>'+
                embalagens+
              '</ul>'+
            '</div>'
          ); 
        }
        
        $("#modal-visualizar-etiqueta").modal('show');  
        fechaLoader(); 
      }    
    },
    beforeSend: function() {
      abreLoader();
    }
  }); 
}

function totalCarrinho(){
  $.ajax({
    url: "modulos/envios/php/busca-carrinho.php",
    type: "POST",
    dataType: "json",
    data: {"pagina": 1},
    success: function (data){  
      $("#menu-carrinho-quantidade").html(data.total);
      buscando_total_carrinho = false;
      if(!buscando_total_carrinho & !buscando_saldo_melhor_envio){
        fechaLoader();
      }
    }
  });   
}

function abreModalSaldo(){
  $("#modal-inserir-saldo").modal("show");
}

function saldoMelhorEnvio(){
  $.ajax({
    url: "modulos/envios/php/saldo-melhor-envio.php",
    type: "POST",
    dataType: "json",
    success: function (data){  
      $("#saldo-melhor-envio").val(data.balance);
      $("#reservado-melhor-envio").val(data.reserved);
      $("#debito-melhor-envio").val(data.debts);
      $(".envios-saldo-melhor-envio-completo").html(
        '<ul class="mb-0">'+
          '<li id="envios-saldo-melhor-envio-completo-disponivel">Disponível: '+(data.balance-data.reserved-data.debts).toLocaleString('pt-BR', {minimumFractionDigits: 2 , style: 'currency', currency: 'BRL'})+'</li>'+
          '<li id="envios-saldo-melhor-envio-completo-saldo">Saldo: '+data.balance.toLocaleString('pt-BR', {minimumFractionDigits: 2 , style: 'currency', currency: 'BRL'})+'</li>'+
          '<li id="envios-saldo-melhor-envio-completo-reservado">Reservado: '+data.reserved.toLocaleString('pt-BR', {minimumFractionDigits: 2 , style: 'currency', currency: 'BRL'})+'</li>'+
          '<li id="envios-saldo-melhor-envio-completo-debitos">Débitos: '+data.debts.toLocaleString('pt-BR', {minimumFractionDigits: 2 , style: 'currency', currency: 'BRL'})+'</li>'+
        '</ul>'
      ); 
      $(".envios-saldo-melhor-envio-simples").html(
        '<ul class="mb-0">'+
          '<li><b>Saldo: '+(data.balance-data.reserved-data.debts).toLocaleString('pt-BR', {minimumFractionDigits: 2 , style: 'currency', currency: 'BRL'})+'</li>'+
        '</ul>'
      );
      buscando_saldo_melhor_envio = false;
      if(!buscando_total_carrinho & !buscando_saldo_melhor_envio){
        fechaLoader();
      }
    }
  });   
}

function fechaTelaDetalhes(){
  $("#envios-tela-detalhes").hide();
}

function atualizaDataTableEtiquetas(){

  DT2 = $('#lista-etiquetas').DataTable({
    "order": [[ 2, 'desc' ]],
    "lengthMenu": [[10, 20, -1], [10, 20, "Tudo"]],
    "pagingType": "numbers",
    "columns": [
      {"orderable": false},
      {"orderable": false},
      {"orderable": true},
      {"orderable": true},
      {"orderable": true},
      {"orderable": true},
      {"orderable": false}
    ],
    columnDefs: [{
      width: 40,
      orderable: false,
      className: 'select-checkbox',
      targets: 0
    }],
    select: {
        style:    'os',
        selector: 'td:first-child'
    },
    'select': 'multi',
    "language": {
      "sEmptyTable": "Nenhum registro encontrado",
      "sInfo": " _START_ até _END_ de _TOTAL_ ",
      "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
      "sInfoFiltered": "(Filtrados de _MAX_ registros)",
      "sInfoPostFix": "",
      "sInfoThousands": ".",
      "sLengthMenu": "_MENU_",
      "sLoadingRecords": "Carregando...",
      "sProcessing": "Processando...",
      "sZeroRecords": "Nenhum registro encontrado",
      "sSearch": "",
      "oPaginate": {
          "sNext": "Próximo",
          "sPrevious": "Anterior",
          "sFirst": "Primeiro",
          "sLast": "Último"
      },
      "oAria": {
          "sSortAscending": ": Ordenar colunas de forma ascendente",
          "sSortDescending": ": Ordenar colunas de forma descendente"
      },
      "select": {
          "rows": {
              "_": "Selecionado %d linhas",
              "0": "Nenhuma linha selecionada",
              "1": "Selecionado 1 linha"
          }
      }
    }
  }).draw();  
  
  $("#lista-etiquetas_length").closest(".col-sm-12").removeClass("col-sm-12 col-md-6").addClass("col-3");
  $("#lista-etiquetas_filter").closest(".col-sm-12").removeClass("col-sm-12 col-md-6").addClass("col-9");
  $("#lista-etiquetas_filter").addClass("text-right");
  $("#lista-etiquetas_filter input").attr("placeholder", "Pesquisar...");

  $("#lista-etiquetas tbody a").click(function(e){
    e.stopPropagation();
  });

  $(".selectAll2").on("click",function(e) {
    if($(this).is(":checked")) {
        DT2.rows().select();        
    } else {
        DT2.rows().deselect(); 
    }
  });

  DT2
  .on('select', function(a,b) {
    selecionaMesmosPedidos(DT2.rows(b[0][0]).data()[0][2]);
    calculaTotalSelecionadoCarrinho();
  })
  .on('deselect', function(a,b) {
    deselecionaMesmosPedidos(DT2.rows(b[0][0]).data()[0][2]);
    calculaTotalSelecionadoCarrinho();
  });

}

function calculaTotalSelecionadoCarrinho(){
  var preco_total = 0;
  var array_rows = DT2.rows({ selected: true }).data();
  var total_rows = DT2.rows({ selected: true }).data().length;

  for(var i = 0; i < total_rows; i++){
    preco_total = preco_total+parseFloat(array_rows[i][1]);
  }
  
  if(preco_total != 0){
    if(($("#saldo-melhor-envio").val()-$("#reservado-melhor-envio").val()-$("#debito-melhor-envio").val()) < preco_total){
      $("#modal-carrinho-btn-comprar").attr("disabled",true);
      $(".modal-carrinho-total-selecionado").html('<div class="saldo-insuficiente">Total selecionado: '+preco_total.toLocaleString('pt-BR', {minimumFractionDigits: 2 , style: 'currency', currency: 'BRL'})+'</div>');
    } else {
      $("#modal-carrinho-btn-comprar").attr("disabled",false);
      $(".modal-carrinho-total-selecionado").html('<div class="saldo-suficiente">Total selecionado: '+preco_total.toLocaleString('pt-BR', {minimumFractionDigits: 2 , style: 'currency', currency: 'BRL'})+'</div>');
    }
  } else {      
    $("#modal-carrinho-btn-comprar").attr("disabled",true);
    $(".modal-carrinho-total-selecionado").html('<div class="saldo-zerado">Total selecionado: R$ 0,00</div>');
  }
}

function ajustaFormCompraEtiqueta(){
  var rows_selecionadas = '';
  DT2.rows({ selected: true }).every(function(rowIdx, tableLoop, rowLoop){
    rows_selecionadas += this.data().DT_RowId+',';
  });
  rows_selecionadas = rows_selecionadas.slice(0,-1);
  $("#ids-etiquetas-compra").val(rows_selecionadas);
}

function selecionaMesmosPedidos(row_selecionada){
  DT2.rows({ selected: false }).every(function(rowIdx, tableLoop, rowLoop){
    if(this.data()[2] == row_selecionada){
      this.select();
    }
  });
  ajustaFormCompraEtiqueta();
}

function deselecionaMesmosPedidos(row_selecionada){
  DT2.rows({ selected: true }).every(function(rowIdx, tableLoop, rowLoop){
    if(this.data()[2] == row_selecionada){
      this.deselect();
    }
  });
  ajustaFormCompraEtiqueta();
}