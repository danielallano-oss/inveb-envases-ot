# -*- coding: utf-8 -*-
"""
Script para verificar los campos de Terminaciones (Seccion 11) en Crear OT
Campos: Proceso, Tipo Pegado, Armado, Sentido de Armado
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
        print("=" * 70)
        print("VERIFICACION DE CAMPOS TERMINACIONES - SECCION 11")
        print("=" * 70)

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
        print("4. Seleccionando tipo de solicitud...")
        tipo_select_element = wait.until(EC.presence_of_element_located((By.ID, "tipo_solicitud_select")))
        select = Select(tipo_select_element)
        select.select_by_value("1")
        time.sleep(3)

        # Función auxiliar para obtener opciones de un select
        def get_select_options(label_text, field_name, exact_match=False):
            try:
                # Buscar por label - usar exact match para evitar "Armado Automatico" cuando buscamos "Armado"
                if exact_match:
                    # Use normalize-space for exact match
                    label = driver.find_element(By.XPATH, f"//label[normalize-space(text())='{label_text}']")
                else:
                    label = driver.find_element(By.XPATH, f"//label[contains(text(), '{label_text}')]")
                parent = label.find_element(By.XPATH, "./..")
                select_elem = parent.find_element(By.TAG_NAME, "select")

                options_elements = select_elem.find_elements(By.TAG_NAME, "option")
                options_list = []
                for opt in options_elements:
                    value = opt.get_attribute("value")
                    text = opt.text
                    options_list.append({"value": value, "text": text})
                return options_list
            except Exception as e:
                return f"Error: {e}"

        # 4. Scroll hacia la sección 11 - Terminaciones
        print("\n5. Buscando seccion 11 - Terminaciones...")
        try:
            # Scroll down para encontrar la sección
            driver.execute_script("window.scrollTo(0, document.body.scrollHeight * 0.7);")
            time.sleep(2)
        except:
            pass

        # 5. Verificar cada campo de Terminaciones
        print("\n" + "=" * 70)
        print("CAMPOS DE TERMINACIONES")
        print("=" * 70)

        # (label_text, field_name, exact_match)
        fields_to_check = [
            ("Proceso", "proceso", False),
            ("Tipo Pegado", "pegado_terminacion", False),
            ("Armado", "armado_id", True),  # exact match para evitar "Armado Automatico"
            ("Sentido de Armado", "sentido_armado", False),
        ]

        results = {}
        for label_text, field_name, exact in fields_to_check:
            print(f"\n--- {label_text.upper()} ({field_name}) ---")
            options = get_select_options(label_text, field_name, exact)
            results[field_name] = options

            if isinstance(options, str):
                print(f"   {options}")
            else:
                for opt in options:
                    print(f"   valor='{opt['value']}' -> '{opt['text']}'")

        # Tomar screenshot de la sección
        driver.save_screenshot(base_path + "terminaciones_fields.png")
        print(f"\n   Screenshot guardado: terminaciones_fields.png")

        # 6. Comparación con valores esperados
        print("\n" + "=" * 70)
        print("COMPARACION CON VALORES ESPERADOS")
        print("=" * 70)

        expected = {
            "proceso": ["Seleccione...", "FLEXO", "DIECUTTER", "CORRUGADO"],
            "pegado_terminacion": ["Seleccione...", "No Aplica", "Pegado Interno", "Pegado Externo", "Pegado 3 Puntos", "Pegado 4 Puntos"],
            "armado_id": ["Seleccione...", "Armado a Maquina", "Con/Sin Armado", "Manual"],
            "sentido_armado": ["Seleccione...", "No aplica", "Ancho a la Derecha", "Ancho a la Izquierda", "Largo a la Izquierda", "Largo a la Derecha"],
        }

        all_ok = True
        for field_name, expected_values in expected.items():
            print(f"\n--- {field_name} ---")
            if isinstance(results.get(field_name), list):
                actual_texts = [opt['text'] for opt in results[field_name]]

                if actual_texts == expected_values:
                    print(f"   ✓ CORRECTO - Valores coinciden")
                else:
                    print(f"   ✗ DIFERENTE")
                    print(f"   Esperado: {expected_values}")
                    print(f"   Actual:   {actual_texts}")
                    all_ok = False
            else:
                print(f"   ✗ ERROR: {results.get(field_name)}")
                all_ok = False

        print("\n" + "=" * 70)
        if all_ok:
            print("RESULTADO: ✓ TODOS LOS CAMPOS CORRECTOS")
        else:
            print("RESULTADO: ✗ ALGUNOS CAMPOS NECESITAN CORRECCION")
        print("=" * 70)

        time.sleep(2)

    except Exception as e:
        print(f"ERROR: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot(base_path + "terminaciones_error.png")

    finally:
        driver.quit()

if __name__ == "__main__":
    main()
