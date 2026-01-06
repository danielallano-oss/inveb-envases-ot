# FASE 3.4: Conocimiento - Base de Conocimiento de Tablas

**ID**: `PASO-03.04-V12`
**Fecha**: 2025-12-17
**Estado**: Completado

---

## Resumen

Este documento consolida todo el conocimiento de tablas del sistema INVEB Envases-OT en un formato estructurado para consumo de IA y agentes. Integra la inferencia (3.1), estructura (3.2) y terminos (3.3) en una base de conocimiento unificada.

---

## 1. GRAFO DE CONOCIMIENTO

### 1.1 Nodos por Tipo

| Tipo Nodo | Cantidad | Descripcion |
|-----------|----------|-------------|
| Tabla | 15 | Tablas existentes analizadas |
| TablaNueva | 5 | Tablas propuestas para normalizacion |
| TerminoTabla | 20 | Terminos ancla de negocio |
| Campo | 23 | Campos especificos documentados |
| ReglaDB | 7 | Relaciones de cascada |

### 1.2 Relaciones

| Relacion | Origen | Destino | Cantidad |
|----------|--------|---------|----------|
| CASCADA_HACIA | Tabla | Tabla | 7 |
| REEMPLAZADA_POR | Tabla | TablaNueva | 1 |
| NORMALIZADA_CON | Tabla | TablaNueva | 5 |
| MAPEA_A | TerminoTabla | Tabla | 20 |
| TIENE_CAMPO | Tabla | Campo | 23 |

---

## 2. ENTIDADES DE CONOCIMIENTO

### 2.1 Tabla Principal: work_orders

```yaml
entidad: work_orders
tipo: tabla_core
alias: [orden de trabajo, OT, solicitud, requerimiento]
campos_total: ~270
campos_cascada: 10
relaciones:
  - clients (client_id)
  - users (creador_id)
  - states (current_area_id)
  - product_types (product_type_id)
  - impresion (impresion)
  - fsc (fsc)
  - tipos_cintas (tipo_cinta)
  - coverage_internals (coverage_internal_id)
  - coverage_externals (coverage_external_id)
  - plantas (planta_id)
  - cartons (carton_id)
problemas:
  - campo current_area_id mal nombrado (deberia ser state_id)
  - campos multi-valor (planta_id="1,2,3")
  - sin foreign keys formales
```

### 2.2 Tablas de Cascada

```yaml
secuencia_cascada:
  - paso: 1
    tabla: product_types
    campo_ot: product_type_id
    alias: [tipo item, tipo producto]
    dispara: impresion

  - paso: 2
    tabla: impresion
    campo_ot: impresion
    alias: [tipo impresion, print type]
    dispara: fsc
    valores:
      - id: 1, nombre: Offset
      - id: 2, nombre: Flexografia
      - id: 3, nombre: Alta Grafica
      - id: 5, nombre: Sin Impresion

  - paso: 3
    tabla: fsc
    campo_ot: fsc
    alias: [certificacion fsc, sello fsc]
    dispara: tipos_cintas

  - paso: 4
    tabla: tipos_cintas
    campo_ot: tipo_cinta
    alias: [tipo cinta, tape type]
    dispara: coverage_internals

  - paso: 5
    tabla: coverage_internals
    campo_ot: coverage_internal_id
    alias: [recubrimiento interno, cobertura interna]
    dispara: coverage_externals
    restriccion_planta: true

  - paso: 6
    tabla: coverage_externals
    campo_ot: coverage_external_id
    alias: [recubrimiento externo, cobertura externa]
    dispara: plantas
    restriccion_planta: true

  - paso: 7
    tabla: plantas
    campo_ot: planta_id
    alias: [planta, fabrica, centro]
    dispara: cartons
    valores:
      - id: 1, nombre: Buin
      - id: 2, nombre: TilTil
      - id: 3, nombre: Osorno

  - paso: 8
    tabla: cartons
    campo_ot: carton_id
    alias: [carton, material, sustrato]
    dispara: null
```

### 2.3 Tabla de Reglas Actual

```yaml
entidad: relacion_filtro_ingresos_principales
tipo: tabla_reglas
registros: 75
columnas:
  - product_type_id
  - impresion_id
  - fsc_id
  - tipo_cinta
  - coverage_internal
  - coverage_external
  - planta_id (PROBLEMA: multi-valor "1,2,3")
  - carton_id
problemas:
  - planta_id almacena IDs concatenados
  - no hay normalizacion
  - dificil de mantener
solucion_propuesta: cascade_valid_combinations + tablas pivote
```

---

## 3. CONOCIMIENTO DE NEGOCIO

### 3.1 Reglas de Negocio Criticas

