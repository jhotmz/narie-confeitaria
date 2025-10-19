<?php
// Iniciar sessão no início para CSRF e gerenciamento de sessão
session_start();

if($_SERVER['REQUEST_METHOD'] === "POST"){
    header('Content-Type: application/json');

require("../script/dbd.php");
//Instanciar objeto
$Dbh = new Dbh();
$pdo = $Dbh->getPdo();

//Dados recebidos do formulário
$cpf = htmlspecialchars($_POST['cpfUsuario']);
$senha = htmlspecialchars($_POST['senhaUsuario']);

include("../function/cpf.php");

if(!validarCPF($cpf)){
    echo json_encode([
        "status" => "erro",
        "mensagem" => "CPF ou senha incorretos!",
    ]);
    exit;
}

// Verifica se o usuário preencheu o formulário
if(empty($cpf) || empty($senha)){
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Preencha todos os campos!"
    ]);
    exit;
}

//verificar se o cpf digitado confere com o do salvo no banco de dados
$logar = $pdo->prepare("SELECT id, senha, nome, role FROM usuario WHERE cpf = :cpf");
$logar->bindParam(":cpf", $cpf);
$logar->execute();
$usuario = $logar->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
echo json_encode([
    "status" => "erro",
    "mensagem" => "CPF ou senha incorretos!"
]);
exit;
}

// Comparar senha digitada com o hash do banco, e caso correto logar usuário
if (password_verify($senha, $usuario['senha'])) {
    // Senha correta -> regenerar ID da sessão
    session_regenerate_id(true);
    $_SESSION['username'] = $usuario['nome'];
    $_SESSION['user_id'] = $usuario['id'];
    $_SESSION['role_user'] = $usuario['role'];

    // Gerar novo token CSRF para próxima requisição
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    echo json_encode([
        "status" => "sucesso",
        "mensagem" => "Login realizado com sucesso!",
        "redirect" => "../index.php"
    ]);
} else {
    echo json_encode([
        "status" => "erro",
        "mensagem" => "CPF ou senha inválidos!"
    ]);
    exit;
}
}


