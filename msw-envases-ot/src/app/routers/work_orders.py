"""
Router de Órdenes de Trabajo - INVEB Cascade Service
Lee y escribe datos en MySQL de Laravel para el dashboard React.
Actualizado: Fixed table column issues for form-options-complete
"""
from datetime import datetime
from typing import Optional, List
from fastapi import APIRouter, HTTPException, status, Query, Depends
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from pydantic import BaseModel, Field
import pymysql
import jwt

from app.config import get_settings
from app.utils.working_hours import WorkingHoursCalculator

router = APIRouter(prefix="/work-orders", tags=["Órdenes de Trabajo"])
security = HTTPBearer(auto_error=False)
settings = get_settings()


def get_current_user_id(credentials: HTTPAuthorizationCredentials = Depends(security)) -> int:
    """Extrae el user_id del token JWT."""
    if not credentials:
        raise HTTPException(status_code=401, detail="Token no proporcionado")
    try:
        payload = jwt.decode(
            credentials.credentials,
            settings.JWT_SECRET_KEY,
            algorithms=[settings.JWT_ALGORITHM]
        )
        return int(payload.get("sub"))
    except jwt.ExpiredSignatureError:
        raise HTTPException(status_code=401, detail="Token expirado")
    except jwt.InvalidTokenError:
        raise HTTPException(status_code=401, detail="Token invalido")


def get_current_user_with_role(credentials: HTTPAuthorizationCredentials = Depends(security)) -> dict:
    """Extrae user_id y role_id del token JWT."""
    if not credentials:
        raise HTTPException(status_code=401, detail="Token no proporcionado")
    try:
        payload = jwt.decode(
            credentials.credentials,
            settings.JWT_SECRET_KEY,
            algorithms=[settings.JWT_ALGORITHM]
        )
        return {
            "user_id": int(payload.get("sub")),
            "role_id": int(payload.get("role_id", 0))
        }
    except jwt.ExpiredSignatureError:
        raise HTTPException(status_code=401, detail="Token expirado")
    except jwt.InvalidTokenError:
        raise HTTPException(status_code=401, detail="Token invalido")


# Schemas
class WorkOrderListItem(BaseModel):
    """Item de la lista de OTs."""
    id: int
    created_at: str
    client_name: str
    descripcion: str
    canal: Optional[str]
    item_tipo: Optional[str]
    estado: str
    estado_abrev: str
    creador_nombre: str
    current_area_id: Optional[int]
    ultimo_cambio_area: Optional[str]  # Fecha cuando la OT entró al área actual
    # Tiempos por área (días laborales - 9.5h/día como Laravel)
    tiempo_total: Optional[float]
    tiempo_venta: Optional[float]
    tiempo_desarrollo: Optional[float]
    tiempo_muestra: Optional[float]
    tiempo_diseno: Optional[float]
    tiempo_externo: Optional[float]
    tiempo_precatalogacion: Optional[float]
    tiempo_catalogacion: Optional[float]
    # Extras
    tipo_solicitud: Optional[int]
    cad: Optional[str]
    carton: Optional[str]
    material_codigo: Optional[str]


class WorkOrderListResponse(BaseModel):
    """Respuesta paginada de OTs."""
    items: List[WorkOrderListItem]
    total: int
    page: int
    page_size: int
    total_pages: int


class FilterOptions(BaseModel):
    """Opciones para los filtros."""
    estados: List[dict]
    areas: List[dict]
    canales: List[dict]
    clientes: List[dict]
    vendedores: List[dict]
    plantas: List[dict]
    impresiones: List[dict]
    procesos: List[dict]
    estilos: List[dict]
    fsc: List[dict]
    org_ventas: List[dict]


class WorkOrderCreate(BaseModel):
    """Schema para crear una OT."""
    # Datos Comerciales (requeridos)
    client_id: int = Field(..., description="ID del cliente")
    descripcion: str = Field(..., max_length=40, description="Descripcion del producto")
    tipo_solicitud: int = Field(..., description="Tipo de solicitud (1-7)")
    canal_id: int = Field(..., description="ID del canal")

    # Datos Comerciales (opcionales)
    org_venta_id: Optional[int] = None
    subsubhierarchy_id: Optional[int] = None
    nombre_contacto: Optional[str] = None
    email_contacto: Optional[str] = None
    telefono_contacto: Optional[str] = None
    volumen_venta_anual: Optional[int] = None
    usd: Optional[int] = None
    oc: Optional[int] = None
    codigo_producto: Optional[str] = None
    dato_sub_cliente: Optional[str] = None
    instalacion_cliente: Optional[int] = None

    # Solicitante (checkboxes)
    analisis: Optional[int] = 0
    plano: Optional[int] = 0
    muestra: Optional[int] = 0
    datos_cotizar: Optional[int] = 0
    boceto: Optional[int] = 0
    nuevo_material: Optional[int] = 0
    prueba_industrial: Optional[int] = 0
    numero_muestras: Optional[int] = None

    # Referencia Material
    reference_type: Optional[int] = None
    reference_id: Optional[int] = None

    # Caracteristicas (Cascade)
    product_type_id: Optional[int] = None
    impresion: Optional[int] = None
    fsc: Optional[str] = None
    cinta: Optional[int] = None
    coverage_internal_id: Optional[int] = None
    coverage_external_id: Optional[int] = None
    carton_color: Optional[int] = None
    carton_id: Optional[int] = None
    cad_id: Optional[int] = None
    cad: Optional[str] = None
    style_id: Optional[int] = None

    # Medidas
    interno_largo: Optional[int] = None
    interno_ancho: Optional[int] = None
    interno_alto: Optional[int] = None
    externo_largo: Optional[int] = None
    externo_ancho: Optional[int] = None
    externo_alto: Optional[int] = None

    # Terminaciones
    process_id: Optional[int] = None
    armado_id: Optional[int] = None
    sentido_armado: Optional[int] = None
    tipo_sentido_onda: Optional[str] = None

    # Colores
    numero_colores: Optional[int] = None
    color_1_id: Optional[int] = None
    color_2_id: Optional[int] = None
    color_3_id: Optional[int] = None
    color_4_id: Optional[int] = None
    color_5_id: Optional[int] = None

    # Desarrollo
    peso_contenido_caja: Optional[int] = None
    autosoportante: Optional[int] = None
    envase_id: Optional[int] = None
    cantidad: Optional[int] = None
    observacion: Optional[str] = None

    # Planta objetivo
    planta_id: Optional[int] = None


class WorkOrderCreateResponse(BaseModel):
    """Respuesta al crear OT."""
    id: int
    message: str


def get_mysql_connection():
    """Crea conexión a MySQL de Laravel."""
    try:
        connection = pymysql.connect(
            host=settings.LARAVEL_MYSQL_HOST,
            port=settings.LARAVEL_MYSQL_PORT,
            user=settings.LARAVEL_MYSQL_USER,
            password=settings.LARAVEL_MYSQL_PASSWORD,
            database=settings.LARAVEL_MYSQL_DATABASE,
            charset='utf8mb4',
            cursorclass=pymysql.cursors.DictCursor
        )
        return connection
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
            detail=f"Error conectando a base de datos Laravel: {str(e)}"
        )


