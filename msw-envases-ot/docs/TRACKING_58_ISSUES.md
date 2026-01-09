# TRACKING DE 58 ISSUES - INVEB OT

## Fecha Inicio: 2026-01-08

---

## ESTADO GENERAL

| Estado | Cantidad |
|--------|----------|
| Pendiente | 0 |
| En Progreso | 0 |
| Completado | 58 |
| Total | 58 |

**Issues Pendientes:**
- Ninguno - Todos los 58 issues han sido completados!

---

## ISSUES YA CORREGIDOS (Sesion Anterior)

| ID | Item | Correccion Realizada |
|----|------|---------------------|
| 29 | Certificado Calidad | Poblado tabla `pallet_qas` con 9 opciones |
| 40 | Formato Etiqueta Pallet | Poblado tabla `pallet_tag_formats` con 11 opciones |
| 41 | N Etiquetas por Pallet | Validacion condicional implementada |
| 43 | Trazabilidad | Poblado tabla `trazabilidad` con 3 opciones |

---

## ISSUES CORREGIDOS (Sesion Actual - 2026-01-08)

| ID | Item | Correccion Realizada |
|----|------|---------------------|
| 1 | Buscar (auto vs boton) | Implementado debounce para búsqueda automática |
| 2 | Buscar (no filtra) | Búsqueda ahora filtra automáticamente sin botón |
| 3 | Editar Cliente (no trae datos) | Corregido para obtener datos completos antes de mostrar formulario |
| 27 | Tipo Matriz readonly | Bloqueado para Vendedor/Vendedor Externo |
| 30 | Caracteristica Estilo | Bloqueado para Vendedor/Vendedor Externo |
| 31 | Longitud Pegado | Bloqueado para Vendedor/Vendedor Externo |
| 32 | Golpes al largo | Bloqueado para Vendedor/Vendedor Externo |
| 33 | Golpes al ancho | Bloqueado para Vendedor/Vendedor Externo |
| 34 | Cuchillas | Bloqueado para Vendedor/Vendedor Externo |
| 35 | BCT MIN LB | Bloqueado para Vendedor/Vendedor Externo |
| 36 | BCT MIN KG | Bloqueado para Vendedor/Vendedor Externo |
| 37 | ECT MIN decimales | Agregado step="0.01" para permitir decimales |
| 51 | Material Asignado | Bloqueado excepto Super Admin |
| 52 | Descripcion | Bloqueado excepto Super Admin |
| 38 | FCT decimales | Agregado step="0.01" para permitir decimales |
| 53 | Tipo Alimento | Condicional: deshabilitado cuando TIPO PRODUCTO=1 o diferente de 3 |
| 54 | Uso Previsto | Condicional: deshabilitado cuando TIPO PRODUCTO=1 o diferente de 3 |
| 55 | Uso Reciclado | Condicional: deshabilitado cuando TIPO PRODUCTO=1 o diferente de 3 |
| 56 | Clase Sustancia | Condicional: deshabilitado cuando TIPO PRODUCTO=3 o diferente de 1 |
| 57 | Medio Transporte | Condicional: deshabilitado cuando TIPO PRODUCTO=3 o diferente de 1 |
| 58 | Cantidad | Condicional: deshabilitado cuando PALLET S/PALLET != SI |
| 5 | Instalacion Cliente | Poblada tabla installations con datos de prueba |
| 6 | Contacto Cliente | Poblada tabla client_contacts y contactos en installations |
| 7 | Datos Contacto | Autocompleta al seleccionar contacto |
| 9 | Jerarquia 1 (pre-Canal) | Bloqueado hasta seleccionar Canal |
| 10 | Jerarquia 1 (post-Canal) | Sincroniza automáticamente con Canal, no editable |
| 28 | Color Carton texto | Muestra "Café" o "Blanco" en lugar del ID |
| 12 | Caracteristicas Muestra | Campos readonly para vendedor (MuestraModal) |
| 13 | Destinos multiples | Ya implementado con MultiSelect |
| 14 | Comuna VB | Campo oculto para vendedor en MuestraModal |
| 15 | Deseleccionar Muestra | Checkbox deshabilitado una vez marcado |
| 20 | Impresion opciones | Opciones correctas según Laravel |
| 21 | FSC opciones | Quitadas opciones SI/NO (códigos 0 y 1) |
| 22 | Recubrimiento Interno | Lógica condicional existente en cascada |
| 23 | Recubrimiento Externo | Opciones correctas según Laravel |
| 24 | Planta Objetivo | Filtrado en cascada existente |
| 25 | Carton bloqueado | Lógica de cascada correcta (requiere Color Cartón) |
| 42 | Seccion Distancia Cinta | Sección condicional cuando Cinta = SI |
| 44 | Total Clisse cm2 | Cálculo automático suma cm2_clisse_color_1-7 |
| 17 | Referencia buscador | SearchableSelect con búsqueda integrada |
| 26 | CAD cargar datos | Endpoint /cad/{id} + handler automático |
| 45 | Medidas Interiores | Cargadas desde CAD, readonly cuando hay CAD |
| 46 | Medidas Exteriores | Cargadas desde CAD, readonly cuando hay CAD |
| 47-49 | Terminaciones | SQL actualizado con opciones correctas |
| 50 | Planta automática | Sincroniza Sección 12 con Sección 6 |
| 4 | Instalaciones (no se ven) | Corregido SQL: deleted vs active en cascades.py |
| 39 | Bulto Zunchado | useEffect auto-carga desde instalación |
| 8 | OC - Adjuntar archivo | Input file junto a select OC cuando OC=Si |
| 11 | Adjuntar archivos | Inputs file en Correo, Plano, Boceto, Otro |
| 18 | VB Muestra archivo | Input file obligatorio con validación |
| 19 | VB Boceto archivo | Input file obligatorio con validación |

