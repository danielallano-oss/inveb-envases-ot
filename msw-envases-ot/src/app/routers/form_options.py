"""
Router para opciones del formulario Cascade.
Provee los datos para los selectores del formulario OT.

FASE 3: Implementacion de endpoints faltantes.
ACTUALIZADO: Lee datos de la base de datos en lugar de hardcodear.
"""
from fastapi import APIRouter, HTTPException
from pydantic import BaseModel
from typing import List, Optional
import pymysql
from app.config import get_settings

settings = get_settings()

router = APIRouter(
    prefix="/form-options",
    tags=["Form Options"],
    responses={404: {"description": "Not found"}},
)


def get_db_connection():
    """Obtiene conexión a la base de datos de Laravel."""
    return pymysql.connect(
        host=settings.LARAVEL_MYSQL_HOST,
        port=settings.LARAVEL_MYSQL_PORT,
        user=settings.LARAVEL_MYSQL_USER,
        password=settings.LARAVEL_MYSQL_PASSWORD,
        database=settings.LARAVEL_MYSQL_DATABASE,
        charset='utf8mb4',
        cursorclass=pymysql.cursors.DictCursor
    )


class SelectOption(BaseModel):
    """Modelo para opciones de selector."""
    value: int | str
    label: str
    description: Optional[str] = None


class FormOptionsResponse(BaseModel):
    """Respuesta con todas las opciones del formulario."""
    product_types: List[SelectOption]
    impresion_types: List[SelectOption]
    fsc_options: List[SelectOption]
    cinta_options: List[SelectOption]
    coverage_internal: List[SelectOption]
    coverage_external: List[SelectOption]
    plantas: List[SelectOption]
    carton_colors: List[SelectOption]
    cartones: List[SelectOption]
    # Seccion 11 - Terminaciones
    procesos: List[SelectOption]
    armados: List[SelectOption]
    pegados: List[SelectOption]
    sentidos_armado: List[SelectOption]


def get_product_types_from_db() -> List[SelectOption]:
    """Obtiene los tipos de producto de la base de datos."""
    try:
        conn = get_db_connection()
        cursor = conn.cursor()
        cursor.execute("""
            SELECT id, descripcion, codigo
            FROM product_types
            WHERE active = 1
            ORDER BY descripcion
        """)
        rows = cursor.fetchall()
        conn.close()
        return [
            SelectOption(value=r['id'], label=r['descripcion'] or r['codigo'], description=r['codigo'])
            for r in rows
        ]
    except Exception as e:
        print(f"Error fetching product_types: {e}")
        return []


# =============================================================================
# DATOS DE RESPALDO (se usan si la BD no está disponible)
# =============================================================================

# Impresion - De migration 2022_06_29_110042_create_impresion_table.php
IMPRESION_TYPES = [
    SelectOption(value=1, label="Offset"),
    SelectOption(value=2, label="Flexografia"),
    SelectOption(value=3, label="Flexografia Alta Grafica"),
    SelectOption(value=4, label="Flexografia Tiro y Retiro"),
    SelectOption(value=5, label="Sin Impresion"),
    SelectOption(value=6, label="Sin Impresion (Solo OF)"),
    SelectOption(value=7, label="Sin Impresion (Trazabilidad Completa)"),
]

# FSC - De migration 2021_09_15_105008_create_table_fsc.php
# Issue 21: Quitadas opciones SI/NO (codigos 0 y 1) segun requerimiento
FSC_OPTIONS = [
    SelectOption(value=2, label="Sin FSC", description="Producto sin FSC"),
    SelectOption(value=3, label="Logo FSC solo EEII", description="Logo solo en especificacion"),
    SelectOption(value=4, label="Logo FSC cliente y EEII", description="Logo en producto y especificacion"),
    SelectOption(value=5, label="Logo FSC solo cliente", description="Logo solo en producto del cliente"),
    SelectOption(value=6, label="FSC solo facturacion", description="FSC solo para facturacion"),
]

# Cinta - Campo booleano en work_orders (tinyint 0/1)
# En Laravel se usa: [1 => "Si", 0=>"No"]
CINTA_OPTIONS = [
    SelectOption(value=1, label="Si", description="Con cinta"),
    SelectOption(value=0, label="No", description="Sin cinta"),
]

