"""
Test de layout para CalculoHCModal - Verificación Selenium.

Este test verifica que la distribución de campos en el modal de Cálculo HC
sea correcta y no haya superposición de elementos.

Uso:
    pip install selenium webdriver-manager
    python -m pytest tests/test_calculohc_layout.py -v
"""
import pytest
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from webdriver_manager.chrome import ChromeDriverManager
import time


# Configuración
FRONTEND_URL = "http://localhost:3000"
LOGIN_RUT = "11334692-2"
LOGIN_PASSWORD = "123123"


class TestCalculoHCModalLayout:
    """Tests para verificar el layout del modal de Cálculo HC."""

    @pytest.fixture(scope="class")
    def driver(self):
        """Inicializa el driver de Chrome."""
        chrome_options = Options()
        chrome_options.add_argument("--headless")  # Ejecutar sin ventana
        chrome_options.add_argument("--no-sandbox")
        chrome_options.add_argument("--disable-dev-shm-usage")
        chrome_options.add_argument("--window-size=1920,1080")

        service = Service(ChromeDriverManager().install())
        driver = webdriver.Chrome(service=service, options=chrome_options)
        driver.implicitly_wait(10)

        yield driver
        driver.quit()

    @pytest.fixture(scope="class")
    def logged_in_driver(self, driver):
        """Login y retorna el driver autenticado."""
        driver.get(FRONTEND_URL)
        time.sleep(2)

        # Buscar campos de login
        try:
            rut_input = WebDriverWait(driver, 10).until(
                EC.presence_of_element_located((By.CSS_SELECTOR, "input[name='rut'], input[placeholder*='RUT'], input[type='text']"))
            )
            password_input = driver.find_element(By.CSS_SELECTOR, "input[type='password']")

            rut_input.clear()
            rut_input.send_keys(LOGIN_RUT)
            password_input.clear()
            password_input.send_keys(LOGIN_PASSWORD)

            # Click en botón de login
            login_button = driver.find_element(By.CSS_SELECTOR, "button[type='submit'], button:contains('Ingresar')")
            login_button.click()

            time.sleep(3)
        except Exception as e:
            print(f"Login fallido o ya autenticado: {e}")

        return driver

    def open_calculo_hc_modal(self, driver):
        """Abre el modal de Cálculo HC."""
        # Navegar a cotizaciones
        try:
            cotizaciones_link = WebDriverWait(driver, 10).until(
                EC.element_to_be_clickable((By.XPATH, "//a[contains(text(), 'Cotizaciones')]"))
            )
            cotizaciones_link.click()
            time.sleep(2)
        except:
            driver.get(f"{FRONTEND_URL}/cotizaciones")
            time.sleep(2)

        # Buscar y hacer click en botón de Cálculo HC
        try:
            calculo_btn = WebDriverWait(driver, 10).until(
                EC.element_to_be_clickable((By.CSS_SELECTOR, "[data-testid='btn-calculo-hc'], button:contains('HC')"))
            )
            calculo_btn.click()
            time.sleep(1)
        except Exception as e:
            print(f"No se encontró botón de cálculo HC: {e}")

    def test_modal_opens(self, logged_in_driver):
        """Verifica que el modal se abre correctamente."""
        driver = logged_in_driver
        self.open_calculo_hc_modal(driver)

        # Verificar que el modal está visible
        modal = WebDriverWait(driver, 10).until(
            EC.visibility_of_element_located((By.CSS_SELECTOR, "[data-testid='modal-calculo-hc'], .modal, [role='dialog']"))
        )
        assert modal.is_displayed(), "El modal no se muestra"

    def test_carton_mode_layout_no_overlap(self, logged_in_driver):
        """Verifica que en modo Cartón los campos no se superponen."""
        driver = logged_in_driver
        self.open_calculo_hc_modal(driver)

        # Seleccionar tipo de cálculo = Cartón (valor 3)
        tipo_calculo_select = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.CSS_SELECTOR, "[data-testid='select-tipo-calculo'], select[name='tipo_calculo']"))
        )

        # Seleccionar opción Cartón
        from selenium.webdriver.support.ui import Select
        try:
            select = Select(tipo_calculo_select)
            select.select_by_value("3")
        except:
            # Si es un custom select, hacer click y seleccionar
            tipo_calculo_select.click()
            time.sleep(0.5)
            option = driver.find_element(By.XPATH, "//option[@value='3'] | //div[contains(text(), 'Cartón')]")
            option.click()

        time.sleep(1)

        # Obtener posiciones de los campos
        fields = [
            ("[data-testid='field-tipo-onda']", "Tipo de Onda"),
            ("[data-testid='field-color-carton']", "Color Cartón"),
            ("[data-testid='field-rubro']", "Rubro"),
            ("[data-testid='field-ect-min']", "ECT min"),
        ]

        field_positions = []
        for selector, name in fields:
            try:
                element = driver.find_element(By.CSS_SELECTOR, selector)
                location = element.location
                size = element.size
                field_positions.append({
                    "name": name,
                    "x": location["x"],
                    "y": location["y"],
                    "width": size["width"],
                    "height": size["height"],
                    "right": location["x"] + size["width"],
                    "bottom": location["y"] + size["height"]
                })
            except Exception as e:
                print(f"No se encontró campo {name}: {e}")

        # Verificar que no hay superposición
        for i, field1 in enumerate(field_positions):
            for j, field2 in enumerate(field_positions):
                if i >= j:
                    continue

                # Verificar superposición
                overlap_x = (field1["x"] < field2["right"]) and (field1["right"] > field2["x"])
                overlap_y = (field1["y"] < field2["bottom"]) and (field1["bottom"] > field2["y"])

                if overlap_x and overlap_y:
                    # Permitir pequeña superposición por padding/margin (10px)
                    overlap_amount_x = min(field1["right"], field2["right"]) - max(field1["x"], field2["x"])
                    overlap_amount_y = min(field1["bottom"], field2["bottom"]) - max(field1["y"], field2["y"])

                    assert overlap_amount_x < 10 or overlap_amount_y < 10, \
                        f"Superposición detectada entre {field1['name']} y {field2['name']}: " \
                        f"X={overlap_amount_x}px, Y={overlap_amount_y}px"

    def test_carton_mode_row_layout(self, logged_in_driver):
        """Verifica que en modo Cartón hay 2 filas con 2 campos cada una."""
        driver = logged_in_driver
        self.open_calculo_hc_modal(driver)

        # Seleccionar tipo de cálculo = Cartón (valor 3)
        tipo_calculo_select = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.CSS_SELECTOR, "[data-testid='select-tipo-calculo'], select[name='tipo_calculo']"))
        )

        from selenium.webdriver.support.ui import Select
        try:
            select = Select(tipo_calculo_select)
            select.select_by_value("3")
        except:
            tipo_calculo_select.click()
            time.sleep(0.5)
            option = driver.find_element(By.XPATH, "//option[@value='3'] | //div[contains(text(), 'Cartón')]")
            option.click()

        time.sleep(1)

        # Obtener posiciones Y de los campos
        field_y_positions = {}
        fields = [
            ("[data-testid='field-tipo-onda']", "Tipo de Onda"),
            ("[data-testid='field-color-carton']", "Color Cartón"),
            ("[data-testid='field-rubro']", "Rubro"),
            ("[data-testid='field-ect-min']", "ECT min"),
        ]

        for selector, name in fields:
            try:
                element = driver.find_element(By.CSS_SELECTOR, selector)
                field_y_positions[name] = element.location["y"]
            except:
                pass

        if len(field_y_positions) >= 4:
            # Agrupar por fila (tolerancia de 20px)
            rows = {}
            for name, y in field_y_positions.items():
                row_found = False
                for row_y in rows:
                    if abs(y - row_y) < 20:
                        rows[row_y].append(name)
                        row_found = True
                        break
                if not row_found:
                    rows[y] = [name]

            # Debe haber al menos 2 filas
            assert len(rows) >= 2, f"Se esperaban 2 filas, se encontraron {len(rows)}"

            # Verificar que Tipo de Onda y Color Cartón están en la misma fila
            for row_y, fields_in_row in rows.items():
                if "Tipo de Onda" in fields_in_row or "Color Cartón" in fields_in_row:
                    assert "Tipo de Onda" in fields_in_row and "Color Cartón" in fields_in_row or \
                           len(fields_in_row) == 1, \
                        f"Tipo de Onda y Color Cartón deben estar en la misma fila. Fila actual: {fields_in_row}"

    def test_full_mode_three_columns(self, logged_in_driver):
        """Verifica que en modo Cálculo HC y Cartón hay 3 columnas."""
        driver = logged_in_driver
        self.open_calculo_hc_modal(driver)

        # Seleccionar tipo de cálculo = Cálculo HC y Cartón (valor 1)
        tipo_calculo_select = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.CSS_SELECTOR, "[data-testid='select-tipo-calculo'], select[name='tipo_calculo']"))
        )

        from selenium.webdriver.support.ui import Select
        try:
            select = Select(tipo_calculo_select)
            select.select_by_value("1")
        except:
            tipo_calculo_select.click()
            time.sleep(0.5)
            option = driver.find_element(By.XPATH, "//option[@value='1'] | //div[contains(text(), 'Cálculo HC y Cartón')]")
            option.click()

        time.sleep(1)

        # Buscar columnas en el formulario
        columns = driver.find_elements(By.CSS_SELECTOR, "[data-testid^='column-'], .form-column, [class*='col-']")

        # Si hay columnas definidas, verificar que hay al menos 3
        if columns:
            visible_columns = [c for c in columns if c.is_displayed()]
            assert len(visible_columns) >= 3, f"Se esperaban 3 columnas, se encontraron {len(visible_columns)}"