```yaml
reglas:
  - id: RN-CASC-001
    descripcion: Impresion determina opciones FSC
    cuando: usuario selecciona impresion
    entonces: cargar opciones FSC validas
    tabla_validacion: relacion_filtro_ingresos_principales

  - id: RN-CASC-002
    descripcion: FSC determina opciones de cinta
    cuando: usuario selecciona fsc
    entonces: cargar tipos de cinta validos
    tabla_validacion: relacion_filtro_ingresos_principales

  - id: RN-CASC-003
    descripcion: Cera solo disponible en Buin
    cuando: coverage = cera
    entonces: planta = solo Buin (id=1)
    restriccion: exclusiva

  - id: RN-CASC-004
    descripcion: Combinacion debe existir en filtro
    cuando: usuario completa ingresos principales
    entonces: validar combinacion existe
    tabla_validacion: relacion_filtro_ingresos_principales
```

### 3.2 Dependencias de Planta

```yaml
dependencias_planta:
  buin:
    id: 1
    coverage_internals: [todos]
    coverage_externals: [todos]
    cartons: [todos]
    exclusivo: cera

  tiltil:
    id: 2
    coverage_internals: [excepto cera]
    coverage_externals: [todos]
    cartons: [subconjunto]

  osorno:
    id: 3
    coverage_internals: [excepto cera]
    coverage_externals: [subconjunto]
    cartons: [subconjunto]
```

---

## 4. CONOCIMIENTO ESTRUCTURAL

### 4.1 Tablas Nuevas Propuestas

```yaml
tablas_nuevas:
  - nombre: cascade_rules
    proposito: Definicion declarativa de reglas de cascada
    campos_clave:
      - trigger_field: Campo que dispara
      - target_field: Campo afectado
      - action: enable/disable/setValue/loadOptions
      - cascade_order: Secuencia de ejecucion
    beneficios:
      - Reglas configurables sin codigo
      - Auditoria de cambios
      - Facil mantenimiento

  - nombre: cascade_valid_combinations
    proposito: Combinaciones validas normalizadas
    campos_clave:
      - cascade_step: Paso de cascada
      - source_value: Valor origen
      - target_value: Valor destino permitido
    reemplaza: relacion_filtro_ingresos_principales (parcial)

  - nombre: coverage_internal_planta
    proposito: Pivote N:M coverage_internals <-> plantas
    normaliza: campo multi-valor planta_id

  - nombre: coverage_external_planta
    proposito: Pivote N:M coverage_externals <-> plantas
    normaliza: campo multi-valor planta_id

  - nombre: carton_planta
    proposito: Pivote N:M cartons <-> plantas
    normaliza: campo multi-valor planta_id
```

### 4.2 Migracion de Datos

```yaml
migracion:
  origen: relacion_filtro_ingresos_principales.planta_id
  formato_actual: "1,2,3" (string concatenado)
  formato_destino: registros individuales en tabla pivote

  ejemplo:
    antes:
      registro_id: 1
      planta_id: "1,2,3"
    despues:
      - pivote_id: 1, filtro_id: 1, planta_id: 1
      - pivote_id: 2, filtro_id: 1, planta_id: 2
      - pivote_id: 3, filtro_id: 1, planta_id: 3
```

---

## 5. CONSULTAS DE CONOCIMIENTO

### 5.1 Busqueda por Termino

```cypher
// Encontrar tabla por termino de negocio
MATCH (t:TerminoTabla)-[:MAPEA_A]->(tabla:Tabla)
WHERE t.valor CONTAINS $termino OR t.sinonimos CONTAINS $termino
RETURN tabla.nombre, tabla.descripcion

// Ejemplo: buscar "orden de trabajo"
// Resultado: work_orders
```

### 5.2 Navegar Cascada

```cypher
// Obtener secuencia completa de cascada
MATCH path = (inicio:Tabla {nombre: 'product_types'})-[:CASCADA_HACIA*]->(fin:Tabla)
RETURN [n IN nodes(path) | n.nombre] AS secuencia

// Resultado: [product_types, impresion, fsc, tipos_cintas,
//             coverage_internals, coverage_externals, plantas, cartons]
```

### 5.3 Identificar Problemas

```cypher
// Tablas con problemas de normalizacion
MATCH (t:Tabla)-[:NORMALIZADA_CON]->(nueva:TablaNueva)
RETURN t.nombre AS tabla_problema,
       t.problema AS descripcion_problema,
       nueva.nombre AS solucion_propuesta
```

---

## 6. PROMPT DE CONTEXTO PARA IA

### 6.1 Contexto de Tablas

