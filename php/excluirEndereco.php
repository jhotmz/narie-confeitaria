<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    require_once("../script/dbd.php");
    $Dbh = new Dbh();
    $pdo = $Dbh->getPdo();

    $id = $_POST['id_endereco'];

    $query_deletar = $pdo->prepare("DELETE FROM endereco_usuario WHERE id = :id");
    $query_deletar->bindParam(":id", $id);

    if ($query_deletar->execute()) {
        echo json_encode([
            "status" => "sucesso",
            "mensagem" => "Endereço excluído!"
        ]);
        exit;
    } else {
        echo json_encode([
            "status" => "erro",
            "mensagem" => "Falha ao excluir o endereço."
        ]);
        exit;
    }

} else {
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Você não tem permissão para acessar esta página!"
    ]);
    exit;
}
