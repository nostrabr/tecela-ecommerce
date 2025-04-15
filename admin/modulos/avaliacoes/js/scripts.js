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
          {"orderable": false},
          {"orderable": false},
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
      "order": [[ 1, 'desc' ]],
      "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "Tudo"]],
      "pagingType": "numbers",
      "columns": [
          {"orderable": true},
          {"orderable": true, "type": 'date-br'},
          {"orderable": true},
          {"orderable": false},
          {"orderable": false},
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

  
function visualiza(nota, data, comentario, lida, identificador, produto, replica, data_replica, cliente){

    var nota_imagem = '';

    if(nota == 1){
        nota_imagem = 
        '<div class="avaliacao-loja">'+
            '<ul>'+
                '<li><img class="estrela img-dourada" estrela="1" id="estrela-1" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Muito ruim" alt="1 estrela"></li>'+
                '<li><img class="estrela" estrela="2" id="estrela-2" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Ruim" alt="2 estrelas"></li>'+
                '<li><img class="estrela" estrela="3" id="estrela-3" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Regular" alt="3 estrelas"></li>'+
                '<li><img class="estrela" estrela="4" id="estrela-4" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Boa" alt="4 estrelas"></li>'+
                '<li><img class="estrela" estrela="5" id="estrela-5" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Muito boa" alt="5 estrelas"></li>'+
            '</ul>'+
        '</div>';
    } else if(nota == 2){
        nota_imagem = 
        '<div class="avaliacao-loja">'+
            '<ul>'+
                '<li><img class="estrela img-dourada" estrela="1" id="estrela-1" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Muito ruim" alt="1 estrela"></li>'+
                '<li><img class="estrela img-dourada" estrela="2" id="estrela-2" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Ruim" alt="2 estrelas"></li>'+
                '<li><img class="estrela" estrela="3" id="estrela-3" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Regular" alt="3 estrelas"></li>'+
                '<li><img class="estrela" estrela="4" id="estrela-4" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Boa" alt="4 estrelas"></li>'+
                '<li><img class="estrela" estrela="5" id="estrela-5" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Muito boa" alt="5 estrelas"></li>'+
            '</ul>'+
        '</div>';
    } else if(nota == 3){
        nota_imagem = 
        '<div class="avaliacao-loja">'+
            '<ul>'+
                '<li><img class="estrela img-dourada" estrela="1" id="estrela-1" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Muito ruim" alt="1 estrela"></li>'+
                '<li><img class="estrela img-dourada" estrela="2" id="estrela-2" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Ruim" alt="2 estrelas"></li>'+
                '<li><img class="estrela img-dourada" estrela="3" id="estrela-3" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Regular" alt="3 estrelas"></li>'+
                '<li><img class="estrela" estrela="4" id="estrela-4" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Boa" alt="4 estrelas"></li>'+
                '<li><img class="estrela" estrela="5" id="estrela-5" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Muito boa" alt="5 estrelas"></li>'+
            '</ul>'+
        '</div>';
    } else if(nota == 4){ 
        nota_imagem = 
        '<div class="avaliacao-loja">'+
            '<ul>'+
                '<li><img class="estrela img-dourada" estrela="1" id="estrela-1" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Muito ruim" alt="1 estrela"></li>'+
                '<li><img class="estrela img-dourada" estrela="2" id="estrela-2" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Ruim" alt="2 estrelas"></li>'+
                '<li><img class="estrela img-dourada" estrela="3" id="estrela-3" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Regular" alt="3 estrelas"></li>'+
                '<li><img class="estrela img-dourada" estrela="4" id="estrela-4" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Boa" alt="4 estrelas"></li>'+
                '<li><img class="estrela" estrela="5" id="estrela-5" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Muito boa" alt="5 estrelas"></li>'+
            '</ul>'+
        '</div>';
    } else if(nota == 5){
        nota_imagem = 
        '<div class="avaliacao-loja">'+
            '<ul>'+
                '<li><img class="estrela img-dourada" estrela="1" id="estrela-1" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Muito ruim" alt="1 estrela"></li>'+
                '<li><img class="estrela img-dourada" estrela="2" id="estrela-2" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Ruim" alt="2 estrelas"></li>'+
                '<li><img class="estrela img-dourada" estrela="3" id="estrela-3" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Regular" alt="3 estrelas"></li>'+
                '<li><img class="estrela img-dourada" estrela="4" id="estrela-4" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Boa" alt="4 estrelas"></li>'+
                '<li><img class="estrela img-dourada" estrela="5" id="estrela-5" src="'+$("#nome_site").val()+'imagens/avaliacao-estrela.png" title="Muito boa" alt="5 estrelas"></li>'+
            '</ul>'+
        '</div>';
    }

    $("#modal-visualizacao-comentario #visualizacao-comentario-nota").html(nota_imagem);
    $("#modal-visualizacao-comentario #visualizacao-comentario-data").html(data);
    if(comentario != '')
      $("#modal-visualizacao-comentario #visualizacao-comentario-cliente").html(cliente+':');
    else 
      $("#modal-visualizacao-comentario #visualizacao-comentario-cliente").html('');
    $("#modal-visualizacao-comentario #visualizacao-comentario-produto").html(produto);
    $("#modal-visualizacao-comentario #visualizacao-comentario-comentario").html(comentario);
    if(replica == ''){
      $("#modal-visualizacao-comentario #visualizacao-comentario-replica-container").hide();
    } else {
      $("#modal-visualizacao-comentario #visualizacao-comentario-replica-container").show();
    }
    $("#modal-visualizacao-comentario #visualizacao-comentario-replica").html(replica+"<br><i class='data-replica'>Data: "+data_replica+"</i>");
    $("#modal-visualizacao-comentario").modal('show');
    
        
    if(lida == 0){
        $.ajax({
            url: "modulos/avaliacoes/php/le-avaliacao.php",
            type: "POST",
            dataType: "json",
            data: {"identificador": identificador},
            beforeSend: function() {
                var n_lidas = parseInt($(".n-nao-lidas").html());
                if(n_lidas == 1){
                    $(".n-nao-lidas").hide()
                } else if(n_lidas > 1){
                    $(".n-nao-lidas").html(n_lidas-1);
                }
                $("#avaliacao-"+identificador+" .img-email-nao-lido").attr('src',$("#nome_site").val()+'imagens/email-lido.png').attr('title','Lido');
                $("#avaliacao-"+identificador).removeClass('comentario-nao-lido');
            }
        });
    }

}

$("#mostrar-avaliacoes").change(function(){
  
    var status = $(this).prop("checked");
    if(status){ status = 1; } 
    else { status = 0; }
    
    $.ajax({
      type: "POST",
      url: "modulos/avaliacoes/php/troca-status-mostrar-avaliacoes.php",
      data: {"status": status},
      success: function(data) {
        fechaLoader();
        if(data === "SESSAO INVALIDA") {
          window.location.href = 'logout.php'
        } else if(data === "ERRO BANCO") {
          mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
        } else if(data === "OK") { 
          mensagemAviso('sucesso', '', 1000);
        } else {
          mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
        }
      },
      beforeSend: function() {
        abreLoader();
      }
    });
  
});

$(".botao-status").click(function(e){
    e.stopPropagation();
});
$(".botao-replica").click(function(e){
    e.stopPropagation();
});

function replica(identificador, replica){
  $("#modal-replica #visualizacao-comentario-identificador").val(identificador);
  $("#modal-replica #replica").val(replica);
  $("#modal-replica").modal('show');
}

function trocaStatus(identificador, status){ 
  $.ajax({
    url: "modulos/avaliacoes/php/troca-status.php",
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
  