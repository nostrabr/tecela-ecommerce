function guardaPaginacaoPesquisa(){
  $("#admin-lista_filter input").keyup(function(){ localStorage.setItem("pesquisa", $(this).val()); });
  $(".paginate_button").click(function(){ localStorage.setItem("paginacao", $(".paginate_button.active a").html()); });
}

function setaPaginacaoPesquisa(){  
  var parametro_url = document.location.search;
  if(parametro_url != ''){
    parametro_url = parametro_url.split('=');
    if(parametro_url[1] == 'e'){
      var pesquisa = localStorage.getItem("pesquisa");
      var paginacao = localStorage.getItem("paginacao");
      if(pesquisa != '' & pesquisa != null){
        $("#admin-lista_filter input").val(pesquisa);
        setTimeout(() => {
          admin_lista.search(pesquisa);
          admin_lista.draw();
        }, 1);
      } else {
        if(paginacao != null){
          $(".paginate_button a").each(function(){
            if($(this).html() == paginacao){
              $(".paginate_button.active").removeClass('active');
              $(this).parent('.paginate_button').addClass('active');
            }
          });
        }
      }
      localStorage.setItem("pesquisa", "");
      localStorage.setItem("paginacao", "");
      var newURL = location.href.split("?")[0];
      window.history.pushState('object', document.title, newURL);
    }
  }
}

