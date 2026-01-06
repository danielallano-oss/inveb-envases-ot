"""
Router de Detalles de Cotización.
Endpoints para CRUD de líneas y cálculo de costos.

Basado en: PLAN_MIGRACION_COTIZACIONES.md
"""
from fastapi import APIRouter, HTTPException, Query, UploadFile, File
from typing import List, Optional
import pymysql
import pymysql.cursors
import os
import json
import logging

from ...schemas.detalle_cotizacion import (
    DetalleCotizacionCreate,
    DetalleCotizacionCreateRequest,
    DetalleCotizacionUpdate,
    DetalleCotizacionResponse,
    DetalleCotizacionConPrecios,
    CalcularDetalleRequest,
    CalcularDetalleResponse,
    MarcarGanadoPerdidoRequest,
    CargaMasivaDetallesRequest,
    CargaMasivaDetallesResponse,
    IndiceComplejidadRequest,
    IndiceComplejidadResponse,
)
from ...services.calculo_costos import (
    CalculoCostosService,
    DatosDetalle,
    DatosRelacionados,
    cargar_datos_relacionados,
)

logger = logging.getLogger(__name__)

router = APIRouter(prefix="/cotizaciones", tags=["Detalles Cotización"])


# =============================================
# CONEXIÓN A BASE DE DATOS
# =============================================

def get_db_connection():
    """Obtiene conexión a MySQL con DictCursor"""
    return pymysql.connect(
        host=os.getenv("MYSQL_HOST", "host.docker.internal"),
        port=int(os.getenv("MYSQL_PORT", "3307")),
        user=os.getenv("MYSQL_USER", "root"),
        password=os.getenv("MYSQL_PASSWORD", "root"),
        database=os.getenv("MYSQL_DATABASE", "envases_ot"),
        cursorclass=pymysql.cursors.DictCursor
    )


# =============================================
# ENDPOINTS CRUD DE DETALLES
# =============================================

@router.get("/{cotizacion_id}/detalles", response_model=List[DetalleCotizacionResponse])
async def list_detalles(cotizacion_id: int):
    """
    Lista todos los detalles de una cotización.
    """
    conn = get_db_connection()
    cursor = conn.cursor()

    try:
        # Verificar que existe la cotización
        cursor.execute(
            "SELECT id FROM cotizacions WHERE id = %s AND active = 1",
            (cotizacion_id,)
        )
        if not cursor.fetchone():
            raise HTTPException(status_code=404, detail="Cotización no encontrada")

        # Obtener detalles con relaciones
        cursor.execute("""
            SELECT
                dc.*,
                p.nombre as planta_nombre,
                cb.codigo as carton_codigo,
                pr.descripcion as proceso_nombre,
                r.descripcion as rubro_nombre
            FROM detalle_cotizacions dc
            LEFT JOIN plantas p ON dc.planta_id = p.id
            LEFT JOIN cardboards cb ON dc.carton_id = cb.id
            LEFT JOIN processes pr ON dc.process_id = pr.id
            LEFT JOIN rubros r ON dc.rubro_id = r.id
            WHERE dc.cotizacion_id = %s
            ORDER BY dc.id
        """, (cotizacion_id,))

        rows = cursor.fetchall()

        # Parsear historial_resultados si existe
        for row in rows:
            if row.get("historial_resultados"):
                try:
                    row["historial_resultados"] = json.loads(row["historial_resultados"])
                except:
                    pass

        return rows

    finally:
        cursor.close()
        conn.close()


