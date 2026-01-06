# FASE 4.2: Configuración - Setup Ambiente de Desarrollo

**ID**: `PASO-04.02-V2`
**Fecha**: 2025-12-19
**Estado**: Actualizado a estándares Monitor One

---

## Resumen

Configuración del ambiente de desarrollo para INVEB Envases-OT utilizando pydantic-settings según estándares Monitor One.

**Stack**: Python 3.12 + FastAPI + pydantic-settings

---

## 1. CONFIGURACIÓN GENERAL

### 1.1 Información del Proyecto

| Parámetro | Valor |
|-----------|-------|
| Nombre | INVEB Envases-OT |
| Framework | FastAPI |
| Python | 3.12+ |
| Base de Datos | PostgreSQL 15 |
| ORM | SQLModel |
| Timezone | America/Santiago |

### 1.2 Estructura de Ambientes

```
┌─────────────────────────────────────────────────────────────┐
│                    AMBIENTES INVEB                           │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  DESARROLLO (Docker)        PRODUCCIÓN (K8s)                │
│  ┌─────────────────┐        ┌─────────────────┐             │
│  │ inveb-api       │        │ Pod: api        │             │
│  │ :8000           │        │ Replicas: 3     │             │
│  └────────┬────────┘        └────────┬────────┘             │
│           │                          │                       │
│  ┌────────▼────────┐        ┌────────▼────────┐             │
│  │ PostgreSQL      │        │ Azure PostgreSQL│             │
│  │ :5432           │        │ Managed         │             │
│  └─────────────────┘        └─────────────────┘             │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

---

## 2. CONFIGURACIÓN CON PYDANTIC-SETTINGS

### 2.1 Archivo settings.py

```python
# src/app/config/settings.py
"""
Configuración del microservicio INVEB Envases OT.
Utiliza pydantic-settings para manejo de variables de entorno.
"""
from pydantic_settings import BaseSettings
from functools import lru_cache
from typing import List


class Settings(BaseSettings):
    """Configuración principal del microservicio."""

    # ═══════════════════════════════════════════════════════════
    # APLICACIÓN
    # ═══════════════════════════════════════════════════════════
    APP_NAME: str = "INVEB Envases OT API"
    APP_VERSION: str = "1.0.0"
    DEBUG: bool = False
    ENVIRONMENT: str = "development"

    # ═══════════════════════════════════════════════════════════
    # BASE DE DATOS
    # ═══════════════════════════════════════════════════════════
    DATABASE_URL: str = "postgresql://postgres:postgres@localhost:5432/inveb_envases"

    # Configuración de pool de conexiones
    DB_POOL_SIZE: int = 5
    DB_MAX_OVERFLOW: int = 10
    DB_POOL_PRE_PING: bool = True

    # ═══════════════════════════════════════════════════════════
    # CORS
    # ═══════════════════════════════════════════════════════════
    CORS_ORIGINS: List[str] = ["http://localhost:3000", "http://localhost:5173"]
    CORS_ALLOW_CREDENTIALS: bool = True
    CORS_ALLOW_METHODS: List[str] = ["*"]
    CORS_ALLOW_HEADERS: List[str] = ["*"]

    # ═══════════════════════════════════════════════════════════
    # API
    # ═══════════════════════════════════════════════════════════
    API_PREFIX: str = "/api/v1"

    # ═══════════════════════════════════════════════════════════
    # LOGGING
    # ═══════════════════════════════════════════════════════════
    LOG_LEVEL: str = "INFO"
    LOG_FORMAT: str = "%(asctime)s - %(name)s - %(levelname)s - %(message)s"

    # ═══════════════════════════════════════════════════════════
    # SEGURIDAD
    # ═══════════════════════════════════════════════════════════
    SECRET_KEY: str = "change-me-in-production"
    ACCESS_TOKEN_EXPIRE_MINUTES: int = 30

    class Config:
        env_file = ".env"
        case_sensitive = True
        extra = "ignore"


@lru_cache()
def get_settings() -> Settings:
    """Retorna instancia cacheada de settings."""
    return Settings()
```

### 2.2 Archivo __init__.py

```python
# src/app/config/__init__.py
from .settings import Settings, get_settings

__all__ = ["Settings", "get_settings"]
```

---

## 3. VARIABLES DE ENTORNO

### 3.1 Archivo .env.example

```env
# ═══════════════════════════════════════════════════════════════
# INVEB ENVASES OT - Variables de Entorno
# Copiar a .env y ajustar valores según ambiente
# ═══════════════════════════════════════════════════════════════