$(document).ready(function () {

  if($("#admin-lista").length != 0){
    admin_lista = $('#admin-lista').DataTable({
      "order": [[ 1, 'asc' ]],
      "lengthMenu": [[50, 100, 500, -1], [50, 100, 500, "Tudo"]],
      "pagingType": "numbers",
      "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
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

    guardaPaginacaoPesquisa();
    setaPaginacaoPesquisa();
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
      "order": [[ 1, 'desc' ]],
      "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "Tudo"]],
      "pagingType": "numbers",
      "columns": [
          {"orderable": true},
          {"orderable": true, "type": 'date-br'},
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
  
  cont_caracteristicas = $("#n_caracteristicas").val();

});

$(".admin-lista-nome-produto").click(function(e){
  e.stopPropagation();
});

$(".botao-status").click(function(e){
  e.stopPropagation();
});

$(".btn-promocao").click(function(e){
  e.stopPropagation();
});

$(".botao-excluir").click(function(e){
  e.stopPropagation();
});

function edita(identificador){
  window.location.href = 'produtos-edita.php?id='+identificador;
}

function trocaStatus(identificador, status){ 
  $.ajax({
    url: "modulos/produtos/php/troca-status.php",
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

function exclui(identificador, nome){
    
  var confirma = confirm("Confirma a exclusão do produto "+nome+"?");

  if(confirma){
    $.ajax({
      url: "modulos/produtos/php/exclui.php",
      type: "POST",
      data: {"identificador": identificador},
      success: function (data){
        if(data === "SESSAO INVALIDA") {
          window.location.href = 'logout.php'
        } else if(data === "ERRO BANCO") {
          mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
        } else if(data === "OK") {  
          $(".botao-excluir").click(function(e){
            e.stopPropagation();
          });
        } else if(data != '' & data != 'SESSAO INVALIDA') {
          mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
        }
      },
      beforeSend: function() {
        $("#produto-"+identificador).remove();
      }
    }); 
  }

}

function geraPalavrasChave(){

  if($("#produtos-cadastra").length > 0){

    var palavras_chave = '';
    var palavras_nome  = $("#nome").val().trim().replace(/[^a-z0-9\s]/gi, '').split(' ');
    
    for(var i = 0; i < palavras_nome.length; i++){
      if(palavras_nome[i].length > 2){
        palavras_chave += palavras_nome[i].toLowerCase()+', ';
      }
    }

    if($("#marca").val() != null){
      palavras_chave += $("#marca option:selected" ).text().toLowerCase()+', ';
    }

    var categoria = $('#arvore-categorias').treeview('getSelected');
    if(categoria[0] != undefined){
      palavras_chave += categoria[0].text.toLowerCase()+', ';
    }

    palavras_chave = palavras_chave.trim().slice(0, -1);

    $("#palavras_chave").val(palavras_chave);

  }

}

$("#nome").change(function(){
  geraPalavrasChave();
  geraSku();
});

$("#marca").change(function(){
  geraPalavrasChave();
  geraSku();
});

$("#atributo-primario").change(function(){
  var id_atributo_primario = $(this).val();
  buscaCaracteristicas('primario',id_atributo_primario);
});

$("#atributo-secundario").change(function(){
  var id_atributo_secundario = $(this).val();
  buscaCaracteristicas('secundario',id_atributo_secundario);
});

$("#caracteristicas-primarias").change(function(){
  geraVariacoes();
});

$("#caracteristicas-secundarias").change(function(){
  geraVariacoes();
});

function buscaCaracteristicas(tipo_atributo,id_atributo){

  if(id_atributo != ''){
    $.ajax({
      url: "modulos/produtos/php/busca-caracteristicas.php",
      type: "POST",
      dataType : "json",
      data: {"atributo": id_atributo},
      success: function (data){
        if(data[0].status === "SESSAO INVALIDA") {
          window.location.href = 'logout.php'
        } else if(data[0].status == 'OK') {

          var i = 0;
          var options = '';

          while (data[i]) {
            options += '<option class="text-capitalize" value="'+data[i].id+'" id-atributo="'+id_atributo+'">'+data[i].nome+'</option>';
            i++;
          }
          
          if(tipo_atributo == 'primario'){
            $("#caracteristicas-primarias").html(options).selectpicker('refresh');
            $("#caracteristicas-secundarias").empty().selectpicker('refresh');
            buscaAtributosSecundarios(id_atributo);
          } else if(tipo_atributo == 'secundario'){
            $("#caracteristicas-secundarias").html(options).selectpicker('refresh');
          }

        } else {
          mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
        }
      }
    });
  } else {
    if(tipo_atributo == 'primario'){
      $("#caracteristicas-primarias").empty().selectpicker('refresh');
      $("#caracteristicas-secundarias").empty().selectpicker('refresh');
      $("#atributo-secundario").empty().append('<option value="" disabled selected>Selecione...</option>');
      geraVariacoes();
    } else if(tipo_atributo == 'secundario'){
      $("#caracteristicas-secundarias").empty().selectpicker('refresh');
      geraVariacoes();
    }
  }

}

function buscaAtributosSecundarios(atributo_utilizado){

  $.ajax({
    url: "modulos/produtos/php/busca-atributos.php",
    type: "POST",
    dataType : "json",
    success: function (data){
      if(data[0].status === "SESSAO INVALIDA") {
        window.location.href = 'logout.php'
      } else if(data[0].status == 'OK') {

        var i = 0;
        var options = '<option value="" disabled selected>Selecione...</option><option value="">Remover</option>';

        while (data[i]) {
          if(data[i].id != atributo_utilizado)
            options += '<option class="text-capitalize" value="'+data[i].id+'">'+data[i].nome+'</option>';
          i++;
        }
        
        $("#atributo-secundario").html(options);

      } else {
        mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
      }
    }
  });

}

function buscaVariacoesCadastradas(){

  var variacoes = [];

  if($("#ids-variacoes").val() != ''){

    var combinacoes_cadastradas   = $("#ids-variacoes").val().split(',');
    var n_combinacoes_cadastradas = combinacoes_cadastradas.length;
  
    for(var i = 0; i < n_combinacoes_cadastradas; i++){

      var ids       = combinacoes_cadastradas[i];
      var nomes     = $("[id-variacao = "+combinacoes_cadastradas[i]+"]").attr('nome-variacao');
      var atributos = $("[id-variacao = "+combinacoes_cadastradas[i]+"]").attr('ids-atributos');
      var estoque   = $("[id-variacao = "+combinacoes_cadastradas[i]+"]").find('[name = variacao-'+combinacoes_cadastradas[i]+']').val();
      var status    = $("[id-variacao = "+combinacoes_cadastradas[i]+"]").find('[name = variacao-status-input-'+combinacoes_cadastradas[i]+']').val();
      
      var caracteristica       = new Object();
      caracteristica.ids       = ids;
      caracteristica.nomes     = nomes;
      caracteristica.atributos = atributos;
      caracteristica.estoque   = estoque;
      caracteristica.status    = status;

      variacoes.push(caracteristica);

    }

  }

  return variacoes;

}

function limpaVariacoes(){
  
  $("#variacoes .variacoes-variante").each(function(){
      $(this).remove();
  });

}

function geraVariacoes(){

  var variacoes_cadastradas   = buscaVariacoesCadastradas();
  var n_variacoes_cadastradas = variacoes_cadastradas.length;
  limpaVariacoes();

  var caracteristicas = [];

  $("#caracteristicas-primarias").find("option:selected").each(function(){    

    var caracteristica_id       = $(this).val();        
    var caracteristica_nome     = $(this).text();   
    var caracteristica_atributo = $(this).attr('id-atributo');

    var caracteristica          = new Object();
    caracteristica.id           = caracteristica_id;
    caracteristica.nome         = caracteristica_nome;
    caracteristica.atributo     = caracteristica_atributo;

    caracteristicas.push(caracteristica);

  });

  $("#caracteristicas-secundarias").find("option:selected").each(function(){    

    var caracteristica_id       = $(this).val();        
    var caracteristica_nome     = $(this).text();
    var caracteristica_atributo = $(this).attr('id-atributo');

    var caracteristica          = new Object();
    caracteristica.id           = caracteristica_id;
    caracteristica.nome         = caracteristica_nome;
    caracteristica.atributo     = caracteristica_atributo;

    caracteristicas.push(caracteristica);

  });
  
  var i, j, combinacoes = [], combinacoes_mesmo_atributo = [], total_caracteristicas = caracteristicas.length;

  for(i = 0; i < total_caracteristicas; ++i) {
    for(j = i + 1; j < total_caracteristicas; ++j) {
      if(caracteristicas[i]['atributo'] != caracteristicas[j]['atributo'] ){
        combinacoes.push([ caracteristicas[i], caracteristicas[j] ]);
      } else {
        combinacoes_mesmo_atributo.push([ caracteristicas[i] ]);
        combinacoes_mesmo_atributo.push([ caracteristicas[j] ]);
      }
    }
  }

  var total_combinacoes = combinacoes.length;
  var ids_variacoes     = [];

  if(total_combinacoes > 0){
    for(var x = 0; x < total_combinacoes; x++){

      var estoque = 0;
      var status  = 1;
      var status_imagem = $("#nome_site").val()+"imagens/status-ativo.png";

      for(var y = 0; y < n_variacoes_cadastradas; y++){
        if(variacoes_cadastradas[y]['ids'] == combinacoes[x][0]["id"]+"-"+combinacoes[x][1]["id"]){
          estoque = variacoes_cadastradas[y]['estoque'];
          status  = variacoes_cadastradas[y]['status'];
          if(status == 0){
            status_imagem = $("#nome_site").val()+"imagens/status-inativo.png";
          }
        }
      }

      $("#variacoes").append("<div id-variacao='"+combinacoes[x][0]["id"]+"-"+combinacoes[x][1]["id"]+"' ids-atributos='"+combinacoes[x][0]["atributo"]+"-"+combinacoes[x][1]["atributo"]+"' nome-variacao='"+combinacoes[x][0]["nome"]+"-"+combinacoes[x][1]["nome"]+"' class='variacoes-variante'><ul><li>"+combinacoes[x][0]["nome"]+" - "+combinacoes[x][1]["nome"]+"</li><li>Estoque: <input name='variacao-"+combinacoes[x][0]["id"]+"-"+combinacoes[x][1]["id"]+"' class='variacoes-variante-estoque only-number' type='number' value='"+estoque+"' min='0'></li><input class='variacoes-variante-ordem' type='hidden' name='variacao-ordem-"+combinacoes[x][0]["id"]+"-"+combinacoes[x][1]["id"]+"'></ul><div class='variacao-status'><img onclick='javascript: trocaStatusVariacao(\""+combinacoes[x][0]['id']+'-'+combinacoes[x][1]['id']+"\");' id='variacao-status-img-"+combinacoes[x][0]["id"]+"-"+combinacoes[x][1]["id"]+"' src='"+status_imagem+"'><input id='variacao-status-input-"+combinacoes[x][0]["id"]+"-"+combinacoes[x][1]["id"]+"' name='variacao-status-input-"+combinacoes[x][0]["id"]+"-"+combinacoes[x][1]["id"]+"' type='hidden' value='"+status+"'></div></div>");
      ids_variacoes.push(combinacoes[x][0]["id"]+"-"+combinacoes[x][1]["id"]);
    }
  } else {

    if(total_caracteristicas == 1){
      
      var estoque = 0;
      var status  = 1;
      var status_imagem = $("#nome_site").val()+"imagens/status-ativo.png";

      for(var y = 0; y < n_variacoes_cadastradas; y++){
        if(variacoes_cadastradas[y]['ids'] == caracteristicas[0]["id"]){
          estoque = variacoes_cadastradas[y]['estoque'];
          status  = variacoes_cadastradas[y]['status'];
          if(status == 0){
            status_imagem = $("#nome_site").val()+"imagens/status-inativo.png";
          }
        }
      }

      $("#variacoes").append("<div id-variacao='"+caracteristicas[0]["id"]+"'' ids-atributos='"+caracteristicas[0]["atributo"]+"' nome-variacao='"+caracteristicas[0]["nome"]+"' class='variacoes-variante'><ul><li>"+caracteristicas[0]["nome"]+"</li><li>Estoque: <input name='variacao-"+caracteristicas[0]["id"]+"' class='variacoes-variante-estoque only-number' type='number' value='"+estoque+"' min='0'></li><input class='variacoes-variante-ordem' type='hidden' name='variacao-ordem-"+caracteristicas[0]["id"]+"'></ul><div class='variacao-status'><img onclick='javascript: trocaStatusVariacao("+caracteristicas[0]["id"]+");' id='variacao-status-img-"+caracteristicas[0]["id"]+"' src='"+status_imagem+"'><input id='variacao-status-input-"+caracteristicas[0]["id"]+"' name='variacao-status-input-"+caracteristicas[0]["id"]+"' type='hidden' value='"+status+"'></div></div>");
      ids_variacoes.push(caracteristicas[0]["id"]);    
      
    } else if(total_caracteristicas > 1){

      combinacoes_mesmo_atributo = combinacoes_mesmo_atributo.filter(function (a) {
        return !this[JSON.stringify(a)] && (this[JSON.stringify(a)] = true);
      }, Object.create(null))

      for(var x = 0; x < combinacoes_mesmo_atributo.length; x++){

        var estoque = 0;
        var status  = 1;
        var status_imagem = $("#nome_site").val()+"imagens/status-ativo.png";

        for(var y = 0; y < n_variacoes_cadastradas; y++){
          if(variacoes_cadastradas[y]['ids'] == combinacoes_mesmo_atributo[x][0]["id"]){
            estoque = variacoes_cadastradas[y]['estoque'];
            status  = variacoes_cadastradas[y]['status'];
            if(status == 0){
              status_imagem = $("#nome_site").val()+"imagens/status-inativo.png";
            }
          }
        }

        $("#variacoes").append("<div id-variacao='"+combinacoes_mesmo_atributo[x][0]["id"]+"' ids-atributos='"+combinacoes_mesmo_atributo[x][0]["atributo"]+"' nome-variacao='"+combinacoes_mesmo_atributo[x][0]["nome"]+"' class='variacoes-variante'><ul><li>"+combinacoes_mesmo_atributo[x][0]["nome"]+"</li><li>Estoque: <input name='variacao-"+combinacoes_mesmo_atributo[x][0]["id"]+"' class='variacoes-variante-estoque only-number' type='number' value='"+estoque+"' min='0'></li><input class='variacoes-variante-ordem' type='hidden' name='variacao-ordem-"+combinacoes_mesmo_atributo[x][0]["id"]+"'></ul><div class='variacao-status'><img onclick='javascript: trocaStatusVariacao("+combinacoes_mesmo_atributo[x][0]["id"]+");' id='variacao-status-img-"+combinacoes_mesmo_atributo[x][0]["id"]+"' src='"+status_imagem+"'><input id='variacao-status-input-"+combinacoes_mesmo_atributo[x][0]["id"]+"' name='variacao-status-input-"+combinacoes_mesmo_atributo[x][0]["id"]+"' type='hidden' value='"+status+"'></div></div>");
        ids_variacoes.push(combinacoes_mesmo_atributo[x][0]["id"]);
      }

    }

  }

  $("#ids-variacoes").val(ids_variacoes);
  reordenaVariacoes();
  $('#variacoes input').bind('click.sortable mousedown.sortable',function(ev){
    ev.target.focus();
  });

}

function reordenaVariacoes(){
  var ordem = 0;
  $(".variacoes-variante-ordem").each(function(){
    ordem++;
    $(this).val(ordem);
  }); 
}

function trocaStatusVariacao(id){ 
  var status_atual = $("#variacao-status-input-"+id).val();
  if(status_atual == 1){
    $("#variacao-status-input-"+id).val(0);
    $("#variacao-status-img-"+id).attr('src',$("#nome_site").val()+'imagens/status-inativo.png');
    $("#variacao-status-input-"+id).closest('.variacoes-variante').addClass('variacoes-variante-desativada');
  } else {
    $("#variacao-status-input-"+id).val(1);
    $("#variacao-status-img-"+id).attr('src',$("#nome_site").val()+'imagens/status-ativo.png');
    $("#variacao-status-input-"+id).closest('.variacoes-variante').removeClass('variacoes-variante-desativada');
  }
}

if($("#produtos-edita").length > 0){
  imagens_dropzone = []
}

//DROPZONE
if(typeof Dropzone !== 'undefined'){

  var contador_imagens = 0;
  var elementos_criados = 0;

  Dropzone.options.imagens = {
    url: "modulos/produtos/php/cadastro-imagens.php",
    maxFilesize: 10,
    dictFileTooBig: "Arquivo muito grande. Máximo: 10 MB.",
    addRemoveLinks: true,
    resizeWidth: 1000,
    acceptedFiles: ".png, .jpg, .gif, .jpeg",
    dictDefaultMessage: "Para adicionar imagens, clique ou arraste neste quadro",
    dictInvalidFileType: "Tipo de arquivo inválido!",
    dictRemoveFile: "",
    init: function(e) {     
      if($("#produtos-edita").length > 0){
        thisDropzone = this;
        $.get('modulos/produtos/php/busca-imagens.php?id='+$("#identificador").val(), function(data) {   
          $.each(data, function(key,value){   
            var mockFile = { id:value.id, name: value.name, size: value.size, capa: value.capa, ordem: value.ordem };              
            thisDropzone.options.addedfile.call(thisDropzone, mockFile);
            thisDropzone.options.thumbnail.call(thisDropzone, mockFile, $("#nome_site").val()+"imagens/produtos/pequena/"+value.name);  
            var imagem_dropzone = new Object();
            imagem_dropzone.id = mockFile.id;
            imagem_dropzone.nome = mockFile.name;
            imagem_dropzone.capa = mockFile.capa;
            imagem_dropzone.ordem = mockFile.ordem;
            imagens_dropzone.push(imagem_dropzone);
            contador_imagens = mockFile.ordem;
            elementos_criados = mockFile.ordem;
          });               
        });
      }
      this.on("error", function(file, message) {
        $("#erros-dropzone").show().append("<div class='col-12'><span>"+file.name+" - "+message+"</span></div>");
        this.removeFile(file);
      });
      this.on("sending", function(file, xhr, formData) {
          formData.append("name", "value");
      });   
      this.on("addedfile", function(file) {

        contador_imagens++;
        elementos_criados++;
        
        var opcoes = Dropzone.createElement(
          '<div class="dropzone-opcoes dropzone-opcoes-'+elementos_criados+'">'+
            '<input type="hidden" class="dropzone-opcoes-input" index="'+elementos_criados+'" nome="'+file.name+'" id="imagem-'+elementos_criados+'" name="imagem[]" value="" ordem="'+contador_imagens+'" capa="0">'+
            '<span class="dropzone-opcoes-ordem">'+contador_imagens+'</span>'+
            '<span id="dropzone-opcoes-capa-'+elementos_criados+'" class="dropzone-opcoes-capa" onclick="javascript: removeCapas('+elementos_criados+');">CAPA</span>'+
          '</div>'
        );
        opcoes.addEventListener("click", function(e) {
          e.preventDefault();
          e.stopPropagation();
        });
        file.previewElement.appendChild(opcoes);
        
        var removeButton = Dropzone.createElement('<img class="dropzone-opcoes-excluir" src="'+$("#nome_site").val()+'imagens/acao-excluir.png">');
        var _this = this;
        removeButton.addEventListener("click", function(e) {
          e.preventDefault();
          e.stopPropagation();
          _this.removeFile(file);
        });
        $(".dropzone-opcoes-"+elementos_criados).append(removeButton);

        if($(".dropzone-opcoes-capa-ativa").length == 0){
          $(".dropzone-opcoes-capa").first().addClass("dropzone-opcoes-capa-ativa");
          $("#imagem-"+elementos_criados).val('['+contador_imagens+']-[1]-['+file.name+']').attr("capa","1");
        } else {
          $("#imagem-"+elementos_criados).val('['+contador_imagens+']-[0]-['+file.name+']').attr("capa","0");
        }
        
        $('.dropzone-opcoes-capa').on('touchstart', function(e) {
          removeCapas($(this).attr('id-capa'));
        });

      });   
      this.on("removedfile", function(e){
        if(contador_imagens != 0)
          throw new Error('This is not an error. This is just to abort javascript');
      });
      this.on('complete', function(e){
        console.log(e);
      });
    },
    removedfile: function(file) {   
      
      contador_imagens--; 

      var name = file.name;   
      
      $.ajax({
          type: 'POST',
          url: 'modulos/produtos/php/exclusao-imagens.php',
          data: {name: name}
      });      

      var _ref;

      (_ref = file.previewElement) !== null ? _ref.parentNode.removeChild(file.previewElement) : void 0;    

      var imagens_sobraram = $(".dropzone-opcoes-ordem").length;
                
      if(imagens_sobraram > 0){      
        var nova_ordem = 0;    
        $(".dropzone-opcoes-ordem").each(function(e){
          nova_ordem++;
          $(this).html(nova_ordem);
        });            
        if($(".dropzone-opcoes-capa-ativa").length == 0){
          $(".dropzone-opcoes-capa").first().addClass("dropzone-opcoes-capa-ativa");
          $(".dropzone-opcoes-input").first().attr("capa","1");
        }        
        nova_ordem = 0;  
        $(".dropzone-opcoes-input").each(function(e){
          nova_ordem++;          
          if($(this).is('[id_imagem]')){
            $(this).attr('ordem',nova_ordem).val('['+nova_ordem+']-['+$(this).attr('capa')+']-['+$(this).attr('nome')+']-['+$(this).attr('id_imagem')+']');
          } else {
            $(this).attr('ordem',nova_ordem).val('['+nova_ordem+']-['+$(this).attr('capa')+']-['+$(this).attr('nome')+']');
          }
        });      
      }

    }  

  };

  $(function(){
    $(".dropzone").sortable({
        items:'.dz-preview',
        cursor: 'move',
        opacity: 0.5,
        containment: '.dropzone',
        distance: 20,
        tolerance: 'pointer',
        update: function( event, ui ) {          
          var nova_ordem = 0;        
          $(".dropzone-opcoes-ordem").each(function(e){
            nova_ordem++;
            $(this).html(nova_ordem);
          }); 
          nova_ordem = 0;  
          $(".dropzone-opcoes-input").each(function(e){
            nova_ordem++;
            if($(this).is('[id_imagem]')){
              $(this).attr('ordem',nova_ordem).val('['+nova_ordem+']-['+$(this).attr('capa')+']-['+$(this).attr('nome')+']-['+$(this).attr('id_imagem')+']');
            } else {
              $(this).attr('ordem',nova_ordem).val('['+nova_ordem+']-['+$(this).attr('capa')+']-['+$(this).attr('nome')+']');
            }
          });    
        },
        create: function(event, ui){           

          if($("#produtos-edita").length > 0){

            $(".btn-salvar-produto").prop("disabled",true);

            var contador_imagens = 0;   
            
            setTimeout(function(r){

              $(".dz-preview").each(function(e){   
                
                $(this).addClass("dz-complete").append(
                  '<div class="dropzone-opcoes dropzone-opcoes-'+imagens_dropzone[contador_imagens].ordem+'">'+
                    '<input type="hidden" class="dropzone-opcoes-input" index="'+imagens_dropzone[contador_imagens].ordem+'" nome="'+imagens_dropzone[contador_imagens].nome+'" id="imagem-'+imagens_dropzone[contador_imagens].ordem+'" name="imagem[]" value="['+imagens_dropzone[contador_imagens].ordem+']-['+imagens_dropzone[contador_imagens].capa+']-['+imagens_dropzone[contador_imagens].nome+']-['+imagens_dropzone[contador_imagens].id+']" id_imagem="'+imagens_dropzone[contador_imagens].id+'" ordem="'+imagens_dropzone[contador_imagens].ordem+'" capa="'+imagens_dropzone[contador_imagens].capa+'">'+
                    '<span class="dropzone-opcoes-ordem">'+imagens_dropzone[contador_imagens].ordem+'</span>'+
                    '<span id="dropzone-opcoes-capa-'+imagens_dropzone[contador_imagens].ordem+'" class="dropzone-opcoes-capa" onclick="javascript: removeCapas('+imagens_dropzone[contador_imagens].ordem+');">CAPA</span>'+
                  '</div>'
                );

                var btn_remover = $(this).find('.dz-remove').html('<img class="dropzone-opcoes-excluir" src="'+$("#nome_site").val()+'imagens/acao-excluir.png" />');
                $(this).find(".dropzone-opcoes").append(btn_remover);

                if(imagens_dropzone[contador_imagens].capa == 1){
                  $("#dropzone-opcoes-capa-"+imagens_dropzone[contador_imagens].ordem).addClass("dropzone-opcoes-capa-ativa");
                }
                   
                $('.dropzone-opcoes-capa').on('touchstart', function(e) {
                  removeCapas($(this).attr('id-capa'));
                });
                
                contador_imagens++;
                
              });
              
              $(".btn-salvar-produto").prop("disabled",false);

            },3000);

          }
          
          fechaLoader();
          
        }
    });
  });

  function removeCapas(n){
    $(".dropzone-opcoes-capa").removeClass('dropzone-opcoes-capa-ativa');
    $("#dropzone-opcoes-capa-"+n).addClass('dropzone-opcoes-capa-ativa');  
    $(".dropzone-opcoes-input").each(function(){      
      if($(this).is('[id_imagem]')){
        $(this).attr("capa","0").val('['+$(this).attr('ordem')+']-[0]-['+$(this).attr('nome')+']-['+$(this).attr('id_imagem')+']');
      } else {
        $(this).attr("capa","0").val('['+$(this).attr('ordem')+']-[0]-['+$(this).attr('nome')+']');
      }
    })   
    if($("#imagem-"+n).is('[id_imagem]')){
      $("#imagem-"+n).attr('capa','1').val('['+$("#imagem-"+n).attr('ordem')+']-[1]-['+$("#imagem-"+n).attr('nome')+']-['+$("#imagem-"+n).attr('id_imagem')+']');
    } else {
      $("#imagem-"+n).attr('capa','1').val('['+$("#imagem-"+n).attr('ordem')+']-[1]-['+$("#imagem-"+n).attr('nome')+']');
    }
  }

}

$("[name='desconto-opcao']").change(function(){
  var opcao = $(this).val();
  if(opcao == 'P'){
    $("#porcentagem-desconto").attr("type","number").val('');
  } else if(opcao == 'V'){
    $("#porcentagem-desconto").attr("type","text").val('').maskMoney({decimal: ",",thousands: ""});
  }
});

//BOTA OU TIRA UM PRODUTO DE UMA PROMOÇÃO
function promocaoProduto(identificador, nome, promocao){

  if(promocao == 0){

    $(".btn-desativa-promocao").addClass("d-none");
    $(".btn-edita-promocao").addClass("d-none");
    $(".btn-cadastra-promocao").removeClass("d-none");
    $("#modal-add-promocao #nome-produto").html(nome);
    $("#modal-add-promocao #porcentagem-desconto").val('');
    $("#modal-add-promocao #validade").val('');
    $("#modal-add-promocao #identificador-produto").val(identificador);
    $("#modal-add-promocao").modal("show");

  } else if(promocao == 1){

    $(".btn-desativa-promocao").removeClass("d-none");
    $(".btn-edita-promocao").removeClass("d-none");
    $(".btn-cadastra-promocao").addClass("d-none");

    $.ajax({
      url: "modulos/produtos/php/busca-promocao.php",
      type: "POST",
      dataType: "json",
      data: {"identificador": identificador},
      success: function (data){
        if(data[0].status === "SESSAO INVALIDA") {
          window.location.href = 'logout.php'
        } else if(data[0].status == 'OK') {
          $("#modal-add-promocao #nome-produto").html(nome);
          if(data[0].tipo == 'V'){
            $("#porcentagem-desconto").attr("type","text").val('').maskMoney({decimal: ",",thousands: ""});
            $("input[name='desconto-opcao'][value='V']").prop("checked", true);
          } else {
            $("#porcentagem-desconto").attr("type","number").val('');
            $("input[name='desconto-opcao'][value='P']").prop("checked", true);
          }
          $("#modal-add-promocao #porcentagem-desconto").val(data[0].valor);
          $("#modal-add-promocao #validade").val(data[0].validade);
          $("#modal-add-promocao #identificador-produto").val(identificador);
          $("#modal-add-promocao").modal("show");
        }
      }
    }); 
  
  } else if(promocao == 2){
       
    var confirma = confirm("Confirma o encerramento da promoção do produto "+nome+"?");
    if(confirma){
        alterarStatusPromocaoProduto(0, identificador);
    }

  }
      
}

function alterarStatusPromocaoProduto(status, identificador){
  
  if(status == 0){
      $.ajax({
          url: "modulos/produtos/php/alterar-status-promocao.php",
          type: "POST",
          data: {"identificador": identificador, "status": status},
          success: function (data){
              if(data === "SESSAO INVALIDA") {
                  window.location.href = 'logout.php'
              } else if(data != '' & data != 'SESSAO INVALIDA') {
                  mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
              } else {
                $("#promocao-"+identificador).removeClass("promocao-ativada").attr("title","Ativar promoção").attr("href","javascript: promocaoProduto('"+identificador+"','"+nome+"','0');").html("Ativar Promoção");
                $("#modal-add-promocao").modal("hide");
              }
          }
      }); 
  } else if(status == 1 | status == 2){

      var porcentagem = $("#modal-add-promocao #porcentagem-desconto").val();
      var validade    = $("#modal-add-promocao #validade").val();
      var nome        = $("#modal-add-promocao #nome-produto").html();
      var tipo_desc   = $("input[name='desconto-opcao']:checked").val();
      identificador   = $("#modal-add-promocao #identificador-produto").val();

      if(porcentagem != '' & validade != '' & validade.length == 10){
          $.ajax({
              url: "modulos/produtos/php/alterar-status-promocao.php",
              type: "POST",
              data: {"identificador": identificador, "porcentagem": porcentagem, "status": status, "validade": validade, "tipo-desconto": tipo_desc},
              success: function (data){
                  if(data === "SESSAO INVALIDA") {
                      window.location.href = 'logout.php'
                  } else if(data != '' & data != 'SESSAO INVALIDA') {
                      mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
                  } else {
                      $("#promocao-"+identificador).addClass("promocao-ativada").attr("title","Ver promoção").attr("href","javascript: promocaoProduto('"+identificador+"','"+nome+"','1');").html("Ver Promoção");
                      $("#modal-add-promocao").modal("hide");
                  }
              }
          }); 
      } else {
        alert('Preencha todos os campos corretamente.');
      }
  }

}

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
          $("#categoria").val(data.id).attr('nome',data.text);
          geraPalavrasChave();
          geraSku();
        },
        onNodeUnselected: function(event, data){
          $("#categoria").val('').attr('nome','');
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

function geraSortableVariacoes(){
  $("#variacoes").sortable({
    items:'.variacoes-variante',
    distance: 20,
    tolerance: 'pointer', 
    opacity: 0.5,
    cursor: 'move',
      update: function( event, ui ) {        
        reordenaVariacoes(); 
      }
  });
  $('#variacoes input').bind('click.sortable mousedown.sortable',function(ev){
    ev.target.focus();
  });
}

$(document).ready(function(){
  buscaCategorias(); 
  geraSortableVariacoes();
});

$("#form-cadastro-produto").submit(function(e){
  if($("#categoria").val() === '' | $("#categoria").val() == 0){
    alert("Selecione uma categoria para o produto");
    e.preventDefault();
    return false;
  }
  verificaSku('C', e);
});

$("#form-edicao-produto").submit(function(e){
  if($("#categoria").val() === '' | $("#categoria").val() == 0){
    alert("Selecione uma categoria para o produto");
    e.preventDefault();
    return false;
  }
  verificaSku('E', e);
});

function verificaSku(tela, e){
  var sku  = $("#sku").val();
  if(tela == 'E'){
    var identificador = $("#identificador").val();
  } else {
    var identificador = '';
  }
  if(sku !== ''){
    $.ajax({
      url: "modulos/produtos/php/verifica-sku.php",
      type: "POST",
      async: false,
      data: {"sku": sku, "tela": tela, "identificador": identificador},
      success: function (data){
        if(data === "SESSAO INVALIDA") {
          window.location.href = 'logout.php';
          return false;
        } else if(data === "NOT-OK") {
          alert("SKU não pode ser o mesmo de outro já cadastrado no sistema.");
          e.preventDefault();
          setTimeout(function(){
            fechaLoader();
          }, 0);
        } else if(data === "OK") {
          return true;
        } else {
          window.location.href = 'logout.php'
          return false;
        }
      }
    });
  }
}

function geraSku(){

  if($("#ultimo_id").length > 0){
    var sku = '';
    var nome = $("#nome").val();
    var categoria = $("#categoria").val();  
    var marca = $("#marca").val();  
      var ultimo_id = $("#ultimo_id").val();
    if(nome != null){ 
      var nomes = nome.replace(/[^A-Za-z0-9 _]/g,'').split(' ');
      for(var i = 0; i<nomes.length; i++){
        sku += nomes[i].substr(0,1).toUpperCase(); 
      }  
    }
    if(categoria != null){ sku += categoria; }
    if(marca != null){ sku += marca; }
    sku += ultimo_id;

    $("#sku").val(sku);
  }

}

$("#btn-salvar-produto-edicao").click(function(){
  $("#form-edicao-produto").attr("action","modulos/produtos/php/edicao.php");
});
$("#btn-salvar-produto-copia").click(function(e){

  var confirma = confirm("Confirma a criação do novo produto?");

  if(confirma){
  
    verificaSku('C', e);

    var imagens = [];

    $("#imagens .dz-filename span").each(function(){
      imagens.push($(this).html());
    });
    
    $.ajax({
      url: "modulos/produtos/php/copia-imagens.php",
      type: "POST",
      async: false,
      data: {"imagens": JSON.stringify(imagens)},
      success: function (data){
        if(data === "SESSAO INVALIDA") {
          window.location.href = 'logout.php'
        } else {
          $("#form-edicao-produto").attr("action","modulos/produtos/php/copia.php");
        }
      },
      beforeSend: function() {    
        abreLoader();
      }
    });

  }

});

function visualizaAvaliacao(nota, data, comentario, replica, data_replica){

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

  $("#visualizacao-comentario-data").html(data);
  $("#visualizacao-comentario-nota").html(nota_imagem);
  $("#visualizacao-comentario-comentario").html(comentario);
  if(replica == ''){
    $("#modal-visualizacao-comentario #visualizacao-comentario-replica-container").hide();
  } else {
    $("#modal-visualizacao-comentario #visualizacao-comentario-replica-container").show();
  }
  $("#modal-visualizacao-comentario #visualizacao-comentario-replica").html(replica+"<br><i class='data-replica'>Data: "+data_replica+"</i>");
  $("#modal-visualizacao-comentario").modal('show');

}

$("#descricao").emojioneArea({
  pickerPosition: 'bottom'
});