# -*- coding: utf-8 -*-
"""
Script para verificar la Sección 12 - Secuencia Operacional
Verifica que los dropdowns muestren las máquinas filtradas por planta
"""
import sys
import io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import Select
import time

def main():
    options = Options()
    options.add_argument('--start-maximized')

    driver = webdriver.Chrome(options=options)
    wait = WebDriverWait(driver, 20)
    base_path = "c:/Users/pg_54/OneDrive/Documentos/Proyectos/Tecnoandina/inveb/invebchile-envases-ot-00e7b5a341a2/invebchile-envases-ot-00e7b5a341a2/msw-envases-ot/scripts/"

    try:
        print("=" * 80)
        print("VERIFICACION SECCION 12 - SECUENCIA OPERACIONAL")
        print("=" * 80)

        # 1. Login
        print("\n1. Navegando a http://localhost:3000...")
        driver.get("http://localhost:3000")
        time.sleep(3)

        print("2. Iniciando sesion...")
        rut_input = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "input[type='text']")))
        rut_input.clear()
        rut_input.send_keys("11334692-2")

        pass_input = driver.find_element(By.CSS_SELECTOR, "input[type='password']")
        pass_input.clear()
        pass_input.send_keys("123123")

        login_btn = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        login_btn.click()
        time.sleep(3)

        # 2. Click en Crear OT
        print("3. Haciendo clic en 'Crear OT'...")
        crear_ot_btn = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Crear OT')]")))
        crear_ot_btn.click()
        time.sleep(3)

        # 3. Seleccionar tipo de solicitud
        print("4. Seleccionando tipo de solicitud (Desarrollo)...")
        tipo_select_element = wait.until(EC.presence_of_element_located((By.ID, "tipo_solicitud_select")))
        select = Select(tipo_select_element)
        select.select_by_value("1")
        time.sleep(4)

        # 4. Scroll a Sección 12
        print("\n5. Navegando a Seccion 12 - Secuencia Operacional...")
        section12 = driver.find_element(By.XPATH, "//div[contains(text(), '12.- Secuencia Operacional')]")
        driver.execute_script("arguments[0].scrollIntoView({block: 'start'});", section12)
        time.sleep(2)

        # 5. Encontrar el select de PLANTA en Sección 12
        print("\n6. Verificando selector de PLANTA...")
        planta_label = driver.find_element(By.XPATH, "//label[contains(text(), 'PLANTA:')]")
        planta_parent = planta_label.find_element(By.XPATH, "./following-sibling::select | ../select")

        # Si no lo encuentra así, buscar de otra forma
        try:
            planta_select = planta_parent
        except:
            # Buscar el select más cercano
            planta_container = planta_label.find_element(By.XPATH, "./..")
            planta_select = planta_container.find_element(By.TAG_NAME, "select")

        plantas_options = planta_select.find_elements(By.TAG_NAME, "option")
        print(f"   Opciones de planta: {[o.text for o in plantas_options]}")

        # 6. Verificar dropdowns de secuencia ANTES de seleccionar planta
        print("\n7. Verificando dropdowns SIN planta seleccionada...")
        first_row_selects = driver.find_elements(By.XPATH, "//table//tbody//tr[1]//select")
        if first_row_selects:
            first_select = first_row_selects[0]
            options_before = first_select.find_elements(By.TAG_NAME, "option")
            print(f"   Total opciones (sin filtro): {len(options_before)}")
            if len(options_before) > 1:
                print(f"   Primeras 5: {[o.text for o in options_before[1:6]]}")

        # 7. Seleccionar planta TIL TIL (id=2)
        print("\n8. Seleccionando planta TIL TIL...")
        select_planta = Select(planta_select)
        select_planta.select_by_value("2")
        time.sleep(2)

        # 8. Verificar dropdowns DESPUÉS de seleccionar planta
        print("\n9. Verificando dropdowns CON planta TIL TIL seleccionada...")
        first_row_selects = driver.find_elements(By.XPATH, "//table//tbody//tr[1]//select")
        if first_row_selects:
            first_select = first_row_selects[0]
            options_after = first_select.find_elements(By.TAG_NAME, "option")
            print(f"   Total opciones (filtradas por TIL TIL): {len(options_after)}")
            print(f"   Opciones disponibles:")
            for opt in options_after[1:]:  # Skip "Seleccionar..."
                print(f"      - {opt.text}")

        # Screenshot de la sección
        driver.save_screenshot(base_path + "seccion12_tiltil.png")
        print(f"\n   Screenshot guardado: seccion12_tiltil.png")

        # 9. Cambiar a planta BUIN
        print("\n10. Cambiando a planta BUIN...")
        select_planta.select_by_value("1")
        time.sleep(2)

        first_row_selects = driver.find_elements(By.XPATH, "//table//tbody//tr[1]//select")
        if first_row_selects:
            first_select = first_row_selects[0]
            options_buin = first_select.find_elements(By.TAG_NAME, "option")
            print(f"   Total opciones (filtradas por BUIN): {len(options_buin)}")
            print(f"   Primeras 10 opciones:")
            for opt in options_buin[1:11]:  # Skip "Seleccionar...", mostrar primeras 10
                print(f"      - {opt.text}")

        driver.save_screenshot(base_path + "seccion12_buin.png")
        print(f"\n   Screenshot guardado: seccion12_buin.png")

        # Resumen
        print("\n" + "=" * 80)
        print("RESUMEN")
        print("=" * 80)

        total_sin_filtro = len(options_before) if 'options_before' in dir() else 0
        total_tiltil = len(options_after) if 'options_after' in dir() else 0
        total_buin = len(options_buin) if 'options_buin' in dir() else 0

        print(f"   Sin filtro de planta: {total_sin_filtro} opciones")
        print(f"   Planta TIL TIL: {total_tiltil} opciones (esperado: ~13)")
        print(f"   Planta BUIN: {total_buin} opciones (esperado: ~31)")

        if total_tiltil > 1 and total_tiltil < total_sin_filtro:
            print("\n   ✓ CORRECTO - El filtro por planta está funcionando")
        else:
            print("\n   ✗ ERROR - El filtro no parece estar funcionando")

        time.sleep(3)

    except Exception as e:
        print(f"ERROR: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot(base_path + "seccion12_error.png")

    finally:
        driver.quit()

if __name__ == "__main__":
    main()
