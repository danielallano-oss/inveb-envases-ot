# FASE 5.5: Diseno UI - Componentes de Interfaz

**ID**: `PASO-05.05-V2`
**Fecha**: 2025-12-19
**Estado**: Actualizado (Documentacion Legado + Monitor One)
**Tipo**: Anexo a Fase de Diseno

---

## Resumen

Este documento inventaria los componentes UI del sistema INVEB Envases-OT:
- **Sistema Legado**: Vistas Blade/jQuery (documentacion de referencia)
- **Monitor One**: React + styled-components (nueva implementacion)

### Estrategia de Migracion UI

| Componente | Legado | Monitor One | Estado |
|------------|--------|-------------|--------|
| Design System | Bootstrap 4 + custom CSS | **CSS Variables + Poppins** | Documentado en [5.5B](FASE_5_5B_ESTANDARES_MONITOR_ONE.md) |
| Framework | jQuery + DataTables | **React + MUI/Tailwind** | Pendiente |
| Cascade Form | Blade + AJAX | **React + FastAPI** | Pendiente |

> **Importante**: Los estandares visuales Monitor One (colores, tipografia, componentes) estan definidos en [FASE_5_5B_ESTANDARES_MONITOR_ONE.md](FASE_5_5B_ESTANDARES_MONITOR_ONE.md).

---

## 1. INVENTARIO DE VISTAS BLADE

### 1.1 Metricas Generales

| Metrica | Cantidad |
|---------|----------|
| Total vistas Blade | 319 |
| Carpetas de vistas | 41 |
| Layouts base | 2 |
| Partials reutilizables | ~15 |

### 1.2 Estructura de Carpetas

```
resources/views/
├── layouts/              # Layouts base
├── partials/             # Componentes reutilizables
├── auth/                 # Autenticacion (login, registro, recovery)
├── work-orders/          # OT - Vista principal del sistema
├── cotizador/            # Modulo de cotizaciones
├── clients/              # Gestion de clientes
├── reports/              # Reportes
├── reports2/             # Reportes adicionales
├── reports3/             # Mas reportes
├── mantenedores/         # Catalogos CRUD
├── pdf/                  # Plantillas PDF
├── email/                # Plantillas email
└── [30+ carpetas mas]    # Mantenedores especificos
```

---

## 2. CLASIFICACION POR SERVICIO

### 2.1 Mapeo Vistas -> Microservicios

| Servicio | Carpetas de Vistas | Tipo |
|----------|-------------------|------|
| **MS-001 OTService** | work-orders/, work-orders-old/ | Core |
| **MS-002 AuthService** | auth/, users/ | Identidad |
| **MS-003 ClientService** | clients/ | Comercial |
| **MS-004 CascadeService** | (embebido en work-orders) | Validacion |
| **MS-005 CotizacionService** | cotizador/ | Comercial |
| **MS-006 ReportService** | reports/, reports2/, reports3/ | Reportes |
| **MS-007 WorkflowService** | asignations/ | Workflow |
| **MS-008 HierarchyService** | hierarchies/, subhierarchies/, subsubhierarchies/ | Catalogos |
| **MS-009 NotificationService** | email/ | Notificaciones |
| **MS-010 CatalogService** | 25+ carpetas de mantenedores | Catalogos |

