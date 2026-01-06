"""
Router de Generación de PDFs - INVEB Cascade Service
Genera PDFs para etiquetas, fichas técnicas y estudios.
FASE 6.24
"""
from datetime import datetime
from io import BytesIO
from fastapi import APIRouter, HTTPException, Depends
from fastapi.responses import StreamingResponse
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
import pymysql
import jwt
import os

from reportlab.lib import colors
from reportlab.lib.pagesizes import A4, letter
from reportlab.lib.units import mm, cm
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle
from reportlab.pdfgen import canvas

from app.config import get_settings

router = APIRouter(prefix="/pdfs", tags=["PDFs"])
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


def get_ot_data(ot_id: int) -> dict:
    """Obtiene datos completos de una OT."""
    conn = get_db_connection()
    try:
        with conn.cursor() as cursor:
            sql = """
            SELECT
                ot.*,
                c.nombre as client_name,
                c.codigo_sap as client_sap_code,
                ca.nombre as canal_nombre,
                pt.nombre as product_type_nombre,
                st.nombre as estilo_nombre,
                st.codigo as estilo_codigo,
                ct.nombre as carton_nombre,
                ct.codigo as carton_codigo,
                ry.nombre as rayado_nombre,
                m.codigo as material_codigo,
                m.descripcion as material_descripcion,
                ssh.nombre as subsubhierarchy_nombre,
                p.nombre as planta_nombre,
                tp.nombre as tipo_palet_nombre,
                proc.nombre as proceso_nombre,
                arm.nombre as armado_nombre,
                peg.nombre as pegado_nombre,
                CONCAT(u.name, ' ', u.last_name) as creador_nombre,
                CONCAT(v.name, ' ', v.last_name) as vendedor_nombre,
                tonda.nombre as tipo_onda_nombre
            FROM work_orders ot
            LEFT JOIN clients c ON ot.client_id = c.id
            LEFT JOIN canals ca ON ot.canal_id = ca.id
            LEFT JOIN product_types pt ON ot.product_type_id = pt.id
            LEFT JOIN styles st ON ot.estilo_id = st.id
            LEFT JOIN cartons ct ON ot.carton_id = ct.id
            LEFT JOIN rayados ry ON ot.rayado_id = ry.id
            LEFT JOIN materials m ON ot.material_id = m.id
            LEFT JOIN subsubhierarchies ssh ON ot.subsubhierarchy_id = ssh.id
            LEFT JOIN plantas p ON ot.planta_id = p.id
            LEFT JOIN pallet_types tp ON ot.tipo_pallet_id = tp.id
            LEFT JOIN processes proc ON ot.process_id = proc.id
            LEFT JOIN armados arm ON ot.armado_id = arm.id
            LEFT JOIN pegados peg ON ot.pegado_id = peg.id
            LEFT JOIN users u ON ot.creador_id = u.id
            LEFT JOIN users v ON ot.vendedor_id = v.id
            LEFT JOIN tipo_ondas tonda ON ot.tipo_onda_id = tonda.id
            WHERE ot.id = %s
            """
            cursor.execute(sql, (ot_id,))
            ot = cursor.fetchone()
            if not ot:
                raise HTTPException(status_code=404, detail="OT no encontrada")

            # Obtener colores
            cursor.execute("""
                SELECT c.nombre, c.codigo, wc.consumo
                FROM work_order_colors wc
                JOIN colors c ON wc.color_id = c.id
                WHERE wc.work_order_id = %s
                ORDER BY wc.id
            """, (ot_id,))
            ot['colores'] = cursor.fetchall()

            return ot
    finally:
        conn.close()


def get_muestra_data(muestra_id: int) -> dict:
    """Obtiene datos de una muestra."""
    conn = get_db_connection()
    try:
        with conn.cursor() as cursor:
            sql = """
            SELECT
                m.*,
                ot.id as ot_id,
                ot.descripcion as ot_descripcion,
                ot.largo, ot.ancho, ot.alto,
                ot.largo_exterior, ot.ancho_exterior, ot.alto_exterior,
                ot.cad,
                c.nombre as client_name,
                c.direccion as client_direccion,
                c.ciudad as client_ciudad,
                ct.nombre as carton_nombre,
                tonda.nombre as tipo_onda_nombre,
                CONCAT(u.name, ' ', u.last_name) as disenador_nombre
            FROM muestras m
            JOIN work_orders ot ON m.work_order_id = ot.id
            LEFT JOIN clients c ON ot.client_id = c.id
            LEFT JOIN cartons ct ON ot.carton_id = ct.id
            LEFT JOIN tipo_ondas tonda ON ot.tipo_onda_id = tonda.id
            LEFT JOIN users u ON m.disenador_id = u.id
            WHERE m.id = %s
            """
            cursor.execute(sql, (muestra_id,))
            muestra = cursor.fetchone()
            if not muestra:
                raise HTTPException(status_code=404, detail="Muestra no encontrada")
            return muestra
    finally:
        conn.close()


