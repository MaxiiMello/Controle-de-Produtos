<?php
require_once '../src/functions/auth.php';
deslogarUsuario();
header('Location: login.php?msg=' . urlencode('Sessao encerrada com sucesso.'));
exit;