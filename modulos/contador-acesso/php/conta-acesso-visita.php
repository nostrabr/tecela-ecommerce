<?php

session_start();

$visitante = filter_var($_SESSION['visitante']);
$width     = filter_input(INPUT_POST,'width',FILTER_SANITIZE_NUMBER_INT);
$height    = filter_input(INPUT_POST,'height',FILTER_SANITIZE_NUMBER_INT);

if(mb_strlen($visitante) == 32){
   
    include_once '../../../bd/conecta.php';

    //BUSCA O CARRINHO
    $busca_carrinho = mysqli_query($conn, "SELECT id FROM carrinho WHERE identificador = '".$visitante."'");

    //SE NÃO EXISTIR GERA UM CARRINHO COMO IDENTIFICADOR A SESSÃO DO VISITANTE PARA PODER CONTAR TODAS AS VISITAS DO SITE E CADASTRA OS DADOS DE GEOLOCALIZAÇÃO NA TABELA VISITANTE
    if(mysqli_num_rows($busca_carrinho) == 0){   

        //VERIFICA O DISPOSITIVO
        $dispositivo = 'Desktop';
        $user_agents = array("iPhone","iPad","Android","webOS","BlackBerry","iPod","Symbian","IsGeneric");
        foreach($user_agents as $user_agent){
            if (strpos($_SERVER['HTTP_USER_AGENT'], $user_agent) !== FALSE) {
                $dispositivo = 'Mobile';
                break;
            }
        }

        if($width <= $height){
            $dispositivo = 'Mobile';
        }
        
        //CLASSE QUE BUSCA A LOCALIZAÇÃO DO VISITANTE
        require_once('geoplugin.class.php');

        $geoplugin = new geoPlugin(); 
        $geoplugin->locate();

        $ip             = $geoplugin->ip;
        $cidade         = $geoplugin->city;
        $uf             = $geoplugin->regionCode;
        $pais           = $geoplugin->countryCode;
        $resolucao_tela = $width.'x'.$height;

        mysqli_query($conn, "INSERT INTO carrinho (identificador) VALUES ('".$visitante."')");
        mysqli_query($conn, "INSERT INTO visita (tipo, ip, cidade, uf, pais, resolucao_tela, dispositivo, visitante) VALUES ('VISITA','$ip','$cidade','$uf','$pais','$resolucao_tela','$dispositivo','$visitante')");
    
    }      
       
    include_once '../../../bd/desconecta.php';    

}