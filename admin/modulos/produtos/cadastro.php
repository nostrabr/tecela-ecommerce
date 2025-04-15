<!--CSS-->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/dropzone.min.css" integrity="sha512-3g+prZHHfmnvE1HBLwUnVuunaPOob7dpksI7/v6UnF/rnKGwHf/GdEq9K7iEN7qTtW+S0iivTcGpeTBqqB04wA==" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.css" integrity="sha512-A81ejcgve91dAWmCGseS60zjrAdohm7PTcAjjiDWtw3Tcj91PNMa1gJ/ImrhG+DbT5V+JQ5r26KT5+kgdVTb5w==" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.css" integrity="sha512-vEia6TQGr3FqC6h55/NdU3QSM5XR6HSl5fW71QTKrgeER98LIMGwymBVM867C1XHIkYD9nMTfWK2A0xcodKHNA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="modulos/produtos/css/style.css">

<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario = filter_var($_SESSION['nivel']);

//LIMPA O DIRETÓRIO DE IMAGENS TEMPORÁRIAS AO INICIAR A PÁGINA
$pasta = "modulos/produtos/arquivos/temp/".$_SESSION['identificador'].'/';
if(is_dir($pasta)){
    $diretorio = dir($pasta);
    while($arquivo = $diretorio->read()){
        if(($arquivo != '.') && ($arquivo != '..')){
            unlink($pasta.$arquivo);
        }
    }
}

$busca_ultimo_id = mysqli_query($conn, "SELECT id FROM produto ORDER BY id DESC LIMIT 1");
if(mysqli_num_rows($busca_ultimo_id) > 0){
    $ultimo_id = mysqli_fetch_array($busca_ultimo_id);
    $ultimo_id = $ultimo_id['id']+1;
} else {
    $ultimo_id = 1;
}

?>