@router.get("/etiqueta-muestra/{muestra_id}")
async def generar_etiqueta_muestra(
    muestra_id: int,
    tipo: str = "producto",
    current_user: dict = Depends(get_current_user)
):
    """
    Genera etiqueta PDF para muestra.
    tipo: 'producto' (10x10cm) o 'cliente' (A4)
    """
    muestra = get_muestra_data(muestra_id)

    buffer = BytesIO()

    if tipo == "producto":
        # Etiqueta de producto 10x10 cm
        page_size = (100*mm, 100*mm)
        c = canvas.Canvas(buffer, pagesize=page_size)

        # Titulo
        c.setFont("Helvetica-Bold", 14)
        c.drawCentredString(50*mm, 90*mm, "ETIQUETA DE MUESTRA")

        c.setFont("Helvetica", 10)
        y = 78*mm

        # Datos de la muestra
        data = [
            f"OT: {muestra.get('ot_id', '')}",
            f"CAD: {muestra.get('cad', '-')}",
            f"Cliente: {muestra.get('client_name', '-')[:30]}",
            f"Descripcion: {muestra.get('ot_descripcion', '-')[:25]}",
            "",
            f"Dim. Int: {muestra.get('largo', 0)} x {muestra.get('ancho', 0)} x {muestra.get('alto', 0)} mm",
            f"Dim. Ext: {muestra.get('largo_exterior', 0)} x {muestra.get('ancho_exterior', 0)} x {muestra.get('alto_exterior', 0)} mm",
            "",
            f"Carton: {muestra.get('carton_nombre', '-')}",
            f"Onda: {muestra.get('tipo_onda_nombre', '-')}",
            "",
            f"Disenador: {muestra.get('disenador_nombre', '-')}",
            f"Fecha: {datetime.now().strftime('%d/%m/%Y')}",
        ]

        for line in data:
            c.drawString(8*mm, y, line)
            y -= 5*mm

        # Recuadro
        c.rect(3*mm, 3*mm, 94*mm, 94*mm)

        c.save()

    else:
        # Etiqueta de cliente A4
        page_size = A4
        c = canvas.Canvas(buffer, pagesize=page_size)
        width, height = page_size

        c.setFont("Helvetica-Bold", 18)
        c.drawCentredString(width/2, height - 50*mm, "ETIQUETA DE ENVIO")

        c.setFont("Helvetica", 14)
        y = height - 80*mm

        data = [
            f"DESTINATARIO:",
            f"{muestra.get('client_name', '-')}",
            "",
            f"Direccion: {muestra.get('client_direccion', '-')}",
            f"Ciudad: {muestra.get('client_ciudad', '-')}",
            "",
            f"OT: {muestra.get('ot_id', '')}",
            f"Muestra ID: {muestra_id}",
            f"Fecha: {datetime.now().strftime('%d/%m/%Y')}",
        ]

        for line in data:
            c.drawString(30*mm, y, line)
            y -= 10*mm

        c.save()

    buffer.seek(0)
    filename = f"Etiqueta_{'Producto' if tipo == 'producto' else 'Cliente'}_{muestra_id}.pdf"

    return StreamingResponse(
        buffer,
        media_type="application/pdf",
        headers={"Content-Disposition": f"attachment; filename={filename}"}
    )


