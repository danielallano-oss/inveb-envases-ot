# -*- coding: utf-8 -*-
"""
Script para verificar TODOS los campos del formulario Crear OT
Comparar valores entre React (localhost:3000) y Laravel (localhost:8080)
"""
import sys
import io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import Select
import time
import json

def get_select_options_by_label(driver, label_text, exact_match=False):
    """Obtiene opciones de un select por texto de label."""
    try:
        if exact_match:
            label = driver.find_element(By.XPATH, f"//label[normalize-space(text())='{label_text}']")
        else:
            label = driver.find_element(By.XPATH, f"//label[contains(text(), '{label_text}')]")
        parent = label.find_element(By.XPATH, "./..")
        select_elem = parent.find_element(By.TAG_NAME, "select")
        options = select_elem.find_elements(By.TAG_NAME, "option")
        return [{"value": opt.get_attribute("value"), "text": opt.text.strip()} for opt in options]
    except Exception as e:
        return None

def get_all_selects_in_section(driver, section_title):
    """Obtiene todos los selects dentro de una sección."""
    try:
        # Buscar el título de la sección
        section = driver.find_element(By.XPATH, f"//*[contains(text(), '{section_title}')]")
        # Buscar el contenedor padre
        container = section.find_element(By.XPATH, "./ancestor::div[contains(@class, 'section') or contains(@class, 'Section') or position()=1]")
        selects = container.find_elements(By.TAG_NAME, "select")
        results = []
        for sel in selects:
            try:
                # Buscar label asociado
                sel_id = sel.get_attribute("id")
                label_text = "Unknown"
                if sel_id:
                    try:
                        label = driver.find_element(By.XPATH, f"//label[@for='{sel_id}']")
                        label_text = label.text.strip()
                    except:
                        pass
                if label_text == "Unknown":
                    # Buscar label hermano anterior
                    try:
                        label = sel.find_element(By.XPATH, "./preceding-sibling::label")
                        label_text = label.text.strip()
                    except:
                        try:
                            label = sel.find_element(By.XPATH, "../label")
                            label_text = label.text.strip()
                        except:
                            pass

                options = sel.find_elements(By.TAG_NAME, "option")
                opt_list = [{"value": opt.get_attribute("value"), "text": opt.text.strip()} for opt in options]
                results.append({"label": label_text, "options": opt_list})
            except:
                pass
        return results
    except Exception as e:
        return []

