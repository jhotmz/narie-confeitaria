<?php
class Pedido {
    private $pdo;
    private $id_usuario;
    private $sessao_id;

    public function __construct($pdo, $id_usuario) {
        $this->pdo = $pdo;
        $this->id_usuario = $id_usuario;
        
    }

    // Retorna as informações do pedido (apenas um)
    public function infoPedido($pedido_id) {
        $sql = "SELECT 
                    p.id AS pedido_id,
                    p.total,
                    p.endereco_entrega,
                    p.observacoes,
                    p.status,
                    p.sessao_id,
                    p.nome_entrega,
                    p.metodo_pagamento,
                    p.telefone,
                    p.data_atualizacao,
                    p.bairro,
                    u.nome AS usuario_nome
                FROM pedidos p
                LEFT JOIN usuario u ON p.usuario_id = u.id
                WHERE p.id = :pedido_id
                AND (p.usuario_id = :usuario_id OR p.sessao_id = :sessao_id)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":pedido_id", $pedido_id);
        $stmt->bindParam(":usuario_id", $this->id_usuario);
        $stmt->bindParam(":sessao_id", $this->id_usuario);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Retorna os itens de um pedido
    public function itensPedido($pedido_id) {
        $sql = "SELECT 
                    i.produto_id,
                    i.quantidade,
                    i.preco_unitario,

                    pr.nome AS produto_nome,
                    pr.preco AS produto_preco,
                    pr.img_produto AS produto_imagem,
                    pr.categoria AS produto_categoria
                FROM pedido_itens i
                JOIN produtos pr ON i.produto_id = pr.id
                WHERE i.pedido_id = :pedido_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":pedido_id", $pedido_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // vários itens
    }
}

