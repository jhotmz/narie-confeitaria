<?php
session_start();

// Bloqueia acesso de usuários comuns
if($_SESSION['role_user'] == 'user'){
    header("Location: ../index.php");
    exit;
}

require_once("../script/dbd.php");
require_once("../function/usuarioClass.php");

//banco de dados
$Dbh = new Dbh();
$pdo = $Dbh->getPdo();

$usuario = new Usuario($pdo);
$exibir_usuario = $usuario->exibirUsuario();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gerenciar Usuários</title>
<link rel="stylesheet" href="../css/gerenciar_usuarios.css">
<link rel="stylesheet" href="../css/alert.css">
</head>
<body>

    <nav class="nav-produto">
        <a href="../index.php"><img class="voltar" src="../img/left-arrow.png" alt="Voltar"></a>
    </nav>
<div class="container">
  <h1>Gerenciar Usuários</h1>
  
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Email</th>
        <th>Role</th>
        <th>Ações</th>
      </tr>
    </thead>
<tbody>
  <?php foreach($exibir_usuario  as $user){ ?>
  <tr>
    <td data-label="ID"><?= $user['id'] ?></td>
    <td data-label="Nome"><?= htmlspecialchars($user['nome']) ?></td>
    <td data-label="Email"><?= htmlspecialchars($user['email']) ?></td>
    <td data-label="Role">
      <form method="POST" action="../php/alterar_role.php">
        <input type="hidden" name="id" value="<?= $user['id'] ?>">
        <select name="role" onchange="this.form.submit()">
          <option value="user" <?= $user['role']=='user'?'selected':'' ?>>User</option>
          <option value="moderator" <?= $user['role']=='moderator'?'selected':'' ?>>Moderator</option>
          <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
        </select>
      </form>
    </td>
    <td data-label="Ações" class="actions">
      <form method="POST" action="excluir_usuario.php" onsubmit="return confirm('Deseja realmente excluir este usuário?');">
        <input type="hidden" name="id" value="<?= $user['id'] ?>">
        <button type="submit">Excluir</button>
      </form>
    </td>
  </tr>
  <?php } ?>
</tbody>
  </table>
</div>
<?php
if(isset($_SESSION['msg'])){
  echo $_SESSION['msg'];
  unset($_SESSION['msg']);
  }
?>

</body>
</html>
