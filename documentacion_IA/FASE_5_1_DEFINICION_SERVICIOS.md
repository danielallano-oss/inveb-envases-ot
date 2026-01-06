# FASE 5.1: Definicion de Servicios/Modulos

**ID**: `PASO-05.01-V2`
**Fecha**: 2025-12-19
**Estado**: Actualizado a Monitor One

---

## Resumen

Este documento define los servicios del sistema INVEB Envases-OT. La implementacion sigue los **estandares Monitor One** de Tecnoandina:

| Aspecto | Legado (Laravel) | Monitor One |
|---------|------------------|-------------|
| Backend | PHP 7.4 + Laravel 5.8 | **Python 3.12 + FastAPI** |
| ORM | Eloquent | **SQLModel** |
| Base de datos | MySQL 8.0 | **PostgreSQL 15** |
| Frontend | Blade + jQuery | **React + styled-components** |

**Estrategia de Migracion**: Implementar microservicios Python/FastAPI que reemplazan gradualmente los controladores Laravel. El primer servicio implementado es **MS-004 CascadeService** (ver [FASE_5_6_IMPLEMENTACION_MICROSERVICIO.md](FASE_5_6_IMPLEMENTACION_MICROSERVICIO.md)).

---

## 0. SERVICIOS IMPLEMENTADOS (Monitor One)

| Servicio | Estado | Ubicacion | Stack |
|----------|--------|-----------|-------|
| **MS-004 CascadeService** | Implementado | `msw-envases-ot/` | Python + FastAPI |
| MS-001 a MS-010 (otros) | Pendiente | - | - |

> **Referencia**: La implementacion detallada del CascadeService esta en [FASE_5_6](FASE_5_6_IMPLEMENTACION_MICROSERVICIO.md).

---

## 1. INVENTARIO DE CONTROLADORES

### 1.1 Total de Controladores: 56

```
Controllers/
├── Core OT (4)
│   ├── WorkOrderController.php
│   ├── WorkOrderExcelController.php
│   ├── WorkOrderOldController.php
│   └── UserWorkOrderController.php
├── Autenticacion (3)
│   ├── AuthController.php
│   ├── UserController.php
│   └── RoleController.php
├── Clientes (2)
│   ├── ClientController.php
│   └── ClasificacionClienteController.php
├── Cotizacion (3)
│   ├── CotizacionController.php
│   ├── CotizacionApprovalController.php
│   └── DetalleCotizacionController.php
├── Cascada/Configuracion (12)
│   ├── ProductTypeController.php
│   ├── CartonController.php
│   ├── TipoCintaController.php
│   ├── ColorController.php
│   └── ... (8 mas)
├── Jerarquia (3)
│   ├── HierarchyController.php
│   ├── SubhierarchyController.php
│   └── SubsubhierarchyController.php
├── Reportes (3)
│   ├── ReportController.php
│   ├── Report2Controller.php
│   └── Report3Controller.php
├── Workflow (4)
│   ├── ManagementController.php
│   ├── ManagementTypeController.php
│   ├── ProcessController.php
│   └── WorkSpaceController.php
├── Notificaciones (1)
│   └── NotificationController.php
└── Mantenedores (21)
    └── *Controller.php
```

---

## 2. DEFINICION DE SERVICIOS

### 2.1 Diagrama de Servicios

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        INVEB ENVASES-OT SERVICES                        │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐         │
│  │   OT SERVICE    │  │  AUTH SERVICE   │  │ CLIENT SERVICE  │         │
│  │   (MS-001)      │  │   (MS-002)      │  │   (MS-003)      │         │
│  │                 │  │                 │  │                 │         │
│  │ - CRUD OT       │  │ - Login/Logout  │  │ - CRUD Clientes │         │
│  │ - Estados       │  │ - Roles         │  │ - Clasificacion │         │
│  │ - Exportacion   │  │ - Permisos      │  │                 │         │
│  └────────┬────────┘  └─────────────────┘  └─────────────────┘         │
│           │                                                              │
│  ┌────────▼────────┐  ┌─────────────────┐  ┌─────────────────┐         │
│  │CASCADE SERVICE  │  │COTIZACION SERV  │  │ REPORT SERVICE  │         │
│  │   (MS-004)      │  │   (MS-005)      │  │   (MS-006)      │         │
│  │                 │  │                 │  │                 │         │
│  │ - Reglas        │  │ - CRUD Cotiz    │  │ - Generacion    │         │
│  │ - Validaciones  │  │ - Aprobaciones  │  │ - Exportacion   │         │
│  │ - Combinaciones │  │ - Detalles      │  │ - Filtros       │         │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘         │
│                                                                          │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐         │
│  │WORKFLOW SERVICE │  │HIERARCHY SERV   │  │NOTIFICATION SRV │         │
│  │   (MS-007)      │  │   (MS-008)      │  │   (MS-009)      │         │
│  │                 │  │                 │  │                 │         │
│  │ - Gestiones     │  │ - Canales       │  │ - Alertas       │         │
│  │ - Transiciones  │  │ - Jerarquias    │  │ - Emails        │         │
│  │ - WorkSpaces    │  │ - Productos     │  │ - Push          │         │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘         │
│                                                                          │
│  ┌──────────────────────────────────────────────────────────┐          │
│  │                  CATALOG SERVICE (MS-010)                 │          │
│  │                                                           │          │
│  │  Mantenedores: Carton, Color, Adhesivo, Material, etc.   │          │
│  └──────────────────────────────────────────────────────────┘          │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 3. ESPECIFICACION DE SERVICIOS

