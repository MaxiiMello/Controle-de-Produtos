<?php

require '../src/config/conexao.php';
require '../src/functions/funcoes.php';

$conn = conectar();

if ($_POST) {
    if (!empty($_POST['nome']) && !empty($_POST['preco']) && !empty($_POST['quantidade'])) {
        inserirProduto($conn, $_POST['nome'], $_POST['categoria'], $_POST['preco'], $_POST['quantidade'], $_POST['descricao']);
    }
}

if (isset($_POST['atualizar_estoque_id']) && isset($_POST['novo_estoque'])) {
    atualizarEstoque($conn, $_POST['atualizar_estoque_id'], $_POST['novo_estoque']);
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
$estoqueBaixo = listarProdutosEstoqueBaixo($conn, 5);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Controle de Produtos</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f6f8fb; color: #1f2937; }
        .container { max-width: 1200px; margin: 0 auto; }
        .card { background: white; border-radius: 12px; padding: 18px; box-shadow: 0 8px 30px rgba(15, 23, 42, 0.08); margin-bottom: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        table, th, td { border: 1px solid #dbe3ee; }
        th, td { padding: 10px; text-align: left; vertical-align: top; }
        th { background-color: #4e1ba1; color: white; }
        input, select, textarea { padding: 8px; margin: 5px 0; border: 1px solid #cbd5e1; border-radius: 8px; width: 100%; box-sizing: border-box; }
        button { padding: 8px 12px; background-color: #16a34a; color: white; border: none; cursor: pointer; border-radius: 8px; }
        button:hover { background-color: #a7c735; }
        a { margin-right: 5px; padding: 6px 10px; background-color: #2563eb; color: white; text-decoration: none; border-radius: 8px; display: inline-block; }
        .actions { display: flex; flex-wrap: wrap; gap: 6px; }
        .stock-form { display: flex; gap: 8px; align-items: center; }
        .stock-form input { width: 90px; margin: 0; }
        .low-stock { background: #fff7ed; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 999px; font-size: 12px; font-weight: bold; }
        .badge-low { background: #bb250a; color: white; }
        .badge-ok { background: #16a34a; color: white; }
        h1, h2 { margin-top: 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Controle de Produtos</h1>

        <div class="card">
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
        </div>

        <div class="card">
            <h2>Produtos com Estoque Baixo</h2>
            <p>Considerando estoque baixo como quantidade menor ou igual a 5.</p>
            <table>
                <tr>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Estoque</th>
                    <th>Status</th>
                </tr>
                <?php if (empty($estoqueBaixo)): ?>
                    <tr><td colspan="4">Nenhum produto com estoque baixo.</td></tr>
                <?php else: ?>
                    <?php foreach ($estoqueBaixo as $p): ?>
                    <tr class="low-stock">
                        <td><?= $p['nome'] ?></td>
                        <td><?= $p['categoria'] ?></td>
                        <td><span class="badge badge-low"><?= $p['quantidade'] ?></span></td>
                        <td><?= $p['status'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </div>

        <div class="card">
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
                    <th>Atualizar estoque</th>
                    <th>Ações</th>
                </tr>
                <?php foreach ($produtos as $p): ?>
                <tr class="<?= ((int)$p['quantidade'] <= 5) ? 'low-stock' : '' ?>">
                    <td><?= $p['id'] ?></td>
                    <td><?= $p['nome'] ?></td>
                    <td><?= $p['categoria'] ?></td>
                    <td><?= $p['preco'] ?></td>
                    <td>
                        <?= $p['quantidade'] ?>
                        <?php if ((int)$p['quantidade'] <= 5): ?>
                            <span class="badge badge-low">Baixo</span>
                        <?php else: ?>
                            <span class="badge badge-ok">OK</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $p['descricao'] ?></td>
                    <td><?= $p['status'] ?></td>
                    <td>
                        <form method="POST" class="stock-form">
                            <input type="hidden" name="atualizar_estoque_id" value="<?= $p['id'] ?>">
                            <input type="number" name="novo_estoque" min="0" value="<?= $p['quantidade'] ?>" required>
                            <button type="submit">Salvar</button>
                        </form>
                    </td>
                    <td>
                        <div class="actions">
                            <?php if ($p['status'] === 'Ativo'): ?>
                                <a href="?desativar=<?= $p['id'] ?>">Desativar</a>
                            <?php else: ?>
                                <a href="?ativar=<?= $p['id'] ?>">Ativar</a>
                            <?php endif; ?>
                            <a href="?deletar=<?= $p['id'] ?>" onclick="return confirm('Deletar?')">Deletar</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>