#!/usr/bin/env python
"""
Script para verificar el formulario de respuesta a consultas en ManageWorkOrder
"""

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
import time

def main():
    # Configurar Chrome
    chrome_options = Options()
    chrome_options.add_argument("--start-maximized")

    driver = webdriver.Chrome(options=chrome_options)
    wait = WebDriverWait(driver, 15)

    try:
        # 1. Ir a la página de login
        print("1. Navegando a localhost:3000...")
        driver.get("http://localhost:3000")
        time.sleep(3)

        # 2. Login - usar id="rut"
        print("2. Haciendo login con 20649380-1...")
        rut_input = wait.until(EC.presence_of_element_located((By.ID, "rut")))
        rut_input.clear()
        rut_input.send_keys("20649380-1")

        password_input = driver.find_element(By.ID, "password")
        password_input.clear()
        password_input.send_keys("123123")

        login_button = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        login_button.click()
        time.sleep(4)

        # 3. Buscar la OT 26609
        print("3. Buscando OT 26609...")

        # Esperar que cargue el dashboard - usar selector genérico
        wait.until(EC.presence_of_element_located((By.XPATH, "//h1[contains(text(), 'rdenes')]")))

        # Buscar input de ID
        id_inputs = driver.find_elements(By.CSS_SELECTOR, "input")
        for inp in id_inputs:
            placeholder = inp.get_attribute("placeholder") or ""
            if "ID" in placeholder or "id" in placeholder.lower():
                inp.clear()
                inp.send_keys("26609")
                break

        # Click en buscar
        search_buttons = driver.find_elements(By.XPATH, "//button[contains(text(), 'Buscar')]")
        if search_buttons:
            search_buttons[0].click()
        time.sleep(3)

        # 4. Click en gestionar OT
        print("4. Haciendo click en Gestionar...")
        gestionar_button = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Gestionar')]")))
        gestionar_button.click()
        time.sleep(4)

        # 5. Verificar el historial
        print("\n5. Verificando historial de gestión...")

        # Buscar elementos con información de consulta
        consulta_elements = driver.find_elements(By.XPATH, "//*[contains(text(), 'Área Consultada')]")
        print(f"   Elementos 'Área Consultada' encontrados: {len(consulta_elements)}")

        # Buscar formulario de respuesta
        answer_forms = driver.find_elements(By.XPATH, "//*[contains(text(), 'Responder Consulta')]")
        print(f"   Formularios 'Responder Consulta' encontrados: {len(answer_forms)}")

        # Buscar textarea de respuesta
        answer_textareas = driver.find_elements(By.CSS_SELECTOR, "textarea[placeholder*='respuesta']")
        print(f"   Textareas de respuesta encontrados: {len(answer_textareas)}")

        # Buscar botón de enviar respuesta
        send_buttons = driver.find_elements(By.XPATH, "//button[contains(text(), 'Enviar Respuesta')]")
        print(f"   Botones 'Enviar Respuesta' encontrados: {len(send_buttons)}")

        # Buscar por "Por Responder"
        por_responder = driver.find_elements(By.XPATH, "//*[contains(text(), 'Por Responder')]")
        print(f"   Textos 'Por Responder' encontrados: {len(por_responder)}")

        # 6. Verificar datos del API
        print("\n6. Verificando respuesta del API...")
        api_response = driver.execute_async_script("""
            var callback = arguments[arguments.length - 1];
            fetch('http://localhost:8001/api/v1/work-orders/26609/management', {
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('inveb_token')
                }
            })
            .then(r => r.json())
            .then(data => callback(data))
            .catch(err => callback({error: err.toString()}));
        """)

        if api_response and 'history' in api_response:
            print("   Datos del API recibidos:")
            for i, item in enumerate(api_response['history'][:5]):
                type_id = item.get('management_type_id', 'N/A')
                answer = item.get('answer')
                print(f"   [{i}] ID: {item['id']}, TypeID: {type_id}, Answer: {'Sí' if answer else 'No'}")
                if type_id == 2:
                    print(f"       -> Es CONSULTA, consulted_work_space_name: {item.get('consulted_work_space_name')}")
                    print(f"       -> Tiene respuesta: {'Sí' if answer else 'NO (debería mostrar formulario)'}")

        # 7. Verificar el HTML de los items del historial
        print("\n7. Buscando items del historial que contengan 'Cual archivo?'...")
        items = driver.find_elements(By.XPATH, "//*[contains(text(), 'Cual archivo?')]")
        for item in items:
            parent = item.find_element(By.XPATH, "./ancestor::div[contains(@class, 'HistoryItem') or position()=1]")
            html = parent.get_attribute('outerHTML')[:1000]
            print(f"   HTML del item:")
            print(f"   {html[:500]}...")

        # 8. Capturar screenshot
        print("\n8. Capturando screenshot...")
        screenshot_path = "c:/Users/pg_54/OneDrive/Documentos/Proyectos/Tecnoandina/inveb/test_answer_form_result.png"
        driver.save_screenshot(screenshot_path)
        print(f"   Screenshot guardado: {screenshot_path}")

        # 9. Resumen
        print("\n" + "="*60)
        print("RESUMEN:")
        print("="*60)
        if len(answer_forms) > 0:
            print("✓ El formulario de respuesta ESTÁ visible")
        else:
            print("✗ El formulario de respuesta NO está visible")
            print("\nDiagnóstico:")
            if len(consulta_elements) > 0:
                print("  - La información de consulta SÍ se muestra (Área Consultada)")
            else:
                print("  - La información de consulta NO se muestra")

            if len(por_responder) > 0:
                print("  - El estado 'Por Responder' SÍ se muestra")
            else:
                print("  - El estado 'Por Responder' NO se muestra")

        # Mantener navegador abierto 30 segundos para inspección visual
        print("\nNavegador se cerrará en 30 segundos...")
        time.sleep(30)

    except Exception as e:
        print(f"Error: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot("c:/Users/pg_54/OneDrive/Documentos/Proyectos/Tecnoandina/inveb/test_answer_form_error.png")
        time.sleep(10)
    finally:
        driver.quit()

if __name__ == "__main__":
    main()
