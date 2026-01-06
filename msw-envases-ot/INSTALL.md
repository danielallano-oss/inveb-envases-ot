# Guia de Instalacion - INVEB Envases OT

Esta guia detalla los pasos para instalar y ejecutar el sistema INVEB Envases OT.

## Requisitos Previos

### Software Requerido
- **Docker** 20.10+ y **Docker Compose** 2.0+
- **Git**

### Para Desarrollo Local (sin Docker)
- **Python** 3.12+
- **Node.js** 20+
- **MySQL** 8.0+

### Base de Datos
- Acceso a la base de datos MySQL `envases_ot` (existente del sistema Laravel)

## Instalacion con Docker (Recomendado)

### 1. Clonar el Repositorio

```bash
git clone <repository-url>
cd msw-envases-ot
```

### 2. Configurar Variables de Entorno

```bash
# Copiar archivo de ejemplo
cp .env.example .env

# Editar con valores reales
nano .env  # o usar tu editor preferido
```

**Variables criticas a configurar:**

```env
# Conexion a MySQL (base de datos Laravel existente)
LARAVEL_MYSQL_HOST=host.docker.internal  # Para conectar al MySQL del host
LARAVEL_MYSQL_PORT=3306
LARAVEL_MYSQL_DATABASE=envases_ot
LARAVEL_MYSQL_USER=root
LARAVEL_MYSQL_PASSWORD=tu_password

# JWT - Cambiar por clave segura
JWT_SECRET_KEY=una-clave-segura-de-al-menos-32-caracteres
```

### 3. Construir y Levantar Servicios

```bash
# Construir imagenes
docker-compose build

# Levantar servicios en background
docker-compose up -d

# Verificar que esten corriendo
docker-compose ps
```

### 4. Verificar Instalacion

```bash
# Ver logs del API
docker-compose logs -f api

# Probar endpoint de salud
curl http://localhost:8001/health
```

### 5. Acceder al Sistema

- **Frontend**: http://localhost:3000
- **API**: http://localhost:8001
- **API Docs**: http://localhost:8001/docs

## Instalacion Manual (Desarrollo)

### Backend (FastAPI)

```bash
# Crear entorno virtual
python -m venv venv

# Activar entorno
# Windows:
venv\Scripts\activate
# Linux/Mac:
source venv/bin/activate

# Instalar dependencias
pip install -r requirements.txt

# Configurar variables
cp .env.example .env
# Editar .env con valores locales

# Ejecutar servidor de desarrollo
uvicorn src.main:app --reload --port 8001
```

### Frontend (React)

```bash
cd frontend

# Instalar dependencias
npm install

# Configurar API URL
# Crear/editar .env.local:
echo "VITE_API_URL=http://localhost:8001/api/v1" > .env.local

# Ejecutar servidor de desarrollo
npm run dev
```

## Comandos Utiles

### Docker

```bash
# Ver logs en tiempo real
docker-compose logs -f

# Reiniciar servicio especifico
docker-compose restart api

# Detener todos los servicios
docker-compose down

# Reconstruir sin cache
docker-compose build --no-cache
```

### Desarrollo

```bash
# Backend - Tests
pytest

# Backend - Linting
black src/
isort src/
flake8 src/

# Frontend - Build produccion
cd frontend && npm run build

# Frontend - Linting
cd frontend && npm run lint
```

## Solucion de Problemas

### Error de conexion a MySQL

**Sintoma**: `Connection refused` o `Access denied`

**Soluciones**:
1. Verificar que MySQL este corriendo
2. Confirmar credenciales en `.env`
3. Si usas Docker, usar `host.docker.internal` como host

```bash
# Probar conexion desde container
docker-compose exec api python -c "
from src.app.database import test_connection
test_connection()
"
```

### Puerto en uso

**Sintoma**: `Address already in use`

**Solucion**: Cambiar puertos en `docker-compose.yaml`:
```yaml
api:
  ports:
    - "8002:8000"  # Usar puerto 8002 en vez de 8001
```

### Frontend no conecta al API

**Sintoma**: Errores de red o CORS

**Soluciones**:
1. Verificar que el API este corriendo
2. Revisar `VITE_API_URL` en frontend
3. Confirmar configuracion CORS en backend

### Contenedor no arranca

```bash
# Ver logs de error
docker-compose logs api

# Verificar estado
docker-compose ps

# Reiniciar todo
docker-compose down && docker-compose up -d
```

## Actualizacion

```bash
# Obtener ultimos cambios
git pull origin main

# Reconstruir imagenes
docker-compose build

# Reiniciar servicios
docker-compose up -d
```

## Estructura de Puertos

| Servicio | Puerto Interno | Puerto Externo |
|----------|---------------|----------------|
| Frontend | 80 | 3000 |
| API | 8000 | 8001 |
| MySQL | 3306 | (host) |

## Soporte

Para soporte tecnico, contactar al equipo de desarrollo de Tecnoandina.
