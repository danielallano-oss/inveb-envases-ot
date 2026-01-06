"""
Extrae la estructura de campos del formulario de Crear OT en Laravel
para replicarla exactamente en React.
"""
import os
import sys
import json
import time
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
    print("Login OK")

def extraer_campos_seccion(driver, seccion_titulo):
    """Extrae los campos de una seccion del formulario"""
    campos = []

    # Buscar la seccion por su titulo
    try:
        secciones = driver.find_elements(By.CSS_SELECTOR, ".card, .panel, .form-section, section")
        for seccion in secciones:
            try:
                header = seccion.find_element(By.CSS_SELECTOR, ".card-header, .panel-heading, h3, h4, .section-header")
                if seccion_titulo.lower() in header.text.lower():
                    print(f"\nSeccion encontrada: {header.text}")

                    # Buscar todos los form-groups o campos en esta seccion
                    form_groups = seccion.find_elements(By.CSS_SELECTOR, ".form-group, .col-md-4, .col-md-3, .col-md-6, .col-sm-4, .col-sm-6")

                    for fg in form_groups:
                        try:
                            # Buscar label
                            labels = fg.find_elements(By.TAG_NAME, "label")
                            label_text = labels[0].text if labels else ""

                            # Buscar input/select
                            inputs = fg.find_elements(By.CSS_SELECTOR, "input, select, textarea")
                            for inp in inputs:
                                campo_info = {
                                    "label": label_text.strip(),
                                    "tipo": inp.tag_name,
                                    "id": inp.get_attribute("id") or "",
                                    "name": inp.get_attribute("name") or "",
                                    "clase": inp.get_attribute("class") or "",
                                }
                                if campo_info["label"] or campo_info["id"] or campo_info["name"]:
                                    campos.append(campo_info)
                        except Exception as e:
                            pass
                    break
            except:
                pass
    except Exception as e:
        print(f"Error buscando seccion: {e}")

    return campos

def main():
    driver = crear_driver()

    try:
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

        print("\n" + "="*80)
        print("ESTRUCTURA DE CAMPOS - FORMULARIO CREAR OT (LARAVEL)")
        print("="*80)

        # Extraer estructura del HTML de la seccion de datos comerciales
        # Buscar todos los labels y sus inputs asociados en orden

        # Buscar la seccion "Datos Comerciales"
        page_source = driver.page_source

        # Usar JavaScript para obtener la estructura
        campos_js = driver.execute_script("""
            var resultado = [];
            var secciones = document.querySelectorAll('.card, .panel, section, .form-section');

            for (var s = 0; s < secciones.length; s++) {
                var seccion = secciones[s];
                var header = seccion.querySelector('.card-header, .panel-heading, h3, h4');
                if (header && header.textContent.toLowerCase().includes('datos comerciales')) {
                    // Encontramos la seccion de datos comerciales
                    var rows = seccion.querySelectorAll('.row, .form-row');
                    var filaNum = 0;

                    for (var r = 0; r < rows.length; r++) {
                        var row = rows[r];
                        var cols = row.querySelectorAll('.col-md-4, .col-md-3, .col-md-6, .col-sm-4, .col-sm-6, .form-group');

                        if (cols.length > 0) {
                            filaNum++;
                            var filaCampos = [];

                            for (var c = 0; c < cols.length; c++) {
                                var col = cols[c];
                                var label = col.querySelector('label');
                                var input = col.querySelector('input, select, textarea');

                                if (label || input) {
                                    filaCampos.push({
                                        columna: c + 1,
                                        label: label ? label.textContent.trim() : '',
                                        tipo: input ? input.tagName.toLowerCase() : '',
                                        id: input ? input.id : '',
                                        name: input ? input.name : ''
                                    });
                                }
                            }

                            if (filaCampos.length > 0) {
                                resultado.push({
                                    fila: filaNum,
                                    campos: filaCampos
                                });
                            }
                        }
                    }
                    break;
                }
            }
            return resultado;
        """)

        print("\nEstructura por filas:")
        print("-" * 80)
        for fila in campos_js:
            print(f"\nFila {fila['fila']}:")
            for campo in fila['campos']:
                print(f"  Col {campo['columna']}: {campo['label']} ({campo['tipo']}) id={campo['id']} name={campo['name']}")

        # Tambien obtener todos los labels en orden de aparicion
        labels_orden = driver.execute_script("""
            var labels = [];
            var secciones = document.querySelectorAll('.card, .panel, section');

            for (var s = 0; s < secciones.length; s++) {
                var seccion = secciones[s];
                var header = seccion.querySelector('.card-header, .panel-heading, h3, h4');
                if (header && header.textContent.toLowerCase().includes('datos comerciales')) {
                    var allLabels = seccion.querySelectorAll('label');
                    for (var i = 0; i < allLabels.length; i++) {
                        var text = allLabels[i].textContent.trim();
                        if (text && text.length > 0) {
                            labels.push(text);
                        }
                    }
                    break;
                }
            }
            return labels;
        """)

        print("\n\nTodos los labels en orden de aparicion:")
        print("-" * 80)
        for i, label in enumerate(labels_orden, 1):
            print(f"  {i}. {label}")

        # Guardar screenshot para referencia
        driver.save_screenshot(os.path.join(os.path.dirname(__file__), "screenshots", "laravel_crear_ot_estructura.png"))
        print("\n\nScreenshot guardado: laravel_crear_ot_estructura.png")

    finally:
        driver.quit()

if __name__ == "__main__":
    main()
