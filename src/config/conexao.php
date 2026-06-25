<?php
// ============================================
// 📁 conexao.php – Conexão com MySQL via PDO
// ============================================

function conectar() {
    $host = $_ENV['MYSQLHOST'] ?? 'localhost';
    $db   = $_ENV['MYSQLDATABASE'] ?? 'controle_produtos';
    $user = $_ENV['MYSQLUSER'] ?? 'root';
    $pass = $_ENV['MYSQLPASSWORD'] ?? '';
    $port = $_ENV['MYSQLPORT'] ?? 3306;

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        die("Erro ao conectar ao banco de dados: " . $e->getMessage());
    }
}
