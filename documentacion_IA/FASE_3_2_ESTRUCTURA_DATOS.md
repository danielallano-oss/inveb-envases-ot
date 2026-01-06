# FASE 3.2: Estructura de Datos Normalizada

**ID**: `PASO-03.02-V12`
**Fecha**: 2025-12-17
**Estado**: Completado

---

## Resumen Ejecutivo

Este documento define la estructura de datos normalizada para resolver los problemas identificados en la Fase 3.1, especialmente:

1. **Campos multi-valor** (planta_id = "1,2,3")
2. **Reglas de cascada hardcodeadas** en JavaScript
3. **Tabla relacion_filtro_ingresos_principales** sin normalizar

---

## 1. TABLAS PIVOTE PARA CAMPOS MULTI-VALOR

### 1.1 coverage_internal_planta

**Problema**: `coverage_internals.planta_id = "1,2,3"`

```sql
-- ============================================================
-- TABLA PIVOTE: coverage_internal_planta
-- Relacion muchos-a-muchos entre recubrimientos internos y plantas
-- ============================================================

CREATE TABLE coverage_internal_planta (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    coverage_internal_id BIGINT UNSIGNED NOT NULL,
    planta_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_cip_coverage FOREIGN KEY (coverage_internal_id)
        REFERENCES coverage_internals(id) ON DELETE CASCADE,
    CONSTRAINT fk_cip_planta FOREIGN KEY (planta_id)
        REFERENCES plantas(id) ON DELETE CASCADE,
    UNIQUE KEY uk_coverage_planta (coverage_internal_id, planta_id),
    INDEX idx_planta (planta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos iniciales (migrados de coverage_internals.planta_id)
INSERT INTO coverage_internal_planta (coverage_internal_id, planta_id) VALUES
-- id=1: "No aplica" -> planta_id="1,2,3"
(1, 1), (1, 2), (1, 3),
-- id=2: "Barniz hidrorepelente" -> planta_id="1,2,3"
(2, 1), (2, 2), (2, 3),
-- id=3: "Cera" -> planta_id="1"
(3, 1);
```

**Diagrama Entidad-Relacion:**

```
┌───────────────────────┐         ┌──────────────────────────┐
│   coverage_internals  │         │  coverage_internal_planta │
├───────────────────────┤         ├──────────────────────────┤
│ id (PK)               │◄────────│ coverage_internal_id (FK)│
│ descripcion           │    N:M  │ planta_id (FK)───────────┼──┐
│ status                │         │ created_at               │  │
│ created_at            │         │ updated_at               │  │
│ updated_at            │         └──────────────────────────┘  │
└───────────────────────┘                                       │
                                                                │
┌───────────────────────┐                                       │
│       plantas         │                                       │
├───────────────────────┤                                       │
│ id (PK)               │◄──────────────────────────────────────┘
│ nombre                │
│ ...                   │
└───────────────────────┘
```

---

### 1.2 carton_planta

**Problema**: `cartons.planta_id = "1,2,3"` (235 registros)

```sql
-- ============================================================
-- TABLA PIVOTE: carton_planta
-- Relacion muchos-a-muchos entre cartones y plantas
-- ============================================================

CREATE TABLE carton_planta (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    carton_id BIGINT UNSIGNED NOT NULL,
    planta_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_cp_carton FOREIGN KEY (carton_id)
        REFERENCES cartons(id) ON DELETE CASCADE,
    CONSTRAINT fk_cp_planta FOREIGN KEY (planta_id)
        REFERENCES plantas(id) ON DELETE CASCADE,
    UNIQUE KEY uk_carton_planta (carton_id, planta_id),
    INDEX idx_planta (planta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Script de migracion (ejecutar para cada carton)
-- Ver seccion 5.2 para script completo
```

---

## 2. SISTEMA DE REGLAS DE CASCADA

### 2.1 Tabla cascade_rules (Nueva)

