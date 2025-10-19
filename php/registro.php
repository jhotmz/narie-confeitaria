<?php

if ($_SERVER['REQUEST_METHOD'] === "POST") {
header('Content-Type: application/json');
    // Varíaveis do formulário
    $nome = htmlspecialchars($_POST['nomeUsuario']);
    $sobrenome = htmlspecialchars($_POST['sobrenomeUsuario']);
    $cpf = $_POST['cpfUsuario'];
    $email = htmlspecialchars($_POST['emailUsuario']);
    $senha = $_POST['senhaUsuario'];
    $confirmarSenha = $_POST['confirmarSenha'];

    // Verificar se todos os campos foram preenchidos
    if (empty($nome) || empty($sobrenome) || empty($cpf) || empty($email) || empty($senha) || empty($confirmarSenha)) {
        echo json_encode([
            "status" => "erro",
            "mensagem" => "Preencha todos os campos!",
        ]);
        exit;
    }
    
    // Garantir que o usuário use um formato de email válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
            "status" => "erro",
            "mensagem" => "Formato de email inválido!",
        ]);
        exit;
    }

    //Verificar CPF
    include_once("../function/cpf.php");
    // Aqui você verifica o CPF
    if (!validarCPF($cpf)) {
        echo json_encode([
            "status" => "erro",
            "mensagem" => "CPF inválido!"
        ]);
        exit;
    }

    require_once('../script/dbd.php');
    // variável PDO
    $Dbh = new Dbh();
    $pdo = $Dbh->getPdo();

    // Checar se já existe um CPF ou email cadastrados no banco de dados
    $consulta = $pdo->prepare("SELECT email, cpf FROM usuario WHERE email = :email OR cpf = :cpf");
    $consulta->bindParam(":email", $email);
    $consulta->bindParam(":cpf", $cpf);
        if($consulta->execute()){
              $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
            $erros = [];

    foreach ($resultado as $row) {
        if ($row['email'] === $email) {
            $erros = "E-mail já cadastrado!";
        }
        if ($row['cpf'] === $cpf) {
            $erros = "CPF já cadastrado!";
        }
    }

    if (!empty($erros)) {
        echo json_encode([
            "status" => "erro",
            "mensagem" => $erros
        ]);
        exit;
    }

    // Se não encontrou, pode continuar com o cadastro
    $emailCadastrado = $email;
    $cpfCadastrado = $cpf;
    }    
    
    // Validação de confirmação de senha
    if ($senha != $confirmarSenha) {
           echo json_encode([
            "status" => "erro",
            "mensagem" => "As senhas não coincidem!",
        ]);
        exit;
    } else {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    }

    // pegar role (nivel do usuario)
    $role_usuario = "user";

    // Cadastrar novo usuário
    $adicionar = $pdo->prepare("INSERT INTO usuario (nome, sobrenome, cpf, email, senha) VALUES (:nome, :sobrenome, :cpf, :email, :senha)");
    $adicionar->bindParam(":nome", $nome);
    $adicionar->bindParam(":sobrenome", $sobrenome);
    $adicionar->bindParam(":cpf", $cpfCadastrado);
    $adicionar->bindParam(":email", $emailCadastrado);
    $adicionar->bindParam(":senha", $senhaHash);
    if ($adicionar->execute()) {
    
    session_start();

    $user_id = $pdo->lastInsertId();
    
    // Armazenar as informações do usuário na sessão
    $_SESSION["username"] = $nome;
    $_SESSION['user_id'] = $user_id;
    $_SESSION['role_user'] = $role_usuario;
    

        echo json_encode([
            "status" => "sucesso",
            "mensagem" => "Registrado com sucesso!",
            "redirect" => "../index.php",
        ]);
        exit;
    } else {
         echo json_encode([
            "status" => "erro",
            "mensagem" => "Erro!",
        ]);
        exit;
    }
} else {
    header("Location: ../index.html");
}
