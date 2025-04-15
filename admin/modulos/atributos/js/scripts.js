if($("#admin-lista").length != 0){
  abreLoader();
}

$(document).ready(function () {

  if($("#admin-lista").length != 0){
    $('#admin-lista').DataTable({
      "order": [[ 0, 'asc' ]],
      "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "Tudo"]],
      "pagingType": "numbers",
      "columns": [
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

  cont_caracteristicas = $("#n_caracteristicas").val();
  verificaVisualizacao();

});

function edita(identificador){
  window.location.href = 'atributos-edita.php?id='+identificador;
}

$(".botao-excluir").click(function(e){
  e.stopPropagation();
});

function exclui(identificador, nome){
    
  var confirma = confirm("Deletar um atriburo irá remover todas as suas características e à removerá dos produtos. Confirma a exclusão do atriburo "+nome+"?");

  if(confirma){
    $.ajax({
      url: "modulos/atributos/php/exclui-atributo.php",
      type: "POST",
      data: {"identificador": identificador},
      success: function (data){
        if(data === "SESSAO INVALIDA") {
          window.location.href = 'logout.php'
        } else if(data != '' & data != 'SESSAO INVALIDA') {
          mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
        }
      },
      beforeSend: function() {
        $("#atributo-"+identificador).remove();
      }
    }); 
  }

}

//FUNÇÕES PARA AS CARACTERÍSTICAS

function addCaracteristica(){
  cont_caracteristicas++;
  $("#n_caracteristicas").val(parseInt($("#n_caracteristicas").val())+1);
  $("#caracteristicas").append(
    '<div class="row" id="container-caracteristica-'+cont_caracteristicas+'">'+
      '<div class="col-1 d-flex align-items-center col-add-remove-caracteristica">'+
        '<a class="acao-add-remove-caracteristica-produto" href="javascript: removeCaracteristica('+cont_caracteristicas+', false)"><img class="img-add-remove-caracteristica" src="'+$("#nome_site").val()+'imagens/remover.png" alt="Remover característica"></a>'+
      '</div>'+
      '<div class="col caracteristica-nome">'+
          '<div class="form-group">'+
              '<input type="text" class="form-control text-capitalize" name="caracteristica-'+cont_caracteristicas+'" id="caracteristica-'+cont_caracteristicas+'" placeholder="Nome" maxlength="50" required>'+
          '</div>'+
      '</div>'+
      '<div class="col-3 caracteristica-cor">'+
          '<div class="form-group">'+
              '<input type="color" class="form-control" name="cor-'+cont_caracteristicas+'" placeholder="Cor" id="cor-'+cont_caracteristicas+'">'+
          '</div>'+
      '</div>'+
      '<div class="col-6 caracteristica-textura">'+
          '<div class="form-group">'+
              '<input type="file" name="imagem-'+cont_caracteristicas+'" id="imagem-'+cont_caracteristicas+'" class="form-control-file imagem" accept=".png, .jpg, .gif, .jpeg" onchange="javascript: inputFileChange('+cont_caracteristicas+');">'+
              '<input type="text" name="arquivo-'+cont_caracteristicas+'" class="arquivo" id="arquivo-'+cont_caracteristicas+'" placeholder="Textura" readonly="readonly">'+
              '<input type="button" class="btn-escolher" value="ESCOLHER" onclick="javascript: inputFileEscolher('+cont_caracteristicas+');">'+
          '</div>'+
      '</div>'+
    '</div>'
  )
  verificaVisualizacao();
}

function removeCaracteristica(n, antigo, identificador){

  var confirma = true;

  if(antigo)
    var confirma = confirm("Deletar uma caracteristica irá remove-la dos produtos. Confirma a exclusão?");

  if(confirma){

    $("#n_caracteristicas").val(parseInt($("#n_caracteristicas").val())-1);
    $("#container-caracteristica-"+n).remove();

    if(antigo){
      $.ajax({
        url: "modulos/atributos/php/exclui-caracteristica.php",
        type: "POST",
        data: {"identificador": identificador},
        success: function (data){
          if(data === "SESSAO INVALIDA") {
            window.location.href = 'logout.php'
          } else if(data != '' & data != 'SESSAO INVALIDA') {
            mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
          }
        }
      }); 
    }

  }

}

function verificaVisualizacao(){
  var visualizacao = $("#visualizacao").val();
  if(visualizacao == 'C'){
    $(".caracteristica-nome").addClass('pr-1');
    $(".caracteristica-cor").addClass('pl-1').show();
    $(".caracteristica-textura").removeClass('pl-1').hide();
  } else if(visualizacao == 'T'){
    $(".caracteristica-nome").addClass('pr-1');
    $(".caracteristica-cor").removeClass('pr-1 pl-1').hide();
    $(".caracteristica-textura").addClass('pl-1').show();
  } else {
    $(".caracteristica-nome").removeClass('pr-1');
    $(".caracteristica-cor").removeClass('pr-1 pl-1').hide();
    $(".caracteristica-textura").removeClass('pl-1').hide();
  }
}