#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Script para probar creacion de usuario duplicado y verificar mensaje de error
"""

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import Select
import time

def main():
    chrome_options = Options()
    chrome_options.add_argument("--start-maximized")
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

        # 2. Navegar directamente a la URL de usuarios
        print("3. Navegando a Mantenedor de Usuarios...")
        driver.get("http://localhost:3000")
        time.sleep(2)

        # Buscar y hacer click en el menu Mantenedores
        try:
            # Intentar con el dropdown
            mantenedores = driver.find_elements(By.XPATH, "//*[contains(text(), 'Mantenedores')]")
            for el in mantenedores:
                if el.is_displayed():
                    el.click()
                    print("   Click en Mantenedores")
                    time.sleep(1)
                    break
        except Exception as e:
            print(f"   Error click Mantenedores: {e}")

        # Click en Usuarios
        try:
            usuarios = wait.until(EC.element_to_be_clickable((By.XPATH, "//a[contains(text(), 'Usuarios')] | //span[contains(text(), 'Usuarios')] | //*[contains(@href, 'usuarios')]")))
            usuarios.click()
            print("   Click en Usuarios")
            time.sleep(3)
        except Exception as e:
            print(f"   Error click Usuarios: {e}")
            # Intentar navegar directamente si hay ruta
            pass

        # 3. Click en Nuevo Usuario
        print("4. Click en Nuevo Usuario...")
        try:
            nuevo_btn = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Nuevo Usuario')]")))
            nuevo_btn.click()
            time.sleep(2)
        except Exception as e:
            print(f"   Error: {e}")

        # 4. Llenar formulario
        print("5. Llenando formulario con usuario duplicado...")

        # RUT - buscar por placeholder
        try:
            rut_field = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "input[placeholder*='12.345.678']")))
            rut_field.clear()
            rut_field.send_keys("16.532.981-3")
            print("   RUT: 16.532.981-3")
        except:
            # Buscar por name
            rut_field = driver.find_element(By.NAME, "rut")
            rut_field.clear()
            rut_field.send_keys("16.532.981-3")
            print("   RUT: 16.532.981-3")

        time.sleep(0.5)

        # Nombre
        try:
            nombre_field = driver.find_element(By.NAME, "nombre")
            nombre_field.clear()
            nombre_field.send_keys("Pablo")
            print("   Nombre: Pablo")
        except Exception as e:
            print(f"   Error nombre: {e}")

        # Apellido
        try:
            apellido_field = driver.find_element(By.NAME, "apellido")
            apellido_field.clear()
            apellido_field.send_keys("Gutierrez")
            print("   Apellido: Gutierrez")
        except Exception as e:
            print(f"   Error apellido: {e}")

        # Email
        try:
            email_field = driver.find_element(By.NAME, "email")
            email_field.clear()
            email_field.send_keys("pg.54gm@gmail.com")
            print("   Email: pg.54gm@gmail.com")
        except Exception as e:
            print(f"   Error email: {e}")

        # Telefono
        try:
            telefono_field = driver.find_element(By.NAME, "telefono")
            telefono_field.clear()
            telefono_field.send_keys("+563961434410")
            print("   Telefono: +563961434410")
        except Exception as e:
            print(f"   Error telefono: {e}")

        # Password
        try:
            password_field = driver.find_element(By.NAME, "password")
            password_field.clear()
            password_field.send_keys("Test*12312")
            print("   Password: Test*12312")
        except Exception as e:
            print(f"   Error password: {e}")

        # Rol
        try:
            role_select = Select(driver.find_element(By.NAME, "role_id"))
            role_select.select_by_visible_text("Administrador")
            print("   Rol: Administrador")
        except Exception as e:
            print(f"   Error rol: {e}")

        time.sleep(1)

        # Screenshot antes de crear
        driver.save_screenshot("c:/Users/pg_54/OneDrive/Documentos/Proyectos/Tecnoandina/inveb/test_before_create.png")
        print("   Screenshot guardado: test_before_create.png")

        # 5. Click en Crear
        print("\n6. Haciendo click en Crear...")
        crear_btn = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Crear')]")))
        crear_btn.click()

        # Esperar respuesta
        print("7. Esperando respuesta del servidor...")
        time.sleep(5)

        # 6. Verificar mensaje de error
        print("\n8. Buscando mensaje de error...")

        # Buscar alertas de error
        page_source = driver.page_source

        # Verificar si hay mensaje de error visible
        error_found = False

        # Buscar por clase de alerta de error
        error_alerts = driver.find_elements(By.CSS_SELECTOR, "[class*='error'], [class*='Error'], [class*='alert']")
        for alert in error_alerts:
            text = alert.text.strip()
            if text and "Ya existe" in text:
                print(f"   ERROR ENCONTRADO: {text}")
                error_found = True

        # Buscar texto especifico en la pagina
        if "Ya existe un usuario con este RUT" in page_source:
            print("   MENSAJE ENCONTRADO EN HTML: 'Ya existe un usuario con este RUT'")
            error_found = True
        elif "Ya existe" in page_source:
            print("   MENSAJE PARCIAL ENCONTRADO: 'Ya existe'")
            error_found = True

        # Verificar logs de consola
        print("\n9. Logs de consola del navegador:")
        logs = driver.get_log('browser')
        for log in logs[-15:]:
            msg = log['message']
            if 'API' in msg or 'Error' in msg or '400' in msg:
                print(f"   [{log['level']}] {msg[:300]}")

        # Screenshot final
        driver.save_screenshot("c:/Users/pg_54/OneDrive/Documentos/Proyectos/Tecnoandina/inveb/test_after_create.png")
        print(f"\n   Screenshot guardado: test_after_create.png")

        # Resumen
        print("\n" + "="*60)
        print("RESUMEN:")
        print("="*60)
        if error_found:
            print("El mensaje de error SI se muestra en la pagina")
        else:
            print("El mensaje de error NO se muestra en la pagina")
            print("\nPosibles causas:")
            print("  - El frontend no recargo con los cambios")
            print("  - El interceptor de axios no esta funcionando")

        # Esperar para inspeccion visual
        print("\nNavegador abierto 60 segundos para inspeccion manual...")
        time.sleep(60)

    except Exception as e:
        print(f"\nError: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot("c:/Users/pg_54/OneDrive/Documentos/Proyectos/Tecnoandina/inveb/test_create_error.png")
        time.sleep(30)
    finally:
        driver.quit()

if __name__ == "__main__":
    main()
