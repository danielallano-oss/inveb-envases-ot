"""
Compara la sección 6 en detalle entre Laravel y React.
Captura screenshots y extrae estructura completa de campos.
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
from webdriver_manager.chrome import ChromeDriverManager

if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8', errors='replace')

def crear_driver():
    opts = Options()
    opts.add_argument("--start-maximized")
    opts.add_argument("--window-size=1920,1080")
    return webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=opts)

def login_laravel(driver):
    driver.get("http://localhost:8080/login")
    time.sleep(3)
    rut_field = WebDriverWait(driver, 10).until(EC.element_to_be_clickable((By.ID, "rut")))
    rut_field.clear()
    rut_field.send_keys("11334692-2")
    pwd_field = driver.find_element(By.ID, "password")
    pwd_field.clear()
    pwd_field.send_keys("123123")
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    time.sleep(3)
    print("Login Laravel OK")

def login_react(driver):
    driver.get("http://localhost:3000/login")
    time.sleep(3)
    rut_field = WebDriverWait(driver, 10).until(EC.element_to_be_clickable((By.CSS_SELECTOR, "input[type='text']")))
    rut_field.clear()
    rut_field.send_keys("11334692-2")
    pwd_field = driver.find_element(By.CSS_SELECTOR, "input[type='password']")
    pwd_field.clear()
    pwd_field.send_keys("123123")
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    time.sleep(3)
    print("Login React OK")

def extraer_seccion6_completa_laravel(driver):
    """Extrae la estructura completa de la sección 6 en Laravel"""
    return driver.execute_script("""
        var resultado = {
            titulo_seccion: '',
            subtitulos: [],
            campos: [],
            estructura_html: ''
        };

        // Buscar la sección 6 por el número
        var secciones = document.querySelectorAll('.card, .panel, section, fieldset');
        var seccion6 = null;

        for (var i = 0; i < secciones.length; i++) {
            var header = secciones[i].querySelector('.card-header, .panel-heading, h3, h4, legend');
            if (header && (header.textContent.includes('6.') ||
                          header.textContent.toLowerCase().includes('tipo item') ||
                          header.textContent.toLowerCase().includes('asistente'))) {
                seccion6 = secciones[i];
                resultado.titulo_seccion = header.textContent.trim();
                break;
            }
        }

        if (!seccion6) {
            return resultado;
        }

        // Extraer subtítulos dentro de la sección
        var subtitulos = seccion6.querySelectorAll('h5, h6, .subtitle, .section-subtitle, strong');
        subtitulos.forEach(function(sub) {
            var text = sub.textContent.trim();
            if (text && text.length > 0 && text.length < 50) {
                resultado.subtitulos.push(text);
            }
        });

        // Extraer TODOS los campos (labels + inputs)
        var allLabels = seccion6.querySelectorAll('label');
        allLabels.forEach(function(label) {
            var labelText = label.textContent.trim();
            if (!labelText) return;

            // Buscar el input/select asociado
            var forId = label.getAttribute('for');
            var input = null;

            if (forId) {
                input = document.getElementById(forId);
            }
            if (!input) {
                var parent = label.closest('.form-group, .col-md-4, .col-md-3, .col-sm-4');
                if (parent) {
                    input = parent.querySelector('input, select, textarea');
                }
            }

            var campo = {
                label: labelText,
                tipo: input ? input.tagName.toLowerCase() : 'sin_input',
                id: input ? input.id : '',
                name: input ? input.name : '',
                opciones: []
            };

            // Si es select, extraer opciones
            if (input && input.tagName === 'SELECT') {
                var options = input.querySelectorAll('option');
                options.forEach(function(opt) {
                    if (opt.value && opt.value !== '') {
                        campo.opciones.push({
                            value: opt.value,
                            text: opt.textContent.trim()
                        });
                    }
                });
            }

            resultado.campos.push(campo);
        });

        // Capturar estructura HTML simplificada
        resultado.estructura_html = seccion6.innerHTML.substring(0, 2000);

        return resultado;
    """)

def extraer_seccion6_completa_react(driver):
    """Extrae la estructura completa de la sección 6 en React"""
    return driver.execute_script("""
        var resultado = {
            titulo_seccion: '',
            subtitulos: [],
            campos: [],
            estructura_html: ''
        };

        // Buscar la sección 6 por varios métodos
        var seccion6 = null;

        // Método 1: Buscar por texto del header
        var allHeaders = document.querySelectorAll('div[class*="SectionHeader"], h1, h2, h3, h4, h5');
        for (var i = 0; i < allHeaders.length; i++) {
            var text = allHeaders[i].textContent;
            if (text.includes('6.') || text.toLowerCase().includes('asistente')) {
                // Encontrar el contenedor padre
                seccion6 = allHeaders[i].closest('div[class*="FormSection"], section, .card');
                resultado.titulo_seccion = text.trim();
                break;
            }
        }

        if (!seccion6) {
            return resultado;
        }

        // Extraer subtítulos (los títulos internos como "Producto", "Recubrimiento", etc.)
        var subtitulos = seccion6.querySelectorAll('h3, h4, h5, div[class*="SectionTitle"], div[class*="Title"]');
        subtitulos.forEach(function(sub) {
            var text = sub.textContent.trim();
            if (text && text.length > 0 && text.length < 50 && text !== resultado.titulo_seccion) {
                resultado.subtitulos.push(text);
            }
        });

        // Extraer TODOS los campos (labels + inputs)
        var allLabels = seccion6.querySelectorAll('label');
        allLabels.forEach(function(label) {
            var labelText = label.textContent.trim();
            if (!labelText) return;

            // Buscar el input/select asociado
            var parent = label.parentElement;
            var input = parent.querySelector('input, select, textarea');

            if (!input) {
                var next = label.nextElementSibling;
                if (next && (next.tagName === 'INPUT' || next.tagName === 'SELECT')) {
                    input = next;
                }
            }

            var campo = {
                label: labelText,
                tipo: input ? input.tagName.toLowerCase() : 'sin_input',
                id: input ? input.id : '',
                name: input ? (input.name || '') : '',
                opciones: []
            };

            // Si es select, extraer opciones
            if (input && input.tagName === 'SELECT') {
                var options = input.querySelectorAll('option');
                options.forEach(function(opt) {
                    if (opt.value && opt.value !== '') {
                        campo.opciones.push({
                            value: opt.value,
                            text: opt.textContent.trim()
                        });
                    }
                });
            }

            resultado.campos.push(campo);
        });

        // Capturar estructura HTML simplificada
        resultado.estructura_html = seccion6.innerHTML.substring(0, 2000);

        return resultado;
    """)

def main():
    driver = crear_driver()
    screenshots_dir = os.path.join(os.path.dirname(__file__), "screenshots")
    os.makedirs(screenshots_dir, exist_ok=True)

    resultados = {}

    try:
        # ===== LARAVEL =====
        print("\n" + "="*80)
        print("ANALIZANDO SECCIÓN 6 EN LARAVEL")
        print("="*80)

        login_laravel(driver)
        driver.get("http://localhost:8080/select-ot")
        time.sleep(3)

        # Seleccionar tipo de solicitud
        select_tipo = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.ID, "tipo_solicitud_select_ppal"))
        )
        Select(select_tipo).select_by_value("1")
        time.sleep(5)

        # Scroll para ver sección 6
        driver.execute_script("window.scrollTo(0, 800);")
        time.sleep(2)

        # Screenshot de Laravel
        driver.save_screenshot(os.path.join(screenshots_dir, "laravel_seccion6_detalle.png"))

        # Extraer estructura
        laravel_data = extraer_seccion6_completa_laravel(driver)
        resultados['laravel'] = laravel_data

        print(f"\nTítulo sección: {laravel_data.get('titulo_seccion', 'No encontrado')}")
        print(f"\nSubtítulos encontrados: {laravel_data.get('subtitulos', [])}")
        print(f"\nCampos encontrados ({len(laravel_data.get('campos', []))}):")
        for campo in laravel_data.get('campos', []):
            print(f"  - {campo['label']} ({campo['tipo']})")
            if campo['opciones']:
                print(f"    Opciones ({len(campo['opciones'])}): ", end="")
                print(", ".join([f"{o['value']}:{o['text']}" for o in campo['opciones'][:5]]))
                if len(campo['opciones']) > 5:
                    print(f"    ... y {len(campo['opciones'])-5} más")

        # ===== REACT =====
        print("\n" + "="*80)
        print("ANALIZANDO SECCIÓN 6 EN REACT")
        print("="*80)

        driver.get("http://localhost:3000/login")
        time.sleep(2)
        login_react(driver)

        driver.get("http://localhost:3000/work-orders/create")
        time.sleep(5)

        # Seleccionar tipo de solicitud si existe
        try:
            select_tipo = WebDriverWait(driver, 5).until(
                EC.presence_of_element_located((By.CSS_SELECTOR, "select"))
            )
            Select(select_tipo).select_by_value("1")
            time.sleep(3)
        except:
            pass

        # Scroll para ver sección 6
        driver.execute_script("window.scrollTo(0, 1200);")
        time.sleep(2)

        # Screenshot de React
        driver.save_screenshot(os.path.join(screenshots_dir, "react_seccion6_detalle.png"))

        # Extraer estructura
        react_data = extraer_seccion6_completa_react(driver)
        resultados['react'] = react_data

        print(f"\nTítulo sección: {react_data.get('titulo_seccion', 'No encontrado')}")
        print(f"\nSubtítulos encontrados: {react_data.get('subtitulos', [])}")
        print(f"\nCampos encontrados ({len(react_data.get('campos', []))}):")
        for campo in react_data.get('campos', []):
            print(f"  - {campo['label']} ({campo['tipo']})")
            if campo['opciones']:
                print(f"    Opciones ({len(campo['opciones'])}): ", end="")
                print(", ".join([f"{o['value']}:{o['text']}" for o in campo['opciones'][:5]]))
                if len(campo['opciones']) > 5:
                    print(f"    ... y {len(campo['opciones'])-5} más")

        # ===== COMPARACIÓN =====
        print("\n" + "="*80)
        print("DIFERENCIAS DETECTADAS")
        print("="*80)

        # Comparar subtítulos
        laravel_subs = set(laravel_data.get('subtitulos', []))
        react_subs = set(react_data.get('subtitulos', []))

        print("\nSubtítulos en Laravel:", laravel_subs if laravel_subs else "Ninguno")
        print("Subtítulos en React:", react_subs if react_subs else "Ninguno")

        if react_subs - laravel_subs:
            print(f"\n⚠️ Subtítulos EXTRAS en React (no están en Laravel):")
            for sub in react_subs - laravel_subs:
                print(f"  - {sub}")

        # Comparar campos
        laravel_labels = [c['label'].lower().strip() for c in laravel_data.get('campos', [])]
        react_labels = [c['label'].lower().strip() for c in react_data.get('campos', [])]

        print(f"\n\nOrden de campos en Laravel:")
        for i, label in enumerate(laravel_labels, 1):
            print(f"  {i}. {label}")

        print(f"\nOrden de campos en React:")
        for i, label in enumerate(react_labels, 1):
            print(f"  {i}. {label}")

        # Comparar opciones de Tipo Item
        print("\n\n=== COMPARACIÓN DE TIPO ITEM ===")
        laravel_tipo_item = next((c for c in laravel_data.get('campos', []) if 'tipo' in c['label'].lower() and 'item' in c['label'].lower()), None)
        react_tipo_item = next((c for c in react_data.get('campos', []) if 'tipo' in c['label'].lower() and 'item' in c['label'].lower()), None)

        if laravel_tipo_item:
            print(f"\nLaravel - {laravel_tipo_item['label']}:")
            for opt in laravel_tipo_item.get('opciones', []):
                print(f"  [{opt['value']}] {opt['text']}")
        else:
            print("\nLaravel - Tipo Item: NO ENCONTRADO")

        if react_tipo_item:
            print(f"\nReact - {react_tipo_item['label']}:")
            for opt in react_tipo_item.get('opciones', []):
                print(f"  [{opt['value']}] {opt['text']}")
        else:
            print("\nReact - Tipo Item: NO ENCONTRADO")

        # Guardar resultados
        with open(os.path.join(screenshots_dir, "comparacion_seccion6_detallada.json"), "w", encoding="utf-8") as f:
            json.dump(resultados, f, ensure_ascii=False, indent=2)

        print(f"\n\nResultados guardados en: {screenshots_dir}")

    except Exception as e:
        print(f"\nError: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot(os.path.join(screenshots_dir, "error.png"))

    finally:
        driver.quit()

if __name__ == "__main__":
    main()
