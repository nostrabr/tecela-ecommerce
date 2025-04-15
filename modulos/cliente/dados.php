<!--CSS-->
<link rel="stylesheet" href="modulos/cliente/css/style.css">

<?php 

//PEGA IDENTIFICADOR DO CLIENTE
$identificador = filter_var($_SESSION['identificador']);

//BUSCA OS DADOS DO CLIENTE
$busca_cliente = mysqli_query($conn, "SELECT nome, sobrenome, cpf, nascimento, telefone, celular FROM cliente WHERE identificador = '$identificador'");

//VERIFICA SE ENCONTROU O CLIENTE, SENÃO MANDA PRO LOGIN
if(mysqli_num_rows($busca_cliente) > 0){

//FETCH
$cliente = mysqli_fetch_array($busca_cliente);

//VALIDA TIPO CLIENTE (FISICA/JURIDICA)
if(strlen($cliente['cpf']) == 18){
    $label_nome = 'Razão Social';
    $label_sobrenome = 'Fantasia';
    $mostra_nascimento = 'd-none';
} else {
    $label_nome = 'Nome';
    $label_sobrenome = 'Sobrenome';
    $mostra_nascimento = '';
}

//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){
    if($_SESSION['RETORNO']['STATUS'] == 'EDITADO-SUCESSO'){
        echo "<script>mensagemAviso('sucesso', 'Editado com sucesso.', 3000);</script>";
    } else if($_SESSION['RETORNO']['ERRO'] == 'ERRO-EDICAO'){
        echo "<script>mensagemAviso('erro', 'Ocorreu um erro ao tentar editar seu cadastro. Se o problema persistir contate o administrador do sistema.', 3000);</script>";
    }
}

?>

<!--CLIENTE DADOS-->
<section id="cliente-dados" class="cliente">

    <h1 class="d-none">Dados pessoais</h1>

    <!--MENU DE OPÇÕES-->
    <div class="row mb-4">
    
        <div class="col-12">

            <div id="cliente-menu">
            
                <ul>
                    <li id="cliente-menu-btn-cliente-dados" class="menu-cliente-ativo" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-dados';">Cadastro</li>
                    <li id="cliente-menu-btn-cliente-acesso" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-acesso';">Acesso</li>
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
    <form action="modulos/cliente/php/edicao-dados.php" method="POST">

        <div class="row">
        
            <div class="col-12">

                <div class="row">

                    <div class="col-12 col-xl-3">
                        <div class="form-group">
                            <label for="nome"><?= $label_nome ?> <span class="campo-obrigatorio">*</span></label>
                            <input type="text" name="nome" id="nome" maxlength="50" class="form-control text-capitalize" value="<?= $cliente['nome'] ?>" required>
                        </div> 
                    </div>          
                    <div class="col-12 col-xl-3">     
                        <div class="form-group">
                            <label for="sobrenome"><?= $label_sobrenome ?> <span class="campo-obrigatorio">*</span></label>
                            <input type="text" name="sobrenome" id="sobrenome" maxlength="50" class="form-control text-capitalize" value="<?= $cliente['sobrenome'] ?>" required>
                        </div>   
                    </div> 
                    <div class="col-12 col-xl-3">            
                        <div class="form-group">
                            <label for="cpf">CPF/CNPJ <span class="campo-obrigatorio">*</span></label>
                            <input type="text" name="cpf" id="cpf-cnpj" maxlength="18" class="form-control" value="<?= $cliente['cpf'] ?>" onblur="javascript: validaCpfCnpj(this.value, this.id);" disabled>
                        </div>   
                    </div>
                    <div class="col-12 col-xl-3 <?= $mostra_nascimento ?>">            
                        <div class="form-group">
                            <label for="nascimento">Nascimento</label>
                            <input type="text" name="nascimento" id="nascimento" class="form-control" value="<?php if($cliente['nascimento'] != '' & $cliente['nascimento'] != '0000-00-00'){ echo date('d/m/Y',strtotime($cliente['nascimento'])); } ?>">
                        </div>   
                    </div>
                
                </div>
                
                <div class="row">
                    
                    <div class="col-12 col-xl-3">             
                        <div class="form-group">
                            <label for="telefone">Telefone </label>
                            <input type="text" name="telefone" id="telefone" class="form-control" value="<?= $cliente['telefone'] ?>">
                        </div>  
                    </div> 
                    
                    <div class="col-12 col-xl-3">             
                        <div class="form-group">
                            <label for="celular">Celular <span class="campo-obrigatorio">*</span></label>
                            <input type="text" name="celular" id="celular" class="form-control" value="<?= $cliente['celular'] ?>" required>
                        </div>  
                    </div> 
                
                </div>

                <div class="row">
                    <div class="col-12">
                        <input type="submit" id="cliente-dados-btn-editar" value="Salvar" class="btn-escuro cliente-btn-editar">
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