@router.get("/detalles/{id}", response_model=DetalleCotizacionConPrecios)
async def get_detalle(id: int, calcular: bool = False):
    """
    Obtiene un detalle específico con sus precios calculados.

    Args:
        id: ID del detalle
        calcular: Si True, recalcula los precios en tiempo real
    """
    conn = get_db_connection()
    cursor = conn.cursor()

    try:
        # Obtener detalle con relaciones
        cursor.execute("""
            SELECT
                dc.*,
                p.nombre as planta_nombre,
                cb.codigo as carton_codigo,
                pr.descripcion as proceso_nombre,
                r.descripcion as rubro_nombre,
                c.estado_id
            FROM detalle_cotizacions dc
            LEFT JOIN plantas p ON dc.planta_id = p.id
            LEFT JOIN cardboards cb ON dc.carton_id = cb.id
            LEFT JOIN processes pr ON dc.process_id = pr.id
            LEFT JOIN rubros r ON dc.rubro_id = r.id
            LEFT JOIN cotizacions c ON dc.cotizacion_id = c.id
            WHERE dc.id = %s
        """, (id,))

        row = cursor.fetchone()
        if not row:
            raise HTTPException(status_code=404, detail="Detalle no encontrado")

        # Parsear historial_resultados
        precios = None
        if row.get("historial_resultados"):
            try:
                precios = json.loads(row["historial_resultados"])
            except:
                pass

        # Si se pide calcular o no hay historial, calcular precios
        if calcular or (not precios and row["estado_id"] == 1):
            try:
                # Construir datos del detalle
                detalle_data = DatosDetalle(
                    id=row["id"],
                    cotizacion_id=row["cotizacion_id"],
                    tipo_detalle_id=row["tipo_detalle_id"],
                    cantidad=row["cantidad"],
                    area_hc=row["area_hc"] or 0,
                    anchura=row["anchura"] or 0,
                    largura=row["largura"] or 0,
                    golpes_largo=row["golpes_largo"] or 0,
                    golpes_ancho=row["golpes_ancho"] or 0,
                    carton_id=row["carton_id"],
                    process_id=row["process_id"],
                    rubro_id=row["rubro_id"],
                    planta_id=row["planta_id"],
                    variable_cotizador_id=row["variable_cotizador_id"] or 1,
                    impresion=row["impresion"] or 0,
                    numero_colores=row["numero_colores"] or 0,
                    porcentaje_cera_interno=row["porcentaje_cera_interno"] or 0,
                    porcentaje_cera_externo=row["porcentaje_cera_externo"] or 0,
                    matriz=row["matriz"] or 0,
                    clisse=row["clisse"] or 0,
                    royalty=row["royalty"] or 0,
                    maquila=row["maquila"] or 0,
                    armado_automatico=row["armado_automatico"] or 0,
                    armado_usd_caja=row["armado_usd_caja"] or 0,
                    pallet=row["pallet"] or 0,
                    zuncho=row["zuncho"] or 0,
                    funda=row["funda"] or 0,
                    stretch_film=row["stretch_film"] or 0,
                    ciudad_id=row["ciudad_id"],
                    margen=row["margen"] or 0,
                )

                # Cargar datos relacionados
                datos = cargar_datos_relacionados(conn, detalle_data)

                # Calcular precios
                service = CalculoCostosService(conn)
                resultado = service.calcular_precios(detalle_data, datos)
                precios = resultado.to_dict()

            except Exception as e:
                logger.error(f"Error calculando precios: {e}")
                precios = {"error": str(e)}

        row["precios"] = precios
        return row

    finally:
        cursor.close()
        conn.close()


@router.post("/{cotizacion_id}/detalles", response_model=DetalleCotizacionResponse)
async def create_detalle(cotizacion_id: int, data: DetalleCotizacionCreateRequest):
    """
    Crea un nuevo detalle en una cotización.
    Solo se puede agregar si la cotización está en borrador.
    """
    conn = get_db_connection()
    cursor = conn.cursor()

    try:
        # Verificar cotización en borrador
        cursor.execute(
            "SELECT estado_id FROM cotizacions WHERE id = %s AND active = 1",
            (cotizacion_id,)
        )
        row = cursor.fetchone()
        if not row:
            raise HTTPException(status_code=404, detail="Cotización no encontrada")
        if row["estado_id"] != 1:
            raise HTTPException(
                status_code=400,
                detail="Solo se pueden agregar detalles a cotizaciones en borrador"
            )

        # Construir INSERT
        data_dict = data.model_dump(exclude_unset=True)
        data_dict["cotizacion_id"] = cotizacion_id

        columns = list(data_dict.keys())
        placeholders = ["%s"] * len(columns)
        values = [data_dict[col] for col in columns]

        # Agregar timestamps
        columns.extend(["created_at", "updated_at"])
        placeholders.extend(["NOW()", "NOW()"])

        query = f"""
            INSERT INTO detalle_cotizacions ({', '.join(columns)})
            VALUES ({', '.join(placeholders)})
        """

        cursor.execute(query, values)
        conn.commit()
        detalle_id = cursor.lastrowid

        # Obtener detalle creado
        cursor.execute(
            "SELECT * FROM detalle_cotizacions WHERE id = %s",
            (detalle_id,)
        )
        return cursor.fetchone()

    except HTTPException:
        raise
    except Exception as e:
        conn.rollback()
        logger.error(f"Error creando detalle: {e}")
        raise HTTPException(status_code=500, detail=str(e))

    finally:
        cursor.close()
        conn.close()


