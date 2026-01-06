# FASE 2.2: Reglas de Negocio Detalladas - Envases OT

**ID**: `PASO-02.02-V12`
**Fecha**: 2025-12-17
**Estado**: Completado

---

## CATALOGO DE ENTIDADES BASE

### 1. ROLES DEL SISTEMA

| ID | Rol | Area | Permisos Especiales |
|----|-----|------|---------------------|
| 1 | Administrador | - | Acceso total |
| 2 | Gerente | - | Visualizacion global |
| 3 | Jefe de Ventas | Ventas (1) | Gestiona vendedores, aprueba |
| 4 | Vendedor | Ventas (1) | Crea OTs, gestiona clientes |
| 5 | Jefe de Desarrollo | Desarrollo (2) | Gestiona ingenieros |
| 6 | Ingeniero | Desarrollo (2) | Diseno estructural |
| 7 | Jefe de Diseno e Impresion | Diseno (3) | Gestiona disenadores |
| 8 | Disenador | Diseno (3) | Diseno grafico |
| 9 | Jefe de Catalogacion | Catalogacion (4) | Gestiona catalogadores |
| 10 | Catalogador | Catalogacion (4) | Crea codigos SAP |
| 11 | Jefe de Precatalogacion | Precatalogacion (5) | Gestiona precatalogadores |
| 12 | Precatalogador | Precatalogacion (5) | Revision previa |
| 13 | Jefe de Muestras | Muestras (6) | Gestiona sala de corte |
| 14 | Tecnico de Muestras | Muestras (6) | Corta muestras |
| 15 | Gerente Comercial | - | Aprobaciones especiales |
| 17 | API | - | Integraciones |
| 18 | Super Administrador | Admin (7) | Todo + modificar OTs cerradas |
| 19 | Vendedor Externo | Ventas (1) | Cotizaciones externas |

---

### 2. AREAS DE TRABAJO (WorkSpaces)

| ID | Area | Descripcion |
|----|------|-------------|
| 1 | Area de Ventas | Ingreso OTs, gestion comercial |
| 2 | Area de Desarrollo | Diseno estructural, ingenieria |
| 3 | Area de Diseno e Impresion | Diseno grafico, arte |
| 4 | Area de Precatalogacion | Revision previa a catalogacion |
| 5 | Area de Catalogacion | Creacion codigos SAP |
| 6 | Area de Muestras | Sala de corte, muestras fisicas |
| 7 | Area de Administracion | SuperAdmin |
| 8 | Hibernacion | OTs pausadas |
| 9 | Cotizacion | OTs en proceso de cotizacion |

---

### 3. ESTADOS DE OT

| ID | Estado | Area Default | Descripcion |
|----|--------|--------------|-------------|
| 1 | Proceso de Ventas | Ventas | OT en gestion comercial |
| 2 | Proceso de Desarrollo | Desarrollo | OT en diseno estructural |
| 3 | Laboratorio | Desarrollo | OT en pruebas laboratorio |
| 4 | Muestra | Desarrollo | OT requiere muestra |
| 5 | Proceso de Diseno | Diseno | OT en diseno grafico |
| 6 | Proceso de Precatalogacion | Precatalogacion | OT en revision previa |
| 7 | Proceso de Catalogacion | Catalogacion | OT creando codigo SAP |
| 8 | OT Terminada | Catalogacion | OT completada exitosamente |
| 9 | Perdido | Ventas | Cliente no avanzo |
| 10 | Consulta Cliente | Ventas | Esperando respuesta cliente |
| 11 | OT Anulada | Ventas | OT cancelada |
| 12 | Rechazado | - | OT rechazada internamente |
| 13 | Entregado | - | OT entregada sin catalogacion |
| 14 | Espera de OC | Ventas | Esperando orden de compra |
| 15 | Falta definicion del Cliente | Ventas | Cliente debe definir |
| 16 | Visto bueno cliente | Ventas | Cliente aprobo diseno |
| 17 | Sala Muestra | Muestras | En sala de corte |
| 18 | Muestra Terminada | Muestras | Muestras completadas |
| 20 | En Revision | - | OT en revision |
| 21 | Pendiente | - | OT pendiente |
| 22 | Muestra Anulada | Muestras | Muestras canceladas |

