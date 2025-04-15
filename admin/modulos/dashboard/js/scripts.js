google.charts.load('current', {'packages':['table']});
google.charts.load("current", {'packages':["corechart"]});
google.charts.load('current', {'packages':['geochart']});
google.charts.setOnLoadCallback(buscaStatusPedidos);
google.charts.setOnLoadCallback(buscaFormasPagamento);
google.charts.setOnLoadCallback(buscaFormasEntrega);
google.charts.setOnLoadCallback(buscaProdutosMaisVendidos);
google.charts.setOnLoadCallback(buscaProdutosMaisVisitados);
google.charts.setOnLoadCallback(buscaProdutosMaisWhatsapp);
google.charts.setOnLoadCallback(buscaProdutosMaisVisitadosWhatsapp);
google.charts.setOnLoadCallback(buscaPalavrasMaisPesquisadas);
google.charts.setOnLoadCallback(buscaGeolocalizacaoMapaEstados);
google.charts.setOnLoadCallback(buscaGeolocalizacaoCidades);
google.charts.setOnLoadCallback(buscaGeolocalizacaoEstados);
google.charts.setOnLoadCallback(buscaCliquesWhatsapp);
google.charts.setOnLoadCallback(buscaDispositivos);
google.charts.setOnLoadCallback(buscaResolucoes);

function buscaFormasPagamento(data_inicio, data_fim){

  document.getElementById('data-site-vendas-formas-pagamento').innerHTML = "Carregando...";
  
  ajax1 = $.ajax({
      url: "modulos/dashboard/php/busca-formas-pagamento.php",
      type: "POST",
      dataType: "json",
      data: {"data-inicio": data_inicio, "data-fim": data_fim},
      success: function (formas_pagamento){ 
        
          var height_pie = '200';
          if(screen.width >= screen.height){ height_pie = '300'; }

          var jsonData = formas_pagamento;
          var data = new google.visualization.DataTable();
          data.addColumn('string', 'Forma de Pagamento');
          data.addColumn('number', 'Total');
          $.each(jsonData, function(i, jsonData){
              var tipo  = jsonData.tipo;
              var total = parseInt($.trim(jsonData.total));
              data.addRows([[tipo, total]]);
          });
          var options = {
              width: '100%',
              height: height_pie,
              backgroundColor: '#f1f1f1',
              is3D: true
          };
          var chart = new google.visualization.PieChart(document.getElementById('data-site-vendas-formas-pagamento'));
          chart.draw(data, options);

      }

  });  

}

function buscaFormasEntrega(data_inicio, data_fim){

  document.getElementById('data-site-vendas-formas-entrega').innerHTML = "Carregando...";
  
  ajax2 = $.ajax({
      url: "modulos/dashboard/php/busca-formas-entrega.php",
      type: "POST",
      dataType: "json",
      data: {"data-inicio": data_inicio, "data-fim": data_fim},
      success: function (formas_entrega){   

          var height_pie = '200';
          if(screen.width >= screen.height){ height_pie = '300'; }

          var jsonData = formas_entrega;
          var data = new google.visualization.DataTable();
          data.addColumn('string', 'Forma de Entrega');
          data.addColumn('number', 'Total');
          $.each(jsonData, function(i, jsonData){
              var tipo  = jsonData.tipo;
              var total = parseInt($.trim(jsonData.total));
              data.addRows([[tipo, total]]);
          });
          var options = {
              width: '100%',
              height: height_pie,
              backgroundColor: '#f1f1f1',
              is3D: true
          };
          var chart = new google.visualization.PieChart(document.getElementById('data-site-vendas-formas-entrega'));
          chart.draw(data, options);
         
      }

  });  

}

