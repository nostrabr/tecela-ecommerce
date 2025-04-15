function aceiteCookies(){
    $("#aviso-cookies").hide(200);
    $.ajax({
        url: $("#site").val()+"modulos/aviso-cookies/php/aceita-cookies.php",
        type: "POST"
    });
}