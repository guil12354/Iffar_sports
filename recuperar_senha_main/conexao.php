<?php

function conectar() {
    $conexao = new mysqli(
        "localhost",
        "root",
        "",
        "iffar_sports3"
    );

    if ($conexao->connect_error) {
        die("Erro ao conectar ao banco de dados.");
    }

    $conexao->set_charset("utf8mb4");

    return $conexao;
}