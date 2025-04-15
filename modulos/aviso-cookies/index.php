<?php if($loja['opcao_mensagem_cookies'] == 1){ ?>

<?php 

//SE TEM SETADO O COOKIE DA LINGUAGEM, ATRIBUI
if(isset($_COOKIE['aviso-cookie'])){    
    $aviso_cookies = $_COOKIE['aviso-cookie'];

//SENÃO SETA COMO BR
} else {    
    //SETA COOKIE
    setcookie("aviso-cookie", "n_aceito", time()+(3600*24*30*12*5), "/");
    $aviso_cookies = 'n_aceito';
}

if($aviso_cookies == 'n_aceito'){

?>

<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/aviso-cookies/css/style.css">

<!--AVISO SOBRE COOKIES-->
<div id="aviso-cookies">
    <div class="container">
        <div class="row">
            <div class="col-12 col-10">    
                Este site armazena cookies para melhorar a experiência do usuário. Você pode desativá-los através do seu navegador, entretanto, algumas áreas e funcionalidades não funcionarão corretamente. Para mais informações você pode consultar os nossos <a href="<?= $loja['site'].'politica-termos-uso' ?>" target="_blank">TERMOS DE USO</a>. Ao continuar navegando por este site e utilizando nossos serviços, você aceita estes termos.
            </div>
            <div class="col-12 col-2">    
                <a id="btn-fechar-aviso-cookies" href="javascript: aceiteCookies();">FECHAR E CONTINUAR</a>
            </div>
        </div>
    </div>
</div>

<!--SCRIPTS-->
<script type="text/javascript" src="<?= $loja['site'] ?>modulos/aviso-cookies/js/scripts.js"></script>

<?php } } ?>
