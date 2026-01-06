# FASE 2.4: Terminos Ancla - Envases OT

**ID**: `PASO-02.04-V12`
**Fecha**: 2025-12-17
**Estado**: Completado

---

## Proposito

Los terminos ancla son palabras clave que mapean directamente a conceptos especificos del sistema.
Permiten identificar rapidamente de que se habla cuando el usuario menciona ciertos terminos.

---

## TERMINOS ANCLA POR CATEGORIA

### 1. ENTIDAD PRINCIPAL: OT (Orden de Trabajo)

| Termino Ancla | Sinonimos | Mapea A |
|---------------|-----------|---------|
| OT | orden, orden de trabajo, work order, ot | WorkOrder |
| crear OT | nueva ot, ingresar ot, cargar ot | /crear-ot |
| editar OT | modificar ot, cambiar ot | /edit-ot/{id} |
| duplicar OT | copiar ot, clonar ot | /duplicar/{id} |
| aprobar OT | aprobar, vb, visto bueno | /aprobarOt/{id} |
| gestionar OT | workflow, avanzar, cambiar estado | /gestionarOt/{id} |

---

### 2. WORKFLOW Y ESTADOS

| Termino Ancla | Sinonimos | Mapea A |
|---------------|-----------|---------|
| estado | status, etapa, fase | States |
| ventas | proceso ventas, comercial | Estado 1 |
| desarrollo | ingenieria, estructural | Estado 2 |
| laboratorio | lab, pruebas | Estado 3 |
| diseno | arte, grafico, impresion | Estado 5 |
| precatalogacion | precat, pre-cat | Estado 6 |
| catalogacion | cat, sap, codigo | Estado 7 |
| terminada | finalizada, completa, lista | Estado 8 |
| perdida | perdido, no avanzo | Estado 9 |
| anulada | anulado, cancelada | Estado 11 |
| consulta cliente | esperando cliente, pregunta | Estado 10 |
| espera OC | orden compra, esperando oc | Estado 14 |
| VB cliente | visto bueno, aprobado cliente | Estado 16 |
| sala muestra | sala corte, muestras | Estado 17 |

---

### 3. AREAS DE TRABAJO

| Termino Ancla | Sinonimos | Mapea A |
|---------------|-----------|---------|
| area ventas | ventas, comercial | Area 1 |
| area desarrollo | desarrollo, ingenieria | Area 2 |
| area diseno | diseno, arte, grafico | Area 3 |
| area precatalogacion | precat | Area 4 |
| area catalogacion | cat, sap | Area 5 |
| sala muestras | sala corte, muestras | Area 6 |

---

### 4. ROLES DE USUARIO

| Termino Ancla | Sinonimos | Mapea A |
|---------------|-----------|---------|
| vendedor | ventas, comercial | Rol 4 |
| jefe ventas | jefe comercial, gerente ventas | Rol 3 |
| ingeniero | ing, estructural | Rol 6 |
| jefe desarrollo | jefe ing | Rol 5 |
| disenador | diseno, arte | Rol 8 |
| jefe diseno | jefe arte | Rol 7 |
| catalogador | cat | Rol 10 |
| precatalogador | precat | Rol 12 |
| tecnico muestras | tecnico corte | Rol 14 |
| jefe muestras | jefe corte | Rol 13 |
| admin | administrador | Rol 1 |
| superadmin | super administrador | Rol 18 |

---

### 5. MUESTRAS

| Termino Ancla | Sinonimos | Mapea A |
|---------------|-----------|---------|
| muestra | sample, prototipo | Muestra |
| crear muestra | nueva muestra | /crear-muestra |
| etiqueta | label, rotulo | /generar_etiqueta_muestra_pdf |
| sala corte | cutting room | SalaCorte |
| destino muestra | envio, destinatario | destinatarios |
| corte | cutting | fecha_corte_* |

---

### 6. COTIZADOR

| Termino Ancla | Sinonimos | Mapea A |
|---------------|-----------|---------|
| cotizacion | quote, presupuesto | Cotizacion |
| crear cotizacion | nueva cotizacion | /cotizador/crear |
| detalle cotizacion | linea, item | DetalleCotizacion |
| aprobar cotizacion | aprobacion | /cotizador/solicitarAprobacion |
| pdf cotizacion | generar pdf | /cotizador/generar_pdf |
| ganado | won, exitoso | detalle ganado |
| perdido | lost, rechazado | detalle perdido |

---