---

### 4. TIPOS DE GESTION

| ID | Tipo | Descripcion |
|----|------|-------------|
| 1 | Cambio de Estado | Avance en workflow |
| 2 | Consulta | Pregunta a otra area |
| 3 | Archivo | Adjuntar documento |
| 4 | Log de Cambios | Registro bitacora |
| 6 | Solicitud Muestra | Pedir muestra fisica |
| 8 | Envio Sala Muestra | Enviar a corte |
| 9 | Envio Diseno Externo | PDF a proveedor externo |
| 10 | Recepcion Diseno Externo | Recibir de proveedor |
| 11 | Solicitud Especial | Gestion especial |

---

### 5. TIPOS DE SOLICITUD OT

| ID | Tipo | Descripcion | Flujo |
|----|------|-------------|-------|
| 1 | Desarrollo Completo | OT nueva desde cero | Ventas->Desarrollo->Diseno->Catalogacion |
| 2 | Solo Diseno Estructural | Solo ingenieria | Ventas->Desarrollo |
| 3 | Solo Diseno Grafico | Solo arte | Ventas->Diseno |
| 4 | Modificacion | Cambio a existente | Segun cambio |
| 5 | Arte con Material | Arte + codigo existente | Ventas->Diseno->Catalogacion |
| 6 | OT Especial | Licitacion/Ficha/Estudio | Ventas<->Desarrollo |
| 7 | Duplicado | Copia de OT existente | Segun original |

---

## REGLAS DE NEGOCIO POR MODULO

### MODULO: WORKFLOW OT

#### REGLA WF-001: Estados Permitidos por Area

**Area Ventas (1):**
- Estados normales: [2, 5, 6, 7, 9, 10, 11, 14, 15, 16, 20, 21]
- Si OT nunca fue a Desarrollo: Solo puede ir a [2] (Desarrollo)
- Si OT tipo_solicitud=6 (Especial): Solo [2, 10, 11, 15, 8]
- Si OT tipo_solicitud=5 (Arte+Material): Depende si ya fue a Diseno

**Area Desarrollo (2):**
- Estados normales: [1, 3, 5, 6, 7, 12, 16, 17]
- Si OT tipo_solicitud=6 (Especial): [1, 3, 12, 13] o [1, 3, 17, 12, 13]
- Puede agregar estado 13 (Entregado) si tipo_solicitud != 1

**Area Diseno (3):**
- Estados normales: [1, 2, 7, 12, 16]
- Puede agregar estado 13 (Entregado) si tipo_solicitud != 1

**Area Catalogacion (4/5):**
- Si current_area=4: [1, 2, 5, 8, 12]
- Si current_area=5: [1, 2, 5, 7, 12]

**Area Muestras (6):**
- Estados: [12, 18, 22]

---

#### REGLA WF-002: Bloqueo VB Cliente por Diseno Externo

```
SI existe Management con:
   - work_order_id = OT actual
   - management_type_id = 9 (Envio Diseno Externo)
   - recibido_diseño_externo = 0 (no recibido)
ENTONCES:
   - NO permitir estado 16 (Visto bueno cliente)
   - Mostrar alerta: "Pendiente recepcion de diseno externo"
```

**Archivos involucrados:**
- `ManagementController.php:96-165`

---

#### REGLA WF-003: Regreso a Area Anterior

```
SI OT esta en estado 10 (Consulta Cliente):
   - Remover estado 10 de opciones
   - Agregar estado 1 (Proceso Ventas)

SI OT esta en estado 14 (Espera OC):
   - Remover estado 14 de opciones
   - Agregar estado 1 (Proceso Ventas)

SI OT esta en estado 15 (Falta definicion):
   - Remover estado 15 de opciones
   - Agregar estado 1 (Proceso Ventas)

SI OT esta en estado 16 (VB Cliente):
   - Remover estado 16 de opciones
   - Agregar estado 1 (Proceso Ventas)
```

