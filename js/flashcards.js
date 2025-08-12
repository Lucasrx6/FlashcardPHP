// Função para virar um flashcard individual
function virarFlashcard(elemento) {
    elemento.classList.add('animacao-viragem');
    
    setTimeout(() => {
        elemento.classList.toggle('virado');
        elemento.classList.remove('animacao-viragem');
    }, 300);
}

// Função para embaralhar os flashcards
function embaralharFlashcards() {
    const lista = document.getElementById('listaFlashcards');
    const flashcards = Array.from(lista.children);
    
    // Algoritmo Fisher-Yates para embaralhar
    for (let i = flashcards.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [flashcards[i], flashcards[j]] = [flashcards[j], flashcards[i]];
    }
    
    // Reordenar os elementos no DOM
    flashcards.forEach(flashcard => {
        lista.appendChild(flashcard);
    });
    
    // Mostrar mensagem de confirmação
    mostrarMensagem('🔀 Flashcards embaralhados com sucesso!');
}

// Função para mostrar todas as perguntas
function mostrarTodasPerguntas() {
    const flashcards = document.querySelectorAll('.flashcard');
    flashcards.forEach(flashcard => {
        flashcard.classList.remove('virado');
    });
    mostrarMensagem('❓ Mostrando todas as perguntas');
}

// Função para mostrar todas as respostas
function mostrarTodasRespostas() {
    const flashcards = document.querySelectorAll('.flashcard');
    flashcards.forEach(flashcard => {
        flashcard.classList.add('virado');
    });
    mostrarMensagem('✅ Mostrando todas as respostas');
}

// Função para mostrar mensagem temporária
function mostrarMensagem(texto, tipo = 'sucesso') {
    // Remover mensagem anterior se existir
    const mensagemAnterior = document.querySelector('.mensagem-temporaria');
    if (mensagemAnterior) {
        mensagemAnterior.remove();
    }
    
    // Definir cores baseadas no tipo
    const cores = {
        sucesso: 'linear-gradient(45deg, #27ae60, #2ecc71)',
        erro: 'linear-gradient(45deg, #e74c3c, #c0392b)',
        aviso: 'linear-gradient(45deg, #f39c12, #e67e22)'
    };
    
    // Criar nova mensagem
    const mensagem = document.createElement('div');
    mensagem.className = 'mensagem-temporaria';
    mensagem.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${cores[tipo] || cores.sucesso};
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        z-index: 1000;
        animation: slideIn 0.3s ease-out;
        max-width: 300px;
    `;
    mensagem.textContent = texto;
    
    document.body.appendChild(mensagem);
    
    // Remover mensagem após 4 segundos
    setTimeout(() => {
        mensagem.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            mensagem.remove();
        }, 300);
    }, 4000);
}

// Adicionar estilos CSS para animações das mensagens
const estilos = document.createElement('style');
estilos.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(estilos);

// Adicionar atalhos de teclado
document.addEventListener('keydown', function(e) {
    // Ctrl + S para embaralhar
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        embaralharFlashcards();
    }
    
    // Ctrl + Q para mostrar perguntas
    if (e.ctrlKey && e.key === 'q') {
        e.preventDefault();
        mostrarTodasPerguntas();
    }
    
    // Ctrl + R para mostrar respostas
    if (e.ctrlKey && e.key === 'r') {
        e.preventDefault();
        mostrarTodasRespostas();
    }
});

// Função para contador de progresso
function atualizarProgresso() {
    const totalFlashcards = document.querySelectorAll('.flashcard').length;
    const flashcardsVirados = document.querySelectorAll('.flashcard.virado').length;
    const porcentagem = totalFlashcards > 0 ? Math.round((flashcardsVirados / totalFlashcards) * 100) : 0;
    
    // Atualizar contador se existir
    const contador = document.querySelector('.contador');
    if (contador && totalFlashcards > 0) {
        contador.innerHTML = `
            📊 Total: ${totalFlashcards} | 
            ✅ Respondidos: ${flashcardsVirados} | 
            📈 Progresso: ${porcentagem}%
        `;
    }
}

// Atualizar progresso quando um flashcard é virado
document.addEventListener('click', function(e) {
    if (e.target.closest('.flashcard')) {
        setTimeout(atualizarProgresso, 350); // Aguardar animação
    }
});

// Inicializar contador
document.addEventListener('DOMContentLoaded', atualizarProgresso);

// Funções para editar e excluir flashcards
function editarFlashcard(id) {
    window.location.href = `editar_flashcard.php?id=${id}`;
}

function excluirFlashcard(id) {
    if (confirm('⚠️ ATENÇÃO!\n\nTem certeza que deseja excluir este flashcard?\n\nEsta ação não pode ser desfeita!')) {
        // Criar formulário para exclusão
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'excluir_flashcard.php';
        form.style.display = 'none';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = id;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

// Verificar mensagens na URL
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');
    
    if (msg === 'excluido') {
        mostrarMensagem('✅ Flashcard excluído com sucesso!');
        // Limpar a URL
        window.history.replaceState({}, document.title, window.location.pathname);
    } else if (msg === 'erro_exclusao') {
        mostrarMensagem('❌ Erro ao excluir flashcard!', 'erro');
        // Limpar a URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});