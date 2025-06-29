-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS sistema_agendamento;

USE sistema_agendamento;

-- Tabela de usuários
CREATE TABLE
    IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL,
        tipo ENUM ('admin', 'medio', 'comum') NOT NULL DEFAULT 'comum',
        ativo TINYINT (1) NOT NULL DEFAULT 1,
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

-- Tabela de agendamentos
CREATE TABLE
    IF NOT EXISTS agendamentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        veiculo VARCHAR(100) NOT NULL,
        empreiteira VARCHAR(100) NOT NULL,
        tipo ENUM ('AGENDADO', 'EMERGENCIAL', 'REAGENDADO') NOT NULL DEFAULT 'AGENDADO',
        identificador VARCHAR(50) NOT NULL,
        data_agendamento DATETIME NOT NULL,
        documento VARCHAR(50),
        confirmacao TINYINT (1) NOT NULL DEFAULT 0,
        usuario_id INT NOT NULL,
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
    );

-- Inserir usuário administrador padrão (senha: admin123)
INSERT INTO
    usuarios (nome, email, senha, tipo)
VALUES
    (
        'Administrador',
        'admin@sistema.com',
        '$2y$10$8tGmHMJQYY9LQpOvQIFZ5OcUgwrLG7HZQnGGb4fBhxZSj0qY9QwDO',
        'admin'
    );