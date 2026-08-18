<?php
require_once '../app/Models/Candidato.php';

class TrabalheConoscoController extends Controller {

    public function receberFormulario() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rawInput = json_decode(file_get_contents('php://input'), true);
            $input = is_array($rawInput) ? array_merge($_POST, $rawInput) : $_POST;

            if (!empty($input['nome']) && !empty($input['email'])) {
                try {
                    $candidatoModel = new Candidato();

                    // Aceita variações de nomes vindos do formulário e mapeia para as colunas do banco
                    $dadosCandidato = [
                        'nome'         => trim($input['nome']),
                        'localizacao'  => trim($input['localizacao'] ?? ''),
                        'email'        => filter_var(trim($input['email']), FILTER_SANITIZE_EMAIL),
                        'whatsapp'     => trim($input['whatsapp'] ?? $input['telefone'] ?? ''),
                        'area_atuacao' => trim($input['area_atuacao'] ?? $input['area'] ?? ''),
                        'portfolio'    => trim($input['portfolio'] ?? ''),
                        'resumo'       => trim($input['resumo'] ?? $input['mensagem'] ?? '')
                    ];

                    $sucesso = $candidatoModel->cadastrar($dadosCandidato);

                    if ($sucesso) {
                        http_response_code(201);
                        echo json_encode(['status' => 'sucesso', 'mensagem' => 'Cadastro recebido com sucesso!']);
                        exit();
                    } else {
                        http_response_code(500);
                        echo json_encode(['status' => 'erro', 'mensagem' => 'Falha ao gravar no banco.']);
                        exit();
                    }
                } catch (Exception $e) {
                    http_response_code(500);
                    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro interno no servidor.']);
                    exit();
                }
            }
        }

        http_response_code(400);
        echo json_encode(['status' => 'erro', 'mensagem' => 'Dados inválidos ou campos obrigatórios (nome, email) ausentes.']);
    }
}