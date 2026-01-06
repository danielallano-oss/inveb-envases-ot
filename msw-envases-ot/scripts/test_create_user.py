#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Script para probar creación de usuario en el frontend React
"""

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import Select
import time
import json

def main():
    chrome_options = Options()
    chrome_options.add_argument("--start-maximized")

    # Habilitar logging de consola del navegador
    chrome_options.set_capability('goog:loggingPrefs', {'browser': 'ALL'})

    driver = webdriver.Chrome(options=chrome_options)
    wait = WebDriverWait(driver, 15)

    try:
        # 1. Login
        print("1. Navegando a localhost:3000...")
        driver.get("http://localhost:3000")
        time.sleep(2)

        print("2. Haciendo login con 22222222-2...")
        rut_input = wait.until(EC.presence_of_element_located((By.ID, "rut")))
        rut_input.clear()
        rut_input.send_keys("22222222-2")

        password_input = driver.find_element(By.ID, "password")
        password_input.clear()
        password_input.send_keys("123123")

        login_button = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        login_button.click()
        time.sleep(4)

        # 2. Navegar a Mantenedores -> Usuarios
        print("3. Navegando a Mantenedor de Usuarios...")

        # Click en menú Mantenedores
        mantenedores_menu = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Mantenedores')]")))
        mantenedores_menu.click()
        time.sleep(1)

        # Click en Usuarios
        usuarios_link = wait.until(EC.element_to_be_clickable((By.XPATH, "//a[contains(text(), 'Usuarios')]")))
        usuarios_link.click()
        time.sleep(3)

        # 3. Click en Nuevo Usuario
        print("4. Click en Nuevo Usuario...")
        nuevo_btn = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Nuevo Usuario')]")))
        nuevo_btn.click()
        time.sleep(2)

        # 4. Llenar formulario
        print("5. Llenando formulario...")

        # RUT
        rut_field = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "input[placeholder*='12.345.678-9']")))
        rut_field.clear()
        rut_field.send_keys("16.532.981-3")

        # Nombre
        inputs = driver.find_elements(By.CSS_SELECTOR, "input")
        for inp in inputs:
            placeholder = inp.get_attribute("placeholder") or ""
            name_attr = inp.get_attribute("name") or ""
            if "nombre" in name_attr.lower() and "apellido" not in name_attr.lower():
                inp.clear()
                inp.send_keys("Pablo")
                print(f"   Nombre: Pablo")
                break

        # Apellido
        for inp in inputs:
            name_attr = inp.get_attribute("name") or ""
            if "apellido" in name_attr.lower():
                inp.clear()
                inp.send_keys("Gutierrez")
                print(f"   Apellido: Gutierrez")
                break

        # Email
        for inp in inputs:
            input_type = inp.get_attribute("type") or ""
            name_attr = inp.get_attribute("name") or ""
            if input_type == "email" or "email" in name_attr.lower():
                inp.clear()
                inp.send_keys("pg.54gm@gmail.com")
                print(f"   Email: pg.54gm@gmail.com")
                break

        # Teléfono
        for inp in inputs:
            name_attr = inp.get_attribute("name") or ""
            if "telefono" in name_attr.lower() or "phone" in name_attr.lower():
                inp.clear()
                inp.send_keys("+563961434410")
                print(f"   Telefono: +563961434410")
                break

        # Contraseña
        for inp in inputs:
            input_type = inp.get_attribute("type") or ""
            name_attr = inp.get_attribute("name") or ""
            if input_type == "password" or "password" in name_attr.lower() or "contrasena" in name_attr.lower():
                inp.clear()
                inp.send_keys("Test*123123")
                print(f"   Password: Test*123123")
                break

        time.sleep(1)

        # Rol - buscar select de rol
        print("6. Seleccionando rol...")
        selects = driver.find_elements(By.TAG_NAME, "select")
        for sel in selects:
            name_attr = sel.get_attribute("name") or ""
            if "role" in name_attr.lower() or "rol" in name_attr.lower():
                select_obj = Select(sel)
                # Buscar Administrador
                for option in select_obj.options:
                    if "Administrador" in option.text:
                        select_obj.select_by_visible_text(option.text)
                        print(f"   Rol: {option.text}")
                        break
                break

        time.sleep(1)

        # 5. Interceptar requests para ver errores
        print("7. Configurando interceptor de red...")

        # Guardar screenshot antes de crear
        driver.save_screenshot("c:/Users/pg_54/OneDrive/Documentos/Proyectos/Tecnoandina/inveb/test_before_create.png")

        # 6. Click en Crear
        print("8. Haciendo click en Crear...")
        crear_btn = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Crear')]")))

        # Ejecutar JavaScript para interceptar la respuesta
        driver.execute_script("""
            window.lastApiResponse = null;
            window.lastApiError = null;
            const originalFetch = window.fetch;
            window.fetch = async function(...args) {
                try {
                    const response = await originalFetch.apply(this, args);
                    const clone = response.clone();
                    if (args[0].includes('/mantenedores/users')) {
                        try {
                            const data = await clone.json();
                            window.lastApiResponse = {status: response.status, data: data, url: args[0]};
                        } catch(e) {
                            window.lastApiResponse = {status: response.status, error: 'No JSON', url: args[0]};
                        }
                    }
                    return response;
                } catch(e) {
                    window.lastApiError = e.toString();
                    throw e;
                }
            };
        """)

        crear_btn.click()
        time.sleep(5)

        # 7. Verificar respuesta
        print("\n9. Verificando respuesta del API...")

        api_response = driver.execute_script("return window.lastApiResponse;")
        api_error = driver.execute_script("return window.lastApiError;")

        print(f"   API Response: {json.dumps(api_response, indent=2) if api_response else 'None'}")
        print(f"   API Error: {api_error}")

        # Verificar logs de consola
        print("\n10. Logs de consola del navegador:")
        logs = driver.get_log('browser')
        for log in logs[-10:]:
            print(f"   [{log['level']}] {log['message'][:200]}")

        # Buscar mensajes de error en la página
        print("\n11. Buscando mensajes de error en la página...")
        error_elements = driver.find_elements(By.CSS_SELECTOR, "[class*='error'], [class*='Error'], [role='alert']")
        for el in error_elements:
            text = el.text.strip()
            if text:
                print(f"   Error encontrado: {text}")

        # Screenshot final
        driver.save_screenshot("c:/Users/pg_54/OneDrive/Documentos/Proyectos/Tecnoandina/inveb/test_after_create.png")
        print("\n   Screenshots guardados")

        # Verificar si cambió la URL (indica éxito)
        current_url = driver.current_url
        print(f"\n12. URL actual: {current_url}")

        # Esperar para inspección
        print("\nNavegador abierto 30 segundos para inspección...")
        time.sleep(30)

    except Exception as e:
        print(f"\nError: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot("c:/Users/pg_54/OneDrive/Documentos/Proyectos/Tecnoandina/inveb/test_create_error.png")
        time.sleep(10)
    finally:
        driver.quit()

if __name__ == "__main__":
    main()
