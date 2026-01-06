# FASE 6: Frontend React - Formulario Cascade

**ID**: `PASO-06.00-V2`
**Fecha**: 2025-12-19
**Estado**: COMPLETADO
**Actualizado**: 2025-12-19 - Correcciones de integracion

---

## Resumen

Implementacion del frontend React para el microservicio MS-004 CascadeService, siguiendo los estandares visuales Monitor One de Tecnoandina.

| Aspecto | Valor |
|---------|-------|
| Framework | React 18 + TypeScript |
| Estilos | styled-components |
| Design System | Monitor One (FASE_5_5B) |
| API Backend | FastAPI (puerto 8001) |
| Ubicacion | `msw-envases-ot/frontend/` |

### Documentos Relacionados

- **[FASE_5_5B](FASE_5_5B_ESTANDARES_MONITOR_ONE.md)**: Estandares UI Monitor One
- **[FASE_5_6](FASE_5_6_IMPLEMENTACION_MICROSERVICIO.md)**: Backend FastAPI
- **[FASE_5_2](FASE_5_2_ESPECIFICACION_API.md)**: Endpoints API

---

## 1. Stack Tecnologico

| Componente | Tecnologia | Version |
|------------|------------|---------|
| Framework | React | 18.x |
| Lenguaje | TypeScript | 5.x |
| Estilos | styled-components | 6.x |
| HTTP Client | Axios | 1.x |
| State Management | React Query | 5.x |
| Build Tool | Vite | 5.x |
| Servidor Dev | Vite Dev Server | 5.x |

---

## 2. Estructura del Frontend

```
msw-envases-ot/
├── frontend/
│   ├── src/
│   │   ├── components/
│   │   │   ├── CascadeForm/
│   │   │   │   ├── CascadeForm.tsx      # Conectado a API real
│   │   │   │   ├── CascadeForm.styles.ts
│   │   │   │   ├── CascadeSelect.tsx
│   │   │   │   └── index.ts
│   │   │   └── common/
│   │   │       ├── Button/
│   │   │       ├── Card/
│   │   │       ├── Select/
│   │   │       └── Spinner/
│   │   ├── pages/
│   │   │   ├── Login/
│   │   │   │   ├── Login.tsx            # Pagina de login
│   │   │   │   └── index.ts
│   │   │   ├── Auth/                    # NUEVO: Recuperacion contrasena
│   │   │   │   ├── index.ts             # Exportaciones
│   │   │   │   ├── ForgotPassword.tsx   # Solicitar recuperacion
│   │   │   │   └── ResetPassword.tsx    # Nueva contrasena
│   │   │   ├── WorkOrders/              # Dashboard OTs
│   │   │   │   ├── WorkOrdersDashboard.tsx   # Lista con filtros
│   │   │   │   └── WorkOrdersDashboard.css   # Estilos Monitor One
│   │   │   └── index.ts
│   │   ├── hooks/
│   │   │   └── useCascadeRules.ts       # Incluye useFormOptions
│   │   ├── services/
│   │   │   └── api.ts                   # Incluye workOrdersApi
│   │   ├── theme/
│   │   │   ├── colors.ts
│   │   │   ├── typography.ts
│   │   │   ├── spacing.ts
│   │   │   └── index.ts
│   │   ├── types/
│   │   │   └── cascade.ts               # Incluye FormOptionsResponse
│   │   ├── App.tsx                      # Con navegacion por tabs
│   │   ├── main.tsx
│   │   └── vite-env.d.ts
│   ├── public/
│   ├── .env                             # Variables de entorno
│   ├── .env.example
│   ├── package.json
│   ├── tsconfig.json
│   ├── vite.config.ts
│   └── Dockerfile
├── src/
│   └── app/
│       └── routers/
│           ├── __init__.py              # Incluye work_orders_router
│           ├── auth.py                  # Autenticacion MySQL
│           ├── work_orders.py           # NUEVO: API Dashboard OTs
│           ├── cascade_rules.py
│           ├── cascade_combinations.py
│           └── form_options.py
├── docker-compose.yaml
└── ...
```

---

## 3. Tema Monitor One (styled-components)

### 3.1 Colores

```typescript
// src/theme/colors.ts
export const colors = {
  // Principales
  primary: '#003A81',
  secondary: '#EC7126',
  accent: '#05C1CA',
  cardHeader: '#01214d',
  corporate: '#6D7883',

  // Fondos
  bgWhite: '#FFFFFF',
  bgLight: '#F2F2F2',
  sidebarBg: '#1A1A2E',
  bgBlueLight: '#D1E3F8',

  // Estados
  success: '#28A745',
  warning: '#FFC107',
  danger: '#DC3545',
  info: '#17A2B8',
  active: '#00E676',
  disabled: '#9E9E9E',

  // Texto
  textPrimary: '#212529',
  textSecondary: '#6C757D',
  textMuted: '#9E9E9E',
  textWhite: '#FFFFFF',
  link: '#003A81',
  linkHover: '#002654',
};
```

### 3.2 Tipografia

```typescript
// src/theme/typography.ts
export const typography = {
  fontFamily: "'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",

  weights: {
    light: 300,
    regular: 400,
    medium: 500,
    semibold: 600,
    bold: 700,
  },

  sizes: {
    h1: '2rem',
    h2: '1.5rem',
    h3: '1.25rem',
    h4: '1rem',
    body: '0.875rem',
    small: '0.75rem',
    tiny: '0.625rem',
  },
};
```

### 3.3 Espaciado

```typescript
// src/theme/spacing.ts
export const spacing = {
  xs: '0.25rem',
  sm: '0.5rem',
  md: '1rem',
  lg: '1.5rem',
  xl: '2rem',
};

export const radius = {
  sm: '4px',
  md: '8px',
  lg: '12px',
  full: '50%',
};

export const shadows = {
  sm: '0 1px 3px rgba(0, 0, 0, 0.08)',
  md: '0 2px 8px rgba(0, 0, 0, 0.1)',
  lg: '0 4px 16px rgba(0, 0, 0, 0.12)',
  hover: '0 4px 12px rgba(0, 58, 129, 0.15)',
};
```

---

## 4. Componente CascadeForm

### 4.1 Especificacion

El componente CascadeForm implementa la secuencia de 8 campos dependientes:

```
[1] Tipo Producto → [2] Impresion → [3] FSC → [4] Cinta
       ↓                                          ↓
[5] Recub. Interno → [6] Recub. Externo → [7] Color Carton → [8] Carton
```

### 4.2 Props

```typescript
interface CascadeFormProps {
  onComplete: (data: CascadeFormData) => void;
  initialValues?: Partial<CascadeFormData>;
  disabled?: boolean;
}

interface CascadeFormData {
  productTypeId: number | null;
  impresion: string | null;
  fsc: string | null;
  cinta: string | null;
  coverageInternalId: number | null;
  coverageExternalId: number | null;
  cartoncColor: string | null;
  cartonId: number | null;
}
```

### 4.3 Comportamiento

| Campo | Trigger | Accion |
|-------|---------|--------|
| productTypeId | onChange | Habilita `impresion`, resetea campos 2-8 |
| impresion | onChange | Habilita `fsc`, valida combinacion, resetea 3-8 |
| fsc | onChange | Habilita `cinta`, resetea 4-8 |
| cinta | onChange | Habilita `coverageInternalId`, resetea 5-8 |
| coverageInternalId | onChange | Habilita `coverageExternalId`, resetea 6-8 |
| coverageExternalId | onChange | Habilita `plantaId`, resetea 7-8 |
| cartonColor | onChange | Habilita `cartonId`, resetea 8 |
| cartonId | onChange | Formulario completo |

---

## 5. Servicios API

### 5.1 Cliente Axios (ACTUALIZADO 2025-12-19)

```typescript
// src/services/api.ts
import axios from 'axios';

// URLs separadas para endpoints versionados y raiz
const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8001/api/v1';
const API_ROOT_URL = import.meta.env.VITE_API_ROOT_URL || 'http://localhost:8001';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: { 'Content-Type': 'application/json' },
});

// Instancia para endpoints de raiz (health check)
const rootApi = axios.create({
  baseURL: API_ROOT_URL,
  headers: { 'Content-Type': 'application/json' },
});

export const cascadeApi = {
  getRules: () => api.get('/cascade-rules/'),
  getRulesByTrigger: (field: string) => api.get(`/cascade-rules/trigger/${field}`),
  validateCombination: (params) => api.get('/cascade-combinations/validate/', { params }),

  // CORREGIDO: Health check usa rootApi (endpoint en raiz, no bajo /api/v1)
  healthCheck: () => rootApi.get('/health'),

  // NUEVO: Opciones del formulario
  getFormOptions: () => api.get('/form-options/'),
};
```

### 5.2 Variables de Entorno (.env)

```bash
# .env
VITE_API_URL=http://localhost:8001/api/v1
VITE_API_ROOT_URL=http://localhost:8001
VITE_APP_NAME=INVEB Cascade Form
VITE_ENV=development
```

### 5.3 Hooks React Query (ACTUALIZADO 2025-12-19)

```typescript
// src/hooks/useCascadeRules.ts
import { useQuery } from '@tanstack/react-query';
import { cascadeApi } from '../services/api';

export function useCascadeRules() {
  return useQuery({
    queryKey: ['cascade-rules'],
    queryFn: cascadeApi.getRules,
    staleTime: 5 * 60 * 1000, // 5 minutos
  });
}

export function useHealthCheck() {
  return useQuery({
    queryKey: ['health'],
    queryFn: cascadeApi.healthCheck,
    staleTime: 30 * 1000, // 30 segundos
  });
}

// NUEVO: Hook para opciones del formulario
export function useFormOptions() {
  return useQuery({
    queryKey: ['form-options'],
    queryFn: cascadeApi.getFormOptions,
    staleTime: 10 * 60 * 1000, // 10 minutos
  });
}
```

---

## 6. Ejecucion

### 6.1 Desarrollo Local

```bash
cd msw-envases-ot/frontend

# Instalar dependencias
npm install

# Ejecutar en modo desarrollo
npm run dev

# Frontend: http://localhost:5173
# API: http://localhost:8001
```

### 6.2 Docker Compose

```bash
cd msw-envases-ot

# Levantar todos los servicios (API + DB + Frontend)
docker-compose up -d

# Frontend: http://localhost:3000
# API: http://localhost:8001
# Swagger: http://localhost:8001/docs
```

---

## 7. Diagrama de Arquitectura

```
┌─────────────────────────────────────────────────────────────────────────┐
│                    ARQUITECTURA FASE 6                                   │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  Browser                                                                 │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │                    React App (Vite)                              │   │
│  │                    http://localhost:3000                         │   │
│  │                                                                   │   │
│  │  ┌─────────────────┐  ┌─────────────────┐                       │   │
│  │  │   CascadeForm   │  │   React Query   │                       │   │
│  │  │   Component     │←→│   Cache         │                       │   │
│  │  └─────────────────┘  └────────┬────────┘                       │   │
│  │                                 │                                 │   │
│  │  styled-components (Monitor One Theme)                           │   │
│  └─────────────────────────────────┼───────────────────────────────┘   │
│                                    │ HTTP/REST                          │
│                                    ▼                                    │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │                    FastAPI Backend                               │   │
│  │                    http://localhost:8001                         │   │
│  │                                                                   │   │
│  │  /api/v1/cascade-rules/                                          │   │
│  │  /api/v1/cascade-combinations/validate/                          │   │
│  └─────────────────────────────────┬───────────────────────────────┘   │
│                                    │                                    │
│                                    ▼                                    │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │                    PostgreSQL 15                                 │   │
│  │                    localhost:5433                                │   │
│  └─────────────────────────────────────────────────────────────────┘   │
│                                                                          │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 8. Correcciones Implementadas (2025-12-19)

| Problema | Solucion | Archivo |
|----------|----------|---------|
| Health check fallaba | Separar rootApi para endpoints raiz | `api.ts` |
| Sin variables entorno | Crear .env con URLs configuradas | `.env` |
| Datos mock en formulario | Conectar a API `/form-options/` | `CascadeForm.tsx` |
| Sin pagina login | Crear Login component con Monitor One | `pages/Login/` |
| Sin navegacion | Agregar estado de usuario en App | `App.tsx` |

## 9. Dashboard de Ordenes de Trabajo (2025-12-19)

### 9.1 Descripcion

Implementacion del dashboard completo de OTs, replicando la funcionalidad de Laravel `work-orders/index.blade.php` con estilo Monitor One.

### 9.2 Backend - Router work_orders.py

**Archivo**: `src/app/routers/work_orders.py`

```python
# Endpoints implementados
GET /api/v1/work-orders/                 # Lista paginada con 14+ filtros
GET /api/v1/work-orders/filter-options   # Opciones para filtros
GET /api/v1/work-orders/{ot_id}          # Detalle de OT
```

**Conexion a MySQL Laravel:**
```python
connection = pymysql.connect(
    host=settings.LARAVEL_MYSQL_HOST,
    port=settings.LARAVEL_MYSQL_PORT,
    user=settings.LARAVEL_MYSQL_USER,
    password=settings.LARAVEL_MYSQL_PASSWORD,
    database=settings.LARAVEL_MYSQL_DATABASE,
)
```

### 9.3 Frontend - WorkOrdersDashboard

**Archivos creados:**
- `frontend/src/pages/WorkOrders/WorkOrdersDashboard.tsx`
- `frontend/src/pages/WorkOrders/WorkOrdersDashboard.css`

**Tipos TypeScript (api.ts):**
```typescript
interface WorkOrderListItem {
  id: number;
  created_at: string;
  client_name: string;
  descripcion: string;
  canal: string | null;
  item_tipo: string | null;
  estado: string;
  estado_abrev: string;
  creador_nombre: string;
  tiempo_total: number | null;
  tiempo_venta: number | null;
  tiempo_desarrollo: number | null;
  tiempo_muestra: number | null;
  tiempo_diseno: number | null;
  tiempo_externo: number | null;
  tiempo_precatalogacion: number | null;
  tiempo_catalogacion: number | null;
}

interface WorkOrderFilters {
  page?: number;
  page_size?: number;
  id_ot?: number;
  date_desde?: string;
  date_hasta?: string;
  client_id?: number[];
  estado_id?: number[];
  area_id?: number[];
  canal_id?: number[];
  vendedor_id?: number[];
  cad?: string;
  carton?: string;
  material?: string;
  descripcion?: string;
  planta_id?: number[];
  tipo_solicitud?: number[];
}
```

### 9.4 Navegacion con Tabs

**App.tsx actualizado:**
```typescript
type PageType = 'dashboard' | 'crear-ot' | 'cascade-form' | 'gestionar-ot' | 'notificaciones';

// Tabs de navegacion
<NavTabs>
  <NavTab $active={currentPage === 'dashboard'}>Dashboard OTs</NavTab>
  <NavTab $active={currentPage === 'crear-ot'}>Crear OT</NavTab>
  <NavTab $active={currentPage === 'cascade-form'}>Formulario Cascade</NavTab>
  <NavTab $active={currentPage === 'notificaciones'}>Notificaciones</NavTab>
</NavTabs>
```

### 9.5 Tablas MySQL Consultadas

| Tabla | Join | Datos |
|-------|------|-------|
| work_orders | Base | Datos OT |
| clients | LEFT JOIN | nombre_sap |
| users | LEFT JOIN | creador nombre |
| canals | LEFT JOIN | canal nombre |
| product_types | LEFT JOIN | item tipo |
| managements | Subquery | estado actual |
| states | LEFT JOIN | estado nombre/abrev |
| cads | LEFT JOIN | codigo CAD |
| cartons | LEFT JOIN | codigo carton |
| materials | LEFT JOIN | codigo material |
| work_spaces | LEFT JOIN | area actual |

### 9.6 Filtros Implementados

| Filtro | Tipo Input | Campo Query |
|--------|------------|-------------|
| ID OT | number | wo.id = |
| Fecha Desde | date | wo.created_at >= |
| Fecha Hasta | date | wo.created_at <= |
| Cliente | multi-select | wo.client_id IN |
| Estado | multi-select | m.state_id IN |
| Area | multi-select | wo.current_area_id IN |
| Canal | multi-select | wo.canal_id IN |
| Vendedor | multi-select | wo.creador_id IN |
| CAD | text | cad.codigo LIKE |
| Carton | text | cart.codigo LIKE |
| Material | text | mat.codigo LIKE |
| Descripcion | text | wo.descripcion LIKE |
| Planta | multi-select | wo.planta_id IN |
| Tipo Solicitud | multi-select | wo.tipo_solicitud IN |

---

## 10. Crear Orden de Trabajo (2025-12-19)

### 10.1 Descripcion

Implementacion del formulario completo para crear ordenes de trabajo, integrando el CascadeForm existente con las secciones adicionales requeridas por el workflow de INVEB.

### 10.2 Backend - Endpoint POST

**Archivo**: `src/app/routers/work_orders.py`

```python
# Nuevo endpoint
POST /api/v1/work-orders/                # Crear nueva OT

# Schema de creacion
class WorkOrderCreate(BaseModel):
    # Datos Comerciales (requeridos)
    client_id: int
    descripcion: str  # max 40 chars
    tipo_solicitud: int  # 1-7
    canal_id: int

    # Opcionales: contacto, solicitante, cascade, medidas, etc.
    ...

class WorkOrderCreateResponse(BaseModel):
    id: int
    message: str
```

**Flujo de creacion:**
1. Validar campos requeridos
2. INSERT en `work_orders` con `creador_id` del token JWT
3. INSERT en `managements` (estado inicial = 1)
4. INSERT en `user_work_order` (asignar usuario)
5. Retornar ID de OT creada

### 10.3 Frontend - CreateWorkOrder.tsx

**Archivo**: `frontend/src/pages/WorkOrders/CreateWorkOrder.tsx`

**Secciones del formulario:**

| Seccion | Campos | Tipo |
|---------|--------|------|
| 1. Datos Comerciales | cliente, descripcion, tipo_solicitud, canal, contacto | requeridos/opcionales |
| 2. Solicita | analisis, plano, muestra, boceto, nuevo_material, etc. | checkboxes |
| 3. Caracteristicas | Integra CascadeForm completo | cascade 8 pasos |
| 4. Medidas | interno/externo largo, ancho, alto | numeros (mm) |
| 5. Terminaciones | proceso, numero_colores, planta | select |
| 6. Desarrollo | peso_contenido_caja, cantidad, observacion | mixto |

### 10.4 Hook useWorkOrders.ts

**Archivo**: `frontend/src/hooks/useWorkOrders.ts`

```typescript
// Hooks implementados
useWorkOrdersList(filters)      // Lista OTs con paginacion
useWorkOrderFilterOptions()     // Opciones para filtros
useWorkOrderDetail(id)          // Detalle de OT
useCreateWorkOrder()            // Mutation para crear OT
```

### 10.5 Tipos TypeScript

```typescript
interface WorkOrderCreateData {
  // Datos Comerciales (requeridos)
  client_id: number;
  descripcion: string;
  tipo_solicitud: number;
  canal_id: number;

  // Opcionales
  nombre_contacto?: string;
  email_contacto?: string;
  telefono_contacto?: string;
  // ...cascade fields, medidas, terminaciones, etc.
}

interface WorkOrderCreateResponse {
  id: number;
  message: string;
}
```

### 10.6 Integracion con CascadeForm

El formulario de creacion integra el CascadeForm existente como seccion 3:

```typescript
// Cuando CascadeForm completa los 8 pasos
const handleCascadeComplete = (data: CascadeFormData) => {
  setFormState(prev => ({ ...prev, cascadeData: data }));
  setCascadeCompleted(true);
};

// Al enviar formulario
const submitData: WorkOrderCreateData = {
  ...datosComerciales,
  product_type_id: formState.cascadeData?.productTypeId,
  impresion: formState.cascadeData?.impresion,
  fsc: formState.cascadeData?.fsc,
  // ...resto de campos cascade
};
```

---

## 11. Editar Orden de Trabajo (2025-12-19)

### 11.1 Descripcion

Implementacion del formulario de edicion de ordenes de trabajo existentes, permitiendo modificar todos los campos de una OT.

### 11.2 Backend - Endpoint PUT

**Archivo**: `src/app/routers/work_orders.py`

```python
# Nuevo endpoint
PUT /api/v1/work-orders/{ot_id}    # Actualizar OT existente

# Schema de actualizacion (todos los campos opcionales)
class WorkOrderUpdate(BaseModel):
    client_id: Optional[int] = None
    descripcion: Optional[str] = None
    tipo_solicitud: Optional[int] = None
    # ... resto de campos opcionales
```

**Flujo de actualizacion:**
1. Verificar que la OT existe y esta activa
2. Construir UPDATE dinamico solo con campos enviados
3. Actualizar `updated_at` automaticamente
4. Retornar confirmacion

### 11.3 Frontend - EditWorkOrder.tsx

**Archivo**: `frontend/src/pages/WorkOrders/EditWorkOrder.tsx`

**Caracteristicas:**
- Carga datos existentes de la OT via `useWorkOrderDetail(id)`
- Pre-llena todos los campos del formulario
- Muestra indicador de carga mientras obtiene datos
- Maneja errores de OT no encontrada
- Usa mismo layout de secciones que CreateWorkOrder

### 11.4 Navegacion

**App.tsx actualizado:**
```typescript
// Nuevo estado para ID de OT en edicion
const [editingOtId, setEditingOtId] = useState<number | null>(null);