# ─────────────────────────────────────────────────────────────
# APLICACIÓN
# ─────────────────────────────────────────────────────────────
APP_NAME=INVEB Envases OT API
APP_VERSION=1.0.0
DEBUG=true
ENVIRONMENT=development

# ─────────────────────────────────────────────────────────────
# BASE DE DATOS (PostgreSQL)
# ─────────────────────────────────────────────────────────────
# Formato: postgresql://user:password@host:port/database
DATABASE_URL=postgresql://postgres:postgres@db:5432/inveb_envases

# Pool de conexiones
DB_POOL_SIZE=5
DB_MAX_OVERFLOW=10
DB_POOL_PRE_PING=true

# ─────────────────────────────────────────────────────────────
# CORS
# ─────────────────────────────────────────────────────────────
# Separar múltiples orígenes con coma
CORS_ORIGINS=http://localhost:3000,http://localhost:5173

# ─────────────────────────────────────────────────────────────
# API
# ─────────────────────────────────────────────────────────────
API_PREFIX=/api/v1

# ─────────────────────────────────────────────────────────────
# LOGGING
# ─────────────────────────────────────────────────────────────
LOG_LEVEL=INFO

# ─────────────────────────────────────────────────────────────
# SEGURIDAD (CAMBIAR EN PRODUCCIÓN)
# ─────────────────────────────────────────────────────────────
SECRET_KEY=change-me-in-production-use-strong-key
ACCESS_TOKEN_EXPIRE_MINUTES=30
```

### 3.2 Diferencias por Ambiente

| Variable | Desarrollo | Staging | Producción |
|----------|------------|---------|------------|
| DEBUG | true | false | false |
| ENVIRONMENT | development | staging | production |
| DATABASE_URL | @db:5432 | @staging-db | @prod-db |
| CORS_ORIGINS | localhost:* | staging.* | *.tecnoandina.com |
| LOG_LEVEL | DEBUG | INFO | WARNING |
| SECRET_KEY | dev-key | staging-key | **strong-random-key** |

---

## 4. CONEXIÓN A BASE DE DATOS

### 4.1 Archivo database.py

```python
# src/app/db/database.py
"""
Configuración de conexión a base de datos PostgreSQL.
Utiliza SQLModel con engine de SQLAlchemy.
"""
from sqlmodel import SQLModel, Session, create_engine
from app.config import get_settings

settings = get_settings()

# Crear engine de conexión
engine = create_engine(
    settings.DATABASE_URL,
    echo=settings.DEBUG,
    pool_pre_ping=settings.DB_POOL_PRE_PING,
    pool_size=settings.DB_POOL_SIZE,
    max_overflow=settings.DB_MAX_OVERFLOW
)


def create_db_and_tables():
    """Crea todas las tablas definidas en los modelos."""
    SQLModel.metadata.create_all(engine)


def get_session():
    """
    Dependency para obtener sesión de base de datos.
    Se usa con FastAPI Depends().
    """
    with Session(engine) as session:
        yield session
```

### 4.2 Uso en Routers

```python
# Ejemplo de uso en router
from fastapi import APIRouter, Depends
from sqlmodel import Session
from app.db import get_session

router = APIRouter()

@router.get("/items")
def list_items(session: Session = Depends(get_session)):
    # session está disponible automáticamente
    items = session.exec(select(Item)).all()
    return items
```

---

## 5. CONFIGURACIÓN DE LOGGING

### 5.1 Setup de Logging

```python
# src/app/config/logging.py
import logging
import sys
from app.config import get_settings

settings = get_settings()


def setup_logging():
    """Configura el logging de la aplicación."""
    logging.basicConfig(
        level=getattr(logging, settings.LOG_LEVEL),
        format=settings.LOG_FORMAT,
        handlers=[
            logging.StreamHandler(sys.stdout)
        ]
    )

    # Reducir verbosidad de librerías externas
    logging.getLogger("uvicorn.access").setLevel(logging.WARNING)
    logging.getLogger("sqlalchemy.engine").setLevel(
        logging.DEBUG if settings.DEBUG else logging.WARNING
    )


def get_logger(name: str) -> logging.Logger:
    """Obtiene un logger con el nombre especificado."""
    return logging.getLogger(name)
```

### 5.2 Uso en Código

```python
from app.config.logging import get_logger

logger = get_logger(__name__)

def process_data():
    logger.info("Procesando datos...")
    try:
        # ... lógica
        logger.debug("Detalle de procesamiento")
    except Exception as e:
        logger.error(f"Error procesando: {e}")
```

---

## 6. CONFIGURACIÓN CORS

### 6.1 Setup en main.py

```python
# src/main.py
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from app.config import get_settings