**Archivos involucrados:**
- `ManagementController.php:167-199`

---

#### REGLA WF-004: Tipos de Gestion por Area

```
SI usuario.area == OT.current_area:
   SI area == Desarrollo (2):
      SI tipo_solicitud == 6: tipos = [1, 2, 3]
      SINO: tipos = [1, 2, 3, 6]
   SI area == Muestras (6) Y hay muestras pendientes:
      SI hay envio externo pendiente: tipos = [1, 2, 3, 8, 10, 11]
      SINO: tipos = [1, 2, 3, 8, 9, 11]
   SINO:
      tipos = [1, 2, 3, 8] (standard)
SINO:
   tipos = [2, 3] (solo consulta y archivo)
```

**Archivos involucrados:**
- `ManagementController.php:327-388`

---

### MODULO: FILTROS Y VISIBILIDAD

#### REGLA FLT-001: Filtro por Rol - Vendedor

```
SI usuario.isVendedor() O usuario.isVendedorExterno():
   - Solo ver OTs donde creador_id = usuario.id
```

**Archivos involucrados:**
- `WorkOrderController.php:100-104`

---

#### REGLA FLT-002: Filtro por Rol - Ingeniero/Disenador/Catalogador

```
SI usuario.isIngeniero() O usuario.isDizenador() O usuario.isCatalogador():
   SI filtro asignado = "NO":
      - Ver OTs sin asignar en su area
   SINO:
      SI hay filtro responsable_id:
         - Ver OTs asignadas a ese responsable
      SINO:
         - Ver solo OTs asignadas a usuario.id
```

**Archivos involucrados:**
- `WorkOrderController.php:117-139`

---

#### REGLA FLT-003: Filtro por Area Actual

```
SI usuario.isDizenador() O usuario.isJefeDiseño():
   - Solo OTs con current_area_id = 3

SI usuario.isPrecatalogador() O usuario.isCatalogador() O sus jefes:
   - Solo OTs con current_area_id IN [4, 5]
```

**Archivos involucrados:**
- `WorkOrderController.php:303-313`

---

#### REGLA FLT-004: Filtro Tecnico Muestras por Sala de Corte

```
SI usuario.isTecnicoMuestras():
   - Solo ver OTs donde las muestras tengan:
     - sala_corte_vendedor = usuario.sala_corte_id, O
     - sala_corte_diseñador = usuario.sala_corte_id, O
     - sala_corte_laboratorio = usuario.sala_corte_id, O
     - sala_corte_1 a sala_corte_4 = usuario.sala_corte_id, O
     - sala_corte_diseñador_revision = usuario.sala_corte_id
```

**Archivos involucrados:**
- `WorkOrderController.php:349-418`

---

#### REGLA FLT-005: Estados Activos por Defecto

```
SI usuario.isVendedor() O usuario.isJefeVenta():
   estados_activos = [1, 2, 3, 4, 5, 6, 7, 10, 12, 13, 14, 15, 16, 17, 18, 20, 21, 22]
   (excluye: 8-Terminada, 9-Perdido, 11-Anulada, 19)

SI usuario.isJefeMuestras() O usuario.isTecnicoMuestras():
   estados_activos = [17] (Solo Sala Muestra)

SI usuario.isSuperAdministrador():
   estados_activos = TODOS [1-22]

OTROS:
   estados_activos = [1-22 excepto cerrados]
```

**Archivos involucrados:**
- `WorkOrderController.php:241-269`

---

### MODULO: MUESTRAS

#### REGLA MUE-001: Creacion de Muestra por Rol

```
SI usuario.role_id IN [5, 6] (Jefe Desarrollo o Ingeniero):
   - Puede crear muestra completa
   - Campos: cad_id, carton_id, pegado_id, destinatarios

SI usuario.role_id IN [13, 14] (Jefe/Tecnico Muestras):
   - Solo puede editar:
     - tiempo_unitario
     - checkboxes fecha_corte_*
     - cantidades
     - carton_muestra_id
```