// Navigate con ID opcional
const handleNavigate = (page: PageType, otId?: number) => {
  setCurrentPage(page);
  if (page === 'editar-ot' && otId) {
    setEditingOtId(otId);
  }
};

// Render condicional
case 'editar-ot':
  return editingOtId ? <EditWorkOrder otId={editingOtId} onNavigate={handleNavigate} /> : null;
```

**Dashboard - Boton de edicion:**
```typescript
<button onClick={() => handleEditOT(ot.id)} title="Editar">
  ✏️
</button>
```

### 11.5 Hooks useWorkOrders.ts

```typescript
// Hook para actualizar OT
export function useUpdateWorkOrder() {
  return useMutation({
    mutationFn: ({ id, data }) => workOrdersApi.update(id, data),
    onSuccess: () => {
      queryClient.invalidateQueries(['workOrders']);
      queryClient.invalidateQueries(['workOrder', id]);
    },
  });
}
```

---

## 12. Gestionar Orden de Trabajo - Workflow (2025-12-19)

### 12.1 Descripcion

Implementacion del sistema de gestion de workflow para ordenes de trabajo, permitiendo transicionar OTs entre areas y estados segun el proceso de INVEB.

### 12.2 Backend - Endpoints de Workflow

**Archivo**: `src/app/routers/work_orders.py`

```python
# Endpoints de gestion
GET  /api/v1/work-orders/{ot_id}/management       # Historial de gestion
GET  /api/v1/work-orders/{ot_id}/workflow-options # Opciones de transicion
POST /api/v1/work-orders/{ot_id}/transition       # Realizar transicion

# Schemas
class ManagementHistoryItem(BaseModel):
    id: int
    work_space: str
    state: str
    user_name: str
    observation: Optional[str]
    created_at: str

class ManagementHistoryResponse(BaseModel):
    ot_id: int
    current_area: str
    current_state: str
    history: List[ManagementHistoryItem]

class WorkflowOptions(BaseModel):
    areas: List[Dict[str, Any]]    # {id, nombre}
    states: List[Dict[str, Any]]   # {id, nombre, abreviatura}

class TransitionRequest(BaseModel):
    work_space_id: int
    state_id: int
    observation: Optional[str] = None

class TransitionResponse(BaseModel):
    id: int
    message: str
    new_area: str
    new_state: str
```

**Flujo de transicion:**
1. Validar que la OT existe
2. INSERT en tabla `managements` con nuevo work_space_id, state_id, user_id
3. UPDATE en `work_orders.current_area_id` al nuevo work_space
4. Retornar confirmacion con nueva area y estado

### 12.3 Frontend - ManageWorkOrder.tsx

**Archivo**: `frontend/src/pages/WorkOrders/ManageWorkOrder.tsx`

**Componentes visuales:**
- **Panel de Estado Actual**: Muestra area y estado actuales con badges
- **Formulario de Transicion**: Selects para nueva area y estado, campo de observacion
- **Historial de Gestion**: Timeline con todas las transiciones anteriores

**Caracteristicas:**
- Carga datos via `useManagementHistory` y `useWorkflowOptions`
- Transiciones via `useTransitionWorkOrder` mutation
- Muestra info basica de la OT (cliente, descripcion, creador)
- Alertas de exito/error para feedback de usuario
- Styled-components con tema Monitor One

### 12.4 Navegacion

**App.tsx actualizado:**
```typescript
// Nuevo estado para ID de OT en gestion
const [managingOtId, setManagingOtId] = useState<number | null>(null);

// Navigate con ID para gestion
const handleNavigate = (page: PageType, otId?: number) => {
  setCurrentPage(page);
  if (page === 'gestionar-ot' && otId) {
    setManagingOtId(otId);
  } else if (page === 'editar-ot' && otId) {
    setEditingOtId(otId);
  }
};

// Render condicional
case 'gestionar-ot':
  return managingOtId ? <ManageWorkOrder otId={managingOtId} onNavigate={handleNavigate} /> : null;
```

**Dashboard - Boton de gestion:**
```typescript
<button onClick={() => handleViewOT(ot.id)} title="Gestionar">
  🔍
</button>
```

### 12.5 Hooks useWorkOrders.ts

```typescript
// Hooks para gestion de workflow
export function useManagementHistory(otId: number) {
  return useQuery({
    queryKey: ['managementHistory', otId],
    queryFn: () => workOrdersApi.getManagementHistory(otId),
  });
}

export function useWorkflowOptions(otId: number) {
  return useQuery({
    queryKey: ['workflowOptions', otId],
    queryFn: () => workOrdersApi.getWorkflowOptions(otId),
  });
}

export function useTransitionWorkOrder() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: ({ id, data }) => workOrdersApi.transition(id, data),
    onSuccess: (_, { id }) => {
      queryClient.invalidateQueries(['managementHistory', id]);
      queryClient.invalidateQueries(['workOrders']);
    },
  });
}
```

### 12.6 Tabla managements

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id | int | PK autoincrement |
| work_order_id | int | FK a work_orders |
| work_space_id | int | FK a work_spaces (area) |
| state_id | int | FK a states |
| user_id | int | FK a users (quien hizo transicion) |
| observation | text | Comentario opcional |
| created_at | datetime | Timestamp de transicion |

---

## 13. Notificaciones de OT (2025-12-19)

### 13.1 Descripcion

Implementacion del sistema de notificaciones para ordenes de trabajo, permitiendo a usuarios ver y gestionar notificaciones relacionadas a OTs asignadas o generadas por otros usuarios.

### 13.2 Backend - Router notifications.py

**Archivo**: `src/app/routers/notifications.py`

```python
# Endpoints implementados
GET  /api/v1/notifications/           # Lista notificaciones paginadas
PUT  /api/v1/notifications/{id}/read  # Marcar como leida
POST /api/v1/notifications/           # Crear nueva notificacion
GET  /api/v1/notifications/count      # Conteo de notificaciones activas

# Schemas
class NotificationItem(BaseModel):
    id: int
    work_order_id: int
    motivo: str
    observacion: Optional[str]
    created_at: str
    generador_nombre: str
    ot_descripcion: str
    client_name: str
    item_tipo: Optional[str]
    estado: str
    area: Optional[str]
    dias_total: Optional[float]

class NotificationListResponse(BaseModel):
    items: List[NotificationItem]
    total: int
    page: int
    page_size: int
    total_pages: int

class CreateNotificationRequest(BaseModel):
    work_order_id: int
    user_id: Optional[int] = None
    motivo: str
    observacion: Optional[str] = None
```

### 13.3 Frontend - Notifications.tsx

**Archivo**: `frontend/src/pages/WorkOrders/Notifications.tsx`

**Funcionalidades:**
- Lista paginada de notificaciones activas del usuario
- Datos mostrados: OT#, dias, cliente, descripcion, estado, area, generador, motivo, observacion, fecha
- Badge de dias con colores segun urgencia (verde < 2, amarillo 2-5, rojo > 5)
- Boton "Gestionar" para ir a ManageWorkOrder
- Boton "Leido" para marcar notificacion como inactiva
- Estado vacio con icono cuando no hay notificaciones
- Paginacion cuando hay mas de 20 items

### 13.4 API Frontend

**Archivo**: `frontend/src/services/api.ts`

```typescript
// Nuevos tipos
interface NotificationItem { ... }
interface NotificationListResponse { ... }
interface MarkReadResponse { id: number; message: string; }
interface CreateNotificationRequest { ... }

// Nuevos metodos
export const notificationsApi = {
  list: async (page, pageSize) => ...,
  markAsRead: async (id) => ...,
  create: async (data) => ...,
  getCount: async () => ...,
};
```

### 13.5 Hooks useWorkOrders.ts

```typescript
// Hooks de notificaciones agregados
useNotificationsList(page, pageSize)   // Lista paginada
useNotificationsCount()                // Conteo con refresh cada minuto
useMarkNotificationRead()              // Mutation para marcar leida
useCreateNotification()                // Mutation para crear
```

### 13.6 Tabla notifications (MySQL Laravel)

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id | bigint | PK autoincrement |
| work_order_id | int | FK a work_orders |
| user_id | int | FK a users (destinatario) |
| generador_id | int | FK a users (creador) |
| motivo | varchar | Razon de la notificacion |
| observacion | varchar | Detalle opcional |
| active | tinyint | 1=activa, 0=leida |
| created_at | datetime | Timestamp creacion |
| updated_at | datetime | Timestamp actualizacion |

---

## 14. Mantenedores (FASE 6.7) - COMPLETADO

### 14.1 Descripcion

Migracion completa de los mantenedores del sistema Laravel monolitico al microservicio React. Se implemento un sistema generico que soporta 55 tablas dinamicamente, mas 3 pantallas personalizadas para entidades complejas.

### 14.2 Arquitectura del Sistema Generico

**Backend: `msw-envases-ot/src/app/routers/mantenedores/generic.py`**

El router generico maneja CRUD para 55 tablas mediante configuracion:

```python
TABLA_CONFIG: Dict[str, Dict[str, Any]] = {
    "tabla_key": {
        "table": "nombre_tabla_mysql",
        "nombre_field": "campo_para_display",
        "columns": ["id", "col1", "col2", ...],
        "has_active": True/False,
        "display_name": "Nombre Visible"
    },
    # ... 55 tablas configuradas
}
```

**Endpoints genericos:**
```
GET    /api/v1/mantenedores/generic/tablas              # Lista tablas disponibles
GET    /api/v1/mantenedores/generic/{tabla_key}         # Lista items paginados
GET    /api/v1/mantenedores/generic/{tabla_key}/{id}    # Detalle item
POST   /api/v1/mantenedores/generic/{tabla_key}/        # Crear item
PUT    /api/v1/mantenedores/generic/{tabla_key}/{id}    # Actualizar item
PUT    /api/v1/mantenedores/generic/{tabla_key}/{id}/activate    # Activar
PUT    /api/v1/mantenedores/generic/{tabla_key}/{id}/deactivate  # Desactivar
```

### 14.3 Tablas Soportadas (55 total)

#### Grupo 1: OTs y Produccion (12 tablas)
| Key | Tabla MySQL | Display Name | has_active |
|-----|-------------|--------------|------------|
| canales | canals | Canales | No |
| organizaciones_ventas | organizaciones_ventas | Organizaciones de Venta | Si |
| tipos_cintas | tipos_cintas | Tipos de Cintas | Si |
| colores | colors | Colores | Si |
| estilos | styles | Estilos | Si |
| tipo_productos | product_types | Tipos de Productos | Si |
| almacenes | almacenes | Almacenes | Si |
| tipo_palet | pallet_types | Tipos de Palet | Si |
| matrices | matrices | Matrices | Si |
| sectores | sectors | Sectores | Si |
| cartones | cartons | Cartones | Si |
| materiales | materials | Materiales | Si |

#### Grupo 2: Configuracion Produccion (8 tablas)
| Key | Tabla MySQL | Display Name | has_active |
|-----|-------------|--------------|------------|
| grupo_imputacion_material | grupo_imputacion_materiales | Grupo Imputacion Material | Si |
| secuencias_operacionales | secuencias_operacionales | Secuencias Operacionales | Si |
| rechazo_conjunto | rechazo_conjunto | Rechazo Conjunto | Si |
| tiempo_tratamiento | tiempo_tratamiento | Tiempo de Tratamiento | Si |
| grupo_materiales_1 | grupo_materiales_1 | Grupo de Materiales 1 | Si |
| grupo_materiales_2 | grupo_materiales_2 | Grupo de Materiales 2 | Si |
| grupo_plantas | grupo_plantas | Grupo de Plantas | Si |
| adhesivos | adhesivos | Adhesivos | Si |

#### Grupo 3: Comercial y Clientes (4 tablas)
| Key | Tabla MySQL | Display Name | has_active |
|-----|-------------|--------------|------------|
| cebes | cebes | CEBES | Si |
| clasificaciones_clientes | clasificacion_clientes | Clasificaciones de Clientes | Si |
| mercados | mercados | Mercados | Si |
| rubros | rubros | Rubros | No |

#### Grupo 4: Procesos del Cotizador (6 tablas)
| Key | Tabla MySQL | Display Name | has_active |
|-----|-------------|--------------|------------|
| armados | armados | Armados | Si |
| procesos | processes | Procesos | Si |
| pegados | pegados | Pegados | Si |
| envases | envases | Envases | Si |
| rayados | rayados | Rayados | Si |
| maquila_servicios | maquila_servicios | Maquila Servicios | Si |

#### Grupo 5: Papeles y Cartones (4 tablas)
| Key | Tabla MySQL | Display Name | has_active |
|-----|-------------|--------------|------------|
| papeles | papers | Papeles | No |
| cardboards | cardboards | Cardboards (Cotizador) | Si |
| carton_esquineros | carton_esquineros | Carton Esquineros | Si |
| tipo_ondas | tipo_ondas | Tipo de Ondas | No |

#### Grupo 6: Plantas y Configuracion (2 tablas)
| Key | Tabla MySQL | Display Name | has_active |
|-----|-------------|--------------|------------|
| plantas | plantas | Plantas | No |
| factores_ondas | factores_ondas | Factores de Ondas | No |

#### Grupo 7: Factores y Calculos (3 tablas)
| Key | Tabla MySQL | Display Name | has_active |
|-----|-------------|--------------|------------|
| factores_desarrollos | factores_desarrollos | Factores de Desarrollos | No |
| factores_seguridads | factores_seguridads | Factores de Seguridad | No |
| areahcs | areahcs | Area HCs | No |

#### Grupo 8: Consumos (3 tablas)
| Key | Tabla MySQL | Display Name | has_active |
|-----|-------------|--------------|------------|
| consumo_adhesivos | consumo_adhesivos | Consumo Adhesivos | No |
| consumo_energias | consumo_energias | Consumo Energias | No |
| consumo_adhesivo_pegados | consumo_adhesivo_pegados | Consumo Adhesivo Pegados | No |

#### Grupo 9: Mermas (2 tablas)
| Key | Tabla MySQL | Display Name | has_active |
|-----|-------------|--------------|------------|
| merma_corrugadoras | merma_corrugadoras | Merma Corrugadoras | No |
| merma_convertidoras | merma_convertidoras | Merma Convertidoras | No |

#### Grupo 10: Logistica (2 tablas)
| Key | Tabla MySQL | Display Name | has_active |
|-----|-------------|--------------|------------|
| ciudades_fletes | ciudades_fletes | Ciudades Fletes | No |
| fletes | fletes | Fletes | No |

#### Grupo 11: Tarifario (3 tablas)
| Key | Tabla MySQL | Display Name | has_active |
|-----|-------------|--------------|------------|
| tarifario | tarifario | Tarifario | No |
| tarifario_margens | tarifario_margens | Tarifario Margenes | No |
| variables_cotizador | variables_cotizadors | Variables Cotizador | No |

#### Grupo 12: Palletizado (2 tablas)
| Key | Tabla MySQL | Display Name | has_active |
|-----|-------------|--------------|------------|
| insumos_palletizados | insumos_palletizados | Insumos Palletizados | Si |
| detalle_precio_palletizados | detalle_precio_palletizados | Detalle Precio Palletizados | No |

#### Grupo 13: Catalogos Generales (4 tablas)
| Key | Tabla MySQL | Display Name | has_active |
|-----|-------------|--------------|------------|
| paises | paises | Paises | Si |
| fsc | fsc | FSC | Si |
| reference_types | reference_types | Tipos de Referencia | Si |
| recubrimiento_types | recubrimiento_types | Tipos de Recubrimiento | Si |

### 14.4 Pantallas Personalizadas (3)

Ademas del sistema generico, existen pantallas con logica especifica:

| Pantalla | Backend | Frontend | Funcionalidad Especial |
|----------|---------|----------|------------------------|
| Clientes | clients.py | ClientsList.tsx, ClientForm.tsx | RUT validation, contactos multiples |
| Usuarios | users.py | UsersList.tsx, UserForm.tsx | Hash passwords, roles, permisos |
| Jerarquias | hierarchies.py | JerarquiasList.tsx | Arbol jerarquico, sub-jerarquias |

### 14.5 Frontend - MantenedorGenerico.tsx

**Archivo**: `frontend/src/pages/Mantenedores/MantenedorGenerico.tsx`

Componente React que renderiza dinamicamente cualquiera de las 55 tablas:

```typescript
interface MantenedorGenericoProps {
  tablaKey: string;  // Key del mantenedor (ej: "colores", "plantas")
  onNavigate: (page: PageType) => void;
}

// Caracteristicas:
// - Detecta columnas automaticamente desde TABLA_CONFIG
// - Genera formulario dinamico para crear/editar
// - Maneja activar/desactivar segun has_active
// - Paginacion y busqueda integradas
// - Tema Monitor One con styled-components
```

### 14.6 Estructura de Archivos Final

```
msw-envases-ot/src/app/routers/mantenedores/
├── __init__.py           # Exporta todos los routers
├── generic.py            # Router generico (55 tablas)
├── clients.py            # CRUD Clientes (personalizado)
├── users.py              # CRUD Usuarios (personalizado)
└── hierarchies.py        # CRUD Jerarquias (personalizado)

msw-envases-ot/frontend/src/pages/Mantenedores/
├── index.ts                 # Exportaciones
├── MantenedorGenerico.tsx   # Componente dinamico (55 tablas)
├── ClientsList.tsx          # Lista clientes
├── ClientForm.tsx           # Form clientes
├── UsersList.tsx            # Lista usuarios
├── UserForm.tsx             # Form usuarios
└── JerarquiasList.tsx       # Lista jerarquias
```

### 14.7 Validaciones Implementadas

**RUT Chileno (Clientes):**
```typescript
function validateRut(rut: string): boolean {
  const cleanRut = rut.replace(/[.-]/g, '');
  const dv = cleanRut.slice(-1).toUpperCase();
  const numbers = cleanRut.slice(0, -1);
  let sum = 0, mul = 2;
  for (let i = numbers.length - 1; i >= 0; i--) {
    sum += parseInt(numbers[i]) * mul;
    mul = mul === 7 ? 2 : mul + 1;
  }
  const expectedDv = 11 - (sum % 11);
  const dvChar = expectedDv === 11 ? '0' : expectedDv === 10 ? 'K' : expectedDv.toString();
  return dv === dvChar;
}
```

### 14.8 Control de Acceso

| Rol | Mantenedores Permitidos |
|-----|------------------------|
| Super Admin | Todos |
| Administrador | Todos excepto Usuarios |
| Vendedor | Solo lectura Clientes |
| Desarrollo | Ninguno |

---

## 15. Cotizaciones (FASE 6.8) - COMPLETADO

### 15.1 Descripcion

Sistema completo de cotizaciones migrado al microservicio React, incluyendo lista, creacion, edicion y aprobacion de cotizaciones.

### 15.2 Pantallas Implementadas

| Pantalla | Archivo | Funcionalidad |
|----------|---------|---------------|
| Lista Cotizaciones | CotizacionesList.tsx | Dashboard con filtros, paginacion, estados |
| Crear/Editar | CotizacionForm.tsx | Formulario completo con validaciones |
| Detalles | DetalleForm.tsx | Gestion de items de cotizacion |
| Aprobaciones | AprobacionesList.tsx | Lista de cotizaciones pendientes de aprobar |

### 15.3 Backend

**Router**: `src/app/routers/cotizaciones.py`

```
GET    /api/v1/cotizaciones/           # Lista paginada con filtros
GET    /api/v1/cotizaciones/{id}       # Detalle cotizacion
POST   /api/v1/cotizaciones/           # Crear cotizacion
PUT    /api/v1/cotizaciones/{id}       # Actualizar cotizacion
GET    /api/v1/cotizaciones/{id}/detalles  # Lista detalles
POST   /api/v1/cotizaciones/{id}/detalles  # Crear detalle
PUT    /api/v1/cotizaciones/detalles/{id}  # Actualizar detalle
DELETE /api/v1/cotizaciones/detalles/{id}  # Eliminar detalle
POST   /api/v1/cotizaciones/{id}/aprobar   # Aprobar cotizacion
POST   /api/v1/cotizaciones/{id}/rechazar  # Rechazar cotizacion
```

---

## 16. Resumen de Fases

| Fase | Descripcion | Estado |
|------|-------------|--------|
| 6.1 | Conectar con datos reales de catalogos | COMPLETADO |
| 6.2 | Dashboard OTs - Lista con filtros y paginacion | COMPLETADO |
| 6.3 | Crear OT - Formulario completo con CascadeForm | COMPLETADO |
| 6.4 | Editar OT | COMPLETADO |
| 6.5 | Gestionar OT (workflow por areas) | COMPLETADO |
| 6.6 | Notificaciones | COMPLETADO |
| 6.7 | Mantenedores (55 genericos + 3 personalizados) | COMPLETADO |
| 6.8 | Cotizaciones (lista, crear, editar, aprobar) | COMPLETADO |

---

## 17. Aprobacion de OTs (FASE 6.9) - COMPLETADO

### 17.1 Descripcion

Pantalla para supervisores que permite aprobar o rechazar ordenes de trabajo pendientes.

### 17.2 Backend

**Endpoints agregados en `work_orders.py`:**
```python
GET  /api/v1/work-orders/pending-approval        # Lista OTs pendientes de aprobacion
PUT  /api/v1/work-orders/{ot_id}/approve         # Aprobar OT
PUT  /api/v1/work-orders/{ot_id}/reject          # Rechazar OT
```

### 17.3 Frontend

**Archivo**: `frontend/src/pages/WorkOrders/AprobacionOTsList.tsx`

| Funcionalidad | Implementacion |
|--------------|----------------|
| Lista OTs pendientes | Tabla con id, fecha, cliente, vendedor, tipo, canal, estado |
| Filtros | Por fecha, cliente, vendedor |
| Aprobar | Boton verde con confirmacion |
| Rechazar | Boton rojo con modal para motivo |
| Navegacion | A gestionar OT para ver detalle |

### 17.4 Integracion App.tsx

```typescript
// PageType actualizado
| 'aprobacion-ots'

