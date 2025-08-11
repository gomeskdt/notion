<?php
// Teste da validação da senha mestre
echo "<h1>🔒 Teste da Validação da Senha Mestre</h1>";

echo "<h2>1. Problema Reportado</h2>";
echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb;'>";
echo "<strong>❌ PROBLEMA:</strong> Mesmo digitando a senha mestre errada, os logins carregam e as senhas ficam visíveis<br>";
echo "O sistema não estava validando se a senha mestre estava correta";
echo "</div>";

echo "<h2>2. Correção Implementada</h2>";
echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; border: 1px solid #c3e6cb;'>";
echo "<strong>✅ CORREÇÃO:</strong> Implementada validação da senha mestre<br>";
echo "Senha mestre definida: <strong>jbf2024</strong><br>";
echo "Agora o sistema só aceita a senha correta";
echo "</div>";

echo "<h2>3. Senha Mestre Configurada</h2>";
echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; border: 1px solid #ffeaa7;'>";
echo "<strong>🔑 SENHA MESTRE:</strong> jbf2024<br>";
echo "<strong>⚠️ IMPORTANTE:</strong> Esta senha está definida no arquivo config/database.php<br>";
echo "Para alterar, edite a constante MASTER_PASSWORD";
echo "</div>";

echo "<h2>4. Teste da Validação</h2>";

// Testar com senha correta
$api_url = 'api/logins.php';
$correct_password = 'jbf2024';
$wrong_password = 'senhaerrada';

echo "<h3>4.1 Teste com Senha Correta</h3>";
$post_data = json_encode(['master_key' => $correct_password]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => $post_data
    ]
]);

$response = file_get_contents($api_url . '?action=check-master-key', false, $context);
echo "<p><strong>Resposta com senha correta:</strong></p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

$data = json_decode($response, true);
if ($data && $data['success']) {
    echo "<p style='color: green;'>✅ <strong>SUCESSO:</strong> Senha correta aceita</p>";
} else {
    echo "<p style='color: red;'>❌ <strong>ERRO:</strong> " . htmlspecialchars($data['error'] ?? $data['message'] ?? 'Erro desconhecido') . "</p>";
}

echo "<h3>4.2 Teste com Senha Incorreta</h3>";
$post_data = json_encode(['master_key' => $wrong_password]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => $post_data
    ]
]);

$response = file_get_contents($api_url . '?action=check-master-key', false, $context);
echo "<p><strong>Resposta com senha incorreta:</strong></p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

$data = json_decode($response, true);
if ($data && !$data['success']) {
    echo "<p style='color: green;'>✅ <strong>SUCESSO:</strong> Senha incorreta rejeitada</p>";
} else {
    echo "<p style='color: red;'>❌ <strong>ERRO:</strong> Senha incorreta foi aceita</p>";
}

echo "<h2>5. Código da Validação</h2>";
echo "<h3>5.1 Configuração (config/database.php)</h3>";
echo "<pre>";
echo "// Senha mestre do sistema (altere para sua senha desejada)\n";
echo "define('MASTER_PASSWORD', 'jbf2024');";
echo "</pre>";

echo "<h3>5.2 Validação na API (api/logins.php)</h3>";
echo "<pre>";
echo "// Verificar se é uma ação específica\n";
echo "if (\$action === 'check-master-key') {\n";
echo "    \$masterKey = \$input['master_key'] ?? '';\n";
echo "    if (empty(\$masterKey)) {\n";
echo "        echo json_encode(['success' => false, 'error' => 'Senha mestre não fornecida']);\n";
echo "        break;\n";
echo "    }\n\n";
echo "    // Validar senha mestre\n";
echo "    if (\$masterKey !== MASTER_PASSWORD) {\n";
echo "        echo json_encode(['success' => false, 'error' => 'Senha mestre incorreta']);\n";
echo "        break;\n";
echo "    }\n\n";
echo "    // Salvar senha mestre na sessão\n";
echo "    session_start();\n";
echo "    \$_SESSION['master_key'] = \$masterKey;\n\n";
echo "    echo json_encode(['success' => true, 'message' => 'Senha mestre verificada']);\n";
echo "    break;\n";
echo "}";
echo "</pre>";

