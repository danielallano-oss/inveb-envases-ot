# REGLAS DE NEGOCIO - Formulario OT (Orden de Trabajo)

## Documento de Referencia para Desarrollo

**Fuente**: Extraido del sistema Laravel original (`ficha-form.blade.php`, `WorkOrderOldController.php`, `Constants.php`)
**Fecha**: 2026-01-08
**Version**: 1.0

---

## 1. ROLES DEL SISTEMA

| ID | Constante | Nombre | Descripcion |
|----|-----------|--------|-------------|
| 1 | Admin | Administrador | Administrador del sistema |
| 2 | Gerente | Gerente | Gerente general |
| 3 | JefeVenta | Jefe de Venta | Jefe del area de ventas |
| 4 | Vendedor | Vendedor | Vendedor interno |
| 5 | JefeDesarrollo | Jefe Desarrollo | Jefe del area de desarrollo |
| 6 | Ingeniero | Ingeniero | Ingeniero de desarrollo |
| 7 | JefeDiseño | Jefe Diseño | Jefe del area de diseno grafico |
| 8 | Diseñador | Disenador | Disenador grafico |
| 9 | JefeCatalogador | Jefe Catalogador | Jefe de catalogacion |
| 10 | Catalogador | Catalogador | Catalogador |
| 11 | JefePrecatalogador | Jefe Precatalogador | Jefe de precatalogacion |
| 12 | Precatalogador | Precatalogador | Precatalogador |
| 13 | JefeMuestras | Jefe Muestras | Jefe de muestras |
| 14 | TecnicoMuestras | Tecnico Muestras | Tecnico de muestras |
| 15 | GerenteComercial | Gerente Comercial | Gerente comercial |
| 17 | API | API | Usuario de API |
| 18 | SuperAdministrador | Super Administrador | Acceso total al sistema |
| 19 | VendedorExterno | Vendedor Externo | Vendedor externo (cliente EDIPAC) |

---

## 2. AREAS DEL SISTEMA

| ID | Area | Roles Asociados |
|----|------|-----------------|
| 1 | Venta | Vendedor (4), Jefe Venta (3), Vendedor Externo (19) |
| 2 | Desarrollo | Ingeniero (6), Jefe Desarrollo (5) |
| 3 | Diseno | Disenador (8), Jefe Diseno (7) |
| 4 | Catalogacion | Catalogador (10), Jefe Catalogador (9) |
| 5 | Precatalogacion | Precatalogador (12), Jefe Precatalogador (11) |
| 6 | Muestras | Tecnico Muestras (14), Jefe Muestras (13) |

---

## 3. REGLAS DE ACCESO POR ROL

### 3.1 Seccion 1: Selector de Vendedor (solo creacion)

**Visibilidad**: Solo visible cuando:
- Tipo de operacion: `create` o `duplicate`
- Roles: 5 (Jefe Desarrollo), 6 (Ingeniero), 7 (Jefe Diseno), 8 (Disenador)

```blade
@if((auth()->user()->role_id == 5 || auth()->user()->role_id == 6 || auth()->user()->role_id == 7 || auth()->user()->role_id == 8) && ($tipo == "create" || $tipo == "duplicate"))
```

### 3.2 Seccion 2: Datos Comerciales

#### Campo: Cliente (`client_id`)
| Condicion | Comportamiento |
|-----------|----------------|
| Vendedor Externo + create/duplicate | Dropdown editable |
| Super Administrador (18) + edit | Dropdown editable |
| Otros roles + edit | **Solo lectura** (`inputReadOnly`) |

#### Campo: Tipo de Solicitud (`tipo_solicitud`)
| Condicion | Comportamiento |
|-----------|----------------|
| create/duplicate | Dropdown editable |
| Super Administrador (18) + edit | Dropdown editable |
| Otros roles + edit | **Solo lectura** |

