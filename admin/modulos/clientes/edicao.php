<?php 

$identificador_cliente = filter_input(INPUT_GET,"id",FILTER_SANITIZE_STRING);
$nivel_usuario         = filter_var($_SESSION['nivel']);

//SE FOR USUÁRIO, DESLOGA
if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
}

//BUSCA CLIENTE
$busca_cliente = mysqli_query($conn, 'SELECT * FROM cliente WHERE identificador = "'.$identificador_cliente.'"'); 

//VERIFICA SE ENCONTROU O CLIENTE
if(mysqli_num_rows($busca_cliente) == 0){
    echo "<script>location.href='clientes.php';</script>";
} else {
    $cliente = mysqli_fetch_array($busca_cliente);
}

//VALIDA O TIPO DO CLIENTE (FISICA/JURIDICA)
if(strlen($cliente['cpf']) == 18){
    $label_nome = 'Razão Social';
    $label_sobrenome = 'Fantasia';
    $mostra_nascimento = 'd-none';
} else {
    $label_nome = 'Nome';
    $label_sobrenome = 'Sobrenome';
    $mostra_nascimento = '';
}

//BUSCA OS DADOS DO CLIENTE
$busca_enderecos_cliente = mysqli_query($conn, "
    SELECT ce.identificador AS endereco_identificador, ce.nome AS endereco_nome, ce.cep AS endereco_cep, ce.logradouro AS endereco_logradouro, ce.numero AS endereco_numero, ce.complemento AS endereco_complemento, ce.bairro AS endereco_bairro, cc.nome AS endereco_cidade, e.nome AS endereco_estado, ce.referencia AS endereco_referencia, ce.padrao AS endereco_padrao
    FROM cliente AS c
    INNER JOIN cliente_endereco AS ce ON c.id = ce.id_cliente
    INNER JOIN cidade AS cc ON cc.id = ce.cidade
    INNER JOIN estado AS e ON e.id = ce.estado
    WHERE c.identificador = '$identificador_cliente' AND ce.status = 1
");

//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){
    if($_SESSION['RETORNO']['ERRO'] == 'ERRO-EMAIL-REPETIDO'){
        echo "<script>mensagemAviso('erro', 'Já existe um cliente cadastrado com esse e-mail.', 3000);</script>";
    }
}

?>

<!-- CSS -->
<link rel="stylesheet" href="modulos/clientes/css/style.css">

