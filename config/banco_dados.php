<?php
// Configurações para banco de dados JSON
define('PASTA_DADOS', __DIR__ . '/../dados/');
define('ARQUIVO_PROVAS', PASTA_DADOS . 'provas.json');
define('ARQUIVO_MATERIAS', PASTA_DADOS . 'materias.json');
define('ARQUIVO_FLASHCARDS', PASTA_DADOS . 'flashcards.json');

// Classe para gerenciamento de dados em JSON
class BancoDados {
    
    public function __construct() {
        $this->criarPastaDados();
        $this->inicializarArquivos();
    }
    
    // Criar pasta de dados se não existir
    private function criarPastaDados() {
        if (!file_exists(PASTA_DADOS)) {
            mkdir(PASTA_DADOS, 0755, true);
        }
    }
    
    // Inicializar arquivos JSON com dados padrão
    private function inicializarArquivos() {
        // Inicializar provas
        if (!file_exists(ARQUIVO_PROVAS)) {
            $provas_padrao = [
                [
                    'id' => 1,
                    'nome' => 'Professor Temporário DF',
                    'descricao' => 'Concurso para professor temporário do Distrito Federal',
                    'data_criacao' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 2,
                    'nome' => 'ENEM',
                    'descricao' => 'Exame Nacional do Ensino Médio',
                    'data_criacao' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 3,
                    'nome' => 'Concurso PCDF',
                    'descricao' => 'Polícia Civil do Distrito Federal',
                    'data_criacao' => date('Y-m-d H:i:s')
                ]
            ];
            $this->salvarArquivo(ARQUIVO_PROVAS, $provas_padrao);
        }
        
        // Inicializar matérias
        if (!file_exists(ARQUIVO_MATERIAS)) {
            $materias_padrao = [
                [
                    'id' => 1,
                    'nome' => 'Português',
                    'cor' => '#e74c3c',
                    'data_criacao' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 2,
                    'nome' => 'Matemática',
                    'cor' => '#3498db',
                    'data_criacao' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 3,
                    'nome' => 'História',
                    'cor' => '#f39c12',
                    'data_criacao' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 4,
                    'nome' => 'Geografia',
                    'cor' => '#2ecc71',
                    'data_criacao' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 5,
                    'nome' => 'Ciências',
                    'cor' => '#9b59b6',
                    'data_criacao' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 6,
                    'nome' => 'Inglês',
                    'cor' => '#1abc9c',
                    'data_criacao' => date('Y-m-d H:i:s')
                ]
            ];
            $this->salvarArquivo(ARQUIVO_MATERIAS, $materias_padrao);
        }
        
        // Inicializar flashcards
        if (!file_exists(ARQUIVO_FLASHCARDS)) {
            $flashcards_padrao = [
                [
                    'id' => 1,
                    'pergunta' => 'Qual é a função sintática do termo sublinhado: "O livro foi lido pelo aluno"?',
                    'resposta' => 'Agente da passiva',
                    'prova_id' => 1,
                    'materia_id' => 1,
                    'dificuldade' => 'medio',
                    'data_criacao' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 2,
                    'pergunta' => 'Complete: "Se eu _______ rico, viajaria pelo mundo."',
                    'resposta' => 'fosse',
                    'prova_id' => 1,
                    'materia_id' => 1,
                    'dificuldade' => 'facil',
                    'data_criacao' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 3,
                    'pergunta' => 'Qual é a capital do Brasil?',
                    'resposta' => 'Brasília',
                    'prova_id' => 2,
                    'materia_id' => 4,
                    'dificuldade' => 'facil',
                    'data_criacao' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 4,
                    'pergunta' => 'Quem escreveu "Dom Casmurro"?',
                    'resposta' => 'Machado de Assis',
                    'prova_id' => 1,
                    'materia_id' => 1,
                    'dificuldade' => 'medio',
                    'data_criacao' => date('Y-m-d H:i:s')
                ]
            ];
            $this->salvarArquivo(ARQUIVO_FLASHCARDS, $flashcards_padrao);
        }
    }
    
