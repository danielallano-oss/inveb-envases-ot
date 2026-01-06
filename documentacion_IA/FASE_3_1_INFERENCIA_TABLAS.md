# FASE 3.1: Inferencia de Tablas

**ID**: `PASO-03.01-V12`
**Fecha**: 2025-12-17
**Estado**: Completado

---

## Resumen Ejecutivo

| Metrica | Valor |
|---------|-------|
| Total de Tablas | 160 |
| Tablas Principales | 12 |
| Tablas de Catalogo | 85+ |
| Tablas de Cascada | 8 |
| Tabla mas grande | work_orders (~270 campos) |

---

## 1. INVENTARIO COMPLETO DE TABLAS

### 1.1 Tablas Principales (Core)

| Tabla | Registros | Campos | Descripcion |
|-------|-----------|--------|-------------|
| `work_orders` | 54 | ~270 | Ordenes de Trabajo (tabla central) |
| `users` | 24 | 24 | Usuarios del sistema |
| `clients` | 58 | 53 | Clientes |
| `managements` | 6 | - | Gestiones sobre OT |
| `muestras` | 2 | - | Muestras de OT |
| `cotizacions` | - | - | Cotizaciones |
| `notifications` | - | - | Notificaciones |

### 1.2 Tablas de Workflow y Permisos

| Tabla | Registros | Descripcion |
|-------|-----------|-------------|
| `states` | 22 | Estados del workflow |
| `work_spaces` | 7 | Areas de trabajo |
| `roles` | 18 | Roles de usuario |

### 1.3 Tablas de Cascada (Criticas)

| Tabla | Registros | Rol en Cascada |
|-------|-----------|----------------|
| `relacion_filtro_ingresos_principales` | 75 | **Combinaciones validas** |
| `product_types` | 36 | Tipo de Item (cascada inicio) |
| `impresion` | 7 | Tipo de impresion |
| `fsc` | 7 | Certificacion FSC |
| `tipos_cintas` | 2 | Tipos de cinta |
| `coverage_internals` | 3 | Recubrimiento interno |
| `coverage_externals` | 5 | Recubrimiento externo |
| `plantas` | 3 | Plantas de produccion |
| `cartons` | 235 | Tipos de carton/color |

### 1.4 Tablas de Jerarquias

| Tabla | Registros | Descripcion |
|-------|-----------|-------------|
| `canals` | 6 | Canales de venta |
| `hierarchies` | 7 | Jerarquia nivel 1 |
| `subhierarchies` | 100 | Jerarquia nivel 2 |
| `subsubhierarchies` | 527 | Jerarquia nivel 3 |

### 1.5 Tablas de Catalogos (85+)

#### Catalogos de Producto
- `cardboards`, `cartons`, `cartons_backup`
- `papers`, `colors`, `styles`
- `envases`, `armados`, `pegados`
- `matrices`, `rayados`
- `pallet_types`, `pallet_patrons`, `pallet_protections`
- `pallet_box_quantities`, `pallet_height`, `pallet_qas`
- `pallet_status_types`, `pallet_tag_formats`

#### Catalogos de Proceso
- `processes`, `precut_types`, `print_type`
- `printing_machines`, `design_types`
- `ink_types`, `adhesivos`
- `secuencias_operacionales`

#### Catalogos de Cliente/Negocio
- `org_ventas`, `organizaciones_ventas`
- `mercados`, `sectores`, `sectors`
- `rubros`, `clasificacion_clientes`
- `instalacion_clientes`, `installations`

#### Catalogos de Configuracion
- `system_variables`, `management_types`
- `reference_types`, `protection_type`
- `coverage_types`, `recubrimiento_types`

---

## 2. ESTRUCTURA DE TABLAS CRITICAS

### 2.1 work_orders (Tabla Central)

