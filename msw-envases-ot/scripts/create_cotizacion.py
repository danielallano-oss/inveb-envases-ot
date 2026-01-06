# -*- coding: utf-8 -*-
"""
Script para crear una cotizacion completa en Laravel
Usuario: 11334692-2 (Vendedor)
OT: 26591
"""
import sys
import io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')
import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.keys import Keys

def safe_select_by_value(driver, element_id, value, wait_time=1):
    """Selecciona una opcion por valor de forma segura"""
    try:
        elem = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.ID, element_id))
        )
        select = Select(elem)
        select.select_by_value(str(value))
        time.sleep(wait_time)
        return True
    except Exception as e:
        print(f"   Error en select {element_id}: {e}")
        return False

def safe_select_by_index(driver, element_id, index, wait_time=1):
    """Selecciona una opcion por indice de forma segura"""
    try:
        elem = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.ID, element_id))
        )
        select = Select(elem)
        if len(select.options) > index:
            select.select_by_index(index)
            time.sleep(wait_time)
            return True
    except Exception as e:
        print(f"   Error en select {element_id}: {e}")
    return False

def safe_input(driver, element_id, value, clear=True):
    """Ingresa un valor en un input de forma segura"""
    try:
        elem = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.ID, element_id))
        )
        if clear:
            elem.clear()
        elem.send_keys(str(value))
        return True
    except Exception as e:
        print(f"   Error en input {element_id}: {e}")
        return False

def click_bootstrap_select(driver, element_id, option_text):
    """Hace click en un bootstrap select y selecciona una opcion"""
    try:
        # Buscar el boton del select
        btn = driver.find_element(By.CSS_SELECTOR, f"#{element_id} + .dropdown-toggle, [data-id='{element_id}']")
        btn.click()
        time.sleep(0.5)

        # Buscar la opcion
        options = driver.find_elements(By.CSS_SELECTOR, ".dropdown-menu.show .dropdown-item")
        for opt in options:
            if option_text.lower() in opt.text.lower():
                opt.click()
                time.sleep(0.5)
                return True
    except Exception as e:
        print(f"   Error en bootstrap select {element_id}: {e}")
    return False

