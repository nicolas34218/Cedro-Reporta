# API de Denúncias - Referência Completa

## Endpoints Implementados

### 1. LISTAR DENÚNCIAS
**GET** `/cidadao/denuncias`  
Route Name: `citizen.reports.index`

Lista todas as denúncias do usuário autenticado, paginadas (10 por página), mais recentes primeiro.

```
Response: View HTML (citizen.reports.index)
Requer: Autenticação
```

---

### 2. CRIAR DENÚNCIA
**GET** `/cidadao/denuncias/nova`  
Route Name: `citizen.reports.create`

Exibe formulário para criar nova denúncia.

```
Response: View HTML (citizen.reports.create)
Requer: Autenticação
```

**POST** `/cidadao/denuncias`  
Route Name: `citizen.reports.store`

Registra nova denúncia no sistema.

```
Body (form-data):
- title: string (obrigatório)
- description: string (min: 10 chars)
- category: string
- address_reference: string (opcional)
- district: string (opcional)

Response: Redirect para citizen.reports.index com mensagem de sucesso
Requer: Autenticação
Validação: ReportRequest
```

---

### 3. VISUALIZAR DETALHES
**GET** `/cidadao/denuncias/{report}`  
Route Name: `citizen.reports.show`

Exibe página completa com detalhes da denúncia.

```
Params:
- report: ID da denúncia (model binding)

Response: View HTML (citizen.reports.show)
Requer: Autenticação + Autorização (ReportPolicy::view)
Dados: 
- report (modelo completo)
- reportFormatted (dados formatados com datas em pt-BR)
```

**GET** `/cidadao/denuncias/{report}/detalhes`  
Route Name: `citizen.reports.details`

Endpoint API para obter detalhes da denúncia em JSON.

```
Params:
- report: ID da denúncia (model binding)

Response (JSON):
{
  "success": true,
  "message": "Detalhes da denúncia obtidos com sucesso.",
  "data": {
    "report": {
      "id": int,
      "title": string,
      "description": string,
      "category": string,
      "status": string,
      "status_config": {
        "color": hex,
        "icon": emoji,
        "description": string
      },
      "location": string|null,
      "created_at": "d/m/Y H:i",
      "created_at_human": "2 horas atrás",
      "updated_at": "d/m/Y H:i"
    },
    "user": {
      "id": int,
      "name": string,
      "email": string
    },
    "full_data": {
      "image_path": string|null,
      "created_at_iso": ISO8601,
      "updated_at_iso": ISO8601,
      "timestamp_created": unix_timestamp
    }
  }
}

Requer: Autenticação + Autorização (ReportPolicy::view)
```

---

### 4. FILTRAR DENÚNCIAS
**GET** `/cidadao/denuncias/buscar`  
Route Name: `citizen.reports.search`

Página HTML com formulário de filtros.

```
Query Params (opcionais):
- category: string
- location: string
- status: string

Response: View HTML (citizen.reports.search)
Requer: Autenticação
Dados:
- reports (denúncias filtradas, paginadas)
- filters (filtros aplicados)
- categories (categorias disponíveis)
- statuses (status disponíveis)
```

**GET** `/cidadao/denuncias/api/filtrar`  
Route Name: `citizen.reports.filter`

Endpoint API para filtrar denúncias com suporte a múltiplos filtros combinados.

```
Query Params (opcionais):
- category: string
- location: string (busca parcial com LIKE)
- status: string
- per_page: integer (1-100, padrão: 10)

Response (JSON):
{
  "success": true,
  "message": "Filtros aplicados com sucesso.",
  "data": {
    "reports": [
      {
        "id": int,
        "title": string,
        "category": string,
        "status": string,
        "location": string|null,
        "created_at": "d/m/Y H:i",
        "created_at_human": string,
        "updated_at": "d/m/Y H:i",
        "status_config": {...}
      }
    ],
    "pagination": {
      "total": int,
      "per_page": int,
      "current_page": int,
      "last_page": int,
      "from": int,
      "to": int
    },
    "filters_applied": {
      "category": string|null,
      "location": string|null,
      "status": string|null
    }
  }
}

Requer: Autenticação
Validação: category (nullable), location (nullable, min:1), status (nullable)
```

**Exemplos de Requisição:**
```
# Filtrar por categoria
GET /cidadao/denuncias/api/filtrar?category=Infraestrutura

# Filtrar por localização
GET /cidadao/denuncias/api/filtrar?location=Centro

# Filtrar por status
GET /cidadao/denuncias/api/filtrar?status=Pendente

# Combinar filtros
GET /cidadao/denuncias/api/filtrar?category=Infraestrutura&status=Pendente&location=Centro

# Com paginação
GET /cidadao/denuncias/api/filtrar?category=Infraestrutura&per_page=20
```

---

### 5. ACOMPANHAR STATUS
**GET** `/cidadao/denuncias/{report}/status`  
Route Name: `citizen.reports.track-status`

Página para acompanhamento visual do status da denúncia.

```
Params:
- report: ID da denúncia

Response: View HTML (citizen.reports.track-status)
Requer: Autenticação + Autorização (ReportPolicy::track)
```

---

## Categorias Disponíveis
- Infraestrutura
- Trânsito
- Limpeza Urbana
- Segurança Pública
- Saúde
- Educação
- Iluminação
- Outro

## Status Disponíveis
- Pendente (⏳ amarelo)
- Em Análise (🔍 azul)
- Resolvida (✓ verde)
- Fechada (✕ vermelho)

## Formatação de Datas
- **HTML**: `d/m/Y H:i` (ex: 23/04/2024 14:30)
- **Humano**: `diffForHumans()` (ex: "2 horas atrás")
- **ISO8601**: Para APIs/storage (ex: "2024-04-23T14:30:00Z")
- **Timestamp**: Unix timestamp para cálculos

## Autenticação e Autorização
- **Middleware**: `auth` (obrigatório em todas as rotas)
- **Policy**: `ReportPolicy` para autorização de acesso
  - Um usuário só vê suas próprias denúncias
  - Exceções: Admin/Secretário (não implementado aqui)

## Segurança
- ✅ CSRF protection (middleware)
- ✅ User isolation (Policy + Query scope)
- ✅ Input validation
- ✅ SQL injection prevention (Eloquent)
- ✅ Rate limiting (pode ser adicionado)

## Performance
- ✅ Eager loading com `load()`
- ✅ Query builder com scopes
- ✅ Paginação para grandes conjuntos
- ✅ Índices recomendados:
  - `reports.user_id`
  - `reports.category`
  - `reports.status`
  - `reports.created_at`