@router.get("/", response_model=WorkOrderListResponse)
async def list_work_orders(
    page: int = Query(1, ge=1),
    page_size: int = Query(20, ge=1, le=100),
    # Filtros
    id_ot: Optional[int] = None,
    date_desde: Optional[str] = None,
    date_hasta: Optional[str] = None,
    client_id: Optional[List[int]] = Query(None),
    estado_id: Optional[List[int]] = Query(None),
    area_id: Optional[List[int]] = Query(None),
    canal_id: Optional[List[int]] = Query(None),
    vendedor_id: Optional[List[int]] = Query(None),
    cad: Optional[str] = None,
    carton: Optional[str] = None,
    material: Optional[str] = None,
    descripcion: Optional[str] = None,
    planta_id: Optional[List[int]] = Query(None),
    tipo_solicitud: Optional[List[int]] = Query(None),
    # Autenticación para filtro por rol
    current_user: dict = Depends(get_current_user_with_role),
):
    """
    Lista órdenes de trabajo con filtros y paginación.

    REGLA DE NEGOCIO (replica Laravel):
    - Vendedores (role_id=4) y Vendedores Externos (role_id=19) solo ven OTs que ellos crearon
    - Otros roles ven todas las OTs según sus filtros
    """
    connection = get_mysql_connection()
    try:
        with connection.cursor() as cursor:
            # Query base con tiempos almacenados (sin cálculo real-time)
            # work_space_id: 1=Ventas, 2=Desarrollo, 3=Diseño Gráfico, 4=Catalogación, 5=Precatalogación, 6=Muestra
            # NOTA: El cálculo real-time se hace en Python usando get_working_hours()
            # para replicar exactamente el comportamiento de Laravel
            base_query = """
                SELECT
                    wo.id,
                    wo.created_at,
                    wo.descripcion,
                    wo.tipo_solicitud,
                    wo.current_area_id,
                    wo.ultimo_cambio_area,
                    c.nombre as client_name,
                    CONCAT(u.nombre, ' ', u.apellido) as creador_nombre,
                    ch.nombre as canal,
                    pt.descripcion as item_tipo,
                    COALESCE(s.nombre, 'Proceso de Ventas') as estado,
                    COALESCE(s.abreviatura, 'PV') as estado_abrev,
                    cad.cad as cad_codigo,
                    cart.codigo as carton_codigo,
                    mat.codigo as material_codigo,
                    -- Tiempos almacenados por área (horas)
                    COALESCE((SELECT SUM(duracion_segundos) / 3600.0 FROM managements
                        WHERE work_order_id = wo.id AND management_type_id = 1 AND work_space_id = 1 AND mostrar = 1), 0) as horas_venta_stored,
                    COALESCE((SELECT SUM(duracion_segundos) / 3600.0 FROM managements
                        WHERE work_order_id = wo.id AND management_type_id = 1 AND work_space_id = 2 AND mostrar = 1), 0) as horas_desarrollo_stored,
                    COALESCE((SELECT SUM(duracion_segundos) / 3600.0 FROM managements
                        WHERE work_order_id = wo.id AND management_type_id = 1 AND work_space_id = 3 AND mostrar = 1), 0) as horas_diseno_stored,
                    COALESCE((SELECT SUM(duracion_segundos) / 3600.0 FROM managements
                        WHERE work_order_id = wo.id AND management_type_id = 1 AND work_space_id = 4 AND mostrar = 1), 0) as horas_catalogacion_stored,
                    COALESCE((SELECT SUM(duracion_segundos) / 3600.0 FROM managements
                        WHERE work_order_id = wo.id AND management_type_id = 1 AND work_space_id = 5 AND mostrar = 1), 0) as horas_precatalogacion_stored,
                    COALESCE((SELECT SUM(duracion_segundos) / 3600.0 FROM managements
                        WHERE work_order_id = wo.id AND management_type_id = 1 AND work_space_id = 6 AND mostrar = 1), 0) as horas_muestra_stored,
                    -- Tiempo total almacenado (horas)
                    COALESCE((SELECT SUM(duracion_segundos) / 3600.0 FROM managements
                        WHERE work_order_id = wo.id AND management_type_id = 1 AND mostrar = 1), 0) as horas_total_stored
                FROM work_orders wo
                LEFT JOIN clients c ON wo.client_id = c.id
                LEFT JOIN users u ON wo.creador_id = u.id
                LEFT JOIN canals ch ON wo.canal_id = ch.id
                LEFT JOIN product_types pt ON wo.product_type_id = pt.id
                LEFT JOIN (
                    SELECT work_order_id, state_id
                    FROM managements
                    WHERE id IN (SELECT MAX(id) FROM managements GROUP BY work_order_id)
                ) m ON wo.id = m.work_order_id
                LEFT JOIN states s ON m.state_id = s.id
                LEFT JOIN cads cad ON wo.cad_id = cad.id
                LEFT JOIN cartons cart ON wo.carton_id = cart.id
                LEFT JOIN materials mat ON wo.material_id = mat.id
                WHERE wo.active = 1
            """

            params = []

            # REGLA DE NEGOCIO: Vendedores (role_id=4) y Vendedores Externos (role_id=19)
            # solo ven las OTs que ellos crearon (replica comportamiento de Laravel)
            user_role_id = current_user.get("role_id", 0)
            user_id = current_user.get("user_id", 0)

            if user_role_id in [4, 19]:  # Vendedor o Vendedor Externo
                base_query += " AND wo.creador_id = %s"
                params.append(user_id)

            # REGLA DE NEGOCIO: Ingenieros, Diseñadores y "Catalogadores" (según Constants de Laravel)
            # solo ven OTs donde están asignados (replica comportamiento de Laravel línea 118)
            # IMPORTANTE: Hay inconsistencia entre DB y código Laravel:
            # - En Constants.php: isCatalogador() = role_id 10, isPrecatalogador() = role_id 12
            # - En tabla roles: role_id 10 = "Precatalogador", role_id 12 = "Catalogador"
            # Laravel usa Constants, así que role_id 10 recibe filtro de asignación
            # Roles: 6=Ingeniero, 8=Diseñador, 10=isCatalogador() en Laravel
            ROLES_FILTRAR_POR_ASIGNACION = [6, 8, 10]
            if user_role_id in ROLES_FILTRAR_POR_ASIGNACION:
                base_query += """
                    AND wo.id IN (
                        SELECT work_order_id
                        FROM user_work_orders
                        WHERE user_id = %s
                    )
                """
                params.append(user_id)

            # REGLA DE NEGOCIO: Filtrado por área según rol (replica Laravel líneas 312-315)
            # Diseñador/Jefe Diseño (7, 8): solo ven OTs en área 3 (Diseño)
            # Precatalogador/JefePrecatalogador/Catalogador/JefeCatalogador (9,10,11,12):
            # solo ven OTs en áreas 4,5 (Precatalogación, Catalogación)
            ROLES_AREA_DISENO = [7, 8]  # Jefe Diseño, Diseñador
            ROLES_AREA_CATALOGACION = [9, 10, 11, 12]  # Jefe Precatalog, Precatalog, Jefe Catalog, Catalogador

            if user_role_id in ROLES_AREA_DISENO:
                base_query += " AND wo.current_area_id = 3"
            elif user_role_id in ROLES_AREA_CATALOGACION:
                base_query += " AND wo.current_area_id IN (4, 5)"

            # Aplicar filtros
            if id_ot:
                base_query += " AND wo.id = %s"
                params.append(id_ot)

            if date_desde:
                base_query += " AND wo.created_at >= %s"
                params.append(date_desde)

            if date_hasta:
                base_query += " AND wo.created_at <= %s"
                params.append(date_hasta + " 23:59:59")

            if client_id:
                placeholders = ','.join(['%s'] * len(client_id))
                base_query += f" AND wo.client_id IN ({placeholders})"
                params.extend(client_id)

            if estado_id:
                placeholders = ','.join(['%s'] * len(estado_id))
                base_query += f" AND m.state_id IN ({placeholders})"
                params.extend(estado_id)

            if area_id:
                placeholders = ','.join(['%s'] * len(area_id))
                base_query += f" AND wo.current_area_id IN ({placeholders})"
                params.extend(area_id)

            if canal_id:
                placeholders = ','.join(['%s'] * len(canal_id))
                base_query += f" AND wo.canal_id IN ({placeholders})"
                params.extend(canal_id)

            if vendedor_id:
                placeholders = ','.join(['%s'] * len(vendedor_id))
                base_query += f" AND wo.creador_id IN ({placeholders})"
                params.extend(vendedor_id)

            if cad:
                base_query += " AND cad.codigo LIKE %s"
                params.append(f"%{cad}%")

            if carton:
                base_query += " AND cart.codigo LIKE %s"
                params.append(f"%{carton}%")

            if material:
                base_query += " AND mat.codigo LIKE %s"
                params.append(f"%{material}%")

            if descripcion:
                base_query += " AND wo.descripcion LIKE %s"
                params.append(f"%{descripcion}%")

            if planta_id:
                placeholders = ','.join(['%s'] * len(planta_id))
                base_query += f" AND wo.planta_id IN ({placeholders})"
                params.extend(planta_id)

            if tipo_solicitud:
                placeholders = ','.join(['%s'] * len(tipo_solicitud))
                base_query += f" AND wo.tipo_solicitud IN ({placeholders})"
                params.extend(tipo_solicitud)

            # Contar total
            count_query = f"SELECT COUNT(*) as total FROM ({base_query}) as subquery"
            cursor.execute(count_query, params)
            total = cursor.fetchone()['total']

            # Ordenar y paginar
            base_query += " ORDER BY wo.id DESC LIMIT %s OFFSET %s"
            offset = (page - 1) * page_size
            params.extend([page_size, offset])

            cursor.execute(base_query, params)
            rows = cursor.fetchall()

            # Inicializar calculador de horas laborales (replica Laravel get_working_hours)
            wh_calculator = WorkingHoursCalculator(connection)
            now = datetime.now()
            HOURS_PER_DAY = 9.5  # Horas laborales por día (igual que Laravel)

            # Mapeo área -> campo de tiempo
            area_to_field = {
                1: 'horas_venta_stored',
                2: 'horas_desarrollo_stored',
                3: 'horas_diseno_stored',
                4: 'horas_catalogacion_stored',
                5: 'horas_precatalogacion_stored',
                6: 'horas_muestra_stored',
            }

            # Transformar resultados con cálculo de tiempo real-time
            items = []
            for row in rows:
                # Obtener horas almacenadas
                horas_venta = float(row['horas_venta_stored'] or 0)
                horas_desarrollo = float(row['horas_desarrollo_stored'] or 0)
                horas_diseno = float(row['horas_diseno_stored'] or 0)
                horas_catalogacion = float(row['horas_catalogacion_stored'] or 0)
                horas_precatalogacion = float(row['horas_precatalogacion_stored'] or 0)
                horas_muestra = float(row['horas_muestra_stored'] or 0)
                horas_total = float(row['horas_total_stored'] or 0)

                # Si la OT está en un área activa, calcular tiempo real usando get_working_hours
                current_area_id = row.get('current_area_id')
                ultimo_cambio = row.get('ultimo_cambio_area')

                if current_area_id and ultimo_cambio:
                    # Calcular horas laborales desde ultimo_cambio_area hasta ahora
                    # Esto replica exactamente Laravel get_working_hours()
                    horas_realtime = wh_calculator.get_working_hours(ultimo_cambio, now)

                    # Sumar al área correspondiente
                    if current_area_id == 1:
                        horas_venta += horas_realtime
                    elif current_area_id == 2:
                        horas_desarrollo += horas_realtime
                    elif current_area_id == 3:
                        horas_diseno += horas_realtime
                    elif current_area_id == 4:
                        horas_catalogacion += horas_realtime
                    elif current_area_id == 5:
                        horas_precatalogacion += horas_realtime
                    elif current_area_id == 6:
                        horas_muestra += horas_realtime

                    # Sumar al total también
                    horas_total += horas_realtime

                # Convertir horas a días laborales (9.5 horas/día)
                items.append(WorkOrderListItem(
                    id=row['id'],
                    created_at=row['created_at'].strftime('%d/%m/%y') if row['created_at'] else '',
                    client_name=row['client_name'] or '',
                    descripcion=row['descripcion'] or '',
                    canal=row['canal'],
                    item_tipo=row['item_tipo'],
                    estado=row['estado'],
                    estado_abrev=row['estado_abrev'],
                    creador_nombre=row['creador_nombre'] or '',
                    tipo_solicitud=row['tipo_solicitud'],
                    current_area_id=current_area_id,
                    ultimo_cambio_area=ultimo_cambio.strftime('%d/%m/%y') if ultimo_cambio else None,
                    cad=row['cad_codigo'],
                    carton=row['carton_codigo'],
                    material_codigo=row['material_codigo'],
                    # Tiempos en días laborales (horas / 9.5)
                    tiempo_total=round(horas_total / HOURS_PER_DAY, 1),
                    tiempo_venta=round(horas_venta / HOURS_PER_DAY, 1),
                    tiempo_desarrollo=round(horas_desarrollo / HOURS_PER_DAY, 1),
                    tiempo_muestra=round(horas_muestra / HOURS_PER_DAY, 1),
                    tiempo_diseno=round(horas_diseno / HOURS_PER_DAY, 1),
                    tiempo_externo=0,  # No hay área específica para externo
                    tiempo_precatalogacion=round(horas_precatalogacion / HOURS_PER_DAY, 1),
                    tiempo_catalogacion=round(horas_catalogacion / HOURS_PER_DAY, 1),
                ))

            total_pages = (total + page_size - 1) // page_size

            return WorkOrderListResponse(
                items=items,
                total=total,
                page=page,
                page_size=page_size,
                total_pages=total_pages
            )
    finally:
        connection.close()


@router.get("/filter-options", response_model=FilterOptions)
async def get_filter_options():
    """
    Obtiene todas las opciones para los filtros del dashboard.
    """
    connection = get_mysql_connection()
    try:
        with connection.cursor() as cursor:
            options = {}

            # Estados (usa 'status' texto, no 'active')
            cursor.execute("SELECT id, nombre, abreviatura FROM states WHERE status = 'active' ORDER BY id")
            options['estados'] = cursor.fetchall()

            # Áreas (work_spaces usa 'status' texto)
            cursor.execute("SELECT id, nombre FROM work_spaces WHERE status = 'active' ORDER BY id")
            options['areas'] = cursor.fetchall()

            # Canales
            cursor.execute("SELECT id, nombre FROM canals WHERE active = 1 ORDER BY nombre")
            options['canales'] = cursor.fetchall()

            # Clientes (top 500 más usados) - ordenados por codigo
            cursor.execute("""
                SELECT DISTINCT c.id, c.nombre as nombre, c.codigo
                FROM clients c
                INNER JOIN work_orders wo ON c.id = wo.client_id
                WHERE c.active = 1
                ORDER BY c.codigo ASC
                LIMIT 500
            """)
            options['clientes'] = cursor.fetchall()

            # Vendedores (usuarios que pueden crear OT)
            cursor.execute("""
                SELECT id, CONCAT(nombre, ' ', apellido) as nombre
                FROM users
                WHERE active = 1 AND role_id IN (2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12)
                ORDER BY nombre
            """)
            options['vendedores'] = cursor.fetchall()

            # Plantas (no tiene columna active)
            cursor.execute("SELECT id, nombre FROM plantas ORDER BY nombre")
            options['plantas'] = cursor.fetchall()

            # Impresiones (tabla impresion - usa 'status' no 'active')
            cursor.execute("""
                SELECT id, descripcion as nombre
                FROM impresion
                WHERE status = 1
                ORDER BY id
            """)
            options['impresiones'] = cursor.fetchall()

            # Procesos
            cursor.execute("SELECT id, descripcion as nombre FROM processes WHERE active = 1 ORDER BY descripcion")
            options['procesos'] = cursor.fetchall()

            # Estilos
            cursor.execute("SELECT id, glosa as nombre FROM styles WHERE active = 1 ORDER BY glosa")
            options['estilos'] = cursor.fetchall()

            # FSC
            cursor.execute("SELECT codigo as id, descripcion as nombre FROM fsc WHERE active = 1 ORDER BY descripcion")
            options['fsc'] = cursor.fetchall()

            # Organizaciones de venta (tabla no existe - hardcoded)
            options['org_ventas'] = [
                {"id": 1, "nombre": "Nacional"},
                {"id": 2, "nombre": "Exportación"}
            ]

            return FilterOptions(**options)
    finally:
        connection.close()


