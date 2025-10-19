<?php
class Dbh
{
    private $pdo;
    private $db_name = "db_narie";
    private $host = "127.0.0.1";
    private $username = "root";
    private $password = "";

    public function __construct()
    {
        try {
            $this->pdo = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         
        } catch (PDOException $e) {
            throw new Exception("Erro de conexão: " . $e->getMessage());
        }
    }

    public function getPdo()
    {
        return $this->pdo;
    }
}








