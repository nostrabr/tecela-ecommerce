if($("#admin-lista").length != 0){
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
      "order": [[ 1, 'desc' ]],
      "lengthMenu": [[50, 100, 500, -1], [50, 100, 500, "Tudo"]],
      "pagingType": "numbers",
      "columns": [
        {"orderable": true},
        {"orderable": true, "type": 'date-br'},
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

function edita(identificador){
  window.location.href = 'orcamentos-visualiza.php?id='+identificador;
}
