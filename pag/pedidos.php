<?php
session_start();



require_once("../script/dbd.php");
$Dbh = new Dbh();
$pdo = $Dbh->getPdo();

if(isset($_SESSION['user_id'])){
    $id_usuario = $_SESSION['user_id'];
}else{
    $id_usuario = null;
    $sessao_id = session_id();
}

$consulta_pedido = $pdo->prepare("SELECT * FROM pedidos WHERE usuario_id = :id OR sessao_id = :sessao_id ORDER BY id DESC");
$consulta_pedido->bindParam(":id", $id_usuario);
$consulta_pedido->bindParam(":sessao_id", $sessao_id);
$consulta_pedido->execute();
$consultaPedidos = $consulta_pedido->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Pedidos</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Ícones -->
    <script src="https://kit.fontawesome.com/yourkit.js" crossorigin="anonymous"></script>

    <style>
        body {
            background-color: #f8f9fa;
            font-family: "Poppins", sans-serif;
        }
        .pedido-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.2s;
        }
        .pedido-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }
        .pedido-status {
            font-weight: 600;
            text-transform: capitalize;
        }
        .pedido-status.preparando { color: #f39c12; }
        .pedido-status.entregue { color: #27ae60; }
        .pedido-status.cancelado { color: #e74c3c; }
        .pedido-status.pendente { color: black; }
        .pedido-link {
            text-decoration: none;
            color: inherit;
        }
                .voltar{
      width: 25px;
      height: 25px;
      gap: 5px;
    }
    </style>
</head>

<body>
    <div class="container my-5">
                <nav>
        <a href="../index.php"><img class="voltar" src="../img/left-arrow.png" alt="Voltar"></a>
    </nav>
        <h2 class="mb-4 text-center fw-bold"> Meus Pedidos</h2>

        <?php if (count($consultaPedidos) === 0): ?>
            <div class="text-center mt-5">
                <p class="text-muted">Você ainda não fez nenhum pedido.</p>
                <a href="../index.php" class="btn btn-primary mt-3">Fazer Pedido</a>
            </div>
        <?php else: ?>
            <div class="row gy-4">
                <?php foreach ($consultaPedidos as $pedido): ?>
                    <div class="col-md-6 col-lg-4">
                        <a href="sucessoPedido.php?pedido=<?= $pedido['id'] ?>" class="pedido-link">
                            <div class="card pedido-card p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="mb-0">#<?= $pedido['id'] ?></h5>
                 <span class="pedido-status <?= ($pedido['status'] == 'pronto_entrega') ? 'preparando' : strtolower($pedido['status']) ?>">
                    <?= ($pedido['status'] == 'pronto_entrega') ? 'Preparando' : ucfirst($pedido['status']) ?>
                 </span>
                                </div>
                                <p class="mb-1 text-muted">
                                    <i class="fa-solid fa-calendar-day me-1"></i>
                                    <?= date('d/m/Y H:i', strtotime($pedido['data_criacao'] ?? $pedido['created_at'] ?? 'now')) ?>
                                </p>
                                <p class="mb-2 text-muted">
                                    <i class="fa-solid fa-location-dot me-1"></i>
                                    <?= htmlspecialchars($pedido['endereco_entrega'] ?? 'Endereço não informado') ?>
                                </p>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small>Método: <strong><?= ucfirst($pedido['metodo_pagamento']) ?></strong></small>
                                    <small class="fw-bold text-success">Total: R$ <?= number_format($pedido['total'], 2, ',', '.') ?></small>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://kit.fontawesome.com/b7b6c03e3a.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
