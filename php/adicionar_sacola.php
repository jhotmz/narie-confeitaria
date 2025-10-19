<?php
header('Content-Type: application/json');
session_start();

require_once("../script/dbd.php");
$Dbh = new Dbh();
$pdo = $Dbh->getPDO();

$produto_id = $_POST['produto_id'] ?? null;

if (!$produto_id) {
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Produto inválido"
    ]);
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$sessao_id = session_id();

// 1) Se o usuário estiver logado, priorizar carrinho pelo user_id
if ($user_id) {
    $stmt = $pdo->prepare("SELECT id FROM sacola WHERE usuario_id = ?");
    $stmt->execute([$user_id]);
    $carrinho = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$carrinho) {
        // Verifica se havia uma sacola dessa sessão antes do login
        $stmt = $pdo->prepare("SELECT id FROM sacola WHERE sessao_id = ?");
        $stmt->execute([$sessao_id]);
        $sacolaSessao = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($sacolaSessao) {
            // Migra a sacola da sessão para o user_id
            $stmt = $pdo->prepare("UPDATE sacola SET usuario_id = ? WHERE id = ?");
            $stmt->execute([$user_id, $sacolaSessao['id']]);
            $carrinho_id = $sacolaSessao['id'];
        } else {
            // Se não tinha, cria uma nova
            $stmt = $pdo->prepare("INSERT INTO sacola (usuario_id, sessao_id) VALUES (?, ?)");
            $stmt->execute([$user_id, $sessao_id]);
            $carrinho_id = $pdo->lastInsertId();
        }
    } else {
        $carrinho_id = $carrinho['id'];
    }
} else {
    // 2) Se não estiver logado, busca por sessão
    $stmt = $pdo->prepare("SELECT id FROM sacola WHERE sessao_id = ?");
    $stmt->execute([$sessao_id]);
    $carrinho = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$carrinho) {
        $stmt = $pdo->prepare("INSERT INTO sacola (sessao_id) VALUES (?)");
        $stmt->execute([$sessao_id]);
        $carrinho_id = $pdo->lastInsertId();
    } else {
        $carrinho_id = $carrinho['id'];
    }
}

// 3) Adiciona ou incrementa item caso já esteja no carrinho
$stmt = $pdo->prepare("SELECT id, quantidade FROM sacola_itens WHERE sacola_id = ? AND produto_id = ?");
$stmt->execute([$carrinho_id, $produto_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($item) {
    $novaQtd = $item['quantidade'] + 1;
    $stmt = $pdo->prepare("UPDATE sacola_itens SET quantidade = ? WHERE id = ?");
    $stmt->execute([$novaQtd, $item['id']]);
} else {
    $stmt = $pdo->prepare("INSERT INTO sacola_itens (sacola_id, produto_id, quantidade) VALUES (?, ?, 1)");
    $stmt->execute([$carrinho_id, $produto_id]);
}

echo json_encode([
    "status" => "sucesso",
    "mensagem" => "Produto adicionado à sacola!"
]);