### 7. CODIGO DE MATERIAL (SAP)

| Termino Ancla | Sinonimos | Mapea A |
|---------------|-----------|---------|
| codigo material | codigo sap, material code | Material |
| crear codigo | generar codigo, nuevo material | /crear-codigo-material |
| CAD | cad, dibujo | Cad |
| carton | carton, sustrato | Carton |
| prefijo | prefix | PrefijoMaterial |
| sufijo | suffix | SufijoMaterial |

---

### 8. GESTIONES

| Termino Ancla | Sinonimos | Mapea A |
|---------------|-----------|---------|
| gestion | management, accion | Management |
| cambio estado | avanzar, mover, transicion | management_type 1 |
| consulta | pregunta, query | management_type 2 |
| archivo | adjunto, file | management_type 3 |
| log cambios | bitacora, historial | management_type 4 |
| envio externo | proveedor, outsource | management_type 9 |

---

### 9. FILTROS Y CASCADA

| Termino Ancla | Sinonimos | Mapea A |
|---------------|-----------|---------|
| tipo item | product type | product_type_id |
| impresion | print type | impresion |
| FSC | certificacion | fsc |
| cinta | tape | cinta |
| recubrimiento | coating, coverage | coverage_internal_id, coverage_external_id |
| planta | plant, fabrica | planta_id |
| color carton | carton color | carton_color |
| cascada | cascade, filtro | relacion_filtro_ingresos_principales |

---

### 10. REPORTES

| Termino Ancla | Sinonimos | Mapea A |
|---------------|-----------|---------|
| reporte | report, informe | Report*Controller |
| carga OT | ots cargadas | gestion_carga_ot_mes |
| conversion | ots completadas | conversion_ot |
| tiempos | duracion, lead time | tiempos_por_area |
| rechazos | motivos rechazo | motivos_rechazos |
| anulaciones | cancelled | anulaciones |
| indicadores | KPI, metricas | indicadores |

---

### 11. MANTENEDORES

| Termino Ancla | Sinonimos | Mapea A |
|---------------|-----------|---------|
| mantenedor | crud, admin | /mantenedores/* |
| usuarios | users | /mantenedores/users |
| clientes | clients | /mantenedores/clients |
| cartones | cartons | /mantenedores/cartons |
| colores | colors | /mantenedores/colors |
| materiales | materials | /mantenedores/materials |
| carga masiva | excel, import | /masive, /uploading |

---

### 12. TIPOS DE SOLICITUD OT

| Termino Ancla | Sinonimos | Mapea A |
|---------------|-----------|---------|
| desarrollo completo | full development | tipo_solicitud 1 |
| solo estructural | structural only | tipo_solicitud 2 |
| solo grafico | graphic only | tipo_solicitud 3 |
| modificacion | modification, cambio | tipo_solicitud 4 |
| arte con material | art + code | tipo_solicitud 5 |
| OT especial | special, licitacion | tipo_solicitud 6 |
| duplicado | duplicate, copy | tipo_solicitud 7 |

---

## MAPEO DE ACCIONES COMUNES

| Accion Usuario | Terminos Clave | Ruta Sistema |
|----------------|----------------|--------------|
| Ver mis OTs | listado, home, inicio | /home |
| Crear nueva OT | crear, nueva, ingresar | /crear-ot |
| Avanzar OT | gestionar, cambiar estado, workflow | /gestionarOt/{id} |
| Ver historial | log, bitacora, historial | /detalleLogOt/{id} |
| Asignar OT | asignar, derivar | /asignarOT |
| Crear muestra | muestra, sample | /crear-muestra |
| Cotizar | cotizacion, presupuesto | /cotizador/crear |
| Ver reportes | reporte, indicadores | /reports/* |
| Administrar | mantenedor, admin | /mantenedores/* |

---

## USO EN PROMPTS

Cuando el usuario mencione estos terminos, el sistema puede:

1. **Identificar contexto**: "crear ot" -> Modulo OT, Funcionalidad crear
2. **Sugerir acciones**: "muestra" -> /crear-muestra, /terminarMuestra
3. **Relacionar conceptos**: "vendedor" -> Area Ventas, Estados permitidos
4. **Generar codigo**: "cotizacion" -> CotizacionController, DetalleCotizacion

---

## Proximos Pasos

1. [x] Terminos ancla definidos
2. [ ] Cargar terminos en Neo4J como nodos TerminoAncla
3. [ ] Crear relaciones SIGNIFICA entre terminos y conceptos
