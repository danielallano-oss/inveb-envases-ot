# INVEB Envases OT - Sistema de Ordenes de Trabajo

Sistema de gestion de Ordenes de Trabajo para INVEB Envases Chile, migrado de Laravel a una arquitectura moderna React + FastAPI.

## Stack Tecnologico

### Frontend
- **React** 18.2 con TypeScript
- **Vite** 5.0 - Build tool
- **TanStack Query** (React Query) - Estado del servidor
- **Styled Components** - Estilos
- **Chart.js** - Graficos
- **Axios** - Cliente HTTP

### Backend
- **Python** 3.12+
- **FastAPI** 0.109 - Framework web
- **SQLModel** - ORM (para modelos auxiliares)
- **PyMySQL** - Conexion a MySQL
- **PyJWT** - Autenticacion JWT
- **ReportLab** - Generacion de PDFs
- **OpenPyXL** - Generacion de Excel

### Infraestructura
- **MySQL** - Base de datos principal (compartida con Laravel)
- **Docker** - Containerizacion
- **Nginx** - Proxy reverso (frontend)

## Estructura del Proyecto

```
msw-envases-ot/
├── frontend/                 # Aplicacion React
│   ├── src/
│   │   ├── components/       # Componentes reutilizables
│   │   ├── hooks/            # Custom hooks (React Query)
│   │   ├── pages/            # Paginas/vistas
│   │   │   ├── Cotizaciones/ # Modulo de cotizaciones
│   │   │   └── Dashboard/    # Dashboard principal
│   │   ├── services/         # API clients
│   │   ├── theme/            # Estilos globales
│   │   └── types/            # TypeScript types
│   ├── nginx.conf            # Configuracion nginx
│   └── Dockerfile
├── src/                      # Backend FastAPI
│   ├── app/
│   │   ├── config/           # Configuracion
│   │   ├── models/           # Modelos SQLModel
│   │   ├── routers/          # Endpoints API
│   │   ├── schemas/          # Pydantic schemas
│   │   ├── services/         # Logica de negocio
│   │   └── utils/            # Utilidades
│   └── main.py
├── docker-compose.yaml
├── Dockerfile
├── requirements.txt
├── INSTALL.md                # Guia de instalacion
└── ARCHITECTURE.md           # Documentacion tecnica
```

## Inicio Rapido

### Con Docker Compose (Recomendado)

```bash
# Clonar repositorio
git clone <repo-url>
cd msw-envases-ot

# Configurar variables de entorno
cp .env.example .env
# Editar .env con valores de produccion

# Levantar servicios
docker-compose up -d

# Verificar estado
docker-compose ps

# Ver logs
docker-compose logs -f api
docker-compose logs -f frontend
```

**URLs disponibles:**
- Frontend: http://localhost:3000
- API: http://localhost:8001
- API Docs (Swagger): http://localhost:8001/docs

### Desarrollo Local

Ver [INSTALL.md](./INSTALL.md) para instrucciones detalladas de instalacion local.

## Modulos Principales

### Cotizaciones
- Listado con filtros avanzados
- Creacion/Edicion de cotizaciones
- Gestion de detalles (productos)
- Generacion de PDF
- Exportacion a Excel

### Dashboard
- Indicadores de rendimiento (KPIs)
- Graficos de seguimiento
- Metricas de conversion

### Maestros
- Clientes
- Tipos de producto
- Configuraciones de negocio

## API Endpoints

### Autenticacion
- `POST /api/v1/auth/login` - Login
- `GET /api/v1/auth/me` - Usuario actual

### Cotizaciones
- `GET /api/v1/cotizaciones/` - Listar
- `GET /api/v1/cotizaciones/{id}` - Obtener detalle
- `POST /api/v1/cotizaciones/` - Crear
- `PUT /api/v1/cotizaciones/{id}` - Actualizar
- `DELETE /api/v1/cotizaciones/{id}` - Eliminar

### Work Orders
- `GET /api/v1/work-orders/` - Listar ordenes
- `GET /api/v1/work-orders/form-options-complete` - Opciones de formulario

### Dashboard
- `GET /api/v1/dashboard/kpis` - Indicadores
- `GET /api/v1/dashboard/seguimiento` - Seguimiento

## Variables de Entorno

Ver [.env.example](./.env.example) para todas las variables disponibles.

Variables criticas:
```env
# Base de datos MySQL (REQUERIDO)
LARAVEL_MYSQL_HOST=localhost
LARAVEL_MYSQL_PORT=3306
LARAVEL_MYSQL_DATABASE=envases_ot
LARAVEL_MYSQL_USER=root
LARAVEL_MYSQL_PASSWORD=secret

# JWT (REQUERIDO)
JWT_SECRET_KEY=tu-clave-secreta-segura
JWT_EXPIRATION_HOURS=24
```

## Documentacion Adicional

- [INSTALL.md](./INSTALL.md) - Guia de instalacion completa
- [ARCHITECTURE.md](./ARCHITECTURE.md) - Arquitectura del sistema

## Notas de Migracion

Este sistema es una migracion del sistema Laravel original. Puntos importantes:

1. **Base de datos compartida**: El sistema usa la misma base de datos MySQL que Laravel (`envases_ot`)
2. **Autenticacion**: Validacion de usuarios contra la tabla `users` de Laravel
3. **Compatibilidad**: Los datos creados son compatibles con el sistema Laravel existente

## Soporte

Sistema desarrollado por Tecnoandina para INVEB Chile.
