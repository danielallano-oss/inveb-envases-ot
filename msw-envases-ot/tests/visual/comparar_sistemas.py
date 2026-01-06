"""
================================================================================
COMPARADOR VISUAL - Laravel vs React+FastAPI
================================================================================
Toma capturas de pantalla de ambos sistemas para comparacion visual.

Uso: python comparar_sistemas.py

Configuracion: Editar config.json para modificar credenciales y URLs
================================================================================
"""
import os
import sys
import json
import time
from datetime import datetime
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from webdriver_manager.chrome import ChromeDriverManager

# Configuracion de encoding para Windows
if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8', errors='replace')

# =============================================================================
# CONFIGURACION
# =============================================================================
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
CONFIG_FILE = os.path.join(BASE_DIR, "config.json")
SCREENSHOTS_DIR = os.path.join(BASE_DIR, "screenshots")
os.makedirs(SCREENSHOTS_DIR, exist_ok=True)

def cargar_config():
    """Carga la configuracion desde config.json"""
    if not os.path.exists(CONFIG_FILE):
        print(f"ERROR: No se encontro el archivo de configuracion: {CONFIG_FILE}")
        print("Creando config.json de ejemplo...")
        config_ejemplo = {
            "laravel": {
                "url": "http://localhost:8080",
                "login_url": "http://localhost:8080/login"
            },
            "react": {
                "url": "http://localhost:3002",
                "login_url": "http://localhost:3002"
            },
            "usuarios": [
                {
                    "nombre": "Vendedor",
                    "rut": "11111111-1",
                    "password": "123456"
                },
                {
                    "nombre": "Admin",
                    "rut": "22222222-2",
                    "password": "123456"
                }
            ],
            "paginas": [
                {
                    "nombre": "Dashboard OTs",
                    "laravel_path": "/work-orders",
                    "react_path": "/work-orders",
                    "espera_selector": "table"
                }
            ]
        }
        with open(CONFIG_FILE, 'w', encoding='utf-8') as f:
            json.dump(config_ejemplo, f, indent=2, ensure_ascii=False)
        print(f"Archivo creado: {CONFIG_FILE}")
        print("Por favor, edita el archivo con las credenciales correctas y ejecuta de nuevo.")
        sys.exit(1)

    with open(CONFIG_FILE, 'r', encoding='utf-8') as f:
        return json.load(f)


def crear_driver():
    """Crea instancia de Chrome WebDriver"""
    opts = Options()
    opts.add_argument("--start-maximized")
    opts.add_argument("--window-size=1920,1080")
    # opts.add_argument("--headless")  # Descomentar para modo sin ventana
    return webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=opts)


def login_laravel(driver, url, rut, password):
    """Login en sistema Laravel"""
    print(f"  Navegando a Laravel: {url}")
    driver.get(url)
    time.sleep(5)

    try:
        # Laravel usa id="rut" y id="password" en el formulario #filterForm
        # Usar element_to_be_clickable para asegurar que el campo este listo
        rut_field = WebDriverWait(driver, 15).until(
            EC.element_to_be_clickable((By.ID, "rut"))
        )
        print(f"    Campo RUT encontrado y clickeable")
        rut_field.click()
        time.sleep(0.3)
        rut_field.clear()
        time.sleep(0.3)
        rut_field.send_keys(rut)
        print(f"    RUT ingresado: {rut}")
        time.sleep(0.5)

        # Buscar campo password por ID
        pass_field = WebDriverWait(driver, 10).until(
            EC.element_to_be_clickable((By.ID, "password"))
        )
        print(f"    Campo Password encontrado y clickeable")
        pass_field.click()
        time.sleep(0.3)
        pass_field.clear()
        time.sleep(0.3)
        pass_field.send_keys(password)
        print(f"    Password ingresado")
        time.sleep(0.5)

        # Click en boton login (Laravel usa button.sbtn.submit)
        login_btn = WebDriverWait(driver, 10).until(
            EC.element_to_be_clickable((By.CSS_SELECTOR, "button[type='submit']"))
        )
        print(f"    Boton submit encontrado, haciendo click...")
        login_btn.click()

        # Esperar redireccion
        time.sleep(10)

        # Verificar que no estemos en login
        current_url = driver.current_url
        print(f"    URL actual: {current_url}")
        if "login" not in current_url.lower():
            print("  Login Laravel OK")
            return True
        else:
            print("  ADVERTENCIA: Posiblemente sigue en login (credenciales incorrectas?)")
            return True  # Continuar de todas formas
    except Exception as e:
        print(f"  ERROR Login Laravel: {e}")
        return False


