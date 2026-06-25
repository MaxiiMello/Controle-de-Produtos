<?php
require '../src/config/conexao.php';
require '../src/functions/funcoes.php';

$conn = conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    if ($_POST['acao'] === 'inserir') {
        $nome        = trim($_POST['nome'] ?? '');
        $categoria_id = $_POST['categoria_id'] ?: null;
        $preco       = str_replace(',', '.', $_POST['preco'] ?? 0);
        $quantidade  = (int)($_POST['quantidade'] ?? 0);
        $descricao   = trim($_POST['descricao'] ?? '');
        if ($nome && $preco > 0) {
            inserirProduto($conn, $nome, $categoria_id, $preco, $quantidade, $descricao);
            header('Location: produtos.php?msg=' . urlencode('Produto cadastrado com sucesso!'));
            exit;
        }
        header('Location: produtos.php?erro=' . urlencode('Preencha nome e preco corretamente.'));
        exit;
    }
    if ($_POST['acao'] === 'atualizar_estoque') {
        $id = (int)($_POST['produto_id'] ?? 0);
        $qtd = (int)($_POST['quantidade'] ?? 0);
        if ($id > 0) { atualizarEstoque($conn, $id, $qtd);
            header('Location: produtos.php?msg=Estoque atualizado!');
            exit; }
    }
}
if (isset($_GET['deletar'])) {
    deletarProduto($conn, (int)$_GET['deletar']);
    header('Location: produtos.php?msg=Produto excluido.');
    exit;
}
if (isset($_GET['ativar'])) { ativarProduto($conn, (int)$_GET['ativar']);
    header('Location: produtos.php?msg=Produto ativado.');
    exit; }
if (isset($_GET['desativar'])) { desativarProduto($conn, (int)$_GET['desativar']);
    header('Location: produtos.php?msg=Produto desativado.');
    exit; }

$busca = trim($_GET['busca'] ?? '');
$categoriaFiltro = $_GET['categoria_id'] ?? '';
$statusFiltro = $_GET['status'] ?? '';
$ordenar = $_GET['ordenar'] ?? 'id';
$ordem = $_GET['ordem'] ?? 'DESC';

$filtros = [];
if ($categoriaFiltro) $filtros['categoria_id'] = $categoriaFiltro;
if ($statusFiltro) $filtros['status'] = $statusFiltro;
if ($busca) $filtros['busca'] = $busca;
$filtros['ordenar'] = $ordenar;
$filtros['ordem'] = $ordem;

$produtos = filtrarProdutos($conn, $filtros);
$categorias = listarCategoriasAtivas($conn);
$titulo = 'Produtos';
require '../src/includes/header.php';
?>

<div class="page-header">
    <h1>Produtos</h1>
    <p>Gerencie todos os produtos do sistema.</p>
</div>

