# FASE 4.3: BD Iterativa - Migraciones con Alembic

**ID**: `PASO-04.03-V2`
**Fecha**: 2025-12-19
**Estado**: Actualizado a estándares Monitor One

---

## Resumen

Gestión de migraciones de base de datos PostgreSQL utilizando Alembic según estándares Monitor One.

**Stack**: Alembic + SQLModel + PostgreSQL 15

---

## 1. CONFIGURACIÓN DE ALEMBIC

### 1.1 Estructura de Archivos

```
msw-envases-ot/
├── alembic/
│   ├── versions/           # Scripts de migración
│   │   └── 001_create_cascade_tables.py
│   ├── env.py              # Configuración de entorno
│   └── script.py.mako      # Template para nuevas migraciones
├── alembic.ini             # Configuración principal
└── src/
    └── app/
        └── models/         # Modelos SQLModel (fuente de verdad)
```

### 1.2 Archivo alembic.ini

```ini
# Alembic Configuration - INVEB Envases OT

[alembic]
script_location = alembic
prepend_sys_path = .
version_path_separator = os

# Database URL (override con variable de entorno)
sqlalchemy.url = postgresql://postgres:postgres@localhost:5432/inveb_envases

[post_write_hooks]
hooks = black
black.type = console_scripts
black.entrypoint = black
black.options = -q

[loggers]
keys = root,sqlalchemy,alembic

[handlers]
keys = console

[formatters]
keys = generic

[logger_root]
level = WARN
handlers = console

[logger_sqlalchemy]
level = WARN
handlers =
qualname = sqlalchemy.engine

[logger_alembic]
level = INFO
handlers =
qualname = alembic

[handler_console]
class = StreamHandler
args = (sys.stderr,)
level = NOTSET
formatter = generic

[formatter_generic]
format = %(levelname)-5.5s [%(name)s] %(message)s
```

### 1.3 Archivo env.py

```python
# alembic/env.py
"""
Alembic Environment Configuration.
INVEB Envases OT - Migraciones de base de datos.
"""
import os
import sys
from logging.config import fileConfig

from sqlalchemy import engine_from_config, pool
from alembic import context

# Agregar src al path para importar modelos
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'src'))

from app.models import *  # noqa: Importar todos los modelos
from sqlmodel import SQLModel

config = context.config

# Override database URL desde variable de entorno
database_url = os.getenv("DATABASE_URL")
if database_url:
    config.set_main_option("sqlalchemy.url", database_url)

if config.config_file_name is not None:
    fileConfig(config.config_file_name)

# Metadata para autogenerate
target_metadata = SQLModel.metadata


def run_migrations_offline() -> None:
    """Run migrations in 'offline' mode."""
    url = config.get_main_option("sqlalchemy.url")
    context.configure(
        url=url,
        target_metadata=target_metadata,
        literal_binds=True,
        dialect_opts={"paramstyle": "named"},
    )
    with context.begin_transaction():
        context.run_migrations()


def run_migrations_online() -> None:
    """Run migrations in 'online' mode."""
    connectable = engine_from_config(
        config.get_section(config.config_ini_section, {}),
        prefix="sqlalchemy.",
        poolclass=pool.NullPool,
    )
    with connectable.connect() as connection:
        context.configure(
            connection=connection,
            target_metadata=target_metadata
        )
        with context.begin_transaction():
            context.run_migrations()


if context.is_offline_mode():
    run_migrations_offline()
else:
    run_migrations_online()
```

---

## 2. MIGRACIONES CREADAS

### 2.1 Inventario de Migraciones

| Revision | Nombre | Tablas | Estado |
|----------|--------|--------|--------|
| 001 | create_cascade_tables | 5 tablas | Listo |

### 2.2 Migración 001: Tablas de Cascada

