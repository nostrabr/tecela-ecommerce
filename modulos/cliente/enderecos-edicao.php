<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/cliente/css/style.css">

<?php 

//PEGA O IDENTIFICADOR DO ENDEREÇO
$identificador = filter_input(INPUT_GET, 'id',FILTER_SANITIZE_STRING);

//BUSCA O ENDEREÇO
$busca_endereco = mysqli_query($conn, "SELECT * FROM cliente_endereco WHERE identificador = '$identificador ' AND status = 1");

//SE ENCONTROU O ENDEREÇO
if(mysqli_num_rows($busca_endereco) > 0){

//FETCH
$endereco = mysqli_fetch_array($busca_endereco);
    
?>

<!--CADASTRO DE CLIENTE-->
<section class="cliente" id="cliente-enderecos-cadastro">

    <h1 class="d-none">Edição de endereço</h1>

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
    <form action="<?= $loja['site'] ?>modulos/cliente/php/edicao-endereco.php" method="POST">

        <input type="hidden" name="identificador" value="<?= $endereco['identificador'] ?>" required>

        <div class="row">
            <div class="col-12 col-xl-4">
                <div class="form-group">
                    <label for="nome">Nome <span class="campo-obrigatorio">*</span></label>
                    <input type="text" name="nome" id="nome" maxlength="50" class="form-control text-capitalize" value="<?= $endereco['nome'] ?>" required>
                    <small>De um nome ao seu endereço para indetificá-lo facilmente</small>
                </div>              
            </div>  
        </div>
            
        <div class="row">
            <div class="col-12 col-xl-4">
                <div class="form-group">
                    <label for="cep">CEP <span class="campo-obrigatorio">*</span></label>
                    <input type="text" name="cep" maxlength="10" minlength="10" id="cep" class="form-control" value="<?= $endereco['cep'] ?>" onkeyup="javascript: buscaEndereco(this.value);" required>
                    <small>Digite o seu CEP e o sistema buscará o endereço para você</small>
                </div>              
            </div>                   
        </div>
            
        <div class="row">
            <div class="col-12 col-xl-6">
                <div class="form-group">
                    <label for="rua">Logradouro <span class="campo-obrigatorio">*</span></label>
                    <input type="text" name="rua" id="rua" maxlength="100" class="form-control text-capitalize" value="<?= $endereco['logradouro'] ?>" required>
                </div>              
            </div>    
            <div class="col-12 col-xl-2">
                <div class="form-group">
                    <label for="numero">Número <span class="campo-obrigatorio">*</span></label>
                    <input type="text" name="numero" id="numero" maxlength="50" class="form-control" value="<?= $endereco['numero'] ?>" required>
                </div>              
            </div>
            <div class="col-12 col-xl-2">
                <div class="form-group">
                    <label for="bairro">Bairro <span class="campo-obrigatorio">*</span></label>
                    <input type="text" name="bairro" id="bairro" maxlength="50" class="form-control text-capitalize" value="<?= $endereco['bairro'] ?>" required>
                </div>              
            </div>    
            <div class="col-12 col-xl-2">
                <div class="form-group">
                    <label for="complemento">Complemento</label>
                    <input type="text" name="complemento" id="complemento" maxlength="20" value="<?= $endereco['complemento'] ?>" class="form-control text-capitalize">
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
                                if($endereco['estado'] == $uf["id"]){
                                    echo "<option value='" . $uf["id"] . "' selected>" . $uf["sigla"] . "</option>";
                                } else {
                                    echo "<option value='" . $uf["id"] . "'>" . $uf["sigla"] . "</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <?php $busca_cidades = mysqli_query($conn,"SELECT id, nome FROM cidade WHERE id_estado = ".$endereco['estado']." ORDER BY nome"); ?>
                    <label for="cidade">Cidade <span class="campo-obrigatorio">*</span></label>
                    <select id="cidade" name="cidade" class="form-control" required>
                        <?php
                            while ($cidade = mysqli_fetch_array($busca_cidades)) {
                                if($endereco['cidade'] == $cidade["id"]){
                                    echo "<option value='" . $cidade["id"] . "' selected>" . $cidade["nome"] . "</option>";
                                } else {
                                    echo "<option value='" . $cidade["id"] . "'>" . $cidade["nome"] . "</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
            </div>
        </div>
            
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="referencia">Ponto de referência</label>
                    <input type="text" name="referencia" maxlength="100" id="referencia" class="form-control" value="<?= $endereco['referencia'] ?>">
                    <small>Adicione um ponto de referência para ajudar o entregador a lhe encontrar</small>
                </div>              
            </div>                   
        </div>
        
        <div class="row">            
            <div class="col-12">
                <input id="cliente-enderecos-btn-editar" type="submit" class="btn-escuro" value="Salvar">
            </div>
        </div>

    </form>

</section>

<?php } else { ?>
    
    <script> window.location.href = '<?= $loja['site'] ?>cliente-enderecos'; </script>

<?php } ?>