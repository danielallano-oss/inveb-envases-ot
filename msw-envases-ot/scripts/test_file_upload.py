"""
Test de carga de archivos con Selenium
Prueba la funcionalidad de subir archivos a una OT
"""
import os
import time
import tempfile
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options

# Configuracion
BASE_URL = "http://localhost:3000"
API_URL = "http://localhost:8001/api/v1"

# Credenciales de prueba (el login usa RUT, no email)
TEST_RUT = "11334692-2"  # Vendedor
TEST_PASSWORD = "123123"


def create_test_file():
    """Crea un archivo temporal para probar la carga."""
    fd, path = tempfile.mkstemp(suffix=".txt", prefix="test_upload_")
    with os.fdopen(fd, 'w') as f:
        f.write(f"Archivo de prueba creado en: {time.strftime('%Y-%m-%d %H:%M:%S')}\n")
        f.write("Este es un archivo de prueba para validar la carga de archivos.\n")
    return path


def setup_driver():
    """Configura el driver de Chrome."""
    options = Options()
    # options.add_argument("--headless")  # Descomentar para modo sin cabeza
    options.add_argument("--window-size=1920,1080")
    options.add_argument("--disable-gpu")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")
    options.set_capability('goog:loggingPrefs', {'browser': 'ALL'})

    driver = webdriver.Chrome(options=options)
    driver.implicitly_wait(10)
    return driver


def login(driver, rut, password):
    """Realiza el login en la aplicacion."""
    print(f"[INFO] Navegando a {BASE_URL}/login")
    driver.get(f"{BASE_URL}/login")
    time.sleep(2)

    # Esperar que cargue el formulario
    wait = WebDriverWait(driver, 15)

    # El formulario tiene campos "Rut" y "Contrasena"
    # Buscar todos los inputs
    inputs = driver.find_elements(By.TAG_NAME, "input")
    print(f"[DEBUG] Inputs encontrados: {len(inputs)}")
    for inp in inputs:
        print(f"    - type: {inp.get_attribute('type')}, name: {inp.get_attribute('name')}, placeholder: {inp.get_attribute('placeholder')}")

    # Buscar campo de RUT (puede ser text o tel)
    rut_field = None
    password_field = None

    for inp in inputs:
        inp_type = inp.get_attribute('type')
        inp_name = (inp.get_attribute('name') or '').lower()
        inp_placeholder = (inp.get_attribute('placeholder') or '').lower()

        if inp_type == 'password':
            password_field = inp
        elif 'rut' in inp_name or 'rut' in inp_placeholder or inp_type in ['text', 'tel']:
            if not rut_field:
                rut_field = inp

    if not rut_field:
        raise Exception("No se encontro campo de RUT")
    if not password_field:
        raise Exception("No se encontro campo de password")

    rut_field.clear()
    rut_field.send_keys(rut)
    print(f"[INFO] RUT ingresado: {rut}")

    password_field.clear()
    password_field.send_keys(password)
    print("[INFO] Password ingresado")

    # Hacer clic en el boton de login (buscar boton "Ingresar")
    buttons = driver.find_elements(By.TAG_NAME, "button")
    login_button = None
    for btn in buttons:
        if "ingresar" in btn.text.lower() or btn.get_attribute('type') == 'submit':
            login_button = btn
            break

    if login_button:
        login_button.click()
        print(f"[INFO] Boton de login clickeado: {login_button.text}")
    else:
        raise Exception("No se encontro boton de login")

    # Esperar a que aparezca un elemento que indica login exitoso
    # En vez de depender de URL, esperamos elementos de la pagina principal
    time.sleep(2)

    # Intentar esperar hasta 15 segundos por un elemento que indica login exitoso
    for _ in range(15):
        try:
            # Buscar elementos que solo existen despues del login
            # Por ejemplo: menu "Ordenes de Trabajo", boton "Salir", etc.
            salir_btn = driver.find_elements(By.XPATH, "//*[contains(text(), 'Salir')]")
            ordenes_header = driver.find_elements(By.XPATH, "//*[contains(text(), 'Ordenes de Trabajo')]")

            if salir_btn or ordenes_header:
                print("[OK] Login exitoso - elementos de sesion detectados")
                return True

            # Verificar si hay mensaje de error
            error_msgs = driver.find_elements(By.XPATH, "//*[contains(text(), 'incorrectos') or contains(text(), 'error')]")
            if error_msgs:
                print(f"[ERROR] Login fallido - mensaje de error detectado")
                return False

        except:
            pass

        time.sleep(1)

    # Ultimo recurso: verificar URL
    current_url = driver.current_url
    print(f"[DEBUG] URL final: {current_url}")

    if "/login" not in current_url:
        print("[OK] Login exitoso (por URL)")
        return True
    else:
        print(f"[ERROR] Login fallido - timeout esperando sesion")
        return False


