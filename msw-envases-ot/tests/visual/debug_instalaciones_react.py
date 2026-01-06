"""
================================================================================
DEBUG - Carga de Instalaciones en React
================================================================================
Captura logs de consola para diagnosticar por que no cargan las instalaciones
al seleccionar un cliente en el formulario Crear OT de React.

Uso: python debug_instalaciones_react.py
================================================================================
"""
import os
import sys
import time
import json
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.desired_capabilities import DesiredCapabilities
from webdriver_manager.chrome import ChromeDriverManager

# Configuracion de encoding para Windows
if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8', errors='replace')

# =============================================================================
# CONFIGURACION
# =============================================================================
REACT_URL = "http://localhost:3002"
RUT = "11334692-2"
PASSWORD = "123123"

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
SCREENSHOTS_DIR = os.path.join(BASE_DIR, "screenshots")
os.makedirs(SCREENSHOTS_DIR, exist_ok=True)


def crear_driver_con_logs():
    """Crea Chrome WebDriver con captura de logs de consola"""
    opts = Options()
    opts.add_argument("--start-maximized")
    opts.add_argument("--window-size=1920,1080")

    # Habilitar logging de rendimiento para capturar logs de consola
    opts.set_capability('goog:loggingPrefs', {'browser': 'ALL', 'performance': 'ALL'})

    return webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=opts)


def get_browser_logs(driver):
    """Obtiene logs del navegador (consola)"""
    try:
        logs = driver.get_log('browser')
        return logs
    except Exception as e:
        print(f"Error obteniendo logs: {e}")
        return []


def print_console_logs(logs, filtro=None):
    """Imprime logs de consola filtrados"""
    print("\n" + "="*80)
    print("LOGS DE CONSOLA DEL NAVEGADOR")
    print("="*80)

    for log in logs:
        mensaje = log.get('message', '')
        nivel = log.get('level', 'INFO')

        # Filtrar por texto si se especifica
        if filtro and filtro.lower() not in mensaje.lower():
            continue

        # Colorear segun nivel
        if nivel == 'SEVERE':
            print(f"[ERROR] {mensaje}")
        elif nivel == 'WARNING':
            print(f"[WARN]  {mensaje}")
        else:
            print(f"[INFO]  {mensaje}")

    print("="*80 + "\n")


def login_react(driver):
    """Login en sistema React"""
    print(f"\n[1] Navegando a React: {REACT_URL}")
    driver.get(REACT_URL)
    time.sleep(3)

    try:
        # React usa campos con name="rut" y name="password"
        rut_field = WebDriverWait(driver, 15).until(
            EC.presence_of_element_located((By.NAME, "rut"))
        )
        print("    Campo RUT encontrado")
        rut_field.clear()
        rut_field.send_keys(RUT)

        pass_field = driver.find_element(By.NAME, "password")
        pass_field.clear()
        pass_field.send_keys(PASSWORD)

        # Click en boton login
        login_btn = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        login_btn.click()
        print("    Credenciales enviadas, esperando...")

        # Esperar a que aparezca el header (indica login exitoso)
        time.sleep(5)

        # Verificar que tengamos acceso al dashboard
        WebDriverWait(driver, 15).until(
            EC.presence_of_element_located((By.XPATH, "//*[contains(text(), 'INVEB')]"))
        )
        print("    Login React OK")
        return True

    except Exception as e:
        print(f"    ERROR Login React: {e}")
        driver.save_screenshot(os.path.join(SCREENSHOTS_DIR, "error_login_react.png"))
        return False