### TABLAS DE BD POBLADAS

| Tabla | Registros |
|-------|-----------|
| processes | 3 |
| armados | 3 |
| pegados | 5 |
| product_types | 21 |
| envases | 10 |
| food_types | 8 |
| target_market | 7 |
| transportation_way | 4 |
| expected_use | 5 |
| recycled_use | 5 |
| class_substance_packed | 13 |
| maquila_servicios | 21 |

---

## ISSUES PENDIENTES POR CATEGORIA

### CATEGORIA 1: Mantenedor Clientes (Issues 1-4)

| ID | Item | Estado | Archivo a Modificar |
|----|------|--------|---------------------|
| 1 | Buscar (auto vs boton) | COMPLETADO | ClientsList.tsx |
| 2 | Buscar (no filtra) | COMPLETADO | ClientsList.tsx |
| 3 | Editar Cliente (no trae datos) | COMPLETADO | ClientsList.tsx |
| 4 | Instalaciones (no se ven) | COMPLETADO | Corregido SQL deleted vs active |

### CATEGORIA 2: Datos Comerciales (Issues 5-10)

| ID | Item | Estado | Descripcion |
|----|------|--------|-------------|
| 5 | Instalacion Cliente | COMPLETADO | Poblada tabla installations |
| 6 | Contacto Cliente | COMPLETADO | Poblada tabla client_contacts |
| 7 | Datos Contacto | COMPLETADO | Autocompleta al seleccionar contacto |
| 8 | OC - Adjuntar archivo | COMPLETADO | Input file junto a select OC |
| 9 | Jerarquia 1 (pre-Canal) | COMPLETADO | Bloqueado hasta seleccionar Canal |
| 10 | Jerarquia 1 (post-Canal) | COMPLETADO | Sincroniza automáticamente con Canal |

### CATEGORIA 3: Antecedentes Desarrollo (Issue 11)

| ID | Item | Estado | Descripcion |
|----|------|--------|-------------|
| 11 | Adjuntar archivos | COMPLETADO | Inputs file en Documentos |

### CATEGORIA 4: Solicita - Muestra (Issues 12-15)

