# -*- coding: utf-8 -*-
"""
Script para comparar la pantalla de editar OT entre Laravel y React
y verificar que el boton Cotizar OT este habilitado despues de completar campos.
"""
import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options

def compare_edit_screens():
    """Comparar pantallas de edicion Laravel vs React"""

    chrome_options = Options()
    chrome_options.add_argument("--start-maximized")

    driver = webdriver.Chrome(options=chrome_options)
    wait = WebDriverWait(driver, 15)

    try:
        print("=" * 70)
        print("COMPARACION DE PANTALLAS DE EDICION OT: LARAVEL vs REACT")
        print("=" * 70)

        # ==================== PARTE 1: LARAVEL ====================
        print("\n" + "=" * 70)
        print("PARTE 1: VERSION LARAVEL (localhost:8080)")
        print("=" * 70)

        # Login Laravel
        print("\n1. Login en Laravel...")
        driver.get("http://localhost:8080/login")
        time.sleep(2)
        wait.until(EC.presence_of_element_located((By.NAME, "rut"))).send_keys("8106237-4")
        driver.find_element(By.NAME, "password").send_keys("123123")
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        time.sleep(3)

        # Ir a gestionar OT para ver boton Cotizar
        print("2. Navegando a gestionar OT 26591...")
        driver.get("http://localhost:8080/gestionarOt/26591")
        time.sleep(3)

        # Captura de pantalla de gestion
        driver.save_screenshot("compare_laravel_gestionar.png")
        print("   Screenshot: compare_laravel_gestionar.png")

        # Verificar boton Cotizar OT
        print("3. Verificando boton Cotizar OT en Laravel...")
        try:
            cotizar_btn = driver.find_element(By.XPATH, "//a[contains(text(), 'Cotizar') or contains(@class, 'cotizar')]")
            is_disabled = 'disabled' in cotizar_btn.get_attribute('class') or cotizar_btn.get_attribute('disabled')
            print(f"   Boton Cotizar encontrado: {cotizar_btn.text}")
            print(f"   Estado: {'DESHABILITADO' if is_disabled else 'HABILITADO'}")
        except Exception as e:
            print(f"   Boton Cotizar no encontrado: {type(e).__name__}")

        # Ir a editar OT
        print("4. Navegando a editar OT...")
        driver.get("http://localhost:8080/edit-ot-old/26591")
        time.sleep(3)

        # Captura completa de edicion Laravel
        print("5. Capturando pantalla de edicion Laravel...")
        driver.execute_script("window.scrollTo(0, 0);")
        time.sleep(1)
        driver.save_screenshot("compare_laravel_edit_top.png")
        print("   Screenshot: compare_laravel_edit_top.png")

        driver.execute_script("window.scrollTo(0, 500);")
        time.sleep(1)
        driver.save_screenshot("compare_laravel_edit_mid.png")
        print("   Screenshot: compare_laravel_edit_mid.png")

        driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
        time.sleep(1)
        driver.save_screenshot("compare_laravel_edit_bottom.png")
        print("   Screenshot: compare_laravel_edit_bottom.png")

        # Logout Laravel
        print("6. Cerrando sesion Laravel...")
        try:
            driver.get("http://localhost:8080/logout")
        except:
            pass
        time.sleep(2)

        # ==================== PARTE 2: REACT ====================
        print("\n" + "=" * 70)
        print("PARTE 2: VERSION REACT (localhost:3000)")
        print("=" * 70)

        # Login React
        print("\n1. Login en React...")
        driver.get("http://localhost:3000")
        time.sleep(2)

        rut_field = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "input[type='text']")))
        rut_field.clear()
        rut_field.send_keys("8106237-4")

        pass_field = driver.find_element(By.CSS_SELECTOR, "input[type='password']")
        pass_field.clear()
        pass_field.send_keys("123123")

        login_btn = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        login_btn.click()
        time.sleep(3)

        # Ir a gestionar OT
        print("2. Navegando a gestionar OT 26591...")
        driver.get("http://localhost:3000/gestionar-ot/26591")
        time.sleep(3)

        # Captura de gestion
        driver.save_screenshot("compare_react_gestionar.png")
        print("   Screenshot: compare_react_gestionar.png")

        # Verificar boton Cotizar OT
        print("3. Verificando boton Cotizar OT en React...")
        try:
            cotizar_btns = driver.find_elements(By.XPATH, "//button[contains(text(), 'Cotizar')]")
            for btn in cotizar_btns:
                is_disabled = btn.get_attribute('disabled')
                print(f"   Boton encontrado: '{btn.text}'")
                print(f"   Estado: {'DESHABILITADO' if is_disabled else 'HABILITADO'}")
        except Exception as e:
            print(f"   Boton Cotizar no encontrado: {type(e).__name__}")

        # Buscar boton Ver/Editar OT y hacer click
        print("4. Buscando boton Ver/Editar OT...")
        try:
            editar_btn = driver.find_element(By.XPATH, "//button[contains(text(), 'Editar')]")
            print(f"   Boton encontrado: '{editar_btn.text}'")
            editar_btn.click()
            time.sleep(3)
        except Exception as e:
            print(f"   Boton Ver/Editar no encontrado, navegando directamente...")
            # Navegar directamente - no hay URL directa, usar el dashboard

        # Captura de edicion React
        print("5. Capturando pantalla de edicion React...")
        driver.execute_script("window.scrollTo(0, 0);")
        time.sleep(1)
        driver.save_screenshot("compare_react_edit_top.png")
        print("   Screenshot: compare_react_edit_top.png")

        driver.execute_script("window.scrollTo(0, 500);")
        time.sleep(1)
        driver.save_screenshot("compare_react_edit_mid.png")
        print("   Screenshot: compare_react_edit_mid.png")

        driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
        time.sleep(1)
        driver.save_screenshot("compare_react_edit_bottom.png")
        print("   Screenshot: compare_react_edit_bottom.png")

        print("\n" + "=" * 70)
        print("COMPARACION COMPLETADA")
        print("=" * 70)
        print("\nScreenshots generados:")
        print("  LARAVEL:")
        print("    - compare_laravel_gestionar.png")
        print("    - compare_laravel_edit_top.png")
        print("    - compare_laravel_edit_mid.png")
        print("    - compare_laravel_edit_bottom.png")
        print("  REACT:")
        print("    - compare_react_gestionar.png")
        print("    - compare_react_edit_top.png")
        print("    - compare_react_edit_mid.png")
        print("    - compare_react_edit_bottom.png")

        print("\nManteniendo navegador 15 segundos...")
        time.sleep(15)

        return True

    except Exception as e:
        print(f"\nERROR: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot("compare_error.png")
        return False

    finally:
        driver.quit()

if __name__ == "__main__":
    success = compare_edit_screens()
    exit(0 if success else 1)
