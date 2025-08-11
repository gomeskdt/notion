<?php
// API para gerenciar logins no MySQL
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Habilitar exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir configuração do banco
require_once '../config/database.php';

// Função de criptografia usando senha mestre
function encrypt($text, $masterKey) {
    if (empty($text) || empty($masterKey)) return '';
    return base64_encode($text); // Simplificado para teste
}

function decrypt($encryptedText, $masterKey) {
    if (empty($encryptedText) || empty($masterKey)) return '';
    return base64_decode($encryptedText); // Simplificado para teste
}

function maskPassword($password) {
    if (empty($password)) return '';
    return '••••••••';
}

function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

// Tratar CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $pdo = getConnection();
    
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';
    
    switch ($method) {
        case 'GET':
            if ($action === 'check-master-key') {
                // Verificar senha mestre
                $masterKey = trim($_GET['key'] ?? '');
                if (empty($masterKey)) {
                    echo json_encode(['success' => false, 'message' => 'Senha mestre não fornecida']);
                    break;
                }
                
                // Validar senha mestre
                if ($masterKey !== MASTER_PASSWORD) {
                    echo json_encode(['success' => false, 'message' => 'Senha mestre incorreta']);
                    break;
                }
                
                // Salvar senha mestre na sessão
                session_start();
                $_SESSION['master_key'] = $masterKey;
                
                echo json_encode(['success' => true, 'message' => 'Senha mestre verificada']);
                break;
            }
            
            if ($action === 'verify-master-key') {
                // Verificar se a senha mestre está correta
                $masterKey = trim($_GET['key'] ?? '');
                if (empty($masterKey)) {
                    echo json_encode(['success' => false, 'message' => 'Senha mestre não fornecida']);
                    break;
                }
                
                // Validar senha mestre
                if ($masterKey !== MASTER_PASSWORD) {
                    echo json_encode(['success' => false, 'message' => 'Senha mestre incorreta']);
                    break;
                }
                
                // Salvar senha mestre na sessão
                session_start();
                $_SESSION['master_key'] = $masterKey;
                
                echo json_encode(['success' => true, 'message' => 'Senha mestre verificada']);
                break;
            }
            
            if ($action === 'get-password') {
                // Obter senha descriptografada
                session_start();
                $masterKey = $_SESSION['master_key'] ?? '';
                if (empty($masterKey)) {
                    echo json_encode(['success' => false, 'error' => 'Senha mestre não verificada']);
                    break;
                }
                
                $id = $_GET['id'] ?? '';
                $type = $_GET['type'] ?? '';
                
                if (empty($id) || empty($type)) {
                    echo json_encode(['success' => false, 'error' => 'ID e tipo são obrigatórios']);
                    break;
                }
                
                $stmt = $pdo->prepare("SELECT * FROM logins WHERE id = ?");
                $stmt->execute([$id]);
                $login = $stmt->fetch();
                
                if (!$login) {
                    echo json_encode(['success' => false, 'error' => 'Login não encontrado']);
                    break;
                }
                
                $encryptedPassword = $type === 'password' ? $login['password'] : $login['passwordwordpress'];
                $decryptedPassword = decrypt($encryptedPassword, $masterKey);
                
                echo json_encode([
                    'success' => true,
                    'password' => $decryptedPassword
                ]);
                break;
            }
            
            // Listar logins
            $search = $_GET['search'] ?? '';
            $sftp = $_GET['sftp'] ?? '';
            $port = $_GET['port'] ?? '';
            
            $where = [];
            $params = [];
            
            if (!empty($search)) {
                $where[] = "(name LIKE ? OR ip LIKE ? OR user LIKE ? OR url LIKE ? OR userwordpress LIKE ?)";
                $searchParam = "%$search%";
                $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
            }
            
            if ($sftp !== '') {
                $where[] = "sftp = ?";
                $params[] = $sftp;
            }
            
            if (!empty($port)) {
                $where[] = "port = ?";
                $params[] = $port;
            }
            
            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
            
            $sql = "SELECT * FROM logins $whereClause ORDER BY updated_at DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $logins = $stmt->fetchAll();
            
            // Mascarar senhas
            foreach ($logins as &$login) {
                $login['password'] = maskPassword($login['password']);
                $login['passwordwordpress'] = maskPassword($login['passwordwordpress']);
                $login['created_at'] = formatDate($login['created_at']);
                $login['updated_at'] = formatDate($login['updated_at']);
            }
            
            echo json_encode([
                'success' => true,
                'data' => $logins,
                'total' => count($logins)
            ]);
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Verificar se é uma ação específica
            if ($action === 'check-master-key') {
                $masterKey = trim($input['master_key'] ?? '');
                if (empty($masterKey)) {
                    echo json_encode(['success' => false, 'error' => 'Senha mestre não fornecida']);
                    break;
                }
                
                // Debug: verificar se a constante está definida
                if (!defined('MASTER_PASSWORD')) {
                    echo json_encode(['success' => false, 'error' => 'MASTER_PASSWORD não está definida']);
                    break;
                }
                
                // Debug: log dos valores para comparação
                error_log("Senha fornecida (trim): '" . $masterKey . "'");
                error_log("MASTER_PASSWORD: '" . MASTER_PASSWORD . "'");
                error_log("Comprimento senha fornecida: " . strlen($masterKey));
                error_log("Comprimento MASTER_PASSWORD: " . strlen(MASTER_PASSWORD));
                error_log("São iguais? " . ($masterKey === MASTER_PASSWORD ? 'SIM' : 'NÃO'));
                
                // Validar senha mestre
                if ($masterKey !== MASTER_PASSWORD) {
                    echo json_encode(['success' => false, 'error' => 'Senha mestre incorreta']);
                    break;
                }
                
                // Salvar senha mestre na sessão
                session_start();
                $_SESSION['master_key'] = $masterKey;
                
                echo json_encode(['success' => true, 'message' => 'Senha mestre verificada']);
                break;
            }
            
            // Se não for uma ação específica, verificar se é criação de login
            if (empty($input['name'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Nome é obrigatório']);
                break;
            }
            
            // Verificar senha mestre
            session_start();
            $masterKey = $_SESSION['master_key'] ?? '';
            if (empty($masterKey)) {
                // Tentar obter da requisição
                $masterKey = $input['master_key'] ?? '';
                if (!empty($masterKey)) {
                    $_SESSION['master_key'] = $masterKey;
                } else {
                    http_response_code(401);
                    echo json_encode(['success' => false, 'error' => 'Senha mestre não verificada']);
                    break;
                }
            }
            
            $sql = "INSERT INTO logins (name, ip, user, password, port, sftp, url, userwordpress, passwordwordpress) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $input['name'],
                $input['ip'] ?? '',
                $input['user'] ?? '',
                encrypt($input['password'] ?? '', $masterKey),
                intval($input['port'] ?? 22),
                !empty($input['sftp']) ? 1 : 0,
                $input['url'] ?? '',
                $input['userwordpress'] ?? '',
                encrypt($input['passwordwordpress'] ?? '', $masterKey)
            ]);
            
            $id = $pdo->lastInsertId();
            echo json_encode([
                'success' => true,
                'message' => 'Login criado com sucesso',
                'id' => $id
            ]);
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? '';
            
            if (empty($id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'ID é obrigatório']);
                break;
            }
            
            // Verificar senha mestre
            session_start();
            $masterKey = $_SESSION['master_key'] ?? '';
            if (empty($masterKey)) {
                // Tentar obter da requisição
                $masterKey = $input['master_key'] ?? '';
                if (!empty($masterKey)) {
                    $_SESSION['master_key'] = $masterKey;
                } else {
                    http_response_code(401);
                    echo json_encode(['success' => false, 'error' => 'Senha mestre não verificada']);
                    break;
                }
            }
            
            // Buscar login existente
            $stmt = $pdo->prepare("SELECT * FROM logins WHERE id = ?");
            $stmt->execute([$id]);
            $existingLogin = $stmt->fetch();
            
            if (!$existingLogin) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Login não encontrado']);
                break;
            }
            
            // Preparar dados para atualização
            $password = !empty($input['password']) ? encrypt($input['password'], $masterKey) : $existingLogin['password'];
            $passwordwordpress = !empty($input['passwordwordpress']) ? encrypt($input['passwordwordpress'], $masterKey) : $existingLogin['passwordwordpress'];
            
            $sql = "UPDATE logins SET name = ?, ip = ?, user = ?, password = ?, port = ?, sftp = ?, url = ?, userwordpress = ?, passwordwordpress = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $input['name'],
                $input['ip'] ?? '',
                $input['user'] ?? '',
                $password,
                intval($input['port'] ?? 22),
                !empty($input['sftp']) ? 1 : 0,
                $input['url'] ?? '',
                $input['userwordpress'] ?? '',
                $passwordwordpress,
                $id
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Login atualizado com sucesso'
            ]);
            break;
            
        case 'DELETE':
            $id = $_GET['id'] ?? '';
            
            if (empty($id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'ID é obrigatório']);
                break;
            }
            
            $sql = "DELETE FROM logins WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Login excluído com sucesso'
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Login não encontrado']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro: ' . $e->getMessage()]);
}
?>
