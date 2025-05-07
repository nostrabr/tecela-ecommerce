<!DOCTYPE html>

<?php 

//BUSCA OS DADOS DA LOJA
$busca_loja = mysqli_query($conn, "SELECT * FROM loja WHERE id = 1");
$loja       = mysqli_fetch_array($busca_loja);

//SE VEIO LOGADO DO SITE
if(isset($_SESSION["plataforma"])) {
    if($_SESSION["plataforma"] === 'SITE') {
        echo "<script>window.location.href='logout.php';</script>";
    }
}

//VERIFICA SE O MODO WHATSAPP ESTÁ ATIVADO E ATRIBUI À VARIÁVEL
if($loja['modo_whatsapp'] == 0){ 
    $modo_whatsapp = false;
    $modo_whatsapp_simples = false;
} else {
    $modo_whatsapp = true;
    if($loja['modo_whatsapp_simples'] == 0){ 
        $modo_whatsapp_simples = false;
    } else {
        $modo_whatsapp_simples = true;
    }
}

//CONFIGURA A VARIÁVEL MODO_ENVIOS
if($loja['modo_envios'] == 0){
    $modo_envios = false;
} else {
    $modo_envios = true;
}

//CONFIGURA A VARIÁVEL LOJA_ROUPAS
if($loja['loja_roupa'] == 0){
    $loja_roupas = false;
} else {
    $loja_roupas = true;
}

?>

<html lang="pt-br">
    
<head>
    
    <!--TÍTULO DA PÁGINA-->
    <title><?= $loja['nome'] ?> - Admin</title>
    
    <!--METATAGS-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $loja['nome'] ?> - Admin">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="Content-Language" content="pt-br">
    
    <!--ICONS-->
    <link rel="shortcut icon" href="<?= $loja['site'] ?>imagens/favicon.png" type="image/x-icon">

    <!--FONTS-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!--POPPER-->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    
    <!--CSS-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= $loja['site'] ?>css/global-admin.css">
    
    <!--SCRIPTS-->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js" integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js" integrity="sha512-Rdk63VC+1UYzGSgd3u2iadi0joUrcwX0IWp2rTh6KXFoAmgOjRS99Vynz1lJPT8dLjvo6JZOqpAHJyfCEZ5KoA==" crossorigin="anonymous"></script>
    
</head>

<body id="body-admin">   

<input type="hidden" id="nome_site" value="<?= $loja['site'] ?>">