# Coverage Internal - De migration 2022_04_18_165500_create_coverage_internal_table.php
COVERAGE_INTERNAL = [
    SelectOption(value=1, label="No aplica", description="Sin recubrimiento interno"),
    SelectOption(value=2, label="Barniz hidrorepelente", description="Proteccion contra humedad"),
    SelectOption(value=3, label="Cera", description="Recubrimiento de cera"),
]

# Coverage External - De migration 2022_04_18_165732_create_coverage_external_table.php
COVERAGE_EXTERNAL = [
    SelectOption(value=1, label="No aplica", description="Sin recubrimiento externo"),
    SelectOption(value=2, label="Barniz hidrorepelente", description="Proteccion contra humedad"),
    SelectOption(value=3, label="Barniz acuoso", description="Barniz base agua"),
    SelectOption(value=4, label="Barniz UV", description="Barniz ultravioleta"),
    SelectOption(value=5, label="Cera", description="Recubrimiento de cera"),
]

# Plantas - De tabla plantas (datos de produccion INVEB Chile)
PLANTAS = [
    SelectOption(value=1, label="Buin", description="Planta Buin - Region Metropolitana"),
    SelectOption(value=2, label="Til Til", description="Planta Til Til - Region Metropolitana"),
    SelectOption(value=3, label="Osorno", description="Planta Osorno - Region de Los Lagos"),
    SelectOption(value=4, label="Chillan", description="Planta Chillan - Region de Nuble"),
]

# Colores de Carton - Igual que Laravel: [1=>"Café", 2=>"Blanco"]
# work_orders.carton_color usa valores 1 y 2
CARTON_COLORS = [
    SelectOption(value=1, label="Café", description="Tapa exterior café"),
    SelectOption(value=2, label="Blanco", description="Tapa exterior blanca"),
]

# Cartones - Ejemplos de tabla cartons (estructura: codigo, onda, color, tipo)
# En produccion estos datos vienen de la BD con filtros por planta, impresion, etc.
CARTONES = [
    SelectOption(value=1, label="BC-KR-350", description="Doble onda BC, Kraft, 350g"),
    SelectOption(value=2, label="BC-KR-450", description="Doble onda BC, Kraft, 450g"),
    SelectOption(value=3, label="BC-BL-350", description="Doble onda BC, Blanco, 350g"),
    SelectOption(value=4, label="C-KR-250", description="Onda C, Kraft, 250g"),
    SelectOption(value=5, label="C-KR-350", description="Onda C, Kraft, 350g"),
    SelectOption(value=6, label="B-KR-200", description="Onda B, Kraft, 200g"),
    SelectOption(value=7, label="E-KR-150", description="Microonda E, Kraft, 150g"),
    SelectOption(value=8, label="E-BL-150", description="Microonda E, Blanco, 150g"),
]

# =============================================================================
# SECCION 11 - TERMINACIONES
# =============================================================================

# Sentido de Armado - Hardcodeado en Laravel (WorkOrderController.php)
SENTIDOS_ARMADO = [
    SelectOption(value=1, label="No aplica"),
    SelectOption(value=2, label="Ancho a la Derecha"),
    SelectOption(value=3, label="Ancho a la Izquierda"),
    SelectOption(value=4, label="Largo a la Izquierda"),
    SelectOption(value=5, label="Largo a la Derecha"),
]


def get_procesos_from_db() -> List[SelectOption]:
    """Obtiene los procesos de la base de datos (type='EV', active=1)."""
    try:
        conn = get_db_connection()
        cursor = conn.cursor()
        cursor.execute("""
            SELECT id, descripcion
            FROM processes
            WHERE active = 1 AND type = 'EV'
            ORDER BY orden ASC
        """)
        rows = cursor.fetchall()
        conn.close()
        return [
            SelectOption(value=r['id'], label=r['descripcion'])
            for r in rows
        ]
    except Exception as e:
        print(f"Error fetching processes: {e}")
        return []


