# FASE 6.3: QA Funcional - Pruebas de Calidad

**ID**: `PASO-06.03-V12`
**Fecha**: 2025-12-18
**Estado**: Completado

---

## Resumen

Este documento detalla las pruebas de QA funcional realizadas al sistema INVEB Envases-OT, incluyendo la corrección de errores críticos identificados y verificación del flujo completo de usuario.

---

## 1. ERRORES CORREGIDOS

### 1.1 Error 500 en /crear-ot y /crear-ot-old

**Problema Identificado**:
- Rutas `/crear-ot` y `/crear-ot-old` retornaban HTTP 500
- Error: `Trying to get property 'id' of non-object`
- Ubicación: `WorkOrderController.php` y `WorkOrderOldController.php`

**Causa Raíz**:
- El código accedía a `auth()->user()->role->area->id` sin verificar null
- Los roles "Administrador" y "Gerente" tienen `work_space_id = NULL`
- La relación `Role->area()` retorna null para estos roles

**Estructura de Datos**:
```
Tabla roles:
- id 1: Administrador, work_space_id = NULL
- id 2: Gerente, work_space_id = NULL
- id 3+: Otros roles con work_space_id asignado
```

**Solucion Aplicada**:
Se agregaron verificaciones null antes de acceder a propiedades de `area`:

```php
// ANTES (error)
if (auth()->user()->role->area->id == 2) {

// DESPUES (corregido)
if (auth()->user()->role->area && auth()->user()->role->area->id == 2) {
```

**Archivos Modificados**:
| Archivo | Lineas | Descripcion |
|---------|--------|-------------|
| `WorkOrderController.php` | 692 | Null check en create() |
| `WorkOrderOldController.php` | 375, 520, 646 | Null checks multiples |

---

## 2. PRUEBAS DE ENDPOINTS CASCADE

### 2.1 Resultados de Pruebas

| Endpoint | Parametros | Estado | Tipo Respuesta |
|----------|------------|--------|----------------|
| getJerarquia2 | hierarchy_id=1 | OK | HTML options |
| getJerarquia3 | subhierarchy_id=1 | OK | HTML options |
| getCad | client_id=1 | OK | JSON |
| getCarton | proceso=1 | OK | JSON |
| getContactosCliente | client_id=1 | OK | HTML options |
| getInstalacionesCliente | client_id=1 | OK | HTML options |
| getUsersByArea | work_space_id=1 | OK | JSON |
| getColorCarton | id=1 | OK | JSON |
| getDesignType | product_type_id=1 | OK | JSON |
| getListaCarton | proceso,carton_color,planta,impresion | Error* | HTML options |

*`getListaCarton` requiere parametros adicionales que vienen de selecciones previas del usuario.

### 2.2 Metricas

```
Endpoints probados: 10
Endpoints exitosos:  9
Tasa de exito:      90%
```

---

## 3. PRUEBA DE FLUJO FUNCIONAL

### 3.1 Escenario de Prueba

Simulación completa del flujo de un usuario:
1. Login con credenciales de prueba
2. Navegación a formulario de creación de OT
3. Verificación de elementos del formulario
4. Prueba de endpoints cascade
5. Verificación de formulario alternativo
6. Acceso a lista de OTs

### 3.2 Resultados

```
╔═══════════════════════════════════════════════════════════╗
║                    RESUMEN DE PRUEBAS                     ║
╚═══════════════════════════════════════════════════════════╝

  Pasos ejecutados: 14
  Pasos exitosos:   14
  Tasa de exito:    100%

  Estado: APROBADO
```

### 3.3 Detalle de Pasos

| Paso | Descripcion | Resultado |
|------|-------------|-----------|
| 1.1 | Pagina de login cargada | OK |
| 1.2 | CSRF token obtenido | OK |
| 1.3 | Login exitoso | OK |
| 2.1 | Formulario /crear-ot cargado | OK |
| 2.2 | Elemento 'cliente' presente | OK |
| 2.3 | Elemento 'jerarquia' presente | OK |
| 2.4 | Elemento 'tipo_solicitud' presente | OK |
| 2.5 | Elemento 'boton_submit' presente | OK |
| 3.1 | getJerarquia2 funcional | OK |
| 3.2 | getContactosCliente funcional | OK |
| 3.3 | getUsersByArea funcional | OK |
| 4.1 | /crear-ot-old funcional | OK |
| 5.1 | /ordenes-trabajo cargado | OK |
| 5.2 | Tabla de OTs presente | OK |

---

## 4. CREDENCIALES DE PRUEBA

### 4.1 Usuario de Prueba

| Campo | Valor |
|-------|-------|
| RUT | 22222222-2 |
| Password | password |
| Role | Administrador (id: 1) |
| Area | NULL (no asignada) |

### 4.2 Nota sobre Roles sin Area

Los roles Administrador y Gerente no tienen `work_space_id` asignado por diseño, ya que son roles de supervision que no pertenecen a un area especifica.

---

## 5. ESTADO DE RUTAS PRINCIPALES

### 5.1 Rutas de Creacion de OT

| Ruta | Controlador | Estado | Notas |
|------|-------------|--------|-------|
| /crear-ot | WorkOrderController@create | OK | Formulario principal |
| /crear-ot-old | WorkOrderOldController@create | OK | Formulario legacy |
| /crear-ot-excel/{id} | WorkOrderExcelController@create | No probado | Importacion Excel |

### 5.2 Rutas de Gestion

| Ruta | Estado |
|------|--------|
| /login | OK |
| /ordenes-trabajo | OK |
| /crear-ot | OK |
| /crear-ot-old | OK |

---

## 6. PROBLEMAS CONOCIDOS

### 6.1 PdfController Inexistente

- **Descripcion**: La ruta `/procesaPDF` referencia un `PdfController` que no existe
- **Impacto**: No afecta funcionalidad principal de OT
- **Recomendacion**: Eliminar ruta o crear controlador si se necesita

### 6.2 getListaCarton Requiere Parametros Completos

- **Descripcion**: El endpoint requiere `carton_color`, `planta`, e `impresion`
- **Impacto**: Ninguno - comportamiento esperado por diseño
- **Nota**: Los parametros se obtienen de selecciones previas en el formulario

---

## 7. SCRIPTS DE PRUEBA

### 7.1 Ubicacion de Scripts

| Script | Proposito |
|--------|-----------|
| `test_crear_ot_docker.php` | Test basico de login y /crear-ot |
| `test_cascade_final.php` | Test de endpoints cascade |
| `test_flujo_funcional.php` | Test de flujo completo |

### 7.2 Ejecucion

```bash
# Copiar y ejecutar tests
docker cp test_flujo_funcional.php inveb-app:/tmp/
docker exec inveb-app sh -c "php /tmp/test_flujo_funcional.php"
```

---

## 8. RESUMEN EJECUTIVO

### 8.1 Metricas Finales

| Metrica | Valor |
|---------|-------|
| Errores criticos corregidos | 2 |
| Endpoints cascade probados | 10 |
| Tasa exito cascade | 90% |
| Flujo funcional completo | 100% |
| Estado general | **APROBADO** |

### 8.2 Correcciones Aplicadas

1. **WorkOrderController.php** - Null check en linea 692
2. **WorkOrderOldController.php** - Null checks en lineas 375, 520, 646

### 8.3 Proximos Pasos

- Fase 6.4: Pruebas de Rendimiento (opcional)
- Fase 7: Documentacion Final y Cierre

---

**Documento generado**: 2025-12-18
**Version**: 1.0