```
CONTEXTO INVEB ENVASES-OT - TABLAS:

Sistema: Gestion de Ordenes de Trabajo para envases de carton corrugado
Base de Datos: MySQL 8.0, 160 tablas

TABLA PRINCIPAL:
- work_orders: ~270 campos, tabla central del sistema
- Alias: orden de trabajo, OT, solicitud

CASCADA DE CAMPOS (8 pasos):
product_types -> impresion -> fsc -> tipos_cintas ->
coverage_internals -> coverage_externals -> plantas -> cartons

PLANTAS DISPONIBLES:
- Buin (id=1): Planta principal, unica con cera
- TilTil (id=2): Planta secundaria
- Osorno (id=3): Planta sur

PROBLEMA CONOCIDO:
- Campo planta_id almacena IDs concatenados ("1,2,3")
- Solucion: Tablas pivote (*_planta)

TABLA DE VALIDACION:
- relacion_filtro_ingresos_principales: 75 combinaciones validas
```

### 6.2 Instrucciones para Agente

```
INSTRUCCIONES PARA CONSULTAS DE TABLAS:

1. Cuando el usuario mencione "OT" o "orden de trabajo":
   - Tabla: work_orders
   - Campos clave: id, codigo, client_id, current_area_id

2. Cuando pregunte sobre cascada:
   - Secuencia: product_types -> impresion -> fsc -> tipos_cintas ->
     coverage_internals -> coverage_externals -> plantas -> cartons
   - Validacion: relacion_filtro_ingresos_principales

3. Cuando mencione plantas:
   - Buin = id 1
   - TilTil = id 2
   - Osorno = id 3

4. Cuando pregunte sobre recubrimientos:
   - Interno: coverage_internals
   - Externo: coverage_externals
   - Cera: solo disponible en Buin
```

---

## 7. INTEGRACION CON NEO4J

### 7.1 Script de Carga de Conocimiento

```cypher
// Crear nodo de conocimiento consolidado
CREATE (k:Conocimiento {
  id: 'KC-TABLAS-001',
  fase: '3.4',
  fecha: datetime(),
  version: '1.0',
  tablas_analizadas: 15,
  tablas_nuevas: 5,
  terminos: 20,
  reglas_cascada: 7
});

// Crear relaciones de conocimiento
MATCH (k:Conocimiento {id: 'KC-TABLAS-001'})
MATCH (t:Tabla)
CREATE (k)-[:DOCUMENTA]->(t);

MATCH (k:Conocimiento {id: 'KC-TABLAS-001'})
MATCH (tn:TablaNueva)
CREATE (k)-[:PROPONE]->(tn);

MATCH (k:Conocimiento {id: 'KC-TABLAS-001'})
MATCH (tt:TerminoTabla)
CREATE (k)-[:DEFINE]->(tt);
```

### 7.2 Nodos de Regla de Negocio

```cypher
// Crear nodos de reglas de negocio
CREATE (:ReglaNegocio {
  id: 'RN-CASC-001',
  descripcion: 'Impresion determina opciones FSC',
  tipo: 'cascada',
  tabla_origen: 'impresion',
  tabla_destino: 'fsc'
});

CREATE (:ReglaNegocio {
  id: 'RN-CASC-003',
  descripcion: 'Cera solo disponible en Buin',
  tipo: 'restriccion',
  condicion: 'coverage = cera',
  resultado: 'planta = 1'
});
```

---

## 8. RESUMEN DE CONOCIMIENTO

### 8.1 Metricas

| Metrica | Valor |
|---------|-------|
| Tablas existentes documentadas | 15 |
| Tablas nuevas propuestas | 5 |
| Terminos ancla definidos | 77 |
| Relaciones de cascada | 7 |
| Reglas de negocio criticas | 4 |
| Combinaciones validas | 75 |
| Problemas identificados | 3 |

### 8.2 Problemas y Soluciones

| Problema | Impacto | Solucion |
|----------|---------|----------|
| Multi-valor planta_id | Consultas complejas | Tablas pivote |
| Sin foreign keys | Sin integridad | ALTER TABLE ADD CONSTRAINT |
| current_area_id mal nombrado | Confusion | Alias en documentacion |

### 8.3 Artefactos Generados

| Artefacto | Ubicacion |
|-----------|-----------|
| Inferencia de tablas | FASE_3_1_INFERENCIA_TABLAS.md |
| Estructura de datos | FASE_3_2_ESTRUCTURA_DATOS.md |
| Terminos ancla | FASE_3_3_TERMINOS_TABLAS.md |
| Conocimiento consolidado | FASE_3_4_CONOCIMIENTO_TABLAS.md |
| Grafo Neo4J | Nodos: Tabla, TablaNueva, TerminoTabla |

---

## 9. SIGUIENTE PASO

**PASO 3.5**: Investigacion - Validacion y QA del conocimiento de tablas.

---

**Documento generado**: 2025-12-17
**Version**: 1.0
