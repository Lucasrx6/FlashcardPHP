<?php
require_once 'config/banco_dados.php';

$bd = new BancoDados();
$mensagem = '';

// Processar ações
if ($_POST) {
    if (isset($_POST['acao'])) {
        switch ($_POST['acao']) {
            case 'adicionar':
                $nome = trim($_POST['nome']);
                $cor = $_POST['cor'];
                
                if (empty($nome)) {
                    $mensagem = '<div class="mensagem-erro">❌ Nome da matéria é obrigatório!</div>';
                } else {
                    if ($bd->inserirMateria($nome, $cor)) {
                        $mensagem = '<div class="mensagem-sucesso">✅ Matéria adicionada com sucesso!</div>';
                    } else {
                        $mensagem = '<div class="mensagem-erro">❌ Erro ao adicionar matéria!</div>';
                    }
                }
                break;
                
            case 'editar':
                $id = $_POST['id'];
                $nome = trim($_POST['nome']);
                $cor = $_POST['cor'];
                
                if (empty($nome)) {
                    $mensagem = '<div class="mensagem-erro">❌ Nome da matéria é obrigatório!</div>';
                } else {
                    if ($bd->atualizarMateria($id, $nome, $cor)) {
                        $mensagem = '<div class="mensagem-sucesso">✅ Matéria atualizada com sucesso!</div>';
                    } else {
                        $mensagem = '<div class="mensagem-erro">❌ Erro ao atualizar matéria!</div>';
                    }
                }
                break;
                
            case 'excluir':
                $id = $_POST['id'];
                
                if ($bd->excluirMateria($id)) {
                    $mensagem = '<div class="mensagem-sucesso">✅ Matéria excluída com sucesso!</div>';
                } else {
                    $mensagem = '<div class="mensagem-erro">❌ Não é possível excluir esta matéria pois existem flashcards associados!</div>';
                }
                break;
        }
    }
}

// Obter todas as matérias com contagem de flashcards
$materias = $bd->obterEstatisticasMaterias();

