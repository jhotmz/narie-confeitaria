<?php
if ($_SRVER['REQUEST_METHOD' === "POST"]) {
    require_once("../script/dbd.php");
    $Dbh = new Dbh();
    $pdo = $Dbh->getPdo();
    $idProduto = $_POST['excluirProduto'];

    $deletarProduto = $pdo->prepare("DELETE FROM produtos WHERE id = :id");
    $deletarProduto->bindParam(":id", $idProduto);
    $deletarProduto->execute();
    header("location: ../pag/gerenciar.php");
} else {
    Header("location: ../index.php");
}
