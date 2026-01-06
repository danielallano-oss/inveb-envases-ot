# FASE 4.1: Docker - Configuración de Contenedores

**ID**: `PASO-04.01-V2`
**Fecha**: 2025-12-19
**Estado**: Actualizado a estándares Monitor One

---

## Resumen

Configuración Docker del microservicio INVEB Envases-OT siguiendo estándares Monitor One de Tecnoandina.

**Stack**: Python 3.12 + FastAPI + PostgreSQL 15

---

## 1. ARQUITECTURA DOCKER

### 1.1 Diagrama de Servicios

```
┌─────────────────────────────────────────────────────────────┐
│                    DOCKER COMPOSE                            │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌─────────────────────┐    ┌─────────────────────┐        │
│  │  inveb-envases-api  │    │  inveb-envases-db   │        │
│  │                     │    │                     │        │
│  │  Python 3.12        │───▶│   PostgreSQL 15     │        │
│  │  FastAPI + Uvicorn  │    │                     │        │
│  │                     │    │  DB: inveb_envases  │        │
│  │  Puerto: 8000:8000  │    │  Puerto: 5432:5432  │        │
│  └─────────────────────┘    └─────────────────────┘        │
│            │                          │                     │
│            └──────────┬───────────────┘                     │
│                       │                                     │
│              inveb-network (bridge)                         │
│                                                              │
│  Volumes:                                                   │
│  - postgres_data:/var/lib/postgresql/data                  │
│  - ./src:/app/src (desarrollo)                             │
└─────────────────────────────────────────────────────────────┘
```

### 1.2 Servicios

| Servicio | Contenedor | Imagen | Puerto Host | Puerto Container |
|----------|------------|--------|-------------|------------------|
| api | inveb-envases-ot-api | python:3.12-slim (build) | 8000 | 8000 |
| db | inveb-envases-ot-db | postgres:15-alpine | 5432 | 5432 |
| pgadmin | inveb-pgadmin | dpage/pgadmin4 | 5050 | 80 |

---

## 2. CONFIGURACIÓN DOCKER-COMPOSE

### 2.1 docker-compose.yaml

```yaml
# INVEB Envases OT - Docker Compose
# Stack: Monitor One (Python + FastAPI + PostgreSQL)

version: '3.8'

services:
  api:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: inveb-envases-ot-api
    ports:
      - "8000:8000"
    environment:
      - DATABASE_URL=postgresql://postgres:postgres@db:5432/inveb_envases
      - DEBUG=true
      - ENVIRONMENT=development
      - CORS_ORIGINS=http://localhost:3000,http://localhost:5173
    depends_on:
      db:
        condition: service_healthy
    volumes:
      - ./src:/app/src:ro
    networks:
      - inveb-network
    restart: unless-stopped
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:8000/health"]
      interval: 30s
      timeout: 10s
      retries: 3

  db:
    image: postgres:15-alpine
    container_name: inveb-envases-ot-db
    ports:
      - "5432:5432"
    environment:
      - POSTGRES_USER=postgres
      - POSTGRES_PASSWORD=postgres
      - POSTGRES_DB=inveb_envases
    volumes:
      - postgres_data:/var/lib/postgresql/data
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U postgres"]
      interval: 5s
      timeout: 5s
      retries: 5
    networks:
      - inveb-network
    restart: unless-stopped

  # Opcional: PgAdmin para administración visual
  pgadmin:
    image: dpage/pgadmin4:latest
    container_name: inveb-pgadmin
    ports:
      - "5050:80"
    environment:
      - PGADMIN_DEFAULT_EMAIL=admin@tecnoandina.com
      - PGADMIN_DEFAULT_PASSWORD=admin
    depends_on:
      - db
    networks:
      - inveb-network
    profiles:
      - tools

volumes:
  postgres_data:

networks:
  inveb-network:
    driver: bridge
```

### 2.2 Dockerfile

```dockerfile
# INVEB Envases OT - Microservice Dockerfile
# Stack: Python 3.12 + FastAPI + SQLModel

FROM python:3.12-slim

# Metadata
LABEL maintainer="Tecnoandina"
LABEL description="INVEB Envases OT Microservice"
LABEL version="1.0.0"

# Variables de entorno
ENV PYTHONDONTWRITEBYTECODE=1
ENV PYTHONUNBUFFERED=1
ENV PYTHONPATH=/app/src

WORKDIR /app

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y --no-install-recommends \
    gcc \
    libpq-dev \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Copiar requirements primero (cache de capas Docker)
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copiar código fuente
COPY src/ ./src/
COPY alembic/ ./alembic/
COPY alembic.ini .

# Exponer puerto
EXPOSE 8000

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:8000/health || exit 1

# Comando de inicio
CMD ["uvicorn", "src.main:app", "--host", "0.0.0.0", "--port", "8000"]
```

