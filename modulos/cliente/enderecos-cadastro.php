<!--CSS-->
<link rel="stylesheet" href="modulos/cliente/css/style.css">

<!-- Adiciona o script para máscara de CEP -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    $(document).ready(function() {
        $("#cep").mask("00000-000"); // Aplica a máscara ao campo de CEP
    });
</script>

<?php

//VERIFICA SE TEM ALGUM CEP SELECIONADO EM SESSION
if(isset($_SESSION["CEP"])){
    $cep = $_SESSION['CEP'];
} else {
    $cep = '';
}

?>

<!--CADASTRO DE CLIENTE-->
<section class="cliente" id="cliente-enderecos-cadastro">

    <h1 class="d-none">Cadastro de endereço</h1>

    <!--MENU DE OPÇÕES-->
    <div class="row mb-4">

        <div class="col-12">

            <div id="cliente-menu">
            
                <ul>
                    <li id="cliente-menu-btn-cliente-dados" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-dados';">Cadastro</li>
                    <li id="cliente-menu-btn-cliente-acesso" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-acesso';">Acesso</li>
                    <li id="cliente-menu-btn-cliente-enderecos" class="menu-cliente-ativo" onclick="javascript: window.location.href = '<?= $loja['site'] ?>cliente-enderecos';">Endereços</li>
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

    <!--FORM DE CADASTRO DE ENDEREÇO-->
    <form action="modulos/cliente/php/cadastro-endereco.php" method="POST">

        <div class="row">
            <div class="col-12 col-xl-4">
                <div class="form-group">
                    <label for="nome">Nome <span class="campo-obrigatorio">*</span></label>
                    <input type="text" name="nome" id="nome" maxlength="50" class="form-control text-capitalize" required>
                    <small>De um nome ao seu endereço para indetificá-lo facilmente</small>
                </div>              
            </div>  
        </div>
            
        <div class="row">
            <div class="col-12 col-xl-4">
                <div class="form-group">
                    <label for="cep">CEP <span class="campo-obrigatorio">*</span></label>
                    <input type="text" name="cep" maxlength="10" minlength="10" id="cep" class="form-control" value="<?= $cep ?>" required>
                    <small>Digite o seu CEP e o sistema buscará o endereço para você</small>
                </div>              
            </div>                   
        </div>
            
        <div class="row">
            <div class="col-12 col-xl-6">
                <div class="form-group">
                    <label for="rua">Logradouro <span class="campo-obrigatorio">*</span></label>
                    <input type="text" name="rua" id="rua" maxlength="100" class="form-control text-capitalize" required>
                </div>              
            </div>    
            <div class="col-12 col-xl-2">
                <div class="form-group">
                    <label for="numero">Número <span class="campo-obrigatorio">*</span></label>
                    <input type="text" name="numero" id="numero" maxlength="50" class="form-control" required>
                </div>              
            </div>
            <div class="col-12 col-xl-2">
                <div class="form-group">
                    <label for="bairro">Bairro <span class="campo-obrigatorio">*</span></label>
                    <input type="text" name="bairro" id="bairro" maxlength="50" class="form-control text-capitalize" required>
                </div>              
            </div>    
            <div class="col-12 col-xl-2">
                <div class="form-group">
                    <label for="complemento">Complemento</label>
                    <input type="text" name="complemento" id="complemento" maxlength="20" class="form-control text-capitalize">
                </div>              
            </div>                   
        </div>

        <div class="row">            
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="estado">Estado <span class="campo-obrigatorio">*</span></label>
                    <select onchange="buscaCidades(this.value);" id="estado" name="estado" class="form-control" required>
                        <?php if($cliente['estado'] == ''){ ?>
                            <option value='' disabled selected></option>
                        <?php } ?>
                        <?php
                            $busca_ufs = mysqli_query($conn,"SELECT * FROM estado ORDER BY sigla ASC");
                            while ($uf = mysqli_fetch_array($busca_ufs)) {
                                echo "<option value='" . $uf["id"] . "'>" . $uf["sigla"] . "</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="cidade">Cidade <span class="campo-obrigatorio">*</span></label>
                    <select id="cidade" name="cidade" class="form-control" required>
                        <option value="" disabled selected></option>
                    </select>
                </div>
            </div>
        </div>
            
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="referencia">Ponto de referência</label>
                    <input type="text" name="referencia" maxlength="100" id="referencia" class="form-control">
                    <small>Adicione um ponto de referência para ajudar o entregador a lhe encontrar</small>
                </div>              
            </div>                   
        </div>
        
        <div class="row">            
            <div class="col-12">
                <input id="cliente-enderecos-btn-cadastrar" type="submit" class="btn-escuro" value="Salvar">
            </div>
        </div>

    </form>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="modulos/cliente/js/scripts.js"></script>