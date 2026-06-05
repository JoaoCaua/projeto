<?php
require 'conexao.php';

$nome = $_GET['nome'] ?? '';
$status = $_GET['status'] ?? '';

$sql = "SELECT * FROM filmes WHERE 1=1";
$params = [];

if (!empty($nome)) {
    $sql .= " AND nome LIKE ?";
    $params[] = "%$nome%";
}

if (!empty($status)) {
    $sql .= " AND status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY nome";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$filmes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Biblioteca de Filmes - Cinemark</title>

<link rel="stylesheet" href="style.css">
<style>

.form-filtros{
    display:flex;
    align-items:flex-end;
    gap:15px;
    flex-wrap:wrap;
    background:#f8f8f8;
    padding:15px;
    border:1px solid #ddd;
    border-radius:10px;
    margin-bottom:20px;
}

.form-filtros label{
    display:flex;
    flex-direction:column;
    gap:5px;
    font-size:14px;
    font-weight:bold;
    color:#333;
}

.form-filtros input,
.form-filtros select{
    padding:10px;
    border:1px solid #ccc;
    border-radius:8px;
    min-width:180px;
    font-size:14px;
}

.form-filtros input:focus,
.form-filtros select:focus{
    outline:none;
    border-color:#333;
}

.botao{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:10px 15px;
    border:none;
    border-radius:8px;
    text-decoration:none;
    color:#fff;
    font-size:14px;
    font-weight:bold;
    cursor:pointer;
}

.filtrar{
    background:#3b82f6;
}

.filtrar:hover{
    background:#2563eb;
}

.limpar{
    background:#94a3b8;
}

.limpar:hover{
    background:#64748b;
}

.editar{
    background:#ffc107;
    color:#000;
}

.excluir{
    background:#dc3545;
    color:#fff;
}

.tabela-filmes{
    width:100%;
    background:#fff;
    border-collapse:collapse;
    text-align:center;
    border-radius:10px;
    overflow:hidden;
}

.tabela-filmes th{
    background:#111;
    color:#fff;
    padding:12px;
}

.tabela-filmes td{
    padding:12px;
    border-bottom:1px solid #eee;
}

.tabela-filmes tr:hover{
    background:#fafafa;
}

.nome-filme{
    font-weight:bold;
}

.nota-badge{
    background:#111;
    color:#fff;
    padding:5px 10px;
    border-radius:20px;
    font-weight:bold;
}

.genero{
    display:inline-block;
    background:#f1f1f1;
    padding:4px 10px;
    border-radius:20px;
    margin:2px;
    font-size:12px;
}

.status{
    padding:6px 12px;
    border-radius:20px;
    font-size:13px;
    font-weight:bold;
}

.status.assistido{
    background:#d4edda;
    color:#155724;
}

.status.quero{
    background:#fee2e2;
    color:#b91c1c;
}

</style>

</head>
<body>

<div class="layout">

    <div class="sidebar">

        <h2>Cinemark</h2>

        <a href="buscar_filmes.php">
            🔍 Buscar
        </a>

        <a href="listafilmes.php" class="ativo">
            📚 Biblioteca
        </a>

    </div>

    <div class="conteudo">

        <h1>Minha Biblioteca</h1>

        <br><br>

        <form method="get" class="form-filtros">

            <label>
                Nome:
                <input type="text" name="nome" value="<?= htmlspecialchars($nome) ?>">
            </label>

            <label>
                Status:
                <select name="status">
                    <option value="">Todos</option>
                    <option value="Assistido" <?= $status == 'Assistido' ? 'selected' : '' ?>>Assistido</option>
                    <option value="Quero assistir" <?= $status == 'Quero assistir' ? 'selected' : '' ?>>Quero assistir</option>
                </select>
            </label>

            <button type="submit" class="botao filtrar">Filtrar</button>

            <a href="listafilmes.php" class="botao limpar">Limpar Filtros</a>

        </form>

        <table class="tabela-filmes">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Nota</th>
                    <th>Gêneros</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>

                <?php if (!empty($filmes)): ?>
                <?php foreach ($filmes as $f): ?>

                <tr>

                    <td><?= $f['id'] ?></td>

                    <td class="nome-filme">
                        <?= htmlspecialchars($f['nome']) ?>
                    </td>

                    <td>
                        <span class="nota-badge">
                            ⭐ <?= number_format($f['classificacao'], 1) ?>
                        </span>
                    </td>

                    <td>
                        <?php
                        $generos = explode(',', $f['generos']);
                        foreach ($generos as $genero):
                        ?>
                            <span class="genero">
                                <?= htmlspecialchars(trim($genero)) ?>
                            </span>
                        <?php endforeach; ?>
                    </td>

                    <td>

                        <?php if($f['status'] == 'Assistido'): ?>

                            <span class="status assistido">
                                ✔ Assistido
                            </span>

                        <?php else: ?>

                            <span class="status quero">
                                ❌ Quero assistir
                            </span>

                        <?php endif; ?>

                    </td>

                    <td>

                        <a
                            href="selecionar_filme.php?id=<?= $f['id'] ?>"
                            class="botao editar"
                        >
                            Editar
                        </a>

                        <a
                            href="excluir.php?id=<?= $f['id'] ?>"
                            class="botao excluir"
                            onclick="return confirm('Deseja excluir este filme?')"
                        >
                            Excluir
                        </a>

                    </td>

                </tr>

                <?php endforeach; ?>

                <?php else: ?>

                <tr>
                    <td colspan="6">
                        Nenhum filme encontrado.
                    </td>
                </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>