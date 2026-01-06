# -*- coding: utf-8 -*-
"""
Script mejorado para editar la OT 26591 en la version local (Laravel)
Maneja elementos dinamicos y espera correctamente.
"""
import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options

def safe_select(driver, element_id, option_index=1, wait_time=2):
    """Selecciona una opcion de un dropdown de forma segura"""
    try:
        # Re-obtener el elemento
        select_elem = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.ID, element_id))
        )
        select = Select(select_elem)
        options = select.options
        if len(options) > option_index:
            selected_text = options[option_index].text
            select.select_by_index(option_index)
            time.sleep(wait_time)
            return selected_text
    except Exception as e:
        print(f"   Error en {element_id}: {e}")
    return None

def edit_ot_laravel():
    """Editar OT 26591 en Laravel (localhost:8080)"""

    chrome_options = Options()
    chrome_options.add_argument("--start-maximized")

    driver = webdriver.Chrome(options=chrome_options)
    wait = WebDriverWait(driver, 15)

    try:
        print("=" * 60)
        print("EDITANDO OT 26591 EN VERSION LOCAL (LARAVEL) - V2")
        print("=" * 60)

        # 1. Login
        print("\n1. Login como Ingeniero...")
        driver.get("http://localhost:8080/login")
        time.sleep(2)

        wait.until(EC.presence_of_element_located((By.NAME, "rut"))).send_keys("8106237-4")
        driver.find_element(By.NAME, "password").send_keys("123123")
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        time.sleep(3)

        # 2. Ir a editar OT
        print("2. Navegando a editar OT 26591...")
        driver.get("http://localhost:8080/edit-ot-old/26591")
        time.sleep(4)

        # 3. Scroll a seccion Caracteristicas
        print("3. Scroll a seccion Caracteristicas...")
        caracteristicas = driver.find_element(By.XPATH, "//*[contains(text(), 'CARACTER')]")
        driver.execute_script("arguments[0].scrollIntoView(true);", caracteristicas)
        time.sleep(2)

        # 4. Verificar y completar campos
        print("\n4. Completando campos para cotizacion...")

        # 4.1 Seleccionar Matriz (despues de CAD)
        print("   4.1 Seleccionando matriz...")
        matriz_text = safe_select(driver, "matriz_id", 1, 2)
        if matriz_text:
            print(f"       Matriz seleccionada: {matriz_text}")

        # 4.2 Seleccionar Carton
        print("   4.2 Seleccionando carton...")
        carton_text = safe_select(driver, "carton_id", 1, 2)
        if carton_text:
            print(f"       Carton seleccionado: {carton_text}")

        # 4.3 Seleccionar Proceso
        print("   4.3 Seleccionando proceso...")
        proceso_text = safe_select(driver, "process_id", 1, 2)
        if proceso_text:
            print(f"       Proceso seleccionado: {proceso_text}")

        # 4.4 Completar Golpes
        print("   4.4 Completando golpes...")
        try:
            golpes_largo = driver.find_element(By.ID, "golpes_largo")
            if not golpes_largo.get_attribute('value'):
                golpes_largo.clear()
                golpes_largo.send_keys("3")
                print("       Golpes al Largo: 3")

            golpes_ancho = driver.find_element(By.ID, "golpes_ancho")
            if not golpes_ancho.get_attribute('value'):
                golpes_ancho.clear()
                golpes_ancho.send_keys("1")
                print("       Golpes al Ancho: 1")
        except Exception as e:
            print(f"       Error golpes: {e}")

        # 5. Captura antes de guardar
        print("\n5. Capturando pantalla antes de guardar...")
        driver.save_screenshot("edit_ot_laravel_v2_before.png")

        # 6. Scroll al final y guardar
        print("6. Guardando cambios...")
        driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
        time.sleep(1)

        save_btn = driver.find_element(By.XPATH, "//button[contains(text(), 'Actualizar')]")
        driver.execute_script("arguments[0].scrollIntoView(true);", save_btn)
        time.sleep(1)
        save_btn.click()
        time.sleep(4)

        # 7. Verificar resultado
        print("7. Verificando resultado...")
        driver.save_screenshot("edit_ot_laravel_v2_after.png")

        # Buscar mensajes
        try:
            alerts = driver.find_elements(By.CSS_SELECTOR, ".alert, .toast, .message")
            for alert in alerts:
                print(f"   Mensaje: {alert.text}")
        except:
            pass

        # Verificar URL
        current_url = driver.current_url
        print(f"   URL actual: {current_url}")

        print("\n" + "=" * 60)
        print("PROCESO COMPLETADO")
        print("=" * 60)

        # Mantener abierto
        print("\nManteniendo navegador abierto 10 segundos...")
        time.sleep(10)

        return True

    except Exception as e:
        print(f"\nERROR: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot("edit_ot_error_v2.png")
        return False

    finally:
        driver.quit()

if __name__ == "__main__":
    success = edit_ot_laravel()
    exit(0 if success else 1)
