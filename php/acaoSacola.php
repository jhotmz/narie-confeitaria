<?php
session_start();
require_once("../script/dbd.php");

$Dbh = new Dbh();
$pdo = $Dbh->getPdo();

$user_id = $_SESSION['user_id'] ?? null;
$id_produto = $_POST['id_produto'] ?? null;
$acao = $_POST['acao'] ?? null;

if (!$id_produto || !$acao) {
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Dados inválidos"
    ]);
    exit;
}

// Pega carrinho da sessão
$sessao_id = session_id();
$stmt = $pdo->prepare("SELECT id FROM sacola WHERE sessao_id = ? OR usuario_id = ?");
$stmt->execute([$sessao_id, $user_id ]);
$carrinho = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$carrinho) {
    echo json_encode([
     "status" => "erro",
     "mensagem" => "Sacola não encontrada!"]);
    exit;
}

$carrinho_id = $carrinho['id'];

// Pega item da sacola
$stmt = $pdo->prepare("SELECT id, quantidade FROM sacola_itens WHERE sacola_id = ? AND id = ?");
$stmt->execute([$carrinho_id, $id_produto]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo json_encode([
        "status" => "erro",
         "mensagem" => "Item não encontrado"
        ]);
    exit;
}

if ($acao === "aumentar") {
    $novaQtd = $item['quantidade'] + 1;
    $stmt = $pdo->prepare("UPDATE sacola_itens SET quantidade = ? WHERE id = ?");
    $stmt->execute([$novaQtd, $item['id']]);
        echo json_encode([
    "status" => "sucesso",
    "mensagem" => "Quantidade atualizada com sucesso!"
]);

} elseif ($acao === "diminuir") {
    $novaQtd = $item['quantidade'] - 1;

    if ($novaQtd < 1) {
        // se zerou, exclui
        $stmt = $pdo->prepare("DELETE FROM sacola_itens WHERE id = ?");
        $stmt->execute([$item['id']]);
    } else {
        $stmt = $pdo->prepare("UPDATE sacola_itens SET quantidade = ? WHERE id = ?");
        $stmt->execute([$novaQtd, $item['id']]);
    }
    echo json_encode([
    "status" => "sucesso",
    "mensagem" => "Quantidade atualizada com sucesso!"
]);
}else{
      $stmt = $pdo->prepare("DELETE FROM sacola_itens WHERE id = ?");
      $stmt->execute([$item['id']]);
          echo json_encode([
    "status" => "sucesso",
    "mensagem" => "Excluido!",
]);
}


