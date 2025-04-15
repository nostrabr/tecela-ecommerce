<?php 
        
//RECEBE OS DADOS DO FORM
$cep = preg_replace("/[^0-9]/",'', trim(strip_tags(filter_input(INPUT_POST, "cep", FILTER_SANITIZE_STRING))));

if(!empty($cep)){

    $caracteres = array(".", "-");
    $cep = str_replace($caracteres, "", $cep);

    $url = "http://viacep.com.br/ws/$cep/xml/";
    
    $endereco = simplexml_load_file($url);

    if(!isset($endereco->erro)){
                
        $logradouro = $endereco->logradouro;
        $bairro = $endereco->bairro;
        $municipio = $endereco->localidade;
        $uf = $endereco->uf;

        include_once '../bd/conecta.php';

        $busca_uf = mysqli_query($conn,"SELECT id FROM estado WHERE sigla = '".mb_strtoupper($uf)."'");
        $sql_uf = mysqli_fetch_array($busca_uf);
        
        $busca_cidade = mysqli_query($conn,"SELECT id FROM cidade WHERE nome = '".$municipio."'");
        $sql_cidade = mysqli_fetch_array($busca_cidade);

        include_once '../bd/desconecta.php';

        $dados[] = array(
            "logradouro" => $logradouro,
            "bairro" => $bairro,
            "municipio" => $sql_cidade["id"],
            "uf" => $sql_uf['id'],
        );
        
        echo json_encode($dados);

    } else {
        
        $dados[] = "CEP NÃO ENCONTRADO";
        echo json_encode($dados);

    }

} else {
        
    $dados[] = "CEP NÃO ENCONTRADO";
    echo json_encode($dados);
    
}