<?php
// Teste simples da senha mestre
require_once 'config/database.php';

echo "<h1>🔑 Teste Simples da Senha Mestre</h1>";

echo "<h2>1. Verificação da Constante</h2>";
if (defined('MASTER_PASSWORD')) {
    echo "<p style='color: green;'>✅ MASTER_PASSWORD está definida</p>";
    echo "<p><strong>Valor:</strong> '" . MASTER_PASSWORD . "'</p>";
    echo "<p><strong>Comprimento:</strong> " . strlen(MASTER_PASSWORD) . "</p>";
} else {
    echo "<p style='color: red;'>❌ MASTER_PASSWORD NÃO está definida</p>";
}

echo "<h2>2. Teste de Comparação</h2>";
$test_senha = 'jbf2024';
echo "<p><strong>Senha de teste:</strong> '$test_senha'</p>";

if (defined('MASTER_PASSWORD')) {
    $igual = ($test_senha === MASTER_PASSWORD);
    echo "<p><strong>Resultado:</strong> " . ($igual ? '✅ IGUAL' : '❌ DIFERENTE') . "</p>";
    
    if (!$igual) {
        echo "<p><strong>MASTER_PASSWORD:</strong> '" . MASTER_PASSWORD . "'</p>";
        echo "<p><strong>Teste:</strong> '$test_senha'</p>";
    }
}

echo "<h2>3. Teste da API</h2>";
$api_url = 'api/logins.php';
$post_data = json_encode(['master_key' => 'jbf2024']);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => $post_data
    ]
]);

$response = file_get_contents($api_url . '?action=check-master-key', false, $context);
echo "<p><strong>Resposta da API:</strong></p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

$data = json_decode($response, true);
if ($data && $data['success']) {
    echo "<p style='color: green;'>✅ API aceitou a senha</p>";
} else {
    echo "<p style='color: red;'>❌ API rejeitou a senha</p>";
}

echo "<h2>4. Links</h2>";
echo "<p><a href='index.html'>Aplicação Principal</a></p>";
echo "<p><a href='teste_debug_senha.php'>Debug Completo</a></p>";
?>
