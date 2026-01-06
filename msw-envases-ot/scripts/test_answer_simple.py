#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Script simplificado para verificar el formulario de respuesta a consultas
"""

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
import time

def main():
    chrome_options = Options()
    chrome_options.add_argument("--start-maximized")

    driver = webdriver.Chrome(options=chrome_options)
    wait = WebDriverWait(driver, 20)

    try:
        print("1. Navegando a localhost:3000...")
        driver.get("http://localhost:3000")
        time.sleep(3)

        print("2. Haciendo login...")
        rut_input = wait.until(EC.presence_of_element_located((By.ID, "rut")))
        rut_input.send_keys("20649380-1")
        driver.find_element(By.ID, "password").send_keys("123123")
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        time.sleep(5)

        print("3. Navegando directamente a gestionar OT 26609...")
        # En lugar de buscar, usar la URL directa si existe, o hacer clic manual

        # Primero buscar el input de ID
        time.sleep(2)
        inputs = driver.find_elements(By.TAG_NAME, "input")
        for inp in inputs:
            try:
                placeholder = inp.get_attribute("placeholder") or ""
                if "ID" in placeholder:
                    inp.clear()
                    inp.send_keys("26609")
                    print(f"   Input encontrado con placeholder: {placeholder}")
                    break
            except:
                pass

        # Click en Buscar
        time.sleep(1)
        buttons = driver.find_elements(By.TAG_NAME, "button")
        for btn in buttons:
            if "Buscar" in btn.text:
                btn.click()
                print("   Click en Buscar")
                break

        time.sleep(3)

        # Click en Ver OT (icono de lupa)
        print("4. Buscando boton Ver OT (lupa)...")
        # Buscar por title="Ver OT"
        ver_buttons = driver.find_elements(By.CSS_SELECTOR, "button[title='Ver OT']")
        if ver_buttons:
            ver_buttons[0].click()
            print(f"   Click en Ver OT (encontrados: {len(ver_buttons)})")
        else:
            # Intentar con el emoji de lupa
            buttons = driver.find_elements(By.TAG_NAME, "button")
            for btn in buttons:
                if "🔍" in btn.text or "Ver" in btn.get_attribute("title") or "":
                    btn.click()
                    print("   Click en boton con lupa")
                    break

        time.sleep(5)

        print("\n5. Verificando elementos en la pagina...")

        # Verificar la existencia de elementos clave
        page_source = driver.page_source

        checks = [
            ("Area Consultada", "Area Consultada" in page_source or "Área Consultada" in page_source),
            ("Por Responder", "Por Responder" in page_source),
            ("Responder Consulta", "Responder Consulta" in page_source),
            ("Enviar Respuesta", "Enviar Respuesta" in page_source),
            ("Cual archivo?", "Cual archivo?" in page_source),
        ]

        print("\n   Resultados de verificacion:")
        for name, found in checks:
            status = "[OK]" if found else "[NO]"
            print(f"   {status} {name}: {'Encontrado' if found else 'NO encontrado'}")

        # Buscar textareas
        textareas = driver.find_elements(By.TAG_NAME, "textarea")
        print(f"\n   Textareas encontrados: {len(textareas)}")
        for i, ta in enumerate(textareas):
            placeholder = ta.get_attribute("placeholder") or "sin placeholder"
            print(f"   [{i}] placeholder: {placeholder}")

        # Screenshot
        screenshot_path = "c:/Users/pg_54/OneDrive/Documentos/Proyectos/Tecnoandina/inveb/test_answer_result.png"
        driver.save_screenshot(screenshot_path)
        print(f"\n   Screenshot guardado: {screenshot_path}")

        # API check
        print("\n6. Verificando datos del API...")
        api_data = driver.execute_async_script("""
            var callback = arguments[arguments.length - 1];
            var token = localStorage.getItem('inveb_token');
            console.log('Token:', token);
            fetch('http://localhost:8001/api/v1/work-orders/26609/management', {
                headers: { 'Authorization': 'Bearer ' + token }
            })
            .then(r => r.json())
            .then(data => callback(JSON.stringify(data)))
            .catch(err => callback('Error: ' + err));
        """)

        if api_data:
            import json
            try:
                data = json.loads(api_data)
                if 'history' in data:
                    for item in data['history'][:3]:
                        print(f"   ID: {item['id']}, TypeID: {item.get('management_type_id')}, HasAnswer: {bool(item.get('answer'))}")
            except:
                print(f"   Raw response: {api_data[:200]}")

        print("\n" + "="*60)
        if "Responder Consulta" in page_source:
            print("SUCCESS: El formulario de respuesta esta visible!")
        else:
            print("PROBLEMA: El formulario NO esta visible")
            print("\nPosibles causas:")
            if "Area Consultada" in page_source or "Área Consultada" in page_source:
                print("  - La info de consulta SI se muestra")
            else:
                print("  - La info de consulta NO se muestra (management_type_id no llega?)")
        print("="*60)

        # Esperar para inspeccion visual
        print("\nNavegador abierto 60 segundos para verificacion manual...")
        time.sleep(60)

    except Exception as e:
        print(f"Error: {e}")
        import traceback
        traceback.print_exc()
        time.sleep(30)
    finally:
        driver.quit()

if __name__ == "__main__":
    main()
