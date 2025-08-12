<?php
require_once 'config/banco_dados.php';

$bd = new BancoDados();
$mensagem = '';
$flashcard = null;

// Verificar se foi passado um ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$flashcard_id = $_GET['id'];

// Processar formulário de edição
if ($_POST) {
    $pergunta = trim($_POST['pergunta']);
    $resposta = trim($_POST['resposta']);
    $prova_id = !empty($_POST['prova_id']) ? $_POST['prova_id'] : null;
    $materia_id = !empty($_POST['materia_id']) ? $_POST['materia_id'] : null;
    $dificuldade = $_POST['dificuldade'];
    
    if (empty($pergunta) || empty($resposta)) {
        $mensagem = '<div class="mensagem-erro">❌ Pergunta e resposta são obrigatórias!</div>';
    } else {
        if ($bd->atualizarFlashcard($flashcard_id, $pergunta, $resposta, $prova_id, $materia_id, $dificuldade)) {
            $mensagem = '<div class="mensagem-sucesso">✅ Flashcard atualizado com sucesso!</div>';
        } else {
            $mensagem = '<div class="mensagem-erro">❌ Erro ao atualizar flashcard!</div>';
        }
    }
}

// Buscar dados do flashcard
$flashcard = $bd->obterFlashcard($flashcard_id);

if (!$flashcard) {
    header('Location: index.php');
    exit();
}

// Adicionar nomes da prova e matéria para exibição
$prova = $flashcard['prova_id'] ? $bd->obterProvaPorId($flashcard['prova_id']) : null;
$materia = $flashcard['materia_id'] ? $bd->obterMateriaPorId($flashcard['materia_id']) : null;

$flashcard['prova_nome'] = $prova ? $prova['nome'] : null;
$flashcard['materia_nome'] = $materia ? $materia['nome'] : null;
$flashcard['materia_cor'] = $materia ? $materia['cor'] : null;

