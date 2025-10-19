<?php
session_start();
//Banco de dados
require_once('../script/dbd.php');
//Sacola
require_once("../function/exibirSacola.php");


//Pegar forma de pagamento na url
$formaPagamento = $_GET['tipo'];
//Formas aceitas
$array_pagamento = ['pix', 'PIX', 'dinheiro', 'maquininha', 'Cartao', 'Dinheiro'];

//verificar se URL é válida
if(!in_array($formaPagamento,$array_pagamento)){
  header("Location: pagamento.php");
}


//Se não ter telefone registrado, não passou pela pagina de endereço/telefone logo não pode estar no resumo do pedido
if(!$_SESSION['telefone']){
  header("Location: ../index.php");
}


//Banco de dados
$Dbh = new Dbh();
$pdo = $Dbh->getPDO();


//Se existir usuário logado, recebe seu respectivo id
if(isset($_SESSION['user_id'])){
  $id_usuario = $_SESSION['user_id'];
  $query_usuario = $pdo->prepare("SELECT nome FROM usuario WHERE id = :id");
  $query_usuario->bindParam(":id", $id_usuario);
  $query_usuario->execute();
  $nomeUsuario = $query_usuario->fetch(PDO::FETCH_ASSOC);
}else{
 $id_usuario = null;
}

$sessao_id = session_id();

//Classe sacola instanciada
$sacola = new Sacola($pdo, $id_usuario, $sessao_id);
$verificarSacola = $sacola->verificarSacola();

//Se existir sacola
if(isset($verificarSacola)){
  $sacola_id = $verificarSacola['id'];
}

$buscarItensSacola = $sacola->buscarItensSacola($sacola_id);

//DADOS DO USUÁRIO NÃO LOGADO PARA COMANDA

$nomeEntrega = $nomeUsuario['nome'] ?? $_SESSION['nomeEntrega'];
$endereco = $_SESSION['endereco'] ?? null;
$bairro = $_SESSION['bairro'] ?? null;
//Concatena os valores do endereço do usuário não logado, se não existir pega endereço do usuario logado
$enderecoCompleto = $endereco.", ".$bairro ?? $_SESSION['enderecoRegistrado'];
$telefone = $_SESSION['telefone'];
$observacao_pedido = htmlspecialchars($_SESSION['observacao_pedido']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumo do pedido</title>
    <link rel="stylesheet" href="../css/resumoPedido.css">
</head>
<body>

  <nav class="nav-voltar">
    <a href="pagamento.php"><img class="voltar" src="../img/left-arrow.png" alt="Voltar"></a>
  </nav>

    <div class="resumo">
      <h2>Resumo do Pedido</h2>
          <!-- Endereço de entrega -->

      <table>
        <thead>
    
          <tr>
            <th>Produto</th>
            <th>Quantidade</th>
            <th>Preço</th>
          </tr>
         
        </thead>
        <tbody>
          <tr>
          <?php
          $valorTotal = 0;
          foreach($buscarItensSacola as $sacolaProdutos){
            $valorTotal += $sacolaProdutos['preco'] * $sacolaProdutos['quantidade'];
          ?>
            <td><?=$sacolaProdutos['nome']?></td>
            <td><?=$sacolaProdutos['quantidade']?></td>
            <td>R$ <?=$sacolaProdutos['preco']?></td>
          </tr>
           <?php
          }
          ?>
        </tbody>
        <tfoot>
          

        <tr>
          <td colspan="2">Forma de pagamento</td>
          <td><?= $formaPagamento ?></td>
        </tr>
        <tr>
          <td colspan="2"><strong>Total</strong></td>
          <td><strong>R$ <?= number_format($valorTotal, 2, ',', '.') ?></strong></td>
        </tr>

        

        </tfoot>
      </table>

<div class="infoCliente">
  <div>Entregar para: <?=$nomeEntrega?></div>
  <div>Endereço: <?=$endereco?>, <?=$bairro?></div>
  <div>Telefone: <?=$telefone?></div>
  <div>Observação do pedido: <?=$observacao_pedido?></div>
</div>
          <form action="../php/pedidoUsuario.php" method="POST">
            <input type="hidden" name="nomeEntrega" id="nomeEntrega" value="<?=$nomeEntrega?>">
            <input type="hidden" name="endereco" id="endereco" value="<?=$endereco?>">
            <input type="hidden" name="bairro" id="bairro" value="<?=$bairro?>">
            <input type="hidden" name="id_sacola" id="id_sacola" value="<?php echo $sacola_id?>">
            <input type="hidden" name="forma_pagamento" id="forma_pagamento" value="<?php echo $formaPagamento?>">
            <input type="hidden" name="telefone" id="telefone" value="<?php echo $telefone?>">
            <input type="hidden" name="observacao_pedido" id="observacao_pedido" value="<?php echo $observacao_pedido?>">
      <button type="submit" class="btn-confirmar">Confirmar Pedido</button>
        </form>
    </div>
  </div>

</body>
</html>