@router.get("/ficha-diseno/{ot_id}")
async def generar_ficha_diseno(
    ot_id: int,
    current_user: dict = Depends(get_current_user)
):
    """
    Genera PDF de ficha de diseno con informacion tecnica de la OT.
    """
    ot = get_ot_data(ot_id)

    buffer = BytesIO()
    doc = SimpleDocTemplate(buffer, pagesize=A4, topMargin=20*mm, bottomMargin=20*mm)
    elements = []

    styles = getSampleStyleSheet()
    title_style = ParagraphStyle(
        'Title',
        parent=styles['Heading1'],
        fontSize=16,
        spaceAfter=12,
        alignment=1  # Center
    )
    header_style = ParagraphStyle(
        'Header',
        parent=styles['Heading2'],
        fontSize=12,
        spaceAfter=6,
        textColor=colors.HexColor('#1a1a2e')
    )
    normal_style = styles['Normal']

    # Titulo
    elements.append(Paragraph(f"FICHA DE DISENO - OT #{ot_id}", title_style))
    elements.append(Spacer(1, 10*mm))

    # Datos Comerciales
    elements.append(Paragraph("DATOS COMERCIALES", header_style))
    commercial_data = [
        ["Vendedor:", ot.get('vendedor_nombre', '-'), "Cliente:", ot.get('client_name', '-')],
        ["Material:", ot.get('material_codigo', '-'), "Descripcion:", ot.get('descripcion', '-')],
        ["CAD:", ot.get('cad', '-'), "Canal:", ot.get('canal_nombre', '-')],
    ]
    t = Table(commercial_data, colWidths=[80, 150, 80, 150])
    t.setStyle(TableStyle([
        ('FONTNAME', (0, 0), (-1, -1), 'Helvetica'),
        ('FONTSIZE', (0, 0), (-1, -1), 9),
        ('FONTNAME', (0, 0), (0, -1), 'Helvetica-Bold'),
        ('FONTNAME', (2, 0), (2, -1), 'Helvetica-Bold'),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
        ('GRID', (0, 0), (-1, -1), 0.5, colors.grey),
    ]))
    elements.append(t)
    elements.append(Spacer(1, 8*mm))

    # Caracteristicas
    elements.append(Paragraph("CARACTERISTICAS", header_style))
    char_data = [
        ["Carton:", ot.get('carton_nombre', '-'), "Estilo:", ot.get('estilo_nombre', '-')],
        ["Tipo Onda:", ot.get('tipo_onda_nombre', '-'), "Rayado:", ot.get('rayado_nombre', '-')],
        ["Proceso:", ot.get('proceso_nombre', '-'), "Armado:", ot.get('armado_nombre', '-')],
        ["Pegado:", ot.get('pegado_nombre', '-'), "Tipo Producto:", ot.get('product_type_nombre', '-')],
    ]
    t = Table(char_data, colWidths=[80, 150, 80, 150])
    t.setStyle(TableStyle([
        ('FONTNAME', (0, 0), (-1, -1), 'Helvetica'),
        ('FONTSIZE', (0, 0), (-1, -1), 9),
        ('FONTNAME', (0, 0), (0, -1), 'Helvetica-Bold'),
        ('FONTNAME', (2, 0), (2, -1), 'Helvetica-Bold'),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
        ('GRID', (0, 0), (-1, -1), 0.5, colors.grey),
    ]))
    elements.append(t)
    elements.append(Spacer(1, 8*mm))

    # Medidas
    elements.append(Paragraph("MEDIDAS", header_style))
    measures_data = [
        ["INTERIORES", "", "EXTERIORES", ""],
        ["Largo:", f"{ot.get('largo', 0)} mm", "Largo:", f"{ot.get('largo_exterior', 0)} mm"],
        ["Ancho:", f"{ot.get('ancho', 0)} mm", "Ancho:", f"{ot.get('ancho_exterior', 0)} mm"],
        ["Alto:", f"{ot.get('alto', 0)} mm", "Alto:", f"{ot.get('alto_exterior', 0)} mm"],
    ]
    t = Table(measures_data, colWidths=[80, 150, 80, 150])
    t.setStyle(TableStyle([
        ('FONTNAME', (0, 0), (-1, -1), 'Helvetica'),
        ('FONTSIZE', (0, 0), (-1, -1), 9),
        ('FONTNAME', (0, 0), (0, -1), 'Helvetica-Bold'),
        ('FONTNAME', (2, 0), (2, -1), 'Helvetica-Bold'),
        ('BACKGROUND', (0, 0), (1, 0), colors.HexColor('#e0e0e0')),
        ('BACKGROUND', (2, 0), (3, 0), colors.HexColor('#e0e0e0')),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
        ('GRID', (0, 0), (-1, -1), 0.5, colors.grey),
    ]))
    elements.append(t)
    elements.append(Spacer(1, 8*mm))

    # Colores
    if ot.get('colores'):
        elements.append(Paragraph("COLORES", header_style))
        color_data = [["#", "Color", "Codigo", "Consumo (g)"]]
        for i, color in enumerate(ot['colores'], 1):
            color_data.append([str(i), color.get('nombre', '-'), color.get('codigo', '-'), str(color.get('consumo', '-'))])
        t = Table(color_data, colWidths=[30, 150, 100, 80])
        t.setStyle(TableStyle([
            ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
            ('BACKGROUND', (0, 0), (-1, 0), colors.HexColor('#1a1a2e')),
            ('TEXTCOLOR', (0, 0), (-1, 0), colors.white),
            ('FONTSIZE', (0, 0), (-1, -1), 9),
            ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
            ('GRID', (0, 0), (-1, -1), 0.5, colors.grey),
        ]))
        elements.append(t)
        elements.append(Spacer(1, 8*mm))

    # Paletizado
    elements.append(Paragraph("PALETIZADO", header_style))
    pallet_data = [
        ["Planta:", ot.get('planta_nombre', '-'), "Tipo Palet:", ot.get('tipo_palet_nombre', '-')],
        ["Cajas/Palet:", str(ot.get('cajas_por_palet', '-')), "", ""],
    ]
    t = Table(pallet_data, colWidths=[80, 150, 80, 150])
    t.setStyle(TableStyle([
        ('FONTNAME', (0, 0), (-1, -1), 'Helvetica'),
        ('FONTSIZE', (0, 0), (-1, -1), 9),
        ('FONTNAME', (0, 0), (0, -1), 'Helvetica-Bold'),
        ('FONTNAME', (2, 0), (2, -1), 'Helvetica-Bold'),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
        ('GRID', (0, 0), (-1, -1), 0.5, colors.grey),
    ]))
    elements.append(t)

    # Footer
    elements.append(Spacer(1, 15*mm))
    elements.append(Paragraph(f"Generado: {datetime.now().strftime('%d/%m/%Y %H:%M')}", normal_style))

    doc.build(elements)
    buffer.seek(0)

    filename = f"Ficha_Diseno_OT_{ot_id}.pdf"

    return StreamingResponse(
        buffer,
        media_type="application/pdf",
        headers={"Content-Disposition": f"attachment; filename={filename}"}
    )