// NavTab agregado
<NavTab onClick={() => handleNavigate('aprobacion-ots')}>
  Aprobar OTs
</NavTab>

// Render
case 'aprobacion-ots':
  return <AprobacionOTsList onNavigate={handleNavigate} />;
```

---

## 18. Asignaciones de OTs (FASE 6.10) - COMPLETADO

### 18.1 Descripcion

Pantalla para asignar profesionales (ingenieros, diseñadores, catalogadores) a ordenes de trabajo pendientes.

### 18.2 Backend

**Endpoints agregados en `work_orders.py`:**
```python
GET  /api/v1/work-orders/pending-assignment      # Lista OTs sin asignar
PUT  /api/v1/work-orders/{ot_id}/assign          # Asignar profesional
GET  /api/v1/work-orders/professionals           # Lista profesionales disponibles
```

**Modelos:**
```python
class AssignmentListItem(BaseModel):
    id: int
    created_at: str
    client_name: str
    vendedor_nombre: str
    tipo_solicitud: str
    canal: Optional[str]
    jerarquia_1: Optional[str]
    jerarquia_2: Optional[str]
    jerarquia_3: Optional[str]
    cad: Optional[str]
    profesional_asignado: Optional[str]
    dias_sin_asignar: int

class AssignProfessionalRequest(BaseModel):
    profesional_id: int
```

### 18.3 Frontend

**Archivo**: `frontend/src/pages/WorkOrders/AsignacionesList.tsx`

| Funcionalidad | Implementacion |
|--------------|----------------|
| Lista OTs | Tabla con OT, fecha, cliente, vendedor, tipo, canal, jerarquia, CAD, profesional, dias |
| Filtros | Estado asignacion (SI/NO), tipo solicitud, fechas |
| Indicador dias | Badge con colores (verde < 3, amarillo 3-6, rojo > 7) |
| Modal asignar | Select de profesionales disponibles |
| Navegacion | Ver OT para ir a gestionar |

**API Service (api.ts):**
```typescript
export interface AssignmentFilters {
  page?: number;
  page_size?: number;
  asignado?: 'SI' | 'NO';
  tipo_solicitud?: string;
  canal_id?: number;
  vendedor_id?: number;
  estado_id?: number;
  date_desde?: string;
  date_hasta?: string;
}

export const assignmentsApi = {
  getPendingAssignment: (filters) => ...,
  assign: (otId, profesionalId) => ...,
  getProfessionals: () => ...,
};
```

### 18.4 Integracion App.tsx

```typescript
// PageType actualizado
| 'asignaciones'

// NavTab agregado
<NavTab onClick={() => handleNavigate('asignaciones')}>
  Asignaciones
</NavTab>

// Render
case 'asignaciones':
  return <AsignacionesList onNavigate={handleNavigate} />;
```

---

## 19. Cotizador Externo (FASE 6.11) - COMPLETADO

### 19.1 Descripcion

Vista simplificada de cotizaciones para usuarios externos. Similar a CotizacionesList pero con:
- Filtros reducidos (fechas, ID, estado, CAD)
- Columnas especificas: N° Productos (total/ganados/perdidos), fechas 1ra y ultima version
- Acciones limitadas: Ver/Editar + Descargar PDF (solo aprobadas)

### 19.2 Frontend - CotizadorExternoList.tsx

**Archivo**: `frontend/src/pages/Cotizaciones/CotizadorExternoList.tsx`

**Funcionalidades:**
- Filtros: date_desde, date_hasta, cotizacion_id, estado_id, cad
- Fechas por defecto: ultimos 3 meses
- Columnas: Cotizacion N°, N° de Productos (total/ganados/perdidos), Creador, Cliente, Fecha 1ra Ver, Fecha Ult Ver, Descrip, CAD, OT, N° Version, Estado, Acciones
- Indicadores visuales para detalles ganados/perdidos
- Descarga PDF para cotizaciones aprobadas
- Navegacion a cotizacion-externa-nueva y cotizacion-externa-editar

### 19.3 Integracion App.tsx

```typescript
// Nuevos PageTypes
| 'cotizador-externo' | 'cotizacion-externa-nueva' | 'cotizacion-externa-editar'

// NavTab
<NavTab onClick={() => handleNavigate('cotizador-externo')}>
  Cot. Externo
</NavTab>

// Render
case 'cotizador-externo':
  return <CotizadorExternoList onNavigate={handleNavigate} />;
case 'cotizacion-externa-nueva':
  return <CotizacionForm onNavigate={handleNavigate} isExterno={true} />;
case 'cotizacion-externa-editar':
  return <CotizacionForm cotizacionId={id} onNavigate={handleNavigate} isExterno={true} />;
```

### 19.4 Actualizacion CotizacionForm

Se agrego prop `isExterno?: boolean` al componente CotizacionForm para:
- Determinar pagina de retorno (cotizador-externo vs cotizaciones)
- Potencial customizacion de campos segun contexto

### 19.5 API - Nuevos campos CotizacionListItem

```typescript
interface CotizacionListItem {
  // ... campos existentes
  detalles_ganados?: number;
  detalles_perdidos?: number;
  fecha_primera_version?: string;
  primer_detalle_descripcion?: string;
  primer_detalle_cad?: string;
  primer_detalle_ot?: number | string;
}

interface CotizacionFilters {
  // ... campos existentes
  cad?: string;  // Nuevo filtro
}
```

---

## 20. Cotizar Multiples OT (FASE 6.12) - COMPLETADO

### 20.1 Descripcion
Pantalla para seleccionar multiples OTs y generar cotizaciones masivas de tipo Corrugado y/o Esquinero.

### 20.2 Componente React

**Archivo**: `frontend/src/pages/WorkOrders/CotizarMultiplesOT.tsx`

### 20.3 Funcionalidades Implementadas

| Funcionalidad | Descripcion |
|---------------|-------------|
| Filtros | Fechas, OT ID, CAD, descripcion |
| Checkboxes | Seleccion individual por tipo (Corrugado/Esquinero) |
| Select All | Header checkboxes para seleccionar todos de un tipo |
| Resumen | Conteo de selecciones por tipo |
| Paginacion | Navegacion entre paginas de resultados |

### 20.4 Integracion en App.tsx

```typescript
// PageType
| 'cotizar-multiples-ot'

// NavTab
<NavTab
  $active={currentPage === 'cotizar-multiples-ot'}
  onClick={() => handleNavigate('cotizar-multiples-ot')}
>
  Cot. Multiples
</NavTab>

// renderPage
case 'cotizar-multiples-ot':
  return <CotizarMultiplesOT onNavigate={handleNavigate} />;
```

---

## 21. Reportes Dashboard (FASE 6.13) - COMPLETADO

### 21.1 Descripcion
Dashboard que lista todos los reportes disponibles del sistema organizados por categoria.

### 21.2 Componente React

**Archivo**: `frontend/src/pages/Reports/ReportsDashboard.tsx`

### 21.3 Categorias de Reportes

| Categoria | Reportes |
|-----------|----------|
| OTs y Gestion | OTs Activas por Usuario, OTs Completadas, OTs Entre Fechas, Gestion OTs Activas, Carga por Mes |
| Tiempos y Rendimiento | Tiempo por Area, Tiempo Disenador Externo, Tiempo Primera Muestra |
| Sala de Muestras | Sala Muestra, Indicador Sala Muestra, Diseno Estructural, Muestras |
| Rechazos y Anulaciones | Anulaciones, Rechazos por Mes, Motivos de Rechazo |

### 21.4 Funcionalidades

- Cards con iconos descriptivos
- Status badges (Disponible/En desarrollo/Deshabilitado)
- Contador de reportes disponibles
- Mensaje informativo sobre estado de migracion
- Estructura preparada para Chart.js

---

## 22. Crear OT Especial (FASE 6.14) - COMPLETADO

### 22.1 Descripcion
Formulario unificado para crear OTs de tipos especiales: Estudio Benchmarking, Ficha Tecnica y Licitacion.

### 22.2 Componente React

**Archivo**: `frontend/src/pages/WorkOrders/CreateSpecialOT.tsx`

### 22.3 Tipos de OT Especial

| Tipo | Descripcion |
|------|-------------|
| Estudio Benchmarking | Analisis de productos de la competencia con ensayos de laboratorio |
| Ficha Tecnica | Especificaciones tecnicas detalladas del producto |
| Licitacion | OTs para procesos de licitacion con requerimientos especiales |

### 22.4 Funcionalidades

- Selector de tipo con cards visuales
- Formulario de datos comerciales comunes
- Seccion especifica de Ensayos Caja (17 tipos de ensayo)
- Campos dinamicos segun tipo seleccionado

---

## 23. Detalle Log OT (FASE 6.15) - COMPLETADO

### 23.1 Descripcion
Visor de historial de cambios (audit log) de una OT especifica, mostrando todas las modificaciones realizadas.

### 23.2 Componente React

**Archivo**: `frontend/src/pages/WorkOrders/DetalleLogOT.tsx`

### 23.3 Funcionalidades Implementadas

| Funcionalidad | Descripcion |
|---------------|-------------|
| Filtros | Rango de fechas, ID de cambio, campo modificado, usuario |
| Tabla aplanada | Estructura anidada de datos_modificados desplegada en filas |
| Badges operacion | Modificacion (azul), Insercion (verde), Eliminacion (rojo) |
| Formato valores | Diferenciacion visual entre vacio, nulo y con valor |
| Paginacion | Navegacion entre paginas de entradas del log |
| Navegacion | Volver a dashboard o a gestion de OT |

### 23.4 Estructura de Datos

```typescript
interface LogEntry {
  id: number;
  work_order_id: number;
  operacion: 'Modificacion' | 'Insercion' | 'Eliminacion';
  observacion: string;
  datos_modificados: Record<string, { old: any; new: any }>;
  user_id: number;
  user_name: string;
  created_at: string;
}

interface FlattenedLogEntry extends Omit<LogEntry, 'datos_modificados'> {
  campo: string;
  valorAntiguo: any;
  valorNuevo: any;
  modIndex: number;
}
```

### 23.5 Integracion en App.tsx

```typescript
// PageType
| 'detalle-log-ot'

// State
const [logOtId, setLogOtId] = useState<number | null>(null);

// handleNavigate
else if (page === 'detalle-log-ot' && id) {
  setLogOtId(id);
  // reset otros estados
}

// getPageTitle
case 'detalle-log-ot': return `Log de Cambios OT #${logOtId}`;

// renderPage
case 'detalle-log-ot':
  return logOtId ? <DetalleLogOT otId={logOtId} onNavigate={handleNavigate} /> : null;
```

---

## 24. Resumen de Fases Actualizado

| Fase | Descripcion | Estado |
|------|-------------|--------|
| 6.1 | Conectar con datos reales de catalogos | COMPLETADO |
| 6.2 | Dashboard OTs - Lista con filtros y paginacion | COMPLETADO |
| 6.3 | Crear OT - Formulario completo con CascadeForm | COMPLETADO |
| 6.4 | Editar OT | COMPLETADO |
| 6.5 | Gestionar OT (workflow por areas) | COMPLETADO |
| 6.6 | Notificaciones | COMPLETADO |
| 6.7 | Mantenedores (55 genericos + 3 personalizados) | COMPLETADO |
| 6.8 | Cotizaciones (lista, crear, editar, aprobar) | COMPLETADO |
| 6.9 | Aprobacion de OTs | COMPLETADO |
| 6.10 | Asignaciones de OTs | COMPLETADO |
| 6.11 | Cotizador Externo | COMPLETADO |
| 6.12 | Cotizar Multiples OT | COMPLETADO |
| 6.13 | Reportes Dashboard | COMPLETADO |
| 6.14 | Crear OT Especial | COMPLETADO |
| 6.15 | Detalle Log OT | COMPLETADO |
| 6.16 | Reportes con Chart.js (15 reportes) | COMPLETADO |
| 6.17 | Recuperacion de Contrasena (forgot/reset) | COMPLETADO |

---

## 25. Reportes con Chart.js (FASE 6.16) - COMPLETADO

### 25.1 Descripcion
Implementacion de 15 reportes con graficos interactivos usando Chart.js y react-chartjs-2.

### 25.2 Dependencias Instaladas

```bash
npm install chart.js react-chartjs-2
```

### 25.3 Reportes Implementados

| Reporte | Archivo | Graficos |
|---------|---------|----------|
| OTs Activas por Usuario | `ReportOTsActivasPorUsuario.tsx` | Bar (stacked), Pie |
| OTs Completadas | `ReportOTsCompletadas.tsx` | Line (trend), Bar (stacked), Bar |
| Tiempo por Area | `ReportTiempoPorArea.tsx` | Bar, Radar, Line (multi) |
| Rechazos por Mes | `ReportRechazosPorMes.tsx` | Bar, Doughnut, Line |
| Carga de OTs por Mes | `ReportCargaMensual.tsx` | Bar (stacked), Line (capacity) |
| Anulaciones de OTs | `ReportAnulaciones.tsx` | Bar (monthly), Doughnut (reasons) |
| OTs Completadas Entre Fechas | `ReportOTsCompletadasFechas.tsx` | Bar (stacked), Line (trend) |
| Gestion OTs Activas | `ReportGestionOTsActivas.tsx` | Bar (stacked), Doughnut (estados) |
| Tiempo Primera Muestra | `ReportTiempoPrimeraMuestra.tsx` | Line (trend), Bar (por tipo) |
| Motivos de Rechazo | `ReportMotivosRechazo.tsx` | Pie, Bar, Line (dual axis) |
| Tiempo Disenador Externo | `ReportTiempoDisenadorExterno.tsx` | Bar, Doughnut, Line |
| Sala de Muestra | `ReportSalaMuestra.tsx` | Line, Doughnut, Bar (stacked) |
| Indicadores Sala Muestra | `ReportIndicadorSalaMuestra.tsx` | Line (dual axis), Radar, Bar |
| Diseno Estructural y Sala | `ReportDisenoEstructuralSala.tsx` | Bar, Doughnut, Line |
| Muestras | `ReportMuestras.tsx` | Pie, Bar, Line (trend) |

### 25.4 Caracteristicas Comunes

- **Conexion a API real de QAS** (todos los reportes actualizados)
- Filtros por fecha, area, periodo
- Cards de resumen con KPIs
- Tablas de detalle con paginacion
- Estados de carga y error
- Badges de estado con colores por area
- Barras de progreso visuales
- Indicadores de tendencia (mejorando/empeorando)

### 25.5 Tipos de Graficos Utilizados

| Tipo | Uso |
|------|-----|
| Bar | Comparaciones entre categorias |
| Bar Stacked | Distribucion de estados |
| Line | Tendencias temporales |
| Pie/Doughnut | Distribucion porcentual |
| Radar | Comparacion de eficiencia |

### 25.6 Integracion en App.tsx

```typescript
// Imports (15 reportes)
import {
  ReportsDashboard,
  ReportOTsActivasPorUsuario,
  ReportOTsCompletadas,
  ReportTiempoPorArea,
  ReportRechazosPorMes,
  ReportCargaMensual,
  ReportAnulaciones,
  ReportOTsCompletadasFechas,
  ReportGestionOTsActivas,
  ReportTiempoPrimeraMuestra,
  ReportMotivosRechazo,
  ReportTiempoDisenadorExterno,
  ReportSalaMuestra,
  ReportIndicadorSalaMuestra,
  ReportDisenoEstructuralSala,
  ReportMuestras
} from './pages/Reports';

// PageTypes (15 rutas de reportes)
| 'reporte-ots-activas' | 'reporte-ots-completadas' | 'reporte-tiempo-area' | 'reporte-rechazos'
| 'reporte-carga-mensual' | 'reporte-anulaciones'
| 'reporte-ots-completadas-fechas' | 'reporte-gestion-ots-activas'
| 'reporte-tiempo-primera-muestra' | 'reporte-motivos-rechazo'
| 'reporte-tiempo-disenador-externo' | 'reporte-sala-muestra'
| 'reporte-indicador-sala-muestra' | 'reporte-diseno-estructural-sala' | 'reporte-muestras'
```

---

## 26. Pantallas Pendientes de Migrar

### 26.1 Reportes
- **Todos los 15 reportes del dashboard estan COMPLETADOS**
- Cada reporte incluye graficos interactivos con Chart.js
- **Todos los reportes conectados a API real de QAS** (MySQL Laravel)

### 26.2 Generacion PDF
- **Laravel**: `cotizador/cotizaciones/generar_pdf.blade.php` y templates
- **Descripcion**: Exportar cotizaciones a PDF
- **Estado**: COMPLETADO
- **Implementacion**:
  - Backend: `src/app/routers/cotizaciones/router.py` - endpoint `/export-pdf`
  - Frontend: `cotizacionesApi.exportPdf(id)` en `services/api.ts`
  - Biblioteca: reportlab 4.0.8 (con fallback a HTML)

---

## 27. Nuevas Funcionalidades Implementadas (2025-12-20)

### 27.1 Carga Masiva de Mantenedores
- **Componente**: `frontend/src/pages/Mantenedores/BulkUpload.tsx`
- **Descripcion**: Wizard de 3 pasos para carga masiva de datos
- **Caracteristicas**:
  - Drag & drop de archivos CSV/Excel
  - Validacion automatica de datos antes de carga
  - Seleccion de tabla/mantenedor destino
  - Descarga de plantilla CSV
  - Vista previa con resaltado de errores
  - Barra de progreso durante la carga
  - Resumen de resultados (exitosos/fallidos)
- **Backend**: `POST /mantenedores/generic/{tabla_key}/bulk`
- **Ruta**: `/carga-masiva`

### 27.2 Cambio de Contrasena
- **Componente**: `frontend/src/pages/Settings/ChangePassword.tsx`
- **Descripcion**: Pantalla para cambiar contraseña del usuario autenticado
- **Caracteristicas**:
  - Validacion de contrasena actual
  - Requisitos de seguridad (minimo 6 caracteres)
  - Indicadores visuales de requisitos
  - Toggle para mostrar/ocultar contrasena
  - Confirmacion de nueva contrasena
- **Backend**: `POST /auth/change-password`
- **Acceso**: Boton "Cambiar Clave" en header del usuario

### 27.3 Exportacion PDF de Cotizaciones
- **Backend**: `GET /cotizaciones/{id}/export-pdf`
- **Descripcion**: Genera PDF profesional de cotizacion
- **Caracteristicas**:
  - Formato A4 con estilos personalizados
  - Informacion del cliente y condiciones comerciales
  - Tabla de detalles de productos
  - Pie de pagina con fecha de generacion
  - Fallback a HTML si reportlab no esta disponible
- **Frontend**: `cotizacionesApi.exportPdf(id)` - descarga automatica

---

## 28. Recuperacion de Contrasena (FASE 6.17) - COMPLETADO

### 28.1 Descripcion

Flujo completo de recuperacion de contrasena "Olvide mi contrasena" integrado con la tabla `password_resets` de Laravel MySQL.

### 28.2 Backend - Endpoints auth.py

**Archivo**: `src/app/routers/auth.py`

```python
# Nuevos endpoints
POST /api/v1/auth/forgot-password       # Solicitar recuperacion
POST /api/v1/auth/validate-reset-token  # Validar token antes de resetear
POST /api/v1/auth/reset-password        # Establecer nueva contrasena

# Schemas
class ForgotPasswordRequest(BaseModel):
    email: str

class ValidateTokenRequest(BaseModel):
    token: str
    email: str

class ResetPasswordRequest(BaseModel):
    token: str
    email: str
    new_password: str
```

**Funciones auxiliares:**
```python
def generate_reset_token() -> str:
    """Genera token seguro de 32 bytes URL-safe"""
    return secrets.token_urlsafe(32)

def hash_token(token: str) -> str:
    """Hash SHA-256 compatible con Laravel"""
    return hashlib.sha256(token.encode()).hexdigest()
