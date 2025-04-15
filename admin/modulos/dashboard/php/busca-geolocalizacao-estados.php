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

        session_write_close();
        
        $data_inicio = filter_input(INPUT_POST, 'data-inicio', FILTER_SANITIZE_STRING);  
        $data_fim    = filter_input(INPUT_POST, 'data-fim', FILTER_SANITIZE_STRING);  

        if(mb_strlen($data_inicio) == 10 & mb_strlen($data_fim) == 10){

            include_once '../../../../bd/conecta.php';

            $array_nomes_ufs        = array('AC','AL','AP','AM','BA','CE','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO','DF','NI');            
            $array_nomes_estados    = array('Acre','Alagoas','Amapá','Amazonas','Bahia','Ceará','Espírito Santo','Goiás','Maranhão','Mato Grosso','Mato Grosso do Sul','Minas Gerais','Pará','Paraíba','Paraná','Pernambuco','Piauí','Rio de Janeiro','Rio Grande do Norte','Rio Grande do Sul','Rondônia','Roraima','Santa Catarina','São Paulo','Sergipe','Tocantins','Distrito Federal','Não Identificado');
            $array_estados          = [];
            
            $busca_estados = mysqli_query($conn, "
                SELECT uf, COUNT(id) AS total 
                FROM visita
                WHERE data_cadastro BETWEEN '$data_inicio 00:00:00' AND '$data_fim 23:59:59' AND tipo = 'VISITA' AND pais = 'BR'
                GROUP BY uf
                ORDER BY total DESC
            ");

            while($estado = mysqli_fetch_array($busca_estados)){
                
                $uf     = $estado['uf'];
                if($uf == ''){ $uf = 'NI'; }

                $array_estados[] = array(
                    "uf"     => $estado['uf'],
                    "estado" => str_replace($array_nomes_ufs, $array_nomes_estados, $uf),
                    "total"  => intval($estado['total'])
                );
            }

            echo json_encode($array_estados);
                
            include_once '../../../../bd/desconecta.php';

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
