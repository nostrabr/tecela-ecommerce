$(".cliente-enderecos-endereco-btn-editar").click(function(e){
    e.stopPropagation();
});
  
$(".cliente-enderecos-endereco-btn-excluir").click(function(e){
    e.stopPropagation();
});

function copiarTexto(){
    let textoCopiado = document.getElementById("txt-copy");
    textoCopiado.select();
    textoCopiado.setSelectionRange(0, 99999)
    document.execCommand("copy");
    $("#txt-copy").blur();
    mensagemAviso('sucesso', 'Chave copiada!', 1000);
}

function selecionarEnderecoPadrao(identificador){
    
    $('.cliente-enderecos-endereco').each(function(){
        $(this).removeClass('cliente-enderecos-endereco-ativo');
        $(this).find(".cliente-enderecos-endereco-label-padrao").remove();
    });
    $("#cliente-endereco-"+identificador).addClass('cliente-enderecos-endereco-ativo');
    $("#cliente-endereco-"+identificador+" .row").append('<div class="cliente-enderecos-endereco-label-padrao"><span class="d-block d-sm-none">P</span><span class="d-none d-sm-block">PADRÃO</span></div>');

    $.ajax({
        url: "modulos/cliente/php/alterar-endereco-padrao.php",
        type: "POST",
        dataType: "json",
        data: {"identificador": identificador},
        success: function (retorno){
            if(retorno[0].status == 'ERRO'){
                fechaLoader();
                mensagemAviso('erro', 'Erro ao alterar endereço padrão. Se o problema persistir, contate o administrador do sistema.', 3000);
            } else {    
                fechaLoader();
            }
        },
        beforeSend: function() {
            abreLoader();
        }
    });

}

function excluirEndereco(identificador, nome){

    var confirma = confirm('Confirma a exclusão do endereço '+nome+'?');

    if(confirma){

        $.ajax({
            url: "modulos/cliente/php/excluir-endereco.php",
            type: "POST",
            dataType: "json",
            data: {"identificador": identificador},
            success: function (retorno){
                if(retorno[0].status == 'ERRO'){
                    fechaLoader();
                    mensagemAviso('erro', 'Erro ao tentar excluir endereço. Se o problema persistir, contate o administrador do sistema.', 3000);
                } else {    
                    location.reload();
                }
            },
            beforeSend: function() {
                abreLoader();
            }
        });

    }

}

function rastrearEtiqueta(etiqueta){
    $.ajax({
        url: $("#site").val()+"modulos/cliente/php/rastrear-etiqueta.php",
        type: "POST",
        dataType: "json",
        data: {"etiqueta": etiqueta},
        success: function (data){ 
            window.open('https://www.melhorrastreio.com.br/rastreio/'+data[etiqueta].melhorenvio_tracking, '_blank');
            fechaLoader()
        },
        beforeSend: function() {
            abreLoader();
        }
    });
}

function textAreaAdjust(el){
    el.style.height = (el.scrollHeight > el.clientHeight) ? (el.scrollHeight)+"px" : "40px";
}

if($('#txt-copy').length > 0){
    textAreaAdjust(document.getElementById('txt-copy'));
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

$(document).ready(function(){
    var cep = $("#cep").val();
    if(cep.length == 10){
        buscaEndereco(cep);
    }
});
