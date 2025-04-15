$(document).ready(function () {

  enviaEmailsPesquisaSatisfacao();

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
    $('#admin-lista').DataTable({
      "order": [[ 1, 'desc' ]],
      "lengthMenu": [[50, 100, 500, -1], [50, 100, 500, "Tudo"]],
      "pagingType": "numbers",
      "columns": [
        {"orderable": true},
        {"orderable": true, "type": 'date-br'},
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

    fechaLoader();
    
  }

});

function enviaEmailsPesquisaSatisfacao(){
  $.ajax({
    url: "modulos/pedidos/php/envia-emails-rastreamento.php"
  });
}

function edita(identificador){
  window.location.href = 'pedidos-visualiza.php?id='+identificador;
}

$(".acao-upload-comprovante").click(function(e){
  e.stopPropagation();
});

$(".acao-whats").click(function(e){
  e.stopPropagation();
});

function modalComprovantePix(identificador){
  $("#identificador-pedido-pix").val(identificador);
  $("#modal-comprovante-pix").modal('show');
}

function imprimirPedido(){

  $("#body-admin").css('padding','30px');
  $(".acao-whats").hide();
  $("#menu-cabecalho").hide();
  $("#menu").hide();
  $("#menu-rodape").hide();
  $(".btn-top-right").hide();
  $("#logo-para-impressao").show();
  $("#pedido-titulo").removeClass('col-4').addClass('col-12').css('text-align','center');
  $(".cliente-pedidos-produto-imagem-container").each(function(){
    $(this).removeClass('col-4').addClass('col-2');
  });
  
  window.print();

  $("#body-admin").css('padding','45px 35px 50px 285px');
  $(".acao-whats").show();
  $("#menu-cabecalho").show();
  $("#menu").show();
  $("#menu-rodape").show();
  $(".btn-top-right").show();
  $("#logo-para-impressao").hide();
  $("#pedido-titulo").removeClass('col-12').addClass('col-4').css('text-align','left');
  $(".cliente-pedidos-produto-imagem-container").each(function(){
    $(this).removeClass('col-2').addClass('col-4');
  });

}