# =============================================
# FORM OPTIONS COMPLETE - debe estar antes de /{ot_id}
# =============================================
# Nota: La implementación está más abajo, pero la ruta debe definirse aquí
# para que FastAPI no intente parsear "form-options-complete" como un int

@router.get("/form-options-complete")
async def get_form_options_complete_route():
    """
    Obtiene TODAS las opciones necesarias para el formulario de crear/editar OT.
    Redirige a la implementación completa.
    """
    return await _get_form_options_complete_impl()


# =============================================
# RUTAS ESTÁTICAS - deben estar antes de /{ot_id}
# =============================================
# Estas rutas se definen aquí para que FastAPI no intente
# parsear sus paths como integers

@router.get("/pending-approval")
async def get_pending_approval_route(
    page: int = Query(1, ge=1),
    page_size: int = Query(20, ge=1, le=100),
    current_user: dict = Depends(get_current_user_with_role)
):
    """Lista OTs pendientes de aprobación según rol del usuario."""
    return await _get_pending_approval_impl(page, page_size, current_user["user_id"], current_user["role_id"])


@router.get("/pending-assignment")
async def get_pending_assignment_route(
    page: int = Query(1, ge=1),
    page_size: int = Query(20, ge=1, le=100),
    asignado: Optional[str] = Query(None, description="SI o NO"),
    tipo_solicitud: Optional[str] = None,
    canal_id: Optional[int] = None,
    vendedor_id: Optional[int] = None,
    estado_id: Optional[int] = None,
    date_desde: Optional[str] = None,
    date_hasta: Optional[str] = None,
    user_id: int = Depends(get_current_user_id)
):
    """Redirige a la implementación de pending-assignment."""
    return await _get_pending_assignment_impl(
        page, page_size, asignado, tipo_solicitud,
        canal_id, vendedor_id, estado_id, date_desde, date_hasta, user_id
    )


@router.get("/professionals")
async def get_professionals_route(user_id: int = Depends(get_current_user_id)):
    """Redirige a la implementación de professionals."""
    return await _get_professionals_impl(user_id)


@router.get("/{ot_id}")
async def get_work_order(ot_id: int):
    """
    Obtiene detalle de una OT específica.
    """
    connection = get_mysql_connection()
    try:
        with connection.cursor() as cursor:
            sql = """
                SELECT
                    wo.*,
                    c.nombre as client_name,
                    c.codigo as client_codigo,
                    CONCAT(u.nombre, ' ', u.apellido) as creador_nombre,
                    ch.nombre as canal_nombre,
                    pt.descripcion as product_type,
                    cad.cad as cad_codigo,
                    cart.codigo as carton_codigo,
                    mat.codigo as material_codigo,
                    s.nombre as estado_nombre,
                    s.abreviatura as estado_abrev,
                    ws.nombre as area_actual
                FROM work_orders wo
                LEFT JOIN clients c ON wo.client_id = c.id
                LEFT JOIN users u ON wo.creador_id = u.id
                LEFT JOIN canals ch ON wo.canal_id = ch.id
                LEFT JOIN product_types pt ON wo.product_type_id = pt.id
                LEFT JOIN cads cad ON wo.cad_id = cad.id
                LEFT JOIN cartons cart ON wo.carton_id = cart.id
                LEFT JOIN materials mat ON wo.material_id = mat.id
                LEFT JOIN work_spaces ws ON wo.current_area_id = ws.id
                LEFT JOIN (
                    SELECT work_order_id, state_id
                    FROM managements
                    WHERE id IN (SELECT MAX(id) FROM managements GROUP BY work_order_id)
                ) m ON wo.id = m.work_order_id
                LEFT JOIN states s ON m.state_id = s.id
                WHERE wo.id = %s
            """
            cursor.execute(sql, (ot_id,))
            ot = cursor.fetchone()

            if not ot:
                raise HTTPException(
                    status_code=status.HTTP_404_NOT_FOUND,
                    detail=f"OT {ot_id} no encontrada"
                )

            # Convertir datetime a string
            for key, value in ot.items():
                if isinstance(value, datetime):
                    ot[key] = value.isoformat()

            return ot
    finally:
        connection.close()


@router.post("/", response_model=WorkOrderCreateResponse)
async def create_work_order(
    data: WorkOrderCreate,
    user_id: int = Depends(get_current_user_id)
):
    """
    Crea una nueva orden de trabajo.

    El usuario autenticado se asigna como creador y la OT inicia en area 1 (Ventas).
    """
    connection = get_mysql_connection()
    try:
        with connection.cursor() as cursor:
            now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

            # Construir campos e insertar OT
            insert_fields = {
                # Campos requeridos
                'client_id': data.client_id,
                'descripcion': data.descripcion,
                'tipo_solicitud': data.tipo_solicitud,
                'canal_id': data.canal_id,
                'creador_id': user_id,
                'current_area_id': 1,  # Inicia en Ventas
                'active': 1,
                'ultimo_cambio_area': now,  # Fecha de entrada al área actual
                'created_at': now,
                'updated_at': now,
            }

            # Agregar campos opcionales si tienen valor
            optional_fields = {
                'org_venta_id': data.org_venta_id,
                'subsubhierarchy_id': data.subsubhierarchy_id,
                'nombre_contacto': data.nombre_contacto,
                'email_contacto': data.email_contacto,
                'telefono_contacto': data.telefono_contacto,
                'volumen_venta_anual': data.volumen_venta_anual,
                'usd': data.usd,
                'oc': data.oc,
                'codigo_producto': data.codigo_producto,
                'dato_sub_cliente': data.dato_sub_cliente,
                'instalacion_cliente': data.instalacion_cliente,
                # Solicitante
                'analisis': data.analisis,
                'plano': data.plano,
                'muestra': data.muestra,
                'datos_cotizar': data.datos_cotizar,
                'boceto': data.boceto,
                'nuevo_material': data.nuevo_material,
                'prueba_industrial': data.prueba_industrial,
                'numero_muestras': data.numero_muestras,
                # Referencia
                'reference_type': data.reference_type,
                'reference_id': data.reference_id,
                # Caracteristicas (Cascade)
                'product_type_id': data.product_type_id,
                'impresion': data.impresion,
                'fsc': data.fsc,
                'cinta': data.cinta,
                'coverage_internal_id': data.coverage_internal_id,
                'coverage_external_id': data.coverage_external_id,
                'carton_color': data.carton_color,
                'carton_id': data.carton_id,
                'cad_id': data.cad_id,
                'cad': data.cad,
                'style_id': data.style_id,
                # Medidas
                'interno_largo': data.interno_largo,
                'interno_ancho': data.interno_ancho,
                'interno_alto': data.interno_alto,
                'externo_largo': data.externo_largo,
                'externo_ancho': data.externo_ancho,
                'externo_alto': data.externo_alto,
                # Terminaciones
                'process_id': data.process_id,
                'armado_id': data.armado_id,
                'sentido_armado': data.sentido_armado,
                'tipo_sentido_onda': data.tipo_sentido_onda,
                # Colores
                'numero_colores': data.numero_colores,
                'color_1_id': data.color_1_id,
                'color_2_id': data.color_2_id,
                'color_3_id': data.color_3_id,
                'color_4_id': data.color_4_id,
                'color_5_id': data.color_5_id,
                # Desarrollo
                'peso_contenido_caja': data.peso_contenido_caja,
                'autosoportante': data.autosoportante,
                'envase_id': data.envase_id,
                'cantidad': data.cantidad,
                'observacion': data.observacion,
                # Planta
                'planta_id': data.planta_id,
            }

            for field, value in optional_fields.items():
                if value is not None:
                    insert_fields[field] = value

            # Construir query dinámico
            columns = ', '.join(insert_fields.keys())
            placeholders = ', '.join(['%s'] * len(insert_fields))
            values = list(insert_fields.values())

            sql = f"INSERT INTO work_orders ({columns}) VALUES ({placeholders})"
            cursor.execute(sql, values)
            ot_id = cursor.lastrowid

            # Crear registro en managements (estado inicial)
            mgmt_sql = """
                INSERT INTO managements
                (work_order_id, work_space_id, state_id, user_id, management_type_id, created_at, updated_at)
                VALUES (%s, 1, 1, %s, 1, %s, %s)
            """
            cursor.execute(mgmt_sql, (ot_id, user_id, now, now))

            # Asignar usuario a la OT (tiempo_inicial = 0 segundos para nueva OT)
            user_wo_sql = """
                INSERT INTO user_work_orders (user_id, work_order_id, area_id, tiempo_inicial, created_at, updated_at)
                VALUES (%s, %s, 1, 0, %s, %s)
            """
            cursor.execute(user_wo_sql, (user_id, ot_id, now, now))

            connection.commit()

            return WorkOrderCreateResponse(
                id=ot_id,
                message=f"Orden de trabajo {ot_id} creada exitosamente"
            )

    except pymysql.Error as e:
        connection.rollback()
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Error al crear OT: {str(e)}"
        )
    finally:
        connection.close()


class WorkOrderUpdate(BaseModel):
    """Schema para actualizar una OT."""
    # Datos Comerciales
    client_id: Optional[int] = None
    descripcion: Optional[str] = Field(None, max_length=40)
    tipo_solicitud: Optional[int] = None
    canal_id: Optional[int] = None
    org_venta_id: Optional[int] = None
    subsubhierarchy_id: Optional[int] = None
    nombre_contacto: Optional[str] = None
    email_contacto: Optional[str] = None
    telefono_contacto: Optional[str] = None
    volumen_venta_anual: Optional[int] = None
    usd: Optional[int] = None
    oc: Optional[int] = None
    codigo_producto: Optional[str] = None
    dato_sub_cliente: Optional[str] = None
    instalacion_cliente: Optional[int] = None
    # Solicitante
    analisis: Optional[int] = None
    plano: Optional[int] = None
    muestra: Optional[int] = None
    datos_cotizar: Optional[int] = None
    boceto: Optional[int] = None
    nuevo_material: Optional[int] = None
    prueba_industrial: Optional[int] = None
    numero_muestras: Optional[int] = None
    # Referencia
    reference_type: Optional[int] = None
    reference_id: Optional[int] = None
    # Caracteristicas (Cascade)
    product_type_id: Optional[int] = None
    impresion: Optional[int] = None
    fsc: Optional[str] = None
    cinta: Optional[int] = None
    coverage_internal_id: Optional[int] = None
    coverage_external_id: Optional[int] = None
    carton_color: Optional[int] = None
    carton_id: Optional[int] = None
    cad_id: Optional[int] = None
    cad: Optional[str] = None
    style_id: Optional[int] = None
    # Medidas
    interno_largo: Optional[int] = None
    interno_ancho: Optional[int] = None
    interno_alto: Optional[int] = None
    externo_largo: Optional[int] = None
    externo_ancho: Optional[int] = None
    externo_alto: Optional[int] = None
    # Terminaciones
    process_id: Optional[int] = None
    armado_id: Optional[int] = None
    sentido_armado: Optional[int] = None
    tipo_sentido_onda: Optional[str] = None
    # Colores
    numero_colores: Optional[int] = None
    color_1_id: Optional[int] = None
    color_2_id: Optional[int] = None
    color_3_id: Optional[int] = None
    color_4_id: Optional[int] = None
    color_5_id: Optional[int] = None
    # Desarrollo
    peso_contenido_caja: Optional[int] = None
    autosoportante: Optional[int] = None
    envase_id: Optional[int] = None
    cantidad: Optional[int] = None
    observacion: Optional[str] = None
    # Planta
    planta_id: Optional[int] = None


class WorkOrderUpdateResponse(BaseModel):
    """Respuesta al actualizar OT."""
    id: int
    message: str