Esta tabla almacena las reglas de cascada de forma declarativa, reemplazando el codigo JavaScript hardcodeado.

```sql
-- ============================================================
-- TABLA: cascade_rules
-- Reglas declarativas de cascada para formularios
-- ============================================================

CREATE TABLE cascade_rules (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rule_code VARCHAR(20) NOT NULL UNIQUE,          -- 'CASC-001', 'CASC-002', etc.
    rule_name VARCHAR(100) NOT NULL,                 -- Nombre descriptivo

    -- Campo que dispara la cascada
    trigger_field VARCHAR(50) NOT NULL,              -- 'product_type_id', 'impresion', etc.
    trigger_table VARCHAR(50),                       -- Tabla del trigger (opcional)

    -- Campo objetivo de la cascada
    target_field VARCHAR(50) NOT NULL,               -- 'impresion', 'fsc', etc.
    target_table VARCHAR(50),                        -- Tabla del target (opcional)

    -- Accion a ejecutar
    action ENUM('enable', 'disable', 'setValue', 'validate', 'loadOptions') NOT NULL,

    -- Condiciones
    condition_type ENUM('hasValue', 'equals', 'notEquals', 'in', 'notIn', 'custom') NOT NULL,
    condition_value TEXT,                            -- JSON con valores para la condicion

    -- Campos a resetear cuando se activa esta regla
    reset_fields TEXT,                               -- JSON array: ["fsc", "cinta", ...]

    -- Endpoint de validacion (si aplica)
    validation_endpoint VARCHAR(200),                -- '/api/validate-cascade/impresion'

    -- Orden de ejecucion en la cascada
    cascade_order INT UNSIGNED NOT NULL DEFAULT 0,

    -- Metadatos
    form_context VARCHAR(50) DEFAULT 'ot',           -- 'ot', 'cotizacion', etc.
    active TINYINT(1) NOT NULL DEFAULT 1,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_trigger (trigger_field),
    INDEX idx_target (target_field),
    INDEX idx_order (cascade_order),
    INDEX idx_context (form_context)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.2 Datos Iniciales - cascade_rules

```sql
-- ============================================================
-- DATOS INICIALES: Reglas de cascada del formulario OT
-- Basado en analisis de ot-creation.js
-- ============================================================

INSERT INTO cascade_rules (
    rule_code, rule_name, trigger_field, target_field, action,
    condition_type, condition_value, reset_fields, validation_endpoint,
    cascade_order, form_context, description
) VALUES

-- CASC-001: TIPO ITEM -> IMPRESION
('CASC-001', 'Tipo Item habilita Impresion',
 'product_type_id', 'impresion', 'enable',
 'hasValue', NULL,
 '["impresion", "fsc", "cinta", "coverage_internal_id", "coverage_external_id", "planta_id", "carton_color", "carton_id"]',
 '/api/cascade/product-type',
 1, 'ot', 'Al seleccionar tipo de item, se habilita impresion y se resetean todos los campos siguientes'),

-- CASC-002: IMPRESION -> FSC
('CASC-002', 'Impresion habilita FSC',
 'impresion', 'fsc', 'enable',
 'hasValue', NULL,
 '["fsc", "cinta", "coverage_internal_id", "coverage_external_id", "planta_id", "carton_color", "carton_id"]',
 '/api/cascade/impresion',
 2, 'ot', 'Al seleccionar impresion, se habilita FSC. Valida combinacion con tabla de reglas'),

-- CASC-003: FSC -> CINTA
('CASC-003', 'FSC habilita Cinta',
 'fsc', 'cinta', 'enable',
 'hasValue', NULL,
 '["cinta", "coverage_internal_id", "coverage_external_id", "planta_id", "carton_color", "carton_id"]',
 '/api/cascade/fsc',
 3, 'ot', 'Al seleccionar FSC, se habilita Cinta'),

