<?php
require '../src/config/conexao.php';
require '../src/functions/funcoes.php';
$conn = conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    if ($_POST['acao'] === 'registrar') {
        $produto_id = (int)($_POST['produto_id'] ?? 0); $tipo = $_POST['tipo'] ?? ''; $quantidade = (int)($_POST['quantidade'] ?? 0); $observacao = trim($_POST['observacao'] ?? '');
        if ($produto_id > 0 && in_array($tipo, ['entrada', 'saida']) && $quantidade > 0) {
            $produto = buscarProduto($conn, $produto_id);
            if ($produto) { $novo = $produto['quantidade'];
                if ($tipo === 'entrada') { $novo += $quantidade; } else { $novo -= $quantidade; if ($novo < 0) $novo = 0; }
                atualizarEstoque($conn, $produto_id, $novo); registrarMovimentacao($conn, $produto_id, $tipo, $quantidade, $observacao);
                header('Location: movimentacoes.php?msg=Movimentacao registrada!'); exit; }
        } header('Location: movimentacoes.php?erro=Dados invalidos.'); exit;
    }
}
$busca = trim($_GET['busca'] ?? '');
$tipoFiltro = $_GET['tipo'] ?? '';
$produtoFiltro = $_GET['produto_id'] ?? '';
$dataInicio = $_GET['data_inicio'] ?? '';
$dataFim = $_GET['data_fim'] ?? '';
$ordenar = $_GET['ordenar'] ?? 'data_movimento';
$ordem = $_GET['ordem'] ?? 'DESC';

$filtros = [];
if ($tipoFiltro) $filtros['tipo'] = $tipoFiltro;
if ($produtoFiltro) $filtros['produto_id'] = $produtoFiltro;
if ($dataInicio) $filtros['data_inicio'] = $dataInicio;
if ($dataFim) $filtros['data_fim'] = $dataFim;
$filtros['ordenar'] = $ordenar;
$filtros['ordem'] = $ordem;

