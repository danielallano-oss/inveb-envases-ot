# FASE 2.1: Deteccion de Funcionalidades - Envases OT

**ID**: `PASO-02.01-V12`
**Fecha**: 2025-12-17
**Estado**: En progreso

## Resumen Ejecutivo

| Metrica | Valor |
|---------|-------|
| Controladores | 61 |
| Rutas definidas | ~250 |
| Modulos principales | 9 |
| Mantenedores CRUD | 30+ |

---

## MODULO 1: AUTENTICACION Y USUARIOS

**Controller**: `Auth/LoginController.php`, `UserController.php`

### Funcionalidades

| ID | Funcionalidad | Ruta | Roles |
|----|---------------|------|-------|
| AUTH-001 | Login tradicional | POST /login | Todos |
| AUTH-002 | Login Azure AD | POST /loginAzure | Todos |
| AUTH-003 | Recuperar contrasena | GET /recoveryPassword | Publico |
| AUTH-004 | Restablecer contrasena | POST /resetPasswordStore | Publico |
| AUTH-005 | Cambiar contrasena | PUT /actualizarContrasena/{id} | Usuario autenticado |
| AUTH-006 | Loguear como otro usuario | GET /log-user/{id} | Administrador |

---

## MODULO 2: ORDENES DE TRABAJO (OT)

**Controllers**: `WorkOrderController.php`, `WorkOrderOldController.php`, `WorkOrderExcelController.php`

### Funcionalidades Principales

| ID | Funcionalidad | Ruta | Roles |
|----|---------------|------|-------|
| OT-001 | Listado de OTs | GET /home, /ordenes-trabajo | Todos |
| OT-002 | Crear OT (formulario nuevo) | GET /crear-ot | Admin, Ventas, Disenadores |
| OT-003 | Crear OT (formulario antiguo) | GET /crear-ot-old | Admin, Ventas, Disenadores |
| OT-004 | Guardar OT | POST /guardar | Admin, Ventas, Disenadores |
| OT-005 | Editar OT | GET /edit-ot/{id} | Segun workflow |
| OT-006 | Actualizar OT | PUT /actualizar-ot/{id} | Segun workflow |
| OT-007 | Duplicar OT | GET /duplicar/{idOt} | Segun rol |
| OT-008 | Modal ver OT | POST /modalOT | Todos |
| OT-009 | Editar descripcion OT | GET /edit-description-ot/{id}/{type} | Segun workflow |

### OTs Especiales (Area Desarrollo)

| ID | Funcionalidad | Ruta | Descripcion |
|----|---------------|------|-------------|
| OT-010 | Crear Licitacion | GET /crear-licitacion | OT tipo licitacion |
| OT-011 | Crear Ficha Tecnica | GET /crear-ficha-tecnica | OT tipo ficha |
| OT-012 | Crear Estudio Benchmarking | GET /crear-estudio-benchmarking | OT tipo estudio |
| OT-013 | Modal OT Estudio | POST /modalOTEstudio | Ver estudio |
| OT-014 | Modal OT Licitacion | POST /modalOTLicitacion | Ver licitacion |
| OT-015 | Modal OT Ficha Tecnica | POST /modalOTFichaTecnica | Ver ficha |

### OT desde Excel

| ID | Funcionalidad | Ruta |
|----|---------------|------|
| OT-020 | Crear OT desde Excel | GET /crear-ot-excel/{id} |
| OT-021 | Guardar OT Excel | POST /guardar-excel/{id} |
| OT-022 | Descargar reporte Excel | GET /descargar-reporte-excel/{id} |
| OT-023 | Descargar Excel SAP | GET /descargar-excel-sap/{id} |
| OT-024 | Importar muestras masivas | POST /guardar-muestra-masiva |

### Aprobacion de OT

| ID | Funcionalidad | Ruta | Roles |
|----|---------------|------|-------|
| OT-030 | Listado aprobacion | GET /listadoAprobacion | Admin, Jefes |
| OT-031 | Aprobar OT | PUT /aprobarOt/{id} | Admin, Jefes |
| OT-032 | Rechazar OT | PUT /rechazarOt/{id} | Admin, Jefes |

### Codigo de Material

| ID | Funcionalidad | Ruta |
|----|---------------|------|
| OT-040 | Crear codigo material | PUT /crear-codigo-material/{idOt} |
| OT-041 | Crear CAD material | PUT /crear-cad-material/{idOt} |
| OT-042 | Buscar CAD | GET /cad |
| OT-043 | Buscar Material | GET /material |

---

## MODULO 3: GESTIONES (Workflow)

**Controller**: `ManagementController.php`

### Funcionalidades

