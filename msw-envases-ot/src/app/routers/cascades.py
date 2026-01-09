"""
Router de Cascadas AJAX - INVEB Cascade Service
Endpoints para carga dinámica de selectores dependientes.
FASE 6.25
"""
from typing import Optional, List
from fastapi import APIRouter, HTTPException, Query, Depends
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from pydantic import BaseModel
import pymysql
import jwt
import os

from app.config import get_settings

router = APIRouter(prefix="/cascades", tags=["Cascadas AJAX"])
security = HTTPBearer(auto_error=False)
settings = get_settings()


def get_current_user(credentials: HTTPAuthorizationCredentials = Depends(security)) -> dict:
    """Extrae info del usuario del token JWT."""
    if not credentials:
        raise HTTPException(status_code=401, detail="Token no proporcionado")
    try:
        payload = jwt.decode(
            credentials.credentials,
            settings.JWT_SECRET_KEY,
            algorithms=[settings.JWT_ALGORITHM]
        )
        return {"id": int(payload.get("sub")), "role_id": payload.get("role_id", 0)}
    except jwt.ExpiredSignatureError:
        raise HTTPException(status_code=401, detail="Token expirado")
    except jwt.InvalidTokenError:
        raise HTTPException(status_code=401, detail="Token invalido")


def get_db_connection():
    """Conexion a MySQL."""
    return pymysql.connect(
        host=os.getenv("MYSQL_HOST", "host.docker.internal"),
        port=int(os.getenv("MYSQL_PORT", "3307")),
        user=os.getenv("MYSQL_USER", "root"),
        password=os.getenv("MYSQL_PASSWORD", "root"),
        database=os.getenv("MYSQL_DATABASE", "envases_ot"),
        cursorclass=pymysql.cursors.DictCursor,
        charset='utf8mb4'
    )


# =============================================
# SCHEMAS
# =============================================

class SelectOption(BaseModel):
    id: int
    nombre: str


class InstalacionOption(BaseModel):
    id: int
    nombre: str
    direccion: Optional[str] = None


class ContactoOption(BaseModel):
    id: int
    nombre: str
    email: Optional[str] = None
    telefono: Optional[str] = None


class InstalacionInfo(BaseModel):
    contactos: List[ContactoOption]
    tipo_pallet_id: Optional[int] = None
    altura_pallet: Optional[float] = None
    sobresalir_carga: Optional[int] = None
    bulto_zunchado: Optional[int] = None
    formato_etiqueta: Optional[int] = None  # ID de formato etiqueta
    etiquetas_pallet: Optional[int] = None
    termocontraible: Optional[int] = None
    fsc: Optional[int] = None
    pais_mercado_destino: Optional[int] = None  # ID de país mercado destino
    certificado_calidad: Optional[int] = None


class ContactoInfo(BaseModel):
    nombre_contacto: str
    email_contacto: Optional[str] = None
    telefono_contacto: Optional[str] = None
    comuna_contacto: Optional[str] = None
    direccion_contacto: Optional[str] = None


class ClienteCotizaResponse(BaseModel):
    instalaciones: List[InstalacionOption]
    clasificacion_id: Optional[int] = None
    clasificacion_nombre: Optional[str] = None


# =============================================
# ENDPOINTS - CLIENTE → INSTALACIONES
# =============================================

@router.get("/clientes/{client_id}/instalaciones", response_model=List[InstalacionOption])
async def get_instalaciones_cliente(
    client_id: int,
    current_user: dict = Depends(get_current_user)
):
    """
    Obtiene instalaciones de un cliente.
    Equivalente a: /getInstalacionesCliente
    """
    conn = get_db_connection()
    try:
        with conn.cursor() as cursor:
            # Issue 4: La tabla usa 'deleted' (0=activo) en lugar de 'active'
            cursor.execute("""
                SELECT id, COALESCE(nombre, CONCAT('Instalación #', id)) as nombre, direccion_contacto as direccion
                FROM installations
                WHERE client_id = %s AND (deleted = 0 OR deleted IS NULL)
                ORDER BY nombre
            """, (client_id,))
            rows = cursor.fetchall()
            return [
                InstalacionOption(
                    id=row["id"],
                    nombre=row["nombre"],
                    direccion=row.get("direccion")
                )
                for row in rows
            ]
    finally:
        conn.close()


