# FASE 2.2B: Reglas de Cascada de Campos - Formulario OT

**ID**: `PASO-02.02B-V12`
**Fecha**: 2025-12-17
**Estado**: Completado

---

## Resumen

El formulario de creacion/edicion de OT tiene un sistema complejo de campos en cascada donde cada campo habilita o deshabilita otros campos segun reglas de negocio especificas.

**Archivos clave:**
- `public/js/ot-creation.js` - Logica de creacion
- `public/js/ot-edition.js` - Logica de edicion
- `public/js/ot-duplication.js` - Logica de duplicacion
- Tabla: `relacion_filtro_ingresos_principales` - 75 registros de combinaciones validas

---

## CASCADA 1: JERARQUIAS

### Flujo de Campos

```
CANAL → JERARQUIA 1 → JERARQUIA 2 → JERARQUIA 3
```

### Reglas Detalladas

#### REGLA JER-001: Canal Auto-Selecciona Jerarquia

```javascript
Al cambiar CANAL:
  Si canal == 1 → hierarchy_id = 3
  Si canal == 2 → hierarchy_id = 5
  Si canal == 3 → hierarchy_id = 4
  Si canal == 4 → hierarchy_id = 2
  Si canal == 5 → hierarchy_id = 1
  Si canal == 6 → hierarchy_id = 6
```

**Archivo:** `ot-creation.js:124-172`

#### REGLA JER-002: Jerarquia Habilita Subjerarquia

```javascript
Estado inicial:
  - hierarchy_id: DESHABILITADO
  - subhierarchy_id: DESHABILITADO
  - subsubhierarchy_id: DESHABILITADO

Al cambiar HIERARCHY_ID:
  1. Llamar AJAX GET /getJerarquia2?hierarchy_id=X
  2. Habilitar subhierarchy_id
  3. Limpiar y deshabilitar subsubhierarchy_id
```

**Archivo:** `ot-creation.js:20-54`

#### REGLA JER-003: Subjerarquia Habilita Sub-Subjerarquia

```javascript
Al cambiar SUBHIERARCHY_ID:
  1. Llamar AJAX GET /getJerarquia3?subhierarchy_id=X
  2. Habilitar subsubhierarchy_id
```

**Archivo:** `ot-creation.js:56-78`

---

## CASCADA 2: INGRESOS PRINCIPALES (Critica)

### Flujo de Campos

```
TIPO ITEM → IMPRESION → FSC → CINTA → RECUB. INTERNO → RECUB. EXTERNO → PLANTA → COLOR CARTON
    ↓           ↓         ↓      ↓           ↓               ↓            ↓           ↓
   (1)         (2)       (3)    (4)         (5)             (6)          (7)         (8)
```

### Estado Inicial

```javascript
Al cargar formulario:
  - Todos los campos de Ingresos Principales: DESHABILITADOS
  - Campos: impresion, fsc, cinta, coverage_internal_id,
            coverage_external_id, planta_id, carton_color, carton_id
```

**Archivo:** `ot-creation.js:189-196`

---

### REGLA CASC-001: TIPO ITEM Habilita IMPRESION

```javascript
Al cambiar PRODUCT_TYPE_ID:
  SI tiene valor:
    1. Copiar texto a product_type_id_text
    2. Habilitar campo IMPRESION
    3. Limpiar error del campo
  SINO:
    1. Limpiar product_type_id_text
```

**Campos afectados:** `impresion, impresion_text`
**Archivo:** `ot-creation.js:200-224`

---

### REGLA CASC-002: IMPRESION Habilita FSC

```javascript
Al cambiar IMPRESION:
  SI tiene valor:
    1. Copiar texto a impresion_text
    2. SI existe fsc_instalation (FSC del cliente):
         - Habilitar FSC con valor predeterminado
         - Disparar evento change de FSC
       SINO:
         - Habilitar FSC vacio
    3. LIMPIAR Y DESHABILITAR todos los campos posteriores:
       - cinta, cinta_text
       - coverage_internal_id, coverage_internal_id_text
       - coverage_external_id, coverage_external_id_text
       - planta_id, planta_id_text
       - carton_color, carton_color_text
       - carton_id, carton_id_text
       - planta_original_sec_ope, planta_aux_1_sec_ope, planta_aux_2_sec_ope
  SINO:
    1. DESHABILITAR todos los campos desde FSC en adelante
```

