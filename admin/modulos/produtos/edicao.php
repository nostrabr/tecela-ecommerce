<!--CSS-->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/dropzone.min.css" integrity="sha512-3g+prZHHfmnvE1HBLwUnVuunaPOob7dpksI7/v6UnF/rnKGwHf/GdEq9K7iEN7qTtW+S0iivTcGpeTBqqB04wA==" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.css" integrity="sha512-A81ejcgve91dAWmCGseS60zjrAdohm7PTcAjjiDWtw3Tcj91PNMa1gJ/ImrhG+DbT5V+JQ5r26KT5+kgdVTb5w==" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.css" integrity="sha512-vEia6TQGr3FqC6h55/NdU3QSM5XR6HSl5fW71QTKrgeER98LIMGwymBVM867C1XHIkYD9nMTfWK2A0xcodKHNA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="modulos/produtos/css/style.css">

<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION E O IDENTIFICADOR DO PRODUTO NA URL
$identificador_produto = filter_input(INPUT_GET,"id",FILTER_SANITIZE_STRING);
$nivel_usuario         = filter_var($_SESSION['nivel']);

//BUSCA PRODUTO
$busca_produto = mysqli_query($conn, 'SELECT * FROM produto WHERE identificador = "'.$identificador_produto.'" AND status != 2'); 

