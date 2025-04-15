$(function(){
    data = {
        "@context": "https://schema.org",
        "@type": "Organization",
        "@id": $("#site").val(),
        "name" : $("#site-nome").val(),
        "url": window.location.href,
        "logo": $("#site").val()+"imagens/incorporacao.png",
        "telephone": "+55"+$("#site-whatsapp").val().replace(/[^0-9]/g,''),
        "address": {
            "@type": "PostalAddress",
            "streetAddress": $("#site-endereco-rua").val()+", "+$("#site-endereco-numero").val(),
            "addressLocality": $("#site-endereco-cidade").val(),
            "addressRegion": $("#site-endereco-uf").val(),
            "postalCode": $("#site-endereco-cep").val(),
            "addressCountry": "BR"
        },
    };
    $("#json-informacoes-loja").html(JSON.stringify(data));
});