**Archivos involucrados:**
- `MuestraController.php:64-278`

---

#### REGLA MUE-002: Estado Muestra al Crear

```
SI OT.current_area_id == 6 (Muestras):
   muestra.estado = 1 (En progreso)
SINO:
   muestra.estado = 0 (Pendiente)
```

**Archivos involucrados:**
- `MuestraController.php:214-219`

---

#### REGLA MUE-003: Destinos Multiples

```
SI hay mas de un destinatario:
   - Crear una muestra por cada destinatario
   - La primera muestra conserva destinatario_1
   - Las siguientes se replican con destinatarios adicionales
   - Retornar array de IDs de muestras creadas
```

**Archivos involucrados:**
- `MuestraController.php:285-297`

---

### MODULO: COTIZADOR

#### REGLA COT-001: Visibilidad Cotizaciones por Rol

```
SI usuario.isAdmin():
   - Ver todas las cotizaciones

SI usuario.isJefeCotizador():
   SI hay filtro creador_id:
      - Filtrar por ese creador
   SINO SI usuario.isJefeVenta():
      - Ver cotizaciones de vendedores con jefe_id = usuario.id
      - Ver propias

SINO (vendedor normal):
   SI usuario tiene vendedores_externos asignados:
      - Ver cotizaciones de usuario + vendedores externos
   SINO:
      - Ver solo cotizaciones propias
```

**Archivos involucrados:**
- `CotizacionController.php:154-188`

---

#### REGLA COT-002: Filtro por Fechas Default

```
SI no hay date_desde ni date_hasta:
   fromDate = primer dia del mes anterior
   toDate = manana

SINO:
   Usar fechas proporcionadas
```

**Archivos involucrados:**
- `CotizacionController.php:62-72`

---

### MODULO: ASIGNACIONES

#### REGLA ASIG-001: Asignacion de OT

```
Payload requerido:
   - id: ID de la OT
   - asignado_id: ID del profesional

Validaciones:
   - Usuario debe tener permiso de asignar (Jefe de area)
   - Profesional debe pertenecer al area correspondiente
   - OT debe estar en estado que permita asignacion

Resultado:
   - Crear registro en user_work_orders
   - Redirigir a /asignacionesConMensaje
```

**Archivos involucrados:**
- `modalAsignacion.js:42-105`
- `UserWorkOrderController.php`

---

### MODULO: CODIGO DE MATERIAL

#### REGLA MAT-001: Secuencia de Codigo Material

```
- Tabla materials_codes contiene ultimo ID usado
- Al crear codigo: obtener siguiente secuencia >= 700000
- Formato codigo: PREFIJO + NUMERO + SUFIJO
- El codigo es unico por OT
```

**Archivos involucrados:**
- `WorkOrderController.php` (crear-codigo-material)
- `MaterialsCode.php` model

---

### MODULO: CASCADA DE CAMPOS (Formulario OT)

#### REGLA CASC-001: Flujo de Habilitacion

```
Orden de cascada:
1. TIPO ITEM (product_type_id)
2. IMPRESION (impresion)
3. FSC (fsc)
4. CINTA (cinta)
5. RECUBRIMIENTO INTERNO (coverage_internal_id)
6. RECUBRIMIENTO EXTERNO (coverage_external_id)
7. PLANTA OBJETIVO (planta_id)
8. COLOR CARTON (carton_color)

Cada campo se habilita solo si:
- El campo anterior tiene valor
- Existe combinacion valida en relacion_filtro_ingresos_principales
```

**Archivos involucrados:**
- `resources/views/work-orders/create.blade.php`
- Endpoints AJAX: postVerificacionFiltro, getRecubrimiento*, getPlantaObjetivo, etc.

---

#### REGLA CASC-002: Validacion de Combinacion