```

**Flujo de forgot-password:**
1. Buscar usuario por email en `users`
2. Eliminar tokens previos del email en `password_resets`
3. Generar nuevo token y guardarlo hasheado
4. Retornar mensaje de exito (en dev, incluye token para testing)

**Flujo de reset-password:**
1. Buscar token hasheado en `password_resets`
2. Verificar que no haya expirado (1 hora)
3. Verificar que el email coincida
4. Hash nueva contrasena con bcrypt (formato Laravel $2y$)
5. Actualizar usuario y eliminar token usado

### 28.3 Frontend - Paginas de Autenticacion

**Estructura de archivos:**
```
frontend/src/pages/Auth/
├── index.ts              # Exportaciones
├── ForgotPassword.tsx    # Solicitar recuperacion
└── ResetPassword.tsx     # Establecer nueva contrasena
```

### 28.4 ForgotPassword.tsx

**Funcionalidades:**
- Formulario con campo email
- Validacion de email requerido
- Mensaje de exito con instrucciones
- En modo desarrollo: muestra token y URL para testing
- Link para volver al login
- Estilos Monitor One con styled-components

**Estados del componente:**
```typescript
const [email, setEmail] = useState('');
const [isLoading, setIsLoading] = useState(false);
const [error, setError] = useState<string | null>(null);
const [success, setSuccess] = useState(false);
const [devInfo, setDevInfo] = useState<{token?: string; url?: string} | null>(null);
```

### 28.5 ResetPassword.tsx

**Funcionalidades:**
- Validacion de token al cargar la pagina
- Campos: nueva contrasena y confirmar contrasena
- Indicador de requisitos de seguridad
- Validacion de coincidencia de contrasenas
- Mensaje de exito con redireccion a login
- Manejo de token invalido/expirado

**Flujo del componente:**
1. Obtiene `token` y `email` de props/URL params
2. Valida token con API al montar
3. Si invalido: muestra error y link a forgot-password
4. Si valido: muestra formulario de nueva contrasena
5. Al enviar: reset password y redirige a login

### 28.6 Modificaciones a Login.tsx

**Nuevos elementos:**
```typescript
// Nuevo styled component
const ForgotPasswordLink = styled.button`
  background: none;
  border: none;
  color: ${theme.colors.primary};
  font-size: ${theme.typography.sizes.small};
  cursor: pointer;
  // ...
`;

// Nueva prop
interface LoginProps {
  onLogin?: (rut: string, password: string) => Promise<boolean>;
  onNavigate?: (page: string) => void;  // NUEVA
}

// Nuevo enlace en el formulario
{onNavigate && (
  <ForgotPasswordLink
    type="button"
    onClick={() => onNavigate('forgot-password')}
  >
    Olvide mi contrasena
  </ForgotPasswordLink>
)}
```

### 28.7 Modificaciones a App.tsx

**Nuevos tipos y estados:**
```typescript
import { ForgotPassword, ResetPassword } from './pages/Auth';

type AuthPage = 'login' | 'forgot-password' | 'reset-password';

// En AppContent
const [authPage, setAuthPage] = useState<AuthPage>('login');
const [resetParams, setResetParams] = useState<{ token?: string; email?: string }>({});
```

**Nueva funcion de navegacion auth:**
```typescript
const handleAuthNavigate = useCallback((page: string, params?: Record<string, string>) => {
  setAuthPage(page as AuthPage);
  if (params) {
    setResetParams(params);
  }
}, []);
```

**Render condicional:**
```typescript
// Si no hay usuario autenticado
switch (authPage) {
  case 'forgot-password':
    return <ForgotPassword onNavigate={handleAuthNavigate} />;
  case 'reset-password':
    return (
      <ResetPassword
        token={resetParams.token}
        email={resetParams.email}
        onNavigate={handleAuthNavigate}
      />
    );
  default:
    return <Login onLogin={handleLogin} onNavigate={handleAuthNavigate} />;
}
```

### 28.8 API Frontend - authApi

**Archivo**: `frontend/src/services/api.ts`

```typescript
export const authApi = {
  // ... login existente

  forgotPassword: async (email: string): Promise<{
    message: string;
    success: boolean;
    _dev_token?: string;
    _dev_reset_url?: string;
  }> => {
    const response = await api.post('/auth/forgot-password', { email });
    return response.data;
  },

  validateResetToken: async (token: string, email: string): Promise<{
    valid: boolean;
    message: string;
    email?: string;
  }> => {
    const response = await api.post('/auth/validate-reset-token', { token, email });
    return response.data;
  },

  resetPassword: async (token: string, email: string, newPassword: string): Promise<{
    message: string;
    success: boolean;
  }> => {
    const response = await api.post('/auth/reset-password', {
      token,
      email,
      new_password: newPassword,
    });
    return response.data;
  },
};
```

### 28.9 Tabla password_resets (MySQL Laravel)

| Campo | Tipo | Descripcion |
|-------|------|-------------|
| email | varchar(255) | Email del usuario |
| token | varchar(255) | Token hasheado SHA-256 |
| created_at | datetime | Timestamp de creacion |

**Notas de compatibilidad:**
- Token guardado con SHA-256 hash (compatible con Laravel)
- Password hash con bcrypt usando prefijo `$2y$` (Laravel) convertido a `$2b$` (Python)
- Expiracion de token: 1 hora

---

## 29. Editar OT Especial (FASE 6.18) - COMPLETADO

### 29.1 Descripcion

Componente unificado para editar OTs de tipos especiales: Estudio Benchmarking, Ficha Tecnica y Licitacion. Detecta automaticamente el tipo de OT basado en `tipo_solicitud` y muestra los campos correspondientes.

### 29.2 Componente React

**Archivo**: `frontend/src/pages/WorkOrders/EditSpecialOT.tsx`

### 29.3 Tipos de OT Soportados

| Tipo | tipo_solicitud | Campos Especificos |
|------|----------------|-------------------|
| Estudio Benchmarking | 7 | 17 ensayos seleccionables |
| Ficha Tecnica | 8 | Campos de especificaciones |
| Licitacion | 9 | Datos de licitacion |

### 29.4 Ensayos de Estudio Benchmarking (17 tipos)

```typescript
const ensayos = [
  { id: 'bct', label: 'BCT (lbf)', field: 'ensayo_bct' },
  { id: 'ect', label: 'ECT (lb/in)', field: 'ensayo_ect' },
  { id: 'bct_humedo', label: 'BCT Húmedo (lbf)', field: 'ensayo_bct_humedo' },
  { id: 'flat_crush', label: 'Flat Crush (kgf)', field: 'ensayo_flat_crush' },
  { id: 'adhesion', label: 'Adhesión', field: 'ensayo_adhesion' },
  { id: 'cobb', label: 'Cobb (g/m²)', field: 'ensayo_cobb' },
  { id: 'gramaje', label: 'Gramaje (g/m²)', field: 'ensayo_gramaje' },
  { id: 'rasgado', label: 'Rasgado (mN)', field: 'ensayo_rasgado' },
  { id: 'espesor', label: 'Espesor (mm)', field: 'ensayo_espesor' },
  { id: 'humedad', label: 'Humedad (%)', field: 'ensayo_humedad' },
  { id: 'mullen', label: 'Mullen (kPa)', field: 'ensayo_mullen' },
  { id: 'ring_crush', label: 'Ring Crush (kN/m)', field: 'ensayo_ring_crush' },
  { id: 'cmt', label: 'CMT (N)', field: 'ensayo_cmt' },
  { id: 'cct', label: 'CCT (kN/m)', field: 'ensayo_cct' },
  { id: 'pin_adhesion', label: 'Pin Adhesión (N)', field: 'ensayo_pin_adhesion' },
  { id: 'scoring', label: 'Scoring (N)', field: 'ensayo_scoring' },
  { id: 'compresion_horz', label: 'Compresión Horizontal (N)', field: 'ensayo_compresion_horizontal' },
];
```

### 29.5 Estructura del Componente

```typescript
interface EditSpecialOTProps {
  otId: number;
  onNavigate: (page: string, id?: number) => void;
}

export default function EditSpecialOT({ otId, onNavigate }: EditSpecialOTProps) {
  // Estados
  const [formData, setFormData] = useState<WorkOrderDetail | null>(null);
  const [otType, setOtType] = useState<'benchmarking' | 'ficha-tecnica' | 'licitacion' | null>(null);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [success, setSuccess] = useState(false);
  const [selectedEnsayos, setSelectedEnsayos] = useState<Record<string, boolean>>({});

  // Detecta tipo de OT al cargar
  useEffect(() => {
    loadWorkOrder();
  }, [otId]);

  // Funcion para detectar tipo segun tipo_solicitud
  const detectOtType = (tipoSolicitud: number) => {
    switch (tipoSolicitud) {
      case 7: return 'benchmarking';
      case 8: return 'ficha-tecnica';
      case 9: return 'licitacion';
      default: return null;
    }
  };

  // ... render dinamico segun tipo detectado
}
```

### 29.6 Integracion en App.tsx

**Cambios realizados:**

1. **Import del componente:**
```typescript
import EditSpecialOT from './pages/WorkOrders/EditSpecialOT';
```

2. **Nuevo PageType:**
```typescript
type PageType =
  | 'dashboard' | 'crear-ot' | 'crear-ot-especial' | 'editar-ot-especial' | ...
```

3. **Handler de navegacion:**
```typescript
} else if (page === 'editar-ot-especial' && id) {
  setEditingOtId(id);
  setManagingOtId(null);
  setLogOtId(null);
  setEditingCotizacionId(null);
}
```

4. **Titulo de pagina:**
```typescript
case 'editar-ot-especial': return `Editar OT Especial #${editingOtId}`;
```

5. **Render condicional:**
```typescript
case 'editar-ot-especial':
  return editingOtId ? <EditSpecialOT otId={editingOtId} onNavigate={handleNavigate} /> : null;
```

### 29.7 API Utilizada

```typescript
// Cargar datos de la OT
const response = await workOrdersApi.get(otId);

// Actualizar OT
await workOrdersApi.update(otId, updateData);
```

### 29.8 Flujo de Usuario

1. Usuario hace clic en "Editar" desde lista de OTs especiales
2. Sistema navega a `editar-ot-especial` con `otId`
3. Componente carga datos de la OT via API
4. Detecta tipo segun `tipo_solicitud` (7, 8 o 9)
5. Renderiza formulario correspondiente al tipo
6. Usuario modifica campos y guarda
7. Sistema actualiza via `workOrdersApi.update()`
8. Muestra mensaje de exito y opcion de volver

### 29.9 Caracteristicas

| Caracteristica | Implementacion |
|---------------|----------------|
| Deteccion automatica de tipo | Basada en tipo_solicitud |
| Checkboxes de ensayos | Grid de 3 columnas para Benchmarking |
| Validacion de tipo valido | Error si tipo_solicitud no es 7, 8 o 9 |
| Estados de carga | Spinner mientras carga datos |
| Mensajes de feedback | Exito y error con estilos Monitor One |
| Navegacion inteligente | Volver a dashboard despues de guardar |

---

## 30. Resumen de Fases Actualizado

| Fase | Descripcion | Estado |
|------|-------------|--------|
| 6.1 | Conectar con datos reales de catalogos | COMPLETADO |
| 6.2 | Dashboard OTs - Lista con filtros y paginacion | COMPLETADO |
| 6.3 | Crear OT - Formulario completo con CascadeForm | COMPLETADO |
| 6.4 | Editar OT | COMPLETADO |
| 6.5 | Gestionar OT (workflow por areas) | COMPLETADO |
| 6.6 | Notificaciones | COMPLETADO |
| 6.7 | Mantenedores (55 genericos + 3 personalizados) | COMPLETADO |
| 6.8 | Cotizaciones (lista, crear, editar, aprobar) | COMPLETADO |
| 6.9 | Aprobacion de OTs | COMPLETADO |
| 6.10 | Asignaciones de OTs | COMPLETADO |
| 6.11 | Cotizador Externo | COMPLETADO |
| 6.12 | Cotizar Multiples OT | COMPLETADO |
| 6.13 | Reportes Dashboard | COMPLETADO |
| 6.14 | Crear OT Especial | COMPLETADO |
| 6.15 | Detalle Log OT | COMPLETADO |
| 6.16 | Reportes con Chart.js (15 reportes) | COMPLETADO |
| 6.17 | Recuperacion de Contrasena (forgot/reset) | COMPLETADO |
| 6.18 | Editar OT Especial (Benchmarking/Ficha/Licitacion) | COMPLETADO |

---

## 31. Aprobacion Externa de Cotizaciones (FASE 6.19) - COMPLETADO

### 31.1 Descripcion

Pantalla para gestionar aprobaciones externas de cotizaciones. Permite a usuarios con rol de vendedor externo ver cotizaciones pendientes de su aprobacion y gestionarlas (aprobar, aprobar parcialmente o rechazar).

### 31.2 Backend - Endpoints

**Archivo**: `src/app/routers/cotizaciones/router.py`

```python
# Nuevos endpoints agregados

GET  /api/v1/cotizaciones/pendientes-aprobacion-externo/
     # Lista cotizaciones pendientes de aprobacion externa
     # Filtros: date_desde, date_hasta, cliente_nombre, cotizacion_id
     # Retorna: items paginados con dias_pendiente, cliente, creador, etc.

POST /api/v1/cotizaciones/{id}/solicitar-aprobacion-externo
     # Enviar cotizacion a aprobacion externa
     # Params: user_id (quien solicita)
     # Actualiza: role_can_show = 4 (externo), estado_id = 2 (por aprobar)

POST /api/v1/cotizaciones/{id}/gestionar-aprobacion-externo
     # Aprobar, aprobar parcial o rechazar cotizacion
     # Body: { action: 'aprobar' | 'aprobar_parcial' | 'rechazar', motivo?: string }
     # Crea registro en cotizacion_approvals

GET  /api/v1/cotizaciones/{id}/historial-aprobaciones
     # Lista historial de aprobaciones de una cotizacion
     # Retorna: registros de cotizacion_approvals con usuario, fecha, motivo
```

### 31.3 Modelos de Datos

**Schemas en router.py:**
```python
class CotizacionPendienteExterno(BaseModel):
    id: int
    cliente_nombre: str
    creador_nombre: str
    created_at: str
    total_detalles: int
    monto_total: float | None
    dias_pendiente: int
    version: int | None
    descripcion: str | None

class GestionarAprobacionRequest(BaseModel):
    action: Literal['aprobar', 'aprobar_parcial', 'rechazar']
    motivo: str | None = None
    user_id: int

class HistorialAprobacion(BaseModel):
    id: int
    action_made: str
    motivo: str | None
    user_nombre: str
    created_at: str
    role_do_action: int | None
```

**Tabla cotizacion_approvals (MySQL Laravel):**
| Campo | Tipo | Descripcion |
|-------|------|-------------|
| id | int | PK autoincrement |
| cotizacion_id | int | FK a cotizacions |
| user_id | int | FK a users (aprobador) |
| action_made | varchar | 'Aprobación Total', 'Aprobación Parcial', 'Rechazo' |
| motivo | text | Comentario/observacion |
| role_do_action | int | Rol siguiente (3=Gerente Comercial, 15=Gerente General) |
| created_at | datetime | Timestamp |

### 31.4 Frontend - AprobacionExternaCotizaciones.tsx

**Archivo**: `frontend/src/pages/Cotizaciones/AprobacionExternaCotizaciones.tsx`

**Funcionalidades implementadas:**
| Funcionalidad | Descripcion |
|--------------|-------------|
| Lista paginada | Cotizaciones pendientes de aprobacion externa |
| Filtros | Fecha desde/hasta, nombre cliente, ID cotizacion |
| Indicador dias | Badge con colores (verde < 3, amarillo 3-7, rojo > 7) |
| Modal aprobar | Formulario con opciones: Total, Parcial, Rechazar |
| Historial | Modal con timeline de aprobaciones anteriores |
| Ver detalle | Navegacion a editar cotizacion |

**Estructura del componente:**
```typescript
interface CotizacionPendiente {
  id: number;
  cliente_nombre: string;
  creador_nombre: string;
  created_at: string;
  total_detalles: number;
  monto_total: number | null;
  dias_pendiente: number;
  version: number | null;
  descripcion: string | null;
}

interface AprobacionExternaCotizacionesProps {
  onNavigate: (page: string, id?: number) => void;
}

export default function AprobacionExternaCotizaciones({ onNavigate }: Props) {
  // Estados
  const [cotizaciones, setCotizaciones] = useState<CotizacionPendiente[]>([]);
  const [loading, setLoading] = useState(true);
  const [filters, setFilters] = useState({
    date_desde: '',
    date_hasta: '',
    cliente_nombre: '',
    cotizacion_id: ''
  });
  const [selectedCotizacion, setSelectedCotizacion] = useState<CotizacionPendiente | null>(null);
  const [showApprovalModal, setShowApprovalModal] = useState(false);
  const [showHistoryModal, setShowHistoryModal] = useState(false);

  // ... implementacion
}
```

### 31.5 API Frontend

**Archivo**: `frontend/src/services/api.ts`

```typescript
// Nuevos metodos en cotizacionesApi
export const cotizacionesApi = {
  // ... metodos existentes

  // Pendientes de aprobacion externa
  getPendientesAprobacionExterno: async (
    filters: Record<string, string | number> = {}
  ): Promise<{ items: CotizacionPendiente[]; total: number }> => {
    const params = new URLSearchParams();
    Object.entries(filters).forEach(([key, value]) => {
      if (value) params.append(key, String(value));
    });
    const response = await api.get(
      `/cotizaciones/pendientes-aprobacion-externo/?${params}`
    );
    return response.data;
  },

  // Solicitar aprobacion externa
  solicitarAprobacionExterno: async (
    id: number,
    userId: number
  ): Promise<{ message: string }> => {
    const response = await api.post(
      `/cotizaciones/${id}/solicitar-aprobacion-externo`,
      null,
      { params: { user_id: userId } }
    );
    return response.data;
  },

  // Gestionar aprobacion (aprobar/rechazar)
  gestionarAprobacionExterno: async (
    id: number,
    data: { action: 'aprobar' | 'aprobar_parcial' | 'rechazar'; motivo?: string }
  ): Promise<{ message: string }> => {
    const user = authApi.getStoredUser();
    const response = await api.post(
      `/cotizaciones/${id}/gestionar-aprobacion-externo`,
      { ...data, user_id: user?.id }
    );
    return response.data;
  },

  // Historial de aprobaciones
  getHistorialAprobaciones: async (
    id: number
  ): Promise<{ items: HistorialAprobacion[] }> => {
    const response = await api.get(`/cotizaciones/${id}/historial-aprobaciones`);
    return response.data;
  },
};
```

### 31.6 Integracion en App.tsx

**Cambios realizados:**

1. **Import del componente:**
```typescript
import {
  CotizacionesList,
  CotizacionForm,
  AprobacionesList,
  AprobacionExternaCotizaciones  // NUEVO
} from './pages/Cotizaciones';
```

2. **Nuevo PageType:**
```typescript
| 'aprobacion-externa-cotizaciones'
```

3. **Titulo de pagina:**
```typescript
case 'aprobacion-externa-cotizaciones':
  return 'Aprobación Externa de Cotizaciones';
```

4. **Render condicional:**
```typescript
case 'aprobacion-externa-cotizaciones':
  return <AprobacionExternaCotizaciones onNavigate={handleNavigate} />;
```

5. **NavTab agregado:**
```typescript
<NavTab
  $active={currentPage === 'aprobacion-externa-cotizaciones'}
  onClick={() => handleNavigate('aprobacion-externa-cotizaciones')}
>
  Aprob. Externa
</NavTab>
```

### 31.7 Flujo de Aprobacion Externa

```
┌─────────────────────────────────────────────────────────────────────┐
│                    FLUJO APROBACION EXTERNA                          │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  1. Vendedor crea cotizacion                                         │
│     └─> estado_id = 1 (Borrador)                                    │
│                                                                      │
│  2. Vendedor solicita aprobacion externa                            │
│     └─> POST /solicitar-aprobacion-externo                          │
│     └─> role_can_show = 4 (Externo), estado_id = 2 (Por Aprobar)   │
│                                                                      │
│  3. Usuario externo ve en lista "Aprob. Externa"                    │
│     └─> GET /pendientes-aprobacion-externo                          │
│                                                                      │
│  4. Usuario externo gestiona aprobacion                             │
│     └─> POST /gestionar-aprobacion-externo                          │
│     │                                                                │
│     ├─> action = 'aprobar'                                          │
│     │   └─> estado_id = 3 (Aprobado)                               │
│     │   └─> action_made = 'Aprobación Total'                       │
│     │                                                                │
│     ├─> action = 'aprobar_parcial'                                  │
│     │   └─> role_can_show = 3 (Gerente Comercial) o 15 (Gte Gral)  │
│     │   └─> action_made = 'Aprobación Parcial'                     │
│     │                                                                │
│     └─> action = 'rechazar'                                         │
│         └─> estado_id = 6 (Rechazado)                              │
│         └─> action_made = 'Rechazo'                                │
│                                                                      │
│  5. Registro queda en cotizacion_approvals                          │
│     └─> GET /historial-aprobaciones para ver timeline              │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

### 31.8 Estados de Cotizacion

| estado_id | Nombre | Descripcion |
|-----------|--------|-------------|
| 1 | Borrador | Cotizacion en edicion |
| 2 | Por Aprobar | Pendiente de aprobacion |
| 3 | Aprobado | Cotizacion aprobada |
| 4 | Vigente | Cotizacion activa |
| 5 | Ganada | Cotizacion ganada |
| 6 | Rechazado | Cotizacion rechazada |
| 7 | Perdida | Cotizacion perdida |

### 31.9 Roles de Aprobacion

| role_can_show | Rol | Descripcion |
|---------------|-----|-------------|
| 3 | Gerente Comercial | Aprueba montos intermedios |
| 4 | Externo | Usuario externo (vendedor) |
| 15 | Gerente General | Aprueba montos altos |

---

## 32. FASE 6.21 - Modulo de Muestras

**Fecha**: 2025-12-20
**Estado**: COMPLETADO

### 32.1 Descripcion

Implementacion del modulo de Muestras para gestionar las muestras asociadas a ordenes de trabajo. Este modulo permite crear, listar y gestionar el flujo de estados de las muestras.

### 32.2 Estados de Muestra