**Archivo:** `ot-creation.js:227-315`

---

### REGLA CASC-003: FSC Habilita CINTA

```javascript
Al cambiar FSC:
  SI tiene valor:
    1. Copiar texto a fsc_text
    2. Construir filtro:
       filtro = [{impresion_id, fsc_id, referencia: 'impresion_fsc'}]
    3. Definir opciones de cinta:
       cint_options = [
         {value: 'No', planta_id: '1,2,3'},  // Sin cinta: todas las plantas
         {value: 'Si', planta_id: '2,3'}     // Con cinta: solo TilTil y Osorno
       ]
    4. Llamar filtroCampos(filtro) → obtener plantas validas
    5. Filtrar opciones de cinta por plantas disponibles
    6. SI hay opciones:
         - Llenar select CINTA con opciones filtradas
         - Habilitar CINTA
         - LIMPIAR campos posteriores
       SINO:
         - Deshabilitar CINTA
         - Mostrar warning "No se encuentran plantas asociadas"
```

**Archivo:** `ot-creation.js:318-392`

---

### REGLA CASC-004: CINTA Habilita RECUBRIMIENTO INTERNO

```javascript
Al cambiar CINTA:
  SI tiene valor:
    1. Copiar texto a cinta_text
    2. Construir filtro:
       filtro = [
         {impresion_id, fsc_id, referencia: 'impresion_fsc'},
         {cinta_id, referencia: 'cinta'}
       ]
    3. Llamar setRecubrimientoInterno() → obtener opciones
    4. Llamar filtroCampos(filtro) → obtener plantas validas
    5. Filtrar recubrimientos por plantas disponibles
    6. SI hay opciones:
         - Llenar select RECUBRIMIENTO_INTERNO
         - Habilitar RECUBRIMIENTO_INTERNO
         - LIMPIAR campos posteriores

    EXCEPCION (Regla CASC-004B):
    7. SI tipo_solicitud == 3 (MuestraConCad) Y
          (rol == 4 (Vendedor) O rol == 19 (VendedorExterno) O
           (rol == 8 (Disenador) Y tipo == 'create')):
         - SALTAR recubrimientos
         - Habilitar PLANTA directamente
```

**Archivo:** `ot-creation.js:394-501`

---

### REGLA CASC-005: RECUBRIMIENTO INTERNO Habilita RECUBRIMIENTO EXTERNO

```javascript
Al cambiar COVERAGE_INTERNAL_ID:
  SI tiene valor:
    1. Copiar texto a coverage_internal_id_text
    2. Construir filtro:
       filtro = [
         {impresion_id, fsc_id, referencia: 'impresion_fsc'},
         {cinta_id, referencia: 'cinta'},
         {recubrimiento_interno_id, referencia: 'recubrimiento_interno'}
       ]
    3. Construir opciones:
       opciones = [{impresion_id, referencia: 'impresion_recubrimiento_externo'}]
    4. Llamar setRecubrimientoExterno(impresion_id, referencia)
    5. Llamar filtroCampos(filtro) → obtener plantas validas
    6. Filtrar recubrimientos externos por plantas
    7. SI hay opciones:
         - Llenar select RECUBRIMIENTO_EXTERNO
         - Habilitar RECUBRIMIENTO_EXTERNO
         - LIMPIAR campos posteriores (planta, carton_color, carton)
```

**Archivo:** `ot-creation.js:503-594`

---

### REGLA CASC-006: RECUBRIMIENTO EXTERNO Habilita PLANTA

