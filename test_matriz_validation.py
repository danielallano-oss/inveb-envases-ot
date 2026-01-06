"""
Test para verificar que el campo Matriz NO es obligatorio en React
"""
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
import time

# Configurar Chrome
options = Options()
# options.add_argument('--headless')  # Comentado para ver el navegador

driver = webdriver.Chrome(options=options)
wait = WebDriverWait(driver, 15)

try:
    # 1. Ir a la pagina de login
    print("1. Navegando a la pagina de login...")
    driver.get("http://localhost:3000/login")
    time.sleep(2)

    # 2. Iniciar sesion - usar ID en lugar de name
    print("2. Iniciando sesion...")
    rut_input = wait.until(EC.presence_of_element_located((By.ID, "rut")))
    rut_input.clear()
    rut_input.send_keys("11334692-2")

    password_input = driver.find_element(By.ID, "password")
    password_input.clear()
    password_input.send_keys("123123")

    # Buscar y hacer clic en el boton de login
    login_button = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
    login_button.click()
    print("   Esperando respuesta del login...")
    time.sleep(4)

    # Verificar si hubo error de login
    current_url = driver.current_url
    print(f"   URL actual: {current_url}")

    # 3. Navegar a crear orden
    print("3. Navegando a crear orden de trabajo...")
    driver.get("http://localhost:3000/work-orders/create")
    time.sleep(3)

    # 4. Hacer clic en "Crear Detalle +"
    print("4. Abriendo modal de crear detalle...")
    try:
        crear_detalle_btn = wait.until(EC.element_to_be_clickable(
            (By.XPATH, "//button[contains(text(), 'Crear Detalle') or contains(text(), 'Detalle +')]")
        ))
        crear_detalle_btn.click()
    except:
        btns = driver.find_elements(By.TAG_NAME, "button")
        for btn in btns:
            if "detalle" in btn.text.lower():
                print(f"   Encontrado boton: {btn.text}")
                btn.click()
                break

    time.sleep(2)

    # 5. Verificar que el modal esta abierto
    print("5. Verificando modal...")
    try:
        modal = wait.until(EC.presence_of_element_located((By.XPATH, "//*[contains(text(), 'Crear Detalle') or contains(text(), 'Editar Detalle')]")))
        print(f"   Modal encontrado")
    except:
        print("   No se encontro el modal")
        driver.save_screenshot("no_modal.png")

    # 6. Buscar campo Matriz
    print("6. Buscando campo Matriz...")
    time.sleep(1)

    matriz_select = driver.find_element(By.NAME, "matriz")
    driver.execute_script("arguments[0].scrollIntoView(true);", matriz_select)
    time.sleep(1)

    # 7. Verificar el label del campo Matriz
    print("7. Verificando label de Matriz...")
    try:
        matriz_container = matriz_select.find_element(By.XPATH, "./..")
        matriz_label = matriz_container.find_element(By.TAG_NAME, "label")
        label_text = matriz_label.text
        print(f"   Label del campo Matriz: '{label_text}'")

        has_asterisk = '*' in label_text
        asterisk_spans = matriz_container.find_elements(By.XPATH, ".//span[contains(@style, 'dc3545')]")
        print(f"   Tiene asterisco en label? {has_asterisk}")
        print(f"   Tiene span de error? {len(asterisk_spans) > 0}")
    except Exception as e:
        print(f"   Error buscando label: {e}")

    # 8. Hacer clic en Guardar Detalle sin llenar campos
    print("8. Intentando guardar sin llenar campos...")
    try:
        guardar_btn = driver.find_element(By.XPATH, "//button[contains(text(), 'Guardar Detalle')]")
        driver.execute_script("arguments[0].scrollIntoView(true);", guardar_btn)
        time.sleep(0.5)
        guardar_btn.click()
        time.sleep(1)
    except Exception as e:
        print(f"   Error al guardar: {e}")

    # 9. Verificar si aparece error de matriz
    print("9. Verificando errores de validacion...")

    # Buscar mensaje de error especifico de matriz
    matriz_error = driver.find_elements(By.XPATH, "//*[contains(text(), 'Seleccione matriz')]")

    if matriz_error:
        print(f"   ERROR: Todavia aparece validacion de matriz requerido!")
        print(f"   Mensaje encontrado: {matriz_error[0].text}")
        driver.save_screenshot("matriz_error.png")
        print("   Screenshot guardado como matriz_error.png")
    else:
        print("   OK: No aparece error de matriz (campo es opcional)")

    # Verificar en el HTML
    all_page_text = driver.page_source
    if "Seleccione matriz" in all_page_text:
        print("   ENCONTRADO: 'Seleccione matriz' en el HTML de la pagina")
    else:
        print("   NO encontrado: 'Seleccione matriz' en el HTML")

    # Tomar screenshot final
    driver.save_screenshot("test_final.png")
    print("\n   Screenshot final guardado como test_final.png")

except Exception as e:
    print(f"Error: {e}")
    import traceback
    traceback.print_exc()
    driver.save_screenshot("error_screenshot.png")

finally:
    print("\nCerrando navegador...")
    driver.quit()
