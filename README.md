# 🚀 Atualizações do Sistema de Flashcards

## ✨ Novas Funcionalidades

### 1. 📝 Sistema de Edição de Flashcards

**O que foi adicionado:**
- Botões de ação (editar/excluir) em cada flashcard na página principal
- Página completa de edição com formulário intuitivo
- Comparação visual "antes vs depois"
- Validação e confirmação de alterações
- Sistema de exclusão com confirmação dupla
- Atalhos de teclado (Ctrl+S para salvar, Ctrl+P para preview)

**Como usar:**
1. Na página principal, clique no ícone ✏️ em qualquer flashcard
2. Edite as informações desejadas
3. Use "Pré-visualizar" para ver como ficará
4. Clique em "Salvar Alterações" ou use Ctrl+S
5. Para excluir, use o botão 🗑️ (confirmação dupla obrigatória)

**Arquivos criados/modificados:**
- `editar_flashcard.php` - Página de edição
- `excluir_flashcard.php` - Script de exclusão
- `index.php` - Adicionados botões de ação
- `css/estilos.css` - Estilos para botões de ação
- `js/flashcards.js` - Funções JavaScript para edição

---

### 2. 💾 Migração para Banco JSON

**O que foi implementado:**
- Sistema de armazenamento em arquivos JSON em vez de MySQL
- Classe `BancoJSON` com todas as funcionalidades do MySQL
- Utilitário automático de migração MySQL → JSON
- Compatibilidade total com o código existente
- Melhoria na performance para pequenos/médios volumes de dados
- Facilidade de backup e portabilidade

**Vantagens do sistema JSON:**
- ✅ **Portabilidade**: Leve os dados para qualquer lugar
- ✅ **Simplicidade**: Não precisa configurar MySQL
- ✅ **Backup fácil**: Apenas copie a pasta `dados/`
- ✅ **Performance**: Rápido para volumes pequenos/médios
- ✅ **Transparência**: Dados legíveis em texto simples
- ✅ **Menos dependências**: Funciona apenas com PHP

**Estrutura de arquivos JSON:**
```
dados/
├── provas.json      # Todas as provas
├── materias.json    # Todas as matérias
└── flashcards.json  # Todos os flashcards
```

**Como migrar do MySQL para JSON:**
1. Acesse: `utilitarios/migrar_mysql_para_json.php`
2. Clique em "Executar Migração"
3. Aguarde o processamento
4. Siga as instruções pós-migração
5. Teste o sistema

**Arquivos criados:**
- `config/banco_json.php` - Nova classe de dados
- `utilitarios/migrar_mysql_para_json.php` - Utilitário de migração

---

## 🔧 Instruções de Instalação das Atualizações

### Para Sistema Novo (Recomendado - JSON):

1. **Estrutura de pastas:**
   ```
   sistema_flashcards/
   ├── config/banco_json.php
   ├── dados/ (será criada automaticamente)
   ├── utilitarios/
   └── [demais arquivos]
   ```

2. **Configuração:**
   - Renomeie `config/banco_json.php` para `config/banco_dados.php`
   - O sistema criará automaticamente os dados iniciais

3. **Teste:**
   - Acesse `index.php`
   - Verifique se há flashcards de exemplo
   - Teste as funcionalidades de edição

### Para Sistema Existente (Migração MySQL → JSON):

1. **Backup:**
   ```bash
   # Faça backup do banco MySQL
   mysqldump -u usuario -p sistema_flashcards > backup_mysql.sql
   ```

2. **Adicionar novos arquivos:**
   - Coloque `banco_json.php` na pasta `config/`
   - Crie pasta `utilitarios/` e adicione o migrador
   - Atualize demais arquivos modificados

3. **Executar migração:**
   - Acesse: `utilitarios/migrar_mysql_para_json.php`
   - Execute a migração completa
   - Verifique se todos os dados foram transferidos

4. **Ativar sistema JSON:**
   ```bash
   # Backup da configuração MySQL
   mv config/banco_dados.php config/banco_dados_mysql.php
   
   # Ativar configuração JSON
   mv config/banco_json.php config/banco_dados.php
   ```

5. **Testar:**
   - Acesse o sistema
   - Verifique se todos os dados estão presentes
   - Teste todas as funcionalidades

---

## 🎯 Funcionalidades por Arquivo

### Edição de Flashcards:

**`editar_flashcard.php`:**
- Formulário completo de edição
- Pré-visualização das mudanças
- Comparação antes/depois
- Validação de dados
- Atalhos de teclado

**Controles disponíveis:**
- ✏️ **Editar**: Formulário completo
- 👁️ **Pré-visualizar**: Ver como ficará
- 🔄 **Restaurar**: Voltar ao original
- 💾 **Salvar**: Aplicar mudanças
- 🗑️ **Excluir**: Remover flashcard

### Sistema JSON:

**`config/banco_json.php`:**
- Classe `BancoJSON` com todas as operações
- Métodos idênticos ao sistema MySQL
- Gerenciamento automático de IDs
- Backup e restauração integrados

**Operações suportadas:**
- ✅ **CRUD completo**: Create, Read, Update, Delete
- ✅ **Relacionamentos**: Provas ↔ Flashcards, Matérias ↔ Flashcards
- ✅ **Estatísticas**: Contagem automática de flashcards
- ✅ **Filtros**: Por prova, matéria, dificuldade
- ✅ **Ordenação**: Por data, nome, relevância
- ✅ **Validação**: Integridade referencial

---

## 📊 Comparação: MySQL vs JSON

| Aspecto | MySQL | JSON |
|---------|-------|------|
| **Configuração** | Complexa (servidor, usuário, senha) | Simples (apenas PHP) |
| **Performance** | Excelente para grandes volumes | Ótima para pequenos/médios volumes |
| **Backup** | Comando mysqldump | Copiar pasta `dados/` |
| **Portabilidade** | Requer MySQL em qualquer servidor | Funciona em qualquer lugar com PHP |
| **Escalabilidade** | Ilimitada | Limitada (recomendado até ~10.000 registros) |
| **Manutenção** | Requer conhecimento SQL | Arquivos de texto simples |
| **Segurança** | Recursos avançados | Segurança básica do filesystem |

**Recomendação:**
- **JSON**: Para uso pessoal, pequenas equipes, prototipagem
- **MySQL**: Para uso corporativo, múltiplos usuários, grandes volumes

---

## 🐛 Solução de Problemas

### Problemas com Edição:
- **Erro "Flashcard não encontrado"**: Verifique se o ID existe nos dados
- **Mudanças não salvam**: Verifique permissões de escrita na pasta `dados/`
- **Botões não aparecem**: Limpe o cache do navegador

### Problemas com Migração:
- **Erro de conexão MySQL**: Verifique `config/banco_dados.php`
- **Erro de permissão**: `chmod
