<?php
require '../src/config/conexao.php';
require '../src/functions/funcoes.php';
$conn = conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    if ($_POST['acao'] === 'inserir') {
        $nome = trim($_POST['nome'] ?? ''); $contato = trim($_POST['contato'] ?? ''); $email = trim($_POST['email'] ?? ''); $telefone = trim($_POST['telefone'] ?? '');
        if ($nome) { inserirFornecedor($conn, $nome, $contato, $email, $telefone); header('Location: fornecedores.php?msg=Fornecedor cadastrado!'); exit; }
        header('Location: fornecedores.php?erro=Informe o nome do fornecedor.'); exit;
    }
}
if (isset($_GET['deletar'])) { $id = (int)$_GET['deletar']; $conn->prepare("DELETE FROM fornecedores WHERE id=?")->execute([$id]); header('Location: fornecedores.php?msg=Fornecedor excluido.'); exit; }

$busca = trim($_GET['busca'] ?? '');
$statusFiltro = $_GET['status'] ?? '';
$ordenar = $_GET['ordenar'] ?? 'nome';
$ordem = $_GET['ordem'] ?? 'ASC';

$filtros = [];
if ($busca) $filtros['busca'] = $busca;
if ($statusFiltro) $filtros['status'] = $statusFiltro;
$filtros['ordenar'] = $ordenar;
$filtros['ordem'] = $ordem;

$fornecedores = filtrarFornecedores($conn, $filtros);
$titulo = 'Fornecedores';
require '../src/includes/header.php';
?>
<div class="page-header"><h1>Fornecedores</h1><p>Gerencie os fornecedores parceiros da empresa.</p></div>

<div class="action-bar">
    <form method="GET" class="filter-bar">
        <div class="search-box">
            <span class="search-icon"><?= icon('search', 16) ?></span>
            <input type="text" name="busca" class="form-control" placeholder="Buscar fornecedor..." value="<?= htmlspecialchars($busca) ?>" style="padding-left:36px;">
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
            <a href="fornecedores.php" class="btn btn-ghost"><?= icon('x', 16) ?> Limpar</a>
        <?php endif; ?>
    </form>
</div>
<div class="grid-2">
    <div class="card fade-in">
        <div class="card-header"><h2><?= icon('plus', 16) ?> Novo Fornecedor</h2></div>
        <div class="card-body">
            <form method="POST"><input type="hidden" name="acao" value="inserir">
                <div class="form-group"><label>Nome</label><input type="text" name="nome" class="form-control" required placeholder="Nome do fornecedor"></div>
                <div class="form-row">
                    <div class="form-group"><label>Contato</label><input type="text" name="contato" class="form-control" placeholder="Nome do contato"></div>
                    <div class="form-group"><label>Telefone</label><input type="text" name="telefone" class="form-control" placeholder="(11) 99999-8888"></div>
                </div>
                <div class="form-group"><label>E-mail</label><input type="email" name="email" class="form-control" placeholder="email@fornecedor.com"></div>
                <button type="submit" class="btn btn-success"><?= icon('plus', 14) ?> Cadastrar</button>
            </form>
        </div>
    </div>
    <div class="card fade-in">
        <div class="card-header"><h2><?= icon('building-2', 16) ?> Fornecedores</h2><div class="card-actions"><span class="badge badge-neutral"><?= count($fornecedores) ?> registro(s)</span></div></div>
        <div class="card-body" style="padding:0;">
            <?php if (empty($fornecedores)): ?>
                <div class="empty-state"><?= icon('building-2', 48) ?><h3>Nenhum fornecedor</h3><p>Cadastre fornecedores para gerenciar sua rede de parceiros.</p></div>
            <?php else: ?>
                <div class="table-container"><table><thead><tr>
                        <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'id', 'ordem' => $ordenar === 'id' && $ordem === 'ASC' ? 'DESC' : 'ASC'])) ?>" class="sort-link">ID <?= $ordenar === 'id' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                        <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'nome', 'ordem' => $ordenar === 'nome' && $ordem === 'ASC' ? 'DESC' : 'ASC'])) ?>" class="sort-link">Nome <?= $ordenar === 'nome' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                        <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'contato', 'ordem' => $ordenar === 'contato' && $ordem === 'ASC' ? 'DESC' : 'ASC'])) ?>" class="sort-link">Contato <?= $ordenar === 'contato' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                        <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'email', 'ordem' => $ordenar === 'email' && $ordem === 'ASC' ? 'DESC' : 'ASC'])) ?>" class="sort-link">E-mail <?= $ordenar === 'email' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                        <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'telefone', 'ordem' => $ordenar === 'telefone' && $ordem === 'ASC' ? 'DESC' : 'ASC'])) ?>" class="sort-link">Telefone <?= $ordenar === 'telefone' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                        <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'status', 'ordem' => $ordenar === 'status' && $ordem === 'ASC' ? 'DESC' : 'ASC'])) ?>" class="sort-link">Status <?= $ordenar === 'status' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                        <th>Acoes</th>
                    </tr></thead>
                    <tbody><?php foreach ($fornecedores as $f): ?><tr>
                        <td><span class="badge badge-neutral">#<?= $f['id'] ?></span></td>
                        <td><span class="product-name"><?= htmlspecialchars($f['nome']) ?></span></td>
                        <td><?= htmlspecialchars($f['contato'] ?? '—') ?></td><td><?= htmlspecialchars($f['email'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($f['telefone'] ?? '—') ?></td>
                        <td><?php if ($f['status'] === 'Ativo'): ?><span class="badge badge-success">Ativo</span><?php else: ?><span class="badge badge-error">Inativo</span><?php endif; ?></td>
                        <td><a href="?deletar=<?= $f['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Excluir fornecedor?')"><?= icon('trash-2', 14) ?></a></td>
                    </tr><?php endforeach; ?></tbody></table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require '../src/includes/footer.php'; ?>