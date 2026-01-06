# FASE 5.2: Especificacion - Endpoints y Contratos API

**ID**: `PASO-05.02-V2`
**Fecha**: 2025-12-19
**Estado**: Actualizado a Monitor One

---

## Resumen

Este documento especifica los endpoints del sistema INVEB Envases-OT. Incluye:
- **Endpoints Legado**: Rutas Laravel web.php (referencia)
- **Endpoints Monitor One**: API REST implementada en FastAPI (activo)

### Estado de Implementacion API

| Servicio | Endpoints Legado | Endpoints FastAPI | Estado |
|----------|------------------|-------------------|--------|
| **MS-004 CascadeService** | 10 AJAX | 15 REST | **Implementado** |
| MS-001 OTService | 25+ web | - | Pendiente |
| MS-002 AuthService | 10 web | - | Pendiente |
| Otros | ~200 web | - | Pendiente |

> **Documentacion OpenAPI**: Con FastAPI, la documentacion se genera automaticamente en `/docs` (Swagger) y `/redoc`.

---

## 1. ESTADO ACTUAL DE ENDPOINTS

### 1.1 Estadisticas Generales

| Metrica | Valor |
|---------|-------|
| Total Rutas Web | ~200+ |
| Rutas API REST | 0 (comentadas) |
| Rutas Protegidas (auth) | ~195 |
| Rutas Publicas | ~10 |
| Grupos de Rutas | 25+ prefijos |

### 1.2 Patron Actual

```
┌─────────────────────────────────────────────────────────────┐
│                    ARQUITECTURA RUTAS                       │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  routes/web.php (100%)        routes/api.php (0%)           │
│  ┌─────────────────┐          ┌─────────────────┐           │
│  │ Auth::routes()  │          │ // Comentado    │           │
│  │ Route::group()  │          │ // API Mobile   │           │
│  │ Route::prefix() │          │ // Desactivado  │           │
│  └─────────────────┘          └─────────────────┘           │
│                                                              │
│  Middleware: auth, role:XXX                                  │
│  Respuestas: Views (Blade) + JSON (AJAX)                    │
└─────────────────────────────────────────────────────────────┘
```

---

## 2. ENDPOINTS IMPLEMENTADOS (Monitor One)

### 2.0 MS-004: CascadeService - FastAPI (IMPLEMENTADO)

**Base URL**: `http://localhost:8000/api/v1`
**Documentacion**: `http://localhost:8000/docs`

#### Cascade Rules

| Metodo | Endpoint | Descripcion |
|--------|----------|-------------|
| GET | `/cascade-rules/` | Listar todas las reglas |
| GET | `/cascade-rules/{id}` | Obtener regla por ID |
| GET | `/cascade-rules/code/{code}` | Obtener regla por codigo |
| GET | `/cascade-rules/trigger/{field}` | Reglas por campo trigger |
| POST | `/cascade-rules/` | Crear nueva regla |
| PATCH | `/cascade-rules/{id}` | Actualizar regla |
| DELETE | `/cascade-rules/{id}` | Eliminar regla |

#### Cascade Combinations

| Metodo | Endpoint | Descripcion |
|--------|----------|-------------|
| GET | `/cascade-combinations/` | Listar combinaciones |
| GET | `/cascade-combinations/{id}` | Obtener con plantas |
| GET | `/cascade-combinations/validate/` | Validar combinacion |
| POST | `/cascade-combinations/` | Crear combinacion |
| DELETE | `/cascade-combinations/{id}` | Eliminar |
| GET | `/cascade-combinations/{id}/plantas` | Plantas de combo |
| POST | `/cascade-combinations/{id}/plantas` | Agregar planta |
| DELETE | `/cascade-combinations/{id}/plantas/{pid}` | Quitar planta |

#### Ejemplo Request/Response

```bash
# Validar combinacion
curl -X GET "http://localhost:8000/api/v1/cascade-combinations/validate/?product_type_id=1&impresion=flexo&fsc=fsc"

# Response
{
  "valid": true,
  "combination": {
    "id": 1,
    "product_type_id": 1,
    "impresion": "flexo",
    "fsc": "fsc",
    "active": true,
    "plantas": [1, 2, 3]
  }
}
```