| ID | Funcionalidad | Ruta | Descripcion |
|----|---------------|------|-------------|
| GEST-001 | Gestionar OT | GET /gestionarOt/{id} | Pantalla principal workflow |
| GEST-002 | Crear gestion | POST /crear-gestion/{id} | Avanzar etapa |
| GEST-003 | Respuesta gestion | POST /respuesta/{id} | Responder gestion |
| GEST-004 | Reactivar OT | GET /reactivarOt/{id} | Reactivar OT cerrada |
| GEST-005 | Retomar OT | GET /retomarOt/{id} | Retomar OT |
| GEST-006 | Detalle log OT | GET /detalleLogOt/{id} | Historial completo |
| GEST-007 | Descargar log Excel | GET /descargar-detalle-log-excel/{id} | Export historial |
| GEST-008 | Generar PDF diseno | GET /generar_diseno_pdf | PDF del diseno |
| GEST-009 | Leer PDF boceto | POST /leer-boceto-pdf | OCR de bocetos |
| GEST-010 | Guardar boceto PDF | POST /guardar-boceto-pdf | Almacenar boceto |
| GEST-011 | Detalle McKee | GET /detalleMckee | Calculo McKee |

---

## MODULO 4: MUESTRAS

**Controller**: `MuestraController.php`

### Funcionalidades

| ID | Funcionalidad | Ruta | Descripcion |
|----|---------------|------|-------------|
| MUE-001 | Crear muestra | POST /crear-muestra | Nueva muestra para OT |
| MUE-002 | Eliminar muestra | GET /eliminar-muestra/{id} | Eliminar muestra |
| MUE-003 | Rechazar muestra | GET /rechazarMuestra/{id} | Cambio estado |
| MUE-004 | Terminar muestra | POST /terminarMuestra | Finalizar muestra |
| MUE-005 | Anular muestra | POST /anularMuestra | Anular muestra |
| MUE-006 | Devolver muestra | POST /devolverMuestra | Devolver a etapa anterior |
| MUE-007 | Obtener muestras OT | GET /getMuestrasOt/{id} | Lista muestras de OT |
| MUE-008 | Muestra prioritaria | PUT /muestraPrioritaria/{id} | Marcar prioritaria |
| MUE-009 | Muestra no prioritaria | PUT /muestraNoPrioritaria/{id} | Desmarcar prioritaria |
| MUE-010 | Generar etiqueta producto | GET /generar_etiqueta_muestra_pdf | PDF etiqueta producto |
| MUE-011 | Generar etiqueta cliente | GET /generar_etiqueta_cliente_pdf | PDF etiqueta cliente |
| MUE-012 | Obtener muestra | GET /getMuestra | Datos de una muestra |
| MUE-013 | Obtener carton muestra | GET /getCartonMuestra | Carton de muestra |

---

## MODULO 5: ASIGNACIONES

**Controller**: `UserWorkOrderController.php`

### Funcionalidades

| ID | Funcionalidad | Ruta | Descripcion |
|----|---------------|------|-------------|
| ASIG-001 | Listado asignaciones | GET /asignaciones | Ver OTs por asignar |
| ASIG-002 | Modal asignacion | POST /modalAsignacion | Popup para asignar |
| ASIG-003 | Asignar OT | POST /asignarOT | Asignar a profesional |
| ASIG-004 | Asignaciones con mensaje | GET /asignacionesConMensaje | Confirmacion |

---

## MODULO 6: NOTIFICACIONES

**Controller**: `NotificationController.php`

### Funcionalidades

| ID | Funcionalidad | Ruta |
|----|---------------|------|
| NOT-001 | Listado notificaciones | GET /notificaciones |
| NOT-002 | Inactivar notificacion | PUT /inactivarNotificacion/{id} |

---

## MODULO 7: REPORTES

**Controllers**: `ReportController.php`, `Report2Controller.php`, `Report3Controller.php`

### Reportes Disponibles (x3 versiones)

| ID | Reporte | Descripcion |
|----|---------|-------------|
| REP-001 | Gestion carga OT mes | OTs cargadas por mes |
| REP-002 | Conversion OT | OTs completadas |
| REP-003 | Conversion OT entre fechas | OTs en rango |
| REP-004 | Gestion OT activos | OTs en proceso |
| REP-005 | Tiempos por area OT mes | Tiempos por departamento |
| REP-006 | Motivos rechazos mes | Causas de rechazo |
| REP-007 | Rechazos por mes | Cantidad rechazos |
| REP-008 | OT activas por area | OTs activas por depto |
| REP-009 | Anulaciones | OTs anuladas |
| REP-010 | Muestras | Reporte de muestras |
| REP-011 | Indicador sala muestras | KPI sala muestras |
| REP-012 | Diseno estructural y sala muestra | Combinado |
| REP-013 | Sala muestra | Especifico sala |
| REP-014 | Tiempo primera muestra | Lead time muestras |
| REP-015 | Tiempo disenador externo | Tiempo proveedores |