$movimentacoes = filtrarMovimentacoes($conn, $filtros);
$produtos = listarProdutosAtivos($conn);
$titulo = 'Movimentacoes';
require '../src/includes/header.php';
?>
<div class="page-header"><h1>Movimentacoes</h1><p>Registre entradas e saidas de produtos do estoque.</p></div>
<div class="grid-2">
    <div class="card fade-in">
        <div class="card-header"><h2><?= icon('arrow-up-down', 16) ?> Registrar Movimentacao</h2></div>
        <div class="card-body">
            <form method="POST"><input type="hidden" name="acao" value="registrar">
                <div class="form-group"><label>Produto</label><select name="produto_id" class="form-control" required><option value="">Selecione um produto...</option>
                    <?php foreach ($produtos as $p): ?><option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?> (Estoque: <?= $p['quantidade'] ?>)</option><?php endforeach; ?></select></div>
                <div class="form-row">
                    <div class="form-group"><label>Tipo</label><select name="tipo" class="form-control" required>
                        <option value="entrada"><?= icon('arrow-down-to-line', 14) ?> Entrada</option>
                        <option value="saida"><?= icon('arrow-up-from-line', 14) ?> Saida</option>
                    </select></div>
                    <div class="form-group"><label>Quantidade</label><input type="number" name="quantidade" class="form-control" min="1" required></div>
                </div>
                <div class="form-group"><label>Observacao</label><input type="text" name="observacao" class="form-control" placeholder="Ex: Compra ao fornecedor"></div>
                <button type="submit" class="btn btn-success"><?= icon('save', 14) ?> Registrar</button>
            </form>
        </div>
    </div>
    <div class="card fade-in">
        <div class="card-header"><h2><?= icon('filter', 16) ?> Filtros</h2></div>
        <div class="card-body">
            <form method="GET" class="form-inline">
                <div class="form-group" style="flex:2;">
                    <select name="produto_id" class="form-control" onchange="this.form.submit()">
                        <option value="">Todos os produtos</option>
                        <?php foreach ($produtos as $p): ?><option value="<?= $p['id'] ?>" <?= $produtoFiltro == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nome']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <select name="tipo" class="form-control" onchange="this.form.submit()">
                        <option value="">Todos os tipos</option>
                        <option value="entrada" <?= $tipoFiltro === 'entrada' ? 'selected' : '' ?>>Entrada</option>
                        <option value="saida" <?= $tipoFiltro === 'saida' ? 'selected' : '' ?>>Saída</option>
                    </select>
                </div>
                <div class="form-group"><input type="date" name="data_inicio" class="form-control" value="<?= htmlspecialchars($dataInicio) ?>" placeholder="Data início" onchange="this.form.submit()"></div>
                <div class="form-group"><input type="date" name="data_fim" class="form-control" value="<?= htmlspecialchars($dataFim) ?>" placeholder="Data fim" onchange="this.form.submit()"></div>
                <input type="hidden" name="ordenar" value="<?= htmlspecialchars($ordenar) ?>">
                <input type="hidden" name="ordem" value="<?= htmlspecialchars($ordem) ?>">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><?= icon('filter', 14) ?> Filtrar</button>
                    <?php if ($tipoFiltro || $produtoFiltro || $dataInicio || $dataFim): ?><a href="movimentacoes.php" class="btn btn-ghost"><?= icon('x', 14) ?> Limpar</a><?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="card fade-in">
    <div class="card-header"><h2><?= icon('list', 16) ?> Historico de Movimentacoes</h2><div class="card-actions"><span class="badge badge-neutral"><?= count($movimentacoes) ?> registro(s)</span></div></div>
    <div class="card-body" style="padding:0;">
        <?php if (empty($movimentacoes)): ?>
            <div class="empty-state"><?= icon('list', 48) ?><h3>Nenhuma movimentacao</h3><p>Registre movimentacoes de entrada e saida para acompanhar o historico.</p></div>
        <?php else: ?>
            <div class="table-container"><table><thead><tr>
                        <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'id', 'ordem' => $ordenar === 'id' && $ordem === 'ASC' ? 'DESC' : 'ASC'])) ?>" class="sort-link">ID <?= $ordenar === 'id' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                        <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'produto_nome', 'ordem' => $ordenar === 'produto_nome' && $ordem === 'ASC' ? 'DESC' : 'ASC'])) ?>" class="sort-link">Produto <?= $ordenar === 'produto_nome' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                        <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'tipo', 'ordem' => $ordenar === 'tipo' && $ordem === 'ASC' ? 'DESC' : 'ASC'])) ?>" class="sort-link">Tipo <?= $ordenar === 'tipo' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                        <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'quantidade', 'ordem' => $ordenar === 'quantidade' && $ordem === 'ASC' ? 'DESC' : 'ASC'])) ?>" class="sort-link">Qtd <?= $ordenar === 'quantidade' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                        <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'data_movimento', 'ordem' => $ordenar === 'data_movimento' && $ordem === 'ASC' ? 'DESC' : 'ASC'])) ?>" class="sort-link">Data <?= $ordenar === 'data_movimento' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                        <th>Observacao</th>
                    </tr></thead>
                <tbody><?php foreach ($movimentacoes as $m): ?><tr>
                    <td><span class="badge badge-neutral">#<?= $m['id'] ?></span></td>
                    <td><span class="product-name"><?= htmlspecialchars($m['produto_nome']) ?></span></td>
                    <td><?php if ($m['tipo'] === 'entrada'): ?><span class="badge badge-success"><?= icon('arrow-down-to-line', 12) ?> Entrada</span><?php else: ?><span class="badge badge-error"><?= icon('arrow-up-from-line', 12) ?> Saida</span><?php endif; ?></td>
                    <td><?= $m['quantidade'] ?></td>
                    <td><span class="badge badge-neutral"><?= date('d/m/Y H:i', strtotime($m['data_movimento'])) ?></span></td>
                    <td><?= htmlspecialchars($m['observacao'] ?? '—') ?></td>
                </tr><?php endforeach; ?></tbody></table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require '../src/includes/footer.php'; ?>