@router.get("/estudio-bench/{ot_id}")
async def generar_estudio_benchmarking(
    ot_id: int,
    current_user: dict = Depends(get_current_user)
):
    """
    Genera PDF de solicitud de estudio de benchmarking (laboratorio).
    """
    ot = get_ot_data(ot_id)

    buffer = BytesIO()
    doc = SimpleDocTemplate(buffer, pagesize=A4, topMargin=20*mm, bottomMargin=20*mm)
    elements = []

    styles = getSampleStyleSheet()
    title_style = ParagraphStyle(
        'Title',
        parent=styles['Heading1'],
        fontSize=16,
        spaceAfter=12,
        alignment=1
    )
    header_style = ParagraphStyle(
        'Header',
        parent=styles['Heading2'],
        fontSize=12,
        spaceAfter=6,
        textColor=colors.HexColor('#1a1a2e')
    )

    # Titulo
    elements.append(Paragraph("SOLICITUD DE ESTUDIO DE BENCHMARKING", title_style))
    elements.append(Paragraph("Laboratorio de Control de Calidad", styles['Normal']))
    elements.append(Spacer(1, 10*mm))

    # Datos de la OT
    elements.append(Paragraph("DATOS DE LA ORDEN DE TRABAJO", header_style))
    ot_data = [
        ["OT #:", str(ot_id), "Fecha Solicitud:", datetime.now().strftime('%d/%m/%Y')],
        ["Cliente:", ot.get('client_name', '-')[:40], "Solicitante:", ot.get('creador_nombre', '-')],
        ["Descripcion:", ot.get('descripcion', '-'), "", ""],
    ]
    t = Table(ot_data, colWidths=[80, 150, 80, 150])
    t.setStyle(TableStyle([
        ('FONTNAME', (0, 0), (-1, -1), 'Helvetica'),
        ('FONTSIZE', (0, 0), (-1, -1), 9),
        ('FONTNAME', (0, 0), (0, -1), 'Helvetica-Bold'),
        ('FONTNAME', (2, 0), (2, -1), 'Helvetica-Bold'),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
        ('GRID', (0, 0), (-1, -1), 0.5, colors.grey),
    ]))
    elements.append(t)
    elements.append(Spacer(1, 8*mm))

    # Identificacion de muestra
    elements.append(Paragraph("IDENTIFICACION DE MUESTRA", header_style))
    sample_data = [
        ["Material:", ot.get('material_codigo', '-'), "Carton:", ot.get('carton_nombre', '-')],
        ["CAD:", ot.get('cad', '-'), "Tipo Onda:", ot.get('tipo_onda_nombre', '-')],
        ["Estilo:", ot.get('estilo_nombre', '-'), "", ""],
    ]
    t = Table(sample_data, colWidths=[80, 150, 80, 150])
    t.setStyle(TableStyle([
        ('FONTNAME', (0, 0), (-1, -1), 'Helvetica'),
        ('FONTSIZE', (0, 0), (-1, -1), 9),
        ('FONTNAME', (0, 0), (0, -1), 'Helvetica-Bold'),
        ('FONTNAME', (2, 0), (2, -1), 'Helvetica-Bold'),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
        ('GRID', (0, 0), (-1, -1), 0.5, colors.grey),
    ]))
    elements.append(t)
    elements.append(Spacer(1, 8*mm))

    # Ensayos solicitados
    elements.append(Paragraph("ENSAYOS SOLICITADOS (marcar los requeridos)", header_style))
    tests = [
        ["[ ] BCT (Resistencia a la compresion)", "[ ] ECT (Edge Crush Test)"],
        ["[ ] Humedad", "[ ] Porosidad"],
        ["[ ] Espesor", "[ ] Cera"],
        ["[ ] Flexion", "[ ] Gramaje"],
        ["[ ] Composicion de Papeles", "[ ] Cobb"],
        ["[ ] Medidas", "[ ] Impresion"],
        ["[ ] Mullen", "[ ] FCT"],
    ]
    t = Table(tests, colWidths=[230, 230])
    t.setStyle(TableStyle([
        ('FONTNAME', (0, 0), (-1, -1), 'Helvetica'),
        ('FONTSIZE', (0, 0), (-1, -1), 10),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 8),
    ]))
    elements.append(t)
    elements.append(Spacer(1, 10*mm))

    # Observaciones
    elements.append(Paragraph("OBSERVACIONES", header_style))
    elements.append(Spacer(1, 5*mm))
    obs_table = Table([
        [""],
        [""],
        [""],
    ], colWidths=[460])
    obs_table.setStyle(TableStyle([
        ('BOX', (0, 0), (-1, -1), 1, colors.black),
        ('INNERGRID', (0, 0), (-1, -1), 0.5, colors.grey),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 20),
    ]))
    elements.append(obs_table)
    elements.append(Spacer(1, 15*mm))

    # Firmas
    signatures = [
        ["___________________________", "___________________________"],
        ["Solicitante", "Recepcion Laboratorio"],
        ["", ""],
        ["Fecha: __/__/____", "Fecha: __/__/____"],
    ]
    t = Table(signatures, colWidths=[230, 230])
    t.setStyle(TableStyle([
        ('FONTNAME', (0, 0), (-1, -1), 'Helvetica'),
        ('FONTSIZE', (0, 0), (-1, -1), 9),
        ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
    ]))
    elements.append(t)

    doc.build(elements)
    buffer.seek(0)

    filename = f"Estudio_Benchmarking_OT_{ot_id}.pdf"

    return StreamingResponse(
        buffer,
        media_type="application/pdf",
        headers={"Content-Disposition": f"attachment; filename={filename}"}
    )