    // Salvar dados no arquivo JSON
    private function salvarArquivo($arquivo, $dados) {
        $json = json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($arquivo, $json) !== false;
    }
    
    // Carregar dados do arquivo JSON
    private function carregarArquivo($arquivo) {
        if (!file_exists($arquivo)) {
            return [];
        }
        
        $conteudo = file_get_contents($arquivo);
        $dados = json_decode($conteudo, true);
        
        return $dados ?: [];
    }
    
    // Gerar próximo ID
    private function proximoId($dados) {
        if (empty($dados)) {
            return 1;
        }
        
        $ids = array_column($dados, 'id');
        return max($ids) + 1;
    }
    
    // MÉTODOS PARA PROVAS
    public function obterProvas() {
        $provas = $this->carregarArquivo(ARQUIVO_PROVAS);
        // Ordenar por nome
        usort($provas, function($a, $b) {
            return strcmp($a['nome'], $b['nome']);
        });
        return $provas;
    }
    
    public function obterProvaPorId($id) {
        $provas = $this->carregarArquivo(ARQUIVO_PROVAS);
        foreach ($provas as $prova) {
            if ($prova['id'] == $id) {
                return $prova;
            }
        }
        return null;
    }
    
    public function inserirProva($nome, $descricao) {
        $provas = $this->carregarArquivo(ARQUIVO_PROVAS);
        
        $nova_prova = [
            'id' => $this->proximoId($provas),
            'nome' => $nome,
            'descricao' => $descricao,
            'data_criacao' => date('Y-m-d H:i:s')
        ];
        
        $provas[] = $nova_prova;
        return $this->salvarArquivo(ARQUIVO_PROVAS, $provas);
    }
    
    public function atualizarProva($id, $nome, $descricao) {
        $provas = $this->carregarArquivo(ARQUIVO_PROVAS);
        
        for ($i = 0; $i < count($provas); $i++) {
            if ($provas[$i]['id'] == $id) {
                $provas[$i]['nome'] = $nome;
                $provas[$i]['descricao'] = $descricao;
                return $this->salvarArquivo(ARQUIVO_PROVAS, $provas);
            }
        }
        return false;
    }
    
    public function excluirProva($id) {
        // Verificar se há flashcards associados
        $flashcards = $this->carregarArquivo(ARQUIVO_FLASHCARDS);
        foreach ($flashcards as $flashcard) {
            if ($flashcard['prova_id'] == $id) {
                return false; // Não pode excluir
            }
        }
        
        $provas = $this->carregarArquivo(ARQUIVO_PROVAS);
        $provas_filtradas = array_filter($provas, function($prova) use ($id) {
            return $prova['id'] != $id;
        });
        
        return $this->salvarArquivo(ARQUIVO_PROVAS, array_values($provas_filtradas));
    }
    
    // MÉTODOS PARA MATÉRIAS
    public function obterMaterias() {
        $materias = $this->carregarArquivo(ARQUIVO_MATERIAS);
        // Ordenar por nome
        usort($materias, function($a, $b) {
            return strcmp($a['nome'], $b['nome']);
        });
        return $materias;
    }
    
    public function obterMateriaPorId($id) {
        $materias = $this->carregarArquivo(ARQUIVO_MATERIAS);
        foreach ($materias as $materia) {
            if ($materia['id'] == $id) {
                return $materia;
            }
        }
        return null;
    }
    
    public function inserirMateria($nome, $cor) {
        $materias = $this->carregarArquivo(ARQUIVO_MATERIAS);
        
        $nova_materia = [
            'id' => $this->proximoId($materias),
            'nome' => $nome,
            'cor' => $cor,
            'data_criacao' => date('Y-m-d H:i:s')
        ];
        
        $materias[] = $nova_materia;
        return $this->salvarArquivo(ARQUIVO_MATERIAS, $materias);
    }
    
