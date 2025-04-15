<?php

//RECEBE O IDENTIFICADOR DO PEDIDO
$identificador_pedido = filter_input(INPUT_GET, "id", FILTER_SANITIZE_STRING);

//SE NÃO FOR UM IDENTIFICADOR VÁLIDO, MANDA PRA HOME
if(mb_strlen($identificador_pedido) != 32){
    echo "<script> window.location.href = '".$loja['site']."'; </script>";
}

//BUSCA PEDIDO
$busca_pedido = mysqli_query($conn, "SELECT id FROM pedido WHERE identificador = '$identificador_pedido'");

//SE NÃO FOR UM PEDIDO VÁLIDO, MANDA PRA HOME
if(mysqli_num_rows($busca_pedido) == 0){
    echo "<script> window.location.href = '".$loja['site']."'; </script>";
} else {
    $pedido = mysqli_fetch_array($busca_pedido);
}


//RETORNO DO FORM
if(isset($_SESSION['RETORNO'])){
    if($_SESSION['RETORNO']['STATUS'] == 'SUCESSO'){
        echo "<script>mensagemAviso('sucesso', 'Avaliação registrada com sucesso', 3000);</script>";
        unset($_SESSION['RETORNO']);
    }
}

?>

<!--CSS-->
<link rel="stylesheet" href="<?= $loja['site'] ?>modulos/avaliacao/css/style.css">

<!--AVALIAÇÃO-->
<section id="avaliacao">
    
    <h2 class="subtitulo-pagina-central-h2">Avaliação</h2>    
    <p class="subtitulo-pagina-central-p">Muito obrigado pelo interesse em nos ajudar. A sua opinião conta muito para nós.</p>

    <?php 

    //BUSCA AVALIAÇÔES
    $avaliacoes = mysqli_query($conn, "SELECT * FROM avaliacao WHERE status = 0 AND id_pedido = ".$pedido['id']." ORDER BY id DESC");

    if(mysqli_num_rows($avaliacoes) > 0){

        ?>

        <form action="<?= $loja['site'] ?>modulos/avaliacao/php/cadastra.php" method="POST">

            <?php 

            $contador = 0;
            
            while($avaliacao = mysqli_fetch_array($avaliacoes)){ 

                $contador++;
                
                if($avaliacao['tipo'] == 'PRODUTO'){

                    $busca_produto = mysqli_query($conn, "SELECT nome FROM produto WHERE id = ".$avaliacao['id_produto']);
                    $produto       = mysqli_fetch_array($busca_produto);

                    ?>

                        <div id="avaliacao-loja-<?= $contador ?>" class="avaliacao-loja">
                            <h2>O que você achou do produto <?= $produto['nome'] ?>?</h2>
                            <ul>
                                <li><img class="estrela estrela-1" onmouseover="javascript: coloreEstrela(<?= $contador ?>,1);" estrela="1" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito ruim" alt="1 estrela"></li>
                                <li><img class="estrela estrela-2" onmouseover="javascript: coloreEstrela(<?= $contador ?>,2);" estrela="2" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Ruim" alt="2 estrelas"></li>
                                <li><img class="estrela estrela-3" onmouseover="javascript: coloreEstrela(<?= $contador ?>,3);" estrela="3" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Regular" alt="3 estrelas"></li>
                                <li><img class="estrela estrela-4" onmouseover="javascript: coloreEstrela(<?= $contador ?>,4);" estrela="4" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Boa" alt="4 estrelas"></li>
                                <li><img class="estrela estrela-5" onmouseover="javascript: coloreEstrela(<?= $contador ?>,5);" estrela="5" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito boa" alt="5 estrelas"></li>
                            </ul>
                            <div class="form-group mt-3">
                                <textarea name="<?= $avaliacao['identificador'] ?>-observacao" class="form-control observacoes" cols="30" rows="5" placeholder="Deixe um comentário sobre ele"></textarea>
                            </div>
                        </div>
                        <input type="hidden" id="nota-<?= $contador ?>" name="<?= $avaliacao['identificador'] ?>-nota">

                    <?php

                } else if($avaliacao['tipo'] == 'EXPERIENCIA-COMPRA'){

                    ?>

                        <div id="avaliacao-loja-<?= $contador ?>" class="avaliacao-loja">
                            <h2>Conte-nos como foi sua experiência em nossa loja:</h2>
                            <ul>
                                <li><img class="estrela estrela-1" onmouseover="javascript: coloreEstrela(<?= $contador ?>,1);" estrela="1" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito ruim" alt="1 estrela"></li>
                                <li><img class="estrela estrela-2" onmouseover="javascript: coloreEstrela(<?= $contador ?>,2);" estrela="2" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Ruim" alt="2 estrelas"></li>
                                <li><img class="estrela estrela-3" onmouseover="javascript: coloreEstrela(<?= $contador ?>,3);" estrela="3" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Regular" alt="3 estrelas"></li>
                                <li><img class="estrela estrela-4" onmouseover="javascript: coloreEstrela(<?= $contador ?>,4);" estrela="4" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Boa" alt="4 estrelas"></li>
                                <li><img class="estrela estrela-5" onmouseover="javascript: coloreEstrela(<?= $contador ?>,5);" estrela="5" src="<?= $loja['site'] ?>imagens/avaliacao-estrela.png" title="Muito boa" alt="5 estrelas"></li>
                            </ul>
                            <div class="form-group mt-3">
                                <textarea id="observacoes-loja" name="<?= $avaliacao['identificador'] ?>-observacao" class="form-control observacoes" cols="30" rows="5" placeholder="Alguma observação para que possamos melhorar?" minlength="30" required></textarea>
                                <small><b>MÍNIMO DE CARACTERES: 30 - TOTAL: <span id="observacoes-caracteres">0</span></b></small>
                            </div>
                        </div>
                        <input type="hidden" id="nota-<?= $contador ?>" name="<?= $avaliacao['identificador'] ?>-nota">

                    <?php

                }
                
            } 
            
            ?>
            
            <div class="form-group mt-3">               
                <input class="btn-escuro" type="submit" value="Enviar" onclick="javascript: enviaPesquisaSatisfacao();">
            </div>

            <input type="hidden" name="identificador-pedido" value="<?= $identificador_pedido ?>">

        </form>

        
    <?php } else { ?>
        
        <div class="row">
            <div class="col-12 col-md-4 col-lg-3 col-xl-2">
                <a class="btn-escuro" href="<?= $loja['site'] ?>categoria/todas/0">Ver produtos</a></li>
            </div>  
        </div>   

    <?php } ?>

</section>

<!--SCRIPTS-->
<script type="text/javascript" src="<?= $loja['site'] ?>modulos/avaliacao/js/scripts.js"></script>