| Estado | Codigo | Descripcion |
|--------|--------|-------------|
| Sin Asignar | 0 | Muestra recien creada |
| En Proceso | 1 | Muestra en produccion |
| Rechazada | 2 | Muestra rechazada |
| Terminada | 3 | Muestra completada |
| Anulada | 4 | Muestra anulada |
| Devuelta | 5 | Muestra devuelta |
| Sala de Corte | 6 | En sala de corte |

### 32.3 Backend - Router de Muestras

**Archivo**: `src/app/routers/muestras.py`

**Endpoints implementados:**

| Metodo | Endpoint | Descripcion |
|--------|----------|-------------|
| GET | `/muestras/ot/{ot_id}` | Lista muestras de una OT |
| GET | `/muestras/options` | Opciones para formulario (salas, cads, cartones) |
| GET | `/muestras/{id}` | Detalle de muestra |
| POST | `/muestras/` | Crear muestra |
| PUT | `/muestras/{id}/terminar` | Marcar como terminada (estado 3) |
| PUT | `/muestras/{id}/rechazar` | Rechazar muestra (estado 2) |
| PUT | `/muestras/{id}/anular` | Anular muestra (estado 4) |
| PUT | `/muestras/{id}/devolver` | Devolver muestra (estado 5) |
| PUT | `/muestras/{id}/prioritaria` | Alternar prioridad |
| PUT | `/muestras/{id}/sala-corte` | Asignar sala de corte (estado 6) |
| PUT | `/muestras/{id}/en-proceso` | Iniciar proceso (estado 1) |
| DELETE | `/muestras/{id}` | Eliminar (solo si estado 0) |

### 32.4 Frontend - API de Muestras

**Archivo**: `frontend/src/services/api.ts`

```typescript
// Estados de muestra
export const ESTADO_MUESTRA = {
  0: 'Sin Asignar',
  1: 'En Proceso',
  2: 'Rechazada',
  3: 'Terminada',
  4: 'Anulada',
  5: 'Devuelta',
  6: 'Sala de Corte',
} as const;

// API de muestras
export const muestrasApi = {
  listByOT: (otId: number) => Promise<MuestraListResponse>,
  getOptions: () => Promise<MuestraOptions>,
  get: (id: number) => Promise<MuestraDetalle>,
  create: (data: MuestraCreate) => Promise<MuestraCreateResponse>,
  terminar: (id: number) => Promise<MuestraActionResponse>,
  rechazar: (id: number, observacion?: string) => Promise<MuestraActionResponse>,
  anular: (id: number) => Promise<MuestraActionResponse>,
  devolver: (id: number, observacion?: string) => Promise<MuestraActionResponse>,
  togglePrioritaria: (id: number) => Promise<MuestraActionResponse>,
  delete: (id: number) => Promise<{ message: string }>,
  asignarSalaCorte: (id: number, salaCorteId: number) => Promise<MuestraActionResponse>,
  iniciarProceso: (id: number) => Promise<MuestraActionResponse>,
};
```

### 32.5 Componentes Frontend

**Ubicacion**: `frontend/src/pages/Muestras/`

| Componente | Descripcion |
|------------|-------------|
| `MuestrasList.tsx` | Lista de muestras de una OT con acciones |
| `MuestraForm.tsx` | Formulario para crear nueva muestra |
| `index.ts` | Exportaciones del modulo |

**Caracteristicas de MuestrasList:**

- Lista todas las muestras de una OT
- Badges de estado con colores
- Indicador de prioridad (estrella)
- Acciones contextuales segun estado
- Modales para rechazar, devolver y asignar sala
- Paginacion y ordenamiento

**Caracteristicas de MuestraForm:**

- Seleccion de sala de corte, CAD y carton
- Observaciones
- 5 destinos de muestras:
  - Vendedor (nombre, direccion, ciudad, cantidad)
  - Disenador (nombre, direccion, ciudad, cantidad)
  - Laboratorio (cantidad)
  - Cliente (cantidad)
  - Disenador Revision (nombre, direccion, cantidad)
- Validacion de al menos una cantidad

### 32.6 Integracion en App.tsx

**Cambios realizados:**

1. **Imports:**
```typescript
import { MuestrasList, MuestraForm } from './pages/Muestras';
```

2. **PageTypes agregados:**
```typescript
| 'muestras-list' | 'muestra-nueva'
```

3. **Estado para OT de muestras:**
```typescript
const [muestrasOtId, setMuestrasOtId] = useState<number | null>(null);
```

4. **Navegacion:**
```typescript
} else if ((page === 'muestras-list' || page === 'muestra-nueva') && id) {
  setMuestrasOtId(id);
  // reset otros estados...
}
```

5. **Titulos:**
```typescript
case 'muestras-list': return `Muestras de OT #${muestrasOtId}`;
case 'muestra-nueva': return `Nueva Muestra para OT #${muestrasOtId}`;
```

6. **Render:**
```typescript
case 'muestras-list':
  return muestrasOtId ? (
    <MuestrasList
      otId={muestrasOtId}
      onNavigate={handleNavigate}
      onCreateMuestra={() => handleNavigate('muestra-nueva', muestrasOtId)}
    />
  ) : null;
case 'muestra-nueva':
  return muestrasOtId ? (
    <MuestraForm
      otId={muestrasOtId}
      onNavigate={handleNavigate}
      onSuccess={() => handleNavigate('muestras-list', muestrasOtId)}
    />
  ) : null;
```

### 32.7 Flujo de Muestras

```
┌─────────────────────────────────────────────────────────────────────┐
│                       FLUJO DE MUESTRAS                              │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  1. Usuario accede a OT en gestion                                   │
│     └─> Clic en "Muestras" o navegacion                             │
│                                                                      │
│  2. Lista de muestras de la OT                                       │
│     └─> GET /muestras/ot/{ot_id}                                    │
│     └─> Muestra tabla con estados y acciones                        │
│                                                                      │
│  3. Crear nueva muestra                                              │
│     └─> POST /muestras/                                             │
│     └─> Estado inicial: 0 (Sin Asignar)                             │
│                                                                      │
│  4. Gestionar muestra segun estado:                                  │
│     │                                                                │
│     ├─> Estado 0 (Sin Asignar)                                      │
│     │   ├─> Asignar sala de corte → Estado 6                        │
│     │   └─> Eliminar (DELETE)                                       │
│     │                                                                │
│     ├─> Estado 6 (Sala de Corte)                                    │
│     │   └─> Iniciar proceso → Estado 1                              │
│     │                                                                │
│     ├─> Estado 1 (En Proceso)                                       │
│     │   ├─> Terminar → Estado 3                                     │
│     │   ├─> Rechazar → Estado 2                                     │
│     │   └─> Devolver → Estado 5                                     │
│     │                                                                │
│     └─> Estados 3, 4 (Terminada, Anulada)                          │
│         └─> Sin acciones adicionales                                │
│                                                                      │
│  5. Prioridad                                                        │
│     └─> Toggle en cualquier estado (excepto 3, 4)                   │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

### 32.8 Acceso al Modulo

El modulo de muestras se accede desde:

1. **Navegacion directa:** `handleNavigate('muestras-list', otId)`
2. **Desde ManageWorkOrder:** Boton o enlace a muestras de la OT

---

## 33. FASE 6.22 - Carga Masiva de Mantenedores

### 33.1 Objetivo

Implementar funcionalidad de carga masiva de datos para mantenedores, permitiendo importar registros desde archivos Excel (.xlsx, .xls) y CSV.

### 33.2 Dependencias Agregadas

```json
// package.json
{
  "dependencies": {
    "xlsx": "^0.18.5"
  }
}
```

### 33.3 Backend - Endpoint Existente

El endpoint ya existe en FastAPI:

```python
# src/app/routers/mantenedores/generic.py
@router.post("/{tabla_key}/bulk", response_model=BulkUploadResult)
async def bulk_upload(
    tabla_key: str,
    request: BulkUploadRequest,
    current_user: dict = Depends(get_current_user)
)
```

**Request:**
```python
class BulkUploadRequest(BaseModel):
    items: List[Dict[str, Any]]
```

**Response:**
```python
class BulkUploadResult(BaseModel):
    total_recibidos: int
    insertados: int
    errores: int
    detalles_errores: List[Dict[str, Any]]
```

### 33.4 Frontend - API Client

```typescript
// src/services/api.ts
export const genericApi = {
  bulkUpload: async (tablaKey: string, items: Array<{ data: Record<string, unknown> }>): Promise<{
    total_recibidos: number;
    insertados: number;
    errores: number;
    detalles_errores: Array<{ fila: number; error: string; data?: Record<string, unknown> }>;
  }> => {
    const response = await api.post(`/mantenedores/generic/${tablaKey}/bulk`, { items });
    return response.data;
  },
};
```

### 33.5 Componente BulkUpload

**Ubicacion:** `src/pages/Mantenedores/BulkUpload.tsx`

**Caracteristicas:**

1. **Soporte de formatos:**
   - Excel (.xlsx, .xls) - usando libreria `xlsx`
   - CSV (con manejo de comillas y separadores)

2. **Flujo de 3 pasos:**
   - Paso 1: Seleccionar mantenedor y archivo
   - Paso 2: Validar y previsualizar datos
   - Paso 3: Ver resultados de la carga

3. **Funcionalidades:**
   - Drag & Drop para archivos
   - Descarga de plantilla Excel/CSV
   - Validacion previa de datos requeridos
   - Reporte de errores por fila
   - Estadisticas de carga (exitosos, fallidos, tasa)

### 33.6 Procesamiento de Excel

```typescript
// Lectura de archivo Excel
const processFile = async (selectedFile: File) => {
  if (selectedFile.name.match(/\.(xlsx|xls)$/i)) {
    const buffer = await selectedFile.arrayBuffer();
    const workbook = XLSX.read(buffer, { type: 'array' });
    const firstSheetName = workbook.SheetNames[0];
    const worksheet = workbook.Sheets[firstSheetName];

    const jsonData = XLSX.utils.sheet_to_json<string[]>(worksheet, {
      header: 1,
      raw: false,
      defval: ''
    });
    rows = jsonData as string[][];
  } else {
    // CSV parsing
    const text = await selectedFile.text();
    rows = parseCSV(text);
  }
};
```

### 33.7 Generacion de Plantilla Excel

```typescript
const downloadTemplate = (format: 'csv' | 'xlsx' = 'xlsx') => {
  if (format === 'xlsx') {
    const worksheetData = [columns];
    const worksheet = XLSX.utils.aoa_to_sheet(worksheetData);
    worksheet['!cols'] = columns.map(() => ({ wch: 20 }));

    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, tablaInfo.display_name);

    XLSX.writeFile(workbook, `plantilla_${selectedTabla}.xlsx`);
  }
};
```

### 33.8 Integracion en App.tsx

```typescript
// PageType
type PageType = ... | 'carga-masiva';

// Titulo
case 'carga-masiva': return 'Carga Masiva de Mantenedores';

// Render
case 'carga-masiva':
  return <BulkUpload onNavigate={handleNavigate} />;
```

### 33.9 Mantenedores Soportados (70+)

La carga masiva soporta todos los mantenedores genericos incluyendo:

- Canales, Mercados, Organizaciones de Ventas
- Tipos de Producto, Materiales, Cartones, Papeles
- Procesos, Pegados, Armados, Envases
- Colores, Estilos, Matrices
- Plantas, Almacenes, Cebes
- Adhesivos, Consumos
- Fletes, Ciudades, Maquila
- Tarifarios, Variables Cotizador
- Y muchos mas...

---

## 34. FASE 6.23 - Exportacion Excel/SAP de OTs

### 34.1 Objetivo

Implementar funcionalidad de exportacion de OTs a formatos Excel y SAP para descarga directa desde el dashboard.

### 34.2 Backend - Nuevo Router exports.py

**Ubicacion:** `src/app/routers/exports.py`

**Endpoints:**

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/exports/ot/{ot_id}/excel` | GET | Descarga OT individual en Excel |
| `/exports/ot/{ot_id}/sap` | GET | Descarga OT en formato SAP |
| `/exports/ots/excel` | GET | Descarga lista de OTs filtrada |

### 34.3 Formato Excel Estandar

```
DATOS DE LA ORDEN DE TRABAJO
- Numero OT, Fecha Creacion, Estado, Creador

DATOS COMERCIALES
- Cliente, Codigo SAP, Descripcion, Canal, Tipo Producto

DIMENSIONES
- Interiores (Largo, Ancho, Alto)
- Sustrato (Largura, Anchura)
- Exteriores (Largo, Ancho, Alto)

ESPECIFICACIONES TECNICAS
- Carton, Estilo, Rayado, Proceso, Armado, Pegado, Adhesivo

CALIDAD
- Gramaje, ECT, FCT, BCT, Mullen

IMPRESION
- Numero de Colores, Tipo Impresion, Colores y Consumos

PALETIZADO
- Planta, Tipo Palet, Cajas por Palet, FSC
```

### 34.4 Formato SAP (Vertical)

```
| Campo SAP          | Descripcion           | Valor        |
|--------------------|-----------------------|--------------|
| MATNR              | Numero de Material    | GE1XXXXX     |
| EN_PLANCHA_SEMI    | Material Semielab.    | GE2XXXXX     |
| MAKTX              | Descripcion Comercial | ...          |
| PRDHA              | Jerarquia SAP         | ...          |
| WERKS              | Centro                | ...          |
| EN_LARGO           | Largo Interior (MM)   | ...          |
| EN_ANCHO           | Ancho Interior (MM)   | ...          |
| EN_COLOR_COMP_1    | Color 1               | ...          |
| EN_CONSUMO_1       | Consumo Color 1 (G)   | ...          |
| ... (70+ campos SAP)                                       |
```

### 34.5 Dependencias Backend

```
# requirements.txt
openpyxl==3.1.2
```

### 34.6 Frontend - API Client

```typescript
// src/services/api.ts
export const exportsApi = {
  downloadOTExcel: async (otId: number): Promise<void> => {
    const response = await api.get(`/exports/ot/${otId}/excel`, {
      responseType: 'blob'
    });
    // Crear link y descargar
  },

  downloadOTSAP: async (otId: number): Promise<void> => {
    const response = await api.get(`/exports/ot/${otId}/sap`, {
      responseType: 'blob'
    });
    // Crear link y descargar
  },

  downloadOTsListExcel: async (filters): Promise<void> => {
    const response = await api.get(`/exports/ots/excel?${params}`, {
      responseType: 'blob'
    });
    // Crear link y descargar
  },
};
```

### 34.7 Integracion en WorkOrdersDashboard

**Boton de exportacion masiva:**
```tsx
<button
  className="btn btn-secondary"
  onClick={handleExportExcel}
  disabled={exporting}
>
  {exporting ? 'Exportando...' : 'Exportar Excel'}
</button>
```

**Botones por fila:**
```tsx
<button onClick={() => exportsApi.downloadOTExcel(ot.id)} title="Descargar Excel">
  📥
</button>
<button onClick={() => exportsApi.downloadOTSAP(ot.id)} title="Descargar SAP">
  📊
</button>
```

### 34.8 Campos SAP Exportados (Principales)

- **Identificacion:** MATNR, EN_PLANCHA_SEMI, MAKTX, PRDHA, PRODH
- **Centro:** WERKS, LGORT, VKORG
- **Dimensiones:** EN_LARGO, EN_ANCHO, EN_ALTO, EN_LARGURA, EN_ANCHURA, etc.
- **Especificaciones:** EN_CARTON, EN_TIPO, EN_ESTILO, EN_GRAMAJE_G_M2
- **Calidad:** EN_ECT_MINIMO, EN_FCT_MINIMO, EN_MULLEN, EN_RESISTENCIA_MT
- **Impresion:** EN_TIPOIMPRESION, EN_COLORES, EN_COLOR_COMP_1-7
- **Consumos:** EN_CONSUMO_1-7, EN_ADHESIVO, EN_CONSUMO_ADH
- **Paletizado:** EN_TIPO_PALET_GE, EN_CAJAS_POR_PALET
- **Certificaciones:** EN_SELLO (FSC)

---

## 35. FASE 6.24 - Generacion de PDFs

### 35.1 Objetivo

Implementar generacion de documentos PDF para etiquetas, fichas tecnicas, estudios de benchmarking y cotizaciones.

### 35.2 Backend - Nuevo Router

**Archivo:** `src/app/routers/pdfs.py`

**Dependencia:**
```
# requirements.txt
reportlab==4.0.8
```

### 35.3 Endpoints Implementados

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/pdfs/etiqueta-muestra/{muestra_id}` | GET | Etiqueta de muestra (producto 10x10cm o cliente A4) |
| `/pdfs/ficha-diseno/{ot_id}` | GET | Ficha de diseno con datos tecnicos de OT |
| `/pdfs/estudio-bench/{ot_id}` | GET | Solicitud de estudio de benchmarking |
| `/pdfs/cotizacion/{cotizacion_id}` | GET | PDF de cotizacion comercial |

### 35.4 Tipos de PDFs

#### Etiqueta de Producto (10x10 cm)
```
┌────────────────────────┐
│  ETIQUETA DE MUESTRA   │
│------------------------│
│  OT: 12345             │
│  CAD: ABC-001          │
│  Cliente: ACME S.A.    │
│  Descripcion: Caja...  │
│                        │
│  Dim. Int: 300x200x150 │
│  Dim. Ext: 310x210x160 │
│                        │
│  Carton: BC            │
│  Onda: C               │
│  Disenador: Juan Perez │
│  Fecha: 21/12/2025     │
└────────────────────────┘
```

#### Etiqueta de Cliente (A4)
- Datos de envio con destinatario, direccion, OT y muestra ID

#### Ficha de Diseno (A4)
- Datos Comerciales (vendedor, cliente, material, CAD)
- Caracteristicas (carton, estilo, tipo onda, proceso)
- Medidas (interiores y exteriores)
- Colores (lista con consumos)
- Paletizado (planta, tipo palet, cajas/palet)

#### Estudio de Benchmarking (A4)
- Datos de la OT
- Identificacion de muestra
- Ensayos solicitados (BCT, ECT, Humedad, etc.)
- Observaciones
- Firmas (solicitante y laboratorio)

#### Cotizacion Comercial (Letter)
- Datos del cliente (nombre, RUT, direccion)
- Ejecutivo comercial
- Detalle de productos
- Estado de la cotizacion

### 35.5 Frontend - API Client

```typescript
// src/services/api.ts
export const pdfsApi = {
  downloadEtiquetaMuestra: async (muestraId: number, tipo: 'producto' | 'cliente'): Promise<void> => {
    const response = await api.get(`/pdfs/etiqueta-muestra/${muestraId}?tipo=${tipo}`, {
      responseType: 'blob'
    });
    // Crear link y descargar
  },

  downloadFichaDiseno: async (otId: number): Promise<void> => {
    const response = await api.get(`/pdfs/ficha-diseno/${otId}`, {
      responseType: 'blob'
    });
    // Crear link y descargar
  },

  downloadEstudioBench: async (otId: number): Promise<void> => {
    const response = await api.get(`/pdfs/estudio-bench/${otId}`, {
      responseType: 'blob'
    });
    // Crear link y descargar
  },

  downloadCotizacion: async (cotizacionId: number): Promise<void> => {
    const response = await api.get(`/pdfs/cotizacion/${cotizacionId}`, {
      responseType: 'blob'
    });
    // Crear link y descargar
  },
};
```

### 35.6 Integracion en Componentes

#### ManageWorkOrder
```tsx
<HeaderActions>
  <PdfButton onClick={() => pdfsApi.downloadFichaDiseno(otId)}>
    Ficha Diseño
  </PdfButton>
  <PdfButton onClick={() => pdfsApi.downloadEstudioBench(otId)}>
    Est. Bench
  </PdfButton>
</HeaderActions>
```

#### MuestrasList
```tsx
// Por cada muestra en la tabla
<ActionButton onClick={() => pdfsApi.downloadEtiquetaMuestra(item.id, 'producto')}>
  Etq
</ActionButton>
<ActionButton onClick={() => pdfsApi.downloadEtiquetaMuestra(item.id, 'cliente')}>
  Envío
</ActionButton>
```

#### CotizacionesList
```tsx
<ActionButton onClick={() => pdfsApi.downloadCotizacion(cotizacion.id)}>
  PDF
</ActionButton>
```

### 35.7 Estructura del Router pdfs.py

```python
from reportlab.lib import colors
from reportlab.lib.pagesizes import A4, letter
from reportlab.lib.units import mm, cm
from reportlab.platypus import SimpleDocTemplate, Paragraph, Table, TableStyle

router = APIRouter(prefix="/pdfs", tags=["PDFs"])

@router.get("/etiqueta-muestra/{muestra_id}")
async def generar_etiqueta_muestra(muestra_id: int, tipo: str = "producto")

@router.get("/ficha-diseno/{ot_id}")
async def generar_ficha_diseno(ot_id: int)

@router.get("/estudio-bench/{ot_id}")
async def generar_estudio_benchmarking(ot_id: int)

@router.get("/cotizacion/{cotizacion_id}")
async def generar_cotizacion_pdf(cotizacion_id: int)
```

---

## 36. FASE 6.25 - Cascadas AJAX para Selectores Dependientes

### 36.1 Objetivo

Implementar endpoints de cascadas AJAX para selectores dependientes, replicando la funcionalidad de Laravel para:
- Cliente → Instalaciones
- Instalacion → Contactos
- Tipo Producto → Servicios Maquila
- Jerarquias con filtro por Rubro

### 36.2 Backend - Nuevo Router

**Archivo:** `src/app/routers/cascades.py`