```bash
# Obtener reglas por trigger
curl -X GET "http://localhost:8000/api/v1/cascade-rules/trigger/product_type_id"

# Response
[
  {
    "id": 1,
    "rule_code": "CASC-001",
    "rule_name": "Product Type -> Impresion",
    "trigger_field": "product_type_id",
    "target_field": "impresion",
    "action": "filter",
    "cascade_order": 1,
    "active": true
  }
]
```

---

## 3. ENDPOINTS LEGADO (Laravel - Referencia)

### 3.1 MS-001: OTService (WorkOrder)

#### Endpoints Existentes (Web)

| Metodo | Ruta | Controlador | Descripcion |
|--------|------|-------------|-------------|
| GET | `/home` | WorkOrderController@index | Listado OTs |
| GET | `/crear-ot` | WorkOrderController@create | Formulario nueva OT |
| POST | `/guardar` | WorkOrderController@store | Guardar OT |
| GET | `/edit-ot/{id}` | WorkOrderController@edit | Editar OT |
| PUT | `/actualizar-ot/{id}` | WorkOrderController@update | Actualizar OT |
| GET | `/duplicar/{idOt}` | WorkOrderController@duplicate | Duplicar OT |
| PUT | `/aprobarOt/{id}` | WorkOrderController@aprobarOt | Aprobar OT |
| PUT | `/rechazarOt/{id}` | WorkOrderController@rechazarOt | Rechazar OT |

#### Endpoints AJAX Existentes

| Metodo | Ruta | Funcion |
|--------|------|---------|
| GET | `/getCad` | Obtener CAD |
| GET | `/getCarton` | Obtener carton |
| GET | `/getDesignType` | Tipo de diseno |
| GET | `/getRecubrimientoInterno` | Recubrimiento interno |
| GET | `/getRecubrimientoExterno` | Recubrimiento externo |
| GET | `/getPlantaObjetivo` | Planta objetivo |
| GET | `/getSecuenciasOperacionales` | Secuencias |
| POST | `/postVerificacionFiltro` | Verificar filtros |

#### Contrato API REST Propuesto

```yaml
# OTService API Contract
openapi: 3.0.0
paths:
  /api/v1/work-orders:
    get:
      summary: Listar ordenes de trabajo
      parameters:
        - name: status
          in: query
          schema:
            type: string
            enum: [pending, approved, rejected, completed]
        - name: client_id
          in: query
          schema:
            type: integer
        - name: page
          in: query
          schema:
            type: integer
      responses:
        200:
          content:
            application/json:
              schema:
                type: object
                properties:
                  data:
                    type: array
                    items:
                      $ref: '#/components/schemas/WorkOrder'
                  meta:
                    $ref: '#/components/schemas/Pagination'
    post:
      summary: Crear orden de trabajo
      requestBody:
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/WorkOrderCreate'
      responses:
        201:
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/WorkOrder'

  /api/v1/work-orders/{id}:
    get:
      summary: Obtener OT por ID
    put:
      summary: Actualizar OT
    delete:
      summary: Eliminar OT (soft delete)

  /api/v1/work-orders/{id}/approve:
    post:
      summary: Aprobar OT

  /api/v1/work-orders/{id}/reject:
    post:
      summary: Rechazar OT
      requestBody:
        content:
          application/json:
            schema:
              properties:
                reason:
                  type: string
                  required: true
```

---

### 2.2 MS-002: AuthService

#### Endpoints Existentes

| Metodo | Ruta | Controlador | Descripcion |
|--------|------|-------------|-------------|
| GET | `/login` | Auth | Vista login |
| POST | `/login` | LoginController@login | Autenticar |
| POST | `/loginAzure` | LoginController@loginAzure | Login Azure AD |
| GET | `/auth/azure/callback` | LoginController@azureCallback | Callback OAuth |
| GET | `/resetPassword` | LoginController@resetPassword | Reset password |
| POST | `/resetPasswordStore` | LoginController@resetPasswordStore | Guardar reset |
| GET | `/recoveryPassword` | LoginController@recoveryPassword | Recuperar |
| POST | `/recoveryEmail` | LoginController@recoveryEmail | Enviar email |