function buscaDispositivos(data_inicio, data_fim){

  document.getElementById('data-site-dispositivos').innerHTML = "Carregando...";
  
  ajax3 = $.ajax({
      url: "modulos/dashboard/php/busca-dispositivos.php",
      type: "POST",
      dataType: "json",
      data: {"data-inicio": data_inicio, "data-fim": data_fim},
      success: function (dispositivos){   

          var height_pie = '200';
          if(screen.width >= screen.height){ height_pie = '300'; }

          var jsonData = dispositivos;
          var data = new google.visualization.DataTable();
          data.addColumn('string', 'Dispositivo');
          data.addColumn('number', 'Total');
          $.each(jsonData, function(i, jsonData){
              var dispositivo = jsonData.dispositivo;
              var total       = parseInt($.trim(jsonData.total));
              data.addRows([[dispositivo, total]]);
          });
          var options = {
              width: '100%',
              height: height_pie,
              backgroundColor: '#f1f1f1',
              is3D: true
          };
          var chart = new google.visualization.PieChart(document.getElementById('data-site-dispositivos'));
          chart.draw(data, options);
      }

  });  

}

function buscaResolucoes(data_inicio, data_fim){

  document.getElementById('data-site-resolucoes').innerHTML = "Carregando...";
  
  ajax4 = $.ajax({
      url: "modulos/dashboard/php/busca-resolucoes.php",
      type: "POST",
      dataType: "json",
      data: {"data-inicio": data_inicio, "data-fim": data_fim},
      success: function (resolucoes){  
        var height_pie = '200';
        if(screen.width >= screen.height){ height_pie = '300'; }
        var jsonData = resolucoes;
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Resolução');
        data.addColumn('number', 'Total');
        $.each(jsonData, function(i, jsonData){
            var resolucao = jsonData.resolucao;
            var total     = parseInt($.trim(jsonData.total));
            data.addRows([[resolucao, total]]);
        });
        var options = {
            width: '100%',
            height: height_pie,
            backgroundColor: '#f1f1f1',
            is3D: true
        };
        var chart = new google.visualization.PieChart(document.getElementById('data-site-resolucoes'));
        chart.draw(data, options);
      }
  });  

}

function buscaStatusPedidos(data_inicio, data_fim){
  
  document.getElementById('data-site-vendas-pedidos').innerHTML = "Carregando...";

  ajax5 = $.ajax({
      url: "modulos/dashboard/php/busca-vendas-pedidos-status.php",
      type: "POST",
      dataType: "json",
      data: {"data-inicio": data_inicio, "data-fim": data_fim},
      success: function (status){  
          var jsonData = status;
          var data = new google.visualization.DataTable();
          data.addColumn('string', 'Status');
          data.addColumn('number', 'Total');
          data.addColumn({ type: 'string', role: 'style' });
          $.each(jsonData, function(i, jsonData){
              var nome_status = jsonData.nome;
              var total_status = parseInt($.trim(jsonData.total));
              var cor_status = jsonData.cor;
              data.addRows([[nome_status, total_status, cor_status]]);
          });
          var options = {
              width: '100%',
              height: '200',
              backgroundColor: '#f1f1f1',
              legend :'none',
              fontName: 'montserrat',
              titleTextStyle: {
                color: '#696969',
                fontSize: 15,
                bold: true
              },
              is3D: true
          };
          var chart = new google.visualization.BarChart(document.getElementById('data-site-vendas-pedidos'));
          chart.draw(data, options);
      }

  });  

}

function buscaProdutosMaisVendidos(data_inicio, data_fim){
    document.getElementById('data-site-mais-vendidos').innerHTML = "Carregando...";
    ajax6 = $.ajax({
        url: "modulos/dashboard/php/busca-produtos-mais-vendidos.php",
        type: "POST",
        dataType: "json",
        data: {"data-inicio": data_inicio, "data-fim": data_fim},
        success: function (produtos){
          var data = new google.visualization.DataTable();
          data.addColumn('string', 'Produtos mais vendidos');
          data.addColumn('number', 'Total');
          for(var i = 0; i < produtos.length; i++){
            data.addRows([[produtos[i].nome,  parseInt(produtos[i].total)]]); 
          }     
          var table = new google.visualization.Table(document.getElementById('data-site-mais-vendidos'));      
          table.draw(data, {showRowNumber: true, width: '100%', height: '100%', fontName: 'montserrat'});
        }
    });  
}