@router.put("/{ot_id}", response_model=WorkOrderUpdateResponse)
async def update_work_order(
    ot_id: int,
    data: WorkOrderUpdate,
    user_id: int = Depends(get_current_user_id)
):
    """
    Actualiza una orden de trabajo existente.
    """
    connection = get_mysql_connection()
    try:
        with connection.cursor() as cursor:
            # Verificar que la OT existe
            cursor.execute("SELECT id FROM work_orders WHERE id = %s AND active = 1", (ot_id,))
            if not cursor.fetchone():
                raise HTTPException(
                    status_code=status.HTTP_404_NOT_FOUND,
                    detail=f"OT {ot_id} no encontrada"
                )

            now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

            # Construir campos a actualizar (solo los que tienen valor)
            update_fields = {'updated_at': now}

            # Iterar sobre todos los campos del modelo
            for field_name, field_value in data.model_dump(exclude_unset=True).items():
                if field_value is not None:
                    update_fields[field_name] = field_value

            if len(update_fields) <= 1:  # Solo updated_at
                return WorkOrderUpdateResponse(
                    id=ot_id,
                    message="No hay campos para actualizar"
                )

            # Construir query dinámico
            set_clause = ', '.join([f"{k} = %s" for k in update_fields.keys()])
            values = list(update_fields.values())
            values.append(ot_id)

            sql = f"UPDATE work_orders SET {set_clause} WHERE id = %s"
            cursor.execute(sql, values)

            connection.commit()

            return WorkOrderUpdateResponse(
                id=ot_id,
                message=f"Orden de trabajo {ot_id} actualizada exitosamente"
            )

    except pymysql.Error as e:
        connection.rollback()
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Error al actualizar OT: {str(e)}"
        )
    finally:
        connection.close()


# ========== GESTION DE OT (Workflow) ==========

class ManagementHistoryItem(BaseModel):
    """Item del historial de gestion."""
    id: int
    work_space: str
    state: str
    user_name: str
    observation: Optional[str]
    created_at: str


class ManagementHistoryResponse(BaseModel):
    """Respuesta con historial de gestion."""
    ot_id: int
    current_area: str
    current_state: str
    history: List[ManagementHistoryItem]


class WorkflowOptions(BaseModel):
    """Opciones disponibles para transicion."""
    areas: List[dict]
    states: List[dict]
    management_types: List[dict]


class TransitionRequest(BaseModel):
    """Solicitud de transicion de OT."""
    management_type_id: int = Field(..., description="ID del tipo de gestion (1=Cambio Estado, 2=Consulta, 3=Archivo)")
    work_space_id: Optional[int] = Field(None, description="ID del area destino (requerido para Cambio de Estado)")
    state_id: Optional[int] = Field(None, description="ID del nuevo estado (requerido para Cambio de Estado)")
    observation: Optional[str] = Field(None, max_length=500, description="Observacion")


class TransitionResponse(BaseModel):
    """Respuesta de transicion."""
    id: int
    message: str
    new_area: str
    new_state: str


@router.get("/{ot_id}/management", response_model=ManagementHistoryResponse)
async def get_management_history(ot_id: int):
    """
    Obtiene el historial de gestion de una OT.
    """
    connection = get_mysql_connection()
    try:
        with connection.cursor() as cursor:
            # Verificar que la OT existe
            cursor.execute("""
                SELECT wo.id, ws.nombre as area_actual, s.nombre as estado_actual
                FROM work_orders wo
                LEFT JOIN work_spaces ws ON wo.current_area_id = ws.id
                LEFT JOIN (
                    SELECT work_order_id, state_id
                    FROM managements
                    WHERE id IN (SELECT MAX(id) FROM managements GROUP BY work_order_id)
                ) latest ON wo.id = latest.work_order_id
                LEFT JOIN states s ON latest.state_id = s.id
                WHERE wo.id = %s AND wo.active = 1
            """, (ot_id,))
            ot = cursor.fetchone()

            if not ot:
                raise HTTPException(
                    status_code=status.HTTP_404_NOT_FOUND,
                    detail=f"OT {ot_id} no encontrada"
                )

            # Obtener historial de managements
            cursor.execute("""
                SELECT
                    m.id,
                    ws.nombre as work_space,
                    s.nombre as state,
                    CONCAT(u.nombre, ' ', u.apellido) as user_name,
                    m.observacion,
                    m.created_at
                FROM managements m
                LEFT JOIN work_spaces ws ON m.work_space_id = ws.id
                LEFT JOIN states s ON m.state_id = s.id
                LEFT JOIN users u ON m.user_id = u.id
                WHERE m.work_order_id = %s
                ORDER BY m.created_at DESC
            """, (ot_id,))
            history_raw = cursor.fetchall()

            history = []
            for item in history_raw:
                history.append(ManagementHistoryItem(
                    id=item['id'],
                    work_space=item['work_space'] or 'N/A',
                    state=item['state'] or 'N/A',
                    user_name=item['user_name'] or 'Sistema',
                    observation=item['observacion'],
                    created_at=item['created_at'].isoformat() if item['created_at'] else ''
                ))

            return ManagementHistoryResponse(
                ot_id=ot_id,
                current_area=ot['area_actual'] or 'Sin asignar',
                current_state=ot['estado_actual'] or 'Sin estado',
                history=history
            )

    finally:
        connection.close()


@router.get("/{ot_id}/workflow-options", response_model=WorkflowOptions)
async def get_workflow_options(
    ot_id: int,
    current_user: dict = Depends(get_current_user_with_role)
):
    """
    Obtiene las opciones disponibles para transicion de una OT.
    Filtra management_types y estados basado en el rol del usuario y el area actual de la OT.

    Regla de negocio (de Laravel):
    - Si el area actual de la OT == work_space_id del rol del usuario: puede hacer Cambio de Estado
    - Si no: solo puede hacer Consulta y Archivo
    - Estados filtrados segun el area del usuario (replica comportamiento Laravel)
    """
    # Constantes de areas (work_space_id)
    AREA_VENTAS = 1
    AREA_DESARROLLO = 2
    AREA_DISENO = 3
    AREA_PRECATALOGACION = 4
    AREA_CATALOGACION = 5
    AREA_MUESTRAS = 6

    # IDs de management_types
    MGMT_CAMBIO_ESTADO = 1
    MGMT_CONSULTA = 2
    MGMT_ARCHIVO = 3

    connection = get_mysql_connection()
    try:
        with connection.cursor() as cursor:
            # Obtener info de la OT incluyendo tipo_solicitud
            cursor.execute("""
                SELECT id, current_area_id, tipo_solicitud
                FROM work_orders WHERE id = %s AND active = 1
            """, (ot_id,))
            ot = cursor.fetchone()

            if not ot:
                raise HTTPException(
                    status_code=status.HTTP_404_NOT_FOUND,
                    detail=f"OT {ot_id} no encontrada"
                )

            ot_current_area = ot.get('current_area_id') or AREA_VENTAS
            tipo_solicitud = ot.get('tipo_solicitud')

            # Obtener role y work_space_id del usuario
            role_id = current_user.get('role_id')

            cursor.execute("""
                SELECT r.id, r.nombre, r.work_space_id
                FROM roles r WHERE r.id = %s
            """, (role_id,))
            role = cursor.fetchone()

            user_work_space_id = role.get('work_space_id') if role else None

            # Determinar si el usuario tiene permisos para cambiar estado
            can_change_state = False

            if user_work_space_id is None:
                # Gerente o Admin (sin area asignada) puede todo
                can_change_state = True
            elif user_work_space_id == ot_current_area:
                # El area del rol coincide con el area actual de la OT
                can_change_state = True
            elif user_work_space_id == AREA_CATALOGACION and ot_current_area in [AREA_CATALOGACION, AREA_PRECATALOGACION]:
                # Catalogador tambien puede en Precatalogacion
                can_change_state = True

            # Filtrar management_types
            if can_change_state:
                allowed_mgmt_types = [MGMT_CAMBIO_ESTADO, MGMT_CONSULTA, MGMT_ARCHIVO]
            else:
                allowed_mgmt_types = [MGMT_CONSULTA, MGMT_ARCHIVO]

            # Obtener management_types filtrados
            placeholders = ','.join(['%s'] * len(allowed_mgmt_types))
            cursor.execute(f"""
                SELECT id, nombre FROM management_types
                WHERE id IN ({placeholders})
                ORDER BY id
            """, tuple(allowed_mgmt_types))
            management_types = cursor.fetchall()

            # Areas de trabajo
            cursor.execute("SELECT id, nombre FROM work_spaces WHERE status = 'active' ORDER BY id")
            areas = cursor.fetchall()

            # =============================================
            # FILTRAR ESTADOS SEGUN AREA DEL USUARIO (replica Laravel)
            # Estados:
            # 1=Proceso Ventas, 2=Diseño Estructural, 3=Laboratorio, 4=Muestra,
            # 5=Diseño Gráfico, 6=Cálculo Paletizado, 7=Catalogación, 8=Terminada,
            # 9=Perdida, 10=Consulta Cliente, 11=Anulada, 12=Rechazada, 13=Entregado,
            # 14=Espera OC, 15=Falta definición Cliente, 16=VB Cliente,
            # 17=Sala Muestras, 18=Muestras Listas, 20=Hibernación, 21=Cotización
            # =============================================
            states_by_area = []

            if user_work_space_id is None:
                # Gerente/Admin: todos los estados activos
                states_by_area = None  # Sin filtro
            elif user_work_space_id == AREA_VENTAS:
                # Vendedor: estados completos (caso general cuando ya fue enviado a desarrollo)
                # Laravel: [2, 5, 6, 7, 9, 10, 11, 14, 15, 16, 20, 21]
                states_by_area = [2, 5, 6, 7, 9, 10, 11, 14, 15, 16, 20, 21]
            elif user_work_space_id == AREA_DESARROLLO:
                # Ingeniero: [1, 3, 5, 6, 7, 12, 16, 17] (caso normal)
                # 1=Ventas, 3=Lab, 5=DG, 6=Calc, 7=Cat, 12=Rechazada, 16=VBC, 17=Sala
                states_by_area = [1, 3, 5, 6, 7, 12, 16, 17]
            elif user_work_space_id == AREA_DISENO:
                # Disenador: [1, 2, 7, 12, 16]
                # 1=Ventas, 2=DE, 7=Cat, 12=Rechazada, 16=VBC
                states_by_area = [1, 2, 7, 12, 16]
                # Si tipo_solicitud != 1 (no es Desarrollo Completo), agregar Entregado (13)
                if tipo_solicitud and tipo_solicitud != 1:
                    states_by_area.append(13)
            elif user_work_space_id == AREA_PRECATALOGACION:
                # Precatalogador: [1, 2, 5, 8, 12]
                # 1=Ventas, 2=DE, 5=DG, 8=Terminada, 12=Rechazada
                states_by_area = [1, 2, 5, 8, 12]
            elif user_work_space_id == AREA_CATALOGACION:
                # Catalogador: depende del area actual de la OT
                if ot_current_area == AREA_PRECATALOGACION:
                    # Si OT en Precatalogación: [1, 2, 5, 8, 12]
                    states_by_area = [1, 2, 5, 8, 12]
                else:
                    # Si OT en Catalogación: [1, 2, 5, 7, 12]
                    states_by_area = [1, 2, 5, 7, 12]
            elif user_work_space_id == AREA_MUESTRAS:
                # Tecnico Muestras: [12, 18, 22]
                # 12=Rechazada, 18=Muestras Listas, 22=Muestra Devuelta
                states_by_area = [12, 18, 22]
            else:
                # Otros: estados basicos
                states_by_area = [1, 2, 7, 12]

            # Obtener estados filtrados
            if states_by_area:
                placeholders = ','.join(['%s'] * len(states_by_area))
                cursor.execute(f"""
                    SELECT id, nombre, abreviatura FROM states
                    WHERE status = 'active' AND id IN ({placeholders})
                    ORDER BY id
                """, tuple(states_by_area))
            else:
                cursor.execute("SELECT id, nombre, abreviatura FROM states WHERE status = 'active' ORDER BY id")
            states = cursor.fetchall()

            return WorkflowOptions(
                areas=areas,
                states=states,
                management_types=management_types
            )

    finally:
        connection.close()