#### Contrato API REST Propuesto

```yaml
paths:
  /api/v1/auth/login:
    post:
      summary: Autenticacion local
      requestBody:
        content:
          application/json:
            schema:
              properties:
                email:
                  type: string
                  format: email
                password:
                  type: string
      responses:
        200:
          content:
            application/json:
              schema:
                properties:
                  token:
                    type: string
                  user:
                    $ref: '#/components/schemas/User'
                  expires_at:
                    type: string
                    format: datetime

  /api/v1/auth/azure:
    get:
      summary: Iniciar flujo OAuth Azure AD
      responses:
        302:
          description: Redirect to Azure

  /api/v1/auth/logout:
    post:
      summary: Cerrar sesion
      security:
        - bearerAuth: []

  /api/v1/auth/refresh:
    post:
      summary: Refrescar token
```

---

### 2.3 MS-003: ClientService

#### Endpoints Existentes

| Metodo | Ruta | Controlador |
|--------|------|-------------|
| GET | `/mantenedores/clients/list` | ClientController@index |
| GET | `/mantenedores/clients/create` | ClientController@create |
| POST | `/mantenedores/clients/guardar` | ClientController@store |
| GET | `/mantenedores/clients/editar/{id}` | ClientController@edit |
| PUT | `/mantenedores/clients/actualizar/{id}` | ClientController@update |
| PUT | `/mantenedores/clients/activar/{id}` | ClientController@active |
| PUT | `/mantenedores/clients/inactivar/{id}` | ClientController@inactive |

#### Endpoints AJAX Existentes

| Metodo | Ruta | Funcion |
|--------|------|---------|
| GET | `/getContactosCliente` | Contactos de cliente |
| GET | `/getDatosContacto` | Datos de contacto |
| GET | `/getInstalacionesCliente` | Instalaciones |
| GET | `/getInformacionInstalacion` | Info instalacion |
| GET | `/getIndicacionesEspeciales` | Indicaciones |

#### Contrato API REST Propuesto

```yaml
paths:
  /api/v1/clients:
    get:
      summary: Listar clientes
      parameters:
        - name: search
          in: query
        - name: active
          in: query
          schema:
            type: boolean
    post:
      summary: Crear cliente

  /api/v1/clients/{id}:
    get:
      summary: Obtener cliente
    put:
      summary: Actualizar cliente
    delete:
      summary: Desactivar cliente

  /api/v1/clients/{id}/contacts:
    get:
      summary: Listar contactos del cliente
    post:
      summary: Agregar contacto

  /api/v1/clients/{id}/installations:
    get:
      summary: Listar instalaciones
    post:
      summary: Agregar instalacion
```

---

### 2.4 MS-004: CascadeService

#### Endpoints AJAX Existentes (Cascade)

| Metodo | Ruta | Funcion | Paso Cascade |
|--------|------|---------|--------------|
| GET | `/getJerarquia2` | SubhierarchyController | Paso 2 |
| GET | `/getJerarquia3` | SubsubhierarchyController | Paso 3 |
| GET | `/getCarton` | WorkOrderController | Paso 4 |
| GET | `/getRecubrimientoInterno` | WorkOrderController | Paso 5 |
| GET | `/getRecubrimientoExterno` | WorkOrderController | Paso 6 |
| GET | `/getColorCarton` | WorkOrderController | Paso 7 |
| GET | `/getListaCarton` | WorkOrderController | Paso 8 |
| POST | `/postVerificacionFiltro` | WorkOrderController | Validacion |

#### Contrato API REST Propuesto