function buscaProdutosMaisVisitados(data_inicio, data_fim){
  document.getElementById('data-site-mais-visitados').innerHTML = "Carregando...";
  ajax7 = $.ajax({
      url: "modulos/dashboard/php/busca-produtos-mais-visitados.php",
      type: "POST",
      dataType: "json",
      data: {"data-inicio": data_inicio, "data-fim": data_fim},
      success: function (produtos){
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Produtos mais visitados');
        data.addColumn('number', 'Total');
        for(var i = 0; i < produtos.length; i++){
          data.addRows([[produtos[i].nome,  parseInt(produtos[i].total)]]); 
        }     
        var table = new google.visualization.Table(document.getElementById('data-site-mais-visitados'));      
        table.draw(data, {showRowNumber: true, width: '100%', height: '100%', fontName: 'montserrat'});
      }
  });  
}

function buscaProdutosMaisVisitadosWhatsapp(data_inicio, data_fim){
  document.getElementById('data-site-mais-visitados-whatsapp').innerHTML = "Carregando...";
  ajax8 = $.ajax({
      url: "modulos/dashboard/php/busca-produtos-mais-visitados-whatsapp.php",
      type: "POST",
      dataType: "json",
      data: {"data-inicio": data_inicio, "data-fim": data_fim},
      success: function (produtos){
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Produtos mais visitados');
        data.addColumn('number', 'Total');
        data.addColumn('number', 'Whats');
        for(var i = 0; i < produtos.length; i++){
          data.addRows([[produtos[i].nome, parseInt(produtos[i].total), parseInt(produtos[i].total_whatsapp)]]); 
        }     
        var table = new google.visualization.Table(document.getElementById('data-site-mais-visitados-whatsapp'));      
        table.draw(data, {showRowNumber: true, width: '100%', height: '100%', fontName: 'montserrat'});
      }
  });  
}

function buscaProdutosMaisWhatsapp(data_inicio, data_fim){
  document.getElementById('data-site-mais-whatsapp').innerHTML = "Carregando...";
  ajax9 = $.ajax({
      url: "modulos/dashboard/php/busca-produtos-mais-whatsapp.php",
      type: "POST",
      dataType: "json",
      data: {"data-inicio": data_inicio, "data-fim": data_fim},
      success: function (produtos){
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Cliques no WhatsApp');
        data.addColumn('number', 'Total');
        for(var i = 0; i < produtos.length; i++){
          data.addRows([[produtos[i].nome,  parseInt(produtos[i].total)]]); 
        }     
        var table = new google.visualization.Table(document.getElementById('data-site-mais-whatsapp'));      
        table.draw(data, {showRowNumber: true, width: '100%', height: '100%', fontName: 'montserrat'});
      }
  });  
}

function buscaPalavrasMaisPesquisadas(data_inicio, data_fim){
  document.getElementById('data-site-mais-pesquisados').innerHTML = "Carregando...";
  ajax10 = $.ajax({
      url: "modulos/dashboard/php/busca-palavras-mais-pesquisadas.php",
      type: "POST",
      dataType: "json",
      data: {"data-inicio": data_inicio, "data-fim": data_fim},
      success: function (palavras){
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Termos mais pesquisados');
        data.addColumn('number', 'Total');
        for(var i = 0; i < palavras.length; i++){
          data.addRows([[palavras[i].nome,  parseInt(palavras[i].total)]]); 
        }     
        var table = new google.visualization.Table(document.getElementById('data-site-mais-pesquisados'));      
        table.draw(data, {showRowNumber: true, width: '100%', height: '100%', fontName: 'montserrat'});
      }
  });  
}

function buscaCliquesWhatsapp(data_inicio, data_fim){
  ajax11 = $.ajax({
      url: "modulos/dashboard/php/busca-cliques-whatsapp.php",
      type: "POST",
      dataType: "json",
      data: {"data-inicio": data_inicio, "data-fim": data_fim},
      success: function (retorno){
        $("#data-site-cliques-whatsapp-flutuante").html(retorno[0].flutuante);
        $("#data-site-cliques-whatsapp-rodape").html(retorno[0].rodape);
        $("#data-site-cliques-whatsapp-contato").html(retorno[0].contato);
        $("#data-site-cliques-whatsapp-total").html(retorno[0].total);
      }
  });  
}

