# -*- coding: utf-8 -*-
"""
Script para explorar el flujo de cotizacion en Laravel
Usuario: 11334692-2 (Vendedor)
"""
import sys
import io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')
import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options

def explore_cotizacion():
    chrome_options = Options()
    chrome_options.add_argument("--start-maximized")
    driver = webdriver.Chrome(options=chrome_options)
    wait = WebDriverWait(driver, 15)

    try:
        print("=" * 70)
        print("EXPLORACION DEL FLUJO DE COTIZACION EN LARAVEL")
        print("=" * 70)

        # 1. Login como Vendedor
        print("\n1. Login como Vendedor (11334692-2)...")
        driver.get("http://localhost:8080/login")
        time.sleep(2)

        wait.until(EC.presence_of_element_located((By.NAME, "rut"))).send_keys("11334692-2")
        driver.find_element(By.NAME, "password").send_keys("123123")
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        time.sleep(3)

        print("   Login exitoso")
        driver.save_screenshot("cotiz_01_login.png")

        # 2. Navegar a gestionar OT 26591
        print("\n2. Navegando a gestionar OT 26591...")
        driver.get("http://localhost:8080/gestionarOt/26591")
        time.sleep(3)
        driver.save_screenshot("cotiz_02_gestionar_ot.png")

        # 3. Buscar y hacer click en boton Cotizar OT
        print("\n3. Buscando boton Cotizar OT...")
        try:
            # Buscar el boton de cotizar
            cotizar_btn = None
            buttons = driver.find_elements(By.TAG_NAME, "a")
            for btn in buttons:
                if "cotizar" in btn.text.lower() or "cotizar" in btn.get_attribute("href") or "":
                    print(f"   Encontrado: '{btn.text}' -> {btn.get_attribute('href')}")
                    if "cotizarOt" in (btn.get_attribute("href") or ""):
                        cotizar_btn = btn
                        break

            if cotizar_btn:
                print(f"   Haciendo click en: {cotizar_btn.get_attribute('href')}")
                cotizar_btn.click()
                time.sleep(5)
            else:
                # Intentar navegar directamente
                print("   Navegando directamente a /cotizador/cotizarOt/26591")
                driver.get("http://localhost:8080/cotizador/cotizarOt/26591")
                time.sleep(5)

        except Exception as e:
            print(f"   Error buscando boton: {e}")
            driver.get("http://localhost:8080/cotizador/cotizarOt/26591")
            time.sleep(5)

        driver.save_screenshot("cotiz_03_pantalla_cotizar.png")
        print("   Screenshot: cotiz_03_pantalla_cotizar.png")

        # 4. Verificar si hay errores
        print("\n4. Verificando errores en la pagina...")
        page_source = driver.page_source.lower()
        if "error" in page_source or "exception" in page_source:
            print("   ADVERTENCIA: Posible error en la pagina")
            # Capturar el error si existe
            try:
                error_elem = driver.find_element(By.CSS_SELECTOR, ".alert-danger, .error, .exception")
                print(f"   Error: {error_elem.text[:200]}")
            except:
                pass
        else:
            print("   No se detectaron errores visibles")

        # 5. Listar campos del formulario
        print("\n5. Listando campos del formulario de cotizacion:")
        print("-" * 60)

        # Inputs
        inputs = driver.find_elements(By.CSS_SELECTOR, "input[type='text'], input[type='number']")
        print(f"\n   INPUTS ({len(inputs)}):")
        for inp in inputs[:20]:  # Limitar a 20
            name = inp.get_attribute("name") or inp.get_attribute("id") or "sin-nombre"
            value = inp.get_attribute("value") or ""
            placeholder = inp.get_attribute("placeholder") or ""
            print(f"      - {name}: '{value[:30]}' ({placeholder[:20]})")

        # Selects
        selects = driver.find_elements(By.TAG_NAME, "select")
        print(f"\n   SELECTS ({len(selects)}):")
        for sel in selects[:15]:  # Limitar a 15
            name = sel.get_attribute("name") or sel.get_attribute("id") or "sin-nombre"
            try:
                select_obj = Select(sel)
                selected = select_obj.first_selected_option.text if select_obj.first_selected_option else ""
                options_count = len(select_obj.options)
                print(f"      - {name}: '{selected[:30]}' ({options_count} opciones)")
            except:
                print(f"      - {name}: (no se pudo leer)")

        # 6. Scroll y capturar mas pantalla
        print("\n6. Capturando secciones de la pantalla...")
        driver.execute_script("window.scrollTo(0, 0);")
        time.sleep(1)
        driver.save_screenshot("cotiz_04_top.png")

        driver.execute_script("window.scrollTo(0, 500);")
        time.sleep(1)
        driver.save_screenshot("cotiz_05_mid1.png")

        driver.execute_script("window.scrollTo(0, 1000);")
        time.sleep(1)
        driver.save_screenshot("cotiz_06_mid2.png")

        driver.execute_script("window.scrollTo(0, 1500);")
        time.sleep(1)
        driver.save_screenshot("cotiz_07_mid3.png")

        driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
        time.sleep(1)
        driver.save_screenshot("cotiz_08_bottom.png")

        # 7. Buscar botones de accion
        print("\n7. Botones de accion encontrados:")
        buttons = driver.find_elements(By.CSS_SELECTOR, "button, input[type='submit']")
        for btn in buttons:
            text = btn.text or btn.get_attribute("value") or ""
            if text:
                print(f"      - '{text}'")

        print("\n" + "=" * 70)
        print("EXPLORACION COMPLETADA")
        print("=" * 70)
        print("\nScreenshots generados:")
        print("  - cotiz_01_login.png")
        print("  - cotiz_02_gestionar_ot.png")
        print("  - cotiz_03_pantalla_cotizar.png")
        print("  - cotiz_04_top.png a cotiz_08_bottom.png")

        # Mantener navegador abierto
        print("\nManteniendo navegador abierto 30 segundos para revision manual...")
        time.sleep(30)

        return True

    except Exception as e:
        print(f"\nERROR: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot("cotiz_error.png")
        return False

    finally:
        driver.quit()

if __name__ == "__main__":
    explore_cotizacion()