```python
router = APIRouter(prefix="/cascades", tags=["Cascadas AJAX"])
```

### 36.3 Endpoints Implementados

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/cascades/clientes/{client_id}/instalaciones` | GET | Instalaciones de un cliente |
| `/cascades/clientes/{client_id}/instalaciones-cotiza` | GET | Instalaciones + clasificacion (para cotizaciones) |
| `/cascades/clientes/{client_id}/contactos` | GET | Contactos del cliente (opcionalmente por instalacion) |
| `/cascades/instalaciones/{instalacion_id}` | GET | Info completa de instalacion + contactos |
| `/cascades/contactos/{contacto_id}` | GET | Datos completos de un contacto |
| `/cascades/productos/{tipo_producto_id}/servicios-maquila` | GET | Servicios maquila segun tipo producto |
| `/cascades/jerarquias/{subsubhierarchy_id}/rubro` | GET | Obtiene rubro_id de jerarquia nivel 3 |
| `/cascades/jerarquias/nivel2-rubro` | GET | Jerarquias nivel 2 filtradas por rubro |
| `/cascades/jerarquias/nivel3-rubro` | GET | Jerarquias nivel 3 filtradas por rubro |

### 36.4 Schemas Pydantic

```python
class SelectOption(BaseModel):
    id: int
    nombre: str

class InstalacionOption(BaseModel):
    id: int
    nombre: str
    direccion: Optional[str] = None

class ContactoOption(BaseModel):
    id: int
    nombre: str
    email: Optional[str] = None
    telefono: Optional[str] = None

class InstalacionInfo(BaseModel):
    contactos: List[ContactoOption]
    tipo_pallet_id: Optional[int] = None
    altura_pallet: Optional[float] = None
    sobresalir_carga: Optional[int] = None
    bulto_zunchado: Optional[int] = None
    formato_etiqueta: Optional[str] = None
    etiquetas_pallet: Optional[int] = None
    termocontraible: Optional[int] = None
    fsc: Optional[int] = None
    pais_mercado_destino: Optional[str] = None
    certificado_calidad: Optional[int] = None

class ContactoInfo(BaseModel):
    nombre_contacto: str
    email_contacto: Optional[str] = None
    telefono_contacto: Optional[str] = None
    comuna_contacto: Optional[str] = None
    direccion_contacto: Optional[str] = None

class ClienteCotizaResponse(BaseModel):
    instalaciones: List[InstalacionOption]
    clasificacion_id: Optional[int] = None
    clasificacion_nombre: Optional[str] = None
```

### 36.5 Mapeo Tipo Producto → Servicios Maquila

```python
mapeos = {
    # Plancha (16) → Paletizado Placas (18)
    16: [18],
    # Caja RSC, Fondo, Tapa, Especial → PM CJ Chica/Mediana/Grande
    3: [15, 16, 17], 4: [15, 16, 17], 5: [15, 16, 17], 15: [15, 16, 17],
    # Caja Bipartida → Pegado Especial
    31: [21],
    # Tabiques → Armado y Paletizado
    18: [19, 20], 33: [19, 20], 20: [19, 20], 19: [19, 20],
    # Wrap Around → Wrap Around
    17: [22], 32: [22], 35: [22],
    # PAD → Paletizado Placas
    6: [18],
    # Esquinero, Zuncho, Bobina, Display → Solo Paletizado
    7: [23], 8: [23], 10: [23], 11: [23],
}
```

### 36.6 Frontend - API Client

```typescript
// src/services/api.ts

// Types
export interface InstalacionOption {
  id: number;
  nombre: string;
  direccion?: string;
}

export interface ContactoOption {
  id: number;
  nombre: string;
  email?: string;
  telefono?: string;
}

export interface InstalacionInfo {
  contactos: ContactoOption[];
  tipo_pallet_id?: number;
  altura_pallet?: number;
  sobresalir_carga?: number;
  bulto_zunchado?: number;
  formato_etiqueta?: string;
  etiquetas_pallet?: number;
  termocontraible?: number;
  fsc?: number;
  pais_mercado_destino?: string;
  certificado_calidad?: number;
}

export interface ContactoInfo {
  nombre_contacto: string;
  email_contacto?: string;
  telefono_contacto?: string;
  comuna_contacto?: string;
  direccion_contacto?: string;
}

export interface ClienteCotizaResponse {
  instalaciones: InstalacionOption[];
  clasificacion_id?: number;
  clasificacion_nombre?: string;
}

export interface SelectOption {
  id: number;
  nombre: string;
}

// API Methods
export const cascadesApi = {
  getInstalacionesCliente: async (clientId: number): Promise<InstalacionOption[]> => {
    const response = await api.get(`/cascades/clientes/${clientId}/instalaciones`);
    return response.data;
  },

  getInstalacionesClienteCotiza: async (clientId: number): Promise<ClienteCotizaResponse> => {
    const response = await api.get(`/cascades/clientes/${clientId}/instalaciones-cotiza`);
    return response.data;
  },

  getContactosCliente: async (clientId: number, instalacionId?: number): Promise<ContactoOption[]> => {
    const params = instalacionId ? `?instalacion_id=${instalacionId}` : '';
    const response = await api.get(`/cascades/clientes/${clientId}/contactos${params}`);
    return response.data;
  },

  getInformacionInstalacion: async (instalacionId: number): Promise<InstalacionInfo> => {
    const response = await api.get(`/cascades/instalaciones/${instalacionId}`);
    return response.data;
  },

  getDatosContacto: async (contactoId: number): Promise<ContactoInfo> => {
    const response = await api.get(`/cascades/contactos/${contactoId}`);
    return response.data;
  },

  getServiciosMaquila: async (tipoProductoId: number): Promise<SelectOption[]> => {
    const response = await api.get(`/cascades/productos/${tipoProductoId}/servicios-maquila`);
    return response.data;
  },

  getRubro: async (subsubhierarchyId: number): Promise<{ rubro_id: number | null }> => {
    const response = await api.get(`/cascades/jerarquias/${subsubhierarchyId}/rubro`);
    return response.data;
  },

  getJerarquia2Rubro: async (hierarchyId: number, rubroId?: number): Promise<SelectOption[]> => {
    const params = rubroId ? `&rubro_id=${rubroId}` : '';
    const response = await api.get(`/cascades/jerarquias/nivel2-rubro?hierarchy_id=${hierarchyId}${params}`);
    return response.data;
  },

  getJerarquia3Rubro: async (subhierarchyId: number, rubroId?: number): Promise<SelectOption[]> => {
    const params = rubroId ? `&rubro_id=${rubroId}` : '';
    const response = await api.get(`/cascades/jerarquias/nivel3-rubro?subhierarchy_id=${subhierarchyId}${params}`);
    return response.data;
  },
};
```

### 36.7 Casos de Uso

#### Formulario de Cotizacion
1. Usuario selecciona Cliente
2. Se cargan Instalaciones con `getInstalacionesClienteCotiza()`
3. Se obtiene clasificacion del cliente para calculos
4. Al seleccionar Instalacion, se cargan Contactos

#### Formulario de OT Especial
1. Usuario selecciona Tipo Producto
2. Se cargan Servicios Maquila con `getServiciosMaquila()`
3. Al seleccionar Jerarquia3, se obtiene Rubro con `getRubro()`
4. Se filtran Jerarquias por Rubro automaticamente

#### Datos de Entrega
1. Al seleccionar Instalacion
2. Se cargan datos por defecto con `getInformacionInstalacion()`
3. Se pre-llenan campos de paletizado, etiquetas, FSC, etc.

### 36.8 Equivalencia Laravel → FastAPI

| Laravel (DetalleCotizacionController) | FastAPI (cascades.py) |
|---------------------------------------|----------------------|
| `getInstalacionesCliente()` | `/cascades/clientes/{id}/instalaciones` |
| `getInstalacionesClienteCotiza()` | `/cascades/clientes/{id}/instalaciones-cotiza` |
| `getContactosCliente()` | `/cascades/clientes/{id}/contactos` |
| `getInformacionInstalacion()` | `/cascades/instalaciones/{id}` |
| `getDatosContacto()` | `/cascades/contactos/{id}` |
| `getServiciosMaquila()` | `/cascades/productos/{id}/servicios-maquila` |
| `getRubro()` | `/cascades/jerarquias/{id}/rubro` |
| `getJerarquia2AreaHC()` | `/cascades/jerarquias/nivel2-rubro` |
| `getJerarquia3ConRubro()` | `/cascades/jerarquias/nivel3-rubro` |

---

## 37. FASE 6.26 - Exportacion Log OT (Bitacora) a Excel

### 37.1 Objetivo

Implementar la exportacion del historial de modificaciones de una OT (bitacora) a formato Excel, permitiendo filtrar por fechas y usuarios.

### 37.2 Backend - Nuevos Endpoints

**Archivo:** `src/app/routers/exports.py`

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/exports/ot/{ot_id}/log` | GET | Obtiene log de modificaciones paginado |
| `/exports/ot/{ot_id}/log/excel` | GET | Descarga log en formato Excel |

### 37.3 Parametros de Filtrado

| Parametro | Tipo | Descripcion |
|-----------|------|-------------|
| `date_desde` | string | Fecha inicio (dd/mm/yyyy) |
| `date_hasta` | string | Fecha fin (dd/mm/yyyy) |
| `user_id` | int | Filtrar por usuario |
| `page` | int | Pagina actual |
| `page_size` | int | Items por pagina |

### 37.4 Respuesta JSON (/log)

```typescript
{
  items: Array<{
    id: number;
    work_order_id: number;
    operacion: string;
    observacion: string;
    datos_modificados: Record<string, unknown>;
    usuario: string;
    created_at: string;
  }>;
  total: number;
  page: number;
  page_size: number;
  total_pages: number;
  usuarios_filtro: Array<{ id: number; nombre: string }>;
}
```

### 37.5 Formato Excel Exportado

| Columna | Descripcion |
|---------|-------------|
| OT | Numero de OT |
| ID Cambio | ID del registro de bitacora |
| Fecha | Fecha y hora del cambio |
| Descripcion | Descripcion del cambio |
| Campo Modificado | Nombre del campo afectado |
| Valor Antiguo | Valor antes del cambio |
| Valor Nuevo | Valor despues del cambio |
| Usuario | Nombre del usuario que realizo el cambio |

### 37.6 Frontend - API Client

```typescript
// src/services/api.ts

export const exportsApi = {
  // ... otros metodos

  getOTLog: async (otId: number, filters: {
    date_desde?: string;
    date_hasta?: string;
    user_id?: number;
    page?: number;
    page_size?: number;
  } = {}): Promise<LogResponse> => {
    const params = new URLSearchParams();
    // ... construir params
    const response = await api.get(`/exports/ot/${otId}/log?${params.toString()}`);
    return response.data;
  },

  downloadOTLogExcel: async (otId: number, filters: {
    date_desde?: string;
    date_hasta?: string;
    user_id?: number;
  } = {}): Promise<void> => {
    const response = await api.get(`/exports/ot/${otId}/log/excel?${params}`, {
      responseType: 'blob'
    });
    // Crear link y descargar
  },
};
```

### 37.7 Tabla de Bitacora

La informacion se extrae de la tabla `bitacora_work_orders`:
- `operacion`: Tipo de operacion (Modificacion, Mckee)
- `datos_modificados`: JSON con campos antiguos y nuevos
- `user_data`: JSON con info del usuario
- `observacion`: Descripcion del cambio

---

## 38. FASE 6.27 - Carga Masiva Tablas del Cotizador

### 38.1 Objetivo

Implementar un sistema de carga masiva via Excel para las tablas del motor de cotizacion, con validaciones especificas, lookups de IDs y preview antes de ejecutar.

### 38.2 Backend - Nuevo Router

**Archivo:** `src/app/routers/bulk_cotizador.py`

### 38.3 Endpoints Implementados

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/bulk-cotizador/tablas` | GET | Lista tablas disponibles |
| `/bulk-cotizador/plantilla/{tabla_key}` | GET | Descarga plantilla Excel |
| `/bulk-cotizador/cargar/{tabla_key}` | POST | Carga archivo Excel |
| `/bulk-cotizador/descargar/{tabla_key}` | GET | Descarga datos actuales |

### 38.4 Tablas Soportadas (17 tablas)

| Clave | Tabla BD | Descripcion |
|-------|----------|-------------|
| `cartones` | cartons | Cartones Corrugados |
| `papeles` | papers | Papeles |
| `fletes` | fletes | Fletes por ciudad |
| `merma_corrugadoras` | merma_corrugadoras | Mermas Corrugadoras |
| `merma_convertidoras` | merma_convertidoras | Mermas Convertidoras |
| `tarifario` | tarifario | Tarifarios |
| `consumo_adhesivos` | consumo_adhesivos | Consumo Adhesivos |
| `consumo_energias` | consumo_energias | Consumo Energias |
| `factores_ondas` | factores_ondas | Factores de Ondas |
| `factores_desarrollos` | factores_desarrollos | Factores de Desarrollos |
| `factores_seguridads` | factores_seguridads | Factores de Seguridad |
| `maquila_servicios` | maquila_servicios | Maquilas/Servicios |
| `plantas` | plantas | Plantas |
| `tipo_ondas` | tipo_ondas | Tipos de Ondas |
| `variables_cotizador` | variables_cotizadors | Variables Cotizador |
| `carton_esquineros` | carton_esquineros | Cartones Esquineros |
| `insumos_palletizados` | insumos_palletizados | Insumos Paletizados |
| `tarifario_margens` | tarifario_margens | Tarifario Margenes |

### 38.5 Flujo de Carga Masiva

1. **Descargar Plantilla:** Usuario descarga Excel con columnas correctas
2. **Llenar Datos:** Usuario completa el archivo con los datos
3. **Preview:** Sube archivo con `modo=preview` para validar
4. **Revision:** Sistema muestra items nuevos, actualizados y errores
5. **Ejecutar:** Si todo OK, envia con `modo=ejecutar` para guardar

### 38.6 Validaciones Especificas

#### Cartones Corrugados
- Ondas validas: C, CB, CE, B, BE, E, P, P-BC, EB, EC, BC
- Tipos validos: SIMPLES, DOBLES, DOBLE MONOTAPA, POWER PLY, SIMPLE EMPLACADO
- Colores validos: blanco, cafe
- Lookup de codigos de papeles en tabla `papers`

#### Fletes
- Lookup de planta por nombre -> planta_id
- Lookup de ciudad por nombre -> ciudad_id

#### Mermas
- Lookup de planta, proceso, rubro, carton segun corresponda

### 38.7 Respuesta de Carga

```typescript
interface BulkResult {
  total_filas: number;      // Total filas procesadas
  insertados: number;       // Registros nuevos
  actualizados: number;     // Registros actualizados
  errores: number;          // Filas con error
  items_nuevos: Array<{linea: number, ...data}>;
  items_actualizados: Array<{linea: number, id: number, ...data}>;
  items_error: Array<{linea: number, errores: string[], datos: object}>;
}
```

### 38.8 Frontend - API Client

```typescript
// src/services/api.ts

export const bulkCotizadorApi = {
  getTablas: async (): Promise<TablaCotizador[]>,
  downloadPlantilla: async (tablaKey: string): Promise<void>,
  cargarArchivo: async (tablaKey: string, archivo: File, modo: 'preview' | 'ejecutar'): Promise<BulkResult>,
  downloadDatosActuales: async (tablaKey: string): Promise<void>,
};
```

### 38.9 Equivalencia Laravel → FastAPI

| Laravel (MantenedorController) | FastAPI (bulk_cotizador.py) |
|--------------------------------|----------------------------|
| `cargaCartonsForm()` | GET `/bulk-cotizador/plantilla/cartones` |
| `importCartons()` | POST `/bulk-cotizador/cargar/cartones` |
| `descargar_excel_cartones_corrugados()` | GET `/bulk-cotizador/descargar/cartones` |
| `importPapeles()` | POST `/bulk-cotizador/cargar/papeles` |
| `importFletes()` | POST `/bulk-cotizador/cargar/fletes` |
| `importMermasCorrugadoras()` | POST `/bulk-cotizador/cargar/merma_corrugadoras` |
| `importMermasConvertidoras()` | POST `/bulk-cotizador/cargar/merma_convertidoras` |
| `importTarifarios()` | POST `/bulk-cotizador/cargar/tarifario` |
| Y 10+ mas... | Usando misma estructura |

---

## 39. FASE 6.28 - Asignaciones con Mensaje

### 39.1 Objetivo

Mejorar el sistema de asignaciones de OTs para soportar mensajes/observaciones al asignar, generar notificaciones automaticamente y registrar en la tabla user_work_orders.

### 39.2 Backend - Endpoint Mejorado

**Archivo:** `src/app/routers/work_orders.py`

**Endpoint:** `PUT /work-orders/{ot_id}/assign`

### 39.3 Request Actualizado

```python
class AssignProfessionalRequest(BaseModel):
    profesional_id: int
    area_id: Optional[int] = None      # Area del asignador (auto-detectada)
    observacion: Optional[str] = None  # Mensaje para la asignacion
    generar_notificacion: bool = True  # Si crea notificacion
```

### 39.4 Response Actualizado

```python
class AssignmentActionResponse(BaseModel):
    id: int
    message: str
    profesional_nombre: str
    es_reasignacion: bool = False      # True si ya tenia asignacion
    notificacion_creada: bool = False  # True si se creo notificacion
```

### 39.5 Funcionalidad Implementada

1. **Registro en user_work_orders:**
   - Nueva asignacion: INSERT con tiempo_inicial calculado
   - Reasignacion: UPDATE del registro existente

2. **Generacion de notificaciones:**
   - Solo cuando asignador != profesional asignado
   - Incluye motivo ("Asignado" o "Reasignado")
   - Incluye observacion si se proporciona

3. **Deteccion automatica de area:**
   - Se obtiene del rol del usuario que asigna
   - Puede ser sobrescrita con area_id

### 39.6 Frontend - API Client

```typescript
// src/services/api.ts

export interface AssignRequest {
  profesional_id: number;
  area_id?: number;
  observacion?: string;
  generar_notificacion?: boolean;
}

export const assignmentsApi = {
  // Version simple (sin mensaje)
  assign: async (otId: number, profesionalId: number): Promise<AssignmentActionResponse>,

  // Version con mensaje/observacion (FASE 6.28)
  assignWithMessage: async (otId: number, data: AssignRequest): Promise<AssignmentActionResponse>,
};
```

### 39.7 Equivalencia Laravel → FastAPI

| Laravel (UserWorkOrderController) | FastAPI (work_orders.py) |
|-----------------------------------|-------------------------|
| `asignarOT()` | PUT `/work-orders/{ot_id}/assign` |
| Tabla `user_work_orders` | Misma tabla |
| Tabla `notifications` | Misma tabla |
| Campo `observacion` | Campo `observacion` en request |

---

## 40. FASE 6.29: API Mobile Endpoints

Implementacion de API REST optimizada para aplicaciones moviles con respuestas ligeras.

### 40.1 Descripcion

Replica funcionalidad de `ApiMobileController.php` de Laravel con endpoints optimizados para consumo mobile:
- Respuestas JSON con formato `{code, message, data}`
- Calculo de tiempos por area
- Determinacion dinamica de estados disponibles
- Gestion de consultas y respuestas

### 40.2 Backend - Router Mobile

```python
# src/app/routers/mobile.py

router = APIRouter(prefix="/mobile", tags=["API Mobile"])

# Endpoints principales:
# GET  /mobile/listar-ordenes-ot      - Lista OTs del vendedor
# POST /mobile/obtener-detalles-ot    - Detalle completo de OT
# POST /mobile/obtener-historico-ot   - Historial de gestiones
# POST /mobile/guardar-gestion-ot     - Crear cambio estado/consulta
# POST /mobile/guardar-respuesta      - Responder consulta
# POST /mobile/actualizar-token-notificacion - Token push mobile

# Endpoints de materiales:
# POST /mobile/listar-materiales-cliente    - Materiales por RUT
# POST /mobile/listar-materiales-jerarquia  - Jerarquia de materiales
# GET  /mobile/listar-jerarquias            - Lista jerarquias

# Endpoints adicionales:
# GET  /mobile/resumen-vendedor        - Dashboard resumido
# GET  /mobile/notificaciones          - Lista notificaciones
# PUT  /mobile/notificaciones/{id}/leer - Marcar leida
```

### 40.3 Funcionalidades Implementadas

1. **Lista de OTs con tiempos:**
   - Calcula tiempo acumulado por area (venta, desarrollo, diseno, etc.)
   - Dias en area actual
   - Estado actual de la OT

2. **Detalle de OT:**
   - Todos los campos del formulario
   - Joins con tablas de catalogos
   - Mapeo de codigos a descripciones

3. **Historial de Gestiones:**
   - Tipos: Cambio estado, Consulta, Archivo
   - Respuestas a consultas
   - Colores por tipo de gestion
   - Determinacion de estados disponibles segun reglas

4. **Guardar Gestiones:**
   - Cambio de estado con calculo de duracion
   - Actualizacion automatica de area
   - Creacion de consultas a otras areas

5. **Resumen Vendedor (nuevo):**
   - Contadores por area
   - OTs recientes
   - Notificaciones pendientes

### 40.4 Logica de Estados Disponibles

```python
# Reglas de negocio para estados:
# - Si ya fue enviado a desarrollo: permite todos los estados
# - Si es Arte con Material y no enviado a diseno: solo diseno grafico
# - Si esta en Consulta Cliente: permite regresar a Ventas
# - Si esta en Espera OC: permite regresar a Ventas
```

### 40.5 Frontend - API Client

```typescript
// src/services/api.ts