```python
# alembic/versions/001_create_cascade_tables.py
"""Create cascade tables - FASE 3

Revision ID: 001
Revises:
Create Date: 2025-12-19

Tablas creadas:
- cascade_rules: Reglas de cascada del formulario OT
- cascade_valid_combinations: Combinaciones válidas producto/impresión/FSC
- cascade_combination_plantas: Plantas por combinación
- coverage_internal_planta: Pivote coverage_internal-planta
- carton_planta: Pivote carton-planta
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa

revision: str = '001'
down_revision: Union[str, None] = None
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    # === CASCADE_RULES ===
    op.create_table(
        'cascade_rules',
        sa.Column('id', sa.Integer(), primary_key=True, autoincrement=True),
        sa.Column('rule_code', sa.String(20), nullable=False, index=True),
        sa.Column('rule_name', sa.String(100), nullable=False),
        sa.Column('trigger_field', sa.String(50), nullable=False),
        sa.Column('trigger_table', sa.String(50), nullable=True),
        sa.Column('target_field', sa.String(50), nullable=False),
        sa.Column('target_table', sa.String(50), nullable=True),
        sa.Column('action', sa.String(20), nullable=False, server_default='enable'),
        sa.Column('condition_type', sa.String(30), nullable=False, server_default='hasValue'),
        sa.Column('condition_value', sa.Text(), nullable=True),
        sa.Column('reset_fields', sa.Text(), nullable=True),
        sa.Column('validation_endpoint', sa.String(100), nullable=True),
        sa.Column('cascade_order', sa.Integer(), nullable=False, server_default='0'),
        sa.Column('form_context', sa.String(30), nullable=False, server_default='ot'),
        sa.Column('description', sa.Text(), nullable=True),
        sa.Column('active', sa.Boolean(), nullable=False, server_default='true'),
        sa.Column('created_at', sa.DateTime(), nullable=False, server_default=sa.func.now()),
        sa.Column('updated_at', sa.DateTime(), nullable=False, server_default=sa.func.now()),
    )
    op.create_index('idx_cascade_rules_trigger', 'cascade_rules', ['trigger_field'])
    op.create_index('idx_cascade_rules_context', 'cascade_rules', ['form_context', 'active'])

    # === CASCADE_VALID_COMBINATIONS ===
    op.create_table(
        'cascade_valid_combinations',
        sa.Column('id', sa.Integer(), primary_key=True, autoincrement=True),
        sa.Column('product_type_id', sa.Integer(), nullable=False, index=True),
        sa.Column('impresion', sa.String(20), nullable=False),
        sa.Column('fsc', sa.String(20), nullable=False),
        sa.Column('active', sa.Boolean(), nullable=False, server_default='true'),
        sa.Column('notes', sa.Text(), nullable=True),
        sa.Column('created_at', sa.DateTime(), nullable=False, server_default=sa.func.now()),
        sa.Column('updated_at', sa.DateTime(), nullable=False, server_default=sa.func.now()),
    )
    op.create_index('idx_combinations_lookup', 'cascade_valid_combinations',
                    ['product_type_id', 'impresion', 'fsc'])

    # === CASCADE_COMBINATION_PLANTAS ===
    op.create_table(
        'cascade_combination_plantas',
        sa.Column('id', sa.Integer(), primary_key=True, autoincrement=True),
        sa.Column('combination_id', sa.Integer(), nullable=False),
        sa.Column('planta_id', sa.Integer(), nullable=False),
        sa.Column('created_at', sa.DateTime(), nullable=False, server_default=sa.func.now()),
        sa.Column('updated_at', sa.DateTime(), nullable=False, server_default=sa.func.now()),
        sa.ForeignKeyConstraint(['combination_id'], ['cascade_valid_combinations.id'],
                                ondelete='CASCADE'),
    )
    op.create_index('idx_comb_plantas_combo', 'cascade_combination_plantas', ['combination_id'])
    op.create_index('idx_comb_plantas_planta', 'cascade_combination_plantas', ['planta_id'])
    op.create_unique_constraint('uk_combination_planta', 'cascade_combination_plantas',
                                ['combination_id', 'planta_id'])

    # === COVERAGE_INTERNAL_PLANTA ===
    op.create_table(
        'coverage_internal_planta',
        sa.Column('id', sa.Integer(), primary_key=True, autoincrement=True),
        sa.Column('coverage_internal_id', sa.Integer(), nullable=False),
        sa.Column('planta_id', sa.Integer(), nullable=False),
        sa.Column('created_at', sa.DateTime(), nullable=False, server_default=sa.func.now()),
        sa.Column('updated_at', sa.DateTime(), nullable=False, server_default=sa.func.now()),
    )
    op.create_index('idx_cov_int_coverage', 'coverage_internal_planta', ['coverage_internal_id'])
    op.create_index('idx_cov_int_planta', 'coverage_internal_planta', ['planta_id'])
    op.create_unique_constraint('uk_cov_int_planta', 'coverage_internal_planta',
                                ['coverage_internal_id', 'planta_id'])

    # === CARTON_PLANTA ===
    op.create_table(
        'carton_planta',
        sa.Column('id', sa.Integer(), primary_key=True, autoincrement=True),
        sa.Column('carton_id', sa.Integer(), nullable=False),
        sa.Column('planta_id', sa.Integer(), nullable=False),
        sa.Column('created_at', sa.DateTime(), nullable=False, server_default=sa.func.now()),
        sa.Column('updated_at', sa.DateTime(), nullable=False, server_default=sa.func.now()),
    )
    op.create_index('idx_carton_planta_carton', 'carton_planta', ['carton_id'])
    op.create_index('idx_carton_planta_planta', 'carton_planta', ['planta_id'])
    op.create_unique_constraint('uk_carton_planta', 'carton_planta', ['carton_id', 'planta_id'])


def downgrade() -> None:
    op.drop_table('carton_planta')
    op.drop_table('coverage_internal_planta')
    op.drop_table('cascade_combination_plantas')
    op.drop_table('cascade_valid_combinations')
    op.drop_table('cascade_rules')
```

