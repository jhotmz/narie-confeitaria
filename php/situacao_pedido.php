<?php
require_once('../script/dbd.php');

$Dbh = new Dbh();
$pdo = $Dbh->getPDO();
$confirmado = "confirmado";
if($_SERVER['REQUEST_METHOD'] === "POST"){
if(isset($_POST['novo_status']) ){
    $id_pedido = htmlspecialchars($_POST['id_pedido']);
    $novo_status = htmlspecialchars($_POST['novo_status']);
    $query = $pdo->prepare("UPDATE pedidos SET status = :status WHERE id = :id");
    $query->bindParam(":status", $novo_status);
    $query->bindParam(":id", $id_pedido);
    $query->execute();
    // echo "Pedido atualizado para $novo_status!";
    header("location: ../pag/pedidos_adm.php");
}else{
    $id_pedido = htmlspecialchars($_POST['id_pedido']);
    $cancelado = "cancelado";
   $query_deletar = $pdo->prepare("UPDATE pedidos SET status = :status WHERE id = :id");
   $query_deletar->bindParam(":id", $id_pedido);
   $query_deletar->bindParam(":status", $cancelado);
   $query_deletar->execute();
//    echo "Pedido cancelado!";
   header("location: ../pag/pedidos_adm.php");
}
}else{
    Header("Location: ../index.php");
}