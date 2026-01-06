# -*- coding: utf-8 -*-
"""
Script para verificar las tablas de Terminaciones (Seccion 11)
Campos: Proceso, Tipo Pegado, Armado, Sentido de Armado
"""
import sys
import io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

import pymysql

def main():
    conn = pymysql.connect(
        host='localhost',
        port=3307,
        user='root',
        password='root',
        database='envases_ot',
        charset='utf8mb4',
        cursorclass=pymysql.cursors.DictCursor
    )

    cursor = conn.cursor()

    print("=" * 60)
    print("VERIFICACION DE TABLAS PARA SECCION 11 - TERMINACIONES")
    print("=" * 60)

    # Buscar tablas relacionadas con terminaciones
    tables_to_check = [
        'sentido_armado', 'sentido_armados',
        'proceso', 'procesos',
        'tipo_pegado', 'tipos_pegado', 'tipo_pegados',
        'armado', 'armados',
        'assembly_directions',  # Possible English name
        'processes',
        'glue_types',
        'assemblies'
    ]

    print("\n1. BUSCANDO TABLAS RELACIONADAS CON TERMINACIONES...")
    cursor.execute("SHOW TABLES")
    all_tables = [t[list(t.keys())[0]] for t in cursor.fetchall()]

    found_tables = []
    for table in all_tables:
        if any(keyword in table.lower() for keyword in ['sentido', 'armado', 'proceso', 'pegado', 'assembly', 'glue', 'terminacion']):
            found_tables.append(table)
            print(f"   - {table}")

    # Buscar en work_orders las columnas relacionadas
    print("\n2. COLUMNAS DE work_orders RELACIONADAS CON TERMINACIONES...")
    cursor.execute("DESCRIBE work_orders")
    columns = cursor.fetchall()
    terminaciones_cols = ['proceso', 'tipo_pegado', 'armado', 'sentido_armado']
    for col in columns:
        if any(tc in col['Field'].lower() for tc in terminaciones_cols):
            print(f"   - {col['Field']}: {col['Type']}")

    # Verificar cada tabla encontrada
    print("\n3. CONTENIDO DE TABLAS ENCONTRADAS...")
    for table in found_tables:
        try:
            cursor.execute(f"SELECT * FROM {table} LIMIT 20")
            rows = cursor.fetchall()
            if rows:
                print(f"\n   === TABLA: {table} ({len(rows)} registros) ===")
                for row in rows:
                    print(f"   {row}")
        except Exception as e:
            print(f"   Error en {table}: {e}")

    # Verificar valores distintos en work_orders
    print("\n4. VALORES DISTINTOS EN work_orders...")
    for col in terminaciones_cols:
        try:
            cursor.execute(f"SELECT DISTINCT {col} FROM work_orders WHERE {col} IS NOT NULL ORDER BY {col} LIMIT 20")
            values = cursor.fetchall()
            if values:
                print(f"\n   {col}: {[v[col] for v in values]}")
        except Exception as e:
            print(f"   {col}: Error - {e}")

    # Buscar tablas con nombres similares usando LIKE
    print("\n5. TODAS LAS TABLAS DE LA BASE DE DATOS...")
    for table in sorted(all_tables):
        print(f"   - {table}")

    conn.close()

if __name__ == "__main__":
    main()
