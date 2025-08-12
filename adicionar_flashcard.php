<?php
require_once 'config/banco_dados.php';

$bd = new BancoDados();
$mensagem = '';

// Processar formulário
if ($_POST) {
    $pergunta = trim($_POST['pergunta']);
    $resposta = trim($_POST['resposta']);
    $prova_id = !empty($_POST['prova_id']) ? $_POST['prova_id'] : null;
    $materia_id = !empty($_POST['materia_id']) ? $_POST['materia_id'] : null;
    $dificuldade = $_POST['dificuldade'];
    
    if (empty($pergunta) || empty($resposta)) {
        $mensagem = '<div class="mensagem-erro">❌ Pergunta e resposta são obrigatórias!</div>';
    } else {
        if ($bd->inserirFlashcard($pergunta, $resposta, $prova_id, $materia_id, $dificuldade)) {
            $mensagem = '<div class="mensagem-sucesso">✅ Flashcard adicionado com sucesso!</div>';
            // Limpar campos após sucesso
            $_POST = array();
        } else {
            $mensagem = '<div class="mensagem-erro">❌ Erro ao adicionar flashcard!</div>';
        }
    }
}

// Obter dados para os selects
$provas = $bd->obterProvas();
$materias = $bd->obterMaterias();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Flashcard - Sistema de Estudos</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .mensagem-erro {
            background: linear-gradient(45deg, #f8d7da, #f1aeb5);
            border: 1px solid #f1aeb5;
            color: #721c24;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .preview-flashcard {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-top: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            border: 2px dashed #3498db;
        }
        
        .campos-obrigatorios {
            background: linear-gradient(45deg, #fff3cd, #ffeaa7);
            border-left: 4px solid #f39c12;
            padding: 15px;
            border-radius: 0 10px 10px 0;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>➕ Adicionar Novo Flashcard</h1>
            <p class="subtitulo">Crie perguntas e respostas para seus estudos</p>
            
            <nav class="navegacao">
                <a href="index.php" class="btn">🏠 Voltar ao Início</a>
                <a href="gerenciar_provas.php" class="btn btn-warning">📋 Gerenciar Provas</a>
                <a href="gerenciar_materias.php" class="btn btn-warning">📚 Gerenciar Matérias</a>
            </nav>
        </header>

        <?php echo $mensagem; ?>

        <section class="secao">
            <div class="campos-obrigatorios">
                <strong>⚠️ Campos obrigatórios:</strong> Pergunta e Resposta devem ser preenchidos.
            </div>
            
            <form method="POST" id="formularioFlashcard">
                <div class="formulario-grupo">
                    <label for="pergunta">* Pergunta:</label>
                    <textarea name="pergunta" id="pergunta" required placeholder="Digite a pergunta do flashcard..."><?php echo isset($_POST['pergunta']) ? htmlspecialchars($_POST['pergunta']) : ''; ?></textarea>
                </div>
                
                <div class="formulario-grupo">
                    <label for="resposta">* Resposta:</label>
                    <textarea name="resposta" id="resposta" required placeholder="Digite a resposta do flashcard..."><?php echo isset($_POST['resposta']) ? htmlspecialchars($_POST['resposta']) : ''; ?></textarea>
                </div>
                
                <div class="filtros">
                    <div class="formulario-grupo">
                        <label for="prova_id">Prova:</label>
                        <select name="prova_id" id="prova_id">
                            <option value="">Selecione uma prova (opcional)</option>
                            <?php foreach ($provas as $prova): ?>
                                <option value="<?php echo $prova['id']; ?>" 
                                        <?php echo (isset($_POST['prova_id']) && $_POST['prova_id'] == $prova['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($prova['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="formulario-grupo">
                        <label for="materia_id">Matéria:</label>
                        <select name="materia_id" id="materia_id">
                            <option value="">Selecione uma matéria (opcional)</option>
                            <?php foreach ($materias as $materia): ?>
                                <option value="<?php echo $materia['id']; ?>" 
                                        <?php echo (isset($_POST['materia_id']) && $_POST['materia_id'] == $materia['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($materia['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="formulario-grupo">
                        <label for="dificuldade">Dificuldade:</label>
                        <select name="dificuldade" id="dificuldade">
                            <option value="facil" <?php echo (isset($_POST['dificuldade']) && $_POST['dificuldade'] == 'facil') ? 'selected' : ''; ?>>🟢 Fácil</option>
                            <option value="medio" <?php echo (!isset($_POST['dificuldade']) || $_POST['dificuldade'] == 'medio') ? 'selected' : ''; ?>>🟡 Médio</option>
                            <option value="dificil" <?php echo (isset($_POST['dificuldade']) && $_POST['dificuldade'] == 'dificil') ? 'selected' : ''; ?>>🔴 Difícil</option>
                        </select>
                    </div>
                </div>
                
                <div class="navegacao">
                    <button type="submit" class="btn btn-success">💾 Salvar Flashcard</button>
                    <button type="button" onclick="limparFormulario()" class="btn">🗑️ Limpar</button>
                    <button type="button" onclick="mostrarPreview()" class="btn btn-warning">👁️ Pré-visualizar</button>
                </div>
            </form>
        </section>
        
        <section class="secao" id="secaoPreview" style="display: none;">
            <h3>👁️ Pré-visualização do Flashcard</h3>
            <div class="preview-flashcard" id="previewFlashcard" onclick="virarFlashcard(this)">
                <div class="pergunta">
                    <strong>❓ Pergunta:</strong><br>
                    <span id="previewPergunta">Sua pergunta aparecerá aqui...</span>
                </div>
                
                <div class="resposta">
                    <strong>✅ Resposta:</strong><br>
                    <span id="previewResposta">Sua resposta aparecerá aqui...</span>
                </div>
                
                <div class="info-flashcard">
                    <div>
                        <span class="etiqueta-materia" id="previewMateria" style="display: none;">
                            Matéria
                        </span>
                        <span class="etiqueta-prova" id="previewProva" style="display: none;">
                            Prova
                        </span>
                    </div>
                    
                    <div class="dificuldade" id="previewDificuldade">
                        🟡 Médio
                    </div>
                </div>
            </div>
            <p style="text-align: center; margin-top: 15px; color: #7f8c8d;">
                💡 Clique no flashcard acima para ver como ficará a alternância entre pergunta e resposta
            </p>
        </section>
        
        <section class="secao">
            <h3>📝 Dicas para Criar Bons Flashcards</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div style="background: linear-gradient(45deg, #e8f5e8, #d4edda); padding: 20px; border-radius: 10px;">
                    <h4>✅ Faça</h4>
                    <ul>
                        <li>Use perguntas claras e objetivas</li>
                        <li>Mantenha as respostas concisas</li>
                        <li>Inclua exemplos quando necessário</li>
                        <li>Use suas próprias palavras</li>
                    </ul>
                </div>
                
                <div style="background: linear-gradient(45deg, #fff3e0, #ffe0b2); padding: 20px; border-radius: 10px;">
                    <h4>⚠️ Evite</h4>
                    <ul>
                        <li>Perguntas muito longas ou complexas</li>
                        <li>Múltiplas perguntas em um flashcard</li>
                        <li>Respostas muito extensas</li>
                        <li>Linguagem muito técnica desnecessária</li>
                    </ul>
                </div>
            </div>
        </section>
    </div>

    <script src="js/flashcards.js"></script>
    <script>
        function limparFormulario() {
            if (confirm('Tem certeza que deseja limpar todos os campos?')) {
                document.getElementById('formularioFlashcard').reset();
                document.getElementById('secaoPreview').style.display = 'none';
            }
        }
        
        function mostrarPreview() {
            const pergunta = document.getElementById('pergunta').value.trim();
            const resposta = document.getElementById('resposta').value.trim();
            
            if (!pergunta || !resposta) {
                alert('Por favor, preencha a pergunta e a resposta antes de visualizar.');
                return;
            }
            
            // Atualizar preview
            document.getElementById('previewPergunta').innerHTML = pergunta.replace(/\n/g, '<br>');
            document.getElementById('previewResposta').innerHTML = resposta.replace(/\n/g, '<br>');
            
            // Atualizar matéria
            const materiaSelect = document.getElementById('materia_id');
            const materiaPreview = document.getElementById('previewMateria');
            if (materiaSelect.value) {
                const materiaCor = getMateriaColor(materiaSelect.value);
                materiaPreview.textContent = materiaSelect.options[materiaSelect.selectedIndex].text;
                materiaPreview.style.backgroundColor = materiaCor;
                materiaPreview.style.display = 'inline-block';
            } else {
                materiaPreview.style.display = 'none';
            }
            
            // Atualizar prova
            const provaSelect = document.getElementById('prova_id');
            const provaPreview = document.getElementById('previewProva');
            if (provaSelect.value) {
                provaPreview.textContent = provaSelect.options[provaSelect.selectedIndex].text;
                provaPreview.style.display = 'inline-block';
            } else {
                provaPreview.style.display = 'none';
            }
            
            // Atualizar dificuldade
            const dificuldade = document.getElementById('dificuldade').value;
            const dificuldadeTextos = {
                'facil': '🟢 Fácil',
                'medio': '🟡 Médio',
                'dificil': '🔴 Difícil'
            };
            document.getElementById('previewDificuldade').textContent = dificuldadeTextos[dificuldade];
            document.getElementById('previewDificuldade').className = 'dificuldade ' + dificuldade;
            
            // Mostrar seção de preview
            document.getElementById('secaoPreview').style.display = 'block';
            document.getElementById('secaoPreview').scrollIntoView({ behavior: 'smooth' });
        }
        
        function getMateriaColor(materiaId) {
            // Cores padrão para as matérias (você pode ajustar conforme necessário)
            const cores = {
                '1': '#e74c3c', // Português
                '2': '#3498db', // Matemática
                '3': '#f39c12', // História
                '4': '#2ecc71', // Geografia
                '5': '#9b59b6', // Ciências
                '6': '#1abc9c'  // Inglês
            };
            return cores[materiaId] || '#95a5a6';
        }
        
        // Auto-preview enquanto digita (opcional)
        let timeoutId;
        function autoPreview() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                const secaoPreview = document.getElementById('secaoPreview');
                if (secaoPreview.style.display === 'block') {
                    mostrarPreview();
                }
            }, 500);
        }
        
        document.getElementById('pergunta').addEventListener('input', autoPreview);
        document.getElementById('resposta').addEventListener('input', autoPreview);
        document.getElementById('materia_id').addEventListener('change', autoPreview);
        document.getElementById('prova_id').addEventListener('change', autoPreview);
        document.getElementById('dificuldade').addEventListener('change', autoPreview);
    </script>
</body>
</html>