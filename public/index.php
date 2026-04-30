<?php

require '../src/config/conexao.php';
require '../src/functions/funcoes.php';

$conn = conectar();

if ($_POST) {
    if (!empty($_POST['nome']) && !empty($_POST['preco']) && !empty($_POST['quantidade'])) {
        inserirProduto($conn, $_POST['nome'], $_POST['categoria'], $_POST['preco'], $_POST['quantidade'], $_POST['descricao']);
    }
}

if (isset($_GET['ativar'])) {
    ativarProduto($conn, $_GET['ativar']);
}

if (isset($_GET['desativar'])) {
    desativarProduto($conn, $_GET['desativar']);
}

if (isset($_GET['deletar'])) {
    deletarProduto($conn, $_GET['deletar']);
}

$produtos = listarProdutos($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Controle de Produtos</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        input, select, textarea { padding: 5px; margin: 5px 0; }
        button { padding: 5px 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
        a { margin-right: 5px; padding: 3px 8px; background-color: #2196F3; color: white; text-decoration: none; }
    </style>
</head>
<body>
    <h1>Controle de Produtos</h1>

    <h2>Adicionar Produto</h2>
    <form method="POST">
        <input type="text" name="nome" placeholder="Nome" required>
        <select name="categoria" required>
            <option>Eletrônicos</option>
            <option>Roupas</option>
            <option>Alimentos</option>
            <option>Móveis</option>
            <option>Outros</option>
        </select>
        <input type="number" name="preco" placeholder="Preço" step="0.01" required>
        <input type="number" name="quantidade" placeholder="Quantidade" required>
        <textarea name="descricao" placeholder="Descrição" rows="2"></textarea>
        <button type="submit">Adicionar</button>
    </form>

    <h2>Produtos</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Categoria</th>
            <th>Preço</th>
            <th>Quantidade</th>
            <th>Descrição</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($produtos as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= $p['nome'] ?></td>
            <td><?= $p['categoria'] ?></td>
            <td><?= $p['preco'] ?></td>
            <td><?= $p['quantidade'] ?></td>
            <td><?= $p['descricao'] ?></td>
            <td><?= $p['status'] ?></td>
            <td>
                <?php if ($p['status'] === 'Ativo'): ?>
                    <a href="?desativar=<?= $p['id'] ?>">Desativar</a>
                <?php else: ?>
                    <a href="?ativar=<?= $p['id'] ?>">Ativar</a>
                <?php endif; ?>
                <a href="?deletar=<?= $p['id'] ?>" onclick="return confirm('Deletar?')">Deletar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>