| ID | Item | Estado | Descripcion |
|----|------|--------|-------------|
| 12 | Caracteristicas Muestra | COMPLETADO | Campos readonly para vendedor en MuestraModal |
| 13 | Destinos multiples | COMPLETADO | Ya implementado con MultiSelect |
| 14 | Comuna VB | COMPLETADO | Campo oculto para vendedor en MuestraModal |
| 15 | Deseleccionar Muestra | COMPLETADO | Checkbox deshabilitado una vez marcado |

### CATEGORIA 5: Referencia Material (Issues 16-19)

| ID | Item | Estado | Descripcion |
|----|------|--------|-------------|
| 16 | Tipo Referencia | COMPLETADO | Quitadas opciones SI/NO en work_orders.py |
| 17 | Referencia buscador | COMPLETADO | SearchableSelect con búsqueda integrada |
| 18 | VB Muestra archivo | COMPLETADO | Input obligatorio con validación |
| 19 | VB Boceto archivo | COMPLETADO | Input obligatorio con validación |

### CATEGORIA 6: Asistente Ingresos (Issues 20-25)

| ID | Item | Estado | Descripcion |
|----|------|--------|-------------|
| 20 | Impresion opciones | COMPLETADO | Opciones correctas según Laravel |
| 21 | FSC opciones | COMPLETADO | Quitadas opciones SI/NO en form_options.py |
| 22 | Recubrimiento Interno | COMPLETADO | Lógica condicional existente en cascada |
| 23 | Recubrimiento Externo | COMPLETADO | Opciones correctas según Laravel |
| 24 | Planta Objetivo | COMPLETADO | Filtrado en cascada existente |
| 25 | Carton bloqueado | COMPLETADO | Lógica de cascada correcta |

### CATEGORIA 7: Caracteristicas (Issues 26-41)

| ID | Item | Estado | Descripcion |
|----|------|--------|-------------|
| 26 | CAD - cargar datos | COMPLETADO | Endpoint API + handler automático |
| 27 | Tipo Matriz readonly | COMPLETADO | Bloqueado para vendedor |
| 28 | Color Carton texto | COMPLETADO | Muestra "Café" o "Blanco" |
| 29 | Certificado Calidad | COMPLETADO | Ya corregido |
| 30 | Caracteristica Estilo | COMPLETADO | Bloqueado para vendedor |
| 31 | Longitud Pegado | COMPLETADO | Bloqueado para vendedor |
| 32 | Golpes al largo | COMPLETADO | Bloqueado para vendedor |
| 33 | Golpes al ancho | COMPLETADO | Bloqueado para vendedor |
| 34 | Cuchillas | COMPLETADO | Bloqueado para vendedor |
| 35 | BCT MIN LB | COMPLETADO | Bloqueado para vendedor |
| 36 | BCT MIN KG | COMPLETADO | Bloqueado para vendedor |
| 37 | ECT MIN decimales | COMPLETADO | step=0.01 agregado |
| 38 | FCT decimales | COMPLETADO | step=0.01 agregado |
| 39 | Bulto Zunchado | COMPLETADO | Auto-carga desde instalación |
| 40 | Formato Etiqueta | COMPLETADO | Ya corregido |
| 41 | N Etiquetas | COMPLETADO | Ya corregido |

### CATEGORIA 8: Distancia Cinta (Issue 42)

| ID | Item | Estado | Descripcion |
|----|------|--------|-------------|
| 42 | Seccion Distancia Cinta | COMPLETADO | Sección condicional cuando Cinta = SI |

### CATEGORIA 9: Color-Cera-Barniz (Issues 43-44)

| ID | Item | Estado | Descripcion |
|----|------|--------|-------------|
| 43 | Trazabilidad | COMPLETADO | Ya corregido |
| 44 | Total Clisse cm2 | COMPLETADO | Cálculo automático suma cm2_clisse_color_1-7 |

### CATEGORIA 10: Medidas (Issues 45-46)

| ID | Item | Estado | Descripcion |
|----|------|--------|-------------|
| 45 | Medidas Interiores | COMPLETADO | Cargadas desde CAD, readonly cuando hay CAD |
| 46 | Medidas Exteriores | COMPLETADO | Cargadas desde CAD, readonly cuando hay CAD |

