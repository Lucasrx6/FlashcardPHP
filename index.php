<?php
require_once 'config/banco_dados.php';

$bd = new BancoDados();

// Processar filtros
$prova_selecionada = isset($_GET['prova']) ? $_GET['prova'] : null;
$materia_selecionada = isset($_GET['materia']) ? $_GET['materia'] : null;

// Obter dados
$provas = $bd->obterProvas();
$materias = $bd->obterMaterias();
$flashcards = $bd->obterFlashcards($prova_selecionada, $materia_selecionada);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Flashcards - Estudos</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📚Flashcards</h1>
            <p class="subtitulo">Organize seus estudos de forma inteligente</p>
            
            <nav class="navegacao">
                <a href="index.php" class="btn">🏠 Início</a>
                <a href="adicionar_flashcard.php" class="btn btn-success">➕ Adicionar Flashcard</a>
                <a href="gerenciar_provas.php" class="btn btn-warning">📋 Gerenciar Provas</a>
                <a href="gerenciar_materias.php" class="btn btn-warning">📚 Gerenciar Matérias</a>
            </nav>
        </header>

        <section class="secao">
            <h2>🔍 Filtrar</h2>
            <form method="GET" class="filtros">
                <div class="formulario-grupo">
                    <label for="prova">Selecionar Prova:</label>
                    <select name="prova" id="prova" onchange="this.form.submit()">
                        <option value="">Todas as Provas</option>
                        <?php foreach ($provas as $prova): ?>
                            <option value="<?php echo $prova['id']; ?>" 
                                    <?php echo $prova_selecionada == $prova['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($prova['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="formulario-grupo">
                    <label for="materia">Selecionar Matéria:</label>
                    <select name="materia" id="materia" onchange="this.form.submit()">
                        <option value="">Todas as Matérias</option>
                        <?php foreach ($materias as $materia): ?>
                            <option value="<?php echo $materia['id']; ?>" 
                                    <?php echo $materia_selecionada == $materia['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($materia['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </section>

        <?php if (!empty($flashcards)): ?>
            <div class="contador">
                📊 Total de flashcards encontrados: <?php echo count($flashcards); ?>
            </div>
            
            <section class="secao">
                <div class="instrucoes">
                    <strong>💡 Como usar:</strong> Clique em qualquer flashcard para ver a resposta. Clique novamente para voltar à pergunta.
                </div>
                
                <div class="lista-flashcards" id="listaFlashcards">
                    <?php foreach ($flashcards as $flashcard): ?>
                        <div class="flashcard" onclick="virarFlashcard(this)">
                            <div class="pergunta">
                                <strong>❓ Pergunta:</strong><br>
                                <?php echo nl2br(htmlspecialchars($flashcard['pergunta'])); ?>
                            </div>
                            
                            <div class="resposta">
                                <strong>✅ Resposta:</strong><br>
                                <?php echo nl2br(htmlspecialchars($flashcard['resposta'])); ?>
                            </div>
                            
                            <div class="info-flashcard">
                                <div>
                                    <?php if ($flashcard['materia_nome']): ?>
                                        <span class="etiqueta-materia" 
                                              style="background-color: <?php echo $flashcard['materia_cor']; ?>">
                                            <?php echo htmlspecialchars($flashcard['materia_nome']); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if ($flashcard['prova_nome']): ?>
                                        <span class="etiqueta-prova">
                                            <?php echo htmlspecialchars($flashcard['prova_nome']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="dificuldade <?php echo $flashcard['dificuldade']; ?>">
                                        <?php 
                                        $icones_dificuldade = [
                                            'facil' => '🟢 Fácil',
                                            'medio' => '🟡 Médio', 
                                            'dificil' => '🔴 Difícil'
                                        ];
                                        echo $icones_dificuldade[$flashcard['dificuldade']];
                                        ?>
                                    </div>
                                    
                                    <div class="acoes-flashcard">
                                        <button onclick="editarFlashcard(<?php echo $flashcard['id']; ?>)" 
                                                class="btn-acao" title="Editar flashcard">
                                            ✏️
                                        </button>
                                        <button onclick="excluirFlashcard(<?php echo $flashcard['id']; ?>)" 
                                                class="btn-acao btn-excluir" title="Excluir flashcard">
                                            🗑️
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            
            <section class="secao">
                <h3>🎯 Controles de Estudo</h3>
                <div class="navegacao">
                    <button onclick="embaralharFlashcards()" class="btn">🔀 Embaralhar</button>
                    <button onclick="mostrarTodasPerguntas()" class="btn">❓ Mostrar Perguntas</button>
                    <button onclick="mostrarTodasRespostas()" class="btn btn-success">✅ Mostrar Respostas</button>
                </div>
            </section>
            
        <?php else: ?>
            <section class="secao">
                <div style="text-align: center; padding: 50px;">
                    <h3>😔 Nenhum flashcard encontrado</h3>
                    <p>Não há flashcards para os filtros selecionados.</p>
                    <a href="adicionar_flashcard.php" class="btn btn-success" style="margin-top: 20px;">
                        ➕ Adicionar Primeiro Flashcard
                    </a>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <script src="js/flashcards.js"></script>
</body>
</html>