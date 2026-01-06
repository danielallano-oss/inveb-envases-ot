# FASE 5.6: Implementación de Microservicio - MS-004 CascadeService

**ID**: `PASO-05.06-V2`
**Fecha**: 2025-12-19
**Estado**: Código creado, listo para ejecutar

---

## Resumen

Implementacion del **MS-004 CascadeService** - el primer microservicio INVEB siguiendo estandares Monitor One.

| Aspecto | Valor |
|---------|-------|
| Servicio | MS-004 CascadeService |
| Ubicacion | `msw-envases-ot/` |
| Stack | Python 3.12 + FastAPI + SQLModel + PostgreSQL |
| Documentacion API | `http://localhost:8000/docs` (cuando ejecute) |

### Documentos Relacionados

- **[FASE_5_1](FASE_5_1_DEFINICION_SERVICIOS.md)**: Definicion conceptual de servicios (MS-001 a MS-010)
- **[FASE_5_2](FASE_5_2_ESPECIFICACION_API.md)**: Especificacion de endpoints API
- **[FASE_4_1](FASE_4_1_DOCKER.md)**: Configuracion Docker
- **[FASE_4_3](FASE_4_3_BD_ITERATIVA.md)**: Migraciones Alembic

---

## 1. Stack Tecnológico

| Componente | Tecnología | Versión |
|------------|------------|---------|
| Lenguaje | Python | 3.12+ |
| Framework | FastAPI | 0.109.0 |
| ORM | SQLModel | 0.0.14 |
| Base de datos | PostgreSQL | 15 |
| Migraciones | Alembic | 1.13.1 |
| Servidor | Uvicorn | 0.27.0 |

**Referencia**: `FASE_0_ESTANDARES_TECNOLOGICOS.md`

---

## 2. Estructura del Microservicio

```
msw-envases-ot/
├── src/
│   ├── app/
│   │   ├── config/
│   │   │   ├── __init__.py
│   │   │   └── settings.py        # Configuración pydantic-settings
│   │   ├── db/
│   │   │   ├── __init__.py
│   │   │   └── database.py        # Conexión PostgreSQL
│   │   ├── models/
│   │   │   ├── __init__.py
│   │   │   ├── cascade_rule.py              # Reglas de cascada
│   │   │   ├── cascade_valid_combination.py # Combinaciones válidas
│   │   │   ├── cascade_combination_planta.py
│   │   │   ├── coverage_internal_planta.py  # Pivote normalizado
│   │   │   └── carton_planta.py             # Pivote normalizado
│   │   ├── routers/
│   │   │   ├── __init__.py
│   │   │   ├── cascade_rules.py       # CRUD reglas
│   │   │   └── cascade_combinations.py # CRUD combinaciones
│   │   ├── schemas/
│   │   ├── services/
│   │   └── utilities/
│   └── main.py                    # Punto de entrada FastAPI
├── tests/
├── alembic/
│   ├── versions/
│   │   └── 001_create_cascade_tables.py
│   ├── env.py
│   └── script.py.mako
├── scripts/
│   └── seed_cascade_rules.py      # Datos iniciales
├── k8s/
├── Dockerfile
├── docker-compose.yaml
├── alembic.ini
├── requirements.txt
├── .env.example
└── README.md
```

---

## 3. Modelos SQLModel

### 3.1 CascadeRule
```python
class CascadeRule(SQLModel, table=True):
    __tablename__ = "cascade_rules"

    id: int (PK)
    rule_code: str          # CASC-001, EXCEP-001
    rule_name: str
    trigger_field: str      # Campo que dispara
    trigger_table: str
    target_field: str       # Campo afectado
    target_table: str
    action: str             # enable, disable, setValue
    condition_type: str     # hasValue, equals, in
    condition_value: str    # JSON
    reset_fields: str       # JSON array
    validation_endpoint: str
    cascade_order: int
    form_context: str       # ot
    description: str
    active: bool
    created_at, updated_at
```

### 3.2 CascadeValidCombination
```python
class CascadeValidCombination(SQLModel, table=True):
    __tablename__ = "cascade_valid_combinations"

    id: int (PK)
    product_type_id: int    # FK product_types
    impresion: str          # flexo, offset, sinImpresion
    fsc: str                # fsc, noFsc, mixto
    active: bool
    notes: str
    plantas: List[CascadeCombinationPlanta]  # Relationship
```

### 3.3 Tablas Pivote Normalizadas
- `coverage_internal_planta`: Reemplaza campo multi-valor
- `carton_planta`: Reemplaza campo multi-valor

---

## 4. Endpoints API

