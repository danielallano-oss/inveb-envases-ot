# FASE 3.3: Terminos Ancla - Entidades de Datos

**ID**: `PASO-03.03-V12`
**Fecha**: 2025-12-17
**Estado**: Completado

---

## Resumen

Este documento define los terminos ancla relacionados con las entidades de datos (tablas) del sistema INVEB Envases-OT. Los terminos ancla permiten mapear el vocabulario del negocio con las estructuras tecnicas.

---

## 1. TERMINOS DE TABLAS PRINCIPALES

### 1.1 Entidades Core

| ID | Termino | Sinonimos | Tabla | Descripcion |
|----|---------|-----------|-------|-------------|
| TA-TBL-001 | orden de trabajo | OT, solicitud, requerimiento | `work_orders` | Registro principal de solicitud de desarrollo |
| TA-TBL-002 | usuario | operador, empleado, trabajador | `users` | Persona con acceso al sistema |
| TA-TBL-003 | cliente | cuenta, empresa, comprador | `clients` | Entidad comercial que solicita productos |
| TA-TBL-004 | gestion | seguimiento, accion, registro | `managements` | Accion realizada sobre una OT |
| TA-TBL-005 | muestra | prototipo, sample, ejemplar | `muestras` | Muestra fisica de producto |
| TA-TBL-006 | cotizacion | presupuesto, quote, oferta | `cotizacions` | Propuesta economica al cliente |
| TA-TBL-007 | notificacion | alerta, aviso, mensaje | `notifications` | Comunicacion automatica del sistema |

### 1.2 Entidades de Workflow

| ID | Termino | Sinonimos | Tabla | Descripcion |
|----|---------|-----------|-------|-------------|
| TA-TBL-008 | estado | status, etapa, fase | `states` | Situacion actual de una OT en el flujo |
| TA-TBL-009 | area | departamento, seccion, workspace | `work_spaces` | Division organizacional |
| TA-TBL-010 | rol | perfil, cargo, permiso | `roles` | Conjunto de permisos de usuario |

### 1.3 Entidades de Jerarquia de Producto

| ID | Termino | Sinonimos | Tabla | Descripcion |
|----|---------|-----------|-------|-------------|
| TA-TBL-011 | canal | channel, via, medio | `canals` | Canal de venta del producto |
| TA-TBL-012 | jerarquia | hierarchy, categoria, nivel1 | `hierarchies` | Primer nivel de clasificacion |
| TA-TBL-013 | subjerarquia | subcategoria, nivel2 | `subhierarchies` | Segundo nivel de clasificacion |
| TA-TBL-014 | subsubjerarquia | nivel3, clasificacion final | `subsubhierarchies` | Tercer nivel de clasificacion |

---

## 2. TERMINOS DE TABLAS DE CASCADA

### 2.1 Campos de Ingresos Principales

| ID | Termino | Sinonimos | Tabla/Campo | Descripcion |
|----|---------|-----------|-------------|-------------|
| TA-CASC-001 | tipo item | tipo producto, product type | `product_types` | Tipo de envase (Caja, Fondo, Tapa, etc.) |
| TA-CASC-002 | impresion | tipo impresion, print type | `impresion` | Metodo de impresion (Offset, Flexografia, etc.) |
| TA-CASC-003 | fsc | certificacion fsc, sello fsc | `fsc` | Certificacion Forest Stewardship Council |
| TA-CASC-004 | cinta | tipo cinta, tape | `tipos_cintas` | Tipo de cinta para el envase |
| TA-CASC-005 | recubrimiento interno | cobertura interna, coverage int | `coverage_internals` | Tratamiento interior del carton |
| TA-CASC-006 | recubrimiento externo | cobertura externa, coverage ext | `coverage_externals` | Tratamiento exterior del carton |
| TA-CASC-007 | planta | fabrica, plant, centro | `plantas` | Planta de produccion (Buin, TilTil, Osorno) |
| TA-CASC-008 | carton | material, board, sustrato | `cartons` | Tipo de carton corrugado |
| TA-CASC-009 | color carton | color material, tono | `work_orders.carton_color` | Color del carton seleccionado |

### 2.2 Valores de Cascada

| ID | Termino | Tabla | Valores |
|----|---------|-------|---------|
| TA-CASC-010 | offset | `impresion` | id=1, impresion de alta calidad |
| TA-CASC-011 | flexografia | `impresion` | id=2, impresion estandar |
| TA-CASC-012 | alta grafica | `impresion` | id=3, flexografia premium |
| TA-CASC-013 | sin impresion | `impresion` | id=5, producto sin imprimir |
| TA-CASC-014 | buin | `plantas` | id=1, planta principal |
| TA-CASC-015 | tiltil | `plantas` | id=2, planta secundaria |
| TA-CASC-016 | osorno | `plantas` | id=3, planta sur |
| TA-CASC-017 | barniz | `coverage_*` | recubrimiento liquido protector |
| TA-CASC-018 | cera | `coverage_internals` | recubrimiento hidrofobico (solo Buin) |

### 2.3 Tabla de Reglas

