<?php
if($_SERVER['REQUEST_METHOD'] == "POST"){
    session_start();
    //Banco de dados
    require_once ("../script/dbd.php");
    $Dbh = new Dbh();
    $pdo = $Dbh->getPdo();
    $_SESSION['msg'] = "";

    //Verifica se os inputs foram preenchidos
    if(empty($_POST['nome_endereco']) || empty($_POST['endereco']) || empty($_POST['bairro'])){
        $_SESSION['msg'] = "<div class='alert alert-error'>Preencha todos os campos!</div>";
        header("location: ../pag/enderecoUsuario.php");
        exit;
    }

    //Variaveis vindas do formulario
    $id_endereco = $_POST['id_endereco'];
    $nome_enderecoAtt = $_POST['nome_endereco'];
    $enderecoAtt = $_POST['endereco'];
    $bairroAtt = $_POST['bairro'];
    $referenciaAtt = $_POST['referencia'];

    //Atualizar informações do endereço
    $query_update = $pdo->prepare("UPDATE endereco_usuario SET endereco = :endereco, bairro = :bairro, referencia = :referencia, nome_endereco = :nome_endereco WHERE id = :id_endereco");
    $query_update->bindParam(":id_endereco", $id_endereco);
    $query_update->bindParam(":endereco", $enderecoAtt);
    $query_update->bindParam(":bairro", $bairroAtt);
    $query_update->bindParam(":referencia", $referenciaAtt);
    $query_update->bindParam(":nome_endereco", $nome_enderecoAtt);
    if($query_update->execute()){
        $_SESSION['msg'] = "Sucesso!";
        header("location: ../pag/enderecoUsuario.php");
    }
}else{
    Header("Location: ../index.php");
}