def navegar_crear_ot(driver):
    """Navega a la pantalla de Crear OT"""
    print("\n[2] Navegando a Crear OT...")

    try:
        # Primero seleccionar tipo de solicitud (paso inicial)
        # Buscar el boton "Crear OT" o navegar directamente
        time.sleep(2)

        # Buscar el tab/boton de Ordenes de Trabajo y hacer click
        try:
            ot_tab = WebDriverWait(driver, 10).until(
                EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Ordenes de Trabajo')]"))
            )
            ot_tab.click()
            time.sleep(2)
        except:
            pass  # Ya estamos en dashboard

        # Buscar boton "Crear OT"
        crear_btn = WebDriverWait(driver, 10).until(
            EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Crear OT')]"))
        )
        crear_btn.click()
        print("    Click en 'Crear OT'")
        time.sleep(3)

        # Verificar que estemos en el formulario
        WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.XPATH, "//*[contains(text(), 'Tipo de Solicitud')]"))
        )
        print("    Formulario Crear OT cargado")
        return True

    except Exception as e:
        print(f"    ERROR navegando a Crear OT: {e}")
        driver.save_screenshot(os.path.join(SCREENSHOTS_DIR, "error_navegar_crear_ot.png"))
        return False


def seleccionar_tipo_solicitud(driver):
    """Selecciona un tipo de solicitud (requerido antes de ver datos comerciales)"""
    print("\n[3] Seleccionando Tipo de Solicitud...")

    try:
        # Buscar el select de tipo de solicitud
        tipo_select = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.XPATH, "//label[contains(text(), 'Tipo de Solicitud')]/following-sibling::select | //label[contains(text(), 'Tipo')]/following::select[1]"))
        )

        # Seleccionar "Desarrollo Completo" (value=1)
        select = Select(tipo_select)
        select.select_by_value("1")
        print("    Tipo de Solicitud seleccionado: Desarrollo Completo")
        time.sleep(3)

        return True

    except Exception as e:
        print(f"    ERROR seleccionando tipo: {e}")
        # Intentar buscar de otra forma
        try:
            selects = driver.find_elements(By.TAG_NAME, "select")
            print(f"    Encontrados {len(selects)} selects en la pagina")
            for i, sel in enumerate(selects):
                try:
                    opts = sel.find_elements(By.TAG_NAME, "option")
                    opt_texts = [o.text for o in opts[:5]]
                    print(f"      Select {i}: {opt_texts}")
                except:
                    pass
        except:
            pass
        driver.save_screenshot(os.path.join(SCREENSHOTS_DIR, "error_tipo_solicitud.png"))
        return False


def seleccionar_cliente(driver):
    """Selecciona un cliente y captura logs de consola"""
    print("\n[4] Seleccionando Cliente...")

    # Capturar logs antes
    logs_antes = get_browser_logs(driver)

    try:
        # Buscar el select de cliente
        cliente_select = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.XPATH, "//label[contains(text(), 'Cliente')]/following-sibling::select"))
        )

        # Listar opciones disponibles
        options = cliente_select.find_elements(By.TAG_NAME, "option")
        print(f"    Clientes disponibles: {len(options) - 1}")  # -1 por el placeholder

        # Buscar CMPC o usar el primero disponible
        cliente_id = None
        cliente_nombre = None
        for opt in options[1:5]:  # Mostrar primeros 5
            print(f"      - {opt.get_attribute('value')}: {opt.text}")
            if "CMPC" in opt.text.upper() and not cliente_id:
                cliente_id = opt.get_attribute('value')
                cliente_nombre = opt.text

        # Si no hay CMPC, usar el primer cliente
        if not cliente_id and len(options) > 1:
            cliente_id = options[1].get_attribute('value')
            cliente_nombre = options[1].text

        if cliente_id:
            select = Select(cliente_select)
            select.select_by_value(cliente_id)
            print(f"\n    Cliente seleccionado: {cliente_nombre} (ID: {cliente_id})")

            # Esperar a que se ejecute el useEffect y la llamada API
            print("    Esperando carga de instalaciones (5 segundos)...")
            time.sleep(5)

            # Capturar logs despues
            logs_despues = get_browser_logs(driver)

            # Mostrar logs relevantes (los nuevos)
            print("\n" + "="*80)
            print("LOGS DE CONSOLA DESPUES DE SELECCIONAR CLIENTE")
            print("="*80)

            # Filtrar logs que contengan DEBUG, API, Error, cascades
            keywords = ['DEBUG', 'API', 'error', 'Error', 'cascades', 'instalaciones', '401', '403', '404', '500']
            for log in logs_despues:
                msg = log.get('message', '')
                for kw in keywords:
                    if kw.lower() in msg.lower():
                        nivel = log.get('level', 'INFO')
                        if nivel == 'SEVERE':
                            print(f"[ERROR] {msg}")
                        else:
                            print(f"[LOG]   {msg}")
                        break

            print("="*80)

            # Verificar si el dropdown de instalaciones tiene opciones
            print("\n[5] Verificando dropdown de Instalaciones...")
            try:
                instalacion_select = driver.find_element(
                    By.XPATH, "//label[contains(text(), 'Instalacion')]/following-sibling::select"
                )
                inst_options = instalacion_select.find_elements(By.TAG_NAME, "option")
                print(f"    Opciones en Instalaciones: {len(inst_options)}")
                for opt in inst_options[:5]:
                    print(f"      - {opt.get_attribute('value')}: {opt.text}")

                if len(inst_options) <= 1:
                    print("\n    [!] PROBLEMA: No se cargaron instalaciones")
                else:
                    print("\n    [OK] Instalaciones cargadas correctamente")

            except Exception as e:
                print(f"    Error buscando dropdown instalaciones: {e}")

            # Guardar screenshot
            driver.save_screenshot(os.path.join(SCREENSHOTS_DIR, "debug_cliente_seleccionado.png"))
            print(f"\n    Screenshot guardado: debug_cliente_seleccionado.png")

            return True
        else:
            print("    No hay clientes disponibles")
            return False

    except Exception as e:
        print(f"    ERROR seleccionando cliente: {e}")
        driver.save_screenshot(os.path.join(SCREENSHOTS_DIR, "error_seleccionar_cliente.png"))
        return False


