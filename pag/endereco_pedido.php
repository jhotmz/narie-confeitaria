<?php
session_start();

require_once('../script/dbd.php');
require_once("../function/exibirSacola.php");

$Dbh = new Dbh();
$pdo = $Dbh->getPdo();
$sessao_id = session_id();

//Se o usuário estiver logado, consulta na tabela endereços 
if (isset($_SESSION['user_id'])) {
  $id_usuario = $_SESSION['user_id'];
  $query_endereco = $pdo->prepare("SELECT * FROM endereco_usuario WHERE id_usuario = :id");
  $query_endereco->bindParam(":id", $id_usuario);
  $query_endereco->execute();
  $enderecoUsuario = $query_endereco->fetchAll(PDO::FETCH_ASSOC);
}else{
  $id_usuario = null;
}

$sacola = new Sacola($pdo, $id_usuario, $sessao_id);
$verificarSacola = $sacola->verificarSacola();

//Se existir sacola
if(isset($verificarSacola)){
  $sacola_id = $verificarSacola['id'];
}

$checarItensSacola = $sacola->linhasItens($sacola_id);
if($checarItensSacola < 1){
  Header("Location: sacola.php");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adicionar Endereço</title>
  <link rel="stylesheet" href="../css/endereco_pedido.css">
  <link rel="stylesheet" href="../css/alert.css">

  <script src="../js/jquery-3.7.1.min.js"></script>
</head>

<body>
  <div class="wrap">
    <!-- Botão voltar -->
    <a href="sacola.php" class="btn-voltar">
      <img src="../img/left-arrow.png" alt="Voltar">
      Voltar
    </a>
    <h1>Adicionar Endereço</h1>
    <p>Preencha as informações abaixo para salvar seu endereço de entrega.</p>

    <!-- Nome só aparece se o usuário não estiver logado -->
  <form action="../php/verificarEndereco.php" method="POST">
    


            <?php
    if (!isset($_SESSION['user_id'])) {
    ?>
            <div class="field guest-only">
          <label for="nome">Nome na entrega</label>
          <input type="text" id="nomeEntrega" name="nomeEntrega" placeholder="Ex: João da Silva" value="<?= htmlspecialchars($_POST['nomeEntrega'] ?? '') ?>">
        </div>

      <div class="field">
        <label for="endereco">Endereço</label>
        <input type="text" id="endereco" name="endereco" placeholder="Rua, número, complemento">
      </div>

      <div class="row">
        <div class="field">
          <label for="bairro">Bairro</label>
          <input type="text" id="bairro" name="bairro" placeholder="Ex: Centro">
        </div>

      </div>

      
      <?php
    }
      ?>
        <?php
        if(isset($id_usuario)){
        ?>
<div class="field endereco-wrap">
  <label for="enderecoRegistrado">Endereço</label>
  <div class="select-com-link">
    <select name="enderecoRegistrado" id="enderecoRegistrado">
      <option value="" disabled selected>Selecione</option>
      <?php foreach($enderecoUsuario as $endereco): ?>
        <option value="<?=$endereco['endereco'].', '.$endereco['bairro']?>"><?=$endereco['endereco'].", ".$endereco['bairro']?></option>
      <?php endforeach; ?>
    </select>
    <a href="enderecoUsuario.php" class="add-endereco">Adicionar endereço</a>
  </div>
</div>

      
        <?php
        }
        ?>

      <div class="row">
        <div class="field">
          <label for="telefone">Telefone</label>
          <input type="text" id="telefone" name="telefone" placeholder="13 123456789">
        </div>

        

      </div>

      <div class="row">
        <div class="field">
          <label for="observacao_pedido">Observações do pedido</label>
          <input type="text" id="observacao_pedido" name="observacao_pedido" placeholder="...">
        </div>
      </div>

      <div class="actions">
        <button type="button" class="btn btn-secondary" onclick="history.back()">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="continuarPagamento">Continuar para pagamento</button>
      </div>
      </form>

      <?php
      if (isset($_SESSION['erro'])) {
        echo $_SESSION['erro'];
        unset($_SESSION['erro']);
      }
      ?>

  </div>

</body>

</html>