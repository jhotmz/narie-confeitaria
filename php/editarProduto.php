<?php
require_once("../script/dbd.php");
$Dbh = new Dbh();
$pdo = $Dbh->getPdo();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $idProduto = $_POST['idProduto'];
    $nomeProduto = $_POST['nomeProduto'];
    $precoProduto = $_POST['precoProduto'];
    $categoriaProduto = $_POST['categoriaProduto'];
  

    // Verifica se há imagem nova
    if (!empty($_FILES['imagemProduto']['name'])) {
        $imagem = $_FILES['imagemProduto'];
        $pasta = "../uploads/";
        $nomeImg = uniqid() . "-" . basename($imagem['name']);
        $caminhoCompleto = $pasta . $nomeImg;

        // Verifica tipo e tamanho
        $tipoPermitido = ['image/jpeg', 'image/png', 'image/webp'];
        if (in_array($imagem['type'], $tipoPermitido) && $imagem['size'] <= (5 * 1024 * 1024)) {
            // Busca imagem antiga pra excluir depois
            $stmtAntiga = $pdo->prepare("SELECT img_produto FROM produtos WHERE id = :id");
            $stmtAntiga->bindParam(":id", $idProduto);
            $stmtAntiga->execute();
            $imagemAntiga = $stmtAntiga->fetchColumn();

            // Faz upload
            if (move_uploaded_file($imagem['tmp_name'], $caminhoCompleto)) {
                // Atualiza no banco
                $sql = "UPDATE produtos 
                        SET nome = :nome, preco = :preco, categoria = :categoria, img_produto = :img 
                        WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":id", $idProduto);
                $stmt->bindParam(":nome", $nomeProduto);
                $stmt->bindParam(":preco", $precoProduto);
                $stmt->bindParam(":categoria", $categoriaProduto);
                $stmt->bindParam(":img", $nomeImg);
                $stmt->execute();

                // Exclui imagem antiga (somente se existir)
           if ($imagemAntiga !== $nomeImg && file_exists("../uploads/" . $imagemAntiga)) {
                    unlink("../uploads/" . $imagemAntiga);
                }
            }
        } else {
            echo "Formato ou tamanho de imagem inválido!";
            exit;
        }
    } else {
        // Atualiza só os dados
        $sql = "UPDATE produtos 
                SET nome = :nome, preco = :preco, categoria = :categoria
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $idProduto);
        $stmt->bindParam(":nome", $nomeProduto);
        $stmt->bindParam(":preco", $precoProduto);
        $stmt->bindParam(":categoria", $categoriaProduto);
        $stmt->execute();
    }

    header("Location: ../pag/gerenciar.php");
    exit;
} else {
    header("Location: ../index.php");
    exit;
}
