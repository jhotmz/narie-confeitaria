<?php
session_start();
//Diretório imagem
$c = "../";

require_once("../script/dbd.php");
require_once("../function/exibirSacola.php");

//Classe do banco
$Dbh = new Dbh();
$pdo = $Dbh->getPdo();

if (isset($_SESSION['user_id'])) {
    $usuario_id = $_SESSION['user_id'];
} else {
    $usuario_id = null;
}

$sessao_id = session_id();
//Instanciar classe sacola
$sacola = new Sacola($pdo, $usuario_id, $sessao_id);
$verificarSacola = $sacola->verificarSacola();


//Se existir sacola
if(isset($verificarSacola)){
  $sacola_id = $verificarSacola['id'] ?? null;
}

//FetchAll para buscar os itens da sacola
$itensSacola = $sacola->buscarItensSacola($sacola_id);
//Verificar se retornou mais de um pedido
$linhaItens= $sacola->linhasItens($sacola_id);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sacola</title>
    <link rel="stylesheet" href="../css/sacola.css">
    <script src="../js/jquery-3.7.1.min.js"></script>
</head>
<body>
    <nav class="nav-produto">
        <a href="../index.php"><img class="voltar" src="../img/left-arrow.png" alt="Voltar"></a>
    </nav>

  <section class="listaSacola">
    <h1 class="titulo">Sacola</h1>

    <div class="aviso">
        <?php
      if ($linhaItens < 1) {
      echo "Sua sacola está vazia!";
      exit;
      }
      ?>
    </div>

    <?php
    $valorTotal = 0;
    foreach($itensSacola as $exibirItens){

       $valorTotal += $exibirItens['preco'] * $exibirItens['quantidade'];
    ?>
    <div class="produtoSacola">
      <img src="<?=$c.$exibirItens['img_produto']?>" style="width: 60px; height: 60px;">
      <div class="infoProduto">
        <h4><?=$exibirItens['nome']?></h4>
        <p>R$ <?=$exibirItens['preco']?></p>
      </div>
      <div class="acoes" data-id_produto="<?=$exibirItens['item_id']?>">
        <button class="diminuirQuantidade">-</button>
        <span><?=$exibirItens['quantidade']?></span>
        <button class="aumentarQuantidade">+</button>
        <img src="../img/trash-can.png" style="width: 18px; height: 18px;" class="excluirProduto">
      </div>
    </div>
      <?php
      }
      ?>

  </section>

<div class="resumoValores">
  <h1>Resumo dos valores</h1>
  <div class="linhaResumo">
    <span>Total</span>
    <span>R$ <?= $valorTotal ?></span>
  </div>
</div>

  <footer class="botaoPagamento">
    <a href="endereco_pedido.php"><button class>Continuar</button></a>
  </footer>
      
  <script>
$(document).ready(function () {
    // Botão diminuir
    $('.diminuirQuantidade').click(function () {
        let id_produto = $(this).closest(".acoes").data("id_produto");
        atualizarQuantidade(id_produto, "diminuir");
    });

    // Botão aumentar
    $('.aumentarQuantidade').click(function () {
        let id_produto = $(this).closest(".acoes").data("id_produto");
        atualizarQuantidade(id_produto, "aumentar");
    });

    //Botão excluir
        $('.excluirProduto').click(function () {
        let id_produto = $(this).closest(".acoes").data("id_produto");
        atualizarQuantidade(id_produto, "excluir");
    });

    // Função genérica para chamada AJAX
    function atualizarQuantidade(id_produto, acao) {
        $.ajax({
            url: "../php/acaoSacola.php",
            type: "POST",
            data: {
                id_produto: id_produto,
                acao: acao
            },
            dataType: "json"
        })
        .done(function (resposta) {
            if (resposta.status === "sucesso") {
                alert(resposta.mensagem);
                location.reload(); // recarrega para atualizar quantidade/total
            } else {
                alert(resposta.mensagem);
            }
        })
        .fail(function (jqXHR, textStatus) {
            $('#resposta').html(`<p style="color: red;">Erro: ${textStatus}</p>`);
        });
    }
});
  </script>
</body>
</html>