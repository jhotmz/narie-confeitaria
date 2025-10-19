<?php
session_start();

require_once ("script/dbd.php");
require_once("function/categoriaClass.php");

$Dbh = new Dbh();
$pdo = $Dbh->getPdo();

$c = 'uploads/';

//Consulta nos produtos 
$exibir = $pdo->prepare("SELECT id, img_produto, nome, preco, categoria FROM produtos");
$exibir->execute();
$exibirProduto = $exibir->fetchAll(PDO::FETCH_ASSOC);

//Intanciar tabela categoria
$categoriaProduto = new Categoria($pdo);

//Array gambiarra para pegar ID de cada categoria e exibir separadamente por sessãa
$categorias = [
"brownie" => "1",
"brigadeiro" => "2",
"bolo" => "3",
"palha_italiana" => "4"
];

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nariê</title>
    <link rel="stylesheet" href="css/index.css">
    <script src="js/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-pap4C7tIXcUeZ3j7bG0hDxhxjvE4Fj4s6iYZ+5Kz8Gh8j0c0VJq5Qx4p8P+rJ1e7Lwz8o1fB3S1NqzK9R+6e5w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body>
<!-- NAVBAR -->
<nav class="navigation-bar">
  <h1 class="titulo-nav">Narie</h1>



  <div class="info-nav">
        <a href="pag/gerenciar_adm.php" >
      <p id="btn-gerenciar">Gerenciar</p>
    </a>
    <a href="pag/sacola.php" class="icon-button">
      <img src="img/sacola.png" alt="Sacola" class="icon-img">
    </a>



    <!-- botão com dois ícones (um some, outro aparece) -->
    <button id="button-menu" class="icon-button" aria-label="Abrir menu">
      <img src="img/menu.png" alt="Abrir menu" class="icon-img icon-open">
    </button>
    
  </div>
</nav>

<!-- Overlay para clique esconder menu -->
<div class="overlay"></div>

<!-- Menu lateral -->
<div class="menu-wide">
  <!-- botão de fechar com imagem -->
  <button id="close-menu" class="close-button" aria-label="Fechar menu">
    <img src="img/close.png" alt="Fechar menu" class="close-img">
  </button>
      <a href="pag/pedidos.php">Pedidos</a>

  <?php
  if(!isset($_SESSION['user_id'])){
  ?>
  <a href="pag/entrar.php">Entrar</a>
  <?php
  }else{
  ?> 
 
  <a href="pag/enderecoUsuario.php">Endereços</a>

  <?php
  }
  ?>
  <a href="php/logout.php">Sair</a>
  
</div>

<header class="header">
  <div class="header-content">
    <p>
      <strong>Nariê</strong>  
      O melhor da confetaria entregue direto na sua casa.
    </p>
    <a href="#produtos" class="btn">Pedir Agora</a>
  </div>
</header>

    <div class="lista-produtos">
      <p><a href="#brownies">Brownies</a></p>
        <p><a href="#brigadeiros">Brigadeiros</a></p>
        <p><a href="#palha_italiana">Palha Italiana</a></p>
        <p><a href="#bolo_pote">Bolos no pote</a></p>
    </div>

<p id="resposta"></p>
  <!-- Lista de produtos -->
  <main class="produtos">
    <h2>Mais pedidos</h2>
    <div class="grid">

    <?php
    foreach($exibirProduto as $produto){
    ?>
      <!-- Produto 1 -->
      <div class="produto" data-id="<?=$produto['id']?>">
        <img src="<?php echo $c.$produto['img_produto']?>">
        <h3><?php echo $produto['nome']?></h3>
        <p>R$ <?php echo $produto['preco']?></p>
        <button class="btnAdicionar">Adicionar</button>
      </div>
   
<?php
    }
?>
 </div>
<!-- Brownie -->
    <h2 id="brownies">Brownies</h2>
    <div class="grid">
      
        <?php
        $exibirCategoria = $categoriaProduto->listarProdutoCategoria($categorias['brownie']);
          foreach($exibirCategoria as $produto){
        ?>
          <div class="produto" data-id="<?=$produto['id']?>">
        <img src="<?=$produto['img_produto']?>" alt="Caixa Especial">
        <h3><?=$produto['nome']?></h3>
        <p>R$ <?=$produto['preco']?></p>
        <button class="btnAdicionar">Adicionar</button>

        </div>
        <?php
        }
        ?>
    </div>

          <h2 id="brigadeiros">Brigadeiros</h2>

          <div class="grid">
          <?php
        $exibirCategoria = $categoriaProduto->listarProdutoCategoria($categorias['brigadeiro']);
          foreach($exibirCategoria as $produto){
        ?>
          <div class="produto" data-id="<?=$produto['id']?>">
            <img src="<?=$produto['img_produto']?>">
        <h3><?=$produto['nome']?></h3>
        <p>R$<?=$produto['preco']?></p>
       <button class="btnAdicionar">Adicionar</button>
          </div>
        <?php
          }
        ?>
        </div>
<h2 id="palha_italiana">Palha italiana</h2>
          <div class="grid">
          <?php
        $exibirCategoria = $categoriaProduto->listarProdutoCategoria($categorias['palha_italiana']);
          foreach($exibirCategoria as $produto){
        ?>
          <div class="produto" data-id="<?=$produto['id']?>">
            <img src="<?=$produto['img_produto']?>">
        <h3><?=$produto['nome']?></h3>
        <p>R$<?=$produto['preco']?></p>
        <button class="btnAdicionar">Adicionar</button>
          </div>
        <?php
          }
        ?>
        </div>
          <h2 id="bolo_pote">Bolos no pote</h2>
                  <div class="grid">
          <?php
        $exibirCategoria = $categoriaProduto->listarProdutoCategoria($categorias['brigadeiro']);
          foreach($exibirCategoria as $produto){
        ?>
          <div class="produto" data-id="<?=$produto['id']?>">
            <img src="<?=$produto['img_produto']?>">
        <h3><?=$produto['nome']?></h3>
        <p>R$<?=$produto['preco']?></p>
        <button class="btnAdicionar">Adicionar</button>
          </div>
        <?php
          }
        ?>
        </div>
  </main>

    <!-- Rodapé mobile -->
    <footer class="mobile-footer">
        <p>&copy; 2025 Nariê Confeitaria</p>
        <nav>
            <a href="#">Home</a>
            <a href="#">Produtos</a>
            <a href="#">Contato</a>
        </nav>
    </footer>

<div id="barraSacola">
  <span id="totalItens">0 itens | R$ 0</span>
  <a href="pag/sacola.php">Ir à sacola</a>
</div>

<script src="js/index.js"></script>

</body>
</html>
