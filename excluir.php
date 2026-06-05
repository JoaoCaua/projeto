<?php
require 'conexao.php';

if (!empty($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM filmes WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}

header("Location: listafilmes.php");
exit;