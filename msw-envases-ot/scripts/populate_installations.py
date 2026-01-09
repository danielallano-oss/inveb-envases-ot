#!/usr/bin/env python3
"""
Script para poblar instalaciones y contactos de prueba para clientes existentes.
Ejecutar: python scripts/populate_installations.py
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


def connect():
    """Conecta a la base de datos MySQL"""
    try:
        conn = pymysql.connect(**DB_CONFIG, cursorclass=pymysql.cursors.DictCursor)
        print("✓ Conexión exitosa a MySQL Railway")
        return conn
    except Exception as e:
        print(f"✗ Error de conexión: {e}")
        sys.exit(1)


def check_and_populate_installations(conn):
    """Verifica y pobla instalaciones para clientes existentes"""
    with conn.cursor() as cursor:
        # Primero, ver cuántos clientes existen
        cursor.execute("SELECT COUNT(*) as total FROM clients")
        result = cursor.fetchone()
        print(f"\n→ Total de clientes: {result['total']}")

        # Ver cuántas instalaciones existen
        cursor.execute("SELECT COUNT(*) as total FROM installations")
        result = cursor.fetchone()
        print(f"→ Total de instalaciones: {result['total']}")

        # Listar primeros 10 clientes
        cursor.execute("""
            SELECT id, rut, nombre
            FROM clients
            ORDER BY id
            LIMIT 10
        """)
        clients = cursor.fetchall()
        print(f"\n→ Primeros 10 clientes:")
        for c in clients:
            print(f"   - ID {c['id']}: {c['nombre']} ({c['rut']})")

        # Verificar instalaciones por cliente
        cursor.execute("""
            SELECT c.id, c.nombre, COUNT(i.id) as instalaciones
            FROM clients c
            LEFT JOIN installations i ON i.client_id = c.id
            GROUP BY c.id, c.nombre
            ORDER BY c.id
            LIMIT 20
        """)
        client_installs = cursor.fetchall()
        print(f"\n→ Instalaciones por cliente:")
        for c in client_installs:
            print(f"   - Cliente {c['id']} ({c['nombre']}): {c['instalaciones']} instalaciones")

        # Si hay clientes sin instalaciones, crear algunas de prueba
        clients_without_installs = [c for c in client_installs if c['instalaciones'] == 0]
        if clients_without_installs:
            print(f"\n→ Creando instalaciones para clientes sin ellas...")
            for client in clients_without_installs[:5]:  # Solo para los primeros 5
                create_installation_for_client(cursor, client['id'], client['nombre'])
            conn.commit()
            print("✓ Instalaciones creadas exitosamente")
        else:
            print("\n✓ Todos los clientes ya tienen instalaciones")


def create_installation_for_client(cursor, client_id, client_name):
    """Crea una instalación de prueba para un cliente"""
    # Crear instalación principal
    cursor.execute("""
        INSERT INTO installations (
            client_id, nombre, direccion_contacto,
            nombre_contacto, email_contacto, phone_contacto,
            nombre_contacto_2, email_contacto_2, phone_contacto_2,
            active, created_at, updated_at
        ) VALUES (
            %s, %s, %s,
            %s, %s, %s,
            %s, %s, %s,
            1, NOW(), NOW()
        )
    """, (
        client_id,
        f"Planta Principal - {client_name[:30]}",
        f"Av. Industrial 123, Santiago",
        f"Contacto Principal",
        f"contacto{client_id}@empresa.cl",
        "+56 9 1234 5678",
        f"Contacto Secundario",
        f"contacto2_{client_id}@empresa.cl",
        "+56 9 8765 4321"
    ))
    print(f"   ✓ Instalación creada para cliente {client_id} ({client_name})")

    # Crear segunda instalación opcional
    cursor.execute("""
        INSERT INTO installations (
            client_id, nombre, direccion_contacto,
            nombre_contacto, email_contacto, phone_contacto,
            active, created_at, updated_at
        ) VALUES (
            %s, %s, %s,
            %s, %s, %s,
            1, NOW(), NOW()
        )
    """, (
        client_id,
        f"Bodega Norte - {client_name[:30]}",
        f"Camino Norte 456, Colina",
        f"Encargado Bodega",
        f"bodega{client_id}@empresa.cl",
        "+56 9 5555 5555"
    ))


def check_contacts_table(conn):
    """Verifica la tabla de contactos"""
    with conn.cursor() as cursor:
        # Ver estructura de client_contacts
        cursor.execute("SHOW TABLES LIKE 'client_contacts'")
        if cursor.fetchone():
            cursor.execute("SELECT COUNT(*) as total FROM client_contacts")
            result = cursor.fetchone()
            print(f"\n→ Total de contactos en client_contacts: {result['total']}")

            if result['total'] == 0:
                print("→ Creando contactos de prueba...")
                # Obtener IDs de clientes
                cursor.execute("SELECT id FROM clients LIMIT 5")
                clients = cursor.fetchall()
                for c in clients:
                    cursor.execute("""
                        INSERT INTO client_contacts (client_id, nombre, email, telefono, created_at, updated_at)
                        VALUES (%s, %s, %s, %s, NOW(), NOW())
                    """, (c['id'], f"Contacto General {c['id']}", f"general{c['id']}@test.cl", "+56 9 0000 0000"))
                conn.commit()
                print("✓ Contactos creados")
        else:
            print("\n→ Tabla client_contacts no existe")


def main():
    print("=" * 60)
    print("POBLADO DE INSTALACIONES Y CONTACTOS - INVEB OT")
    print("=" * 60)

    conn = connect()
    try:
        check_and_populate_installations(conn)
        check_contacts_table(conn)
        print("\n" + "=" * 60)
        print("PROCESO COMPLETADO")
        print("=" * 60)
    except Exception as e:
        print(f"\n✗ Error: {e}")
        import traceback
        traceback.print_exc()
    finally:
        conn.close()


if __name__ == "__main__":
    main()