---

## 3. COMANDOS DE ALEMBIC

### 3.1 Comandos Básicos

```bash
# Ver estado actual de migraciones
alembic current

# Ver historial de migraciones
alembic history

# Aplicar todas las migraciones pendientes
alembic upgrade head

# Aplicar una migración específica
alembic upgrade 001

# Revertir última migración
alembic downgrade -1

# Revertir a una revisión específica
alembic downgrade 001

# Revertir todas las migraciones
alembic downgrade base
```

### 3.2 Crear Nueva Migración

```bash
# Migración vacía (manual)
alembic revision -m "add_new_table"

# Migración autogenerada (basada en modelos)
alembic revision --autogenerate -m "add_new_table"
```

### 3.3 Comandos Docker

```bash
# Ver estado
docker exec inveb-envases-ot-api alembic current

# Aplicar migraciones
docker exec inveb-envases-ot-api alembic upgrade head

# Revertir última
docker exec inveb-envases-ot-api alembic downgrade -1

# Crear nueva migración
docker exec inveb-envases-ot-api alembic revision --autogenerate -m "descripcion"
```

---

## 4. SEEDERS (DATOS INICIALES)

### 4.1 Script de Seeder

```python
# scripts/seed_cascade_rules.py
"""
Seeder de reglas de cascada.
Equivalente Python del CascadeRulesTableSeeder de Laravel.
"""
import sys
import os
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'src'))

from sqlmodel import Session, delete
from app.db import engine
from app.models import CascadeRule

CASCADE_RULES = [
    {
        "rule_code": "CASC-001",
        "rule_name": "Tipo Item habilita Impresion",
        "trigger_field": "product_type_id",
        "trigger_table": "product_types",
        "target_field": "impresion",
        "target_table": "impresion",
        "action": "enable",
        "condition_type": "hasValue",
        "reset_fields": '["impresion", "fsc", "cinta", "coverage_internal_id", "coverage_external_id", "planta_id", "carton_color", "carton_id"]',
        "cascade_order": 1,
        "form_context": "ot",
        "active": True,
        "description": "Al seleccionar tipo de item, se habilita impresion",
    },
    # ... más reglas (ver archivo completo en msw-envases-ot/scripts/)
]


def seed_cascade_rules():
    """Insertar reglas de cascada en la base de datos."""
    print("Seeding cascade rules...")

    with Session(engine) as session:
        # Limpiar tabla
        session.exec(delete(CascadeRule))
        session.commit()

        # Insertar reglas
        for rule_data in CASCADE_RULES:
            rule = CascadeRule(**rule_data)
            session.add(rule)

        session.commit()
        print(f"✅ Inserted {len(CASCADE_RULES)} cascade rules.")


if __name__ == "__main__":
    seed_cascade_rules()
```

### 4.2 Ejecutar Seeders

```bash
# Local
python scripts/seed_cascade_rules.py

# Docker
docker exec inveb-envases-ot-api python scripts/seed_cascade_rules.py
```

---

## 5. FLUJO DE TRABAJO

### 5.1 Agregar Nueva Tabla

```bash
# 1. Crear modelo SQLModel
# src/app/models/nueva_tabla.py

# 2. Importar en __init__.py
# src/app/models/__init__.py

# 3. Generar migración
alembic revision --autogenerate -m "add_nueva_tabla"

# 4. Revisar migración generada
# alembic/versions/xxx_add_nueva_tabla.py

# 5. Aplicar migración
alembic upgrade head

# 6. Verificar
alembic current
```

