<?php
require_once '../app/Core/Database.php';

class Orcamento {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function cadastrar($dados) {
        $query = "INSERT INTO orcamentos (nome, email, telefone, servicos, objetivo) 
                  VALUES (:nome, :email, :telefone, :servicos, :objetivo)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nome', $dados['nome']);
        $stmt->bindParam(':email', $dados['email']);
        $stmt->bindParam(':telefone', $dados['telefone']);
        $stmt->bindParam(':servicos', $dados['servicos']);
        $stmt->bindParam(':objetivo', $dados['objetivo']);
        
        return $stmt->execute();
    }
}