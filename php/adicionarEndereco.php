<?php
session_start();

// Função para limpar e validar os dados
function validar_input($data) {
    // 1. Remove espaços em branco no início e no fim
    $data = trim($data);
    
    // 2. Converte caracteres especiais em entidades HTML
    // É mais seguro fazer isso APÓS a validação, antes de usar em consultas
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Inicializa a mensagem de erro
$_SESSION['msg'] = '';

// Variaveis vindas do form, já aplicando o trim para limpar
// Definindo um valor padrão como string vazia para evitar erros caso o campo não exista no POST
$endereco = isset($_POST['endereco']) ? trim($_POST['endereco']) : '';
$bairro = isset($_POST['bairro']) ? trim($_POST['bairro']) : '';
$referencia = isset($_POST['referencia']) ? trim($_POST['referencia']) : '';
$nomeEndereco = isset($_POST['nomeEndereco']) ? trim($_POST['nomeEndereco']) : '';

// --- VALIDAÇÃO COM EMPTY E TRIM ---
$erros = [];

if (empty($endereco)) {
    $erros[] = "O campo Endereço é obrigatório.";
}

if (empty($bairro)) {
    $erros[] = "O campo Bairro é obrigatório.";
}

if (empty($nomeEndereco)) {
    $erros[] = "O campo Nome Endereço é obrigatório.";
}

// O campo 'referencia' geralmente não é obrigatório, mas se for, adicione a validação aqui.

if (!empty($erros)) {
    // Se houver erros, monta a mensagem e interrompe a execução
    $_SESSION['msg'] = "<p class='alert alert-error'>" . implode('<br>', $erros) . "</p>";
    header("Location:../pag/enderecoUsuario.php"); // Redireciona de volta
    exit;
}

//Banco de dados
require_once("../script/dbd.php");
$Dbh = new Dbh();
$pdo = $Dbh->getPdo();

//Variaveis vindas do form
$enderecoUser = htmlspecialchars($endereco);
$bairroUser = htmlspecialchars($bairro);
$referenciaUser = htmlspecialchars($referencia);
$nome_enderecoUser = htmlspecialchars($nomeEndereco);
$id_usuario = $_SESSION['user_id'];

//adicionar valores no banco de dados
$query_adicionar = $pdo->prepare("INSERT INTO endereco_usuario (id_usuario, endereco, bairro, referencia, nome_endereco) VALUES (:id_usuario, :endereco, :bairro, :referencia, :nome_endereco)");
$query_adicionar->bindParam(":id_usuario",$id_usuario);
$query_adicionar->bindParam(":bairro",$bairroUser);
$query_adicionar->bindParam(":endereco",$enderecoUser);
$query_adicionar->bindParam(":referencia",$referenciaUser);
$query_adicionar->bindParam(":nome_endereco",$nome_enderecoUser);

if($query_adicionar->execute() ){
    $_SESSION['msg'] = "<p class='alert alert-success'>Sucesso ao cadastrar endereço!</p>";
    header("Location:../pag/enderecoUsuario.php");
}