```yaml
paths:
  /api/v1/cascade/hierarchy/{level}:
    get:
      summary: Obtener opciones de jerarquia por nivel
      parameters:
        - name: level
          in: path
          schema:
            type: integer
            minimum: 1
            maximum: 3
        - name: parent_id
          in: query
          description: ID del nivel padre (requerido para level > 1)

  /api/v1/cascade/cartons:
    get:
      summary: Obtener cartones filtrados
      parameters:
        - name: hierarchy_id
          in: query
        - name: plant_id
          in: query

  /api/v1/cascade/coverage/internal:
    get:
      summary: Obtener recubrimientos internos
      parameters:
        - name: carton_id
          in: query
        - name: plant_id
          in: query

  /api/v1/cascade/coverage/external:
    get:
      summary: Obtener recubrimientos externos

  /api/v1/cascade/validate:
    post:
      summary: Validar combinacion completa
      requestBody:
        content:
          application/json:
            schema:
              properties:
                hierarchy_1: { type: integer }
                hierarchy_2: { type: integer }
                hierarchy_3: { type: integer }
                carton_id: { type: integer }
                coverage_internal_id: { type: integer }
                coverage_external_id: { type: integer }
                color_id: { type: integer }
                plant_id: { type: integer }
      responses:
        200:
          content:
            application/json:
              schema:
                properties:
                  valid: { type: boolean }
                  errors: { type: array }
                  suggestions: { type: array }
```

---

### 2.5 MS-005: CotizacionService

#### Endpoints Existentes

| Metodo | Ruta | Controlador |
|--------|------|-------------|
| GET | `/cotizador/crear` | CotizacionController@create |
| GET | `/cotizador/edit/{id}` | CotizacionController@create |
| GET | `/cotizador/index` | CotizacionController@index |
| GET | `/cotizador/generar_pdf` | CotizacionController@generar_pdf |
| POST | `/cotizador/enviar_pdf` | CotizacionController@enviar_pdf |
| POST | `/cotizador/calcularDetalleCotizacion` | CotizacionController |
| POST | `/cotizador/guardarDetalleCotizacion/{id}` | DetalleCotizacionController |
| POST | `/cotizador/solicitarAprobacion/{id}` | CotizacionController |

#### Contrato API REST Propuesto

```yaml
paths:
  /api/v1/quotations:
    get:
      summary: Listar cotizaciones
    post:
      summary: Crear cotizacion

  /api/v1/quotations/{id}:
    get:
      summary: Obtener cotizacion
    put:
      summary: Actualizar cotizacion

  /api/v1/quotations/{id}/details:
    get:
      summary: Listar detalles de cotizacion
    post:
      summary: Agregar detalle

  /api/v1/quotations/{id}/calculate:
    post:
      summary: Calcular precios

  /api/v1/quotations/{id}/submit-approval:
    post:
      summary: Solicitar aprobacion

  /api/v1/quotations/{id}/approve:
    post:
      summary: Aprobar cotizacion

  /api/v1/quotations/{id}/reject:
    post:
      summary: Rechazar cotizacion

  /api/v1/quotations/{id}/pdf:
    get:
      summary: Generar PDF
```

---

### 2.6 MS-006: ReportService

#### Endpoints Existentes

| Metodo | Ruta | Controlador | Descripcion |
|--------|------|-------------|-------------|
| GET | `/reportes` | ReportController@index | Dashboard reportes |
| GET | `/reporte1` | ReportController@reporte1 | Reporte 1 |
| GET | `/reporte-gestion-carga-ot-mes` | ReportController | Carga OT/mes |
| GET | `/reporte-conversion-ot` | ReportController | Conversion OT |
| GET | `/reporte-tiempos-por-area-ot-mes` | ReportController | Tiempos por area |
| GET | `/reporte-motivos-rechazos-mes` | ReportController | Motivos rechazo |
| GET | `/reporte-anulaciones` | ReportController | Anulaciones |
| GET | `/reporte-muestras` | ReportController | Muestras |

#### Contrato API REST Propuesto

