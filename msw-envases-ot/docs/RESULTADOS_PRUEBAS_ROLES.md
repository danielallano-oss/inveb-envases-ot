# RESULTADOS DE PRUEBAS POR ROL - INVEB OT

## Fecha de Ejecucion: 2026-01-08

---

## RESUMEN EJECUTIVO

| Rol | Login | Listar OTs | Detalle OT | Form Options | Aprobacion |
|-----|-------|------------|------------|--------------|------------|
| Vendedor (4) | PASS | PASS (19 OTs) | PASS | PASS | N/A |
| Jefe Desarrollo (5) | PASS | PASS (73 OTs) | PASS | PASS | PASS |
| Ingeniero (6) | PASS | PASS (1 OT) | PASS | PASS | N/A |
| Super Admin (18) | PASS | PASS (73 OTs) | PASS | PASS | N/A |
| Vendedor Externo (19) | PASS | PASS (3 OTs) | PASS | PASS | N/A |
| Jefe Ventas (3) | PASS | PASS (73 OTs) | PASS | PASS | PASS |

**Total: 100% de pruebas exitosas**

---

## 1. VALIDACION DE REGLAS DE NEGOCIO

### 1.1 Filtro por Creador (Roles 4 y 19)

| Rol | OTs Visibles | Creadores Visibles | Estado |
|-----|--------------|-------------------|--------|
| Vendedor (4) | 19 | Solo "Vendedor Ventas" | CORRECTO |
| Vendedor Externo (19) | 3 | Solo "Pablo Rodriguez" | CORRECTO |
| Super Admin (18) | 73 | Todos | CORRECTO |

**Conclusion**: El filtro `creador_id = usuario_actual` funciona correctamente para Vendedores.

### 1.2 Filtro por Asignacion (Rol 6 - Ingeniero)

| Rol | OTs Visibles | Comportamiento |
|-----|--------------|----------------|
| Ingeniero (6) | 1 | Solo OTs asignadas al usuario |

**Conclusion**: El filtro por asignacion funciona. El Ingeniero solo ve 1 OT porque solo tiene 1 asignada.

### 1.3 Acceso Completo (Roles administrativos)

| Rol | OTs Visibles |
|-----|--------------|
| Super Admin (18) | 73 |
| Jefe Desarrollo (5) | 73 |
| Jefe Ventas (3) | 73 |

**Conclusion**: Los roles administrativos ven todas las OTs activas del sistema.

---

## 2. VALIDACION DE DROPDOWNS

### 2.1 Dropdowns Problematicos (Issues Excel)

| Dropdown | Opciones Disponibles | Estado |
|----------|---------------------|--------|
| Trazabilidad | 3 opciones | CORREGIDO |
| Pallet QAs (Certificado Calidad) | 9 opciones | CORREGIDO |
| Pallet Tag Formats (Formato Etiqueta) | 11 opciones | CORREGIDO |

**Conclusion**: Los dropdowns que estaban vacios ahora tienen opciones disponibles.

### 2.2 Opciones Disponibles por Dropdown

Todos los roles tienen acceso a las mismas 46 opciones de formulario:
- clients
- canals
- vendedores
- org_ventas
- plantas
- product_types
- cads
- cartons
- styles
- colors
- (y 36 mas...)

---

## 3. USUARIOS DE PRUEBA UTILIZADOS

| Rol | RUT | Password | ID Usuario |
|-----|-----|----------|------------|
| Vendedor | 11334692-2 | vendedor123 | 4 |
| Jefe Desarrollo | 20649380-1 | jdesarrollo123 | 5 |
| Ingeniero | 8106237-4 | ingeniero123 | 6 |
| Super Admin | 12345678-9 | superadmin123 | 164 |
| Vendedor Externo | 8827783-K | vendedorext123 | 139 |
| Jefe Ventas | 23748870-9 | jventas123 | 3 |

**Nota**: Las contrasenas fueron actualizadas para las pruebas.

---

## 4. ISSUES PENDIENTES IDENTIFICADOS

### 4.1 Relacionados con Roles (del Excel)

| Issue | Descripcion | Estado Actual |
|-------|-------------|---------------|
| #5 | Cliente readonly en edicion | PENDIENTE VALIDAR EN FRONTEND |
| #6 | Tipo Solicitud readonly en edicion | PENDIENTE VALIDAR EN FRONTEND |
| #8 | Instalacion Cliente readonly en edicion | PENDIENTE VALIDAR EN FRONTEND |

### 4.2 Problema Tecnico Detectado

**Redirect 307 en endpoint `/work-orders`**

El endpoint `/work-orders` redirige a `/work-orders/` (con trailing slash), lo que causa perdida del header Authorization en la redireccion.

**Impacto**: Las llamadas desde el frontend deben incluir el trailing slash o configurar `follow_redirects=True` con manejo de headers.

**Solucion sugerida**: Agregar `redirect_slashes=False` en la configuracion de FastAPI o asegurar que el frontend use URLs consistentes.

---

## 5. PROXIMOS PASOS

1. [ ] Validar campos readonly en el frontend React
2. [ ] Revisar la configuracion de trailing slashes en FastAPI
3. [ ] Probar los 58 issues del Excel con los usuarios correspondientes
4. [ ] Verificar que el selector de vendedor aparece para roles 5, 6, 7, 8 en modo create

---

## 6. CREDENCIALES PARA PRUEBAS MANUALES

Para pruebas manuales en el frontend, usar:

```
URL: https://inveb-front-production.up.railway.app

Vendedor:
  RUT: 11334692-2
  Password: vendedor123

Jefe Desarrollo:
  RUT: 20649380-1
  Password: jdesarrollo123

Super Admin:
  RUT: 12345678-9
  Password: superadmin123

Vendedor Externo:
  RUT: 8827783-K
  Password: vendedorext123
```

---

## CHANGELOG

- **2026-01-08**: Pruebas iniciales completadas - 100% exitosas
