<?php
// Teste específico para verificar o problema da senha mestre
echo "<h1>🔑 Teste de Verificação de Senha Mestre</h1>";

echo "<h2>1. Problema Reportado</h2>";
echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb;'>";
echo "<strong>❌ ERRO:</strong> 'Nome obrigatório' ao clicar em 'Verificar Senha & Carregar'<br>";
echo "O usuário espera: Verificar senha mestre e listar todos os logins";
echo "</div>";

echo "<h2>2. Análise do Problema</h2>";
echo "<p>O erro 'Nome obrigatório' sugere que a API está interpretando a requisição como um POST para criar login, ";
echo "em vez de verificar a senha mestre ou listar logins.</p>";

echo "<h2>3. Teste da API - Verificação de Senha</h2>";

// Testar verificação de senha mestre
$api_url = 'api/logins.php';
$test_master_key = 'test123'; // Senha de teste

echo "<h3>3.1 Teste POST - Verificar Senha Mestre</h3>";
$post_data = json_encode(['master_key' => $test_master_key]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => $post_data
    ]
]);

$response = file_get_contents($api_url . '?action=check-master-key', false, $context);
echo "<p><strong>Resposta da verificação de senha:</strong></p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

echo "<h3>3.2 Teste GET - Listar Logins</h3>";
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'Content-Type: application/json'
    ]
]);

$response = file_get_contents($api_url, false, $context);
echo "<p><strong>Resposta da listagem de logins:</strong></p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

echo "<h2>4. Verificação do Código JavaScript</h2>";
echo "<h3>4.1 Função checkMasterKeyAndLoad()</h3>";
echo "<pre>";
echo "function checkMasterKeyAndLoad() {\n";
echo "    const masterKey = document.getElementById('password').value;\n";
echo "    if (!masterKey) {\n";
echo "        showNotification('Digite a senha mestre primeiro!', 'error');\n";
echo "        return;\n";
echo "    }\n\n";
echo "    fetch(`${API_URL}?action=check-master-key`, {\n";
echo "        method: 'POST',\n";
echo "        headers: { 'Content-Type': 'application/json' },\n";
echo "        body: JSON.stringify({ master_key: masterKey })\n";
echo "    })\n";
echo "    .then(response => response.json())\n";
echo "    .then(data => {\n";
echo "        if (data.success) {\n";
echo "            showNotification('Senha mestre verificada com sucesso!', 'success');\n";
echo "            loadLogins(); // ← Aqui carrega os logins\n";
echo "        } else {\n";
echo "            showNotification(data.error || 'Senha mestre incorreta', 'error');\n";
echo "        }\n";
echo "    });\n";
echo "}";
echo "</pre>";

echo "<h3>4.2 Função loadLogins()</h3>";
echo "<pre>";
echo "function loadLogins() {\n";
echo "    const masterKey = document.getElementById('password').value;\n";
echo "    const search = document.getElementById('searchInput').value;\n\n";
echo "    let url = API_URL;\n";
echo "    const params = new URLSearchParams();\n";
echo "    if (search) params.append('search', search);\n";
echo "    if (params.toString()) url += '?' + params.toString();\n\n";
echo "    fetch(url, {\n";
echo "        method: 'GET',\n";
echo "        headers: { 'Content-Type': 'application/json' }\n";
echo "    })\n";
echo "    .then(response => response.json())\n";
echo "    .then(data => {\n";
echo "        if (data.success) {\n";
echo "            logins = data.data;\n";
echo "            renderLogins();\n";
echo "            updateCounter();\n";
echo "        } else {\n";
echo "            showNotification('Erro ao carregar logins: ' + data.error, 'error');\n";
echo "        }\n";
echo "    });\n";
echo "}";
echo "</pre>";

echo "<h2>5. Possíveis Causas do Problema</h2>";
echo "<ul>";
echo "<li>🚫 <strong>API confundindo métodos:</strong> GET sendo interpretado como POST</li>";
echo "<li>🔗 <strong>URL incorreta:</strong> action=check-master-key não sendo processado</li>";
echo "<li>📝 <strong>Dados sendo enviados:</strong> Body sendo interpretado como dados de login</li>";
echo "<li>🔄 <strong>Ordem de execução:</strong> loadLogins() executando antes da verificação</li>";
echo "<li>🗄️ <strong>Problema no banco:</strong> API tentando criar registro em vez de listar</li>";
echo "</ul>";

echo "<h2>6. Soluções Propostas</h2>";
echo "<ol>";
echo "<li>🔍 <strong>Verificar logs da API:</strong> Ver o que está sendo recebido</li>";
echo "<li>🔧 <strong>Separar as chamadas:</strong> Verificar senha primeiro, depois carregar</li>";
echo "<li>📝 <strong>Adicionar logs:</strong> Mais detalhes no JavaScript</li>";
echo "<li>🛠️ <strong>Testar API isoladamente:</strong> Verificar cada endpoint</li>";
echo "<li>🔒 <strong>Verificar autenticação:</strong> Se a senha está sendo validada</li>";
echo "</ol>";

echo "<h2>7. Teste Manual</h2>";
echo "<p><strong>Para testar manualmente:</strong></p>";
echo "<ol>";
echo "<li>Abra o console do navegador (F12)</li>";
echo "<li>Digite uma senha mestre no campo</li>";
echo "<li>Clique em 'Verificar Senha & Carregar'</li>";
echo "<li>Observe os logs no console</li>";
echo "<li>Verifique a aba Network para ver as requisições</li>";
echo "</ol>";

echo "<h2>8. Comandos para Testar API</h2>";
echo "<pre>";
echo "# Testar verificação de senha\n";
echo "curl -X POST 'https://pass.jbfdigital.com.br/api/logins.php?action=check-master-key' \\\n";
echo "  -H 'Content-Type: application/json' \\\n";
echo "  -d '{\"master_key\":\"sua_senha_aqui\"}'\n\n";
echo "# Testar listagem de logins\n";
echo "curl -X GET 'https://pass.jbfdigital.com.br/api/logins.php' \\\n";
echo "  -H 'Content-Type: application/json'";
echo "</pre>";

echo "<h2>9. Links de Teste</h2>";
echo "<p><a href='index.html' target='_blank'>🏠 Aplicação Principal</a></p>";
echo "<p><a href='api/logins.php' target='_blank'>🔌 API Direta</a></p>";
echo "<p><a href='teste_carregamento_debug.php' target='_blank'>🔍 Debug Carregamento</a></p>";

echo "<h2>10. Status do Debug</h2>";
echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; border: 1px solid #ffeaa7;'>";
echo "<strong>🔍 DEBUG ATIVO!</strong><br>";
echo "Verifique o console do navegador e execute os testes acima para identificar ";
echo "exatamente onde está ocorrendo o erro 'Nome obrigatório'.";
echo "</div>";
?>
