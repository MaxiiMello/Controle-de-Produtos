<?php
require '../src/config/conexao.php';
require '../src/functions/funcoes.php';
require_once '../src/functions/auth.php';
require_once '../src/includes/icons.php';

$conn = conectar();

// Se ja estiver logado, redireciona para o dashboard
iniciarSessao();
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$msg = $_GET['msg'] ?? '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email && $senha) {
        if (logarUsuario($conn, $email, $senha)) {
            header('Location: index.php');
            exit;
        } else {
            $erro = 'Email ou senha incorretos.';
        }
    } else {
        $erro = 'Preencha todos os campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Controle de Produtos</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;
            background: #F8FAFC;
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:20px;
        }
        .login-container {
            display:flex;
            width:100%;
            max-width:920px;
            min-height:560px;
            background:white;
            border-radius:16px;
            box-shadow:0 4px 24px rgba(0,0,0,0.08),0 1px 4px rgba(0,0,0,0.04);
            overflow:hidden;
        }
        .login-left {
            flex:1;
            background:linear-gradient(135deg,#0F172A 0%,#1E293B 100%);
            padding:48px 40px;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            color:white;
        }
        .login-left .brand {
            display:flex;
            align-items:center;
            gap:12px;
        }
        .login-left .brand-icon {
            width:44px;height:44px;
            background:#2563EB;
            border-radius:12px;
            display:flex;align-items:center;justify-content:center;
            font-size:20px;font-weight:700;
        }
        .login-left .brand-text { font-size:20px;font-weight:700;letter-spacing:-0.3px; }
        .login-left .brand-sub { color:rgba(255,255,255,0.4);font-size:12px;font-weight:500;margin-top:-2px; }
        .login-left .features { margin-top:40px; }
        .login-left .feature { display:flex;align-items:center;gap:12px;margin-bottom:20px; }
        .login-left .feature svg { width:20px;height:20px;color:#2563EB;flex-shrink:0; }
        .login-left .feature-text { font-size:14px;color:rgba(255,255,255,0.7); }
        .login-left .feature-text strong { color:white; }
        .login-left .footer-text { font-size:12px;color:rgba(255,255,255,0.3); }
        .login-right {
            flex:1;
            padding:48px 40px;
            display:flex;
            flex-direction:column;
            justify-content:center;
        }
        .login-right h1 { font-size:24px;font-weight:700;letter-spacing:-0.5px;color:#1E293B;margin-bottom:4px; }
        .login-right p { font-size:14px;color:#64748B;margin-bottom:32px; }
        .form-group { margin-bottom:20px; }
        .form-group label { display:block;font-size:13px;font-weight:600;color:#1E293B;margin-bottom:6px; }
        .form-group .input-wrapper {
            display:flex;align-items:center;
            border:1px solid #E2E8F0;border-radius:8px;
            padding:0 14px;
            transition:all 0.2s ease;
        }
        .form-group .input-wrapper:focus-within {
            border-color:#2563EB;
            box-shadow:0 0 0 3px rgba(37,99,235,0.1);
        }
        .form-group .input-wrapper svg { width:18px;height:18px;color:#94A3B8;flex-shrink:0; }
        .form-group .input-wrapper input {
            width:100%;
            padding:12px 14px;
            border:none;outline:none;
            font-family:'Inter',sans-serif;font-size:14px;color:#1E293B;
            background:transparent;
        }
        .btn-login {
            width:100%;
            padding:12px;
            background:#2563EB;color:white;
            border:none;border-radius:8px;
            font-family:'Inter',sans-serif;font-size:14px;font-weight:600;
            cursor:pointer;
            display:flex;align-items:center;justify-content:center;gap:8px;
            transition:all 0.2s ease;
        }
        .btn-login:hover { background:#1D4ED8;box-shadow:0 2px 8px rgba(37,99,235,0.3); }
        .alert-error {
            padding:12px 16px;
            background:#FEF2F2;color:#DC2626;
            border:1px solid #FECACA;
            border-radius:8px;
            font-size:13px;font-weight:500;
            display:flex;align-items:center;gap:8px;
            margin-bottom:20px;
        }
        @media (max-width:768px) {
            .login-left { display:none; }
            .login-right { padding:32px 24px; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div>
                <div class="brand">
                    <div class="brand-icon">CP</div>
                    <div>
                        <div class="brand-text">Controle de Produtos</div>
                        <div class="brand-sub">Sistema de Gestao</div>
                    </div>
                </div>
                <div class="features">
                    <div class="feature">
                        <?= icon('package', 20) ?>
                        <div class="feature-text"><strong>Gestao de Produtos</strong><br>Cadastro e controle completo</div>
                    </div>
                    <div class="feature">
                        <?= icon('boxes', 20) ?>
                        <div class="feature-text"><strong>Controle de Estoque</strong><br>Entradas e saidas em tempo real</div>
                    </div>
                    <div class="feature">
                        <?= icon('file-bar-chart', 20) ?>
                        <div class="feature-text"><strong>Relatorios</strong><br>Analise de dados e metricas</div>
                    </div>
                </div>
            </div>
            <div class="footer-text">v1.0.0 — MySQL + PHP</div>
        </div>
        <div class="login-right">
            <h1>Acessar Sistema</h1>
            <p>Informe suas credenciais para continuar.</p>

            <?php if ($msg): ?>
                <div class="alert-success" style="padding:12px 16px;background:#F0FDF4;color:#15803D;border:1px solid #BBF7D0;border-radius:8px;font-size:13px;font-weight:500;display:flex;align-items:center;gap:8px;margin-bottom:20px;">
                    <?= icon('check', 18) ?>
                    <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>
            <?php if ($erro): ?>
                <div class="alert-error">
                    <?= icon('alert-circle', 18) ?>
                    <?= $erro ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>E-mail</label>
                    <div class="input-wrapper">
                        <?= icon('mail', 18) ?>
                        <input type="email" name="email" placeholder="seu@email.com" required autocomplete="email">
                    </div>
                </div>
                <div class="form-group">
                    <label>Senha</label>
                    <div class="input-wrapper">
                        <?= icon('lock', 18) ?>
                        <input type="password" name="senha" placeholder="Sua senha" required autocomplete="current-password">
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <?= icon('log-in', 18) ?>
                    Entrar
                </button>
            </form>
        </div>
    </div>
</body>
</html>