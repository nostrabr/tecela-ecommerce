<?php

//CONECTA COM O BANCO DE DADOS

//SETA OS DADOS DO SERVIDOR
$servername = '162.241.129.229';
$database = 'tecela_banco';
$username = 'tecela_admin';
$password = 'w1e2s3l4e5i6';

//CRIA A CONEXÃO
$conn = mysqli_connect($servername, $username, $password, $database);

//SETA O CHARSET COMO UTF8
mysqli_set_charset($conn, 'utf8');

//VERIFICA CONEXÃO
if (!$conn) {
    die("Conexão com o BD falhou: " . mysqli_connect_error());
}