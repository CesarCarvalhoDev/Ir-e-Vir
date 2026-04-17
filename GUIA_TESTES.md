# 🧪 Guia de Testes - Sistema de Autenticação

## ✅ Checklist de Funcionalidades

### Autenticação

- [ ] **Página de Login**
  - [ ] Formulário de login é exibido quando não autenticado
  - [ ] Campo de email aceita entrada
  - [ ] Campo de senha aceita entrada
  - [ ] Botão "Entrar" submete o formulário
  - [ ] Mensagens de erro aparecem em caso de credenciais inválidas

- [ ] **Página de Registro**
  - [ ] Link "Registre-se aqui" abre modal
  - [ ] Formulário de registro contém: Nome, Email, Senha, Confirmar Senha
  - [ ] Validação de email duplicado funciona
  - [ ] Validação de senhas não coincidentes funciona
  - [ ] Novo usuário pode ser criado

- [ ] **Funcionalidade de Login**
  - [ ] Login com admin@irever.com / password123 funciona
  - [ ] Login com usuario1@example.com / password123 funciona
  - [ ] Token é armazenado no localStorage
  - [ ] Usuário é redirecionado para o dashboard
  - [ ] Logout funciona e limpa o token

### Dashboard Admin

- [ ] **Acesso**
  - [ ] Admin vê painel do administrador
  - [ ] Título mostra "Painel do Administrador"
  - [ ] Seção de "Nova Cobrança" é visível
  - [ ] Lista de todas as cobranças é exibida

- [ ] **Funcionalidades**
  - [ ] Formulário de nova cobrança funciona
  - [ ] Lista atualiza após criar cobrança
  - [ ] Campo de busca por placa funciona
  - [ ] Resultado da busca é exibido corretamente
  - [ ] Botão "Sair" funciona

### Dashboard Usuário

- [ ] **Acesso**
  - [ ] Usuário comum vê "Meus Estacionamentos"
  - [ ] Dashboard é diferente do painel admin
  - [ ] Título não mostra "Painel do Administrador"

- [ ] **Cobranças Pessoais**
  - [ ] Apenas cobranças do usuário são exibidas
  - [ ] Usuário 1 vê apenas suas cobranças
  - [ ] Usuário 2 vê apenas suas cobranças
  - [ ] Cobranças de outros usuários não aparecem

- [ ] **Busca por Placa**
  - [ ] Campo de busca é exibido
  - [ ] Busca pela placa do veículo do usuário funciona
  - [ ] Resultado da busca é exibido em card
  - [ ] Busca pela placa de outro usuário retorna erro
  - [ ] Limpar busca funciona corretamente

- [ ] **Cards de Cobrança**
  - [ ] Placa é exibida corretamente
  - [ ] Status da cobrança é mostrado com cor apropriada
  - [ ] Valor é formatado em reais
  - [ ] Data de vencimento é exibida
  - [ ] Datas de entrada e saída são mostradas
  - [ ] Botão "Comprovante" é exibido
  - [ ] Botão "Pagar" aparece apenas para cobranças pendentes

### Persistência

- [ ] **localStorage**
  - [ ] Token é armazenado após login
  - [ ] Usuário é armazenado após login
  - [ ] Dados são preservados ao recarregar a página
  - [ ] Dados são removidos após logout

- [ ] **Sessão**
  - [ ] Usuário permanece autenticado após refresh
  - [ ] Token inválido faz logout automático

## 🔍 Testes de API

### Teste de Login (Postman/Thunder Client)

```
POST http://localhost:8000/api/v1/auth/login
Content-Type: application/json

{
  "email": "admin@irever.com",
  "password": "password123"
}
```

Resposta esperada:
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Administrador",
    "email": "admin@irever.com",
    "role": "admin"
  },
  "token": "1|xxxxxxxxxxxxx..."
}
```

### Teste de Obter Cobranças (Autenticado)

```
GET http://localhost:8000/api/v1/charges
Authorization: Bearer {token_aqui}
```

Resposta esperada: Array de cobranças do usuário

### Teste de Busca por Placa

```
GET http://localhost:8000/api/v1/charges/plate/ABC-1234
Authorization: Bearer {token_aqui}
```

### Teste de Logout

```
POST http://localhost:8000/api/v1/auth/logout
Authorization: Bearer {token_aqui}
```

## 📊 Dados de Teste

### Usuários Criados

1. **Admin**
   - Email: admin@irever.com
   - Senha: password123
   - Veículos: Nenhum (admin)
   - Cobranças: Pode ver todas

2. **Usuário 1**
   - Email: usuario1@example.com
   - Senha: password123
   - Veículos: 2 (com placas geradas automaticamente)
   - Cobranças: 2-6 (dependendo dos stays)

3. **Usuário 2**
   - Email: usuario2@example.com
   - Senha: password123
   - Veículos: 2
   - Cobranças: 2-6

4. **Usuário 3**
   - Email: usuario3@example.com
   - Senha: password123
   - Veículos: 2
   - Cobranças: 2-6

## 🚨 Casos de Erro

### Erro: "Invalid credentials"
- [ ] Testar com email errado
- [ ] Testar com senha errada
- [ ] Mensagem de erro aparece

### Erro: "Email já registrado"
- [ ] Tentar registrar com email existente
- [ ] Mensagem de erro aparece

### Erro: "Token inválido"
- [ ] Deletar token do localStorage manualmente
- [ ] Tentar acessar endpoint protegido
- [ ] Usuário é redirecionado para login

### Erro: "Unauthorized"
- [ ] Tentar acessar API sem token
- [ ] Tentar acessar cobranças de outro usuário
- [ ] Erro 401 é retornado

## 📱 Testes de Responsividade

- [ ] Login funciona em desktop
- [ ] Login funciona em mobile
- [ ] Dashboard funciona em desktop
- [ ] Dashboard funciona em mobile
- [ ] Cards se adaptam ao tamanho da tela
- [ ] Formulários são usáveis em mobile

## 🔐 Testes de Segurança

- [ ] Token Bearer é usado nas requisições
- [ ] Senhas são hasheadas no banco de dados
- [ ] Senhas não aparecem nas respostas
- [ ] CORS bloqueia requisições não autorizadas
- [ ] Rotas protegidas retornam 401 sem autenticação
- [ ] Usuários não podem acessar cobranças de outros

## 📝 Notas

- Todos os testes devem ser executados com o backend e frontend rodando
- Use as credenciais de teste fornecidas
- Dados são regenerados a cada `php artisan migrate:fresh --seed`
- LocalStorage pode ser limpo no DevTools do navegador