-- CASC-004: CINTA -> RECUBRIMIENTO INTERNO
('CASC-004', 'Cinta habilita Recubrimiento Interno',
 'cinta', 'coverage_internal_id', 'enable',
 'hasValue', NULL,
 '["coverage_internal_id", "coverage_external_id", "planta_id", "carton_color", "carton_id"]',
 '/api/cascade/cinta',
 4, 'ot', 'Al seleccionar Cinta, se habilita Recubrimiento Interno'),

-- CASC-005: REC. INTERNO -> REC. EXTERNO
('CASC-005', 'Rec. Interno habilita Rec. Externo',
 'coverage_internal_id', 'coverage_external_id', 'enable',
 'hasValue', NULL,
 '["coverage_external_id", "planta_id", "carton_color", "carton_id"]',
 '/api/cascade/coverage-internal',
 5, 'ot', 'Al seleccionar Recubrimiento Interno, se habilita Recubrimiento Externo'),

-- CASC-006: REC. EXTERNO -> PLANTA
('CASC-006', 'Rec. Externo habilita Planta',
 'coverage_external_id', 'planta_id', 'enable',
 'hasValue', NULL,
 '["planta_id", "carton_color", "carton_id"]',
 '/api/cascade/coverage-external',
 6, 'ot', 'Al seleccionar Recubrimiento Externo, se habilita Planta'),

-- CASC-007: PLANTA -> COLOR CARTON
('CASC-007', 'Planta habilita Color Carton',
 'planta_id', 'carton_color', 'enable',
 'hasValue', NULL,
 '["carton_color", "carton_id"]',
 '/api/cascade/planta',
 7, 'ot', 'Al seleccionar Planta, se habilita Color Carton'),

-- CASC-008: COLOR CARTON -> CARTON
('CASC-008', 'Color Carton habilita Carton',
 'carton_color', 'carton_id', 'enable',
 'hasValue', NULL,
 '["carton_id"]',
 '/api/cascade/carton-color',
 8, 'ot', 'Al seleccionar Color Carton, se habilita Carton');
```

### 2.3 Reglas de Excepcion

```sql
-- ============================================================
-- REGLAS DE EXCEPCION
-- Casos especiales que modifican el flujo normal de cascada
-- ============================================================

INSERT INTO cascade_rules (
    rule_code, rule_name, trigger_field, target_field, action,
    condition_type, condition_value, reset_fields, validation_endpoint,
    cascade_order, form_context, description
) VALUES

-- EXCEP-001: Vendedor con MuestraConCad salta recubrimientos
('EXCEP-001', 'MuestraConCad salta recubrimientos',
 'tipo_solicitud', 'coverage_internal_id', 'setValue',
 'equals', '{"tipo_solicitud": 3, "role": [4, 19]}',
 NULL,
 NULL,
 100, 'ot', 'Cuando tipo_solicitud=3 (MuestraConCad) y usuario es Vendedor, coverage_internal=14 (N/A)'),

-- EXCEP-002: Vendedor con MuestraConCad salta recubrimiento externo
('EXCEP-002', 'MuestraConCad salta rec externo',
 'tipo_solicitud', 'coverage_external_id', 'setValue',
 'equals', '{"tipo_solicitud": 3, "role": [4, 19]}',
 NULL,
 NULL,
 101, 'ot', 'Cuando tipo_solicitud=3 (MuestraConCad) y usuario es Vendedor, coverage_external=14 (N/A)');
```

---

## 3. NORMALIZACION DE COMBINACIONES VALIDAS

### 3.1 Tabla cascade_valid_combinations (Reemplaza relacion_filtro_ingresos_principales)

```sql
-- ============================================================
-- TABLA: cascade_valid_combinations
-- Combinaciones validas entre campos de cascada
-- Reemplaza: relacion_filtro_ingresos_principales
-- ============================================================

