<?php
// Configuração do banco de dados MySQL
define('DB_HOST', 'localhost');
define('DB_NAME', 'sql_pass_jbfdigi');
define('DB_USER', 'sql_pass_jbfdigi');
define('DB_PASSWORD', '19e5a50834e03');
define('DB_CHARSET', 'utf8mb4');

// Senha mestre do sistema (altere para sua senha desejada)
define('MASTER_PASSWORD', trim('jbf2024'));

// Função para conectar ao banco
function getConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Erro de conexão: " . $e->getMessage());
    }
}

// Função para criar a tabela se não existir
function createTableIfNotExists() {
    $pdo = getConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS logins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        ip VARCHAR(255),
        user VARCHAR(255),
        password TEXT,
        port INT DEFAULT 22,
        sftp BOOLEAN DEFAULT FALSE,
        url VARCHAR(500),
        userwordpress VARCHAR(255),
        passwordwordpress TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_name (name),
        INDEX idx_ip (ip),
        INDEX idx_user (user),
        INDEX idx_sftp (sftp),
        INDEX idx_port (port)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    try {
        $pdo->exec($sql);
        return true;
    } catch (PDOException $e) {
        die("Erro ao criar tabela: " . $e->getMessage());
    }
}

// Criar tabela automaticamente
createTableIfNotExists();
?>
