<?php

//CONECTA COM O BANCO DE DADOS

//SETA OS DADOS DO SERVIDOR
$servername = '31.97.12.109';
$database = 'tebanco';
$username = 'teuser';
$password = '@Mvp258080nostra';

//CRIA A CONEXÃO
$conn = mysqli_connect($servername, $username, $password, $database);

//SETA O CHARSET COMO UTF8
mysqli_set_charset($conn, 'utf8');

//VERIFICA CONEXÃO
if (!$conn) {
    die("Conexão com o BD falhou: " . mysqli_connect_error());
}