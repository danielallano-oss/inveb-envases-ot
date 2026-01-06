"""
Compara la sección 6 (Formulario Cascade/Tipo Item) entre Laravel y React.
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
    """Login en Laravel"""
    driver.get("http://localhost:8080/login")
    time.sleep(3)

    rut_field = WebDriverWait(driver, 10).until(
        EC.element_to_be_clickable((By.ID, "rut"))
    )
    rut_field.clear()
    rut_field.send_keys("11334692-2")

    pwd_field = driver.find_element(By.ID, "password")
    pwd_field.clear()
    pwd_field.send_keys("123123")

    submit = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
    submit.click()
    time.sleep(3)
    print("Login Laravel OK")

def login_react(driver):
    """Login en React"""
    driver.get("http://localhost:3000/login")
    time.sleep(3)

    rut_field = WebDriverWait(driver, 10).until(
        EC.element_to_be_clickable((By.CSS_SELECTOR, "input[type='text'], input[name='rut']"))
    )
    rut_field.clear()
    rut_field.send_keys("11334692-2")

    pwd_field = driver.find_element(By.CSS_SELECTOR, "input[type='password']")
    pwd_field.clear()
    pwd_field.send_keys("123123")

    submit = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
    submit.click()
    time.sleep(3)
    print("Login React OK")

def extraer_seccion6_laravel(driver):
    """Extrae todos los campos de la sección 6 en Laravel"""
    campos = driver.execute_script("""
        var resultado = [];
        var secciones = document.querySelectorAll('.card, .panel, section, fieldset');

        for (var s = 0; s < secciones.length; s++) {
            var seccion = secciones[s];
            var header = seccion.querySelector('.card-header, .panel-heading, h3, h4, legend');

            // Buscar sección que contenga "Tipo Item" o sea la sección 6
            if (header && (header.textContent.toLowerCase().includes('tipo item') ||
                          header.textContent.includes('6.') ||
                          header.textContent.toLowerCase().includes('producto'))) {

                console.log("Encontrada seccion:", header.textContent);

                // Extraer todos los labels y sus inputs/selects
                var formGroups = seccion.querySelectorAll('.form-group, .col-md-3, .col-md-4, .col-md-6, .col-sm-4, .col-sm-6');

                for (var i = 0; i < formGroups.length; i++) {
                    var fg = formGroups[i];
                    var label = fg.querySelector('label');
                    var input = fg.querySelector('input, select, textarea');

                    if (label || input) {
                        var campo = {
                            label: label ? label.textContent.trim() : '',
                            tipo: input ? input.tagName.toLowerCase() : '',
                            id: input ? input.id : '',
                            name: input ? input.name : '',
                            opciones: []
                        };

                        // Si es select, extraer opciones
                        if (input && input.tagName === 'SELECT') {
                            var options = input.querySelectorAll('option');
                            for (var o = 0; o < options.length; o++) {
                                if (options[o].value) {
                                    campo.opciones.push({
                                        value: options[o].value,
                                        text: options[o].textContent.trim()
                                    });
                                }
                            }
                        }

                        if (campo.label || campo.id || campo.name) {
                            resultado.push(campo);
                        }
                    }
                }
            }
        }

        return resultado;
    """)
    return campos

def extraer_seccion6_react(driver):
    """Extrae todos los campos de la sección 6 en React"""
    campos = driver.execute_script("""
        var resultado = [];

        // Buscar la sección del CascadeForm o sección 6
        var secciones = document.querySelectorAll('section, .card, div[class*="Section"], div[class*="cascade"]');

        // También buscar por el texto del header
        var allHeaders = document.querySelectorAll('h1, h2, h3, h4, h5, .section-title, [class*="SectionTitle"]');
        var targetSection = null;

        for (var h = 0; h < allHeaders.length; h++) {
            var headerText = allHeaders[h].textContent.toLowerCase();
            if (headerText.includes('producto') || headerText.includes('cascade') ||
                headerText.includes('tipo item') || headerText.includes('6.')) {
                // Encontrar el contenedor padre de este header
                targetSection = allHeaders[h].closest('section') ||
                               allHeaders[h].closest('.card') ||
                               allHeaders[h].closest('div[class*="Section"]') ||
                               allHeaders[h].parentElement.parentElement;
                break;
            }
        }

        if (!targetSection) {
            // Si no encontramos por header, buscar el CascadeForm directamente
            targetSection = document.querySelector('[class*="CascadeForm"], [class*="cascade"], form');
        }

        if (targetSection) {
            console.log("Sección encontrada en React");

            // Buscar todos los campos en esta sección
            var formGroups = targetSection.querySelectorAll('label, [class*="FormGroup"], [class*="InputGroup"]');
            var labels = targetSection.querySelectorAll('label');

            for (var i = 0; i < labels.length; i++) {
                var label = labels[i];
                var labelText = label.textContent.trim();

                // Buscar el input/select asociado
                var forId = label.getAttribute('for');
                var input = null;

                if (forId) {
                    input = document.getElementById(forId);
                }

                if (!input) {
                    // Buscar en el mismo contenedor padre
                    var parent = label.parentElement;
                    input = parent.querySelector('input, select, textarea');
                }

                if (!input) {
                    // Buscar en el siguiente elemento
                    var next = label.nextElementSibling;
                    if (next && (next.tagName === 'INPUT' || next.tagName === 'SELECT' || next.tagName === 'TEXTAREA')) {
                        input = next;
                    }
                }

                var campo = {
                    label: labelText,
                    tipo: input ? input.tagName.toLowerCase() : '',
                    id: input ? input.id : '',
                    name: input ? (input.name || input.getAttribute('name')) : '',
                    opciones: []
                };

                // Si es select, extraer opciones
                if (input && input.tagName === 'SELECT') {
                    var options = input.querySelectorAll('option');
                    for (var o = 0; o < options.length; o++) {
                        if (options[o].value) {
                            campo.opciones.push({
                                value: options[o].value,
                                text: options[o].textContent.trim()
                            });
                        }
                    }
                }

                if (campo.label) {
                    resultado.push(campo);
                }
            }
        }

        return resultado;
    """)
    return campos

def extraer_cad_matriz_laravel(driver):
    """Extrae las opciones de CAD y Matriz en Laravel"""
    resultado = driver.execute_script("""
        var data = {cad: [], matriz: []};

        // Buscar select de CAD
        var cadSelect = document.querySelector('select[name*="cad"], select[id*="cad"], #cad');
        if (cadSelect) {
            var options = cadSelect.querySelectorAll('option');
            for (var i = 0; i < options.length; i++) {
                if (options[i].value) {
                    data.cad.push({value: options[i].value, text: options[i].textContent.trim()});
                }
            }
        }

        // Buscar select de Matriz
        var matrizSelect = document.querySelector('select[name*="matriz"], select[id*="matriz"], #matriz');
        if (matrizSelect) {
            var options = matrizSelect.querySelectorAll('option');
            for (var i = 0; i < options.length; i++) {
                if (options[i].value) {
                    data.matriz.push({value: options[i].value, text: options[i].textContent.trim()});
                }
            }
        }

        return data;
    """)
    return resultado

def main():
    driver = crear_driver()
    resultados = {
        "laravel_seccion6": [],
        "react_seccion6": [],
        "laravel_cad_matriz": {},
        "diferencias": []
    }

    screenshots_dir = os.path.join(os.path.dirname(__file__), "screenshots")
    os.makedirs(screenshots_dir, exist_ok=True)

    try:
        # ===== LARAVEL =====
        print("\n" + "="*80)
        print("EXTRAYENDO SECCIÓN 6 DE LARAVEL (localhost:8080)")
        print("="*80)

        login_laravel(driver)

        # Navegar a crear OT
        driver.get("http://localhost:8080/select-ot")
        time.sleep(3)

        # Seleccionar tipo de solicitud
        select_tipo = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.ID, "tipo_solicitud_select_ppal"))
        )
        Select(select_tipo).select_by_value("1")
        time.sleep(5)

        # Scroll para ver toda la página
        driver.execute_script("window.scrollTo(0, document.body.scrollHeight/2);")
        time.sleep(2)

        # Guardar screenshot de Laravel sección 6
        driver.save_screenshot(os.path.join(screenshots_dir, "laravel_seccion6.png"))

        # Extraer campos de la sección 6
        campos_laravel = extraer_seccion6_laravel(driver)
        resultados["laravel_seccion6"] = campos_laravel

        print(f"\nCampos encontrados en Laravel sección 6: {len(campos_laravel)}")
        for campo in campos_laravel:
            print(f"  - {campo['label']} ({campo['tipo']}) id={campo['id']} name={campo['name']}")
            if campo['opciones']:
                print(f"    Opciones: {len(campo['opciones'])}")
                for op in campo['opciones'][:3]:
                    print(f"      [{op['value']}] {op['text']}")
                if len(campo['opciones']) > 3:
                    print(f"      ... y {len(campo['opciones'])-3} más")

        # Extraer CAD y Matriz
        cad_matriz = extraer_cad_matriz_laravel(driver)
        resultados["laravel_cad_matriz"] = cad_matriz

        print(f"\nCAD opciones en Laravel: {len(cad_matriz.get('cad', []))}")
        for op in cad_matriz.get('cad', [])[:5]:
            print(f"  [{op['value']}] {op['text']}")

        print(f"\nMatriz opciones en Laravel: {len(cad_matriz.get('matriz', []))}")
        for op in cad_matriz.get('matriz', [])[:5]:
            print(f"  [{op['value']}] {op['text']}")

        # ===== REACT =====
        print("\n" + "="*80)
        print("EXTRAYENDO SECCIÓN 6 DE REACT (localhost:3000)")
        print("="*80)

        driver.get("http://localhost:3000/login")
        time.sleep(2)
        login_react(driver)

        # Navegar a crear OT
        driver.get("http://localhost:3000/work-orders/create")
        time.sleep(5)

        # Scroll para ver toda la página
        driver.execute_script("window.scrollTo(0, document.body.scrollHeight/3);")
        time.sleep(2)

        # Guardar screenshot de React sección 6
        driver.save_screenshot(os.path.join(screenshots_dir, "react_seccion6.png"))

        # Extraer campos de la sección 6
        campos_react = extraer_seccion6_react(driver)
        resultados["react_seccion6"] = campos_react

        print(f"\nCampos encontrados en React sección 6: {len(campos_react)}")
        for campo in campos_react:
            print(f"  - {campo['label']} ({campo['tipo']}) id={campo['id']} name={campo['name']}")
            if campo['opciones']:
                print(f"    Opciones: {len(campo['opciones'])}")
                for op in campo['opciones'][:3]:
                    print(f"      [{op['value']}] {op['text']}")
                if len(campo['opciones']) > 3:
                    print(f"      ... y {len(campo['opciones'])-3} más")

        # ===== COMPARACIÓN =====
        print("\n" + "="*80)
        print("COMPARACIÓN SECCIÓN 6")
        print("="*80)

        laravel_labels = set([c['label'].lower().strip() for c in campos_laravel if c['label']])
        react_labels = set([c['label'].lower().strip() for c in campos_react if c['label']])

        solo_laravel = laravel_labels - react_labels
        solo_react = react_labels - laravel_labels

        print(f"\nCampos en Laravel: {len(laravel_labels)}")
        print(f"Campos en React: {len(react_labels)}")

        if solo_laravel:
            print(f"\n⚠️ CAMPOS SOLO EN LARAVEL (faltan en React):")
            for label in sorted(solo_laravel):
                print(f"  - {label}")
                resultados["diferencias"].append({
                    "tipo": "falta_en_react",
                    "campo": label
                })

        if solo_react:
            print(f"\n⚠️ CAMPOS SOLO EN REACT (extras):")
            for label in sorted(solo_react):
                print(f"  - {label}")
                resultados["diferencias"].append({
                    "tipo": "extra_en_react",
                    "campo": label
                })

        if not solo_laravel and not solo_react:
            print("\n✅ Los campos coinciden entre Laravel y React")

        # Guardar resultados
        with open(os.path.join(screenshots_dir, "comparacion_seccion6.json"), "w", encoding="utf-8") as f:
            json.dump(resultados, f, ensure_ascii=False, indent=2)

        print(f"\n\nResultados guardados en: {screenshots_dir}/comparacion_seccion6.json")

    except Exception as e:
        print(f"\nError: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot(os.path.join(screenshots_dir, "error_seccion6.png"))

    finally:
        driver.quit()

if __name__ == "__main__":
    main()
