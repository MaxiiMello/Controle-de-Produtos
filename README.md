# Sistema de Controle de Produtos

Sistema web completo para gerenciamento de estoque e produtos, desenvolvido com PHP, MySQL e JavaScript.

## Funcionalidades

### Gerenciamento de Produtos
- Cadastro, edição e exclusão de produtos
- Controle de estoque com alertas de estoque baixo
- Upload de imagens
- Filtros por categoria, status e busca textual
- Ordenação instantânea por qualquer coluna

### Categorias
- CRUD completo de categorias
- Associação com produtos
- Contagem de produtos por categoria

### Fornecedores
- Cadastro de fornecedores
- Filtros e ordenação
- Controle de status (ativo/inativo)

### Movimentações de Estoque
- Registro de entradas e saídas
- Filtros por produto, tipo e período
- Histórico completo de movimentações
- Atualização automática do estoque

### Dashboard
- Visão geral do estoque
- Estatísticas em tempo real
- Alertas de estoque baixo
- Últimas movimentações

### Relatórios
- Produtos por status
- Produtos por categoria
- Top produtos mais movimentados
- Movimentações dos últimos 30 dias

## Tecnologias

- **Backend:** PHP 8.x com PDO
- **Frontend:** HTML5, CSS3, JavaScript vanilla
- **Banco de Dados:** MySQL
- **Design:** Sistema de design moderno responsivo

## Estrutura do Projeto

```
Controle-de-Produtos/
├── public/                  # Arquivos públicos
│   ├── index.php           # Dashboard
│   ├── produtos.php        # Gerenciamento de produtos
│   ├── categorias.php      # Gerenciamento de categorias
│   ├── fornecedores.php    # Gerenciamento de fornecedores
│   ├── movimentacoes.php   # Movimentações de estoque
│   ├── estoque.php         # Visualização de estoque
│   ├── relatorios.php      # Relatórios e análises
│   ├── login.php           # Página de login
│   ├── logout.php          # Logout
│   ├── editar_produto.php  # Edição de produto
│   ├── setup.php           # Configuração inicial
│   ├── assets/
│   │   ├── css/
│   │   │   └── moderno.css # Estilos do sistema
│   │   └── js/
│   │       └── sortable.js # Ordenação client-side
│   └── includes/
│       ├── header.php      # Cabeçalho
│       └── footer.php      # Rodapé
├── src/
│   ├── config/
│   │   └── conexao.php     # Conexão com banco de dados
│   ├── functions/
│   │   ├── funcoes.php     # Funções do sistema
│   │   └── auth.php        # Funções de autenticação
│   └── includes/
│       ├── header.php
│       ├── footer.php
│       └── icons.php
├── database/
│   ├── schema.sql          # Estrutura do banco
│   └── inserts.sql         # Dados iniciais
├── nixpacks.toml           # Configuração Railway
└── .gitignore

## Requisitos

- PHP 8.0 ou superior
- MySQL 5.7 ou superior
- Extensões PHP: pdo, pdo_mysql, mbstring

## Instalação Local

### 1. Clone o repositório

```bash
git clone https://github.com/seuusuario/controle-produtos.git
cd controle-produtos
```

### 2. Configure o banco de dados

Crie um banco MySQL e execute os scripts:

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/inserts.sql
```

### 3. Configure a conexão

Edite o arquivo `src/config/conexao.php` com suas credenciais:

```php
$host = 'localhost';
$db   = 'controle_produtos';
$user = 'root';
$pass = 'sua_senha';
```

### 4. Inicie o servidor

```bash
php -S localhost:8000 -t public
```

Acesse: http://localhost:8000

### 5. Login

- **Email:** admin@admin.com
- **Senha:** admin123

## Deploy no Railway

### 1. Suba para o GitHub

```bash
git init
git add .
git commit -m "Primeira versão"
git branch -M main
git remote add origin https://github.com/seuusuario/controle-produtos.git
git push -u origin main
```

### 2. Crie conta no Railway

- Acesse [Railway](https://railway.com)
- Faça login com GitHub

### 3. Crie novo projeto

- **New Project** → **Deploy from GitHub Repo**
- Selecione o repositório

### 4. Adicione MySQL

- **New** → **Database** → **MySQL**
- Anote as credenciais geradas

### 5. Configure variáveis de ambiente

O sistema já está configurado para usar variáveis de ambiente:
- `MYSQLHOST`
- `MYSQLPORT`
- `MYSQLUSER`
- `MYSQLPASSWORD`
- `MYSQLDATABASE`

### 6. Execute o schema

Conecte ao banco MySQL criado e execute:

```bash
mysql -h host -u usuario -p database < database/schema.sql
mysql -h host -u usuario -p database < database/inserts.sql
```

### 7. Acesse o sistema

Após o deploy, acesse a URL fornecida pelo Railway.

## Características

- ✅ Sistema de login com bcrypt
- ✅ CRUD completo de produtos e categorias
- ✅ Filtros avançados em todas as páginas
- ✅ Ordenação client-side (sem reload)
- ✅ Controle de estoque com alertas
- ✅ Registro de movimentações
- ✅ Dashboard com estatísticas
- ✅ Relatórios e análises
- ✅ Design responsivo moderno
- ✅ Pronto para deploy

## Segurança

- Senhas criptografadas com bcrypt
- Proteção contra SQL injection (PDO prepared statements)
- Validação de entrada de dados
- Controle de acesso por sessão

## Licença

MIT License

## Autor

Desenvolvido como projeto de sistema web dinâmico com PHP e MySQL.