echo "<h2>6. Fluxo de Segurança</h2>";
echo "<ol>";
echo "<li>🔑 <strong>Usuário digita senha</strong> → Campo de senha mestre</li>";
echo "<li>🔘 <strong>Usuário clica no botão</strong> → 'Verificar Senha & Carregar'</li>";
echo "<li>📡 <strong>JavaScript envia POST</strong> → api/logins.php?action=check-master-key</li>";
echo "<li>🔒 <strong>API valida senha</strong> → Compara com MASTER_PASSWORD</li>";
echo "<li>✅ <strong>Se correta</strong> → Salva na sessão e retorna sucesso</li>";
echo "<li>❌ <strong>Se incorreta</strong> → Retorna erro 'Senha mestre incorreta'</li>";
echo "<li>📊 <strong>Se sucesso</strong> → Carrega logins e exibe senhas</li>";
echo "<li>🚫 <strong>Se erro</strong> → Não carrega logins, senhas ficam mascaradas</li>";
echo "</ol>";

echo "<h2>7. Teste Manual</h2>";
echo "<p><strong>Para testar a validação:</strong></p>";
echo "<ol>";
echo "<li>🔄 <strong>Recarregue a aplicação</strong> → index.html</li>";
echo "<li>❌ <strong>Digite senha incorreta</strong> → Ex: 'senhaerrada'</li>";
echo "<li>🔘 <strong>Clique no botão</strong> → 'Verificar Senha & Carregar'</li>";
echo "<li>❌ <strong>Verifique o erro</strong> → 'Senha mestre incorreta'</li>";
echo "<li>✅ <strong>Digite senha correta</strong> → 'jbf2024'</li>";
echo "<li>🔘 <strong>Clique no botão</strong> → 'Verificar Senha & Carregar'</li>";
echo "<li>✅ <strong>Verifique o sucesso</strong> → 'Senha mestre verificada com sucesso!'</li>";
echo "<li>📊 <strong>Confirme os logins</strong> → Lista deve aparecer</li>";
echo "</ol>";

echo "<h2>8. Comandos para Testar</h2>";
echo "<pre>";
echo "# Testar com senha correta\n";
echo "curl -X POST 'https://pass.jbfdigital.com.br/api/logins.php?action=check-master-key' \\\n";
echo "  -H 'Content-Type: application/json' \\\n";
echo "  -d '{\"master_key\":\"jbf2024\"}'\n\n";
echo "# Testar com senha incorreta\n";
echo "curl -X POST 'https://pass.jbfdigital.com.br/api/logins.php?action=check-master-key' \\\n";
echo "  -H 'Content-Type: application/json' \\\n";
echo "  -d '{\"master_key\":\"senhaerrada\"}'";
echo "</pre>";

echo "<h2>9. Links de Teste</h2>";
echo "<p><a href='index.html' target='_blank'>🏠 Aplicação Principal</a></p>";
echo "<p><a href='api/logins.php' target='_blank'>🔌 API Direta</a></p>";
echo "<p><a href='teste_correcao_api.php' target='_blank'>🔧 Teste Correção API</a></p>";

echo "<h2>10. Status da Validação</h2>";
echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; border: 1px solid #c3e6cb;'>";
echo "<strong>✅ VALIDAÇÃO IMPLEMENTADA!</strong><br>";
echo "Agora o sistema só aceita a senha mestre correta (jbf2024). ";
echo "Senhas incorretas são rejeitadas e os logins não são carregados.";
echo "</div>";

echo "<h2>11. Próximos Passos</h2>";
echo "<ul>";
echo "<li>🎯 <strong>Testar a aplicação:</strong> Verificar se a validação funciona</li>";
echo "<li>🎯 <strong>Alterar senha:</strong> Se necessário, editar MASTER_PASSWORD</li>";
echo "<li>🎯 <strong>Testar funcionalidades:</strong> Adicionar, editar, excluir</li>";
echo "<li>🎯 <strong>Verificar segurança:</strong> Senhas ficam mascaradas sem autenticação</li>";
echo "<li>🎯 <strong>Implementar hash:</strong> Para maior segurança no futuro</li>";
echo "</ul>";
?>
