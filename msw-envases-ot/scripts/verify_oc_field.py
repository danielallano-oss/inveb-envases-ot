# -*- coding: utf-8 -*-
"""
Script para verificar el campo OC en la pantalla Crear OT
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
        # 1. Login
        print("1. Navegando a http://localhost:3000...")
        driver.get("http://localhost:3000")
        time.sleep(2)

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

        # 2. Click en boton Crear OT
        print("3. Haciendo clic en 'Crear OT'...")
        crear_ot_btn = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Crear OT')]")))
        crear_ot_btn.click()
        time.sleep(3)

        # 3. Seleccionar tipo de solicitud
        print("4. Seleccionando tipo de solicitud...")
        tipo_select_element = wait.until(EC.presence_of_element_located((By.ID, "tipo_solicitud_select")))
        select = Select(tipo_select_element)
        select.select_by_value("1")
        time.sleep(3)

        # 4. Buscar el campo OC
        print("5. Buscando campo OC...")
        try:
            # Buscar el label OC y luego el select siguiente
            oc_label = driver.find_element(By.XPATH, "//label[text()='OC']")
            parent = oc_label.find_element(By.XPATH, "./..")
            oc_select = parent.find_element(By.TAG_NAME, "select")

            # Obtener las opciones
            options_elements = oc_select.find_elements(By.TAG_NAME, "option")
            print("\n   === OPCIONES DEL CAMPO OC ===")
            for opt in options_elements:
                value = opt.get_attribute("value")
                text = opt.text
                print(f"   valor='{value}' -> '{text}'")

            # Hacer clic para abrir y tomar screenshot
            oc_select.click()
            time.sleep(0.5)
            driver.save_screenshot(base_path + "oc_field_options.png")
            print(f"\n   Screenshot guardado: oc_field_options.png")

        except Exception as e:
            print(f"   Error: {e}")

        print("\n=== VERIFICACION COMPLETADA ===")
        time.sleep(2)

    except Exception as e:
        print(f"ERROR: {e}")
        import traceback
        traceback.print_exc()

    finally:
        driver.quit()

if __name__ == "__main__":
    main()
