<?php
session_start();

// Caso não esteja logado..
if(!isset($_SESSION['user_id'])){
    Header("location: ../index.php");
    exit;
}

//conexão com banco de dados
require_once("../script/dbd.php");
$Dbh = new Dbh();
$pdo = $Dbh->getPdo();

// id do usuario
$id_usuario = $_SESSION['user_id'];

//consulta nos endereços salvos do usuario
$query_endereco = $pdo->prepare("SELECT * FROM endereco_usuario WHERE id_usuario = :id_usuario");
$query_endereco->bindParam(":id_usuario", $id_usuario);
$query_endereco->execute();
$exibirEnd = $query_endereco->fetchAll(PDO::FETCH_ASSOC);
$linhas = $query_endereco->rowCount();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meus Endereços - Narie</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/enderecoUsuario.css">
  <link rel="stylesheet" href="../css/alert.css">
   <script src="../js/jquery-3.7.1.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
 
  <div class="main-content">
      <a href="../index.php" class="btn-voltar">
      <img src="../img/left-arrow.png" alt="Voltar">
      Voltar
    </a>
    <h2><i class="fa-solid fa-location-dot"></i> Meus Endereços</h2>

    <div class="card-form">
      <h3><i class="fa-solid fa-plus"></i> Adicionar novo endereço</h3>

      <form action="../php/adicionarEndereco.php" method="POST">
       <div class="form-group">
          <label for="nomeEndereco">Nome do endereço</label>
          <input type="text" name="nomeEndereco" id="nomeEndereco" placeholder="Ex.: Casa principal, trabalho">
        </div>

        <div class="form-group">
          <label for="endereco">Endereço</label>
          <input type="text" name="endereco" id="endereco" placeholder="Rua, número, complemento...">
        </div>
        <div class="form-group">
          <label for="bairro">Bairro</label>
          <input type="text"  name="bairro" id="bairro" placeholder="Digite o bairro">
        </div>

        <div class="form-group">
          <label for="referencia">Referência</label>
          <input type="text" id="referencia"  name="referencia" placeholder="Ex: Próximo ao supermercado X, portão azul.">
        </div>

        <button type="submit" class="btn" name="salvarEndereco"><i class="fa-solid fa-save"></i> Salvar Endereço</button>
      </form>
      <div class="resposta">
        <?php
        if(isset($_SESSION['msg'])){
          echo $_SESSION['msg'];
          unset($_SESSION['msg']);
        }
        ?>
      </div>
    </div>

    <div class="enderecos">
      <?php
      if($linhas < 1){
        echo "Você ainda não salvou nenhum endereço!";
        exit;
      }
      foreach($exibirEnd as $exibir){
      ?>
      <div class="endereco-card">
        <h4><?php echo $exibir['nome_endereco']?></h4>
        <p><?php echo $exibir['endereco']?></p>
        <p><?php echo $exibir['bairro']?></p>
        <div class="acoes">
<button title="Editar" type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalEditar" 
    data-id="<?= $exibir['id'] ?>" 
    data-id-usuario="<?= $id_usuario ?>" 
    data-nome-endereco="<?= $exibir['nome_endereco'] ?>" 
    data-endereco="<?= $exibir['endereco'] ?>"
    data-bairro="<?= $exibir['bairro'] ?>" 
    data-referencia="<?= $exibir['referencia'] ?? "" ?>">
    <i class="fa-solid fa-pen-to-square"></i>
</button>
          <button title="Excluir" data-id="<?=$exibir['id']?>" class="excluirEndereco"><i class="fa-solid fa-trash"></i></button>
        </div>
      </div>
        <?php
      }
        ?>


    </div>
  </div>

<!-- Modal -->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Editar endereço</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="../php/editarEnderecoUsuario.php" method="POST">
        <input type="hidden" name="id_endereco" id="id_endereco">

        <div class="form-group">
          <label for="nome_endereco">Nome do Endereço (Ex: Casa, Trabalho)</label>
          <input type="text" class="form-control" name="nome_endereco" id="nome_endereco" >
        </div>

        <div class="form-group">
          <label for="endereco">Endereço (Rua, Avenida, etc.)</label>
          <input type="text" class="form-control" name="endereco" id="endereco" required>
        </div>

        <div class="form-group">
          <label for="bairro">Bairro</label>
          <input type="text" class="form-control" name="bairro" id="bairro" required>
        </div>

        <div class="form-group">
          <label for="referencia">Ponto de Referência (Opcional)</label>
          <textarea class="form-control" name="referencia" id="referencia" rows="2" placeholder="Ex: Próximo ao supermercado X, portão azul."></textarea>
        </div>  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary">Salvar alterações</button>
      </div>
    </form>
    </div>
  </div>
</div>


  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<script>

$(document).ready(function () {
    // Ouvinte de evento correto: o ID do modal é #modalEditar
    $('#modalEditar').on('show.bs.modal', function(event) {
        
        var button = $(event.relatedTarget);
        
        // 1. Acesso aos dados (usando camelCase para data attributes)
        var id = button.data('id');
        var nome_endereco = button.data('nomeEndereco'); // Ajuste: camelCase
        var id_usuario = button.data('idUsuario');       // Ajuste: camelCase
        var endereco = button.data('endereco');
        var bairro = button.data('bairro');
        var referencia = button.data('referencia');

        var modal = $(this);

        // 2. Aplicar valor aos inputs
        modal.find('#id_endereco').val(id);
        modal.find('#id_usuario').val(id_usuario);
        modal.find('#nome_endereco').val(nome_endereco);
        modal.find('#endereco').val(endereco);
        modal.find('#bairro').val(bairro);
        
        // Trata o caso em que a referência pode ser a string padrão "referência não informada"
        if (referencia === "referência não informada") {
            modal.find('#referencia').val(''); // Define como vazio para o usuário digitar
        } else {
            modal.find('#referencia').val(referencia);
        }
    });

    // EXCLUIR ENDEREÇO
    $('.excluirEndereco').on('click', function(event) {
        let id = $(this).data('id');
                $.ajax({
            url: "../php/excluirEndereco.php",
            type: "POST",
            data: {
              id_endereco: id
            },
            dataType: "json"
        })
        .done(function (resposta) {
            if (resposta.status === "sucesso") {
               alert(resposta.mensagem);
              location.reload(); // recarrega para atualizar quantidade/total
            }else{
                alert(resposta.mensagem);
            }
        })
        .fail(function (jqXHR, textStatus) {
            $('#resposta').html(`<p style="color: red;">Erro: ${textStatus}</p>`);
        });
    });
    
});
</script>

</body>
</html>
