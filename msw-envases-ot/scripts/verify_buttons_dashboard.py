# -*- coding: utf-8 -*-
"""
Script para verificar botones navegando desde el dashboard
"""
import sys
import io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')
import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options

def verify_buttons():
    chrome_options = Options()
    chrome_options.add_argument("--start-maximized")
    driver = webdriver.Chrome(options=chrome_options)
    wait = WebDriverWait(driver, 15)

    try:
        print("=" * 60)
        print("VERIFICACION DE BOTONES DESDE DASHBOARD")
        print("=" * 60)

        # Login
        print("\n1. Login como Ingeniero...")
        driver.get("http://localhost:3000")
        time.sleep(2)

        rut = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "input[type='text']")))
        rut.clear()
        rut.send_keys("8106237-4")

        pwd = driver.find_element(By.CSS_SELECTOR, "input[type='password']")
        pwd.clear()
        pwd.send_keys("123123")

        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        time.sleep(4)

        # Dashboard
        print("2. En el Dashboard, buscando OT 26591...")
        driver.save_screenshot("step1_dashboard.png")

        # Buscar en la tabla la OT 26591
        time.sleep(2)

        # Buscar fila con 26591
        rows = driver.find_elements(By.CSS_SELECTOR, "tbody tr")
        ot_row = None
        for row in rows:
            if "26591" in row.text:
                ot_row = row
                print(f"   Encontrada fila: {row.text[:80]}...")
                break

        if not ot_row:
            print("   OT 26591 no encontrada en el dashboard. Buscando...")
            # Intentar buscar en el filtro
            try:
                search = driver.find_element(By.CSS_SELECTOR, "input[placeholder*='Buscar']")
                search.send_keys("26591")
                time.sleep(2)
                rows = driver.find_elements(By.CSS_SELECTOR, "tbody tr")
                for row in rows:
                    if "26591" in row.text:
                        ot_row = row
                        break
            except:
                pass

        # Hacer click en el boton Gestionar de esa fila
        if ot_row:
            print("3. Buscando boton Gestionar en la fila...")
            try:
                # Buscar boton en la fila
                gestionar_btn = ot_row.find_element(By.XPATH, ".//button[contains(text(), 'Gestionar') or @title='Gestionar']")
                print(f"   Boton encontrado: {gestionar_btn.text}")
                gestionar_btn.click()
                time.sleep(4)
            except:
                # Intentar con icono de engranaje o similar
                try:
                    icons = ot_row.find_elements(By.TAG_NAME, "button")
                    if icons:
                        print(f"   Haciendo click en primer boton de acciones...")
                        icons[0].click()
                        time.sleep(4)
                except Exception as e:
                    print(f"   Error: {e}")

        # Capturar pantalla de gestionar
        print("4. Capturando pantalla de gestionar OT...")
        driver.save_screenshot("step2_gestionar.png")
        print("   Screenshot: step2_gestionar.png")

        # Listar botones
        print("\n5. Listando botones en la pagina de gestion:")
        print("-" * 50)
        buttons = driver.find_elements(By.TAG_NAME, "button")
        cotizar_found = False
        editar_found = False

        for i, btn in enumerate(buttons):
            try:
                text = btn.text.strip().replace('\n', ' ')[:50]
                if text:
                    disabled = btn.get_attribute("disabled")
                    print(f"   [{i+1}] '{text}' (disabled={disabled})")

                    if "Cotizar" in text:
                        cotizar_found = True
                        print(f"       *** BOTON COTIZAR ENCONTRADO ***")
                    if "Editar" in text:
                        editar_found = True
                        print(f"       *** BOTON EDITAR ENCONTRADO ***")
            except:
                pass

        print("\n" + "=" * 60)
        print("RESULTADO:")
        print(f"   Boton Cotizar OT: {'ENCONTRADO' if cotizar_found else 'NO ENCONTRADO'}")
        print(f"   Boton Ver/Editar OT: {'ENCONTRADO' if editar_found else 'NO ENCONTRADO'}")
        print("=" * 60)

        time.sleep(10)
        return cotizar_found or editar_found

    except Exception as e:
        print(f"ERROR: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot("error.png")
        return False
    finally:
        driver.quit()

if __name__ == "__main__":
    verify_buttons()
