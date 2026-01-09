# MATRIZ DE ROLES Y FUNCIONALIDADES - INVEB OT

## Documento de Validacion de Permisos

**Fecha**: 2026-01-08
**Version**: 1.0
**Fuentes**: Laravel (`ficha-form.blade.php`, `WorkOrderController.php`, `Constants.php`) + FastAPI (`work_orders.py`)

---

## 1. ROLES ACTIVOS EN SISTEMA

| ID | Rol | Usuarios Activos | Area Asociada |
|----|-----|------------------|---------------|
| 1 | Administrador | 5 | - |
| 2 | Gerente | 1 | - |
| 3 | Jefe de Ventas | 1 | Venta (1) |
| 4 | Vendedor | 11 | Venta (1) |
| 5 | Jefe de Desarrollo | 1 | Desarrollo (2) |
| 6 | Ingeniero | 1 | Desarrollo (2) |
| 7 | Jefe de Diseno e Impresion | 1 | Diseno (3) |
| 8 | Disenador | 1 | Diseno (3) |
| 9 | Jefe de Precatalogacion | 1 | Precatalogacion (5) |
| 10 | Precatalogador | 1 | Precatalogacion (5) |
| 11 | Jefe de Catalogacion | 1 | Catalogacion (4) |
| 12 | Catalogador | 1 | Catalogacion (4) |
| 13 | Jefe de Muestras | 1 | Muestras (6) |
| 14 | Tecnico de Muestras | 3 | Muestras (6) |
| 15 | Gerente Comercial | 0 | - |
| 16 | Visualizador | 0 | - |
| 18 | Super Administrador | 0 | - (acceso total) |
| 19 | Vendedor Externo | 2 | Venta (1) |

---

## 2. FUNCIONALIDADES POR ROL

### 2.1 Visualizacion de OTs en Dashboard

| Rol | Regla de Filtrado | Implementado en FastAPI |
|-----|-------------------|-------------------------|
| Vendedor (4) | Solo OTs creadas por el usuario | SI (linea 313) |
| Vendedor Externo (19) | Solo OTs creadas por el usuario | SI (linea 313) |
| Ingeniero (6) | OTs asignadas al usuario | SI (linea 324-327) |
| Disenador (8) | OTs asignadas al usuario + area=3 | SI (linea 324-343) |
| Catalogador (10) | OTs asignadas al usuario + area=4,5 | SI (linea 324-345) |
| Jefe Diseno (7) | OTs en area Diseno (3) | SI (linea 342-343) |
| Jefe Catalogador (9,11) | OTs en area Catalogacion (4,5) | SI (linea 344-345) |
| Otros roles | Todas las OTs | SI |

### 2.2 Creacion de OTs

| Rol | Puede Crear OT | Campos Especiales |
|-----|----------------|-------------------|
| Vendedor (4) | SI | Campo `vendedor_id` = usuario actual |
| Vendedor Externo (19) | SI | Campos adicionales: dato_sub_cliente, contacto |
| Jefe Desarrollo (5) | SI | Puede seleccionar vendedor |
| Ingeniero (6) | SI | Puede seleccionar vendedor |
| Jefe Diseno (7) | SI | Puede seleccionar vendedor |
| Disenador (8) | SI | Puede seleccionar vendedor |
| Super Admin (18) | SI | Acceso total |

### 2.3 Edicion de OTs

#### Campos SIEMPRE Solo Lectura despues de crear:

| Campo | Editable por | Solo Lectura para |
|-------|--------------|-------------------|
| `client_id` (Cliente) | Super Admin (18) | Todos los demas |
| `tipo_solicitud` | Super Admin (18) | Todos los demas |
| `instalacion_cliente` | Super Admin (18) | Todos los demas |

#### Campos con permisos especiales:

| Campo | Editable por Roles |
|-------|-------------------|
| Caracteristicas Estilo (agregar) | 5, 6, 18 |
| Formula McKee | 5, 6 |
| Analisis Anchura | 5, 6 |
| Descripcion Material | 18 |

### 2.4 Aprobaciones

| Funcion | Rol Autorizado | Estado Requerido |
|---------|----------------|------------------|
| Ver OTs pendientes aprobacion | 3 (Jefe Venta), 5 (Jefe Desarrollo) | - |
| Aprobar OT (paso 1) | 3 (Jefe Venta) | aprobacion_jefe_venta = 1 |
| Aprobar OT (paso 2) | 5 (Jefe Desarrollo) | aprobacion_jefe_venta = 2 AND aprobacion_jefe_desarrollo = 1 |

### 2.5 Cambio de Estado

| Rol | Puede cambiar a estados |
|-----|------------------------|
| Vendedor (4) | Estados de Venta: 1-7, 10, 12-18, 20-22 |
| Jefe Venta (3) | Estados de Venta: 1-7, 10, 12-18, 20-22 |
| Ingeniero (6) | Todos los estados |
| Jefe Desarrollo (5) | Todos los estados |
| Super Admin (18) | Todos los estados (1-22) |

---

## 3. PERMISOS EN FORMULARIO CREATE/EDIT

### 3.1 Seccion 2: Datos Comerciales