---

## MODULO 8: COTIZADOR

**Controllers**: `CotizacionController.php`, `DetalleCotizacionController.php`, `CotizacionApprovalController.php`, `AreahcController.php`

### Cotizaciones

| ID | Funcionalidad | Ruta | Descripcion |
|----|---------------|------|-------------|
| COT-001 | Crear cotizacion | GET /cotizador/crear | Nueva cotizacion interna |
| COT-002 | Editar cotizacion | GET /cotizador/edit/{id} | Editar existente |
| COT-003 | Listado cotizaciones | GET /cotizador/index | Lista interna |
| COT-004 | Crear cotizacion externa | GET /cotizador/crear_externo | Para externos |
| COT-005 | Listado externo | GET /cotizador/index_externo | Lista externa |
| COT-006 | Aprobar cotizacion externa | GET /cotizador/aprobar_externo/{id} | Aprobacion |
| COT-007 | Generar PDF | GET /cotizador/generar_pdf | Export PDF |
| COT-008 | Enviar PDF | POST /cotizador/enviar_pdf | Email con PDF |
| COT-009 | Detalle costos | GET /cotizador/detalle_costos | Desglose |
| COT-010 | Aprobaciones | GET /cotizador/aprobaciones | Lista pendientes |
| COT-011 | Versionar cotizacion | POST /cotizador/versionarCotizacion/{id} | Nueva version |
| COT-012 | Duplicar cotizacion | POST /cotizador/duplicarCotizacion/{id} | Copiar |
| COT-013 | Retomar cotizacion | POST /cotizador/retomarCotizacion/{id} | Continuar |
| COT-014 | Cotizar OT | GET /cotizador/cotizarOt/{id} | Desde OT |
| COT-015 | Solicitar aprobacion | POST /cotizador/solicitarAprobacion/{id} | Enviar a aprobar |
| COT-016 | Gestionar aprobacion | POST /cotizador/gestionar-cotizacion/{id} | Aprobar/Rechazar |

### Detalle Cotizacion (AJAX)

| ID | Funcionalidad | Descripcion |
|----|---------------|-------------|
| COT-020 | Calcular detalle | Calcula costos |
| COT-021 | Guardar detalle | Persiste linea |
| COT-022 | Editar detalle | Modifica linea |
| COT-023 | Eliminar detalle | Borra linea |
| COT-024 | Carga masiva detalles | Import Excel |
| COT-025 | Detalle ganado/perdido | Marcar resultado |
| COT-026 | Detalle a OT | Crear OT desde detalle |
| COT-027 | Sincronizar detalles | Actualizar calculos |

### Area HC (Calculo)

| ID | Funcionalidad | Ruta |
|----|---------------|------|
| COT-030 | Crear area HC | GET /cotizador/crear_areahc |
| COT-031 | Calcular area HC | POST /cotizador/calcularAreaHC |

---

## MODULO 9: MANTENEDORES

### 9.1 Mantenedores Principales (CRUD completo)

Cada mantenedor tiene: list, create, store, edit, update, active, inactive

| Controller | Entidad | Ruta Base |
|------------|---------|-----------|
| UserController | Usuarios | /mantenedores/users/ |
| ClientController | Clientes | /mantenedores/clients/ |
| SectorController | Sectores | /mantenedores/sectors/ |
| HierarchyController | Jerarquias nivel 1 | /mantenedores/hierarchies/ |
| SubhierarchyController | Jerarquias nivel 2 | /mantenedores/subhierarchies/ |
| SubsubhierarchyController | Jerarquias nivel 3 | /mantenedores/subsubhierarchies/ |
| ProductTypeController | Tipos producto | /mantenedores/product-types/ |
| PalletTypeController | Tipos pallet | /mantenedores/pallet-types/ |
| StyleController | Estilos | /mantenedores/styles/ |
| CartonController | Cartones | /mantenedores/cartons/ |
| ColorController | Colores | /mantenedores/colors/ |
| SecuenciaOperacionalController | Secuencias operacionales | /mantenedores/secuencias-operacionales/ |
| AlmacenController | Almacenes | /mantenedores/almacenes/ |
| TipoCintaController | Tipos cinta | /mantenedores/tipos-cintas/ |
| RechazoConjuntoController | Rechazos conjunto | /mantenedores/rechazo-conjunto/ |
| GrupoImputacionMaterialController | Grupo imputacion | /mantenedores/grupo-imputacion-material/ |
| OrganizacionVentaController | Organizacion venta | /mantenedores/organizacion-venta/ |
| TiempoTratamientoController | Tiempo tratamiento | /mantenedores/tiempo-tratamiento/ |
| GrupoMateriales1Controller | Grupo materiales 1 | /mantenedores/grupo-materiales-1/ |
| GrupoMateriales2Controller | Grupo materiales 2 | /mantenedores/grupo-materiales-2/ |
| GrupoPlantasController | Grupo plantas | /mantenedores/grupo-plantas/ |
| MaterialController | Materiales | /mantenedores/materials/ |
| CanalController | Canales | /mantenedores/canals/ |
| AdhesivoController | Adhesivos | /mantenedores/adhesivos/ |
| CeBeController | CeBes | /mantenedores/cebes/ |
| ClasificacionClienteController | Clasificacion clientes | /mantenedores/clasificaciones_clientes/ |