def main():
    options = Options()
    options.add_argument('--start-maximized')

    driver = webdriver.Chrome(options=options)
    wait = WebDriverWait(driver, 20)
    base_path = "c:/Users/pg_54/OneDrive/Documentos/Proyectos/Tecnoandina/inveb/invebchile-envases-ot-00e7b5a341a2/invebchile-envases-ot-00e7b5a341a2/msw-envases-ot/scripts/"

    all_fields = {}

    try:
        print("=" * 80)
        print("VERIFICACION COMPLETA DE CAMPOS - FORMULARIO CREAR OT (React)")
        print("=" * 80)

        # Login
        print("\n1. Navegando a http://localhost:3000...")
        driver.get("http://localhost:3000")
        time.sleep(3)

        print("2. Iniciando sesion...")
        rut_input = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "input[type='text']")))
        rut_input.clear()
        rut_input.send_keys("11334692-2")

        pass_input = driver.find_element(By.CSS_SELECTOR, "input[type='password']")
        pass_input.clear()
        pass_input.send_keys("123123")

        login_btn = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        login_btn.click()
        time.sleep(3)

        # Click en Crear OT
        print("3. Haciendo clic en 'Crear OT'...")
        crear_ot_btn = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Crear OT')]")))
        crear_ot_btn.click()
        time.sleep(3)

        # Seleccionar tipo de solicitud
        print("4. Seleccionando tipo de solicitud...")
        tipo_select_element = wait.until(EC.presence_of_element_located((By.ID, "tipo_solicitud_select")))
        select = Select(tipo_select_element)
        select.select_by_value("1")
        time.sleep(3)

        # =====================================================================
        # SECCION 13 - DATOS PARA DESARROLLO
        # =====================================================================
        print("\n" + "=" * 80)
        print("SECCION 13 - DATOS PARA DESARROLLO")
        print("=" * 80)

        # Scroll hacia la sección
        driver.execute_script("window.scrollTo(0, document.body.scrollHeight * 0.8);")
        time.sleep(2)

        # Campos a verificar en Sección 13
        section13_fields = [
            ("Tipo Producto", "tipo_producto", False),
            ("Tipo Alimento", "tipo_alimento", False),
            ("Uso Previsto", "uso_previsto", False),
            ("Uso Reciclado", "uso_reciclado", False),
            ("Clase Sustancia", "clase_sustancia", False),
            ("Medio de Transporte", "medio_transporte", False),
            ("Mercado Destino", "mercado_destino", False),
            ("Autosoportante", "autosoportante", False),
            ("Envase Primario", "envase_primario", False),
            ("Pallet sobre Pallet", "pallet_sobre_pallet", False),
        ]

        for label_text, field_name, exact in section13_fields:
            opts = get_select_options_by_label(driver, label_text, exact)
            all_fields[field_name] = {"label": label_text, "options": opts}

            print(f"\n--- {label_text.upper()} ---")
            if opts:
                for opt in opts[:10]:  # Mostrar solo primeras 10
                    print(f"   valor='{opt['value']}' -> '{opt['text']}'")
                if len(opts) > 10:
                    print(f"   ... y {len(opts) - 10} más")
            else:
                print("   NO ENCONTRADO")

        # =====================================================================
        # SECCION 12 - SECUENCIA OPERACIONAL
        # =====================================================================
        print("\n" + "=" * 80)
        print("SECCION 12 - SECUENCIA OPERACIONAL")
        print("=" * 80)

        # Scroll hacia arriba
        driver.execute_script("window.scrollTo(0, document.body.scrollHeight * 0.5);")
        time.sleep(2)

        # Buscar campo Planta en Secuencia Operacional
        planta_opts = get_select_options_by_label(driver, "Planta", False)
        all_fields["planta_secuencia"] = {"label": "Planta (Secuencia)", "options": planta_opts}
        print(f"\n--- PLANTA (Secuencia Operacional) ---")
        if planta_opts:
            for opt in planta_opts[:10]:
                print(f"   valor='{opt['value']}' -> '{opt['text']}'")
        else:
            print("   NO ENCONTRADO")

        # Buscar los selects de Original/Alternativas
        # Estos tienen múltiples filas con los mismos campos
        print("\n--- SELECTS DE SECUENCIA (Original/Alternativas) ---")
        try:
            # Buscar todos los selects en la sección 12
            all_selects = driver.find_elements(By.TAG_NAME, "select")
            secuencia_selects = []
            for sel in all_selects:
                try:
                    # Verificar si es parte de secuencia operacional
                    parent_text = sel.find_element(By.XPATH, "./ancestor::div[5]").text
                    if "ORIGINAL" in parent_text.upper() or "ALTERNATIVA" in parent_text.upper():
                        options = sel.find_elements(By.TAG_NAME, "option")
                        if len(options) > 1:
                            opt_texts = [o.text.strip() for o in options[:5]]
                            if opt_texts not in [s['options'] for s in secuencia_selects]:
                                secuencia_selects.append({"options": opt_texts})
                except:
                    pass

            if secuencia_selects:
                print(f"   Encontrados {len(secuencia_selects)} tipos de selects diferentes")
                for i, sel in enumerate(secuencia_selects[:3]):
                    print(f"   Tipo {i+1}: {sel['options']}")
        except Exception as e:
            print(f"   Error: {e}")

        # =====================================================================
        # SECCION 11 - TERMINACIONES (ya verificados)
        # =====================================================================
        print("\n" + "=" * 80)
        print("SECCION 11 - TERMINACIONES (ya verificados anteriormente)")
        print("=" * 80)
        print("   Proceso, Tipo Pegado, Armado, Sentido de Armado - CORREGIDOS")

        # =====================================================================
        # OTROS CAMPOS
        # =====================================================================
        print("\n" + "=" * 80)
        print("OTROS CAMPOS DEL FORMULARIO")
        print("=" * 80)

        driver.execute_script("window.scrollTo(0, document.body.scrollHeight * 0.6);")
        time.sleep(1)

        other_fields = [
            ("Servicios Maquila", "servicios_maquila", False),
            ("Maquila", "maquila", True),
        ]

        for label_text, field_name, exact in other_fields:
            opts = get_select_options_by_label(driver, label_text, exact)
            all_fields[field_name] = {"label": label_text, "options": opts}

            print(f"\n--- {label_text.upper()} ---")
            if opts:
                for opt in opts[:10]:
                    print(f"   valor='{opt['value']}' -> '{opt['text']}'")
            else:
                print("   NO ENCONTRADO")

        # =====================================================================
        # RESUMEN
        # =====================================================================
        print("\n" + "=" * 80)
        print("RESUMEN - CAMPOS QUE PODRÍAN TENER VALORES MOCK")
        print("=" * 80)

        # Campos con valores sospechosos (muy pocos o genéricos)
        suspicious = []
        for field_name, data in all_fields.items():
            if data["options"]:
                opts = data["options"]
                # Si tiene muy pocos valores (solo Seleccione + 1-2 opciones genéricas)
                texts = [o["text"] for o in opts if o["text"] and o["text"] != "Seleccione..."]
                if len(texts) <= 3 and any(t in ["Si", "No", "Sí", "Opción 1", "Opción 2"] for t in texts):
                    suspicious.append(field_name)
                # Si parece tener tipos de producto en lugar de categorías
                if field_name == "tipo_producto" and any("Bandeja" in t or "Caja" in t for t in texts):
                    suspicious.append(field_name)

        if suspicious:
            print("\nCampos sospechosos de tener valores mock:")
            for f in suspicious:
                print(f"   - {all_fields[f]['label']}")
        else:
            print("\nNo se detectaron campos sospechosos automáticamente.")
            print("Revise los valores manualmente comparando con Laravel.")

        # Guardar resultados
        with open(base_path + "form_fields_react.json", "w", encoding="utf-8") as f:
            json.dump(all_fields, f, ensure_ascii=False, indent=2)
        print(f"\nResultados guardados en: form_fields_react.json")

        driver.save_screenshot(base_path + "form_fields_screenshot.png")
        print(f"Screenshot guardado: form_fields_screenshot.png")

        time.sleep(2)

    except Exception as e:
        print(f"ERROR: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot(base_path + "form_fields_error.png")

    finally:
        driver.quit()

if __name__ == "__main__":
    main()
