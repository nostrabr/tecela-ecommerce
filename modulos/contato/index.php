<!--CSS-->
<link rel="stylesheet" href="modulos/contato/css/style.css">

<?php 

//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){
    if($_SESSION['RETORNO']['STATUS-EMAIL'] == 'EMAIL-ENVIADO'){
        echo "<script>mensagemAviso('sucesso', 'E-mail enviado com sucesso. Em breve responderemos sua solicitação.', 3000);</script>";
    } else if($_SESSION['RETORNO']['STATUS-EMAIL'] == 'ERRO-CAPTCHA'){
        echo "<script>mensagemAviso('erro', 'Erro ao processar reCAPTCHA. Tente novamente.', 3000);</script>";
    } else if($_SESSION['RETORNO']['STATUS-EMAIL'] == 'ERRO-CAMPOS-BRANCO'){
        echo "<script>mensagemAviso('erro', 'Preencha todos os campos e o reCAPTCHA para prosseguir.', 3000);</script>";
    } else {
        echo "<script>mensagemAviso('erro', 'Erro ao enviar e-mail! Se o problema persistir contate o administrador do sistema.', 3000);</script>";
    }
}

?>

<!--CONTATO-->
<section id="contato">

    <h1 class="d-none">Contato</h1>
    
    <p class="mb-0"><b>Precisa falar conosco?</b></p>
    <p>Escolha uma das formas de contato listadas abaixo que teremos o prazer em lhe ajudar.</p>

    <?php if($loja['telefone'] != ''){ ?>
        <label for="contato-btn-telefone">Telefone</label>
        <p id="contato-telefone">
            <a id="contato-btn-telefone" href="tel:+55<?= preg_replace("/[^0-9]/", "", $loja['telefone']) ?>" title="Ligar para a loja">
            <img src="imagens/telefone-escuro.png" alt="Telefone"> <?= $loja['telefone'] ?>
            </a>
        </p>
    <?php } ?>

    <label for="contato-btn-whatsapp">WhatsApp</label>
    <p id="contato-whatsapp">
        <a id="contato-btn-whatsapp" href="https://wa.me/55<?= preg_replace("/[^0-9]/", "", $loja['whatsapp']) ?>?text=Atendimento%20online%20%7C%20Ol%C3%A1%20gostaria%20de%20mais%20informa%C3%A7%C3%B5es..." title="Chamar no Whats" target="_blank">
        <img src="imagens/whatsapp-escuro.png" alt="WhatsApp"> <?= $loja['whatsapp'] ?>
        </a>
    </p>
    
    <label for="contato-redes-sociais">Redes Sociais</label>
    <ul id="contato-redes-sociais">
        <?php if($loja['facebook'] != ''){ ?><li class="contato-redes-sociais-li"><a id="contato-btn-facebook" class="mr-1" href="<?= $loja['facebook'] ?>" target="_blank" title="Visitar Facebook"><img src="imagens/facebook-escuro.png" alt="Facebook"></a></li><?php } ?>
        <?php if($loja['instagram'] != ''){ ?><li class="contato-redes-sociais-li"><a id="contato-btn-instagram" class="mr-1" href="<?= $loja['instagram'] ?>" target="_blank" title="Visitar Instagram"><img src="imagens/instagram-escuro.png" alt="Instagram"></a></li><?php } ?>
        <?php if($loja['twiter'] != ''){ ?><li class="contato-redes-sociais-li"><a id="contato-btn-twitter" class="mr-1" href="<?= $loja['twiter'] ?>" target="_blank" title="Visitar Twitter"><img src="imagens/twitter-escuro.png" alt="Twitter"></a></li><?php } ?>
        <?php if($loja['youtube'] != ''){ ?><li class="fcontato-redes-sociais-li"><a id="contato-btn-youtube" class="mr-1" href="<?= $loja['youtube'] ?>" target="_blank" title="Visitar YouTube"><img src="imagens/youtube-escuro.png" alt="YouTube"></a></li><?php } ?>
        <?php if($loja['pinterest'] != ''){ ?><li class="contato-redes-sociais-li"><a id="contato-btn-pinterest" class="mr-1" href="<?= $loja['pinterest'] ?>" target="_blank" title="Visitar Pinterest"><img src="imagens/pinterest-escuro.png" alt="Pinterest"></a></li><?php } ?>
        <?php if($loja['tiktok'] != ''){ ?><li class="contato-redes-sociais-li"><a id="contato-btn-tiktok" href="<?= $loja['tiktok'] ?>" target="_blank" title="Visitar TikTok"><img src="imagens/tiktok-escuro.png" alt="TikTok"></a></li><?php } ?>
    </ul>
    
    <p class="mt-4 mb-0"><b>Formulário</b></p>
    <p class="mb-0">Se preferir nos enviar um e-mail, preencha o formulário abaixo com seus dados de contato que em breve retornaremos.</p>
    
    <form action="<?php if($loja['recaptcha'] != ''){ ?>modulos/contato/php/recaptcha.php<?php } else { ?>modulos/envio-email/index.php<?php } ?>" method="POST">       
        <input type="hidden" name="tipo-envio" value="formulario-contato" required>
        <div class="form-group">
            <label for="nome">Nome</label>
            <input type="text" name="nome" id="nome" class="form-control text-capitalize" required>
        </div>
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" name="email" id="email" class="form-control text-lowercase" required>
        </div>
        <div class="form-group">
            <label for="celular">Telefone</label>
            <input type="text" name="telefone" id="celular" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="mensagem">Mensagem</label>
            <textarea name="mensagem" id="mensagem" class="form-control" rows="5" required></textarea>
        </div>
        <?php if($loja['recaptcha'] != ''){ ?>
            <div class="form-group">
                <div class="g-recaptcha" data-sitekey="<?= $loja['recaptcha'] ?>"></div>
            </div>
        <?php } ?>
        <div class="form-group">
            <input class="btn" type="submit" name="contato-btn-enviar-formulario" value="Enviar">
        </div>
    </form>
        
</section>

<?php if($loja['recaptcha'] != ''){ ?>
    <script src='https://www.google.com/recaptcha/api.js?hl=pt-BR'></script>
<?php } ?>

<?php /* LIMPA A SESSION DE RETORNO */ unset($_SESSION['RETORNO']); ?>