// Cores pré-definidas para facilitar a escolha
$cores_predefinidas = [
    '#e74c3c' => 'Vermelho',
    '#3498db' => 'Azul',
    '#f39c12' => 'Laranja', 
    '#2ecc71' => 'Verde',
    '#9b59b6' => 'Roxo',
    '#1abc9c' => 'Turquesa',
    '#34495e' => 'Cinza Escuro',
    '#e67e22' => 'Laranja Escuro',
    '#27ae60' => 'Verde Escuro',
    '#8e44ad' => 'Roxo Escuro'
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Matérias - Sistema de Flashcards</title>
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
        
        .tabela-materias {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .tabela-materias th {
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .tabela-materias td {
            padding: 15px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .tabela-materias tr:hover {
            background-color: #f8f9fa;
        }
        
        .amostra-cor {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-block;
            border: 3px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            vertical-align: middle;
            margin-right: 10px;
        }
        
        .cores-predefinidas {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        
        .opcao-cor {
            padding: 10px;
            border: 2px solid #ecf0f1;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }
        
        .opcao-cor:hover {
            border-color: #3498db;
            transform: translateY(-2px);
        }
        
        .opcao-cor.selecionada {
            border-color: #27ae60;
            background: #d5f4e6;
        }
        
        .acoes {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn-pequeno {
            padding: 8px 15px;
            font-size: 0.9rem;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            backdrop-filter: blur(5px);
        }
        
        .modal-conteudo {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .estatisticas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card-estatistica {
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .card-estatistica h3 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .grid-materias {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .card-materia {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border-left: 5px solid;
        }
        
        .card-materia:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📚 Gerenciar Matérias</h1>
            <p class="subtitulo">Organize suas matérias de estudo</p>
            
            <nav class="navegacao">
                <a href="index.php" class="btn">🏠 Voltar ao Início</a>
                <a href="adicionar_flashcard.php" class="btn btn-success">➕ Adicionar Flashcard</a>
                <a href="gerenciar_provas.php" class="btn btn-warning">📋 Gerenciar Provas</a>
            </nav>
        </header>

        <?php echo $mensagem; ?>

        <!-- Estatísticas -->
        <section class="secao">
            <h2>📊 Estatísticas</h2>
            <div class="estatisticas">
                <div class="card-estatistica">
                    <h3><?php echo count($materias); ?></h3>
                    <p>Matérias Cadastradas</p>
                </div>
                <div class="card-estatistica">
                    <h3><?php echo array_sum(array_column($materias, 'total_flashcards')); ?></h3>
                    <p>Total de Flashcards</p>
                </div>
                <div class="card-estatistica">
                    <h3><?php echo count(array_filter($materias, function($m) { return $m['total_flashcards'] > 0; })); ?></h3>
                    <p>Matérias com Flashcards</p>
                </div>
            </div>
        </section>

        <!-- Formulário para adicionar nova matéria -->
        <section class="secao">
            <h2>➕ Adicionar Nova Matéria</h2>
            <form method="POST" id="formAdicionar">
                <input type="hidden" name="acao" value="adicionar">
                <input type="hidden" name="cor" id="corSelecionada" value="#3498db">
                
                <div class="formulario-grupo">
                    <label for="nome">* Nome da Matéria:</label>
                    <input type="text" name="nome" id="nome" required placeholder="Ex: Português, Matemática, História...">
                </div>
                
                <div class="formulario-grupo">
                    <label>Escolher Cor:</label>
                    <div class="cores-predefinidas">
                        <?php foreach ($cores_predefinidas as $cor => $nome_cor): ?>
                            <div class="opcao-cor <?php echo $cor === '#3498db' ? 'selecionada' : ''; ?>" 
                                 onclick="selecionarCor('<?php echo $cor; ?>', this)">
                                <div class="amostra-cor" style="background-color: <?php echo $cor; ?>"></div>
                                <div style="font-size: 0.8rem; margin-top: 5px;"><?php echo $nome_cor; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div style="margin-top: 15px;">
                        <label for="corPersonalizada">Ou escolha uma cor personalizada:</label>
                        <input type="color" id="corPersonalizada" value="#3498db" 
                               onchange="selecionarCorPersonalizada(this.value)">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success">💾 Adicionar Matéria</button>
            </form>
        </section>

        <!-- Lista de matérias existentes -->
        <section class="secao">
            <h2>📚 Matérias Cadastradas</h2>
            
            <?php if (empty($materias)): ?>
                <div style="text-align: center; padding: 50px;">
                    <h3>😔 Nenhuma matéria cadastrada</h3>
                    <p>Adicione sua primeira matéria usando o formulário acima.</p>
                </div>
            <?php else: ?>
                <div class="grid-materias">
                    <?php foreach ($materias as $materia): ?>
                        <div class="card-materia" style="border-left-color: <?php echo $materia['cor']; ?>">
                            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                <div class="amostra-cor" style="background-color: <?php echo $materia['cor']; ?>"></div>
                                <h3 style="margin: 0; color: #2c3e50;"><?php echo htmlspecialchars($materia['nome']); ?></h3>
                            </div>
                            
                            <div style="margin-bottom: 15px;">
                                <span class="badge" style="background: <?php echo $materia['total_flashcards'] > 0 ? '#27ae60' : '#95a5a6'; ?>; color: white; padding: 5px 10px; border-radius: 20px; font-size: 0.9rem;">
                                    <?php echo $materia['total_flashcards']; ?> flashcards
                                </span>
                            </div>
                            
                            <div style="font-size: 0.9rem; color: #7f8c8d; margin-bottom: 15px;">
                                Criada em: <?php echo date('d/m/Y', strtotime($materia['data_criacao'])); ?>
                            </div>
                            
                            <div class="acoes">
                                <button onclick="editarMateria(<?php echo $materia['id']; ?>, '<?php echo addslashes($materia['nome']); ?>', '<?php echo $materia['cor']; ?>')" 
                                        class="btn btn-pequeno">
                                    ✏️ Editar
                                </button>
                                
                                <?php if ($materia['total_flashcards'] == 0): ?>
                                    <button onclick="excluirMateria(<?php echo $materia['id']; ?>, '<?php echo addslashes($materia['nome']); ?>')" 
                                            class="btn btn-pequeno" style="background: #e74c3c;">
                                        🗑️ Excluir
                                    </button>
                                <?php else: ?>
                                    <button onclick="alert('Não é possível excluir esta matéria pois possui <?php echo $materia['total_flashcards']; ?> flashcards associados.')" 
                                            class="btn btn-pequeno" style="background: #95a5a6; cursor: not-allowed;">
                                        🔒 Protegida
                                    </button>
                                <?php endif; ?>
                                
                                <a href="index.php?materia=<?php echo $materia['id']; ?>" 
                                   class="btn btn-pequeno btn-warning">
                                    👁️ Ver Flashcards
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <!-- Modal para edição -->
    <div id="modalEdicao" class="modal">
        <div class="modal-conteudo">
            <h3>✏️ Editar Matéria</h3>
            <form method="POST" id="formEdicao">
                <input type="hidden" name="acao" value="editar">
                <input type="hidden" name="id" id="editId">
                <input type="hidden" name="cor" id="editCorSelecionada">
                
                <div class="formulario-grupo">
                    <label for="editNome">* Nome da Matéria:</label>
                    <input type="text" name="nome" id="editNome" required>
                </div>
                
                <div class="formulario-grupo">
                    <label>Escolher Cor:</label>
                    <div class="cores-predefinidas" id="editCoresPredefinidas">
                        <?php foreach ($cores_predefinidas as $cor => $nome_cor): ?>
                            <div class="opcao-cor" onclick="selecionarCorEdicao('<?php echo $cor; ?>', this)">
                                <div class="amostra-cor" style="background-color: <?php echo $cor; ?>"></div>
                                <div style="font-size: 0.8rem; margin-top: 5px;"><?php echo $nome_cor; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div style="margin-top: 15px;">
                        <label for="editCorPersonalizada">Ou escolha uma cor personalizada:</label>
                        <input type="color" id="editCorPersonalizada" 
                               onchange="selecionarCorPersonalizadaEdicao(this.value)">
                    </div>
                </div>
                
                <div class="navegacao">
                    <button type="submit" class="btn btn-success">💾 Salvar Alterações</button>
                    <button type="button" onclick="fecharModal()" class="btn">❌ Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para exclusão -->
    <div id="modalExclusao" class="modal">
        <div class="modal-conteudo">
            <h3>🗑️ Confirmar Exclusão</h3>
            <p id="textoExclusao"></p>
            <form method="POST" id="formExclusao">
                <input type="hidden" name="acao" value="excluir">
                <input type="hidden" name="id" id="excluirId">
                
                <div class="navegacao">
                    <button type="submit" class="btn" style="background: #e74c3c;">🗑️ Confirmar Exclusão</button>
                    <button type="button" onclick="fecharModal()" class="btn">❌ Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function selecionarCor(cor, elemento) {
            // Remover seleção anterior
            document.querySelectorAll('.opcao-cor').forEach(el => el.classList.remove('selecionada'));
            // Selecionar novo
            elemento.classList.add('selecionada');
            document.getElementById('corSelecionada').value = cor;
            document.getElementById('corPersonalizada').value = cor;
        }
        
        function selecionarCorPersonalizada(cor) {
            // Remover seleção das cores pré-definidas
            document.querySelectorAll('.opcao-cor').forEach(el => el.classList.remove('selecionada'));
            document.getElementById('corSelecionada').value = cor;
        }
        
        function selecionarCorEdicao(cor, elemento) {
            // Remover seleção anterior no modal de edição
            document.querySelectorAll('#editCoresPredefinidas .opcao-cor').forEach(el => el.classList.remove('selecionada'));
            // Selecionar novo
            elemento.classList.add('selecionada');
            document.getElementById('editCorSelecionada').value = cor;
            document.getElementById('editCorPersonalizada').value = cor;
        }
        
        function selecionarCorPersonalizadaEdicao(cor) {
            // Remover seleção das cores pré-definidas no modal de edição
            document.querySelectorAll('#editCoresPredefinidas .opcao-cor').forEach(el => el.classList.remove('selecionada'));
            document.getElementById('editCorSelecionada').value = cor;
        }
        
        function editarMateria(id, nome, cor) {
            document.getElementById('editId').value = id;
            document.getElementById('editNome').value = nome;
            document.getElementById('editCorSelecionada').value = cor;
            document.getElementById('editCorPersonalizada').value = cor;
            
            // Selecionar cor atual
            document.querySelectorAll('#editCoresPredefinidas .opcao-cor').forEach(el => {
                el.classList.remove('selecionada');
                const corElemento = el.querySelector('.amostra-cor').style.backgroundColor;
                // Converter RGB para HEX para comparar
                if (rgbToHex(corElemento) === cor.toUpperCase()) {
                    el.classList.add('selecionada');
                }
            });
            
            document.getElementById('modalEdicao').style.display = 'block';
        }
        
        function excluirMateria(id, nome) {
            document.getElementById('excluirId').value = id;
            document.getElementById('textoExclusao').innerHTML = 
                `Tem certeza que deseja excluir a matéria "<strong>${nome}</strong>"?<br><br>
                <span style="color: #e74c3c;">⚠️ Esta ação não pode ser desfeita!</span>`;
            document.getElementById('modalExclusao').style.display = 'block';
        }
        
        function fecharModal() {
            document.getElementById('modalEdicao').style.display = 'none';
            document.getElementById('modalExclusao').style.display = 'none';
        }
        
        // Função auxiliar para converter RGB para HEX
        function rgbToHex(rgb) {
            if (rgb.startsWith('#')) return rgb.toUpperCase();
            
            const rgbArray = rgb.match(/\d+/g);
            if (rgbArray) {
                return '#' + rgbArray.map(x => {
                    const hex = parseInt(x).toString(16);
                    return hex.length === 1 ? '0' + hex : hex;
                }).join('').toUpperCase();
            }
            return rgb;
        }
        
        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            const modalEdicao = document.getElementById('modalEdicao');
            const modalExclusao = document.getElementById('modalExclusao');
            
            if (event.target === modalEdicao) {
                modalEdicao.style.display = 'none';
            }
            if (event.target === modalExclusao) {
                modalExclusao.style.display = 'none';
            }
        }
        
        // Fechar modal com ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                fecharModal();
            }
        });
    </script>
</body>
</html>