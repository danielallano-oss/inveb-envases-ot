# -*- coding: utf-8 -*-
"""
Script para editar la OT 26591 en la version local (Laravel)
y completar los campos necesarios para cotizar.
"""
import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.keys import Keys

def edit_ot_laravel():
    """Editar OT 26591 en Laravel (localhost:8080)"""

    chrome_options = Options()
    chrome_options.add_argument("--start-maximized")

    driver = webdriver.Chrome(options=chrome_options)
    wait = WebDriverWait(driver, 15)

    try:
        print("=" * 60)
        print("EDITANDO OT 26591 EN VERSION LOCAL (LARAVEL)")
        print("=" * 60)

        # 1. Login
        print("\n1. Navegando a localhost:8080...")
        driver.get("http://localhost:8080/login")
        time.sleep(2)

        print("2. Iniciando sesion como Ingeniero...")
        rut_field = wait.until(EC.presence_of_element_located((By.NAME, "rut")))
        rut_field.clear()
        rut_field.send_keys("8106237-4")

        pass_field = driver.find_element(By.NAME, "password")
        pass_field.clear()
        pass_field.send_keys("123123")

        login_btn = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        login_btn.click()
        time.sleep(3)

        # 2. Ir a editar OT
        print("3. Navegando a editar OT 26591...")
        driver.get("http://localhost:8080/edit-ot-old/26591")
        time.sleep(3)

        # 3. Scroll hasta la seccion de Caracteristicas
        print("4. Buscando seccion de Caracteristicas...")
        driver.execute_script("window.scrollTo(0, 500);")
        time.sleep(1)

        # 4. Verificar campos actuales
        print("\n5. Verificando campos actuales...")

        # Verificar CAD
        try:
            cad_select = driver.find_element(By.ID, "cad_id")
            cad_value = Select(cad_select).first_selected_option.text
            print(f"   CAD actual: {cad_value}")
        except Exception as e:
            print(f"   Error CAD: {e}")
            cad_select = None

        # Verificar Matriz
        try:
            matriz_select = driver.find_element(By.ID, "matriz_id")
            matriz_value = Select(matriz_select).first_selected_option.text
            print(f"   Matriz actual: {matriz_value}")
        except Exception as e:
            print(f"   Error Matriz: {e}")
            matriz_select = None

        # Verificar Carton
        try:
            carton_select = driver.find_element(By.ID, "carton_id")
            carton_value = Select(carton_select).first_selected_option.text
            print(f"   Carton actual: {carton_value}")
        except Exception as e:
            print(f"   Error Carton: {e}")
            carton_select = None

        # Verificar Proceso
        try:
            proceso_select = driver.find_element(By.ID, "process_id")
            proceso_value = Select(proceso_select).first_selected_option.text
            print(f"   Proceso actual: {proceso_value}")
        except Exception as e:
            print(f"   Error Proceso: {e}")
            proceso_select = None

        # Verificar Golpes
        try:
            golpes_largo = driver.find_element(By.ID, "golpes_largo")
            print(f"   Golpes al Largo: {golpes_largo.get_attribute('value')}")
        except:
            golpes_largo = None

        try:
            golpes_ancho = driver.find_element(By.ID, "golpes_ancho")
            print(f"   Golpes al Ancho: {golpes_ancho.get_attribute('value')}")
        except:
            golpes_ancho = None

        # 5. Completar campos faltantes
        print("\n6. Completando campos faltantes...")

        # Seleccionar Matriz si no esta seleccionada
        if matriz_select:
            try:
                select_matriz = Select(matriz_select)
                # Buscar opcion que contenga "Completa" o la primera disponible
                options = [o for o in select_matriz.options if o.get_attribute('value')]
                if options:
                    print(f"   Seleccionando matriz: {options[0].text}")
                    select_matriz.select_by_index(1)  # Primera opcion despues de "Seleccionar"
                    time.sleep(1)
            except Exception as e:
                print(f"   Error seleccionando matriz: {e}")

        # Seleccionar Carton
        if carton_select:
            try:
                select_carton = Select(carton_select)
                # Seleccionar primer carton disponible
                options = [o for o in select_carton.options if o.get_attribute('value')]
                if len(options) > 0:
                    print(f"   Seleccionando carton: {options[0].text}")
                    select_carton.select_by_index(1)
                    time.sleep(1)
            except Exception as e:
                print(f"   Error seleccionando carton: {e}")

        # Seleccionar Proceso
        if proceso_select:
            try:
                select_proceso = Select(proceso_select)
                current = select_proceso.first_selected_option.get_attribute('value')
                if not current:
                    options = [o for o in select_proceso.options if o.get_attribute('value')]
                    if options:
                        print(f"   Seleccionando proceso: {options[0].text}")
                        select_proceso.select_by_index(1)
                        time.sleep(1)
            except Exception as e:
                print(f"   Error seleccionando proceso: {e}")

        # Completar Golpes si estan vacios
        if golpes_largo and not golpes_largo.get_attribute('value'):
            print("   Ingresando golpes al largo: 3")
            golpes_largo.clear()
            golpes_largo.send_keys("3")

        if golpes_ancho and not golpes_ancho.get_attribute('value'):
            print("   Ingresando golpes al ancho: 1")
            golpes_ancho.clear()
            golpes_ancho.send_keys("1")

        # Captura antes de guardar
        driver.save_screenshot("edit_ot_laravel_before_save.png")
        print("\n   Screenshot guardado: edit_ot_laravel_before_save.png")

        # 6. Scroll hasta el boton guardar
        print("\n7. Buscando boton guardar...")
        driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
        time.sleep(1)

        # Buscar boton guardar
        try:
            save_btn = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
            print(f"   Boton encontrado: {save_btn.text}")

            # Hacer click
            print("8. Guardando cambios...")
            save_btn.click()
            time.sleep(3)

            # Verificar resultado
            driver.save_screenshot("edit_ot_laravel_after_save.png")
            print("   Screenshot guardado: edit_ot_laravel_after_save.png")

            # Verificar si hay mensaje de exito o error
            try:
                alert = driver.find_element(By.CSS_SELECTOR, ".alert")
                print(f"   Mensaje: {alert.text}")
            except:
                pass

        except Exception as e:
            print(f"   Error al guardar: {e}")

        print("\n" + "=" * 60)
        print("PROCESO COMPLETADO")
        print("=" * 60)

        # Mantener navegador abierto para verificacion
        print("\nManteniendo navegador abierto 15 segundos...")
        time.sleep(15)

        return True

    except Exception as e:
        print(f"\nERROR: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot("edit_ot_error.png")
        return False

    finally:
        driver.quit()

if __name__ == "__main__":
    success = edit_ot_laravel()
    exit(0 if success else 1)