export const mobileApi = {
  listarOrdenesOT: async (): Promise<ListaOTsResponse>,
  obtenerDetallesOT: async (otId: number): Promise<...>,
  obtenerHistoricoOT: async (otId: number): Promise<HistorialOTResponse>,
  guardarGestionOT: async (request: GuardarGestionRequest): Promise<...>,
  guardarRespuesta: async (gestionId: number, observacion: string): Promise<...>,
  actualizarTokenPush: async (token: string): Promise<...>,
  listarMaterialesCliente: async (rutClientes: string[]): Promise<...>,
  listarMaterialesJerarquia: async (codigos: string[]): Promise<...>,
  listarJerarquias: async (): Promise<...>,
  resumenVendedor: async (): Promise<MobileResumen>,
  listarNotificaciones: async (limit?: number): Promise<...>,
  marcarNotificacionLeida: async (notifId: number): Promise<...>,
};
```

### 40.6 Interfaces TypeScript

```typescript
interface MobileOT {
  id: number;
  item: string | null;
  cliente_id: number;
  cliente: string;
  descripcion: string | null;
  area: string | null;
  area_abreviatura: string | null;
  estado: string;
  dias_area_actual: number;
  created_at: string;
  tiempos?: {
    venta: number;
    desarrollo: number;
    diseno: number;
    catalogacion: number;
    precatalogacion: number;
  };
}

interface MobileGestion {
  id: number;
  tipo_gestion: string;
  observacion: string | null;
  area: string;
  usuario: string;
  fecha: string;
  color: string;
  nuevo_estado?: string;
  area_consultada?: string;
  estado_consulta?: string;
  responder?: boolean;
}
```

### 40.7 Equivalencia Laravel → FastAPI

| Laravel (ApiMobileController) | FastAPI (mobile.py) |
|------------------------------|---------------------|
| `getOrdenesOt()` | GET `/mobile/listar-ordenes-ot` |
| `getDetailsOt()` | POST `/mobile/obtener-detalles-ot` |
| `getHistoryOt()` | POST `/mobile/obtener-historico-ot` |
| `saveGestionOt()` | POST `/mobile/guardar-gestion-ot` |
| `saveAnswerOt()` | POST `/mobile/guardar-respuesta` |
| `updateTokenNotificationSeller()` | POST `/mobile/actualizar-token-notificacion` |
| `postMaterialesCliente()` | POST `/mobile/listar-materiales-cliente` |
| `postMaterialesJerarquia()` | POST `/mobile/listar-materiales-jerarquia` |
| `getJerarquias()` | GET `/mobile/listar-jerarquias` |
| (nuevo) | GET `/mobile/resumen-vendedor` |
| (nuevo) | GET `/mobile/notificaciones` |

---

## 41. FASE 6.30-6.32: Formularios OT Completos y Duplicar

### 41.1 Descripcion

Implementacion completa del endpoint de opciones de formulario para OT que carga todos los catalogos desde MySQL, mejoras al formulario de crear OT con selectores de jerarquias dependientes, y funcionalidad para duplicar OTs.

### 41.2 Backend - Endpoint Opciones Formulario Completo

**Archivo**: `work_orders.py`

```python
@router.get("/form-options-complete", response_model=FormOptionsComplete)
async def get_form_options_complete():
    """
    Obtiene TODAS las opciones necesarias para el formulario de crear/editar OT.
    Replica la funcionalidad de WorkOrderController@create de Laravel.
    """
    # 40+ catalogos cargados desde MySQL:
    # clients, canals, vendedores, org_ventas, plantas
    # product_types, cads, cartons, styles, colors, envases
    # processes, armados, impresiones, fsc
    # materials, recubrimientos, coverages_internal/external
    # reference_types, design_types
    # trazabilidad, tipo_cinta, pallet_types, salas_corte
    # hierarchies, subhierarchies, subsubhierarchies
    # tipos_solicitud, maquila_servicios, comunas, pais_referencia
    # secuencia_operacional
```

### 41.3 Backend - Duplicar OT

```python
@router.post("/{ot_id}/duplicate", response_model=DuplicateOTResponse)
async def duplicate_work_order(ot_id: int, user_id: int = Depends(get_current_user_id)):
    """
    Duplica una OT existente con toda su informacion.
    La nueva OT inicia en estado inicial y area de Ventas.
    """
    # Copia todos los campos excepto: id, created_at, updated_at, aprobado, material_id
    # Agrega prefijo "[Copia OT-{id}]" a la descripcion
    # Crea registro inicial en managements
```

### 41.4 Frontend - Hook Opciones Completas

**Archivo**: `useWorkOrders.ts`

```typescript
export function useFormOptionsComplete() {
  return useQuery({
    queryKey: ['formOptionsComplete'],
    queryFn: () => workOrdersApiExtended.getFormOptionsComplete(),
    staleTime: 1000 * 60 * 5, // 5 minutos
  });
}

export function useDuplicateWorkOrder() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (otId: number) => workOrdersApiExtended.duplicate(otId),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['workOrders'] });
    },
  });
}
```

### 41.5 Frontend - Formulario Crear OT Mejorado

**Mejoras a CreateWorkOrder.tsx**:

1. **Selectores de Jerarquias Dependientes**:
   - Jerarquia 1 → filtra Jerarquia 2
   - Jerarquia 2 → filtra Jerarquia 3
   - Reset automatico al cambiar padre

2. **Nuevos Campos**:
   - org_venta_id
   - hierarchy_id, subhierarchy_id, subsubhierarchy_id
   - color_1_id a color_5_id
   - envase_id, autosoportante

3. **Uso de useMemo para filtrado eficiente**:
```typescript
const filteredSubhierarchies = useMemo(() => {
  if (!formOptions?.subhierarchies || !formState.hierarchy_id) return [];
  return formOptions.subhierarchies.filter(
    sh => sh.hierarchy_id === formState.hierarchy_id
  );
}, [formOptions?.subhierarchies, formState.hierarchy_id]);
```

### 41.6 Formulario Editar OT Mejorado (FASE 6.31)

**Archivo**: `EditWorkOrder.tsx`

**Mejoras implementadas**:

1. **Uso de useFormOptionsComplete** para cargar catalogos completos
2. **Selectores de Jerarquias Dependientes** con useMemo para filtrado eficiente
3. **Selectores de Colores dinamicos** (segun numero_colores seleccionado)
4. **Campos adicionales**: org_venta_id, envase_id, autosoportante

```typescript
// Filtrado dependiente de jerarquias
const filteredSubhierarchies = useMemo(() => {
  if (!formOptions?.subhierarchies || !formState?.hierarchy_id) return [];
  return formOptions.subhierarchies.filter(
    sh => sh.hierarchy_id === formState.hierarchy_id
  );
}, [formOptions?.subhierarchies, formState?.hierarchy_id]);

// Selectores de colores dinamicos
{formState.numero_colores && formState.numero_colores > 0 && (
  <FormGrid $columns={5}>
    {[1, 2, 3, 4, 5].slice(0, formState.numero_colores).map(n => (
      <FormGroup key={n}>
        <Label>Color {n}</Label>
        <Select
          value={formState[`color_${n}_id`] || ''}
          onChange={(e) => handleInputChange(`color_${n}_id`, Number(e.target.value))}
        >
          {formOptions?.colors.map(c => (
            <option key={c.id} value={c.id}>{c.nombre}</option>
          ))}
        </Select>
      </FormGroup>
    ))}
  </FormGrid>
)}
```

### 41.7 Interfaces TypeScript

```typescript
export interface FormOptionsComplete {
  clients: ClientOption[];
  canals: CatalogOption[];
  vendedores: CatalogOption[];
  org_ventas: CatalogOption[];
  plantas: CatalogOption[];
  product_types: CatalogOption[];
  cads: CatalogOption[];
  cartons: CatalogOption[];
  styles: CatalogOption[];
  colors: CatalogOption[];
  envases: CatalogOption[];
  processes: CatalogOption[];
  armados: CatalogOption[];
  impresiones: CatalogOption[];
  fsc: CatalogOption[];
  materials: CatalogOption[];
  recubrimientos: CatalogOption[];
  coverages_internal: CatalogOption[];
  coverages_external: CatalogOption[];
  reference_types: CatalogOption[];
  design_types: CatalogOption[];
  trazabilidad: CatalogOption[];
  tipo_cinta: CatalogOption[];
  pallet_types: CatalogOption[];
  salas_corte: CatalogOption[];
  hierarchies: CatalogOption[];
  subhierarchies: HierarchyOption[];
  subsubhierarchies: HierarchyOption[];
  tipos_solicitud: CatalogOption[];
  maquila_servicios: CatalogOption[];
  comunas: CatalogOption[];
  pais_referencia: CatalogOption[];
  secuencia_operacional: CatalogOption[];
}

export interface DuplicateOTResponse {
  id: number;
  original_id: number;
  message: string;
}
```

---

## 42. FASE 6.33 - Sistema de Emails

### 42.1 Objetivo

Implementar sistema de envio de correos electronicos replicando la funcionalidad de Laravel Mail.

### 42.2 Backend - Email Service

**Archivo**: `src/app/services/email_service.py`

**Funcionalidades**:

1. **Configuracion SMTP** desde variables de entorno
2. **Templates HTML** con estilos profesionales
3. **Tipos de correos**:
   - Recuperacion de contrasena (token 5 min)
   - Nuevo cliente registrado
   - Cotizaciones pendientes de aprobacion
   - Alerta margen bruto negativo
   - Recordatorio actualizacion matrices
   - Envio cotizacion PDF
   - Notificaciones de OT

```python
# Configuracion via variables de entorno
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@invebchile.cl
MAIL_FROM_NAME=CMPC
APP_URL=https://envases-ot.inveb.cl
ADMIN_EMAIL=maria.botella@cmpc.com
```

### 42.3 Backend - Router Emails

**Archivo**: `src/app/routers/emails.py`

**Endpoints**:

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/emails/password-recovery` | POST | Solicitar recuperacion de contrasena |
| `/emails/notify-ot` | POST | Enviar notificacion de OT por email |
| `/emails/test` | POST | Probar configuracion SMTP |
| `/emails/pending-quotations` | GET | Notificar cotizaciones pendientes |
| `/emails/matrix-reminder` | GET | Enviar recordatorio de matrices |
| `/emails/notify-new-client` | POST | Notificar nuevo cliente |
| `/emails/notify-negative-margin/{id}` | POST | Alerta margen negativo |

### 42.4 Templates HTML

Todos los templates incluyen:
- Header con logo CMPC
- Contenido estructurado
- Botones de accion
- Footer con disclaimer

```python
class EmailTemplates:
    @staticmethod
    def password_recovery(reset_url: str) -> str:
        # Template con enlace de recuperacion (5 min validez)

    @staticmethod
    def new_client_notification(client_name: str, client_id: int, manage_url: str) -> str:
        # Template con boton "Gestionar Clientes"

    @staticmethod
    def pending_quotations(user_name: str, approve_url: str) -> str:
        # Template con boton "Gestionar Cotizaciones"

    @staticmethod
    def negative_margin_alert(quotation_id: int) -> str:
        # Template de alerta con estilo warning

    @staticmethod
    def ot_notification(ot_id: int, ot_description: str, message: str, sender_name: str) -> str:
        # Template generico para notificaciones OT
```

### 42.5 Frontend - API Client

```typescript
// src/services/api.ts
export const emailsApi = {
  requestPasswordRecovery: async (rut: string): Promise<EmailResponse> => { ... },
  sendOTNotification: async (data: OTNotificationRequest): Promise<EmailResponse> => { ... },
  testEmail: async (to_email: string): Promise<EmailResponse> => { ... },
  notifyPendingQuotations: async (): Promise<EmailResponse> => { ... },
  sendMatrixReminder: async (): Promise<EmailResponse> => { ... },
  notifyNewClient: async (clientId: number): Promise<EmailResponse> => { ... },
  notifyNegativeMargin: async (quotationId: number): Promise<EmailResponse> => { ... },
};
```

### 42.6 Tareas Programadas

Los endpoints `/pending-quotations` y `/matrix-reminder` estan disenados para ser invocados por un scheduler externo (cron job):

```bash
# Crontab sugerido
# Cotizaciones pendientes: L-V 08:00 AM
0 8 * * 1-5 curl -X GET https://api.inveb.cl/emails/pending-quotations

# Recordatorio matrices: Viernes 05:00 AM
0 5 * * 5 curl -X GET https://api.inveb.cl/emails/matrix-reminder
```

### 42.7 Seguridad

- Tokens de recuperacion con expiracion de 5 minutos
- Respuestas genericas para no revelar existencia de usuarios
- Validacion de emails con Pydantic EmailStr
- BCC automatico al admin en notificaciones importantes

---

## 43. FASE 6.34 - Materiales y CAD

### 43.1 Objetivo

Implementar gestion de materiales, asignacion de CAD a OTs, y subida de archivos de diseno.

### 43.2 Backend - Router Materials

**Archivo**: `src/app/routers/materials.py`

**Endpoints**:

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/materials/` | GET | Lista materiales con filtros |
| `/materials/search` | GET | Busqueda rapida de materiales |
| `/materials/{id}` | GET | Detalle de material |
| `/materials/` | POST | Crear material |
| `/materials/{id}` | PUT | Actualizar material |
| `/materials/cads/list` | GET | Lista CADs disponibles |
| `/materials/cads/search` | GET | Busqueda de CADs |
| `/materials/assign-cad` | POST | Asigna CAD a OT |
| `/materials/ot/{ot_id}/material` | GET | Material de una OT |

### 43.3 Backend - Router Uploads

**Archivo**: `src/app/routers/uploads.py`

**Endpoints**:

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/uploads/ot/{ot_id}/file` | POST | Sube archivo de diseno a OT |
| `/uploads/ot/{ot_id}/files` | GET | Lista archivos de OT |
| `/uploads/ot/{ot_id}/file/{type}` | DELETE | Elimina archivo de OT |
| `/uploads/management/{id}/file` | POST | Sube archivo a gestion |
| `/uploads/management/{id}/files` | GET | Lista archivos de gestion |
| `/uploads/file/{file_id}` | DELETE | Elimina archivo por ID |

### 43.4 Tipos de Archivos OT

Los archivos de diseno soportados para OT son:

- `plano` - Plano/diseno actual
- `boceto` - Boceto actual
- `ficha_tecnica` - Ficha tecnica del producto
- `correo_cliente` - Archivo de correo del cliente
- `speed` - Archivo de velocidad/produccion
- `otro` - Otro archivo de diseno
- `oc` - Orden de compra
- `licitacion` - Archivo de licitacion
- `vb_muestra` - VoBo de muestra
- `vb_boceto` - VoBo de boceto

### 43.5 Configuracion de Uploads

```python
# Variables de entorno
UPLOAD_DIR=/tmp/uploads  # Directorio de almacenamiento

# Configuracion interna
MAX_FILE_SIZE = 50 * 1024 * 1024  # 50 MB max
ALLOWED_EXTENSIONS = {
    "pdf", "doc", "docx", "xls", "xlsx",  # Documentos
    "png", "jpg", "jpeg", "gif",           # Imagenes
    "dwg", "dxf",                          # CAD
    "ai", "eps", "psd"                     # Diseno
}
```

### 43.6 Frontend - API Client Materials

```typescript
// src/services/api.ts
export const materialsApi = {
  list: async (filters): Promise<MaterialListResponse> => { ... },
  search: async (query, limit): Promise<MaterialItem[]> => { ... },
  get: async (id): Promise<MaterialDetail> => { ... },
  create: async (data): Promise<MaterialDetail> => { ... },
  update: async (id, data): Promise<MaterialDetail> => { ... },
  listCADs: async (search, limit): Promise<CADItem[]> => { ... },
  searchCADs: async (query, limit): Promise<CADItem[]> => { ... },
  assignCAD: async (data): Promise<CADAssignResponse> => { ... },
  getOTMaterial: async (otId): Promise<{ ot_id: number; material: MaterialItem | null }> => { ... },
};
```

### 43.7 Frontend - API Client Uploads

```typescript
// src/services/api.ts
export type OTFileType = 'plano' | 'boceto' | 'ficha_tecnica' | 'correo_cliente' |
                         'speed' | 'otro' | 'oc' | 'licitacion' | 'vb_muestra' | 'vb_boceto';

export const uploadsApi = {
  uploadOTFile: async (otId, file, fileType): Promise<UploadResponse> => { ... },
  getOTFiles: async (otId): Promise<OTFilesResponse> => { ... },
  deleteOTFile: async (otId, fileType): Promise<{ message: string }> => { ... },
  uploadManagementFile: async (managementId, file): Promise<UploadResponse> => { ... },
  getManagementFiles: async (managementId): Promise<FileInfo[]> => { ... },
  deleteFile: async (fileId): Promise<{ message: string }> => { ... },
  getFileUrl: (relativePath): string => { ... },
};
```

### 43.8 Estructura de Almacenamiento

```
/tmp/uploads/
├── ot_{ot_id}/
│   ├── 20251221143022_abc12345.pdf   # plano
│   ├── 20251221143055_def67890.jpg   # boceto
│   └── ...
└── managements/
    └── {management_id}/
        ├── 20251221150000_ghi11111.xlsx
        └── ...
```

### 43.9 Campos de Base de Datos

Los archivos de OT se almacenan en campos de la tabla `work_orders`:

| Campo DB | Tipo Archivo |
|----------|--------------|
| `ant_des_plano_actual_file` | plano |
| `ant_des_boceto_actual_file` | boceto |
| `ficha_tecnica_file` | ficha_tecnica |
| `ant_des_correo_cliente_file` | correo_cliente |
| `ant_des_speed_file` | speed |
| `ant_des_otro_file` | otro |
| `oc_file` | oc |
| `licitacion_file` | licitacion |
| `ant_des_vb_muestra_file` | vb_muestra |
| `ant_des_vb_boce_file` | vb_boceto |

---

## 45. Validacion de Campos Obligatorios (2025-12-23)

### 45.1 Descripcion

Implementacion de validaciones de campos requeridos en el formulario de creacion de OT, replicando exactamente el comportamiento de Laravel (`ot-form-validation.js`). Los campos obligatorios se resaltan con borde rojo y etiqueta en rojo cuando no se completan.

### 45.2 Validaciones Implementadas

**Archivo**: `frontend/src/pages/WorkOrders/CreateWorkOrder.tsx`

#### Campos Siempre Requeridos

| Campo | Validacion |
|-------|-----------|
| client_id | Requerido |
| descripcion | Requerido, max 40 chars |
| tipo_solicitud | Requerido |
| canal_id | Requerido |
| nombre_contacto | Requerido |
| email_contacto | Requerido, formato email |
| telefono_contacto | Requerido |
| observacion | Requerido, min 10 chars, max 1000 chars |
| sentido_armado | Requerido |

#### Condicionales por Tipo de Solicitud

| Condicion | Campos Requeridos |
|-----------|-------------------|
| No es Muestra (tipo != 3) | volumen_venta_anual |
| Desarrollo Completo (tipo 1, 4, 7) | product_type_id, peso_contenido_caja, envase_id, cantidad_cajas_apiladas |
| Tipos 1, 5, 7 | cinta |
| Arte con Material (tipo 5) | reference_type, reference_id, bloqueo_referencia |

#### Condicionales por Rol

| Rol | Campos Requeridos |
|-----|-------------------|
| Vendedor (4) | org_venta_id, termocontraible, armado_automatico, planta_id, impresion_borde, impresion_sobre_rayado, pegado_terminacion, coverage_internal_id, coverage_external_id |
| VendedorExterno (19) | planta_id, coverage_internal_id, coverage_external_id, pegado_terminacion |
| JefeVenta (3) | impresion_borde, impresion_sobre_rayado, restriccion_pallet |
| JefeDesarrollo (5) | planta_id, maquila, impresion_borde, impresion_sobre_rayado, caracteristicas_adicionales, armado_id |
| Ingeniero (6) | planta_id, maquila, impresion_borde, impresion_sobre_rayado, caracteristicas_adicionales |

#### Condicionales por Otros Campos

| Condicion | Campos Requeridos |
|-----------|-------------------|
| FSC = Si (1) | pais_id |
| restriccion_pallet = Si (1) | tamano_pallet_type_id, altura_pallet, permite_sobresalir_carga, pallet_qa_id, bulto_zunchado_pallet, formato_etiqueta_pallet, etiquetas_por_pallet |
| coverage_internal_id != 1 | percentage_coverage_internal |
| coverage_external_id != 1 | percentage_coverage_external |
| impresion = 2 (Offset) | design_type_id |

### 45.3 Constantes de Roles

```typescript
const ROLES = {
  Admin: 1,
  Gerente: 2,
  JefeVenta: 3,
  Vendedor: 4,
  JefeDesarrollo: 5,
  Ingeniero: 6,
  JefeDiseño: 7,
  Diseñador: 8,
  JefeCatalogador: 9,
  Catalogador: 10,
  JefePrecatalogador: 11,
  Precatalogador: 12,
  JefeMuestras: 13,
  TecnicoMuestras: 14,
  GerenteComercial: 15,
  API: 17,
  SuperAdministrador: 18,
  VendedorExterno: 19,
} as const;
```

### 45.4 Feedback Visual