@router.get("/cotizacion/{cotizacion_id}")
async def generar_cotizacion_pdf(
    cotizacion_id: int,
    current_user: dict = Depends(get_current_user)
):
    """
    Genera PDF de cotizacion comercial.
    """
    conn = get_db_connection()
    try:
        with conn.cursor() as cursor:
            # Obtener cotizacion
            cursor.execute("""
                SELECT
                    cot.*,
                    c.nombre as client_name,
                    c.rut as client_rut,
                    c.direccion as client_direccion,
                    CONCAT(u.name, ' ', u.last_name) as vendedor_nombre,
                    u.email as vendedor_email,
                    e.nombre as estado_nombre
                FROM cotizacions cot
                LEFT JOIN clients c ON cot.client_id = c.id
                LEFT JOIN users u ON cot.vendedor_id = u.id
                LEFT JOIN estado_cotizacions e ON cot.estado_id = e.id
                WHERE cot.id = %s
            """, (cotizacion_id,))
            cot = cursor.fetchone()
            if not cot:
                raise HTTPException(status_code=404, detail="Cotizacion no encontrada")

            # Obtener detalles
            cursor.execute("""
                SELECT
                    dc.*,
                    pt.nombre as product_type_nombre
                FROM detalle_cotizacions dc
                LEFT JOIN product_types pt ON dc.product_type_id = pt.id
                WHERE dc.cotizacion_id = %s
                ORDER BY dc.id
            """, (cotizacion_id,))
            detalles = cursor.fetchall()
    finally:
        conn.close()

    buffer = BytesIO()
    doc = SimpleDocTemplate(buffer, pagesize=letter, topMargin=20*mm, bottomMargin=20*mm)
    elements = []

    styles = getSampleStyleSheet()
    title_style = ParagraphStyle(
        'Title',
        parent=styles['Heading1'],
        fontSize=18,
        spaceAfter=12,
        alignment=1
    )
    header_style = ParagraphStyle(
        'Header',
        parent=styles['Heading2'],
        fontSize=12,
        spaceAfter=6,
        textColor=colors.HexColor('#1a1a2e')
    )

    # Titulo
    elements.append(Paragraph(f"COTIZACION #{cotizacion_id}", title_style))
    elements.append(Paragraph(f"Fecha: {datetime.now().strftime('%d/%m/%Y')}", styles['Normal']))
    elements.append(Spacer(1, 10*mm))

    # Datos del cliente
    elements.append(Paragraph("DATOS DEL CLIENTE", header_style))
    client_data = [
        ["Cliente:", cot.get('client_name', '-')],
        ["RUT:", cot.get('client_rut', '-')],
        ["Direccion:", cot.get('client_direccion', '-')],
    ]
    t = Table(client_data, colWidths=[100, 360])
    t.setStyle(TableStyle([
        ('FONTNAME', (0, 0), (0, -1), 'Helvetica-Bold'),
        ('FONTSIZE', (0, 0), (-1, -1), 10),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
    ]))
    elements.append(t)
    elements.append(Spacer(1, 8*mm))

    # Vendedor
    elements.append(Paragraph("EJECUTIVO COMERCIAL", header_style))
    elements.append(Paragraph(f"{cot.get('vendedor_nombre', '-')} - {cot.get('vendedor_email', '-')}", styles['Normal']))
    elements.append(Spacer(1, 8*mm))

    # Detalles
    if detalles:
        elements.append(Paragraph("DETALLE DE PRODUCTOS", header_style))
        detail_header = ["#", "Tipo", "Cantidad", "Descripcion"]
        detail_data = [detail_header]
        for i, det in enumerate(detalles, 1):
            detail_data.append([
                str(i),
                det.get('product_type_nombre', '-'),
                str(det.get('cantidad', 0)),
                str(det.get('descripcion', '-'))[:40] if det.get('descripcion') else '-'
            ])

        t = Table(detail_data, colWidths=[30, 100, 80, 250])
        t.setStyle(TableStyle([
            ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
            ('BACKGROUND', (0, 0), (-1, 0), colors.HexColor('#1a1a2e')),
            ('TEXTCOLOR', (0, 0), (-1, 0), colors.white),
            ('FONTSIZE', (0, 0), (-1, -1), 9),
            ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
            ('GRID', (0, 0), (-1, -1), 0.5, colors.grey),
        ]))
        elements.append(t)
        elements.append(Spacer(1, 8*mm))

    # Estado
    elements.append(Paragraph(f"Estado: {cot.get('estado_nombre', '-')}", styles['Normal']))

    # Footer
    elements.append(Spacer(1, 20*mm))
    elements.append(Paragraph("Este documento es una cotizacion comercial y no constituye un compromiso de venta.", styles['Normal']))

    doc.build(elements)
    buffer.seek(0)

    filename = f"Cotizacion_{cotizacion_id}.pdf"

    return StreamingResponse(
        buffer,
        media_type="application/pdf",
        headers={"Content-Disposition": f"attachment; filename={filename}"}
    )