### 4.1 Cascade Rules

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/v1/cascade-rules/` | Listar reglas |
| GET | `/api/v1/cascade-rules/{id}` | Obtener por ID |
| GET | `/api/v1/cascade-rules/code/{code}` | Obtener por código |
| GET | `/api/v1/cascade-rules/trigger/{field}` | Reglas por trigger |
| POST | `/api/v1/cascade-rules/` | Crear regla |
| PATCH | `/api/v1/cascade-rules/{id}` | Actualizar |
| DELETE | `/api/v1/cascade-rules/{id}` | Eliminar |

### 4.2 Cascade Combinations

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/v1/cascade-combinations/` | Listar combinaciones |
| GET | `/api/v1/cascade-combinations/{id}` | Obtener con plantas |
| GET | `/api/v1/cascade-combinations/validate/` | Validar combinación |
| POST | `/api/v1/cascade-combinations/` | Crear combinación |
| DELETE | `/api/v1/cascade-combinations/{id}` | Eliminar |
| GET | `/api/v1/cascade-combinations/{id}/plantas` | Plantas de combinación |
| POST | `/api/v1/cascade-combinations/{id}/plantas` | Agregar planta |
| DELETE | `/api/v1/cascade-combinations/{id}/plantas/{pid}` | Quitar planta |

---

## 5. Reglas de Cascada Implementadas

### 5.1 Reglas Principales (CASC-*)

| Código | Trigger | Target | Orden |
|--------|---------|--------|-------|
| CASC-001 | product_type_id | impresion | 1 |
| CASC-002 | impresion | fsc | 2 |
| CASC-003 | fsc | cinta | 3 |
| CASC-004 | cinta | coverage_internal_id | 4 |
| CASC-005 | coverage_internal_id | coverage_external_id | 5 |
| CASC-006 | coverage_external_id | planta_id | 6 |
| CASC-007 | planta_id | carton_color | 7 |
| CASC-008 | carton_color | carton_id | 8 |

### 5.2 Excepciones (EXCEP-*)

| Código | Condición | Acción |
|--------|-----------|--------|
| EXCEP-001 | tipo_solicitud=3 + rol Vendedor | coverage_internal=N/A |
| EXCEP-002 | tipo_solicitud=3 + rol Vendedor | coverage_external=N/A |

---

## 6. Migración de Datos

### 6.1 Alembic Migration

```bash
# Ejecutar migraciones
alembic upgrade head

# Crear nueva migración
alembic revision --autogenerate -m "descripcion"

# Rollback
alembic downgrade -1
```

### 6.2 Seeder de Datos Iniciales

```bash
# Poblar reglas de cascada
python scripts/seed_cascade_rules.py
```

---

## 7. Ejecución

### 7.1 Docker Compose (Recomendado)

```bash
cd msw-envases-ot
docker-compose up -d

# API: http://localhost:8000
# Docs: http://localhost:8000/docs
# DB: localhost:5432
```

### 7.2 Desarrollo Local

```bash
# Instalar dependencias
pip install -r requirements.txt

# Configurar .env
cp .env.example .env

# Migraciones
alembic upgrade head

# Datos iniciales
python scripts/seed_cascade_rules.py

# Ejecutar
uvicorn src.main:app --reload
```

---

## 8. Mapeo Laravel -> Python

Esta implementación reemplaza los siguientes archivos Laravel que fueron eliminados:

| Archivo Laravel (Eliminado) | Archivo Python (Nuevo) |
|-----------------------------|------------------------|
| `database/migrations/2025_12_19_000001_create_cascade_rules_table.php` | `alembic/versions/001_create_cascade_tables.py` |
| `database/migrations/2025_12_19_000002_create_cascade_valid_combinations_table.php` | (incluido en 001) |
| `database/migrations/2025_12_19_000003_create_cascade_combination_plantas_table.php` | (incluido en 001) |
| `database/migrations/2025_12_19_000004_create_coverage_internal_planta_table.php` | (incluido en 001) |
| `database/migrations/2025_12_19_000005_create_carton_planta_table.php` | (incluido en 001) |
| `database/seeds/CascadeRulesTableSeeder.php` | `scripts/seed_cascade_rules.py` |
| `database/seeds/CoverageInternalPlantaSeeder.php` | (pendiente migración datos) |
| `database/seeds/CartonPlantaSeeder.php` | (pendiente migración datos) |

---

## 9. Comandos de Ejecución

```bash
# Navegar al microservicio
cd msw-envases-ot

# Levantar servicios (API + PostgreSQL)
docker-compose up -d

# Verificar servicios
docker-compose ps

# Ejecutar migraciones
docker exec inveb-envases-ot-api alembic upgrade head

# Cargar datos iniciales
docker exec inveb-envases-ot-api python scripts/seed_cascade_rules.py

# Verificar API
curl http://localhost:8000/health
curl http://localhost:8000/docs
```

---

## 10. Siguiente Paso

**FASE 6**: Implementación del frontend React con:
- Componente de formulario cascada
- Integración con API FastAPI
- Estilos Monitor One (styled-components)

---

**Documento generado**: 2025-12-19
**Actualizado**: 2025-12-19
**Version**: 2.0
**Stack**: Python 3.12 + FastAPI + SQLModel + PostgreSQL