function buscaVendasTotalVisitas(data_inicio, data_fim){
  ajax12 = $.ajax({
      url: "modulos/dashboard/php/busca-vendas-total-visitas.php",
      type: "POST",
      data: {"data-inicio": data_inicio, "data-fim": data_fim},
      success: function (retorno){
        $("#data-site-vendas-totais-total-visitas").html(retorno);
      }
  });  
}

function buscaVendasTotalPedidos(data_inicio, data_fim){
  ajax13 = $.ajax({
      url: "modulos/dashboard/php/busca-vendas-total-pedidos.php",
      type: "POST",
      data: {"data-inicio": data_inicio, "data-fim": data_fim},
      success: function (retorno){
          $("#data-site-vendas-totais-total-pedidos").html(retorno);
      }
  });  
}

function buscaVendasTotalVendido(data_inicio, data_fim){
  ajax14 = $.ajax({
      url: "modulos/dashboard/php/busca-vendas-total-vendido.php",
      type: "POST",
      data: {"data-inicio": data_inicio, "data-fim": data_fim},
      success: function (retorno){
        $("#data-site-vendas-totais-total-vendido").html(retorno);
      }
  });  
}

function buscaVendasTotalConversao(data_inicio, data_fim){
  ajax15 = $.ajax({
      url: "modulos/dashboard/php/busca-vendas-total-conversao.php",
      type: "POST",
      data: {"data-inicio": data_inicio, "data-fim": data_fim},
      success: function (retorno){
        $("#data-site-vendas-totais-total-conversao").html(retorno);
      }
  });  
}

function buscaGeolocalizacaoMapaEstados(data_inicio, data_fim){
  
  ajax16 = $.ajax({
    url: "modulos/dashboard/php/busca-geolocalizacao-estados.php",
    type: "POST",
    dataType: "json",
    data: {"data-inicio": data_inicio, "data-fim": data_fim},
    success: function (estados){
      var jsonData = estados;
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'UF');
      data.addColumn('string', 'Estado');
      data.addColumn('number', 'Acessos');
      $.each(jsonData, function(i, jsonData){
          var uf     = 'BR-'+jsonData.uf;
          var estado = jsonData.estado;
          var total  = parseInt($.trim(jsonData.total));
          data.addRows([[uf, estado, total]]);
      });
      var options = {
        region: 'BR',
        resolution: 'provinces',
        forceIFrame: false,
        backgroundColor: '#F1F1F1',
        enableRegionInteractivity: true
      };
      var chart = new google.visualization.GeoChart(document.getElementById('data-site-geolocalizacao-clientes-estado-mapa'));
      chart.draw(data, options);
  
      document.getElementById('data-site-geolocalizacao-clientes-estado-mapa-label-carregando').innerHTML = "";

    }

  });  
    
}

function buscaGeolocalizacaoCidades(data_inicio, data_fim){

  document.getElementById('data-site-geolocalizacao-clientes-cidade').innerHTML = "Carregando...";

  ajax17 = $.ajax({
      url: "modulos/dashboard/php/busca-geolocalizacao-cidades.php",
      type: "POST",
      dataType: "json",
      data: {"data-inicio": data_inicio, "data-fim": data_fim},
      success: function (cidades){
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Cidade');
        data.addColumn('string', 'UF');
        data.addColumn('number', 'Acessos');
        for(var i = 0; i < cidades.length; i++){
          data.addRows([[cidades[i].cidade, cidades[i].uf,  parseInt(cidades[i].total)]]); 
        }     
        var table = new google.visualization.Table(document.getElementById('data-site-geolocalizacao-clientes-cidade'));      
        table.draw(data, {showRowNumber: true, width: '100%', height: '100%', fontName: 'montserrat'});
      }
  });  
}

