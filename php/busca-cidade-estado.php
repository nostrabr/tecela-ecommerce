<?php

header('Access-Control-Allow-Origin: *');

$uf = trim(strip_tags(filter_input(INPUT_POST, "uf", FILTER_SANITIZE_STRING)));

if(!empty($uf)){

    include_once '../bd/conecta.php';

    $cidades = mysqli_query($conn,"SELECT id, nome FROM cidade WHERE id_estado = ".$uf);

    while ($cidade = mysqli_fetch_array($cidades)) {
        ?><option value="<?= $cidade["id"] ?>"><?= $cidade["nome"] ?></option><?php
    }

    include_once '../bd/desconecta.php';

}