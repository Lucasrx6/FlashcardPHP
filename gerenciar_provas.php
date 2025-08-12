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
                $descricao = trim($_POST['descricao']);
                
                if (empty($nome)) {
                    $mensagem = '<div class="mensagem-erro">❌ Nome da prova é obrigatório!</div>';
                } else {
                    if ($bd->inserirProva($nome, $descricao)) {
                        $mensagem = '<div class="mensagem-sucesso">✅ Prova adicionada com sucesso!</div>';
                    } else {
                        $mensagem = '<div class="mensagem-erro">❌ Erro ao adicionar prova!</div>';
                    }
                }
                break;
                
            case 'editar':
                $id = $_POST['id'];
                $nome = trim($_POST['nome']);
                $descricao = trim($_POST['descricao']);
                
                if (empty($nome)) {
                    $mensagem = '<div class="mensagem-erro">❌ Nome da prova é obrigatório!</div>';
                } else {
                    if ($bd->atualizarProva($id, $nome, $descricao)) {
                        $mensagem = '<div class="mensagem-sucesso">✅ Prova atualizada com sucesso!</div>';
                    } else {
                        $mensagem = '<div class="mensagem-erro">❌ Erro ao atualizar prova!</div>';
                    }
                }
                break;
                
            case 'excluir':
                $id = $_POST['id'];
                if ($bd->excluirProva($id)) {
                    $mensagem = '<div class="mensagem-sucesso">✅ Prova excluída com sucesso!</div>';
                } else {
                    $mensagem = '<div class="mensagem-erro">❌ Erro ao excluir prova! Verifique se não há flashcards associados.</div>';
                }
                break;
        }
    }
}

