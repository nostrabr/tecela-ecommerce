<?php 

setlocale(LC_MONETARY, 'pt_BR');

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE TENTAR ACESSAR COMO USUÁRIO OU ADMINISTRADOR, DESLOGA DO SISTEMA
if($nivel_usuario == 'U' | $nivel_usuario == 'A'){
    echo "<script>location.href='logout.php';</script>";
} else if($nivel_usuario == 'M' | $nivel_usuario == 'S'){
    $busca_frete = mysqli_query($conn, 'SELECT * FROM frete WHERE id = 1'); 
    $frete       = mysqli_fetch_array($busca_frete);
}

//FUNÇÃO QUE RETIRA TODOS OS ACENTOS DE UMA PALAVRA
function tirarAcentos($string){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
}

//TRATA OS SERVIÇOS DO MELHOR ENVIO
if($frete['melhor_envio_servicos'] != ''){
    $melhor_envio_servicos = explode(',',$frete['melhor_envio_servicos']);
} else {
    $melhor_envio_servicos = array();
    $servicos = mysqli_query($conn, "SELECT melhor_envio_id_servico AS id_servico FROM frete_transportadora_servico");
    while($servico = mysqli_fetch_array($servicos)){
        array_push($melhor_envio_servicos, $servico['id_servico']);
    }
}

//TRATA OS ESTADOS DO FRETE GRÁTIS
if($frete['frete_gratis_estados'] != ''){
    $frete_gratis_estados = explode(',',$frete['frete_gratis_estados']);
} else {
    $frete_gratis_estados = array();
}

//TRATA AS CIDADES DO FRETE MOTOBOY
if($frete['frete_motoboy_cidades'] != ''){
    $frete_motoboy_cidades = explode(',',$frete['frete_motoboy_cidades']);
} else {
    $frete_motoboy_cidades = array();
}

//TRATA AS CIDADES DO FRETE RETIRADA
if($frete['frete_retirar_cidades'] != ''){
    $frete_retirar_cidades = explode(',',$frete['frete_retirar_cidades']);
} else {
    $frete_retirar_cidades = array();
}

?>

<!--CSS-->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="modulos/configuracoes/css/style.css">

