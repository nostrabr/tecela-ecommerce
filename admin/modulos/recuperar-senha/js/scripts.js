//FUNÇÃO QUE GERA UMA NOVA SENHA
function geraNovaSenha(){

    var email = prompt("Digite o e-mail cadastrado na sua conta:");
 
    if (email != null){
        
        var urlData = "&email="+email;
        
        $.ajax({
            type: "POST",
            url: "modulos/recuperar-senha/php/recuperar.php",
            async: true,
            data: urlData,
            dataType : "json",
            success: function(retorno) {
                if(retorno[0].status === "OK"){
                    mensagemAviso("sucesso","Conta encontrada! Uma nova senha foi enviada para o e-mail selecionado.",3000);
                } else if(retorno[0].status === "erro-email"){
                    mensagemAviso("erro","Erro ao enviar e-mail: "+retorno[0].erro+". Se o erro persistir, contate o administrador do sistema.",3000);
                } else if(retorno[0].status === "ERRO-1"){
                    mensagemAviso("erro","E-mail não pode estar em branco.",3000);
                } else if(retorno[0].status === "ERRO-2"){
                    mensagemAviso("erro","E-mail não encontrado.",3000);
                } else if(retorno[0].status === "ERRO-3"){
                    mensagemAviso("erro","Erro não identificado! Caso o problema persista, contate o administrador do sistema.",3000);                    
                }
            },
            beforeSend: function() {
                abreLoader();
            }
        });
        
    }
    
}