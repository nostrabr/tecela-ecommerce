/*******************************/
/* EVENTOS DO RD STATION AFTER */
/*******************************/


/***********/
/* FUNÇÕES */
/***********/

function rdStationFechamentoPedido(pedido){
  $.ajax({
    type: "POST",
    data: {'pedido': pedido},
    url: $("#site").val()+"php/rd-station-fechamento-pedido.php"
  });    
}

function rdStationCarrinhoAbandonado(){    
  $.ajax({
    type: "POST",
    url: $("#site").val()+"php/rd-station-abandono-carrinho.php"
  });  
}

/***********/
/* EVENTOS */
/***********/

$(document).ready(function(){  

    //PEDIDO REALIZADO 
    if($("#carrinho-confirmacao-pedido-confirmado").length > 0){
      rdStationFechamentoPedido($("#pedido-confirmacao-identificador").val());
    }

    if(window.location.href == $("#site").val()){
      rdStationCarrinhoAbandonado()
    }

});