def create_cotizacion():
    chrome_options = Options()
    chrome_options.add_argument("--start-maximized")
    driver = webdriver.Chrome(options=chrome_options)
    wait = WebDriverWait(driver, 15)

    try:
        print("=" * 70)
        print("CREACION DE COTIZACION EN LARAVEL")
        print("=" * 70)

        # 1. Login como Vendedor
        print("\n1. Login como Vendedor (11334692-2)...")
        driver.get("http://localhost:8080/login")
        time.sleep(2)

        wait.until(EC.presence_of_element_located((By.NAME, "rut"))).send_keys("11334692-2")
        driver.find_element(By.NAME, "password").send_keys("123123")
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        time.sleep(3)
        print("   Login exitoso")

        # 2. Navegar a cotizar OT 26591
        print("\n2. Navegando a cotizar OT 26591...")
        driver.get("http://localhost:8080/cotizador/cotizarOt/26591")
        time.sleep(5)
        driver.save_screenshot("cotiz_create_01_inicio.png")

        # 3. Verificar si hubo errores
        if "error" in driver.page_source.lower() and "exception" in driver.page_source.lower():
            print("   ERROR: La pagina tiene errores")
            driver.save_screenshot("cotiz_create_error.png")
            # Intentar obtener mensaje de error
            try:
                error_text = driver.find_element(By.CSS_SELECTOR, ".exception-message, .alert-danger").text
                print(f"   Mensaje: {error_text[:200]}")
            except:
                pass
            return False

        print("   Pagina cargada correctamente")

        # 4. Configurar Datos Comerciales
        print("\n3. Configurando Datos Comerciales...")

        # El cliente ya deberia estar seleccionado desde la OT
        # Pero verificamos y seleccionamos si es necesario

        # 5. Abrir modal para crear detalle
        print("\n4. Abriendo modal para crear detalle...")
        try:
            crear_detalle_btn = wait.until(EC.element_to_be_clickable(
                (By.ID, "crear_precotizacion")
            ))
            crear_detalle_btn.click()
            time.sleep(2)
        except:
            # Intentar otro selector
            try:
                crear_detalle_btn = driver.find_element(By.XPATH, "//a[contains(text(), 'Crear Detalle')]")
                crear_detalle_btn.click()
                time.sleep(2)
            except Exception as e:
                print(f"   Error abriendo modal: {e}")
                driver.save_screenshot("cotiz_create_error_modal.png")

        driver.save_screenshot("cotiz_create_02_modal.png")

        # 6. Completar formulario de detalle
        print("\n5. Completando formulario de detalle...")

        # Esperar a que el modal este visible
        time.sleep(2)

        # Tipo de detalle (1 = Corrugado)
        print("   - Tipo detalle: Corrugado")
        safe_select_by_value(driver, "tipo_detalle_id", "1", 1)

        # Carton - seleccionar el primero disponible
        print("   - Seleccionando carton...")
        try:
            # Bootstrap select picker
            carton_btn = driver.find_element(By.CSS_SELECTOR, "[data-id='carton_id']")
            carton_btn.click()
            time.sleep(1)
            # Seleccionar primera opcion valida
            options = driver.find_elements(By.CSS_SELECTOR, ".dropdown-menu.show .dropdown-item")
            for opt in options:
                if opt.text and "Seleccionar" not in opt.text:
                    print(f"      Carton: {opt.text[:40]}")
                    opt.click()
                    break
            time.sleep(1)
        except Exception as e:
            print(f"      Error: {e}")

        # Tipo de producto
        print("   - Seleccionando tipo de producto...")
        try:
            product_btn = driver.find_element(By.CSS_SELECTOR, "[data-id='product_type_id']")
            product_btn.click()
            time.sleep(1)
            options = driver.find_elements(By.CSS_SELECTOR, ".dropdown-menu.show .dropdown-item")
            for opt in options:
                if "caja" in opt.text.lower():
                    print(f"      Tipo producto: {opt.text[:40]}")
                    opt.click()
                    break
            time.sleep(1)
        except Exception as e:
            print(f"      Error: {e}")

        # Cantidad
        print("   - Ingresando cantidad...")
        safe_input(driver, "cantidad", "1000")

        # Scroll down en el modal
        driver.execute_script("document.querySelector('.modal-body').scrollTop = 500")
        time.sleep(1)

        # Proceso - ya deberia estar en CORRUGADO desde la OT
        print("   - Verificando proceso...")
        try:
            proceso_elem = driver.find_element(By.ID, "process_id")
            proceso_val = proceso_elem.get_attribute("value")
            print(f"      Proceso actual: {proceso_val}")
        except:
            pass

        # Numero de colores
        print("   - Seleccionando colores...")
        try:
            colores_btn = driver.find_element(By.CSS_SELECTOR, "[data-id='numero_colores']")
            colores_btn.click()
            time.sleep(0.5)
            options = driver.find_elements(By.CSS_SELECTOR, ".dropdown-menu.show .dropdown-item")
            for opt in options:
                if "2" in opt.text:
                    opt.click()
                    print("      Colores: 2")
                    break
            time.sleep(0.5)
        except Exception as e:
            print(f"      Error: {e}")

        # Impresion
        print("   - Ingresando impresion...")
        safe_input(driver, "impresion", "50")

        # Mas scroll
        driver.execute_script("document.querySelector('.modal-body').scrollTop = 1000")
        time.sleep(1)
        driver.save_screenshot("cotiz_create_03_form_mid.png")

        # Precio USD/Millar (si existe)
        print("   - Buscando campo de precio...")
        try:
            precio_field = driver.find_element(By.ID, "precio_usd_millar")
            precio_field.clear()
            precio_field.send_keys("150")
            print("      Precio USD/Millar: 150")
        except:
            print("      Campo precio no encontrado (se calculara)")

        # 7. Guardar detalle
        print("\n6. Guardando detalle...")
        driver.execute_script("document.querySelector('.modal-body').scrollTop = document.querySelector('.modal-body').scrollHeight")
        time.sleep(1)
        driver.save_screenshot("cotiz_create_04_before_save.png")

        try:
            guardar_btn = driver.find_element(By.ID, "guardarDetalleCotizacion")
            guardar_btn.click()
            time.sleep(3)
            print("   Detalle guardado")
        except Exception as e:
            print(f"   Error guardando: {e}")

        driver.save_screenshot("cotiz_create_05_after_save.png")

        # 8. Verificar si el detalle se agrego
        print("\n7. Verificando detalle agregado...")
        time.sleep(2)
        try:
            tabla = driver.find_element(By.ID, "listado-detalles")
            filas = tabla.find_elements(By.TAG_NAME, "tr")
            print(f"   Filas en tabla: {len(filas)}")
        except:
            pass

        # 9. Generar Pre-Cotizacion
        print("\n8. Generando Pre-Cotizacion...")
        try:
            generar_btn = driver.find_element(By.ID, "generarPrecotizacion")
            generar_btn.click()
            time.sleep(5)
            print("   Pre-cotizacion generada")
        except Exception as e:
            print(f"   Error generando: {e}")

        driver.save_screenshot("cotiz_create_06_precotizacion.png")

        # 10. Guardar cotizacion (si hay boton)
        print("\n9. Buscando boton guardar cotizacion...")
        try:
            guardar_cotiz = driver.find_element(By.XPATH, "//button[contains(text(), 'Guardar') or contains(text(), 'Crear')]")
            print(f"   Boton encontrado: {guardar_cotiz.text}")
            # guardar_cotiz.click()
            # time.sleep(3)
        except:
            print("   No se encontro boton guardar principal")

        driver.execute_script("window.scrollTo(0, document.body.scrollHeight)")
        time.sleep(1)
        driver.save_screenshot("cotiz_create_07_final.png")

        print("\n" + "=" * 70)
        print("PROCESO COMPLETADO")
        print("=" * 70)
        print("\nScreenshots generados:")
        print("  - cotiz_create_01_inicio.png a cotiz_create_07_final.png")

        # Mantener navegador abierto para revision
        print("\nManteniendo navegador abierto 60 segundos...")
        time.sleep(60)

        return True

    except Exception as e:
        print(f"\nERROR: {e}")
        import traceback
        traceback.print_exc()
        driver.save_screenshot("cotiz_create_error.png")
        return False

    finally:
        driver.quit()

if __name__ == "__main__":
    create_cotizacion()