### 2.2 Vistas por Categoria

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    DISTRIBUCION DE VISTAS                                    │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  CORE (OT + Cotizador)              MANTENEDORES                            │
│  ┌─────────────────────┐            ┌─────────────────────┐                 │
│  │ work-orders/        │            │ adhesivos/          │                 │
│  │ cotizador/          │            │ almacenes/          │                 │
│  │ asignations/        │            │ canals/             │                 │
│  └─────────────────────┘            │ cartons/            │                 │
│                                     │ cebes/              │                 │
│  CLIENTES                           │ colors/             │                 │
│  ┌─────────────────────┐            │ hierarchies/        │                 │
│  │ clients/            │            │ materials/          │                 │
│  │ clasificaciones_    │            │ pallet-types/       │                 │
│  │ clientes/           │            │ product-types/      │                 │
│  └─────────────────────┘            │ sectors/            │                 │
│                                     │ styles/             │                 │
│  REPORTES                           │ [+15 mas...]        │                 │
│  ┌─────────────────────┐            └─────────────────────┘                 │
│  │ reports/            │                                                     │
│  │ reports2/           │            INFRAESTRUCTURA                         │
│  │ reports3/           │            ┌─────────────────────┐                 │
│  │ pdf/                │            │ layouts/            │                 │
│  └─────────────────────┘            │ partials/           │                 │
│                                     │ email/              │                 │
│                                     │ vendor/             │                 │
│                                     └─────────────────────┘                 │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. PATRONES DE VISTAS

### 3.1 Patron CRUD Estandar

Cada mantenedor sigue el patron:

```
[entidad]/
├── index.blade.php    # Lista con DataTable
├── create.blade.php   # Formulario crear
├── edit.blade.php     # Formulario editar
├── form.blade.php     # Partial del formulario (reutilizado)
└── [masive.blade.php] # Carga masiva (opcional)
```

### 3.2 Layouts Identificados

| Layout | Ubicacion | Uso |
|--------|-----------|-----|
| `app` | layouts/app.blade.php | Layout principal autenticado |
| `auth` | layouts/auth.blade.php | Layout login/registro |

### 3.3 Partials Comunes

| Partial | Proposito |
|---------|-----------|
| `partials/sidebar` | Menu lateral |
| `partials/navbar` | Barra superior |
| `partials/alerts` | Mensajes flash |
| `partials/modals` | Modales reutilizables |

---

## 4. COMPONENTES UI CRITICOS

### 4.1 Formulario Cascade (OT)

**Ubicacion**: `work-orders/create.blade.php`, `work-orders/edit.blade.php`

**Campos en Secuencia**:
1. Jerarquia 1 (Sector)
2. Jerarquia 2 (Subsector)
3. Jerarquia 3 (Rubro)
4. Carton
5. Recubrimiento Interno
6. Recubrimiento Externo
7. Color
8. Planta

**Comportamiento**: AJAX cascade - cada campo filtra el siguiente.

### 4.2 DataTables

**Uso**: Todas las listas (index.blade.php)
**Libreria**: jQuery DataTables
**Caracteristicas**:
- Paginacion server-side
- Busqueda
- Ordenamiento
- Export (Excel, PDF, CSV)

### 4.3 Select2

**Uso**: Selectores con busqueda
**Implementacion**: Campos de cliente, producto, cascade

### 4.4 Modales Bootstrap

**Uso**: Confirmaciones, formularios rapidos, detalles
**Patron**: Modal generico con contenido dinamico via AJAX

---

## 5. TECNOLOGIAS FRONTEND

### 5.1 Stack Actual

| Tecnologia | Version | Uso |
|------------|---------|-----|
| Bootstrap | 4.x | Framework CSS |
| jQuery | 3.x | Manipulacion DOM |
| DataTables | 1.10.x | Tablas interactivas |
| Select2 | 4.x | Selectores avanzados |
| SweetAlert2 | - | Alertas/Confirmaciones |
| Chart.js | - | Graficos en reportes |

### 5.2 Assets

```
public/
├── css/
│   ├── app.css         # Estilos compilados
│   └── custom.css      # Estilos personalizados
├── js/
│   ├── app.js          # JS compilado
│   └── custom/         # Scripts por modulo
└── vendor/             # Librerias externas
```

---

## 6. MAPEO VISTAS -> RUTAS

### 6.1 Rutas Core

| Vista | Ruta | Controlador |
|-------|------|-------------|
| work-orders/index | /work-orders | WorkOrderController@index |
| work-orders/create | /work-orders/create | WorkOrderController@create |
| work-orders/edit | /work-orders/{id}/edit | WorkOrderController@edit |
| cotizador/index | /cotizador | CotizacionController@index |
| clients/index | /clients | ClientController@index |

