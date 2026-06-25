<?php
// ============================================
// 📁 setup.php — Configuracao inicial do sistema
// ============================================
require '../src/config/conexao.php';
require '../src/functions/funcoes.php';
require_once '../src/functions/auth.php';

$conn = conectar();
$mensagens = [];

// Verifica se o admin ja existe
$stmt = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE email = 'admin@admin.com'");
$adminExiste = $stmt->fetch()['total'] > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar_admin'])) {
    $nome  = trim($_POST['nome'] ?? 'Administrador');
    $email = trim($_POST['email'] ?? 'admin@admin.com');
    $senha = $_POST['senha'] ?? 'admin123';

    if (registrarUsuario($conn, $nome, $email, $senha, 'admin')) {
        $mensagens[] = ['tipo' => 'success', 'texto' => "Usuario '{$nome}' criado com sucesso! Email: {$email} / Senha: (informada)"];
        $adminExiste = true;
    } else {
        $mensagens[] = ['tipo' => 'error', 'texto' => 'Erro ao criar usuario. Talvez o email ja exista.'];
    }
}

if (isset($_POST['ver_senha'])) {
    $senha_teste = $_POST['senha_teste'] ?? 'admin123';
    $hash_gerado = password_hash($senha_teste, PASSWORD_DEFAULT);
    $hash_info    = password_get_info($hash_gerado);
    $mensagens[] = ['tipo' => 'info', 'texto' => "Hash gerado para '{$senha_teste}': {$hash_gerado}"];
    $mensagens[] = ['tipo' => 'info', 'texto' => 'Algoritmo: ' . ($hash_info['algoName'] ?? 'bcrypt') . ' | Custo: ' . ($hash_info['options']['cost'] ?? 10)];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup — Controle de Produtos</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:#F8FAFC; padding:40px; color:#1E293B; }
        .setup { max-width:640px; margin:0 auto; }
        h1 { font-size:24px; margin-bottom:4px; }
        p { color:#64748B; margin-bottom:24px; }
        .card { background:white; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,.08); border:1px solid #E2E8F0; margin-bottom:16px; }
        .card h2 { font-size:16px; margin-bottom:16px; }
        .alert { padding:12px 16px; border-radius:8px; font-size:13px; margin-bottom:12px; }
        .alert-success { background:#F0FDF4; color:#15803D; border:1px solid #BBF7D0; }
        .alert-error { background:#FEF2F2; color:#DC2626; border:1px solid #FECACA; }
        .alert-info { background:#EFF6FF; color:#1D4ED8; border:1px solid #BFDBFE; }
        .form-group { margin-bottom:12px; }
        .form-group label { display:block; font-size:13px; font-weight:600; margin-bottom:4px; }
        .form-control { width:100%; padding:10px 12px; border:1px solid #E2E8F0; border-radius:8px; font-family:'Inter',sans-serif; font-size:13px; }
        .form-control:focus { outline:none; border-color:#2563EB; box-shadow:0 0 0 3px rgba(37,99,235,.1); }
        .btn { padding:10px 20px; background:#2563EB; color:white; border:none; border-radius:8px; font-family:'Inter',sans-serif; font-size:13px; font-weight:600; cursor:pointer; }
        .btn:hover { background:#1D4ED8; }
        .btn-ghost { background:transparent; color:#64748B; border:1px solid #E2E8F0; }
        .btn-ghost:hover { background:#F8FAFC; }
        .btn-group { display:flex; gap:8px; margin-top:4px; }
        code { background:#F1F5F9; padding:2px 6px; border-radius:4px; font-size:12px; word-break:break-all; }
        .mt-4 { margin-top:16px; }
    </style>
</head>
<body>
<div class="setup">
    <h1>Configuracao do Sistema</h1>
    <p>Utilitario para configuracao inicial e gerenciamento de usuarios.</p>

    <?php foreach ($mensagens as $m): ?>
        <div class="alert alert-<?= $m['tipo'] ?>"><?= htmlspecialchars($m['texto']) ?></div>
    <?php endforeach; ?>

    <div class="card">
        <h2>Status do Banco de Dados</h2>
        <?php
        $tabelas = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        echo '<p style="margin-bottom:8px;font-size:13px;">Tabelas encontradas: <strong>' . count($tabelas) . '</strong></p>';
        echo '<div style="display:flex;flex-wrap:wrap;gap:4px;">';
        foreach ($tabelas as $t) {
            $count = $conn->query("SELECT COUNT(*) as c FROM `$t`")->fetch()['c'];
            echo '<span class="badge" style="background:#F1F5F9;padding:4px 10px;border-radius:999px;font-size:12px;">' . htmlspecialchars($t) . ' (' . $count . ')</span>';
        }
        echo '</div>';
        ?>
    </div>

    <?php if (!$adminExiste): ?>
    <div class="card">
        <h2>Criar Usuario Administrador</h2>
        <form method="POST">
            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" class="form-control" value="Administrador" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="admin@admin.com" required>
            </div>
            <div class="form-group">
                <label>Senha</label>
                <input type="text" name="senha" class="form-control" value="admin123" required>
            </div>
            <button type="submit" name="criar_admin" class="btn">Criar Administrador</button>
        </form>
    </div>
    <?php else: ?>
    <div class="card">
        <h2>Usuario Administrador</h2>
        <p style="font-size:13px;color:#64748B;">
            O usuario admin ja existe. Acesse o sistema em
            <a href="login.php" style="color:#2563EB;font-weight:600;">login.php</a>
        </p>
        <div style="background:#F8FAFC;border-radius:8px;padding:12px;margin-top:8px;font-size:13px;">
            <p><strong>Credenciais padrao:</strong></p>
            <p>Email: <code>admin@admin.com</code></p>
            <p>Senha: <code>admin123</code></p>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <h2>Gerar Hash de Senha</h2>
        <form method="POST">
            <div class="form-group">
                <label>Senha para testar</label>
                <input type="text" name="senha_teste" class="form-control" value="admin123">
            </div>
            <button type="submit" name="ver_senha" class="btn btn-ghost">Gerar Hash</button>
        </form>
    </div>

    <div class="card">
        <h2>Links uteis</h2>
        <div class="btn-group">
            <a href="login.php" class="btn">Ir para Login</a>
            <a href="index.php" class="btn btn-ghost">Ir para Dashboard</a>
        </div>
    </div>
</div>
</body>
</html>