@router.put("/detalles/{id}", response_model=DetalleCotizacionResponse)
async def update_detalle(id: int, data: DetalleCotizacionUpdate):
    """
    Actualiza un detalle de cotización.
    Solo se puede editar si la cotización está en borrador.
    """
    conn = get_db_connection()
    cursor = conn.cursor()

    try:
        # Verificar que existe y la cotización está en borrador
        cursor.execute("""
            SELECT dc.id, c.estado_id
            FROM detalle_cotizacions dc
            JOIN cotizacions c ON dc.cotizacion_id = c.id
            WHERE dc.id = %s
        """, (id,))
        row = cursor.fetchone()

        if not row:
            raise HTTPException(status_code=404, detail="Detalle no encontrado")
        if row["estado_id"] != 1:
            raise HTTPException(
                status_code=400,
                detail="Solo se pueden editar detalles de cotizaciones en borrador"
            )

        # Construir UPDATE
        data_dict = data.model_dump(exclude_unset=True)
        if not data_dict:
            raise HTTPException(status_code=400, detail="No hay campos para actualizar")

        updates = [f"{key} = %s" for key in data_dict.keys()]
        updates.append("updated_at = NOW()")
        values = list(data_dict.values())
        values.append(id)

        query = f"""
            UPDATE detalle_cotizacions
            SET {', '.join(updates)}
            WHERE id = %s
        """

        cursor.execute(query, values)
        conn.commit()

        # Obtener detalle actualizado
        cursor.execute(
            "SELECT * FROM detalle_cotizacions WHERE id = %s",
            (id,)
        )
        return cursor.fetchone()

    except HTTPException:
        raise
    except Exception as e:
        conn.rollback()
        logger.error(f"Error actualizando detalle: {e}")
        raise HTTPException(status_code=500, detail=str(e))

    finally:
        cursor.close()
        conn.close()


@router.delete("/detalles/{id}")
async def delete_detalle(id: int):
    """
    Elimina un detalle de cotización.
    Solo se puede eliminar si la cotización está en borrador.
    """
    conn = get_db_connection()
    cursor = conn.cursor()

    try:
        # Verificar que existe y la cotización está en borrador
        cursor.execute("""
            SELECT dc.id, c.estado_id
            FROM detalle_cotizacions dc
            JOIN cotizacions c ON dc.cotizacion_id = c.id
            WHERE dc.id = %s
        """, (id,))
        row = cursor.fetchone()

        if not row:
            raise HTTPException(status_code=404, detail="Detalle no encontrado")
        if row["estado_id"] != 1:
            raise HTTPException(
                status_code=400,
                detail="Solo se pueden eliminar detalles de cotizaciones en borrador"
            )

        cursor.execute("DELETE FROM detalle_cotizacions WHERE id = %s", (id,))
        conn.commit()

        return {"message": "Detalle eliminado exitosamente"}

    except HTTPException:
        raise
    except Exception as e:
        conn.rollback()
        logger.error(f"Error eliminando detalle: {e}")
        raise HTTPException(status_code=500, detail=str(e))

    finally:
        cursor.close()
        conn.close()


# =============================================
# ENDPOINTS DE CÁLCULO
# =============================================

@router.post("/detalles/{id}/calcular", response_model=CalcularDetalleResponse)
async def calcular_detalle(id: int):
    """
    Calcula los precios de un detalle en tiempo real.
    No guarda los resultados, solo los retorna.
    """
    conn = get_db_connection()
    cursor = conn.cursor()

    try:
        # Obtener detalle
        cursor.execute("SELECT * FROM detalle_cotizacions WHERE id = %s", (id,))
        row = cursor.fetchone()

        if not row:
            raise HTTPException(status_code=404, detail="Detalle no encontrado")

        # Construir datos
        from decimal import Decimal
        detalle_data = DatosDetalle(
            id=row["id"],
            cotizacion_id=row["cotizacion_id"],
            tipo_detalle_id=row["tipo_detalle_id"],
            cantidad=row["cantidad"],
            area_hc=Decimal(str(row["area_hc"] or 0)),
            anchura=row["anchura"] or 0,
            largura=row["largura"] or 0,
            golpes_largo=row["golpes_largo"] or 0,
            golpes_ancho=row["golpes_ancho"] or 0,
            carton_id=row["carton_id"],
            process_id=row["process_id"],
            rubro_id=row["rubro_id"],
            planta_id=row["planta_id"],
            variable_cotizador_id=row["variable_cotizador_id"] or 1,
            impresion=row["impresion"] or 0,
            numero_colores=row["numero_colores"] or 0,
            porcentaje_cera_interno=row["porcentaje_cera_interno"] or 0,
            porcentaje_cera_externo=row["porcentaje_cera_externo"] or 0,
            matriz=row["matriz"] or 0,
            clisse=row["clisse"] or 0,
            royalty=row["royalty"] or 0,
            maquila=row["maquila"] or 0,
            armado_automatico=row["armado_automatico"] or 0,
            armado_usd_caja=Decimal(str(row["armado_usd_caja"] or 0)),
            pallet=row["pallet"] or 0,
            zuncho=row["zuncho"] or 0,
            funda=row["funda"] or 0,
            stretch_film=row["stretch_film"] or 0,
            ciudad_id=row["ciudad_id"],
            margen=Decimal(str(row["margen"] or 0)),
        )

        # Cargar datos relacionados
        datos = cargar_datos_relacionados(conn, detalle_data)

        # Calcular precios
        service = CalculoCostosService(conn)
        resultado = service.calcular_precios(detalle_data, datos)

        return CalcularDetalleResponse(
            success=True,
            precios=resultado.to_dict(),
            warnings=[]
        )

    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Error calculando detalle: {e}")
        return CalcularDetalleResponse(
            success=False,
            precios=None,
            error=str(e)
        )

    finally:
        cursor.close()
        conn.close()


