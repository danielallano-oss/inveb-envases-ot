# FASE 5.3: Terminos - Terminos Ancla Microservicios

**ID**: `PASO-05.03-V2`
**Fecha**: 2025-12-19
**Estado**: Actualizado a Monitor One

---

## Resumen

Este documento define los terminos ancla (vocabulario de dominio) para cada microservicio. Los terminos son **independientes de la tecnologia** - aplican tanto a la implementacion legado (Laravel) como a Monitor One (Python/FastAPI).

> **Nota**: El primer servicio implementado es **MS-004 CascadeService** en `msw-envases-ot/`. Ver [FASE_5_6](FASE_5_6_IMPLEMENTACION_MICROSERVICIO.md) para detalles de implementacion.

---

## 1. GLOSARIO GLOBAL DEL SISTEMA

### 1.1 Terminos Fundamentales

| Termino | Definicion | Contexto |
|---------|------------|----------|
| **OT** | Orden de Trabajo - Unidad fundamental de produccion | Core del negocio |
| **Cascade** | Sistema de dependencia secuencial de campos | Validacion |
| **Jerarquia** | Clasificacion de productos en 3 niveles | Catalogos |
| **Carton** | Material base para envases | Producto |
| **Planta** | Ubicacion de produccion (fabrica) | Operaciones |
| **Cotizacion** | Presupuesto de productos para cliente | Comercial |
| **Muestra** | Prototipo fisico de envase | Desarrollo |
| **Gestion** | Accion/interaccion sobre una OT | Workflow |
| **Mantenedor** | CRUD de entidad maestra | Administracion |

### 1.2 Diagrama de Dominios

