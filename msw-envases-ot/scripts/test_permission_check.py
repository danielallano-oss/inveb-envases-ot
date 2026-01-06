#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Test de permisos para formulario de respuesta a consultas
Verifica que solo usuarios del area consultada pueden ver el formulario
"""

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
import time
import requests

def get_user_info(rut, password):
    """Obtiene info del usuario desde API"""
    try:
        r = requests.post('http://localhost:8001/api/v1/auth/login',
                         json={'rut': rut, 'password': password})
        if r.status_code == 200:
            return r.json().get('user', {})
    except:
        pass
    return {}

def test_with_user(rut, password):
    """Prueba el formulario con un usuario especifico"""
    user_info = get_user_info(rut, password)
    print(f"\n{'='*60}")
    print(f"Usuario: {user_info.get('nombre', 'N/A')} {user_info.get('apellido', '')}")
    print(f"Role: {user_info.get('role_nombre', 'N/A')}")
    print(f"work_space_id: {user_info.get('work_space_id', 'N/A')}")
    print('='*60)

    chrome_options = Options()
    chrome_options.add_argument("--start-maximized")
    driver = webdriver.Chrome(options=chrome_options)
    wait = WebDriverWait(driver, 15)

    try:
        # 1. Login
        print("1. Navegando a localhost:3000...")
        driver.get("http://localhost:3000")
        time.sleep(2)

        print(f"2. Haciendo login con {rut}...")
        rut_input = wait.until(EC.presence_of_element_located((By.ID, "rut")))
        rut_input.send_keys(rut)
        driver.find_element(By.ID, "password").send_keys(password)
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        time.sleep(4)

        # 2. Buscar OT 26609
        print("3. Buscando OT 26609...")
        inputs = driver.find_elements(By.TAG_NAME, "input")
        for inp in inputs:
            try:
                placeholder = inp.get_attribute("placeholder") or ""
                if "ID" in placeholder:
                    inp.clear()
                    inp.send_keys("26609")
                    break
            except:
                pass

        time.sleep(1)
        buttons = driver.find_elements(By.TAG_NAME, "button")
        for btn in buttons:
            if "Buscar" in btn.text:
                btn.click()
                break
        time.sleep(3)

        # 3. Click en Ver OT
        print("4. Click en Ver OT...")
        ver_buttons = driver.find_elements(By.CSS_SELECTOR, "button[title='Ver OT']")
        if ver_buttons:
            ver_buttons[0].click()
        time.sleep(4)

        # 4. Verificar si aparece el formulario
        print("\n5. Verificando formulario de respuesta...")
        page_source = driver.page_source

        has_responder_form = "Responder Consulta" in page_source
        has_textarea = len(driver.find_elements(By.CSS_SELECTOR, "textarea")) > 0
        has_enviar_btn = "Enviar Respuesta" in page_source

        # Verificar el area consultada
        consulta_info = "Area Consultada" in page_source or "Consultada" in page_source

        print(f"   - Info de consulta visible: {'SI' if consulta_info else 'NO'}")
        print(f"   - Titulo 'Responder Consulta': {'SI' if has_responder_form else 'NO'}")
        print(f"   - Textarea presente: {'SI' if has_textarea else 'NO'}")
        print(f"   - Boton 'Enviar Respuesta': {'SI' if has_enviar_btn else 'NO'}")

        # Screenshot
        screenshot_name = f"test_permission_{rut.replace('-','_')}.png"
        screenshot_path = f"c:/Users/pg_54/OneDrive/Documentos/Proyectos/Tecnoandina/inveb/{screenshot_name}"
        driver.save_screenshot(screenshot_path)
        print(f"\n   Screenshot: {screenshot_path}")

        # Resultado
        print("\n" + "="*60)
        user_ws = user_info.get('work_space_id')
        consulted_ws = 2  # La consulta en OT 26609 es para area 2

        should_see_form = (user_ws == consulted_ws) or (user_ws == 4 and consulted_ws in [4, 5])
        form_visible = has_responder_form and has_enviar_btn

        if should_see_form:
            if form_visible:
                print(f"[OK] Usuario area {user_ws} PUEDE ver formulario (consulta area {consulted_ws})")
                result = "PASS"
            else:
                print(f"[ERROR] Usuario area {user_ws} DEBERIA ver formulario pero NO lo ve")
                result = "FAIL"
        else:
            if not form_visible:
                print(f"[OK] Usuario area {user_ws} NO puede ver formulario (consulta area {consulted_ws})")
                result = "PASS"
            else:
                print(f"[ERROR] Usuario area {user_ws} NO deberia ver formulario pero SI lo ve")
                result = "FAIL"
        print("="*60)

        time.sleep(5)
        return result

    except Exception as e:
        print(f"Error: {e}")
        import traceback
        traceback.print_exc()
        return "ERROR"
    finally:
        driver.quit()

def main():
    print("\n" + "="*70)
    print("TEST DE PERMISOS - FORMULARIO RESPONDER CONSULTA")
    print("="*70)
    print("La consulta en OT 26609 es para work_space_id = 2")
    print("Solo usuarios del area 2 (o area 4 para areas 4/5) pueden responder")

    # Test con usuario del area 2 (Jefe Desarrollo)
    result1 = test_with_user("20649380-1", "123123")

    print("\n\n" + "="*70)
    print("RESUMEN DE TESTS")
    print("="*70)
    print(f"Usuario 20649380-1 (area 2): {result1}")

if __name__ == "__main__":
    main()
