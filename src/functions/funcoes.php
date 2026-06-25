<?php
// ============================================
// 📁 funcoes.php – Funções do sistema
// ============================================

// -------------------------------------------------------
// CATEGORIAS
// -------------------------------------------------------
function inserirCategoria($conn, $nome, $descricao) {
    $sql = "INSERT INTO categorias (nome, descricao, status) VALUES (?, ?, 'Ativo')";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$nome, $descricao]);
}

function listarCategorias($conn) {
    $sql = "SELECT * FROM categorias ORDER BY nome ASC";
    return $conn->query($sql)->fetchAll();
}

function listarCategoriasAtivas($conn) {
    $sql = "SELECT * FROM categorias WHERE status = 'Ativo' ORDER BY nome ASC";
    return $conn->query($sql)->fetchAll();
}

function buscarCategoria($conn, $id) {
    $sql = "SELECT * FROM categorias WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function editarCategoria($conn, $id, $nome, $descricao) {
    $sql = "UPDATE categorias SET nome = ?, descricao = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$nome, $descricao, $id]);
}

function ativarCategoria($conn, $id) {
    $sql = "UPDATE categorias SET status = 'Ativo' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$id]);
}

function desativarCategoria($conn, $id) {
    $sql = "UPDATE categorias SET status = 'Inativo' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$id]);
}

function excluirCategoria($conn, $id) {
    $sql = "DELETE FROM categorias WHERE id = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$id]);
}

function filtrarCategorias($conn, $filtros = []) {
    $sql = "SELECT * FROM categorias WHERE 1=1";
    $params = [];
    
    if (!empty($filtros['busca'])) {
        $sql .= " AND (nome LIKE ? OR descricao LIKE ?)";
        $params[] = "%{$filtros['busca']}%";
        $params[] = "%{$filtros['busca']}%";
    }
    
    if (!empty($filtros['status'])) {
        $sql .= " AND status = ?";
        $params[] = $filtros['status'];
    }
    
    if (!empty($filtros['ordenar'])) {
        $ordem = in_array($filtros['ordem'], ['ASC', 'DESC']) ? $filtros['ordem'] : 'ASC';
        $sql .= " ORDER BY {$filtros['ordenar']} {$ordem}";
    } else {
        $sql .= " ORDER BY nome ASC";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function contarProdutosPorCategoria($conn, $categoria_id) {
    $sql = "SELECT COUNT(*) AS total FROM produtos WHERE categoria_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$categoria_id]);
    return $stmt->fetch()['total'];
}

// -------------------------------------------------------
// PRODUTOS
// -------------------------------------------------------
function inserirProduto($conn, $nome, $categoria_id, $preco, $quantidade, $descricao, $imagem = null) {
    $sql = "INSERT INTO produtos (nome, categoria_id, preco, quantidade, descricao, status, imagem)
            VALUES (?, ?, ?, ?, ?, 'Ativo', ?)";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$nome, $categoria_id, $preco, $quantidade, $descricao, $imagem]);
}

function listarProdutos($conn) {
    $sql = "SELECT p.*, c.nome AS categoria_nome
            FROM produtos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            ORDER BY p.id DESC";
    return $conn->query($sql)->fetchAll();
}

function listarProdutosAtivos($conn) {
    $sql = "SELECT p.*, c.nome AS categoria_nome
            FROM produtos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            WHERE p.status = 'Ativo'
            ORDER BY p.nome ASC";
    return $conn->query($sql)->fetchAll();
}

function buscarProduto($conn, $id) {
    $sql = "SELECT p.*, c.nome AS categoria_nome
            FROM produtos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            WHERE p.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function editarProduto($conn, $id, $nome, $categoria_id, $preco, $quantidade, $descricao, $imagem = null) {
    if ($imagem !== null) {
        $sql = "UPDATE produtos SET nome = ?, categoria_id = ?, preco = ?, quantidade = ?, descricao = ?, imagem = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$nome, $categoria_id, $preco, $quantidade, $descricao, $imagem, $id]);
    } else {
        $sql = "UPDATE produtos SET nome = ?, categoria_id = ?, preco = ?, quantidade = ?, descricao = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$nome, $categoria_id, $preco, $quantidade, $descricao, $id]);
    }
}

function atualizarEstoque($conn, $id, $quantidade) {
    $sql = "UPDATE produtos SET quantidade = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$quantidade, $id]);
}

function ativarProduto($conn, $id) {
    $sql = "UPDATE produtos SET status = 'Ativo' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$id]);
}

function desativarProduto($conn, $id) {
    $sql = "UPDATE produtos SET status = 'Inativo' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$id]);
}

function deletarProduto($conn, $id) {
    $sql = "DELETE FROM produtos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$id]);
}

function listarProdutosEstoqueBaixo($conn, $limite = 5) {
    $sql = "SELECT p.*, c.nome AS categoria_nome
            FROM produtos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            WHERE p.quantidade <= ?
            ORDER BY p.quantidade ASC, p.id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$limite]);
    return $stmt->fetchAll();
}

function pesquisarProdutos($conn, $termo) {
    $sql = "SELECT p.*, c.nome AS categoria_nome
            FROM produtos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            WHERE p.nome LIKE ? OR p.descricao LIKE ? OR c.nome LIKE ?
            ORDER BY p.nome ASC";
    $like = "%{$termo}%";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$like, $like, $like]);
    return $stmt->fetchAll();
}