function buscaGeolocalizacaoEstados(data_inicio, data_fim){
  document.getElementById('data-site-geolocalizacao-clientes-estado').innerHTML = "Carregando...";
  ajax18 = $.ajax({
      url: "modulos/dashboard/php/busca-geolocalizacao-estados.php",
      type: "POST",
      dataType: "json",
      data: {"data-inicio": data_inicio, "data-fim": data_fim},
      success: function (estados){        
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Estado');
        data.addColumn('number', 'Acessos');
        for(var i = 0; i < estados.length; i++){
          data.addRows([[estados[i].estado,  parseInt(estados[i].total)]]); 
        }     
        var table = new google.visualization.Table(document.getElementById('data-site-geolocalizacao-clientes-estado'));      
        table.draw(data, {showRowNumber: true, width: '100%', height: '100%', fontName: 'montserrat'});
      }
  });  
}

function cancelaRequisicoes(){
  if(ajax1){ajax1.abort();}
  if(ajax2){ajax2.abort();}
  if(ajax3){ajax3.abort();}
  if(ajax4){ajax4.abort();}
  if(ajax5){ajax5.abort();}
  if(ajax6){ajax6.abort();}
  if(ajax7){ajax7.abort();}
  if(ajax8){ajax8.abort();}
  if(ajax9){ajax9.abort();}
  if(ajax10){ajax10.abort();}
  if(ajax11){ajax11.abort();}
  if(ajax12){ajax12.abort();}
  if(ajax13){ajax13.abort();}
  if(ajax14){ajax14.abort();}
  if(ajax15){ajax15.abort();}
  if(ajax16){ajax16.abort();}
  if(ajax17){ajax17.abort();}
  if(ajax18){ajax18.abort();}
}

$(window).on('beforeunload', function() {
  cancelaRequisicoes()
});

$(function(){
  buscaCliquesWhatsapp($("#data-site-data-inicio").val(), $("#data-site-data-fim").val());
  buscaVendasTotalVisitas($("#data-site-data-inicio").val(), $("#data-site-data-fim").val());
  buscaVendasTotalPedidos($("#data-site-data-inicio").val(), $("#data-site-data-fim").val());
  buscaVendasTotalVendido($("#data-site-data-inicio").val(), $("#data-site-data-fim").val());
  buscaVendasTotalConversao($("#data-site-data-inicio").val(), $("#data-site-data-fim").val());
  buscaStatusPedidos($("#data-site-data-inicio").val(), $("#data-site-data-fim").val());
  buscaProdutosMaisVendidos($("#data-site-data-inicio").val(), $("#data-site-data-fim").val());
  buscaProdutosMaisVisitados($("#data-site-data-inicio").val(), $("#data-site-data-fim").val());
  buscaProdutosMaisWhatsapp($("#data-site-data-inicio").val(), $("#data-site-data-fim").val());
  buscaProdutosMaisVisitadosWhatsapp($("#data-site-data-inicio").val(), $("#data-site-data-fim").val());
  buscaPalavrasMaisPesquisadas($("#data-site-data-inicio").val(), $("#data-site-data-fim").val());
  buscaGeolocalizacaoCidades($("#data-site-data-inicio").val(), $("#data-site-data-fim").val());
  buscaGeolocalizacaoEstados($("#data-site-data-inicio").val(), $("#data-site-data-fim").val());
  buscaDispositivos($("#data-site-data-inicio").val(), $("#data-site-data-fim").val());
  buscaResolucoes($("#data-site-data-inicio").val(), $("#data-site-data-fim").val());
  buscaFormasPagamento($("#data-site-data-inicio").val(), $("#data-site-data-fim").val());
  buscaFormasEntrega($("#data-site-data-inicio").val(), $("#data-site-data-fim").val());
  buscaGeolocalizacaoMapaEstados($("#data-site-data-inicio").val(), $("#data-site-data-fim").val());
});

$("#data-site-data-inicio").blur(function(){
  abreLoader()
  window.location.href = 'dashboard.php?data-inicio='+$(this).val()+"&data-fim="+$("#data-site-data-fim").val()
});

$("#data-site-data-fim").blur(function(){
  abreLoader()
  window.location.href = 'dashboard.php?data-inicio='+$("#data-site-data-inicio").val()+"&data-fim="+$(this).val()
});