# EstoqueDeFerramentas (Tool Inventory) — PHP + MySQL (sem framework)

Aplicação web simples para **controle de estoque de ferramentas** (ex.: alicate, chave de fenda, martelo), com **registro/login**, **CRUD de ferramentas**, **movimentações de estoque (IN/OUT)** e **dashboard com saldo atual**.

Projeto pensado no formato de **prova prática (3 horas)**, com foco em simplicidade, validações e regras de negócio essenciais.

---

## Tecnologias
- PHP 8+
- MySQL/MariaDB
- Apache (LAMPP/XAMPP)
- PDO (prepared statements)
- HTML/CSS/JS (sem framework)

---

## Regras de negócio implementadas
- Registro e Login com sessão
- Senhas armazenadas com `password_hash` e verificadas com `password_verify`
- Email único para usuário
- Ferramentas com estoque mínimo
- Movimentações:
  - `IN` (entrada)
  - `OUT` (saída) **não permite saldo negativo**
- Saldo atual calculado por SQL: **soma(IN) - soma(OUT)**
- Indicador de estoque baixo quando `estoque_atual < estoque_minimo`
- Campo **status (0/1)**:
  - `users.status`: 1 ativo / 0 inativo (login bloqueado para inativo)
  - `tools.status`: 1 ativo / 0 inativo (movimentação bloqueada para inativo)
  - Ao excluir ferramenta com movimentações: o sistema **inativa** (status=0) em vez de apagar

Mensagens obrigatórias do enunciado mantidas:
- Registro: `"register completed successfully"`
- Login inválido: `"invalid credentials"`

---

## Estrutura do projeto (pastas/arquivos)
Os **nomes dos arquivos** seguem padrão em inglês conforme solicitado.

```
/EstoqueDeFerramentas
  index.php
  dashboard.php
  login_create.php
  register_create.php
  logout_delete.php
  tool_list.php
  tool_create.php
  tool_edit.php
  tool_delete.php
  move_list.php
  move_create.php
  database.sql

  /assets
    /css
      style.css
    /js
      app.js

  /config
    /shared
      config.php
      database.php
      auth.php
      shared.php
```

---

## Pré-requisitos
- Apache + PHP (LAMPP/XAMPP)
- MySQL/MariaDB
- Extensão PDO MySQL habilitada (`pdo_mysql`)
- Acesso ao phpMyAdmin ou terminal MySQL

---

## Instalação (passo a passo)

### 1) Colocar o projeto no servidor
No LAMPP:
- Copie a pasta do projeto para:
  - `/opt/lampp/htdocs/saep/EstoqueDeFerramentas/`

### 2) Criar o banco de dados
Opção A (phpMyAdmin):
1. Abra o phpMyAdmin
2. Importe o arquivo `database.sql`

Opção B (terminal MySQL):
```bash
mysql -u root -p < database.sql
```

### 3) Configurar conexão com o banco
Edite:
- `config/shared/config.php`

Exemplo:
```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'tool_inventory');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 4) Acessar no navegador
Abra:
- `http://localhost/saep/EstoqueDeFerramentas/`

---

## Como usar (fluxo rápido)
1. Acesse o sistema e vá em **Cadastrar**
2. Crie um usuário
3. Faça login
4. Cadastre ferramentas em **Ferramentas**
5. Registre entradas/saídas em **Nova Movimentação**
6. Acompanhe o saldo no **Painel** e o histórico em **Movimentações**

---

## Regras do Status (0/1)

### Usuários (`users.status`)
- `1` = ativo (pode fazer login)
- `0` = inativo (login bloqueado com mensagem "Usuário inativo")

### Ferramentas (`tools.status`)
- `1` = ativa (pode registrar movimentações)
- `0` = inativa (não aparece para movimentação; se tentar via POST, bloqueia)

Exclusão de ferramenta:
- Se **não** tiver movimentações: exclui do banco
- Se **tiver** movimentações: inativa (`status = 0`)

---

## Páginas principais
- `index.php`: redireciona para login ou dashboard conforme sessão
- `register_create.php`: cadastro
- `login_create.php`: login
- `dashboard.php`: painel com saldo atual e indicador de estoque baixo
- `tool_*`: CRUD de ferramentas
- `move_*`: criação e listagem de movimentações
- `logout_delete.php`: encerra sessão

---

## Dicas de troubleshooting

### Erro 404 ao abrir páginas
Verifique se a URL contém o caminho correto da pasta:
- `http://localhost/saep/EstoqueDeFerramentas/`

### Tela branca/erro 500
1. Veja logs do Apache (`/opt/lampp/logs/error_log`)
2. Rode lint do PHP:
```bash
php -l dashboard.php
```

### Include/require não encontrado
Os arquivos da raiz devem incluir:
```php
require_once __DIR__ . '/config/shared/shared.php';
```

---

## Entregáveis da prova (checklist)
- [ ] DER (JPG/PNG) com users, tools, stock_moves e relacionamentos
- [ ] Caso de uso (JPG/PNG) com ator User e casos obrigatórios
- [ ] `database.sql` exportado
- [ ] Código fonte em pasta com estrutura organizada
- [ ] Registro/Login com sessão
- [ ] Dashboard com saldo atual (IN - OUT)
- [ ] CRUD ferramentas
- [ ] Movimentação IN/OUT com bloqueio de saldo negativo
- [ ] Histórico de movimentações
- [ ] CSS/JS separados

---

## Licença
Uso educacional/demonstração.
