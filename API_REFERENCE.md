# API - Ir e Vir Estacionamento

## Base URL
```
http://localhost:8000/api/v1
```

## CORS
A API está configurada para aceitar requisições de:
- http://localhost:3000
- http://localhost:5173
- http://127.0.0.1:5173

## Endpoints

### 1. Listar todas as cobranças
```
GET /charges
```

**Resposta (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "stay_id": 1,
      "amount": "50.00",
      "status": "pending",
      "created_at": "2026-03-17T16:45:14Z",
      "updated_at": "2026-03-17T16:45:14Z"
    }
  ]
}
```

---

### 2. Gerar nova cobrança
```
POST /charges/{stay_id}
```

**Parâmetros:**
- `stay_id` (path): ID da permanência (obrigatório)

**Corpo da requisição:**
```json
{}
```

**Resposta (201 Created):**
```json
{
  "data": {
    "id": 1,
    "stay_id": 1,
    "amount": "50.00",
    "status": "pending",
    "created_at": "2026-03-17T16:45:14Z",
    "updated_at": "2026-03-17T16:45:14Z"
  }
}
```

**Erros:**
- **400 Bad Request**: Erro ao gerar cobrança (Stay não encontrada, erro no cálculo, etc)
```json
{
  "message": "Erro ao tentar gerar cobrança",
  "errors": "Stay not found"
}
```

---

### 3. Buscar cobrança por placa do veículo
```
GET /charges/plate/{plate}
```

**Parâmetros:**
- `plate` (path): Placa do veículo (ex: ABC1234)

**Resposta (200 OK):**
```json
{
  "data": {
    "id": 1,
    "stay_id": 1,
    "amount": "50.00",
    "vehicle": {
      "plate": "ABC1234",
      "model": "Honda Civic"
    },
    "status": "pending",
    "created_at": "2026-03-17T16:45:14Z"
  }
}
```

**Erros:**
- **404 Not Found**: Nenhuma cobrança encontrada para essa placa
```json
{
  "message": "Charge not found for plate ABC1234"
}
```

---

## Exemplo de Uso com Axios (Vue.js)

```javascript
import axios from 'axios'

const apiURL = 'http://localhost:8000/api/v1'

// 1. Listar cobranças
async function listarCobracas() {
  try {
    const response = await axios.get(`${apiURL}/charges`)
    console.log(response.data.data)
  } catch (error) {
    console.error('Erro:', error.response?.data?.message || error.message)
  }
}

// 2. Gerar cobrança
async function gerarCobranca(stayId) {
  try {
    const response = await axios.post(`${apiURL}/charges/${stayId}`, {})
    console.log('Cobrança criada:', response.data.data)
  } catch (error) {
    console.error('Erro:', error.response?.data?.errors || error.message)
  }
}

// 3. Buscar por placa
async function buscarPorPlaca(placa) {
  try {
    const response = await axios.get(`${apiURL}/charges/plate/${placa}`)
    console.log('Resultado:', response.data.data)
  } catch (error) {
    console.error('Placa não encontrada:', error.response?.data?.message)
  }
}
```

---

## Estrutura de Response

### Sucesso (2xx)
Todos os endpoints retornam dados dentro de `response.data.data` ou `response.data`

### Erro (4xx/5xx)
```json
{
  "message": "Descrição do erro",
  "errors": "Detalhes adicionais do erro (quando aplicável)"
}
```

---

## Mudanças Realizadas

### Backend
1. ✅ CORS configurado para aceitar frontend (porta 5173 e 3000)
2. ✅ Rotas reorganizadas com prefix `/v1` e rota específica `/charges/plate/{plate}`
3. ✅ Middleware StatefulAPI ativado

### Frontend
1. ✅ URLs atualizadas para usar `/api/v1`
2. ✅ FormCobranca simplificado (apenas pede Stay ID)
3. ✅ BuscaPlaca melhorada com melhor formatação de resultado
4. ✅ Tratamento de erros aprimorado em todas as requisições
