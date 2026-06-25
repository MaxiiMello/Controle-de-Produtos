<?php
// ============================================
// 📁 editar_produto.php — Editar Produto (Moderno)
// ============================================
require '../src/config/conexao.php';
require '../src/functions/funcoes.php';

$conn = conectar();

$id = (int)($_GET['id'] ?? 0);
$produto = buscarProduto($conn, $id);

if (!$produto) {
    header('Location: produtos.php?erro=' . urlencode('Produto não encontrado.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome        = trim($_POST['nome'] ?? '');
    $categoria_id = $_POST['categoria_id'] ?: null;
    $preco       = str_replace(',', '.', $_POST['preco'] ?? 0);
    $quantidade  = (int)($_POST['quantidade'] ?? 0);
    $descricao   = trim($_POST['descricao'] ?? '');

    if ($nome && $preco > 0) {
        editarProduto($conn, $id, $nome, $categoria_id, $preco, $quantidade, $descricao);
        header('Location: produtos.php?msg=' . urlencode('Produto atualizado com sucesso!'));
        exit;
    }
    $erro = 'Preencha nome e preço corretamente.';
}

$categorias = listarCategoriasAtivas($conn);
$titulo = 'Editar Produto';
require '../src/includes/header.php';
?>

<div class="page-header">
    <h1>✏️ Editar Produto #<?= $id ?></h1>
    <p>Atualize as informações do produto.</p>
</div>

<div class="card fade-in" style="max-width:640px;">
    <div class="card-header">
        <h2>📦 <?= htmlspecialchars($produto['nome']) ?></h2>
    </div>
    <div class="card-body">
        <?php if (isset($erro)): ?>
            <div class="alert alert-error">❌ <?= $erro ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nome do Produto</label>
                <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($produto['nome']) ?>" required>
            </div>

            <div class="form-group">
                <label>Categoria</label>
                <select name="categoria_id" class="form-control">
                    <option value="">Sem categoria</option>
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $c['id'] == $produto['categoria_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Preço</label>
                    <input type="number" name="preco" class="form-control" step="0.01" value="<?= $produto['preco'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Quantidade</label>
                    <input type="number" name="quantidade" class="form-control" value="<?= $produto['quantidade'] ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Descrição</label>
                <textarea name="descricao" class="form-control" rows="3"><?= htmlspecialchars($produto['descricao'] ?? '') ?></textarea>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-success">💾 Salvar Alterações</button>
                <a href="produtos.php" class="btn btn-ghost">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require '../src/includes/footer.php'; ?>