### 3.1 MS-001: OT Service (Orden de Trabajo)

| Atributo | Valor |
|----------|-------|
| **ID** | MS-001-V12 |
| **Nombre** | OTService |
| **Responsabilidad** | Gestion completa del ciclo de vida de Ordenes de Trabajo |
| **Prioridad** | Critica |

**Controladores asociados:**
- WorkOrderController
- WorkOrderExcelController
- WorkOrderOldController
- UserWorkOrderController

**Funcionalidades:**
```yaml
funcionalidades:
  - crear_ot: Crear nueva OT con validacion de cascada
  - editar_ot: Modificar OT existente
  - listar_ot: Listar OTs con filtros
  - cambiar_estado: Transicion de estados
  - duplicar_ot: Crear OT basada en existente
  - exportar_excel: Generar Excel de OTs
  - asignar_usuario: Asignar responsable a OT
```

**Dependencias:**
- CascadeService (validacion de campos)
- WorkflowService (estados)
- ClientService (cliente de OT)
- NotificationService (alertas)

---

### 3.2 MS-002: Auth Service (Autenticacion)

| Atributo | Valor |
|----------|-------|
| **ID** | MS-002-V12 |
| **Nombre** | AuthService |
| **Responsabilidad** | Autenticacion, usuarios y roles |
| **Prioridad** | Critica |

**Controladores asociados:**
- AuthController
- UserController
- RoleController

**Funcionalidades:**
```yaml
funcionalidades:
  - login: Autenticar usuario
  - logout: Cerrar sesion
  - crud_usuarios: Gestion de usuarios
  - crud_roles: Gestion de roles
  - asignar_permisos: Permisos por rol
  - verificar_permiso: Check de acceso
```

---

### 3.3 MS-003: Client Service (Clientes)

| Atributo | Valor |
|----------|-------|
| **ID** | MS-003-V12 |
| **Nombre** | ClientService |
| **Responsabilidad** | Gestion de clientes y clasificaciones |
| **Prioridad** | Alta |

**Controladores asociados:**
- ClientController
- ClasificacionClienteController

**Funcionalidades:**
```yaml
funcionalidades:
  - crud_clientes: Gestion de clientes
  - buscar_cliente: Busqueda con filtros
  - clasificar_cliente: Asignar clasificacion
  - listar_por_clasificacion: Filtrar por tipo
```

---

### 3.4 MS-004: Cascade Service (Reglas Cascada)

| Atributo | Valor |
|----------|-------|
| **ID** | MS-004-V12 |
| **Nombre** | CascadeService |
| **Responsabilidad** | Validacion y aplicacion de reglas de cascada |
| **Prioridad** | Critica |

**Controladores asociados:**
- ProductTypeController
- CartonController
- TipoCintaController
- ColorController
- (tablas de cascada)

**Funcionalidades:**
```yaml
funcionalidades:
  - validar_combinacion: Verificar combinacion valida
  - obtener_opciones: Opciones para campo destino
  - aplicar_regla: Ejecutar regla de cascada
  - obtener_secuencia: Secuencia de cascada completa
  - filtrar_por_planta: Opciones segun planta
```

**Tablas relacionadas:**
- cascade_rules (nueva)
- cascade_valid_combinations (nueva)
- relacion_filtro_ingresos_principales (actual)

---

### 3.5 MS-005: Cotizacion Service

| Atributo | Valor |
|----------|-------|
| **ID** | MS-005-V12 |
| **Nombre** | CotizacionService |
| **Responsabilidad** | Gestion de cotizaciones y aprobaciones |
| **Prioridad** | Alta |

**Controladores asociados:**
- CotizacionController
- CotizacionApprovalController
- DetalleCotizacionController