@router.post("/calcular-preview", response_model=CalcularDetalleResponse)
async def calcular_preview(data: CalcularDetalleRequest):
    """
    Calcula precios sin guardar el detalle.
    Útil para preview en formulario.
    """
    conn = get_db_connection()

    try:
        from decimal import Decimal

        # Construir datos del detalle desde el request
        detalle_data = DatosDetalle(
            tipo_detalle_id=data.tipo_detalle_id or 1,
            cantidad=data.cantidad or 0,
            area_hc=data.area_hc or Decimal("0"),
            anchura=data.anchura or 0,
            largura=data.largura or 0,
            carton_id=data.carton_id,
            process_id=data.process_id,
            rubro_id=data.rubro_id,
            planta_id=data.planta_id or 1,
        )

        # Cargar datos relacionados
        datos = cargar_datos_relacionados(conn, detalle_data)

        # Calcular precios
        service = CalculoCostosService(conn)
        resultado = service.calcular_precios(detalle_data, datos)

        return CalcularDetalleResponse(
            success=True,
            precios=resultado.to_dict(),
            warnings=[]
        )

    except Exception as e:
        logger.error(f"Error calculando preview: {e}")
        return CalcularDetalleResponse(
            success=False,
            precios=None,
            error=str(e)
        )

    finally:
        conn.close()


# =============================================
# ENDPOINTS DE ESTADO
# =============================================

@router.post("/detalles/{id}/ganado")
async def marcar_ganado(id: int):
    """
    Marca un detalle como ganado.
    """
    conn = get_db_connection()
    cursor = conn.cursor()

    try:
        cursor.execute("""
            UPDATE detalle_cotizacions
            SET ganado = 1, perdido = 0, updated_at = NOW()
            WHERE id = %s
        """, (id,))

        if cursor.rowcount == 0:
            raise HTTPException(status_code=404, detail="Detalle no encontrado")

        conn.commit()
        return {"message": "Detalle marcado como ganado"}

    finally:
        cursor.close()
        conn.close()


@router.post("/detalles/{id}/perdido")
async def marcar_perdido(id: int):
    """
    Marca un detalle como perdido.
    """
    conn = get_db_connection()
    cursor = conn.cursor()

    try:
        cursor.execute("""
            UPDATE detalle_cotizacions
            SET perdido = 1, ganado = 0, updated_at = NOW()
            WHERE id = %s
        """, (id,))

        if cursor.rowcount == 0:
            raise HTTPException(status_code=404, detail="Detalle no encontrado")

        conn.commit()
        return {"message": "Detalle marcado como perdido"}

    finally:
        cursor.close()
        conn.close()


@router.post("/detalles/{id}/limpiar-estado")
async def limpiar_estado(id: int):
    """
    Limpia el estado ganado/perdido de un detalle.
    """
    conn = get_db_connection()
    cursor = conn.cursor()

    try:
        cursor.execute("""
            UPDATE detalle_cotizacions
            SET ganado = 0, perdido = 0, updated_at = NOW()
            WHERE id = %s
        """, (id,))

        if cursor.rowcount == 0:
            raise HTTPException(status_code=404, detail="Detalle no encontrado")

        conn.commit()
        return {"message": "Estado del detalle limpiado"}

    finally:
        cursor.close()
        conn.close()


# =============================================
# CARGA MASIVA
# =============================================

