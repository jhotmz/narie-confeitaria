<?php
session_start();
require_once("../script/dbd.php");
$Dbh = new Dbh();
$pdo = $Dbh->getPdo();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Limpa o sessao_id, mantendo a sacola vinculada ao usuário (para persistência)
    $stmt = $pdo->prepare("UPDATE sacola SET sessao_id = NULL WHERE usuario_id = ?");
    $stmt->execute([$user_id]);
}

session_destroy();
header("Location: ../index.php");
exit;