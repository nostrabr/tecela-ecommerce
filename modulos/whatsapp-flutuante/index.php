<?php if($loja['design_whatsapp_flutuante'] == 1){ ?>

<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/whatsapp-flutuante/css/style.css">

<!--WHATSAPP FLUTUANTE-->
<div id="whatsapp-flutuante">
    <a id="whatsapp-flutuante-link" href="https://wa.me/55<?= preg_replace("/[^0-9]/", "", $loja['whatsapp']) ?>?text=Atendimento%20online%20%7C%20Ol%C3%A1%20gostaria%20de%20mais%20informa%C3%A7%C3%B5es..." target="_blank"><img src="<?= $loja['site'] ?>/imagens/whatsapp-flutuante.png" alt="WhatsApp Flutuante"></a>
</div>

<?php } ?>