```yaml
paths:
  /api/v1/reports/ot-load-monthly:
    get:
      parameters:
        - name: year
        - name: month
      responses:
        200:
          content:
            application/json

  /api/v1/reports/ot-conversion:
    get:
      parameters:
        - name: from_date
        - name: to_date

  /api/v1/reports/time-by-area:
    get:
      parameters:
        - name: area_id
        - name: period

  /api/v1/reports/rejections:
    get:
      parameters:
        - name: period

  /api/v1/reports/export:
    post:
      summary: Exportar reporte
      requestBody:
        content:
          application/json:
            schema:
              properties:
                report_type: { type: string }
                format: { type: string, enum: [pdf, excel, csv] }
                filters: { type: object }
```

---

### 2.7 MS-007: WorkflowService

#### Endpoints Existentes (Gestion)

| Metodo | Ruta | Controlador |
|--------|------|-------------|
| GET | `/gestionarOt/{id}` | ManagementController@gestionarOt |
| GET | `/reactivarOt/{id}` | ManagementController@reactivarOt |
| GET | `/detalleLogOt/{id}` | ManagementController@detalleLogOt |
| POST | `/crear-gestion/{id}` | ManagementController@store |
| POST | `/respuesta/{id}` | ManagementController@storeRespuesta |
| GET | `/retomarOt/{id}` | ManagementController@retomarOt |

#### Contrato API REST Propuesto

```yaml
paths:
  /api/v1/workflow/work-orders/{id}/status:
    get:
      summary: Estado actual del workflow
    put:
      summary: Cambiar estado

  /api/v1/workflow/work-orders/{id}/transitions:
    get:
      summary: Transiciones disponibles

  /api/v1/workflow/work-orders/{id}/history:
    get:
      summary: Historial de estados

  /api/v1/workflow/work-orders/{id}/manage:
    post:
      summary: Registrar gestion
      requestBody:
        content:
          application/json:
            schema:
              properties:
                action: { type: string }
                comment: { type: string }
                attachments: { type: array }
```

---

### 2.8 MS-008: HierarchyService

#### Endpoints Existentes (Mantenedores)

| Prefijo | Entidad |
|---------|---------|
| `/mantenedores/users/` | Usuarios |
| `/mantenedores/hierarchies/` | Jerarquias 1 |
| `/mantenedores/subhierarchies/` | Jerarquias 2 |
| `/mantenedores/subsubhierarchies/` | Jerarquias 3 |

#### CRUD Estandar por Entidad

| Metodo | Ruta | Accion |
|--------|------|--------|
| GET | `list` | index |
| GET | `create` | create |
| POST | `guardar` | store |
| GET | `editar/{id}` | edit |
| PUT | `actualizar/{id}` | update |
| PUT | `activar/{id}` | active |
| PUT | `inactivar/{id}` | inactive |

---

### 2.9 MS-009: NotificationService

#### Endpoints Existentes

| Metodo | Ruta | Controlador |
|--------|------|-------------|
| GET | `/notificaciones` | NotificationController@index |
| PUT | `/inactivarNotificacion/{id}` | NotificationController@inactivarNotificacion |

#### Contrato API REST Propuesto

```yaml
paths:
  /api/v1/notifications:
    get:
      summary: Listar notificaciones del usuario
      parameters:
        - name: read
          in: query
          schema:
            type: boolean

  /api/v1/notifications/{id}/read:
    post:
      summary: Marcar como leida

  /api/v1/notifications/read-all:
    post:
      summary: Marcar todas como leidas
```

---

### 2.10 MS-010: CatalogService

#### Endpoints Existentes (Mantenedores)

| Prefijo | Entidad |
|---------|---------|
| `/mantenedores/cartons/` | Cartones |
| `/mantenedores/colors/` | Colores |
| `/mantenedores/styles/` | Estilos |
| `/mantenedores/product-types/` | Tipos producto |
| `/mantenedores/pallet-types/` | Tipos pallet |
| `/mantenedores/sectors/` | Sectores |
| `/mantenedores/canals/` | Canales |
| `/mantenedores/adhesivos/` | Adhesivos |
| `/mantenedores/secuencias-operacionales/` | Secuencias |
| `/mantenedores/almacenes/` | Almacenes |

---

## 3. ESQUEMAS DE DATOS (Schemas)

### 3.1 WorkOrder Schema

