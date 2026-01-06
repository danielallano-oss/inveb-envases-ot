# -*- coding: utf-8 -*-
"""
Script para verificar el campo OC en la base de datos
"""
import pymysql

def main():
    conn = pymysql.connect(
        host='localhost',
        port=3307,
        user='root',
        password='root',
        database='envases_ot',
        cursorclass=pymysql.cursors.DictCursor
    )

    try:
        with conn.cursor() as cursor:
            # 1. Ver estructura de la tabla work_orders
            print("=== Columnas de work_orders que contienen 'oc' o 'orden' ===")
            cursor.execute("""
                SELECT COLUMN_NAME, DATA_TYPE, COLUMN_TYPE, IS_NULLABLE
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = 'envases_ot'
                AND TABLE_NAME = 'work_orders'
                AND (COLUMN_NAME LIKE '%oc%' OR COLUMN_NAME LIKE '%orden%')
            """)
            columns = cursor.fetchall()
            for col in columns:
                print(f"  {col['COLUMN_NAME']}: {col['COLUMN_TYPE']} (nullable: {col['IS_NULLABLE']})")

            if not columns:
                print("  No se encontraron columnas con 'oc' u 'orden'")

            # 2. Buscar tabla de ordenes de compra
            print("\n=== Tablas que contienen 'orden' o 'oc' ===")
            cursor.execute("""
                SELECT TABLE_NAME
                FROM INFORMATION_SCHEMA.TABLES
                WHERE TABLE_SCHEMA = 'envases_ot'
                AND (TABLE_NAME LIKE '%orden%' OR TABLE_NAME LIKE '%oc%' OR TABLE_NAME LIKE '%compra%')
            """)
            tables = cursor.fetchall()
            for t in tables:
                print(f"  {t['TABLE_NAME']}")

            if not tables:
                print("  No se encontraron tablas relacionadas")

            # 3. Ver valores de OC en work_orders existentes
            print("\n=== Valores de campos 'oc' en work_orders (muestra) ===")
            cursor.execute("""
                SELECT id, oc, oc_file
                FROM work_orders
                WHERE oc IS NOT NULL
                LIMIT 5
            """)
            rows = cursor.fetchall()
            for row in rows:
                print(f"  OT {row['id']}: oc={row['oc']}, oc_file={row['oc_file']}")

            if not rows:
                print("  No hay registros con oc != NULL")

            # 4. Ver valores distintos de OC
            print("\n=== Valores distintos de 'oc' ===")
            cursor.execute("SELECT DISTINCT oc FROM work_orders WHERE oc IS NOT NULL LIMIT 10")
            values = cursor.fetchall()
            for v in values:
                print(f"  '{v['oc']}'")

    finally:
        conn.close()

if __name__ == "__main__":
    main()
