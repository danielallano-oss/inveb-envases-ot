# FASE 6.1: BD Minima y Metricas de Exito

**ID**: `PASO-06.01-V12`
**Fecha**: 2025-12-17
**Estado**: Completado

---

## Resumen

Este documento define las metricas de exito para la validacion del sistema INVEB Envases-OT y establece el dataset minimo necesario para ejecutar pruebas de validacion iterativa.

---

## 1. METRICAS DE EXITO

### 1.1 Categorias de Metricas

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    METRICAS DE EXITO V12                                     │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  FUNCIONALES           TECNICAS              DE NEGOCIO                     │
│  ┌─────────────────┐  ┌─────────────────┐   ┌─────────────────┐            │
│  │ Flujos OT       │  │ Performance     │   │ Cobertura       │            │
│  │ Cascade valido  │  │ Errores         │   │ Tiempo proceso  │            │
│  │ Autenticacion   │  │ Disponibilidad  │   │ Productividad   │            │
│  └─────────────────┘  └─────────────────┘   └─────────────────┘            │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 1.2 Metricas Funcionales

| ID | Metrica | Descripcion | Umbral Exito | Critico |
|----|---------|-------------|--------------|---------|
| MF-001 | Flujo OT Completo | OT puede crearse, aprobarse, rechazarse | 100% | Si |
| MF-002 | Cascade Valido | 8 pasos de cascade funcionan secuencialmente | 100% | Si |
| MF-003 | Login Funcional | Autenticacion local y Azure OAuth | 100% | Si |
| MF-004 | CRUD Clientes | Crear, editar, listar clientes | 100% | Si |
| MF-005 | CRUD Cotizaciones | Flujo cotizacion completo | 100% | No |
| MF-006 | Reportes Generan | Reportes principales sin error | 95% | No |
| MF-007 | Mantenedores CRUD | 25+ mantenedores operativos | 90% | No |

### 1.3 Metricas Tecnicas

| ID | Metrica | Descripcion | Umbral Exito | Critico |
|----|---------|-------------|--------------|---------|
| MT-001 | Tiempo Respuesta | Paginas cargan < 3s | 95% paginas | Si |
| MT-002 | Errores 500 | Sin errores de servidor | 0 errores criticos | Si |
| MT-003 | Errores 404 | Rutas existentes accesibles | < 5% rutas rotas | No |
| MT-004 | Cascade AJAX | Respuesta cascade < 500ms | 95% requests | Si |
| MT-005 | Session Activa | Sesion mantiene 120 min | 100% | No |
| MT-006 | BD Conexion | Conexion estable MySQL | 99.9% uptime | Si |

### 1.4 Metricas de Negocio

| ID | Metrica | Descripcion | Umbral Exito | Critico |
|----|---------|-------------|--------------|---------|
| MN-001 | OTs por Dia | Sistema soporta carga diaria | >= 50 OTs/dia | No |
| MN-002 | Usuarios Concurrentes | Soporte multiusuario | >= 20 usuarios | No |
| MN-003 | Cobertura Funcional | Funcionalidades documentadas operativas | >= 90% | Si |
| MN-004 | Tiempo Ciclo OT | Creacion a aprobacion | Baseline actual | No |

---

## 2. DATASET MINIMO DE PRUEBA

### 2.1 Datos Maestros Requeridos

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    DATASET MINIMO                                            │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  IDENTIDAD           CATALOGOS             CORE                             │
│  ┌─────────────────┐ ┌─────────────────┐  ┌─────────────────┐              │
│  │ 3 usuarios      │ │ 3 jerarquias    │  │ 2 clientes      │              │
│  │ 3 roles         │ │ 3 cartones      │  │ 3 OTs           │              │
│  │ 3 areas         │ │ 3 plantas       │  │ 1 cotizacion    │              │
│  └─────────────────┘ │ 3 recubrim.     │  └─────────────────┘              │
│                      │ 3 colores       │                                    │
│                      └─────────────────┘                                    │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 2.2 Tablas y Registros Minimos