| ID | Termino | Sinonimos | Tabla | Descripcion |
|----|---------|-----------|-------|-------------|
| TA-CASC-019 | combinacion valida | regla cascada, filtro | `relacion_filtro_ingresos_principales` | Combinacion permitida de campos |
| TA-CASC-020 | ingresos principales | campos cascada, main inputs | N/A | Conjunto de campos con dependencia |

---

## 3. TERMINOS DE TABLAS NUEVAS (Propuestas)

### 3.1 Sistema de Reglas

| ID | Termino | Sinonimos | Tabla Nueva | Descripcion |
|----|---------|-----------|-------------|-------------|
| TA-NEW-001 | regla cascada | cascade rule, trigger rule | `cascade_rules` | Definicion declarativa de comportamiento |
| TA-NEW-002 | campo trigger | disparador, source field | `cascade_rules.trigger_field` | Campo que inicia la cascada |
| TA-NEW-003 | campo target | objetivo, destino | `cascade_rules.target_field` | Campo afectado por la cascada |
| TA-NEW-004 | accion | action, operacion | `cascade_rules.action` | Que hacer (enable, disable, setValue) |
| TA-NEW-005 | orden cascada | secuencia, prioridad | `cascade_rules.cascade_order` | Posicion en la cadena de cascada |

### 3.2 Combinaciones Normalizadas

| ID | Termino | Sinonimos | Tabla Nueva | Descripcion |
|----|---------|-----------|-------------|-------------|
| TA-NEW-006 | paso cascada | cascade step, etapa | `cascade_valid_combinations.cascade_step` | Identificador del paso (impresion_fsc, cinta, etc.) |
| TA-NEW-007 | valor origen | source value | `cascade_valid_combinations.source_value` | Valor del campo que dispara |
| TA-NEW-008 | valor destino | target value | `cascade_valid_combinations.target_value` | Valor valido del campo destino |

### 3.3 Tablas Pivote

| ID | Termino | Sinonimos | Tabla Nueva | Descripcion |
|----|---------|-----------|-------------|-------------|
| TA-NEW-009 | pivote | relacion m:n, junction table | `*_planta` | Tabla intermedia muchos-a-muchos |
| TA-NEW-010 | normalizacion | denormalize fix | N/A | Proceso de separar campos multi-valor |

---

## 4. TERMINOS DE CAMPOS ESPECIFICOS

### 4.1 Campos de work_orders (Cascada)

| ID | Campo | Termino Negocio | Tipo | Descripcion |
|----|-------|-----------------|------|-------------|
| TA-FLD-001 | `product_type_id` | tipo de item | FK | Tipo de producto/envase |
| TA-FLD-002 | `impresion` | tipo impresion | FK | Metodo de impresion |
| TA-FLD-003 | `fsc` | certificacion fsc | FK | Nivel de certificacion FSC |
| TA-FLD-004 | `cinta` | usa cinta | boolean | Si lleva cinta |
| TA-FLD-005 | `tipo_cinta` | tipo de cinta | FK | Tipo de cinta si aplica |
| TA-FLD-006 | `coverage_internal_id` | recubrimiento int | FK | Tratamiento interior |
| TA-FLD-007 | `coverage_external_id` | recubrimiento ext | FK | Tratamiento exterior |
| TA-FLD-008 | `planta_id` | planta produccion | FK | Donde se fabrica |
| TA-FLD-009 | `carton_id` | tipo carton | FK | Material base |
| TA-FLD-010 | `carton_color` | color carton | FK | Color del material |

### 4.2 Campos de work_orders (Dimensiones)

| ID | Campo | Termino Negocio | Tipo | Descripcion |
|----|-------|-----------------|------|-------------|
| TA-FLD-011 | `interno_largo` | largo interno | int | Dimension interna largo (mm) |
| TA-FLD-012 | `interno_ancho` | ancho interno | int | Dimension interna ancho (mm) |
| TA-FLD-013 | `interno_alto` | alto interno | int | Dimension interna alto (mm) |
| TA-FLD-014 | `externo_largo` | largo externo | int | Dimension externa largo (mm) |
| TA-FLD-015 | `externo_ancho` | ancho externo | int | Dimension externa ancho (mm) |
| TA-FLD-016 | `externo_alto` | alto externo | int | Dimension externa alto (mm) |
| TA-FLD-017 | `largura_hm` | largo hoja | int | Dimension hoja montada largo |
| TA-FLD-018 | `anchura_hm` | ancho hoja | int | Dimension hoja montada ancho |

### 4.3 Campos de work_orders (Workflow)

| ID | Campo | Termino Negocio | Tipo | Descripcion |
|----|-------|-----------------|------|-------------|
| TA-FLD-019 | `current_area_id` | estado actual | FK | Estado actual (mal nombrado, es state) |
| TA-FLD-020 | `creador_id` | creador | FK | Usuario que creo la OT |
| TA-FLD-021 | `terminado` | finalizada | boolean | Si la OT esta completa |
| TA-FLD-022 | `active` | activa | boolean | Si la OT esta vigente |
| TA-FLD-023 | `tipo_solicitud` | tipo OT | int | 1:Nueva, 2:MuestraSin, 3:MuestraCon, 4:Duplicar |