@router.post("/{ot_id}/transition", response_model=TransitionResponse)
async def transition_work_order(
    ot_id: int,
    data: TransitionRequest,
    user_id: int = Depends(get_current_user_id)
):
    """
    Realiza una transicion de estado/area en una OT.
    Tipos de gestion:
    - 1: Cambio de Estado (requiere work_space_id y state_id)
    - 2: Consulta (solo registra observacion)
    - 3: Archivo (archiva la OT)
    """
    # Constantes de tipos de gestion
    MGMT_CAMBIO_ESTADO = 1
    MGMT_CONSULTA = 2
    MGMT_ARCHIVO = 3

    connection = get_mysql_connection()
    try:
        with connection.cursor() as cursor:
            # Verificar que la OT existe y obtener datos actuales
            cursor.execute("""
                SELECT wo.id, wo.current_area_id, ws.nombre as area_actual, s.nombre as estado_actual
                FROM work_orders wo
                LEFT JOIN work_spaces ws ON wo.current_area_id = ws.id
                LEFT JOIN managements m ON wo.id = m.work_order_id
                LEFT JOIN states s ON m.state_id = s.id
                WHERE wo.id = %s AND wo.active = 1
                ORDER BY m.id DESC LIMIT 1
            """, (ot_id,))
            ot = cursor.fetchone()

            if not ot:
                raise HTTPException(
                    status_code=status.HTTP_404_NOT_FOUND,
                    detail=f"OT {ot_id} no encontrada"
                )

            now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            new_area_name = ot.get('area_actual') or 'N/A'
            new_state_name = ot.get('estado_actual') or 'N/A'
            message = ""

            # Procesar segun tipo de gestion
            if data.management_type_id == MGMT_CAMBIO_ESTADO:
                # Validar que state_id sea requerido
                if not data.state_id:
                    raise HTTPException(
                        status_code=status.HTTP_400_BAD_REQUEST,
                        detail="Para Cambio de Estado se requiere state_id"
                    )

                # Constantes de estados especiales
                STATE_SALA_MUESTRAS = 17
                STATE_MUESTRAS_LISTAS = 18
                STATE_RECHAZADA = 12
                AREA_MUESTRAS = 6
                AREA_DESARROLLO = 2

                # Determinar work_space_id automaticamente si no se proporciona
                effective_work_space_id = data.work_space_id

                # Mapeo de estados a areas (replica logica Laravel)
                state_to_area_map = {
                    1: 1,   # Proceso de Ventas -> Area Ventas
                    2: 2,   # Proceso de Diseño Estructural -> Area Desarrollo
                    3: 2,   # Laboratorio -> Area Desarrollo
                    5: 3,   # Proceso de Diseño Grafico -> Area Diseño
                    6: 2,   # Proceso de Calculo Paletizado -> Area Desarrollo
                    7: 5,   # Proceso de Catalogacion -> Area Catalogacion
                    17: 6,  # Sala de Muestras -> Area Muestras
                    18: 2,  # Muestras Listas -> Area Desarrollo
                }

                if not effective_work_space_id:
                    # Si no se proporciono area, obtenerla del mapeo o de la tabla states
                    if data.state_id in state_to_area_map:
                        effective_work_space_id = state_to_area_map[data.state_id]
                    else:
                        # Intentar obtener work_space_id de la tabla states
                        cursor.execute("SELECT work_space_id FROM states WHERE id = %s", (data.state_id,))
                        state_area = cursor.fetchone()
                        if state_area and state_area.get('work_space_id'):
                            effective_work_space_id = state_area['work_space_id']
                        else:
                            # Usar el area actual de la OT como fallback
                            effective_work_space_id = ot.get('current_area_id') or 1

                # Logica especial para Sala de Muestras (state_id = 17)
                if data.state_id == STATE_SALA_MUESTRAS:
                    # Verificar que existan muestras en la OT
                    cursor.execute("SELECT COUNT(*) as count FROM muestras WHERE work_order_id = %s", (ot_id,))
                    muestras_count = cursor.fetchone()
                    if not muestras_count or muestras_count['count'] == 0:
                        raise HTTPException(
                            status_code=status.HTTP_400_BAD_REQUEST,
                            detail="Debes ingresar al menos una muestra para enviar a Sala de Muestras"
                        )

                    # Forzar area = 6 (Sala de Muestras)
                    effective_work_space_id = AREA_MUESTRAS

                # Crear registro de management
                cursor.execute("""
                    INSERT INTO managements
                    (work_order_id, work_space_id, state_id, management_type_id, user_id, observacion, created_at, updated_at)
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
                """, (ot_id, effective_work_space_id, data.state_id, MGMT_CAMBIO_ESTADO, user_id, data.observation, now, now))

                # Actualizar current_area_id de la OT
                cursor.execute("""
                    UPDATE work_orders SET current_area_id = %s, ultimo_cambio_area = %s, updated_at = %s WHERE id = %s
                """, (effective_work_space_id, now, now, ot_id))

                # Logica especial post-transicion para Sala de Muestras
                if data.state_id == STATE_SALA_MUESTRAS:
                    # Actualizar muestras a estado "En Proceso" (1) e iniciar tiempo en sala
                    cursor.execute("""
                        UPDATE muestras
                        SET estado = 1, inicio_sala_corte = %s, updated_at = %s
                        WHERE work_order_id = %s AND estado IN (0, 1)
                    """, (now, now, ot_id))

                # Obtener nombres para respuesta
                cursor.execute("SELECT nombre FROM work_spaces WHERE id = %s", (effective_work_space_id,))
                area_row = cursor.fetchone()
                cursor.execute("SELECT nombre FROM states WHERE id = %s", (data.state_id,))
                state_row = cursor.fetchone()

                new_area_name = area_row['nombre'] if area_row else 'N/A'
                new_state_name = state_row['nombre'] if state_row else 'N/A'
                message = f"OT {ot_id} transicionada exitosamente"

            elif data.management_type_id == MGMT_CONSULTA:
                # Solo registrar consulta (sin cambiar estado)
                cursor.execute("""
                    INSERT INTO managements
                    (work_order_id, work_space_id, state_id, management_type_id, user_id, observacion, created_at, updated_at)
                    VALUES (%s, %s, NULL, %s, %s, %s, %s, %s)
                """, (ot_id, ot.get('current_area_id'), MGMT_CONSULTA, user_id, data.observation, now, now))

                message = f"Consulta registrada en OT {ot_id}"

            elif data.management_type_id == MGMT_ARCHIVO:
                # Archivar OT (registrar gestion y desactivar)
                cursor.execute("""
                    INSERT INTO managements
                    (work_order_id, work_space_id, state_id, management_type_id, user_id, observacion, created_at, updated_at)
                    VALUES (%s, %s, NULL, %s, %s, %s, %s, %s)
                """, (ot_id, ot.get('current_area_id'), MGMT_ARCHIVO, user_id, data.observation or 'OT archivada', now, now))

                # Marcar OT como archivada (no la desactivamos, solo la marcamos)
                cursor.execute("""
                    UPDATE work_orders SET archived = 1, updated_at = %s WHERE id = %s
                """, (now, ot_id))

                message = f"OT {ot_id} archivada exitosamente"

            else:
                raise HTTPException(
                    status_code=status.HTTP_400_BAD_REQUEST,
                    detail=f"Tipo de gestion {data.management_type_id} no valido"
                )

            connection.commit()

            return TransitionResponse(
                id=ot_id,
                message=message,
                new_area=new_area_name,
                new_state=new_state_name
            )

    except HTTPException:
        connection.rollback()
        raise
    except pymysql.Error as e:
        connection.rollback()
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Error al transicionar OT: {str(e)}"
        )
    finally:
        connection.close()


# =============================================
# APROBACIÓN DE OTs
# =============================================

class ApprovalListItem(BaseModel):
    """Item de la lista de OTs pendientes de aprobación."""
    id: int
    created_at: str
    client_name: str
    descripcion: str
    canal: Optional[str]
    item_tipo: Optional[str]
    estado: str
    estado_abrev: str
    creador_nombre: str


class ApprovalListResponse(BaseModel):
    """Respuesta paginada de OTs pendientes de aprobación."""
    items: List[ApprovalListItem]
    total: int
    page: int
    page_size: int
    total_pages: int


class ApprovalActionResponse(BaseModel):
    """Respuesta de acción de aprobar/rechazar."""
    id: int
    message: str
    new_state: str


async def _get_pending_approval_impl(
    page: int,
    page_size: int,
    user_id: int,
    role_id: int
):
    """
    Implementación: Lista OTs pendientes de aprobación según rol.

    Lógica de Laravel (WorkOrderController.php líneas 10555-10561):
    - Jefe de Venta (role_id=3): OTs donde aprobacion_jefe_venta = 1
    - Jefe de Desarrollo (role_id=5): OTs donde aprobacion_jefe_venta = 2 AND aprobacion_jefe_desarrollo = 1

    Estados de aprobación:
    - 0: Sin asignar
    - 1: Pendiente de aprobación
    - 2: Aprobado
    - 3: Rechazado
    """
    connection = get_mysql_connection()
    try:
        with connection.cursor() as cursor:
            # Construir WHERE según rol (igual que Laravel)
            # role_id 3 = Jefe de Venta
            # role_id 5 = Jefe de Desarrollo
            if role_id == 3:  # Jefe de Venta
                where_clause = "wo.aprobacion_jefe_venta = 1"
            elif role_id == 5:  # Jefe de Desarrollo
                where_clause = "wo.aprobacion_jefe_venta = 2 AND wo.aprobacion_jefe_desarrollo = 1"
            else:
                # Otros roles: mostrar todas las pendientes (admin, etc)
                where_clause = "(wo.aprobacion_jefe_venta = 1 OR (wo.aprobacion_jefe_venta = 2 AND wo.aprobacion_jefe_desarrollo = 1))"

            # Contar total
            count_sql = f"""
                SELECT COUNT(*) as total
                FROM work_orders wo
                WHERE wo.active = 1
                AND {where_clause}
            """
            cursor.execute(count_sql)
            total = cursor.fetchone()['total']

            # Calcular paginación
            total_pages = (total + page_size - 1) // page_size if total > 0 else 0
            offset = (page - 1) * page_size

            # Obtener OTs pendientes de aprobación
            sql = f"""
                SELECT
                    wo.id,
                    wo.created_at,
                    wo.descripcion,
                    c.nombre as client_name,
                    CONCAT(u.nombre, ' ', u.apellido) as creador_nombre,
                    ch.nombre as canal,
                    pt.descripcion as item_tipo,
                    COALESCE(s.nombre, 'Proceso de Ventas') as estado,
                    COALESCE(s.abreviatura, 'PV') as estado_abrev,
                    wo.aprobacion_jefe_venta,
                    wo.aprobacion_jefe_desarrollo
                FROM work_orders wo
                LEFT JOIN clients c ON wo.client_id = c.id
                LEFT JOIN users u ON wo.creador_id = u.id
                LEFT JOIN canals ch ON wo.canal_id = ch.id
                LEFT JOIN product_types pt ON wo.product_type_id = pt.id
                LEFT JOIN (
                    SELECT m1.work_order_id, m1.state_id
                    FROM managements m1
                    INNER JOIN (
                        SELECT work_order_id, MAX(created_at) as max_date
                        FROM managements
                        GROUP BY work_order_id
                    ) m2 ON m1.work_order_id = m2.work_order_id AND m1.created_at = m2.max_date
                ) last_mgmt ON wo.id = last_mgmt.work_order_id
                LEFT JOIN states s ON last_mgmt.state_id = s.id
                WHERE wo.active = 1
                AND {where_clause}
                ORDER BY wo.created_at DESC
                LIMIT %s OFFSET %s
            """
            cursor.execute(sql, (page_size, offset))
            rows = cursor.fetchall()

            items = []
            for row in rows:
                items.append(ApprovalListItem(
                    id=row['id'],
                    created_at=row['created_at'].strftime('%Y-%m-%d') if row['created_at'] else '',
                    client_name=row['client_name'] or 'Sin cliente',
                    descripcion=row['descripcion'] or '',
                    canal=row['canal'],
                    item_tipo=row['item_tipo'],
                    estado=row['estado'],
                    estado_abrev=row['estado_abrev'],
                    creador_nombre=row['creador_nombre'] or 'Sin creador'
                ))

            return ApprovalListResponse(
                items=items,
                total=total,
                page=page,
                page_size=page_size,
                total_pages=total_pages
            )

    finally:
        connection.close()


