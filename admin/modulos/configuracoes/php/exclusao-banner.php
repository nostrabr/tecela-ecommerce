<?php


//INICIA A SESSÃO
session_start();

//VALIDA A SESSÃO
if(isset($_SESSION["DONO"])){
    
    //GERA O TOKEN
    $token_usuario = md5('18f80a949b97de988368995777c5aaea'.$_SERVER['REMOTE_ADDR']);
    
    //SE FOR DIFERENTE
    if($_SESSION["DONO"] !== $token_usuario){

        //VERIFICA SE VEIO DO AJAX
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            
            //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
            echo "SESSAO INVALIDA";
            
        } else {
            
            //REDIRECIONA PARA A TELA DE LOGIN
            echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";
            
        }

    } else {
        
        //RECEBE OS DADOS DO FORM
        $id = filter_input(INPUT_POST, 'id');
        $nivel_usuario = filter_var($_SESSION['nivel']);

        //CONFIRMA SE VEIO TUDO PREENCHIDO
        if(!empty($id) & $nivel_usuario != 'U'){

            include_once '../../../../bd/conecta.php';

            //BUSCA A ORDEM DO BANNER À SER EXCLUÍDO
            $busca_ordem = mysqli_query($conn, "SELECT ordem, imagem_desktop, imagem_mobile FROM banner WHERE id = '$id'");
            $banner = mysqli_fetch_array($busca_ordem);
            $ordem_livre = $banner["ordem"];

            //BUSCA O FIM DA ORDEM
            $consulta_qtde_ordem = mysqli_query($conn, "SELECT COUNT(id) AS total_ordem FROM banner"); 
            $qtde_ordens = mysqli_fetch_array($consulta_qtde_ordem);
            $ordem_final = $qtde_ordens["total_ordem"];
                
            //DELETA A IMAGEM DA PASTA BANNERS
            unlink("../../../../imagens/banners/original/".$banner["imagem_desktop"]);
            unlink("../../../../imagens/banners/pequena/".$banner["imagem_desktop"]);
            unlink("../../../../imagens/banners/original/".$banner["imagem_mobile"]);
            unlink("../../../../imagens/banners/pequena/".$banner["imagem_mobile"]);

            //EXCLUI DO BANCO
            mysqli_query($conn, "DELETE FROM banner WHERE id = '$id'");

            $contador = $ordem_livre;

            while($contador < $ordem_final){
                
                $proximo_da_ordem = $contador + 1;
                    
                $busca_id = mysqli_query($conn, "SELECT id FROM banner WHERE ordem = '$proximo_da_ordem'");
                $banner_id = mysqli_fetch_array($busca_id);
                $id_proximo = $banner_id['id'];
                
                mysqli_query($conn, "UPDATE banner SET ordem = '$contador' WHERE id = '$id_proximo'");
                
                $contador++;
                
            }
            
            include_once '../../../../bd/desconecta.php';

            //REDIRECIONA PARA A TELA DE USUÁRIOS
            echo "OK";

        } else {
            
            //REDIRECIONA PARA A TELA DE LOGIN
            echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";
            
        }

    }
    
} else {
    
    //VERIFICA SE VEIO DO AJAX
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

        //RETORNA PRO AJAX QUE A SESSÃO É INVÁLIDA
        echo "SESSAO INVALIDA";

    } else {

        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";

    }
        
}