@router.post("/{cotizacion_id}/detalles/carga-masiva", response_model=CargaMasivaDetallesResponse)
async def carga_masiva_detalles(cotizacion_id: int, data: CargaMasivaDetallesRequest):
    """
    Carga múltiples detalles de una vez.
    """
    conn = get_db_connection()
    cursor = conn.cursor()

    total_procesados = 0
    total_exitosos = 0
    errores = []

    try:
        # Verificar cotización en borrador
        cursor.execute(
            "SELECT estado_id FROM cotizacions WHERE id = %s AND active = 1",
            (cotizacion_id,)
        )
        row = cursor.fetchone()
        if not row:
            raise HTTPException(status_code=404, detail="Cotización no encontrada")
        if row["estado_id"] != 1:
            raise HTTPException(
                status_code=400,
                detail="Solo se pueden agregar detalles a cotizaciones en borrador"
            )

        # Procesar cada detalle
        for i, detalle in enumerate(data.detalles):
            total_procesados += 1
            try:
                data_dict = detalle.model_dump(exclude_unset=True)
                data_dict["cotizacion_id"] = cotizacion_id

                columns = list(data_dict.keys())
                placeholders = ["%s"] * len(columns)
                values = [data_dict[col] for col in columns]

                columns.extend(["created_at", "updated_at"])
                placeholders.extend(["NOW()", "NOW()"])

                query = f"""
                    INSERT INTO detalle_cotizacions ({', '.join(columns)})
                    VALUES ({', '.join(placeholders)})
                """

                cursor.execute(query, values)
                total_exitosos += 1

            except Exception as e:
                errores.append({
                    "index": i,
                    "error": str(e)
                })

        conn.commit()

        return CargaMasivaDetallesResponse(
            success=len(errores) == 0,
            total_procesados=total_procesados,
            total_exitosos=total_exitosos,
            total_errores=len(errores),
            errores=errores
        )

    except HTTPException:
        raise
    except Exception as e:
        conn.rollback()
        logger.error(f"Error en carga masiva: {e}")
        raise HTTPException(status_code=500, detail=str(e))

    finally:
        cursor.close()
        conn.close()


# =============================================
# ÍNDICE DE COMPLEJIDAD
# =============================================

@router.post("/detalles/{id}/calcular-complejidad", response_model=IndiceComplejidadResponse)
async def calcular_indice_complejidad(id: int):
    """
    Calcula el índice de complejidad de un detalle.
    El índice determina el margen mínimo sugerido.
    """
    conn = get_db_connection()
    cursor = conn.cursor()

    try:
        # Obtener detalle
        cursor.execute("SELECT * FROM detalle_cotizacions WHERE id = %s", (id,))
        row = cursor.fetchone()

        if not row:
            raise HTTPException(status_code=404, detail="Detalle no encontrado")

        # Calcular factores de complejidad
        factores = {}
        indice = 0

        # Factor por número de colores
        num_colores = row["numero_colores"] or 0
        if num_colores > 0:
            factor_colores = min(num_colores * 2, 10)
            factores["colores"] = factor_colores
            indice += factor_colores

        # Factor por matriz
        if row["matriz"]:
            factores["matriz"] = 5
            indice += 5

        # Factor por clisse
        if row["clisse"]:
            factores["clisse"] = 3
            indice += 3

        # Factor por maquila
        if row["maquila"]:
            factores["maquila"] = 5
            indice += 5

        # Factor por servicios de embalaje
        servicios_embalaje = sum([
            1 if row["pallet"] else 0,
            1 if row["zuncho"] else 0,
            1 if row["funda"] else 0,
            1 if row["stretch_film"] else 0,
        ])
        if servicios_embalaje > 0:
            factores["embalaje"] = servicios_embalaje * 2
            indice += servicios_embalaje * 2

        # Buscar margen sugerido según índice
        cursor.execute("""
            SELECT margen_minimo_usd_mm2
            FROM tarifario_margens
            WHERE indice_complejidad <= %s
            ORDER BY indice_complejidad DESC
            LIMIT 1
        """, (indice,))

        margen_row = cursor.fetchone()
        margen_sugerido = margen_row["margen_minimo_usd_mm2"] if margen_row else None

        # Actualizar detalle con índice calculado
        cursor.execute("""
            UPDATE detalle_cotizacions
            SET indice_complejidad = %s, margen_sugerido = %s, updated_at = NOW()
            WHERE id = %s
        """, (indice, margen_sugerido, id))
        conn.commit()

        return IndiceComplejidadResponse(
            indice_complejidad=indice,
            factores=factores,
            margen_sugerido=margen_sugerido
        )

    finally:
        cursor.close()
        conn.close()
