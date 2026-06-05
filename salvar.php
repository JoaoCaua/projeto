<?php
require 'conexao.php';

$id = $_POST['id'] ?? '';
$nome = $_POST['nome'] ?? '';
$classificacao = $_POST['classificacao'] ?? '';
$status = $_POST['status'] ?? '';

if ($id) {
    // Atualizar
    $sql = "UPDATE filmes 
            SET nome = ?, classificacao = ?, status = ? 
            WHERE id = ?";

    $pdo->prepare($sql)->execute([
        $nome,
        $classificacao,
        $status,
        $id
    ]);

} else {
    // Inserir
    $sql = "INSERT INTO filmes 
            (nome, classificacao, status) 
            VALUES (?, ?, ?)";

    $pdo->prepare($sql)->execute([
        $nome,
        $classificacao,
        $status
    ]);
}

header("Location: listafilmes.php");
exit;