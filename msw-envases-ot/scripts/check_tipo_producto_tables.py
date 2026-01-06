# -*- coding: utf-8 -*-
"""
Script para verificar las tablas relacionadas con TIPO PRODUCTO
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

    print("=" * 80)
    print("VERIFICACION DE TABLAS PARA TIPO PRODUCTO Y SECCION 13")
    print("=" * 80)

    # Buscar tablas relacionadas
    tables_to_check = [
        'product_types', 'product_type', 'tipos_producto', 'tipo_producto',
        'food_types', 'food_type', 'tipos_alimento', 'tipo_alimento',
        'expected_uses', 'expected_use', 'uso_previsto', 'usos_previstos',
        'recycled_uses', 'recycled_use', 'uso_reciclado',
        'class_substance', 'class_substances', 'clase_sustancia',
        'transportation', 'transportation_ways', 'medio_transporte',
        'envases', 'envase', 'primary_packages',
        'target_markets', 'mercado_destino',
        'developing_product_types',  # Posible nombre alternativo
        'product_categories', 'categorias_producto'
    ]

    print("\n1. BUSCANDO TABLAS EN LA BASE DE DATOS...")
    cursor.execute("SHOW TABLES")
    all_tables = [t[list(t.keys())[0]] for t in cursor.fetchall()]

    found_tables = []
    keywords = ['product', 'food', 'expected', 'recycled', 'substance',
                'transport', 'envase', 'market', 'destino', 'developing',
                'categoria', 'tipo', 'alimento', 'uso']

    for table in all_tables:
        if any(kw in table.lower() for kw in keywords):
            found_tables.append(table)

    print(f"\nTablas relacionadas encontradas ({len(found_tables)}):")
    for t in sorted(found_tables):
        print(f"   - {t}")

    # Verificar contenido de cada tabla encontrada
    print("\n2. CONTENIDO DE TABLAS RELEVANTES...")

    for table in sorted(found_tables):
        try:
            cursor.execute(f"SELECT * FROM {table} LIMIT 15")
            rows = cursor.fetchall()
            cursor.execute(f"SELECT COUNT(*) as total FROM {table}")
            total = cursor.fetchone()['total']

            print(f"\n=== TABLA: {table} ({total} registros) ===")
            if rows:
                # Mostrar columnas
                columns = list(rows[0].keys())
                print(f"   Columnas: {columns}")
                print("   Datos:")
                for row in rows[:10]:
                    # Mostrar solo campos relevantes
                    relevant = {k: v for k, v in row.items()
                               if k in ['id', 'nombre', 'descripcion', 'name', 'description', 'active', 'activo']}
                    print(f"      {relevant}")
                if total > 10:
                    print(f"      ... y {total - 10} registros más")
        except Exception as e:
            print(f"\n=== TABLA: {table} - Error: {e} ===")

    # Buscar en work_orders las columnas relacionadas
    print("\n3. COLUMNAS DE work_orders PARA SECCION 13...")
    cursor.execute("DESCRIBE work_orders")
    columns = cursor.fetchall()

    section13_keywords = ['product_type', 'food_type', 'expected_use', 'recycled',
                          'class_substance', 'transport', 'envase', 'market',
                          'autosoportante', 'pallet', 'developing']

    for col in columns:
        if any(kw in col['Field'].lower() for kw in section13_keywords):
            print(f"   - {col['Field']}: {col['Type']}")

    # Verificar el controlador Laravel para ver qué campo usa
    print("\n4. VERIFICANDO VALORES DISTINTOS EN work_orders...")
    section13_cols = ['product_type_developing_id', 'food_type_id', 'expected_use_id',
                      'recycled_use_id', 'class_substance_packed_id', 'transportation_way_id',
                      'envase_id', 'target_market_id']

    for col in section13_cols:
        try:
            cursor.execute(f"SELECT DISTINCT {col} FROM work_orders WHERE {col} IS NOT NULL ORDER BY {col} LIMIT 10")
            values = cursor.fetchall()
            if values:
                vals = [v[col] for v in values]
                print(f"   {col}: {vals}")
        except Exception as e:
            print(f"   {col}: Error - {e}")

    conn.close()

if __name__ == "__main__":
    main()