<div class="action-bar">
    <form method="GET" class="filter-bar">
        <div class="search-box">
            <span class="search-icon"><?= icon('search', 16) ?></span>
            <input type="text" name="busca" class="form-control" placeholder="Buscar produto..." value="<?= htmlspecialchars($busca) ?>" style="padding-left:36px;">
        </div>
        
        <select name="categoria_id" class="form-control" onchange="this.form.submit()">
            <option value="">Todas Categorias</option>
            <?php foreach ($categorias as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $categoriaFiltro == $c['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <select name="status" class="form-control" onchange="this.form.submit()">
            <option value="">Todos Status</option>
            <option value="Ativo" <?= $statusFiltro === 'Ativo' ? 'selected' : '' ?>>Ativo</option>
            <option value="Inativo" <?= $statusFiltro === 'Inativo' ? 'selected' : '' ?>>Inativo</option>
        </select>
        
        <input type="hidden" name="ordenar" value="<?= htmlspecialchars($ordenar) ?>">
        <input type="hidden" name="ordem" value="<?= htmlspecialchars($ordem) ?>">
        
        <button type="submit" class="btn btn-primary"><?= icon('filter', 16) ?> Filtrar</button>
        <?php if ($busca || $categoriaFiltro || $statusFiltro): ?>
            <a href="produtos.php" class="btn btn-ghost"><?= icon('x', 16) ?> Limpar</a>
        <?php endif; ?>
    </form>
</div>

<div class="card fade-in">
    <div class="card-header">
        <h2><?= icon('plus', 16) ?> Novo Produto</h2>
    </div>
    <div class="card-body">
        <form method="POST" class="form-inline">
            <input type="hidden" name="acao" value="inserir">
            <div class="form-group">
                <input type="text" name="nome" class="form-control" placeholder="Nome do produto" required>
            </div>
            <div class="form-group">
                <select name="categoria_id" class="form-control">
                    <option value="">Sem categoria</option>
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <input type="number" name="preco" class="form-control" placeholder="Preco" step="0.01" required>
            </div>
            <div class="form-group">
                <input type="number" name="quantidade" class="form-control" placeholder="Qtd" required>
            </div>
            <div class="form-group">
                <input type="text" name="descricao" class="form-control" placeholder="Descricao (opcional)">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success"><?= icon('plus', 14) ?> Cadastrar</button>
            </div>
        </form>
    </div>
</div>

<div class="card fade-in">
    <div class="card-header">
        <h2><?= icon('package', 16) ?> Lista de Produtos</h2>
        <div class="card-actions">
            <span class="badge badge-neutral"><?= count($produtos) ?> produto(s)</span>
        </div>
    </div>
    <div class="card-body" style="padding:0;">
        <?php if (empty($produtos)): ?>
            <div class="empty-state">
                <?= icon('package', 48) ?>
                <h3>Nenhum produto encontrado</h3>
                <p><?= $busca ? 'Nenhum resultado para sua busca.' : 'Cadastre seu primeiro produto.' ?></p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'id', 'ordem' => $ordenar === 'id' && $ordem === 'DESC' ? 'ASC' : 'DESC'])) ?>" class="sort-link">Codigo <?= $ordenar === 'id' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                            <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'nome', 'ordem' => $ordenar === 'nome' && $ordem === 'DESC' ? 'ASC' : 'DESC'])) ?>" class="sort-link">Nome <?= $ordenar === 'nome' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                            <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'categoria_nome', 'ordem' => $ordenar === 'categoria_nome' && $ordem === 'DESC' ? 'ASC' : 'DESC'])) ?>" class="sort-link">Categoria <?= $ordenar === 'categoria_nome' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                            <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'preco', 'ordem' => $ordenar === 'preco' && $ordem === 'DESC' ? 'ASC' : 'DESC'])) ?>" class="sort-link">Preco <?= $ordenar === 'preco' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                            <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'quantidade', 'ordem' => $ordenar === 'quantidade' && $ordem === 'DESC' ? 'ASC' : 'DESC'])) ?>" class="sort-link">Qtd <?= $ordenar === 'quantidade' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                            <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'status', 'ordem' => $ordenar === 'status' && $ordem === 'DESC' ? 'ASC' : 'DESC'])) ?>" class="sort-link">Status <?= $ordenar === 'status' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                            <th>Estoque</th>
                            <th>Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($produtos as $p): ?>
                        <tr>
                            <td><span class="badge badge-neutral">#<?= $p['id'] ?></span></td>
                            <td><span class="product-name"><?= htmlspecialchars($p['nome']) ?></span></td>
                            <td><span class="badge badge-neutral"><?= htmlspecialchars($p['categoria_nome'] ?? '—') ?></span></td>
                            <td><strong>R$ <?= number_format($p['preco'], 2, ',', '.') ?></strong></td>
                            <td>
                                <?php $baixo = (int)$p['quantidade'] <= 5; ?>
                                <span class="badge <?= $baixo ? 'badge-error' : 'badge-success' ?>"><?= $p['quantidade'] ?></span>
                            </td>
                            <td>
                                <?php if ($p['status'] === 'Ativo'): ?>
                                    <span class="badge badge-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge badge-error">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" class="form-inline" style="gap:4px;flex-wrap:nowrap;">
                                    <input type="hidden" name="acao" value="atualizar_estoque">
                                    <input type="hidden" name="produto_id" value="<?= $p['id'] ?>">
                                    <input type="number" name="quantidade" value="<?= $p['quantidade'] ?>" min="0" class="form-control" style="width:70px;padding:4px 8px;">
                                    <button type="submit" class="btn btn-primary btn-sm"><?= icon('save', 14) ?></button>
                                </form>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="editar_produto.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm"><?= icon('edit', 14) ?></a>
                                    <?php if ($p['status'] === 'Ativo'): ?>
                                        <a href="?desativar=<?= $p['id'] ?>" class="btn btn-ghost btn-sm"><?= icon('toggle-left', 14) ?></a>
                                    <?php else: ?>
                                        <a href="?ativar=<?= $p['id'] ?>" class="btn btn-success btn-sm"><?= icon('toggle-right', 14) ?></a>
                                    <?php endif; ?>
                                    <a href="?deletar=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Excluir este produto?')"><?= icon('trash-2', 14) ?></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require '../src/includes/footer.php'; ?>