**Valores del dropdown Tipo Solicitud:**
```php
[
    1 => "Desarrollo Completo",
    2 => "Cotiza con CAD",
    3 => "Muestra con CAD",
    4 => "Cotiza sin CAD",
    5 => "Arte con Material",
    6 => "Otras Solicitudes Desarrollo",
    7 => "Proyecto Innovacion"
]
```

#### Campo: Instalacion Cliente (`instalacion_cliente`)
| Condicion | Comportamiento |
|-----------|----------------|
| create/duplicate | Dropdown editable (dinamico segun cliente) |
| Super Administrador (18) + edit | Dropdown editable |
| Otros roles + edit | **Solo lectura** |

#### Boton: Indicaciones Especiales Cliente
**Visibilidad**: Solo para roles 5, 6, 7, 8, 9, 10, 11, 12
```blade
@if(auth()->user()->role_id == 5 || auth()->user()->role_id == 6 || auth()->user()->role_id == 7 || auth()->user()->role_id == 8 || auth()->user()->role_id == 9 || auth()->user()->role_id == 10 || auth()->user()->role_id == 11 || auth()->user()->role_id == 12)
```

### 3.3 Seccion 7: Caracteristicas

#### Boton: Agregar Caracteristica Estilo
**Visibilidad**: Solo roles 5 (Jefe Desarrollo), 6 (Ingeniero), 18 (Super Admin)
```blade
@if((auth()->user()->role_id == 5 || auth()->user()->role_id == 6 || auth()->user()->role_id == 18))
```

#### Seccion: Formula McKee y Analisis Anchura
**Visibilidad**: Solo roles 5 (Jefe Desarrollo), 6 (Ingeniero)
```blade
@if(auth()->user()->role_id == 5 || auth()->user()->role_id == 6)
```

### 3.4 Seccion 13: Material Asignado

#### Campo: Descripcion Material
| Rol | Comportamiento |
|-----|----------------|
| Super Administrador (18) | Editable (`inputEditDescripcion`) |
| Otros roles | **Solo lectura** (`inputReadOnly`) |

---

## 4. DROPDOWNS Y TABLAS DE REFERENCIA

### 4.1 Seccion 6: Asistente Ingresos Principales

| Campo | Variable | Tabla BD | Filtro |
|-------|----------|----------|--------|
| Tipo Item | `$productTypes` | `product_types` | `active = 1` |
| Impresion | `$impresion` | `impresion` | - |
| FSC | `$fsc` | `fsc` | - |
| Recubrimiento Interno | `$coverageInternal` | `coverage_internals` | - |
| Recubrimiento Externo | `$coverageExternal` | `coverage_externals` | - |
| Planta Objetivo | `$plantaObjetivo` | `plantas` | `active = 1` |
| Carton | `$cartons` | `cartons` | `active = 1` |

### 4.2 Seccion 7: Caracteristicas

| Campo | Variable | Tabla BD | Filtro |
|-------|----------|----------|--------|
| CAD | `$cads` | `cads` | - |
| Matriz | `$matriz` | `matrices` | - |
| Estilo | `$styles` | `styles` | `active = 1` |
| **Certificado Calidad** | `$palletQa` | `pallet_qas` | `active = 1` |
| Pais/Mercado Destino | `$paisReferencia` | `paises` | - |
| Tamano Pallet | `$palletTypes` | `pallet_types` | `active = 1` |
| **Formato Etiqueta Pallet** | `$palletTagFormat` | `pallet_tag_formats` | `active = 1` |

### 4.3 Seccion 8: Color-Cera-Barniz

| Campo | Variable | Tabla BD | Filtro |
|-------|----------|----------|--------|
| **Trazabilidad** | `$trazabilidad` | `trazabilidad` | `status = 1` |
| Tipo Diseno | `$designTypes` | `design_types` | `active = 1` |
| Numero Colores | - | Hardcoded | `[0,1,2,3,4,5,6,7]` |
| Color 1-6 | `$colors` | `colors` | `active = 1` |
| Barniz UV | `$colors_barniz` | `colors` | `tipo = 'barniz'` |

