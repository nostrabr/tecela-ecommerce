$(document).ready(function(){
    acertaOrdemCategorias();
});

var nestedSortables = [].slice.call(document.querySelectorAll('.nested-sortable'));
for (var i = 0; i < nestedSortables.length; i++) {
	new Sortable(nestedSortables[i], {
		group: 'nested',
        animation: 150,
        ghostClass: "ghost",
		fallbackOnBody: true,
        swapThreshold: 0.65,
        sort: true,
        onEnd: function(evt) {

            //PEGA OS DADOS DA CATEGORIA ALTERADA
            var id                  = evt.item.getAttribute('id');
            var nivel_atual         = evt.item.getAttribute('nivel');
            var nivel_novo          = parseInt(evt.item.parentNode.parentNode.getAttribute('nivel'))+1;
            var pai_novo            = evt.item.parentNode.parentNode.getAttribute('id');

            //ALTERA O PAI
            evt.item.setAttribute('pai',pai_novo);
            evt.item.setAttribute('nivel',nivel_novo);
            evt.item.classList.remove("nested-"+nivel_atual);
            evt.item.classList.add("nested-"+nivel_novo); 
            editaCategoria(id, nivel_novo, pai_novo);

            //ALTERA OS FILHOS
            var diferenca_ninhos = nivel_atual - nivel_novo;
            $("#"+id).find(".nested").each(function(){
                var id_filho          = parseInt($(this).attr("id"));
                var pai_atual_filho   = parseInt($(this).attr("pai"));
                var nivel_atual_filho = parseInt($(this).attr("nivel"));
                var nivel_novo_filho  = nivel_atual_filho - diferenca_ninhos;
                $(this).attr("nivel",nivel_novo_filho);
                $(this).removeClass("nested-"+nivel_atual_filho);
                $(this).addClass("nested-"+nivel_novo_filho);
                editaCategoria(id_filho, nivel_novo_filho, pai_atual_filho);    
            });

            //ACERTA A ORDEM DAS CATEGORIAS
            acertaOrdemCategorias();

        }

    });
    
}

//ACERTA A ORDEM DAS CATEGORIAS
function acertaOrdemCategorias(){
    
    var ordem = 1;    
    var nivel_max = buscaNivelMaximo();   
    for(var i = 0; i<nivel_max; i++){
        $("#ninho").find(".nested-"+(i+1)).each(function(){
            $(this).attr("ordem",ordem);
            editaOrdemCategoria($(this).attr("id"), ordem);
            ordem++;
        });
        ordem = 1;
    }
}

//BUSCA O NÍVEL MÁXIMO
function buscaNivelMaximo(){
    var nivel_max = 0;        
    $("#ninho").find(".nested").each(function(){
        var nivel_busca = $(this).attr("nivel");
        if(nivel_busca > nivel_max)
            nivel_max = nivel_busca;
    });     
    return nivel_max;
}

//TROCA O NOME DA CATEGORIA
function trocaNome(identificador){    
    var nome = $("#nome-"+identificador).val();
    $.ajax({
        url: "modulos/categorias/php/edicao-nome-categoria.php",
        type: "POST",
        data: {"identificador": identificador, "nome": nome},
        success: function (data){
            if(data === "SESSAO INVALIDA") {
                window.location.href = 'logout.php'
            } else if(data != '' & data != 'SESSAO INVALIDA') {
                mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
            }
        }
    });  
}

//ADICIONA CATEGORIA
function novaCategoria(){    
    var nome = $("#nova-categoria").val();
    if(nome != ''){        
        $("#loader").show();
        $("#modal-add-categoria").modal("hide");
        $.ajax({
            url: "modulos/categorias/php/cadastro.php",
            type: "POST",
            data: {"nome": nome},
            success: function (data){
                if(data === "SESSAO INVALIDA") {
                    window.location.href = 'logout.php'
                } else if(data === "OK") {
                    location.reload();
                } else {
                    mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
                }
            }
        });  
    } else {
        $("#modal-add-categoria").modal("hide");
    }
}

//SETA O FOCO NO CAMPO NOME AO ABRIR O MODAL DE NOVA CATEGORIA
$("#modal-add-categoria").on('shown.bs.modal', function () {
    $('#nova-categoria').focus();
});

//EXCLUI CATEGORIA
function excluiCategoria(identificador, nome){

    var confirma = confirm("Confirma a exclusão da categoria "+nome+" e de todas as suas filhas?\nObs: Esta ação é irreversível, e todos os produtos cadastrados nestas categorias vão ser recategorizados para o pai mais próximo.");
    if(confirma){
        $("#loader").show();
        $("#modal-add-categoria").modal("hide");
        $.ajax({
            url: "modulos/categorias/php/exclusao.php",
            type: "POST",
            data: {"identificador": identificador},
            success: function (data){
                if(data === "SESSAO INVALIDA") {
                    window.location.href = 'logout.php'
                } else if(data === "OK") {
                    location.reload();
                } else {
                    mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
                }
            }
        }); 
    }

}

