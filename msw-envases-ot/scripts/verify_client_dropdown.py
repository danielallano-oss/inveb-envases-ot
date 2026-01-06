# -*- coding: utf-8 -*-
"""
Script para verificar el combo de clientes en la pantalla Crear OT
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
        try:
            crear_ot_btn = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Crear OT')]")))
            crear_ot_btn.click()
            time.sleep(3)
        except:
            driver.get("http://localhost:3000/work-orders/create")
            time.sleep(3)

        # 3. Seleccionar tipo de solicitud usando Select
        print("4. Seleccionando tipo de solicitud...")
        try:
            tipo_select_element = wait.until(EC.presence_of_element_located((By.ID, "tipo_solicitud_select")))
            select = Select(tipo_select_element)
            select.select_by_value("1")
            print("   Tipo seleccionado exitosamente")
            time.sleep(3)
        except Exception as e:
            print(f"   Error con Select: {e}")
            # Intentar con click directo
            tipo_select_element.click()
            time.sleep(0.5)
            option = driver.find_element(By.CSS_SELECTOR, "#tipo_solicitud_select option[value='1']")
            option.click()
            time.sleep(3)

        # Screenshot despues de seleccionar tipo
        driver.save_screenshot(base_path + "step4_tipo_seleccionado.png")
        print("   Screenshot guardado: step4_tipo_seleccionado.png")

        # 4. Buscar el combo de clientes
        print("5. Buscando combo de clientes...")
        time.sleep(2)

        # Buscar por span que contiene el texto del placeholder
        try:
            client_dropdown = driver.find_element(By.XPATH,
                "//span[contains(text(), 'Seleccione cliente')]")
            print("   Encontrado por span")
            # Click en el contenedor padre
            client_dropdown.find_element(By.XPATH, "./..").click()
        except:
            try:
                # Buscar cualquier elemento con ese texto
                client_dropdown = driver.find_element(By.XPATH,
                    "//*[contains(text(), 'Seleccione cliente')]")
                print(f"   Encontrado: {client_dropdown.tag_name}")
                client_dropdown.click()
            except:
                # Ultimo intento - buscar por estructura del formulario
                print("   Buscando por estructura del DOM...")
                # El SearchableSelect deberia estar despues del label Cliente
                form_groups = driver.find_elements(By.XPATH, "//label[contains(text(), 'Cliente')]/parent::*")
                for fg in form_groups:
                    print(f"   FormGroup encontrado con texto: {fg.text[:50]}...")
                    # Buscar el div clickeable dentro
                    clickable = fg.find_elements(By.CSS_SELECTOR, "div[class]")
                    for c in clickable:
                        if "Seleccione" in c.text or c.get_attribute("class"):
                            print(f"   Haciendo clic en div: {c.text[:30]}")
                            c.click()
                            break
                    break

        time.sleep(1)

        # 5. Screenshot con dropdown abierto
        driver.save_screenshot(base_path + "step5_dropdown_abierto.png")
        print("   Screenshot guardado: step5_dropdown_abierto.png")

        # 6. Buscar opciones visibles
        print("\n6. Buscando opciones...")
        all_divs = driver.find_elements(By.TAG_NAME, "div")
        clientes_encontrados = []
        for div in all_divs:
            text = div.text.strip()
            if text and ("CMPC" in text or "EDIPAC" in text or "DESPAFOODS" in text):
                if len(text) < 100:  # Evitar divs contenedores grandes
                    clientes_encontrados.append(text)

        if clientes_encontrados:
            print("   Clientes encontrados:")
            for c in clientes_encontrados[:10]:
                has_code = " - " in c
                status = "[CON CODIGO]" if has_code else "[SIN CODIGO]"
                print(f"   {status} {c}")
        else:
            print("   No se encontraron clientes visibles")

        print("\n=== VERIFICACION COMPLETADA ===")
        time.sleep(2)

    except Exception as e:
        print(f"ERROR: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot(base_path + "error.png")

    finally:
        driver.quit()

if __name__ == "__main__":
    main()