### 4.4 Seccion 11: Terminaciones

| Campo | Variable | Tabla BD | Filtro |
|-------|----------|----------|--------|
| **Proceso** | `$procesos` | `processes` | `type = 'EV' AND active = 1` |
| **Tipo Pegado** | - | Hardcoded | Ver valores abajo |
| **Armado** | `$armados` | `armados` | `active = 1` |
| Sentido Armado | `$sentidos_armado` | `sentidos_armado` | - |
| Maquila | - | Hardcoded | `[1 => "Si", 0 => "No"]` |
| Servicios Maquila | `$maquila_servicios` | `maquila_servicios` | `active = 1` |

**Valores Tipo Pegado:**
```php
[
    0 => "No Aplica",
    2 => "Pegado Interno",
    3 => "Pegado Externo",
    4 => "Pegado 3 Puntos",
    5 => "Pegado 4 Puntos"
]
```

### 4.5 Seccion 13: Datos para Desarrollo

| Campo | Variable | Tabla BD | Filtro |
|-------|----------|----------|--------|
| **Tipo Producto** | `$productTypeDeveloping` | `product_types` | `active = 1` |
| **Tipo Alimento** | `$foodType` | `food_types` | `deleted = 0` |
| **Uso Previsto** | `$expectedUse` | `expected_use` | `deleted = 0` |
| **Uso Reciclado** | `$recycledUse` | `recycled_use` | `deleted = 0` |
| **Clase Sustancia** | `$classSubstancePacked` | `class_substance_packed` | `deleted = 0` |
| **Medio Transporte** | `$transportationWay` | `transportation_way` | `deleted = 0` |
| Envase Primario | `$envases` | `envases` | `active = 1` |
| **Mercado Destino** | `$targetMarket` | `target_market` | `deleted = 0` |

---

## 5. CAMPOS CONDICIONALES Y DEPENDENCIAS

### 5.1 Campos dependientes de Instalacion Cliente

**REGLA**: Los siguientes campos SOLO son obligatorios si hay una instalacion seleccionada:

| Campo | Obligatorio Si |
|-------|----------------|
| Bulto Zunchado Pallet | `instalacion_cliente_id != null` |
| Formato Etiqueta Pallet | `instalacion_cliente_id != null` |
| N Etiquetas por Pallet | `instalacion_cliente_id != null` |

**Comportamiento cuando NO hay instalacion:**
- Campos deben estar **deshabilitados** (disabled)
- Fondo gris (`backgroundColor: #f5f5f5`)
- NO mostrar error de validacion

### 5.2 Archivo OC

**Visibilidad**: El campo de subida de archivo OC solo se muestra cuando `OC = Si`

### 5.3 Numero de Muestras

**Visibilidad**: Solo visible cuando se selecciona checkbox "Muestra"

### 5.4 Seccion Distancia Cinta

**Visibilidad**: Solo visible cuando `Cinta = Si` (Seccion 6)

---

## 6. TIPOS DE SOLICITUD Y FLUJOS

| ID | Tipo | Descripcion |
|----|------|-------------|
| 1 | Desarrollo Completo | Flujo completo de desarrollo |
| 2 | Cotiza con CAD | Cotizacion con diseno existente |
| 3 | Muestra con CAD | Solicitud de muestra fisica |
| 4 | Cotiza sin CAD | Cotizacion sin diseno |
| 5 | Arte con Material | Solo trabajo de arte |
| 6 | Otras Solicitudes | Solicitudes especiales |
| 7 | Proyecto Innovacion | Proyectos I+D |

---

## 7. VALIDACIONES DEL FORMULARIO

### 7.1 Campos Obligatorios Globales

- `client_id` - Cliente
- `tipo_solicitud` - Tipo de Solicitud
- `descripcion` - Descripcion (max 40 caracteres)
- Al menos uno de: `analisis`, `prueba_industrial`, `muestra`

### 7.2 Validaciones Numericas

