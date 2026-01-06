# -*- coding: utf-8 -*-
"""
Script para verificar el campo CINTA en Crear OT
Debe mostrar "Si" o "No" como en Laravel
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
        print("=" * 70)
        print("VERIFICACION DEL CAMPO CINTA - Crear OT")
        print("=" * 70)

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

        # 2. Click en boton Crear OT
        print("3. Haciendo clic en 'Crear OT'...")
        crear_ot_btn = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Crear OT')]")))
        crear_ot_btn.click()
        time.sleep(3)

        # 3. Seleccionar tipo de solicitud
        print("4. Seleccionando tipo de solicitud (Desarrollo)...")
        tipo_select_element = wait.until(EC.presence_of_element_located((By.ID, "tipo_solicitud_select")))
        select = Select(tipo_select_element)
        select.select_by_value("1")
        time.sleep(4)

        # 4. Buscar todos los labels "Cinta" en la página
        print("\n5. Buscando todos los campos Cinta en la página...")

        # Scroll hacia abajo para cargar todos los elementos
        driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
        time.sleep(2)

        # Volver arriba y buscar
        driver.execute_script("window.scrollTo(0, 0);")
        time.sleep(1)

        # Buscar todos los labels con texto "Cinta"
        all_labels = driver.find_elements(By.TAG_NAME, "label")
        cinta_labels = [l for l in all_labels if l.text.strip() == "Cinta"]

        print(f"\n   Encontrados {len(cinta_labels)} campos con label 'Cinta'")

        for idx, label in enumerate(cinta_labels):
            try:
                # Hacer scroll al elemento
                driver.execute_script("arguments[0].scrollIntoView({block: 'center'});", label)
                time.sleep(0.5)

                # Buscar el input/select hermano o en el mismo contenedor
                parent = label.find_element(By.XPATH, "./..")

                # Intentar encontrar input
                try:
                    input_elem = parent.find_element(By.TAG_NAME, "input")
                    value = input_elem.get_attribute("value")
                    field_type = "input"
                except:
                    # Intentar encontrar select
                    try:
                        select_elem = parent.find_element(By.TAG_NAME, "select")
                        options = select_elem.find_elements(By.TAG_NAME, "option")
                        value = [o.text.strip() for o in options]
                        field_type = "select"
                    except:
                        value = "No encontrado"
                        field_type = "?"

                print(f"\n--- Campo Cinta #{idx+1} ---")
                print(f"   Tipo: {field_type}")
                print(f"   Valor/Opciones: {value}")

                # Verificar si es correcto
                if field_type == "input":
                    if value in ['Si', 'No', '']:
                        print(f"   ✓ CORRECTO - Campo de texto (readOnly) con valores Si/No")
                    else:
                        print(f"   ? VERIFICAR - Valor: '{value}'")
                elif field_type == "select":
                    # Verificar opciones
                    if any('Sin Cinta' in str(v) or 'Cinta Normal' in str(v) for v in value):
                        print(f"   ✗ ERROR - Muestra tipos de cinta en lugar de Si/No")
                    elif any('Si' in str(v) and 'No' in str(v) for v in [value]):
                        print(f"   ✓ CORRECTO - Select con opciones Si/No")
                    else:
                        print(f"   ? VERIFICAR - Opciones: {value}")

            except Exception as e:
                print(f"\n--- Campo Cinta #{idx+1} ---")
                print(f"   Error: {e}")

        # Screenshot del campo
        driver.save_screenshot(base_path + "cinta_field_all.png")
        print(f"\nScreenshot guardado: cinta_field_all.png")

        # Ahora verificar la sección específica donde está el campo Cinta readOnly
        print("\n" + "=" * 70)
        print("BUSCANDO CAMPO CINTA EN SECCION DE DATOS HEREDADOS")
        print("=" * 70)

        # El campo Cinta está después de Material, Flauta, FSC
        # Buscar por secuencia de labels
        try:
            # Scroll a la sección correcta
            material_label = driver.find_element(By.XPATH, "//label[text()='Material']")
            driver.execute_script("arguments[0].scrollIntoView({block: 'center'});", material_label)
            time.sleep(1)
            driver.save_screenshot(base_path + "cinta_field_section.png")
            print("\nScreenshot de la sección Material/Cinta guardado: cinta_field_section.png")
        except Exception as e:
            print(f"\nNo se encontró sección Material: {e}")

    except Exception as e:
        print(f"ERROR: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot(base_path + "cinta_field_error.png")

    finally:
        driver.quit()

if __name__ == "__main__":
    main()