def take_screenshot(driver, name):
    """Toma una captura de pantalla."""
    screenshots_dir = os.path.join(os.path.dirname(__file__), "screenshots")
    os.makedirs(screenshots_dir, exist_ok=True)

    filename = os.path.join(screenshots_dir, f"{name}_{time.strftime('%Y%m%d_%H%M%S')}.png")
    driver.save_screenshot(filename)
    print(f"[INFO] Captura guardada: {filename}")
    return filename


def check_console_logs(driver):
    """Obtiene los logs de la consola del navegador."""
    print("[INFO] Revisando logs de consola...")
    try:
        logs = driver.get_log('browser')
        if logs:
            print("[INFO] Logs de consola:")
            for log in logs[-20:]:  # Ultimos 20 logs
                level = log.get('level', 'INFO')
                message = log.get('message', '')
                # Filtrar logs relevantes
                if any(x in message.lower() for x in ['files', 'upload', 'error', 'transition', 'management']):
                    print(f"    [{level}] {message[:200]}")
        return logs
    except Exception as e:
        print(f"[WARN] No se pudieron obtener logs de consola: {e}")
        return []


def test_file_upload(driver, ot_id=None):
    """Prueba la carga de archivos en una OT."""
    print("\n" + "=" * 60)
    print("TEST DE CARGA DE ARCHIVOS")
    print("=" * 60)

    wait = WebDriverWait(driver, 10)

    # 1. Navegar a lista de OTs
    print("\n--- PASO 1: LISTA DE OTS ---")
    driver.get(f"{BASE_URL}/work-orders")
    time.sleep(3)
    take_screenshot(driver, "1_work_orders_list")

    # 2. Buscar primera OT para gestionar (es una SPA, no usa URLs)
    print("\n--- PASO 2: ENCONTRAR OT ---")
    try:
        # Esperar que cargue la tabla de OTs
        time.sleep(3)

        # Buscar boton "Ver OT" (emoji lupa o title="Ver OT")
        ver_buttons = driver.find_elements(By.XPATH, "//button[@title='Ver OT'] | //button[contains(text(), '🔍')]")
        print(f"[DEBUG] Botones 'Ver OT' encontrados: {len(ver_buttons)}")

        if ver_buttons:
            # Click en el primer boton de ver OT
            ver_buttons[0].click()
            print("[OK] Click en boton Ver OT")
            time.sleep(3)
        else:
            # Buscar cualquier boton en la tabla que pueda llevar a gestionar
            print("[WARN] No se encontraron botones Ver OT, buscando alternativas...")
            table_buttons = driver.find_elements(By.CSS_SELECTOR, "table button, td button")
            print(f"[DEBUG] Botones en tabla: {len(table_buttons)}")
            if table_buttons:
                for btn in table_buttons[:5]:
                    print(f"    - '{btn.text}' title='{btn.get_attribute('title')}'")
                # Hacer clic en el primero que tenga titulo "Ver"
                for btn in table_buttons:
                    if "ver" in (btn.get_attribute('title') or '').lower():
                        btn.click()
                        print(f"[OK] Click en boton: {btn.get_attribute('title')}")
                        time.sleep(3)
                        break

        take_screenshot(driver, "2_manage_page")
    except Exception as e:
        print(f"[ERROR] Error navegando a OT: {e}")
        import traceback
        traceback.print_exc()
        return False

    # 3. Analizar formulario
    print("\n--- PASO 3: ANALIZAR FORMULARIO ---")

    # Buscar todos los selects
    selects = driver.find_elements(By.TAG_NAME, "select")
    print(f"[DEBUG] Selects encontrados: {len(selects)}")
    for i, s in enumerate(selects):
        try:
            name = s.get_attribute("name") or s.get_attribute("id") or f"select_{i}"
            print(f"    Select '{name}':")
            options = s.find_elements(By.TAG_NAME, "option")
            for opt in options[:8]:
                print(f"        - value='{opt.get_attribute('value')}' text='{opt.text}'")
        except:
            pass

    # Buscar inputs de archivo
    file_inputs = driver.find_elements(By.CSS_SELECTOR, "input[type='file']")
    print(f"[DEBUG] File inputs encontrados: {len(file_inputs)}")
    for i, fi in enumerate(file_inputs):
        print(f"    FileInput {i}: name='{fi.get_attribute('name')}' multiple={fi.get_attribute('multiple')}")

    # 4. Seleccionar tipo "Archivo"
    print("\n--- PASO 4: SELECCIONAR TIPO ARCHIVO ---")
    from selenium.webdriver.support.select import Select

    archivo_selected = False
    for s in selects:
        try:
            select = Select(s)
            for opt in select.options:
                if "archivo" in opt.text.lower():
                    select.select_by_visible_text(opt.text)
                    print(f"[OK] Seleccionado tipo: '{opt.text}'")
                    archivo_selected = True
                    time.sleep(1)
                    break
            if archivo_selected:
                break
        except Exception as e:
            continue

    if not archivo_selected:
        print("[WARN] No se pudo seleccionar 'Archivo' automaticamente")
        # Intentar otro metodo: buscar por valor
        for s in selects:
            try:
                select = Select(s)
                select.select_by_value("3")  # Archivo suele ser value=3
                print("[OK] Seleccionado tipo por valor '3'")
                archivo_selected = True
                time.sleep(1)
                break
            except:
                continue

    take_screenshot(driver, "3_tipo_archivo")

    # Re-buscar file inputs (pueden aparecer despues de seleccionar tipo)
    time.sleep(2)
    file_inputs = driver.find_elements(By.CSS_SELECTOR, "input[type='file']")
    print(f"[DEBUG] File inputs despues de seleccionar tipo: {len(file_inputs)}")

    # 5. Subir archivo
    print("\n--- PASO 5: SUBIR ARCHIVO ---")
    test_file = create_test_file()
    print(f"[INFO] Archivo de prueba creado: {test_file}")

    try:
        if file_inputs:
            file_input = file_inputs[0]
            # El input file puede estar oculto, pero podemos enviarle keys
            file_input.send_keys(test_file)
            print("[OK] Archivo enviado al input")
            time.sleep(2)
            take_screenshot(driver, "4_file_attached")
        else:
            print("[ERROR] No se encontro input de archivo")
            take_screenshot(driver, "4_no_file_input")
            os.remove(test_file)
            return False
    except Exception as e:
        print(f"[ERROR] Error subiendo archivo: {e}")
        os.remove(test_file)
        return False

    # 6. Agregar observacion
    print("\n--- PASO 6: OBSERVACION ---")
    try:
        textarea = driver.find_element(By.TAG_NAME, "textarea")
        textarea.clear()
        textarea.send_keys("Test de carga de archivo con Selenium - " + time.strftime('%Y-%m-%d %H:%M:%S'))
        print("[OK] Observacion ingresada")
    except:
        print("[WARN] No se encontro textarea para observacion")

    take_screenshot(driver, "5_before_submit")

    # 7. Enviar formulario
    print("\n--- PASO 7: ENVIAR ---")
    buttons = driver.find_elements(By.TAG_NAME, "button")
    # Evitar problemas de codificacion con caracteres Unicode
    button_texts = []
    for b in buttons:
        try:
            button_texts.append(b.text.encode('ascii', 'replace').decode('ascii'))
        except:
            button_texts.append('[?]')
    print(f"[DEBUG] Botones: {button_texts}")

    ejecutar_btn = None
    for btn in buttons:
        btn_text = btn.text.lower()
        if "transicion" in btn_text or "ejecutar" in btn_text or "guardar" in btn_text or "enviar" in btn_text:
            ejecutar_btn = btn
            break

    if ejecutar_btn:
        print(f"[INFO] Clickeando boton: '{ejecutar_btn.text}'")
        ejecutar_btn.click()
        print("[INFO] Esperando respuesta...")
        time.sleep(5)
        take_screenshot(driver, "6_after_submit")
    else:
        print("[ERROR] No se encontro boton de envio")
        os.remove(test_file)
        return False

    # 8. Verificar resultado
    print("\n--- PASO 8: VERIFICAR ---")

    # Revisar logs de consola
    check_console_logs(driver)

    # Buscar mensaje de exito/error
    try:
        alerts = driver.find_elements(By.CSS_SELECTOR, "[class*='success'], [class*='error'], [class*='alert'], [role='alert']")
        for alert in alerts:
            if alert.text.strip():
                print(f"[INFO] Mensaje encontrado: {alert.text}")
    except:
        pass

    # Buscar archivos en historial
    print("\n[INFO] Buscando archivos en historial...")
    try:
        # Buscar seccion de archivos
        archivos_elements = driver.find_elements(By.XPATH, "//*[contains(text(), 'Archivos') or contains(text(), 'archivos')]")
        print(f"[DEBUG] Elementos con 'Archivos': {len(archivos_elements)}")

        # Buscar links a /files/
        file_links = driver.find_elements(By.CSS_SELECTOR, "a[href*='/files/']")
        if file_links:
            print(f"[OK] Se encontraron {len(file_links)} links de archivos:")
            for link in file_links:
                print(f"    - {link.text}: {link.get_attribute('href')}")
        else:
            print("[WARN] No se encontraron links de archivos en la pagina")
    except Exception as e:
        print(f"[WARN] Error buscando archivos: {e}")

    take_screenshot(driver, "7_final_result")

    # Limpiar
    if os.path.exists(test_file):
        os.remove(test_file)
        print(f"[INFO] Archivo temporal eliminado")

    print("\n[INFO] Test completado")
    return True


def main():
    """Funcion principal del test."""
    print("=" * 60)
    print("TEST DE CARGA DE ARCHIVOS - INVEB ENVASES OT")
    print("=" * 60)

    driver = None

    try:
        # Configurar driver
        driver = setup_driver()

        # 1. Login
        print("\n--- LOGIN ---")
        if not login(driver, TEST_RUT, TEST_PASSWORD):
            take_screenshot(driver, "login_failed")
            raise Exception("Login fallido")

        take_screenshot(driver, "login_success")

        # 2. Test de carga de archivo
        test_file_upload(driver)

        print("\n" + "=" * 60)
        print("TEST FINALIZADO")
        print("=" * 60)

    except Exception as e:
        print(f"\n[ERROR FATAL] {e}")
        if driver:
            take_screenshot(driver, "error_fatal")
        import traceback
        traceback.print_exc()

    finally:
        if driver:
            # No esperar input en modo no interactivo
            try:
                import sys
                if sys.stdin.isatty():
                    input("\nPresione Enter para cerrar el navegador...")
            except:
                pass
            driver.quit()


if __name__ == "__main__":
    main()
