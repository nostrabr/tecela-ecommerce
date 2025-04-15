

<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE TENTAR ACESSAR COMO USUÁRIO, DESLOGA DO SISTEMA
if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
} else if($nivel_usuario == 'M' | $nivel_usuario == 'S' | $nivel_usuario == 'A'){
    $busca_loja = mysqli_query($conn, 'SELECT * FROM loja WHERE id = 1'); 
    $loja       = mysqli_fetch_array($busca_loja);
}

//BUSCA O SEO DAS PÁGINAS E COLOCA EM UM ARRAY PARA DISTRIBUIR
$busca_seo = mysqli_query($conn, "SELECT titulo, descricao, palavras_chave FROM seo ORDER BY id ASC");

//ESTANCIA O ARRAY
$array_seo = [];

//PREENCHE O ARRAY
while($seo = mysqli_fetch_array($busca_seo)){
    $array_seo[] = array(
        "titulo"         => $seo["titulo"],
        "descricao"      => $seo["descricao"],
        "palavras_chave" => $seo["palavras_chave"]
    );
}

?>

<!--SECTION CONFIGURAÇÕES-->
<section id="configuracoes-seo">

    <div class="container-fluid">

        <!-- ROW DO TÍTULO -->
        <div class="row">
            <div class="col-8">    
                <div id="admin-titulo-pagina">Configuração de SEO</div>
            </div>
            <div class="col-4 text-right">
                <button type="button" class="btn btn-dark btn-top-right" onclick="javascript: window.location.href = 'configuracoes.php';">VOLTAR</button>
            </div>
        </div>
        
        <!-- FORM DE EDIÇÃO -->
        <form action="modulos/configuracoes/php/edicao-seo.php" method="POST">

            <div class="row admin-subtitulo">
                <div class="col-12">Dados das páginas</div>
                <div class="col-12"><small>O preenchimento dos campos abaixo são de extrema importância. Descrições breves e objetivas são mais bem vistas pelos motores de busca.</small></div>
            </div>

            <div class="row admin-subtitulo">
                <div class="col-12">HOME</div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="home-titulo">Título</label>
                        <input class="form-control" name="home-titulo" id="home-titulo" maxlength="80" value="<?= $array_seo[0]['titulo'] ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="home-descricao">Descrição</label>
                        <textarea class="form-control" name="home-descricao" id="home-descricao" rows="2"><?= str_replace("<br>","",str_replace("<br/>","",str_replace("<br />","",$array_seo[0]['descricao']))) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="home-palavras-chave">Palavras chave</label>
                        <textarea class="form-control" name="home-palavras-chave" id="palavras-chave" rows="1"><?= $array_seo[0]['palavras_chave'] ?></textarea>
                        <small>Separar as palavras com virgula. Ex: loja, roupas, femininas</small>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row admin-subtitulo">
                <div class="col-12">PRODUTOS</div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="produtos-titulo">Título</label>
                        <input class="form-control" name="produtos-titulo" id="produtos-titulo" maxlength="80" value="<?= $array_seo[1]['titulo'] ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="produtos-descricao">Descrição</label>
                        <textarea class="form-control" name="produtos-descricao" id="produtos-descricao" rows="2"><?= str_replace("<br>","",str_replace("<br/>","",str_replace("<br />","",$array_seo[1]['descricao']))) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="produtos-palavras-chave">Palavras chave</label>
                        <textarea class="form-control" name="produtos-palavras-chave" id="produtos-palavras-chave" rows="1"><?= $array_seo[1]['palavras_chave'] ?></textarea>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row admin-subtitulo">
                <div class="col-12">CONTATO</div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="contato-titulo">Título</label>
                        <input class="form-control" name="contato-titulo" id="contato-titulo" maxlength="80" value="<?= $array_seo[2]['titulo'] ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="contato-descricao">Descrição</label>
                        <textarea class="form-control" name="contato-descricao" id="contato-descricao" rows="2"><?= str_replace("<br>","",str_replace("<br/>","",str_replace("<br />","",$array_seo[2]['descricao']))) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="contato-palavras-chave">Palavras chave</label>
                        <textarea class="form-control" name="contato-palavras-chave" id="contato-palavras-chave" rows="1"><?= $array_seo[2]['palavras_chave'] ?></textarea>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row admin-subtitulo">
                <div class="col-12">LOCALIZAÇÃO</div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="localizacao-titulo">Título</label>
                        <input class="form-control" name="localizacao-titulo" id="localizacao-titulo" maxlength="80" value="<?= $array_seo[3]['titulo'] ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="localizacao-descricao">Descrição</label>
                        <textarea class="form-control" name="localizacao-descricao" id="localizacao-descricao" rows="2"><?= str_replace("<br>","",str_replace("<br/>","",str_replace("<br />","",$array_seo[3]['descricao']))) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="localizacao-palavras-chave">Palavras chave</label>
                        <textarea class="form-control" name="localizacao-palavras-chave" id="localizacao-palavras-chave" rows="1"><?= $array_seo[3]['palavras_chave'] ?></textarea>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row admin-subtitulo">
                <div class="col-12">SOBRE</div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="sobre-titulo">Título</label>
                        <input class="form-control" name="sobre-titulo" id="sobre-titulo" maxlength="80" value="<?= $array_seo[4]['titulo'] ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="sobre-descricao">Descrição</label>
                        <textarea class="form-control" name="sobre-descricao" id="sobre-descricao" rows="2"><?= str_replace("<br>","",str_replace("<br/>","",str_replace("<br />","",$array_seo[4]['descricao']))) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="sobre-palavras-chave">Palavras chave</label>
                        <textarea class="form-control" name="sobre-palavras-chave" id="sobre-palavras-chave" rows="1"><?= $array_seo[4]['palavras_chave'] ?></textarea>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row admin-subtitulo">
                <div class="col-12">CADASTRO DE CLIENTE</div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="cadastro-cliente-titulo">Título</label>
                        <input class="form-control" name="cadastro-cliente-titulo" id="home-titulo" maxlength="80" value="<?= $array_seo[5]['titulo'] ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="cadastro-cliente-descricao">Descrição</label>
                        <textarea class="form-control" name="cadastro-cliente-descricao" id="cadastro-cliente-descricao" rows="2"><?= str_replace("<br>","",str_replace("<br/>","",str_replace("<br />","",$array_seo[5]['descricao']))) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="cadastro-cliente-palavras-chave">Palavras chave</label>
                        <textarea class="form-control" name="cadastro-cliente-palavras-chave" id="cadastro-cliente-palavras-chave" rows="1"><?= $array_seo[5]['palavras_chave'] ?></textarea>
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

    </div>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="modulos/configuracoes/js/scripts.js"></script>