```
Campos principales: ~270 campos
Campos de identificacion:
  - id (bigint unsigned, PK)
  - tipo_solicitud (int) - 1:OTNueva, 2:MuestraSinCad, 3:MuestraConCad, 4:Duplicar
  - descripcion (varchar 191)

Campos de cliente:
  - client_id (int unsigned)
  - nombre_contacto, email_contacto, telefono_contacto

Campos de cascada:
  - product_type_id (int unsigned) -> product_types.id
  - impresion (int) -> impresion.id
  - fsc (int) -> fsc.id
  - cinta (tinyint)
  - tipo_cinta (tinyint) -> tipos_cintas.id
  - coverage_internal_id (int unsigned) -> coverage_internals.id
  - coverage_external_id (int unsigned) -> coverage_externals.id
  - planta_id (int unsigned) -> plantas.id
  - carton_id (int unsigned) -> cartons.id
  - carton_color (int unsigned)

Campos de workflow:
  - current_area_id (int unsigned) -> work_spaces.id
  - creador_id (int unsigned) -> users.id
  - terminado (tinyint)
  - active (tinyint)

Campos de jerarquia:
  - canal_id (int unsigned) -> canals.id
  - subsubhierarchy_id (int unsigned) -> subsubhierarchies.id

Campos de dimension:
  - interno_largo, interno_ancho, interno_alto
  - externo_largo, externo_ancho, externo_alto
  - largura_hm, anchura_hm

Campos de impresion:
  - numero_colores
  - color_1_id a color_7_id
  - impresion_1 a impresion_7 (porcentajes)

Campos de palletizado:
  - pallet_type_id, pallet_patron_id
  - cajas_por_pallet, placas_por_pallet

Timestamps:
  - created_at, updated_at
```

### 2.2 relacion_filtro_ingresos_principales (Reglas de Cascada)

```sql
CREATE TABLE relacion_filtro_ingresos_principales (
  id bigint unsigned PRIMARY KEY AUTO_INCREMENT,
  filtro_1 int unsigned NOT NULL,      -- Campo origen en cascada
  filtro_2 varchar(191),                -- Valor valido
  planta_id varchar(191) NOT NULL,      -- Plantas aplicables (ej: "1,2,3")
  referencia varchar(191) NOT NULL,     -- Paso de cascada
  created_at timestamp,
  updated_at timestamp
);

Valores de 'referencia':
  - impresion_fsc         (impresion -> fsc)
  - cinta                 (fsc -> cinta)
  - recubrimiento_interno (cinta -> coverage_internal)
  - impresion_recubrimiento_externo (coverage_internal -> coverage_external)

Total registros: 75 combinaciones validas
```

### 2.3 states (Estados del Workflow)

| ID | Nombre | Abrev | Area (work_space_id) |
|----|--------|-------|----------------------|
| 1 | Proceso de Ventas | PV | 1 (Ventas) |
| 2 | Proceso de Diseno Estructural | PDE | 2 (Desarrollo) |
| 3 | Laboratorio | L | 2 (Desarrollo) |
| 4 | Muestra | M | 2 (Desarrollo) |
| 5 | Proceso de Diseno Grafico | PDG | 3 (Diseno) |
| 6 | Proceso de Calculo Paletizado | PP | 5 (Precatalogacion) |
| 7 | Proceso de Catalogacion | PC | 4 (Catalogacion) |
| 8 | Terminada | T | 4 (Catalogacion) |
| 9 | Perdida | P | 1 (Ventas) |
| 10 | Consulta Cliente | CC | 1 (Ventas) |
| 11 | Anulada | A | 1 (Ventas) |
| 12 | Rechazada | R | 1 (Ventas) |
| 13 | Entregado | E | 2 (Desarrollo) |
| 14 | Espera de OC | EOC | 1 (Ventas) |
| 15 | Falta definicion del Cliente | FDC | 1 (Ventas) |
| 16 | Visto Bueno Cliente | VBC | 1 (Ventas) |
| 17 | Sala de Muestras | SM | 6 (Muestras) |
| 18 | Muestras Listas | ML | 6 (Muestras) |
| 20 | Hibernacion | H | 1 (Ventas) |
| 21 | Cotizacion | CT | 1 (Ventas) |
| 22 | Muestra Devuelta | MD | 6 (Muestras) |

### 2.4 work_spaces (Areas)

| ID | Nombre | Abreviatura |
|----|--------|-------------|
| 1 | Area de Ventas | Ven |
| 2 | Area de Diseno Estructural | Ing |
| 3 | Area de Diseno Grafico | D Graf |
| 4 | Area de Catalogacion | Cat |
| 5 | Area de Precatalogacion | P Cat |
| 6 | Area de Muestras | Mtra |
| 7 | Area Super Administrador | SA (inactive) |

### 2.5 roles

| ID | Nombre | work_space_id |
|----|--------|---------------|
| 1 | Administrador | NULL |
| 2 | Gerente | NULL |
| 3 | Jefe de Ventas | 1 |
| 4 | Vendedor | 1 |
| 5 | Jefe de Desarrollo | 2 |
| 6 | Ingeniero | 2 |
| 7 | Jefe de Diseno e Impresion | 3 |
| 8 | Disenador | 3 |
| 9 | Jefe de Precatalogacion | 4 |
| 10 | Precatalogador | 4 |
| 11 | Jefe de Catalogacion | 5 |
| 12 | Catalogador | 5 |
| 13 | Super Administrador | 7 |
| 14 | Tecnico de Muestras | 6 |
| 15 | Gerente Comercial | NULL |
| 16 | Visualizador | NULL |
| 19 | Vendedor Externo | 1 |

