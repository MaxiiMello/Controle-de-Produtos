<?php
require '../src/config/conexao.php';
require '../src/functions/funcoes.php';
require_once '../src/includes/icons.php';

$conn = conectar();

$totalProdutos   = contarTotalProdutos($conn);
$totalCategorias = contarTotalCategorias($conn);
$estoqueTotal    = somarEstoqueTotal($conn);
$estoqueBaixoQtd = contarProdutosEstoqueBaixo($conn, 5);
$produtosBaixo   = listarProdutosEstoqueBaixo($conn, 5);
$ultimasMovs     = listarUltimasMovimentacoes($conn, 5);

$titulo = 'Dashboard';
require '../src/includes/header.php';
?>

<div class="page-header">
    <h1>Dashboard</h1>
    <p>Visao geral do estoque e movimentacoes do sistema.</p>
</div>

<div class="stats-grid stagger">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Total de Produtos</span>
            <div class="stat-icon blue"><?= icon('package', 20) ?></div>
        </div>
        <div class="stat-value"><?= $totalProdutos ?></div>
        <div class="stat-label">Produtos ativos no sistema</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Categorias</span>
            <div class="stat-icon green"><?= icon('tags', 20) ?></div>
        </div>
        <div class="stat-value"><?= $totalCategorias ?></div>
        <div class="stat-label">Categorias cadastradas</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Itens em Estoque</span>
            <div class="stat-icon blue"><?= icon('boxes', 20) ?></div>
        </div>
        <div class="stat-value"><?= number_format(somarQuantidadeTotal($conn) ?? 0, 0, ',', '.') ?></div>
        <div class="stat-label">Unidades totais</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Valor em Estoque</span>
            <div class="stat-icon yellow"><?= icon('dollar-sign', 20) ?></div>
        </div>
        <div class="stat-value">R$ <?= number_format(somarValorEstoque($conn), 2, ',', '.') ?></div>
        <div class="stat-label">Total em produtos</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Estoque Baixo</span>
            <div class="stat-icon red"><?= icon('alert-circle', 20) ?></div>
        </div>
        <?php if ($totalProdutos == 0): ?>
            <div class="stat-value" style="color: var(--text-muted); font-size: 20px;">Sem produtos</div>
            <div class="stat-label">Cadastre produtos primeiro</div>
        <?php elseif ($estoqueBaixoQtd > 0): ?>
            <div class="stat-value" style="color: var(--error);"><?= $estoqueBaixoQtd ?></div>
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
            <h2><?= icon('alert-circle', 16) ?> Estoque Baixo</h2>
            <div class="card-actions">
                <a href="produtos.php" class="btn btn-primary btn-sm">Ver todos</a>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($produtosBaixo)): ?>
                <div class="empty-state">
                    <?= icon('check', 48) ?>
                    <h3>Estoque saudavel</h3>
                    <p>Nenhum produto com estoque baixo no momento.</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Categoria</th>
                                <th>Qtd</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($produtosBaixo as $p): ?>
                            <tr>
                                <td><span class="product-name"><?= htmlspecialchars($p['nome']) ?></span></td>
                                <td><span class="badge badge-neutral"><?= htmlspecialchars($p['categoria_nome'] ?? '—') ?></span></td>
                                <td><span class="badge badge-error"><?= $p['quantidade'] ?> un.</span></td>
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
            <h2><?= icon('arrow-up-down', 16) ?> Ultimas Movimentacoes</h2>
            <div class="card-actions">
                <a href="movimentacoes.php" class="btn btn-primary btn-sm">Ver todas</a>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($ultimasMovs)): ?>
                <div class="empty-state">
                    <?= icon('list', 48) ?>
                    <h3>Nenhuma movimentacao</h3>
                    <p>Registre movimentacoes de entrada e saida de produtos.</p>
                    <a href="movimentacoes.php" class="btn btn-primary btn-sm">Registrar</a>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Tipo</th>
                                <th>Qtd</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($ultimasMovs as $m): ?>
                            <tr>
                                <td><span class="product-name"><?= htmlspecialchars($m['produto_nome']) ?></span></td>
                                <td>
                                    <?php if ($m['tipo'] === 'entrada'): ?>
                                        <span class="badge badge-success">
                                            <?= icon('arrow-down-to-line', 12) ?> Entrada
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-error">
                                            <?= icon('arrow-up-from-line', 12) ?> Saida
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $m['quantidade'] ?></td>
                                <td><span class="badge badge-neutral"><?= date('d/m/Y H:i', strtotime($m['data_movimento'])) ?></span></td>
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
        <h2><?= icon('zap', 16) ?> Acoes Rapidas</h2>
    </div>
    <div class="card-body">
        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <a href="produtos.php" class="btn btn-primary"><?= icon('plus', 16) ?> Novo Produto</a>
            <a href="categorias.php" class="btn btn-success"><?= icon('tags', 16) ?> Nova Categoria</a>
            <a href="movimentacoes.php" class="btn btn-warning"><?= icon('arrow-up-down', 16) ?> Registrar Movimentacao</a>
            <a href="fornecedores.php" class="btn btn-ghost"><?= icon('building-2', 16) ?> Novo Fornecedor</a>
        </div>
    </div>
</div>

<?php require '../src/includes/footer.php'; ?>