| Campo | Tipo | Min | Max |
|-------|------|-----|-----|
| `numero_muestras` | number | 1 | - |
| `items_set` | number | 0 | - |
| `veces_item` | number | 0 | - |
| `altura_pallet` | number | 0 | - |
| `etiquetas_pallet` | number | 0 | - |
| `cintas_x_caja` | number | 0 | 10 |

### 7.3 Validaciones de Texto

| Campo | Max Length |
|-------|------------|
| `descripcion` | 40 |

---

## 8. DATOS PARA POBLAR EN BD RAILWAY

### 8.1 Tabla: `trazabilidad`

```sql
INSERT INTO trazabilidad (id, descripcion, status) VALUES
(1, 'Sin Trazabilidad (Solo Placas)', 1),
(2, 'Trazabilidad Solo OF', 1),
(3, 'Trazabilidad Completa', 1)
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion), status=1;
```

### 8.2 Tabla: `pallet_qas`

```sql
INSERT INTO pallet_qas (id, descripcion, active, created_at, updated_at) VALUES
(1, 'Certificado Estandar', 1, NOW(), NOW()),
(2, 'Certificado Premium', 1, NOW(), NOW()),
(3, 'Sin Certificado', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion), active=1;
```

### 8.3 Tabla: `pallet_tag_formats`

```sql
INSERT INTO pallet_tag_formats (id, descripcion, active, created_at, updated_at) VALUES
(1, 'Etiqueta Simple', 1, NOW(), NOW()),
(2, 'Etiqueta con Codigo de Barras', 1, NOW(), NOW()),
(3, 'Etiqueta QR', 1, NOW(), NOW()),
(4, 'Sin Etiqueta', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion), active=1;
```

### 8.4 Tabla: `processes`

```sql
INSERT INTO processes (id, descripcion, type, active, orden) VALUES
(1, 'Flexo', 'EV', 1, 1),
(2, 'Diecutter', 'EV', 1, 2),
(3, 'Sin Proceso', 'EV', 1, 3)
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion), active=1;
```

### 8.5 Tabla: `armados`

```sql
INSERT INTO armados (id, descripcion, active) VALUES
(1, 'Maquina', 1),
(2, 'Con y Sin Pegamento', 1),
(3, 'Manual', 1)
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion), active=1;
```

### 8.6 Tabla: `product_types` (Tipo Producto para Desarrollo)

```sql
INSERT INTO product_types (id, codigo, descripcion, active) VALUES
(1, 'UV', 'U.Vta/Set', 1),
(2, 'SS', 'Subset', 1)
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion), active=1;
```

### 8.7 Tabla: `food_types`

```sql
INSERT INTO food_types (id, descripcion, deleted) VALUES
(1, 'Frutas y Verduras', 0),
(2, 'Carnes y Pescados', 0),
(3, 'Lacteos', 0),
(4, 'Bebidas', 0),
(5, 'Congelados', 0),
(6, 'Secos y Granos', 0),
(7, 'Conservas', 0),
(8, 'No Alimentario', 0)
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);
```

### 8.8 Tabla: `target_market`

```sql
INSERT INTO target_market (id, descripcion, deleted) VALUES
(1, 'Nacional', 0),
(2, 'Europeo', 0),
(3, 'Norteamericano', 0),
(4, 'Asiatico', 0),
(5, 'Latinoamericano', 0),
(6, 'Otro', 0)
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);
```

### 8.9 Tabla: `transportation_way`

```sql
INSERT INTO transportation_way (id, descripcion, deleted) VALUES
(1, 'Terrestre', 0),
(2, 'Maritimo', 0),
(3, 'Aereo', 0),
(4, 'Multimodal', 0)
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);
```

### 8.10 Tabla: `expected_use`

```sql
INSERT INTO expected_use (id, descripcion, deleted) VALUES
(1, 'Exportacion', 0),
(2, 'Mercado Nacional', 0),
(3, 'Retail', 0),
(4, 'Industrial', 0)
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);
```

### 8.11 Tabla: `recycled_use`

