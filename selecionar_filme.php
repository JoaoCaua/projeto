<?php

require 'conexao.php';

$apiKey = "6bbcf38a59efcea1b1f92c009e2faf5e";

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Filme não encontrado.");
}

$url = "https://api.themoviedb.org/3/movie/$id?api_key=$apiKey&language=pt-BR&append_to_response=credits,watch/providers";

$json = file_get_contents($url);

$filme = json_decode($json, true);

$diretores = [];

if (!empty($filme['credits']['crew'])) {

    foreach ($filme['credits']['crew'] as $pessoa) {

        if (($pessoa['job'] ?? '') === 'Director') {
            $diretores[] = $pessoa['name'];
        }

    }

}

$plataformas = $filme['watch/providers']['results']['BR']['flatrate'] ?? [];

if (isset($_POST['status'])) {

    $status = $_POST['status'];

    $classificacao = round($filme['vote_average'], 1);

    $generos = implode(', ', array_column($filme['genres'], 'name'));

    $stmt = $pdo->prepare("SELECT id FROM filmes WHERE id = ?");
    $stmt->execute([$id]);
    $existe = $stmt->fetch();

    if ($existe) {

        $stmt = $pdo->prepare("
            UPDATE filmes
            SET nome = ?, status = ?, classificacao = ?, generos = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $filme['title'],
            $status,
            $classificacao,
            $generos,
            $id
        ]);

    } else {

        $stmt = $pdo->prepare("
            INSERT INTO filmes (id, nome, status, classificacao, generos)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $id,
            $filme['title'],
            $status,
            $classificacao,
            $generos
        ]);
    }

    header("Location: listafilmes.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($filme['title']) ?> - Cinemark</title>

<link rel="stylesheet" href="style.css">

<style>

.card-filme{
    display:flex;
    gap:25px;
    margin-top:20px;
}

.card-filme img{
    width:280px;
    border-radius:10px;
}

.info h1{
    margin-bottom:10px;
}

.info p{
    margin:6px 0;
}

.badge{
    display:inline-block;
    background:#eee;
    padding:4px 8px;
    border-radius:6px;
    margin:3px;
    font-size:13px;
}

.status-box{
    margin-top:20px;
}

button{
    padding:10px 15px;
    border:none;
    border-radius:6px;
    cursor:pointer;
    margin-right:10px;
}

.assistido{
    background:#28a745;
    color:white;
}

.quero{
    background:#007bff;
    color:white;
}

.voltar{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:10px 16px;
    background:#333;
    color:white;
    text-decoration:none;
    border-radius:8px;
    font-size:14px;
    font-weight:bold;
    margin-bottom:25px;
}

.voltar:hover{
    background:#222;
}

</style>

</head>
<body>

<div class="layout">

    <div class="sidebar">
        <h2>Cinemark</h2>
        <a href="buscar_filmes.php">🔍 Buscar</a>
        <a href="listafilmes.php">📚 Biblioteca</a>
    </div>

    <div class="conteudo">

        <a href="javascript:history.back()" class="voltar">← Voltar</a>

        <div class="card-filme">

            <img src="https://image.tmdb.org/t/p/w300<?= $filme['poster_path'] ?>">

            <div class="info">

                <h1><?= htmlspecialchars($filme['title']) ?></h1>

                <p><strong>Nota:</strong> <?= number_format($filme['vote_average'], 1) ?>/10 (<?= number_format($filme['vote_count'], 0, ',', '.') ?> avaliações)</p>

                <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($filme['release_date'])) ?></p>

                <p><strong>Duração:</strong> <?= $filme['runtime'] ?> min</p>

                <p><strong>Gêneros:</strong></p>

                <?php foreach ($filme['genres'] as $g): ?>
                    <span class="badge"><?= $g['name'] ?></span>
                <?php endforeach; ?>

                <p><strong>Diretor(es):</strong></p>

                <?php if (!empty($diretores)): ?>
                    <?php foreach ($diretores as $d): ?>
                        <span class="badge"><?= htmlspecialchars($d) ?></span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span>Não informado</span>
                <?php endif; ?>

                <p><strong>Onde assistir (BR):</strong></p>

                <?php if (!empty($plataformas)): ?>
                    <?php foreach ($plataformas as $p): ?>
                        <span class="badge"><?= htmlspecialchars($p['provider_name']) ?></span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span>Indisponível</span>
                <?php endif; ?>

                <p><strong>Sinopse:</strong></p>
                <p><?= htmlspecialchars($filme['overview']) ?></p>

                <form method="POST" class="status-box">

                    <button type="submit" name="status" value="Assistido" class="assistido">
                        ✔ Assistido
                    </button>

                    <button type="submit" name="status" value="Quero assistir" class="quero">
                        👀 Quero assistir
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

</body>
</html>