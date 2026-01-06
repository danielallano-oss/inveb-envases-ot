# Configuracion de Entorno Local - Envases OT

Este documento describe los pasos necesarios para configurar el sistema Envases-OT en un entorno local de desarrollo.

## Requisitos

- Docker y Docker Compose
- PHP 7.4+
- Acceso a la base de datos QAS (envases-ot.inveb.cl) para sincronizacion inicial

## Arquitectura Docker

El sistema utiliza los siguientes contenedores:

| Contenedor | Descripcion |
|------------|-------------|
| `inveb-app` | Aplicacion Laravel (PHP-FPM + Apache) |
| `inveb-mysql-compose` | Base de datos MySQL 8.0 |

## Configuracion Inicial

### 1. Levantar contenedores

```bash
docker-compose up -d
```

### 2. Configurar base de datos

Ejecutar las migraciones de Laravel:

```bash
docker exec inveb-app php artisan migrate
```

## Tablas de Catalogo Requeridas

Para que el sistema funcione correctamente, las siguientes tablas deben tener datos. Estas tablas NO se crean con migraciones y deben sincronizarse desde QAS o importarse manualmente.

### Tablas Principales (Requeridas)

| Tabla | Descripcion | Registros Minimos |
|-------|-------------|-------------------|
| `users` | Usuarios del sistema | Al menos 1 usuario activo |
| `roles` | Roles de usuario | 18 |
| `clients` | Clientes | Segun OTs a visualizar |
| `plantas` | Plantas | 3 |
| `canals` | Canales de venta | 6 |
| `cotizacion_estados` | Estados de cotizacion | 6 |

### Tablas de Catalogos para Combos

| Tabla | Descripcion | Campo Formulario |
|-------|-------------|------------------|
| `product_types` | Tipos de producto | TIPO ITEM |
| `impresion` | Tipos de impresion | IMPRESION |
| `fsc` | Certificaciones FSC | FSC |
| `tipos_cintas` | Tipos de cinta | CINTA |
| `colors` | Colores disponibles | COLORES |
| `cartons` | Tipos de carton | CARTON |
| `carton_esquineros` | Esquineros de carton | ESQUINERO |
| `materials` | Materiales | MATERIAL |
| `reference_types` | Tipos de referencia | REFERENCIA (tipo) |
| `product_type_developing` | Tipos desarrollo | TIPO PRODUCTO (desarrollo) |

### Tablas de Control de Cascada (CRITICAS)

Estas tablas controlan la logica de habilitacion de campos en el formulario de OT:

| Tabla | Descripcion | Registros |
|-------|-------------|-----------|
| `relacion_filtro_ingresos_principales` | Relaciones de filtro cascada | 75 |
| `plantas` | Plantas disponibles | 3 |

**Sin estas tablas, los combos del formulario de OT no se habilitaran correctamente.**

### Tabla de Secuencias

| Tabla | Descripcion | Valor Inicial |
|-------|-------------|---------------|
| `materials_codes` | Secuencia de codigos material | ID >= 700000 |

## Scripts de Sincronizacion

Se incluyen scripts PHP para sincronizar datos desde QAS:

### sync_users.php
Sincroniza usuarios necesarios para pruebas.

### sync_clients.php
Sincroniza clientes referenciados en OTs.

### sync_catalogos.php
Sincroniza todas las tablas de catalogo.

### sync_filtros.php
Sincroniza la tabla `relacion_filtro_ingresos_principales` (cascada de campos).

### Ejecucion de scripts

```bash
docker exec inveb-app php sync_users.php
docker exec inveb-app php sync_clients.php
docker exec inveb-app php sync_catalogos.php
docker exec inveb-app php sync_filtros.php
```

## Problemas Conocidos y Soluciones

### 1. Error: "Field 'titulo' doesn't have a default value"

La tabla `managements` requiere que el campo `titulo` acepte NULL:

```sql
ALTER TABLE managements MODIFY COLUMN titulo VARCHAR(255) NULL DEFAULT '';
```

### 2. Error: "Trying to get property 'id' of non-object" en MaterialsCode

La tabla `materials_codes` debe tener al menos un registro inicial:

```sql
INSERT INTO materials_codes (id, created_at, updated_at) VALUES (700000, NOW(), NOW());
```

### 3. Combos deshabilitados en formulario OT

