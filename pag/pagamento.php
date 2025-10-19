<?php
session_start();

if(!isset($_SESSION['user_id'])){
  if(!isset($_SESSION['nomeEntrega'])){
    Header("Location: ../index.php");
  }
}else{
  if(!isset($_SESSION['endereco']) || !isset($_SESSION['telefone'])){
    Header("Location: ../index.php");
  }

}
  
?>

<!DOCTYPE html>
<html lang="pt_br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento</title>
    <link rel="stylesheet" href="../css/pagamento.css">
</head>
<body>      
  <nav class="nav-voltar">
    <a href="endereco_pedido.php"><img class="voltar" src="../img/left-arrow.png" alt="Voltar"></a>
  </nav>
  <div class="container">

    <h1>Escolha sua forma de pagamento</h1>
    <div class="payment-options">
      <div class="option" data-link="resumoPedido.php?tipo=PIX">
        <h3>PIX</h3>
        <p>Pague rapidamente pelo  app do seu banco.</p>
      </div>
      <div class="option" data-link="resumoPedido.php?tipo=Cartao">
        <h3>Pagar na entrega</h3>
        <p>Maquininha disponível no ato da entrega.</p>
      </div>
      <div class="option" data-link="resumoPedido.php?tipo=Dinheiro">
        <h3>Dinheiro</h3>
        <p>Pague em espécie na entrega.</p>
      </div>
    </div>
    <button class="btn-continuar" onclick="continuar()">Continuar</button>
  </div>
    <script>

    let selectedOption = null;

    // pega todas as opções
    const options = document.querySelectorAll('.option');
    options.forEach(option => {
      option.addEventListener('click', () => {
        // remove seleção anterior
        options.forEach(o => o.classList.remove('selected'));
        // adiciona classe na clicada
        option.classList.add('selected');
        // guarda o link
        selectedOption = option.getAttribute('data-link');
      });
    });

    function continuar() {
      if (selectedOption) {
        window.location.href = selectedOption;
      } else {
        alert("Por favor, escolha uma forma de pagamento primeiro.");
      }
    }
  
  </script>
</body>
</html>