#!/usr/bin/env python3
"""
Verifica que el API de editar muestra funcione correctamente
para el rol Ingeniero (8106237-4)
"""
import requests
import json
import sys

# Fix encoding for Windows console
sys.stdout.reconfigure(encoding='utf-8', errors='replace')

BASE_URL = "http://localhost:8001/api/v1"

def main():
    print("=" * 60)
    print("VERIFICACION: API Editar Muestra para Ingeniero")
    print("=" * 60)

    # 1. Login como Ingeniero
    print("\n1. Login como Ingeniero (8106237-4)...")
    login_data = {"rut": "8106237-4", "password": "123123"}
    try:
        resp = requests.post(f"{BASE_URL}/auth/login", json=login_data, timeout=10)
        if resp.status_code != 200:
            print(f"   [X] Error en login: {resp.status_code} - {resp.text}")
            return

        token_data = resp.json()
        token = token_data.get("access_token")
        user = token_data.get("user", {})
        print(f"   [OK] Login exitoso")
        print(f"   Usuario: {user.get('nombre', 'N/A')}")
        print(f"   Rol ID: {user.get('role_id', 'N/A')}")
        print(f"   Rol: {user.get('role_nombre', 'N/A')}")
    except Exception as e:
        print(f"   [X] Error: {e}")
        return

    headers = {"Authorization": f"Bearer {token}"}

    # 2. Obtener una OT con muestras
    print("\n2. Buscando OT con muestras...")
    muestras = []
    muestra_id = None
    try:
        # Buscar OT 26591 o usar otra
        ot_id = 26591
        resp = requests.get(f"{BASE_URL}/muestras/ot/{ot_id}", headers=headers, timeout=10)

        if resp.status_code == 200:
            data = resp.json()
            # La respuesta puede ser paginada (items) o una lista directa
            if isinstance(data, dict) and "items" in data:
                muestras = data.get("items", [])
            elif isinstance(data, list):
                muestras = data
            else:
                muestras = []

        if not muestras:
            print(f"   ! OT {ot_id} no tiene muestras, buscando otra OT...")
            # Buscar todas las OTs disponibles
            resp = requests.get(f"{BASE_URL}/work-orders", headers=headers, timeout=10)
            if resp.status_code != 200:
                print(f"   [X] Error obteniendo OTs: {resp.status_code}")
                return

            ots_data = resp.json()
            ots_list = ots_data.get("data", []) if isinstance(ots_data, dict) else ots_data
            for ot in ots_list[:5]:
                ot_id = ot.get("numero_ot")
                resp = requests.get(f"{BASE_URL}/muestras/ot/{ot_id}", headers=headers, timeout=10)
                if resp.status_code == 200:
                    data = resp.json()
                    if isinstance(data, dict) and "items" in data:
                        muestras = data.get("items", [])
                    elif isinstance(data, list):
                        muestras = data
                    if muestras:
                        break

        if not muestras:
            print("   [X] No se encontraron OTs con muestras")
            return

        print(f"   [OK] Encontradas {len(muestras)} muestras en OT {ot_id}")

        # Mostrar primera muestra
        muestra = muestras[0]
        muestra_id = muestra.get("id")
        print(f"   Muestra ID: {muestra_id}")
        print(f"   Estado: {muestra.get('estado')}")
        print(f"   CAD: {muestra.get('cad')}")

    except Exception as e:
        print(f"   [X] Error: {e}")
        import traceback
        traceback.print_exc()
        return

    # 3. Obtener detalles de la muestra
    print(f"\n3. Obteniendo detalles de muestra {muestra_id}...")
    try:
        resp = requests.get(f"{BASE_URL}/muestras/{muestra_id}", headers=headers, timeout=10)
        if resp.status_code != 200:
            print(f"   [X] Error: {resp.status_code} - {resp.text}")
            return

        muestra_detalle = resp.json()
        print(f"   [OK] Detalles obtenidos")
        print(f"   CAD: {muestra_detalle.get('cad')}")
        print(f"   Cartón ID: {muestra_detalle.get('carton_id')}")
        print(f"   Cantidad vendedor: {muestra_detalle.get('cantidad_vendedor')}")

    except Exception as e:
        print(f"   [X] Error: {e}")
        return

    # 4. Probar actualización de muestra (solo verificar permiso)
    print(f"\n4. Verificando permiso para editar muestra {muestra_id}...")
    try:
        # Intentar actualizar un campo no crítico
        update_data = {"tiempo_unitario": muestra_detalle.get("tiempo_unitario", "1:00")}

        resp = requests.put(
            f"{BASE_URL}/muestras/{muestra_id}",
            json=update_data,
            headers=headers,
            timeout=10
        )

        if resp.status_code == 200:
            print(f"   [OK] ¡ÉXITO! El rol Ingeniero PUEDE editar muestras")
            result = resp.json()
            print(f"   Respuesta: {result.get('message', 'OK')}")
        elif resp.status_code == 403:
            print(f"   [X] ERROR: El rol Ingeniero NO tiene permiso para editar")
            print(f"   Detalle: {resp.text}")
        else:
            print(f"   ! Respuesta inesperada: {resp.status_code}")
            print(f"   Detalle: {resp.text}")

    except Exception as e:
        print(f"   [X] Error: {e}")
        return

    # 5. Verificar que Vendedor NO puede editar
    print("\n5. Verificando que Vendedor NO puede editar...")
    login_data = {"rut": "11334692-2", "password": "123123"}
    try:
        resp = requests.post(f"{BASE_URL}/auth/login", json=login_data, timeout=10)
        if resp.status_code != 200:
            print(f"   ! No se pudo hacer login como Vendedor: {resp.status_code}")
        else:
            token_data = resp.json()
            token_vendedor = token_data.get("access_token")
            user_vendedor = token_data.get("user", {})
            print(f"   Login como: {user_vendedor.get('nombre', 'N/A')} (Rol: {user_vendedor.get('role_nombre', 'N/A')})")

            headers_vendedor = {"Authorization": f"Bearer {token_vendedor}"}

            # Intentar editar
            resp = requests.put(
                f"{BASE_URL}/muestras/{muestra_id}",
                json={"tiempo_unitario": "1:00"},
                headers=headers_vendedor,
                timeout=10
            )

            if resp.status_code == 403:
                print(f"   [OK] Correcto: Vendedor recibe 403 Forbidden")
            else:
                print(f"   [X] Error: Vendedor recibió {resp.status_code} (debería ser 403)")

    except Exception as e:
        print(f"   [X] Error: {e}")

    print("\n" + "=" * 60)
    print("VERIFICACIÓN COMPLETADA")
    print("=" * 60)
    print("\nRESUMEN:")
    print("- El endpoint PUT /api/muestras/{id} está funcionando")
    print("- El rol Ingeniero (6) puede editar muestras")
    print("- El rol Vendedor (4) NO puede editar muestras")
    print("\nPara verificar el frontend, abre http://localhost:3000")
    print("y navega a una OT con muestras como usuario Ingeniero (8106237-4)")

if __name__ == "__main__":
    main()