function filtrarProdutos($conn, $filtros = []) {
    $sql = "SELECT p.*, c.nome AS categoria_nome
            FROM produtos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            WHERE 1=1";
    $params = [];
    
    if (!empty($filtros['categoria_id'])) {
        $sql .= " AND p.categoria_id = ?";
        $params[] = $filtros['categoria_id'];
    }
    
    if (!empty($filtros['status'])) {
        $sql .= " AND p.status = ?";
        $params[] = $filtros['status'];
    }
    
    if (!empty($filtros['busca'])) {
        $sql .= " AND (p.nome LIKE ? OR p.descricao LIKE ?)";
        $params[] = "%{$filtros['busca']}%";
        $params[] = "%{$filtros['busca']}%";
    }
    
    if (!empty($filtros['ordenar'])) {
        $ordem = in_array($filtros['ordem'], ['ASC', 'DESC']) ? $filtros['ordem'] : 'ASC';
        
        // Mapa de campos permitidos para ordenação
        $camposOrdenacao = [
            'id' => 'p.id',
            'nome' => 'p.nome',
            'preco' => 'p.preco',
            'quantidade' => 'p.quantidade',
            'status' => 'p.status',
            'categoria_nome' => 'c.nome'
        ];
        
        $campo = $camposOrdenacao[$filtros['ordenar']] ?? 'p.id';
        $sql .= " ORDER BY {$campo} {$ordem}";
    } else {
        $sql .= " ORDER BY p.id DESC";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// -------------------------------------------------------
// FORNECEDORES
// -------------------------------------------------------
function inserirFornecedor($conn, $nome, $contato, $email, $telefone) {
    $sql = "INSERT INTO fornecedores (nome, contato, email, telefone, status) VALUES (?, ?, ?, ?, 'Ativo')";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$nome, $contato, $email, $telefone]);
}

function listarFornecedores($conn) {
    $sql = "SELECT * FROM fornecedores ORDER BY nome ASC";
    return $conn->query($sql)->fetchAll();
}