def login_react(driver, url, rut, password):
    """Login en sistema React"""
    print(f"  Navegando a React: {url}")
    driver.get(url)
    time.sleep(5)

    try:
        # React usa id="rut" y id="password"
        # Usar element_to_be_clickable para asegurar que el campo este listo
        rut_field = WebDriverWait(driver, 15).until(
            EC.element_to_be_clickable((By.ID, "rut"))
        )
        print(f"    Campo RUT encontrado y clickeable")
        rut_field.click()
        time.sleep(0.3)
        rut_field.clear()
        time.sleep(0.3)
        rut_field.send_keys(rut)
        print(f"    RUT ingresado: {rut}")
        time.sleep(0.5)

        pass_field = WebDriverWait(driver, 10).until(
            EC.element_to_be_clickable((By.ID, "password"))
        )
        print(f"    Campo Password encontrado y clickeable")
        pass_field.click()
        time.sleep(0.3)
        pass_field.clear()
        time.sleep(0.3)
        pass_field.send_keys(password)
        print(f"    Password ingresado")
        time.sleep(0.5)

        # Click en boton Ingresar
        login_btn = WebDriverWait(driver, 10).until(
            EC.element_to_be_clickable((By.CSS_SELECTOR, "button[type='submit']"))
        )
        print(f"    Boton submit encontrado, haciendo click...")
        login_btn.click()

        # Esperar a que desaparezca el formulario de login o cambie la URL
        time.sleep(10)

        # Verificar que no estemos en la pagina de login
        current_url = driver.current_url
        print(f"    URL actual: {current_url}")

        if "login" not in current_url.lower() and "work-orders" in current_url.lower():
            print(f"  Login React OK - Redirigido a: {current_url}")
            return True
        else:
            # Intentar detectar si hay error de login
            try:
                error = driver.find_element(By.CSS_SELECTOR, "[class*='error'], [class*='Error']")
                print(f"  Mensaje de error detectado: {error.text}")
            except:
                print("  Login React completado (verificar visualmente)")
            return True
    except Exception as e:
        print(f"  ERROR Login React: {e}")
        return False


def navegar_react_menu(driver, menu_texto):
    """Navega en React SPA haciendo click en el menu con el texto especificado"""
    try:
        # Buscar el tab del menu con el texto exacto
        menu_items = driver.find_elements(By.CSS_SELECTOR, "[class*='NavTab'], nav button, nav a")
        for item in menu_items:
            if menu_texto.lower() in item.text.lower():
                print(f"    Click en menu: '{item.text}'")
                item.click()
                time.sleep(3)
                return True

        # Fallback: buscar por texto exacto
        menu = WebDriverWait(driver, 10).until(
            EC.element_to_be_clickable((By.XPATH, f"//button[contains(text(), '{menu_texto}')] | //a[contains(text(), '{menu_texto}')]"))
        )
        print(f"    Click en menu (xpath): '{menu.text}'")
        menu.click()
        time.sleep(3)
        return True
    except Exception as e:
        print(f"    ERROR navegando a menu '{menu_texto}': {e}")
        return False


