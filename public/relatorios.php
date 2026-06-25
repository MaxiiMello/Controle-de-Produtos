<?php
require '../src/config/conexao.php';
require '../src/functions/funcoes.php';
$conn = conectar();

$totalProdutos    = contarTotalProdutos($conn);
$totalCategorias  = contarCategoriasAtivas($conn);
$totalFornecedores = contarFornecedoresAtivos($conn);
$totalMovRecent   = contarMovimentacoesRecentes($conn, 30);
$produtosStatus   = contarProdutosPorStatus($conn);
$movPeriodo       = listarMovimentacoesPeriodo($conn, 30);
$topMov           = listarTopMovimentacoes($conn, 10);
$catStats         = listarProdutosPorCategoria($conn);

$titulo = 'Relatorios';
require '../src/includes/header.php';
?>

<div class="page-header">
    <h1>Relatorios</h1>
    <p>Analise de dados e metricas do sistema.</p>
</div>

<div class="stats-grid stagger">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Produtos</span>
            <div class="stat-icon blue"><?= icon('package', 20) ?></div>
        </div>
        <div class="stat-value"><?= $totalProdutos ?></div>
        <div class="stat-label">Produtos ativos</div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Categorias</span>
            <div class="stat-icon green"><?= icon('tags', 20) ?></div>
        </div>
        <div class="stat-value"><?= $totalCategorias ?></div>
        <div class="stat-label">Categorias ativas</div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Fornecedores</span>
            <div class="stat-icon yellow"><?= icon('building-2', 20) ?></div>
        </div>
        <div class="stat-value"><?= $totalFornecedores ?></div>
        <div class="stat-label">Fornecedores ativos</div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Mov. (30 dias)</span>
            <div class="stat-icon blue"><?= icon('arrow-up-down', 20) ?></div>
        </div>
        <div class="stat-value"><?= $totalMovRecent ?></div>
        <div class="stat-label">Movimentacoes recentes</div>
    </div>
</div>