function buscarFornecedor($conn, $id) {
    $sql = "SELECT * FROM fornecedores WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function filtrarFornecedores($conn, $filtros = []) {
    $sql = "SELECT * FROM fornecedores WHERE 1=1";
    $params = [];
    
    if (!empty($filtros['busca'])) {
        $sql .= " AND (nome LIKE ? OR contato LIKE ? OR email LIKE ?)";
        $params[] = "%{$filtros['busca']}%";
        $params[] = "%{$filtros['busca']}%";
        $params[] = "%{$filtros['busca']}%";
    }
    
    if (!empty($filtros['status'])) {
        $sql .= " AND status = ?";
        $params[] = $filtros['status'];
    }
    
    if (!empty($filtros['ordenar'])) {
        $ordem = in_array($filtros['ordem'], ['ASC', 'DESC']) ? $filtros['ordem'] : 'ASC';
        $sql .= " ORDER BY {$filtros['ordenar']} {$ordem}";
    } else {
        $sql .= " ORDER BY nome ASC";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// -------------------------------------------------------
// MOVIMENTAÇÕES (Entrada/Saída de Estoque)
// -------------------------------------------------------
function registrarMovimentacao($conn, $produto_id, $tipo, $quantidade, $observacao = null) {
    $sql = "INSERT INTO movimentacoes (produto_id, tipo, quantidade, observacao) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$produto_id, $tipo, $quantidade, $observacao]);
}

function listarMovimentacoes($conn) {
    $sql = "SELECT m.*, p.nome AS produto_nome
            FROM movimentacoes m
            INNER JOIN produtos p ON m.produto_id = p.id
            ORDER BY m.data_movimento DESC
            LIMIT 100";
    return $conn->query($sql)->fetchAll();
}

function listarMovimentacoesPorProduto($conn, $produto_id) {
    $sql = "SELECT m.*, p.nome AS produto_nome
            FROM movimentacoes m
            INNER JOIN produtos p ON m.produto_id = p.id
            WHERE m.produto_id = ?
            ORDER BY m.data_movimento DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$produto_id]);
    return $stmt->fetchAll();
}

function filtrarMovimentacoes($conn, $filtros = []) {
    $sql = "SELECT m.*, p.nome AS produto_nome
            FROM movimentacoes m
            INNER JOIN produtos p ON m.produto_id = p.id
            WHERE 1=1";
    $params = [];
    
    if (!empty($filtros['tipo'])) {
        $sql .= " AND m.tipo = ?";
        $params[] = $filtros['tipo'];
    }
    
    if (!empty($filtros['produto_id'])) {
        $sql .= " AND m.produto_id = ?";
        $params[] = $filtros['produto_id'];
    }
    
    if (!empty($filtros['data_inicio'])) {
        $sql .= " AND DATE(m.data_movimento) >= ?";
        $params[] = $filtros['data_inicio'];
    }
    
    if (!empty($filtros['data_fim'])) {
        $sql .= " AND DATE(m.data_movimento) <= ?";
        $params[] = $filtros['data_fim'];
    }
    
    if (!empty($filtros['ordenar'])) {
        $ordem = in_array($filtros['ordem'], ['ASC', 'DESC']) ? $filtros['ordem'] : 'DESC';
        
        // Mapa de campos permitidos para ordenação
        $camposOrdenacao = [
            'id' => 'm.id',
            'produto_nome' => 'p.nome',
            'tipo' => 'm.tipo',
            'quantidade' => 'm.quantidade',
            'data_movimento' => 'm.data_movimento'
        ];
        
        $campo = $camposOrdenacao[$filtros['ordenar']] ?? 'm.data_movimento';
        $sql .= " ORDER BY {$campo} {$ordem}";
    } else {
        $sql .= " ORDER BY m.data_movimento DESC";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// -------------------------------------------------------
// ESTATÍSTICAS / DASHBOARD
// -------------------------------------------------------
function contarTotalProdutos($conn) {
    return $conn->query("SELECT COUNT(*) AS total FROM produtos WHERE status = 'Ativo'")->fetch()['total'];
}

function contarTotalCategorias($conn) {
    return $conn->query("SELECT COUNT(*) AS total FROM categorias WHERE status = 'Ativo'")->fetch()['total'];
}

function contarProdutosEstoqueBaixo($conn, $limite = 5) {
    $sql = "SELECT COUNT(*) AS total FROM produtos WHERE quantidade <= ? AND status = 'Ativo'";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$limite]);
    return $stmt->fetch()['total'];
}

function somarEstoqueTotal($conn) {
    return $conn->query("SELECT SUM(preco * quantidade) AS total FROM produtos WHERE status = 'Ativo'")->fetch()['total'];
}

function somarQuantidadeTotal($conn) {
    return $conn->query("SELECT SUM(quantidade) AS total FROM produtos WHERE status = 'Ativo'")->fetch()['total'];
}

function listarUltimasMovimentacoes($conn, $limite = 10) {
    $sql = "SELECT m.*, p.nome AS produto_nome
            FROM movimentacoes m
            INNER JOIN produtos p ON m.produto_id = p.id
            ORDER BY m.data_movimento DESC
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$limite]);
    return $stmt->fetchAll();
}

