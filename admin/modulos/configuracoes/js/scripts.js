if($("#admin-lista").length != 0 | $("#admin-lista-dois").length != 0 | $("#admin-lista-tres").length != 0 | $("#admin-lista-quatro").length != 0 | $("#admin-lista-cinco").length != 0 | $("#admin-lista-sete").length != 0){
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

  if($("#admin-lista-dois").length != 0){
    $('#admin-lista-dois').DataTable({
      "order": [[ 0, 'asc' ]],
      "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "Tudo"]],
      "pagingType": "numbers",
      "columns": [
        {"orderable": true},
        {"orderable": false},
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

  if($("#admin-lista-tres").length != 0){
    $('#admin-lista-tres').DataTable({
      "lengthMenu": [[50], [50]],
      "pagingType": "numbers",
      "aaSorting": [],
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

  if($("#admin-lista-quatro").length != 0){
    $('#admin-lista-quatro').DataTable({
      "order": [[ 0, 'asc' ]],
      "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "Tudo"]],
      "pagingType": "numbers",
      "columns": [
        {"orderable": true},
        {"orderable": false},
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

  if($("#admin-lista-cinco").length != 0){
    $('#admin-lista-cinco').DataTable({
      "order": [[ 1, 'asc' ]],
      "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "Tudo"]],
      "pagingType": "numbers",
      "columns": [
        {"orderable": false},
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

  if($("#admin-lista-seis").length != 0){
    $('#admin-lista-cinco').DataTable({
      "order": [[ 1, 'asc' ]],
      "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "Tudo"]],
      "pagingType": "numbers",
      "columns": [
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

    fechaLoader();
    
  }

  if($("#admin-lista-sete").length != 0){
    $('#admin-lista-sete').DataTable({
      "order": [[ 0, 'asc' ]],
      "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "Tudo"]],
      "pagingType": "numbers",
      "columns": [
        {"orderable": true},
        {"orderable": false}
      ],
      "aaSorting": [],
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

  if($("#n-faixas-desconto").length > 0){
    cont_faixas_desconto = parseInt($("#n-faixas-desconto").val());
  }

});

$(".botao-status").click(function(e){
  e.stopPropagation();
});

$(".botao-excluir").click(function(e){
  e.stopPropagation();
});

$(".botao-duplicar").click(function(e){
  e.stopPropagation();
});

function edita(identificador){
  window.location.href = 'configuracoes-edita-usuarios.php?id='+identificador;
}

function editaBanner(identificador){
  window.location.href = 'configuracoes-design-banners-edita.php?id='+identificador;
}

function editaBannerSecundario(identificador){
  window.location.href = 'configuracoes-design-banners-secundarios-edita.php?id='+identificador;
}

function editaBannerProduto(identificador){
  window.location.href = 'configuracoes-design-banners-produto-edita.php?id='+identificador;
}

function editaInformacaoAdicional(identificador){
  window.location.href = 'configuracoes-design-informacoes-adicionais-edita.php?id='+identificador;
}

function editaPaginaCustomizada(identificador){
  window.location.href = 'configuracoes-paginas-customizadas-edita.php?id='+identificador;
}

function trocaStatus(identificador, status){  
  $.ajax({
    url: "modulos/configuracoes/php/troca-status-usuario.php",
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

function trocaStatusBanner(identificador, status, id, ordem){  
  $.ajax({
    url: "modulos/configuracoes/php/troca-status-banner.php",
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
        $(".botao-excluir").click(function(e){
          e.stopPropagation();
        });
      } else {
        mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
      }
    },
    beforeSend: function() {    
      if(status == 1){
        $("#status-"+identificador).html('<a class="botao-status" href="javascript: trocaStatusBanner(\''+identificador+'\',0,'+id+','+ordem+')" title="Ativar"><img class="status-desativado" src="'+$("#nome_site").val()+'imagens/status-inativo.png" alt="Inativo"></a><span class="d-none">desligado off inativo 1</span><a href="javascript: excluiBanner('+id+','+ordem+');" title="Excluir" class="botao-excluir"><img class="acao-excluir" src="'+$("#nome_site").val()+'imagens/acao-excluir.png"></a>');
      } else if(status == 0){
        $("#status-"+identificador).html('<a class="botao-status" href="javascript: trocaStatusBanner(\''+identificador+'\',1,'+id+','+ordem+')" title="Desativar"><img class="status-ativado" src="'+$("#nome_site").val()+'imagens/status-ativo.png" alt="Ativo"></a><span class="d-none">ligado on ativo 1</span><a href="javascript: excluiBanner('+id+','+ordem+');" title="Excluir" class="botao-excluir"><img class="acao-excluir" src="'+$("#nome_site").val()+'imagens/acao-excluir.png"></a>');
      }
    }
  });  
}

function trocaStatusBannerSecundario(identificador, status, id, ordem){  
  $.ajax({
    url: "modulos/configuracoes/php/troca-status-banner-secundario.php",
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
        $(".botao-excluir").click(function(e){
          e.stopPropagation();
        });
      } else {
        mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
      }
    },
    beforeSend: function() {    
      if(status == 1){
        $("#status-"+identificador).html('<a class="botao-status" href="javascript: trocaStatusBannerSecundario(\''+identificador+'\',0,'+id+','+ordem+')" title="Ativar"><img class="status-desativado" src="'+$("#nome_site").val()+'imagens/status-inativo.png" alt="Inativo"></a><span class="d-none">desligado off inativo 1</span><a href="javascript: excluiBannerSecundario('+id+','+ordem+');" title="Excluir" class="botao-excluir"><img class="acao-excluir" src="'+$("#nome_site").val()+'imagens/acao-excluir.png"></a>');
      } else if(status == 0){
        $("#status-"+identificador).html('<a class="botao-status" href="javascript: trocaStatusBannerSecundario(\''+identificador+'\',1,'+id+','+ordem+')" title="Desativar"><img class="status-ativado" src="'+$("#nome_site").val()+'imagens/status-ativo.png" alt="Ativo"></a><span class="d-none">ligado on ativo 1</span><a href="javascript: excluiBannerSecundario('+id+','+ordem+');" title="Excluir" class="botao-excluir"><img class="acao-excluir" src="'+$("#nome_site").val()+'imagens/acao-excluir.png"></a>');
      }
    }
  });  
}

function trocaStatusBannerProduto(identificador, status, id, ordem){  
  $.ajax({
    url: "modulos/configuracoes/php/troca-status-banner-produto.php",
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
        $(".botao-excluir").click(function(e){
          e.stopPropagation();
        });
      } else {
        mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
      }
    },
    beforeSend: function() {    
      if(status == 1){
        $("#status-"+identificador).html('<a class="botao-status" href="javascript: trocaStatusBannerProduto(\''+identificador+'\',0,'+id+','+ordem+')" title="Ativar"><img class="status-desativado" src="'+$("#nome_site").val()+'imagens/status-inativo.png" alt="Inativo"></a><span class="d-none">desligado off inativo 1</span><a href="javascript: excluiBannerProduto('+id+','+ordem+');" title="Excluir" class="botao-excluir"><img class="acao-excluir" src="'+$("#nome_site").val()+'imagens/acao-excluir.png"></a>');
      } else if(status == 0){
        $("#status-"+identificador).html('<a class="botao-status" href="javascript: trocaStatusBannerProduto(\''+identificador+'\',1,'+id+','+ordem+')" title="Desativar"><img class="status-ativado" src="'+$("#nome_site").val()+'imagens/status-ativo.png" alt="Ativo"></a><span class="d-none">ligado on ativo 1</span><a href="javascript: excluiBannerProduto('+id+','+ordem+');" title="Excluir" class="botao-excluir"><img class="acao-excluir" src="'+$("#nome_site").val()+'imagens/acao-excluir.png"></a>');
      }
    }
  });  
}


function trocaStatusInformacaoAdicional(identificador, status){  
  $.ajax({
    url: "modulos/configuracoes/php/troca-status-informacao-adicional.php",
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
        $("#status-"+identificador).html('<a class="botao-status" href="javascript: trocaStatusBannerSecundario(\''+identificador+'\',0)" title="Ativar"><img class="status-desativado" src="'+$("#nome_site").val()+'imagens/status-inativo.png" alt="Inativo"></a><span class="d-none">desligado off inativo 1</span>');
      } else if(status == 0){
        $("#status-"+identificador).html('<a class="botao-status" href="javascript: trocaStatusBannerSecundario(\''+identificador+'\',1)" title="Desativar"><img class="status-ativado" src="'+$("#nome_site").val()+'imagens/status-ativo.png" alt="Ativo"></a><span class="d-none">ligado on ativo 1</span>');
      }
    }
  });  
}

function trocaStatusPaginaCustomizada(identificador, status, titulo){  
  $.ajax({
    url: "modulos/configuracoes/php/troca-status-pagina-customizada.php",
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
        $(".botao-excluir").click(function(e){
          e.stopPropagation();
        });
      } else {
        mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
      }
    },
    beforeSend: function() {    
      if(status == 1){
        $("#status-"+identificador).html('<a class="botao-status" href="javascript: trocaStatusPaginaCustomizada(\''+identificador+'\',0)" title="Ativar"><img class="status-desativado" src="'+$("#nome_site").val()+'imagens/status-inativo.png" alt="Inativo"></a><span class="d-none">desligado off inativo 1</span><a href="javascript: duplicaPaginaCustomizada(\''+identificador+'\',\''+titulo+'\');" title="Duplicar" class="botao-duplicar"><img class="acao-duplicar" src="'+$("#nome_site").val()+'imagens/acao-duplicar.png"></a><a href="javascript: excluiPaginaCustomizada(\''+identificador+'\',\''+titulo+'\');" title="Excluir" class="botao-excluir"><img class="acao-excluir" src="'+$("#nome_site").val()+'imagens/acao-excluir.png"></a>');
      } else if(status == 0){
        $("#status-"+identificador).html('<a class="botao-status" href="javascript: trocaStatusPaginaCustomizada(\''+identificador+'\',1)" title="Desativar"><img class="status-ativado" src="'+$("#nome_site").val()+'imagens/status-ativo.png" alt="Ativo"></a><span class="d-none">ligado on ativo 1</span><a href="javascript: duplicaPaginaCustomizada(\''+identificador+'\',\''+titulo+'\');" title="Duplicar" class="botao-duplicar"><img class="acao-duplicar" src="'+$("#nome_site").val()+'imagens/acao-duplicar.png"></a><a href="javascript: excluiPaginaCustomizada(\''+identificador+'\',\''+titulo+'\');" title="Excluir" class="botao-excluir"><img class="acao-excluir" src="'+$("#nome_site").val()+'imagens/acao-excluir.png"></a>');
      }
    }
  });  
}

//EXCLUI BANNER
function excluiBanner(idBanner, ordemBanner){
    
  var resposta = confirm("Confirma exclusão do banner " + ordemBanner + "?");
  
  if (resposta === true) {
      
      var urlData = "&id="+idBanner;

      $.ajax({
          type: "POST",
          url: "modulos/configuracoes/php/exclusao-banner.php",
          async: true,
          data: urlData,
          success: function(data) {
            if(data === "SESSAO INVALIDA") {
              window.location.href = 'logout.php'
            } else if(data === "ERRO BANCO") {
              mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
            } else if(data === "OK") { 
              window.location.href = 'configuracoes-design-banners.php'
            } else {
              mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
            }
          },
          beforeSend: function() {
            abreLoader();
          }
      });
      
  } 
  
}

//EXCLUI BANNER
function excluiBannerSecundario(idBanner, ordemBanner){
    
  var resposta = confirm("Confirma exclusão do banner secundário " + ordemBanner + "?");
  
  if (resposta === true) {
      
      var urlData = "&id="+idBanner;

      $.ajax({
          type: "POST",
          url: "modulos/configuracoes/php/exclusao-banner-secundario.php",
          async: true,
          data: urlData,
          success: function(data) {
            if(data === "SESSAO INVALIDA") {
              window.location.href = 'logout.php'
            } else if(data === "ERRO BANCO") {
              mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
            } else if(data === "OK") { 
              window.location.href = 'configuracoes-design-banners-secundarios.php'
            } else {
              mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
            }
          },
          beforeSend: function() {
            abreLoader();
          }
      });
      
  } 
  
}


//EXCLUI BANNER
function excluiBannerProduto(idBanner, ordemBanner){
    
  var resposta = confirm("Confirma exclusão do banner de produto " + ordemBanner + "?");
  
  if (resposta === true) {
      
      var urlData = "&id="+idBanner;

      $.ajax({
          type: "POST",
          url: "modulos/configuracoes/php/exclusao-banner-produto.php",
          async: true,
          data: urlData,
          success: function(data) {
            if(data === "SESSAO INVALIDA") {
              window.location.href = 'logout.php'
            } else if(data === "ERRO BANCO") {
              mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
            } else if(data === "OK") { 
              window.location.href = 'configuracoes-design-banners-produto.php'
            } else {
              mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
            }
          },
          beforeSend: function() {
            abreLoader();
          }
      });
      
  } 
  
}

//DUPLICA PÁGINA CUSTOMIZADA
function duplicaPaginaCustomizada(identificador, titulo){
    
  var resposta = confirm("Confirma duplicação da página " + titulo + "?");
  
  if (resposta === true) {
      
      var urlData = "&id="+identificador;

      $.ajax({
          type: "POST",
          url: "modulos/configuracoes/php/duplicacao-pagina-customizada.php",
          async: true,
          data: urlData,
          success: function(data) {
            if(data === "SESSAO INVALIDA") {
              window.location.href = 'logout.php'
            } else if(data === "ERRO BANCO") {
              mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
            } else if(data === "OK") { 
              window.location.href = 'configuracoes-paginas-customizadas.php'
            } else {
              mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
            }
          },
          beforeSend: function() {
            abreLoader();
          }
      });
      
  } 
  
}

//EXCLUI PÁGINA CUSTOMIZADA
function excluiPaginaCustomizada(identificador, titulo){
    
  var resposta = confirm("Confirma exclusão da página " + titulo + "?");
  
  if (resposta === true) {
      
      var urlData = "&id="+identificador;

      $.ajax({
          type: "POST",
          url: "modulos/configuracoes/php/exclusao-pagina-customizada.php",
          async: true,
          data: urlData,
          success: function(data) {
            if(data === "SESSAO INVALIDA") {
              window.location.href = 'logout.php'
            } else if(data === "ERRO BANCO") {
              mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
            } else if(data === "OK") { 
              window.location.href = 'configuracoes-paginas-customizadas.php'
            } else {
              mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
            }
          },
          beforeSend: function() {
            abreLoader();
          }
      });
      
  } 
  
}

if($(".summernote").length != 0 | $(".summernote11").length != 0){

  // TRADUÇÃO DO SUMMERNOTE
  (function ($) {
    $.extend($.summernote.lang, {
      'pt-BR': {
        font: {
          bold: 'Negrito',
          italic: 'Itálico',
          underline: 'Sublinhado',
          clear: 'Remover estilo da fonte',
          height: 'Altura da linha',
          name: 'Fonte',
          strikethrough: 'Riscado',
          size: 'Tamanho da fonte'
        },
        image: {
          image: 'Imagem',
          insert: 'Inserir imagem',
          resizeFull: 'Redimensionar Completamente',
          resizeHalf: 'Redimensionar pela Metade',
          resizeQuarter: 'Redimensionar um Quarto',
          floatLeft: 'Flutuar para Esquerda',
          floatRight: 'Flutuar para Direira',
          floatNone: 'Não Flutuar',
          dragImageHere: 'Arraste uma imagem para cá',
          selectFromFiles: 'Selecione a partir dos arquivos',
          url: 'URL da imagem'
        },
        video: {
          video: 'Vídeo',
          videoLink: 'Link do ví­deo',
          insert: 'Inserir vídeo',
          url: 'URL do vídeo',
          providers: '(YouTube, Vimeo, Vine, Instagram, DailyMotion ou Youku)'
        },
        link: {
          link: 'Link',
          insert: 'Inserir link',
          unlink: 'Remover link',
          edit: 'Editar',
          textToDisplay: 'Texto para exibir',
          url: 'Para qual URL esse link leva?',
          openInNewWindow: 'Abrir em uma nova janela'
        },
        table: {
          table: 'Tabela'
        },
        hr: {
          insert: 'Inserir linha horizontal'
        },
        style: {
          style: 'Estilo',
          normal: 'Normal',
          blockquote: 'Citação',
          pre: 'Código',
          h1: 'Título 1',
          h2: 'Título 2',
          h3: 'Título 3',
          h4: 'Título 4',
          h5: 'Título 5',
          h6: 'Título 6'
        },
        lists: {
          unordered: 'Lista com marcadores',
          ordered: 'Lista numerada'
        },
        options: {
          help: 'Ajuda',
          fullscreen: 'Tela cheia',
          codeview: 'Ver código-fonte'
        },
        paragraph: {
          paragraph: 'Parágrafo',
          outdent: 'Menor tabulação',
          indent: 'Maior tabulação',
          left: 'Alinhar à esquerda',
          center: 'Alinhar ao centro',
          right: 'Alinha à direita',
          justify: 'Justificado'
        },
        color: {
          recent: 'Cor recente',
          more: 'Mais cores',
          background: 'Fundo',
          foreground: 'Fonte',
          transparent: 'Transparente',
          setTransparent: 'Fundo transparente',
          reset: 'Restaurar',
          resetToDefault: 'Restaurar padrão'
        },
        shortcut: {
          shortcuts: 'Atalhos do teclado',
          close: 'Fechar',
          textFormatting: 'Formatação de texto',
          action: 'Ação',
          paragraphFormatting: 'Formatação de parágrafo',
          documentStyle: 'Estilo de documento'
        },
        history: {
          undo: 'Desfazer',
          redo: 'Refazer'
        },
        help: {
          'insertParagraph': 'Inserir parágrafo',
          'undo': 'Desfazer o último comando',
          'redo': 'Refazer o último comando',
          'tab': 'Tab',
          'untab': 'Desfazer tab',
          'bold': 'Colocar em negrito',
          'italic': 'Colocar em itálico',
          'underline': 'Sublinhado',
          'strikethrough': 'Tachado',
          'removeFormat': 'Remover estilo',
          'justifyLeft': 'Alinhar à esquerda',
          'justifyCenter': 'Centralizar',
          'justifyRight': 'Alinhar à direita',
          'justifyFull': 'Justificar',
          'insertUnorderedList': 'Lista não ordenada',
          'insertOrderedList': 'Lista ordenada',
          'outdent': 'Recuar parágrafo atual',
          'indent': 'Avançar parágrafo atual',
          'formatPara': 'Alterar formato do bloco para parágrafo(tag P)',
          'formatH1': 'Alterar formato do bloco para H1',
          'formatH2': 'Alterar formato do bloco para H2',
          'formatH3': 'Alterar formato do bloco para H3',
          'formatH4': 'Alterar formato do bloco para H4',
          'formatH5': 'Alterar formato do bloco para H5',
          'formatH6': 'Alterar formato do bloco para H6',
          'insertHorizontalRule': 'Inserir régua horizontal',
          'linkDialog.show': 'Inserir um Hiperlink'
        }
      }
    });
  })(jQuery);

  //INICIA O TEXTAREA SUMMERNOTE
  $('.summernote').summernote({
    height: 200,
    lang: "pt-BR",
    fontNames: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    fontNamesIgnoreCheck: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    callbacks: {
        onImageUpload: function(files, editor, $editable) {
            sendFile1(files[0],editor,$editable);
        }
    }, 
    toolbar: [
        ['style', ['style']],
        ['style', ['bold', 'italic', 'underline', 'clear']],   
        ['fontname', ['fontname']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video', 'hr']],
        ['view', ['fullscreen', 'codeview', 'help']]
    ]
  });

  //INICIA O TEXTAREA SUMMERNOTE
  $('.summernote2').summernote({
    height: 200,
    lang: "pt-BR",
    fontNames: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    fontNamesIgnoreCheck: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    callbacks: {
        onImageUpload: function(files, editor, $editable) {
            sendFile2(files[0],editor,$editable);
        }
    }, 
    toolbar: [
        ['style', ['style']],
        ['style', ['bold', 'italic', 'underline', 'clear']],   
        ['fontname', ['fontname']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video', 'hr']],
        ['view', ['fullscreen', 'codeview', 'help']]
    ]
  });

  //INICIA O TEXTAREA SUMMERNOTE
  $('.summernote3').summernote({
    height: 200,
    lang: "pt-BR",
    fontNames: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    fontNamesIgnoreCheck: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    callbacks: {
        onImageUpload: function(files, editor, $editable) {
            sendFile3(files[0],editor,$editable);
        }
    }, 
    toolbar: [
        ['style', ['style']],
        ['style', ['bold', 'italic', 'underline', 'clear']],   
        ['fontname', ['fontname']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video', 'hr']],
        ['view', ['fullscreen', 'codeview', 'help']]
    ]
  });

  //INICIA O TEXTAREA SUMMERNOTE
  $('.summernote4').summernote({
    height: 200,
    lang: "pt-BR",
    fontNames: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    fontNamesIgnoreCheck: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    callbacks: {
        onImageUpload: function(files, editor, $editable) {
            sendFile4(files[0],editor,$editable);
        }
    }, 
    toolbar: [
        ['style', ['style']],
        ['style', ['bold', 'italic', 'underline', 'clear']],   
        ['fontname', ['fontname']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video', 'hr']],
        ['view', ['fullscreen', 'codeview', 'help']]
    ]
  });

  //INICIA O TEXTAREA SUMMERNOTE
  $('.summernote5').summernote({
    height: 200,
    lang: "pt-BR",
    fontNames: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    fontNamesIgnoreCheck: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    callbacks: {
        onImageUpload: function(files, editor, $editable) {
            sendFile5(files[0],editor,$editable);
        }
    }, 
    toolbar: [
        ['style', ['style']],
        ['style', ['bold', 'italic', 'underline', 'clear']],   
        ['fontname', ['fontname']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video', 'hr']],
        ['view', ['fullscreen', 'codeview', 'help']]
    ]
  });

  //INICIA O TEXTAREA SUMMERNOTE
  $('.summernote6').summernote({
    height: 200,
    lang: "pt-BR",
    fontNames: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    fontNamesIgnoreCheck: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    callbacks: {
        onImageUpload: function(files, editor, $editable) {
            sendFile6(files[0],editor,$editable);
        }
    }, 
    toolbar: [
        ['style', ['style']],
        ['style', ['bold', 'italic', 'underline', 'clear']],   
        ['fontname', ['fontname']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video', 'hr']],
        ['view', ['fullscreen', 'codeview', 'help']]
    ]
  });

  //INICIA O TEXTAREA SUMMERNOTE
  $('.summernote7').summernote({
    height: 200,
    lang: "pt-BR",
    fontNames: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    fontNamesIgnoreCheck: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    callbacks: {
        onImageUpload: function(files, editor, $editable) {
            sendFile7(files[0],editor,$editable);
        }
    }, 
    toolbar: [
        ['style', ['style']],
        ['style', ['bold', 'italic', 'underline', 'clear']],   
        ['fontname', ['fontname']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video', 'hr']],
        ['view', ['fullscreen', 'codeview', 'help']]
    ]
  });

  //INICIA O TEXTAREA SUMMERNOTE
  $('.summernote8').summernote({
    height: 200,
    lang: "pt-BR",
    fontNames: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    fontNamesIgnoreCheck: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    callbacks: {
        onImageUpload: function(files, editor, $editable) {
            sendFile8(files[0],editor,$editable);
        }
    }, 
    toolbar: [
        ['style', ['style']],
        ['style', ['bold', 'italic', 'underline', 'clear']],   
        ['fontname', ['fontname']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video', 'hr']],
        ['view', ['fullscreen', 'codeview', 'help']]
    ]
  });

  //INICIA O TEXTAREA SUMMERNOTE
  $('.summernote9').summernote({
    height: 200,
    lang: "pt-BR",
    fontNames: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    fontNamesIgnoreCheck: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    callbacks: {
        onImageUpload: function(files, editor, $editable) {
            sendFile9(files[0],editor,$editable);
        }
    }, 
    toolbar: [
        ['style', ['style']],
        ['style', ['bold', 'italic', 'underline', 'clear']],   
        ['fontname', ['fontname']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video', 'hr']],
        ['view', ['fullscreen', 'codeview', 'help']]
    ]
  });
  
  //INICIA O TEXTAREA SUMMERNOTE
  $('.summernote10').summernote({
    height: 200,
    lang: "pt-BR",
    fontNames: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    fontNamesIgnoreCheck: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    callbacks: {
        onImageUpload: function(files, editor, $editable) {
            sendFile10(files[0],editor,$editable);
        }
    }, 
    toolbar: [
        ['style', ['style']],
        ['style', ['bold', 'italic', 'underline', 'clear']],   
        ['fontname', ['fontname']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video', 'hr']],
        ['view', ['fullscreen', 'codeview', 'help']]
    ]
  });

   //INICIA O TEXTAREA SUMMERNOTE
   $('.summernote11').summernote({
    height: 200,
    lang: "pt-BR",
    fontNames: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    fontNamesIgnoreCheck: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    callbacks: {
        onImageUpload: function(files, editor, $editable) {
            sendFile11(files[0],editor,$editable);
        }
    }, 
    toolbar: [
        ['style', ['style']],
        ['style', ['bold', 'italic', 'underline', 'clear']],   
        ['fontname', ['fontname']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video', 'hr']],
        ['view', ['fullscreen', 'codeview', 'help']]
    ]
  });

  //INICIA O TEXTAREA SUMMERNOTE
  $('.summernote12').summernote({
   height: 200,
   lang: "pt-BR",
   fontNames: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
   fontNamesIgnoreCheck: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
   callbacks: {
       onImageUpload: function(files, editor, $editable) {
           sendFile12(files[0],editor,$editable);
       }
   }, 
   toolbar: [
       ['style', ['style']],
       ['style', ['bold', 'italic', 'underline', 'clear']],   
       ['fontname', ['fontname']],
       ['fontsize', ['fontsize']],
       ['color', ['color']],
       ['para', ['ul', 'ol', 'paragraph']],
       ['height', ['height']],
       ['table', ['table']],
       ['insert', ['link', 'picture', 'video', 'hr']],
       ['view', ['fullscreen', 'codeview', 'help']]
   ]
 });

  //SETA A FONTE MONTSERRAT COMO DEFAULT DO SUMMERNOTE
  $('#summernote').summernote('fontName', 'Montserrat');
  $('#summernote2').summernote('fontName', 'Montserrat');
  $('#summernote3').summernote('fontName', 'Montserrat');
  $('#summernote4').summernote('fontName', 'Montserrat');
  $('#summernote5').summernote('fontName', 'Montserrat');
  $('#summernote6').summernote('fontName', 'Montserrat');
  $('#summernote7').summernote('fontName', 'Montserrat');
  $('#summernote8').summernote('fontName', 'Montserrat');
  $('#summernote9').summernote('fontName', 'Montserrat');
  $('#summernote10').summernote('fontName', 'Montserrat');
  $('#summernote11').summernote('fontName', 'Montserrat');
  $('#summernote12').summernote('fontName', 'Montserrat');

  //GUARDA NO SERVIDOR AS IMAGEM DO SUMMERNOTE
  function sendFile1(file,editor,welEditable) {
    data = new FormData();
    data.append("file", file);
    $.ajax({
      url: "modulos/configuracoes/php/upload-imagens.php",
      data: data,
      cache: false,
      contentType: false,
      processData: false,
      type: 'POST',
      success: function(url){
        var image = $('<img>').attr('src', url);
        $('.summernote').summernote('insertNode', image[0]);
      }, error: function(data) {
        mensagemAviso("Erro inesperado! Se o problema persistir, contate o administrador do sistema.","problema");
      }
    });
  }

  //GUARDA NO SERVIDOR AS IMAGEM DO SUMMERNOTE
  function sendFile2(file,editor,welEditable) {
    data = new FormData();
    data.append("file", file);
    $.ajax({
      url: "modulos/configuracoes/php/upload-imagens.php",
      data: data,
      cache: false,
      contentType: false,
      processData: false,
      type: 'POST',
      success: function(url){
        var image = $('<img>').attr('src', url);
        $('.summernote2').summernote('insertNode', image[0]);
      }, error: function(data) {
        mensagemAviso("Erro inesperado! Se o problema persistir, contate o administrador do sistema.","problema");
      }
    });    
  }

  //GUARDA NO SERVIDOR AS IMAGEM DO SUMMERNOTE
  function sendFile3(file,editor,welEditable) {
    data = new FormData();
    data.append("file", file);
    $.ajax({
      url: "modulos/configuracoes/php/upload-imagens.php",
      data: data,
      cache: false,
      contentType: false,
      processData: false,
      type: 'POST',
      success: function(url){
        var image = $('<img>').attr('src', url);
        $('.summernote3').summernote('insertNode', image[0]);
      }, error: function(data) {
        mensagemAviso("Erro inesperado! Se o problema persistir, contate o administrador do sistema.","problema");
      }
    });    
  }

  //GUARDA NO SERVIDOR AS IMAGEM DO SUMMERNOTE
  function sendFile4(file,editor,welEditable) {
    data = new FormData();
    data.append("file", file);
    $.ajax({
      url: "modulos/configuracoes/php/upload-imagens.php",
      data: data,
      cache: false,
      contentType: false,
      processData: false,
      type: 'POST',
      success: function(url){
        var image = $('<img>').attr('src', url);
        $('.summernote4').summernote('insertNode', image[0]);
      }, error: function(data) {
        mensagemAviso("Erro inesperado! Se o problema persistir, contate o administrador do sistema.","problema");
      }
    });    
  }

  //GUARDA NO SERVIDOR AS IMAGEM DO SUMMERNOTE
  function sendFile5(file,editor,welEditable) {
    data = new FormData();
    data.append("file", file);
    $.ajax({
      url: "modulos/configuracoes/php/upload-imagens.php",
      data: data,
      cache: false,
      contentType: false,
      processData: false,
      type: 'POST',
      success: function(url){
        var image = $('<img>').attr('src', url);
        $('.summernote5').summernote('insertNode', image[0]);
      }, error: function(data) {
        mensagemAviso("Erro inesperado! Se o problema persistir, contate o administrador do sistema.","problema");
      }
    });    
  }

  //GUARDA NO SERVIDOR AS IMAGEM DO SUMMERNOTE
  function sendFile6(file,editor,welEditable) {
    data = new FormData();
    data.append("file", file);
    $.ajax({
      url: "modulos/configuracoes/php/upload-imagens.php",
      data: data,
      cache: false,
      contentType: false,
      processData: false,
      type: 'POST',
      success: function(url){
        var image = $('<img>').attr('src', url);
        $('.summernote6').summernote('insertNode', image[0]);
      }, error: function(data) {
        mensagemAviso("Erro inesperado! Se o problema persistir, contate o administrador do sistema.","problema");
      }
    });    
  }

  //GUARDA NO SERVIDOR AS IMAGEM DO SUMMERNOTE
  function sendFile7(file,editor,welEditable) {
    data = new FormData();
    data.append("file", file);
    $.ajax({
      url: "modulos/configuracoes/php/upload-imagens.php",
      data: data,
      cache: false,
      contentType: false,
      processData: false,
      type: 'POST',
      success: function(url){
        var image = $('<img>').attr('src', url);
        $('.summernote7').summernote('insertNode', image[0]);
      }, error: function(data) {
        mensagemAviso("Erro inesperado! Se o problema persistir, contate o administrador do sistema.","problema");
      }
    });    
  }

  //GUARDA NO SERVIDOR AS IMAGEM DO SUMMERNOTE
  function sendFile8(file,editor,welEditable) {
    data = new FormData();
    data.append("file", file);
    $.ajax({
      url: "modulos/configuracoes/php/upload-imagens.php",
      data: data,
      cache: false,
      contentType: false,
      processData: false,
      type: 'POST',
      success: function(url){
        var image = $('<img>').attr('src', url);
        $('.summernote8').summernote('insertNode', image[0]);
      }, error: function(data) {
        mensagemAviso("Erro inesperado! Se o problema persistir, contate o administrador do sistema.","problema");
      }
    });    
  }

  //GUARDA NO SERVIDOR AS IMAGEM DO SUMMERNOTE
  function sendFile9(file,editor,welEditable) {
    data = new FormData();
    data.append("file", file);
    $.ajax({
      url: "modulos/configuracoes/php/upload-imagens.php",
      data: data,
      cache: false,
      contentType: false,
      processData: false,
      type: 'POST',
      success: function(url){
        var image = $('<img>').attr('src', url);
        $('.summernote9').summernote('insertNode', image[0]);
      }, error: function(data) {
        mensagemAviso("Erro inesperado! Se o problema persistir, contate o administrador do sistema.","problema");
      }
    });    
  }

  //GUARDA NO SERVIDOR AS IMAGEM DO SUMMERNOTE
  function sendFile10(file,editor,welEditable) {
    data = new FormData();
    data.append("file", file);
    $.ajax({
      url: "modulos/configuracoes/php/upload-imagens.php",
      data: data,
      cache: false,
      contentType: false,
      processData: false,
      type: 'POST',
      success: function(url){
        var image = $('<img>').attr('src', url);
        $('.summernote10').summernote('insertNode', image[0]);
      }, error: function(data) {
        mensagemAviso("Erro inesperado! Se o problema persistir, contate o administrador do sistema.","problema");
      }
    });    
  }
  
  //GUARDA NO SERVIDOR AS IMAGEM DO SUMMERNOTE
  function sendFile11(file,editor,welEditable) {
    data = new FormData();
    data.append("file", file);
    $.ajax({
      url: "modulos/configuracoes/php/upload-imagens-paginas-customizadas.php",
      data: data,
      cache: false,
      contentType: false,
      processData: false,
      type: 'POST',
      success: function(url){
        var image = $('<img>').attr('src', url);
        $('.summernote11').summernote('insertNode', image[0]);
      }, error: function(data) {
        mensagemAviso("Erro inesperado! Se o problema persistir, contate o administrador do sistema.","problema");
      }
    });    
  }
  
  //GUARDA NO SERVIDOR AS IMAGEM DO SUMMERNOTE
  function sendFile12(file,editor,welEditable) {
    data = new FormData();
    data.append("file", file);
    $.ajax({
      url: "modulos/configuracoes/php/upload-imagens.php",
      data: data,
      cache: false,
      contentType: false,
      processData: false,
      type: 'POST',
      success: function(url){
        var image = $('<img>').attr('src', url);
        $('.summernote12').summernote('insertNode', image[0]);
      }, error: function(data) {
        mensagemAviso("Erro inesperado! Se o problema persistir, contate o administrador do sistema.","problema");
      }
    });    
  }

  $('#form-pagina-customizada').on('submit', function() {
    var codigoFonte = $('#summernote11').summernote('code');
    $('#codigo-fonte').val(codigoFonte);
  });

}

$("#modo-whatsapp").change(function(){
  var modo_whatsapp = $(this).prop('checked');
  if(modo_whatsapp){
    $("#row-modo-simples").removeClass("modo-whats-desativado");
  } else {
    $("#row-modo-simples").addClass("modo-whats-desativado");
    $("#row-modo-whatsapp-com-preco").addClass("modo-whats-simples-desativado");
    $("#modo-whatsapp-simples").prop("checked",false);
    $("#modo-whatsapp-preco").prop("checked",false);
  }
});

$("#modo-whatsapp-simples").change(function(){
  var modo_whatsapp_simples = $(this).prop('checked');
  if(modo_whatsapp_simples){
    $("#row-modo-whatsapp-com-preco").removeClass("modo-whats-simples-desativado");
  } else {
    $("#row-modo-whatsapp-com-preco").addClass("modo-whats-simples-desativado");
    $("#modo-whatsapp-preco").prop("checked",false);
  }
});

$("#frete-gratis").change(function(){
  if($(this).prop('checked')){
    $("#frete-gratis-parametros").removeClass("d-none");
  } else {
    $("#frete-gratis-parametros").addClass("d-none");
  }
});

$("#frete-retirar").change(function(){
  if($(this).prop('checked')){
    $("#frete-retirar-parametros").removeClass("d-none");
  } else {
    $("#frete-retirar-parametros").addClass("d-none");
  }
});

$("#frete-motoboy").change(function(){
  if($(this).prop('checked')){
    $("#frete-motoboy-parametros").removeClass("d-none");
    $("#frete-motoboy-cidades").attr('required', 'required');
  } else {
    $("#frete-motoboy-cidades").removeAttr('required');
    $("#frete-motoboy-parametros").addClass("d-none");
  }
});

$("#tw").change(function(){
  if($(this).prop('checked')){
    $("#frete-tw-parametros").removeClass("d-none");
    $("#frete-tw-parametros input").each(function(){
      $(this).attr('required', 'required');
    });
  } else {
    $("#frete-tw-parametros input").each(function(){
      $(this).removeAttr('required');
    });
    $("#frete-tw-parametros").addClass("d-none");
  }
});

$(function(){
  var requiredCheckboxes = $('#frete-servicos :checkbox');
  requiredCheckboxes.change(function(){
      if(requiredCheckboxes.is(':checked')) {
          requiredCheckboxes.removeAttr('required');
      } else {
          requiredCheckboxes.attr('required', 'required');
      }
  });
});

$("#frete-motoboy-prazo").change(function(){
  if($(this).val() == '' | parseInt($(this).val()) < 0){
    $(this).val(0);
  } 
});

$("#design-banner-principal").change(function(){
  
  var status = $(this).prop("checked");
  if(status){ status = 1; } 
  else { status = 0; }
  
  $.ajax({
    type: "POST",
    url: "modulos/configuracoes/php/troca-status-modo-banners-iguais.php",
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

$("#barra-categorias-mobile").change(function(){
  
  var status = $(this).prop("checked");
  if(status){ status = 1; } 
  else { status = 0; }
  
  $.ajax({
    type: "POST",
    url: "modulos/configuracoes/php/troca-status-barra-categorias-mobile.php",
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

$("#barra-categorias-desktop").change(function(){
  
  var status = $(this).prop("checked");
  if(status){ status = 1; } 
  else { status = 0; }
  
  $.ajax({
    type: "POST",
    url: "modulos/configuracoes/php/troca-status-barra-categorias-desktop.php",
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

$("#design-sessao-categorias").change(function(){
  
  var status = $(this).prop("checked");
  if(status){ status = 1; } 
  else { status = 0; }
  
  $.ajax({
    type: "POST",
    url: "modulos/configuracoes/php/troca-status-design-sessao-categorias.php",
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

$("#menu-links-pesquisar").change(function(){
  
  var status = $(this).prop("checked");
  if(status){ status = 1; } 
  else { status = 0; }
  
  $.ajax({
    type: "POST",
    url: "modulos/configuracoes/php/troca-status-menu-links-pesquisar.php",
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

$("#whatsapp-flutuante").change(function(){
  
  var status = $(this).prop("checked");
  if(status){ status = 1; } 
  else { status = 0; }
  
  $.ajax({
    type: "POST",
    url: "modulos/configuracoes/php/troca-status-whatsapp-flutuante.php",
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

function resetarMelhorEnvio(){

  var resposta = confirm("Confirma exclusão das configurações do Melhor Envio?");
  
  if (resposta === true) {
  
    $.ajax({
      type: "POST",
      url: "modulos/configuracoes/php/configuracao-frete-melhor-envio-reset.php",
      data: {"status": status},
      success: function(data) {
        fechaLoader();
        if(data === "SESSAO INVALIDA") {
          window.location.href = 'logout.php'
        } else if(data === "ERRO BANCO") {
          mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
        } else if(data === "OK") { 
          location.reload();
        } else {
          mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
        }
      },
      beforeSend: function() {
        abreLoader();
      }
    });

  }

}

$("#pix").change(function(){
  var pix = $(this).prop('checked');
  if(pix){
    $("#chave-pix").attr("required",true);
    $("#dados-pix").removeClass("dados-pix-desativado");
  } else {
    $("#chave-pix").attr("required",false);
    $("#dados-pix").addClass("dados-pix-desativado");
  }
});

$(".faixa-desconto-de").maskMoney({prefix: "R$ ",decimal: ",",thousands: "."});
$(".faixa-desconto-ate").maskMoney({prefix: "R$ ",decimal: ",",thousands: "."});
$(".faixa-desconto-porcentagem").maskMoney({decimal: ",",thousands: "."});

//FUNÇÕES PARA AS CARACTERÍSTICAS
function addFaixaDescontoPagamento(){
  cont_faixas_desconto++;
  $("#n-faixas-desconto").val(parseInt($("#n-faixas-desconto").val())+1);
  $("#faixas-desconto").append(
    '<div class="row mt-3 mt-lg-0" id="faixa-desconto-'+cont_faixas_desconto+'">'+
      '<div class="col-12 col-lg-3">'+
        '<div class="row">'+
          '<div class="col-2 col-sm-1 col-lg-2">'+
            '<a href="javascript: removeFaixaDescontoPagamento('+cont_faixas_desconto+',false)"><img class="faixas-desconto-botao" src="'+$("#nome_site").val()+'imagens/remover.png"></a>'+
          '</div>'+
          '<div class="col-10 col-sm-11 col-lg-10">'+
            '<div class="form-group">'+
              '<select name="faixa-desconto-tipo-pagamento-'+cont_faixas_desconto+'" id="faixa-desconto-tipo-pagamento-'+cont_faixas_desconto+'" class="form-control" required>'+
                '<option value="" selected disabled>Pagamento...</option>'+
                '<option value="1">PIX</option>'+
                '<option value="2">Boleto</option>'+
                '<option value="3">Cartão</option>'+
              '</select>'+
            '</div>'+
          '</div>'+
        '</div>'+
      '</div>'+
      '<div class="col-6 col-lg-3">'+
        '<div class="form-group">'+
          '<input type="text" name="faixa-desconto-de-'+cont_faixas_desconto+'" id="faixa-desconto-de-'+cont_faixas_desconto+'" class="form-control faixa-desconto-de" placeholder="De" required>'+
        '</div>'+
      '</div>'+
      '<div class="col-6 col-lg-3">'+
        '<div class="form-group">'+
          '<input type="text" name="faixa-desconto-ate-'+cont_faixas_desconto+'" id="faixa-desconto-ate-'+cont_faixas_desconto+'" class="form-control faixa-desconto-ate" placeholder="Até">'+
        '</div>'+
      '</div>'+
      '<div class="col-12 col-lg-3">'+
        '<div class="row">'+
          '<div class="col-5 pr-0">'+
            '<div class="form-group">'+
              '<input type="text" name="faixa-desconto-porcentagem-'+cont_faixas_desconto+'" id="faixa-desconto-porcentagem-'+cont_faixas_desconto+'" class="form-control faixa-desconto-porcentagem" placeholder="%">'+
            '</div>'+
          '</div>'+
          '<div class="col-2 p-0 d-flex align-items-center justify-content-center">ou</div>'+
          '<div class="col-5 pl-0">'+    
            '<div class="form-group">'+
              '<input type="text" name="faixa-desconto-valor-'+cont_faixas_desconto+'" id="faixa-desconto-valor-'+cont_faixas_desconto+'" class="form-control faixa-desconto-porcentagem" placeholder="Valor">'+
            '</div>'+
          '</div>'+
        '</div>'+
      '</div>'+
    '</div>'
  )
  $(".faixa-desconto-de").maskMoney({prefix: "R$ ",decimal: ",",thousands: "."});
  $(".faixa-desconto-ate").maskMoney({prefix: "R$ ",decimal: ",",thousands: "."});
  $(".faixa-desconto-porcentagem").maskMoney({decimal: ",",thousands: "."});
}

function removeFaixaDescontoPagamento(n, antigo, identificador){

  var confirma = confirm("Confirma a exclusão desta faixa de preços?");

  if(confirma){

    $("#n-faixas-desconto").val(parseInt($("#n-faixas-desconto").val())-1);
    $("#faixa-desconto-"+n).remove();

    if(antigo){
      $.ajax({
        url: "modulos/configuracoes/php/exclusao-faixa-desconto.php",
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

$("#configuracoes-pagamento input[name='ativar-pagamento']").click(function(){
  var data_name = $(this).val();
  $(".tab-pane .alert-success").removeClass('d-block').addClass('d-none');
  $(".tab-pane .alert-danger").removeClass('d-none').addClass('d-block');
  $("#conteudo-tab-"+data_name+" .alert-success").removeClass('d-none').addClass('d-block');
  $("#conteudo-tab-"+data_name+" .alert-danger").removeClass('d-block').addClass('d-none');
});

if($("#form-pagina-customizada").length > 0){

  function buscaCategorias(){
    $.ajax({
      url: "modulos/produtos/php/busca-categorias.php",
      type: "POST",
      dataType: "json",
      success: function (categorias){
        $('#arvore-categorias').treeview({
          data: categorias,
          levels: 10,
          selectedBackColor: '#696969',
          state: {
            checked: true,
          },
          onNodeSelected: function(event, data) {
            $("#categoria").val(data.id);
          },
          onNodeUnselected: function(event, data){
            $("#categoria").val('');
          }
        }); 
        var categoria = $("#categoria").val(); 
        if(categoria != ''){
          $(".node-arvore-categorias").each(function(e){
            var node = $("#arvore-categorias").treeview('getNode', e);
            if(node.id == categoria){
              $(".node-arvore-categorias").each(function(e){
                if($(this).attr('data-nodeid') == node.nodeId){
                  $(this).addClass('node-selected');
                }
              });    
            }
          });
        }  
      }
    }); 
  }

  $(document).ready(function(){
    buscaCategorias(); 
  });

}
