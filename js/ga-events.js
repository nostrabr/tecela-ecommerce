/*******************************/
/* EVENTOS DO GOOGLE ANALYTICS */
/*******************************/


/***********/
/* FUNÇÕES */
/***********/

function gaPurchase(){
  $.ajax({
    type: "POST",
    url: $("#site").val()+"php/g-analytics-check-purchase.php",
    success: function (data) {
      if(data != ''){
        gtag('event', 'purchase', $.parseJSON(data));
      }
    }
  });    
}
gaPurchase();

function gaRefund(){
  $.ajax({
    type: "POST",
    url: $("#site").val()+"php/g-analytics-check-refund.php",
    success: function (data) {
      if(data != ''){
        gtag('event', 'refund', $.parseJSON(data));
      }
    }
  });    
}
gaRefund();

function gaAddPaymentInfo(){
  var payment_info_coupon  = $("input[name=cupom]").val();  
  var payment_payment_type = $(".carrinho-pagamento-forma-pagamento-ativo").attr("tipo");  
  $.ajax({
    type: "POST",
    async: false,
    data: {'coupon':payment_info_coupon, 'payment_type': payment_payment_type},
    url: $("#site").val()+"php/g-analytics-add-payment-info.php",
    success: function (data) {
      gtag('event', 'add_payment_info', $.parseJSON(data));
    }
  });    
}

function gaAddShippingInfo(){
  var shipping_info_coupon  = $("input[name=cupom-desconto]").val();  
  var shipping_info_type    = $("input[name=frete]").val(); 
  var shipping_info_value   = $("input[name=valor-frete]").val(); 
  $.ajax({
    type: "POST",
    async: false,
    data: {'coupon':shipping_info_coupon, 'shipping_type':shipping_info_type, 'shipping_value':shipping_info_value},
    url: $("#site").val()+"php/g-analytics-add-shipping-info.php",
    success: function (data) {
      gtag('event', 'add_shipping_info', $.parseJSON(data));
    }
  }); 
}

function gaAddToCart(){
  var product_id       = $("#produto-id").val();
  var product_discount = '';
  if($("#produto-preco .produto-container-valor-original").length > 0){ 
    product_discount = $("#produto-preco .produto-container-valor-original").html().slice(3).replace(/\./g, "").replace(',','.');
  }
  var product_value    = $("#produto-preco .produto-container-valor-final").html().slice(3).replace(/\./g, "").replace(',','.');
  var product_qtty     = $("#produto-quantidade-input").val();
  var product_variant  = "";
  if($(".caracteristica-ativa").length > 0){
    $(".caracteristica-ativa").each(function(){
      product_variant += $(this).attr('title')+'/';
    });
    product_variant = product_variant.slice(0, -1);
  }
  $.ajax({
    type: "POST",
    async: false,
    data: {'product_id':product_id, 'product_discount':product_discount, 'product_value':product_value, 'product_qtty':product_qtty, 'product_variant':product_variant},
    url: $("#site").val()+"php/g-analytics-add-to-cart.php",
    success: function (data) {
      gtag("event", "add_to_cart", $.parseJSON(data));
    }
  }); 
}

function gaBeginCheckout(){
  $.ajax({
    type: "POST",
    async: false,
    url: $("#site").val()+"php/g-analytics-begin-checkout.php",
    success: function (data) {
      gtag("event", "begin_checkout", $.parseJSON(data)); 
    }
  });  
}

function gaRemoveFromCart(cart_product_id){
  $.ajax({
    type: "POST",
    async: false,
    data: {'cart_product_id':cart_product_id},
    url: $("#site").val()+"php/g-analytics-remove-from-cart.php",
    success: function (data) {
      gtag('event', 'remove_from_cart', $.parseJSON(data));
    }
  });
}

function gaSelectItem(product_id, product_list, product_list_id, product_value, product_discount){  
  if(product_discount != '' & product_discount != undefined){ 
    product_discount = product_discount.slice(3).replace(/\./g, "").replace(',','.');
  }
  product_value    = product_value.slice(3).replace(/\./g, "").replace(',','.');
  $.ajax({
    type: "POST",
    async: false,
    data: {'product_id':product_id, 'product_list':product_list, 'product_list_id':product_list_id, 'product_discount':product_discount, 'product_value':product_value,},
    url: $("#site").val()+"php/g-analytics-select-item.php",
    success: function (data) {
      gtag('event', 'select_item', $.parseJSON(data));
    }
  });
}

function gaViewCart(){  
  $.ajax({
    type: "POST",
    url: $("#site").val()+"php/g-analytics-view-cart.php",
    success: function (data) {
      gtag('event', 'view_cart', $.parseJSON(data));
    }
  });
}