### 2.3 Dockerfile.dev (Desarrollo con hot-reload)

```dockerfile
# Dockerfile para desarrollo con hot-reload
FROM python:3.12-slim

ENV PYTHONDONTWRITEBYTECODE=1
ENV PYTHONUNBUFFERED=1
ENV PYTHONPATH=/app/src

WORKDIR /app

RUN apt-get update && apt-get install -y --no-install-recommends \
    gcc libpq-dev curl \
    && rm -rf /var/lib/apt/lists/*

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# En desarrollo, el código se monta como volumen
EXPOSE 8000

# Hot-reload habilitado
CMD ["uvicorn", "src.main:app", "--host", "0.0.0.0", "--port", "8000", "--reload"]
```

---

## 3. VARIABLES DE ENTORNO

### 3.1 Archivo .env

```env
# ═══════════════════════════════════════════════════════════════
# APLICACIÓN
# ═══════════════════════════════════════════════════════════════
APP_NAME=INVEB Envases OT API
APP_VERSION=1.0.0
DEBUG=true
ENVIRONMENT=development

# ═══════════════════════════════════════════════════════════════
# BASE DE DATOS (PostgreSQL)
# ═══════════════════════════════════════════════════════════════
DATABASE_URL=postgresql://postgres:postgres@db:5432/inveb_envases

# ═══════════════════════════════════════════════════════════════
# CORS
# ═══════════════════════════════════════════════════════════════
CORS_ORIGINS=http://localhost:3000,http://localhost:5173

# ═══════════════════════════════════════════════════════════════
# API
# ═══════════════════════════════════════════════════════════════
API_PREFIX=/api/v1
```

### 3.2 Diferencias por Ambiente

| Variable | Desarrollo | Producción |
|----------|------------|------------|
| DEBUG | true | false |
| ENVIRONMENT | development | production |
| DATABASE_URL | postgresql://...@db:5432/... | postgresql://...@prod-host/... |
| CORS_ORIGINS | localhost:3000,5173 | dominio.tecnoandina.com |

---

## 4. COMANDOS DE OPERACIÓN

### 4.1 Comandos Básicos

```bash
# Navegar al directorio del microservicio
cd msw-envases-ot

# Construir imágenes
docker-compose build

# Iniciar servicios
docker-compose up -d

# Ver logs
docker-compose logs -f api

# Detener servicios
docker-compose down

# Detener y eliminar volúmenes
docker-compose down -v
```

### 4.2 Acceso a Contenedores

```bash
# Acceder al contenedor de la API
docker exec -it inveb-envases-ot-api bash

# Acceder a PostgreSQL
docker exec -it inveb-envases-ot-db psql -U postgres -d inveb_envases

# Ejecutar migraciones Alembic
docker exec -it inveb-envases-ot-api alembic upgrade head

# Ver estado de migraciones
docker exec -it inveb-envases-ot-api alembic current

# Ejecutar seeder
docker exec -it inveb-envases-ot-api python scripts/seed_cascade_rules.py
```

### 4.3 Backup de Base de Datos

```bash
# Crear backup
docker exec inveb-envases-ot-db pg_dump -U postgres inveb_envases > backup_$(date +%Y%m%d_%H%M%S).sql

# Restaurar backup
docker exec -i inveb-envases-ot-db psql -U postgres inveb_envases < backup.sql
```

---

## 5. PLAN DE ROLLBACK

### 5.1 Escenarios de Rollback

| Escenario | Trigger | Acción |
|-----------|---------|--------|
| Build falla | Error en Dockerfile | Usar imagen anterior |
| API no inicia | Error de configuración | Revisar logs, revertir .env |
| BD corrupta | Error en migración | Restaurar backup, revertir Alembic |
| Red no funciona | Problema de network | Recrear network |

### 5.2 Script de Rollback