<!--SECTION CONFIGURAÇÕES-->
<section id="configuracoes-frete">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Configuração do frete</div>
            </div>
            <div class="col-4 text-right">  
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes.php';">VOLTAR</button>
            </div>
        </div>

        <?php if($frete['melhor_envio'] == 0){ ?>

            <!-- FORM DE CONFIGURAÇÃO DO MELHOR ENVIO -->
            <form action="modulos/configuracoes/php/configuracao-frete-melhor-envio.php" method="POST">

                <div class="row admin-subtitulo">
                    <div class="col-12">
                        <div>ATIVAR O MELHOR ENVIO</div>                        
                        <small>Todas as informações abaixo precisam ser cadastradas iguais as da conta criada no site do Melhor Envio. O 'Client ID' e o 'Secret' são fornecidos após a criação de um aplicativo.</small>                        
                    </div>                    
                </div>
                
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="producao" name="ambiente" value="P" class="custom-control-input" <?php if($frete['melhor_envio_ambiente'] == 'P'){ echo 'checked'; } ?>>
                            <label class="custom-control-label" for="producao">Produção</label>
                        </div>
                            <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="sandbox" name="ambiente" value="S" class="custom-control-input" <?php if($frete['melhor_envio_ambiente']  == 'S'){ echo 'checked'; } ?>>
                            <label class="custom-control-label" for="sandbox">Sandbox</label>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="url-redirecionamento">URL de redirecionamento <span class="campo-obrigatorio">*</span></label>
                            <input type="url" class="form-control text-lowercase" name="url-redirecionamento" id="url-redirecionamento" value="<?= $loja['site'].'admin/modulos/frete/melhor-envio/gera-token.php' ?>" readonly>
                            <small>Esta é a URL de redirecionamento que deve ser cadastrada no Melhor Envio.</small>
                        </div>
                    </div>
                </div>  
                
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="nome">Nome <span class="campo-obrigatorio">*</span></label>
                            <input type="password" class="form-control" name="nome" id="nome" value="<?= $frete['melhor_envio_nome_aplicacao'] ?>" required>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="email">E-mail <span class="campo-obrigatorio">*</span></label>
                            <input type="password" class="form-control text-lowercase" name="email" id="email" value="<?= $frete['melhor_envio_email_aplicacao'] ?>" required>
                        </div>
                    </div>
                </div>  
                
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="client-id">Client ID <span class="campo-obrigatorio">*</span></label>
                            <input type="password" class="form-control" name="client-id" id="client-id" value="<?= $frete['melhor_envio_client_id'] ?>" required>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="client-secret">Secret <span class="campo-obrigatorio">*</span></label>
                            <input type="password" class="form-control" name="client-secret" id="client-secret" value="<?= $frete['melhor_envio_client_secret'] ?>" required>
                        </div>
                    </div>
                </div> 

                <div class="row mt-3">
                    <div class="col-12 text-center text-md-right">
                        <div class="form-group">
                            <button type="submit" class="btn btn-dark btn-bottom">SALVAR</button>
                        </div>
                    </div>
                </div>
            
            </form>

        <?php } else if($frete['melhor_envio'] == 1){ ?>

            <!-- FORM DE EDIÇÃO -->
            <form action="modulos/configuracoes/php/edicao-frete.php" method="POST">

                <div class="row">
                    <div class="col-12 col-md-3">
                        <div class="form-group mb-0">
                            <label for="cep">Cep de origem <span class="campo-obrigatorio">*</span></label>
                            <input type="text" class="form-control text-capitalize" name="cep" id="cep" maxlength="10" minlength="10" value="<?= $frete['cep'] ?>" required>
                            <small>Para cálculo</small>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group mb-0">
                            <label for="prazo-minimo">Prazo mínimo em dias <span class="campo-obrigatorio">*</span></label>
                            <input type="number" class="form-control" name="prazo-minimo" id="prazo-minimo" min="0" value="<?= $frete['prazo_minimo'] ?>" required>
                            <small>A quantidade de dias acima será somada ao prazo do frete em todas modalidades</small>
                        </div>
                    </div>
                </div>         

                <hr>       

                <div class="row admin-subtitulo"><div class="col-12">TRANSPORTADORAS E SERVIÇOS <span class="campo-obrigatorio">*</span></div></div>

                <div id="frete-servicos" class="row">
                    <?php $transportadoras = mysqli_query($conn, "SELECT ft.melhor_envio_nome AS nome_transportadora, fts.melhor_envio_nome_servico AS nome_servico, fts.melhor_envio_id_servico AS id_servico FROM frete_transportadora_servico as fts LEFT JOIN frete_transportadora as ft ON ft.melhor_envio_id = fts.melhor_envio_id_transportadora ORDER BY ft.melhor_envio_nome, fts.melhor_envio_nome_servico"); ?>
                    <?php while($transportadora = mysqli_fetch_array($transportadoras)){ ?>
                        <div class="form-group mb-1 col-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="servicos[]" value="<?= $transportadora['id_servico'] ?>" id="servico-<?= $transportadora['id_servico'] ?>" <?php if(in_array($transportadora['id_servico'], $melhor_envio_servicos)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="servico-<?= $transportadora['id_servico'] ?>"><?= mb_strtoupper($transportadora['nome_transportadora']) ?> - <?= $transportadora['nome_servico'] ?></label>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <hr>            

                <div class="row admin-subtitulo"><div class="col-12">PROPRIEDADES</div></div>

                <div class="row">
                    <div class="form-group mb-1 col-12">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="aviso-recebimento" id="aviso-recebimento" <?php if($frete['melhor_envio_aviso_recebimento'] == 1){ echo 'checked'; } ?>>
                            <label class="custom-control-label" for="aviso-recebimento">AVISO DE RECEBIMENTO</label>
                            <small>Caso ativado, deve resultar em cotações mais caras com Correios e JadLog apenas, visto que são serviços que apenas estas transportadoras oferecem.</small>
                        </div>
                    </div>
                    <div class="form-group mb-1 col-12">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="maos-proprias" id="maos-proprias" <?php if($frete['melhor_envio_maos_proprias'] == 1){ echo 'checked'; } ?>>
                            <label class="custom-control-label" for="maos-proprias">MÃOS PRÓPRIAS</label>
                            <small>Caso ativado, deve resultar em cotações mais caras com Correios apenas, visto que são serviços que apenas esta transportadora oferece.</small>
                        </div>
                    </div>
                    <div class="form-group mb-1 col-12">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="coleta" id="coleta" <?php if($frete['melhor_envio_coleta'] == 1){ echo 'checked'; } ?>>
                            <label class="custom-control-label" for="coleta">COLETA</label>
                        </div>
                    </div>
                </div>
                
                <hr>            

                <div class="row admin-subtitulo"><div class="col-12">RETIRAR NA LOJA</div></div>

                <div class="row">
                    <div class="form-group col-12 mb-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="frete-retirar" id="frete-retirar" <?php if($frete['frete_retirar'] == 1){ echo "checked"; } ?>>
                            <label class="custom-control-label" for="frete-retirar">ATIVAR</label>
                        </div>
                    </div>
                </div>

                <div id="frete-retirar-parametros" class="<?php if($frete['frete_retirar'] == 0){ echo "d-none"; } ?>">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="frete-retirar-cidades">Cidades</label>
                                <select id="frete-retirar-cidades" name="frete-retirar-cidades[]" data-style="btn-default" data-dropup-auto="false" class="selectpicker form-control" data-live-search="true" title="Selecione..." multiple data-selected-text-format="count > 8">
                                    <?php $cidades = mysqli_query($conn, "SELECT id, nome FROM cidade ORDER BY nome ASC"); ?>
                                    <?php while($cidade = mysqli_fetch_array($cidades)){ ?>
                                        <option value="<?= $cidade['id'] ?>" data-tokens="<?= mb_strtolower(tirarAcentos($cidade['nome'])) ?>" <?php if(in_array($cidade['id'],$frete_retirar_cidades)){ echo "selected"; } ?>><?= $cidade['nome'] ?></option>
                                    <?php } ?>
                                </select>
                                <small>Selecione as cidades autorizadas. Caso não selecione, o sistema liberará para todas.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>            

                <div class="row admin-subtitulo"><div class="col-12">FRETE GRÁTIS</div></div>

                <div class="row">
                    <div class="form-group col-12 mb-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="frete-gratis" id="frete-gratis" <?php if($frete['frete_gratis'] == 1){ echo "checked"; } ?>>
                            <label class="custom-control-label" for="frete-gratis">ATIVAR</label>
                        </div>
                    </div>
                </div>
                <div id="frete-gratis-parametros" class="<?php if($frete['frete_gratis'] == 0){ echo "d-none"; } ?>">
                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <label for="frete-gratis-minimo">Valor mínimo da compra</label>
                                <input type="text" name="frete-gratis-minimo" id="frete-gratis-minimo" class="form-control valor-com-prefixo" value="<?= money_format('%.2n', $frete['frete_gratis_valor_minimo']) ?>">
                                <small>Campo em branco = R$ 0,00</small>
                            </div>
                        </div>
                    </div>
                    <label for="frete-gratis-estados">Para os estados</label>
                    <div class="row">
                        <div class="col-12">
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-pr" value="18" <?php if(in_array('18',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-pr">PR</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-sc" value="24" <?php if(in_array('24',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-sc">SC</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-rs" value="23" <?php if(in_array('23',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-rs">RS</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-es" value="8" <?php if(in_array('8',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-es">ES</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-mg" value="11" <?php if(in_array('11',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-mg">MG</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-rj" value="19" <?php if(in_array('19',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-rj">RJ</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-sp" value="26" <?php if(in_array('26',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-sp">SP</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-df" value="7" <?php if(in_array('7',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-df">DF</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-go" value="9" <?php if(in_array('9',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-go">GO</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-ms" value="12" <?php if(in_array('12',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-ms">MS</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-mt" value="13" <?php if(in_array('13',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-mt">MT</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-al" value="2" <?php if(in_array('2',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-al">AL</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-ba" value="5" <?php if(in_array('5',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-ba">BA</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-ce" value="6" <?php if(in_array('6',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-ce">CE</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-ma" value="10" <?php if(in_array('10',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-ma">MA</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-pb" value="15" <?php if(in_array('15',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-pb">PB</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-pe" value="16" <?php if(in_array('16',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-pe">PE</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-pi" value="17" <?php if(in_array('17',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-pi">PI</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-rn" value="20" <?php if(in_array('20',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-rn">RN</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-se" value="25" <?php if(in_array('25',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-se">SE</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-ac" value="1" <?php if(in_array('1',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-ac">AC</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-am" value="3" <?php if(in_array('3',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-am">AM</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-ap" value="4" <?php if(in_array('4',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-ap">AP</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-pa" value="14" <?php if(in_array('14',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-pa">PA</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-ro" value="21" <?php if(in_array('21',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-ro">RO</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-rr" value="22" <?php if(in_array('22',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-rr">RR</label>
                            </div>
                            <div class="custom-control custom-checkbox d-inline-flex frete-gratis-estado">
                                <input type="checkbox" class="custom-control-input" name="frete-gratis-estados[]" id="frete-gratis-to" value="27" <?php if(in_array('27',$frete_gratis_estados)){ echo 'checked'; } ?>>
                                <label class="custom-control-label" for="frete-gratis-to">TO</label>
                            </div>
                        </div>
                    </div>
                    <small>Caso não haja estado selecionado, o frete grátis ficará desabilitado.</small>
                </div>
                
                <hr>            

                <div class="row admin-subtitulo"><div class="col-12">MOTOBOY</div></div>

                <div class="row">
                    <div class="form-group col-12 mb-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="frete-motoboy" id="frete-motoboy" <?php if($frete['frete_motoboy'] == 1){ echo "checked"; } ?>>
                            <label class="custom-control-label" for="frete-motoboy">ATIVAR</label>
                        </div>
                    </div>
                </div>
                <div id="frete-motoboy-parametros" class="<?php if($frete['frete_motoboy'] == 0){ echo "d-none"; } ?>">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="frete-motoboy-minimo">Valor mínimo da compra</label>
                                <input type="text" name="frete-motoboy-minimo" id="frete-motoboy-minimo" class="form-control valor-com-prefixo" value="<?= money_format('%.2n', $frete['frete_motoboy_valor_minimo']) ?>">
                                <small>Campo em branco = R$ 0,00</small>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="frete-motoboy-entrega">Valor da entrega</label>
                                <input type="text" name="frete-motoboy-entrega" id="frete-motoboy-entrega" class="form-control valor-com-prefixo" value="<?= money_format('%.2n', $frete['frete_motoboy_valor_entrega']) ?>">
                                <small>Campo em branco = R$ 0,00</small>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="frete-motoboy-prazo">Prazo</label>
                                <input type="number" min="0" name="frete-motoboy-prazo" id="frete-motoboy-prazo" class="form-control" value="<?= $frete['frete_motoboy_prazo'] ?>">
                                <small>0 = No mesmo dia</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="frete-motoboy-cidades">Cidades</label>
                                <select id="frete-motoboy-cidades" name="frete-motoboy-cidades[]" data-style="btn-default" data-dropup-auto="false" class="selectpicker form-control" data-live-search="true" title="Selecione..." multiple data-selected-text-format="count > 8"  <?php if($frete['frete_motoboy'] == 1){ echo "required"; } ?>>
                                    <?php $cidades = mysqli_query($conn, "SELECT id, nome FROM cidade ORDER BY nome ASC"); ?>
                                    <?php while($cidade = mysqli_fetch_array($cidades)){ ?>
                                        <option value="<?= $cidade['id'] ?>" data-tokens="<?= mb_strtolower(tirarAcentos($cidade['nome'])) ?>" <?php if(in_array($cidade['id'],$frete_motoboy_cidades)){ echo "selected"; } ?>><?= $cidade['nome'] ?></option>
                                    <?php } ?>
                                </select>
                                <small>Selecione as cidades autorizadas</small>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>            

                <div class="row admin-subtitulo">
                    <div class="col-12">
                        <div>TRANSPORTADORA TW</div>
                        <small>Para ativar a cotação de frete TW, é necessário entrar em contato com a transportadora e solicitar as credenciais de acesso à API. <a href="https://glpi.twtransportes.com.br/marketplace/formcreator/front/formdisplay.php?id=5">LINK</a></small>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-12 mb-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="tw" id="tw" <?php if($frete['tw'] == 1){ echo "checked"; } ?>>
                            <label class="custom-control-label" for="tw">ATIVAR</label>
                        </div>
                    </div>
                </div>
                <div id="frete-tw-parametros" class="<?php if($frete['tw'] == 0){ echo "d-none"; } ?>">
                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <label for="frete-tw-dominio">Domínio <span class="campo-obrigatorio">*</span></label>
                                <input type="text" name="frete-tw-dominio" id="frete-tw-dominio" class="form-control" value="<?= $frete['tw_dominio'] ?>">
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <label for="frete-tw-login">Login <span class="campo-obrigatorio">*</span></label>
                                <input type="text" name="frete-tw-login" id="frete-tw-login" class="form-control" value="<?= $frete['tw_login'] ?>">
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <label for="frete-tw-senha">Senha <span class="campo-obrigatorio">*</span></label>
                                <input type="password" name="frete-tw-senha" id="frete-tw-senha" class="form-control" value="<?= $frete['tw_senha'] ?>">
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <label for="frete-tw-cnpj-pagador">CNPJ do pagador <span class="campo-obrigatorio">*</span></label>
                                <input type="text" name="frete-tw-cnpj-pagador" id="frete-tw-cnpj-pagador" class="form-control cnpj" value="<?= $frete['tw_cnpj_pagador'] ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <?php if($nivel_usuario == 'S'){ ?>
                    
                    <hr>            

                    <div class="row admin-subtitulo">
                        <div class="col-12">
                            <div>MÓDULO DE ENVIOS</div>
                            <small>Com o módulo de envios ativado a loja pode gerar etiquetas através do Melhor Envio.</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-12 mb-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="modo-envios" id="modo-envios" <?php if($modo_envios){ echo "checked"; } ?>>
                                <label class="custom-control-label" for="modo-envios">ATIVAR</label>
                            </div>
                        </div>
                    </div>
                    
                    <hr>            

                    <div class="row admin-subtitulo">
                        <div class="col-12">
                            <div>RESETAR MELHOR ENVIO</div>
                            <small>Ao aplicar o reset as configurações serão todas perdidas e será necessário configurar novamente.</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-12">
                            <button onclick="javascript: resetarMelhorEnvio();" class="btn btn-dark">RESETAR</button>
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

        <?php } ?>

    </div>

</section>

<!--SCRIPTS-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="modulos/configuracoes/js/scripts.js"></script>