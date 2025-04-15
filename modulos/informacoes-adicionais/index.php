<?php 

//BUSCA AS INFORMAÇÕES ADICIONAIS
$informacoes_adicionais        = mysqli_query($conn,"SELECT * FROM informacao_adicional WHERE status = 1 ORDER BY id");
$contador_informacao_adicional = 0;

//SE TIVER ALGUMA ATIVA
if(mysqli_num_rows($informacoes_adicionais) > 0){

?>

<!--CSS-->
<link rel="stylesheet" href="modulos/informacoes-adicionais/css/style.css">

<!--INFORMAÇÕES ADICIONAIS-->
<section id="informacoes-adicionais-container">
    
    <div id="informacoes-adicionais">
        
        <div class="row">

            <?php while($informacao_adicional = mysqli_fetch_array($informacoes_adicionais)){ $contador_informacao_adicional++; ?>

                <div class="col-12 col-sm">
                    <ul>
                        <li class="informacoes-adicionais-icone"><img src="imagens/informacoes-adicionais/pequena/<?= $informacao_adicional['imagem'] ?>" alt="<?= $informacao_adicional['titulo'] ?>"></li>
                        <li>
                            <ul>
                                <li class="informacoes-adicionais-titulo"><?= $informacao_adicional['titulo'] ?></li>
                                <li class="informacoes-adicionais-descricao"><?= $informacao_adicional['descricao'] ?></li>
                            </ul>                        
                        </li>
                    </ul>    
                </div>

            <?php } ?>

        </div>
    </div>

</section>

<?php } ?>