@router.get("/clientes/{client_id}/instalaciones-cotiza", response_model=ClienteCotizaResponse)
async def get_instalaciones_cliente_cotiza(
    client_id: int,
    current_user: dict = Depends(get_current_user)
):
    """
    Obtiene instalaciones y clasificación de un cliente para cotizaciones.
    Equivalente a: /getInstalacionesClienteCotiza
    """
    conn = get_db_connection()
    try:
        with conn.cursor() as cursor:
            # Obtener cliente con clasificación
            cursor.execute("""
                SELECT c.clasificacion as clasificacion_id, cc.name as clasificacion_nombre
                FROM clients c
                LEFT JOIN clasificacion_clientes cc ON c.clasificacion = cc.id
                WHERE c.id = %s
            """, (client_id,))
            client = cursor.fetchone()

            # Obtener instalaciones - Issue 4: usa 'deleted' en lugar de 'active'
            cursor.execute("""
                SELECT id, COALESCE(nombre, CONCAT('Instalación #', id)) as nombre, direccion_contacto as direccion
                FROM installations
                WHERE client_id = %s AND (deleted = 0 OR deleted IS NULL)
                ORDER BY nombre
            """, (client_id,))
            instalaciones = cursor.fetchall()

            return ClienteCotizaResponse(
                instalaciones=[
                    InstalacionOption(
                        id=row["id"],
                        nombre=row["nombre"],
                        direccion=row.get("direccion")
                    )
                    for row in instalaciones
                ],
                clasificacion_id=client["clasificacion_id"] if client else None,
                clasificacion_nombre=client["clasificacion_nombre"] if client else None
            )
    finally:
        conn.close()


# =============================================
# ENDPOINTS - CLIENTE/INSTALACIÓN → CONTACTOS
# =============================================

@router.get("/clientes/{client_id}/contactos", response_model=List[ContactoOption])
async def get_contactos_cliente(
    client_id: int,
    instalacion_id: Optional[int] = Query(None, description="Filtrar por instalación"),
    current_user: dict = Depends(get_current_user)
):
    """
    Obtiene contactos de un cliente, opcionalmente filtrados por instalación.
    Equivalente a: /getContactosCliente

    Si se proporciona instalacion_id, obtiene los contactos embebidos en la instalación.
    Si no, obtiene los contactos de client_contacts.
    """
    conn = get_db_connection()
    try:
        with conn.cursor() as cursor:
            contactos = []

            if instalacion_id:
                # Obtener contactos embebidos en la instalación
                cursor.execute("""
                    SELECT
                        nombre_contacto, email_contacto, phone_contacto,
                        nombre_contacto_2, email_contacto_2, phone_contacto_2,
                        nombre_contacto_3, email_contacto_3, phone_contacto_3,
                        nombre_contacto_4, email_contacto_4, phone_contacto_4,
                        nombre_contacto_5, email_contacto_5, phone_contacto_5
                    FROM installations
                    WHERE id = %s AND client_id = %s
                """, (instalacion_id, client_id))
                row = cursor.fetchone()

                if row:
                    # Construir lista de contactos desde campos embebidos
                    for i in range(1, 6):
                        suffix = "" if i == 1 else f"_{i}"
                        nombre = row.get(f"nombre_contacto{suffix}")
                        if nombre:
                            contactos.append(ContactoOption(
                                id=i,  # ID sintético basado en posición
                                nombre=nombre,
                                email=row.get(f"email_contacto{suffix}"),
                                telefono=row.get(f"phone_contacto{suffix}")
                            ))
            else:
                # Sin instalación, obtener de client_contacts
                cursor.execute("""
                    SELECT id, nombre, email, telefono
                    FROM client_contacts
                    WHERE client_id = %s
                    ORDER BY nombre
                """, (client_id,))
                rows = cursor.fetchall()
                contactos = [
                    ContactoOption(
                        id=row["id"],
                        nombre=row["nombre"],
                        email=row.get("email"),
                        telefono=row.get("telefono")
                    )
                    for row in rows
                ]

            return contactos
    finally:
        conn.close()


# =============================================
# ENDPOINTS - INSTALACIÓN → INFO COMPLETA
# =============================================