---

## 3. CATALOGOS DE CASCADA

### 3.1 impresion (7 registros)

| ID | Descripcion | Status |
|----|-------------|--------|
| 1 | Offset | 1 |
| 2 | Flexografia | 1 |
| 3 | Flexografia Alta Grafica | 1 |
| 4 | Flexografia Tiro y Retiro | 1 |
| 5 | Sin Impresion | 1 |
| 6 | Sin Impresion (Solo OF) | 0 |
| 7 | Sin Impresion (Trazabilidad Completa) | 0 |

### 3.2 fsc (7 registros)

| ID | Descripcion | Codigo |
|----|-------------|--------|
| 1 | No | 0 |
| 2 | Si | 1 |
| 3 | Sin FSC | 2 |
| 4 | Logo FSC solo EEII | 3 |
| 5 | Logo FSC cliente y EEII | 4 |
| 6 | Logo FSC solo cliente | 5 |
| 7 | FSC solo facturacion | 6 |

### 3.3 plantas (3 registros)

| ID | Nombre | Centro |
|----|--------|--------|
| 1 | BUIN | 1600 |
| 2 | TIL TIL | 1611 |
| 3 | OSORNO | 1617 |

### 3.4 coverage_internals (3 registros)

| ID | Descripcion | Planta_id |
|----|-------------|-----------|
| 1 | No aplica | 1,2,3 |
| 2 | Barniz hidrorepelente | 1,2,3 |
| 3 | Cera | 1 |

### 3.5 coverage_externals (5 registros)

| ID | Descripcion |
|----|-------------|
| 1 | No aplica |
| 2 | Barniz hidrorepelente |
| 3 | Barniz acuoso |
| 4 | Barniz UV |
| 5 | Cera |

---

## 4. RELACIONES ENTRE TABLAS

### 4.1 Diagrama de Relaciones Principales

```
                    ┌─────────────┐
                    │   users     │
                    └──────┬──────┘
                           │ creador_id
                           ▼
┌─────────┐      ┌─────────────────────┐      ┌──────────┐
│ clients │◄─────│    work_orders      │─────►│  states  │
└─────────┘      │  (tabla central)    │      └──────────┘
  client_id      └─────────┬───────────┘      current_area_id
                           │                         │
         ┌─────────────────┼─────────────────┐       │
         │                 │                 │       │
         ▼                 ▼                 ▼       ▼
┌──────────────┐  ┌──────────────┐  ┌─────────────────────┐
│ product_types│  │   cartons    │  │    work_spaces      │
└──────────────┘  └──────────────┘  └─────────────────────┘
         │                                       │
         │        CASCADA                        │
         ▼                                       ▼
┌──────────────┐                          ┌──────────┐
│  impresion   │                          │  roles   │
└──────┬───────┘                          └──────────┘
       │
       ▼
┌──────────────┐    ┌────────────────────────────────────┐
│     fsc      │    │ relacion_filtro_ingresos_principales│
└──────┬───────┘    │      (75 combinaciones validas)    │
       │            └────────────────────────────────────┘
       ▼
┌──────────────┐
│ tipos_cintas │
└──────┬───────┘
       │
       ▼
┌───────────────────┐
│ coverage_internals│
└───────┬───────────┘
        │
        ▼
┌───────────────────┐
│ coverage_externals│
└───────┬───────────┘
        │
        ▼
┌──────────────┐
│   plantas    │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│   cartons    │
└──────────────┘
```

### 4.2 Relaciones Implicitas (Eloquent ORM)

Las relaciones no estan definidas como FK en MySQL, sino en los modelos Eloquent:

```php
// WorkOrder.php
belongsTo(User::class, 'creador_id')
belongsTo(Client::class, 'client_id')
belongsTo(State::class, 'current_area_id')  // Mal nombrado, deberia ser state_id
belongsTo(ProductType::class, 'product_type_id')
belongsTo(Carton::class, 'carton_id')
belongsTo(Plant::class, 'planta_id')
belongsTo(Canal::class, 'canal_id')
belongsTo(SubSubHierarchy::class, 'subsubhierarchy_id')

// State.php
belongsTo(WorkSpace::class, 'work_space_id')

// Role.php
belongsTo(WorkSpace::class, 'work_space_id')

// User.php
belongsTo(Role::class, 'role_id')
```

---

## 5. OBSERVACIONES Y PROBLEMAS IDENTIFICADOS

### 5.1 Problemas de Diseno

