<?php
require_once __DIR__ . '/../Core/Database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // 1. Tabela de Usuários (Login Admin)
    $conn->exec("
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            senha VARCHAR(255) NOT NULL,
            is_admin TINYINT(1) DEFAULT 1,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 2. Tabela Candidatos (Trabalhe Conosco)
    $conn->exec("
        CREATE TABLE IF NOT EXISTS candidatos_trabalhe_conosco (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(150) NOT NULL,
            localizacao VARCHAR(100) DEFAULT NULL,
            email VARCHAR(150) NOT NULL,
            whatsapp VARCHAR(30) DEFAULT NULL,
            area_atuacao VARCHAR(100) DEFAULT NULL,
            portfolio VARCHAR(255) DEFAULT NULL,
            resumo TEXT DEFAULT NULL,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 3. Tabela Orçamentos
    $conn->exec("
        CREATE TABLE IF NOT EXISTS orcamentos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(150) NOT NULL,
            email VARCHAR(150) NOT NULL,
            telefone VARCHAR(30) DEFAULT NULL,
            servico_interesse VARCHAR(100) DEFAULT NULL,
            mensagem TEXT DEFAULT NULL,
            status VARCHAR(20) DEFAULT 'pendente',
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 4. Inserção do Usuário Admin Único
    $emailAdmin = 'adminamazonperin';
    $senhaAdmin = password_hash('amazonpicperin1!', PASSWORD_DEFAULT);
    $nomeAdmin  = 'Admin Amazon Perin';

    $stmtCheck = $conn->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
    $stmtCheck->execute([':email' => $emailAdmin]);

    if (!$stmtCheck->fetch()) {
        $stmtInsert = $conn->prepare("
            INSERT INTO usuarios (nome, email, senha, is_admin) 
            VALUES (:nome, :email, :senha, 1)
        ");
        $stmtInsert->execute([
            ':nome'  => $nomeAdmin,
            ':email' => $emailAdmin,
            ':senha' => $senhaAdmin
        ]);
        echo "✅ Migration concluída com sucesso! Tabelas criadas e usuário 'adminamazonperin' registrado.";
    } else {
        echo "✅ Migration executada! Tabelas verificadas (o usuário admin já estava cadastrado).";
    }

} catch (PDOException $e) {
    die("❌ Erro ao executar a migration: " . $e->getMessage());
}