```sql
INSERT INTO recycled_use (id, descripcion, deleted) VALUES
(1, 'Si', 0),
(2, 'No', 0),
(3, 'Parcial', 0)
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);
```

### 8.12 Tabla: `class_substance_packed`

```sql
INSERT INTO class_substance_packed (id, descripcion, deleted) VALUES
(1, 'Solido', 0),
(2, 'Liquido', 0),
(3, 'Semisolido', 0),
(4, 'Polvo', 0),
(5, 'Granulado', 0)
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);
```

### 8.13 Tabla: `envases`

```sql
INSERT INTO envases (id, descripcion, active) VALUES
(1, 'Granel', 1),
(2, 'Pote', 1),
(3, 'Bolsa', 1),
(4, 'Bandeja', 1),
(5, 'Botella', 1),
(6, 'Lata', 1),
(7, 'Tetrapack', 1),
(8, 'Sachet', 1),
(9, 'Caja', 1),
(10, 'Otro', 1)
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion), active=1;
```

### 8.14 Tabla: `maquila_servicios`

```sql
INSERT INTO maquila_servicios (id, servicio, precio_clp_caja, active) VALUES
(1, 'Desgaje Cabezal Par', 7, 1),
(2, 'Desgaje Unitario', 3, 1),
(3, 'PM CJ Chica (0-30 cm)', 33, 1),
(4, 'PM CJ Mediana (30-70 cm)', 41, 1),
(5, 'PM CJ Grande (70-100 cm)', 84, 1),
(6, 'Paletizado Placas', 15, 1),
(7, 'Armado y Paletizado Tabiques Simple', 42, 1),
(8, 'Armado y Paletizado Tabiques Doble', 55, 1)
ON DUPLICATE KEY UPDATE servicio=VALUES(servicio), active=1;
```

---

## 9. MAPEO DE ISSUES EXCEL A REGLAS

### Issues Seccion 7 (Caracteristicas)

| Issue | Campo | Regla |
|-------|-------|-------|
| #30 | Certificado Calidad | Tabla `pallet_qas` debe tener datos |
| #40 | Formato Etiqueta Pallet | Tabla `pallet_tag_formats` debe tener datos |
| #41 | N Etiquetas por Pallet | Solo obligatorio si `instalacion_cliente_id != null` |

### Issues Seccion 8 (Color-Cera-Barniz)

| Issue | Campo | Regla |
|-------|-------|-------|
| #44 | Trazabilidad | Tabla `trazabilidad` debe tener datos con `status=1` |

### Issues Seccion 11 (Terminaciones)

| Issue | Campo | Regla |
|-------|-------|-------|
| - | Proceso | Tabla `processes` con `type='EV'` |
| - | Armado | Tabla `armados` debe tener datos |

### Issues Seccion 13 (Datos para Desarrollo)

| Issue | Campo | Regla |
|-------|-------|-------|
| - | Tipo Producto | Tabla `product_types` |
| - | Tipo Alimento | Tabla `food_types` |
| - | Uso Previsto | Tabla `expected_use` |
| - | Mercado Destino | Tabla `target_market` |

---

## 10. CONSIDERACIONES ESPECIALES

### 10.1 Vendedor Externo (role_id = 19)

- Siempre pertenece al cliente EDIPAC (client_id = 8)
- Ve campos adicionales: `dato_sub_cliente`, `nombre_contacto`, `email_contacto`, `telefono_contacto`
- Layout diferente en Seccion 2

### 10.2 Super Administrador (role_id = 18)

- Puede editar campos normalmente bloqueados en modo edicion
- Puede modificar Material Asignado descripcion
- Acceso a todos los estados de OT

### 10.3 Jefe Desarrollo + Ingeniero (roles 5, 6)

- Acceso a Formula McKee
- Acceso a Analisis de Anchura/Combinabilidad
- Pueden agregar Caracteristicas de Estilo

---

## CHANGELOG

- **v1.0** (2026-01-08): Documento inicial extraido de Laravel
