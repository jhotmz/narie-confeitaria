<?php
Class Sacola{
        private $pdo;
        private $usuario_id;
        private $sessao_id;
        private $sacola_id;

    public function __construct($pdo, $usuario_id, $sessao_id) {
        $this->pdo = $pdo;
        $this->usuario_id = $usuario_id;
        $this->sessao_id = $sessao_id;
    }

    public function verificarSacola() {
        if ($this->usuario_id) {
            $stmt = $this->pdo->prepare("SELECT id FROM sacola WHERE usuario_id = :usuario_id");
            $stmt->bindParam(":usuario_id", $this->usuario_id);
        } else {
            $stmt = $this->pdo->prepare("SELECT id FROM sacola WHERE sessao_id = :sessao_id");
            $stmt->bindParam(":sessao_id", $this->sessao_id);
        }

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    }

    public function linhasItens($sacola_id){
        $stmt = $this->pdo->prepare("
            SELECT si.id AS item_id, si.quantidade, p.id AS produto_id, p.nome, p.preco, p.img_produto
            FROM sacola_itens si
            INNER JOIN produtos p ON si.produto_id = p.id
            WHERE si.sacola_id = ?
        ");
        $stmt->execute([$sacola_id]);
        return $stmt->rowCount();        
    }

        public function buscarItensSacola($sacola_id){
    // Agora busca os itens
    $stmt = $this->pdo->prepare("
        SELECT si.id AS item_id, si.quantidade, p.id AS produto_id, p.nome, p.preco, p.img_produto
        FROM sacola_itens si
        INNER JOIN produtos p ON si.produto_id = p.id
        WHERE si.sacola_id = ?
    ");
    $stmt->execute([$sacola_id]);
       
    return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
}
