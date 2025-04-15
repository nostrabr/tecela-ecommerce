function listaFiliais(){
    $.ajax({
        type: "POST",
        url: $("#site").val()+"custom/busca-filiais.php",
        dataType : "json",
        success: function (filiais) {  
            var html_filiais = '<div class="col-12" id="container-filiais"><div id="filiais"><select class="form-control" name="filial" id="filial" required><option value="" disabled selected>Seleciona a filial de retirada..</option>';                        
            for(var i=0; i<filiais.length; i++){
                html_filiais += '<option value="'+filiais[i]['identificador']+'">'+filiais[i]['nome']+' - '+filiais[i]['endereco']+'</option>';
            }
            html_filiais += '</select></div></div>';              
            $("#carrinho-frete-resultados").append(html_filiais);   

            $("#carrinho-frete-btn-continuar").click(function(){
                if($("#container-filiais").length == 1){
                    if($("#filial").val() == null){
                        alert('Selecione a filial de retirada');
                        return false;
                    } else {
                        $.ajax({
                            type: "POST",
                            url: $("#site").val()+"custom/cadastra-filial-pedido.php",
                            data: {'filial': $("#filial").val()},
                            beforeSend: function() {
                                abreLoader();
                            }
                        });
                    }
                }
            });

            fechaLoader();

        },
        beforeSend: function() {
            abreLoader();
        }
    });
}

function clicaTipoFrete(){  
    $(".carrinho-frete-resultado").click(function(){
        if($(this).attr('tipo') == 'Retirar'){
            if($("#container-filiais").length == 0){
                listaFiliais();
            }
        } else {
            $("#container-filiais").remove();
        }
    });
}

if($("#cliente-pedidos-resumo").length > 0){
    $.ajax({
        type: "POST",
        url: $("#nome_site").val()+"custom/busca-filial-pedido.php",
        data: {'carrinho': $("#carrinho").val()},
        dataType : "json",
        success: function (filial) {  
            if(filial != ''){
                $("#cliente-pedidos-resumo").append(
                    '<div class="cliente-pedidos-resumo-entrega">'+
                        '<div class="cliente-pedidos-resumo-informacao">'+
                            '<ul class="m-0">'+
                                '<li>Filial escolhida para retirada:</li>'+
                                '<li>'+filial[0]['nome']+'</li>'+
                                '<li>Endereço: '+filial[0]['endereco']+' - '+filial[0]['cidade']+'/'+filial[0]['estado']+'</li>'+
                                '<li>E-mail: '+filial[0]['email']+'</li>'+
                                '<li>Telefone: '+filial[0]['telefone']+'</li>'+
                            '</ul>'+                       
                        '</div>'+  
                    '</div>'
                );
            }
        }
    });
}

$(".banner-secundario-item-capa").hover(function(){
    $(this).css('background-color','rgba(0,0,0,0.4)');
    $(this).parent('.banner-secundario-item').find('img').css('object-position','left');
});
$(".banner-secundario-item-capa").mouseleave(function(){
    $(this).css('background-color','rgba(0,0,0,0.0)');
    $(this).parent('.banner-secundario-item').find('img').css('object-position','center');
});
$("#rodape .col-xl-8").html('NTBR Licenças de Marcas');