#!/usr/bin/env python3
"""
Script para poblar tablas de referencia de dropdowns vacíos en Railway.
Ejecutar: python scripts/populate_dropdowns.py
"""
import pymysql
import sys

# Configuración Railway MySQL
DB_CONFIG = {
    "host": "metro.proxy.rlwy.net",
    "port": 18336,
    "user": "root",
    "password": "KDLBLgaWqIyWHVISVXABqbuEAuSyHJrj",
    "database": "envases_ot",
    "charset": "utf8mb4"
}

SQL_STATEMENTS = [
    # 1. PROCESSES (Sección 11 - Proceso)
    """
    INSERT INTO processes (id, descripcion, type, active, orden) VALUES
    (1, 'Flexo', 'EV', 1, 1),
    (2, 'Diecutter', 'EV', 1, 2),
    (3, 'Sin Proceso', 'EV', 1, 3)
    ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion), active=1
    """,

    # 2. ARMADOS (Sección 11 - Armado)
    """
    INSERT INTO armados (id, descripcion, active) VALUES
    (1, 'Máquina', 1),
    (2, 'Con y Sin Pegamento', 1),
    (3, 'Manual', 1)
    ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion), active=1
    """,

    # 3. PEGADOS (Sección 11 - Tipo Pegado)
    """
    INSERT INTO pegados (id, codigo, descripcion, active) VALUES
    (1, 'SI', 'Con Pegado', 1),
    (2, 'NO', 'Sin Pegado', 1)
    ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion), active=1
    """,

    # 4. PRODUCT_TYPES (Sección 13 - Tipo Producto para cotizaciones)
    """
    INSERT INTO product_types (id, codigo, descripcion, active) VALUES
    (1, 'UV', 'U.Vta/Set', 1),
    (2, 'SS', 'Subset', 1)
    ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion), active=1
    """,

    # 5. ENVASES (Sección 13 - Envase Primario)
    """
    INSERT INTO envases (id, descripcion, active) VALUES
    (1, 'Granel', 1),
    (2, 'Pote', 1),
    (3, 'Bolsa', 1),
    (4, 'Bandeja', 1),
    (5, 'Botella', 1),
    (6, 'Lata', 1),
    (7, 'Tetrapack', 1),
    (8, 'Sachet', 1),
    (9, 'Caja', 1),
    (10, 'Otro', 1)
    ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion), active=1
    """,

    # 6. FOOD_TYPES (Sección 13 - Tipo Alimento)
    """
    INSERT INTO food_types (id, descripcion, deleted) VALUES
    (1, 'Frutas y Verduras', 0),
    (2, 'Carnes y Pescados', 0),
    (3, 'Lácteos', 0),
    (4, 'Bebidas', 0),
    (5, 'Congelados', 0),
    (6, 'Secos y Granos', 0),
    (7, 'Conservas', 0),
    (8, 'No Alimentario', 0)
    ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion)
    """,

    # 7. TARGET_MARKET (Sección 13 - Mercado Destino)
    """
    INSERT INTO target_market (id, descripcion, deleted) VALUES
    (1, 'Nacional', 0),
    (2, 'Europeo', 0),
    (3, 'Norteamericano', 0),
    (4, 'Asiático', 0),
    (5, 'Latinoamericano', 0),
    (6, 'Otro', 0)
    ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion)
    """,

    # 8. TRANSPORTATION_WAY (Sección 13 - Medio Transporte)
    """
    INSERT INTO transportation_way (id, descripcion, deleted) VALUES
    (1, 'Terrestre', 0),
    (2, 'Marítimo', 0),
    (3, 'Aéreo', 0),
    (4, 'Multimodal', 0)
    ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion)
    """,

    # 9. EXPECTED_USE (Sección 13 - Uso Previsto)
    """
    INSERT INTO expected_use (id, descripcion, deleted) VALUES
    (1, 'Exportación', 0),
    (2, 'Mercado Nacional', 0),
    (3, 'Retail', 0),
    (4, 'Industrial', 0)
    ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion)
    """,

    # 10. RECYCLED_USE (Sección 13 - Uso Reciclado)
    """
    INSERT INTO recycled_use (id, descripcion, deleted) VALUES
    (1, 'Sí', 0),
    (2, 'No', 0),
    (3, 'Parcial', 0)
    ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion)
    """,

    # 11. CLASS_SUBSTANCE_PACKED (Sección 13 - Clase Sustancia)
    """
    INSERT INTO class_substance_packed (id, descripcion, deleted) VALUES
    (1, 'Sólido', 0),
    (2, 'Líquido', 0),
    (3, 'Semisólido', 0),
    (4, 'Polvo', 0),
    (5, 'Granulado', 0)
    ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion)
    """,

    # 12. MAQUILA_SERVICIOS (Sección 11 - Servicios Maquila)
    """
    INSERT INTO maquila_servicios (id, servicio, precio_clp_caja, active) VALUES
    (1, 'Desgaje Cabezal Par', 7, 1),
    (2, 'Desgaje Unitario', 3, 1),
    (3, 'PM CJ Chica (0-30 cm)', 33, 1),
    (4, 'PM CJ Mediana (30-70 cm)', 41, 1),
    (5, 'PM CJ Grande (70-100 cm)', 84, 1),
    (6, 'Paletizado Placas', 15, 1),
    (7, 'Armado y Paletizado Tabiques Simple', 42, 1),
    (8, 'Armado y Paletizado Tabiques Doble', 55, 1)
    ON DUPLICATE KEY UPDATE servicio=VALUES(servicio), active=1
    """
]


def main():
    print("Conectando a Railway MySQL...")
    try:
        conn = pymysql.connect(**DB_CONFIG)
        print("Conexión exitosa.")

        cursor = conn.cursor()

        for i, sql in enumerate(SQL_STATEMENTS, 1):
            try:
                cursor.execute(sql)
                print(f"[{i}/{len(SQL_STATEMENTS)}] OK - {sql.strip().split()[2]} insertados/actualizados")
            except Exception as e:
                print(f"[{i}/{len(SQL_STATEMENTS)}] ERROR: {e}")

        conn.commit()
        print("\nTodas las inserciones completadas exitosamente.")

        # Verificar conteos
        print("\n=== Verificación de datos ===")
        tables = [
            ("processes", "WHERE type='EV' AND active=1"),
            ("armados", "WHERE active=1"),
            ("pegados", "WHERE active=1"),
            ("product_types", "WHERE active=1"),
            ("envases", "WHERE active=1"),
            ("food_types", "WHERE deleted=0"),
            ("target_market", "WHERE deleted=0"),
            ("transportation_way", "WHERE deleted=0"),
            ("expected_use", "WHERE deleted=0"),
            ("recycled_use", "WHERE deleted=0"),
            ("class_substance_packed", "WHERE deleted=0"),
            ("maquila_servicios", "WHERE active=1"),
        ]

        for table, where in tables:
            try:
                cursor.execute(f"SELECT COUNT(*) FROM {table} {where}")
                count = cursor.fetchone()[0]
                print(f"  {table}: {count} registros")
            except Exception as e:
                print(f"  {table}: ERROR - {e}")

        cursor.close()
        conn.close()
        print("\nScript finalizado.")

    except Exception as e:
        print(f"Error de conexión: {e}")
        sys.exit(1)


if __name__ == "__main__":
    main()
