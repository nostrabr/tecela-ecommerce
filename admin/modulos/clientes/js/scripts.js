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
      "order": [[ 3, 'desc' ]],
      "lengthMenu": [[50, 100, 500, -1], [50, 100, 500, "Tudo"]],
      "pagingType": "numbers",
      "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true, "type": 'date-br'},
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
      "order": [[ 4, 'desc' ]],
      "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "Tudo"]],
      "pagingType": "numbers",
      "columns": [
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true},
        {"orderable": true, "type": 'date-br'},
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
  window.location.href = 'clientes-edita.php?id='+identificador;
}

function visualiza(identificador){
  window.location.href = 'clientes-email-detalhes.php?id='+identificador;
}

if($(".summernote").length != 0){

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
    height: 350,
    lang: "pt-BR",
    fontNames: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
    fontNamesIgnoreCheck: ['Montserrat', 'Serif', 'Sans', 'Arial', 'Arial Black', 'Courier', 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Sacramento'],
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

}

function trocaLabelNomes(cpf_cnpj){    
  cpf_cnpj = cpf_cnpj.replace(/[^\d]+/g,"");
  if(cpf_cnpj.length === 14) {
      $("#label-cliente-nome").html('Razão Social');
      $("#label-cliente-sobrenome").html('Fantasia');
  } else {
      $("#label-cliente-nome").html('Nome');
      $("#label-cliente-sobrenome").html('Sobrenome');
  }
}