    public function atualizarMateria($id, $nome, $cor) {
        $materias = $this->carregarArquivo(ARQUIVO_MATERIAS);
        
        for ($i = 0; $i < count($materias); $i++) {
            if ($materias[$i]['id'] == $id) {
                $materias[$i]['nome'] = $nome;
                $materias[$i]['cor'] = $cor;
                return $this->salvarArquivo(ARQUIVO_MATERIAS, $materias);
            }
        }
        return false;
    }
    
    public function excluirMateria($id) {
        // Verificar se há flashcards associados
        $flashcards = $this->carregarArquivo(ARQUIVO_FLASHCARDS);
        foreach ($flashcards as $flashcard) {
            if ($flashcard['materia_id'] == $id) {
                return false; // Não pode excluir
            }
        }
        
        $materias = $this->carregarArquivo(ARQUIVO_MATERIAS);
        $materias_filtradas = array_filter($materias, function($materia) use ($id) {
            return $materia['id'] != $id;
        });
        
        return $this->salvarArquivo(ARQUIVO_MATERIAS, array_values($materias_filtradas));
    }
    
    // MÉTODOS PARA FLASHCARDS
    public function inserirFlashcard($pergunta, $resposta, $prova_id, $materia_id, $dificuldade) {
        $flashcards = $this->carregarArquivo(ARQUIVO_FLASHCARDS);
        
        $novo_flashcard = [
            'id' => $this->proximoId($flashcards),
            'pergunta' => $pergunta,
            'resposta' => $resposta,
            'prova_id' => $prova_id,
            'materia_id' => $materia_id,
            'dificuldade' => $dificuldade,
            'data_criacao' => date('Y-m-d H:i:s')
        ];
        
        $flashcards[] = $novo_flashcard;
        return $this->salvarArquivo(ARQUIVO_FLASHCARDS, $flashcards);
    }
    
    public function atualizarFlashcard($id, $pergunta, $resposta, $prova_id, $materia_id, $dificuldade) {
        $flashcards = $this->carregarArquivo(ARQUIVO_FLASHCARDS);
        
        for ($i = 0; $i < count($flashcards); $i++) {
            if ($flashcards[$i]['id'] == $id) {
                $flashcards[$i]['pergunta'] = $pergunta;
                $flashcards[$i]['resposta'] = $resposta;
                $flashcards[$i]['prova_id'] = $prova_id;
                $flashcards[$i]['materia_id'] = $materia_id;
                $flashcards[$i]['dificuldade'] = $dificuldade;
                return $this->salvarArquivo(ARQUIVO_FLASHCARDS, $flashcards);
            }
        }
        return false;
    }
    
    public function excluirFlashcard($id) {
        $flashcards = $this->carregarArquivo(ARQUIVO_FLASHCARDS);
        $flashcards_filtrados = array_filter($flashcards, function($flashcard) use ($id) {
            return $flashcard['id'] != $id;
        });
        
        return $this->salvarArquivo(ARQUIVO_FLASHCARDS, array_values($flashcards_filtrados));
    }
    
    public function obterFlashcard($id) {
        $flashcards = $this->carregarArquivo(ARQUIVO_FLASHCARDS);
        foreach ($flashcards as $flashcard) {
            if ($flashcard['id'] == $id) {
                return $flashcard;
            }
        }
        return null;
    }
    
