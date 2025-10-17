-- create_db.sql
CREATE DATABASE IF NOT EXISTS plano_alimentar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE plano_alimentar;

-- tabelas principais
CREATE TABLE IF NOT EXISTS planos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  descricao TEXT,
  data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS refeicoes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  plano_id INT NOT NULL,
  nome VARCHAR(150) NOT NULL,
  horario TIME NULL,
  FOREIGN KEY (plano_id) REFERENCES planos(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS alimentos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  refeicao_id INT NOT NULL,
  nome VARCHAR(150) NOT NULL,
  quantidade DECIMAL(10,3) NOT NULL,
  unidade VARCHAR(40) NOT NULL,
  FOREIGN KEY (refeicao_id) REFERENCES refeicoes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS substituicoes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  refeicao_id INT NOT NULL,
  nome VARCHAR(150) NOT NULL,
  FOREIGN KEY (refeicao_id) REFERENCES refeicoes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS alimentos_substituicao (
  id INT AUTO_INCREMENT PRIMARY KEY,
  substituicao_id INT NOT NULL,
  nome VARCHAR(150) NOT NULL,
  quantidade DECIMAL(10,3) NOT NULL,
  unidade VARCHAR(40) NOT NULL,
  FOREIGN KEY (substituicao_id) REFERENCES substituicoes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS precos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  alimento_nome VARCHAR(150) NOT NULL,
  preco DECIMAL(12,4) NOT NULL,
  unidade VARCHAR(40) NOT NULL,
  data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(alimento_nome, unidade)
);
