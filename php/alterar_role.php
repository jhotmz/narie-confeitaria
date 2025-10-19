<?php
session_start();
require_once '../script/dbd.php'; // ajuste para sua conexão com o banco
$Dbh = new Dbh();
$pdo = $Dbh->getPdo();

// Só admin pode acessar
if(!isset($_SESSION['role_user']) || $_SESSION['role_user'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Verifica se o ID e o role foram enviados
if(!isset($_POST['id'], $_POST['role'])) {
    $_SESSION['msg'] = "Dados inválidos.";
    header("Location: gerenciar_usuarios.php");
    exit;
}

// Sanitização dos dados
$id = htmlspecialchars($_POST['id']);
$role = htmlspecialchars($_POST['role']);

// Valida role permitida
$roles_permitidos = ['user','moderator','admin'];
if(!$id || !in_array($role, $roles_permitidos)) {
    $_SESSION['msg'] = "<div class='alert alert-error'>Dados inválidos ou role não permitida.</div>";
    header("Location: ../pag/gerenciar_usuarios.php");
    exit;
}

// Evita alterar a própria role para user
if($id == $_SESSION['user_id'] && $role != 'admin') {
    $_SESSION['msg'] = "<div class='alert alert-error'>Você não pode se remover como admin.</div>";
    header("Location: ../pag/gerenciar_usuarios.php");
    exit;
}

// Atualiza no banco
$stmt = $pdo->prepare("UPDATE usuario SET role = :role_user WHERE id = :id_usuario");
$stmt->bindParam(":role_user", $role);
$stmt->bindParam(":id_usuario", $id);

if($stmt->execute()) {
    $_SESSION['msg'] = "<div class='alert alert-success'>Nível alterado com sucesso.</div>";
} else {
    $_SESSION['msg'] = "<div class='alert alert-error'>Erro ao atualizar role.</div>";
}

header("Location: ../pag/gerenciar_usuarios.php");
exit;