```javascript
Al cambiar COVERAGE_EXTERNAL_ID:
  SI tiene valor:
    1. Copiar texto a coverage_external_id_text
    2. Construir filtro completo:
       filtro = [
         {impresion_id, fsc_id, referencia: 'impresion_fsc'},
         {cinta_id, referencia: 'cinta'},
         {recubrimiento_interno_id, referencia: 'recubrimiento_interno'},
         {impresion_id, recubrimiento_externo_id, referencia: 'impresion_recubrimiento_externo'}
       ]
    3. Llamar setPlantaObjetivo() → obtener plantas
    4. Llamar filtroCampos(filtro) → obtener plantas validas
    5. Filtrar plantas por resultado
    6. SI hay opciones:
         - Llenar select PLANTA
         - Habilitar PLANTA
         - LIMPIAR campos posteriores (carton_color, carton)
```

**Archivo:** `ot-creation.js:596-672`

---

### REGLA CASC-007: PLANTA Habilita COLOR CARTON y SEC. OPERACIONALES

```javascript
Al cambiar PLANTA_ID:
  SI tiene valor:
    1. Copiar texto a planta_id_text

    2. CONFIGURAR SECUENCIAS OPERACIONALES segun planta:

       SI planta == 1 (Buin):
         - planta_original: 'Buin'
         - planta_aux_1: 'TilTil'
         - planta_aux_2: 'Osorno'
         - sec_ope_planta_orig_id: 1
         - sec_ope_planta_aux_1_id: 2
         - sec_ope_planta_aux_2_id: 3
         - termocontraible: DESHABILITADO, valor = 0

       SI planta == 2 (TilTil):
         - planta_original: 'TilTil'
         - planta_aux_1: 'Buin'
         - planta_aux_2: 'Osorno'
         - sec_ope_planta_orig_id: 2
         - sec_ope_planta_aux_1_id: 1
         - sec_ope_planta_aux_2_id: 3
         - termocontraible: HABILITADO

       SI planta == 3 (Osorno):
         - planta_original: 'Osorno'
         - planta_aux_1: 'Buin'
         - planta_aux_2: 'TilTil'
         - sec_ope_planta_orig_id: 3
         - sec_ope_planta_aux_1_id: 1
         - sec_ope_planta_aux_2_id: 2
         - termocontraible: DESHABILITADO, valor = 0

    3. Cargar secuencias operacionales para cada planta
    4. Habilitar checkboxes planta_aux_1 y planta_aux_2

    5. Llamar setColorCarton() → obtener colores
    6. Filtrar colores por:
       - Plantas validas del filtro
       - Impresion_id actual
    7. Eliminar colores duplicados
    8. SI hay opciones:
         - Llenar select CARTON_COLOR (BLANCO=2, KRAFT=1)
         - Habilitar CARTON_COLOR
```

**Archivo:** `ot-creation.js:674-799`

---

### REGLA CASC-008: COLOR CARTON Habilita CARTON

```javascript
Al cambiar CARTON_COLOR:
  SI tiene valor:
    1. Copiar texto a carton_color_text
    2. Llamar AJAX /getListaCarton con:
       - carton_color
       - planta_id
       - impresion_id
    3. Llenar select CARTON con opciones
    4. Habilitar CARTON
```

---

## CASCADA 3: CAMPOS DEPENDIENTES DE RECUBRIMIENTO

### REGLA CCB-001: Bloqueo Color-Cera-Barniz

```javascript
Validacion ejecutada en multiples puntos:

SI coverage_internal_id == '' O coverage_external_id == '':
  DESHABILITAR Y LIMPIAR:
    - impresion (tipo impresion grafica)
    - design_type_id
    - complejidad
    - numero_colores
    - color_1_id a color_6_id
    - impresion_1 a impresion_6
    - color_interno
    - impresion_color_interno
    - indicador_facturacion_diseno_grafico
    - cm2_clisse_color_1 a cm2_clisse_color_7
```

**Archivo:** `ot-creation.js:175-186`

---

## ENDPOINTS AJAX UTILIZADOS

