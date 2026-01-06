# -*- coding: utf-8 -*-
"""
Script para verificar processes y sentido_armado
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
    print("VERIFICACION DE PROCESSES Y SENTIDO ARMADO")
    print("=" * 60)

    # Tabla processes
    print("\n1. TABLA: processes")
    cursor.execute("SELECT * FROM processes ORDER BY id")
    rows = cursor.fetchall()
    for row in rows:
        print(f"   id={row['id']}: {row.get('descripcion', row.get('nombre', row.get('name', '')))} (active={row.get('active', 'N/A')})")

    # Estructura de processes
    print("\n2. ESTRUCTURA processes:")
    cursor.execute("DESCRIBE processes")
    for col in cursor.fetchall():
        print(f"   {col['Field']}: {col['Type']}")

    # Buscar tabla sentido o direccion armado
    print("\n3. BUSCANDO TABLAS 'sentido' o 'direccion'...")
    cursor.execute("SHOW TABLES LIKE '%sentido%'")
    for t in cursor.fetchall():
        print(f"   {t}")
    cursor.execute("SHOW TABLES LIKE '%direccion%'")
    for t in cursor.fetchall():
        print(f"   {t}")
    cursor.execute("SHOW TABLES LIKE '%direction%'")
    for t in cursor.fetchall():
        print(f"   {t}")

    # Verificar valores de sentido_armado en work_orders con JOIN
    print("\n4. VALORES sentido_armado EN work_orders (con frecuencia):")
    cursor.execute("""
        SELECT sentido_armado, COUNT(*) as count
        FROM work_orders
        WHERE sentido_armado IS NOT NULL
        GROUP BY sentido_armado
        ORDER BY sentido_armado
    """)
    for row in cursor.fetchall():
        print(f"   valor={row['sentido_armado']}: {row['count']} registros")

    # Buscar en codigo Laravel
    print("\n5. BUSCAR MIGRACIONES O SEEDERS PARA SENTIDO ARMADO...")
    print("   (Necesario revisar codigo Laravel)")

    # Verificar maquila_servicios
    print("\n6. TABLA: maquila_servicios")
    cursor.execute("SELECT * FROM maquila_servicios LIMIT 10")
    rows = cursor.fetchall()
    for row in rows:
        print(f"   {row}")

    conn.close()

if __name__ == "__main__":
    main()
