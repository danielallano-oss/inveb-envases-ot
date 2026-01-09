#!/usr/bin/env python3
"""
Script para insertar datos en pallet_qas y pallet_tag_formats en Railway.
"""
import pymysql

# Conexión a Railway MySQL
connection = pymysql.connect(
    host='metro.proxy.rlwy.net',
    port=18336,
    user='root',
    password='KDLBLgaWqIyWHVISVXABqbuEAuSyHJrj',
    database='envases_ot',
    charset='utf8mb4',
    cursorclass=pymysql.cursors.DictCursor
)

try:
    with connection.cursor() as cursor:
        # Insertar pallet_qas (Certificado de Calidad)
        print("Insertando datos en pallet_qas...")
        cursor.execute("""
            INSERT INTO pallet_qas (id, descripcion, active, created_at, updated_at) VALUES
            (1, 'Certificado Estándar', 1, NOW(), NOW()),
            (2, 'Certificado Premium', 1, NOW(), NOW()),
            (3, 'Sin Certificado', 1, NOW(), NOW())
            ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion), active=1
        """)
        print(f"  -> {cursor.rowcount} filas afectadas")

        # Insertar pallet_tag_formats (Formato Etiqueta Pallet)
        print("Insertando datos en pallet_tag_formats...")
        cursor.execute("""
            INSERT INTO pallet_tag_formats (id, descripcion, active, created_at, updated_at) VALUES
            (1, 'Etiqueta Simple', 1, NOW(), NOW()),
            (2, 'Etiqueta con Código de Barras', 1, NOW(), NOW()),
            (3, 'Etiqueta QR', 1, NOW(), NOW()),
            (4, 'Sin Etiqueta', 1, NOW(), NOW())
            ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion), active=1
        """)
        print(f"  -> {cursor.rowcount} filas afectadas")

        connection.commit()
        print("\n✅ Datos insertados correctamente!")

        # Verificar los datos
        print("\n=== Verificación ===")
        cursor.execute("SELECT * FROM pallet_qas WHERE active = 1")
        print("\npallet_qas:")
        for row in cursor.fetchall():
            print(f"  {row['id']}: {row['descripcion']}")

        cursor.execute("SELECT * FROM pallet_tag_formats WHERE active = 1")
        print("\npallet_tag_formats:")
        for row in cursor.fetchall():
            print(f"  {row['id']}: {row['descripcion']}")

finally:
    connection.close()
