<?php
/**
 * Utilitário para migrar dados do MySQL para JSON
 * Execute este arquivo uma vez para fazer a migração
 */

echo "<h1>🔄 Migração MySQL → JSON</h1>";
echo "<p>Este utilitário irá migrar todos os dados do MySQL para arquivos JSON.</p>";

// Verificar se deve executar a migração
if (!isset($_GET['executar']) || $_GET['executar'] !== 'sim') {
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>⚠️ ATENÇÃO</h3>";
    echo "<p>Esta operação irá:</p>";
    echo "<ul>";
    echo "<li>✅ Ler todos os dados do banco MySQL</li>";
    echo "<li>📁 Criar arquivos JSON na pasta 'dados/'</li>";
    echo "<li>🔄 Manter todos os dados existentes</li>";
    echo "<li>⚡ Preparar o sistema para usar JSON</li>";
    echo "</ul>";
    echo "<p><strong>Importante:</strong> Certifique-se de ter um backup do banco MySQL antes de prosseguir.</p>";
    echo "<a href='?executar=sim' style='background: #27ae60; color: white; padding: 15px 30px; text-decoration: none; border-radius: 10px; display: inline-block; margin-top: 15px;'>🚀 Executar Migração</a>";
    echo "</div>";
    exit();
}

try {
    // Carregar configuração do MySQL
    require_once '../config/banco_dados.php';
    
    echo "<h2>📊 Iniciando migração...</h2>";
    
    // Conectar ao MySQL
    $bd_mysql = new BancoDados();
    echo "<p>✅ Conectado ao MySQL</p>";
    
    // Carregar configuração JSON
    require_once '../config/banco_json.php';
    $bd_json = new BancoJSON();
    echo "<p>✅ Sistema JSON inicializado</p>";
    
    // Migrar Provas
    echo "<h3>📋 Migrando Provas...</h3>";
    $provas_mysql = $bd_mysql->obterProvas();
    
    // Limpar arquivo de provas para começar do zero
    file_put_contents(ARQUIVO_PROVAS, json_encode([], JSON_PRETTY_PRINT));
    
    foreach ($provas_mysql as $prova) {
        $bd_json->inserirProva($prova['nome'], $prova['descricao']);
        echo "<p>→ Migrada: " . htmlspecialchars($prova['nome']) . "</p>";
    }
    echo "<p><strong>✅ " . count($provas_mysql) . " provas migradas!</strong></p>";
    
    // Migrar Matérias
    echo "<h3>📚 Migrando Matérias...</h3>";
    $materias_mysql = $bd_mysql->obterMaterias();
    
    // Limpar arquivo de matérias
    file_put_contents(ARQUIVO_MATERIAS, json_encode([], JSON_PRETTY_PRINT));
    
    foreach ($materias_mysql as $materia) {
        $bd_json->inserirMateria($materia['nome'], $materia['cor']);
        echo "<p>→ Migrada: " . htmlspecialchars($materia['nome']) . " <span style='background: " . $materia['cor'] . "; color: white; padding: 2px 8px; border-radius: 15px;'>●</span></p>";
    }
    echo "<p><strong>✅ " . count($materias_mysql) . " matérias migradas!</strong></p>";
    
    // Criar mapeamento de IDs (MySQL → JSON)
    $provas_json = $bd_json->obterProvas();
    $materias_json = $bd_json->obterMaterias();
    
    // Mapear IDs das provas
    $map_provas = [];
    foreach ($provas_mysql as $i => $prova_mysql) {
        if (isset($provas_json[$i])) {
            $map_provas[$prova_mysql['id']] = $provas_json[$i]['id'];
        }
    }
    
    // Mapear IDs das matérias
    $map_materias = [];
    foreach ($materias_mysql as $i => $materia_mysql) {
        if (isset($materias_json[$i])) {
            $map_materias[$materia_mysql['id']] = $materias_json[$i]['id'];
        }
    }
    
    // Migrar Flashcards
    echo "<h3>🎯 Migrando Flashcards...</h3>";
    $flashcards_mysql = $bd_mysql->obterFlashcards();
    
    // Limpar arquivo de flashcards
    file_put_contents(ARQUIVO_FLASHCARDS, json_encode([], JSON_PRETTY_PRINT));
    
    $flashcards_migrados = 0;
    foreach ($flashcards_mysql as $flashcard) {
        // Mapear IDs
        $prova_id_json = isset($map_provas[$flashcard['prova_id']]) ? $map_provas[$flashcard['prova_id']] : null;
        $materia_id_json = isset($map_materias[$flashcard['materia_id']]) ? $map_materias[$flashcard['materia_id']] : null;
        
        $bd_json->inserirFlashcard(
            $flashcard['pergunta'],
            $flashcard['resposta'],
            $prova_id_json,
            $materia_id_json,
            $flashcard['dificuldade']
        );
        
        $flashcards_migrados++;
        
        if ($flashcards_migrados % 10 == 0) {
            echo "<p>→ Migrados: $flashcards_migrados flashcards...</p>";
        }
    }
    echo "<p><strong>✅ $flashcards_migrados flashcards migrados!</strong></p>";
    
    // Relatório final
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h2>🎉 Migração Concluída com Sucesso!</h2>";
    echo "<h3>📊 Resumo:</h3>";
    echo "<ul>";
    echo "<li>📋 Provas: " . count($provas_mysql) . " migradas</li>";
    echo "<li>📚 Matérias: " . count($materias_mysql) . " migradas</li>";
    echo "<li>🎯 Flashcards: $flashcards_migrados migrados</li>";
    echo "</ul>";
    
    echo "<h3>📁 Arquivos criados:</h3>";
    echo "<ul>";
    echo "<li><code>" . ARQUIVO_PROVAS . "</code></li>";
    echo "<li><code>" . ARQUIVO_MATERIAS . "</code></li>";
    echo "<li><code>" . ARQUIVO_FLASHCARDS . "</code></li>";
    echo "</ul>";
    
    echo "<h3>⚡ Próximos passos:</h3>";
    echo "<ol>";
    echo "<li>Renomeie <code>config/banco_dados.php</code> para <code>config/banco_dados_mysql.php</code> (backup)</li>";
    echo "<li>Renomeie <code>config/banco_json.php</code> para <code>config/banco_dados.php</code></li>";
    echo "<li>Teste o sistema acessando a página principal</li>";
    echo "<li>Se tudo funcionar, você pode desabilitar o MySQL</li>";
    echo "</ol>";
    
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 8px; margin: 15px 0;'>";
    echo "<strong>⚠️ Importante:</strong> Mantenha o backup do MySQL até ter certeza de que tudo está funcionando corretamente!";
    echo "</div>";
    
    echo "</div>";
    
    // Mostrar conteúdo dos arquivos (primeiros registros)
    echo "<h3>👀 Prévia dos Arquivos JSON:</h3>";
    
    echo "<h4>📋 Provas (primeiros 3 registros):</h4>";
    echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 8px; overflow-x: auto;'>";
    $provas_preview = array_slice($provas_json, 0, 3);
    echo htmlspecialchars(json_encode($provas_preview, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "</pre>";
    
    echo "<h4>📚 Matérias (primeiros 3 registros):</h4>";
    echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 8px; overflow-x: auto;'>";
    $materias_preview = array_slice($materias_json, 0, 3);
    echo htmlspecialchars(json_encode($materias_preview, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "</pre>";
    
    echo "<h4>🎯 Flashcards (primeiros 2 registros):</h4>";
    echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 8px; overflow-x: auto;'>";
    $flashcards_json = $bd_json->obterFlashcards();
    $flashcards_preview = array_slice($flashcards_json, 0, 2);
    echo htmlspecialchars(json_encode($flashcards_preview, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f1aeb5; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h2>❌ Erro na Migração</h2>";
    echo "<p><strong>Erro:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Verifique:</p>";
    echo "<ul>";
    echo "<li>Se o MySQL está funcionando</li>";
    echo "<li>Se as configurações em config/banco_dados.php estão corretas</li>";
    echo "<li>Se o PHP tem permissão para criar arquivos na pasta 'dados/'</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<hr>";
echo "<p style='text-align: center; color: #7f8c8d; margin-top: 30px;'>";
echo "🛠️ Utilitário de Migração | Sistema de Flashcards<br>";
echo "Desenvolvido para facilitar a transição do MySQL para JSON";
echo "</p>";
?>