//VERIFICA SE ENCONTROU O PRODUTO
if(mysqli_num_rows($busca_produto) == 0){
    echo "<script>location.href='produtos.php';</script>";
} else {
    
    $produto = mysqli_fetch_array($busca_produto);
    
    //BUSCA AS VARIAÇÕES DO PRODUTO
    $variacoes         = mysqli_query($conn, "
        SELECT pv.id_caracteristica_primaria, pv.id_caracteristica_secundaria, pv.estoque, pv.ordem AS variacao_ordem, pv.status AS variacao_status, 
        (SELECT c.nome FROM caracteristica AS c WHERE c.id = pv.id_caracteristica_primaria) AS nome_caracteristica_primaria, 
        (SELECT c.nome FROM caracteristica AS c WHERE c.id = pv.id_caracteristica_secundaria) AS nome_caracteristica_secundaria 
        FROM produto_variacao AS pv 
        WHERE pv.status != 2 AND pv.id_produto = ".$produto['id']." ORDER BY pv.ordem
    ");
    
    //MONTA ARRAYS DE CARACTERISTICAS E VARIAÇÕES
    $array_caracteristicas_primarias   = [];
    $array_caracteristicas_secundarias = [];
    $array_variacoes                   = [];

    //PREENCHE OS ARRAYS
    while($variacao = mysqli_fetch_array($variacoes)){        
        if(!in_array($variacao['id_caracteristica_primaria'],$array_caracteristicas_primarias)){ array_push($array_caracteristicas_primarias,$variacao['id_caracteristica_primaria']); }
        if(!in_array($variacao['id_caracteristica_secundaria'],$array_caracteristicas_secundarias)){ array_push($array_caracteristicas_secundarias,$variacao['id_caracteristica_secundaria']); }
        $array_variacoes[] = array(
            "id_atributo_primario"           => $produto['atributo_primario'],
            "id_caracteristica_primaria"     => $variacao['id_caracteristica_primaria'],
            "nome_caracteristica_primaria"   => $variacao['nome_caracteristica_primaria'],
            "id_atributo_secundario"         => $produto['atributo_secundario'],
            "id_caracteristica_secundaria"   => $variacao['id_caracteristica_secundaria'],
            "nome_caracteristica_secundaria" => $variacao['nome_caracteristica_secundaria'],
            "variacao_status"                => $variacao['variacao_status'],
            "variacao_ordem"                 => $variacao['variacao_ordem'],
            "estoque"                        => $variacao['estoque']
        );
    };

    //TOTAL DE VARIAÇÕES
    $n_variacoes       = count($array_variacoes);

    //BUSCA A QUANTIDADE DE IMAGENS CADASTRADA
    $busca_imagens     = mysqli_query($conn, "SELECT COUNT(id) AS total FROM produto_imagem WHERE id_produto = '".$produto['id']."'");
    $imagens           = mysqli_fetch_array($busca_imagens);
    $n_imagens         = $imagens['total'];

    //BUSCA AS TAGS DO PRODUTO
    $tags_produto = mysqli_query($conn, "SELECT id_tag FROM produto_tag WHERE id_produto = '".$produto['id']."'");

    $array_tags_produto = [];

    //PREENCHE ARRAY
    while($tag_produto = mysqli_fetch_array($tags_produto)){
        $array_tags_produto[] = $tag_produto['id_tag'];
    }

}

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

$avaliacoes_produto = mysqli_query($conn, "SELECT * FROM avaliacao WHERE tipo = 'PRODUTO' AND status = 1 AND id_produto = '".$produto['id']."' ORDER BY data_cadastro DESC");
$n_avaliacoes       = mysqli_num_rows($avaliacoes_produto);
    
?>

<!--SECTION PRODUTO-->
<section id="produtos-edita">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-9">    
                <div id="admin-titulo-pagina">Produtos - Edição</div>
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
            <?php if($n_avaliacoes > 0){ ?>
                <li class="nav-item">
                    <a class="nav-link" id="tab-avaliacoes" data-toggle="tab" href="#conteudo-tab-avaliacoes" role="tab" aria-controls="conteudo-tab-avaliacoes" aria-selected="false">Nota</a>
                </li>
            <?php } ?>
        </ul>

        <!-- FORM DE CADASTRO -->
        <form id="form-edicao-produto" action="" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="identificador" id="identificador" value="<?= $identificador_produto ?>">
            <input type="hidden" id="n_imagens" value="<?= $n_imagens ?>">
        
            <div class="tab-content">

                <div class="tab-pane active" id="conteudo-tab-produto" role="tabpanel" aria-labelledby="tab-produto">
        
                    <div class="row admin-subtitulo"><div class="col-12">Dados</div></div>

                    <div class="row">
                        <div class="col-12 col-md-10">
                            <div class="form-group">
                                <label for="nome">Nome <span class="campo-obrigatorio">*</span></label>
                                <input type="text" class="form-control text-capitalize" name="nome" id="nome" maxlength="100" value="<?= $produto['nome'] ?>" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label for="relevancia">Relevância <span class="campo-obrigatorio">*</span></label>
                                <input type="number" class="form-control" name="relevancia" id="relevancia" value="<?= $produto['relevancia'] ?>" min="0" required>
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
                                        <option class="text-capitalize" value="<?= $tag['id'] ?>" <?php if(in_array($tag['id'], $array_tags_produto)){ echo 'selected'; } ?>><?= $tag['nome'] ?></option>
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
                                <input type="hidden" id="categoria" name="categoria" nome="<?= $categoria['nome'] ?>" value="<?= $produto['id_categoria'] ?>" required>
                            </div>   
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="marca">Marca <span class="campo-obrigatorio">*</span></label>
                                <select class="form-control text-capitalize" name="marca" id="marca" required>
                                    <?php $marcas = mysqli_query($conn, "SELECT id, nome FROM marca ORDER BY nome ASC"); ?>
                                    <?php while($marca = mysqli_fetch_array($marcas)){ ?>
                                        <option class="text-capitalize" value="<?= $marca['id'] ?>" <?php if($produto['id_marca'] == $marca['id']){ echo 'selected'; } ?>><?= $marca['nome'] ?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="categoria_google">Categoria Google</label>
                                <input type="number" class="form-control" name="categoria_google" id="categoria_google" value="<?= $produto['categoria_google'] ?>">
                                <small><a href="https://www.google.com/basepages/producttype/taxonomy-with-ids.pt-BR.txt" target="_blank">Link</a> para consulta</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-4 <?php if($loja['modo_whatsapp'] == 1){ echo 'd-none'; } ?>">
                            <div class="form-group">
                                <label for="mpn">MPN</label>
                                <input type="text" class="form-control" name="mpn" id="mpn" value="<?= $produto['mpn'] ?>" maxlength="70">
                                <small>Número da peça do fabricante.</small>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 <?php if($loja['modo_whatsapp'] == 1){ echo 'd-none'; } ?>">
                            <div class="form-group">
                                <label for="gtin">GTIN</label>
                                <input type="text" class="form-control" name="gtin" id="gtin" value="<?= $produto['gtin'] ?>" maxlength="14">
                                <small>Os GTIN são os números de um código de barras que são usados ​​pelo fabricante da marca para identificar de maneira exclusiva um produto dentro do mercado global.</small>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="sku">SKU <span class="campo-obrigatorio">*</span></label>
                                <input type="text" class="form-control" id="sku" name="sku" value="<?= $produto['sku'] ?>" required>
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
                                <input type="text" class="form-control" name="preco" id="preco" value="<?= 'R$ '.number_format($produto['preco'], 2, ',', '.') ?>">
                                <small>Valor zerado mostra o produto só para consulta</small>
                            </div>
                        </div>
                        <div class="col-12 col-md <?php if($loja['modo_whatsapp'] == 1){ echo 'd-none'; } ?>">
                            <div class="form-group">
                                <label for="peso">Peso</label>
                                <input type="number" class="form-control only-number" name="peso" id="peso" value="<?= $produto['peso'] ?>">
                                <small>Valor em gramas</small>
                            </div>
                        </div>
                        <div class="col-12 col-md <?php if($loja['modo_whatsapp'] == 1){ echo 'd-none'; } ?>">
                            <div class="form-group">
                                <label for="altura">Altura</label>
                                <input type="number" class="form-control only-number" name="altura" id="altura" value="<?= $produto['altura'] ?>">
                                <small>Valor em centímetros</small>
                            </div>
                        </div>
                        <div class="col-12 col-md <?php if($loja['modo_whatsapp'] == 1){ echo 'd-none'; } ?>">
                            <div class="form-group">
                                <label for="largura">Largura</label>
                                <input type="number" class="form-control only-number" name="largura" id="largura" value="<?= $produto['largura'] ?>">
                                <small>Valor em centímetros</small>
                            </div>
                        </div>
                        <div class="col-12 col-md <?php if($loja['modo_whatsapp'] == 1){ echo 'd-none'; } ?>">
                            <div class="form-group">
                                <label for="comprimento">Comprimento</label>
                                <input type="number" class="form-control only-number" name="comprimento" id="comprimento" value="<?= $produto['comprimento'] ?>">
                                <small>Valor em centímetros</small>
                            </div>
                        </div>
                        <div class="col-12 col-md <?php if($loja['modo_whatsapp'] == 1){ if($loja['modo_whatsapp_preco'] == 0){ echo 'd-none'; } else { echo 'col-md-2'; } } ?>">
                            <div class="form-group">
                                <label for="estoque">Estoque</label>
                                <input type="number" class="form-control only-number" name="estoque" id="estoque" value="<?= $produto['estoque'] ?>">
                            </div>
                        </div>
                    </div>            
                    
                    <hr>        

                    <div class="row admin-subtitulo">
                        <div class="col-12">SEO</div>
                        <div class="col-12"><small>Adicione palavras chave e uma breve descrição para o seu produto. Estas informações são importantes para os motores de busca encontrarem de forma mais fácil seu produto.</small></div>
                    </div>   

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="palavras_chave">Palavras chave <span class="campo-obrigatorio">*</span></label>
                                <textarea class="form-control" name="palavras_chave" id="palavras_chave" rows="3" required><?= $produto['palavras_chave'] ?></textarea>
                                <small>Separar as palavras com virgula. Ex: loja, roupas, femininas</small>
                            </div>
                        </div>
                    </div>  

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="descricao">Descrição <span class="campo-obrigatorio">*</span></label>
                                <?php if(base64_encode(base64_decode($produto['descricao'], true)) === $produto['descricao']){ $produto_descricao = base64_decode($produto['descricao']); } else { $produto_descricao = $produto['descricao']; } ?>
                                <textarea class="form-control" name="descricao" id="descricao" rows="6" required><?= str_replace('<br />','',$produto_descricao) ?></textarea>
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
                                <button id="btn-salvar-produto-copia" type="submit" class="btn btn-dark btn-bottom btn-salvar-produto">SALVAR COMO NOVO PRODUTO</button>
                            </div>
                            <div class="form-group">
                                <button id="btn-salvar-produto-edicao" type="submit" class="btn btn-dark btn-bottom btn-salvar-produto">SALVAR</button>
                            </div>
                        </div>
                    </div>  

                </div>

                <div class="tab-pane" id="conteudo-tab-caracteristicas" role="tabpanel" aria-labelledby="tab-caracteristicas">

                    <div class="row admin-subtitulo">
                        <div class="col-12">CARACTERÍSTICAS</div>
                        <div class="col-12"><small>Adicione atributos com o botão abaixo para disponibilizar as variações do seu produto.</small></div>
                        <div class="col-12"><small>Uma variação com o estoque zerado é considerada como desativada e não será disponibilizada.</small></div>
                    </div>                   
                    
                    <?php if($loja_roupas){ ?>

                        <div id="generos-row" class="row">
                            <div class="col-12">
                                <ul>
                                    <li><label for="generos">Gênero <span class="campo-obrigatorio">*</span></label></li>
                                    <li id="generos">
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-secondary">
                                                <input type="radio" name="genero" id="genero-masculino" value="male" autocomplete="off" <?php if($produto['genero'] == 'male'){ echo 'checked'; } ?>> Masculino
                                            </label>
                                            <label class="btn btn-secondary">
                                                <input type="radio" name="genero" id="genero-feminino" value="female" autocomplete="off" <?php if($produto['genero'] == 'female'){ echo 'checked'; } ?>> Feminino
                                            </label>
                                            <label class="btn btn-secondary">
                                                <input type="radio" name="genero" id="genero-unisex" value="unisex" autocomplete="off" <?php if($produto['genero'] == 'unisex'){ echo 'checked'; } ?>> Unisex
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
                                                <input type="radio" name="idade" id="idade-newborn" value="newborn" autocomplete="off" <?php if($produto['idade'] == 'newborn'){ echo 'checked'; } ?>> Recém nascido
                                            </label>
                                            <label class="btn btn-secondary label-btn-group-idade">
                                                <input type="radio" name="idade" id="idade-infant" value="infant" autocomplete="off" <?php if($produto['idade'] == 'infant'){ echo 'checked'; } ?>> 3 a 12 meses
                                            </label>
                                            <label class="btn btn-secondary label-btn-group-idade">
                                                <input type="radio" name="idade" id="idade-toddler" value="toddler" autocomplete="off" <?php if($produto['idade'] == 'toddler'){ echo 'checked'; } ?>> 1 a 5 anos
                                            </label>
                                            <label class="btn btn-secondary label-btn-group-idade">
                                                <input type="radio" name="idade" id="idade-kids" value="kids" autocomplete="off" <?php if($produto['idade'] == 'kids'){ echo 'checked'; } ?>> Infantil
                                            </label>
                                            <label class="btn btn-secondary label-btn-group-idade">
                                                <input type="radio" name="idade" id="idade-adult" value="adult" autocomplete="off" <?php if($produto['idade'] == 'adult'){ echo 'checked'; } ?>> Adulto
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

                                                <?php if($produto['atributo_primario'] != ''){ $atributo_primario = $produto['atributo_primario']; ?>
                                                    <option value="">Remover</option>
                                                <?php } else { $atributo_primario = ''; ?> 
                                                    <option value="" disabled selected>Selecione...</option>
                                                <?php } ?>

                                                <?php $busca_atributos_primarios = mysqli_query($conn, "SELECT id, nome FROM atributo WHERE status = 1 ORDER BY nome") ?>                                                    
                                                <?php while($atributo = mysqli_fetch_array($busca_atributos_primarios)){ ?> 
                                                    <option value="<?= $atributo['id'] ?>" <?php if($atributo['id'] == $produto['atributo_primario']){ echo 'selected'; } ?>><?= $atributo['nome'] ?></option>
                                                <?php } ?>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <div class="form-group">
                                            <select class="form-control text-capitalize selectpicker" name="caracteristicas-primarias[]" id="caracteristicas-primarias" data-style="btn-default" data-dropup-auto="false" data-live-search="true" title="Características primárias.." multiple data-selected-text-format="count > 8">
                                            
                                                <?php if($atributo_primario != ''){ ?>
                                                    <?php $busca_caracteristicas_primarias = mysqli_query($conn, "SELECT id, nome FROM caracteristica WHERE status = 1 AND id_atributo = '$atributo_primario' ORDER BY nome") ?>                                                    
                                                    <?php while($caracteristica = mysqli_fetch_array($busca_caracteristicas_primarias)){ ?> 
                                                        <option value="<?= $caracteristica['id'] ?>" id-atributo="<?= $atributo_primario ?>" <?php if(in_array($caracteristica['id'],$array_caracteristicas_primarias)){ echo 'selected'; } ?>><?= $caracteristica['nome'] ?></option>
                                                    <?php } ?>
                                                <?php } ?>

                                            </select>
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

                                                <?php if($atributo_primario != ''){ ?>
                                                    <?php if($produto['atributo_secundario'] != '' & $produto['atributo_secundario'] != 0){ ?>
                                                        <option value="">Remover</option>
                                                    <?php } else { ?> 
                                                        <option value="" disabled selected>Selecione...</option>
                                                        <option value="">Remover</option>
                                                    <?php } ?>
                                                    <?php $busca_atributos_secundarios = mysqli_query($conn, "SELECT id, nome FROM atributo WHERE status = 1 ORDER BY nome") ?>                                                    
                                                    <?php while($atributo = mysqli_fetch_array($busca_atributos_secundarios)){ ?> 
                                                        <?php if($atributo_primario != $atributo['id']){ ?>
                                                            <option value="<?= $atributo['id'] ?>" <?php if($atributo['id'] == $produto['atributo_secundario']){ echo 'selected'; } ?>><?= $atributo['nome'] ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } else { ?> 
                                                    <option value="" disabled selected>Selecione...</option>
                                                <?php } ?>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <div class="form-group">
                                            <select class="form-control text-capitalize selectpicker" name="caracteristicas-secundarias[]" id="caracteristicas-secundarias" data-style="btn-default" data-dropup-auto="false" data-live-search="true" title="Características secundárias.." multiple data-selected-text-format="count > 8">
                                                <?php if($produto['atributo_secundario']  != ''){ ?>
                                                    <?php $busca_caracteristicas_secundarias = mysqli_query($conn, "SELECT id, nome FROM caracteristica WHERE status = 1 AND id_atributo = '".$produto['atributo_secundario']."' ORDER BY nome") ?>                                                    
                                                    <?php while($caracteristica = mysqli_fetch_array($busca_caracteristicas_secundarias)){ ?> 
                                                        <option value="<?= $caracteristica['id'] ?>" id-atributo="<?= $produto['atributo_secundario'] ?>" <?php if(in_array($caracteristica['id'],$array_caracteristicas_secundarias)){ echo 'selected'; } ?>><?= $caracteristica['nome'] ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
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
                            <div id="variacoes">
                                
                                <?php if($n_variacoes > 0){ ?>
                                    <?php $ids_variacoes = ''; ?>
                                    <?php for($i = 0; $i < $n_variacoes; $i++){ ?>

                                        <?php 
                                            
                                            //GERA O NOME DA VARIAÇÃO
                                            $nome_variacao = $array_variacoes[$i]['nome_caracteristica_primaria'];
                                            if($array_variacoes[$i]['nome_caracteristica_secundaria'] != ''){ $nome_variacao .= '-'.$array_variacoes[$i]['nome_caracteristica_secundaria']; }

                                            //GERA O IDENTIFICADOR PARA O ELEMENTO
                                            $id_variacao   = $array_variacoes[$i]['id_caracteristica_primaria'];
                                            if($array_variacoes[$i]['id_caracteristica_secundaria'] != ''){ $id_variacao .= '-'.$array_variacoes[$i]['id_caracteristica_secundaria']; }
                                            
                                            //IDS DOS ATRIBUTOS
                                            $ids_atributos = $array_variacoes[$i]['id_atributo_primario'];
                                            if($array_variacoes[$i]['id_atributo_secundario'] != ''){ $ids_atributos .= '-'.$array_variacoes[$i]['id_atributo_secundario']; }

                                            $ids_variacoes .= $id_variacao.',';

                                            //GERA AS VARIÁVEIS DE STATUS
                                            if($array_variacoes[$i]['variacao_status'] == 1){
                                                $variacao_status = 1;
                                                $variacao_status_imagem = $loja['site']."imagens/status-ativo.png";
                                            } else {
                                                $variacao_status = 0;
                                                $variacao_status_imagem = $loja['site']."imagens/status-inativo.png";
                                            }

                                        ?>

                                        <div id-variacao="<?= $id_variacao ?>" ids-atributos="<?= $ids_atributos ?>" nome-variacao="<?= $nome_variacao ?>" class='variacoes-variante <?php if($variacao_status == 0){ echo "variacoes-variante-desativada"; } ?>'><ul><li><?= $nome_variacao ?></li><li>Estoque: <input name='variacao-<?= $id_variacao ?>' class='variacoes-variante-estoque only-number' type='number' value='<?= $array_variacoes[$i]['estoque'] ?>' min='0'></li><input class='variacoes-variante-ordem' type='hidden' name='variacao-ordem-<?= $id_variacao ?>' value="<?= $array_variacoes[$i]['variacao_ordem'] ?>"></ul><div class='variacao-status'><img onclick='javascript: trocaStatusVariacao("<?= $id_variacao ?>");' id='variacao-status-img-<?= $id_variacao ?>' src='<?= $variacao_status_imagem ?>'><input id='variacao-status-input-<?= $id_variacao ?>' name='variacao-status-input-<?= $id_variacao ?>' type='hidden' value='<?= $variacao_status ?>'></div></div>

                                    <?php } ?>
                                    <?php $ids_variacoes = substr($ids_variacoes,0,-1); ?>
                                    
                                <?php } ?>

                            </div>
                        </div>
                    </div>                      

                    <input type="hidden" id="ids-variacoes" name="ids-variacoes" value="<?= $ids_variacoes ?>"> 

                </div>
                
                <?php if($n_avaliacoes > 0){ ?>
                    
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
                    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
                
                    <div class="tab-pane" id="conteudo-tab-avaliacoes" role="tabpanel" aria-labelledby="tab-avaliacoes">

                        <div class="row admin-subtitulo">
                            <div class="col-12">AVALIAÇÕES</div>
                            <div class="col-12"><small>O que as pessoas que compraram estão achando do seu produto</small></div>
                        </div>  

                        <?php 

                        $busca_media_produto = mysqli_query($conn, "SELECT AVG(nota) AS media FROM avaliacao WHERE tipo = 'PRODUTO' AND status = 1 AND id_produto = ".$produto['id']);
                        $total_media_produto = mysqli_fetch_array($busca_media_produto);
                        $total_media_produto = $total_media_produto['media'];
                        
                        if($total_media_produto > 0){ ?>
                            <li class="text-capitalize avaliacao-loja avaliacao-loja-geral" title="<?= number_format($total_media_produto,2,'.','') ?>">  
                                <?php 
                                    $media_quebrada = explode('.',number_format($total_media_produto,2,'.',''));
                                    $media_quebrada = '0.'.$media_quebrada[1];
                                    $media_quebrada = 1-$media_quebrada;
                                ?>
                                <ul>
                                    <li><img style="<?php if($total_media_produto > 0 AND $total_media_produto <= 1){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_produto >= 1){ echo 'img-dourada'; } ?>" estrela="1" id="estrela-1" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="1 estrela"></li>
                                    <li><img style="<?php if($total_media_produto > 1 AND $total_media_produto <= 2){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_produto >= 2){ echo 'img-dourada'; } ?>" estrela="2" id="estrela-2" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="2 estrelas"></li>
                                    <li><img style="<?php if($total_media_produto > 2 AND $total_media_produto <= 3){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_produto >= 3){ echo 'img-dourada'; } ?>" estrela="3" id="estrela-3" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="3 estrelas"></li>
                                    <li><img style="<?php if($total_media_produto > 3 AND $total_media_produto <= 4){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_produto >= 4){ echo 'img-dourada'; } ?>" estrela="4" id="estrela-4" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="4 estrelas"></li>
                                    <li><img style="<?php if($total_media_produto > 4 AND $total_media_produto <= 5){ echo 'filter: grayscale('.$media_quebrada.');'; } ?>" class="estrela <?php if($total_media_produto >= 5){ echo 'img-dourada'; } ?>" estrela="5" id="estrela-5" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" alt="5 estrelas"></li>
                                </ul>
                            </li>
                        <?php } ?>
                        
                        <div id="lista-avaliacoes">
                            <div class="row">
                                <div class="col-12">   
                                    <table id="admin-lista-dois" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="col">NOTA</th>
                                                <th scope="col">DATA</th>
                                                <th scope="col" class="d-none d-lg-table-cell">COMENTÁRIO</th>
                                                <th scope="col" class="d-none d-lg-table-cell">RÉPLICA</th>
                                            </tr>
                                        </thead>
                                        <tbody>      
                                            <?php while($avaliacao_produto = mysqli_fetch_array($avaliacoes_produto)){ ?> 
                                                <tr id="avaliacao-<?= $avaliacao_produto['identificador'] ?>" class="cursor-pointer" title="Visualizar" onclick="javascript: visualizaAvaliacao('<?= $avaliacao_produto['nota'] ?>','<?= date('d/m/Y H:i', strtotime($avaliacao_produto['data_cadastro']))?>','<?= addslashes(str_replace('"','\'',$avaliacao_produto['comentario'])) ?>','<?= addslashes(str_replace('"','\'',$avaliacao_produto['replica'])) ?>','<?= date('d/m/Y H:i', strtotime($avaliacao_produto['data_replica']))?>');">
                                                    <td class="text-capitalize avaliacao-loja">   
                                                        <span class="d-none">Nota <?= $avaliacao_produto['nota'] ?></span>                                         
                                                        <ul>
                                                            <li><img class="estrela <?php if($avaliacao_produto['nota'] >= 1){ echo 'img-dourada'; } ?>" estrela="1" id="estrela-1" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito ruim" alt="1 estrela"></li>
                                                            <li><img class="estrela <?php if($avaliacao_produto['nota'] >= 2){ echo 'img-dourada'; } ?>" estrela="2" id="estrela-2" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Ruim" alt="2 estrelas"></li>
                                                            <li><img class="estrela <?php if($avaliacao_produto['nota'] >= 3){ echo 'img-dourada'; } ?>" estrela="3" id="estrela-3" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Regular" alt="3 estrelas"></li>
                                                            <li><img class="estrela <?php if($avaliacao_produto['nota'] >= 4){ echo 'img-dourada'; } ?>" estrela="4" id="estrela-4" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Boa" alt="4 estrelas"></li>
                                                            <li><img class="estrela <?php if($avaliacao_produto['nota'] >= 5){ echo 'img-dourada'; } ?>" estrela="5" id="estrela-5" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito boa" alt="5 estrelas"></li>
                                                        </ul>
                                                    </td> 
                                                    <td class="text-capitalize"><?= date('d/m/Y', strtotime($avaliacao_produto['data_cadastro'])) ?></td>   
                                                    <td class="d-none d-lg-table-cell"><?= mb_strimwidth($avaliacao_produto['comentario'], 0, 25, "...") ?></td>
                                                    <td class="d-none d-lg-table-cell"><?= mb_strimwidth($avaliacao_produto['replica'], 0, 20, "...") ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                <?php } ?>
                
            </div>

        </form>

    </div>    

</section>

<!-- MODAL VISUALIZAÇÃO DE COMENTÁRIO -->
<div class="modal fade" id="modal-visualizacao-comentario" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <ul class="mb-0">
                <li><h5 class="modal-title mb-1" id="exampleModalLongTitle">Avaliação</h5></li>
            </ul>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div id="visualizacao-comentario-nota" class="col-12"></div>
                <div id="visualizacao-comentario-data" class="col-12 mt-2 mb-1"></div>
                <div id="visualizacao-comentario-comentario" class="col-12"></div>
                <div id="visualizacao-comentario-replica-container"class="col-12 mt-2">
                    <ul>
                        <li id="visualizacao-comentario-replica-titulo">Réplica:</li>
                        <li id="visualizacao-comentario-replica"></li>
                    </ul>
                </div>
            </div>
        </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!--SCRIPTS-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/min/dropzone.min.js" integrity="sha512-9WciDs0XP20sojTJ9E7mChDXy6pcO0qHpwbEJID1YVavz2H6QBz5eLoDD8lseZOb2yGT8xDNIV7HIe1ZbuiDWg==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js" integrity="sha512-0bEtK0USNd96MnO4XhH8jhv3nyRF0eK87pJke6pkYf3cM0uDIhNJy9ltuzqgypoIFXw3JSuiy04tVk4AjpZdZw==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.js" integrity="sha512-Hyk+1XSRfagqzuSHE8M856g295mX1i5rfSV5yRugcYFlvQiE3BKgg5oFRfX45s7I8qzMYFa8gbFy9xMFbX7Lqw==" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.js" integrity="sha512-hkvXFLlESjeYENO4CNi69z3A1puvONQV5Uh+G4TUDayZxSLyic5Kba9hhuiNLbHqdnKNMk2PxXKm0v7KDnWkYA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript" src="modulos/produtos/js/scripts.js"></script>