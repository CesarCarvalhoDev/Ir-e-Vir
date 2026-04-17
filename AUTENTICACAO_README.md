# 🅿️ Ir e Vir - Sistema de Autenticação

## 📋 Visão Geral

Sistema completo de login e autenticação para a aplicação Ir e Vir usando:
- **Backend**: Laravel 11 com Sanctum API
- **Frontend**: Vue 3 com Vite

## 🚀 Instalação e Configuração

### Backend (Laravel)

1. **Ir para o diretório do back-end**
```bash
cd back-end-ir-e-vir
```

2. **Instalar dependências**
```bash
composer install
```

3. **Configurar variáveis de ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurar banco de dados em .env**
```
DB_CONNECTION=sqlite
DB_DATABASE=database.sqlite
```

5. **Executar migrações**
```bash
php artisan migrate
```

6. **Executar seeders para criar usuários de teste**
```bash
php artisan db:seed
```

7. **Iniciar o servidor**
```bash
php artisan serve
```

O backend estará disponível em: `http://localhost:8000`

### Frontend (Vue.js)

1. **Ir para o diretório do front-end**
```bash
cd front-end-ir-e-vir
```

2. **Instalar dependências**
```bash
npm install
```

3. **Configurar variáveis de ambiente**
O arquivo `.env` já está configurado com:
```
VITE_API_URL=http://localhost:8000/api/v1
```

4. **Iniciar o servidor de desenvolvimento**
```bash
npm run dev
```

O frontend estará disponível em: `http://localhost:5173`

## 👤 Credenciais de Teste

### Admin
- Email: `admin@irever.com`
- Senha: `password123`

### Usuários Comuns
- Email: `usuario1@example.com`
- Email: `usuario2@example.com`
- Email: `usuario3@example.com`
- Senha (todas): `password123`

## 🔐 Fluxo de Autenticação

### Login
1. Usuário acessa a aplicação
2. Se não estiver autenticado, vê a página de login
3. Insere email e senha
4. Sistema faz POST para `/api/v1/auth/login`
5. Backend retorna token Bearer e dados do usuário
6. Token é armazenado no localStorage
7. Usuário é redirecionado para seu dashboard

### Dashboard
- **Admin**: Vê painel de administração com todas as cobranças e formulário para criar novas
- **Usuário Comum**: Vê apenas suas cobranças filtradas por seu(s) veículo(s)

### Busca por Placa
- Usuário pode buscar cobranças de seus veículos pela placa
- A busca é automaticamente filtrada apenas para seus veículos

### Logout
- Usuário clica em "Sair"
- Sistema faz POST para `/api/v1/auth/logout` com token
- Token é deletado do servidor
- LocalStorage é limpo
- Usuário retorna para a página de login

## 📡 Endpoints da API

### Autenticação
```
POST /api/v1/auth/register
POST /api/v1/auth/login
POST /api/v1/auth/logout       (Requer autenticação)
GET  /api/v1/auth/me           (Requer autenticação)
```

### Cobranças
```
GET  /api/v1/charges            (Requer autenticação)
POST /api/v1/charges/{stay}     (Requer autenticação)
GET  /api/v1/charges/plate/{plate}  (Requer autenticação)
```

## 🗂️ Estrutura de Arquivos

### Backend
```
app/
  ├── Models/
  │   ├── User.php          (com relacionamento vehicles)
  │   ├── Vehicle.php       (com relacionamento user)
  │   ├── Stay.php
  │   └── Charge.php
  ├── Http/
  │   ├── Controllers/Api/V1/
  │   │   ├── AuthController.php     (NEW)
  │   │   └── ChargeController.php   (MODIFICADO)
  │   └── Requests/
  │       ├── LoginRequest.php       (NEW)
  │       └── RegisterRequest.php    (NEW)
  └── Services/
      └── Charge/
          └── GetChargesByUserService.php (NEW)

database/
  ├── migrations/
  │   └── 0001_01_01_000000_create_users_table.php  (role adicionado)
  ├── factories/
  │   └── VehicleFactory.php
  └── seeders/
      └── DatabaseSeeder.php  (MODIFICADO - adiciona usuários)

routes/
  └── api.php  (MODIFICADO - rotas de auth adicionadas)
```

### Frontend
```
src/
  ├── App.vue                  (MODIFICADO - adicionado auth)
  ├── main.js
  ├── style.css
  ├── components/
  │   ├── LoginPage.vue        (NEW)
  │   ├── UserDashboard.vue    (NEW)
  │   ├── ChargeCard.vue       (NEW)
  │   ├── FormCobranca.vue
  │   ├── ListaCobrancas.vue
  │   └── BuscaPlaca.vue
  └── services/
      └── authService.js       (NEW)

.env  (NEW)
```

## 🔑 Funcionalidades Principais

### 1. Sistema de Login e Registro
- Interface de login responsiva
- Modal de registro integrado
- Validação de formulários
- Tratamento de erros

### 2. Autenticação com Sanctum
- Tokens Bearer de longa duração
- Logout com limpeza de token
- Verificação de autenticação na inicialização

### 3. Filtro de Cobranças por Usuário
- Admin vê todas as cobranças
- Usuários veem apenas suas cobranças
- Busca por placa filtrada por usuário

### 4. Dashboard Personalizado
- Admin: Painel de gestão completo
- Usuários: Dashboard simples com suas cobranças

### 5. Busca Automática de Cobranças
- Campo de busca por placa
- Resultado exibido em card
- Validação de campos

## 🐛 Solução de Problemas

### Erro CORS
Se receber erro de CORS, verifique:
1. A URL da API em `.env` está correta
2. O servidor Laravel está rodando
3. Configure CORS em `config/cors.php`

### Token Inválido
Se o token expirar:
1. Será retornado erro 401
2. Usuário deve fazer login novamente
3. Local storage será limpo automaticamente

### Migração Falhando
Se a migração falhar:
```bash
php artisan migrate:rollback
php artisan migrate
php artisan db:seed
```

## 📝 Notas Adicionais

- Os tokens Sanctum são armazenados de forma segura
- Senhas são hasheadas com bcrypt
- CORS é configurado para aceitar requisições locais
- Frontend usa axios para requisições HTTP

## 🔄 Próximas Implementações

- [ ] Refresh token automático
- [ ] 2FA (Two-Factor Authentication)
- [ ] Recuperação de senha
- [ ] Edição de perfil
- [ ] Notificações de vencimento
- [ ] Integração com gateway de pagamento
- [ ] Dashboard de estatísticas