### 9.2 Mantenedores Cotizador (Carga Masiva Excel)

Cada uno tiene: masive, uploading, descargar_excel

| Entidad | Ruta Base |
|---------|-----------|
| Cartones corrugados | /mantenedores/cotizador/cartons |
| Cartones esquineros | /mantenedores/cotizador/cartones-esquineros |
| Papeles | /mantenedores/cotizador/papeles |
| Fletes | /mantenedores/cotizador/fletes |
| Mermas corrugadoras | /mantenedores/cotizador/mermas_corrugadoras |
| Mermas convertidoras | /mantenedores/cotizador/mermas_convertidoras |
| Paletizados | /mantenedores/cotizador/paletizados |
| Insumos paletizados | /mantenedores/cotizador/insumos_paletizados |
| Tarifarios margen | /mantenedores/cotizador/tarifarios |
| Consumo adhesivos | /mantenedores/cotizador/consumo_adhesivos |
| Consumo adhesivos pegados | /mantenedores/cotizador/consumo_adhesivos_pegados |
| Consumo energia | /mantenedores/cotizador/consumo_energia |
| Factores seguridad | /mantenedores/cotizador/factores_seguridad |
| Factores onda | /mantenedores/cotizador/factores_onda |
| Factores desarrollo | /mantenedores/cotizador/factores_desarrollo |
| Maquilas | /mantenedores/cotizador/maquilas |
| Ondas | /mantenedores/cotizador/ondas |
| Plantas | /mantenedores/cotizador/plantas |
| Variables | /mantenedores/cotizador/variables |
| Margenes minimos | /mantenedores/cotizador/margenes_minimos |
| Porcentajes margenes minimos | /mantenedores/cotizador/porcentajes_margenes_minimos |
| Mano obra mantencion | /mantenedores/cotizador/mano_obra_mantencion |
| Matrices | /mantenedores/matrices |
| Materiales | /mantenedores/materiales |

---

## ENDPOINTS AJAX (Combos y Validaciones)

| Endpoint | Descripcion |
|----------|-------------|
| getJerarquia2 | Subjerarquias |
| getJerarquia3 | Sub-subjerarquias |
| getCad | Buscar CAD |
| getCadByMaterial | CAD por material |
| getCarton | Obtener carton |
| getDesignType | Tipo diseno |
| getCartonColor | Color carton |
| postVerificacionFiltro | Validar cascada |
| getRecubrimientoInterno | Recubrimientos int |
| getRecubrimientoExterno | Recubrimientos ext |
| getPlantaObjetivo | Plantas objetivo |
| getColorCarton | Colores carton |
| getListaCarton | Lista cartones |
| getUsersByArea | Usuarios por area |
| getContactosCliente | Contactos cliente |
| getDatosContacto | Datos contacto |
| getInstalacionesCliente | Instalaciones |
| getMaquilaServicio | Maquilas servicio |
| getSecuenciasOperacionales | Secuencias op |
| getMatriz | Datos matriz |
| validarExcel | Validar Excel |

---

## RESUMEN DE FUNCIONALIDADES POR TIPO

| Tipo | Cantidad |
|------|----------|
| Autenticacion | 6 |
| OT Principal | 15 |
| OT Excel | 5 |
| OT Aprobacion | 3 |
| OT Codigo Material | 4 |
| Gestiones | 11 |
| Muestras | 13 |
| Asignaciones | 4 |
| Notificaciones | 2 |
| Reportes | 15 (x3 versiones) |
| Cotizador | 17 |
| Detalle Cotizacion | 8 |
| Mantenedores CRUD | 26 |
| Mantenedores Excel | 22 |
| Endpoints AJAX | 20+ |
| **TOTAL** | **~180 funcionalidades** |

