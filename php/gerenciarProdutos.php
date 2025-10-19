<?php  
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === "POST") {
  
    //variáveis vindas do formulário
    $nomeProduto = htmlspecialchars($_POST['nomeProduto']);
    $descricaoProduto = htmlspecialchars($_POST['descricaoProduto']);
    $valorProduto = htmlspecialchars($_POST['valorProduto']);
    $categoriaProduto = htmlspecialchars($_POST['categoriaProduto']);

    //verificar se todos os campos foram preenchidos
    if(empty($nomeProduto) || empty($descricaoProduto) || empty($valorProduto) || empty($categoriaProduto)){
        echo json_encode([
            "status" => "erro",
            "mensagem" => "Preencha todos os campos!"
        ]);
        exit;
    }

    // Verificar se foi inserido imagem
    if (!isset($_FILES['imagemUsuario']) || $_FILES['imagemUsuario']['error'] != 0) {
        echo json_encode([
            "status" => "erro",
            "mensagem" => "Envie uma imagem válida!"
        ]);
        exit;
    }

    //Validações do arquivo
    $arquivo = $_FILES['imagemUsuario'];
    $extensoesPermitidas = ['jpg', 'jpeg', 'png'];
    $tamanhoMaximo = 5 * 1024 * 1024; // 2MB

    // Verificar extensão da imagem
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    if (!in_array($extensao, $extensoesPermitidas)) {
        echo json_encode([
            "status" => "erro",
            "mensagem" => "Formato de imagem não permitido! Use jpg, png, gif ou webp."
        ]);
        exit;
    }

    // Verificar tamanho do arquivo
    if ($arquivo['size'] > $tamanhoMaximo) {
        echo json_encode([
            "status" => "erro",
            "mensagem" => "A imagem deve ter no máximo 5MB!"
        ]);
        exit;
    }

    // Salvar caminho da imagem
    if (isset($_FILES['imagemUsuario']) && $_FILES['imagemUsuario']['error'] == 0) {
        $pasta = "../uploads/";
        $caminhoSql = "uploads/";
        $nomeArquivo = uniqid() . "-" . basename($_FILES['imagemUsuario']['name']);
        $caminho = $pasta . $nomeArquivo;
    }

     // Aqui salva o arquivo de fato
    if (!move_uploaded_file($_FILES['imagemUsuario']['tmp_name'], $caminho)) {
        echo json_encode([
            "status" => "erro",
            "mensagem" => "Erro ao salvar a imagem no servidor!"
        ]);
        exit;
    }
    
    $caminhoBanco = $caminhoSql . $nomeArquivo;

    require_once("../script/dbd.php");
    $Dbh = new Dbh();
    $pdo = $Dbh->getPdo();

    $adicionarProduto = $pdo->prepare("INSERT INTO produtos(img_produto, nome, preco, categoria) VALUES (:imagem, :produto, :preco, :categoria)");
    $adicionarProduto->bindParam(":imagem", $caminhoBanco);
    $adicionarProduto->bindParam(":produto", $nomeProduto);
    $adicionarProduto->bindParam(":preco", $valorProduto);
    $adicionarProduto->bindParam(":categoria", $categoriaProduto);


    if ($adicionarProduto->execute()) {
        echo json_encode([
            "status" => "sucesso",
            "mensagem" => "Sucesso ao cadastrar o item!",
            "redirect" => "../pag/gerenciar.php"
        ]);
        exit;
    } else {
        echo json_encode([
            "status" => "erro",
            "mensagem" => "Algo deu errado",
        ]);
        exit;
    }
} else {
    header("location: ../index.php");
}
