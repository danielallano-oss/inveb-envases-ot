#!/usr/bin/env python3
"""
Verifica que el botón de editar muestra aparezca para el rol Ingeniero
en la versión refactorizada (localhost:3000)
"""
import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options

def main():
    # Configurar Chrome en modo headless
    options = Options()
    options.add_argument('--headless')
    options.add_argument('--no-sandbox')
    options.add_argument('--disable-dev-shm-usage')
    options.add_argument('--window-size=1920,1080')

    driver = webdriver.Chrome(options=options)
    wait = WebDriverWait(driver, 10)

    try:
        print("=" * 60)
        print("VERIFICACIÓN: Botón Editar Muestra para Ingeniero")
        print("=" * 60)

        # 1. Ir a la página de login
        print("\n1. Accediendo a localhost:3000...")
        driver.get("http://localhost:3000")
        time.sleep(2)

        # 2. Login como Ingeniero
        print("2. Haciendo login como Ingeniero (8106237-4)...")
        rut_input = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "input[placeholder*='RUT']")))
        rut_input.clear()
        rut_input.send_keys("8106237-4")

        password_input = driver.find_element(By.CSS_SELECTOR, "input[type='password']")
        password_input.clear()
        password_input.send_keys("123123")

        login_btn = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        login_btn.click()
        time.sleep(3)

        # 3. Verificar que el login fue exitoso
        print("3. Verificando login exitoso...")
        try:
            user_info = wait.until(EC.presence_of_element_located((By.XPATH, "//*[contains(text(), 'Ingeniero')]")))
            print(f"   ✓ Login exitoso - Usuario: Ingeniero")
        except:
            print("   ✗ Error: No se pudo verificar el login")
            driver.save_screenshot("error_login.png")
            return

        # 4. Buscar una OT que tenga muestras (26591 o 26609)
        print("4. Navegando a gestionar OT con muestras...")

        # Ir directamente a gestionar OT 26591
        driver.get("http://localhost:3000")
        time.sleep(2)

        # Buscar en la tabla de OTs
        try:
            # Buscar la OT 26591 o cualquier OT disponible
            ot_link = wait.until(EC.presence_of_element_located((By.XPATH, "//td[contains(text(), '26591')]")))
            print(f"   ✓ Encontrada OT 26591")

            # Buscar el botón gestionar en la misma fila
            row = ot_link.find_element(By.XPATH, "./..")
            gestionar_btn = row.find_element(By.XPATH, ".//button[contains(@title, 'Gestionar') or contains(text(), 'Gestionar')]")
            gestionar_btn.click()
            time.sleep(2)
        except Exception as e:
            print(f"   ! No se encontró OT 26591, buscando otra OT...")
            # Intentar con cualquier OT que tenga botón gestionar
            try:
                gestionar_btns = driver.find_elements(By.XPATH, "//button[contains(@title, 'Gestionar')]")
                if gestionar_btns:
                    gestionar_btns[0].click()
                    time.sleep(2)
                else:
                    print("   ✗ No se encontraron OTs para gestionar")
                    return
            except:
                print(f"   ✗ Error: {e}")
                driver.save_screenshot("error_ot.png")
                return

        # 5. Expandir la sección "Gestionar Muestras"
        print("5. Expandiendo sección 'Gestionar Muestras'...")
        try:
            muestras_header = wait.until(EC.element_to_be_clickable(
                (By.XPATH, "//*[contains(text(), 'Gestionar Muestras')]")
            ))
            muestras_header.click()
            time.sleep(2)
            print("   ✓ Sección de muestras expandida")
        except Exception as e:
            print(f"   ✗ Error al expandir muestras: {e}")
            driver.save_screenshot("error_muestras.png")
            return

        # 6. Verificar que existe el botón de editar (✎)
        print("6. Buscando botón de editar muestra (✎)...")
        try:
            # Buscar el botón con el símbolo de editar
            edit_btn = wait.until(EC.presence_of_element_located(
                (By.XPATH, "//button[contains(text(), '✎') or @title='Editar muestra']")
            ))
            print("   ✓ ¡ÉXITO! Botón de editar muestra encontrado")

            # Verificar que es clickeable
            if edit_btn.is_displayed() and edit_btn.is_enabled():
                print("   ✓ El botón está visible y habilitado")

                # Intentar hacer click para abrir el modal
                print("\n7. Probando apertura del modal de edición...")
                edit_btn.click()
                time.sleep(2)

                # Buscar el modal
                try:
                    modal_title = wait.until(EC.presence_of_element_located(
                        (By.XPATH, "//*[contains(text(), 'Editar Muestra')]")
                    ))
                    print("   ✓ ¡Modal de edición abierto correctamente!")

                    # Verificar campos del modal
                    campos = ["CAD", "Cartón", "Planta de Corte", "Cantidad"]
                    for campo in campos:
                        try:
                            driver.find_element(By.XPATH, f"//*[contains(text(), '{campo}')]")
                            print(f"      - Campo '{campo}' presente")
                        except:
                            pass

                    # Cerrar modal
                    close_btn = driver.find_element(By.XPATH, "//button[contains(text(), '×') or contains(text(), 'Cancelar')]")
                    close_btn.click()
                    time.sleep(1)

                except Exception as e:
                    print(f"   ! Modal no se abrió: {e}")
            else:
                print("   ! El botón no está visible o está deshabilitado")

        except Exception as e:
            print(f"   ✗ ERROR: Botón de editar NO encontrado")
            print(f"   Detalle: {e}")

            # Guardar screenshot para debug
            driver.save_screenshot("error_no_edit_button.png")
            print("   Screenshot guardado: error_no_edit_button.png")

            # Mostrar los botones que sí existen en la sección de muestras
            print("\n   Botones encontrados en acciones:")
            buttons = driver.find_elements(By.CSS_SELECTOR, "button")
            for btn in buttons:
                text = btn.text.strip()
                title = btn.get_attribute("title") or ""
                if text or title:
                    print(f"      - '{text}' (title: {title})")

        print("\n" + "=" * 60)
        print("VERIFICACIÓN COMPLETADA")
        print("=" * 60)

    except Exception as e:
        print(f"\nError general: {e}")
        driver.save_screenshot("error_general.png")
    finally:
        driver.quit()

if __name__ == "__main__":
    main()
