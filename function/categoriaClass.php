<?php
class Categoria
{
        private $pdo;
        public $categoria;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function categoriaProduto(){
        $stmt = $this->pdo->prepare("
            SELECT p.id, p.nome, p.preco, c.nomeCategoria AS categoria
            FROM produtos p
            JOIN categoriaproduto c ON p.categoria = c.id
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function chamarCategoriaProduto(){
        $stmt = $this->pdo->prepare("SELECT id, nomeCategoria FROM categoriaproduto");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarProdutoCategoria($categoria){
        $stmt = $this->pdo->prepare("SELECT id, img_produto, nome, preco FROM produtos WHERE categoria = :categoria LIMIT 10");
        $stmt->bindParam(":categoria", $categoria);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}