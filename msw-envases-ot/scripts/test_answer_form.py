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
import json

def main():
    # Configurar Chrome
    chrome_options = Options()
    chrome_options.add_argument("--start-maximized")

    driver = webdriver.Chrome(options=chrome_options)
    wait = WebDriverWait(driver, 10)

    try:
        # 1. Ir a la página de login
        print("1. Navegando a localhost:3000...")
        driver.get("http://localhost:3000")
        time.sleep(2)

        # 2. Login
        print("2. Haciendo login con 20649380-1...")
        rut_input = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "input[placeholder*='RUT']")))
        rut_input.send_keys("20649380-1")

        password_input = driver.find_element(By.CSS_SELECTOR, "input[type='password']")
        password_input.send_keys("123123")

        login_button = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        login_button.click()
        time.sleep(3)

        # 3. Buscar la OT 26609 y hacer click en gestionar
        print("3. Buscando OT 26609...")

        # Buscar por ID
        search_input = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "input[placeholder*='ID']")))
        search_input.clear()
        search_input.send_keys("26609")

        # Click en buscar
        search_button = driver.find_element(By.XPATH, "//button[contains(text(), 'Buscar')]")
        search_button.click()
        time.sleep(2)

        # 4. Click en gestionar OT
        print("4. Haciendo click en Gestionar...")
        gestionar_button = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Gestionar')]")))
        gestionar_button.click()
        time.sleep(3)

        # 5. Verificar el historial
        print("5. Verificando historial de gestión...")

        # Buscar el panel de historial
        history_panel = wait.until(EC.presence_of_element_located((By.XPATH, "//div[contains(text(), 'Historial de Gestion')]")))
        print(f"   Panel de historial encontrado: {history_panel.text}")

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

        # 6. Verificar datos del API
        print("\n6. Verificando respuesta del API...")

        # Obtener el localStorage para el token
        token = driver.execute_script("return localStorage.getItem('inveb_token');")
        print(f"   Token encontrado: {'Sí' if token else 'No'}")

        # Hacer request al API directamente desde el navegador
        api_response = driver.execute_script("""
            return fetch('http://localhost:8001/api/v1/work-orders/26609/management', {
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('inveb_token')
                }
            }).then(r => r.json());
        """)
        time.sleep(2)

        # Obtener la respuesta
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

        print(f"   Respuesta del API:")
        if api_response and 'history' in api_response:
            for item in api_response['history'][:3]:
                print(f"   - ID: {item['id']}, Type: {item.get('management_type_id', 'N/A')}, Answer: {item.get('answer', 'N/A')}")
                if item.get('management_type_id') == 2:
                    print(f"     Consulta encontrada! consulted_work_space_name: {item.get('consulted_work_space_name')}")

        # 7. Capturar screenshot
        print("\n7. Capturando screenshot...")
        driver.save_screenshot("test_answer_form_result.png")
        print("   Screenshot guardado como test_answer_form_result.png")

        # 8. Verificar el HTML del historial
        print("\n8. Inspeccionando HTML del historial...")
        history_html = driver.execute_script("""
            var historyList = document.querySelector('[class*="HistoryList"]');
            if (!historyList) {
                // Buscar por contenido
                var divs = document.querySelectorAll('div');
                for (var i = 0; i < divs.length; i++) {
                    if (divs[i].textContent.includes('Cual archivo?')) {
                        return divs[i].outerHTML.substring(0, 2000);
                    }
                }
            }
            return historyList ? historyList.outerHTML.substring(0, 2000) : 'No encontrado';
        """)
        print(f"   HTML del historial (primeros 500 chars):")
        print(f"   {history_html[:500] if history_html else 'No encontrado'}...")

        print("\n" + "="*50)
        print("RESUMEN:")
        print("="*50)
        if len(answer_forms) > 0:
            print("✓ El formulario de respuesta está visible")
        else:
            print("✗ El formulario de respuesta NO está visible")
            print("  Posibles causas:")
            print("  - El campo management_type_id no está llegando correctamente")
            print("  - La condición isPendingConsulta no se cumple")
            print("  - Hay un error de renderizado en React")

        input("\nPresiona Enter para cerrar el navegador...")

    except Exception as e:
        print(f"Error: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot("test_answer_form_error.png")
        input("\nPresiona Enter para cerrar...")
    finally:
        driver.quit()

if __name__ == "__main__":
    main()
