<?php
session_start();

if(!$_GET['pedido']){
  header("location: ../index.php");
}else{
  $id_pedido = $_GET['pedido'];
}

require_once("../script/dbd.php");
$Dbh = new Dbh();
$pdo = $Dbh->getPdo();

require_once('../function/pedidosClass.php');

$pedido_id = $_GET['pedido']; // ou como estiver passando o ID na URL
$sessao_id = session_id();

if(isset($_SESSION['user_id'])){
$usuario_id = $_SESSION['user_id'];
}else{
  $usuario_id = $sessao_id;
}



// Consulta para verificar se o pedido pertence ao usuário logado
$sql = "SELECT id FROM pedidos 
        WHERE id = :id 
        AND (usuario_id = :id_usuario OR sessao_id = :sessao_id)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":id", $pedido_id);
$stmt->bindParam(":id_usuario", $usuario_id);
$stmt->bindParam(":sessao_id", $usuario_id);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Se não encontrar, o pedido não pertence ao usuário
if (!$result) {
  echo "<p>❌ Você não tem permissão para ver este pedido.</p>";
  exit;
}

//Se o usuário estiver logado, o WHERE na função recebe o id do usuario logado, se não, recebe da sessão id
if(isset($_SESSION['user_id'])){
$pedido = new Pedido($pdo, $_SESSION['user_id']);
}else{
  //recebe id da sessão
  $pedido = new Pedido($pdo, $sessao_id);
}

$pedidos = $pedido->infoPedido($pedido_id);


$itensPedido = $pedido->itensPedido($pedido_id);

$nomeExibicao = $pedidos['usuario_nome'] ?? $pedidos['nome_entrega'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status do pedido</title>
    <link rel="stylesheet" href="../css/sucessoPedido.css">
</head>
<body>
  <div class="wrap">
    <nav>
      <a href="pedidos.php"><img class="voltar" src="../img/left-arrow.png" alt="Voltar"></a>
      <div class="small">Confeitaria • Pedido #<strong><?=$pedidos['pedido_id']?></strong></div>
    </nav>

    <header class="page-title">
      <h1>Status do Pedido</h1>
      <div class="small">Atualizado em: <strong>    <?= date('d/m/Y H:i', strtotime($pedidos['data_atualizacao'])) ?>
</strong></div>
    </header>

    <!-- Defina aqui o status atual: pendente, confirmado, preparando, saiu_para_entrega, entregue, cancelado -->
    <main data-status="preparando" class="grid">
      <!-- LEFT: timeline + details -->
      <section class="glass timeline" aria-labelledby="timelineTitle">
        <h3 id="timelineTitle" style="margin-top:0;color:var(--accent-start)">Progresso do Pedido</h3>

        <div style="position:relative;">
          <div class="steps" id="steps">
            <div class="step" data-step="pendente">
              <div class="icon">🕒</div>
              <div class="label">Pendente</div>
            </div>

            <div class="step" data-step="confirmado">
              <div class="icon">✅</div>
              <div class="label">Confirmado</div>
            </div>

            <div class="step" data-step="preparando">
              <div class="icon">🧁</div>
              <div class="label">Preparando</div>
            </div>

            <div class="step" data-step="saiu_para_entrega">
              <div class="icon">🏍️</div>
              <div class="label">Saiu p/ Entrega</div>
            </div>

            <div class="step" data-step="entregue">
              <div class="icon">📦</div>
              <div class="label">Entregue</div>
            </div>
          </div>
        </div>

        <p class="status-note">Status atual: <strong id="statusText"><?=$pedidos['status']?></strong></p>

        <hr style="border:none;border-top:1px solid #f0e6df;margin:18px 0;">

        <div class="small" style="margin-bottom:8px;color:var(--muted)"><strong>Observações do pedido</strong></div>
        <div style="background:#fff8f3;padding:12px;border-radius:8px;border-left:4px solid var(--accent-end);color:var(--muted)">
          <?= $pedidos['observacoes']?>
        </div>

      </section>

      <!-- RIGHT: summary -->
      <aside class="glass summary" aria-labelledby="summaryTitle">
        <div class="order-card">
          <div class="order-meta">
            <small>Pedido #<?=$pedidos['pedido_id']?></small>
  
            <h2></h2>
            <small>Cliente: <?=$nomeExibicao?><strong></strong></small>
            <small>Telefone: <?=$pedidos['telefone']?><strong></strong></small>
          </div>
        </div>

        <div class="address">
          <div style="display:flex;align-items:flex-start;gap:10px;">
            <div style="font-size:18px;line-height:1">📍</div>
            <div>
              <div style="font-weight:600;color:#3b2a20"><?=$pedidos['endereco_entrega']?></div>
              <div style="color:var(--muted);margin-top:4px"><?=$pedidos['bairro']?></div>
            </div>
          </div>
        </div>

        <div style="margin-bottom:8px;font-weight:600;color:var(--accent-start)">Itens</div>
        <table class="items" role="table" aria-label="Itens do pedido">
          <tbody>
            <?php
             foreach ($itensPedido as $item) {
            ?>
            <tr>
              <td class="name"><?=$item['produto_nome']?></td>
              <td class="qty"><?=$item['quantidade']?></td>
              <td class="price"><?=$item['preco_unitario']?></td>
            </tr>
            <?php
             }
            ?>
          </tbody>
        </table>

                <div class="metodo-pagamento">
          <div>Metodo de pagamento</div>
          <div><?=$pedidos['metodo_pagamento']?></div>
        </div>

        <div class="total-row">
          <div>Total</div>
          <div>R$ <?=$pedidos['total']?></div>
        </div>


  

        <div class="actions">
          <button class="btn btn-ghost" onclick="window.location.href='contato.php'">Falar com a loja</button>
        </div>

        <div class="status-note">
          Previsão: <strong id="eta">30-45 min</strong> • Entregador: <strong id="entregador">Em definição</strong>
        </div>
      </aside>
    </main>
  </div>

</body>
</html>