function gaViewItem(){  
  var product_id = $("#produto-id").val();
  var product_discount = '';
  if($("#produto-preco .produto-container-valor-original").length > 0){ 
    product_discount = $("#produto-preco .produto-container-valor-original").html().slice(3).replace(/\./g, "").replace(',','.');
  }
  var product_value    = $("#produto-preco .produto-container-valor-final").html().slice(3).replace(/\./g, "").replace(',','.');
  var product_qtty     = $("#produto-quantidade-input").val();
  var product_variant  = "";
  if($(".caracteristica-ativa").length > 0){
    $(".caracteristica-ativa").each(function(){
      product_variant += $(this).attr('title')+'/';
    });
    product_variant = product_variant.slice(0, -1);
  }
  $.ajax({
    type: "POST",
    data: {'product_id':product_id, 'product_discount':product_discount, 'product_value':product_value, 'product_qtty':product_qtty, 'product_variant':product_variant},
    url: $("#site").val()+"php/g-analytics-view-item.php",
    success: function (data) {
      gtag('event', 'view_item', $.parseJSON(data));
    }
  });
}

/***********/
/* EVENTOS */
/***********/

$(document).ready(function(){  
 
  //CASO TENHA INSTALADO O ANALYTICS
  if (typeof gtag !== 'undefined') {
    
    //DEFINE A URL DO SITE
    var url  = window.location.href; 
    var url_fixa_geral    = url.split("/")[url.split("/").length -1];
    var url_fixa_pesquisa = url.split("/")[url.split("/").length -2];
    var url_fixa_produto  = url.split("/")[url.split("/").length -4];
    var titulo_pagina     = $("h1").html();

    if(url_fixa_produto == 'produto'){
      url_fixa_geral = 'produto';
    }
    
    //LOGIN NA PÁGINA DO CLIENTE
    if(url_fixa_geral == 'cliente-dados?gtag=login-cliente'){
      gtag("event", "login", {
        method: "Área do cliente"
      });
      window.history.replaceState({}, document.title, "/" + "cliente-dados");
    } 
    
    //LOGIN NA PÁGINA DO CARRINHO
    if(url_fixa_geral == 'carrinho-frete?gtag=login-carrinho'){
      gtag("event", "login", {
        method: "Carrinho"
      });
      window.history.replaceState({}, document.title, "/" + "cliente-frete");
    }

    //PESQUISA 
    if(url_fixa_pesquisa == 'pesquisa'){      
      var palavras_chave = url_fixa_geral.replace(/-/g,' ');
      gtag("event", "search", {
        search_term: palavras_chave
      })
    }

    //ADIÇÃO DOS DADOS DE PAGAMENTO    
    $("#carrinho-frete-btn-finalizar").click(function(){
      gaAddPaymentInfo();
    });

    //ADIÇÃO DOS DADOS DE FRETE   
    $("#carrinho-frete-btn-continuar").click(function(){
      gaAddShippingInfo();
    });
    
    //ADICIONAR PRODUTO AO CARRINHO
    $("#produto-btn-adicionar-carrinho").click(function(){ 
      gaAddToCart();
    });
    $("#produto-btn-comprar").click(function(){ 
      gaAddToCart();
    });
  
    //COMEÇA O CHECKOUT
    $("#carrinho #carrinho-botoes-btn-avancar").click(function(e){
      gaBeginCheckout();
    });    

    //REMOVE PRODUTO DO CARRINHO
    $("#carrinho .carrinho-produto-texto-btn-excluir").click(function(e){
      gaRemoveFromCart($(this).attr('data-id-carrinho-produto'));
    });    

    //SELECIONA UM ITEM
    $(".produto-link").click(function(e){
      gaSelectItem($(this).attr('data-produto-id'), $(this).attr('data-produto-lista'), $(this).attr('data-produto-lista-id'), $(this).find('.produto-container-valor-final').html(), $(this).find('.produto-container-valor-original').html());
    });

    //VE O CARRINHO
    if($("#carrinho").length > 0){
      gaViewCart();
    }

    //VE UM ITEM
    if($("#produto").length > 0){
      gaViewItem();
    }

  //CASO NÃO TENHA INSTALADO O ANALYTICS
  } else {    
    
    //LOGIN NA PÁGINA DO CLIENTE - REMOVE O PARAMETRO DO GOOGLE
    if(url_fixa_geral == 'cliente-dados?gtag=login-cliente'){
      window.history.replaceState({}, document.title, "/" + "cliente-dados");
    } 
    
    //LOGIN NA PÁGINA DO CARRINHO - REMOVE O PARAMETRO DO GOOGLE
    if(url_fixa_geral == 'carrinho-frete?gtag=login-carrinho'){
      window.history.replaceState({}, document.title, "/" + "cliente-frete");
    }

  }

});