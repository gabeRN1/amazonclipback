<?php
require_once '../app/Models/Orcamento.php';
require_once '../app/Core/Controller.php';

class OrcamentoController extends Controller {

    // Função auxiliar para gerar os logs
    private function criarLog($tipo, $dados) {
        // Define o caminho da pasta logs (um nível antes da pasta app)
        $caminhoPastaLogs = __DIR__ . '/../../logs';
        
        // Cria a pasta automaticamente se ela não existir
        if (!is_dir($caminhoPastaLogs)) {
            mkdir($caminhoPastaLogs, 0777, true);
        }

        $arquivoLog = $caminhoPastaLogs . '/orcamento_' . date('Y-m-d') . '.log';
        $dataHora = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'IP_Desconhecido';
        
        $mensagem = "[$dataHora] [IP: $ip] [$tipo]: \n";
        $mensagem .= print_r($dados, true) . "\n";
        $mensagem .= str_repeat("-", 50) . "\n";
        
        // Escreve no arquivo de log
        file_put_contents($arquivoLog, $mensagem, FILE_APPEND);
    }
    
    public function enviar() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { 
            http_response_code(200); 
            exit(); 
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
            http_response_code(405);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido']);
            exit(); 
        }

        // 1. CAPTURA DOS DADOS (O MAIS IMPORTANTE PARA O DEBUG)
        // Capturamos o texto puro (Raw) recebido na requisição antes de qualquer coisa
        $rawInput = file_get_contents("php://input");
        
        // Decodificamos o JSON ou usamos o $_POST padrão
        $dados = json_decode($rawInput, true) ?: $_POST;

        // 2. GRAVA O LOG DO QUE CHEGOU DO FRONTEND
        $this->criarLog("DADOS_RECEBIDOs_DO_FRONTEND", [
            'Headers' => function_exists('getallheaders') ? getallheaders() : [],
            'Payload_Bruto_RAW' => $rawInput,
            '$_POST_Nativo' => $_POST,
            'Dados_Finais_Interpretados' => $dados
        ]);

        // Validação rígida
        if (empty($dados['nome']) || empty($dados['email']) || empty($dados['telefone']) || empty($dados['objetivo'])) {
            // Registra no log que a validação falhou e o que estava faltando
            $this->criarLog("FALHA_VALIDACAO", "Campos obrigatórios ausentes. Verifique a estrutura acima.");
            
            http_response_code(400); 
            echo json_encode(["sucesso" => false, "mensagem" => "Preencha todos os campos obrigatórios."]);
            exit();
        }

        try {
            $orcamentoModel = new Orcamento();
            
            $servicos = isset($dados['servicos']) 
                ? (is_array($dados['servicos']) ? implode(', ', $dados['servicos']) : $dados['servicos']) 
                : '';

            $dadosOrcamento = [
                'nome'     => htmlspecialchars(trim($dados['nome'])),
                'email'    => filter_var(trim($dados['email']), FILTER_SANITIZE_EMAIL),
                'telefone' => htmlspecialchars(trim($dados['telefone'])),
                'servicos' => htmlspecialchars($servicos),
                'objetivo' => htmlspecialchars(trim($dados['objetivo']))
            ];

            if ($orcamentoModel->cadastrar($dadosOrcamento)) {
                $this->criarLog("SUCESSO", "Orçamento salvo no banco com sucesso!");
                http_response_code(201); 
                echo json_encode(["sucesso" => true, "mensagem" => "Orçamento solicitado com sucesso!"]);
            } else {
                $this->criarLog("ERRO_BANCO", "A Query executou mas retornou falso.");
                http_response_code(500); 
                echo json_encode(["sucesso" => false, "mensagem" => "Falha ao gravar orçamento no banco."]);
            }
        } catch (Exception $e) {
            $this->criarLog("ERRO_INTERNO_CATCH", $e->getMessage());
            http_response_code(500);
            echo json_encode(["sucesso" => false, "mensagem" => "Erro interno do servidor."]);
        }
    }
}