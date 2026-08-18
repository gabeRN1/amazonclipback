<?php
require_once '../app/Models/Candidato.php';

class TrabalheConoscoController extends Controller {

    public function receberFormulario() {
        // Libera requisições originadas do domínio amazonpicture
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

            if (!empty($input['nome']) && !empty($input['email'])) {
                $candidatoModel = new Candidato();
                $sucesso = $candidatoModel->cadastrar([
                    'nome' => trim($input['nome']),
                    'localizacao' => trim($input['localizacao'] ?? ''),
                    'email' => trim($input['email']),
                    'whatsapp' => trim($input['whatsapp'] ?? ''),
                    'area_atuacao' => trim($input['area_atuacao'] ?? ''),
                    'portfolio' => trim($input['portfolio'] ?? ''),
                    'resumo' => trim($input['resumo'] ?? '')
                ]);

                if ($sucesso) {
                    http_response_code(201);
                    echo json_encode(['status' => 'sucesso', 'mensagem' => 'Cadastro recebido']);
                    exit();
                }
            }
        }

        http_response_code(400);
        echo json_encode(['status' => 'erro', 'mensagem' => 'Dados inválidos']);
    }
}