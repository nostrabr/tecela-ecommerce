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
            $dados[] = array(
                "status" => "SESSAO INVALIDA"
            );
            echo json_encode($dados);
            
        } else {
            
            //REDIRECIONA PARA A TELA DE LOGIN
            echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";
            
        }

    } else {

        include_once '../../../../bd/conecta.php';            
        
        $data = array();
	
	    $sql = "SELECT * FROM categoria";
    
        $res = mysqli_query($conn, $sql);

		//iterate on results row and create new index array of data
		while( $row = mysqli_fetch_assoc($res) ) { 
			$tmp = array();
			$tmp['id'] = $row['id'];
			$tmp['text'] = $row['nome'];
			$tmp['parent_id'] = $row['pai'];
			array_push($data, $tmp); 
		}
		$itemsByReference = array();

        // Build array of item references:
        foreach($data as $key => &$item) {
            $itemsByReference[$item['id']] = &$item;
            // Children array:
            $itemsByReference[$item['id']]['nodes'] = array();
        }

        // Set items as children of the relevant parent item.
        foreach($data as $key => &$item)  {
            if($item['parent_id'] && isset($itemsByReference[$item['parent_id']])) {
                $itemsByReference [$item['parent_id']]['nodes'][] = &$item;
            }
        }
        
        // Remove items that were added to parents elsewhere:
        foreach($data as $key => &$item) {
            if(empty($item['nodes'])) {
                unset($item['nodes']);
            }
            if($item['parent_id'] && isset($itemsByReference[$item['parent_id']])) {
                unset($data[$key]);
            }
        }

        //REINDEXA
        $data = array_values($data);

        // Encode:
        echo json_encode($data);

        include_once '../../../../bd/desconecta.php';

        
    }
    
} else {
    
    //VERIFICA SE VEIO DO AJAX
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

        $dados[] = array(
            "status" => "SESSAO INVALIDA"
        );
        echo json_encode($dados);

    } else {

        //REDIRECIONA PARA A TELA DE LOGIN
        echo "<script>location.href='../../../modulos/login/php/encerra-sessao.php';</script>";

    }
        
}