<?php
require_once '../app/Core/Database.php';

class Candidato {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
        // Ativa o modo de exceção do PDO para lançar erros ao invés de falhar em silêncio
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function cadastrar($dados) {
        $query = "INSERT INTO candidatos_trabalhe_conosco 
                  (nome, localizacao, email, whatsapp, area_atuacao, portfolio, resumo) 
                  VALUES 
                  (:nome, :localizacao, :email, :whatsapp, :area_atuacao, :portfolio, :resumo)";
        
        try {
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(':nome', $dados['nome'] ?? '');
            $stmt->bindValue(':localizacao', $dados['localizacao'] ?? '');
            $stmt->bindValue(':email', $dados['email'] ?? '');
            $stmt->bindValue(':whatsapp', $dados['whatsapp'] ?? '');
            $stmt->bindValue(':area_atuacao', $dados['area_atuacao'] ?? '');
            $stmt->bindValue(':portfolio', $dados['portfolio'] ?? '');
            $stmt->bindValue(':resumo', $dados['resumo'] ?? '');
            
            return $stmt->execute();
        } catch (PDOException $e) {
            // Salva o erro exato do banco no arquivo debug.log
            $log = "[" . date('Y-m-d H:i:s') . "] ERRO CANDIDATO: " . $e->getMessage() . PHP_EOL;
            $log .= "DADOS RECEBIDOS: " . print_r($dados, true) . PHP_EOL . "---" . PHP_EOL;
            file_put_contents(__DIR__ . '/debug.log', $log, FILE_APPEND);
            return false;
        }
    }

    public function contarTotal($area_atuacao = '', $localizacao = '') {
        $condicoes = [];
        if (!empty($area_atuacao)) { $condicoes[] = "area_atuacao = :area_atuacao"; }
        if (!empty($localizacao)) { $condicoes[] = "localizacao = :localizacao"; }

        $where = count($condicoes) > 0 ? " WHERE " . implode(" AND ", $condicoes) : "";
        
        $query = "SELECT COUNT(id) as total FROM candidatos_trabalhe_conosco $where";
        $stmt = $this->conn->prepare($query);

        if (!empty($area_atuacao)) { $stmt->bindValue(':area_atuacao', $area_atuacao); }
        if (!empty($localizacao)) { $stmt->bindValue(':localizacao', $localizacao); }

        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $resultado['total'];
    }

    public function obterDadosPaginados($area_atuacao = '', $localizacao = '', $limite = 10, $offset = 0) {
        $condicoes = [];
        if (!empty($area_atuacao)) { $condicoes[] = "area_atuacao = :area_atuacao"; }
        if (!empty($localizacao)) { $condicoes[] = "localizacao = :localizacao"; }

        $where = count($condicoes) > 0 ? " WHERE " . implode(" AND ", $condicoes) : "";

        $query = "SELECT id, nome, localizacao, email, whatsapp, area_atuacao, portfolio, resumo, criado_em 
                  FROM candidatos_trabalhe_conosco 
                  $where 
                  ORDER BY criado_em DESC 
                  LIMIT :limite OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        if (!empty($area_atuacao)) { $stmt->bindValue(':area_atuacao', $area_atuacao); }
        if (!empty($localizacao)) { $stmt->bindValue(':localizacao', $localizacao); }
        
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obterAreasAtuacao() {
        $query = "SELECT DISTINCT area_atuacao FROM candidatos_trabalhe_conosco 
                  WHERE area_atuacao IS NOT NULL AND area_atuacao != '' 
                  ORDER BY area_atuacao ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function obterLocalizacoes() {
        $query = "SELECT DISTINCT localizacao FROM candidatos_trabalhe_conosco 
                  WHERE localizacao IS NOT NULL AND localizacao != '' 
                  ORDER BY localizacao ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}