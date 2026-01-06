# Documentacion de Pantallas - Frontend React INVEB

**ID**: `DOC-PANTALLAS-V1`
**Fecha**: 2025-12-19
**Sistema**: MS-004 CascadeService - Frontend React

---

## Indice

1. [Login](#1-login)
2. [Dashboard OTs](#2-dashboard-ots)
3. [Crear OT](#3-crear-ot)
4. [Editar OT](#4-editar-ot)
5. [Gestionar OT](#5-gestionar-ot)
6. [Notificaciones](#6-notificaciones)
7. [CascadeForm](#7-cascadeform)
8. [Mantenedor Clientes](#8-mantenedor-clientes)
9. [Mantenedor Usuarios](#9-mantenedor-usuarios)

---

## 1. Login

### 1.1 Informacion General

| Aspecto | Valor |
|---------|-------|
| **Archivo** | `frontend/src/pages/Login/Login.tsx` |
| **Ruta** | Pantalla inicial (sin autenticar) |
| **Proposito** | Autenticacion de usuarios contra MySQL Laravel |

### 1.2 Descripcion Visual

```
┌─────────────────────────────────────────┐
│                                         │
│            ┌─────────────┐              │
│            │   INVEB     │              │
│            │  Sistema OT │              │
│            │             │              │
│            │ RUT         │              │
│            │ [12345678-9]│              │
│            │             │              │
│            │ Contrasena  │              │
│            │ [••••••••]  │              │
│            │             │              │
│            │ [Ingresar]  │              │
│            │             │              │
│            │ MS-004 v1.0 │              │
│            └─────────────┘              │
│                                         │
└─────────────────────────────────────────┘
```

### 1.3 Campos del Formulario

| Campo | Tipo | Validacion | Descripcion |
|-------|------|------------|-------------|
| RUT | text | Formato chileno (12345678-9) | Identificador unico del usuario |
| Contrasena | password | Requerido | Clave de acceso |

### 1.4 Funciones

| Funcion | Descripcion | Trigger |
|---------|-------------|---------|
| `formatRut()` | Formatea RUT automaticamente con guion y DV mayuscula | onChange del input RUT |
| `handleRutChange()` | Procesa cambios en el RUT y limpia errores | onChange del input RUT |
| `handleSubmit()` | Envia credenciales al API `/auth/login` | Submit del formulario |

### 1.5 Estados

| Estado | Tipo | Descripcion |
|--------|------|-------------|
| `rut` | string | Valor del campo RUT formateado |
| `password` | string | Valor del campo contrasena |
| `error` | string | null | Mensaje de error a mostrar |
| `isLoading` | boolean | Indica si esta procesando login |

### 1.6 Flujo de Autenticacion

```
Usuario ingresa RUT → formatRut() → Estado rut actualizado
Usuario ingresa password → Estado password actualizado
Click "Ingresar" → handleSubmit()
    ├─ Validacion campos → Si vacio → Mostrar error
    ├─ setIsLoading(true)
    ├─ onLogin(rut, password) → authApi.login()
    │   ├─ Exito → Token guardado en localStorage → Redirige a Dashboard
    │   └─ Error → Mostrar mensaje de error
    └─ setIsLoading(false)
```

### 1.7 API Endpoints Usados

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/api/v1/auth/login` | POST | Autenticacion con RUT y password |

### 1.8 Estilos Monitor One

- Fondo con gradiente azul corporativo
- Card centrada con sombra
- Inputs con bordes redondeados
- Boton primario azul

---

## 2. Dashboard OTs

### 2.1 Informacion General

| Aspecto | Valor |
|---------|-------|
| **Archivo** | `frontend/src/pages/WorkOrders/WorkOrdersDashboard.tsx` |
| **CSS** | `frontend/src/pages/WorkOrders/WorkOrdersDashboard.css` |
| **Ruta** | `/dashboard` (pagina principal post-login) |
| **Proposito** | Lista de OTs con filtros avanzados y paginacion |

### 2.2 Descripcion Visual

```
┌─────────────────────────────────────────────────────────────────────┐
│ Ordenes de Trabajo                      [Crear OT] [Notificaciones] │
├─────────────────────────────────────────────────────────────────────┤
│ FILTROS                                                             │
│ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐              │
│ │ Desde  │ │ Hasta  │ │ ID OT  │ │Material│ │Creador │              │
│ └────────┘ └────────┘ └────────┘ └────────┘ └────────┘              │
│ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐                         │
│ │ Canal  │ │ Estado │ │ Area   │ │Cliente │                         │
│ └────────┘ └────────┘ └────────┘ └────────┘                         │
│ ┌────────┐ ┌────────┐ ┌──────────┐ ┌────────┐                       │
│ │  CAD   │ │ Carton │ │Descripcion│ │ Planta │                       │
│ └────────┘ └────────┘ └──────────┘ └────────┘                       │
│                                         [Filtrar] [Limpiar]         │
├─────────────────────────────────────────────────────────────────────┤
│ Mostrando 20 de 150 registros                                       │
├─────────────────────────────────────────────────────────────────────┤
│ OT│Fecha│Cliente│Descripcion│Canal│Item│Estado│T│V│D│M│G│E│P│C│Acc │
├───┼─────┼───────┼───────────┼─────┼────┼──────┼─┼─┼─┼─┼─┼─┼─┼─┼────┤
│123│12/12│ACME   │Caja xyz   │  E  │CAJA│ REV  │2│1│0│1│0│0│0│0│✏️🔍│
│124│12/12│Beta   │Envase abc │  M  │ENV │ APR  │5│2│1│0│1│0│1│0│✏️🔍│
└───┴─────┴───────┴───────────┴─────┴────┴──────┴─┴─┴─┴─┴─┴─┴─┴─┴────┘
│                    [Anterior] Pagina 1 de 8 [Siguiente]             │
└─────────────────────────────────────────────────────────────────────┘
```

### 2.3 Filtros Disponibles

| Filtro | Tipo Input | Campo API | Multi-select |
|--------|------------|-----------|--------------|
| Fecha Desde | date | `date_desde` | No |
| Fecha Hasta | date | `date_hasta` | No |
| ID OT | number | `id_ot` | No |
| Material | text | `material` | No |
| Creador/Vendedor | select | `vendedor_id` | Si |
| Canal | select | `canal_id` | Si |
| Estado | select | `estado_id` | Si |
| Area | select | `area_id` | Si |
| Cliente | select | `client_id` | Si |
| CAD | text | `cad` | No |
| Carton | text | `carton` | No |
| Descripcion | text | `descripcion` | No |
| Planta | select | `planta_id` | Si |

### 2.4 Columnas de la Tabla

| Columna | Campo API | Descripcion |
|---------|-----------|-------------|
| OT | `id` | Numero de orden (con icono segun tipo_solicitud) |
| Fecha | `created_at` | Fecha de creacion |
| Cliente | `client_name` | Nombre SAP del cliente |
| Descripcion | `descripcion` | Descripcion del producto |
| Canal | `canal` | Primera letra del canal (E/M/etc) |
| Item | `item_tipo` | Tipo de producto |
| Estado | `estado_abrev` | Abreviatura del estado actual |
| T (Total) | `tiempo_total` | Dias totales |
| V (Ventas) | `tiempo_venta` | Dias en ventas |
| D (Desarrollo) | `tiempo_desarrollo` | Dias en desarrollo |
| M (Muestra) | `tiempo_muestra` | Dias en muestra |
| G (Grafico) | `tiempo_diseno` | Dias en diseno grafico |
| E (Externo) | `tiempo_externo` | Dias en diseno externo |
| P (Paletizado) | `tiempo_precatalogacion` | Dias en calculo paletizado |
| C (Catalogacion) | `tiempo_catalogacion` | Dias en catalogacion |
| Acciones | - | Botones editar y gestionar |

### 2.5 Funciones

| Funcion | Descripcion | Trigger |
|---------|-------------|---------|
| `loadWorkOrders()` | Carga lista de OTs con filtros aplicados | useEffect, submit filtros |
| `handleFilterSubmit()` | Aplica filtros y recarga lista | Click "Filtrar" |
| `handlePageChange()` | Cambia pagina de resultados | Click paginacion |
| `handleViewOT()` | Navega a pantalla Gestionar OT | Click icono 🔍 |
| `handleEditOT()` | Navega a pantalla Editar OT | Click icono ✏️ |
| `renderTimeBadge()` | Renderiza badge de tiempo con color | Render de columnas tiempo |
| `getOTIcon()` | Retorna icono segun tipo_solicitud | Render columna OT |

### 2.6 Estados

| Estado | Tipo | Descripcion |
|--------|------|-------------|
| `ots` | WorkOrderListItem[] | Lista de OTs actual |
| `loading` | boolean | Indica carga en progreso |
| `error` | string | null | Mensaje de error |
| `filterOptions` | FilterOptions | null | Opciones para selects de filtros |
| `page` | number | Pagina actual |
| `totalPages` | number | Total de paginas |
| `total` | number | Total de registros |
| `filters` | WorkOrderFilters | Filtros aplicados |
| `filterForm` | object | Estado del formulario de filtros |

### 2.7 API Endpoints Usados

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/api/v1/work-orders/` | GET | Lista OTs con filtros y paginacion |
| `/api/v1/work-orders/filter-options` | GET | Opciones para filtros (clientes, estados, etc.) |

### 2.8 Badges de Tiempo

| Dias | Color | Clase CSS |
|------|-------|-----------|
| 0-2 | Verde | `badge-success` |
| 2-5 | Amarillo | `badge-warning` |
| >5 | Rojo | `badge-danger` |

---

## 3. Crear OT

### 3.1 Informacion General

| Aspecto | Valor |
|---------|-------|
| **Archivo** | `frontend/src/pages/WorkOrders/CreateWorkOrder.tsx` |
| **Ruta** | Acceso desde boton "Crear OT" en Dashboard |
| **Proposito** | Formulario completo para crear nueva Orden de Trabajo |

### 3.2 Secciones del Formulario

#### Seccion 1: Datos Comerciales

| Campo | Tipo | Requerido | Descripcion |
|-------|------|-----------|-------------|
| Cliente | select | Si | Busqueda de cliente por nombre SAP |
| Descripcion | text (max 40) | Si | Descripcion corta del producto |
| Tipo Solicitud | select | Si | 1-7 (Normal, Desarrollo, etc.) |
| Canal | select | Si | Canal de venta |
| Org Venta | select | No | Organizacion de venta |
| Nombre Contacto | text | No | Persona de contacto |
| Email Contacto | email | No | Email de contacto |
| Telefono Contacto | text | No | Telefono de contacto |

#### Seccion 2: Solicita

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| Analisis | checkbox | Solicita analisis |
| Plano | checkbox | Solicita plano |
| Muestra | checkbox | Solicita muestra |
| Datos Cotizar | checkbox | Solicita datos para cotizar |
| Boceto | checkbox | Solicita boceto |
| Nuevo Material | checkbox | Es nuevo material |
| Prueba Industrial | checkbox | Requiere prueba industrial |
| Numero Muestras | number | Cantidad de muestras |

#### Seccion 3: Caracteristicas (CascadeForm)

Integra el componente CascadeForm completo con 8 pasos:
1. Tipo de Producto
2. Impresion
3. FSC
4. Cinta
5. Cobertura
6. Color Carton
7. Carton
8. Estilo (CAD)

#### Seccion 4: Medidas

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| Interno Largo | number (mm) | Largo interior |
| Interno Ancho | number (mm) | Ancho interior |
| Interno Alto | number (mm) | Alto interior |
| Externo Largo | number (mm) | Largo exterior |
| Externo Ancho | number (mm) | Ancho exterior |
| Externo Alto | number (mm) | Alto exterior |

#### Seccion 5: Terminaciones

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| Proceso | select | Proceso de terminacion |
| Numero Colores | number | Cantidad de colores |
| Planta | select | Planta de produccion |

#### Seccion 6: Desarrollo

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| Peso Contenido | number (g) | Peso del contenido de la caja |
| Cantidad | number | Cantidad solicitada |
| Observacion | textarea | Observaciones adicionales |

### 3.3 Funciones

| Funcion | Descripcion | Trigger |
|---------|-------------|---------|
| `handleCascadeComplete()` | Recibe datos del CascadeForm completado | CascadeForm onSubmit |
| `handleInputChange()` | Actualiza estado del campo correspondiente | onChange de inputs |
| `handleCheckboxChange()` | Toggle de campos checkbox | onChange de checkboxes |
| `handleSubmit()` | Valida y envia datos para crear OT | Submit formulario |

### 3.4 Validaciones

- Cliente: Requerido
- Descripcion: Requerido, max 40 caracteres
- Tipo Solicitud: Requerido
- Canal: Requerido
- CascadeForm: Debe completar los 8 pasos

### 3.5 API Endpoints Usados

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/api/v1/work-orders/` | POST | Crear nueva OT |
| `/api/v1/work-orders/filter-options` | GET | Opciones para selects |
| `/api/v1/form-options/` | GET | Opciones para CascadeForm |

### 3.6 Flujo de Creacion

```
Usuario completa Datos Comerciales
    ↓
Usuario marca opciones en Solicita
    ↓
Usuario completa CascadeForm (8 pasos)
    ↓
Usuario ingresa Medidas (opcional)
    ↓
Usuario selecciona Terminaciones (opcional)
    ↓
Usuario agrega Desarrollo (opcional)
    ↓
Click "Crear OT"
    ├─ Validacion cliente → Error si vacio
    ├─ Validacion descripcion → Error si vacio o >40 chars
    ├─ Validacion tipo_solicitud → Error si vacio
    ├─ Validacion canal → Error si vacio
    ├─ Construccion payload WorkOrderCreateData
    ├─ POST /work-orders/
    │   ├─ Exito → Alert exito + Navegar a Dashboard
    │   └─ Error → Mostrar mensaje de error
    └─ Fin
```

---

## 4. Editar OT

### 4.1 Informacion General

| Aspecto | Valor |
|---------|-------|
| **Archivo** | `frontend/src/pages/WorkOrders/EditWorkOrder.tsx` |
| **Ruta** | Acceso desde boton ✏️ en Dashboard |
| **Proposito** | Edicion de OT existente con todos sus campos |

### 4.2 Diferencias con Crear OT

| Aspecto | Crear OT | Editar OT |
|---------|----------|-----------|
| Titulo | "Crear Nueva OT" | "Editar OT #123" |
| Carga inicial | Formulario vacio | Pre-carga datos existentes |
| Boton submit | "Crear OT" | "Guardar Cambios" |
| Endpoint | POST /work-orders/ | PUT /work-orders/{id} |
| Campos requeridos | Todos los marcados | Ninguno (solo envia cambios) |

### 4.3 Funciones Adicionales

| Funcion | Descripcion | Trigger |
|---------|-------------|---------|
| `useWorkOrderDetail(id)` | Hook para cargar datos existentes | Mount del componente |
| Pre-llenado de campos | Mapea datos de API a FormState | Cuando data disponible |

### 4.4 Estados Adicionales

| Estado | Tipo | Descripcion |
|--------|------|-------------|
| `otId` | number | ID de la OT siendo editada |
| `isLoadingData` | boolean | Indica carga de datos existentes |

### 4.5 API Endpoints Usados

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/api/v1/work-orders/{id}` | GET | Obtener datos actuales de la OT |
| `/api/v1/work-orders/{id}` | PUT | Actualizar OT con nuevos datos |

---

## 5. Gestionar OT

### 5.1 Informacion General

| Aspecto | Valor |
|---------|-------|
| **Archivo** | `frontend/src/pages/WorkOrders/ManageWorkOrder.tsx` |
| **Ruta** | Acceso desde boton 🔍 en Dashboard |
| **Proposito** | Transicionar OT entre areas y estados del workflow |

### 5.2 Descripcion Visual

```
┌─────────────────────────────────────────────────────────────────────┐
│ Gestionar Orden de Trabajo                [OT #123] [← Volver]      │
├─────────────────────────────────────────────────────────────────────┤
│ ┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐         │
│ │ Cliente: ACME   │ │ Desc: Caja xyz  │ │ Creador: Juan P │         │
│ └─────────────────┘ └─────────────────┘ └─────────────────┘         │
├─────────────────────────────────────────────────────────────────────┤
│ TRANSICION DE ESTADO                    │ HISTORIAL DE GESTION      │
│ ┌─────────────────────────────────────┐ │ ┌─────────────────────────┐│
│ │ Estado Actual                       │ │ │ [Ventas] [En Revision] ││
│ │ ┌───────────┐ ┌───────────┐         │ │ │ 15/12/2024 14:30       ││
│ │ │ Ventas    │ │En Revision│         │ │ │ Por: Maria Garcia      ││
│ │ └───────────┘ └───────────┘         │ │ │ "Enviado para revision"││
│ │                                     │ │ ├─────────────────────────┤│
│ │ Nueva Area                          │ │ │ [Ventas] [Pendiente]   ││
│ │ ┌─────────────────────────────┐     │ │ │ 14/12/2024 10:00       ││
│ │ │ Seleccione area...       ▼ │     │ │ │ Por: Juan Perez        ││
│ │ └─────────────────────────────┘     │ │ │ "OT creada"            ││
│ │                                     │ │ └─────────────────────────┘│
│ │ Nuevo Estado                        │ │                           │
│ │ ┌─────────────────────────────┐     │ │                           │
│ │ │ Seleccione estado...     ▼ │     │ │                           │
│ │ └─────────────────────────────┘     │ │                           │
│ │                                     │ │                           │
│ │ Observacion (opcional)              │ │                           │
│ │ ┌─────────────────────────────┐     │ │                           │
│ │ │                             │     │ │                           │
│ │ │                             │     │ │                           │
│ │ └─────────────────────────────┘     │ │                           │
│ │                                     │ │                           │
│ │ [    Realizar Transicion    ]       │ │                           │
│ └─────────────────────────────────────┘ │                           │
└─────────────────────────────────────────────────────────────────────┘
```

### 5.3 Campos del Formulario de Transicion

| Campo | Tipo | Requerido | Descripcion |
|-------|------|-----------|-------------|
| Nueva Area | select | Si | Area destino (Ventas, Desarrollo, etc.) |
| Nuevo Estado | select | Si | Estado destino |
| Observacion | textarea | No | Comentario de la transicion |

### 5.4 Panel de Historial

Muestra todas las transiciones anteriores con:
- Badge de Area (azul)
- Badge de Estado (gris)
- Fecha y hora de transicion
- Nombre del usuario que realizo
- Observacion si existe (en italica)

### 5.5 Funciones

| Funcion | Descripcion | Trigger |
|---------|-------------|---------|
| `useManagementHistory(id)` | Carga historial de gestion | Mount |
| `useWorkflowOptions(id)` | Carga opciones de areas y estados | Mount |
| `useWorkOrderDetail(id)` | Carga info basica de la OT | Mount |
| `handleTransition()` | Ejecuta transicion de estado | Click boton |
| `formatDate()` | Formatea fecha para historial | Render historial |

### 5.6 Estados

| Estado | Tipo | Descripcion |
|--------|------|-------------|
| `selectedArea` | number | null | Area seleccionada para transicion |
| `selectedState` | number | null | Estado seleccionado para transicion |
| `observation` | string | Texto de observacion |
| `successMessage` | string | null | Mensaje de exito |
| `errorMessage` | string | null | Mensaje de error |

### 5.7 API Endpoints Usados

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/api/v1/work-orders/{id}` | GET | Datos basicos de la OT |
| `/api/v1/work-orders/{id}/management` | GET | Historial de gestion |
| `/api/v1/work-orders/{id}/workflow-options` | GET | Opciones de areas y estados |
| `/api/v1/work-orders/{id}/transition` | POST | Ejecutar transicion |

### 5.8 Flujo de Transicion

```
Pantalla carga historial actual y opciones
    ↓
Usuario selecciona Nueva Area
    ↓
Usuario selecciona Nuevo Estado
    ↓
Usuario agrega Observacion (opcional)
    ↓
Click "Realizar Transicion"
    ├─ Validacion area → Error si no seleccionada
    ├─ Validacion estado → Error si no seleccionado
    ├─ POST /work-orders/{id}/transition
    │   ├─ Exito → Mostrar mensaje + Refrescar historial
    │   └─ Error → Mostrar mensaje de error
    └─ Limpiar formulario
```

---

## 6. Notificaciones

### 6.1 Informacion General

| Aspecto | Valor |
|---------|-------|
| **Archivo** | `frontend/src/pages/WorkOrders/Notifications.tsx` |
| **Ruta** | Tab "Notificaciones" o boton en header |
| **Proposito** | Ver y gestionar notificaciones de OTs asignadas |

### 6.2 Descripcion Visual

```
┌─────────────────────────────────────────────────────────────────────┐
│ Notificaciones                                      [Volver]        │
├─────────────────────────────────────────────────────────────────────┤
│ Mostrando 5 de 5 notificaciones                                     │
├─────────────────────────────────────────────────────────────────────┤
│ OT │Dias│Cliente│Desc   │Item│Estado│Area  │Generador│Motivo│Acc   │
├────┼────┼───────┼───────┼────┼──────┼──────┼─────────┼──────┼──────┤
│#123│ 3  │ACME   │Caja..│CAJA│ REV  │Ventas│M.Garcia │Revisa│[G][L]│
│#124│ 7  │Beta   │Env...│ENV │ APR  │Desa..│J.Perez  │Urgent│[G][L]│
└────┴────┴───────┴───────┴────┴──────┴──────┴─────────┴──────┴──────┘
│                    [Anterior] Pagina 1 de 1 [Siguiente]             │
└─────────────────────────────────────────────────────────────────────┘

[G] = Gestionar   [L] = Leido
```

### 6.3 Columnas de la Tabla

| Columna | Campo API | Descripcion |
|---------|-----------|-------------|
| OT | `work_order_id` | ID de la orden de trabajo |
| Dias | `dias_total` | Dias desde creacion (badge coloreado) |
| Cliente | `client_name` | Nombre del cliente |
| Descripcion | `ot_descripcion` | Descripcion de la OT |
| Item | `item_tipo` | Tipo de producto |
| Estado | `estado` | Estado actual de la OT |
| Area | `area` | Area actual de la OT |
| Generador | `generador_nombre` | Quien creo la notificacion |
| Motivo | `motivo` | Razon de la notificacion |
| Observacion | `observacion` | Detalle adicional (tooltip) |
| Fecha | `created_at` | Fecha de creacion |
| Acciones | - | Botones Gestionar y Leido |

### 6.4 Acciones

| Boton | Accion | Descripcion |
|-------|--------|-------------|
| Gestionar | `handleManageOT(work_order_id)` | Navega a pantalla Gestionar OT |
| Leido | `handleMarkRead(notification_id)` | Marca notificacion como inactiva |

### 6.5 Funciones

| Funcion | Descripcion | Trigger |
|---------|-------------|---------|
| `useNotificationsList(page, pageSize)` | Carga lista paginada | Mount, cambio pagina |
| `handleMarkRead()` | Marca notificacion como leida | Click boton "Leido" |
| `handleManageOT()` | Navega a gestion de OT | Click boton "Gestionar" |

### 6.6 Estados

| Estado | Tipo | Descripcion |
|--------|------|-------------|
| `page` | number | Pagina actual de resultados |
| `successMessage` | string | null | Mensaje de exito |
| `errorMessage` | string | null | Mensaje de error |

### 6.7 API Endpoints Usados

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/api/v1/notifications/` | GET | Lista paginada de notificaciones |
| `/api/v1/notifications/{id}/read` | PUT | Marcar como leida |

### 6.8 Badge de Dias

| Dias | Color | Urgencia |
|------|-------|----------|
| 0-2 | Verde | Normal |
| 2-5 | Amarillo | Atencion |
| >5 | Rojo | Urgente |

---

## 7. CascadeForm

### 7.1 Informacion General

| Aspecto | Valor |
|---------|-------|
| **Archivo** | `frontend/src/components/CascadeForm/CascadeForm.tsx` |
| **Estilos** | `frontend/src/components/CascadeForm/CascadeForm.styles.ts` |
| **Proposito** | Formulario de 8 pasos para caracteristicas de producto |

### 7.2 Pasos del Formulario

| Paso | Campo | API Field | Descripcion |
|------|-------|-----------|-------------|
| 1 | Tipo de Producto | `productTypeId` | Tipo base del producto |
| 2 | Impresion | `impresion` | Tipo de impresion |
| 3 | FSC | `fsc` | Certificacion FSC |
| 4 | Cinta | `cinta` | Tipo de cinta |
| 5 | Cobertura | `coverageInternalId`, `coverageExternalId` | Cobertura int/ext |
| 6 | Color Carton | `cartonColor` | Color del carton |
| 7 | Carton | `cartonId` | Tipo de carton |
| 8 | Estilo (CAD) | `styleId` | Codigo CAD |

### 7.3 Funcionamiento Cascade

El formulario implementa logica de cascada donde la seleccion de un campo puede:
1. Filtrar opciones de campos siguientes
2. Saltar campos no aplicables
3. Pre-seleccionar valores unicos
4. Mostrar advertencias de combinaciones invalidas

### 7.4 Funciones Principales

| Funcion | Descripcion |
|---------|-------------|
| `useFormOptions()` | Hook que carga todas las opciones del formulario |
| `useCascadeRules()` | Hook que carga reglas de cascada |
| `handleStepChange()` | Procesa cambio de valor en un paso |
| `validateCombination()` | Valida combinacion de campos seleccionados |
| `getFilteredOptions()` | Filtra opciones basado en selecciones previas |

### 7.5 Estados

| Estado | Tipo | Descripcion |
|--------|------|-------------|
| `currentStep` | number | Paso actual (1-8) |
| `formData` | CascadeFormData | Datos seleccionados |
| `isComplete` | boolean | Todos los pasos completados |
| `validationErrors` | string[] | Errores de validacion |

### 7.6 API Endpoints Usados

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/api/v1/form-options/` | GET | Todas las opciones del formulario |
| `/api/v1/cascade-rules/` | GET | Reglas de cascada |
| `/api/v1/cascade-combinations/validate/` | GET | Validar combinacion |

### 7.7 Tipos de Datos

```typescript
interface CascadeFormData {
  productTypeId?: number;
  impresion?: string;
  fsc?: string;
  cinta?: string;
  coverageInternalId?: number;
  coverageExternalId?: number;
  cartonColor?: string;
  cartonId?: number;
  styleId?: number;
}
```

---

## 8. Mantenedor Clientes

### 8.1 Informacion General

| Aspecto | Valor |
|---------|-------|
| **Archivo Lista** | `frontend/src/pages/Mantenedores/ClientsList.tsx` |
| **Archivo Form** | `frontend/src/pages/Mantenedores/ClientForm.tsx` |
| **Ruta** | Tab "Clientes" en navegacion principal |
| **Proposito** | CRUD completo de clientes del sistema |

### 8.2 Descripcion Visual

```
┌─────────────────────────────────────────────────────────────────────┐
│ Mantenedor de Clientes                            [+ Nuevo Cliente] │
├─────────────────────────────────────────────────────────────────────┤
│ FILTROS                                                             │
│ ┌──────────────────┐ ┌────────────────┐ ┌────────────────┐          │
│ │ Buscar RUT/Nombre│ │ Clasificacion ▼│ │ Estado: Todos ▼│          │
│ └──────────────────┘ └────────────────┘ └────────────────┘          │
├─────────────────────────────────────────────────────────────────────┤
│ Mostrando 20 de 150 clientes                                        │
├─────────────────────────────────────────────────────────────────────┤
│ RUT       │Nombre SAP│Razon Social│Clasif│Ciudad │Email   │Acc     │
├───────────┼──────────┼────────────┼──────┼───────┼────────┼────────┤
│12345678-9 │ACME LTDA │Acme Corp   │A     │Stgo   │a@b.com │✏️ ✓/✗  │
│98765432-1 │Beta SpA  │Beta Corp   │B     │Valpo  │c@d.com │✏️ ✓/✗  │
└───────────┴──────────┴────────────┴──────┴───────┴────────┴────────┘
│                    [Anterior] Pagina 1 de 8 [Siguiente]             │
└─────────────────────────────────────────────────────────────────────┘
```

### 8.3 Filtros Disponibles

| Filtro | Tipo Input | Campo API | Descripcion |
|--------|------------|-----------|-------------|
| Buscar | text | `search` | Busca en RUT, nombre y razon social |
| Clasificacion | select | `clasificacion_id` | Filtra por clasificacion |
| Estado | select | `activo` | Activos, Inactivos o Todos |

### 8.4 Columnas de la Tabla

| Columna | Campo API | Descripcion |
|---------|-----------|-------------|
| RUT | `rut` | RUT formateado del cliente |
| Nombre SAP | `nombre` | Nombre en SAP |
| Razon Social | `razon_social` | Razon social legal |
| Clasificacion | `clasificacion_nombre` | Categoria del cliente |
| Ciudad | `ciudad` | Ciudad del cliente |
| Email | `email` | Email de contacto |
| Acciones | - | Editar, Activar/Desactivar |

### 8.5 Formulario Cliente (Modal)

| Campo | Tipo | Requerido | Validacion |
|-------|------|-----------|------------|
| RUT | text | Si | Formato chileno, modulo 11 |
| Nombre SAP | text | Si | Max 100 caracteres |
| Razon Social | text | No | Max 200 caracteres |
| Giro | text | No | Giro comercial |
| Direccion | text | No | Direccion completa |
| Comuna | text | No | Comuna |
| Ciudad | text | No | Ciudad |
| Telefono | text | No | Telefono de contacto |
| Email | email | No | Formato email valido |
| Contacto | text | No | Nombre persona contacto |
| Clasificacion | select | Si | Lista de clasificaciones |

### 8.6 Funciones

| Funcion | Descripcion | Trigger |
|---------|-------------|---------|
| `useClientsList(filters)` | Carga lista paginada con filtros | Mount, filtros |
| `useClasificaciones()` | Carga opciones de clasificacion | Mount |
| `useCreateClient()` | Mutation para crear cliente | Submit crear |
| `useUpdateClient()` | Mutation para editar cliente | Submit editar |
| `useActivateClient()` | Mutation para activar | Click activar |
| `useDeactivateClient()` | Mutation para desactivar | Click desactivar |
| `formatRut()` | Formatea RUT con guion y DV | Input RUT |
| `validateRut()` | Valida RUT con modulo 11 | Blur RUT |

### 8.7 Estados

| Estado | Tipo | Descripcion |
|--------|------|-------------|
| `clients` | ClientListItem[] | Lista de clientes |
| `page` | number | Pagina actual |
| `totalPages` | number | Total de paginas |
| `filters` | ClientFilters | Filtros aplicados |
| `showModal` | boolean | Muestra modal crear/editar |
| `editingClient` | ClientDetail | null | Cliente siendo editado |
| `successMessage` | string | null | Mensaje de exito |
| `errorMessage` | string | null | Mensaje de error |

### 8.8 API Endpoints Usados

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/api/v1/mantenedores/clients/` | GET | Lista paginada con filtros |
| `/api/v1/mantenedores/clients/{id}` | GET | Detalle de cliente |
| `/api/v1/mantenedores/clients/` | POST | Crear nuevo cliente |
| `/api/v1/mantenedores/clients/{id}` | PUT | Actualizar cliente |
| `/api/v1/mantenedores/clients/{id}/activate` | PUT | Activar cliente |
| `/api/v1/mantenedores/clients/{id}/deactivate` | PUT | Desactivar cliente |
| `/api/v1/mantenedores/clients/clasificaciones` | GET | Lista clasificaciones |

### 8.9 Validacion de RUT

```typescript
// Algoritmo Modulo 11 para validar RUT chileno
function validateRut(rut: string): boolean {
  // 1. Limpiar RUT (solo numeros y K)
  // 2. Separar cuerpo y digito verificador
  // 3. Calcular digito con modulo 11
  // 4. Comparar digito calculado con ingresado
}
```

---

## 9. Mantenedor Usuarios

### 9.1 Informacion General

| Aspecto | Valor |
|---------|-------|
| **Archivo Lista** | `frontend/src/pages/Mantenedores/UsersList.tsx` |
| **Archivo Form** | `frontend/src/pages/Mantenedores/UserForm.tsx` |
| **Ruta** | Tab "Usuarios" en navegacion principal |
| **Proposito** | CRUD completo de usuarios del sistema |

### 9.2 Descripcion Visual

```
┌─────────────────────────────────────────────────────────────────────┐
│ Mantenedor de Usuarios                            [+ Nuevo Usuario] │
├─────────────────────────────────────────────────────────────────────┤
│ FILTROS                                                             │
│ ┌──────────────────┐ ┌────────────┐ ┌────────────┐ ┌──────────────┐ │
│ │ Buscar RUT/Nombre│ │ Rol      ▼ │ │ WorkSpace ▼│ │ Estado: Todos│ │
│ └──────────────────┘ └────────────┘ └────────────┘ └──────────────┘ │
├─────────────────────────────────────────────────────────────────────┤
│ Mostrando 15 de 45 usuarios                                         │
├─────────────────────────────────────────────────────────────────────┤
│ RUT       │Nombre    │Apellido │Rol      │WorkSpace│Email   │Acc   │
├───────────┼──────────┼─────────┼─────────┼─────────┼────────┼──────┤
│12345678-9 │Juan      │Perez    │Admin    │Stgo     │j@b.com │✏️ ✓/✗│
│98765432-1 │Maria     │Garcia   │Vendedor │Valpo    │m@d.com │✏️ ✓/✗│
└───────────┴──────────┴─────────┴─────────┴─────────┴────────┴──────┘
│                    [Anterior] Pagina 1 de 3 [Siguiente]             │
└─────────────────────────────────────────────────────────────────────┘
```

### 9.3 Filtros Disponibles

| Filtro | Tipo Input | Campo API | Descripcion |
|--------|------------|-----------|-------------|
| Buscar | text | `search` | Busca en RUT, nombre y apellido |
| Rol | select | `role_id` | Filtra por rol |
| WorkSpace | select | `work_space_id` | Filtra por area de trabajo |
| Estado | select | `activo` | Activos, Inactivos o Todos |

### 9.4 Columnas de la Tabla

| Columna | Campo API | Descripcion |
|---------|-----------|-------------|
| RUT | `rut` | RUT formateado del usuario |
| Nombre | `nombre` | Nombre del usuario |
| Apellido | `apellido` | Apellido del usuario |
| Rol | `role_nombre` | Nombre del rol |
| WorkSpace | `work_space_nombre` | Area de trabajo |
| Email | `email` | Email del usuario |
| Acciones | - | Editar, Activar/Desactivar |

### 9.5 Formulario Usuario (Modal)

| Campo | Tipo | Requerido | Validacion |
|-------|------|-----------|------------|
| RUT | text | Si | Formato chileno, modulo 11 |
| Nombre | text | Si | Max 100 caracteres |
| Apellido | text | Si | Max 100 caracteres |
| Email | email | Si | Formato email valido |
| Telefono | text | No | Telefono de contacto |
| Password | password | Si (crear) | Min 6 caracteres, opcional al editar |
| Rol | select | Si | Lista de roles |
| WorkSpace | select | Si | Lista de areas de trabajo |

### 9.6 Funciones

| Funcion | Descripcion | Trigger |
|---------|-------------|---------|
| `useUsersList(filters)` | Carga lista paginada con filtros | Mount, filtros |
| `useRoles()` | Carga opciones de roles | Mount |
| `useWorkSpaces()` | Carga opciones de workspaces | Mount |
| `useCreateUser()` | Mutation para crear usuario | Submit crear |
| `useUpdateUser()` | Mutation para editar usuario | Submit editar |
| `useActivateUser()` | Mutation para activar | Click activar |
| `useDeactivateUser()` | Mutation para desactivar | Click desactivar |

### 9.7 Estados

| Estado | Tipo | Descripcion |
|--------|------|-------------|
| `users` | UserListItem[] | Lista de usuarios |
| `page` | number | Pagina actual |
| `totalPages` | number | Total de paginas |
| `filters` | UserFilters | Filtros aplicados |
| `showModal` | boolean | Muestra modal crear/editar |
| `editingUser` | UserDetail | null | Usuario siendo editado |
| `successMessage` | string | null | Mensaje de exito |
| `errorMessage` | string | null | Mensaje de error |

### 9.8 API Endpoints Usados

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/api/v1/mantenedores/users/` | GET | Lista paginada con filtros |
| `/api/v1/mantenedores/users/{id}` | GET | Detalle de usuario |
| `/api/v1/mantenedores/users/` | POST | Crear nuevo usuario |
| `/api/v1/mantenedores/users/{id}` | PUT | Actualizar usuario |
| `/api/v1/mantenedores/users/{id}/activate` | PUT | Activar usuario |
| `/api/v1/mantenedores/users/{id}/deactivate` | PUT | Desactivar usuario |
| `/api/v1/mantenedores/users/roles` | GET | Lista de roles |
| `/api/v1/mantenedores/users/workspaces` | GET | Lista de workspaces |

### 9.9 Seguridad

- **Password hashing**: Contraseñas hasheadas con bcrypt en backend
- **Auto-proteccion**: Usuario no puede desactivarse a si mismo
- **Password opcional**: Al editar, password vacio mantiene el actual

---

## Anexos

### A. Navegacion entre Pantallas

```
Login
  └─→ Dashboard OTs
        ├─→ Crear OT ←─┐
        │      └─→ Dashboard (exito)
        ├─→ Editar OT ←─┐
        │      └─→ Dashboard (exito)
        ├─→ Gestionar OT ←─┐
        │      └─→ Dashboard (volver)
        ├─→ Notificaciones
        │      ├─→ Gestionar OT
        │      └─→ Dashboard (volver)
        ├─→ Mantenedor Clientes
        │      └─→ CRUD Modal (crear/editar)
        └─→ Mantenedor Usuarios
               └─→ CRUD Modal (crear/editar)
```

### B. Componentes Compartidos

| Componente | Ubicacion | Uso |
|------------|-----------|-----|
| Button | `components/common/Button` | Botones estilizados |
| Card | `components/common/Card` | Contenedores con sombra |
| Select | `components/common/Select` | Selectores estilizados |
| Spinner | `components/common/Spinner` | Indicador de carga |

### C. Hooks Personalizados

| Hook | Archivo | Descripcion |
|------|---------|-------------|
| `useWorkOrdersList` | `hooks/useWorkOrders.ts` | Lista de OTs |
| `useWorkOrderDetail` | `hooks/useWorkOrders.ts` | Detalle de OT |
| `useCreateWorkOrder` | `hooks/useWorkOrders.ts` | Mutation crear |
| `useUpdateWorkOrder` | `hooks/useWorkOrders.ts` | Mutation actualizar |
| `useManagementHistory` | `hooks/useWorkOrders.ts` | Historial gestion |
| `useWorkflowOptions` | `hooks/useWorkOrders.ts` | Opciones workflow |
| `useTransitionWorkOrder` | `hooks/useWorkOrders.ts` | Mutation transicion |
| `useNotificationsList` | `hooks/useWorkOrders.ts` | Lista notificaciones |
| `useNotificationsCount` | `hooks/useWorkOrders.ts` | Conteo notificaciones |
| `useMarkNotificationRead` | `hooks/useWorkOrders.ts` | Mutation marcar leida |
| `useFormOptions` | `hooks/useCascadeRules.ts` | Opciones formulario |
| `useCascadeRules` | `hooks/useCascadeRules.ts` | Reglas cascade |
| `useHealthCheck` | `hooks/useCascadeRules.ts` | Estado del API |
| `useClientsList` | `hooks/useMantenedores.ts` | Lista de clientes |
| `useClientDetail` | `hooks/useMantenedores.ts` | Detalle de cliente |
| `useClasificaciones` | `hooks/useMantenedores.ts` | Lista clasificaciones |
| `useCreateClient` | `hooks/useMantenedores.ts` | Mutation crear cliente |
| `useUpdateClient` | `hooks/useMantenedores.ts` | Mutation editar cliente |
| `useActivateClient` | `hooks/useMantenedores.ts` | Mutation activar cliente |
| `useDeactivateClient` | `hooks/useMantenedores.ts` | Mutation desactivar cliente |
| `useUsersList` | `hooks/useMantenedores.ts` | Lista de usuarios |
| `useUserDetail` | `hooks/useMantenedores.ts` | Detalle de usuario |
| `useRoles` | `hooks/useMantenedores.ts` | Lista de roles |
| `useWorkSpaces` | `hooks/useMantenedores.ts` | Lista de workspaces |
| `useCreateUser` | `hooks/useMantenedores.ts` | Mutation crear usuario |
| `useUpdateUser` | `hooks/useMantenedores.ts` | Mutation editar usuario |
| `useActivateUser` | `hooks/useMantenedores.ts` | Mutation activar usuario |
| `useDeactivateUser` | `hooks/useMantenedores.ts` | Mutation desactivar usuario |

---

**Documento generado**: 2025-12-19
**Version**: 1.0