//EDITA CATEGORIA
function editaCategoria(id, nivel, pai){
    
    $.ajax({
        url: "modulos/categorias/php/edicao.php",
        type: "POST",
        data: {"id": id, "nivel": nivel, "pai": pai},
        success: function (data){
            if(data === "SESSAO INVALIDA") {
                window.location.href = 'logout.php'
            } else if(data != '' & data != 'SESSAO INVALIDA') {
                mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
            }
        }
    }); 

}

//EDITA NIVEL CATEGORIA
function editaOrdemCategoria(id, ordem){
    
    $.ajax({
        url: "modulos/categorias/php/edicao-ordem-categoria.php",
        type: "POST",
        data: {"id": id, "ordem": ordem},
        success: function (data){
            if(data === "SESSAO INVALIDA") {
                window.location.href = 'logout.php'
            } else if(data != '' & data != 'SESSAO INVALIDA') {
                mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
            }
        }
    }); 

}

//BOTA OU TIRA UMA CATEGORIA DE UMA PROMOÇÃO
function promocaoCategoria(identificador, nome, promocao){

    if(promocao == 0){
        $("#modal-add-promocao #nome-categoria").html(nome);
        $("#modal-add-promocao #porcentagem-desconto").val('');
        $("#modal-add-promocao #validade").val('');
        $("#modal-add-promocao #identificador-categoria").val(identificador);
        $("#modal-add-promocao").modal("show");
    } else if(promocao == 1){
         
        var confirma = confirm("Confirma o encerramento da promoção da categoria "+nome+"?");
        if(confirma){
            $("#promocao-"+identificador).removeClass("promocao-ativada").attr("title","Ativar promoção").attr("href","javascript: promocaoCategoria('"+identificador+"','"+nome+"','0');");
            alterarStatusPromocaoCategoria(0, identificador);
        }
    
    }
        
}

function alterarStatusPromocaoCategoria(status, identificador){
    
    if(status == 0){
        $.ajax({
            url: "modulos/categorias/php/alterar-status-promocao.php",
            type: "POST",
            data: {"identificador": identificador, "status": status},
            success: function (data){
                if(data === "SESSAO INVALIDA") {
                    window.location.href = 'logout.php'
                } else if(data != '' & data != 'SESSAO INVALIDA') {
                    mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
                }
            }
        }); 
    } else if(status == 1){

        var porcentagem = $("#modal-add-promocao #porcentagem-desconto").val();
        var validade    = $("#modal-add-promocao #validade").val();
        var nome        = $("#modal-add-promocao #nome-categoria").html();
        identificador   = $("#modal-add-promocao #identificador-categoria").val();

        if(porcentagem != '' & validade != '' & validade.length == 10){
            $.ajax({
                url: "modulos/categorias/php/alterar-status-promocao.php",
                type: "POST",
                data: {"identificador": identificador, "porcentagem": porcentagem, "status": status, "validade": validade},
                success: function (data){
                    if(data === "SESSAO INVALIDA") {
                        window.location.href = 'logout.php'
                    } else if(data != '' & data != 'SESSAO INVALIDA') {
                        mensagemAviso('erro', 'Erro inesperado! Se o problema persistir, contate o administrador do sistema.', 3000);
                    } else {
                        $("#promocao-"+identificador).addClass("promocao-ativada").attr("title","Desativar promoção").attr("href","javascript: promocaoCategoria('"+identificador+"','"+nome+"','1');");
                        $("#modal-add-promocao").modal("hide");
                    }
                }
            }); 
        } else {
            $("#modal-add-promocao").modal("hide");
        }
    }

}

//EXCLUI CATEGORIA
function imagemCategoria(identificador){

    $("#identificador-categoria-imagem").val(identificador)
    $("#modal-add-imagem").modal("show");

}

$(document).on('keypress',function(e) {
    if(e.which === 13 & document.activeElement.id == "nova-categoria") {
        novaCategoria();
    } 
    if(e.which === 13 & $("#modal-add-promocao").css("display") == "block") {
        $(".btn-altera-promocao").trigger("click");
    }
});

$(".btn-promocao").on('touchstart', function(e) {
    $(this).trigger('click'); 
});

$(".acao-add-imagem").on('touchstart', function(e) {
    $(this).trigger('click');
});

$(".acao-excluir").on('touchstart', function(e) {});