<!--SECTION PRODUTO-->
<section id="produtos-cadastra">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-9">    
                <div id="admin-titulo-pagina">Produtos - Cadastro</div>
            </div>
            <div class="col-3 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'produtos.php?acao=e';">VOLTAR</button>
            </div>
        </div>

        <!-- ABAS -->
        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tab-produto" data-toggle="tab" href="#conteudo-tab-produto" role="tab" aria-controls="conteudo-tab-produto" aria-selected="true">Dados</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-caracteristicas" data-toggle="tab" href="#conteudo-tab-caracteristicas" role="tab" aria-controls="conteudo-tab-caracteristicas" aria-selected="false">Caracteristicas</a>
            </li>
        </ul>

        <!-- FORM DE CADASTRO -->
        <form id="form-cadastro-produto" action="modulos/produtos/php/cadastro.php" method="POST" enctype="multipart/form-data">
        
            <div class="tab-content">

                <div class="tab-pane active" id="conteudo-tab-produto" role="tabpanel" aria-labelledby="tab-produto">

                    <div class="row admin-subtitulo"><div class="col-12">Dados</div></div>

                    <div class="row">
                        <div class="col-12 col-md-10">
                            <div class="form-group">
                                <label for="nome">Nome <span class="campo-obrigatorio">*</span></label>
                                <input type="text" class="form-control text-capitalize" name="nome" id="nome" maxlength="100" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label for="relevancia">Relevância <span class="campo-obrigatorio">*</span></label>
                                <input type="number" class="form-control" name="relevancia" id="relevancia" value="0" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="tags">Tags</label>
                                <select class="form-control text-capitalize selectpicker" name="tags[]" id="tags" data-style="btn-default" data-dropup-auto="false" data-live-search="true" title="" multiple data-selected-text-format="count > 8">
                                    <?php $tags = mysqli_query($conn, "SELECT id, nome FROM tag"); ?>
                                    <?php while($tag = mysqli_fetch_array($tags)){ ?>
                                        <option class="text-capitalize" value="<?= $tag['id'] ?>"><?= $tag['nome'] ?></option>
                                    <?php } ?>
                                </select>
                                <?php if(mysqli_num_rows($tags) == 0){ ?>
                                    <small>Você não tem tags cadastradas ainda. Acesse pelo menu lateral a aba 'Categorias e Tags' para começar utilizar esta funcionalidade.</small>
                                <?php } ?>
                            </div>
                        </div>
                    </div>      
                    <div class="row">                                        
                        <div class="col-12">
                            <div class="form-group">
                                <label for="categoria">Categoria <span class="campo-obrigatorio">*</span></label>
                                <div class="text-capitalize" id="arvore-categorias"></div>
                                <input type="hidden" id="categoria" name="categoria" nome="" required>
                            </div>   
                        </div>
                    </div>                    
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="marca">Marca <span class="campo-obrigatorio">*</span></label>
                                <select class="form-control text-capitalize" name="marca" id="marca" required>
                                    <option value="" disabled selected></option>
                                    <?php $marcas = mysqli_query($conn, "SELECT id, nome FROM marca ORDER BY nome ASC"); ?>
                                    <?php while($marca = mysqli_fetch_array($marcas)){ ?>
                                        <option class="text-capitalize" value="<?= $marca['id'] ?>"><?= $marca['nome'] ?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="categoria_google">Categoria Google</label>
                                <input type="number" class="form-control" name="categoria_google" id="categoria_google">
                                <small><a href="https://www.google.com/basepages/producttype/taxonomy-with-ids.pt-BR.txt" target="_blank">Link</a> para consulta</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-4 <?php if($loja['modo_whatsapp'] == 1){ echo 'd-none'; } ?>">
                            <div class="form-group">
                                <label for="mpn">MPN</label>
                                <input type="text" class="form-control" name="mpn" id="mpn" maxlength="70">
                                <small>Número da peça do fabricante.</small>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 <?php if($loja['modo_whatsapp'] == 1){ echo 'd-none'; } ?>">
                            <div class="form-group">
                                <label for="gtin">GTIN</label>
                                <input type="text" class="form-control" name="gtin" id="gtin" maxlength="14">
                                <small>Os GTIN são os números de um código de barras que são usados ​​pelo fabricante da marca para identificar de maneira exclusiva um produto dentro do mercado global.</small>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="sku">SKU <span class="campo-obrigatorio">*</span></label>
                                <input type="text" class="form-control" name="sku" id="sku" maxlength="25" value="<?= $ultimo_id ?>" required>
                                <small>Identificação interna do produto</small>
                            </div>
                        </div>
                    </div>  
                    
                    <hr class="<?php if($loja['modo_whatsapp'] == 1){ if($loja['modo_whatsapp_preco'] == 0){ echo 'd-none'; } } ?>">            

                    <div class="row admin-subtitulo <?php if($loja['modo_whatsapp'] == 1){ if($loja['modo_whatsapp_preco'] == 0){ echo 'd-none'; } } ?>">
                        <div class="col-12">Valores</div>
                        <div class="col-12"><small class="<?php if($loja['modo_whatsapp'] == 1){ echo 'd-none'; } ?>">Caso o produto possua variações o estoque considerado será o de cada uma, e não o estoque geral oferecido na sessão VALORES.</small></div>
                    </div>
                    
                    <div class="row <?php if($loja['modo_whatsapp'] == 1){ if($loja['modo_whatsapp_preco'] == 0){ echo 'd-none'; } } ?>">
                        <div class="col-12 col-md <?php if($loja['modo_whatsapp'] == 1){ if($loja['modo_whatsapp_preco'] == 0){ echo 'd-none'; } else { echo 'col-md-2'; } } ?>">
                            <div class="form-group">
                                <label for="preco">Preço</label>
                                <input type="text" class="form-control" name="preco" id="preco">
                                <small>Valor zerado mostra o produto só para consulta</small>
                            </div>
                        </div>
                        <div class="col-12 col-md <?php if($loja['modo_whatsapp'] == 1){ echo 'd-none'; } ?>">
                            <div class="form-group">
                                <label for="peso">Peso</label>
                                <input type="number" class="form-control only-number" name="peso" id="peso">
                                <small>Valor em gramas</small>
                            </div>
                        </div>
                        <div class="col-12 col-md <?php if($loja['modo_whatsapp'] == 1){ echo 'd-none'; } ?>">
                            <div class="form-group">
                                <label for="altura">Altura</label>
                                <input type="number" class="form-control only-number" name="altura" id="altura">
                                <small>Valor em centímetros</small>
                            </div>
                        </div>
                        <div class="col-12 col-md <?php if($loja['modo_whatsapp'] == 1){ echo 'd-none'; } ?>">
                            <div class="form-group">
                                <label for="largura">Largura</label>
                                <input type="number" class="form-control only-number" name="largura" id="largura">
                                <small>Valor em centímetros</small>
                            </div>
                        </div>
                        <div class="col-12 col-md <?php if($loja['modo_whatsapp'] == 1){ echo 'd-none'; } ?>">
                            <div class="form-group">
                                <label for="comprimento">Comprimento</label>
                                <input type="number" class="form-control only-number" name="comprimento" id="comprimento">
                                <small>Valor em centímetros</small>
                            </div>
                        </div>
                        <div class="col-12 col-md <?php if($loja['modo_whatsapp'] == 1){ if($loja['modo_whatsapp_preco'] == 0){ echo 'd-none'; } else { echo 'col-md-2'; } } ?>">
                            <div class="form-group">
                                <label for="estoque">Estoque</label>
                                <input type="number" class="form-control only-number" name="estoque" id="estoque">
                            </div>
                        </div>
                    </div>         
                    
                    <hr>        

                    <div class="row admin-subtitulo">
                        <div class="col-12">SEO</div>
                        <div class="col-12"><small>Adicione palavras chave e uma breve descrição para o seu produto. Estas informações são importantes para os motores de busca encontrarem de forma mais fácil seu produto.</small></div>
                    </div>    

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="palavras_chave">Palavras chave <span class="campo-obrigatorio">*</span></label>
                                <textarea class="form-control" name="palavras_chave" id="palavras_chave" rows="3" required></textarea>
                                <small>Separar as palavras com virgula. Ex: loja, roupas, femininas</small>
                            </div>
                        </div>
                    </div>          

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="descricao">Descrição <span class="campo-obrigatorio">*</span></label>
                                <textarea class="form-control" name="descricao" id="descricao" rows="6" required></textarea>
                            </div>
                        </div>
                    </div>
                                        
                    <hr>            

                    <div class="row admin-subtitulo">
                        <div class="col-12">MÍDIA</div>
                        <div class="col-12"><small>Tamanho máximo das imagens: 5MB</small></div>
                        <div class="col-12"><small>Extensões aceitas: <b>.jpg, .jpeg, .gif e .png</b></small></div>
                    </div>
                
                    <div id="cadastra-imagens">
                        <label for="imagens" class="label-form">Imagens</label>
                        <div id="imagens" class="dropzone">
                            <div class="fallback">
                                <input name="file" type="file" multiple/>
                            </div>
                        </div>
                    </div>
                    
                    <div id="erros-dropzone" class="row"></div>                     
            
                    <div class="row mt-4">
                        <div class="col-12 text-center text-md-right">
                            <div class="form-group">
                                <button type="submit" class="btn btn-dark btn-bottom">CADASTRAR</button>
                            </div>
                        </div>
                    </div> 

                </div>

                <div class="tab-pane" id="conteudo-tab-caracteristicas" role="tabpanel" aria-labelledby="tab-caracteristicas">

                    <div class="row admin-subtitulo">
                        <div class="col-12">CARACTERÍSTICAS</div>
                    </div>
                    
                    <?php if($loja_roupas){ ?>

                        <div id="generos-row" class="row mt-3">
                            <div class="col-12">
                                <ul>
                                    <li><label for="generos">Gênero <span class="campo-obrigatorio">*</span></label></li>
                                    <li id="generos">
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-secondary active">
                                                <input type="radio" name="genero" id="genero-masculino" value="male" autocomplete="off" checked> Masculino
                                            </label>
                                            <label class="btn btn-secondary">
                                                <input type="radio" name="genero" id="genero-feminino" value="female" autocomplete="off"> Feminino
                                            </label>
                                            <label class="btn btn-secondary">
                                                <input type="radio" name="genero" id="genero-unisex" value="unisex" autocomplete="off"> Unisex
                                            </label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row mt-3 pb-3">
                            <div class="col-12">
                                <ul>
                                    <li><label for="idades">Grupo de idade <span class="campo-obrigatorio">*</span></label></li>
                                    <li id="idades">
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-secondary label-btn-group-idade">
                                                <input type="radio" name="idade" id="idade-newborn" value="newborn" autocomplete="off"> Recém nascido
                                            </label>
                                            <label class="btn btn-secondary label-btn-group-idade">
                                                <input type="radio" name="idade" id="idade-infant" value="infant" autocomplete="off"> 3 a 12 meses
                                            </label>
                                            <label class="btn btn-secondary label-btn-group-idade">
                                                <input type="radio" name="idade" id="idade-toddler" value="toddler" autocomplete="off"> 1 a 5 anos
                                            </label>
                                            <label class="btn btn-secondary label-btn-group-idade">
                                                <input type="radio" name="idade" id="idade-kids" value="kids" autocomplete="off"> Infantil
                                            </label>
                                            <label class="btn btn-secondary active label-btn-group-idade">
                                                <input type="radio" name="idade" id="idade-adult" value="adult" autocomplete="off" checked> Adulto
                                            </label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    <?php } ?>

                    <div class="row">                        
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <label for="atributo-primario-row" class="mb-0">Atributo primário</label>
                                <small class="mb-2">Selecione um atributo primário e depois as suas características para começar a gerar variações.</small>
                                <div id="atributo-primario-row" class="row">
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <select class="form-control text-capitalize" name="atributo-primario" id="atributo-primario">
                                                <option value="" disabled selected>Selecione...</option>
                                                <?php $busca_atributos_primarios = mysqli_query($conn, "SELECT id, nome FROM atributo WHERE status = 1 ORDER BY nome") ?>
                                                <?php while($atributo = mysqli_fetch_array($busca_atributos_primarios)){ ?> 
                                                    <option value="<?= $atributo['id'] ?>"><?= $atributo['nome'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <div class="form-group">
                                            <select class="form-control text-capitalize selectpicker" name="caracteristicas-primarias[]" id="caracteristicas-primarias" data-style="btn-default" data-dropup-auto="false" data-live-search="true" title="Características primárias.." multiple data-selected-text-format="count > 8"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">                        
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <label for="atributo-secundario-row" class="mb-0">Atributo secundário</label>
                                <small class="mb-2">Para liberar o atributo secundário escolha um primário.</small>
                                <div id="atributo-secundario-row" class="row">
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <select class="form-control text-capitalize" name="atributo-secundario" id="atributo-secundario">
                                                <option value="" disabled selected>Atributo secundário...</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <div class="form-group">
                                            <select class="form-control text-capitalize selectpicker" name="caracteristicas-secundarias[]" id="caracteristicas-secundarias" data-style="btn-default" data-dropup-auto="false" data-live-search="true" title="Características secundárias.." multiple data-selected-text-format="count > 8"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">                        
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <label for="palavras_chave">Variações <span class="campo-obrigatorio">*</span></label>
                                <small>Adicione atributos primários e/ou secundários e o sistema disponibilizará as possíveis combinações entre eles.</small>
                                <small>As variações com estoque zerado aparecem na loja indisponíves para compra, e as desativadas não aparecem.</small>
                                <small>Para alterar a ordem de exibição das variações no site, basta arrasta-las.</small>
                            </div>
                        </div>
                    </div>    
                        
                    <div class="row mt-3">
                        <div class="col-12">
                            <div id="variacoes"></div>
                        </div>
                    </div>  
                    
                    <input type="hidden" id="ids-variacoes" name="ids-variacoes" value=""> 
                    <input type="hidden" id="ultimo_id" value="<?= $ultimo_id ?>">

                </div>
                
            </div> 

        </form>

    </div>    

</section>

<!--SCRIPTS-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/min/dropzone.min.js" integrity="sha512-9WciDs0XP20sojTJ9E7mChDXy6pcO0qHpwbEJID1YVavz2H6QBz5eLoDD8lseZOb2yGT8xDNIV7HIe1ZbuiDWg==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js" integrity="sha512-0bEtK0USNd96MnO4XhH8jhv3nyRF0eK87pJke6pkYf3cM0uDIhNJy9ltuzqgypoIFXw3JSuiy04tVk4AjpZdZw==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.js" integrity="sha512-Hyk+1XSRfagqzuSHE8M856g295mX1i5rfSV5yRugcYFlvQiE3BKgg5oFRfX45s7I8qzMYFa8gbFy9xMFbX7Lqw==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.js" integrity="sha512-hkvXFLlESjeYENO4CNi69z3A1puvONQV5Uh+G4TUDayZxSLyic5Kba9hhuiNLbHqdnKNMk2PxXKm0v7KDnWkYA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript" src="modulos/produtos/js/scripts.js"></script>