```
Endpoint: POST /postVerificacionFiltro

Input:
   - product_type_id
   - impresion_id
   - fsc_id (opcional)
   - cinta_id (opcional)
   - planta_id (opcional)

Proceso:
   - Buscar en relacion_filtro_ingresos_principales
   - Validar que combinacion existe y esta activa
   - Retornar campos habilitados para siguiente nivel
```

---

## MATRIZ DE PERMISOS POR FUNCIONALIDAD

| Funcionalidad | Admin | Gerente | JefeVenta | Vendedor | JefeDev | Ingeniero | JefeDis | Disenador | JefeCat | Catalogador | JefeMue | TecMue |
|---------------|-------|---------|-----------|----------|---------|-----------|---------|-----------|---------|-------------|---------|--------|
| Crear OT | X | - | X | X | X | X | X | X | - | - | - | - |
| Editar OT | X | - | * | * | * | * | * | * | * | * | - | - |
| Ver OTs | X | X | X | X | X | X | X | X | X | X | X | X |
| Aprobar OT | X | - | X | - | - | - | - | - | - | - | - | - |
| Crear Gestion | X | - | * | * | * | * | * | * | * | * | * | * |
| Asignar OT | X | - | X | - | X | - | X | - | X | - | X | - |
| Crear Muestra | - | - | - | - | X | X | - | - | - | - | - | - |
| Editar Muestra | - | - | - | - | X | X | - | - | - | - | X | X |
| Crear Codigo SAP | - | - | - | - | - | - | - | - | X | X | - | - |
| Ver Reportes | X | X | X | X | X | X | X | X | X | X | X | - |
| Crear Cotizacion | X | - | X | X | - | - | - | - | - | - | - | - |
| Aprobar Cotizacion | X | X | X | - | - | - | - | - | - | - | - | - |
| Mantenedores | X | - | - | - | - | - | - | - | - | - | - | - |

`*` = Segun estado OT y area actual
`X` = Permiso total
`-` = Sin permiso

---

## DIAGRAMA DE ESTADOS OT

```
                                    [INICIO]
                                       |
                                       v
                        +---> (1) Proceso Ventas <---+
                        |              |             |
                        |              v             |
                        |     (2) Proceso Desarrollo |
                        |         /    |    \        |
                        |        v     v     v       |
                        |      (3)   (4)   (17)      |
                        |      Lab  Muestra SalaMue  |
                        |        \    |    /         |
                        |         v   v   v          |
                        |     (5) Proceso Diseno     |
                        |              |             |
                        |              v             |
                        |     (6) Precatalogacion    |
                        |              |             |
                        |              v             |
                        |     (7) Catalogacion       |
                        |              |             |
                        |              v             |
                        |     (8) OT TERMINADA       |
                        |                            |
                        |  Estados de Espera:        |
                        +-- (10) Consulta Cliente    |
                        +-- (14) Espera OC           |
                        +-- (15) Falta Definicion    |
                        +-- (16) VB Cliente ---------+

                        Estados Finales Alternativos:
                        (9) Perdido
                        (11) Anulada
                        (12) Rechazado
                        (13) Entregado
                        (18) Muestra Terminada
                        (22) Muestra Anulada
```

---

## INTEGRACIONES EXTERNAS

### Disenador Externo (Proveedor)

1. **Envio PDF a Proveedor** (management_type=9)
   - Registra envio con proveedor_id
   - Marca recibido_diseno_externo = 0
   - Bloquea avance a VB Cliente

2. **Recepcion de Proveedor** (management_type=10)
   - Actualiza recibido_diseno_externo = 1
   - Desbloquea VB Cliente

### SAP (Codigo Material)
- Prefijos y sufijos desde tablas: prefijo_material, sufijo_material
- Secuencia desde: materials_codes
- Material final en: materials

---

## PROXIMOS PASOS

1. [x] Documentacion de reglas de negocio completada
2. [ ] Cargar entidades en Neo4J (FASE 2.3)
3. [ ] Definir terminos ancla (FASE 2.4)
