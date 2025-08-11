<?php
// Teste de debug da senha mestre
echo "<h1>🔍 Debug da Senha Mestre</h1>";

echo "<h2>1. Verificação da Constante MASTER_PASSWORD</h2>";

// Verificar se a constante está definida
if (defined('MASTER_PASSWORD')) {
    echo "<p style='color: green;'>✅ <strong>MASTER_PASSWORD está definida</strong></p>";
    echo "<p><strong>Valor:</strong> " . MASTER_PASSWORD . "</p>";
    echo "<p><strong>Comprimento:</strong> " . strlen(MASTER_PASSWORD) . " caracteres</p>";
    echo "<p><strong>Tipo:</strong> " . gettype(MASTER_PASSWORD) . "</p>";
} else {
    echo "<p style='color: red;'>❌ <strong>MASTER_PASSWORD NÃO está definida</strong></p>";
}

echo "<h2>2. Teste de Comparação</h2>";

$test_password = 'jbf2024';
echo "<p><strong>Senha de teste:</strong> '$test_password'</p>";
echo "<p><strong>Comprimento:</strong> " . strlen($test_password) . " caracteres</p>";
echo "<p><strong>Tipo:</strong> " . gettype($test_password) . "</p>";

if (defined('MASTER_PASSWORD')) {
    $is_equal = ($test_password === MASTER_PASSWORD);
    echo "<p><strong>São iguais?</strong> " . ($is_equal ? 'SIM' : 'NÃO') . "</p>";
    
    if (!$is_equal) {
        echo "<h3>Análise da Diferença:</h3>";
        echo "<ul>";
        echo "<li><strong>MASTER_PASSWORD:</strong> '" . MASTER_PASSWORD . "'</li>";
        echo "<li><strong>Test Password:</strong> '$test_password'</li>";
        echo "<li><strong>Diferença de comprimento:</strong> " . (strlen(MASTER_PASSWORD) - strlen($test_password)) . "</li>";
        echo "<li><strong>Caracteres ASCII MASTER_PASSWORD:</strong> ";
        for ($i = 0; $i < strlen(MASTER_PASSWORD); $i++) {
            echo ord(MASTER_PASSWORD[$i]) . " ";
        }
        echo "</li>";
        echo "<li><strong>Caracteres ASCII Test:</strong> ";
        for ($i = 0; $i < strlen($test_password); $i++) {
            echo ord($test_password[$i]) . " ";
        }
        echo "</li>";
        echo "</ul>";
    }
}

echo "<h2>3. Verificação do Arquivo de Configuração</h2>";

$config_file = 'config/database.php';
if (file_exists($config_file)) {
    echo "<p style='color: green;'>✅ <strong>Arquivo config/database.php existe</strong></p>";
    
    $content = file_get_contents($config_file);
    echo "<h3>Conteúdo do arquivo:</h3>";
    echo "<pre>" . htmlspecialchars($content) . "</pre>";
    
    // Procurar pela definição da MASTER_PASSWORD
    if (strpos($content, 'MASTER_PASSWORD') !== false) {
        echo "<p style='color: green;'>✅ <strong>MASTER_PASSWORD encontrada no arquivo</strong></p>";
        
        // Extrair a linha com MASTER_PASSWORD
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            if (strpos($line, 'MASTER_PASSWORD') !== false) {
                echo "<p><strong>Linha encontrada:</strong> " . htmlspecialchars(trim($line)) . "</p>";
                break;
            }
        }
    } else {
        echo "<p style='color: red;'>❌ <strong>MASTER_PASSWORD NÃO encontrada no arquivo</strong></p>";
    }
} else {
    echo "<p style='color: red;'>❌ <strong>Arquivo config/database.php NÃO existe</strong></p>";
}

echo "<h2>4. Teste da API</h2>";

// Testar a API diretamente
$api_url = 'api/logins.php';
$test_master_key = 'jbf2024';

echo "<h3>4.1 Teste POST - Verificar Senha Mestre</h3>";
$post_data = json_encode(['master_key' => $test_master_key]);

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

// Decodificar resposta
$data = json_decode($response, true);
if ($data) {
    if ($data['success']) {
        echo "<p style='color: green;'>✅ <strong>SUCESSO:</strong> Senha aceita pela API</p>";
    } else {
        echo "<p style='color: red;'>❌ <strong>ERRO:</strong> " . htmlspecialchars($data['error'] ?? $data['message'] ?? 'Erro desconhecido') . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ <strong>ERRO:</strong> Resposta não é JSON válido</p>";
}

echo "<h2>5. Verificação de Logs</h2>";
echo "<p>Verifique os logs do servidor para ver as mensagens de debug:</p>";
echo "<ul>";
echo "<li>📁 <strong>Local:</strong> /var/log/apache2/error.log (Linux)</li>";
echo "<li>📁 <strong>Local:</strong> /var/log/httpd/error_log (CentOS/RHEL)</li>";
echo "<li>📁 <strong>Local:</strong> logs/error.log (XAMPP/WAMP)</li>";
echo "</ul>";

echo "<h2>6. Possíveis Problemas</h2>";
echo "<ul>";
echo "<li>🚫 <strong>Constante não definida:</strong> MASTER_PASSWORD não foi carregada</li>";
echo "<li>🔤 <strong>Encoding:</strong> Problemas de caracteres especiais</li>";
echo "<li>📝 <strong>Espaços extras:</strong> Espaços antes/depois da senha</li>";
echo "<li>🔄 <strong>Cache:</strong> Arquivo não foi recarregado</li>";
echo "<li>📁 <strong>Caminho:</strong> Arquivo de configuração não encontrado</li>";
echo "</ul>";

echo "<h2>7. Soluções</h2>";
echo "<ol>";
echo "<li>🔄 <strong>Recarregue a página</strong> → Limpar cache do navegador</li>";
echo "<li>📝 <strong>Verifique o arquivo</strong> → config/database.php</li>";
echo "<li>🔤 <strong>Verifique encoding</strong> → UTF-8 sem BOM</li>";
echo "<li>🚫 <strong>Remova espaços</strong> → Antes e depois da senha</li>";
echo "<li>📁 <strong>Verifique permissões</strong> → Arquivo legível</li>";
echo "</ol>";

echo "<h2>8. Links de Teste</h2>";
echo "<p><a href='index.html' target='_blank'>🏠 Aplicação Principal</a></p>";
echo "<p><a href='api/logins.php' target='_blank'>🔌 API Direta</a></p>";
echo "<p><a href='config/database.php' target='_blank'>⚙️ Arquivo de Configuração</a></p>";

echo "<h2>9. Status do Debug</h2>";
echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; border: 1px solid #ffeaa7;'>";
echo "<strong>🔍 DEBUG ATIVO!</strong><br>";
echo "Verifique os logs do servidor e as informações acima para identificar ";
echo "por que a senha mestre não está sendo validada corretamente.";
echo "</div>";
?>
