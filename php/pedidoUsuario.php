<?php
session_start();

require_once('../script/dbd.php');
$Dbh = new Dbh();
$pdo = $Dbh->getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idSacola = $_POST['id_sacola'] ?? null;

    if(isset($_SESSION['user_id'])){
    $usuarioId = $_SESSION['user_id']; // supondo que você salva o ID do usuário na sessão
    }else{
        $usuarioId = null;
    }

    $formaPagamento = $_POST['forma_pagamento'] ?? null; // pode vir do form
    $endereco = $_POST['endereco'];
    $bairro = $_POST['bairro'];
    $telefone = $_POST['telefone'];
    $observacao_pedido = $_POST['observacao_pedido'];
    
    //Para usuários não logados
    $sessao_id = session_id() ?? null;
    $nomeEntrega = htmlspecialchars($_POST['nomeEntrega']) ?? "Usuário não informado";

    if (!$idSacola || !$sessao_id){
        die("Sacola ou usuário não identificado.");
    }

    try {
        $pdo->beginTransaction();

        // 1. Buscar itens da sacola
        $stmt = $pdo->prepare("SELECT si.produto_id, si.quantidade, p.nome, p.preco 
                               FROM sacola_itens si
                               JOIN produtos p ON p.id = si.produto_id
                               WHERE si.sacola_id = :id_sacola");
        $stmt->execute([':id_sacola' => $idSacola]);
        $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$itens) {
            throw new Exception("Sacola vazia.");
        }

        // calcular total
        $total = 0;
        foreach ($itens as $item) {
            $total += $item['preco'] * $item['quantidade'];
        }

        // 2. Inserir o pedido
        $stmt = $pdo->prepare("INSERT INTO pedidos 
            (usuario_id, sessao_id, total, metodo_pagamento, status, endereco_entrega, data_criacao, nome_entrega, telefone, bairro, observacoes)
            VALUES (:usuario_id, :sessao_id, :total, :metodo_pagamento, 'pendente', :endereco, NOW(), :nome_entrega, :telefone, :bairro, :observacao_pedido)");
        $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':sessao_id' => $sessao_id,
            ':total' => $total,
            ':metodo_pagamento' => $formaPagamento,
            ':endereco' => $endereco,
            ':nome_entrega' => $nomeEntrega,
            ':telefone' => $telefone,
            ':bairro' => $bairro,
            ':observacao_pedido' => $observacao_pedido,
        ]);

        $pedidoId = $pdo->lastInsertId();

        // 3. Inserir itens do pedido
        $stmt = $pdo->prepare("INSERT INTO pedido_itens 
            (pedido_id, produto_id, quantidade, preco_unitario)
            VALUES (:pedido_id, :produto_id, :quantidade, :preco_unitario)");

        foreach ($itens as $item) {
            $stmt->execute([
                ':pedido_id' => $pedidoId,
                ':produto_id' => $item['produto_id'],
                ':quantidade' => $item['quantidade'],
                ':preco_unitario' => $item['preco']
            ]);
        }

        // 4. Limpar ou finalizar sacola
        $stmt = $pdo->prepare("DELETE FROM sacola_itens WHERE sacola_id = :id_sacola");
        $stmt->execute([':id_sacola' => $idSacola]);

        $pdo->commit();
        //zerar informações do session
        if(isset($_SESSION['user_id'])){
            unset($_SESSION['nomeEntrega']);
            unset($_SESSION['endereco']);
            unset($_SESSION['bairro']);
            unset($_SESSION['telefone']);
        }

        // redirecionar ou mostrar sucesso
        header("Location: ../pag/sucessoPedido.php?pedido=$pedidoId");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro ao confirmar pedido: " . $e->getMessage());
    }
} else {
    die("Requisição inválida.");
}
