# Arquitectura del Sistema - INVEB Envases OT

## Vision General

INVEB Envases OT es un sistema de gestion de Ordenes de Trabajo migrado de Laravel a una arquitectura moderna de microservicios con React (frontend) y FastAPI (backend).

```
┌─────────────────────────────────────────────────────────────┐
│                        CLIENTE                               │
│                    (Navegador Web)                           │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    FRONTEND (React)                          │
│                     Puerto: 3000                             │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐          │
│  │   Pages     │  │  Components │  │   Hooks     │          │
│  │  (Vistas)   │  │ (UI/Forms)  │  │ (API State) │          │
│  └─────────────┘  └─────────────┘  └─────────────┘          │
│                         │                                    │
│                    ┌────┴────┐                               │
│                    │  Nginx  │ (Proxy /api/ -> Backend)      │
│                    └────┬────┘                               │
└─────────────────────────┼───────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│                    BACKEND (FastAPI)                         │
│                     Puerto: 8001                             │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐          │
│  │   Routers   │──│  Services   │──│   Models    │          │
│  │ (Endpoints) │  │  (Logica)   │  │  (ORM/SQL)  │          │
│  └─────────────┘  └─────────────┘  └─────────────┘          │
└─────────────────────────┼───────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│                    BASE DE DATOS                             │
│                    MySQL: envases_ot                         │
│  (Compartida con sistema Laravel existente)                  │
└─────────────────────────────────────────────────────────────┘
```

## Componentes Principales

### Frontend (React + TypeScript)

**Tecnologias:**
- React 18.2 con TypeScript
- Vite 5.0 (build tool)
- TanStack Query (React Query) para manejo de estado del servidor
- Styled Components para estilos
- Axios para peticiones HTTP

**Estructura:**
```
frontend/src/
├── components/          # Componentes reutilizables
│   ├── Layout/          # Layout principal, sidebar, header
│   ├── Table/           # Tablas con paginacion
│   └── Form/            # Componentes de formulario
├── hooks/               # Custom hooks
│   ├── useCotizaciones.ts  # CRUD cotizaciones
│   └── useFormOptions.ts   # Opciones de formulario
├── pages/               # Vistas/Paginas
│   ├── Cotizaciones/    # Modulo principal
│   │   ├── List.tsx     # Listado con filtros
│   │   ├── Form.tsx     # Creacion/Edicion
│   │   └── DetalleForm.tsx  # Formulario de detalles
│   └── Dashboard/       # Dashboard con KPIs
├── services/            # Clientes API
│   └── api.ts           # Configuracion Axios
├── types/               # Tipos TypeScript
└── theme/               # Estilos globales
```

**Patrones:**
- Container/Presentational para separacion de logica y UI
- Custom hooks para encapsular logica de API
- React Query para cache y sincronizacion

### Backend (FastAPI + Python)

**Tecnologias:**
- Python 3.12+
- FastAPI 0.109
- PyMySQL (conexion MySQL directa)
- PyJWT (autenticacion)
- ReportLab/OpenPyXL (generacion documentos)

**Estructura:**
```
src/app/
├── config/              # Configuracion
│   └── settings.py      # Variables de entorno
├── routers/             # Endpoints API
│   ├── auth.py          # Login, logout, me
│   ├── cotizaciones.py  # CRUD cotizaciones
│   ├── work_orders.py   # Ordenes de trabajo
│   └── dashboard.py     # KPIs y metricas
├── services/            # Logica de negocio
│   ├── mysql_service.py # Queries MySQL
│   └── auth_service.py  # Autenticacion
├── schemas/             # Pydantic schemas
│   └── cotizacion.py    # DTOs
└── utils/               # Utilidades
    └── pdf_generator.py # Generacion PDFs
```

**Patrones:**
- Dependency Injection para servicios
- Repository pattern para acceso a datos
- Schemas Pydantic para validacion

## Flujo de Datos

### Autenticacion