// Obter dados
$provas = $bd->obterEstatisticasProvas();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Provas - Sistema de Estudos</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .tabela {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .tabela th,
        .tabela td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .tabela th {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            font-weight: bold;
        }
        
        .tabela tr:hover {
            background-color: #f8f9fa;
        }
        
        .btn-pequeno {
            padding: 5px 10px;
            font-size: 0.8rem;
            margin: 2px;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 30px;
            border: none;
            border-radius: 15px;
            width: 80%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .fechar {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .fechar:hover {
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📋 Gerenciar Provas</h1>
            <p class="subtitulo">Organize suas provas e concursos</p>
            
            <nav class="navegacao">
                <a href="index.php" class="btn">🏠 Voltar ao Início</a>
                <a href="gerenciar_materias.php" class="btn btn-warning">📚 Gerenciar Matérias</a>
                <button onclick="abrirModal('modalAdicionar')" class="btn btn-success">➕ Nova Prova</button>
            </nav>
        </header>

        <?php echo $mensagem; ?>

        <section class="secao">
            <h2>📊 Suas Provas</h2>
            
            <?php if (!empty($provas)): ?>
                <div class="contador">
                    📊 Total de provas: <?php echo count($provas); ?>
                </div>
                
                <table class="tabela">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Flashcards</th>
                            <th>Data de Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($provas as $prova): ?>
                            <tr>
                                <td><?php echo $prova['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($prova['nome']); ?></strong></td>
                                <td><?php echo htmlspecialchars($prova['descricao']); ?></td>
                                <td>
                                    <span style="background: #3498db; color: white; padding: 5px 10px; border-radius: 20px; font-size: 0.8rem;">
                                        <?php echo $prova['total_flashcards']; ?> flashcards
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($prova['data_criacao'])); ?></td>
                                <td>
                                    <button onclick="editarProva(<?php echo htmlspecialchars(json_encode($prova)); ?>)" 
                                            class="btn btn-pequeno" style="background: #f39c12;">
                                        ✏️ Editar
                                    </button>
                                    
                                    <?php if ($prova['total_flashcards'] == 0): ?>
                                        <button onclick="excluirProva(<?php echo $prova['id']; ?>, '<?php echo htmlspecialchars($prova['nome']); ?>')" 
                                                class="btn btn-pequeno" style="background: #e74c3c;">
                                            🗑️ Excluir
                                        </button>
                                    <?php else: ?>
                                        <span style="color: #7f8c8d; font-size: 0.8rem;">
                                            Não pode excluir<br>(tem flashcards)
                                        </span>
                                    <?php endif; ?>
                                    
                                    <a href="index.php?prova=<?php echo $prova['id']; ?>" 
                                       class="btn btn-pequeno" style="background: #27ae60;">
                                        👁️ Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
            <?php else: ?>
                <div style="text-align: center; padding: 50px;">
                    <h3>😔 Nenhuma prova cadastrada</h3>
                    <p>Comece adicionando sua primeira prova.</p>
                    <button onclick="abrirModal('modalAdicionar')" class="btn btn-success" style="margin-top: 20px;">
                        ➕ Adicionar Primeira Prova
                    </button>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <!-- Modal Adicionar -->
    <div id="modalAdicionar" class="modal">
        <div class="modal-content">
            <span class="fechar" onclick="fecharModal('modalAdicionar')">&times;</span>
            <h2>➕ Nova Prova</h2>
            
            <form method="POST">
                <input type="hidden" name="acao" value="adicionar">
                
                <div class="formulario-grupo">
                    <label for="nome">* Nome da Prova:</label>
                    <input type="text" name="nome" id="nome" required 
                           placeholder="Ex: ENEM, Concurso PCDF..." maxlength="100">
                </div>
                
                <div class="formulario-grupo">
                    <label for="descricao">Descrição:</label>
                    <textarea name="descricao" id="descricao" 
                              placeholder="Descrição opcional da prova..." maxlength="500"></textarea>
                </div>
                
                <div class="navegacao">
                    <button type="submit" class="btn btn-success">💾 Salvar Prova</button>
                    <button type="button" onclick="fecharModal('modalAdicionar')" class="btn">❌ Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar -->
    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <span class="fechar" onclick="fecharModal('modalEditar')">&times;</span>
            <h2>✏️ Editar Prova</h2>
            
            <form method="POST" id="formEditar">
                <input type="hidden" name="acao" value="editar">
                <input type="hidden" name="id" id="editarId">
                
                <div class="formulario-grupo">
                    <label for="editarNome">* Nome da Prova:</label>
                    <input type="text" name="nome" id="editarNome" required maxlength="100">
                </div>
                
                <div class="formulario-grupo">
                    <label for="editarDescricao">Descrição:</label>
                    <textarea name="descricao" id="editarDescricao" maxlength="500"></textarea>
                </div>
                
                <div class="navegacao">
                    <button type="submit" class="btn btn-success">💾 Salvar Alterações</button>
                    <button type="button" onclick="fecharModal('modalEditar')" class="btn">❌ Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Excluir -->
    <div id="modalExcluir" class="modal">
        <div class="modal-content">
            <span class="fechar" onclick="fecharModal('modalExcluir')">&times;</span>
            <h2>🗑️ Excluir Prova</h2>
            
            <div style="text-align: center; margin: 20px 0;">
                <div style="font-size: 4rem;">⚠️</div>
                <h3>ATENÇÃO!</h3>
                <p>Tem certeza que deseja excluir a prova:</p>
                <p><strong id="nomeProvaExcluir"></strong></p>
                <p style="color: #e74c3c; font-weight: bold;">Esta ação não pode ser desfeita!</p>
            </div>
            
            <form method="POST" id="formExcluir">
                <input type="hidden" name="acao" value="excluir">
                <input type="hidden" name="id" id="excluirId">
                
                <div class="navegacao">
                    <button type="submit" class="btn" style="background: #e74c3c;">🗑️ Sim, Excluir</button>
                    <button type="button" onclick="fecharModal('modalExcluir')" class="btn btn-success">❌ Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Funções para modais
        function abrirModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
            if (modalId === 'modalAdicionar') {
                document.getElementById('nome').focus();
            }
        }
        
        function fecharModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            if (modalId === 'modalAdicionar') {
                document.getElementById('nome').value = '';
                document.getElementById('descricao').value = '';
            }
        }
        
        // Fechar modal clicando fora
        window.onclick = function(event) {
            const modais = ['modalAdicionar', 'modalEditar', 'modalExcluir'];
            modais.forEach(function(modalId) {
                const modal = document.getElementById(modalId);
                if (event.target === modal) {
                    fecharModal(modalId);
                }
            });
        }
        
        // Função para editar prova
        function editarProva(prova) {
            document.getElementById('editarId').value = prova.id;
            document.getElementById('editarNome').value = prova.nome;
            document.getElementById('editarDescricao').value = prova.descricao || '';
            abrirModal('modalEditar');
        }
        
        // Função para excluir prova
        function excluirProva(id, nome) {
            document.getElementById('excluirId').value = id;
            document.getElementById('nomeProvaExcluir').textContent = nome;
            abrirModal('modalExcluir');
        }
        
        // Atalhos de teclado
        document.addEventListener('keydown', function(e) {
            // ESC para fechar modais
            if (e.key === 'Escape') {
                fecharModal('modalAdicionar');
                fecharModal('modalEditar');
                fecharModal('modalExcluir');
            }
            
            // Ctrl + N para nova prova
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                abrirModal('modalAdicionar');
            }
        });
        
        // Verificar se há mensagem e auto-fechar modais
        <?php if ($mensagem): ?>
            // Se há mensagem, fechar todos os modais
            setTimeout(() => {
                fecharModal('modalAdicionar');
                fecharModal('modalEditar');
                fecharModal('modalExcluir');
            }, 100);
        <?php endif; ?>
    </script>
</body>
</html>