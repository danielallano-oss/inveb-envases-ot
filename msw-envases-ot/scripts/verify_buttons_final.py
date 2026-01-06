# -*- coding: utf-8 -*-
"""
Script final para verificar botones en React
"""
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
        print("VERIFICACION FINAL DE BOTONES EN REACT")
        print("=" * 60)

        # Login
        print("\n1. Login...")
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

        # Ir a gestionar OT
        print("2. Navegando a gestionar OT 26591...")
        driver.get("http://localhost:3000/gestionar-ot/26591")
        time.sleep(5)

        # Captura
        driver.save_screenshot("final_react_gestionar.png")
        print("   Screenshot: final_react_gestionar.png")

        # Listar TODOS los botones
        print("\n3. Listando TODOS los botones en la pagina:")
        print("-" * 50)
        buttons = driver.find_elements(By.TAG_NAME, "button")
        for i, btn in enumerate(buttons):
            try:
                text = btn.text.strip().replace('\n', ' ')[:50]
                disabled = btn.get_attribute("disabled")
                print(f"   [{i+1}] '{text}' (disabled={disabled})")
            except:
                pass

        # Buscar especificamente botones con Cotizar o Editar
        print("\n4. Buscando botones especificos:")
        print("-" * 50)

        # Buscar por texto parcial
        for search in ["Cotizar", "Editar", "OT"]:
            found = driver.find_elements(By.XPATH, f"//button[contains(text(), '{search}')]")
            print(f"   Botones con '{search}': {len(found)}")
            for btn in found:
                print(f"      - '{btn.text.strip()[:40]}'")

        print("\n" + "=" * 60)
        print("VERIFICACION COMPLETADA")
        print("=" * 60)

        time.sleep(10)
        return True

    except Exception as e:
        print(f"ERROR: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        driver.quit()

if __name__ == "__main__":
    verify_buttons()
