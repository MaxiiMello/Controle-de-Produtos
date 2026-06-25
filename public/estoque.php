<?php
require '../src/config/conexao.php';
require '../src/functions/funcoes.php';
$conn = conectar();

$ordenar = $_GET['ordenar'] ?? 'id';
$ordem = $_GET['ordem'] ?? 'ASC';

$filtrosEstoque = [];
$filtrosEstoque['ordenar'] = $ordenar;
$filtrosEstoque['ordem'] = $ordem;

$estoqueCompleto = listarEstoqueCompleto($conn, $ordenar, $ordem);
$totalProdutos   = contarTotalProdutos($conn);
$quantidadeTotal = somarQuantidadeTotal($conn);
$valorEstoque    = somarValorEstoque($conn);
$estoqueBaixo    = contarProdutosEstoqueBaixo($conn, 5);
$produtosBaixo   = listarProdutosEstoqueBaixo($conn, 10);
$catStats        = listarProdutosPorCategoria($conn);

$titulo = 'Estoque';
require '../src/includes/header.php';
?>

<div class="page-header">
    <h1>Estoque</h1>
    <p>Visao geral e controle de todos os itens em estoque.</p>
</div>

<div class="stats-grid stagger">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Itens em Estoque</span>
            <div class="stat-icon blue"><?= icon('boxes', 20) ?></div>
        </div>
        <div class="stat-value"><?= number_format($quantidadeTotal ?? 0, 0, ',', '.') ?></div>
        <div class="stat-label">Unidades totais</div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Valor do Estoque</span>
            <div class="stat-icon green"><?= icon('dollar-sign', 20) ?></div>
        </div>
        <div class="stat-value">R$ <?= number_format($valorEstoque, 2, ',', '.') ?></div>
        <div class="stat-label">Valor total em produtos</div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Produtos Ativos</span>
            <div class="stat-icon blue"><?= icon('package', 20) ?></div>
        </div>
        <div class="stat-value"><?= $totalProdutos ?></div>
        <div class="stat-label">Produtos cadastrados</div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Estoque Baixo</span>
            <div class="stat-icon red"><?= icon('alert-circle', 20) ?></div>
        </div>
        <?php if ($totalProdutos == 0): ?>
            <div class="stat-value" style="color: var(--text-muted); font-size: 20px;">Sem produtos</div>
            <div class="stat-label">Cadastre produtos primeiro</div>
        <?php elseif ($estoqueBaixo > 0): ?>
            <div class="stat-value" style="color: var(--error);"><?= $estoqueBaixo ?></div>
            <div class="stat-label">Produtos com ate 5 unidades</div>
            <div class="stat-trend down"><?= icon('trending-down', 14) ?> Necessita reposicao</div>
        <?php else: ?>
            <div class="stat-value" style="color: var(--success);">0</div>
            <div class="stat-label">Produtos com ate 5 unidades</div>
            <div class="stat-trend up"><?= icon('trending-up', 14) ?> Estoque OK</div>
        <?php endif; ?>
    </div>
</div>