def ejecutar_acciones_previas(driver, acciones):
    """Ejecuta acciones previas antes de capturar (seleccionar opciones, clicks, etc.)

    Args:
        driver: WebDriver instance
        acciones: Lista de acciones a ejecutar. Cada accion es un dict con:
            - tipo: 'select', 'click', 'input'
            - selector: CSS selector del elemento
            - valor: valor a seleccionar/ingresar (para select e input)
    """
    from selenium.webdriver.support.ui import Select

    for accion in acciones:
        try:
            tipo = accion.get('tipo')
            selector = accion.get('selector')
            valor = accion.get('valor')

            print(f"    Ejecutando accion: {tipo} en '{selector}'")

            if tipo == 'select':
                # Esperar que el select este presente
                element = WebDriverWait(driver, 10).until(
                    EC.presence_of_element_located((By.CSS_SELECTOR, selector))
                )
                select = Select(element)
                # Intentar seleccionar por texto visible
                try:
                    select.select_by_visible_text(valor)
                except:
                    # Fallback: seleccionar por valor
                    select.select_by_value(str(valor))
                print(f"      Seleccionado: '{valor}'")
                time.sleep(2)

            elif tipo == 'click':
                element = WebDriverWait(driver, 10).until(
                    EC.element_to_be_clickable((By.CSS_SELECTOR, selector))
                )
                element.click()
                print(f"      Click realizado")
                time.sleep(2)

            elif tipo == 'input':
                element = WebDriverWait(driver, 10).until(
                    EC.element_to_be_clickable((By.CSS_SELECTOR, selector))
                )
                element.clear()
                element.send_keys(valor)
                print(f"      Ingresado: '{valor}'")
                time.sleep(1)

            elif tipo == 'wait':
                # Esperar un tiempo especifico
                wait_time = int(valor) if valor else 3
                print(f"      Esperando {wait_time} segundos...")
                time.sleep(wait_time)

        except Exception as e:
            print(f"    ERROR en accion {tipo}: {e}")


def capturar_pagina(driver, url, nombre_archivo, espera_selector=None, scroll_to_table=False, react_menu=None, acciones_previas=None):
    """Navega a URL y toma captura de pantalla

    Args:
        driver: WebDriver instance
        url: URL to navigate (ignored if react_menu is provided)
        nombre_archivo: Screenshot filename
        espera_selector: CSS selector to wait for
        scroll_to_table: Whether to scroll to table element
        react_menu: Menu text to click for React SPA navigation (overrides URL navigation)
        acciones_previas: Lista de acciones a ejecutar antes de capturar
    """
    if react_menu:
        print(f"  Navegando via menu React: '{react_menu}'")
        if not navegar_react_menu(driver, react_menu):
            print(f"  ADVERTENCIA: No se pudo navegar al menu, intentando URL...")
            driver.get(url)
    else:
        print(f"  Capturando: {url}")
        driver.get(url)

    # Esperar carga inicial
    time.sleep(5)

    if espera_selector:
        try:
            element = WebDriverWait(driver, 20).until(
                EC.presence_of_element_located((By.CSS_SELECTOR, espera_selector))
            )
            print(f"    Selector '{espera_selector}' encontrado")

            # Scroll hacia la tabla si se requiere
            if scroll_to_table:
                driver.execute_script("arguments[0].scrollIntoView({behavior: 'smooth', block: 'start'});", element)
                time.sleep(2)
                print(f"    Scroll hacia tabla realizado")
        except:
            print(f"    ADVERTENCIA: Selector '{espera_selector}' no encontrado, esperando mas...")
            time.sleep(5)

    # Ejecutar acciones previas si se especificaron
    if acciones_previas:
        print(f"  Ejecutando {len(acciones_previas)} acciones previas...")
        ejecutar_acciones_previas(driver, acciones_previas)
        time.sleep(3)  # Esperar a que se actualice la pagina

    # Esperar renderizado completo y datos
    time.sleep(5)

    filepath = os.path.join(SCREENSHOTS_DIR, nombre_archivo)
    driver.save_screenshot(filepath)
    print(f"  Guardado: {nombre_archivo}")
    return filepath