---

## 5. GLOSARIO DE CONCEPTOS TECNICOS

### 5.1 Conceptos de Base de Datos

| Termino | Definicion | Ejemplo INVEB |
|---------|------------|---------------|
| FK (Foreign Key) | Clave foranea, referencia a otra tabla | `work_orders.client_id` -> `clients.id` |
| Tabla Pivote | Tabla intermedia para relacion N:M | `coverage_internal_planta` |
| Normalizacion | Proceso de eliminar redundancia | Separar `planta_id="1,2,3"` en registros |
| Campo Multi-valor | Anti-patron: multiples valores en un campo | `planta_id = "1,2,3"` |
| Cascada | Dependencia entre campos de formulario | `impresion` -> habilita -> `fsc` |

### 5.2 Conceptos de Negocio INVEB

| Termino | Definicion | Contexto |
|---------|------------|----------|
| OT | Orden de Trabajo, solicitud de desarrollo | Documento central del sistema |
| Ingresos Principales | Campos con dependencia en cascada | Seccion del formulario OT |
| Combinacion Valida | Conjunto de valores permitidos juntos | Controlado por `relacion_filtro_*` |
| Hoja Montada (HM) | Carton cortado listo para convertir | Dimensiones largura_hm, anchura_hm |
| FSC | Forest Stewardship Council | Certificacion de sostenibilidad |

---

## 6. MAPEO TERMINO -> TABLA

### Vista Rapida de Busqueda

```
"orden de trabajo" -> work_orders
"OT"               -> work_orders
"cliente"          -> clients
"usuario"          -> users
"estado"           -> states
"area"             -> work_spaces
"rol"              -> roles
"impresion"        -> impresion (tabla) o work_orders.impresion (campo)
"fsc"              -> fsc (tabla) o work_orders.fsc (campo)
"planta"           -> plantas
"carton"           -> cartons
"recubrimiento"    -> coverage_internals / coverage_externals
"cascada"          -> relacion_filtro_ingresos_principales (actual)
                   -> cascade_rules + cascade_valid_combinations (propuesto)
"regla"            -> cascade_rules (propuesto)
"combinacion"      -> cascade_valid_combinations (propuesto)
"pivote"           -> *_planta (propuesto)
```

---

## 7. TERMINOS POR CATEGORIA (Neo4J)

### Para carga en Neo4J:

```javascript
const TERMINOS_TABLAS = [
  // Tablas Core
  { valor: "orden de trabajo", categoria: "tabla_core", tabla: "work_orders" },
  { valor: "ot", categoria: "tabla_core", tabla: "work_orders" },
  { valor: "cliente", categoria: "tabla_core", tabla: "clients" },
  { valor: "usuario", categoria: "tabla_core", tabla: "users" },

  // Tablas Workflow
  { valor: "estado", categoria: "tabla_workflow", tabla: "states" },
  { valor: "area", categoria: "tabla_workflow", tabla: "work_spaces" },
  { valor: "rol", categoria: "tabla_workflow", tabla: "roles" },

  // Tablas Cascada
  { valor: "tipo item", categoria: "tabla_cascada", tabla: "product_types" },
  { valor: "impresion", categoria: "tabla_cascada", tabla: "impresion" },
  { valor: "fsc", categoria: "tabla_cascada", tabla: "fsc" },
  { valor: "cinta", categoria: "tabla_cascada", tabla: "tipos_cintas" },
  { valor: "recubrimiento interno", categoria: "tabla_cascada", tabla: "coverage_internals" },
  { valor: "recubrimiento externo", categoria: "tabla_cascada", tabla: "coverage_externals" },
  { valor: "planta", categoria: "tabla_cascada", tabla: "plantas" },
  { valor: "carton", categoria: "tabla_cascada", tabla: "cartons" },

  // Plantas especificas
  { valor: "buin", categoria: "planta", tabla: "plantas", id: 1 },
  { valor: "tiltil", categoria: "planta", tabla: "plantas", id: 2 },
  { valor: "osorno", categoria: "planta", tabla: "plantas", id: 3 },

  // Tablas Nuevas
  { valor: "regla cascada", categoria: "tabla_nueva", tabla: "cascade_rules" },
  { valor: "combinacion valida", categoria: "tabla_nueva", tabla: "cascade_valid_combinations" },
  { valor: "pivote", categoria: "tabla_nueva", tabla: "*_planta" }
];
```

---

## 8. RESUMEN ESTADISTICO

| Categoria | Cantidad de Terminos |
|-----------|---------------------|
| Tablas Core | 7 |
| Tablas Workflow | 3 |
| Tablas Jerarquia | 4 |
| Campos Cascada | 20 |
| Tablas Nuevas | 10 |
| Campos Especificos | 23 |
| Glosario Tecnico | 10 |
| **Total** | **77** |

---

## 9. SIGUIENTE PASO

**PASO 3.4**: Estrategia de Migracion - Definir plan de migracion de datos.

---

**Documento generado**: 2025-12-17
**Version**: 1.0