| Endpoint | Metodo | Parametros | Retorna |
|----------|--------|------------|---------|
| /getJerarquia2 | GET | hierarchy_id | Opciones subjerarquia |
| /getJerarquia3 | GET | subhierarchy_id | Opciones sub-subjerarquia |
| /postVerificacionFiltro | POST | Array de filtros | Array plantas validas |
| /getRecubrimientoInterno | GET | - | Opciones recubrimiento int |
| /getRecubrimientoExterno | GET | impresion_id, referencia | Opciones recubrimiento ext |
| /getPlantaObjetivo | GET | - | Opciones plantas |
| /getColorCarton | GET | - | Opciones colores |
| /getListaCarton | GET | color, planta, impresion | Opciones cartones |
| /getSecuenciasOperacionales | GET | planta_id | Secuencias operacionales |

---

## FUNCION filtroCampos()

```javascript
async function filtroCampos(filtros) {
  // Envia array de filtros a /postVerificacionFiltro
  // Cada filtro tiene: referencia y campos especificos
  // Retorna: array de planta_id validos para esa combinacion

  // Ejemplo filtro:
  filtros = [
    {impresion_id: 1, fsc_id: 2, referencia: 'impresion_fsc'},
    {cinta_id: 0, referencia: 'cinta'},
    {recubrimiento_interno_id: 3, referencia: 'recubrimiento_interno'}
  ]

  // Resultado: ['1', '2', '3'] (IDs de plantas validas)
}
```

---

## TABLA: relacion_filtro_ingresos_principales

Esta tabla contiene 75 registros que definen las combinaciones validas.

| Campo | Descripcion |
|-------|-------------|
| id | ID del registro |
| impresion_id | ID tipo impresion |
| fsc_id | ID certificacion FSC |
| cinta_id | ID tipo cinta (0=No, 1=Si) |
| recubrimiento_interno_id | ID recubrimiento interno |
| recubrimiento_externo_id | ID recubrimiento externo |
| planta_id | Planta donde aplica |
| active | Estado del registro |

---

## DIAGRAMA DE DEPENDENCIAS

```
                    +-----------+
                    |   CANAL   |
                    +-----+-----+
                          |
                          v
                   +-----------+
                   |JERARQUIA 1|
                   +-----+-----+
                         |
              +----------+----------+
              v                     v
       +-----------+         +-----------+
       |JERARQUIA 2|         | TIPO ITEM |
       +-----+-----+         +-----+-----+
             |                     |
             v                     v
       +-----------+         +-----------+
       |JERARQUIA 3|         | IMPRESION |
       +-----------+         +-----+-----+
                                   |
                                   v
                             +-----------+
                             |    FSC    |
                             +-----+-----+
                                   |
                                   v
                             +-----------+
                             |   CINTA   |
                             +-----+-----+
                                   |
            +----------------------+----------------------+
            |                                             |
            v                                             v
     (tipo_solicitud=3                            +-----------+
      & rol vendedor)                             |REC.INTERNO|
            |                                     +-----+-----+
            |                                           |
            |                                           v
            |                                     +-----------+
            |                                     |REC.EXTERNO|
            |                                     +-----+-----+
            |                                           |
            +--------------------->+<-------------------+
                                   |
                                   v
                             +-----------+
                             |  PLANTA   |
                             +-----+-----+
                                   |
              +--------------------+--------------------+
              |                    |                    |
              v                    v                    v
       +-----------+        +-----------+        +-----------+
       |SEC.OPERAT.|        |COLOR CART.|        |TERMOCONTR.|
       +-----------+        +-----+-----+        +-----------+
                                  |
                                  v
                            +-----------+
                            |  CARTON   |
                            +-----------+
```

---

## IMPACTO EN MIGRACION

Para migrar este sistema a un nuevo frontend:

1. **Replicar tabla `relacion_filtro_ingresos_principales`** - Es la fuente de verdad
2. **Mantener los 8 endpoints AJAX** - Son criticos para la validacion
3. **Preservar el orden de la cascada** - Un campo no puede habilitarse sin su predecesor
4. **Implementar la excepcion tipo_solicitud=3** - Vendedores saltan recubrimientos
5. **Configurar plantas auxiliares** - Dependen de la planta principal seleccionada