### 6.2 Rutas AJAX Cascade

| Endpoint | Proposito |
|----------|-----------|
| /ajax/hierarchy2/{h1_id} | Obtener Jerarquia2 segun Jerarquia1 |
| /ajax/hierarchy3/{h2_id} | Obtener Jerarquia3 segun Jerarquia2 |
| /ajax/cartons/{h3_id} | Obtener Cartones segun Jerarquia3 |
| /ajax/coverages/{carton_id} | Obtener Recubrimientos |
| /ajax/colors/{coverage_id} | Obtener Colores |
| /ajax/plants/{color_id} | Obtener Plantas |

---

## 7. GAPS DE UI IDENTIFICADOS

| Gap | Descripcion | Severidad | Recomendacion |
|-----|-------------|-----------|---------------|
| GAP-UI-001 | JS embebido en vistas | MEDIO | Extraer a archivos separados |
| GAP-UI-002 | Sin componentes Vue/React | BAJO | Mantener jQuery por ahora |
| GAP-UI-003 | CSS no modular | BAJO | Considerar SASS/SCSS |
| GAP-UI-004 | No hay design system | MEDIO | Documentar patrones existentes |
| GAP-UI-005 | Partials inconsistentes | BAJO | Estandarizar nomenclatura |

---

## 8. TERMINOS UI

### 8.1 Glosario de Componentes

| Termino | Definicion | Uso |
|---------|------------|-----|
| **Cascade Form** | Formulario con campos dependientes | Crear/Editar OT |
| **DataTable** | Tabla interactiva con paginacion | Listas index |
| **Modal Gestion** | Modal para gestiones rapidas | Workflow OT |
| **Sidebar** | Menu lateral colapsable | Navegacion |
| **Breadcrumb** | Ruta de navegacion | Contexto |
| **Card** | Contenedor de informacion | Dashboard |
| **Alert** | Mensaje de sistema | Feedback usuario |

---

## 9. INTEGRACION CON NEO4J

```cypher
// Crear nodos de componentes UI
CREATE (ui:ComponenteUI {
  id: 'UI-VIEWS-V12',
  tipo: 'Inventario Vistas',
  total_vistas: 319,
  carpetas: 41,

  vistas_core: ['work-orders', 'cotizador', 'clients'],
  vistas_mantenedores: 25,
  vistas_reportes: 3,

  tecnologias: ['Bootstrap 4', 'jQuery 3', 'DataTables', 'Select2'],
  patron_crud: 'index/create/edit/form',

  fase: '5.5',
  fecha: datetime()
});

// Relacionar con servicios
MATCH (ms:Microservicio {id: 'MS-001'})
MATCH (ui:ComponenteUI {id: 'UI-VIEWS-V12'})
CREATE (ms)-[:TIENE_VISTAS]->(ui);
```

---

## 10. RESUMEN

### Estado: COMPLETADO

| Aspecto | Detalle |
|---------|---------|
| Vistas documentadas | 319 archivos Blade |
| Carpetas clasificadas | 41 |
| Patrones identificados | CRUD, Cascade, DataTable |
| Tecnologias | Bootstrap 4, jQuery 3, DataTables |
| Gaps identificados | 5 |
| Terminos UI | 7 definidos |

### Integracion con Fase 5

Este documento complementa:
- **5.1**: Vistas mapeadas a servicios
- **5.2**: Rutas mapeadas a vistas
- **5.3**: Terminos UI agregados al glosario

### Documento Relacionado

- **[FASE_5_5B_ESTANDARES_MONITOR_ONE.md](FASE_5_5B_ESTANDARES_MONITOR_ONE.md)**: Estandares de UI extraidos del proyecto Monitor One de Tecnoandina, adaptados para INVEB. Incluye paleta de colores corporativa, tipografia Poppins, y patrones de componentes.

---

**Documento generado**: 2025-12-17
**Actualizado**: 2025-12-19
**Version**: 1.1
**Fase**: 5.5 - Diseno UI (Anexo)
