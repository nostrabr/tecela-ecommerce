<!DOCTYPE html>

<?php 

// $url_atual_redirecionamento = explode('.',$_SERVER['SERVER_NAME']);

// if(!in_array('admin',$url_atual_redirecionamento)){
//     echo '<script>window.location.href = "https://admin.'.$_SERVER['SERVER_NAME'].'"</script>';
// } 

//BUSCA OS DADOS DA LOJA
$busca_loja = mysqli_query($conn, "SELECT nome, site, custom_css FROM loja WHERE id = 1");
$loja       = mysqli_fetch_array($busca_loja);

?>

<html>
    
<head>
    
    <!--TÍTULO DA PÁGINA-->
    <title><?= $loja['nome'] ?> - Admin</title>
    
    <!--METATAGS-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $loja['nome'] ?> - Admin">
    
    <!--ICONS-->
    <link rel="shortcut icon" href="<?= $loja['site'] ?>imagens/favicon.png" type="image/x-icon">
    
    <!--FONTS-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">
    
    <!--CSS-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= $loja['site'] ?>css/global-admin.css">
    <link rel="stylesheet" href="modulos/login/css/style.css">
    
    <!--SCRIPTS-->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    
</head>

<body>   

    <!--TELA DE LOGIN-->
    <div id="login" class="container-fluid">
        
        <div class="row">
            
            <!--ACESSO-->
            <div id="login-acesso" class="col-12">
                <div id="login-container" class="container-fluid">
                    <div id="login-row" class="row justify-content-center align-items-center">
                        <div id="login-acesso-box">
                            <div class="container-fluid">
                                
                                <!--LOGO-->
                                <div id="login-acesso-logo" class="row justify-content-center mt-5">
                                    <div>
                                        <ul>
                                            <li class="text-center"><img id="login-acesso-img" src="<?= $loja['site'] ?>imagens/logo-admin.png"></li>
                                            <li><p class="text-white">PAINEL ADMINISTRATIVO</p></li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <!--DADOS-->
                                <div class="justify-content-center">
                                    
                                    <!--FORM DE ACESSO-->
                                    <div class="w-100">
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <label for="form-login" class="text-white">Login:</label>
                                                <input type="text" class="form-control form-control-lg form-control" id="form-login" name="login">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <label for="form-senha" class="text-white">Senha:</label>
                                                <input type="password" class="form-control form-control-lg" id="form-senha" name="senha">
                                            </div>
                                        </div>
                                        <div id="esqueceu-senha">
                                            <a href="javascript: geraNovaSenha();" class="text-white">Esqueceu sua senha?</a>
                                        </div>
                                        <div class="form-group row justify-content-center">
                                            <button type="button" class="mt-3 mt-md-4" id="form-button">ENTRAR</button>
                                        </div>
                                    </div>
                                    
                                </div>
                                
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>   
            
        </div>    
        
    </div>
    
</body>

<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>css/<?= $loja['custom_css'] ?>">

<!--SCRIPTS-->
<script type="text/javascript" src="modulos/recuperar-senha/js/scripts.js"></script>
<script type="text/javascript" src="modulos/login/js/scripts.js"></script>

</html>