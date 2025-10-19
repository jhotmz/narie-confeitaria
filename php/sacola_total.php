<?php
session_start();
require_once("../script/dbd.php");
$Dbh = new Dbh();
$pdo = $Dbh->getPdo();

$user_id = $_SESSION['user_id'] ?? null;
$sessao_id = session_id();

$stmt = $pdo->prepare($user_id ? "SELECT id FROM sacola WHERE usuario_id = ?" : "SELECT id FROM sacola WHERE sessao_id = ?");
$stmt->execute($user_id ? [$user_id] : [$sessao_id]);
$carrinho = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$carrinho) {
    echo json_encode(['quantidade' => 0, 'total' => 0]);
    exit;
}

$carrinho_id = $carrinho['id'];

$stmt = $pdo->prepare("
    SELECT SUM(si.quantidade) AS total_itens,
           SUM(si.quantidade * p.preco) AS valor_total
    FROM sacola_itens si
    INNER JOIN produtos p ON si.produto_id = p.id
    WHERE si.sacola_id = ?
");
$stmt->execute([$carrinho_id]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'quantidade' => (int)($res['total_itens'] ?? 0),
    'total' => (float)($res['valor_total'] ?? 0)
]);
