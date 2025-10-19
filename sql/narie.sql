create database db_narie;
use db_narie;

create table usuario(
id int auto_increment not null primary key,
nome varchar(40),
sobrenome varchar(255),
email varchar(255),
senha varchar(255),
cpf varchar(12),
role ENUM('admin', 'user', 'moderator') DEFAULT 'user'
);

create table endereco_usuario(
id int auto_increment primary key not null,
endereco varchar(255),
id_usuario int,
FOREIGN KEY (id_usuario) REFERENCES usuario(id)
);


create table categoriaProduto(
id int auto_increment not null primary key,
nomeCategoria varchar(255)
);


create table produtos(
id int auto_increment primary key not null,
img_produto varchar(255),
nome varchar(255),
preco int, 
categoria int,
estoque int,
CONSTRAINT fK_id_usuario FOREIGN KEY (categoria) REFERENCES categoriaProduto (id)
   ON DELETE CASCADE 
   ON UPDATE CASCADE
);

CREATE TABLE sacola (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL, 
    sessao_id VARCHAR(255),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

create table sacola_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sacola_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT DEFAULT 1,
    FOREIGN KEY (sacola_id) REFERENCES sacola (id),
    FOREIGN KEY (produto_id) REFERENCES produtos (id)
);

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    metodo_pagamento ENUM('PIX', 'Cartão', 'Dinheiro') NOT NULL,
    status ENUM('pendente', 'confirmado', 'preparando', 'pronto_entrega', 'saiu_para_entrega', 'entregue', 'cancelado') DEFAULT 'pendente',
    endereco_entrega VARCHAR(255) NOT NULL,
    observacoes TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id)
);

CREATE TABLE pedido_itens (
    id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);