```
┌─────────────────────────────────────────────────────────────────────────┐
│                      DOMINIOS INVEB ENVASES-OT                          │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ┌──────────────┐     ┌──────────────┐     ┌──────────────┐             │
│  │   COMERCIAL  │────▶│  PRODUCCION  │────▶│  OPERACIONES │             │
│  │              │     │              │     │              │             │
│  │ - Cotizacion │     │ - OT         │     │ - Gestion    │             │
│  │ - Cliente    │     │ - Muestra    │     │ - Workflow   │             │
│  │ - Contacto   │     │ - CAD        │     │ - Reporte    │             │
│  └──────────────┘     └──────────────┘     └──────────────┘             │
│          │                   │                    │                      │
│          └───────────────────┼────────────────────┘                      │
│                              ▼                                           │
│                    ┌──────────────────┐                                  │
│                    │    CATALOGOS     │                                  │
│                    │                  │                                  │
│                    │ - Jerarquia      │                                  │
│                    │ - Carton         │                                  │
│                    │ - Recubrimiento  │                                  │
│                    │ - Color          │                                  │
│                    │ - Planta         │                                  │
│                    └──────────────────┘                                  │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 2. TERMINOS POR MICROSERVICIO

### 2.1 MS-001: OTService (WorkOrder)

#### Terminos Ancla

| Termino | Definicion | Sinonimos | Relaciones |
|---------|------------|-----------|------------|
| **WorkOrder** | Orden de trabajo completa | OT, Orden | Cliente, Producto |
| **OTCode** | Codigo unico de OT | Numero OT | Formato: OT-YYYY-NNNNNN |
| **OTStatus** | Estado actual de la OT | Estado | pending/approved/rejected/completed |
| **OTDetail** | Especificaciones tecnicas | Detalle | Dimensiones, material |
| **Reproceso** | OT derivada de otra OT | Reprocesamiento | OT padre |
| **Cronograma** | Timeline de produccion | Fechas | Hitos |
| **Despacho** | Entrega de producto | Envio | Planta destino |
| **CAD** | Codigo de diseno estructural | Plano | Material |
| **Material** | Codigo SAP de producto | Codigo material | CAD |

#### Invariantes de Dominio

```
- Una OT debe tener exactamente un cliente
- Una OT debe tener una combinacion valida de cascade (8 campos)
- El codigo OT es inmutable una vez creado
- Una OT aprobada no puede ser editada (solo duplicada)
- Un reproceso debe referenciar una OT padre existente
```

---

### 2.2 MS-002: AuthService

#### Terminos Ancla

| Termino | Definicion | Sinonimos | Relaciones |
|---------|------------|-----------|------------|
| **User** | Usuario del sistema | Usuario | Rol, Area |
| **Session** | Sesion activa | Login | Token |
| **Credential** | Datos de acceso | Contraseña | User |
| **AzureAuth** | Autenticacion via Azure AD | SSO, OAuth | User |
| **Role** | Rol de usuario | Perfil | Permisos |
| **Permission** | Permiso granular | Acceso | Rol |

#### Invariantes de Dominio

```
- Un usuario puede tener multiples roles
- Un usuario debe pertenecer a un area
- Las sesiones expiran despues de 120 minutos
- Los passwords deben cumplir politica de seguridad
- Azure AD es prioridad sobre login local
```

---

### 2.3 MS-003: ClientService

#### Terminos Ancla

| Termino | Definicion | Sinonimos | Relaciones |
|---------|------------|-----------|------------|
| **Client** | Empresa cliente | Cliente, Cuenta | RUT |
| **Contact** | Persona de contacto | Contacto | Client |
| **Installation** | Ubicacion del cliente | Planta cliente, Instalacion | Client |
| **SpecialIndication** | Instrucciones especiales | Indicacion, Nota | Installation |
| **ClientClassification** | Categoria de cliente | Clasificacion | Client |
| **RUT** | Identificador tributario | NIT, RFC | Client |

#### Invariantes de Dominio

```
- Un cliente tiene RUT unico en el sistema
- Un cliente puede tener multiples contactos
- Un cliente puede tener multiples instalaciones
- Cada instalacion tiene direccion unica
- Las indicaciones especiales son por instalacion
```

---

### 2.4 MS-004: CascadeService

#### Terminos Ancla

| Termino | Definicion | Sinonimos | Relaciones |
|---------|------------|-----------|------------|
| **Cascade** | Cadena de dependencia | Secuencia, Filtro | 8 pasos |
| **Hierarchy1** | Tipo de producto (nivel 1) | Jerarquia 1, Sector | Root |
| **Hierarchy2** | Subtipo producto (nivel 2) | Jerarquia 2 | Hierarchy1 |
| **Hierarchy3** | Detalle producto (nivel 3) | Jerarquia 3 | Hierarchy2 |
| **CascadeStep** | Paso individual | Nivel | Orden 1-8 |
| **ValidCombination** | Combo permitido | Combinacion valida | Regla |
| **CascadeRule** | Regla de filtrado | Regla cascade | Declarativa |

#### Secuencia de Cascade (8 Pasos)

```
┌────────────────────────────────────────────────────────────────────┐
│                    SECUENCIA CASCADE                                │
├────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  [1] Jerarquia1 ──▶ [2] Jerarquia2 ──▶ [3] Jerarquia3              │
│         │                  │                  │                     │
│         └──────────────────┼──────────────────┘                     │
│                            ▼                                        │
│                    [4] Carton ──▶ [5] RecubrimientoInt              │
│                         │                  │                        │
│                         └──────────────────┼────────────────────    │
│                                            ▼                        │
│                    [6] RecubrimientoExt ──▶ [7] Color               │
│                                                   │                 │
│                                                   ▼                 │
│                                            [8] Planta               │
│                                                                     │
└────────────────────────────────────────────────────────────────────┘
```

#### Invariantes de Dominio

```
- Cada paso depende del anterior (orden estricto)
- Una combinacion invalida bloquea la creacion de OT
- Los filtros son deterministas (mismo input = mismo output)
- Jerarquia3 define el "rubro" del producto
- La planta es el ultimo paso y define disponibilidad
```

---

### 2.5 MS-005: CotizacionService

#### Terminos Ancla

| Termino | Definicion | Sinonimos | Relaciones |
|---------|------------|-----------|------------|
| **Quotation** | Cotizacion completa | Cotizacion | Cliente |
| **QuotationDetail** | Linea de cotizacion | Detalle | Producto |
| **QuotationStatus** | Estado cotizacion | Estado | pending/approved/rejected |
| **Margin** | Margen de ganancia | Rentabilidad | Porcentaje |
| **MinimumMargin** | Margen minimo permitido | Margen piso | Regla |
| **PriceCalculation** | Calculo de precio | Costeo | Formula |
| **Approval** | Aprobacion de cotizacion | Visto bueno | Workflow |

#### Invariantes de Dominio

```
- Una cotizacion debe tener al menos un detalle
- El margen no puede ser menor al margen minimo por clasificacion
- Cotizaciones bajo margen requieren aprobacion especial
- Una cotizacion aprobada genera OTs automaticamente
- Versiones de cotizacion mantienen historial
```

---

### 2.6 MS-006: ReportService

#### Terminos Ancla

| Termino | Definicion | Sinonimos | Relaciones |
|---------|------------|-----------|------------|
| **Report** | Reporte generado | Informe | Datos |
| **KPI** | Indicador clave | Metrica, Indicador | Dashboard |
| **Period** | Periodo de tiempo | Rango fechas | Filtro |
| **Conversion** | Tasa de conversion OT | Ratio | OT aprobadas/totales |
| **LeadTime** | Tiempo de ciclo | Tiempo proceso | OT |
| **RejectionReason** | Motivo de rechazo | Causa rechazo | OT rechazada |

#### Invariantes de Dominio

```
- Los reportes son de solo lectura
- Los datos se calculan en tiempo real o batch
- Los periodos pueden ser: dia, semana, mes, custom
- KPIs tienen umbrales de alerta configurables
```

---

### 2.7 MS-007: WorkflowService

#### Terminos Ancla

| Termino | Definicion | Sinonimos | Relaciones |
|---------|------------|-----------|------------|
| **State** | Estado en maquina de estados | Estado | OT/Cotizacion |
| **Transition** | Cambio de estado | Transicion | From/To state |
| **Management** | Gestion/accion sobre entidad | Gestion | OT |
| **Response** | Respuesta a gestion | Respuesta | Management |
| **Reactivation** | Reactivar entidad | Reactivacion | OT anulada |
| **Log** | Registro de auditoria | Historial | Todas entidades |

#### Estados de OT

```
┌────────────────────────────────────────────────────────────────────┐
│                    MAQUINA DE ESTADOS OT                           │
├────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  [DRAFT] ──crear──▶ [PENDING] ──aprobar──▶ [APPROVED]              │
│     │                   │                       │                   │
│     │                   │ rechazar              │ completar         │
│     │                   ▼                       ▼                   │
│     │              [REJECTED]              [COMPLETED]              │
│     │                   │                                           │
│     │                   │ retomar                                   │
│     │                   ▼                                           │
│     └────anular───▶ [CANCELLED] ◀──────────────┘                   │
│                                                                     │
└────────────────────────────────────────────────────────────────────┘
```

---

### 2.8 MS-008: HierarchyService

#### Terminos Ancla

| Termino | Definicion | Sinonimos | Relaciones |
|---------|------------|-----------|------------|
| **Area** | Area organizacional | Departamento | Usuarios |
| **Role** | Rol de sistema | Perfil | Permisos |
| **Permission** | Permiso atomico | Acceso | Accion |
| **Sector** | Sector industrial | Rubro | Jerarquia |
| **OrgStructure** | Estructura organizacional | Organigrama | Areas |

---

### 2.9 MS-009: NotificationService

#### Terminos Ancla

| Termino | Definicion | Sinonimos | Relaciones |
|---------|------------|-----------|------------|
| **Notification** | Notificacion del sistema | Aviso | Usuario |
| **Alert** | Alerta urgente | Alerta | Prioridad |
| **Channel** | Canal de notificacion | Via | Email/Web/Push |
| **Template** | Plantilla de mensaje | Template | Tipo notificacion |
| **ReadStatus** | Estado de lectura | Leido/No leido | Notification |

---

### 2.10 MS-010: CatalogService

#### Terminos Ancla

| Termino | Definicion | Sinonimos | Relaciones |
|---------|------------|-----------|------------|
| **Carton** | Tipo de carton | Material base | Onda, gramos |
| **Coverage** | Recubrimiento | Cobertura | Interno/Externo |
| **Color** | Color del carton | Tono | Carton |
| **Style** | Estilo de envase | Tipo caja | FEFCO |
| **ProductType** | Tipo de producto | Categoria | Sector |
| **Plant** | Planta de produccion | Fabrica | Ubicacion |
| **OperationalSequence** | Secuencia de operaciones | Proceso | Planta |
| **Adhesive** | Tipo de adhesivo | Pegamento | Proceso |
| **Canal** | Canal/Onda del carton | Flauta | A/B/C/E/BC |

---

## 3. MATRIZ DE TERMINOS COMPARTIDOS

| Termino | MS-001 | MS-003 | MS-004 | MS-005 | MS-010 |
|---------|--------|--------|--------|--------|--------|
| Client | X | **Owner** | - | X | - |
| Carton | X | - | X | X | **Owner** |
| Hierarchy | X | - | **Owner** | X | - |
| Plant | X | - | X | X | **Owner** |
| Coverage | X | - | X | - | **Owner** |
| Status | **Owner** | - | - | X | - |

**Owner** = Servicio dueno del termino (fuente de verdad)
**X** = Servicio que consume el termino

---

## 4. CONTEXTOS DELIMITADOS (Bounded Contexts)

### 4.1 Mapa de Contextos

```
┌─────────────────────────────────────────────────────────────────────────┐
│                    BOUNDED CONTEXTS                                      │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ┌─────────────────┐         ┌─────────────────┐                        │
│  │ SALES CONTEXT   │ ◀──────▶│ PRODUCTION CTX  │                        │
│  │                 │         │                 │                        │
│  │ - CotizacionSvc │         │ - OTService     │                        │
│  │ - ClientService │         │ - WorkflowSvc   │                        │
│  └─────────────────┘         └─────────────────┘                        │
│          │                           │                                   │
│          │    Shared Kernel          │                                   │
│          └──────────┬────────────────┘                                   │
│                     ▼                                                    │
│          ┌─────────────────┐                                            │
│          │ CATALOG CONTEXT │                                            │
│          │                 │                                            │
│          │ - CatalogSvc    │                                            │
│          │ - CascadeService│                                            │
│          │ - HierarchySvc  │                                            │
│          └─────────────────┘                                            │
│                                                                          │
│  ┌─────────────────┐         ┌─────────────────┐                        │
│  │ IDENTITY CTX    │         │ REPORTING CTX   │                        │
│  │                 │         │                 │                        │
│  │ - AuthService   │         │ - ReportService │                        │
│  │ - NotificationS │         │                 │                        │
│  └─────────────────┘         └─────────────────┘                        │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 5. SIGUIENTE PASO

**PASO 5.4**: Conocimiento - Consolidar documentacion y ejecutar QA.

---

**Documento generado**: 2025-12-17
**Actualizado**: 2025-12-19
**Version**: 2.0 (Compatible con Monitor One)
