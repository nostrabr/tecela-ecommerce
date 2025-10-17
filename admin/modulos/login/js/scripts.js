//PROCESSA O LOGIN
$(document).on('keypress',function(e) {
    if(e.which === 13) {
        processaLogin();
    }
});

$("#form-button").click(function(){
    processaLogin(); 
});

function processaLogin(){
        
    var login = $("#form-login").val();
    var senha = $("#form-senha").val();
    
    var urlData = "&login="+login+"&senha="+senha;
    
    $.ajax({
        type: "POST",
        url: "modulos/login/php/processa-login.php",
        async: true,
        data: urlData,
        success: function(data) {
            if(data === "OK"){
                window.location.href = "dashboard.php";
            } else if (data === "NOT OK"){ 
                mensagemAviso('erro', 'Dados de acesso incorretos', 2000);
            }
        },
        beforeSend: function() {
            abreLoader();
        }
    });
    
}