```bash
#!/bin/bash
# rollback.sh - Script de rollback para INVEB Docker

echo "=== INICIANDO ROLLBACK ==="

# 1. Detener servicios
echo "[1/5] Deteniendo servicios..."
docker-compose down

# 2. Restaurar backup de BD
if [ -f "backup_latest.sql" ]; then
    echo "[2/5] Restaurando backup de BD..."
    docker-compose up -d db
    sleep 10
    docker exec -i inveb-envases-ot-db psql -U postgres inveb_envases < backup_latest.sql
else
    echo "[2/5] No hay backup disponible, saltando..."
fi

# 3. Revertir última migración Alembic
echo "[3/5] Revirtiendo migración..."
docker-compose up -d api
sleep 5
docker exec inveb-envases-ot-api alembic downgrade -1

# 4. Restaurar configuración anterior
if [ -f ".env.backup" ]; then
    echo "[4/5] Restaurando .env..."
    cp .env.backup .env
fi

# 5. Reiniciar servicios
echo "[5/5] Reiniciando servicios..."
docker-compose down
docker-compose up -d

echo "=== ROLLBACK COMPLETADO ==="
docker-compose ps
```

### 5.3 Comandos de Emergencia

```bash
# Rollback rápido - solo reiniciar
docker-compose restart

# Rollback medio - recrear contenedores
docker-compose down && docker-compose up -d

# Rollback de migración Alembic
docker exec inveb-envases-ot-api alembic downgrade -1

# Rollback completo - eliminar todo y reconstruir
docker-compose down -v
docker system prune -f
docker-compose build --no-cache
docker-compose up -d
```

---

## 6. VERIFICACIÓN DE AMBIENTE

### 6.1 Checklist de Verificación

```bash
# 1. Verificar contenedores corriendo
docker-compose ps
# Esperado: api (Up, healthy), db (Up, healthy)

# 2. Verificar health endpoint
curl http://localhost:8000/health
# Esperado: {"status": "ok"}

# 3. Verificar documentación API
curl -I http://localhost:8000/docs
# Esperado: HTTP/1.1 200 OK

# 4. Verificar conexión a BD
docker exec inveb-envases-ot-api python -c "
from app.db import engine
from sqlmodel import text
with engine.connect() as conn:
    result = conn.execute(text('SELECT 1'))
    print('Conexión exitosa!')
"

# 5. Verificar migraciones
docker exec inveb-envases-ot-api alembic current
# Esperado: Head revision ID
```

### 6.2 URLs de Acceso

| Servicio | URL | Descripción |
|----------|-----|-------------|
| API | http://localhost:8000 | Health check |
| Swagger UI | http://localhost:8000/docs | Documentación interactiva |
| ReDoc | http://localhost:8000/redoc | Documentación alternativa |
| OpenAPI JSON | http://localhost:8000/openapi.json | Especificación OpenAPI |
| PgAdmin | http://localhost:5050 | Administración BD (opcional) |

---

## 7. ESTRUCTURA DE ARCHIVOS

```
msw-envases-ot/
├── src/
│   ├── app/
│   │   ├── config/
│   │   │   ├── __init__.py
│   │   │   └── settings.py
│   │   ├── db/
│   │   │   ├── __init__.py
│   │   │   └── database.py
│   │   ├── models/
│   │   ├── routers/
│   │   ├── schemas/
│   │   ├── services/
│   │   └── utilities/
│   └── main.py
├── tests/
├── alembic/
│   ├── versions/
│   └── env.py
├── scripts/
│   └── seed_cascade_rules.py
├── k8s/
├── Dockerfile
├── Dockerfile.dev
├── docker-compose.yaml
├── alembic.ini
├── requirements.txt
├── .env.example
└── README.md
```

---

## 8. COMPARACIÓN CON SISTEMA LEGADO

| Aspecto | Sistema Legado | Monitor One |
|---------|----------------|-------------|
| Lenguaje | PHP 7.4 | Python 3.12 |
| Framework | Laravel 5.8 | FastAPI |
| Servidor | Apache | Uvicorn |
| Base de datos | MySQL 8.0 | PostgreSQL 15 |
| ORM | Eloquent | SQLModel |
| Migraciones | Laravel migrations | Alembic |
| Puerto API | 8080 | 8000 |
| Puerto BD | 3307 | 5432 |

---

## 9. SIGUIENTE PASO

**FASE 4.2**: Configuración - Setup de ambiente con pydantic-settings.

---

**Documento actualizado**: 2025-12-19
**Versión**: 2.0 (Migrado a estándares Monitor One)

### Historial de Cambios

| Versión | Fecha | Cambios |
|---------|-------|---------|
| 1.0 | 2025-12-17 | Versión inicial (Laravel/PHP) |
| 2.0 | 2025-12-19 | Migrado a Monitor One (Python/FastAPI) |