@router.put("/{ot_id}/approve", response_model=ApprovalActionResponse)
async def approve_work_order(
    ot_id: int,
    current_user: dict = Depends(get_current_user_with_role)
):
    """
    Aprueba una OT pendiente según el rol del usuario.

    Lógica de Laravel (WorkOrderController.php líneas 10567-10574):
    - Jefe de Venta (role_id=3): actualiza aprobacion_jefe_venta = 2
    - Jefe de Desarrollo (role_id=5): actualiza aprobacion_jefe_desarrollo = 2
    """
    user_id = current_user["user_id"]
    role_id = current_user["role_id"]

    connection = get_mysql_connection()
    try:
        with connection.cursor() as cursor:
            # Verificar que la OT existe
            cursor.execute("""
                SELECT id, aprobacion_jefe_venta, aprobacion_jefe_desarrollo
                FROM work_orders
                WHERE id = %s AND active = 1
            """, (ot_id,))
            ot = cursor.fetchone()

            if not ot:
                raise HTTPException(
                    status_code=status.HTTP_404_NOT_FOUND,
                    detail=f"OT {ot_id} no encontrada"
                )

            now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

            # Actualizar según rol (igual que Laravel)
            if role_id == 3:  # Jefe de Venta
                if ot['aprobacion_jefe_venta'] == 2:
                    raise HTTPException(
                        status_code=status.HTTP_400_BAD_REQUEST,
                        detail=f"OT {ot_id} ya fue aprobada por Jefe de Venta"
                    )
                cursor.execute("""
                    UPDATE work_orders
                    SET aprobacion_jefe_venta = 2, updated_at = %s
                    WHERE id = %s
                """, (now, ot_id))
                approval_type = "Jefe de Venta"

            elif role_id == 5:  # Jefe de Desarrollo
                if ot['aprobacion_jefe_desarrollo'] == 2:
                    raise HTTPException(
                        status_code=status.HTTP_400_BAD_REQUEST,
                        detail=f"OT {ot_id} ya fue aprobada por Jefe de Desarrollo"
                    )
                cursor.execute("""
                    UPDATE work_orders
                    SET aprobacion_jefe_desarrollo = 2, updated_at = %s
                    WHERE id = %s
                """, (now, ot_id))
                approval_type = "Jefe de Desarrollo"
            else:
                raise HTTPException(
                    status_code=status.HTTP_403_FORBIDDEN,
                    detail="No tiene permisos para aprobar OTs"
                )

            connection.commit()

            return ApprovalActionResponse(
                id=ot_id,
                message=f"OT {ot_id} aprobada por {approval_type}",
                new_state="Aprobado"
            )

    except pymysql.Error as e:
        connection.rollback()
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Error al aprobar OT: {str(e)}"
        )
    finally:
        connection.close()


@router.put("/{ot_id}/reject", response_model=ApprovalActionResponse)
async def reject_work_order(
    ot_id: int,
    current_user: dict = Depends(get_current_user_with_role)
):
    """
    Rechaza una OT pendiente según el rol del usuario.

    Lógica de Laravel (WorkOrderController.php líneas 10577-10584):
    - Jefe de Venta (role_id=3): actualiza aprobacion_jefe_venta = 3
    - Jefe de Desarrollo (role_id=5): actualiza aprobacion_jefe_desarrollo = 3
    """
    user_id = current_user["user_id"]
    role_id = current_user["role_id"]

    connection = get_mysql_connection()
    try:
        with connection.cursor() as cursor:
            # Verificar que la OT existe
            cursor.execute("""
                SELECT id, aprobacion_jefe_venta, aprobacion_jefe_desarrollo
                FROM work_orders
                WHERE id = %s AND active = 1
            """, (ot_id,))
            ot = cursor.fetchone()

            if not ot:
                raise HTTPException(
                    status_code=status.HTTP_404_NOT_FOUND,
                    detail=f"OT {ot_id} no encontrada"
                )

            now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

            # Actualizar según rol (igual que Laravel)
            if role_id == 3:  # Jefe de Venta
                cursor.execute("""
                    UPDATE work_orders
                    SET aprobacion_jefe_venta = 3, updated_at = %s
                    WHERE id = %s
                """, (now, ot_id))
                rejection_type = "Jefe de Venta"

            elif role_id == 5:  # Jefe de Desarrollo
                cursor.execute("""
                    UPDATE work_orders
                    SET aprobacion_jefe_desarrollo = 3, updated_at = %s
                    WHERE id = %s
                """, (now, ot_id))
                rejection_type = "Jefe de Desarrollo"
            else:
                raise HTTPException(
                    status_code=status.HTTP_403_FORBIDDEN,
                    detail="No tiene permisos para rechazar OTs"
                )

            connection.commit()

            return ApprovalActionResponse(
                id=ot_id,
                message=f"OT {ot_id} rechazada por {rejection_type}",
                new_state="Rechazado"
            )

    except pymysql.Error as e:
        connection.rollback()
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Error al rechazar OT: {str(e)}"
        )
    finally:
        connection.close()


# =============================================
# ASIGNACIONES DE OTs
# =============================================

class AssignmentListItem(BaseModel):
    """Item de la lista de OTs pendientes de asignacion."""
    id: int
    created_at: str
    client_name: str
    vendedor_nombre: str
    tipo_solicitud: str
    canal: Optional[str]
    jerarquia_1: Optional[str]
    jerarquia_2: Optional[str]
    jerarquia_3: Optional[str]
    cad: Optional[str]
    profesional_asignado: Optional[str]
    dias_sin_asignar: int


class AssignmentListResponse(BaseModel):
    """Respuesta de lista de OTs pendientes de asignacion."""
    items: List[AssignmentListItem]
    total: int
    page: int
    page_size: int
    total_pages: int


class AssignProfessionalRequest(BaseModel):
    """Request para asignar profesional a OT."""
    profesional_id: int
    area_id: Optional[int] = None  # Area del asignador (opcional, se detecta automaticamente)
    observacion: Optional[str] = None  # Mensaje opcional para la asignacion
    generar_notificacion: bool = True  # Si se debe generar notificacion


class AssignmentActionResponse(BaseModel):
    """Respuesta de accion de asignacion."""
    id: int
    message: str
    profesional_nombre: str
    es_reasignacion: bool = False
    notificacion_creada: bool = False


async def _get_pending_assignment_impl(
    page: int,
    page_size: int,
    asignado: Optional[str],
    tipo_solicitud: Optional[str],
    canal_id: Optional[int],
    vendedor_id: Optional[int],
    estado_id: Optional[int],
    date_desde: Optional[str],
    date_hasta: Optional[str],
    user_id: int,
):
    """
    Implementación: Lista OTs pendientes de asignacion.
    Usado por jefes de area para asignar profesionales.
    Replica logica de Laravel: filtra por current_area_id y user_work_orders.
    """
    connection = get_mysql_connection()
    try:
        with connection.cursor() as cursor:
            # Obtener el area (work_space_id) del usuario actual
            cursor.execute("""
                SELECT r.work_space_id as area_id
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE u.id = %s
            """, [user_id])
            user_row = cursor.fetchone()
            if not user_row or not user_row.get("area_id"):
                raise HTTPException(
                    status_code=status.HTTP_400_BAD_REQUEST,
                    detail="Usuario no tiene area asignada"
                )
            user_area_id = user_row["area_id"]

            # Estados activos por defecto (igual que Laravel)
            estados_activos = [1, 2, 3, 4, 5, 6, 7, 10, 12, 14, 15, 16, 17, 18]

            # Query base - obtiene profesional asignado desde user_work_orders
            query = """
                SELECT
                    ot.id,
                    ot.created_at,
                    c.nombre as client_name,
                    CONCAT(u.nombre, ' ', u.apellido) as vendedor_nombre,
                    ot.tipo_solicitud,
                    cn.nombre as canal,
                    h1.descripcion as jerarquia_1,
                    h2.descripcion as jerarquia_2,
                    h3.descripcion as jerarquia_3,
                    ot.cad,
                    (
                        SELECT CONCAT(pu.nombre, ' ', pu.apellido)
                        FROM user_work_orders uwo
                        JOIN users pu ON uwo.user_id = pu.id
                        WHERE uwo.work_order_id = ot.id AND uwo.area_id = %s AND uwo.active = 1
                        LIMIT 1
                    ) as profesional_asignado,
                    DATEDIFF(NOW(), ot.created_at) as dias_sin_asignar
                FROM work_orders ot
                LEFT JOIN clients c ON ot.client_id = c.id
                LEFT JOIN users u ON ot.creador_id = u.id
                LEFT JOIN canals cn ON ot.canal_id = cn.id
                LEFT JOIN subsubhierarchies h3 ON ot.subsubhierarchy_id = h3.id
                LEFT JOIN subhierarchies h2 ON h3.subhierarchy_id = h2.id
                LEFT JOIN hierarchies h1 ON h2.hierarchy_id = h1.id
                WHERE ot.active = 1
            """
            params = [user_area_id]

            # Filtro de asignado basado en user_work_orders
            if asignado == "SI":
                # OTs que YA tienen asignacion en el area del jefe
                query += """
                    AND EXISTS (
                        SELECT 1 FROM user_work_orders uwo
                        WHERE uwo.work_order_id = ot.id
                        AND uwo.area_id = %s
                        AND uwo.active = 1
                    )
                """
                params.append(user_area_id)
            else:
                # Por defecto (NO o null): OTs SIN asignacion en el area Y que estan en el area
                # Caso especial: Catalogadores manejan areas 4 y 5
                if user_area_id in [4, 5]:
                    query += """
                        AND NOT EXISTS (
                            SELECT 1 FROM user_work_orders uwo
                            WHERE uwo.work_order_id = ot.id
                            AND uwo.area_id = %s
                            AND uwo.active = 1
                        )
                        AND ot.current_area_id IN (4, 5)
                    """
                    params.append(user_area_id)  # Para NOT EXISTS
                else:
                    query += """
                        AND NOT EXISTS (
                            SELECT 1 FROM user_work_orders uwo
                            WHERE uwo.work_order_id = ot.id
                            AND uwo.area_id = %s
                            AND uwo.active = 1
                        )
                        AND ot.current_area_id = %s
                    """
                    params.append(user_area_id)  # Para NOT EXISTS
                    params.append(user_area_id)  # Para current_area_id

            if tipo_solicitud:
                query += " AND ot.tipo_solicitud = %s"
                params.append(tipo_solicitud)

            if canal_id:
                query += " AND ot.canal_id = %s"
                params.append(canal_id)

            if vendedor_id:
                query += " AND ot.creador_id = %s"
                params.append(vendedor_id)

            # Filtro por estado usando managements (igual que Laravel)
            if estado_id:
                query += """
                    AND EXISTS (
                        SELECT 1 FROM managements m
                        WHERE m.work_order_id = ot.id
                        AND m.management_type_id = 1
                        AND m.state_id = %s
                        AND m.id = (
                            SELECT MAX(m2.id) FROM managements m2
                            WHERE m2.work_order_id = ot.id AND m2.management_type_id = 1
                        )
                    )
                """
                params.append(estado_id)
            else:
                # Por defecto filtra por estados activos
                estados_placeholder = ','.join(['%s'] * len(estados_activos))
                query += f"""
                    AND EXISTS (
                        SELECT 1 FROM managements m
                        WHERE m.work_order_id = ot.id
                        AND m.management_type_id = 1
                        AND m.state_id IN ({estados_placeholder})
                        AND m.id = (
                            SELECT MAX(m2.id) FROM managements m2
                            WHERE m2.work_order_id = ot.id AND m2.management_type_id = 1
                        )
                    )
                """
                params.extend(estados_activos)

            if date_desde:
                query += " AND ot.created_at >= %s"
                params.append(date_desde)

            if date_hasta:
                query += " AND ot.created_at <= %s"
                params.append(date_hasta + " 23:59:59")

            # Contar total
            count_query = f"SELECT COUNT(*) as total FROM ({query}) as subq"
            cursor.execute(count_query, params)
            total = cursor.fetchone()["total"]

            # Paginacion - ordenar por más antigua primero (ASC) como en Laravel
            query += " ORDER BY ot.created_at ASC LIMIT %s OFFSET %s"
            offset = (page - 1) * page_size
            params.extend([page_size, offset])

            cursor.execute(query, params)
            rows = cursor.fetchall()

            # Mapeo de tipo_solicitud
            tipo_map = {
                1: "Desarrollo Nuevo",
                2: "Repeticion",
                3: "Modificacion",
                4: "Cotizacion",
            }

            items = []
            for row in rows:
                items.append(AssignmentListItem(
                    id=row["id"],
                    created_at=row["created_at"].strftime("%Y-%m-%d %H:%M:%S") if row["created_at"] else "",
                    client_name=row["client_name"] or "",
                    vendedor_nombre=row["vendedor_nombre"] or "",
                    tipo_solicitud=tipo_map.get(row["tipo_solicitud"], "Desconocido") if row["tipo_solicitud"] else "-",
                    canal=row["canal"],
                    jerarquia_1=row["jerarquia_1"],
                    jerarquia_2=row["jerarquia_2"],
                    jerarquia_3=row["jerarquia_3"],
                    cad=row["cad"],
                    profesional_asignado=row["profesional_asignado"],
                    dias_sin_asignar=row["dias_sin_asignar"] or 0
                ))

            total_pages = (total + page_size - 1) // page_size

            return AssignmentListResponse(
                items=items,
                total=total,
                page=page,
                page_size=page_size,
                total_pages=total_pages
            )

    except pymysql.Error as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Error al obtener OTs pendientes de asignacion: {str(e)}"
        )
    finally:
        connection.close()