<div class="grid-2">
    <div class="card fade-in">
        <div class="card-header">
            <h2><?= icon('bar-chart-3', 16) ?> Produtos por Status</h2>
        </div>
        <div class="card-body" style="padding:0;">
            <?php if (empty($produtosStatus)): ?>
                <div class="empty-state">
                    <?= icon('package', 48) ?>
                    <h3>Nenhum produto</h3>
                    <p>Cadastre produtos para gerar relatorios.</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th><a href="#" class="sort-link">Status</a></th>
                                <th><a href="#" class="sort-link">Quantidade</a></th>
                                <th>Percentual</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $total = array_sum(array_column($produtosStatus, 'total'));
                        foreach ($produtosStatus as $s):
                            $pct = $total > 0 ? round($s['total'] / $total * 100, 1) : 0;
                        ?>
                            <tr>
                                <td>
                                    <?php if ($s['status'] === 'Ativo'): ?>
                                        <span class="badge badge-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge badge-error">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= $s['total'] ?></strong></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <div style="flex:1;height:8px;background:var(--border-light);border-radius:999px;overflow:hidden;">
                                            <div style="width:<?= $pct ?>%;height:100%;background:<?= $s['status'] === 'Ativo' ? 'var(--success)' : 'var(--error)' ?>;border-radius:999px;"></div>
                                        </div>
                                        <span style="font-size:12px;color:var(--text-secondary);min-width:40px;"><?= $pct ?>%</span>
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

    <div class="card fade-in">
        <div class="card-header">
            <h2><?= icon('tags', 16) ?> Produtos por Categoria</h2>
        </div>
        <div class="card-body" style="padding:0;">
            <?php if (empty($catStats)): ?>
                <div class="empty-state">
                    <?= icon('tags', 48) ?>
                    <h3>Nenhuma categoria</h3>
                    <p>Cadastre categorias e produtos para visualizar.</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th><a href="#" class="sort-link">Categoria</a></th>
                                <th><a href="#" class="sort-link">Produtos</a></th>
                                <th><a href="#" class="sort-link">Estoque</a></th>
                                <th><a href="#" class="sort-link">Valor</a></th>
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
        <h2><?= icon('arrow-up-down', 16) ?> Top Produtos Mais Movimentados</h2>
        <div class="card-actions">
            <span class="badge badge-neutral">Ultimos 30 dias</span>
        </div>
    </div>
    <div class="card-body" style="padding:0;">
        <?php if (empty($topMov)): ?>
            <div class="empty-state">
                <?= icon('arrow-up-down', 48) ?>
                <h3>Sem movimentacoes</h3>
                <p>Registre movimentacoes para ver os produtos mais movimentados.</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table>
                        <thead>
                            <tr>
                                <th><a href="#" class="sort-link">Produto</a></th>
                                <th><a href="#" class="sort-link">Movimentacoes</a></th>
                                <th><a href="#" class="sort-link">Total Entradas</a></th>
                                <th><a href="#" class="sort-link">Total Saidas</a></th>
                                <th><a href="#" class="sort-link">Saldo</a></th>
                            </tr>
                        </thead>
                    <tbody>
                    <?php foreach ($topMov as $m): ?>
                        <?php $saldo = $m['total_entradas'] - $m['total_saidas']; ?>
                        <tr>
                            <td><span class="product-name"><?= htmlspecialchars($m['produto_nome']) ?></span></td>
                            <td><span class="badge badge-info"><?= $m['total_mov'] ?>x</span></td>
                            <td><span class="badge badge-success"><?= icon('arrow-down-to-line', 12) ?> +<?= $m['total_entradas'] ?></span></td>
                            <td><span class="badge badge-error"><?= icon('arrow-up-from-line', 12) ?> -<?= $m['total_saidas'] ?></span></td>
                            <td>
                                <?php if ($saldo > 0): ?>
                                    <span class="badge badge-success">+<?= $saldo ?></span>
                                <?php elseif ($saldo < 0): ?>
                                    <span class="badge badge-error"><?= $saldo ?></span>
                                <?php else: ?>
                                    <span class="badge badge-neutral">0</span>
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
        <h2><?= icon('bar-chart-3', 16) ?> Movimentacoes (Ultimos 30 dias)</h2>
        <div class="card-actions">
            <a href="movimentacoes.php" class="btn btn-primary btn-sm"><?= icon('arrow-up-down', 14) ?> Ver todas</a>
        </div>
    </div>
    <div class="card-body" style="padding:0;">
        <?php if (empty($movPeriodo)): ?>
            <div class="empty-state">
                <?= icon('bar-chart-3', 48) ?>
                <h3>Sem dados no periodo</h3>
                <p>Registre movimentacoes para gerar graficos e relatorios.</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table>
                        <thead>
                            <tr>
                                <th><a href="#" class="sort-link">Data</a></th>
                                <th><a href="#" class="sort-link">Entradas</a></th>
                                <th><a href="#" class="sort-link">Saidas</a></th>
                                <th><a href="#" class="sort-link">Saldo do Dia</a></th>
                            </tr>
                        </thead>
                    <tbody>
                    <?php foreach ($movPeriodo as $m): ?>
                        <?php $saldo = $m['entradas'] - $m['saidas']; ?>
                        <tr>
                            <td><span class="badge badge-neutral"><?= date('d/m/Y', strtotime($m['data'])) ?></span></td>
                            <td><span class="badge badge-success"><?= icon('arrow-down-to-line', 12) ?> +<?= $m['entradas'] ?></span></td>
                            <td><span class="badge badge-error"><?= icon('arrow-up-from-line', 12) ?> -<?= $m['saidas'] ?></span></td>
                            <td>
                                <?php if ($saldo > 0): ?>
                                    <span class="badge badge-success">+<?= $saldo ?></span>
                                <?php elseif ($saldo < 0): ?>
                                    <span class="badge badge-error"><?= $saldo ?></span>
                                <?php else: ?>
                                    <span class="badge badge-neutral">0</span>
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