# Ir e Vir - Frontend

Frontend em Vue 3 + Vite + Tailwind CSS para o sistema de estacionamento Ir e Vir.

## 📋 Requisitos

- Node.js 16+
- npm ou yarn

## 🚀 Instalação e Execução

### 1. Instale as dependências
```bash
npm install
```

### 2. Inicie o servidor de desenvolvimento
```bash
npm run dev
```

A aplicação estará disponível em `http://localhost:5173`

### 3. Faça o build para produção
```bash
npm run build
```

## 📦 Estrutura do Projeto

```
src/
├── App.vue                 # Componente principal
├── main.js                 # Entrada da aplicação
└── components/
    ├── FormCobranca.vue    # Formulário para criar cobranças
    ├── ListaCobrancas.vue  # Lista de cobranças existentes
    └── BuscaPlaca.vue      # Busca de cobranças por placa
```

## ⚙️ Configuração da API

Certifique-se de que seu backend Laravel está rodando em `http://localhost:8000`

Se precisar alterar a URL da API, edite o arquivo `src/App.vue` e procure por:
```javascript
const apiURL = 'http://localhost:8000/api'
```

## 🎨 Funcionalidades

- ✅ **Criar Cobranças**: Formulário para registrar novas cobranças
- ✅ **Listar Cobranças**: Visualiza todas as cobranças registradas
- ✅ **Buscar por Placa**: Encontra cobranças pelo número da placa do veículo
- ✅ **Interface Responsiva**: Design moderno com Tailwind CSS
- ✅ **Integração com API**: Comunicação com backend Laravel via Axios

## 🔌 Endpoints da API Utilizados

- `GET /api/charges` - Lista todas as cobranças
- `POST /api/charges/{stay_id}` - Cria uma nova cobrança
- `GET /api/charges/{plate}` - Busca cobranças por placa

## 📝 Notas

- Certifique-se que o backend está configurado para aceitar CORS
- As datas são formatadas automaticamente para o padrão brasileiro (DD/MM/YYYY)

---

Desenvolvido com ❤️
