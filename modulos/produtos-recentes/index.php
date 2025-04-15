<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/produtos-recentes/css/style.css">

<!--PRODUTOS-->
<section id="produtos-recentes">

    <div id="produtos-recentes-produtos">

        <h2 class="d-none">Produtos vistos recentemente</h2>

        <div class="titulo-section">
            <span>VISTOS RECENTEMENTE</span>
            <a class="d-none d-md-block" href="<?= $loja['site'] ?>vistos-recentemente">Ver todos ></a>
        </div>

        <div class="row"></div>

        <div class="btn-ver-todos d-block d-md-none">
            <a href="<?= $loja['site'] ?>vistos-recentemente">
                <ul>
                    <li>Ver todos</li>
                    <li><i class="fas fa-chevron-right"></i></li>
                </ul>                
            </a>
        </div>

    </div>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="<?= $loja['site'] ?>modulos/produtos-recentes/js/scripts.js"></script>