def verificar_network_requests(driver):
    """Verifica las peticiones de red usando Performance logs"""
    print("\n[6] Verificando peticiones de red...")

    try:
        perf_logs = driver.get_log('performance')

        print("\n    Peticiones a /cascades/:")
        cascade_requests = []

        for log in perf_logs:
            try:
                message = json.loads(log['message'])
                method = message.get('message', {}).get('method', '')
                params = message.get('message', {}).get('params', {})

                if method == 'Network.requestWillBeSent':
                    url = params.get('request', {}).get('url', '')
                    if 'cascades' in url or 'instalaciones' in url:
                        http_method = params.get('request', {}).get('method', 'GET')
                        print(f"      [{http_method}] {url}")
                        cascade_requests.append(url)

                elif method == 'Network.responseReceived':
                    url = params.get('response', {}).get('url', '')
                    if 'cascades' in url or 'instalaciones' in url:
                        status = params.get('response', {}).get('status', '???')
                        print(f"      Response: {status} - {url}")

            except:
                pass

        if not cascade_requests:
            print("      [!] No se encontraron peticiones a /cascades/")
            print("      Esto indica que el useEffect no se esta ejecutando")

    except Exception as e:
        print(f"    Error obteniendo performance logs: {e}")


def main():
    print("\n" + "="*80)
    print("DEBUG - CARGA DE INSTALACIONES EN REACT")
    print("="*80)

    driver = crear_driver_con_logs()

    try:
        # 1. Login
        if not login_react(driver):
            print("\nFallo en login, abortando...")
            return

        # 2. Navegar a Crear OT
        if not navegar_crear_ot(driver):
            print("\nFallo navegando a Crear OT, abortando...")
            return

        # 3. Seleccionar tipo de solicitud
        if not seleccionar_tipo_solicitud(driver):
            print("\nFallo seleccionando tipo, intentando continuar...")

        # 4. Seleccionar cliente y capturar logs
        seleccionar_cliente(driver)

        # 5. Verificar peticiones de red
        verificar_network_requests(driver)

        # Esperar para revision manual
        print("\n" + "="*80)
        print("DEBUG COMPLETADO")
        print("Presiona Enter para cerrar el navegador...")
        print("="*80)
        input()

    except Exception as e:
        print(f"\nError general: {e}")
        driver.save_screenshot(os.path.join(SCREENSHOTS_DIR, "error_general.png"))

    finally:
        driver.quit()


if __name__ == "__main__":
    main()
