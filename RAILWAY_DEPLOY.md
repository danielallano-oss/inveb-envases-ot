# Guía de Despliegue en Railway - INVEB

## Requisitos Previos

1. Cuenta en [Railway](https://railway.app)
2. Repositorio en GitHub con el código del proyecto

---

## Paso 1: Crear Proyecto en Railway

1. Ir a [Railway Dashboard](https://railway.app/dashboard)
2. Click en **"New Project"**
3. Seleccionar **"Empty Project"**

---

## Paso 2: Agregar Base de Datos MySQL

1. En el proyecto, click en **"+ New"**
2. Seleccionar **"Database"** → **"MySQL"**
3. Railway creará automáticamente las variables:
   - `MYSQL_HOST`
   - `MYSQL_PORT`
   - `MYSQL_DATABASE`
   - `MYSQL_USER`
   - `MYSQL_PASSWORD`

---

## Paso 3: Desplegar FastAPI (API Backend)

1. Click en **"+ New"** → **"GitHub Repo"**
2. Seleccionar tu repositorio
3. En **Settings**:
   - **Root Directory**: `msw-envases-ot`
   - **Watch Paths**: `msw-envases-ot/**`

4. Agregar **Variables de Entorno**:
```
DEBUG=false
ENVIRONMENT=production
LARAVEL_MYSQL_HOST=${{MySQL.MYSQL_HOST}}
LARAVEL_MYSQL_PORT=${{MySQL.MYSQL_PORT}}
LARAVEL_MYSQL_DATABASE=${{MySQL.MYSQL_DATABASE}}
LARAVEL_MYSQL_USER=${{MySQL.MYSQL_USER}}
LARAVEL_MYSQL_PASSWORD=${{MySQL.MYSQL_PASSWORD}}
JWT_SECRET_KEY=tu-clave-secreta-muy-segura-cambiar-esto
JWT_EXPIRATION_HOURS=24
PORT=8000
```

5. En **Settings** → **Networking**:
   - Click en **"Generate Domain"** para obtener URL pública
   - Ejemplo: `inveb-api.up.railway.app`

---

## Paso 4: Desplegar Frontend React

1. Click en **"+ New"** → **"GitHub Repo"**
2. Seleccionar el mismo repositorio
3. En **Settings**:
   - **Root Directory**: `msw-envases-ot/frontend`
   - **Watch Paths**: `msw-envases-ot/frontend/**`

4. Agregar **Variables de Entorno** (Build Variables):
```
VITE_API_URL=https://[TU-API-URL].up.railway.app/api/v1
```
   > Reemplaza `[TU-API-URL]` con la URL generada en el Paso 3

5. En **Settings** → **Networking**:
   - Click en **"Generate Domain"**
   - Ejemplo: `inveb-frontend.up.railway.app`

---

## Paso 5: (Opcional) Desplegar Laravel Backend

Si necesitas el backend Laravel también:

1. Click en **"+ New"** → **"GitHub Repo"**
2. Seleccionar el mismo repositorio
3. En **Settings**:
   - **Root Directory**: `.` (raíz del proyecto)

4. Agregar **Variables de Entorno**:
```
APP_NAME=INVEB
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:pP2XG5u4ZxSBmhykCliLt4S0AE6xFvEjvI3E+T/JpNE=
APP_URL=https://[TU-LARAVEL-URL].up.railway.app
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQL_HOST}}
DB_PORT=${{MySQL.MYSQL_PORT}}
DB_DATABASE=${{MySQL.MYSQL_DATABASE}}
DB_USERNAME=${{MySQL.MYSQL_USER}}
DB_PASSWORD=${{MySQL.MYSQL_PASSWORD}}
```

---

## Paso 6: Importar Base de Datos

### Opción A: Desde Railway CLI

```bash
# Instalar Railway CLI
npm install -g @railway/cli

# Login
railway login

# Conectar al proyecto
railway link

# Importar SQL
railway run mysql -h $MYSQL_HOST -u $MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE < envases_ot_backup_completo.sql
```

### Opción B: Desde MySQL Workbench o DBeaver

1. En Railway, ir al servicio MySQL
2. Click en **"Connect"** para ver credenciales
3. Usar las credenciales para conectar desde tu cliente SQL favorito
4. Importar el archivo `envases_ot_backup_completo.sql`

---

## Usuarios de Prueba

| Rol | RUT | Contraseña |
|-----|-----|------------|
| Administrador | 22222222-2 | admin123 |
| Gerente | 33333333-3 | test123 |
| Jefe de Ventas | 23748870-9 | test123 |
| Vendedor | 11334692-2 | test123 |
| Jefe de Desarrollo | 20649380-1 | test123 |
| Ingeniero | 8106237-4 | test123 |

---

## URLs Finales (Ejemplo)

Una vez desplegado, tendrás URLs como:

- **Frontend**: `https://inveb-frontend.up.railway.app`
- **API**: `https://inveb-api.up.railway.app`
- **API Docs**: `https://inveb-api.up.railway.app/docs`

---

## Troubleshooting

### El frontend no conecta con la API

1. Verificar que `VITE_API_URL` esté correctamente configurado
2. La URL debe incluir `/api/v1` al final
3. Redeploy el frontend después de cambiar variables de entorno

### Error de conexión a MySQL

1. Verificar que las variables `${{MySQL.*}}` estén correctamente referenciadas
2. En Railway, las referencias a otros servicios usan la sintaxis `${{ServiceName.VARIABLE}}`

### El build del frontend falla

1. Verificar que el Root Directory sea `msw-envases-ot/frontend`
2. Revisar los logs de build en Railway

---

## Costos Estimados

- **Free Tier**: $5 USD de crédito mensual
- **Uso típico para demo**: ~$2-3 USD/semana con 3 servicios activos
- Los servicios se pausan automáticamente si no hay tráfico

---

## Soporte

Para problemas con Railway: https://docs.railway.app