### 5.2 Modificar Tabla Existente

```bash
# 1. Modificar modelo SQLModel

# 2. Generar migración
alembic revision --autogenerate -m "alter_tabla_add_column"

# 3. Revisar y ajustar migración si es necesario

# 4. Aplicar
alembic upgrade head
```

### 5.3 Rollback de Emergencia

```bash
# Revertir última migración
alembic downgrade -1

# Revertir a estado anterior conocido
alembic downgrade abc123

# Revertir todo
alembic downgrade base
```

---

## 6. VERIFICACIÓN

### 6.1 Verificar Tablas Creadas

```bash
# Conectar a PostgreSQL
docker exec -it inveb-envases-ot-db psql -U postgres -d inveb_envases

# Listar tablas
\dt

# Describir tabla
\d cascade_rules

# Contar registros
SELECT COUNT(*) FROM cascade_rules;

# Ver reglas
SELECT rule_code, rule_name, cascade_order FROM cascade_rules ORDER BY cascade_order;
```

### 6.2 Script de Verificación

```python
# scripts/verify_migrations.py
"""Verifica que las migraciones se aplicaron correctamente."""
import sys
sys.path.insert(0, 'src')

from sqlmodel import Session, text
from app.db import engine

EXPECTED_TABLES = [
    'cascade_rules',
    'cascade_valid_combinations',
    'cascade_combination_plantas',
    'coverage_internal_planta',
    'carton_planta',
    'alembic_version'
]

def verify():
    print("Verificando migraciones...")

    with Session(engine) as session:
        # Obtener tablas existentes
        result = session.exec(text("""
            SELECT table_name FROM information_schema.tables
            WHERE table_schema = 'public'
        """))
        existing_tables = [row[0] for row in result]

        # Verificar cada tabla
        all_ok = True
        for table in EXPECTED_TABLES:
            if table in existing_tables:
                print(f"✓ {table}")
            else:
                print(f"✗ {table} - NO EXISTE")
                all_ok = False

        # Verificar versión de Alembic
        result = session.exec(text("SELECT version_num FROM alembic_version"))
        version = result.first()
        print(f"\nVersión Alembic: {version[0] if version else 'N/A'}")

        return all_ok

if __name__ == "__main__":
    success = verify()
    sys.exit(0 if success else 1)
```

---

## 7. COMPARACIÓN CON SISTEMA LEGADO

| Aspecto | Laravel (Legado) | Alembic (Monitor One) |
|---------|------------------|----------------------|
| Archivo migración | PHP class | Python script |
| Comando up | php artisan migrate | alembic upgrade head |
| Comando down | php artisan migrate:rollback | alembic downgrade -1 |
| Autogenerar | make:migration | revision --autogenerate |
| Estado | migrate:status | alembic current |
| Historial | migrate:status | alembic history |
| Seeder | php artisan db:seed | python scripts/seed_*.py |
| BD | MySQL | PostgreSQL |

---

## 8. MEJORES PRÁCTICAS

### 8.1 Nombrado de Migraciones

```bash
# Formato: NNN_descripcion_accion.py
001_create_cascade_tables.py
002_add_user_preferences.py
003_alter_work_orders_add_status.py
004_drop_legacy_columns.py
```

### 8.2 Revisión de Migraciones

Siempre revisar migraciones autogeneradas antes de aplicar:
- Verificar nombres de tablas/columnas
- Verificar tipos de datos
- Verificar índices y constraints
- Verificar orden de operaciones

### 8.3 Backup Antes de Migrar

```bash
# Crear backup antes de migración destructiva
docker exec inveb-envases-ot-db pg_dump -U postgres inveb_envases > backup_pre_migration.sql

# Aplicar migración
alembic upgrade head

# Si falla, restaurar
docker exec -i inveb-envases-ot-db psql -U postgres inveb_envases < backup_pre_migration.sql
```

---

## 9. SIGUIENTE PASO

**FASE 5**: Implementación de Microservicios

---

**Documento actualizado**: 2025-12-19
**Versión**: 2.0 (Migrado a estándares Monitor One)

### Historial de Cambios

| Versión | Fecha | Cambios |
|---------|-------|---------|
| 1.0 | 2025-12-17 | Versión inicial (Laravel/MySQL) |
| 2.0 | 2025-12-19 | Migrado a Monitor One (Alembic/PostgreSQL) |
