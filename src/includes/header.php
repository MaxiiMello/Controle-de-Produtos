<?php
// ============================================
// 📁 header.php — Layout ERP Moderno (Sidebar + Topbar)
// ============================================
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/icons.php';

// Protecao: apenas paginas de login e assets nao precisam de autenticacao
$pagina = basename($_SERVER['PHP_SELF']);
$publicPages = ['login.php', 'logout.php'];
if (!in_array($pagina, $publicPages) && stripos($pagina, '.css') === false && stripos($pagina, '.js') === false) {
    verificarLogin();
}

$pagina = basename($_SERVER['PHP_SELF']);
$usuario = getUsuarioLogado();

$conn = function_exists('conectar') ? @conectar() : null;
$estoqueBaixoCount = 0;
if ($conn) {
    try {
        $stmt = $conn->query("SELECT COUNT(*) as total FROM produtos WHERE quantidade <= 5 AND status = 'Ativo'");
        $estoqueBaixoCount = $stmt ? $stmt->fetch()['total'] : 0;
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ($titulo ?? 'Dashboard') . ' — Controle de Produtos' ?></title>
    <link rel="stylesheet" href="assets/css/moderno.css">
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="logo-icon">CP</div>
        <div>
            <div class="logo-text">Controle de Produtos</div>
            <span class="logo-sub">Sistema de Gestao</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <div class="sidebar-section">Principal</div>

        <a href="index.php" class="sidebar-link <?= $pagina === 'index.php' ? 'active' : '' ?>">
            <span class="icon"><?= icon('layout-dashboard') ?></span>
            Dashboard
        </a>

        <a href="produtos.php" class="sidebar-link <?= strpos($pagina, 'produto') !== false ? 'active' : '' ?>">
            <span class="icon"><?= icon('package') ?></span>
            Produtos
            <?php if ($estoqueBaixoCount > 0): ?>
                <span class="badge-nav"><?= $estoqueBaixoCount ?></span>
            <?php endif; ?>
        </a>

        <a href="categorias.php" class="sidebar-link <?= $pagina === 'categorias.php' ? 'active' : '' ?>">
            <span class="icon"><?= icon('tags') ?></span>
            Categorias
        </a>

        <a href="estoque.php" class="sidebar-link <?= $pagina === 'estoque.php' ? 'active' : '' ?>">
            <span class="icon"><?= icon('boxes') ?></span>
            Estoque
        </a>

        <div class="sidebar-section">Operacional</div>

        <a href="movimentacoes.php" class="sidebar-link <?= $pagina === 'movimentacoes.php' ? 'active' : '' ?>">
            <span class="icon"><?= icon('arrow-up-down') ?></span>
            Movimentacoes
        </a>

        <a href="fornecedores.php" class="sidebar-link <?= $pagina === 'fornecedores.php' ? 'active' : '' ?>">
            <span class="icon"><?= icon('building-2') ?></span>
            Fornecedores
        </a>

        <div class="sidebar-section">Analytics</div>

        <a href="relatorios.php" class="sidebar-link <?= $pagina === 'relatorios.php' ? 'active' : '' ?>">
            <span class="icon"><?= icon('file-bar-chart') ?></span>
            Relatorios
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="#" class="sidebar-link" style="font-size:12px;color:rgba(255,255,255,0.3);">
            <span class="icon"><?= icon('settings', 16) ?></span>
            v1.0.0
        </a>
    </div>
</aside>

<div class="main-wrapper">
    <header class="topbar">
        <div class="topbar-left">
            <button class="mobile-toggle" onclick="toggleSidebar()"><?= icon('menu') ?></button>
            <div>
                <div class="topbar-title"><?= $titulo ?? 'Dashboard' ?></div>
            </div>
        </div>
        <div class="topbar-right">
            <div class="topbar-search">
                <?= icon('search', 16) ?>
                <input type="text" placeholder="Pesquisar produtos..." id="searchGlobal" onkeydown="if(event.key==='Enter'){window.location='produtos.php?busca='+encodeURIComponent(this.value);}">
            </div>
            <div class="topbar-avatar" title="<?= htmlspecialchars($usuario['nome'] ?? '') ?>">
                <?= strtoupper(substr($usuario['nome'] ?? 'U', 0, 2)) ?>
            </div>
            <a href="logout.php" class="btn btn-ghost btn-sm"><?= icon('log-out', 16) ?> Sair</a>
        </div>
    </header>

    <main class="main-content">
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success fade-in">
                <?= icon('check', 18) ?>
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['erro'])): ?>
            <div class="alert alert-error fade-in">
                <?= icon('alert-circle', 18) ?>
                <?= htmlspecialchars($_GET['erro']) ?>
            </div>
        <?php endif; ?>