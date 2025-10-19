<?php
session_start();
$_SESSION['erro'] = '';

// Se o usuário estiver logado
if (isset($_SESSION['user_id'])) {

    // Validação: endereço registrado e telefone obrigatórios
    if (empty($_POST['telefone']) || empty($_POST['enderecoRegistrado'])) {
        $_SESSION['erro'] = "<div class='alert alert-error'>Os campos são obrigatórios.</div>";
        header("Location: ../pag/endereco_pedido.php");
        exit;
    }

    // Captura e higieniza dados
    $nomeEntrega = htmlspecialchars($_POST['nomeEntrega'] ?? '');
    $endereco = htmlspecialchars($_POST['enderecoRegistrado'] ?? '');
    $bairro = htmlspecialchars($_POST['bairro'] ?? '');
    $telefone = htmlspecialchars($_POST['telefone'] ?? '');
    $observacao_pedido = htmlspecialchars($_POST['observacao_pedido'] ?? '');

    // Salva na sessão
    $_SESSION['nomeEntrega'] = $nomeEntrega;
    $_SESSION['endereco'] = $endereco;
    $_SESSION['bairro'] = $bairro;
    $_SESSION['telefone'] = $telefone;
    $_SESSION['observacao_pedido'] = $observacao_pedido;

    // Redireciona para pagamento
    header("Location: ../pag/pagamento.php");
    exit;

} else {
    // Se o usuário NÃO estiver logado
    if (empty($_POST['nomeEntrega']) || empty($_POST['endereco']) || empty($_POST['bairro']) || empty($_POST['telefone'])) {
        $_SESSION['erro'] = "<div class='alert alert-error'>Os campos são obrigatórios.</div>";
        header("Location: ../pag/endereco_pedido.php");
        exit;
    }

    // Captura e higieniza dados
    $nomeEntrega = htmlspecialchars($_POST['nomeEntrega'] ?? '');
    $endereco = htmlspecialchars($_POST['endereco'] ?? '');
    $bairro = htmlspecialchars($_POST['bairro'] ?? '');
    $telefone = htmlspecialchars($_POST['telefone'] ?? '');
    $observacao_pedido = htmlspecialchars($_POST['observacao_pedido'] ?? '');


    // Salva na sessão
    $_SESSION['nomeEntrega'] = $nomeEntrega;
    $_SESSION['endereco'] = $endereco;
    $_SESSION['bairro'] = $bairro;
    $_SESSION['telefone'] = $telefone;
    $_SESSION['observacao_pedido'] = $observacao_pedido;
    
    // Redireciona
    header("Location: ../pag/pagamento.php");
    exit;
}
