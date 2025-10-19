<?php
session_start();

//CASO não exista login, ou o login ser igual a usuario comum, ir para pagina index
if(!isset($_SESSION['user_id']) || $_SESSION['role_user'] == 'user'){
  header("Location: ../index.php");
}

//garantir url na status
if (!isset($_GET['status']) && !isset($_GET['id_pedido'])) {
    header("Location: pedidos_adm.php?status=pendente");
    exit;
}

require_once("../script/dbd.php");
$Dbh = new Dbh();
$pdo = $Dbh->getPdo();

if(isset($_GET['id_pedido'])){
  $sql = "SELECT 
            p.id AS pedido_id,
            p.total,
            p.endereco_entrega,
            p.observacoes,
            p.status,
            p.sessao_id,
            p.nome_entrega,
            p.metodo_pagamento,
            p.telefone,
            u.nome AS usuario_nome,
            
            i.produto_id,
            i.quantidade,
            i.preco_unitario,
            
            pr.id,
            pr.nome AS produto_nome,
            pr.preco AS produto_preco,
            pr.img_produto AS produto_imagem,
            pr.categoria AS produto_categoria
        FROM pedidos p
        LEFT JOIN usuario u ON p.usuario_id = u.id
        JOIN pedido_itens i ON p.id = i.pedido_id
        JOIN produtos pr ON i.produto_id = pr.id
        WHERE p.id = :id_pedido
        ORDER BY p.id ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id_pedido", $_GET['id_pedido']);
        
}else{
$sql = "SELECT 
            p.id AS pedido_id,
            p.total,
            p.endereco_entrega,
            p.observacoes,
            p.status,
            p.sessao_id,
            p.nome_entrega,
            p.metodo_pagamento,
            p.telefone,
            u.nome AS usuario_nome,
            
            i.produto_id,
            i.quantidade,
            i.preco_unitario,
            
            pr.id,
            pr.nome AS produto_nome,
            pr.preco AS produto_preco,
            pr.img_produto AS produto_imagem,
            pr.categoria AS produto_categoria
        FROM pedidos p
        LEFT JOIN usuario u ON p.usuario_id = u.id
        JOIN pedido_itens i ON p.id = i.pedido_id
        JOIN produtos pr ON i.produto_id = pr.id
        WHERE p.status = :status
        ORDER BY p.id ASC";   
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":status", $_GET['status']);
}

$stmt->execute();
$row = $stmt->rowCount();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pedidosAgrupados = [];