@router.get("/instalaciones/{instalacion_id}", response_model=InstalacionInfo)
async def get_informacion_instalacion(
    instalacion_id: int,
    current_user: dict = Depends(get_current_user)
):
    """
    Obtiene información completa de una instalación incluyendo contactos.
    Equivalente a: /getInformacionInstalacion
    """
    conn = get_db_connection()
    try:
        with conn.cursor() as cursor:
            # Datos de la instalación
            cursor.execute("""
                SELECT
                    tipo_pallet as tipo_pallet_id,
                    altura_pallet,
                    sobresalir_carga,
                    bulto_zunchado,
                    formato_etiqueta,
                    etiquetas_pallet,
                    termocontraible,
                    fsc,
                    pais_mercado_destino,
                    certificado_calidad
                FROM installations
                WHERE id = %s
            """, (instalacion_id,))
            instalacion = cursor.fetchone()

            if not instalacion:
                raise HTTPException(status_code=404, detail="Instalación no encontrada")

            # Los contactos están embebidos en la tabla installations
            # Obtener datos de contacto de la instalación
            cursor.execute("""
                SELECT
                    nombre_contacto, email_contacto, phone_contacto,
                    nombre_contacto_2, email_contacto_2, phone_contacto_2,
                    nombre_contacto_3, email_contacto_3, phone_contacto_3,
                    nombre_contacto_4, email_contacto_4, phone_contacto_4,
                    nombre_contacto_5, email_contacto_5, phone_contacto_5
                FROM installations
                WHERE id = %s
            """, (instalacion_id,))
            contacto_data = cursor.fetchone()

            # Construir lista de contactos desde campos embebidos
            contactos = []
            if contacto_data:
                for i in range(1, 6):
                    suffix = "" if i == 1 else f"_{i}"
                    nombre = contacto_data.get(f"nombre_contacto{suffix}")
                    if nombre:
                        contactos.append(ContactoOption(
                            id=i,  # ID sintético
                            nombre=nombre,
                            email=contacto_data.get(f"email_contacto{suffix}"),
                            telefono=contacto_data.get(f"phone_contacto{suffix}")
                        ))

            return InstalacionInfo(
                contactos=contactos,
                tipo_pallet_id=instalacion.get("tipo_pallet_id"),
                altura_pallet=instalacion.get("altura_pallet"),
                sobresalir_carga=instalacion.get("sobresalir_carga"),
                bulto_zunchado=instalacion.get("bulto_zunchado"),
                formato_etiqueta=instalacion.get("formato_etiqueta"),
                etiquetas_pallet=instalacion.get("etiquetas_pallet"),
                termocontraible=instalacion.get("termocontraible"),
                fsc=instalacion.get("fsc"),
                pais_mercado_destino=instalacion.get("pais_mercado_destino"),
                certificado_calidad=instalacion.get("certificado_calidad")
            )
    finally:
        conn.close()


# =============================================
# ENDPOINTS - CONTACTO → DATOS
# =============================================

@router.get("/contactos/{contacto_id}", response_model=ContactoInfo)
async def get_datos_contacto(
    contacto_id: int,
    current_user: dict = Depends(get_current_user)
):
    """
    Obtiene datos completos de un contacto.
    Equivalente a: /getDatosContacto
    """
    conn = get_db_connection()
    try:
        with conn.cursor() as cursor:
            # Usar client_contacts en lugar de contact_instalations
            cursor.execute("""
                SELECT
                    nombre as nombre_contacto,
                    email as email_contacto,
                    telefono as telefono_contacto
                FROM client_contacts
                WHERE id = %s
            """, (contacto_id,))
            contacto = cursor.fetchone()

            if not contacto:
                raise HTTPException(status_code=404, detail="Contacto no encontrado")

            return ContactoInfo(
                nombre_contacto=contacto["nombre_contacto"] or "",
                email_contacto=contacto.get("email_contacto"),
                telefono_contacto=contacto.get("telefono_contacto"),
                comuna_contacto=None,  # No disponible en client_contacts
                direccion_contacto=None  # No disponible en client_contacts
            )
    finally:
        conn.close()


# =============================================
# ENDPOINTS - TIPO PRODUCTO → SERVICIOS MAQUILA
# =============================================

@router.get("/productos/{tipo_producto_id}/servicios-maquila", response_model=List[SelectOption])
async def get_servicios_maquila(
    tipo_producto_id: int,
    current_user: dict = Depends(get_current_user)
):
    """
    Obtiene servicios de maquila disponibles según el tipo de producto.
    Equivalente a: /getServiciosMaquila
    Mapeo basado en DetalleCotizacionController.php
    """
    # Mapeo de tipo producto a servicios de maquila (según Laravel)
    mapeos = {
        # Plancha (16) → Paletizado Placas (18)
        16: [18],
        # Caja Regular RSC (3), Fondo (4), Tapa (5), Caja Especial (15) → PM CJ Chica/Mediana/Grande
        3: [15, 16, 17],
        4: [15, 16, 17],
        5: [15, 16, 17],
        15: [15, 16, 17],
        # Caja Bipartida (31) → Pegado Especial (21)
        31: [21],
        # Tabiques (18, 33, 20, 19) → Armado y Paletizado (19, 20)
        18: [19, 20],
        33: [19, 20],
        20: [19, 20],
        19: [19, 20],
        # Wrap Around (17, 32, 35) → Wrap Around (22)
        17: [22],
        32: [22],
        35: [22],
        # PAD (6) → Paletizado Placas (18)
        6: [18],
        # Esquinero (7), Zuncho (8), Bobina (10), Display (11) → Solo Paletizado (23)
        7: [23],
        8: [23],
        10: [23],
        11: [23],
    }

    servicio_ids = mapeos.get(tipo_producto_id, [])

    if not servicio_ids:
        return []

    conn = get_db_connection()
    try:
        with conn.cursor() as cursor:
            placeholders = ', '.join(['%s'] * len(servicio_ids))
            cursor.execute(f"""
                SELECT id, servicio as nombre
                FROM maquila_servicios
                WHERE id IN ({placeholders}) AND active = 1
                ORDER BY servicio
            """, servicio_ids)
            rows = cursor.fetchall()
            return [
                SelectOption(id=row["id"], nombre=row["nombre"])
                for row in rows
            ]
    finally:
        conn.close()


