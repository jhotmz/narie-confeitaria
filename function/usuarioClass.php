<?php
class Usuario{
private $pdo;

public function __construct($pdo){
    $this->pdo = $pdo;
}

public function exibirUsuario(){
    $query_usuario = $this->pdo->prepare("SELECT * FROM usuario");
    $query_usuario->execute();
    $exibir_usuarios = $query_usuario->fetchAll(PDO::FETCH_ASSOC);
    return $exibir_usuarios;
}

}