settings = get_settings()

app = FastAPI(
    title=settings.APP_NAME,
    version=settings.APP_VERSION
)

# Configurar CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=settings.CORS_ORIGINS,
    allow_credentials=settings.CORS_ALLOW_CREDENTIALS,
    allow_methods=settings.CORS_ALLOW_METHODS,
    allow_headers=settings.CORS_ALLOW_HEADERS,
)
```

---

## 7. VALIDACIÓN DE CONFIGURACIÓN

### 7.1 Script de Validación

```python
# scripts/validate_config.py
"""Valida que la configuración esté correcta."""
import sys
sys.path.insert(0, 'src')

from app.config import get_settings

def validate():
    print("Validando configuración...")

    try:
        settings = get_settings()
        print(f"✓ APP_NAME: {settings.APP_NAME}")
        print(f"✓ ENVIRONMENT: {settings.ENVIRONMENT}")
        print(f"✓ DEBUG: {settings.DEBUG}")
        print(f"✓ DATABASE_URL: {settings.DATABASE_URL[:30]}...")
        print(f"✓ CORS_ORIGINS: {settings.CORS_ORIGINS}")
        print(f"✓ API_PREFIX: {settings.API_PREFIX}")
        print("\n✅ Configuración válida!")
        return True
    except Exception as e:
        print(f"\n❌ Error de configuración: {e}")
        return False

if __name__ == "__main__":
    success = validate()
    sys.exit(0 if success else 1)
```

### 7.2 Ejecución

```bash
# Validar configuración
python scripts/validate_config.py

# O desde Docker
docker exec inveb-envases-ot-api python scripts/validate_config.py
```

---

## 8. VARIABLES SENSIBLES

### 8.1 Variables que NO deben estar en repositorio

| Variable | Descripción | Manejo |
|----------|-------------|--------|
| DATABASE_URL | URL completa con password | Secreto K8s |
| SECRET_KEY | Clave de encriptación | Secreto K8s |
| API_KEYS | Claves de API externas | Secreto K8s |

### 8.2 Manejo en Kubernetes

```yaml
# k8s/secrets.yaml (NO commitear valores reales)
apiVersion: v1
kind: Secret
metadata:
  name: inveb-envases-secrets
type: Opaque
stringData:
  DATABASE_URL: "postgresql://user:CHANGE_ME@host:5432/db"
  SECRET_KEY: "CHANGE_ME_STRONG_KEY"
```

### 8.3 Uso en Deployment

```yaml
# k8s/deployment.yaml
spec:
  containers:
    - name: api
      envFrom:
        - secretRef:
            name: inveb-envases-secrets
      env:
        - name: ENVIRONMENT
          value: "production"
```

---

## 9. DESARROLLO LOCAL

### 9.1 Setup Inicial

```bash
# 1. Clonar y navegar
cd msw-envases-ot

# 2. Crear entorno virtual
python -m venv venv
source venv/bin/activate  # Linux/Mac
venv\Scripts\activate     # Windows

# 3. Instalar dependencias
pip install -r requirements.txt

# 4. Configurar variables
cp .env.example .env
# Editar .env con valores locales

# 5. Validar configuración
python scripts/validate_config.py

# 6. Ejecutar migraciones
alembic upgrade head

# 7. Iniciar servidor
uvicorn src.main:app --reload
```

### 9.2 Con Docker

```bash
# 1. Configurar variables
cp .env.example .env

# 2. Levantar servicios
docker-compose up -d

# 3. Verificar
curl http://localhost:8000/health

# 4. Ver logs
docker-compose logs -f api
```

---

## 10. COMPARACIÓN CON SISTEMA LEGADO

| Aspecto | Laravel (Legado) | FastAPI (Monitor One) |
|---------|------------------|----------------------|
| Archivo config | .env | .env + pydantic-settings |
| Validación | Manual | Automática (Pydantic) |
| Tipado | No | Sí (Python type hints) |
| Cache config | config:cache | @lru_cache |
| Secretos | .env | K8s Secrets |
| Documentación | Manual | Auto-generada |

---

## 11. SIGUIENTE PASO

**FASE 4.3**: BD Iterativa - Migraciones con Alembic.

---

**Documento actualizado**: 2025-12-19
**Versión**: 2.0 (Migrado a estándares Monitor One)

### Historial de Cambios

| Versión | Fecha | Cambios |
|---------|-------|---------|
| 1.0 | 2025-12-17 | Versión inicial (Laravel) |
| 2.0 | 2025-12-19 | Migrado a Monitor One (pydantic-settings) |
