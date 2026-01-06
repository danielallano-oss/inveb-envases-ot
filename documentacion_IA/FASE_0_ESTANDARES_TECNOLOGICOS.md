# FASE 0: Estándares Tecnológicos Monitor One

## Objetivo
Definir el stack tecnológico estándar de Monitor One que será aplicado en la modernización del sistema INVEB Envases-OT.

---

## 1. Stack Backend

### 1.1 Lenguaje y Framework
| Componente | Tecnología | Versión |
|------------|------------|---------|
| Lenguaje | Python | 3.12+ |
| Framework Web | FastAPI | Latest |
| ORM | SQLModel | Latest |
| Servidor ASGI | Uvicorn | Latest |
| Validación | Pydantic | v2 |

### 1.2 Base de Datos
| Componente | Tecnología |
|------------|------------|
| Base de datos principal | PostgreSQL |
| Migraciones | Alembic |
| Cache | Redis (opcional) |

### 1.3 Estructura de Proyecto Microservicio
```
msw-{nombre-servicio}/
├── src/
│   ├── app/
│   │   ├── config/           # Configuración y variables de entorno
│   │   │   └── settings.py
│   │   ├── db/               # Conexión y configuración de BD
│   │   │   ├── database.py
│   │   │   └── sql/          # Scripts SQL puros si se requieren
│   │   ├── models/           # Modelos SQLModel (ORM)
│   │   │   └── __init__.py
│   │   ├── routers/          # Endpoints FastAPI (controladores)
│   │   │   └── __init__.py
│   │   ├── schemas/          # Schemas Pydantic (DTOs)
│   │   │   └── __init__.py
│   │   ├── services/         # Lógica de negocio
│   │   │   └── __init__.py
│   │   └── utilities/        # Helpers y utilidades
│   │       └── __init__.py
│   └── main.py               # Punto de entrada
├── tests/                    # Tests unitarios y de integración
│   └── __init__.py
├── k8s/                      # Manifiestos Kubernetes
│   ├── deployment.yaml
│   └── service.yaml
├── Dockerfile
├── docker-compose.yaml
├── pyproject.toml            # Dependencias (Poetry/pip)
├── requirements.txt
└── README.md
```

### 1.4 Patrón main.py
```python
from fastapi import FastAPI
from app.routers import ROUTERS

app = FastAPI(
    title="INVEB Envases OT API",
    description="Microservicio de Órdenes de Trabajo",
    version="1.0.0"
)

# Registrar routers
for router in ROUTERS:
    app.include_router(router, prefix="/api/v1")

if __name__ == "__main__":
    import uvicorn
    uvicorn.run("main:app", host="0.0.0.0", port=8000, reload=True)
```

### 1.5 Patrón de Modelo SQLModel
```python
from sqlmodel import SQLModel, Field
from typing import Optional
from datetime import datetime

class CascadeRuleBase(SQLModel):
    rule_code: str = Field(max_length=20, index=True)
    rule_name: str = Field(max_length=100)
    trigger_field: str = Field(max_length=50)
    target_field: str = Field(max_length=50)
    active: bool = Field(default=True)

class CascadeRule(CascadeRuleBase, table=True):
    __tablename__ = "cascade_rules"

    id: Optional[int] = Field(default=None, primary_key=True)
    created_at: datetime = Field(default_factory=datetime.utcnow)
    updated_at: datetime = Field(default_factory=datetime.utcnow)

class CascadeRuleCreate(CascadeRuleBase):
    pass

class CascadeRuleRead(CascadeRuleBase):
    id: int
```

### 1.6 Patrón de Router
```python
from fastapi import APIRouter, Depends, HTTPException
from sqlmodel import Session, select
from app.db.database import get_session
from app.models.cascade_rule import CascadeRule, CascadeRuleCreate, CascadeRuleRead

router = APIRouter(prefix="/cascade-rules", tags=["Cascade Rules"])

@router.get("/", response_model=list[CascadeRuleRead])
def list_rules(session: Session = Depends(get_session)):
    rules = session.exec(select(CascadeRule)).all()
    return rules

@router.post("/", response_model=CascadeRuleRead)
def create_rule(rule: CascadeRuleCreate, session: Session = Depends(get_session)):
    db_rule = CascadeRule.model_validate(rule)
    session.add(db_rule)
    session.commit()
    session.refresh(db_rule)
    return db_rule
```

---

## 2. Stack Frontend

### 2.1 Tecnologías
| Componente | Tecnología | Versión |
|------------|------------|---------|
| Framework | React | 18+ |
| Lenguaje | TypeScript | 5+ |
| Estilos | styled-components | Latest |
| Estado | Context API / Zustand | - |
| HTTP Client | Axios / Fetch | - |
| Build Tool | Vite | Latest |

### 2.2 Estructura de Proyecto Frontend
```
web-{nombre-app}/
├── src/
│   ├── assets/              # Imágenes, fuentes
│   ├── components/          # Componentes reutilizables
│   │   ├── common/
│   │   └── forms/
│   ├── config/              # Configuración
│   │   └── Colors.ts
│   ├── hooks/               # Custom hooks
│   ├── pages/               # Páginas/Vistas
│   ├── services/            # Llamadas API
│   ├── store/               # Estado global
│   ├── types/               # TypeScript types/interfaces
│   ├── utils/               # Utilidades
│   ├── App.tsx
│   └── main.tsx
├── public/
├── tests/
├── Dockerfile
├── package.json
├── tsconfig.json
└── vite.config.ts
```

