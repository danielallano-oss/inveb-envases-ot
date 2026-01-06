# Documentacion Tecnica - INVEB Envases OT

**Sistema**: MS-004 CascadeService
**Version**: 1.0
**Fecha**: 2025-12-26
**Cliente**: Tecnoandina - INVEB Chile

---

## Tabla de Contenidos

1. [Resumen Ejecutivo](#1-resumen-ejecutivo)
2. [Arquitectura del Sistema](#2-arquitectura-del-sistema)
3. [Modulos y Funcionalidades](#3-modulos-y-funcionalidades)
4. [Roles y Permisos](#4-roles-y-permisos)
5. [Workflow de OTs](#5-workflow-de-ots)
6. [Formulario de Cascada](#6-formulario-de-cascada)
7. [Pantallas del Frontend](#7-pantallas-del-frontend)
8. [API Endpoints Principales](#8-api-endpoints-principales)
9. [Guia de Instalacion Rapida](#9-guia-de-instalacion-rapida)

---

## 1. Resumen Ejecutivo

### 1.1 Objetivo del Sistema

INVEB Envases OT es un sistema de gestion de Ordenes de Trabajo (OT) para la industria de envases de carton corrugado. El sistema permite:

- **Crear y gestionar OTs** desde el area comercial hasta catalogacion
- **Controlar el workflow** entre 6 areas de trabajo
- **Gestionar muestras fisicas** en sala de corte
- **Generar cotizaciones** con calculos automatizados
- **Visualizar reportes** de rendimiento y KPIs

### 1.2 Metricas del Sistema

| Metrica | Valor |
|---------|-------|
| Modulos principales | 9 |
| Funcionalidades totales | ~180 |
| Roles de usuario | 19 |
| Areas de trabajo | 9 |
| Estados de OT | 22 |
| Mantenedores CRUD | 48 |
| Reportes disponibles | 15 |

### 1.3 Stack Tecnologico

| Capa | Tecnologia |
|------|------------|
| Frontend | React 18 + TypeScript + Vite |
| Backend API | FastAPI (Python 3.12) |
| Base de Datos | MySQL 8.0 (compartida con Laravel) |
| Contenedores | Docker + Docker Compose |
| Autenticacion | JWT |

---

## 2. Arquitectura del Sistema

### 2.1 Diagrama de Componentes

```
┌─────────────────────────────────────────────────────────────┐
│                    CLIENTE (Browser)                         │
│                   http://localhost:3000                      │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│              FRONTEND (React + Vite)                        │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐       │
│  │  Login   │ │Dashboard │ │  OT CRUD │ │Reportes  │       │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘       │
│  ┌──────────┐ ┌──────────┐ ┌──────────────────────┐        │
│  │Manten.   │ │Notific.  │ │  CascadeForm        │        │
│  └──────────┘ └──────────┘ └──────────────────────┘        │
└─────────────────────────────────────────────────────────────┘
                              │ API REST
                              ▼
┌─────────────────────────────────────────────────────────────┐
│              BACKEND API (FastAPI)                          │
│                  http://localhost:8001                       │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐       │
│  │  Auth    │ │WorkOrders│ │ Reports  │ │Manteined.│       │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘       │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│              BASE DE DATOS (MySQL 8.0)                      │
│                  Puerto: 3306/3307                          │
│  ┌─────────────────────────────────────────────────────┐   │
│  │  envases_ot (Base compartida con Laravel)            │   │
│  │  - work_orders, users, clients, muestras, etc.       │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

### 2.2 Puertos del Sistema (Desarrollo Local)

> **NOTA**: Las URLs `localhost` son para entorno de desarrollo local.
> El sistema en produccion esta desplegado en el dominio interno de Tecnoandina
> y no es accesible desde internet publico.

| Servicio | Puerto | URL (desarrollo) |
|----------|--------|------------------|
| Frontend React | 3000 | http://localhost:3000 |
| Backend API | 8001 | http://localhost:8001 |
| API Docs (Swagger) | 8001 | http://localhost:8001/docs |
| MySQL | 3306 | localhost:3306 |

---

## 3. Modulos y Funcionalidades

### 3.1 Modulo de Autenticacion

| Funcionalidad | Descripcion |
|---------------|-------------|
| Login tradicional | Autenticacion con RUT y contrasena |
| Login Azure AD | Autenticacion corporativa (opcional) |
| Recuperar contrasena | Envio de link por email |
| Cambiar contrasena | Actualizacion de credenciales |

### 3.2 Modulo de Ordenes de Trabajo (OT)

| Funcionalidad | Descripcion |
|---------------|-------------|
| Listado de OTs | Grilla con filtros avanzados y paginacion |
| Crear OT | Formulario completo con cascada de campos |
| Editar OT | Modificacion de OT existente |
| Gestionar OT | Transiciones de estado entre areas |
| Duplicar OT | Copia de OT existente |
| Aprobar/Rechazar OT | Workflow de aprobacion |
| Crear desde Excel | Importacion masiva |

### 3.3 Modulo de Muestras

| Funcionalidad | Descripcion |
|---------------|-------------|
| Crear muestra | Nueva muestra para OT |
| Gestionar sala de corte | Tracking de muestras fisicas |
| Etiquetas PDF | Generacion de etiquetas |
| Priorizar muestras | Marcar muestras urgentes |

### 3.4 Modulo de Cotizador

| Funcionalidad | Descripcion |
|---------------|-------------|
| Crear cotizacion | Nueva cotizacion con calculos |
| Versionar cotizacion | Multiples versiones |
| Aprobar cotizacion | Flujo de aprobacion |
| Generar PDF | Exportacion para cliente |

### 3.5 Modulo de Reportes

| Reporte | Descripcion |
|---------|-------------|
| Carga OT por Mes | OTs ingresadas mensualmente |
| Conversion OT | Tasa de OTs completadas |
| Tiempos por Area | Lead time por departamento |
| Motivos de Rechazo | Analisis de rechazos |
| Rechazos por Mes | Tendencia de rechazos |
| Anulaciones | OTs canceladas |
| Sala de Muestras | KPIs de muestras |
| Tiempo Primera Muestra | Lead time de muestras |

### 3.6 Modulo de Mantenedores

**Mantenedores CRUD principales:**
- Usuarios, Clientes, Sectores
- Jerarquias (3 niveles)
- Tipos de Producto, Estilos, Cartones
- Colores, Adhesivos, Materiales
- Plantas, Secuencias Operacionales

**Mantenedores con carga Excel:**
- Cartones corrugados, Papeles, Fletes
- Mermas, Paletizados, Tarifarios
- Factores de seguridad, ondas, desarrollo

---

## 4. Roles y Permisos

### 4.1 Catalogo de Roles

| ID | Rol | Area | Permisos Clave |
|----|-----|------|----------------|
| 1 | Administrador | - | Acceso total |
| 2 | Gerente | - | Visualizacion global |
| 3 | Jefe de Ventas | Ventas | Aprueba OTs, gestiona vendedores |
| 4 | Vendedor | Ventas | Crea OTs, gestiona clientes |
| 5 | Jefe de Desarrollo | Desarrollo | Gestiona ingenieros |
| 6 | Ingeniero | Desarrollo | Diseno estructural |
| 7 | Jefe de Diseno | Diseno | Gestiona disenadores |
| 8 | Disenador | Diseno | Diseno grafico |
| 9 | Jefe de Catalogacion | Catalogacion | Gestiona catalogadores |
| 10 | Catalogador | Catalogacion | Crea codigos SAP |
| 13 | Jefe de Muestras | Muestras | Gestiona sala de corte |
| 14 | Tecnico de Muestras | Muestras | Corta muestras fisicas |
| 18 | Super Administrador | Admin | Todo + modificar OTs cerradas |

### 4.2 Matriz de Permisos

| Accion | Admin | Jefe | Operador |
|--------|-------|------|----------|
| Crear OT | Si | Si | Si (su area) |
| Editar OT | Si | Su area | Su OT |
| Aprobar OT | Si | Si | No |
| Asignar OT | Si | Si | No |
| Ver Reportes | Si | Si | Limitado |
| Mantenedores | Si | No | No |

---

## 5. Workflow de OTs

### 5.1 Areas de Trabajo

| ID | Area | Descripcion |
|----|------|-------------|
| 1 | Ventas | Ingreso OTs, gestion comercial |
| 2 | Desarrollo | Diseno estructural, ingenieria |
| 3 | Diseno e Impresion | Diseno grafico, arte |
| 4 | Precatalogacion | Revision previa a catalogacion |
| 5 | Catalogacion | Creacion codigos SAP |
| 6 | Muestras | Sala de corte, muestras fisicas |

### 5.2 Estados de OT

| Estado | Descripcion | Siguiente |
|--------|-------------|-----------|
| Proceso Ventas | OT en gestion comercial | Desarrollo |
| Proceso Desarrollo | Diseno estructural | Diseno |
| Laboratorio | Pruebas de laboratorio | Diseno |
| Muestra | Requiere muestra fisica | Sala Muestra |
| Proceso Diseno | Diseno grafico | Precatalogacion |
| Precatalogacion | Revision previa | Catalogacion |
| Catalogacion | Creando codigo SAP | Terminada |
| **OT Terminada** | Completada exitosamente | FIN |

**Estados de Espera:**
- Consulta Cliente, Espera OC, Falta Definicion, VB Cliente

**Estados Finales Alternativos:**
- Perdido, Anulada, Rechazado, Entregado

### 5.3 Diagrama de Flujo

```
[VENTAS] ─────► [DESARROLLO] ─────► [DISENO] ─────► [PRECATALOG.] ─────► [CATALOGACION] ─────► [TERMINADA]
    │               │                   │                                        │
    ▼               ▼                   ▼                                        ▼
[Consulta]     [Laboratorio]       [VB Cliente]                            [Rechazado]
[Espera OC]    [Muestra] ──► [SALA MUESTRAS]
[Perdido]      [Entregado]
[Anulada]
```

---

## 6. Formulario de Cascada

### 6.1 Concepto

El formulario de creacion/edicion de OT tiene un sistema de **campos en cascada** donde cada campo habilita o filtra los siguientes segun reglas de negocio.

### 6.2 Secuencia de Campos

```
TIPO ITEM → IMPRESION → FSC → CINTA → RECUB.INTERNO → RECUB.EXTERNO → PLANTA → COLOR → CARTON
    (1)         (2)      (3)    (4)        (5)             (6)          (7)      (8)     (9)
```

### 6.3 Reglas Principales

| Campo | Habilita | Condicion |
|-------|----------|-----------|
| Tipo Item | Impresion | Siempre |
| Impresion | FSC | Siempre |
| FSC | Cinta | Validacion de combinacion |
| Cinta | Recubrimientos | Segun plantas disponibles |
| Recubrimiento Ext. | Planta | Segun combinacion |
| Planta | Color Carton | Configura secuencias operacionales |
| Color Carton | Carton | Filtra por planta e impresion |

### 6.4 Tabla de Combinaciones

La tabla `relacion_filtro_ingresos_principales` contiene **75 combinaciones validas** que determinan que opciones estan disponibles en cada paso.

---

## 7. Pantallas del Frontend

### 7.1 Login

```
┌─────────────────────────────────┐
│         INVEB Sistema OT        │
│                                 │
│  RUT: [12345678-9]              │
│  Contrasena: [••••••••]         │
│                                 │
│  [      Ingresar      ]         │
└─────────────────────────────────┘
```

### 7.2 Dashboard de OTs

```
┌────────────────────────────────────────────────────────────────┐
│ Ordenes de Trabajo                    [Crear OT] [Notificaciones] │
├────────────────────────────────────────────────────────────────┤
│ FILTROS: [Fecha] [Cliente] [Estado] [Area] [Filtrar] [Limpiar] │
├────────────────────────────────────────────────────────────────┤
│ OT │Fecha│Cliente│Descripcion│Estado│T│V│D│M│G│E│P│C│Acciones │
├────┼─────┼───────┼───────────┼──────┼─┼─┼─┼─┼─┼─┼─┼─┼─────────┤
│123 │12/12│ACME   │Caja xyz   │ REV  │2│1│0│1│0│0│0│0│ [E] [G] │
└────┴─────┴───────┴───────────┴──────┴─┴─┴─┴─┴─┴─┴─┴─┴─────────┘
```

### 7.3 Gestionar OT

```
┌────────────────────────────────────────────────────────────────┐
│ Gestionar OT #123                                    [Volver]   │
├─────────────────────────────┬──────────────────────────────────┤
│ TRANSICION DE ESTADO        │ HISTORIAL                        │
│                             │                                  │
│ Estado Actual:              │ [Ventas] [En Revision]           │
│ [Ventas] [En Revision]      │ 15/12/2024 - Maria Garcia        │
│                             │ "Enviado para revision"          │
│ Nueva Area: [Seleccione ▼]  │                                  │
│ Nuevo Estado: [Seleccione ▼]│ [Ventas] [Pendiente]             │
│ Observacion: [          ]   │ 14/12/2024 - Juan Perez          │
│                             │ "OT creada"                      │
│ [Realizar Transicion]       │                                  │
└─────────────────────────────┴──────────────────────────────────┘
```

### 7.4 Reportes

```
┌────────────────────────────────────────────────────────────────┐
│ Rechazos por Mes                                     [Volver]   │
├────────────────────────────────────────────────────────────────┤
│ [Ano: 2024 ▼] [Actualizar]                                     │
├────────────────────────────────────────────────────────────────┤
│ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌────────────┐ │
│ │ 156         │ │ 13.0        │ │ 12          │ │ Desarrollo │ │
│ │ Total Ano   │ │ Promedio/Mes│ │ Ultimo Mes  │ │ + Rechazos │ │
│ └─────────────┘ └─────────────┘ └─────────────┘ └────────────┘ │
├────────────────────────────────────────────────────────────────┤
│ [Grafico de Barras - Rechazos por Mes]                         │
│ [Grafico Dona - Distribucion por Area]                         │
├────────────────────────────────────────────────────────────────┤
│ Detalle por Mes y Area                                         │
│ Mes      │ Area        │ Rechazos                              │
│ Enero    │ Desarrollo  │ 15                                    │
│ Enero    │ Diseno      │ 8                                     │
└──────────┴─────────────┴───────────────────────────────────────┘
```

---

## 8. API Endpoints Principales

### 8.1 Autenticacion

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/api/v1/auth/login` | POST | Login con RUT y password |
| `/api/v1/auth/me` | GET | Datos del usuario autenticado |

### 8.2 Ordenes de Trabajo

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/api/v1/work-orders/` | GET | Lista paginada con filtros |
| `/api/v1/work-orders/{id}` | GET | Detalle de OT |
| `/api/v1/work-orders/` | POST | Crear nueva OT |
| `/api/v1/work-orders/{id}` | PUT | Actualizar OT |
| `/api/v1/work-orders/{id}/transition` | POST | Cambiar estado |
| `/api/v1/work-orders/filter-options` | GET | Opciones para filtros |

### 8.3 Reportes

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/api/v1/reports/rechazos-mes` | GET | Rechazos por mes |
| `/api/v1/reports/ots-completadas` | GET | OTs completadas por fechas |
| `/api/v1/reports/anulaciones` | GET | OTs anuladas |
| `/api/v1/reports/muestras` | GET | Reporte de muestras |
| `/api/v1/reports/sala-muestra` | GET | KPIs sala de muestras |
| `/api/v1/reports/tiempo-primera-muestra` | GET | Lead time muestras |

### 8.4 Cascada de Campos

| Endpoint | Metodo | Descripcion |
|----------|--------|-------------|
| `/api/v1/form-options/` | GET | Todas las opciones del formulario |
| `/api/v1/cascade-rules/` | GET | Reglas de cascada |
| `/api/v1/cascade-combinations/validate/` | GET | Validar combinacion |

---

## 9. Guia de Instalacion Rapida

### 9.1 Requisitos

- Docker 20.10+ y Docker Compose 2.0+
- Acceso a base de datos MySQL `envases_ot`

### 9.2 Pasos de Instalacion

```bash
# 1. Clonar repositorio
git clone <repository-url>
cd msw-envases-ot

# 2. Configurar variables de entorno
cp .env.example .env
# Editar .env con credenciales de MySQL

# 3. Construir y levantar servicios
docker-compose build
docker-compose up -d

# 4. Verificar instalacion
docker-compose ps
curl http://localhost:8001/health
```

### 9.3 Accesos (Desarrollo Local)

> **NOTA**: Estas URLs son para entorno de desarrollo local unicamente.
> En produccion, el sistema esta desplegado en infraestructura interna de Tecnoandina.

| Servicio | URL (desarrollo) |
|----------|------------------|
| Frontend | http://localhost:3000 |
| API | http://localhost:8001 |
| API Docs | http://localhost:8001/docs |

### 9.4 Credenciales de Prueba

```
RUT: (usar usuario existente en MySQL)
Password: (contrasena del usuario)
```

---

## Documentos de Referencia

Para informacion detallada, consultar los siguientes documentos en la carpeta `docs/`:

| Documento | Contenido |
|-----------|-----------|
| [FUNCIONALIDADES.md](./FUNCIONALIDADES.md) | Lista completa de ~180 funcionalidades |
| [REGLAS_NEGOCIO.md](./REGLAS_NEGOCIO.md) | Reglas de negocio detalladas por modulo |
| [CASCADA_CAMPOS.md](./CASCADA_CAMPOS.md) | Logica del formulario de cascada |
| [PANTALLAS.md](./PANTALLAS.md) | Documentacion detallada de cada pantalla |

---

## Soporte

Para soporte tecnico, contactar al equipo de desarrollo de Tecnoandina.

---

**Documento generado**: 2025-12-26
**Version**: 1.0