// Obter dados para os selects
$provas = $bd->obterProvas();
$materias = $bd->obterMaterias();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Flashcard - Sistema de Estudos</title>
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
        
        .flashcard-atual {
            background: linear-gradient(45deg, #e8f5e8, #d4edda);
            border: 2px solid #27ae60;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .comparacao {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }
        
        .card-comparacao {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .card-comparacao h4 {
            margin-bottom: 15px;
            color: #2c3e50;
        }
        
        @media (max-width: 768px) {
            .comparacao {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>✏️ Editar Flashcard</h1>
            <p class="subtitulo">Modifique as informações do seu flashcard</p>
            
            <nav class="navegacao">
                <a href="index.php" class="btn">🏠 Voltar ao Início</a>
                <a href="adicionar_flashcard.php" class="btn btn-success">➕ Adicionar Novo</a>
                <button onclick="excluirEsteFlashcard()" class="btn" style="background: #e74c3c;">🗑️ Excluir Este Flashcard</button>
            </nav>
        </header>

        <?php echo $mensagem; ?>

        <!-- Flashcard Atual -->
        <section class="secao">
            <h2>📄 Flashcard Atual</h2>
            <div class="flashcard-atual">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                    <h3>ID: <?php echo $flashcard['id']; ?></h3>
                    <div>
                        <?php if ($flashcard['materia_nome']): ?>
                            <span class="etiqueta-materia" style="background-color: #27ae60;">
                                <?php echo htmlspecialchars($flashcard['materia_nome']); ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($flashcard['prova_nome']): ?>
                            <span class="etiqueta-prova">
                                <?php echo htmlspecialchars($flashcard['prova_nome']); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong>❓ Pergunta:</strong><br>
                    <?php echo nl2br(htmlspecialchars($flashcard['pergunta'])); ?>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong>✅ Resposta:</strong><br>
                    <?php echo nl2br(htmlspecialchars($flashcard['resposta'])); ?>
                </div>
                
                <div style="text-align: right; color: #7f8c8d; font-size: 0.9rem;">
                    Criado em: <?php echo date('d/m/Y H:i', strtotime($flashcard['data_criacao'])); ?>
                </div>
            </div>
        </section>

        <!-- Formulário de Edição -->
        <section class="secao">
            <h2>✏️ Editar Informações</h2>
            <form method="POST" id="formularioEdicao">
                <div class="formulario-grupo">
                    <label for="pergunta">* Pergunta:</label>
                    <textarea name="pergunta" id="pergunta" required placeholder="Digite a pergunta do flashcard..."><?php echo htmlspecialchars($flashcard['pergunta']); ?></textarea>
                </div>
                
                <div class="formulario-grupo">
                    <label for="resposta">* Resposta:</label>
                    <textarea name="resposta" id="resposta" required placeholder="Digite a resposta do flashcard..."><?php echo htmlspecialchars($flashcard['resposta']); ?></textarea>
                </div>
                
                <div class="filtros">
                    <div class="formulario-grupo">
                        <label for="prova_id">Prova:</label>
                        <select name="prova_id" id="prova_id">
                            <option value="">Selecione uma prova (opcional)</option>
                            <?php foreach ($provas as $prova): ?>
                                <option value="<?php echo $prova['id']; ?>" 
                                        <?php echo ($flashcard['prova_id'] == $prova['id']) ? 'selected' : ''; ?>>
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
                                        <?php echo ($flashcard['materia_id'] == $materia['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($materia['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="formulario-grupo">
                        <label for="dificuldade">Dificuldade:</label>
                        <select name="dificuldade" id="dificuldade">
                            <option value="facil" <?php echo ($flashcard['dificuldade'] == 'facil') ? 'selected' : ''; ?>>🟢 Fácil</option>
                            <option value="medio" <?php echo ($flashcard['dificuldade'] == 'medio') ? 'selected' : ''; ?>>🟡 Médio</option>
                            <option value="dificil" <?php echo ($flashcard['dificuldade'] == 'dificil') ? 'selected' : ''; ?>>🔴 Difícil</option>
                        </select>
                    </div>
                </div>
                
                <div class="navegacao">
                    <button type="submit" class="btn btn-success">💾 Salvar Alterações</button>
                    <button type="button" onclick="mostrarComparacao()" class="btn btn-warning">👁️ Pré-visualizar</button>
                    <button type="button" onclick="resetarFormulario()" class="btn">🔄 Restaurar Original</button>
                </div>
            </form>
        </section>
        
        <!-- Comparação Antes/Depois -->
        <section class="secao" id="secaoComparacao" style="display: none;">
            <h3>🔄 Comparação: Antes vs Depois</h3>
            <div class="comparacao">
                <div class="card-comparacao">
                    <h4>📄 Versão Atual (Banco de Dados)</h4>
                    <div style="margin-bottom: 10px;">
                        <strong>Pergunta:</strong><br>
                        <span id="perguntaOriginal"><?php echo nl2br(htmlspecialchars($flashcard['pergunta'])); ?></span>
                    </div>
                    <div>
                        <strong>Resposta:</strong><br>
                        <span id="respostaOriginal"><?php echo nl2br(htmlspecialchars($flashcard['resposta'])); ?></span>
                    </div>
                </div>
                
                <div class="card-comparacao">
                    <h4>✏️ Nova Versão (Suas Alterações)</h4>
                    <div style="margin-bottom: 10px;">
                        <strong>Pergunta:</strong><br>
                        <span id="perguntaNova"></span>
                    </div>
                    <div>
                        <strong>Resposta:</strong><br>
                        <span id="respostaNova"></span>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        const dadosOriginais = {
            pergunta: <?php echo json_encode($flashcard['pergunta']); ?>,
            resposta: <?php echo json_encode($flashcard['resposta']); ?>,
            prova_id: <?php echo json_encode($flashcard['prova_id']); ?>,
            materia_id: <?php echo json_encode($flashcard['materia_id']); ?>,
            dificuldade: <?php echo json_encode($flashcard['dificuldade']); ?>
        };
        
        function resetarFormulario() {
            if (confirm('Tem certeza que deseja restaurar os valores originais?')) {
                document.getElementById('pergunta').value = dadosOriginais.pergunta;
                document.getElementById('resposta').value = dadosOriginais.resposta;
                document.getElementById('prova_id').value = dadosOriginais.prova_id || '';
                document.getElementById('materia_id').value = dadosOriginais.materia_id || '';
                document.getElementById('dificuldade').value = dadosOriginais.dificuldade;
                
                document.getElementById('secaoComparacao').style.display = 'none';
            }
        }
        
        function mostrarComparacao() {
            const perguntaNova = document.getElementById('pergunta').value.trim();
            const respostaNova = document.getElementById('resposta').value.trim();
            
            if (!perguntaNova || !respostaNova) {
                alert('Por favor, preencha a pergunta e a resposta antes de visualizar.');
                return;
            }
            
            // Atualizar comparação
            document.getElementById('perguntaNova').innerHTML = perguntaNova.replace(/\n/g, '<br>');
            document.getElementById('respostaNova').innerHTML = respostaNova.replace(/\n/g, '<br>');
            
            // Mostrar seção
            document.getElementById('secaoComparacao').style.display = 'block';
            document.getElementById('secaoComparacao').scrollIntoView({ behavior: 'smooth' });
        }
        
        function excluirEsteFlashcard() {
            if (confirm('⚠️ ATENÇÃO!\n\nTem certeza que deseja EXCLUIR este flashcard?\n\nEsta ação não pode ser desfeita!')) {
                if (confirm('Confirma novamente a exclusão? O flashcard será removido permanentemente.')) {
                    // Criar formulário para exclusão
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'excluir_flashcard.php';
                    
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'id';
                    input.value = <?php echo $flashcard_id; ?>;
                    
                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        }
        
        // Verificar mudanças no formulário
        function verificarMudancas() {
            const perguntaAtual = document.getElementById('pergunta').value;
            const respostaAtual = document.getElementById('resposta').value;
            const provaAtual = document.getElementById('prova_id').value;
            const materiaAtual = document.getElementById('materia_id').value;
            const dificuldadeAtual = document.getElementById('dificuldade').value;
            
            const houveMudanca = 
                perguntaAtual !== dadosOriginais.pergunta ||
                respostaAtual !== dadosOriginais.resposta ||
                provaAtual !== (dadosOriginais.prova_id || '') ||
                materiaAtual !== (dadosOriginais.materia_id || '') ||
                dificuldadeAtual !== dadosOriginais.dificuldade;
            
            return houveMudanca;
        }
        
        // Avisar sobre mudanças não salvas
        window.addEventListener('beforeunload', function (e) {
            if (verificarMudancas()) {
                e.preventDefault();
                e.returnValue = '';
                return 'Você tem alterações não salvas. Tem certeza que deseja sair?';
            }
        });
        
        // Atalhos de teclado
        document.addEventListener('keydown', function(e) {
            // Ctrl + S para salvar
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                document.getElementById('formularioEdicao').submit();
            }
            
            // Ctrl + P para pré-visualizar
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                mostrarComparacao();
            }
        });
    </script>
</body>
</html>