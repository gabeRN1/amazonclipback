<?php
require_once '../app/Core/Database.php';

class Orcamento {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
        // Ativa o modo de exceção do PDO para lançar erros ao invés de falhar em silêncio
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function cadastrar($dados) {
        // Query ajustada com os nomes exatos das colunas da tabela 'orcamentos'
        $query = "INSERT INTO orcamentos (nome, email, telefone, servico_interesse, mensagem) 
                  VALUES (:nome, :email, :telefone, :servico_interesse, :mensagem)";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Mapeia flexivelmente caso a chave venha como 'servicos' ou 'servico_interesse'
            $servicos = $dados['servico_interesse'] ?? $dados['servicos'] ?? '';
            $mensagem = $dados['mensagem'] ?? $dados['objetivo'] ?? '';

            $stmt->bindValue(':nome', $dados['nome'] ?? '');
            $stmt->bindValue(':email', $dados['email'] ?? '');
            $stmt->bindValue(':telefone', $dados['telefone'] ?? '');
            $stmt->bindValue(':servico_interesse', $servicos);
            $stmt->bindValue(':mensagem', $mensagem);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            // Salva o erro exato do banco no arquivo debug.log
            $log = "[" . date('Y-m-d H:i:s') . "] ERRO ORÇAMENTO: " . $e->getMessage() . PHP_EOL;
            $log .= "DADOS RECEBIDOS: " . print_r($dados, true) . PHP_EOL . "---" . PHP_EOL;
            file_put_contents(__DIR__ . '/debug.log', $log, FILE_APPEND);
            return false;
        }
    }
}