@router.put("/{ot_id}/assign", response_model=AssignmentActionResponse)
async def assign_professional(
    ot_id: int,
    data: AssignProfessionalRequest,
    user_id: int = Depends(get_current_user_id)
):
    """
    Asigna un profesional a una OT.
    - Registra en user_work_orders la asignacion
    - Genera notificacion si corresponde
    - Soporta observacion/mensaje
    """
    connection = get_mysql_connection()
    try:
        with connection.cursor() as cursor:
            # Verificar que la OT existe
            cursor.execute(
                "SELECT id, ultimo_cambio_area FROM work_orders WHERE id = %s AND active = 1",
                (ot_id,)
            )
            ot = cursor.fetchone()
            if not ot:
                raise HTTPException(
                    status_code=status.HTTP_404_NOT_FOUND,
                    detail=f"OT {ot_id} no encontrada"
                )

            # Verificar que el profesional existe
            cursor.execute(
                "SELECT id, nombre, apellido FROM users WHERE id = %s",
                (data.profesional_id,)
            )
            profesional = cursor.fetchone()
            if not profesional:
                raise HTTPException(
                    status_code=status.HTTP_404_NOT_FOUND,
                    detail=f"Profesional {data.profesional_id} no encontrado"
                )

            # Obtener area del usuario que asigna (work_space_id del rol)
            area_id = data.area_id
            if not area_id:
                cursor.execute(
                    "SELECT r.work_space_id as area_id FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = %s",
                    (user_id,)
                )
                area_row = cursor.fetchone()
                if area_row and area_row.get("area_id"):
                    area_id = area_row["area_id"]
                else:
                    area_id = 1  # Default

            # Verificar si ya existe asignacion para esta OT y area
            cursor.execute(
                "SELECT id, user_id FROM user_work_orders WHERE work_order_id = %s AND area_id = %s",
                (ot_id, area_id)
            )
            existing = cursor.fetchone()

            es_reasignacion = False
            motivo = "Asignado"

            if existing:
                # Reasignacion - actualizar registro existente
                cursor.execute(
                    """UPDATE user_work_orders
                       SET user_id = %s, updated_at = NOW()
                       WHERE id = %s""",
                    (data.profesional_id, existing["id"])
                )
                es_reasignacion = True
                motivo = "Reasignado"
            else:
                # Nueva asignacion - calcular tiempo inicial
                tiempo_inicial = 0
                if ot.get("ultimo_cambio_area"):
                    cursor.execute(
                        "SELECT TIMESTAMPDIFF(SECOND, %s, NOW()) as diff",
                        (ot["ultimo_cambio_area"],)
                    )
                    diff_row = cursor.fetchone()
                    if diff_row:
                        tiempo_inicial = diff_row["diff"] or 0

                cursor.execute(
                    """INSERT INTO user_work_orders (work_order_id, user_id, area_id, tiempo_inicial, created_at, updated_at)
                       VALUES (%s, %s, %s, %s, NOW(), NOW())""",
                    (ot_id, data.profesional_id, area_id, tiempo_inicial)
                )

            # Generar notificacion si corresponde
            notificacion_creada = False
            if data.generar_notificacion and user_id != data.profesional_id:
                cursor.execute(
                    """INSERT INTO notifications (work_order_id, user_id, generador_id, motivo, observacion, active, created_at, updated_at)
                       VALUES (%s, %s, %s, %s, %s, 1, NOW(), NOW())""",
                    (ot_id, data.profesional_id, user_id, motivo, data.observacion or "")
                )
                notificacion_creada = True

            connection.commit()

            profesional_nombre = f"{profesional['nombre']} {profesional['apellido']}"

            return AssignmentActionResponse(
                id=ot_id,
                message=f"{'Reasignado' if es_reasignacion else 'Asignado'} a {profesional_nombre}",
                profesional_nombre=profesional_nombre,
                es_reasignacion=es_reasignacion,
                notificacion_creada=notificacion_creada
            )

    except pymysql.Error as e:
        connection.rollback()
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Error al asignar profesional: {str(e)}"
        )
    finally:
        connection.close()


async def _get_professionals_impl(user_id: int):
    """
    Implementacion: Lista profesionales disponibles para asignacion.
    Replica logica de Laravel: subordinados (role_id + 1) + jefe actual.
    Ejemplo: Jefe Diseño (7) ve Diseñadores (8) + él mismo.
    """
    connection = get_mysql_connection()
    try:
        with connection.cursor() as cursor:
            # Obtener el role_id del usuario actual
            cursor.execute(
                "SELECT role_id FROM users WHERE id = %s",
                [user_id]
            )
            user_row = cursor.fetchone()
            if not user_row:
                raise HTTPException(
                    status_code=status.HTTP_400_BAD_REQUEST,
                    detail="Usuario no encontrado"
                )
            current_role_id = user_row["role_id"]
            subordinate_role_id = current_role_id + 1

            # Obtener profesionales: subordinados (role_id + 1) + el jefe actual
            cursor.execute("""
                SELECT u.id, u.nombre, u.apellido, r.nombre as rol
                FROM users u
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE u.active = 1
                AND (u.role_id = %s OR u.id = %s)
                ORDER BY u.nombre, u.apellido
            """, [subordinate_role_id, user_id])
            rows = cursor.fetchall()

            return [
                {
                    "id": row["id"],
                    "nombre": f"{row['nombre']} {row['apellido']}",
                    "rol": row["rol"]
                }
                for row in rows
            ]

    except pymysql.Error as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Error al obtener profesionales: {str(e)}"
        )
    finally:
        connection.close()


# =============================================
# OPCIONES DEL FORMULARIO OT (COMPLETO)
# =============================================

class FormOptionsComplete(BaseModel):
    """Todas las opciones necesarias para el formulario de crear/editar OT."""
    # Catálogos principales
    clients: List[dict]
    canals: List[dict]
    vendedores: List[dict]
    org_ventas: List[dict]
    plantas: List[dict]
    # Catálogos de producto
    product_types: List[dict]
    cads: List[dict]
    cartons: List[dict]
    styles: List[dict]
    colors: List[dict]
    envases: List[dict]
    # Catálogos de procesos
    processes: List[dict]
    armados: List[dict]
    impresiones: List[dict]
    fsc: List[dict]
    # Catálogos de materiales
    materials: List[dict]
    recubrimientos: List[dict]
    coverages_internal: List[dict]
    coverages_external: List[dict]
    # Catálogos de referencia
    reference_types: List[dict]
    design_types: List[dict]
    bloqueo_referencia: List[dict]  # Hardcoded como en Laravel
    indicador_facturacion: List[dict]  # Hardcoded como en Laravel
    # Catálogos de calidad
    trazabilidad: List[dict]
    tipo_cinta: List[dict]
    pallet_types: List[dict]
    salas_corte: List[dict]
    # Jerarquías
    hierarchies: List[dict]
    subhierarchies: List[dict]
    subsubhierarchies: List[dict]
    # Otros
    tipos_solicitud: List[dict]
    maquila_servicios: List[dict]
    comunas: List[dict]
    pais_referencia: List[dict]
    # Configuración
    secuencia_operacional: List[dict]
    # Sección 13 - Datos para Desarrollo
    food_types: List[dict]
    expected_uses: List[dict]
    recycled_uses: List[dict]
    class_substance_packeds: List[dict]
    transportation_ways: List[dict]
    target_markets: List[dict]


