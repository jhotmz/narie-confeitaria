<?php

class Produto
{
    private $pdo;
    private $id_categoria;

    public function __construct($pdo, $id_categoria) {
        $this->pdo = $pdo;
        $this->id_categoria = $id_categoria;
    }

    public function listarTodos() {
        $stmt = $this->pdo->prepare("SELECT * FROM produtos");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contar() {
        $stmt = $this->pdo->prepare("SELECT * FROM produtos");
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function produtosFiltro() {
        $stmt = $this->pdo->prepare("SELECT * FROM produtos WHERE categoria = :id_categoria");
        $stmt->bindParam(":id_categoria", $this->id_categoria);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
}