Si los combos CINTA, RECUBRIMIENTO, PLANTA OBJETIVO o COLOR CARTON estan deshabilitados:

1. Verificar que `relacion_filtro_ingresos_principales` tenga datos
2. Verificar que `plantas` tenga datos
3. Verificar que `cartons` tenga el campo `impresion_id` con valores

### 4. Columnas de cartons con tipos incorrectos

Las columnas de codigo en `cartons` deben ser VARCHAR:

```sql
ALTER TABLE cartons MODIFY COLUMN codigo_tapa_interior VARCHAR(200) NULL;
ALTER TABLE cartons MODIFY COLUMN codigo_onda_1 VARCHAR(200) NULL;
ALTER TABLE cartons MODIFY COLUMN codigo_onda_1_2 VARCHAR(200) NULL;
ALTER TABLE cartons MODIFY COLUMN codigo_tapa_media VARCHAR(200) NULL;
ALTER TABLE cartons MODIFY COLUMN codigo_onda_2 VARCHAR(200) NULL;
ALTER TABLE cartons MODIFY COLUMN codigo_tapa_exterior VARCHAR(200) NULL;
```

## Verificacion del Entorno

Ejecutar el siguiente script para verificar que todas las tablas tienen datos:

```bash
docker exec inveb-app php check_setup.php
```

## Conteos Esperados

| Tabla | Conteo Minimo |
|-------|---------------|
| users | 1+ |
| clients | 10+ |
| plantas | 3 |
| canals | 6 |
| cotizacion_estados | 6 |
| product_types | 5+ |
| impresion | 7 |
| fsc | 7 |
| tipos_cintas | 2 |
| colors | 1000+ |
| cartons | 200+ |
| materials | 2000+ |
| relacion_filtro_ingresos_principales | 75 |
| materials_codes | 1+ |

## Flujo de Cascada del Formulario OT

El formulario de creacion de OT tiene campos que se habilitan en cascada:

```
TIPO ITEM -> IMPRESION -> FSC -> CINTA -> RECUBRIMIENTO INTERNO
    -> RECUBRIMIENTO EXTERNO -> PLANTA OBJETIVO -> COLOR CARTON
```

La tabla `relacion_filtro_ingresos_principales` define que combinaciones son validas entre estos campos.

## Conexion a Base de Datos QAS (Solo lectura)

```php
$qas = new PDO(
    "mysql:host=envases-ot.inveb.cl;dbname=envases_ot",
    "tandina",
    "1a35a2f5a454526a7fb54f98da4117f0"
);
```

**Nota:** Esta conexion es solo para sincronizacion inicial. El sistema local debe funcionar de forma independiente.

## URLs Hardcodeadas Corregidas

**IMPORTANTE:** Algunos archivos JavaScript tenian URLs hardcodeadas a produccion que causaban redireccion al ambiente QAS/PRD.

Se corrigieron los siguientes archivos cambiando las URLs absolutas a relativas:

| Archivo | Problema | Solucion |
|---------|----------|----------|
| `public/js/modalAsignacion.js` | rootURL apuntaba a https://envases-ot.inveb.cl/ | Cambiado a "/" |
| `public/js/modalOT.js` | rootURL apuntaba a http://test.envases-ot.inveb.cl/ | Cambiado a "/" |
| `public/js/ot-muestras.js` | URLs de PDFs hardcodeadas | Cambiadas a rutas relativas |
| `public/js/cotizador/detalle-cotizacion.js` | Link a gestionarOt hardcodeado | Cambiado a ruta relativa |
| `public/js/cotizador/detalle-cotizacion-externo.js` | Link a gestionarOt hardcodeado | Cambiado a ruta relativa |
| `public/js/cotizador/detalle-cotizacion-externo-aprobacion.js` | Link a gestionarOt hardcodeado | Cambiado a ruta relativa |

**Nota:** Aun hay URLs hardcodeadas en templates de email y algunas vistas blade que cargan imagenes desde produccion. Estas son menos criticas para pruebas locales pero deben considerarse para un ambiente completamente aislado.

## Usuarios de Prueba

Para facilitar pruebas con diferentes roles, se configuraron usuarios con password `password`:

```bash
docker exec inveb-app php get_ruts.php
```

Este script muestra los RUTs disponibles para login. Tambien se puede usar `list_users.php` para ver todos los usuarios agrupados por rol.
