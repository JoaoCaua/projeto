<?php

$apiKey = "6bbcf38a59efcea1b1f92c009e2faf5e";

$categoria = $_GET['categoria'] ?? 'popular';
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

if ($pagina < 1) {
    $pagina = 1;
}

if (!empty($_GET['busca'])) {

    $busca = urlencode($_GET['busca']);

    $url = "https://api.themoviedb.org/3/search/movie?api_key=$apiKey&language=pt-BR&query=$busca&page=$pagina";

} else {

    $endpoint = ($categoria == 'top')
        ? 'top_rated'
        : 'popular';

    $url = "https://api.themoviedb.org/3/movie/$endpoint?api_key=$apiKey&language=pt-BR&page=$pagina";
}

$json = file_get_contents($url);
$dados = json_decode($json, true);

$filmes = $dados['results'] ?? [];

$totalPaginas = isset($dados['total_pages'])
    ? min($dados['total_pages'], 500)
    : 1;

$buscaAtual = $_GET['busca'] ?? '';

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Buscar Filmes - Cinemark</title>

<link rel="stylesheet" href="style.css">
<style>

form{
    display:flex;
    gap:10px;
    margin-bottom:20px;
}

form input{
    flex:1;
    padding:10px;
}

form button{
    padding:10px 20px;
    cursor:pointer;
}

.abas{
    margin:20px 0;
    display:flex;
    gap:10px;
}

.abas a{
    text-decoration:none;
    padding:10px 15px;
    border:1px solid #ddd;
    border-radius:8px;
    color:#333;
    background:#f5f5f5;
}

.abas a:hover{
    background:#e9e9e9;
}

.abas .ativa{
    background:#333;
    color:#fff;
}

.resultados{
    display:grid;
    grid-template-columns:repeat(5, 1fr);
    gap:30px;
    margin-top:20px;
}

.card{
    border:1px solid #ddd;
    border-radius:10px;
    overflow:hidden;
    text-align:center;
    text-decoration:none;
    color:inherit;
    background:white;
    transition:0.2s;
}

.card:hover{
    transform:translateY(-4px);
    box-shadow:0 4px 12px rgba(0,0,0,.15);
}

.card img{
    width:100%;
    height:250px;
    object-fit:cover;
}

.card h3{
    padding:10px;
    font-size:14px;
    min-height:50px;
}

.poster-container{
    position:relative;
}

.nota{
    position:absolute;
    top:10px;
    right:10px;
    background:#111;
    color:white;
    padding:6px 10px;
    border-radius:20px;
    font-size:14px;
    font-weight:bold;
    box-shadow:0 2px 5px rgba(0,0,0,.3);
}

.paginacao{
    margin:40px 0;
    display:flex;
    justify-content:center;
    gap:8px;
    flex-wrap:wrap;
}

.paginacao a{
    padding:10px 15px;
    border:1px solid #ddd;
    border-radius:8px;
    text-decoration:none;
    color:#333;
}

.paginacao a:hover{
    background:#f5f5f5;
}

.paginacao .ativa{
    background:#333;
    color:white;
    border-color:#333;
}

.card h3{
    padding:12px 12px 5px;
    font-size:15px;
    margin:0;
    min-height:60px;
    display:flex;
    align-items:center;
    justify-content:center;
}

.ano{
    padding-bottom:10px;
    font-size:12px;
}

</style>

</head>

<body>

<div class="layout">

    <div class="sidebar">

        <h2>Cinemark</h2>

        <a href="buscar_filmes.php" class="ativo">
            🔍 Buscar
        </a>

        <a href="listafilmes.php">
            📚 Biblioteca
        </a>

    </div>

    <div class="conteudo">

        <h1>Buscar Filmes</h1>

        <form method="GET">

            <input
                type="text"
                name="busca"
                placeholder="Digite o nome do filme"
                value="<?= htmlspecialchars($buscaAtual) ?>"
            >

            <button type="submit">
                Pesquisar
            </button>

        </form>

        <div class="abas">

            <a href="?categoria=popular"
               class="<?= $categoria == 'popular' ? 'ativa' : '' ?>">
                🔥 Populares
            </a>

            <a href="?categoria=top"
               class="<?= $categoria == 'top' ? 'ativa' : '' ?>">
                ⭐ Mais Bem Avaliados
            </a>

        </div>

        <div class="resultados">

        <?php foreach($filmes as $filme): ?>

            <?php
            $poster = !empty($filme['poster_path'])
                ? "https://image.tmdb.org/t/p/w300".$filme['poster_path']
                : "https://via.placeholder.com/300x450?text=Sem+Capa";
            ?>

            <?php
            $ano = !empty($filme['release_date'])
                ? date('Y', strtotime($filme['release_date']))
                : '----';
            ?>

            <a class="card" href="selecionar_filme.php?id=<?= $filme['id'] ?>">

                <div class="poster-container">

                    <img src="<?= $poster ?>" alt="<?= htmlspecialchars($filme['title']) ?>">

                    <span class="nota">
                        ⭐ <?= number_format($filme['vote_average'], 1) ?>
                    </span>

                </div>

                <h3><?= htmlspecialchars($filme['title']) ?></h3>

                <div class="ano">
                    <?= $ano ?>
                </div>

            </a>

        <?php endforeach; ?>

        </div>

        <?php if($totalPaginas > 1): ?>

        <div class="paginacao">

            <?php if($pagina > 1): ?>

                <a href="?categoria=<?= $categoria ?>&busca=<?= urlencode($buscaAtual) ?>&pagina=<?= $pagina - 1 ?>">
                    ← Anterior
                </a>

            <?php endif; ?>

            <?php

            $inicio = max(1, $pagina - 2);
            $fim = min($totalPaginas, $pagina + 2);

            for($i = $inicio; $i <= $fim; $i++):

            ?>

                <a
                    href="?categoria=<?= $categoria ?>&busca=<?= urlencode($buscaAtual) ?>&pagina=<?= $i ?>"
                    class="<?= $i == $pagina ? 'ativa' : '' ?>"
                >
                    <?= $i ?>
                </a>

            <?php endfor; ?>

            <?php if($pagina < $totalPaginas): ?>

                <a href="?categoria=<?= $categoria ?>&busca=<?= urlencode($buscaAtual) ?>&pagina=<?= $pagina + 1 ?>">
                    Próxima →
                </a>

            <?php endif; ?>

        </div>

        <?php endif; ?>

    </div>

</div>

</body>
</html>