// -------------------------------------------------------
// RELATÓRIOS / ESTOQUE
// -------------------------------------------------------
function listarEstoqueCompleto($conn, $ordenar = 'id', $ordem = 'ASC') {
    $sql = "SELECT p.*, c.nome AS categoria_nome
            FROM produtos p
            LEFT JOIN categorias c ON p.categoria_id = c.id";
    
    $camposOrdenacao = [
        'id' => 'p.id',
        'nome' => 'p.nome',
        'categoria_nome' => 'c.nome',
        'preco' => 'p.preco',
        'quantidade' => 'p.quantidade',
        'status' => 'p.status'
    ];
    
    $campo = $camposOrdenacao[$ordenar] ?? 'p.id';
    $ordem = in_array($ordem, ['ASC', 'DESC']) ? $ordem : 'ASC';
    
    $sql .= " ORDER BY {$campo} {$ordem}";
    return $conn->query($sql)->fetchAll();
}

function contarProdutosPorStatus($conn) {
    $sql = "SELECT status, COUNT(*) AS total FROM produtos GROUP BY status";
    return $conn->query($sql)->fetchAll();
}

function listarProdutosPorCategoria($conn) {
    $sql = "SELECT c.nome AS categoria, COUNT(p.id) AS total, SUM(p.quantidade) AS estoque, SUM(p.preco * p.quantidade) AS valor_total
            FROM categorias c
            LEFT JOIN produtos p ON p.categoria_id = c.id AND p.status = 'Ativo'
            GROUP BY c.id, c.nome
            ORDER BY total DESC";
    return $conn->query($sql)->fetchAll();
}

function listarMovimentacoesPeriodo($conn, $dias = 30) {
    $sql = "SELECT DATE(data_movimento) AS data,
                   SUM(CASE WHEN tipo = 'entrada' THEN quantidade ELSE 0 END) AS entradas,
                   SUM(CASE WHEN tipo = 'saida' THEN quantidade ELSE 0 END) AS saidas
            FROM movimentacoes
            WHERE data_movimento >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY DATE(data_movimento)
            ORDER BY data DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$dias]);
    return $stmt->fetchAll();
}

function listarTopMovimentacoes($conn, $limite = 10) {
    $sql = "SELECT p.nome AS produto_nome,
                   COUNT(m.id) AS total_mov,
                   SUM(CASE WHEN m.tipo = 'entrada' THEN m.quantidade ELSE 0 END) AS total_entradas,
                   SUM(CASE WHEN m.tipo = 'saida' THEN m.quantidade ELSE 0 END) AS total_saidas
            FROM movimentacoes m
            INNER JOIN produtos p ON m.produto_id = p.id
            GROUP BY m.produto_id, p.nome
            ORDER BY total_mov DESC
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$limite]);
    return $stmt->fetchAll();
}

function somarValorEstoque($conn) {
    $sql = "SELECT SUM(preco * quantidade) AS total FROM produtos WHERE status = 'Ativo'";
    return $conn->query($sql)->fetch()['total'] ?? 0;
}

function contarCategoriasAtivas($conn) {
    $sql = "SELECT COUNT(*) AS total FROM categorias WHERE status = 'Ativo'";
    return $conn->query($sql)->fetch()['total'];
}

function contarFornecedoresAtivos($conn) {
    $sql = "SELECT COUNT(*) AS total FROM fornecedores WHERE status = 'Ativo'";
    return $conn->query($sql)->fetch()['total'];
}

function contarMovimentacoesRecentes($conn, $dias = 30) {
    $sql = "SELECT COUNT(*) AS total FROM movimentacoes WHERE data_movimento >= DATE_SUB(CURDATE(), INTERVAL ? DAY)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$dias]);
    return $stmt->fetch()['total'];
}

// -------------------------------------------------------
// USUÁRIOS (Login)
// -------------------------------------------------------
function buscarUsuarioPorEmail($conn, $email) {
    $sql = "SELECT * FROM usuarios WHERE email = ? AND status = 'Ativo'";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    return $stmt->fetch();
}