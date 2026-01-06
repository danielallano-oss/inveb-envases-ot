# -*- coding: utf-8 -*-
"""
Test del endpoint de transicion de OT
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

    try:
        print("=" * 80)
        print("TEST DE TRANSICION DE ESTADO")
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

        # 2. Click en el icono de "Ver OT" de la primera OT
        print("\n3. Buscando OT para gestionar...")

        # Esperar que cargue la tabla de OTs
        time.sleep(2)

        # Buscar el boton de Ver OT (tiene title="Ver OT" y emoji 🔍)
        try:
            view_btn = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[@title='Ver OT']")))
            view_btn.click()
            print("   Clic en boton 'Ver OT' exitoso")

        except Exception as e:
            print(f"   Error buscando boton Ver OT: {e}")
            # Intentar buscar por el emoji
            try:
                emoji_btn = driver.find_element(By.XPATH, "//button[contains(text(), '🔍')]")
                emoji_btn.click()
                print("   Clic en boton con emoji lupa exitoso")
            except:
                print("   No se encontro el boton Ver OT")

        time.sleep(3)

        # 3. Verificar que estamos en la pagina de gestion
        print("\n4. Verificando pagina de gestion...")
        try:
            title = driver.find_element(By.XPATH, "//*[contains(text(), 'Gestionar Orden')]")
            print(f"   Titulo encontrado: {title.text}")
        except:
            print("   No se encontro el titulo de Gestionar Orden")

        # 4. Intentar seleccionar area y estado
        print("\n5. Seleccionando nueva area y estado...")
        try:
            # Buscar selects de area y estado
            selects = driver.find_elements(By.TAG_NAME, "select")
            print(f"   Encontrados {len(selects)} selects")

            if len(selects) >= 2:
                # Seleccionar area (primer select)
                area_select = Select(selects[0])
                area_options = area_select.options
                print(f"   Opciones de area: {[o.text for o in area_options[:5]]}")

                if len(area_options) > 1:
                    area_select.select_by_index(1)
                    time.sleep(1)

                # Seleccionar estado (segundo select)
                state_select = Select(selects[1])
                state_options = state_select.options
                print(f"   Opciones de estado: {[o.text for o in state_options[:5]]}")

                if len(state_options) > 1:
                    state_select.select_by_index(1)
                    time.sleep(1)

        except Exception as e:
            print(f"   Error seleccionando: {e}")

        # 5. Click en boton de transicion
        print("\n6. Haciendo clic en Realizar Transicion...")
        try:
            transition_btn = driver.find_element(By.XPATH, "//button[contains(text(), 'Realizar Transicion') or contains(text(), 'Transicion')]")
            transition_btn.click()
            time.sleep(3)
        except Exception as e:
            print(f"   Error en boton: {e}")

        # 6. Verificar resultado
        print("\n7. Verificando resultado...")
        try:
            # Buscar mensaje de exito
            success = driver.find_elements(By.XPATH, "//*[contains(text(), 'exitosamente') or contains(text(), 'Exito')]")
            if success:
                print(f"   EXITO: {success[0].text}")

            # Buscar mensaje de error
            error = driver.find_elements(By.XPATH, "//*[contains(text(), 'error') or contains(text(), 'Error') or contains(text(), '500')]")
            if error:
                print(f"   ERROR: {error[0].text}")

        except Exception as e:
            print(f"   Error verificando: {e}")

        # Screenshot
        driver.save_screenshot("c:/Users/pg_54/OneDrive/Documentos/Proyectos/Tecnoandina/inveb/invebchile-envases-ot-00e7b5a341a2/invebchile-envases-ot-00e7b5a341a2/msw-envases-ot/scripts/test_transition_result.png")
        print("\n   Screenshot guardado: test_transition_result.png")

        time.sleep(5)

    except Exception as e:
        print(f"ERROR: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot("c:/Users/pg_54/OneDrive/Documentos/Proyectos/Tecnoandina/inveb/invebchile-envases-ot-00e7b5a341a2/invebchile-envases-ot-00e7b5a341a2/msw-envases-ot/scripts/test_transition_error.png")

    finally:
        driver.quit()

if __name__ == "__main__":
    main()