foreach ($pedidos as $row) {
    $id = $row['pedido_id'];
    if (!isset($pedidosAgrupados[$id])) {
        $pedidosAgrupados[$id] = [
            'pedido_id' => $row['pedido_id'],
            'valor_total' => $row['total'],
            'endereco' => $row['endereco_entrega'],
            'status' => $row['status'],
            'usuario_nome' => $row['usuario_nome'],
            'nome_entrega' => $row['nome_entrega'],
            'metodo_pagamento' => $row['metodo_pagamento'],
            'telefone' => $row['telefone'],
            'observacoes' => $row['observacoes'],

            'itens' => []
        ];
    }

  $pedidosAgrupados[$id]['itens'][] = [
    'produto' => $row['produto_id'],
    'nome'       => $row['produto_nome'],
    'imagem'     => $row['produto_imagem'],
    'preco'      => $row['produto_preco'],
    'categoria'  => $row['produto_categoria'],
    'quantidade' => $row['quantidade'],
];

}  
      $fluxoStatus = [
    'pendente' => 'confirmado',
    'confirmado' => 'preparando',
    'preparando' => 'pronto_entrega',
    'pronto_entrega' => 'saiu_para_entrega',
    'saiu_para_entrega' => 'entregue'
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pedidos - Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <link rel="stylesheet" href="../css/pedidos_adm.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"></head>
    <script src="../js/jquery-3.7.1.min.js"></script>
</head>
<body>

  <nav class="nav-voltar">
    <a href="gerenciar_adm.php"><img class="voltar" src="../img/left-arrow.png" alt="Voltar"></a>
  </nav>
  <h1>Gerenciar Pedidos</h1>
  <button type="submit" onclick="recarregarPagina()"  class="btn-recarregar">Recarregar</button>

  <!-- Abas de status -->
   <?php
   if(isset($_GET['status'])){
   ?>
  <div class="status-tabs">
    <button data-status="pendente">Pendentes</button>
    <button data-status="confirmado">Confirmado</button>
    <button data-status="preparando">Preparando</button>
    <button data-status="pronto_entrega">Pronto p/ Entrega</button>
    <button data-status="saiu_para_entrega">Saiu p/ Entrega</button>
    <button data-status="entregue">Entregue</button>
    <button data-status="cancelado">Pedidos cancelados</button>
  </div>
  <?php
   }
  ?>
<form action="<?php echo $_SERVER['PHP_SELF']?>" method="GET" class="pesquisa-form">
  <div class="input-group">
    <input 
      type="text" 
      name="id_pedido" 
      id="id_pedido" 
      class="input-pesquisa" 
      placeholder="Buscar pedido por ID..."
    >
    <button type="submit" class="btn-pesquisar">
      <i class="fas fa-search"></i> Pesquisar
    </button>
  </div>
</form>



  <div class="pedidos-container">
        <?php
        if($row < 1){
          echo "Sem pedidos!";
        }
  foreach($pedidosAgrupados as $pedidos){
    $nomeExibicao = $pedidos['usuario_nome'] ?? $pedidos['nome_entrega'];
  ?>
    <!-- Pedido exemplo -->
    <div class="pedido-card">

      <div class="pedido-header">
        <h2>Pedido # <?=$pedidos['pedido_id']?></h2>
        <i class="fa-solid fa-clock"></i>
      </div>
      <div class="pedido-info">
        <p><strong><i class="fa-solid fa-user"></i></strong> <?=$nomeExibicao?></p>
        <strong><i class="fa-solid fa-cookie"></i></strong>
        <div class="itens-lista">
         <?php  foreach ($pedidos['itens'] as $item) {
        echo "
                {$item['quantidade']}x {$item['nome']} 
                (R$ ".number_format($item['preco']*$item['quantidade'], 2, ',', '.').")<br>
              ";
          } 
          ?>
          </div>
   
        <p><strong><i class="fa-solid fa-location-dot"></i></strong>  <?=$pedidos['endereco']?></p>
        <p><strong><i class="fa-solid fa-money-bill-wave"></i></strong> R$  <?=$pedidos['valor_total']?></p>
        <p><strong><i class="fa-solid fa-credit-card"></i></strong> <?=$pedidos['metodo_pagamento']?></p>
      </div>

      <div class="pedido-status status-<?=$pedidos['status']?>">
        <i class="fa-solid fa-hourglass-half"></i> <?=$pedidos['status']?>
      </div>
      <div class="pedido-actions">
        <button type="button"class="btn-detalhes" data-toggle="modal" data-target="#detalhesPedido" data-id="<?= $pedidos['pedido_id']?>" data-nome="<?= $nomeExibicao?>" data-tipo="<?= $pedidos['metodo_pagamento']?>" data-total="<?= $pedidos['valor_total']?>" data-telefone="<?= $pedidos['telefone']?>" data-observacoes="<?= $pedidos['observacoes']?>">
        <i class="fa-solid fa-eye">
        </i> Detalhes
      </button>
        
         <?php if($pedidos['status'] === 'pendente'){ ?>
      <!-- Botões aprovar/negado -->
      <form action="../php/situacao_pedido.php" method="POST">
        <input type="hidden" name="id_pedido" value="<?= $pedidos['pedido_id'] ?>">
        <button type="submit" name="novo_status" value="confirmado" class="btn-aprovar">
          <i class="fa-solid fa-check"></i> Aprovar
        </button>
        <button type="submit" name="negarPedido" value="1" class="btn-rejeitar" onclick="return confirm('Tem certeza que deseja rejeitar este pedido?');">
          <i class="fa-solid fa-xmark"></i> Rejeitar
        </button>
      </form>
    <?php
    } else if(isset($fluxoStatus[$pedidos['status']])){ ?>
      <!-- Botão avançar para o próximo status -->
      <form action="../php/situacao_pedido.php" method="POST" >
        <input type="hidden" name="id_pedido" value="<?= $pedidos['pedido_id'] ?>">
        <input type="hidden" name="novo_status" value="<?= $fluxoStatus[$pedidos['status']] ?>">
        <button type="submit" class="btn btn-success">
          Avançar para "<?= $fluxoStatus[$pedidos['status']] ?>"
        </button>
        <button type="submit" name="negarPedido" value="1" class="btn-rejeitar" onclick="return confirm('Tem certeza que deseja rejeitar este pedido?');">
          <i class="fa-solid fa-xmark"></i> Cancelar pedido
        </button>
      </form>
    <?php } ?>

      </div>
     
    </div>
  <?php
      }
    ?> 
  </div>


<!-- Modal Detalhes -->
<div class="modal fade" id="detalhesPedido" tabindex="-1" role="dialog" aria-labelledby="detalhesPedidoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="detalhesPedidoLabel">Detalhes do Pedido</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body bg-light">
        <div class="container">
          <div class="row mb-3">
            <div class="col-5 font-weight-bold text-secondary">ID do Pedido:</div>
            <div class="col-7" id="id_pedido">#0001</div>
          </div>

          <div class="row mb-3">
            <div class="col-5 font-weight-bold text-secondary">Cliente:</div>
            <div class="col-7" id="nome_usuario">João Silva</div>
          </div>

          <div class="row mb-3">
            <div class="col-5 font-weight-bold text-secondary">Telefone</div>
            <div class="col-7" id="telefone_usuario"></div>
          </div>

          <div class="row mb-3">
            <div class="col-5 font-weight-bold text-secondary">Método de Pagamento:</div>
            <div class="col-7" id="metodo_pagamento">Pix</div>
          </div>

          <div class="row mb-3">
            <div class="col-5 font-weight-bold text-secondary">Observações do pedido</div>
            <div class="col-7" id="observacao_pedido"></div>
          </div>

          <div class="row mb-2">
            <div class="col-5 font-weight-bold text-secondary">Valor Total:</div>
            <div class="col-7 text-success font-weight-bold" id="valor">R$ 150,00</div>
          </div>

        
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<script>

    //Adicionar valor ao modal
    $(function() {
      $('#detalhesPedido').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nome = button.data('nome');
        var tipo = button.data('tipo');
        var valor = button.data('total');
        var telefone = button.data('telefone');
        var observacoes = button.data('observacoes');
        var modal = $(this);

        // aplicar valor ao id
        modal.find('#id_pedido').text(id);
        modal.find('#nome_usuario').text(nome);
        modal.find('#metodo_pagamento').text(tipo);
        modal.find('#valor').text(valor);
        modal.find('#telefone_usuario').text(telefone);
        modal.find('#observacao_pedido').text(observacoes);

      });

    });
 
    // Selecionar status de entrega 
    document.querySelectorAll('.status-tabs button').forEach(btn => {
    btn.addEventListener('click', function() {
      // remove "ativo" de todos
      document.querySelectorAll('.status-tabs button').forEach(b => b.classList.remove('active'));
      
      // adiciona "ativo" no clicado
      this.classList.add('active');

      // pega o valor do data-status
      let status = this.getAttribute('data-status');

      // atualiza a URL sem recarregar
      // const url = new URL(window.location);
      // url.searchParams.set('status', status);
      // window.history.pushState({}, '', url);


       //recarregar para aplicar o filtro:
       window.location.href = "pedidos_adm.php?status="+status;
    });
  });

  // Mantém o botão correto ativo quando entrar na página
  window.addEventListener("DOMContentLoaded", () => {
    const urlParams = new URLSearchParams(window.location.search);
    const currentStatus = urlParams.get("status");

    if(currentStatus) {
      const activeBtn = document.querySelector(`.status-tabs button[data-status="${currentStatus}"]`);
      if(activeBtn) {
        activeBtn.classList.add("active");
      }
    }
  });
  //Evitar cancelar pedido acidentalmente 
  function confirmarCancelamento() {
    return confirm('Tem certeza que deseja cancelar este pedido?');
    }
    
    function recarregarPagina() {
    // Se você tem uma variável status no PHP:
  
      let status = "<?php echo isset($_GET['status']) ? $_GET['status'] : 'pendente'; ?>"
      window.location.href = "pedidos_adm.php?status=" + status;
}


</script>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