```yaml
components:
  schemas:
    WorkOrder:
      type: object
      properties:
        id:
          type: integer
        code:
          type: string
          example: "OT-2025-001234"
        client_id:
          type: integer
        hierarchy_1_id:
          type: integer
        hierarchy_2_id:
          type: integer
        hierarchy_3_id:
          type: integer
        carton_id:
          type: integer
        coverage_internal_id:
          type: integer
        coverage_external_id:
          type: integer
        color_id:
          type: integer
        plant_id:
          type: integer
        status:
          type: string
          enum: [draft, pending, approved, rejected, completed, cancelled]
        created_by:
          type: integer
        created_at:
          type: string
          format: datetime
        updated_at:
          type: string
          format: datetime
```

### 3.2 Client Schema

```yaml
    Client:
      type: object
      properties:
        id:
          type: integer
        name:
          type: string
        rut:
          type: string
        email:
          type: string
        phone:
          type: string
        active:
          type: boolean
        contacts:
          type: array
          items:
            $ref: '#/components/schemas/Contact'
        installations:
          type: array
          items:
            $ref: '#/components/schemas/Installation'
```

### 3.3 CascadeOption Schema

```yaml
    CascadeOption:
      type: object
      properties:
        id:
          type: integer
        name:
          type: string
        code:
          type: string
        parent_id:
          type: integer
          nullable: true
        level:
          type: integer
        active:
          type: boolean
        children_count:
          type: integer
```

---

## 4. AUTENTICACION Y SEGURIDAD

### 4.1 Esquema de Seguridad Propuesto

```yaml
components:
  securitySchemes:
    bearerAuth:
      type: http
      scheme: bearer
      bearerFormat: JWT

    azureOAuth:
      type: oauth2
      flows:
        authorizationCode:
          authorizationUrl: https://login.microsoftonline.com/{tenant}/oauth2/v2.0/authorize
          tokenUrl: https://login.microsoftonline.com/{tenant}/oauth2/v2.0/token
          scopes:
            openid: OpenID Connect
            profile: User profile
            email: Email address
```

### 4.2 Roles y Permisos Existentes

| Rol | Permisos OT | Permisos Cotizador |
|-----|-------------|-------------------|
| Administrador | Full | Full |
| Super Administrador | Full | Full |
| Jefe de Ventas | CRUD + Aprobar | CRUD + Aprobar |
| Vendedor | CRUD | CRUD |
| Vendedor Externo | CRUD | CRUD (externo) |
| Dibujante Tecnico | CRUD | Read |
| Jefe de Diseno Estructural | CRUD + Aprobar | Read |
| Jefe de Diseno Grafico | CRUD | Read |
| Disenador | CRUD | Read |

---

## 5. RESUMEN DE ENDPOINTS POR SERVICIO

| Servicio | Endpoints Web | Endpoints AJAX | API Propuestos |
|----------|---------------|----------------|----------------|
| MS-001: OTService | 25+ | 15+ | 8 |
| MS-002: AuthService | 10 | 0 | 5 |
| MS-003: ClientService | 12 | 8 | 6 |
| MS-004: CascadeService | 0 | 10 | 5 |
| MS-005: CotizacionService | 20+ | 12+ | 10 |
| MS-006: ReportService | 20+ | 0 | 6 |
| MS-007: WorkflowService | 8 | 2 | 5 |
| MS-008: HierarchyService | 28 | 2 | 4 |
| MS-009: NotificationService | 2 | 0 | 3 |
| MS-010: CatalogService | 70+ | 0 | 4 |

---

## 6. SIGUIENTE PASO

**PASO 5.3**: Terminos - Definir terminos ancla para microservicios.

---

**Documento generado**: 2025-12-17
**Actualizado**: 2025-12-19
**Version**: 2.0 (Migrado a Monitor One)

### Historial de Cambios

| Version | Fecha | Cambios |
|---------|-------|---------|
| 1.0 | 2025-12-17 | Version inicial (propuestas API) |
| 2.0 | 2025-12-19 | Agregados endpoints FastAPI implementados |
