<!--CSS-->
<link rel="stylesheet" href="modulos/cliente/css/style.css">

<?php 

//PEGA IDENTIFICADOR DO CLIENTE
$identificador = filter_var($_SESSION['identificador']);

//BUSCA OS DADOS DO CLIENTE
$busca_enderecos_cliente = mysqli_query($conn, "
    SELECT ce.identificador AS endereco_identificador, ce.nome AS endereco_nome, ce.cep AS endereco_cep, ce.logradouro AS endereco_logradouro, ce.numero AS endereco_numero, ce.complemento AS endereco_complemento, ce.bairro AS endereco_bairro, cc.nome AS endereco_cidade, e.nome AS endereco_estado, ce.referencia AS endereco_referencia, ce.padrao AS endereco_padrao
    FROM cliente AS c
    INNER JOIN cliente_endereco AS ce ON c.id = ce.id_cliente
    INNER JOIN cidade AS cc ON cc.id = ce.cidade
    INNER JOIN estado AS e ON e.id = ce.estado
    WHERE c.identificador = '$identificador' AND ce.status = 1
");

//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){
    if($_SESSION['RETORNO']['STATUS'] == 'SUCESSO-CADASTRO'){
        echo "<script>mensagemAviso('sucesso', 'Endereço cadastrado com sucesso.', 3000);</script>";
    } else if($_SESSION['RETORNO']['ERRO'] == 'ERRO-CADASTRO'){
        echo "<script>mensagemAviso('erro', 'Erro ao cadastrar endereço. Se o problema persistir contate o administrador do sistema.', 3000);</script>";
    } else if($_SESSION['RETORNO']['STATUS'] == 'SUCESSO-EDICAO'){
        echo "<script>mensagemAviso('sucesso', 'Endereço editado com sucesso.', 3000);</script>";
    } else if($_SESSION['RETORNO']['ERRO'] == 'ERRO-EDICAO'){
        echo "<script>mensagemAviso('erro', 'Erro ao editar endereço. Se o problema persistir contate o administrador do sistema.', 3000);</script>";
    }
}

?>

<!--CLIENTE ENDEREÇOS-->
<section id="cliente-enderecos" class="cliente">

    <h1 class="d-none">Dados de endereços</h1>

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
                            <div class="cliente-enderecos-endereco-btn-editar"><a href="cliente-enderecos-edicao/<?= $endereco['endereco_identificador'] ?>" title="Editar endereço"><img src="imagens/acao-editar.png" alt="Editar"></a></div>     
                            <div class="cliente-enderecos-endereco-btn-excluir"><a href="javascript: excluirEndereco('<?= $endereco['endereco_identificador'] ?>','<?= $endereco['endereco_nome'] ?>');" title="Remover endereço"><img src="imagens/acao-excluir.png" alt="Excluir"></a></div>         
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
                Nenhum endereço encontrado! 
            </div>
        </div>

    <?php } ?>

    <div class="row">
        <div class="col-12">
            <a href="cliente-enderecos-cadastro" id="cliente-enderecos-btn-novo" class="btn-escuro">Novo endereço</a>
        </div>
    </div>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="modulos/cliente/js/scripts.js"></script>

<?php /* LIMPA A SESSION DE RETORNO */ unset($_SESSION['RETORNO']); ?>