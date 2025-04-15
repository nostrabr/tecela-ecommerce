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

//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){
    if($_SESSION['RETORNO']['STATUS'] == 'SUCESSO-EDICAO'){
        echo "<script>mensagemAviso('sucesso', 'Dados de acesso editados com sucesso.', 3000);</script>";
    }
}

?>

<!--CLIENTE DADOS DE ACESSO-->
<section id="cliente-acesso" class="cliente">

    <h1 class="d-none">Dados de acesso</h1>

    <!--MENU DE OPÇÕES-->
    <div class="row mb-4">
    
        <div class="col-12">

            <div id="cliente-menu">
            
                <ul>
                    <li id="cliente-menu-btn-cliente-dados"     onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-dados';">Cadastro</li>
                    <li id="cliente-menu-btn-cliente-acesso"    class="menu-cliente-ativo" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-acesso';">Acesso</li>
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
    <form action="cliente-acesso-verificacao" method="POST">

        <div class="row">
        
            <div class="col-12">

                <div class="row">
                    <div class="col-12 col-xl-3">             
                        <div class="form-group">
                            <label for="email">E-mail <span class="campo-obrigatorio">*</span></label>
                            <input type="email" name="email" id="email" maxlength="50" class="form-control text-lowercase" value="<?= $cliente['email'] ?>" required>
                        </div>  
                    </div>       
                    <div class="col-12 col-xl-3">     
                        <div class="form-group">
                            <label for="senha">Senha</label>
                            <input type="password" name="senha" id="senha" maxlength="32" minlength="8" class="form-control">
                            <small>Preencha para alterar</small>
                        </div>   
                    </div>             
                </div>

                <div class="row">
                    <div class="col-12">
                        <input type="submit" id="cliente-acesso-btn-avancar" value="Avançar" class="btn-escuro">
                    </div>
                </div>

            </div>

        </div>

    </form>

</section>

<?php /* LIMPA A SESSION DE RETORNO */ unset($_SESSION['RETORNO']); ?>

<?php } else { ?>

<script> window.location.href = 'login'; </script>

<?php } ?>