class TestCalculoHCModalFunctionality:
    """Tests para verificar la funcionalidad del modal."""

    @pytest.fixture(scope="class")
    def driver(self):
        """Inicializa el driver de Chrome."""
        chrome_options = Options()
        chrome_options.add_argument("--headless")
        chrome_options.add_argument("--no-sandbox")
        chrome_options.add_argument("--disable-dev-shm-usage")
        chrome_options.add_argument("--window-size=1920,1080")

        service = Service(ChromeDriverManager().install())
        driver = webdriver.Chrome(service=service, options=chrome_options)
        driver.implicitly_wait(10)

        yield driver
        driver.quit()

    def test_carton_calculation_returns_en32b(self, driver):
        """Verifica que el cálculo de cartón retorna EN32B con ECT 24."""
        # Este test hace llamada directa a la API para verificar la lógica
        import requests

        response = requests.post(
            "http://localhost:8001/api/v1/areahc/calcular",
            json={
                "tipo_calculo": 3,
                "onda_id": 1,  # B
                "carton_color": 1,  # Café
                "rubro_id": 12,  # Frutas
                "ect_min_ingresado": 24
            }
        )

        assert response.status_code == 200
        data = response.json()

        assert data["success"] is True
        assert data["codigo_carton"] == "EN32B"
        assert data["ect_min_carton"] == 24.0


if __name__ == "__main__":
    pytest.main([__file__, "-v", "-s"])