| Tabla | Registros Minimos | Proposito |
|-------|-------------------|-----------|
| **users** | 3 | Admin, Vendedor, Ingeniero |
| **roles** | 3 | Administrador, Vendedor, Ingeniero |
| **work_spaces** | 3 | Ventas, Diseno, Catalogacion |
| **plantas** | 3 | BUIN, TIL TIL, OSORNO |
| **clients** | 2 | Cliente activo, Cliente inactivo |
| **hierarchies** | 3 | Nivel 1 (sectores) |
| **sub_hierarchies** | 3 | Nivel 2 |
| **sub_sub_hierarchies** | 3 | Nivel 3 (rubros) |
| **cartons** | 3 | Cartones diferentes |
| **coverage_internals** | 3 | Recubrimientos internos |
| **coverage_externals** | 3 | Recubrimientos externos |
| **colors** | 3 | Colores base |
| **impresion** | 3 | Tipos impresion |
| **fsc** | 3 | Opciones FSC |
| **work_orders** | 3 | OT pendiente, aprobada, rechazada |
| **cotizaciones** | 1 | Cotizacion de prueba |

### 2.3 Script de Dataset Minimo

```sql
-- ============================================================
-- DATASET MINIMO PARA VALIDACION INVEB ENVASES-OT
-- Fase 6.1 - Metodologia Frontend-First V12
-- ============================================================

-- USUARIOS DE PRUEBA
-- (Asumiendo que ya existen roles y areas)
INSERT INTO users (name, email, password, role_id, work_space_id, status)
VALUES
('Admin Test', 'admin@test.cl', '$2y$10$...', 1, 1, 1),
('Vendedor Test', 'vendedor@test.cl', '$2y$10$...', 4, 1, 1),
('Ingeniero Test', 'ingeniero@test.cl', '$2y$10$...', 6, 2, 1);

-- CLIENTES DE PRUEBA
INSERT INTO clients (rut, razon_social, nombre_fantasia, status)
VALUES
('76.123.456-7', 'Empresa Test S.A.', 'Test SA', 1),
('76.654.321-0', 'Cliente Inactivo Ltda', 'Inactivo', 0);

-- JERARQUIAS (si no existen)
-- Nivel 1
INSERT INTO hierarchies (nombre, status) VALUES
('ALIMENTOS', 1),
('BEBIDAS', 1),
('OTROS', 1);

-- Nivel 2
INSERT INTO sub_hierarchies (hierarchy_id, nombre, status) VALUES
(1, 'Frutas', 1),
(1, 'Verduras', 1),
(2, 'Jugos', 1);

-- Nivel 3
INSERT INTO sub_sub_hierarchies (sub_hierarchy_id, nombre, status) VALUES
(1, 'Manzanas', 1),
(1, 'Peras', 1),
(3, 'Naturales', 1);

-- ORDENES DE TRABAJO DE PRUEBA
-- (Con diferentes estados para validar flujo)
INSERT INTO work_orders (
    codigo_ot, client_id, creador_id, current_area_id,
    hierarchy_id, sub_hierarchy_id, sub_sub_hierarchy_id,
    carton_id, planta_id, status
)
VALUES
('OT-2025-000001', 1, 1, 1, 1, 1, 1, 1, 1, 'pending'),
('OT-2025-000002', 1, 1, 2, 1, 1, 1, 1, 1, 'approved'),
('OT-2025-000003', 1, 1, 1, 2, 2, 2, 2, 2, 'rejected');
```

---

## 3. CRITERIOS DE VALIDACION

### 3.1 Matriz de Validacion

| Escenario | Precondicion | Accion | Resultado Esperado | Metrica |
|-----------|--------------|--------|-------------------|---------|
| VAL-001 | Usuario logueado | Crear OT | OT creada con codigo | MF-001 |
| VAL-002 | OT pendiente | Aprobar OT | Estado = approved | MF-001 |
| VAL-003 | OT pendiente | Rechazar OT | Estado = rejected | MF-001 |
| VAL-004 | Formulario OT | Seleccionar Jerarquia1 | Jerarquia2 se filtra | MF-002 |
| VAL-005 | Formulario OT | Completar cascade | 8 campos validos | MF-002 |
| VAL-006 | Login page | Login local | Sesion iniciada | MF-003 |
| VAL-007 | Login page | Login Azure | Redirect y sesion | MF-003 |
| VAL-008 | Lista clientes | Crear cliente | Cliente guardado | MF-004 |
| VAL-009 | Detalle cliente | Editar cliente | Cambios guardados | MF-004 |
| VAL-010 | Home | Cargar dashboard | < 3 segundos | MT-001 |

### 3.2 Secuencia de Cascade (8 Pasos)

