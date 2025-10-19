<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: ../index.php");
}
$c = '../';
$username = $_SESSION['username'];
require_once("../script/dbd.php");
$Dbh = new Dbh();
$pdo = $Dbh->getPdo();

//categoria produtos
$categoria = $pdo->prepare("SELECT id, nomeCategoria FROM categoriaproduto");
$categoria->execute();
$exibirCategoria = $categoria->fetchAll(PDO::FETCH_ASSOC);

//produtos 
require_once("../function/produtoClass.php");

//Caso exista filtro e tenha valor, filtra bonitinho
if(isset($_GET['filtrarProdutos']) && $_GET['filtrarProdutos'] >= 1){
  $produto = new Produto($pdo, $_GET['filtrarProdutos']);
  $exibirProduto = $produto->produtosFiltro();
}else{
  $produto = new Produto($pdo, null);
$exibirProduto = $produto->listarTodos();  
}

$row = $produto->contar();


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/gerenciar.css">
    <link rel="stylesheet" href="../css/alert.css">
    <title>Gerenciar</title>
    <script src="../js/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"></head>
<body>
  <header>
    <h2 class="title">Gerenciar Produtos - Narie</h2>
  </header>  

    <nav class="nav-produto">
        <a href="gerenciar_adm.php"><img class="voltar" src="../img/left-arrow.png" alt="Voltar"></a>
    </nav>

  <div class="container">
    <h1>Adicionar Produto</h1>
    
      <div>
        <label for="imagemProduto">Imagem do Produto:</label>
        <input type="file" id="imagemProduto" name="imagemProduto" accept="image/*">
      </div>

      <div>
        <label for="nomeProduto">Nome do Produto:</label>
        <input type="text" id="nomeProduto" name="nomeProduto" placeholder="Ex: Brownie de Chocolate" required>
      </div>

      <div>
        <label for="descricaoProduto">Descrição:</label>
        <textarea id="descricaoProduto" name="descricaoProduto" rows="3" placeholder="Breve descrição do produto..."></textarea>
      </div>

      <div>
        <label for="valorProduto">Valor (R$):</label>
        <input type="number" step="0.01" id="valorProduto" name="valorProduto" placeholder="Ex: 9.90" required>
      </div>

      <div>
        <label for="categoriaProduto">Categoria:</label>
        <select id="categoriaProduto" name="categoriaProduto">
          <option value="">Selecione</option>
          <?php
          foreach($exibirCategoria as $exibir){
          ?>
        <option value="<?=$exibir['id']?>"><?=$exibir['nomeCategoria']?></option>
          <?php
          }
          ?>
        </select>
      </div>


      <button type="submit" id="cadastrarProduto">Salvar Produto</button>
        <p id="resposta"></p>

<div class="container my-3">
  <h4 class="mb-3">Gerenciar Produtos</h4>
  <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="GET">
  <select name="filtrarProdutos" id="filtrarProdutos">
    <option value="">Todos</option>
          <?php
          foreach($exibirCategoria as $exibir){
          ?>
        <option value="<?=$exibir['id']?>"><?=$exibir['nomeCategoria']?></option>
          <?php
          }
          ?>

  </select>
  <div>
 <?php
   if(isset($_GET['filtrarProdutos']) && $_GET['filtrarProdutos'] >= 1){
    echo "Selecionado: ".$_GET['filtrarProdutos'];
   };
   ?>

  </div>

  <button type="submit" class=" btn mb-4">Filtrar</button>
  </form>
<div class="row g-3">
  <?php foreach ($exibirProduto as $listarProduto): ?>
    <div class="col-12 col-md-6 col-lg-4">
      <div class="card h-100 shadow-sm">
        <img src="<?= $c.$listarProduto['img_produto'] ?>" class="card-img-top img-fluid" alt="Produto">

        <div class="card-body p-3">
          <h6 class="card-title mb-1"><?= $listarProduto['nome'] ?></h6>
          <p class="mb-1 small text-muted"><?= $listarProduto['categoria'] ?></p>
          <p class="mb-2 fw-bold">R$ <?= $listarProduto['preco'] ?></p>

          <div class="d-flex flex-wrap gap-2 mt-2">
            <button type="button"
                    class="btn btn-sm btn-primary"
                    data-toggle="modal"
                    data-target="#editarModal"
                    data-id="<?= $listarProduto['id'] ?>"
                    data-produto_nome="<?= $listarProduto['nome'] ?>"
                    data-preco="<?= $listarProduto['preco'] ?>"
                    data-categoria="<?= $listarProduto['categoria'] ?>">
              Editar
            </button>

            <button type="button"
                    class="btn btn-sm btn-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#excluirModal"
                    data-id="<?= $listarProduto['id'] ?>"
                    data-nome_produto="<?= $listarProduto['nome'] ?>">
              Excluir
            </button>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

  </div>
</div>
</div>


<!-- Modal Editar -->
<?php
include_once ("../modal/editar.php");
?>

<!-- Modal Excluir -->
<?php
include_once ("../modal/excluir.php");
?>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<script src="../js/gerenciar.js"></script>
</body>
</html>