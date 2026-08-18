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

    $stmt = $conn->prepare("
        INSERT INTO candidatos_trabalhe_conosco 
        (nome, localizacao, email, whatsapp, area_atuacao, portfolio, resumo) 
        VALUES (:nome, :localizacao, :email, :whatsapp, :area_atuacao, :portfolio, :resumo)
    ");

    $stmt->execute([
        ':nome'         => $_POST['nome'] ?? '',
        ':localizacao'  => $_POST['localizacao'] ?? '',
        ':email'        => $_POST['email'] ?? '',
        ':whatsapp'     => $_POST['whatsapp'] ?? '',
        ':area_atuacao' => $_POST['area_atuacao'] ?? '',
        ':portfolio'    => $_POST['portfolio'] ?? '',
        ':resumo'       => $_POST['resumo'] ?? ''
    ]);

    // Opcional: Chame sua função/API de envio de e-mail aqui no PHP se desejar centralizar

    echo json_encode(['success' => true, 'message' => 'Candidatura cadastrada com sucesso!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar no banco: ' . $e->getMessage()]);
}