<?php

//BUSCA OS DADOS DA LOJA
$busca_loja = mysqli_query($conn, "
    SELECT l.*, c.nome AS nome_cidade, e.sigla AS sigla_estado 
    FROM loja AS l 
    LEFT JOIN cidade AS c ON l.cidade = c.id 
    LEFT JOIN estado AS e ON l.estado = e.id 
    WHERE l.id = 1
");
$loja       = mysqli_fetch_array($busca_loja);

?>

<!DOCTYPE html>

<html lang = "pt-br">
    
<head>

    <?php //SE A LOJA NÃO ESTIVER MAIS EM MANUTENÇÃO, REDIRECIONA PARA A PÁGINA PRINCIPAL
    if($loja['site_manutencao'] == 0){ echo "<script>window.location.href = '".$loja['site']."';</script>"; } ?>

    <?php if($loja['facebook_pixel'] != ''){ ?>
        <!-- FACEBOOK PIXEL -->
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '<?= $loja['facebook_pixel'] ?>');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?= $loja['facebook_pixel'] ?>&ev=PageView&noscript=1" /></noscript>
    <?php } ?>

    <?php if($loja['google_analytics'] != ''){ ?>
        <!--GOOGLE ANALYTICS-->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $loja['google_analytics'] ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?= $loja['google_analytics'] ?>');
        </script>
    <?php } ?>
    
    <?php if($loja['google_tag_manager_script'] != ''){ ?>
        <!--GOOGLE TAG MANAGER SCRIPT-->
        <script>
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','<?= $loja['google_tag_manager_script'] ?>');
        </script>
    <?php } ?>
    
    <!--METATAGS-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
    <title><?= $loja['nome'] ?> - Site em Manutenção</title>
    <meta name="description" content="<?= $loja['sobre'] ?>">
    <meta name="keywords" content="<?= $loja['palavras_chave'] ?>">
    <meta property="og:title" content="<?= $loja['nome'] ?>">
    <meta property="og:description" content="<?= $loja['sobre'] ?>">

    <!--METATAG DE INCORPORAÇÃO-->
    <meta property="fb:app_id" content="" /> <!--  Non-Essential, But Required for Analytics -->
    <meta property="og:image" content="<?= $loja['site'].'imagens/incorporacao.png' ?>">
    <meta property="og:image:url" content="<?= $loja['site'].'imagens/incorporacao.png' ?>">
    <meta property="og:image:secure_url" content="<?= $loja['site'].'imagens/incorporacao.png' ?>">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="">
    <meta property="og:image:height" content="">
    <meta property="og:image:alt" content=""> <!-- A descrição da imagem sendo incorporada -->
    <meta property="og:url" content="<?= $loja['site'] ?>">
    <meta property="og:site_name" content="<?= $loja['nome'] ?>">
    <meta property="og:type" content="website">
    <meta name="twitter:site" content=""> <!--  Non-Essential, But Required for Analytics -->
    <meta name="twitter:title" content="<?= $loja['nome'] ?>">
    <meta name="twitter:description" content=" <?= $loja['sobre'] ?>">
    <meta name="twitter:image" content="<?= $loja['site'].'imagens/incorporacao.png' ?>">
    <meta name="twitter:image:alt" content="<?= $loja['site'] ?>">
    <meta name="twitter:card" content="summary_large_image">
    
    <!--ICONS-->
    <link rel="apple-touch-icon" sizes="57x57" href="<?= $loja['site'] ?>imagens/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?= $loja['site'] ?>imagens/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?= $loja['site'] ?>imagens/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= $loja['site'] ?>imagens/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?= $loja['site'] ?>imagens/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?= $loja['site'] ?>imagens/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?= $loja['site'] ?>imagens/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?= $loja['site'] ?>imagens/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $loja['site'] ?>imagens/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= $loja['site'] ?>imagens/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $loja['site'] ?>imagens/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?= $loja['site'] ?>imagens/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $loja['site'] ?>imagens/favicon-16x16.png">
    <link rel="manifest" href="<?= $loja['site'] ?>imagens/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?= $loja['site'] ?>imagens/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    
    <!--FONTS-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/92360c3dda.js" crossorigin="anonymous"></script>
    
    <!--CSS-->
    <link rel="stylesheet" href="<?= $loja['site'] ?>css/global-site.css">
    <link rel="stylesheet" href="<?= $loja['site'] ?>modulos/manutencao/css/style.css">

</head>

    <body>   
    
        <!--MANUTENÇÃO-->
        <section id="manutencao">
        
            <ul>
                <li><img src="<?= $loja['site'].'imagens/logo.png' ?>" alt="<?= $loja['nome'] ?>"></li>
                <li id="manutencao-texto">Site em<br>manutenção!</li>
            </ul>

        </section>

    </body>  

</html> 

<!--CSS CUSTOM-->
<link rel="stylesheet" href="<?= $loja['site'] ?>css/<?= $loja['custom_css'] ?>">