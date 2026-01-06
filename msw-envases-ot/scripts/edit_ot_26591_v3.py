# -*- coding: utf-8 -*-
"""
Script simplificado para editar la OT 26591 en la version local (Laravel)
"""
import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options

def edit_ot_laravel():
    """Editar OT 26591 en Laravel (localhost:8080)"""

    chrome_options = Options()
    chrome_options.add_argument("--start-maximized")

    driver = webdriver.Chrome(options=chrome_options)
    wait = WebDriverWait(driver, 15)

    try:
        print("=" * 60)
        print("EDITANDO OT 26591 EN VERSION LOCAL (LARAVEL)")
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
        time.sleep(5)

        # 3. Scroll hacia abajo para ver campos
        print("3. Scroll a seccion de caracteristicas...")
        driver.execute_script("window.scrollTo(0, 800);")
        time.sleep(2)

        # 4. Completar campos
        print("\n4. Completando campos...")

        # 4.1 Seleccionar Matriz
        print("   Buscando Matriz...")
        try:
            matriz_elem = wait.until(EC.presence_of_element_located((By.ID, "matriz_id")))
            matriz_select = Select(matriz_elem)
            if len(matriz_select.options) > 1:
                matriz_select.select_by_index(1)
                print(f"   Matriz: {matriz_select.first_selected_option.text}")
                time.sleep(2)
        except Exception as e:
            print(f"   Matriz no disponible: {type(e).__name__}")

        # 4.2 Seleccionar Carton
        print("   Buscando Carton...")
        try:
            carton_elem = wait.until(EC.presence_of_element_located((By.ID, "carton_id")))
            carton_select = Select(carton_elem)
            if len(carton_select.options) > 1:
                carton_select.select_by_index(1)
                print(f"   Carton: {carton_select.first_selected_option.text}")
                time.sleep(2)
        except Exception as e:
            print(f"   Carton no disponible: {type(e).__name__}")

        # 4.3 Seleccionar Proceso
        print("   Buscando Proceso...")
        try:
            proceso_elem = wait.until(EC.presence_of_element_located((By.ID, "process_id")))
            proceso_select = Select(proceso_elem)
            if len(proceso_select.options) > 1:
                proceso_select.select_by_index(1)
                print(f"   Proceso: {proceso_select.first_selected_option.text}")
                time.sleep(2)
        except Exception as e:
            print(f"   Proceso no disponible: {type(e).__name__}")

        # 4.4 Completar Golpes
        print("   Buscando Golpes...")
        try:
            golpes_largo = driver.find_element(By.ID, "golpes_largo")
            if not golpes_largo.get_attribute('value'):
                golpes_largo.clear()
                golpes_largo.send_keys("3")
                print("   Golpes al Largo: 3")
        except Exception as e:
            print(f"   Golpes Largo: {type(e).__name__}")

        try:
            golpes_ancho = driver.find_element(By.ID, "golpes_ancho")
            if not golpes_ancho.get_attribute('value'):
                golpes_ancho.clear()
                golpes_ancho.send_keys("1")
                print("   Golpes al Ancho: 1")
        except Exception as e:
            print(f"   Golpes Ancho: {type(e).__name__}")

        # 5. Screenshot antes de guardar
        driver.save_screenshot("edit_ot_before_save.png")
        print("\n5. Screenshot: edit_ot_before_save.png")

        # 6. Guardar
        print("6. Guardando...")
        driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
        time.sleep(1)

        try:
            save_btn = driver.find_element(By.XPATH, "//button[contains(text(), 'Actualizar')]")
            save_btn.click()
            time.sleep(4)
            print("   Guardado ejecutado")
        except Exception as e:
            print(f"   Error guardar: {type(e).__name__}")

        # 7. Screenshot despues de guardar
        driver.save_screenshot("edit_ot_after_save.png")
        print("7. Screenshot: edit_ot_after_save.png")

        print("\n" + "=" * 60)
        print("PROCESO COMPLETADO - Verificar screenshots")
        print("=" * 60)

        time.sleep(10)
        return True

    except Exception as e:
        print(f"\nERROR: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot("edit_ot_error.png")
        return False

    finally:
        driver.quit()

if __name__ == "__main__":
    success = edit_ot_laravel()
    exit(0 if success else 1)