**Funcionalidades:**
```yaml
funcionalidades:
  - crear_cotizacion: Nueva cotizacion desde OT
  - editar_cotizacion: Modificar cotizacion
  - aprobar_cotizacion: Flujo de aprobacion
  - rechazar_cotizacion: Rechazar con motivo
  - agregar_detalle: Items de cotizacion
  - calcular_total: Sumar items
```

---

### 3.6 MS-006: Report Service

| Atributo | Valor |
|----------|-------|
| **ID** | MS-006-V12 |
| **Nombre** | ReportService |
| **Responsabilidad** | Generacion de reportes y exportaciones |
| **Prioridad** | Media |

**Controladores asociados:**
- ReportController
- Report2Controller
- Report3Controller

**Funcionalidades:**
```yaml
funcionalidades:
  - generar_reporte: Reporte parametrizado
  - exportar_pdf: Salida PDF
  - exportar_excel: Salida Excel
  - filtrar_datos: Aplicar filtros
  - agrupar_datos: Agrupaciones personalizadas
```

---

### 3.7 MS-007: Workflow Service

| Atributo | Valor |
|----------|-------|
| **ID** | MS-007-V12 |
| **Nombre** | WorkflowService |
| **Responsabilidad** | Gestion de estados, transiciones y gestiones |
| **Prioridad** | Critica |

**Controladores asociados:**
- ManagementController
- ManagementTypeController
- ProcessController
- WorkSpaceController

**Funcionalidades:**
```yaml
funcionalidades:
  - crear_gestion: Nueva gestion sobre OT
  - transicion_estado: Cambiar estado de OT
  - validar_transicion: Verificar transicion valida
  - obtener_estados: Estados disponibles
  - historial_gestiones: Log de acciones
```

---

### 3.8 MS-008: Hierarchy Service

| Atributo | Valor |
|----------|-------|
| **ID** | MS-008-V12 |
| **Nombre** | HierarchyService |
| **Responsabilidad** | Gestion de jerarquias de producto |
| **Prioridad** | Media |

**Controladores asociados:**
- CanalController
- HierarchyController
- SubhierarchyController
- SubsubhierarchyController

**Funcionalidades:**
```yaml
funcionalidades:
  - crud_canal: Gestion de canales
  - crud_jerarquia: Nivel 1
  - crud_subjerarquia: Nivel 2
  - crud_subsubjerarquia: Nivel 3
  - obtener_arbol: Arbol completo
```

---

### 3.9 MS-009: Notification Service

| Atributo | Valor |
|----------|-------|
| **ID** | MS-009-V12 |
| **Nombre** | NotificationService |
| **Responsabilidad** | Envio de notificaciones y alertas |
| **Prioridad** | Media |

**Controladores asociados:**
- NotificationController

**Funcionalidades:**
```yaml
funcionalidades:
  - enviar_notificacion: Notificar usuario
  - marcar_leida: Actualizar estado
  - listar_notificaciones: Por usuario
  - notificar_cambio_estado: Trigger automatico
```

---

### 3.10 MS-010: Catalog Service (Mantenedores)

| Atributo | Valor |
|----------|-------|
| **ID** | MS-010-V12 |
| **Nombre** | CatalogService |
| **Responsabilidad** | CRUD de catalogos y mantenedores |
| **Prioridad** | Baja |

**Controladores asociados:**
- MantenedorController
- AdhesivoController
- AlmacenController
- ArmadoController
- MaterialController
- MercadoController
- PalletTypeController
- PegadoController
- RayadoController
- SectorController
- StyleController
- (otros 10+)

**Funcionalidades:**
```yaml
funcionalidades:
  - crud_generico: CRUD para cualquier catalogo
  - listar_activos: Solo registros activos
  - buscar: Busqueda por nombre/codigo
  - activar_desactivar: Cambiar estado
```

---

## 4. MATRIZ DE DEPENDENCIAS

```
              MS-001  MS-002  MS-003  MS-004  MS-005  MS-006  MS-007  MS-008  MS-009  MS-010
MS-001 OT       -       X       X       X       X       -       X       X       X       X
MS-002 Auth     -       -       -       -       -       -       -       -       -       -
MS-003 Client   -       X       -       -       -       -       -       -       -       -
MS-004 Cascade  -       -       -       -       -       -       -       -       -       X
MS-005 Cotiz    X       X       X       -       -       -       -       -       X       -
MS-006 Report   X       X       -       -       X       -       -       -       -       -
MS-007 Workflow X       X       -       -       -       -       -       -       X       -
MS-008 Hierarch -       X       -       -       -       -       -       -       -       -
MS-009 Notif    -       X       -       -       -       -       -       -       -       -
MS-010 Catalog  -       X       -       -       -       -       -       -       -       -

X = Depende de
```

---

## 5. PRIORIZACION

