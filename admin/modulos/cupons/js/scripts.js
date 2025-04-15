if($("#admin-lista").length != 0 | $("#admin-lista-dois").length != 0){
  abreLoader();
}

$(document).ready(function () {

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
      "order": [[ 2, 'desc' ]],
      "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "Tudo"]],
      "pagingType": "numbers",
      "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true, "type": 'date-br'},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true}
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
    $('#admin-lista-dois').DataTable({
      "order": [[ 2, 'desc' ]],
      "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "Tudo"]],
      "pagingType": "numbers",
      "columns": [
        {"orderable": true, "type": 'date-br'},
        {"orderable": true},
        {"orderable": true}
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

$(".botao-status").click(function(e){
  e.stopPropagation();
});

function edita(identificador){
  window.location.href = 'cupons-edita.php?id='+identificador;
}

function trocaStatus(identificador, status){  
  $.ajax({
    url: "modulos/cupons/php/troca-status.php",
    type: "POST",
    data: {"identificador": identificador},
    success: function (data){
      if(data === "SESSAO INVALIDA") {
        window.location.href = 'logout.php'
      } else if(data === "ERRO BANCO") {
        mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
      } else if(data === "OK") {  
        $(".botao-status").click(function(e){
          e.stopPropagation();
        });
      } else {
        mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
      }
    },
    beforeSend: function() {    
      if(status == 1){
        $("#status-"+identificador).html('<a class="botao-status" href="javascript: trocaStatus(\''+identificador+'\',0)" title="Ativar"><img class="status-desativado" src="'+$("#nome_site").val()+'imagens/status-inativo.png" alt="Inativo"></a><span class="d-none">desligado off inativo 1</span>');
      } else if(status == 0){
        $("#status-"+identificador).html('<a class="botao-status" href="javascript: trocaStatus(\''+identificador+'\',1)" title="Desativar"><img class="status-ativado" src="'+$("#nome_site").val()+'imagens/status-ativo.png" alt="Ativo"></a><span class="d-none">ligado on ativo 1</span>');
      }
    }
  });  
}