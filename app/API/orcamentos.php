<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../app/Core/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Captura os serviços selecionados (caso venha array de checkboxes)
    $servicos = isset($_POST['servicos']) ? (is_array($_POST['servicos']) ? implode(', ', $_POST['servicos']) : $_POST['servicos']) : '';

    $stmt = $conn->prepare("
        INSERT INTO orcamentos 
        (nome, email, telefone, servico_interesse, mensagem) 
        VALUES (:nome, :email, :telefone, :servico_interesse, :mensagem)
    ");

    $stmt->execute([
        ':nome'              => $_POST['nome'] ?? '',
        ':email'             => $_POST['email'] ?? '',
        ':telefone'          => $_POST['telefone'] ?? '',
        ':servico_interesse' => $servicos,
        ':mensagem'          => $_POST['objetivo'] ?? ''
    ]);

    // Opcional: Disparo do e-mail de orçamento

    echo json_encode(['success' => true, 'message' => 'Orçamento salvo com sucesso!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar no banco: ' . $e->getMessage()]);
}