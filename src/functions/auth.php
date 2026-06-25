<?php
// ============================================
// 📁 auth.php — Autenticacao e Sessao
// ============================================

function iniciarSessao() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function verificarLogin() {
    iniciarSessao();
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php?erro=' . urlencode('Faca login para acessar o sistema.'));
        exit;
    }
}

function logarUsuario($conn, $email, $senha) {
    $sql = "SELECT * FROM usuarios WHERE email = ? AND status = 'Ativo' LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        iniciarSessao();
        session_regenerate_id(true);
        $_SESSION['usuario_id']    = $usuario['id'];
        $_SESSION['usuario_nome']  = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_nivel'] = $usuario['nivel'];
        return true;
    }
    return false;
}

function deslogarUsuario() {
    iniciarSessao();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

function registrarUsuario($conn, $nome, $email, $senha, $nivel = 'operador') {
    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (nome, email, senha, nivel, status) VALUES (?, ?, ?, ?, 'Ativo')";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$nome, $email, $hash, $nivel]);
}

function usuarioEstaLogado() {
    iniciarSessao();
    return isset($_SESSION['usuario_id']);
}

function getUsuarioLogado() {
    iniciarSessao();
    if (isset($_SESSION['usuario_id'])) {
        return [
            'id'    => $_SESSION['usuario_id'],
            'nome'  => $_SESSION['usuario_nome'],
            'email' => $_SESSION['usuario_email'],
            'nivel' => $_SESSION['usuario_nivel'],
        ];
    }
    return null;
}