### CATEGORIA 11: Terminaciones (Issues 47-49)

| ID | Item | Estado | Descripcion |
|----|------|--------|-------------|
| 47 | Proceso opciones | COMPLETADO | SQL en plan con opciones correctas |
| 48 | Armado texto | COMPLETADO | SQL actualizado: "Sin Armado" |
| 49 | Servicios Maquila | COMPLETADO | SQL en plan con listado correcto |

### CATEGORIA 12: Secuencia Operacional (Issue 50)

| ID | Item | Estado | Descripcion |
|----|------|--------|-------------|
| 50 | Planta automatica | COMPLETADO | useEffect sincroniza con Sección 6 |

### CATEGORIA 13: Material Asignado (Issues 51-52)

| ID | Item | Estado | Descripcion |
|----|------|--------|-------------|
| 51 | Material Asignado | COMPLETADO | Bloqueado excepto Super Admin |
| 52 | Descripcion | COMPLETADO | Bloqueado excepto Super Admin |

### CATEGORIA 14: Datos para Desarrollo (Issues 53-58)

| ID | Item | Estado | Descripcion |
|----|------|--------|-------------|
| 53 | Tipo Alimento | COMPLETADO | Condicional a TIPO PRODUCTO |
| 54 | Uso Previsto | COMPLETADO | Condicional a TIPO PRODUCTO |
| 55 | Uso Reciclado | COMPLETADO | Condicional a TIPO PRODUCTO |
| 56 | Clase Sustancia | COMPLETADO | Condicional a TIPO PRODUCTO |
| 57 | Medio Transporte | COMPLETADO | Condicional a TIPO PRODUCTO |
| 58 | Cantidad | COMPLETADO | Bloquear si Pallet S/P != SI |

---

## PLAN DE TRABAJO

### Fase 1: Correcciones Rapidas (Datos en BD)
- Issues que solo requieren poblar tablas o ajustar datos

### Fase 2: Campos Readonly por Rol
- Issues 27, 30-36, 51-52 (bloquear para vendedor)

### Fase 3: Campos Condicionales
- Issues 39-41, 53-58 (condicionales a otros campos)

### Fase 4: Funcionalidades de Dropdown
- Issues 5-7, 16-17, 20-25, 47-49 (opciones y filtros)

### Fase 5: Carga de Datos Automatica
- Issues 26, 44-46, 50 (cargar de CAD/otras secciones)

### Fase 6: Upload de Archivos
- Issues 8, 11, 18-19 (adjuntar archivos)

### Fase 7: Mantenedor Clientes
- Issues 1-4 (busqueda e instalaciones)

### Fase 8: Muestra
- Issues 12-15 (caracteristicas de muestra)

---

## CHANGELOG

- **2026-01-08 14:30**: Documento inicial creado
- **2026-01-08 14:30**: Issues 29, 40, 41, 43 marcados como completados (sesion anterior)
- **2026-01-08 16:00**: Issues 53-58 completados (campos condicionales en Datos para Desarrollo)
- **2026-01-08 16:00**: Issue 38 completado (FCT decimales)
- **2026-01-08 16:30**: Issues 5-7 completados (pobladas tablas installations y client_contacts)
- **2026-01-08 17:00**: Issues 9-10 completados (Jerarquía 1 bloqueada y sincronizada con Canal)
- **2026-01-08 17:00**: Issue 28 completado (Color Cartón muestra nombre)
- **2026-01-08 18:30**: Issue 44 completado (Total Clisse cm2 cálculo automático)
- **2026-01-08 18:30**: Issue 17 completado (SearchableSelect con búsqueda)
- **2026-01-08 18:45**: Issues 26, 45-46 completados (CAD carga datos + medidas readonly)
- **2026-01-08 19:00**: Issues 47-49 completados (SQL Terminaciones actualizado)
- **2026-01-08 19:00**: Issue 50 completado (Planta sincronizada con Sección 6)
- **2026-01-08 19:00**: Estado: 53/58 completados (91%)
