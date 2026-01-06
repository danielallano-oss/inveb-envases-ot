#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Test de Gestionar Muestras para Jefe de Desarrollo
Usuario: 20649380-1 / 123123 / Jefe de Desarrollo (role_id=5)
OT: 26591 - tiene muestra ID 2
"""

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
import time
import sys

# Fix encoding for Windows console
sys.stdout.reconfigure(encoding='utf-8')

# Configuracion
REACT_URL = "http://localhost:3000"
USER_RUT = "20649380-1"
USER_PASS = "123123"
OT_ID = 26591

def main():
    print("=" * 60)
    print("TEST: Gestionar Muestras - Jefe de Desarrollo")
    print("=" * 60)

    # Setup Chrome
    options = Options()
    options.add_argument("--start-maximized")

    driver = webdriver.Chrome(options=options)
    wait = WebDriverWait(driver, 15)

    try:
        # 1. Login
        print("\n[1] Iniciando sesion como Jefe de Desarrollo...")
        driver.get(REACT_URL)
        time.sleep(2)

        # Buscar campos de login
        rut_input = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "input[placeholder*='RUT'], input[name='rut'], input[type='text']")))
        rut_input.clear()
        rut_input.send_keys(USER_RUT)

        pass_input = driver.find_element(By.CSS_SELECTOR, "input[type='password']")
        pass_input.clear()
        pass_input.send_keys(USER_PASS)

        # Click login
        login_btn = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        login_btn.click()
        time.sleep(3)

        print("    Login exitoso")

        # 2. Buscar OT 26591 en la tabla
        print(f"\n[2] Buscando OT {OT_ID} en el dashboard...")

        # Esperar a que cargue la tabla
        wait.until(EC.presence_of_element_located((By.TAG_NAME, "table")))
        time.sleep(2)

        # Buscar input de busqueda si existe
        try:
            search_inputs = driver.find_elements(By.CSS_SELECTOR, "input[type='text'], input[type='search']")
            for inp in search_inputs:
                placeholder = inp.get_attribute("placeholder") or ""
                if "buscar" in placeholder.lower() or "search" in placeholder.lower() or "ot" in placeholder.lower():
                    inp.clear()
                    inp.send_keys(str(OT_ID))
                    time.sleep(2)
                    print(f"    Busqueda realizada: {OT_ID}")
                    break
        except:
            print("    No hay campo de busqueda, buscando directamente en tabla")

        # 3. Click en boton Ver OT (lupa)
        print(f"\n[3] Buscando boton Ver OT para {OT_ID}...")

        try:
            # Buscar fila con la OT
            rows = driver.find_elements(By.CSS_SELECTOR, "table tbody tr")
            target_row = None

            for row in rows:
                if str(OT_ID) in row.text:
                    target_row = row
                    print(f"    Fila encontrada: {row.text[:80]}...")
                    break

            if target_row:
                # Buscar boton de ver (lupa emoji o title='Ver OT')
                ver_btn = target_row.find_element(By.CSS_SELECTOR, "button[title='Ver OT'], button.btn-icon")
                ver_btn.click()
                time.sleep(3)
                print("    Click en Ver OT")
            else:
                print(f"    OT {OT_ID} no encontrada en tabla visible")
                # Intentar buscar con filtros o scroll
                driver.save_screenshot("debug_no_ot.png")
                print("    Screenshot: debug_no_ot.png")
                return

        except Exception as e:
            print(f"    Error: {e}")
            driver.save_screenshot("debug_ver_ot.png")

        # 4. Verificar seccion Gestionar Muestras
        print("\n[4] Verificando seccion 'Gestionar Muestras'...")
        time.sleep(2)

        try:
            # Buscar por texto exacto o aproximado
            muestras_elements = driver.find_elements(By.XPATH, "//*[contains(text(), 'Gestionar Muestras') or contains(text(), 'Muestras')]")

            if muestras_elements:
                print(f"    ENCONTRADO: {len(muestras_elements)} elemento(s) con 'Muestras'")

                # Click en el header para expandir
                for elem in muestras_elements:
                    if "Gestionar" in elem.text:
                        elem.click()
                        time.sleep(2)
                        print(f"    Click para expandir: {elem.text}")
                        break
            else:
                print("    NO se encontro seccion de muestras")

                # Verificar si el rol tiene acceso
                page_text = driver.find_element(By.TAG_NAME, "body").text
                if "Gestionar" in page_text:
                    print("    'Gestionar' encontrado en pagina")

                driver.save_screenshot("debug_no_muestras_section.png")
                print("    Screenshot: debug_no_muestras_section.png")

        except Exception as e:
            print(f"    Error buscando muestras: {e}")
            driver.save_screenshot("debug_muestras_error.png")

        # 5. Verificar tabla de muestras expandida
        print("\n[5] Verificando contenido de muestras...")
        time.sleep(2)

        try:
            # Buscar tabla dentro de la seccion de muestras
            tables = driver.find_elements(By.TAG_NAME, "table")
            print(f"    Tablas encontradas: {len(tables)}")

            # Buscar datos de la muestra ID 2
            page_source = driver.page_source

            if "EN7HN" in page_source:
                print("    ENCONTRADO: Carton EN7HN (muestra ID 2)")
            elif "Sin Asignar" in page_source:
                print("    ENCONTRADO: Estado 'Sin Asignar'")
            elif "No hay muestras" in page_source:
                print("    PROBLEMA: Mensaje 'No hay muestras' encontrado")
            else:
                print("    No se encontraron datos de muestras visibles")

            # Buscar botones de accion
            star_btns = driver.find_elements(By.XPATH, "//button[contains(text(), '★') or contains(@title, 'prioritaria')]")
            if star_btns:
                print(f"    ENCONTRADO: {len(star_btns)} boton(es) de prioridad")

        except Exception as e:
            print(f"    Error verificando muestras: {e}")

        # 6. Screenshot final
        print("\n[6] Capturando estado final...")
        driver.save_screenshot("test_muestras_final.png")
        print("    Screenshot: test_muestras_final.png")

        print("\n" + "=" * 60)
        print("TEST COMPLETADO")
        print("=" * 60)

        # Mantener ventana abierta
        input("\nPresiona Enter para cerrar el navegador...")

    except Exception as e:
        print(f"\nERROR GENERAL: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot("error_muestras.png")

    finally:
        driver.quit()

if __name__ == "__main__":
    main()
