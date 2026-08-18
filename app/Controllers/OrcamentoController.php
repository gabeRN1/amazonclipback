<?php
require_once '../app/Models/Orcamento.php';

class OrcamentoController extends Controller {
    
    public function enviar() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }

        $dados = json_decode(file_get_contents("php://input"), true) ?: $_POST;

        if (empty($dados['nome']) || empty($dados['email']) || empty($dados['telefone']) || empty($dados['objetivo']) || empty($dados['aceite_privacidade'])) {
            http_response_code(400); 
            echo json_encode(["erro" => "Preencha os campos obrigatórios."]);
            exit();
        }

        try {
            $orcamentoModel = new Orcamento();
            
            // Os checkboxes de "servicos" podem vir como array se enviados via JSON ou form padrão
            $servicos = isset($dados['servicos']) && is_array($dados['servicos']) 
                        ? implode(', ', $dados['servicos']) 
                        : ($dados['servicos'] ?? '');

            $dadosOrcamento = [
                'nome' => trim($dados['nome']),
                'email' => filter_var(trim($dados['email']), FILTER_SANITIZE_EMAIL),
                'telefone' => trim($dados['telefone']),
                'servicos' => $servicos,
                'objetivo' => trim($dados['objetivo'])
            ];

            if ($orcamentoModel->cadastrar($dadosOrcamento)) {
                http_response_code(201); 
                echo json_encode(["sucesso" => "Orçamento solicitado com sucesso!"]);
            } else {
                http_response_code(500); 
                echo json_encode(["erro" => "Falha ao gravar no banco."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["erro" => "Erro interno."]);
        }
    }
}