CREATE TABLE cascade_valid_combinations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- Identificacion del paso de cascada
    cascade_step VARCHAR(50) NOT NULL,               -- 'impresion_fsc', 'cinta', etc.

    -- Valores de la combinacion
    source_field VARCHAR(50) NOT NULL,               -- Campo origen
    source_value INT UNSIGNED NOT NULL,              -- Valor del campo origen
    target_field VARCHAR(50) NOT NULL,               -- Campo destino
    target_value INT UNSIGNED NOT NULL,              -- Valor valido del campo destino

    -- Metadatos
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_step (cascade_step),
    INDEX idx_source (source_field, source_value),
    INDEX idx_target (target_field, target_value),
    UNIQUE KEY uk_combination (cascade_step, source_value, target_value)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA PIVOTE: cascade_combination_plantas
-- Plantas donde aplica cada combinacion
-- ============================================================

CREATE TABLE cascade_combination_plantas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    combination_id INT UNSIGNED NOT NULL,
    planta_id BIGINT UNSIGNED NOT NULL,

    CONSTRAINT fk_ccp_combination FOREIGN KEY (combination_id)
        REFERENCES cascade_valid_combinations(id) ON DELETE CASCADE,
    CONSTRAINT fk_ccp_planta FOREIGN KEY (planta_id)
        REFERENCES plantas(id) ON DELETE CASCADE,
    UNIQUE KEY uk_comb_planta (combination_id, planta_id),
    INDEX idx_planta (planta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3.2 Ejemplo de Migracion de Datos

```sql
-- ============================================================
-- MIGRACION: relacion_filtro_ingresos_principales -> cascade_valid_combinations
-- ============================================================

-- Ejemplo para registro original:
-- id=1, filtro_1=1, filtro_2=2, planta_id="1", referencia="impresion_fsc"

-- Paso 1: Insertar combinacion
INSERT INTO cascade_valid_combinations
(cascade_step, source_field, source_value, target_field, target_value)
VALUES
('impresion_fsc', 'impresion', 1, 'fsc', 2);

-- Obtener ID generado
SET @combo_id = LAST_INSERT_ID();

-- Paso 2: Insertar plantas asociadas
INSERT INTO cascade_combination_plantas (combination_id, planta_id)
VALUES (@combo_id, 1);

-- Para registro con multiples plantas (planta_id="1,2,3"):
-- id=6, filtro_1=2, filtro_2=2, planta_id="1,2,3", referencia="impresion_fsc"

INSERT INTO cascade_valid_combinations
(cascade_step, source_field, source_value, target_field, target_value)
VALUES
('impresion_fsc', 'impresion', 2, 'fsc', 2);

SET @combo_id = LAST_INSERT_ID();

INSERT INTO cascade_combination_plantas (combination_id, planta_id)
VALUES
(@combo_id, 1),
(@combo_id, 2),
(@combo_id, 3);
```

---

## 4. DIAGRAMA DE ESTRUCTURA COMPLETA

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    ESTRUCTURA DE DATOS NORMALIZADA                          │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌─────────────────┐                    ┌─────────────────────────┐        │
│  │  cascade_rules  │                    │ cascade_valid_combinations│       │
│  ├─────────────────┤                    ├─────────────────────────┤        │
│  │ rule_code (PK)  │                    │ id (PK)                 │        │
│  │ trigger_field   │                    │ cascade_step            │        │
│  │ target_field    │                    │ source_field            │        │
│  │ action          │                    │ source_value            │        │
│  │ condition_type  │                    │ target_field            │        │
│  │ cascade_order   │                    │ target_value            │        │
│  └─────────────────┘                    └───────────┬─────────────┘        │
│         │                                           │                       │
│         │ Define reglas                             │ 1:N                   │
│         │                                           ▼                       │
│         │                               ┌─────────────────────────┐        │
│         │                               │cascade_combination_plantas│       │
│         │                               ├─────────────────────────┤        │
│         │                               │ combination_id (FK)     │        │
│         │                               │ planta_id (FK)──────────┼───┐    │
│         │                               └─────────────────────────┘   │    │
│         │                                                             │    │
│         ▼                                                             │    │
│  ┌──────────────────────────────────────────────────────────────┐    │    │
│  │                      work_orders                              │    │    │
│  │  (Aplica reglas de cascade_rules + valida con combinations)  │    │    │
│  └──────────────────────────────────────────────────────────────┘    │    │
│                                                                       │    │
│  ┌─────────────────┐     ┌─────────────────────────┐                 │    │
│  │coverage_internals│     │coverage_internal_planta │                 │    │
│  ├─────────────────┤     ├─────────────────────────┤                 │    │
│  │ id (PK)         │◄────│ coverage_internal_id(FK)│                 │    │
│  │ descripcion     │     │ planta_id (FK)──────────┼─────────────────┼────┤
│  └─────────────────┘     └─────────────────────────┘                 │    │
│                                                                       │    │
│  ┌─────────────────┐     ┌─────────────────────────┐                 │    │
│  │    cartons      │     │     carton_planta       │                 │    │
│  ├─────────────────┤     ├─────────────────────────┤                 │    │
│  │ id (PK)         │◄────│ carton_id (FK)          │                 │    │
│  │ codigo          │     │ planta_id (FK)──────────┼─────────────────┼────┤
│  │ ...             │     └─────────────────────────┘                 │    │
│  └─────────────────┘                                                 │    │
│                                                                       │    │
│  ┌─────────────────┐                                                 │    │
│  │    plantas      │◄────────────────────────────────────────────────┘    │
│  ├─────────────────┤                                                      │
│  │ id (PK)         │                                                      │
│  │ nombre          │                                                      │
│  │ ...             │                                                      │
│  └─────────────────┘                                                      │
│                                                                            │
└────────────────────────────────────────────────────────────────────────────┘
```

---

## 5. SCRIPTS DE MIGRACION

### 5.1 Script Completo: Migracion coverage_internals

```sql
-- ============================================================
-- MIGRACION: coverage_internals.planta_id -> coverage_internal_planta
-- ============================================================

-- Paso 1: Crear tabla pivote
CREATE TABLE IF NOT EXISTS coverage_internal_planta (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    coverage_internal_id BIGINT UNSIGNED NOT NULL,
    planta_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_coverage_planta (coverage_internal_id, planta_id),
    INDEX idx_planta (planta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Paso 2: Migrar datos existentes
-- Para coverage_internals donde planta_id = "1,2,3"
INSERT INTO coverage_internal_planta (coverage_internal_id, planta_id)
SELECT ci.id, p.id
FROM coverage_internals ci
CROSS JOIN plantas p
WHERE FIND_IN_SET(p.id, ci.planta_id) > 0;

-- Paso 3: Verificar migracion
SELECT
    ci.id,
    ci.descripcion,
    ci.planta_id as planta_id_original,
    GROUP_CONCAT(cip.planta_id ORDER BY cip.planta_id) as plantas_migradas
FROM coverage_internals ci
LEFT JOIN coverage_internal_planta cip ON ci.id = cip.coverage_internal_id
GROUP BY ci.id;

-- Paso 4: Agregar FK (despues de verificar)
ALTER TABLE coverage_internal_planta
ADD CONSTRAINT fk_cip_coverage FOREIGN KEY (coverage_internal_id)
    REFERENCES coverage_internals(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_cip_planta FOREIGN KEY (planta_id)
    REFERENCES plantas(id) ON DELETE CASCADE;

-- Paso 5: (OPCIONAL) Eliminar columna antigua despues de validar
-- ALTER TABLE coverage_internals DROP COLUMN planta_id;
```

### 5.2 Script Completo: Migracion cartons

```sql
-- ============================================================
-- MIGRACION: cartons.planta_id -> carton_planta
-- ============================================================

-- Paso 1: Crear tabla pivote
CREATE TABLE IF NOT EXISTS carton_planta (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    carton_id BIGINT UNSIGNED NOT NULL,
    planta_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_carton_planta (carton_id, planta_id),
    INDEX idx_planta (planta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Paso 2: Migrar datos existentes
INSERT INTO carton_planta (carton_id, planta_id)
SELECT c.id, p.id
FROM cartons c
CROSS JOIN plantas p
WHERE c.planta_id IS NOT NULL
  AND c.planta_id != ''
  AND FIND_IN_SET(p.id, c.planta_id) > 0;

-- Paso 3: Manejar cartones sin planta (aplicables a todas)
INSERT INTO carton_planta (carton_id, planta_id)
SELECT c.id, p.id
FROM cartons c
CROSS JOIN plantas p
WHERE c.planta_id IS NULL OR c.planta_id = ''
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;

-- Paso 4: Verificar
SELECT
    COUNT(DISTINCT carton_id) as cartones_migrados,
    COUNT(*) as total_relaciones
FROM carton_planta;

-- Paso 5: Agregar FK
ALTER TABLE carton_planta
ADD CONSTRAINT fk_cp_carton FOREIGN KEY (carton_id)
    REFERENCES cartons(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_cp_planta FOREIGN KEY (planta_id)
    REFERENCES plantas(id) ON DELETE CASCADE;
```

### 5.3 Script Completo: Migracion relacion_filtro_ingresos_principales

```sql
-- ============================================================
-- MIGRACION: relacion_filtro_ingresos_principales ->
--            cascade_valid_combinations + cascade_combination_plantas
-- ============================================================

-- Paso 1: Crear tablas nuevas
CREATE TABLE IF NOT EXISTS cascade_valid_combinations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cascade_step VARCHAR(50) NOT NULL,
    source_field VARCHAR(50) NOT NULL,
    source_value INT UNSIGNED NOT NULL,
    target_field VARCHAR(50) NOT NULL,
    target_value INT UNSIGNED NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_step (cascade_step),
    INDEX idx_source (source_field, source_value),
    UNIQUE KEY uk_combination (cascade_step, source_value, target_value)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cascade_combination_plantas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    combination_id INT UNSIGNED NOT NULL,
    planta_id BIGINT UNSIGNED NOT NULL,
    UNIQUE KEY uk_comb_planta (combination_id, planta_id),
    INDEX idx_planta (planta_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Paso 2: Migrar combinaciones
-- Mapeo de referencia a campos:
-- impresion_fsc: impresion -> fsc
-- cinta: fsc -> cinta (tipos_cintas)
-- recubrimiento_interno: cinta -> coverage_internal_id
-- impresion_recubrimiento_externo: coverage_internal_id -> coverage_external_id

-- Insertar combinaciones impresion_fsc
INSERT INTO cascade_valid_combinations
(cascade_step, source_field, source_value, target_field, target_value)
SELECT DISTINCT
    'impresion_fsc' as cascade_step,
    'impresion' as source_field,
    filtro_1 as source_value,
    'fsc' as target_field,
    CAST(filtro_2 AS UNSIGNED) as target_value
FROM relacion_filtro_ingresos_principales
WHERE referencia = 'impresion_fsc'
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;

-- Insertar combinaciones cinta
INSERT INTO cascade_valid_combinations
(cascade_step, source_field, source_value, target_field, target_value)
SELECT DISTINCT
    'cinta' as cascade_step,
    'fsc' as source_field,
    filtro_1 as source_value,
    'cinta' as target_field,
    CAST(filtro_2 AS UNSIGNED) as target_value
FROM relacion_filtro_ingresos_principales
WHERE referencia = 'cinta'
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;

-- Insertar combinaciones recubrimiento_interno
INSERT INTO cascade_valid_combinations
(cascade_step, source_field, source_value, target_field, target_value)
SELECT DISTINCT
    'recubrimiento_interno' as cascade_step,
    'cinta' as source_field,
    filtro_1 as source_value,
    'coverage_internal_id' as target_field,
    CAST(filtro_2 AS UNSIGNED) as target_value
FROM relacion_filtro_ingresos_principales
WHERE referencia = 'recubrimiento_interno'
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;

-- Insertar combinaciones recubrimiento_externo
INSERT INTO cascade_valid_combinations
(cascade_step, source_field, source_value, target_field, target_value)
SELECT DISTINCT
    'recubrimiento_externo' as cascade_step,
    'coverage_internal_id' as source_field,
    filtro_1 as source_value,
    'coverage_external_id' as target_field,
    CAST(filtro_2 AS UNSIGNED) as target_value
FROM relacion_filtro_ingresos_principales
WHERE referencia = 'impresion_recubrimiento_externo'
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;

-- Paso 3: Migrar plantas asociadas a cada combinacion
-- Esto requiere un procedimiento o script por cada combinacion original

DELIMITER //
CREATE PROCEDURE migrate_combination_plantas()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_id INT;
    DECLARE v_filtro_1 INT;
    DECLARE v_filtro_2 VARCHAR(191);
    DECLARE v_planta_id VARCHAR(191);
    DECLARE v_referencia VARCHAR(191);
    DECLARE v_combo_id INT;
    DECLARE v_planta INT;

    DECLARE cur CURSOR FOR
        SELECT id, filtro_1, filtro_2, planta_id, referencia
        FROM relacion_filtro_ingresos_principales;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO v_id, v_filtro_1, v_filtro_2, v_planta_id, v_referencia;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Obtener ID de la combinacion migrada
        SELECT id INTO v_combo_id
        FROM cascade_valid_combinations
        WHERE source_value = v_filtro_1
          AND target_value = CAST(v_filtro_2 AS UNSIGNED)
          AND cascade_step = CASE v_referencia
              WHEN 'impresion_fsc' THEN 'impresion_fsc'
              WHEN 'cinta' THEN 'cinta'
              WHEN 'recubrimiento_interno' THEN 'recubrimiento_interno'
              WHEN 'impresion_recubrimiento_externo' THEN 'recubrimiento_externo'
          END
        LIMIT 1;

        -- Insertar plantas
        IF v_combo_id IS NOT NULL AND v_planta_id IS NOT NULL AND v_planta_id != '' THEN
            -- Para cada planta en la lista separada por comas
            SET @plantas = v_planta_id;
            WHILE LENGTH(@plantas) > 0 DO
                SET v_planta = CAST(SUBSTRING_INDEX(@plantas, ',', 1) AS UNSIGNED);
                INSERT IGNORE INTO cascade_combination_plantas (combination_id, planta_id)
                VALUES (v_combo_id, v_planta);

                IF LOCATE(',', @plantas) > 0 THEN
                    SET @plantas = SUBSTRING(@plantas, LOCATE(',', @plantas) + 1);
                ELSE
                    SET @plantas = '';
                END IF;
            END WHILE;
        END IF;
    END LOOP;

    CLOSE cur;
END //
DELIMITER ;

-- Ejecutar procedimiento
CALL migrate_combination_plantas();

-- Limpiar
DROP PROCEDURE migrate_combination_plantas;

-- Paso 4: Agregar FK
ALTER TABLE cascade_combination_plantas
ADD CONSTRAINT fk_ccp_combination FOREIGN KEY (combination_id)
    REFERENCES cascade_valid_combinations(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_ccp_planta FOREIGN KEY (planta_id)
    REFERENCES plantas(id) ON DELETE CASCADE;

-- Paso 5: Verificar
SELECT
    cvc.cascade_step,
    COUNT(DISTINCT cvc.id) as combinaciones,
    COUNT(ccp.id) as relaciones_plantas
FROM cascade_valid_combinations cvc
LEFT JOIN cascade_combination_plantas ccp ON cvc.id = ccp.combination_id
GROUP BY cvc.cascade_step;
```

---

## 6. API ENDPOINTS PROPUESTOS

### 6.1 Endpoints para Reglas de Cascada

```
GET  /api/v1/cascade-rules/
     Retorna todas las reglas de cascada activas

GET  /api/v1/cascade-rules/{id}
     Retorna regla especifica por ID

GET  /api/v1/cascade-rules/trigger/{field}
     Retorna reglas para un trigger especifico

GET  /api/v1/cascade-combinations/
     Retorna combinaciones validas

POST /api/v1/cascade-rules/validate-combination
     Body: { source_field, source_value, target_field, target_value, planta_id }
     Valida si una combinacion es permitida
```

### 6.2 Endpoints para Opciones del Formulario (IMPLEMENTADO 2025-12-19)

```
GET  /api/v1/form-options/
     Retorna TODAS las opciones del formulario en una sola llamada
     Response: { product_types, impresion_types, fsc_options, cinta_options,
                 coverage_internal, coverage_external, plantas, carton_colors, cartones }

GET  /api/v1/form-options/product-types
     Retorna tipos de producto disponibles

GET  /api/v1/form-options/impresion-types
     Retorna tipos de impresion disponibles

GET  /api/v1/form-options/fsc-options
     Retorna opciones FSC disponibles

GET  /api/v1/form-options/cinta-options
     Retorna opciones de cinta disponibles

GET  /api/v1/form-options/coverage-internal
     Retorna opciones de recubrimiento interno

GET  /api/v1/form-options/coverage-external
     Retorna opciones de recubrimiento externo

GET  /api/v1/form-options/plantas
     Retorna plantas disponibles

GET  /api/v1/form-options/carton-colors
     Retorna colores de carton disponibles

GET  /api/v1/form-options/cartones
     Retorna tipos de carton disponibles
```

**Archivo implementado**: `src/app/routers/form_options.py`

### 6.3 Ejemplo de Respuesta

```json
// GET /api/cascade/rules/ot
{
  "rules": [
    {
      "rule_code": "CASC-001",
      "trigger_field": "product_type_id",
      "target_field": "impresion",
      "action": "enable",
      "cascade_order": 1,
      "reset_fields": ["impresion", "fsc", "cinta", "coverage_internal_id", "coverage_external_id", "planta_id", "carton_color", "carton_id"]
    }
  ]
}

// GET /api/cascade/validate/impresion_fsc?source=2&planta=1
{
  "valid_options": [
    {"id": 2, "descripcion": "Si"},
    {"id": 3, "descripcion": "Sin FSC"},
    {"id": 4, "descripcion": "Logo FSC solo EEII"},
    {"id": 5, "descripcion": "Logo FSC cliente y EEII"},
    {"id": 6, "descripcion": "Logo FSC solo cliente"}
  ]
}
```

---

## 7. RESUMEN DE CAMBIOS

### Tablas Nuevas

| Tabla | Proposito | Registros Estimados |
|-------|-----------|---------------------|
| `cascade_rules` | Reglas declarativas de cascada | ~10 |
| `cascade_valid_combinations` | Combinaciones validas normalizadas | ~75 |
| `cascade_combination_plantas` | Pivote combinacion-planta | ~150 |
| `coverage_internal_planta` | Pivote recubrimiento-planta | ~7 |
| `carton_planta` | Pivote carton-planta | ~500+ |

### Columnas a Deprecar (Fase posterior)

| Tabla | Columna | Razon |
|-------|---------|-------|
| `coverage_internals` | `planta_id` | Reemplazada por pivote |
| `cartons` | `planta_id` | Reemplazada por pivote |
| `relacion_filtro_ingresos_principales` | Toda la tabla | Reemplazada por nuevas tablas |

---

## 8. SIGUIENTE PASO

**PASO 3.3**: Definir terminos ancla para las nuevas entidades de datos.

---

**Documento generado**: 2025-12-17
**Version**: 1.0
