"""
Script para verificar que los botones "Cotizar OT" y "Ver/Editar OT" aparecen
correctamente en la versión factorizada (localhost:3000).
"""
import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options

def test_cotizar_editar_buttons():
    """Test que los botones Cotizar OT y Ver/Editar OT aparecen para el Ingeniero"""

    # Configurar Chrome
    chrome_options = Options()
    chrome_options.add_argument("--start-maximized")

    driver = webdriver.Chrome(options=chrome_options)
    wait = WebDriverWait(driver, 15)

    try:
        print("=" * 60)
        print("VERIFICACION DE BOTONES COTIZAR OT Y VER/EDITAR OT")
        print("=" * 60)

        # 1. Ir a la página de login
        print("\n1. Navegando a localhost:3000...")
        driver.get("http://localhost:3000")
        time.sleep(2)

        # 2. Hacer login como Ingeniero
        print("2. Iniciando sesión como Ingeniero (8106237-4)...")

        # Esperar y llenar campo RUT
        rut_field = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "input[type='text']")))
        rut_field.clear()
        rut_field.send_keys("8106237-4")

        # Llenar password
        pass_field = driver.find_element(By.CSS_SELECTOR, "input[type='password']")
        pass_field.clear()
        pass_field.send_keys("123123")

        # Click en botón login
        login_btn = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        login_btn.click()

        print("   Esperando dashboard...")
        time.sleep(3)

        # 3. Buscar y hacer click en OT 26591
        print("3. Buscando OT #26591 en el dashboard...")

        # Buscar la OT en la tabla
        try:
            # Esperar que cargue la tabla
            wait.until(EC.presence_of_element_located((By.TAG_NAME, "table")))
            time.sleep(2)

            # Buscar la fila con OT 26591 o hacer click en el botón Gestionar
            ot_link = None
            rows = driver.find_elements(By.CSS_SELECTOR, "tbody tr")
            for row in rows:
                if "26591" in row.text:
                    print("   Encontrada OT #26591")
                    # Buscar botón de gestionar en esta fila
                    buttons = row.find_elements(By.TAG_NAME, "button")
                    for btn in buttons:
                        if "Gestionar" in btn.text or btn.get_attribute("title") == "Gestionar":
                            ot_link = btn
                            break
                    if not ot_link:
                        # Intentar con enlaces
                        links = row.find_elements(By.TAG_NAME, "a")
                        for link in links:
                            if "gestionar" in link.get_attribute("href").lower():
                                ot_link = link
                                break
                    break

            if ot_link:
                ot_link.click()
            else:
                # Intentar navegación directa
                print("   Navegando directamente a gestionar OT #26591...")
                driver.get("http://localhost:3000/gestionar-ot/26591")

        except Exception as e:
            print(f"   No se encontró en tabla, navegando directamente: {e}")
            driver.get("http://localhost:3000/gestionar-ot/26591")

        print("4. Esperando carga de página Gestionar OT...")
        time.sleep(4)

        # 4. Verificar botones
        print("\n5. VERIFICANDO BOTONES EN LA PÁGINA:")
        print("-" * 40)

        # Captura de pantalla
        driver.save_screenshot("verify_buttons_react.png")
        print("   Screenshot guardado: verify_buttons_react.png")

        # Buscar todos los botones en la página
        all_buttons = driver.find_elements(By.TAG_NAME, "button")
        print(f"\n   Total de botones encontrados: {len(all_buttons)}")

        cotizar_found = False
        editar_found = False

        print("\n   Listado de botones:")
        for i, btn in enumerate(all_buttons):
            btn_text = btn.text.strip()
            btn_title = btn.get_attribute("title") or ""
            btn_disabled = btn.get_attribute("disabled")
            if btn_text:
                print(f"   [{i+1}] '{btn_text}' (title='{btn_title}', disabled={btn_disabled})")

            if "Cotizar" in btn_text:
                cotizar_found = True
                print(f"       *** ENCONTRADO: Botón Cotizar OT ***")

            if "Editar" in btn_text or "Ver/Editar" in btn_text:
                editar_found = True
                print(f"       *** ENCONTRADO: Botón Ver/Editar OT ***")

        # También buscar por ActionButton styled component
        print("\n   Buscando botones por clases específicas...")
        action_buttons = driver.find_elements(By.CSS_SELECTOR, "[class*='ActionButton']")
        print(f"   ActionButtons encontrados: {len(action_buttons)}")
        for btn in action_buttons:
            print(f"   - '{btn.text}'")
            if "Cotizar" in btn.text:
                cotizar_found = True
            if "Editar" in btn.text:
                editar_found = True

        # Buscar en el header
        print("\n   Buscando en HeaderActions...")
        try:
            header_actions = driver.find_elements(By.CSS_SELECTOR, "[class*='HeaderActions']")
            for ha in header_actions:
                inner_buttons = ha.find_elements(By.TAG_NAME, "button")
                print(f"   Botones en HeaderActions: {len(inner_buttons)}")
                for btn in inner_buttons:
                    print(f"   - '{btn.text}'")
        except:
            pass

        print("\n" + "=" * 60)
        print("RESULTADO:")
        print("=" * 60)
        print(f"   Botón 'Cotizar OT':    {'✓ ENCONTRADO' if cotizar_found else '✗ NO ENCONTRADO'}")
        print(f"   Botón 'Ver/Editar OT': {'✓ ENCONTRADO' if editar_found else '✗ NO ENCONTRADO'}")

        if cotizar_found and editar_found:
            print("\n   ✓ TODOS LOS BOTONES PRESENTES")
        else:
            print("\n   ✗ FALTAN BOTONES")

            # Debug: imprimir HTML del header
            print("\n   DEBUG - Contenido del header:")
            try:
                header = driver.find_element(By.CSS_SELECTOR, "[class*='Header']")
                print(f"   {header.get_attribute('outerHTML')[:1000]}...")
            except Exception as e:
                print(f"   Error obteniendo header: {e}")

        print("=" * 60)

        # Mantener navegador abierto por unos segundos para verificación visual
        print("\nManteniendo navegador abierto 10 segundos para verificación visual...")
        time.sleep(10)

        return cotizar_found and editar_found

    except Exception as e:
        print(f"\nERROR: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot("error_screenshot.png")
        return False

    finally:
        driver.quit()

if __name__ == "__main__":
    success = test_cotizar_editar_buttons()
    exit(0 if success else 1)
