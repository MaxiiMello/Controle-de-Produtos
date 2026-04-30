<?php
// =============================
// 📁 conexao.php
// =============================

function conectar() {
    try {
        return new PDO("mysql:host=localhost;dbname=controle_produtos", "root", "");
    } catch (PDOException $e) {
        die("Erro: " . $e->getMessage());
    }
}
?>