| Campo | CREATE | EDIT Normal | EDIT Super Admin |
|-------|--------|-------------|------------------|
| Cliente | Editable | Solo Lectura | Editable |
| Tipo Solicitud | Editable | Solo Lectura | Editable |
| Instalacion Cliente | Editable | Solo Lectura | Editable |
| Descripcion | Editable | Editable | Editable |
| Codigo Producto | Editable | Editable | Editable |
| Vol Vta Anual | Editable | Editable | Editable |
| USD | Editable | Editable | Editable |
| Org Venta | Editable | Editable | Editable |
| Canal | Editable | Editable | Editable |
| OC | Editable | Editable | Editable |
| Jerarquias | Editable | Editable | Editable |

### 3.2 Seccion 7: Caracteristicas

| Campo | Todos los Roles | Solo roles 5, 6, 18 |
|-------|-----------------|---------------------|
| CAD | Editable | Editable |
| Matriz | Editable | Editable |
| Estilo | Editable | Editable |
| Caracteristicas Adicionales | Solo Lectura | Editable (boton agregar) |
| Formula McKee | Oculto | Visible y funcional |
| Analisis Anchura | Oculto | Visible y funcional |

### 3.3 Seccion 13: Material Asignado

| Campo | Roles normales | Super Admin (18) |
|-------|----------------|------------------|
| Material Asignado | Solo Lectura | Solo Lectura |
| Descripcion Material | Solo Lectura | Editable |

---

## 4. VALIDACION ACTUAL vs ESPERADA

### 4.1 Issues del Excel Relacionados con Roles

| Issue | Descripcion | Rol Afectado | Estado |
|-------|-------------|--------------|--------|
| #5 | Cliente deberia ser solo lectura en edicion | Todos excepto 18 | PENDIENTE VALIDAR |
| #6 | Tipo Solicitud deberia ser solo lectura en edicion | Todos excepto 18 | PENDIENTE VALIDAR |
| #8 | Instalacion Cliente deberia ser solo lectura en edicion | Todos excepto 18 | PENDIENTE VALIDAR |

### 4.2 Funcionalidades a Validar

| Funcionalidad | Laravel | FastAPI | Frontend | Estado |
|---------------|---------|---------|----------|--------|
| Filtro OTs por creador (Vendedor) | SI | SI | ? | VALIDAR |
| Filtro OTs por asignacion (Ingeniero) | SI | SI | ? | VALIDAR |
| Filtro OTs por area (Diseno) | SI | SI | ? | VALIDAR |
| Selector vendedor en create | SI | ? | ? | VALIDAR |
| Campos readonly en edit | SI | ? | ? | VALIDAR |
| Boton Caracteristicas Estilo | SI | ? | ? | VALIDAR |
| Formula McKee visible | SI | ? | ? | VALIDAR |
| Aprobacion Jefe Venta | SI | SI | ? | VALIDAR |
| Aprobacion Jefe Desarrollo | SI | SI | ? | VALIDAR |

---

## 5. INCONSISTENCIAS DETECTADAS

### 5.1 Inconsistencia en IDs de Roles

**En Laravel (Constants.php):**
```php
const JefeCatalogador = 9;
const Catalogador = 10;
const JefePrecatalogador = 11;
const Precatalogador = 12;
```

**En Base de Datos Railway:**
```
ID 9:  Jefe de Precatalogacion
ID 10: Precatalogador
ID 11: Jefe de Catalogacion
ID 12: Catalogador
```

**Impacto**: El codigo Laravel usa `Constants::Catalogador = 10` pero en la BD ese ID es "Precatalogador".
Esto causa que el filtro de asignacion se aplique al rol incorrecto.

**Recomendacion**: Validar si esto afecta el comportamiento y decidir si corregir en BD o en codigo.

### 5.2 Rol 17 (API) faltante en BD

El rol 17 existe en Constants.php pero no en la tabla roles de Railway.

---

## 6. PRUEBAS RECOMENDADAS

### Por Rol:

#### Vendedor (4):
1. [ ] Login con vendedor@inveb.cl
2. [ ] Verificar que solo ve OTs propias
3. [ ] Crear OT nueva
4. [ ] Verificar campos readonly en edicion
5. [ ] Verificar que NO ve Formula McKee

#### Jefe Desarrollo (5):
1. [ ] Login con jefe.desarrollo@inveb.cl
2. [ ] Verificar que ve todas las OTs del area
3. [ ] Verificar selector de vendedor al crear
4. [ ] Verificar Formula McKee visible
5. [ ] Verificar puede aprobar OTs

#### Super Admin (18):
1. [ ] Login con superadmin (crear usuario si no existe)
2. [ ] Verificar puede editar Cliente en edicion
3. [ ] Verificar puede editar Tipo Solicitud en edicion
4. [ ] Verificar puede editar Descripcion Material

#### Vendedor Externo (19):
1. [ ] Login con vendedor externo
2. [ ] Verificar solo ve OTs propias
3. [ ] Verificar campos adicionales (dato_sub_cliente)
4. [ ] Verificar cliente fijo (EDIPAC)

---

## 7. USUARIOS DE PRUEBA

| Rol | RUT | Password (probable) | Nombre |
|-----|-----|---------------------|--------|
| Vendedor (4) | 11334692-2 | vendedor123 | Vendedor Ventas |
| Jefe Desarrollo (5) | ? | ? | Jefe Desarrollo |
| Ingeniero (6) | ? | ? | Ingeniero Desarrollo |
| Vendedor Externo (19) | ? | ? | Carlos Veloso |

**Nota**: Las contrasenas deben validarse con el equipo.

---

## CHANGELOG

- **v1.0** (2026-01-08): Documento inicial con matriz de roles y funcionalidades
