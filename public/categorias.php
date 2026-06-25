<?php
require '../src/config/conexao.php';
require '../src/functions/funcoes.php';
$conn = conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    if ($_POST['acao'] === 'inserir') { $nome = trim($_POST['nome'] ?? ''); $descricao = trim($_POST['descricao'] ?? '');
        if ($nome) { inserirCategoria($conn, $nome, $descricao); header('Location: categorias.php?msg=Categoria cadastrada!'); exit; } }
    if ($_POST['acao'] === 'editar') { $id = (int)($_POST['id'] ?? 0); $nome = trim($_POST['nome'] ?? ''); $descricao = trim($_POST['descricao'] ?? '');
        if ($id && $nome) { editarCategoria($conn, $id, $nome, $descricao); header('Location: categorias.php?msg=Categoria atualizada!'); exit; } }
}
if (isset($_GET['deletar'])) { $id = (int)$_GET['deletar']; $total = contarProdutosPorCategoria($conn, $id);
    if ($total > 0) { header('Location: categorias.php?erro=' . urlencode("Nao e possivel excluir: {$total} produto(s) vinculado(s).")); exit; }
    excluirCategoria($conn, $id); header('Location: categorias.php?msg=Categoria excluida.'); exit; }
if (isset($_GET['ativar'])) { ativarCategoria($conn, (int)$_GET['ativar']); header('Location: categorias.php?msg=Ativada.'); exit; }
if (isset($_GET['desativar'])) { desativarCategoria($conn, (int)$_GET['desativar']); header('Location: categorias.php?msg=Desativada.'); exit; }

$busca = trim($_GET['busca'] ?? '');
$statusFiltro = $_GET['status'] ?? '';
$ordenar = $_GET['ordenar'] ?? 'nome';
$ordem = $_GET['ordem'] ?? 'ASC';

$filtros = [];
if ($busca) $filtros['busca'] = $busca;
if ($statusFiltro) $filtros['status'] = $statusFiltro;
$filtros['ordenar'] = $ordenar;
$filtros['ordem'] = $ordem;

$categorias = filtrarCategorias($conn, $filtros);
$editando = isset($_GET['editar']) ? buscarCategoria($conn, (int)$_GET['editar']) : null;
$titulo = 'Categorias';
require '../src/includes/header.php';
?>

<div class="page-header"><h1>Categorias</h1><p>Organize seus produtos por categorias.</p></div>

<div class="action-bar">
    <form method="GET" class="filter-bar">
        <div class="search-box">
            <span class="search-icon"><?= icon('search', 16) ?></span>
            <input type="text" name="busca" class="form-control" placeholder="Buscar categoria..." value="<?= htmlspecialchars($busca) ?>" style="padding-left:36px;">
        </div>
        
        <select name="status" class="form-control" onchange="this.form.submit()">
            <option value="">Todos Status</option>
            <option value="Ativo" <?= $statusFiltro === 'Ativo' ? 'selected' : '' ?>>Ativo</option>
            <option value="Inativo" <?= $statusFiltro === 'Inativo' ? 'selected' : '' ?>>Inativo</option>
        </select>
        
        <input type="hidden" name="ordenar" value="<?= htmlspecialchars($ordenar) ?>">
        <input type="hidden" name="ordem" value="<?= htmlspecialchars($ordem) ?>">
        
        <button type="submit" class="btn btn-primary"><?= icon('filter', 16) ?> Filtrar</button>
        <?php if ($busca || $statusFiltro): ?>
            <a href="categorias.php" class="btn btn-ghost"><?= icon('x', 16) ?> Limpar</a>
        <?php endif; ?>
    </form>
</div>

<div class="grid-2">
    <div class="card fade-in">
        <div class="card-header"><h2><?= icon('plus', 16) ?> <?= $editando ? 'Editar Categoria' : 'Nova Categoria' ?></h2></div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="acao" value="<?= $editando ? 'editar' : 'inserir' ?>">
                <?php if ($editando): ?><input type="hidden" name="id" value="<?= $editando['id'] ?>"><?php endif; ?>
                <div class="form-group"><label>Nome</label><input type="text" name="nome" class="form-control" value="<?= $editando ? htmlspecialchars($editando['nome']) : '' ?>" required placeholder="Ex: Eletronicos"></div>
                <div class="form-group"><label>Descricao</label><textarea name="descricao" class="form-control" rows="2" placeholder="Descricao (opcional)"><?= $editando ? htmlspecialchars($editando['descricao'] ?? '') : '' ?></textarea></div>
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-success"><?= icon('save', 14) ?> <?= $editando ? 'Salvar' : 'Cadastrar' ?></button>
                    <?php if ($editando): ?><a href="categorias.php" class="btn btn-ghost"><?= icon('x', 14) ?> Cancelar</a><?php endif; ?>
                </div>
            </form>
        </div>
    </div>
    <div class="card fade-in">
        <div class="card-header"><h2><?= icon('tags', 16) ?> Categorias</h2><div class="card-actions"><span class="badge badge-neutral"><?= count($categorias) ?> registro(s)</span></div></div>
        <div class="card-body" style="padding:0;">
            <?php if (empty($categorias)): ?>
                <div class="empty-state"><?= icon('tags', 48) ?><h3>Nenhuma categoria</h3><p>Crie sua primeira categoria para organizar os produtos.</p></div>
            <?php else: ?>
                <div class="table-container">
                    <table><thead><tr>
                        <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'id', 'ordem' => $ordenar === 'id' && $ordem === 'ASC' ? 'DESC' : 'ASC'])) ?>" class="sort-link">ID <?= $ordenar === 'id' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                        <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'nome', 'ordem' => $ordenar === 'nome' && $ordem === 'ASC' ? 'DESC' : 'ASC'])) ?>" class="sort-link">Nome <?= $ordenar === 'nome' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                        <th>Produtos</th>
                        <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'status', 'ordem' => $ordenar === 'status' && $ordem === 'ASC' ? 'DESC' : 'ASC'])) ?>" class="sort-link">Status <?= $ordenar === 'status' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                        <th>Acoes</th>
                    </tr></thead>
                        <tbody><?php foreach ($categorias as $c): $qtd = contarProdutosPorCategoria($conn, $c['id']); ?>
                            <tr><td><span class="badge badge-neutral">#<?= $c['id'] ?></span></td>
                                <td><span class="product-name"><?= htmlspecialchars($c['nome']) ?></span></td>
                                <td><span class="badge badge-info"><?= $qtd ?> produtos</span></td>
                                <td><?php if ($c['status'] === 'Ativo'): ?><span class="badge badge-success">Ativo</span><?php else: ?><span class="badge badge-error">Inativo</span><?php endif; ?></td>
                                <td><div class="table-actions">
                                    <a href="?editar=<?= $c['id'] ?>" class="btn btn-warning btn-sm"><?= icon('edit', 14) ?></a>
                                    <?php if ($c['status'] === 'Ativo'): ?><a href="?desativar=<?= $c['id'] ?>" class="btn btn-ghost btn-sm"><?= icon('toggle-left', 14) ?></a><?php else: ?><a href="?ativar=<?= $c['id'] ?>" class="btn btn-success btn-sm"><?= icon('toggle-right', 14) ?></a><?php endif; ?>
                                    <?php if ($qtd == 0): ?><a href="?deletar=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Excluir categoria?')"><?= icon('trash-2', 14) ?></a><?php endif; ?>
                                </div></td></tr>
                        <?php endforeach; ?></tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require '../src/includes/footer.php'; ?>