```
Validacion Cascade:
┌────────────────────────────────────────────────────────────────────────┐
│ PASO │ CAMPO              │ AJAX ENDPOINT                │ VALIDA     │
├──────┼────────────────────┼─────────────────────────────┼────────────┤
│  1   │ Jerarquia 1        │ /ajax/hierarchies           │ Lista      │
│  2   │ Jerarquia 2        │ /ajax/sub-hierarchies/{id}  │ Filtrado   │
│  3   │ Jerarquia 3        │ /ajax/subsub-hierarchies/{id}│ Filtrado   │
│  4   │ Carton             │ /ajax/cartons/{id}          │ Filtrado   │
│  5   │ Recubrimiento Int  │ /ajax/coverage-int/{id}     │ Filtrado   │
│  6   │ Recubrimiento Ext  │ /ajax/coverage-ext/{id}     │ Filtrado   │
│  7   │ Color              │ /ajax/colors/{id}           │ Filtrado   │
│  8   │ Planta             │ /ajax/plantas/{id}          │ Disponible │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 4. PLAN DE EJECUCION

### 4.1 Fases de Validacion

| Fase | Actividad | Duracion | Dependencia |
|------|-----------|----------|-------------|
| V1 | Verificar BD conecta | 5 min | Docker up |
| V2 | Verificar login funciona | 10 min | V1 |
| V3 | Crear OT de prueba | 15 min | V2 |
| V4 | Validar cascade completo | 20 min | V3 |
| V5 | Probar aprobacion/rechazo | 10 min | V3 |
| V6 | Verificar reportes | 15 min | V3 |
| V7 | Probar mantenedores | 20 min | V2 |

### 4.2 Checklist Pre-Validacion

- [ ] Docker containers corriendo (inveb-app, inveb-mysql)
- [ ] BD envases_ot accesible
- [ ] Dataset minimo insertado
- [ ] Usuario admin puede loguearse
- [ ] Rutas principales responden (200 OK)

---

## 5. GAPS CRITICOS A VALIDAR

### 5.1 Gaps Identificados en Fase 5

| Gap ID | Descripcion | Impacto Validacion | Accion |
|--------|-------------|-------------------|--------|
| GAP-003 | Cascade hardcoded | ALTO | Validar JS funciona |
| GAP-002 | Logica en controladores | MEDIO | Validar flujos |
| GAP-004 | Sin capa Service | MEDIO | N/A para validacion |

### 5.2 Riesgos de Validacion

| Riesgo | Probabilidad | Impacto | Mitigacion |
|--------|--------------|---------|------------|
| BD sin datos | Alta | Critico | Ejecutar script dataset |
| Cascade JS falla | Media | Alto | Revisar console.log |
| Sesion expira | Baja | Medio | Verificar config |
| Rutas rotas | Media | Medio | Probar rutas criticas |

---

## 6. INTEGRACION CON NEO4J

```cypher
// Crear nodo de Fase 6.1
CREATE (f61:FaseValidacion {
  id: 'FASE-6.1-V12',
  nombre: 'BD Minima y Metricas de Exito',
  fecha: datetime(),

  metricas_funcionales: 7,
  metricas_tecnicas: 6,
  metricas_negocio: 4,

  tablas_dataset: 16,
  escenarios_validacion: 10,

  gaps_criticos: 1,
  riesgos_identificados: 4,

  estado: 'COMPLETADO',
  fase: '6.1'
});

// Relacionar con Fase 5
MATCH (f5:FaseConocimiento {id: 'FASE-5-V12'})
MATCH (f61:FaseValidacion {id: 'FASE-6.1-V12'})
CREATE (f5)-[:SIGUIENTE]->(f61);
```

---

## 7. RESUMEN

### Estado: COMPLETADO

| Aspecto | Detalle |
|---------|---------|
| Metricas definidas | 17 (7 funcionales + 6 tecnicas + 4 negocio) |
| Tablas dataset | 16 tablas minimas |
| Escenarios validacion | 10 escenarios |
| Gaps criticos | 1 (Cascade hardcoded) |
| Riesgos | 4 identificados |

### Siguiente Paso

**FASE 6.2**: Pruebas de Integracion - Ejecutar validaciones con dataset minimo.

---

**Documento generado**: 2025-12-17
**Version**: 1.0
**Fase**: 6.1 - BD Minima y Metricas