async def _get_form_options_complete_impl() -> FormOptionsComplete:
    """
    Implementación de form-options-complete.
    La ruta está definida más arriba para evitar conflicto con /{ot_id}.
    """
    connection = get_mysql_connection()
    try:
        with connection.cursor() as cursor:
            options = {}

            # ========== CATÁLOGOS PRINCIPALES ==========

            # Clientes activos - ordenados por codigo
            cursor.execute("""
                SELECT id, nombre, codigo, rut
                FROM clients
                WHERE active = 1
                ORDER BY codigo ASC
                LIMIT 2000
            """)
            options['clients'] = cursor.fetchall()

            # Canales
            cursor.execute("SELECT id, nombre FROM canals WHERE active = 1 ORDER BY nombre")
            options['canals'] = cursor.fetchall()

            # Vendedores (usuarios con roles de venta)
            cursor.execute("""
                SELECT id, CONCAT(nombre, ' ', apellido) as nombre
                FROM users
                WHERE active = 1 AND role_id IN (3, 4, 19)
                ORDER BY nombre
            """)
            options['vendedores'] = cursor.fetchall()

            # Organizaciones de venta (tabla no existe - hardcoded)
            options['org_ventas'] = [
                {"id": 1, "nombre": "2020 OV NACIONAL PLANTA PLACILLA"},
                {"id": 2, "nombre": "2000 OV EXPORTACIONES"}
            ]

            # Plantas (no tiene columna codigo)
            cursor.execute("SELECT id, nombre FROM plantas ORDER BY nombre")
            options['plantas'] = cursor.fetchall()

            # ========== CATÁLOGOS DE PRODUCTO ==========

            # Tipos de producto
            cursor.execute("""
                SELECT id, descripcion as nombre, codigo
                FROM product_types
                WHERE active = 1
                ORDER BY descripcion
            """)
            options['product_types'] = cursor.fetchall()

            # CADs (no tiene columna descripcion)
            cursor.execute("""
                SELECT id, cad as codigo
                FROM cads
                WHERE active = 1
                ORDER BY cad
            """)
            options['cads'] = cursor.fetchall()

            # Cartones (no tiene columna descripcion, usar codigo y onda)
            cursor.execute("""
                SELECT id, codigo, onda
                FROM cartons
                WHERE active = 1
                ORDER BY codigo
            """)
            options['cartons'] = cursor.fetchall()

            # Estilos
            cursor.execute("""
                SELECT id, glosa as nombre, codigo
                FROM styles
                WHERE active = 1
                ORDER BY glosa
            """)
            options['styles'] = cursor.fetchall()

            # Colores (usa descripcion, no nombre)
            cursor.execute("""
                SELECT id, descripcion as nombre, codigo
                FROM colors
                WHERE active = 1
                ORDER BY descripcion
            """)
            options['colors'] = cursor.fetchall()

            # Envases
            cursor.execute("""
                SELECT id, descripcion as nombre
                FROM envases
                WHERE active = 1
                ORDER BY descripcion
            """)
            options['envases'] = cursor.fetchall()

            # ========== CATÁLOGOS DE PROCESOS ==========

            # Procesos (no tiene columna codigo)
            cursor.execute("""
                SELECT id, descripcion as nombre
                FROM processes
                WHERE active = 1
                ORDER BY descripcion
            """)
            options['processes'] = cursor.fetchall()

            # Armados
            cursor.execute("""
                SELECT id, descripcion as nombre
                FROM armados
                WHERE active = 1
                ORDER BY descripcion
            """)
            options['armados'] = cursor.fetchall()

            # Impresiones (tabla impresion - usa status)
            cursor.execute("""
                SELECT id, descripcion as nombre
                FROM impresion
                WHERE status = 1
                ORDER BY id
            """)
            options['impresiones'] = cursor.fetchall()

            # FSC
            cursor.execute("""
                SELECT codigo as id, descripcion as nombre
                FROM fsc
                WHERE active = 1
                ORDER BY descripcion
            """)
            options['fsc'] = cursor.fetchall()

            # ========== CATÁLOGOS DE MATERIALES ==========

            # Materiales (solo últimos 500 por performance)
            cursor.execute("""
                SELECT id, codigo, descripcion
                FROM materials
                WHERE active = 1
                ORDER BY codigo DESC
                LIMIT 500
            """)
            options['materials'] = cursor.fetchall()

            # Recubrimientos
            cursor.execute("""
                SELECT id, descripcion as nombre
                FROM recubrimiento_types
                WHERE active = 1
                ORDER BY descripcion
            """)
            options['recubrimientos'] = cursor.fetchall()

            # Coberturas internas (usa status)
            cursor.execute("""
                SELECT id, descripcion as nombre
                FROM coverage_internal
                WHERE status = 1
                ORDER BY descripcion
            """)
            options['coverages_internal'] = cursor.fetchall()

            # Coberturas externas (usa status)
            cursor.execute("""
                SELECT id, descripcion as nombre
                FROM coverage_external
                WHERE status = 1
                ORDER BY descripcion
            """)
            options['coverages_external'] = cursor.fetchall()

            # ========== CATÁLOGOS DE REFERENCIA ==========

            # Tipos de referencia (usa codigo como value, como Laravel)
            cursor.execute("""
                SELECT codigo as id, descripcion as nombre
                FROM reference_types
                WHERE active = 1
                ORDER BY codigo
            """)
            options['reference_types'] = cursor.fetchall()

            # Tipos de diseño
            cursor.execute("""
                SELECT id, descripcion as nombre
                FROM design_types
                WHERE active = 1
                ORDER BY descripcion
            """)
            options['design_types'] = cursor.fetchall()

            # Bloqueo Referencia (hardcoded como en Laravel)
            options['bloqueo_referencia'] = [
                {"id": 1, "nombre": "Si"},
                {"id": 0, "nombre": "No"}
            ]

            # Indicador Facturación D.E. (hardcoded como en Laravel)
            options['indicador_facturacion'] = [
                {"id": 1, "nombre": "RRP"},
                {"id": 2, "nombre": "E-Commerce"},
                {"id": 3, "nombre": "Esquineros"},
                {"id": 4, "nombre": "Geometría"},
                {"id": 5, "nombre": "Participación nuevo Mercado"},
                {"id": 7, "nombre": "Innovación"},
                {"id": 8, "nombre": "Sustentabilidad"},
                {"id": 9, "nombre": "Automatización"},
                {"id": 10, "nombre": "No Aplica"},
                {"id": 11, "nombre": "Ahorro"}
            ]

            # ========== CATÁLOGOS DE CALIDAD ==========

            # Trazabilidad (usa status)
            cursor.execute("""
                SELECT id, descripcion as nombre
                FROM trazabilidad
                WHERE status = 1
                ORDER BY descripcion
            """)
            options['trazabilidad'] = cursor.fetchall()

            # Tipos de cinta (tabla no existe)
            options['tipo_cinta'] = []

            # Tipos de pallet
            cursor.execute("""
                SELECT id, descripcion as nombre
                FROM pallet_types
                WHERE active = 1
                ORDER BY descripcion
            """)
            options['pallet_types'] = cursor.fetchall()

            # Salas de corte (tabla no existe)
            options['salas_corte'] = []

            # ========== JERARQUÍAS ==========

            # Jerarquías nivel 1
            cursor.execute("""
                SELECT id, descripcion as nombre
                FROM hierarchies
                WHERE active = 1
                ORDER BY descripcion
            """)
            options['hierarchies'] = cursor.fetchall()

            # Subjerarquías nivel 2
            cursor.execute("""
                SELECT id, descripcion as nombre, hierarchy_id
                FROM subhierarchies
                WHERE active = 1
                ORDER BY descripcion
            """)
            options['subhierarchies'] = cursor.fetchall()

            # Subsubjerarquías nivel 3
            cursor.execute("""
                SELECT id, descripcion as nombre, subhierarchy_id
                FROM subsubhierarchies
                WHERE active = 1
                ORDER BY descripcion
            """)
            options['subsubhierarchies'] = cursor.fetchall()

            # ========== OTROS CATÁLOGOS ==========

            # Tipos de solicitud (hardcoded como en Laravel)
            options['tipos_solicitud'] = [
                {"id": 1, "nombre": "Desarrollo Completo"},
                {"id": 4, "nombre": "Cotiza sin CAD"},
                {"id": 2, "nombre": "Cotiza con CAD"},
                {"id": 3, "nombre": "Muestra con CAD"},
                {"id": 7, "nombre": "OT Proyectos Innovación"},
                {"id": 5, "nombre": "Arte con Material"},
                {"id": 6, "nombre": "Otras Solicitudes Desarrollo"},
            ]

            # Maquila servicios
            cursor.execute("""
                SELECT id, servicio as nombre
                FROM maquila_servicios
                WHERE active = 1
                ORDER BY servicio
            """)
            options['maquila_servicios'] = cursor.fetchall()

            # Comunas (tabla no existe en esta BD, retornar lista vacía)
            options['comunas'] = []

            # País referencia
            cursor.execute("""
                SELECT id, name as nombre
                FROM paises
                WHERE active = 1
                ORDER BY name
            """)
            options['pais_referencia'] = cursor.fetchall()

            # Secuencia operacional
            cursor.execute("""
                SELECT id, codigo, descripcion, nombre_corto, planta_id
                FROM secuencias_operacionales
                WHERE active = 1 AND deleted = 0
                ORDER BY codigo
            """)
            options['secuencia_operacional'] = cursor.fetchall()

            # ========== SECCIÓN 13 - DATOS PARA DESARROLLO ==========

            # Tipos de alimento
            cursor.execute("""
                SELECT id, descripcion as nombre
                FROM food_types
                WHERE deleted = 0
                ORDER BY id
            """)
            options['food_types'] = cursor.fetchall()

            # Uso previsto
            cursor.execute("""
                SELECT id, descripcion as nombre
                FROM expected_uses
                ORDER BY id
            """)
            options['expected_uses'] = cursor.fetchall()

            # Uso reciclado
            cursor.execute("""
                SELECT id, descripcion as nombre
                FROM recycled_uses
                ORDER BY id
            """)
            options['recycled_uses'] = cursor.fetchall()

            # Clase sustancia a embalar
            cursor.execute("""
                SELECT id, descripcion as nombre
                FROM class_substance_packeds
                WHERE deleted = 0
                ORDER BY id
            """)
            options['class_substance_packeds'] = cursor.fetchall()

            # Medio de transporte
            cursor.execute("""
                SELECT id, descripcion as nombre
                FROM transportation_ways
                WHERE deleted = 0
                ORDER BY id
            """)
            options['transportation_ways'] = cursor.fetchall()

            # Mercado destino
            cursor.execute("""
                SELECT id, descripcion as nombre
                FROM target_markets
                WHERE deleted = 0
                ORDER BY id
            """)
            options['target_markets'] = cursor.fetchall()

            return FormOptionsComplete(**options)

    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Error al obtener opciones del formulario: {str(e)}"
        )
    finally:
        connection.close()


# =============================================
# DUPLICAR OT
# =============================================

class DuplicateOTResponse(BaseModel):
    """Respuesta al duplicar OT."""
    id: int
    original_id: int
    message: str


@router.post("/{ot_id}/duplicate", response_model=DuplicateOTResponse)
async def duplicate_work_order(
    ot_id: int,
    user_id: int = Depends(get_current_user_id)
):
    """
    Duplica una OT existente con toda su información.
    La nueva OT inicia en estado inicial y área de Ventas.
    """
    connection = get_mysql_connection()
    try:
        with connection.cursor() as cursor:
            # Obtener OT original
            cursor.execute("""
                SELECT * FROM work_orders WHERE id = %s AND active = 1
            """, (ot_id,))
            original = cursor.fetchone()

            if not original:
                raise HTTPException(
                    status_code=status.HTTP_404_NOT_FOUND,
                    detail=f"OT {ot_id} no encontrada"
                )

            now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

            # Campos a excluir de la copia
            exclude_fields = {'id', 'created_at', 'updated_at', 'aprobado', 'material_id'}

            # Construir campos para la nueva OT
            new_fields = {}
            for key, value in original.items():
                if key not in exclude_fields:
                    new_fields[key] = value

            # Sobrescribir algunos valores
            new_fields['creador_id'] = user_id
            new_fields['current_area_id'] = 1  # Inicia en Ventas
            new_fields['active'] = 1
            new_fields['created_at'] = now
            new_fields['updated_at'] = now
            new_fields['aprobado'] = 0  # No aprobada

            # Agregar referencia a OT original en descripción
            new_fields['descripcion'] = f"[Copia OT-{ot_id}] {original.get('descripcion', '')}"[:40]

            # Construir query de inserción
            columns = ', '.join(new_fields.keys())
            placeholders = ', '.join(['%s'] * len(new_fields))
            values = list(new_fields.values())

            sql = f"INSERT INTO work_orders ({columns}) VALUES ({placeholders})"
            cursor.execute(sql, values)
            new_ot_id = cursor.lastrowid

            # Crear registro inicial en managements
            cursor.execute("""
                INSERT INTO managements
                (work_order_id, work_space_id, state_id, user_id, management_type_id, observacion, created_at, updated_at)
                VALUES (%s, 1, 1, %s, 1, %s, %s, %s)
            """, (new_ot_id, user_id, f"Duplicado de OT-{ot_id}", now, now))

            connection.commit()

            return DuplicateOTResponse(
                id=new_ot_id,
                original_id=ot_id,
                message=f"OT {ot_id} duplicada exitosamente como OT {new_ot_id}"
            )

    except pymysql.Error as e:
        connection.rollback()
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Error al duplicar OT: {str(e)}"
        )
    finally:
        connection.close()