```
1. Usuario ingresa credenciales
2. Frontend -> POST /api/v1/auth/login
3. Backend valida contra tabla `users` de MySQL
4. Backend genera JWT token
5. Frontend almacena token en localStorage
6. Peticiones subsiguientes incluyen token en header Authorization
```

### CRUD Cotizaciones

```
1. Usuario solicita listado
2. Frontend -> GET /api/v1/cotizaciones/
3. Backend ejecuta query a MySQL (con filtros)
4. Backend retorna JSON con paginacion
5. React Query cachea resultado
6. UI renderiza tabla con datos
```

### Guardado de Detalles

```
1. Usuario agrega detalle a cotizacion
2. Frontend acumula detalles en estado local
3. Al guardar cotizacion:
   - POST /api/v1/cotizaciones/ (nueva)
   - PUT /api/v1/cotizaciones/{id} (edicion)
4. Backend procesa en transaccion:
   a. Guarda/actualiza cotizacion
   b. Guarda/actualiza detalles
5. Frontend recibe respuesta y actualiza cache
```

## Base de Datos

### Conexion

El sistema se conecta a la base de datos MySQL existente del sistema Laravel:

```python
# Configuracion en settings.py
LARAVEL_MYSQL_HOST = "localhost"
LARAVEL_MYSQL_PORT = 3306
LARAVEL_MYSQL_DATABASE = "envases_ot"
```

### Tablas Principales

| Tabla | Descripcion |
|-------|-------------|
| `users` | Usuarios del sistema |
| `cotizaciones` | Cotizaciones |
| `cotizacion_detalles` | Detalles/productos de cotizacion |
| `clientes` | Clientes |
| `product_types` | Tipos de producto |
| `impresiones` | Tipos de impresion |
| `anilox` | Configuraciones anilox |

### Queries

Las queries se ejecutan directamente con PyMySQL (sin ORM para tablas Laravel):

```python
# Ejemplo en mysql_service.py
def get_cotizaciones(filters):
    query = """
        SELECT c.*, cl.nombre as cliente_nombre
        FROM cotizaciones c
        LEFT JOIN clientes cl ON c.cliente_id = cl.id
        WHERE c.deleted_at IS NULL
        ORDER BY c.created_at DESC
    """
    return execute_query(query)
```

## Seguridad

### Autenticacion JWT

```python
# Estructura del token
{
    "sub": "user_id",
    "email": "usuario@empresa.com",
    "name": "Nombre Usuario",
    "exp": 1234567890  # Expiracion
}
```

### Validacion de Requests

1. Nginx valida headers basicos
2. FastAPI valida JWT en cada request protegido
3. Pydantic valida estructura de datos
4. Queries parametrizadas previenen SQL injection

### CORS

Configurado para permitir solo origenes especificos:

```python
CORS_ORIGINS = ["http://localhost:3000"]
```

## Despliegue

### Docker Compose

```yaml
services:
  frontend:
    # React + Nginx
    # Sirve archivos estaticos
    # Proxea /api/ al backend

  api:
    # FastAPI + Uvicorn
    # Conecta a MySQL del host
    # Expone puerto 8001
```

### Puertos

| Servicio | Interno | Externo |
|----------|---------|---------|
| Frontend | 80 | 3000 |
| API | 8000 | 8001 |

### Variables de Entorno

Ver `.env.example` para lista completa de variables requeridas.

## Consideraciones de Migracion

### Compatibilidad con Laravel

1. **Misma base de datos**: El sistema React/FastAPI usa la misma BD MySQL
2. **Mismas tablas**: No se modificaron estructuras existentes
3. **Mismos usuarios**: Autenticacion contra tabla `users` de Laravel

### Diferencias con Laravel

1. **Frontend separado**: SPA vs Blade templates
2. **API RESTful**: Endpoints JSON vs controllers MVC
3. **Autenticacion JWT**: Token vs sesiones

## Proximos Pasos (Roadmap)

1. Agregar tests unitarios e integracion
2. Implementar logging centralizado
3. Agregar monitoreo de performance
4. Documentar API con OpenAPI/Swagger mejorado