<!--SECTION CLIENTES-->
<section id="cliente-edita">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Clientes - Edição</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'clientes.php';">VOLTAR</button>
            </div>
        </div>

        <!-- FORM DE EDIÇÃo -->
        <form action="modulos/clientes/php/edicao.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="identificador" value="<?= $identificador_cliente ?>">    

            <div class="row admin-subtitulo"><div class="col-12">Dados pessoais</div></div>

            <div class="row">
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="nome"><span id="label-cliente-nome"><?= $label_nome ?></span> <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control text-capitalize" name="nome" id="nome" maxlength="50" value="<?= $cliente['nome'] ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="sobrenome"><span id="label-cliente-sobrenome"><?= $label_sobrenome ?></span> <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control text-capitalize" name="sobrenome" id="sobrenome" maxlength="50" value="<?= $cliente['sobrenome'] ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="nascimento">Data Nascimento</label>
                        <input type="text" class="form-control" name="nascimento" id="nascimento" minlength="10" maxlength="10" value="<?php if($cliente['nascimento'] != '' & $cliente['nascimento'] != '0000-00-00'){ echo date('d/m/Y', strtotime($cliente['nascimento'])); } ?>">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="cpf">CPF/CNPJ <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control" name="cpf" id="cpf-cnpj" maxlength="14" value="<?= $cliente['cpf'] ?>" onblur="javascript: validaCpfCnpj(this.value, this.id); trocaLabelNomes(this.value);" required>
                    </div>
                </div>
            </div>
                        
            <hr>            

            <div class="row admin-subtitulo"><div class="col-12">Dados de acesso</div></div>

            <div class="row">
                <div class="col-12 col-md-8">
                    <div class="form-group">
                        <label for="email">E-mail <span class="campo-obrigatorio">*</span></label>
                        <input type="email" class="form-control text-lowercase" name="email" id="email" maxlength="50" value="<?= $cliente['email'] ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <input type="password" class="form-control" name="senha" id="senha" maxlength="32">
                        <small>Preencha para alterar</small>
                    </div>
                </div>
            </div>
                        
            <hr>            

            <div class="row admin-subtitulo"><div class="col-12">Contato</div></div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="text" class="form-control" name="telefone" id="telefone" minlength="14" maxlength="14" value="<?= $cliente['telefone'] ?>">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="celular">Celular <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control" name="celular" id="celular" minlength="15" maxlength="15" value="<?= $cliente['celular'] ?>" required>
                    </div>
                </div>
            </div>
            
            <hr>            
            
            <div class="row admin-subtitulo"><div class="col-12">Endereços</div></div>

            <?php if(mysqli_num_rows($busca_enderecos_cliente) > 0){ ?>

                <!--LISTA OS ENDEREÇOS-->
                <?php while($endereco = mysqli_fetch_array($busca_enderecos_cliente)){ ?>
                    <div class="row">        
                        <div class="col-12">            
                            <div id="cliente-endereco-<?= $endereco['endereco_identificador'] ?>" class="cliente-enderecos-endereco <?php if($endereco['endereco_padrao'] == 1){ echo 'cliente-enderecos-endereco-ativo'; } ?>" title="<?php if($endereco['endereco_padrao'] == 0){ echo 'Selecionar como endereço padrão'; } ?>" onclick="javascript: selecionarEnderecoPadrao('<?= $endereco['endereco_identificador'] ?>');">
                                <div class="row">           
                                    <?php if($endereco['endereco_padrao'] == 1){ ?>
                                        <div class="cliente-enderecos-endereco-label-padrao">
                                            <span class="d-block d-sm-none">P</span>
                                            <span class="d-none d-sm-block">PADRÃO</span>
                                        </div>   
                                    <?php } ?>
                                    <div class="col-12 cliente-enderecos-endereco-nome text-uppercase"><?= $endereco['endereco_nome'] ?></div>
                                    <div class="col-12 cliente-enderecos-endereco-cep"><?= 'CEP: '.$endereco['endereco_cep'] ?></div>
                                    <div class="col-12 cliente-enderecos-endereco-dados text-capitalize"><?= $endereco['endereco_logradouro'].', '.$endereco['endereco_numero'] ?><?php if($endereco['endereco_complemento'] != ''){ echo ' - '.$endereco['endereco_complemento']; } ?><?= ' - '.$endereco['endereco_bairro'] ?></div>
                                    <div class="col-12 cliente-enderecos-endereco-cid-est text-capitalize"><?= $endereco['endereco_cidade'].' - '.$endereco['endereco_estado'] ?></div>
                                    <?php if($endereco['endereco_referencia'] != ''){ ?>
                                        <div class="col-12 cliente-enderecos-endereco-referencia"><?= $endereco['endereco_referencia'] ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <?php } else { ?>

                <div class="row">        
                    <div id="cliente-enderecos-aviso-sem-enderecos" class="col-12">  
                        Nenhum endereço cadastrado! 
                    </div>
                </div>

            <?php } ?>
            
            <div class="row mt-3">
                <div class="col-12 text-center text-md-right">
                    <div class="form-group">
                        <button type="submit" class="btn btn-dark btn-bottom">SALVAR</button>
                    </div>
                </div>
            </div>

        </form>

    </div>

</section>

<?php /* LIMPA A SESSION DE RETORNO */ unset($_SESSION['RETORNO']); ?>

<!--SCRIPTS-->
<script type="text/javascript" src="modulos/clientes/js/scripts.js"></script>