# =============================================
# ENDPOINTS - JERARQUÍA 3 → RUBRO
# =============================================

@router.get("/jerarquias/{subsubhierarchy_id}/rubro")
async def get_rubro(
    subsubhierarchy_id: int,
    current_user: dict = Depends(get_current_user)
):
    """
    Obtiene el ID de rubro asociado a una jerarquía nivel 3.
    Equivalente a: /getRubro
    """
    conn = get_db_connection()
    try:
        with conn.cursor() as cursor:
            cursor.execute("""
                SELECT rubro_id
                FROM subsubhierarchies
                WHERE id = %s
            """, (subsubhierarchy_id,))
            row = cursor.fetchone()

            if not row:
                raise HTTPException(status_code=404, detail="Jerarquía no encontrada")

            return {"rubro_id": row.get("rubro_id")}
    finally:
        conn.close()


# =============================================
# ENDPOINTS - JERARQUÍA 2 CON FILTRO RUBRO
# =============================================

@router.get("/jerarquias/nivel2-rubro", response_model=List[SelectOption])
async def get_jerarquia2_con_rubro(
    hierarchy_id: int = Query(..., description="ID de Jerarquía nivel 1"),
    rubro_id: Optional[int] = Query(None, description="ID de Rubro para filtrar"),
    current_user: dict = Depends(get_current_user)
):
    """
    Obtiene jerarquías nivel 2 filtradas por nivel 1 y opcionalmente por rubro.
    Equivalente a: /getJerarquia2AreaHC
    """
    conn = get_db_connection()
    try:
        with conn.cursor() as cursor:
            if rubro_id:
                # Filtrar subhierarchies que tengan subsubhierarchies con ese rubro
                cursor.execute("""
                    SELECT DISTINCT sh.id, sh.descripcion as nombre
                    FROM subhierarchies sh
                    JOIN subsubhierarchies ssh ON ssh.subhierarchy_id = sh.id
                    WHERE sh.hierarchy_id = %s
                      AND sh.active = 1
                      AND ssh.rubro_id = %s
                    ORDER BY sh.descripcion
                """, (hierarchy_id, rubro_id))
            else:
                cursor.execute("""
                    SELECT id, descripcion as nombre
                    FROM subhierarchies
                    WHERE hierarchy_id = %s AND active = 1
                    ORDER BY descripcion
                """, (hierarchy_id,))

            rows = cursor.fetchall()
            return [SelectOption(id=row["id"], nombre=row["nombre"]) for row in rows]
    finally:
        conn.close()


@router.get("/jerarquias/nivel3-rubro", response_model=List[SelectOption])
async def get_jerarquia3_con_rubro(
    subhierarchy_id: int = Query(..., description="ID de Jerarquía nivel 2"),
    rubro_id: Optional[int] = Query(None, description="ID de Rubro para filtrar"),
    current_user: dict = Depends(get_current_user)
):
    """
    Obtiene jerarquías nivel 3 filtradas por nivel 2 y opcionalmente por rubro.
    Equivalente a: /getJerarquia3ConRubro
    """
    conn = get_db_connection()
    try:
        with conn.cursor() as cursor:
            query = """
                SELECT id, descripcion as nombre
                FROM subsubhierarchies
                WHERE subhierarchy_id = %s AND active = 1
            """
            params = [subhierarchy_id]

            if rubro_id:
                query += " AND rubro_id = %s"
                params.append(rubro_id)

            query += " ORDER BY descripcion"

            cursor.execute(query, params)
            rows = cursor.fetchall()
            return [SelectOption(id=row["id"], nombre=row["nombre"]) for row in rows]
    finally:
        conn.close()