| Prioridad | Servicio | Razon |
|-----------|----------|-------|
| 1 | MS-002 AuthService | Base para todos |
| 2 | MS-004 CascadeService | Core del formulario OT |
| 3 | MS-001 OTService | Funcionalidad principal |
| 4 | MS-007 WorkflowService | Estados de OT |
| 5 | MS-003 ClientService | Datos de cliente |
| 6 | MS-005 CotizacionService | Flujo comercial |
| 7 | MS-009 NotificationService | Comunicacion |
| 8 | MS-008 HierarchyService | Clasificacion |
| 9 | MS-006 ReportService | Reporteria |
| 10 | MS-010 CatalogService | Mantenedores |

---

## 6. ARQUITECTURA MONITOR ONE

### 6.1 Estructura de Microservicios

```
inveb/
├── msw-envases-ot/              # MS-004 CascadeService (IMPLEMENTADO)
│   ├── src/
│   │   ├── app/
│   │   │   ├── config/settings.py
│   │   │   ├── db/database.py
│   │   │   ├── models/
│   │   │   ├── routers/
│   │   │   ├── schemas/
│   │   │   └── services/
│   │   └── main.py
│   ├── alembic/
│   ├── Dockerfile
│   └── docker-compose.yaml
│
├── msw-envases-auth/            # MS-002 AuthService (PENDIENTE)
├── msw-envases-client/          # MS-003 ClientService (PENDIENTE)
├── msw-envases-workflow/        # MS-007 WorkflowService (PENDIENTE)
└── ...
```

### 6.2 Patron de Implementacion (Monitor One)

```python
# Ejemplo: msw-envases-ot/src/app/services/cascade_service.py
from sqlmodel import Session, select
from app.models import CascadeRule, CascadeValidCombination

class CascadeService:
    def __init__(self, session: Session):
        self.session = session

    def validate_combination(
        self,
        product_type_id: int,
        impresion: str,
        fsc: str
    ) -> bool:
        """Valida si la combinacion es permitida."""
        statement = select(CascadeValidCombination).where(
            CascadeValidCombination.product_type_id == product_type_id,
            CascadeValidCombination.impresion == impresion,
            CascadeValidCombination.fsc == fsc,
            CascadeValidCombination.active == True
        )
        result = self.session.exec(statement).first()
        return result is not None

    def get_rules_for_trigger(self, trigger_field: str) -> list[CascadeRule]:
        """Obtiene reglas activas para un campo trigger."""
        statement = select(CascadeRule).where(
            CascadeRule.trigger_field == trigger_field,
            CascadeRule.active == True
        ).order_by(CascadeRule.cascade_order)
        return self.session.exec(statement).all()
```

### 6.3 Comunicacion Entre Servicios

```
┌─────────────────────────────────────────────────────────────────────────┐
│                    ARQUITECTURA MONITOR ONE                              │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  Frontend (React)                                                        │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │                     API Gateway / Nginx                          │   │
│  └───────────┬─────────────────┬─────────────────┬─────────────────┘   │
│              │                 │                 │                      │
│  ┌───────────▼───┐   ┌────────▼────────┐   ┌───▼───────────┐         │
│  │ msw-envases-ot│   │msw-envases-auth │   │msw-envases-*  │         │
│  │ (CascadeServ) │   │ (AuthService)   │   │ (Otros)       │         │
│  │ :8000         │   │ :8001           │   │ :800X         │         │
│  │ Python/FastAPI│   │ Python/FastAPI  │   │ Python/FastAPI│         │
│  └───────┬───────┘   └─────────────────┘   └───────────────┘         │
│          │                                                              │
│  ┌───────▼───────────────────────────────────────────────────────┐    │
│  │                    PostgreSQL 15                               │    │
│  │                    inveb_envases                               │    │
│  └───────────────────────────────────────────────────────────────┘    │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 7. RESUMEN

| Metrica | Valor |
|---------|-------|
| Controladores existentes | 56 |
| Servicios definidos | 10 |
| Servicios criticos | 4 (Auth, OT, Cascade, Workflow) |
| Servicios alta prioridad | 2 (Client, Cotizacion) |
| Servicios media prioridad | 3 (Report, Hierarchy, Notification) |
| Servicios baja prioridad | 1 (Catalog) |

---

## 8. SIGUIENTE PASO

**PASO 5.2**: Especificacion - Definir endpoints y contratos API para cada servicio.

---

**Documento generado**: 2025-12-17
**Actualizado**: 2025-12-19
**Version**: 2.0 (Migrado a Monitor One)

### Historial de Cambios

| Version | Fecha | Cambios |
|---------|-------|---------|
| 1.0 | 2025-12-17 | Version inicial (Laravel Services) |
| 2.0 | 2025-12-19 | Migrado a Monitor One (Python/FastAPI microservicios) |
