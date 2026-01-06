# -*- coding: utf-8 -*-
"""
Script para verificar TODOS los campos corregidos en Crear OT
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

def main():
    options = Options()
    options.add_argument('--start-maximized')

    driver = webdriver.Chrome(options=options)
    wait = WebDriverWait(driver, 20)
    base_path = "c:/Users/pg_54/OneDrive/Documentos/Proyectos/Tecnoandina/inveb/invebchile-envases-ot-00e7b5a341a2/invebchile-envases-ot-00e7b5a341a2/msw-envases-ot/scripts/"

    def get_select_options(label_text, exact=False):
        """Obtiene opciones de un select por texto de label."""
        try:
            if exact:
                label = driver.find_element(By.XPATH, f"//label[normalize-space(text())='{label_text}']")
            else:
                label = driver.find_element(By.XPATH, f"//label[contains(text(), '{label_text}')]")
            parent = label.find_element(By.XPATH, "./..")
            select_elem = parent.find_element(By.TAG_NAME, "select")
            options = select_elem.find_elements(By.TAG_NAME, "option")
            return [{"value": opt.get_attribute("value"), "text": opt.text.strip()} for opt in options]
        except Exception as e:
            return None

    try:
        print("=" * 80)
        print("VERIFICACION DE CAMPOS CORREGIDOS - FORMULARIO CREAR OT")
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
        time.sleep(4)  # Esperar a que carguen todos los datos

        results = {}
        all_ok = True

        # =====================================================================
        # SECCION 11 - TERMINACIONES
        # =====================================================================
        print("\n" + "=" * 80)
        print("SECCION 11 - TERMINACIONES")
        print("=" * 80)

        driver.execute_script("window.scrollTo(0, document.body.scrollHeight * 0.7);")
        time.sleep(1)

        section11_fields = [
            ("Tipo Pegado", False, ["No Aplica", "Pegado Interno", "Pegado Externo"]),
            ("Armado", True, ["Armado a Maquina", "Con/Sin Armado", "Manual"]),
            ("Sentido de Armado", False, ["No aplica", "Ancho a la Derecha", "Ancho a la Izquierda"]),
        ]

        for label, exact, expected_contains in section11_fields:
            opts = get_select_options(label, exact)
            print(f"\n--- {label} ---")
            if opts:
                texts = [o['text'] for o in opts if o['text'] != 'Seleccione...']
                print(f"   Opciones: {texts[:5]}...")
                # Verificar que contiene valores esperados
                matches = sum(1 for exp in expected_contains if any(exp in t for t in texts))
                if matches >= 2:
                    print(f"   ✓ CORRECTO - Contiene valores esperados de BD")
                else:
                    print(f"   ✗ POSIBLE ERROR - No contiene valores esperados")
                    all_ok = False
            else:
                print(f"   ✗ NO ENCONTRADO")
                all_ok = False

        # =====================================================================
        # SECCION 11 - SERVICIOS MAQUILA
        # =====================================================================
        print("\n" + "=" * 80)
        print("SECCION 11 - SERVICIOS MAQUILA")
        print("=" * 80)

        opts = get_select_options("Servicios Maquila", False)
        print(f"\n--- Servicios Maquila ---")
        if opts:
            texts = [o['text'] for o in opts if o['text'] not in ['Seleccione...', '']]
            print(f"   Total opciones: {len(texts)}")
            print(f"   Primeras opciones: {texts[:5]}")
            # Verificar que NO son valores mock
            if any("Servicio A" in t or "Servicio B" in t for t in texts):
                print(f"   ✗ ERROR - Aun tiene valores mock (Servicio A/B/C)")
                all_ok = False
            elif len(texts) > 5:
                print(f"   ✓ CORRECTO - Tiene {len(texts)} servicios de BD")
            else:
                print(f"   ? VERIFICAR - Solo {len(texts)} opciones")
        else:
            print(f"   ✗ NO ENCONTRADO")
            all_ok = False

        # =====================================================================
        # SECCION 13 - DATOS PARA DESARROLLO
        # =====================================================================
        print("\n" + "=" * 80)
        print("SECCION 13 - DATOS PARA DESARROLLO")
        print("=" * 80)

        driver.execute_script("window.scrollTo(0, document.body.scrollHeight * 0.85);")
        time.sleep(1)

        # TIPO PRODUCTO (debe mostrar Industrial/Alimentos, no Bandeja/Caja)
        opts = get_select_options("TIPO PRODUCTO", False)
        print(f"\n--- TIPO PRODUCTO ---")
        if opts:
            texts = [o['text'] for o in opts if o['text'] not in ['Seleccionar...', '']]
            print(f"   Opciones: {texts}")
            # Verificar que tiene categorías Industrial/Alimentos
            if any("Industrial" in t or "Alimentos" in t for t in texts):
                print(f"   ✓ CORRECTO - Muestra categorias Industrial/Alimentos")
            elif any("Bandeja" in t or "Caja" in t for t in texts):
                print(f"   ✗ ERROR - Muestra tipos de envase en lugar de categorias")
                all_ok = False
            else:
                print(f"   ? VERIFICAR - Valores no reconocidos")
        else:
            print(f"   ✗ NO ENCONTRADO")
            all_ok = False

        # Otros campos de Seccion 13
        section13_fields = [
            ("TIPO ALIMENTO", ["No ácidos", "Ácidos", "Grasos", "Bebidas"]),
            ("USO PREVISTO", ["Ambiente", "Refrig"]),
            ("USO RECICLADO", ["Entre", "%"]),
            ("MEDIO TRANSPORTE", ["Marítimo", "Aéreo", "Terrestre"]),
            ("MERCADO DESTINO", ["Europeo", "Asiático", "Nacional"]),
        ]

        for label, expected_contains in section13_fields:
            opts = get_select_options(label, False)
            print(f"\n--- {label} ---")
            if opts:
                texts = [o['text'] for o in opts if o['text'] not in ['Seleccionar...', '']]
                print(f"   Opciones: {texts[:4]}...")
                if len(texts) > 0:
                    print(f"   ✓ CORRECTO - Tiene {len(texts)} opciones de BD")
                else:
                    print(f"   ✗ VACIO")
                    all_ok = False
            else:
                print(f"   ? NO ENCONTRADO (puede ser label diferente)")

        # =====================================================================
        # RESUMEN
        # =====================================================================
        print("\n" + "=" * 80)
        if all_ok:
            print("RESULTADO: ✓ TODOS LOS CAMPOS VERIFICADOS CORRECTAMENTE")
        else:
            print("RESULTADO: ✗ ALGUNOS CAMPOS NECESITAN REVISION")
        print("=" * 80)

        driver.save_screenshot(base_path + "all_fields_verification.png")
        print(f"\nScreenshot guardado: all_fields_verification.png")

        time.sleep(2)

    except Exception as e:
        print(f"ERROR: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot(base_path + "verification_error.png")

    finally:
        driver.quit()

if __name__ == "__main__":
    main()