| Problema | Tabla | Descripcion |
|----------|-------|-------------|
| Campo mal nombrado | work_orders.current_area_id | Deberia ser current_state_id (apunta a states) |
| Redundancia | cartons.planta_id | Es varchar con valores "1,2,3" en lugar de FK |
| Redundancia | coverage_internals.planta_id | Es varchar con valores "1,2,3" |
| Sin FK explicitas | Todas | Relaciones solo en Eloquent, no en BD |
| Tabla gigante | work_orders | ~270 campos, deberia normalizarse |

### 5.2 Campos con Valores Multiples (Anti-patron)

```sql
-- Estos campos almacenan IDs separados por coma
coverage_internals.planta_id = "1,2,3"
cartons.planta_id = "1,2,3"
relacion_filtro_ingresos_principales.planta_id = "1,2,3"
```

### 5.3 Tablas de Backup (Posible Deuda Tecnica)

- `cartons_backup`
- `clients_backup`
- `muestras_backup`
- `work_orders_bkp_old`

---

## 6. METRICAS POR CATEGORIA

### Distribucion de Tablas

```
Tablas Core (OT/Workflow): 12 (7.5%)
Tablas de Cascada: 8 (5%)
Tablas de Jerarquia: 4 (2.5%)
Tablas de Catalogos: 85+ (53%)
Tablas de Configuracion: 15 (9.4%)
Tablas de Backup/Legacy: 10 (6.3%)
Tablas de OAuth: 6 (3.8%)
Otras: ~20 (12.5%)
```

---

## 7. SIGUIENTE PASO

**PASO 3.2**: Definir estructura de datos normalizada para:
1. Tabla de reglas de cascada (`cascade_rules`)
2. Tabla de combinaciones validas normalizada
3. Normalizacion de campos multi-valor

---

## Anexo A: Lista Completa de 160 Tablas

```
additional_characteristics_type    instalacion_clientes
adhesivos                         installations
almacenes                         insumos_palletizados
answers                           management_types
areahcs                           managements
armados                           mano_obra_mantencion
audits                            maquila_servicios
bitacora_campos_modificados       margenes_minimos
bitacora_work_orders              materials
cads                              materials_codes
canals                            matrices
cantidad_base                     mercados
cardboards                        merma_convertidoras
carton_esquineros                 merma_corrugadoras
cartons                           migrations
cartons_backup                    muestras
cebes                             muestras_backup
changelogs                        notifications
ciudades_fletes                   oauth_access_tokens
clasificacion_clientes            oauth_auth_codes
class_substance_packed            oauth_clients
class_substance_packeds           oauth_personal_access_clients
client_contacts                   oauth_refresh_tokens
clients                           org_ventas
clients_backup                    organizaciones_ventas
codigo_materials                  paises
colors                            pallet_box_quantities
consumo_adhesivo_pegados          pallet_height
consumo_adhesivos                 pallet_patrons
consumo_energias                  pallet_protections
cotizacion_approvals              pallet_qas
cotizacion_estados                pallet_status_types
cotizacions                       pallet_tag_formats
coverage_external                 pallet_types
coverage_externals                pallets
coverage_internal                 papers
coverage_internals                password_resets
coverage_types                    password_security
design_types                      pegados
detalle_cotizacions               plantas
detalle_precio_palletizados       plants
envases                           porcentajes_margenes
expected_use                      precut_types
expected_uses                     prefijo_materials
factores_desarrollos              print_type
factores_ondas                    printing_machines
factores_seguridads               processes
files                             product_type_developing
fletes                            product_types
food_types                        protection_type
formato_bobinas                   proveedores
fsc                               rayados
grupo_imputacion_materiales       rechazo_conjunto
grupo_materiales_1                recubrimiento_types
grupo_materiales_2                recycled_use
grupo_plantas                     recycled_uses
hierarchies                       reference_types
impresion                         relacion_filtro_ingresos_principales
indicaciones_especiales           reporte_diseno_estructural_sala_muestra
indicaciones_especiales_old       roles
ink_types                         rubros
                                  rubros_backup
salas_cortes                      tipos_cintas
sectores                          transportation_way
sectors                           transportation_ways
secuencias_operacionales          trazabilidad
states                            user_work_orders
styles                            users
subhierarchies                    variables_cotizadors
subsubhierarchies                 work_orders
sufijo_materials                  work_orders_bkp_old
system_variables                  work_spaces
target_market                     zunchos
target_markets
tarifario
tarifario_margens
tarifarios
tiempo_tratamiento
tipo_barniz
tipo_ondas
```

---

**Documento generado**: 2025-12-17
**Fuente**: Base de datos MySQL `envases_ot` (local Docker)
