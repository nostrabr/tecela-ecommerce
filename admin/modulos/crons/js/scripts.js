function geraXmls(){
    $.ajax({url: "modulos/crons/xml-produtos-facebook.php"});  
    $.ajax({url: "modulos/crons/xml-produtos.php"});  
    $.ajax({url: "modulos/crons/xml-sitemap.php"});  
}

geraXmls();