-- Active: 1777556819615@@127.0.0.1@3306@controle_produtos
-- ============================================
-- Banco de Dados: controle_produtos
-- Schema Completo
-- ============================================

CREATE DATABASE IF NOT EXISTS controle_produtos
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE controle_produtos;

-- --------------------------------------------
-- Tabela: categorias
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(255) DEFAULT NULL,
    status VARCHAR(20) DEFAULT 'Ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------
-- Tabela: produtos
-- Relacionamento: categorias (1) → produtos (N)
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    categoria_id INT DEFAULT NULL,
    preco DECIMAL(10, 2) NOT NULL,
    quantidade INT NOT NULL,
    descricao VARCHAR(255) DEFAULT NULL,
    status VARCHAR(20) DEFAULT 'Ativo',
    imagem VARCHAR(255) DEFAULT NULL,
    CONSTRAINT fk_produtos_categoria
        FOREIGN KEY (categoria_id)
        REFERENCES categorias(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------
-- Tabela: fornecedores
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS fornecedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    contato VARCHAR(100) DEFAULT NULL,
    email VARCHAR(150) DEFAULT NULL,
    telefone VARCHAR(30) DEFAULT NULL,
    status VARCHAR(20) DEFAULT 'Ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE produto_fornecedor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    fornecedor_id INT NOT NULL,
    preco_compra DECIMAL(10,2),
    
    FOREIGN KEY (produto_id)
        REFERENCES produtos(id)
        ON DELETE CASCADE,
        
    FOREIGN KEY (fornecedor_id)
        REFERENCES fornecedores(id)
        ON DELETE CASCADE
);

-- --------------------------------------------
-- Tabela: movimentacoes
-- Relacionamento: produtos (1) → movimentacoes (N)
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS movimentacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    tipo ENUM('entrada', 'saida') NOT NULL,
    quantidade INT NOT NULL,
    data_movimento DATETIME DEFAULT CURRENT_TIMESTAMP,
    observacao VARCHAR(255) DEFAULT NULL,
    CONSTRAINT fk_movimentacoes_produto
        FOREIGN KEY (produto_id)
        REFERENCES produtos(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------
-- Tabela: usuarios (bônus – login)
-- --------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nivel ENUM('admin', 'operador') DEFAULT 'operador',
    status VARCHAR(20) DEFAULT 'Ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------
-- Inserção de dados iniciais (opcional)
-- --------------------------------------------
INSERT INTO categorias (nome, descricao) VALUES
('Eletrônicos', 'Produtos eletrônicos e tecnológicos'),
('Roupas', 'Vestuário em geral'),
('Alimentos', 'Alimentos e bebidas'),
('Móveis', 'Móveis para casa e escritório'),
('Outros', 'Outras categorias');

INSERT INTO usuarios (nome, email, senha, nivel) VALUES
('Administrador', 'admin@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');