def generar_html_comparacion(capturas, timestamp):
    """Genera HTML con comparacion lado a lado"""
    html = f"""<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Comparacion Visual - {timestamp}</title>
    <style>
        body {{ font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }}
        h1 {{ color: #1a1a2e; text-align: center; }}
        h2 {{ color: #00a19b; border-bottom: 2px solid #00a19b; padding-bottom: 10px; }}
        .comparacion {{
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 40px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }}
        .sistema {{ text-align: center; }}
        .sistema h3 {{
            margin: 0 0 10px 0;
            padding: 10px;
            border-radius: 4px;
        }}
        .laravel h3 {{ background: #ff6b6b; color: white; }}
        .react h3 {{ background: #00a19b; color: white; }}
        .sistema img {{
            max-width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
        }}
        .sistema img:hover {{ border-color: #00a19b; }}
        .timestamp {{ text-align: center; color: #666; margin-bottom: 20px; }}
        .diferencias {{
            background: #fff3cd;
            padding: 15px;
            border-radius: 4px;
            margin-top: 10px;
        }}
    </style>
</head>
<body>
    <h1>Comparacion Visual Laravel vs React+FastAPI</h1>
    <p class="timestamp">Generado: {timestamp}</p>
"""

    for captura in capturas:
        html += f"""
    <h2>{captura['usuario']} - {captura['pagina']}</h2>
    <div class="comparacion">
        <div class="sistema laravel">
            <h3>Laravel (localhost:8080)</h3>
            <a href="{os.path.basename(captura['laravel'])}" target="_blank">
                <img src="{os.path.basename(captura['laravel'])}" alt="Laravel">
            </a>
        </div>
        <div class="sistema react">
            <h3>React+FastAPI (localhost:3002)</h3>
            <a href="{os.path.basename(captura['react'])}" target="_blank">
                <img src="{os.path.basename(captura['react'])}" alt="React">
            </a>
        </div>
    </div>
"""

    html += """
    <div class="diferencias">
        <h3>Notas:</h3>
        <ul>
            <li>Click en las imagenes para ver en tamano completo</li>
            <li>Compare visualmente: columnas, datos, formatos, colores</li>
            <li>Verifique que los valores numericos coincidan</li>
        </ul>
    </div>
</body>
</html>
"""

    html_path = os.path.join(SCREENSHOTS_DIR, f"comparacion_{timestamp.replace(':', '-').replace(' ', '_')}.html")
    with open(html_path, 'w', encoding='utf-8') as f:
        f.write(html)

    print(f"\nHTML generado: {html_path}")
    return html_path


def main():
    """Funcion principal"""
    print("="*60)
    print("COMPARADOR VISUAL - Laravel vs React+FastAPI")
    print("="*60)

    config = cargar_config()
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    capturas = []

    for usuario in config['usuarios']:
        print(f"\n{'='*60}")
        print(f"USUARIO: {usuario['nombre']} ({usuario['rut']})")
        print(f"{'='*60}")

        # Driver para Laravel
        print("\n[LARAVEL]")
        driver_laravel = crear_driver()
        try:
            if login_laravel(driver_laravel, config['laravel']['login_url'], usuario['rut'], usuario['password']):
                for pagina in config['paginas']:
                    url = config['laravel']['url'] + pagina['laravel_path']
                    nombre = f"laravel_{usuario['nombre']}_{pagina['nombre']}_{timestamp.replace(':', '-').replace(' ', '_')}.png"
                    scroll = pagina.get('scroll_to_table', False)
                    # Acciones previas para Laravel
                    acciones_laravel = pagina.get('laravel_acciones_previas', [])
                    laravel_path = capturar_pagina(driver_laravel, url, nombre, pagina.get('espera_selector'), scroll, None, acciones_laravel)

                    # Capturar React con mismo usuario
                    print("\n[REACT]")
                    driver_react = crear_driver()
                    try:
                        if login_react(driver_react, config['react']['login_url'], usuario['rut'], usuario['password']):
                            url_react = config['react']['url'] + pagina['react_path']
                            nombre_react = f"react_{usuario['nombre']}_{pagina['nombre']}_{timestamp.replace(':', '-').replace(' ', '_')}.png"
                            # Usar navegacion por menu SPA si esta configurado
                            react_menu = pagina.get('react_menu')
                            # Acciones previas para React
                            acciones_react = pagina.get('react_acciones_previas', [])
                            react_path = capturar_pagina(driver_react, url_react, nombre_react, pagina.get('espera_selector'), scroll, react_menu, acciones_react)

                            capturas.append({
                                'usuario': usuario['nombre'],
                                'pagina': pagina['nombre'],
                                'laravel': laravel_path,
                                'react': react_path
                            })
                    finally:
                        driver_react.quit()
        finally:
            driver_laravel.quit()

    # Generar HTML de comparacion
    if capturas:
        html_path = generar_html_comparacion(capturas, timestamp)
        print(f"\n{'='*60}")
        print("COMPLETADO")
        print(f"{'='*60}")
        print(f"Capturas guardadas en: {SCREENSHOTS_DIR}")
        print(f"Abrir en navegador: {html_path}")

        # Abrir automaticamente en Windows
        if sys.platform == 'win32':
            os.startfile(html_path)
    else:
        print("\nNo se generaron capturas.")


if __name__ == "__main__":
    main()
