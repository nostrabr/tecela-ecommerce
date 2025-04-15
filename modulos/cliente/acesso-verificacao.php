<!--CSS-->
<link rel="stylesheet" href="modulos/cliente/css/style.css">

<?php 

//PEGA IDENTIFICADOR DO CLIENTE
$identificador = filter_var($_SESSION['identificador']);

//BUSCA OS DADOS DO CLIENTE
$busca_cliente = mysqli_query($conn, "SELECT email FROM cliente WHERE identificador = '$identificador'");

//VERIFICA SE ENCONTROU O CLIENTE, SENÃO MANDA PRO LOGIN
if(mysqli_num_rows($busca_cliente) > 0){
    
//FETCH
$cliente = mysqli_fetch_array($busca_cliente);

//RECEBE OS DADOS DO FORM
$email = trim(strip_tags(filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING)));  
$senha = trim(strip_tags(filter_input(INPUT_POST, "senha", FILTER_SANITIZE_STRING)));  

//SE NADA FOI ALTERADO, NÂO DEIXA ACESSAR A TELA
if(($senha == '' & $email === $cliente['email']) | !isset($_POST['email']) | !isset($_POST['senha'])){
    
    echo "<script> window.location.href = 'cliente-acesso'; </script>";

} else {
   
//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){
    if($_SESSION['RETORNO']['ERRO'] == 'ERRO-DADOS-NAO-CONFEREM'){
        echo "<script>mensagemAviso('erro', 'Dados digitados não conferem.', 3000);</script>";
    } else if($_SESSION['RETORNO']['ERRO'] == 'ERRO-EMAIL-REPETIDO'){
        echo "<script>mensagemAviso('erro', 'Este e-mail já está sendo utilizado em nossa loja.', 3000);</script>";
    }
}

?>

<!--CLIENTE DADOS-->
<section id="cliente-acesso" class="cliente">

    <h1 class="d-none">Verificação de alteração de dados de acesso</h1>

    <!--MENU DE OPÇÕES-->
    <div class="row mb-4">
    
        <div class="col-12">

            <div id="cliente-menu">
            
                <ul>
                    <li id="cliente-menu-btn-cliente-dados" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-dados';">Cadastro</li>
                    <li id="cliente-menu-btn-cliente-acesso" class="menu-cliente-ativo" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-acesso';">Acesso</li>
                    <li id="cliente-menu-btn-cliente-enderecos" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-enderecos';">Endereços</li>
                    <?php if(!$modo_whatsapp_simples){ ?>
                        <?php if($modo_whatsapp){ ?>
                            <li id="cliente-menu-btn-cliente-orcamentos" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-orcamentos';">Orçamentos</li>
                        <?php } else { ?>
                            <li id="cliente-menu-btn-cliente-pedidos" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-pedidos';">Pedidos</li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            
            </div>

        </div>

    </div>

    <!--FORM-->
    <form action="modulos/cliente/php/edicao-acesso-verificacao.php" method="POST">

        <div class="row">
        
            <div class="col-12">

                <div class="row">
                    
                    <?php if($email !== $cliente['email']){ ?>
                        <div class="col-12 col-xl-3">             
                            <div class="form-group">
                                <label for="email">Confirmar e-mail <span class="campo-obrigatorio">*</span></label>
                                <input type="hidden" name="email" maxlength="50" value="<?= $email ?>" class="form-control text-lowercase" required>
                                <input type="email" name="email-confirmacao" id="email" maxlength="50" class="form-control text-lowercase" required>
                            </div>  
                        </div>    
                    <?php } ?>   
                    <?php if($senha != ''){ ?>
                        <div class="col-12 col-xl-3">     
                            <div class="form-group">
                                <label for="senha">Confirmar senha <span class="campo-obrigatorio">*</span></label>
                                <input type="hidden" name="senha" maxlength="32" minlength="8" value="<?= $senha ?>" class="form-control" required>
                                <input type="password" name="senha-confirmacao" id="senha" maxlength="32" minlength="8" class="form-control" required>
                            </div>   
                        </div>      
                    <?php } ?>           
                </div>

                <div class="row">
                    <div class="col-12">
                        <input type="submit" id="cliente-acesso-btn-editar" value="Salvar" class="btn-escuro">
                    </div>
                </div>

            </div>

        </div>

    </form>

</section>

<?php /* LIMPA A SESSION DE RETORNO */ unset($_SESSION['RETORNO']); ?>

<?php } } else { ?>

<script> window.location.href = 'login'; </script>

<?php } ?>