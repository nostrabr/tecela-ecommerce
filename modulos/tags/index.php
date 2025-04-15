<?php 

$tags   = mysqli_query($conn, "
    SELECT DISTINCT(t.id), t.nome 
    FROM tag AS t
    INNER JOIN produto_tag AS pt ON pt.id_tag = t.id
    ORDER BY t.nome ASC
"); 
$n_tags = mysqli_num_rows($tags);

if($n_tags > 0){

?>

<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/tags/css/style.css">

<section id="tags">

    <div id="tags-tags">

        <h2 class="d-none">Navegue pelas tags</h2>
        
        <div class="titulo-section">
            <span>NAVEGUE PELAS TAGS</span>
        </div>

        <div class="row">
            <?php $contador_tags = 0; ?>
            <?php while($tag = mysqli_fetch_array($tags)){ ?>
                <?php $contador_tags++; ?>
                <div class="col-12 col-md-6 col-lg-3 <?php if($contador_tags > 11){ echo 'd-none'; } ?>">
                    <a class="tags-tag" href="<?= $loja['site'] ?>categoria/todas/0/1/3/9999999/T/T/T/relevancia/<?= $tag['id'] ?>" alt="<?= $tag['nome'] ?>">
                        <?= $tag['nome'] ?>
                    </a>
                </div>
            <?php } ?>
            <div class="col-12 col-md-6 col-lg-3">
                <a class="tags-tag" href="<?= $loja['site'] ?>categoria/todas/0" alt="Todos Produtos">ACESSAR TODAS</a>
            </div>
            <?php if($contador_tags > 11){ ?>
            <div class="col-12">
                <a id="btn-mostra-todas-tags" class="tags-tag" href="javascript: mostraTags();" alt="Ver todas tags">VER TODAS</a>
            </div>
            <?php } ?>
        </div>

    </div>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="<?= $loja['site'] ?>modulos/tags/js/scripts.js"></script>

<?php } ?>