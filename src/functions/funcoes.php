<?php
// =============================
// 📁 funcoes.php
// =============================

function inserirProduto($conn, $nome, $categoria, $preco, $quantidade, $descricao) {
    $sql = "INSERT INTO produtos (nome, categoria, preco, quantidade, descricao, status) VALUES (?, ?, ?, ?, ?, 'Ativo')";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$nome, $categoria, $preco, $quantidade, $descricao]);
}

function listarProdutos($conn) {
    $sql = "SELECT * FROM produtos ORDER BY id DESC";
    return $conn->query($sql)->fetchAll();
}

function listarProdutosEstoqueBaixo($conn, $limite = 5) {
    $sql = "SELECT * FROM produtos WHERE quantidade <= ? ORDER BY quantidade ASC, id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$limite]);
    return $stmt->fetchAll();
}

function atualizarEstoque($conn, $id, $quantidade) {
    $sql = "UPDATE produtos SET quantidade=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$quantidade, $id]);
}

function ativarProduto($conn, $id) {
    $sql = "UPDATE produtos SET status='Ativo' WHERE id=?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$id]);
}

function desativarProduto($conn, $id) {
    $sql = "UPDATE produtos SET status='Inativo' WHERE id=?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$id]);
}

function deletarProduto($conn, $id) {
    $sql = "DELETE FROM produtos WHERE id=?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$id]);
}
?>