Los campos con error muestran:
- Borde rojo (#dc3545)
- Label en rojo
- Mensaje "Campo obligatorio" debajo del campo
- Atributo `data-has-error="true"` para testing

```typescript
<FormGroup data-has-error={!!fieldErrors.campo}>
  <Label style={{ color: fieldErrors.campo ? '#dc3545' : undefined }}>
    Campo *
  </Label>
  <Input $hasError={!!fieldErrors.campo} ... />
  {fieldErrors.campo && <FieldError>{fieldErrors.campo}</FieldError>}
</FormGroup>
```

### 45.5 Styled Components con Error

```typescript
const Input = styled.input<{ $hasError?: boolean }>`
  border: 1px solid ${props => props.$hasError ? '#dc3545' : theme.colors.border};
  background: ${props => props.$hasError ? '#fff5f5' : 'white'};
`;

const FieldError = styled.span`
  font-size: 0.7rem;
  color: #dc3545;
  margin-top: 0.25rem;
`;
```

### 45.6 Funcion validateForm

```typescript
const validateForm = useCallback((): FieldErrors => {
  const errors: FieldErrors = {};
  const role = getCurrentUserRole();
  const tipoSolicitud = formState.tipo_solicitud;

  // Helpers
  const esDesarrolloCompletoOCotizanSinCad = tipoSolicitud === 1 || tipoSolicitud === 4 || tipoSolicitud === 7;
  const noEsMuestra = tipoSolicitud !== 3;
  const esVendedor = role === ROLES.Vendedor;
  // ... mas helpers

  // Validaciones siempre requeridas
  if (!formState.client_id) errors.client_id = 'Campo obligatorio';
  // ... mas validaciones

  // Validaciones condicionales por tipo, rol, y campos
  // ... (ver codigo completo en CreateWorkOrder.tsx)

  return errors;
}, [formState]);
```

### 45.7 Archivos Modificados

| Archivo | Cambios |
|---------|---------|
| `CreateWorkOrder.tsx` | Constantes ROLES, getCurrentUserRole(), validateForm() expandido, feedback visual |
| `Select.tsx` | Prop `$hasError` en StyledSelect |
| `CascadeForm.tsx` | Prop `fieldErrors` para campos cascade |

---

## 44. Resumen de Fases Actualizado

| Fase | Descripcion | Estado |
|------|-------------|--------|
| 6.1 | Conectar con datos reales de catalogos | COMPLETADO |
| 6.2 | Dashboard OTs - Lista con filtros y paginacion | COMPLETADO |
| 6.3 | Crear OT - Formulario completo con CascadeForm | COMPLETADO |
| 6.4 | Editar OT | COMPLETADO |
| 6.5 | Gestionar OT (workflow por areas) | COMPLETADO |
| 6.6 | Notificaciones | COMPLETADO |
| 6.7 | Mantenedores (55 genericos + 3 personalizados) | COMPLETADO |
| 6.8 | Cotizaciones (lista, crear, editar, aprobar) | COMPLETADO |
| 6.9 | Aprobacion de OTs | COMPLETADO |
| 6.10 | Asignaciones de OTs | COMPLETADO |
| 6.11 | Cotizador Externo | COMPLETADO |
| 6.12 | Cotizar Multiples OT | COMPLETADO |
| 6.13 | Reportes Dashboard | COMPLETADO |
| 6.14 | Crear OT Especial | COMPLETADO |
| 6.15 | Detalle Log OT | COMPLETADO |
| 6.16 | Reportes con Chart.js (15 reportes) | COMPLETADO |
| 6.17 | Recuperacion de Contrasena (forgot/reset) | COMPLETADO |
| 6.18 | Editar OT Especial (Benchmarking/Ficha/Licitacion) | COMPLETADO |
| 6.19 | Aprobacion Externa de Cotizaciones | COMPLETADO |
| 6.20 | Crear OT desde Cotizacion | COMPLETADO |
| 6.21 | Modulo de Muestras | COMPLETADO |
| 6.22 | Carga Masiva de Mantenedores (Excel/CSV) | COMPLETADO |
| 6.23 | Exportacion Excel/SAP de OTs | COMPLETADO |
| 6.24 | Generacion de PDFs (etiquetas, fichas, cotizaciones) | COMPLETADO |
| 6.25 | Cascadas AJAX (selectores dependientes) | COMPLETADO |
| 6.26 | Exportacion Log OT (Bitacora) a Excel | COMPLETADO |
| 6.27 | Carga Masiva Tablas del Cotizador | COMPLETADO |
| 6.28 | Asignaciones con Mensaje y Notificaciones | COMPLETADO |
| 6.29 | API Mobile Endpoints | COMPLETADO |
| 6.30 | Formulario Crear OT Completo (40+ catalogos) | COMPLETADO |
| 6.31 | Formulario Editar OT Completo | COMPLETADO |
| 6.32 | Duplicar OT | COMPLETADO |
| 6.33 | Sistema de Emails (SMTP, templates, notificaciones) | COMPLETADO |
| 6.34 | Materiales y CAD (gestion materiales, uploads) | COMPLETADO |
| 6.35 | Validacion de Campos Obligatorios (replica Laravel) | COMPLETADO |
| 6.36 | DetalleForm Layout Identico a Laravel | COMPLETADO |

---

## FASE 6.36: DetalleForm - Layout Identico a Laravel

**Fecha**: 2025-12-25
**Estado**: COMPLETADO

### Descripcion

Correccion del formulario `DetalleForm.tsx` para que su layout y campos coincidan exactamente con el modal `modal-detalle-cotizacion.blade.php` de Laravel.

### Diferencias Identificadas y Corregidas

#### Layout General
| Aspecto | Laravel | React (Antes) | React (Despues) |
|---------|---------|---------------|-----------------|
| Columnas | 3 columnas (col-4) | 4 columnas | 3 columnas |
| Fondo modal | #F2F4FD | Blanco | #F2F4FD |
| Tabs vs Dropdown | Dropdown centrado | Tabs laterales | Dropdown centrado |

#### Secciones del Formulario
| Seccion | Laravel | Implementado |
|---------|---------|--------------|
| SELECCIONAR TIPO DE PRODUCTO | Card con dropdown centrado | SI |
| CORRUGADO / ESQUINERO | Header centrado | SI |
| Caracteristicas | Card con 3 columnas | SI |
| CAMPOS OPCIONALES (Carta Oferta) | Card col-8 | SI |
| CAMPOS OPCIONALES (Convertir a OT) | Card col-4 | SI |
| Servicios | Card 3 columnas | SI |
| Destino | Card con campos y boton | SI |

#### Campos Agregados (Faltantes en React)
1. **Columna 1 (Caracteristicas)**:
   - Buscar por Material (boton verde)
   - Estimacion Carton (boton)
   - Calculo AHC (boton)
   - Ancho Hoja Madre (mm) con icono ayuda
   - Largo Hoja Madre (mm) con icono ayuda
   - Maquina Impresora

2. **Columna 2 (Caracteristicas)**:
   - Barniz (Si/No dropdown)
   - Tipo de Barniz
   - Cobertura color (%)
   - Cobertura barniz (cm2)
   - Clisse por un golpe (cm2)

3. **Columna 3 (Caracteristicas)**:
   - Zunchos
   - Ensamblado
   - Desgajado Cabezal

4. **Campos Opcionales (Carta Oferta)**:
   - Medidas (Internas/Externas)
   - Largo, Ancho, Alto (mm)
   - BCT MIN (LB) y BCT MIN (KG) - calculado
   - Descripcion (material)
   - CAD (material)
   - Cod. interno cliente
   - Clausula Devolucion de Pallets
   - Clausula Ajuste de Precios

5. **Campos Opcionales (OT)**:
   - Codigo (material)
   - Jerarquia 1, 2, 3

6. **Servicios**:
   - Cuchillos y gomas (m)
   - Armado (US$/UN)

7. **Destino**:
   - Boton "Agregar Destino"
   - Boton "Limpiar"

### Interface DetalleCotizacion Actualizada

```typescript
export interface DetalleCotizacion {
  // Campos existentes...
  tipo_medida: number;           // 1=Internas, 2=Externas
  printing_machine_id: number | null;
  numero_colores_esquinero: number;
  barniz: number;                // Si/No
  cobertura_color_percent: number;
  cobertura_barniz_cm2: number;
  cobertura_color_cm2: number;   // Clisse por un golpe
  ensamblado: number;
  desgajado_cabezal: number;
  bct_min_lb: number | null;
  bct_min_kg: number | null;     // Calculado automaticamente
  descripcion_material_detalle: string;
  cad_material_detalle: string;
  codigo_cliente: string;
  devolucion_pallets: number;
  ajuste_precios: number;
  codigo_material_detalle: string;
  hierarchy_id: number | null;
  subhierarchy_id: number | null;
  subsubhierarchy_id: number | null;
  cuchillos_gomas: number;
  maquila_esquinero: number;
  clisse_esquinero: number;
  cantidad_esquinero: number;
}
```

### Archivos Modificados

| Archivo | Cambios |
|---------|---------|
| `DetalleForm.tsx` | Reescrito completamente para coincidir con Laravel |

### Scripts de Verificacion

| Script | Proposito |
|--------|-----------|
| `analyze_detalle_forms.py` | Selenium: Analiza estructura de ambos formularios |
| `verify_detalle_forms.py` | Selenium: Verifica que React coincide con Laravel |

### Verificacion con Selenium

Para verificar que el formulario React coincide con Laravel:

```bash
# Ejecutar script de verificacion
python verify_detalle_forms.py
```

El script verifica:
- 6 secciones principales
- 40+ campos del formulario
- 6 botones de accion

### Resultado Esperado

El formulario React en `localhost:3000/work-orders/create` al hacer clic en "+ Detalle" debe mostrar:

1. Card "SELECCIONAR TIPO DE PRODUCTO" con dropdown centrado
2. Card con header "CORRUGADO" (o ESQUINERO)
3. Header "Caracteristicas" con boton verde "Buscar por Material"
4. 3 columnas de campos igual que Laravel
5. Dos cards de campos opcionales (8:4 columnas)
6. Card "Servicios" con 3 columnas
7. Card "Destino" con boton "Agregar Destino"
8. Botones "Limpiar" y "Guardar Detalle" al final

---

## 46. Verificacion Selenium DetalleForm (2025-12-25)

### 46.1 Descripcion

Verificacion automatizada con Python Selenium para comparar el formulario DetalleForm entre Laravel (localhost:8080) y React (localhost:3000). Se reconstruyo el contenedor Docker del frontend para aplicar los cambios de layout.

### 46.2 Resultados de la Verificacion

```
============================================================
COMPARISON RESULTS
============================================================

Laravel has 59 unique labels
React has 52 unique labels

--- COMMON LABELS (52) ---
(Todos los campos del modal Detalle coinciden)

*** MISSING IN REACT (7) ***
- CLASIFICACION, CLIENTE, CONTACTOS, EMAIL, INSTALACION, NOMBRE, TELEFONO
(Estos campos son del formulario principal, NO del modal Detalle)

LABEL MATCH: 88.1%
============================================================
```

### 46.3 Elementos Clave Verificados

| Elemento | Estado |
|----------|--------|
| CARACTERISTICAS | OK |
| SELECCIONAR TIPO DE PRODUCTO | OK |
| CAMPOS OPCIONALES | OK |
| CORRUGADO | OK |
| Buscar por Material | OK |
| Estimacion Carton | OK |
| Calculo AHC | OK |
| BARNIZ | OK |
| COBERTURA COLOR | OK |
| COBERTURA BARNIZ | OK |
| CLISSE POR UN GOLPE | OK |
| ENSAMBLADO | OK |
| DESGAJADO CABEZAL | OK |
| BCT MIN | OK |
| JERARQUIA | OK |

### 46.4 Campos del Modal Detalle (52 labels verificados)

Todos los campos del modal coinciden exactamente:
- Tipo de Producto, Carton, Area HC, Ancho/Largo Hoja Madre
- Tipo item, Maquina Impresora, Impresion, Numero Colores
- Golpes largo/ancho, Proceso, Tipo de Pegado, Cinta Desgarro
- Barniz, Tipo de Barniz, Cobertura color, Cobertura barniz
- Clisse por un golpe, Pallet, Altura Pallet, Zunchos, Funda, Strech Film
- Ensamblado, Desgajado Cabezal, Rubro, Medidas
- Largo/Ancho/Alto (mm), BCT MIN (LB/KG)
- Descripcion, CAD, Codigo interno, Codigo material
- Clausulas, Jerarquia 1/2/3, Matriz, Clisse, Royalty, Maquila
- Cuchillos y gomas, Armado, Lugar de Destino, Pallets Apilados, Cantidad

### 46.5 Comandos de Verificacion

```bash
# Reconstruir frontend con cambios
cd msw-envases-ot
docker-compose build frontend --no-cache
docker-compose up -d frontend

# Ejecutar comparacion Selenium
python compare_detalle_selenium.py
```

### 46.6 Scripts Utilizados

| Script | Archivo |
|--------|---------|
| Comparacion completa | `compare_detalle_selenium.py` |
| Verificacion rapida | `verify_detalle_forms.py` |

### 46.7 Conclusiones

1. **Match del 88.1%**: Todos los campos del modal DetalleForm coinciden
2. **Campos "faltantes"**: Los 7 labels no encontrados (CLIENTE, INSTALACION, etc.) son del formulario principal de cotizacion, no del modal
3. **Estructura identica**: Layout de 3 columnas, secciones y botones coinciden con Laravel
4. **Frontend actualizado**: Contenedor Docker reconstruido y desplegado en puerto 3000

---

## 47. Resumen de Costos por Producto (2025-12-25)

### 47.1 Descripcion

Implementacion de la seccion "Resumen de Costos por producto" en el formulario de cotizaciones React, replicando la funcionalidad visible en la version QAS de Laravel. Esta seccion muestra 4 tablas con calculos de costos despues de generar/actualizar una pre-cotizacion.

### 47.2 Comparacion QAS vs Local

| Seccion | QAS (Laravel) | React (Nuevo) |
|---------|---------------|---------------|
| Parametros Por Producto | OK | OK |
| Nuevos Detalles Cotizacion | OK | OK |
| Costos Productos (USD/MM2) | OK | OK |
| Costos Servicios (USD/MM2) | OK | OK |
| Solicitar Aprobacion | OK | OK (placeholder) |

### 47.3 Backend - Endpoint de Costos

**Archivo**: `src/app/routers/cotizaciones/router.py`

**Nuevo Endpoint**:
```python
@router.get("/{id}/costos-resumen")
async def get_cotizacion_costos_resumen(id: int):
    """
    Obtiene el resumen de costos de una cotizacion con los datos calculados
    de historial_resultados para mostrar las tablas de:
    - Parametros Por Producto
    - Nuevos Detalles Cotizacion
    - Costos Productos (USD/MM2)
    - Costos Servicios (USD/MM2)
    """
```

**Response Schema**:
```typescript
interface CostosResumenResponse {
  cotizacion_id: number;
  estado_id: number;
  estado_nombre: string;
  cliente_nombre: string;
  tiene_resultados: boolean;
  parametros_producto: ParametroProducto[];
  nuevos_detalles: NuevoDetalle[];
  costos_productos: CostoProducto[];
  costos_servicios: CostoServicio[];
}
```

### 47.4 Frontend - CotizacionForm.tsx

**Archivo**: `frontend/src/pages/Cotizaciones/CotizacionForm.tsx`

**Nuevos Styled Components**:
- `CostSummarySection` - Contenedor principal con borde verde
- `CostSummaryTitle` - Titulo "Resumen de Costos por producto"
- `CostCard` - Card para cada tabla
- `CostCardHeader` - Header verde (#28a745)
- `CostCardBody` - Contenedor de tabla
- `CostTable` - Tabla estilizada
- `ApprovalButton` - Boton "Solicitar Aprobacion"

**Nuevo State**:
```typescript
const [showCostSummary, setShowCostSummary] = useState(false);
const [costData, setCostData] = useState<CostosResumenResponse | null>(null);
const [isLoadingCosts, setIsLoadingCosts] = useState(false);
```

**Flujo**:
1. Usuario hace clic en "Generar Pre-Cotizacion"
2. Se guarda la cotizacion
3. Se llama al endpoint `/costos-resumen`
4. Se muestra la seccion con las 4 tablas de costos
5. Aparece boton "Solicitar Aprobacion"

### 47.5 API Service

**Archivo**: `frontend/src/services/api.ts`

**Nuevo metodo**:
```typescript
getCostosResumen: async (id: number): Promise<CostosResumenResponse> => {
  const response = await api.get<CostosResumenResponse>(`/cotizaciones/${id}/costos-resumen`);
  return response.data;
}
```

**Nuevas interfaces**:
- `ParametroProducto`
- `NuevoDetalle`
- `CostoProducto`
- `CostoServicio`
- `CostosResumenResponse`

### 47.6 Estructura de las Tablas

**Tabla 1 - Parametros Por Producto**:
| Columna | Campo |
|---------|-------|
| N° | numero |
| Descripcion | descripcion |
| CAD | cad |
| Planta | planta |
| Tipo Producto | tipo_producto |
| Item | item |
| Carton | carton |
| Flete | flete |
| Margen Papeles (USD/Mm2) | margen_papeles |
| Margen (USD/Mm2) | margen |
| Margen MINIMO (USD/Mm2) | margen_minimo |
| Precio (USD/Mm2) | precio_usd_mm2 |
| Precio (USD/Ton) | precio_usd_ton |
| Precio (USD/UN) | precio_usd_un |
| Precio ($/UN) | precio_clp_un |
| Cantidad | cantidad |
| Precio Total (MUSD) | precio_total_musd |

**Tabla 2 - Nuevos Detalles Cotizacion**:
| Columna | Campo |
|---------|-------|
| N° | numero |
| Descripcion | descripcion |
| CAD | cad |
| Tipo Producto | tipo_producto |
| Item | item |
| Carton | carton |
| MC (USD/Mm2) | mc_usd_mm2 |
| Margen bruto sin flete | margen_bruto_sin_flete |
| Margen de servir | margen_servir |
| Mg EBITDA (%) | mg_ebitda |

**Tabla 3 - Costos Productos (USD/MM2)**:
| Columna | Campo |
|---------|-------|
| Costo Directo | costo_directo |
| Costo Indirecto | costo_indirecto |
| GVV | gvv |
| Costo Fijo | costo_fijo |
| Costo Total | costo_total |

**Tabla 4 - Costos Servicios (USD/MM2)**:
| Columna | Campo |
|---------|-------|
| Maquila | maquila |
| Armado | armado |
| Clisses | clisses |
| Matriz | matriz |
| Mano de Obra | mano_obra |
| Flete | flete |

### 47.7 Datos de Costos (historial_resultados)

Los costos se calculan en Laravel y se almacenan en el campo JSON `detalle_cotizacions.historial_resultados`:

```json
{
  "costo_directo": {"usd_mm2": 1052.702, "usd_ton": 1224.6, "usd_caja": 0.435},
  "costo_indirecto": {"usd_mm2": 108.749, "usd_ton": 126.2, "usd_caja": 0.045},
  "costo_gvv": {"usd_mm2": 7.491, "usd_ton": 8.7, "usd_caja": 0.003},
  "costo_fijo_total": {"usd_mm2": 284.749, "usd_ton": 330.5, "usd_caja": 0.115},
  "costo_total": {"usd_mm2": 1453.692, "usd_ton": 1690.0, "usd_caja": 0.598},
  "precio_usd_mm2": 1453.7,
  "precio_usd_ton": 1224.6,
  "precio_usd_un": 0.435,
  "precio_clp_un": 422.7,
  "margen_papeles": 93.0,
  "margen": 0.0,
  "margen_minimo": 76.0,
  "mc_usd_mm2": 284.749,
  "margen_bruto_sin_flete": 54.0,
  "margen_servir": 34.0,
  "mg_ebitda": 0.0,
  "precio_total_musd": 0.435
}
```

### 47.8 Build y Verificacion

```bash
# Build del frontend
cd msw-envases-ot/frontend
npm run build

# Resultado: Build exitoso
# vite v5.4.21 building for production...
# ✓ 226 modules transformed
# ✓ built in 8.48s
```

### 47.9 Archivos Modificados

| Archivo | Cambios |
|---------|---------|
| `router.py` | Nuevo endpoint GET /{id}/costos-resumen |
| `api.ts` | Nuevo metodo getCostosResumen + interfaces |
| `CotizacionForm.tsx` | Seccion Resumen de Costos con 4 tablas |

### 47.10 Notas de Implementacion

1. **Visibilidad**: La seccion solo se muestra despues de guardar la cotizacion
2. **Datos vacios**: Se muestra mensaje "Sin datos de costos calculados" si no hay historial_resultados
3. **Formato numerico**: Los valores se formatean con toFixed(2) o toFixed(3) segun el campo
4. **Boton Aprobacion**: El boton "Solicitar Aprobacion" muestra un placeholder (pendiente implementacion completa)
5. **Estilos Laravel**: Se replican los colores y estilos del header verde (#28a745)

---

**Documento generado**: 2025-12-19
**Ultima actualizacion**: 2025-12-25 - Implementacion Resumen de Costos por Producto FASE 6.47
**Version**: 47.0 (Resumen de Costos: 4 tablas + endpoint + styled-components)
**Stack**: React 18 + TypeScript + styled-components + Vite + Chart.js + Axios + xlsx + openpyxl + reportlab + smtplib
