#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Script para probar creacion de usuario duplicado - Version 3
"""

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.action_chains import ActionChains
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

        print("3. Buscando menu de navegacion...")

        # Imprimir todos los elementos del menu para debug
        nav_elements = driver.find_elements(By.CSS_SELECTOR, "nav *, header *")
        print(f"   Elementos de navegacion encontrados: {len(nav_elements)}")

        # Buscar elementos que contengan texto de menu
        all_elements = driver.find_elements(By.XPATH, "//*[contains(text(), 'Mantenedores') or contains(text(), 'Usuario')]")
        print(f"   Elementos con 'Mantenedores' o 'Usuario': {len(all_elements)}")
        for el in all_elements[:10]:
            try:
                tag = el.tag_name
                text = el.text[:50] if el.text else "sin texto"
                print(f"      <{tag}> {text}")
            except:
                pass

        # Intentar hacer hover o click en Mantenedores
        print("\n4. Intentando acceder a Mantenedores...")

        # Buscar por diferentes selectores
        selectors = [
            "//button[contains(text(), 'Mantenedores')]",
            "//*[contains(text(), 'Mantenedores')]",
            "//a[contains(text(), 'Mantenedores')]",
            "//div[contains(text(), 'Mantenedores')]",
        ]

        mantenedores_clicked = False
        for selector in selectors:
            try:
                elements = driver.find_elements(By.XPATH, selector)
                for el in elements:
                    if el.is_displayed():
                        # Intentar hover
                        actions = ActionChains(driver)
                        actions.move_to_element(el).perform()
                        time.sleep(0.5)
                        el.click()
                        print(f"   Click en Mantenedores usando: {selector}")
                        mantenedores_clicked = True
                        break
                if mantenedores_clicked:
                    break
            except Exception as e:
                pass

        time.sleep(2)

        # Buscar submenu de Usuarios
        print("\n5. Buscando opcion Usuarios...")
        driver.save_screenshot("c:/Users/pg_54/OneDrive/Documentos/Proyectos/Tecnoandina/inveb/test_menu_open.png")

        # Buscar enlaces o botones de usuarios
        user_selectors = [
            "//a[contains(text(), 'Usuarios')]",
            "//button[contains(text(), 'Usuarios')]",
            "//*[contains(text(), 'Usuarios')]",
            "//a[contains(@href, 'usuario')]",
        ]

        usuarios_clicked = False
        for selector in user_selectors:
            try:
                elements = driver.find_elements(By.XPATH, selector)
                for el in elements:
                    if el.is_displayed():
                        el.click()
                        print(f"   Click en Usuarios usando: {selector}")
                        usuarios_clicked = True
                        break
                if usuarios_clicked:
                    break
            except:
                pass

        time.sleep(3)

        # Verificar si llegamos a la pagina de usuarios
        print("\n6. Verificando pagina actual...")
        current_url = driver.current_url
        page_title = driver.title
        print(f"   URL: {current_url}")
        print(f"   Title: {page_title}")

        # Buscar boton de Nuevo Usuario
        print("\n7. Buscando boton Nuevo Usuario...")
        nuevo_selectors = [
            "//button[contains(text(), 'Nuevo Usuario')]",
            "//button[contains(text(), 'Nuevo')]",
            "//button[contains(text(), '+ Nuevo')]",
        ]

        for selector in nuevo_selectors:
            try:
                btn = driver.find_element(By.XPATH, selector)
                if btn.is_displayed():
                    btn.click()
                    print(f"   Click en Nuevo Usuario usando: {selector}")
                    break
            except:
                pass

        time.sleep(2)
        driver.save_screenshot("c:/Users/pg_54/OneDrive/Documentos/Proyectos/Tecnoandina/inveb/test_form_open.png")

        # Verificar si el formulario esta abierto
        print("\n8. Verificando formulario...")

        # Buscar inputs del formulario
        inputs = driver.find_elements(By.TAG_NAME, "input")
        print(f"   Inputs encontrados: {len(inputs)}")

        for inp in inputs:
            try:
                name = inp.get_attribute("name") or "sin-name"
                placeholder = inp.get_attribute("placeholder") or "sin-placeholder"
                inp_type = inp.get_attribute("type") or "text"
                print(f"      name={name}, type={inp_type}, placeholder={placeholder[:30]}")
            except:
                pass

        # Llenar el formulario si hay campos
        print("\n9. Llenando formulario...")

        form_filled = False
        for inp in inputs:
            try:
                name = inp.get_attribute("name") or ""
                placeholder = inp.get_attribute("placeholder") or ""
                inp_type = inp.get_attribute("type") or ""

                if name == "rut" or "RUT" in placeholder or "12.345" in placeholder:
                    inp.clear()
                    inp.send_keys("16.532.981-3")
                    print("   RUT: 16.532.981-3")
                    form_filled = True
                elif name == "nombre":
                    inp.clear()
                    inp.send_keys("Pablo")
                    print("   Nombre: Pablo")
                elif name == "apellido":
                    inp.clear()
                    inp.send_keys("Gutierrez")
                    print("   Apellido: Gutierrez")
                elif name == "email" or inp_type == "email":
                    inp.clear()
                    inp.send_keys("pg.54gm@gmail.com")
                    print("   Email: pg.54gm@gmail.com")
                elif name == "telefono":
                    inp.clear()
                    inp.send_keys("+563961434410")
                    print("   Telefono: +563961434410")
                elif name == "password" or inp_type == "password":
                    inp.clear()
                    inp.send_keys("Test*12312")
                    print("   Password: Test*12312")
            except Exception as e:
                pass

        # Seleccionar rol
        try:
            from selenium.webdriver.support.ui import Select
            selects = driver.find_elements(By.TAG_NAME, "select")
            for sel in selects:
                name = sel.get_attribute("name") or ""
                if "role" in name.lower():
                    select = Select(sel)
                    for opt in select.options:
                        if "Administrador" in opt.text:
                            select.select_by_visible_text(opt.text)
                            print(f"   Rol: {opt.text}")
                            break
        except:
            pass

        time.sleep(1)

        if not form_filled:
            print("\n   FORMULARIO NO ENCONTRADO - guardando estado actual")
            driver.save_screenshot("c:/Users/pg_54/OneDrive/Documentos/Proyectos/Tecnoandina/inveb/test_no_form.png")
            print("\n   Esperando 60 segundos para inspeccion manual...")
            time.sleep(60)
            return

        # Click en Crear
        print("\n10. Haciendo click en Crear...")
        try:
            crear_btn = driver.find_element(By.XPATH, "//button[contains(text(), 'Crear')]")
            crear_btn.click()
            print("   Click en Crear")
        except Exception as e:
            print(f"   Error al hacer click en Crear: {e}")

        time.sleep(5)

        # Verificar mensaje de error
        print("\n11. Verificando mensaje de error...")
        page_source = driver.page_source

        error_messages = [
            "Ya existe un usuario con este RUT",
            "Ya existe un usuario",
            "Ya existe",
            "RUT duplicado",
        ]

        error_found = False
        for msg in error_messages:
            if msg in page_source:
                print(f"   MENSAJE ENCONTRADO: '{msg}'")
                error_found = True
                break

        # Buscar elementos de alerta
        alerts = driver.find_elements(By.CSS_SELECTOR, "[class*='alert'], [class*='error'], [class*='Alert'], [class*='Error']")
        for alert in alerts:
            text = alert.text.strip()
            if text:
                print(f"   Alerta encontrada: {text}")
                if "Ya existe" in text:
                    error_found = True

        driver.save_screenshot("c:/Users/pg_54/OneDrive/Documentos/Proyectos/Tecnoandina/inveb/test_result.png")

        # Logs de consola
        print("\n12. Logs de consola:")
        logs = driver.get_log('browser')
        for log in logs[-10:]:
            if 'API' in log['message'] or 'Error' in log['message'] or '400' in log['message']:
                print(f"   {log['message'][:200]}")

        # Resumen
        print("\n" + "="*60)
        if error_found:
            print("RESULTADO: Mensaje de error VISIBLE")
        else:
            print("RESULTADO: Mensaje de error NO visible")
        print("="*60)

        print("\nEsperando 60 segundos para inspeccion...")
        time.sleep(60)

    except Exception as e:
        print(f"\nError: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot("c:/Users/pg_54/OneDrive/Documentos/Proyectos/Tecnoandina/inveb/test_error.png")
        time.sleep(30)
    finally:
        driver.quit()

if __name__ == "__main__":
    main()
