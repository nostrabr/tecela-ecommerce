<?php

//INICIA A SESSÃO
session_start();

$identificador_pedido = trim(strip_tags(filter_input(INPUT_POST, "identificador-pedido", FILTER_SANITIZE_STRING)));   
$visitante            = filter_var($_SESSION['visitante']);
unset($_SESSION['RETORNO']); 

if(mb_strlen($identificador_pedido) == 32 & mb_strlen($visitante) == 32){

    include_once '../../../bd/conecta.php';

    //BUSCA PEDIDO
    $busca_pedido = mysqli_query($conn, "SELECT id FROM pedido WHERE identificador = '$identificador_pedido'");

    //SE NÃO FOR UM PEDIDO VÁLIDO, MANDA PRA HOME
    if(mysqli_num_rows($busca_pedido) == 0){
        echo "<script>location.href='/';</script>";
    } else {
        
        $pedido = mysqli_fetch_array($busca_pedido);

        //BUSCA AVALIAÇÔES
        $avaliacoes = mysqli_query($conn, "SELECT identificador FROM avaliacao WHERE status = 0 AND id_pedido = ".$pedido['id']." ORDER BY id DESC");

        if(mysqli_num_rows($avaliacoes) == 0){
            echo "<script>location.href='/';</script>";
        } else {

            while($avaliacao = mysqli_fetch_array($avaliacoes)){ 
                
                $observacao = trim(strip_tags(filter_input(INPUT_POST, $avaliacao['identificador']."-observacao", FILTER_SANITIZE_STRING)));   
                $nota       = trim(strip_tags(filter_input(INPUT_POST, $avaliacao['identificador']."-nota", FILTER_SANITIZE_STRING)));   

                if($nota != ''){
                    mysqli_query($conn, "UPDATE avaliacao SET status = 1, nota = '$nota', comentario = '$observacao', data_cadastro = NOW() WHERE identificador = '".$avaliacao['identificador']."'");
                }

            }
            
            //PREENCHE A SESSION DE RETORNO COM SUCESSO
            $_SESSION['RETORNO'] = array(
                'ERRO'   => false,
                'STATUS' => 'SUCESSO'
            );  

            ?>

            <form id="form-nova-avaliacao" style="display: none;" action="../../../modulos/envio-email/index.php" method="POST">        
                <input type="text" name="tipo-envio" value="nova-avaliacao">     
                <input type="text" name="identificador-pedido" value="<?= $identificador_pedido ?>">
            </form>

            <?php
            
            //REDIRECIONA PARA A TELA DE CADASTRO
            echo "<script>document.getElementById('form-nova-avaliacao').submit();</script>";

        }

    }

    
    include_once '../../../bd/desconecta.php';

} else {
        
    //REDIRECIONA PARA A TELA DE LOGIN
    echo "<script>location.href='/';</script>";

}