### 2.3 Paleta de Colores Corporativa
```typescript
// src/config/Colors.ts
export const Colors = {
  // Primarios
  primary: '#003A81',           // Azul corporativo
  primaryLight: '#1565C0',
  primaryDark: '#002855',

  // Secundarios
  secondary: '#EC7126',         // Naranja
  secondaryLight: '#FF9149',
  secondaryDark: '#B85000',

  // Acento
  accent: '#05C1CA',            // Cyan

  // Neutros
  white: '#FFFFFF',
  background: '#F5F5F5',
  surface: '#FFFFFF',

  // Texto
  textPrimary: '#212121',
  textSecondary: '#757575',
  textDisabled: '#9E9E9E',

  // Estados
  success: '#4CAF50',
  warning: '#FF9800',
  error: '#F44336',
  info: '#2196F3',

  // Bordes
  border: '#E0E0E0',
  divider: '#EEEEEE'
};
```

### 2.4 Tipografía
- **Familia principal**: Poppins
- **Pesos**: 400 (Regular), 500 (Medium), 600 (SemiBold), 700 (Bold)
- **Tamaños base**:
  - h1: 2rem (32px)
  - h2: 1.5rem (24px)
  - h3: 1.25rem (20px)
  - body: 1rem (16px)
  - small: 0.875rem (14px)

---

## 3. Infraestructura

### 3.1 Containerización
| Componente | Tecnología |
|------------|------------|
| Containers | Docker |
| Orquestación | Kubernetes |
| Registry | Azure Container Registry |

### 3.2 Patrón Dockerfile Backend
```dockerfile
FROM python:3.12-slim

WORKDIR /app

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y --no-install-recommends \
    gcc \
    && rm -rf /var/lib/apt/lists/*

# Copiar requirements primero (cache de capas)
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copiar código fuente
COPY src/ ./src/

# Exponer puerto
EXPOSE 8000

# Comando de inicio
CMD ["uvicorn", "src.main:app", "--host", "0.0.0.0", "--port", "8000"]
```

### 3.3 CI/CD
| Componente | Tecnología |
|------------|------------|
| Pipelines | Azure Pipelines |
| IaC | Terraform |
| API Gateway | Kong |

---

## 4. Convenciones de Nombrado

### 4.1 Repositorios
| Tipo | Prefijo | Ejemplo |
|------|---------|---------|
| Microservicio Web | msw- | msw-envases-ot |
| Microservicio Mobile | msm- | msm-envases-ot |
| Worker/Background | worker- | worker-notifications |
| Paquete compartido | pkg- | pkg-common |
| Frontend Web | web- | web-envases-ot |

### 4.2 Código Python
- **Archivos/módulos**: snake_case (`cascade_rules.py`)
- **Clases**: PascalCase (`CascadeRule`)
- **Funciones/variables**: snake_case (`get_active_rules`)
- **Constantes**: UPPER_SNAKE_CASE (`MAX_RETRIES`)

### 4.3 Código TypeScript
- **Archivos componentes**: PascalCase (`CascadeForm.tsx`)
- **Archivos utils**: camelCase (`apiClient.ts`)
- **Interfaces/Types**: PascalCase con prefijo I opcional (`ICascadeRule` o `CascadeRule`)
- **Funciones/variables**: camelCase (`getCascadeRules`)

### 4.4 Base de Datos
- **Tablas**: snake_case plural (`cascade_rules`)
- **Columnas**: snake_case (`rule_code`)
- **Índices**: `idx_{tabla}_{columna}`
- **Foreign Keys**: `fk_{tabla_origen}_{tabla_destino}`
- **Unique**: `uk_{tabla}_{columnas}`

---

## 5. Seguridad

### 5.1 Autenticación
- JWT (JSON Web Tokens)
- OAuth 2.0 con Azure AD

### 5.2 Buenas Prácticas
- Variables de entorno para secretos
- HTTPS obligatorio
- CORS configurado por ambiente
- Rate limiting en API Gateway
- Validación de entrada con Pydantic

---

## 6. Testing

### 6.1 Backend
```python
# tests/test_cascade_rules.py
import pytest
from fastapi.testclient import TestClient
from src.main import app

client = TestClient(app)

def test_list_cascade_rules():
    response = client.get("/api/v1/cascade-rules/")
    assert response.status_code == 200
    assert isinstance(response.json(), list)
```

### 6.2 Cobertura Mínima
- Backend: 80%
- Frontend: 70%

---

## 7. Documentación

### 7.1 API
- OpenAPI/Swagger generado automáticamente por FastAPI
- Disponible en `/docs` (Swagger UI) y `/redoc` (ReDoc)

### 7.2 Código
- Docstrings en Python (Google style)
- JSDoc en TypeScript para funciones públicas

---

## 8. Migración desde Laravel

### 8.1 Mapeo de Conceptos

| Laravel | Python/FastAPI |
|---------|----------------|
| Route | Router |
| Controller | Router + Service |
| Model (Eloquent) | SQLModel |
| Migration | Alembic migration |
| Seeder | Script Python / Fixture |
| Request (Form Request) | Pydantic Schema |
| Middleware | FastAPI Middleware/Depends |
| Service Provider | Dependency Injection |
| Blade View | React Component |
| .env | .env + pydantic-settings |

### 8.2 Estrategia de Migración
1. **Fase de coexistencia**: APIs nuevas en FastAPI, frontend consume ambos
2. **Migración incremental**: Mover funcionalidad por módulos
3. **Cutover**: Desactivar endpoints Laravel cuando toda funcionalidad migrada

---

## Historial de Cambios

| Fecha | Versión | Descripción |
|-------|---------|-------------|
| 2025-12-19 | 1.0.0 | Documento inicial basado en estándares Monitor One |