<div class="grid-2">
    <div class="card fade-in">
        <div class="card-header">
            <h2><?= icon('alert-circle', 16) ?> Produtos com Estoque Baixo</h2>
            <div class="card-actions">
                <a href="produtos.php" class="btn btn-primary btn-sm"><?= icon('package', 14) ?> Gerenciar</a>
            </div>
        </div>
        <div class="card-body" style="padding:0;">
            <?php if (empty($produtosBaixo)): ?>
                <div class="empty-state">
                    <?= icon('check', 48) ?>
                    <h3>Estoque saudavel</h3>
                    <p>Nenhum produto com quantidade abaixo do minimo.</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Categoria</th>
                                <th>Qtd Atual</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($produtosBaixo as $p): ?>
                            <tr>
                                <td><span class="product-name"><?= htmlspecialchars($p['nome']) ?></span></td>
                                <td><span class="badge badge-neutral"><?= htmlspecialchars($p['categoria_nome'] ?? '—') ?></span></td>
                                <td><span class="badge badge-error"><?= $p['quantidade'] ?> un.</span></td>
                                <td>
                                    <?php if ((int)$p['quantidade'] <= 2): ?>
                                        <span class="badge badge-error"><?= icon('alert-circle', 12) ?> Critico</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning"><?= icon('alert-circle', 12) ?> Atencao</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card fade-in">
        <div class="card-header">
            <h2><?= icon('bar-chart-3', 16) ?> Estoque por Categoria</h2>
        </div>
        <div class="card-body" style="padding:0;">
            <?php if (empty($catStats)): ?>
                <div class="empty-state">
                    <?= icon('tags', 48) ?>
                    <h3>Nenhuma categoria</h3>
                    <p>Cadastre categorias para visualizar a distribuicao do estoque.</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Categoria</th>
                                <th>Produtos</th>
                                <th>Unidades</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($catStats as $c): ?>
                            <tr>
                                <td><span class="product-name"><?= htmlspecialchars($c['categoria'] ?? 'Sem categoria') ?></span></td>
                                <td><span class="badge badge-info"><?= $c['total'] ?></span></td>
                                <td><?= $c['estoque'] ?? 0 ?></td>
                                <td>R$ <?= number_format($c['valor_total'] ?? 0, 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card fade-in">
    <div class="card-header">
        <h2><?= icon('list', 16) ?> Estoque Completo</h2>
        <div class="card-actions">
            <span class="badge badge-neutral"><?= count($estoqueCompleto) ?> registro(s)</span>
        </div>
    </div>
    <div class="card-body" style="padding:0;">
        <?php if (empty($estoqueCompleto)): ?>
            <div class="empty-state">
                <?= icon('package', 48) ?>
                <h3>Estoque vazio</h3>
                <p>Cadastre produtos para visualizar o estoque completo.</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'id', 'ordem' => $ordenar === 'id' && $ordem === 'ASC' ? 'DESC' : 'ASC'])) ?>" class="sort-link">Codigo <?= $ordenar === 'id' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                            <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'nome', 'ordem' => $ordenar === 'nome' && $ordem === 'ASC' ? 'DESC' : 'ASC'])) ?>" class="sort-link">Produto <?= $ordenar === 'nome' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                            <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'categoria_nome', 'ordem' => $ordenar === 'categoria_nome' && $ordem === 'ASC' ? 'DESC' : 'ASC'])) ?>" class="sort-link">Categoria <?= $ordenar === 'categoria_nome' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                            <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'preco', 'ordem' => $ordenar === 'preco' && $ordem === 'ASC' ? 'DESC' : 'ASC'])) ?>" class="sort-link">Preco <?= $ordenar === 'preco' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                            <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'quantidade', 'ordem' => $ordenar === 'quantidade' && $ordem === 'ASC' ? 'DESC' : 'ASC'])) ?>" class="sort-link">Qtd <?= $ordenar === 'quantidade' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                            <th>Valor Total</th>
                            <th><a href="?<?= http_build_query(array_merge($_GET, ['ordenar' => 'status', 'ordem' => $ordenar === 'status' && $ordem === 'ASC' ? 'DESC' : 'ASC'])) ?>" class="sort-link">Status <?= $ordenar === 'status' ? ($ordem === 'ASC' ? '↑' : '↓') : '' ?></a></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($estoqueCompleto as $p): ?>
                        <?php $valorItem = $p['preco'] * $p['quantidade']; ?>
                        <tr>
                            <td><span class="badge badge-neutral">#<?= $p['id'] ?></span></td>
                            <td><span class="product-name"><?= htmlspecialchars($p['nome']) ?></span></td>
                            <td><span class="badge badge-neutral"><?= htmlspecialchars($p['categoria_nome'] ?? '—') ?></span></td>
                            <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                            <td>
                                <?php $baixo = (int)$p['quantidade'] <= 5; ?>
                                <span class="badge <?= $baixo ? 'badge-error' : 'badge-success' ?>"><?= $p['quantidade'] ?></span>
                            </td>
                            <td><strong>R$ <?= number_format($valorItem, 2, ',', '.') ?></strong></td>
                            <td>
                                <?php if ($p['status'] === 'Ativo'): ?>
                                    <span class="badge badge-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge badge-error">Inativo</span>
                                <?php endif; ?>
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