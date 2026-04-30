CREATE DATABASE IF NOT EXISTS controle_produtos;
USE controle_produtos;

CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    categoria VARCHAR(50),
    preco DECIMAL(10, 2) NOT NULL,
    quantidade INT NOT NULL,
    descricao VARCHAR(255),
    status VARCHAR(20) DEFAULT 'Ativo'
);

