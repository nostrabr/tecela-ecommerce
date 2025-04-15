<!--CSS-->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<link rel="stylesheet" href="modulos/configuracoes/css/style.css">

<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE TENTAR ACESSAR COMO USUÁRIO OU ADMINISTRADOR, DESLOGA DO SISTEMA
if($nivel_usuario == 'U' | $nivel_usuario == 'A'){
    echo "<script>location.href='logout.php';</script>";
} else if($nivel_usuario == 'M' | $nivel_usuario == 'S'){
    $busca_loja = mysqli_query($conn, 'SELECT * FROM loja WHERE id = 1'); 
    $loja       = mysqli_fetch_array($busca_loja);
}

?>

<!--SECTION CONFIGURAÇÕES-->
<section id="configuracoes-loja">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Configuração da Loja</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes.php';">VOLTAR</button>
            </div>
        </div>
        
        <!-- FORM DE EDIÇÃO -->
        <form action="modulos/configuracoes/php/edicao-loja.php" method="POST">

            <div class="row admin-subtitulo"><div class="col-12">Dados</div></div>

            <div class="row">
                <div class="col-12 col-md-8">
                    <div class="form-group">
                        <label for="nome">Nome <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control text-capitalize" name="nome" id="nome" maxlength="60" value="<?= $loja['nome'] ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="cpf-cnpj">CPF/CNPJ <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control" name="cpf-cnpj" id="cpf-cnpj" maxlength="18" minlength="14" value="<?= $loja['cpf_cnpj'] ?>" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="text" class="form-control" name="telefone" id="telefone" maxlength="14" value="<?= $loja['telefone'] ?>">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="whatsapp">Whatsapp</label>
                        <input type="text" class="form-control text-lowercase" name="whatsapp" id="whatsapp" maxlength="15" value="<?= $loja['whatsapp'] ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="site">Site <span class="campo-obrigatorio">*</span></label>
                        <input type="url" class="form-control text-lowercase" name="site" id="site" maxlength="50" value="<?= $loja['site'] ?>" <?php if($nivel_usuario != 'S'){ echo 'readonly'; } ?> required>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="email">E-mail de formulários <span class="campo-obrigatorio">*</span></label>
                        <input type="email" class="form-control text-lowercase" name="email" id="email" maxlength="50" value="<?= $loja['email'] ?>" required>
                        <small>E-mail que recebe os contatos através do site</small>
                    </div>
                </div>
            </div>

            <hr>            

            <div class="row admin-subtitulo"><div class="col-12">Endereço</div></div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="rua">Logradouro <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control text-capitalize" name="rua" id="rua" maxlength="100" value="<?= $loja['rua'] ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <div class="form-group">
                        <label for="numero">Número <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control text-capitalize" name="numero" id="numero" maxlength="10" value="<?= $loja['numero'] ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="bairro">Bairro <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control text-capitalize" name="bairro" id="bairro" maxlength="30" value="<?= $loja['bairro'] ?>" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="cep">Cep <span class="campo-obrigatorio">*</span></label>
                        <input type="text" class="form-control" name="cep" id="cep" maxlength="10" minlength="10" value="<?= $loja['cep'] ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="complemento">Complemento</label>
                        <input type="text" class="form-control text-capitalize" name="complemento" id="complemento" maxlength="30" value="<?= $loja['complemento'] ?>">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="estado">Estado <span class="campo-obrigatorio">*</span></label>
                        <select onchange="buscaCidades(this.value);" id="estado" name="estado" class="form-control" required>
                            <?php
                                $busca_ufs = mysqli_query($conn,"SELECT * FROM estado ORDER BY sigla ASC");
                                while ($uf = mysqli_fetch_array($busca_ufs)) {
                                    if($loja['estado'] == $uf["id"]){
                                        echo "<option value='" . $uf["id"] . "' selected>" . $uf["sigla"] . "</option>";
                                    } else {
                                        echo "<option value='" . $uf["id"] . "'>" . $uf["sigla"] . "</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <?php $busca_cidades = mysqli_query($conn,"SELECT id, nome FROM cidade WHERE id_estado = ".$loja['estado']." ORDER BY nome"); ?>
                        <label for="cidade">Cidade <span class="campo-obrigatorio">*</span></label>
                        <select id="cidade" name="cidade" class="form-control" required>
                            <?php
                                while ($cidade = mysqli_fetch_array($busca_cidades)) {
                                    if($loja['cidade'] == $cidade["id"]){
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
                        <label for="google-maps">Código para incorporar Google Maps <span class="campo-obrigatorio">*</span></label>
                        <textarea class="form-control" name="google-maps" id="google-maps" rows="5" required><?= $loja['google_maps'] ?></textarea>
                        <small>Código de incorporação do Google Maps da localização do estabelecimento. <a href="http://letmegooglethat.com/?q=Compartilhar+um+mapa+ou+rotas+com+outros" target="_blank">Onde eu encontro isso?</a>.</small>
                    </div>
                </div>
            </div>

            <div class="row">                            
                <div class="col-12">                        
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input text-uppercase" id="exibir-endereco" name="exibir-endereco" <?php if($loja['exibir_endereco'] == 1){ echo 'checked'; } ?> >
                        <label class="custom-control-label" for="exibir-endereco">Exibir a localização e endereço no site</label>
                    </div>
                </div>   
            </div>
            
            <hr>            

            <div class="row admin-subtitulo">
                <div class="col-12">Redes sociais</div>
                <div class="col-12"><small>Mantenha o campo em branco caso não queira exibir a rede social.</small></div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="facebook">Facebook</label>
                        <input type="url" class="form-control text-lowercase" name="facebook" id="facebook" maxlength="100" value="<?= $loja['facebook'] ?>">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="instagram">Instagram</label>
                        <input type="url" class="form-control text-lowercase" name="instagram" id="instagram" maxlength="100" value="<?= $loja['instagram'] ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="twiter">Twitter</label>
                        <input type="url" class="form-control text-lowercase" name="twiter" id="twiter" maxlength="100" value="<?= $loja['twiter'] ?>">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="youtube">Youtube</label>
                        <input type="url" class="form-control text-lowercase" name="youtube" id="youtube" maxlength="100" value="<?= $loja['youtube'] ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="pinterest">Pinterest</label>
                        <input type="url" class="form-control text-lowercase" name="pinterest" id="pinterest" maxlength="100" value="<?= $loja['pinterest'] ?>">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="tiktok">Tiktok</label>
                        <input type="url" class="form-control text-lowercase" name="tiktok" id="tiktok" maxlength="100" value="<?= $loja['tiktok'] ?>">
                    </div>
                </div>
            </div>
            
            <hr>            

            <div class="row admin-subtitulo"><div class="col-12">Sobre</div></div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="summernote">Texto da página sobre</label>
                        <textarea class="summernote" name="summernote"><?= $loja['pagina_sobre'] ?></textarea>
                        <small>Esta é a descrição da página sobre da loja. Aqui pode-se incluir imagens, videos e tudo que for preciso para demonstrar tudo o que sua loja é capaz.</small>
                    </div>
                </div>
            </div>
            
            <hr>            

            <div class="row admin-subtitulo">
                <div class="col-12">reCAPTCHA</div>
                <div class="col-12"><small>Para configurar seu reCAPTCHA para o formulário de contato acesse o <a href="https://www.google.com/recaptcha/admin" target="_blank">site do Google reCAPTCHA</a> para gerar as chaves de integração e configura-las nos campos abaixo.</small></div>
                <div class="col-12"><small>O formato aceito pelo site é o <b>reCAPTCHA v2 - Caixa de seleção "Não sou um robô"</b></small></div>
                <div class="col-12"><small>Para desinstalar, basta deixar os campos de configuração em branco.</small></div>
                <div class="col-12"><small><b>Observação importante: Caso as chaves estejam configuradas erradas, o formulário de contato e cadastro do cliente não irão funcionar.</b></small></div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="google-recaptcha-chave-site">Chave do site</label>
                        <input type="text" class="form-control" name="google-recaptcha-chave-site" id="google-recaptcha-chave-site" maxlength="50" value="<?= $loja['recaptcha'] ?>">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="google-recaptcha-chave-secreta">Chave secreta</label>
                        <input type="password" class="form-control" name="google-recaptcha-chave-secreta" id="google-recaptcha-chave-secreta" maxlength="50" value="<?= $loja['recaptcha_secret'] ?>">
                    </div>
                </div>
            </div>

            <hr>            
                
            <div class="row admin-subtitulo"><div class="col-12">Outros</div></div>

            <div class="row mb-2">                            
                <div class="col-12">                        
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input text-uppercase" id="site-manutencao" name="site-manutencao" <?php if($loja['site_manutencao'] == 1){ echo 'checked'; } ?> >
                        <label class="custom-control-label" for="site-manutencao">Site em manutenção</label>
                        <small>Com esta opção marcada, o site fica em modo de manutenção.</small>
                    </div>
                </div>   
            </div>

            <div class="row mb-2">                            
                <div class="col-12">                        
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input text-uppercase" id="mensagem-cookies" name="mensagem-cookies" <?php if($loja['opcao_mensagem_cookies'] == 1){ echo 'checked'; } ?> >
                        <label class="custom-control-label" for="mensagem-cookies">Aviso sobre cookies</label>
                        <small>Com esta opção marcada, o site mostra a mensagem de uso de cookies pelo site de acordo com os Termos de Uso.</small>
                    </div>
                </div>   
            </div>

            <div class="row">                            
                <div class="col-12">                        
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input text-uppercase" id="opcao-validar-email-cadastro" name="opcao-validar-email-cadastro" <?php if($loja['opcao_validar_email_cadastro'] == 1){ echo 'checked'; } ?> >
                        <label class="custom-control-label" for="opcao-validar-email-cadastro">Enviar e-mail de verificação de cadastro</label>
                        <small>Com esta opção marcada, o sistema envia um e-mail de verificação com um código para o cliente validar o e-mail recém cadastrado.</small>
                        <small>Caso não esteja marcado, o cliente pode proseguir direto para o carrinho ou área do cliente sem perder tempo.</small>
                    </div>
                </div>   
            </div>

            <?php if($nivel_usuario == 'S'){ ?>
            
                <hr>            

                <div class="row admin-subtitulo"><div class="col-12">Outros Super Admin</div></div>

                <div class="row">                            
                    <div class="col-12">                        
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input text-uppercase" id="modo-whatsapp" name="modo-whatsapp" <?php if($modo_whatsapp){ echo 'checked'; } ?> >
                            <label class="custom-control-label" for="modo-whatsapp">Modo Whatsapp</label>
                            <small>O modo Whatsapp desabilita o carrinho de pagamento e oculta os valores dos produtos liberando somente para consulta ou orçamento.</small>
                            <small>Para este modulo funcionar completamente é necessário um sistema de frete instalado.</small>
                        </div>
                    </div>   
                </div>

                <div id="row-modo-simples" class="row <?php if(!$modo_whatsapp){ echo 'modo-whats-desativado'; } ?> mt-3">                            
                    <div class="col-12">                        
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input text-uppercase" id="modo-whatsapp-simples" name="modo-whatsapp-simples" <?php if($modo_whatsapp_simples){ echo 'checked'; } ?> >
                            <label class="custom-control-label" for="modo-whatsapp-simples">Modo simples</label>
                            <small>Com o modo simples ativado, a loja não pede o cadastro do cliente, não cadastra o orçamento e envia o orçamento direto para o whatsapp.</small>
                        </div>
                    </div>   
                </div>

                <div id="row-modo-whatsapp-com-preco" class="row <?php if(!$modo_whatsapp_simples){ echo 'modo-whats-simples-desativado'; } ?> mt-3">                            
                    <div class="col-12">                        
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input text-uppercase" id="modo-whatsapp-preco" name="modo-whatsapp-preco" <?php if($loja['modo_whatsapp_preco'] == 1){ echo 'checked'; } ?> >
                            <label class="custom-control-label" for="modo-whatsapp-preco">Exibir preço</label>
                            <small>Com esta opção ativada, a loja vai mostrar o preço dos produtos mesmo em modo Whatsapp, e o orçamento vira pedido.</small>
                        </div>
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

<!--SCRIPTS-->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script type="text/javascript" src="modulos/configuracoes/js/scripts.js"></script>