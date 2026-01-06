"""
Compara las opciones del campo "Tipo de Referencia" entre Laravel y React.
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

def extraer_opciones_select(driver, selector_or_id, by_type="id"):
    """Extrae todas las opciones de un select"""
    try:
        if by_type == "id":
            select_element = WebDriverWait(driver, 10).until(
                EC.presence_of_element_located((By.ID, selector_or_id))
            )
        elif by_type == "css":
            select_element = WebDriverWait(driver, 10).until(
                EC.presence_of_element_located((By.CSS_SELECTOR, selector_or_id))
            )
        elif by_type == "name":
            select_element = WebDriverWait(driver, 10).until(
                EC.presence_of_element_located((By.NAME, selector_or_id))
            )
        else:
            return []

        select = Select(select_element)
        opciones = []
        for option in select.options:
            value = option.get_attribute("value")
            text = option.text.strip()
            if value and value != "":
                opciones.append({"value": value, "text": text})
        return opciones
    except Exception as e:
        print(f"Error extrayendo opciones: {e}")
        return []

def extraer_opciones_js(driver, seccion_titulo, label_buscar):
    """Extrae opciones de un select usando JavaScript buscando por seccion y label"""
    try:
        opciones = driver.execute_script("""
            var seccionTitulo = arguments[0].toLowerCase();
            var labelBuscar = arguments[1].toLowerCase();
            var result = [];

            // Buscar todas las secciones
            var secciones = document.querySelectorAll('.card, .panel, section, fieldset, .form-section');

            for (var i = 0; i < secciones.length; i++) {
                var seccion = secciones[i];
                var header = seccion.querySelector('.card-header, .panel-heading, h3, h4, legend, .section-header');

                if (header && header.textContent.toLowerCase().includes(seccionTitulo)) {
                    // Encontramos la seccion correcta
                    var labels = seccion.querySelectorAll('label');

                    for (var j = 0; j < labels.length; j++) {
                        var label = labels[j];
                        if (label.textContent.toLowerCase().includes(labelBuscar)) {
                            // Buscar el select asociado
                            var forId = label.getAttribute('for');
                            var select = null;

                            if (forId) {
                                select = document.getElementById(forId);
                            }

                            if (!select) {
                                // Buscar select dentro del mismo contenedor padre
                                var parent = label.parentElement;
                                select = parent.querySelector('select');
                            }

                            if (!select) {
                                // Buscar en el siguiente elemento
                                var next = label.nextElementSibling;
                                if (next && next.tagName === 'SELECT') {
                                    select = next;
                                }
                            }

                            if (select) {
                                var options = select.querySelectorAll('option');
                                for (var k = 0; k < options.length; k++) {
                                    var opt = options[k];
                                    if (opt.value && opt.value !== '') {
                                        result.push({
                                            value: opt.value,
                                            text: opt.textContent.trim()
                                        });
                                    }
                                }
                            }
                            break;
                        }
                    }
                    break;
                }
            }

            return result;
        """, seccion_titulo, label_buscar)
        return opciones
    except Exception as e:
        print(f"Error en JS: {e}")
        return []

def main():
    driver = crear_driver()
    resultados = {
        "laravel": {},
        "react": {},
        "diferencias": []
    }

    try:
        # ===== LARAVEL =====
        print("\n" + "="*80)
        print("EXTRAYENDO OPCIONES DE LARAVEL (localhost:8080)")
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

        # Buscar el campo tipo_referencia
        # Intentar varios selectores posibles
        campos_a_buscar = [
            ("Tipo Referencia", "tipo_referencia", "reference_type", "referencia"),
            ("Bloqueo Referencia", "bloqueo_referencia", "bloqueo_ref", "bloqueo"),
            ("Indicador Facturacion", "indicador_facturacion", "indicador_fact", "facturacion"),
        ]

        for label_name, *posibles_ids in campos_a_buscar:
            print(f"\n--- Buscando: {label_name} ---")

            # Intentar con JavaScript primero
            opciones_js = extraer_opciones_js(driver, "referencia material", label_name.lower())

            if not opciones_js:
                # Intentar con IDs directos
                for selector in posibles_ids:
                    opciones = extraer_opciones_select(driver, selector, "id")
                    if opciones:
                        opciones_js = opciones
                        break
                    opciones = extraer_opciones_select(driver, selector, "name")
                    if opciones:
                        opciones_js = opciones
                        break

            if opciones_js:
                resultados["laravel"][label_name] = opciones_js
                print(f"  Laravel - {label_name}: {len(opciones_js)} opciones")
                for op in opciones_js:
                    print(f"    [{op['value']}] {op['text']}")
            else:
                print(f"  Laravel - {label_name}: NO ENCONTRADO")

        # Guardar screenshot Laravel
        screenshots_dir = os.path.join(os.path.dirname(__file__), "screenshots")
        os.makedirs(screenshots_dir, exist_ok=True)
        driver.save_screenshot(os.path.join(screenshots_dir, "laravel_tipo_referencia.png"))

        # ===== REACT =====
        print("\n" + "="*80)
        print("EXTRAYENDO OPCIONES DE REACT (localhost:3000)")
        print("="*80)

        driver.get("http://localhost:3000/login")
        time.sleep(2)

        login_react(driver)

        # Navegar a crear OT
        driver.get("http://localhost:3000/work-orders/create")
        time.sleep(5)

        # Intentar seleccionar tipo de solicitud primero
        try:
            tipo_select = WebDriverWait(driver, 5).until(
                EC.presence_of_element_located((By.CSS_SELECTOR, "select[name='tipo_solicitud'], #tipo_solicitud"))
            )
            Select(tipo_select).select_by_value("1")
            time.sleep(3)
        except Exception as e:
            print(f"No se pudo seleccionar tipo_solicitud en React: {e}")

        # Buscar campos en React
        for label_name, *posibles_ids in campos_a_buscar:
            print(f"\n--- Buscando: {label_name} ---")

            opciones_react = []

            # Intentar con varios selectores
            for selector in posibles_ids:
                # Por name
                try:
                    opciones_react = extraer_opciones_select(driver, f"select[name='{selector}']", "css")
                    if opciones_react:
                        break
                except:
                    pass

                # Por ID
                try:
                    opciones_react = extraer_opciones_select(driver, selector, "id")
                    if opciones_react:
                        break
                except:
                    pass

            # Si no se encontro, buscar por JavaScript
            if not opciones_react:
                opciones_react = extraer_opciones_js(driver, "referencia material", label_name.lower())

            if opciones_react:
                resultados["react"][label_name] = opciones_react
                print(f"  React - {label_name}: {len(opciones_react)} opciones")
                for op in opciones_react:
                    print(f"    [{op['value']}] {op['text']}")
            else:
                print(f"  React - {label_name}: NO ENCONTRADO")

        # Guardar screenshot React
        driver.save_screenshot(os.path.join(screenshots_dir, "react_tipo_referencia.png"))

        # ===== COMPARACION =====
        print("\n" + "="*80)
        print("COMPARACION DE OPCIONES")
        print("="*80)

        for campo in campos_a_buscar:
            label_name = campo[0]
            laravel_opts = resultados["laravel"].get(label_name, [])
            react_opts = resultados["react"].get(label_name, [])

            print(f"\n{label_name}:")
            print(f"  Laravel: {len(laravel_opts)} opciones")
            print(f"  React:   {len(react_opts)} opciones")

            if laravel_opts and not react_opts:
                resultados["diferencias"].append({
                    "campo": label_name,
                    "problema": "Campo existe en Laravel pero no en React",
                    "laravel": laravel_opts,
                    "react": []
                })
                print(f"  DIFERENCIA: Campo falta en React!")

            elif laravel_opts and react_opts:
                laravel_textos = set([o["text"] for o in laravel_opts])
                react_textos = set([o["text"] for o in react_opts])

                solo_laravel = laravel_textos - react_textos
                solo_react = react_textos - laravel_textos

                if solo_laravel or solo_react:
                    resultados["diferencias"].append({
                        "campo": label_name,
                        "problema": "Opciones diferentes",
                        "solo_laravel": list(solo_laravel),
                        "solo_react": list(solo_react)
                    })
                    if solo_laravel:
                        print(f"  Solo en Laravel: {solo_laravel}")
                    if solo_react:
                        print(f"  Solo en React: {solo_react}")
                else:
                    print(f"  OK - Mismas opciones")

        # Guardar resultados en JSON
        with open(os.path.join(screenshots_dir, "comparacion_tipo_referencia.json"), "w", encoding="utf-8") as f:
            json.dump(resultados, f, ensure_ascii=False, indent=2)

        print(f"\n\nResultados guardados en: {screenshots_dir}/comparacion_tipo_referencia.json")

        if resultados["diferencias"]:
            print("\n" + "="*80)
            print("RESUMEN DE DIFERENCIAS")
            print("="*80)
            for diff in resultados["diferencias"]:
                print(f"\n  Campo: {diff['campo']}")
                print(f"  Problema: {diff['problema']}")
        else:
            print("\n  Sin diferencias encontradas!")

    except Exception as e:
        print(f"\nError: {e}")
        import traceback
        traceback.print_exc()

    finally:
        driver.quit()

if __name__ == "__main__":
    main()