def get_armados_from_db() -> List[SelectOption]:
    """Obtiene los tipos de armado de la base de datos."""
    try:
        conn = get_db_connection()
        cursor = conn.cursor()
        cursor.execute("""
            SELECT id, descripcion
            FROM armados
            WHERE active = 1
            ORDER BY id
        """)
        rows = cursor.fetchall()
        conn.close()
        return [
            SelectOption(value=r['id'], label=r['descripcion'])
            for r in rows
        ]
    except Exception as e:
        print(f"Error fetching armados: {e}")
        return []


def get_pegados_from_db() -> List[SelectOption]:
    """Obtiene los tipos de pegado de la base de datos."""
    try:
        conn = get_db_connection()
        cursor = conn.cursor()
        cursor.execute("""
            SELECT id, descripcion
            FROM pegados
            WHERE active = 1
            ORDER BY id
        """)
        rows = cursor.fetchall()
        conn.close()
        return [
            SelectOption(value=r['id'], label=r['descripcion'])
            for r in rows
        ]
    except Exception as e:
        print(f"Error fetching pegados: {e}")
        return []


@router.get("/", response_model=FormOptionsResponse)
async def get_all_options():
    """
    Obtiene todas las opciones del formulario en una sola llamada.
    Optimizado para carga inicial del formulario.
    Lee datos de la base de datos donde aplica.
    """
    # Obtener datos de la BD
    product_types = get_product_types_from_db()
    procesos = get_procesos_from_db()
    armados = get_armados_from_db()
    pegados = get_pegados_from_db()

    return FormOptionsResponse(
        product_types=product_types,
        impresion_types=IMPRESION_TYPES,
        fsc_options=FSC_OPTIONS,
        cinta_options=CINTA_OPTIONS,
        coverage_internal=COVERAGE_INTERNAL,
        coverage_external=COVERAGE_EXTERNAL,
        plantas=PLANTAS,
        carton_colors=CARTON_COLORS,
        cartones=CARTONES,
        # Seccion 11 - Terminaciones
        procesos=procesos,
        armados=armados,
        pegados=pegados,
        sentidos_armado=SENTIDOS_ARMADO,
    )


@router.get("/product-types", response_model=List[SelectOption])
async def get_product_types():
    """Obtiene tipos de producto de la base de datos."""
    return get_product_types_from_db()


@router.get("/impresion-types", response_model=List[SelectOption])
async def get_impresion_types():
    """Obtiene tipos de impresion disponibles."""
    return IMPRESION_TYPES


@router.get("/fsc-options", response_model=List[SelectOption])
async def get_fsc_options():
    """Obtiene opciones FSC disponibles."""
    return FSC_OPTIONS


@router.get("/cinta-options", response_model=List[SelectOption])
async def get_cinta_options():
    """Obtiene opciones de cinta disponibles."""
    return CINTA_OPTIONS


@router.get("/coverage-internal", response_model=List[SelectOption])
async def get_coverage_internal():
    """Obtiene opciones de recubrimiento interno."""
    return COVERAGE_INTERNAL


@router.get("/coverage-external", response_model=List[SelectOption])
async def get_coverage_external():
    """Obtiene opciones de recubrimiento externo."""
    return COVERAGE_EXTERNAL


@router.get("/plantas", response_model=List[SelectOption])
async def get_plantas():
    """Obtiene plantas disponibles."""
    return PLANTAS


@router.get("/carton-colors", response_model=List[SelectOption])
async def get_carton_colors():
    """Obtiene colores de carton disponibles."""
    return CARTON_COLORS


@router.get("/cartones", response_model=List[SelectOption])
async def get_cartones():
    """Obtiene tipos de carton disponibles."""
    return CARTONES


# =============================================================================
# SECCION 11 - TERMINACIONES ENDPOINTS
# =============================================================================

@router.get("/procesos", response_model=List[SelectOption])
async def get_procesos():
    """Obtiene procesos disponibles (type='EV', active=1)."""
    return get_procesos_from_db()


@router.get("/armados", response_model=List[SelectOption])
async def get_armados():
    """Obtiene tipos de armado disponibles."""
    return get_armados_from_db()


@router.get("/pegados", response_model=List[SelectOption])
async def get_pegados():
    """Obtiene tipos de pegado disponibles."""
    return get_pegados_from_db()


@router.get("/sentidos-armado", response_model=List[SelectOption])
async def get_sentidos_armado():
    """Obtiene sentidos de armado disponibles."""
    return SENTIDOS_ARMADO