    public function obterFlashcards($prova_id = null, $materia_id = null) {
        $flashcards = $this->carregarArquivo(ARQUIVO_FLASHCARDS);
        $provas = $this->carregarArquivo(ARQUIVO_PROVAS);
        $materias = $this->carregarArquivo(ARQUIVO_MATERIAS);
        
        // Criar índices para lookup rápido
        $provas_index = [];
        foreach ($provas as $prova) {
            $provas_index[$prova['id']] = $prova;
        }
        
        $materias_index = [];
        foreach ($materias as $materia) {
            $materias_index[$materia['id']] = $materia;
        }
        
        // Filtrar flashcards
        $resultado = [];
        foreach ($flashcards as $flashcard) {
            // Aplicar filtros
            if ($prova_id && $flashcard['prova_id'] != $prova_id) {
                continue;
            }
            if ($materia_id && $flashcard['materia_id'] != $materia_id) {
                continue;
            }
            
            // Adicionar informações das provas e matérias
            $flashcard_completo = $flashcard;
            
            if ($flashcard['prova_id'] && isset($provas_index[$flashcard['prova_id']])) {
                $flashcard_completo['prova_nome'] = $provas_index[$flashcard['prova_id']]['nome'];
            } else {
                $flashcard_completo['prova_nome'] = null;
            }
            
            if ($flashcard['materia_id'] && isset($materias_index[$flashcard['materia_id']])) {
                $flashcard_completo['materia_nome'] = $materias_index[$flashcard['materia_id']]['nome'];
                $flashcard_completo['materia_cor'] = $materias_index[$flashcard['materia_id']]['cor'];
            } else {
                $flashcard_completo['materia_nome'] = null;
                $flashcard_completo['materia_cor'] = null;
            }
            
            $resultado[] = $flashcard_completo;
        }
        
        // Ordenar por data de criação (mais recentes primeiro)
        usort($resultado, function($a, $b) {
            return strcmp($b['data_criacao'], $a['data_criacao']);
        });
        
        return $resultado;
    }
    
    // MÉTODOS PARA ESTATÍSTICAS
    public function obterEstatisticasProvas() {
        $provas = $this->carregarArquivo(ARQUIVO_PROVAS);
        $flashcards = $this->carregarArquivo(ARQUIVO_FLASHCARDS);
        
        $resultado = [];
        foreach ($provas as $prova) {
            $total_flashcards = 0;
            foreach ($flashcards as $flashcard) {
                if ($flashcard['prova_id'] == $prova['id']) {
                    $total_flashcards++;
                }
            }
            
            $prova['total_flashcards'] = $total_flashcards;
            $resultado[] = $prova;
        }
        
        return $resultado;
    }
    
    public function obterEstatisticasMaterias() {
        $materias = $this->carregarArquivo(ARQUIVO_MATERIAS);
        $flashcards = $this->carregarArquivo(ARQUIVO_FLASHCARDS);
        
        $resultado = [];
        foreach ($materias as $materia) {
            $total_flashcards = 0;
            foreach ($flashcards as $flashcard) {
                if ($flashcard['materia_id'] == $materia['id']) {
                    $total_flashcards++;
                }
            }
            
            $materia['total_flashcards'] = $total_flashcards;
            $resultado[] = $materia;
        }
        
        return $resultado;
    }
    
    // MÉTODOS DE BACKUP E MIGRAÇÃO
    public function exportarDados() {
        return [
            'provas' => $this->carregarArquivo(ARQUIVO_PROVAS),
            'materias' => $this->carregarArquivo(ARQUIVO_MATERIAS),
            'flashcards' => $this->carregarArquivo(ARQUIVO_FLASHCARDS),
            'data_export' => date('Y-m-d H:i:s')
        ];
    }
    
    public function importarDados($dados) {
        $sucesso = true;
        
        if (isset($dados['provas'])) {
            $sucesso = $sucesso && $this->salvarArquivo(ARQUIVO_PROVAS, $dados['provas']);
        }
        
        if (isset($dados['materias'])) {
            $sucesso = $sucesso && $this->salvarArquivo(ARQUIVO_MATERIAS, $dados['materias']);
        }
        
        if (isset($dados['flashcards'])) {
            $sucesso = $sucesso && $this->salvarArquivo(ARQUIVO_FLASHCARDS, $dados['flashcards']);
        }
        
        return $sucesso;
    }
}
?>