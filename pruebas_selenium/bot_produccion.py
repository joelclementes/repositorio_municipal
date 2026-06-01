import os
import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import Select
from dotenv import load_dotenv

# Rutas absolutas de los archivos dummy y config
BASE_DIR = os.path.dirname(os.path.abspath(__file__))

# Cargar variables de entorno desde .env.bot para no interferir con el .env de Laravel
load_dotenv(os.path.join(BASE_DIR, '.env.bot'))

# --- CONFIGURACIÓN ---
URL_LOGIN = os.getenv('BOT_URL_LOGIN')
USERNAME = os.getenv('BOT_USERNAME')
PASSWORD = os.getenv('BOT_PASSWORD')

if not USERNAME or not PASSWORD:
    print("ERROR: Asegúrate de configurar BOT_USERNAME y BOT_PASSWORD en el archivo .env.bot")
    exit(1)

# Rutas absolutas de los archivos dummy
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
PDF_PATH = os.path.join(BASE_DIR, "dummy.pdf")
EXCEL_PATH = os.path.join(BASE_DIR, "dummy.xlsx")

def setup_driver():
    options = webdriver.ChromeOptions()
    # Si deseas ver el navegador en acción, comenta la siguiente línea.
    # options.add_argument('--headless')
    options.add_argument('--window-size=1920,1080')
    # Usamos Selenium Manager nativo de Selenium 4.6+
    driver = webdriver.Chrome(options=options)
    return driver

def main():
    if not os.path.exists(PDF_PATH) or not os.path.exists(EXCEL_PATH):
        print("ERROR: Asegúrate de que dummy.pdf y dummy.xlsx existan en la misma carpeta.")
        return

    driver = setup_driver()
    wait = WebDriverWait(driver, 15)
    short_wait = WebDriverWait(driver, 5)

    try:
        # 1. Iniciar sesión
        print("Iniciando sesión...")
        driver.get(URL_LOGIN)
        
        # Encontrar y rellenar formulario
        wait.until(EC.presence_of_element_located((By.NAME, "email"))).send_keys(USERNAME)
        driver.find_element(By.NAME, "password").send_keys(PASSWORD)
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        
        # Esperar a llegar al dashboard
        wait.until(EC.url_contains("/dashboard"))
        print("Sesión iniciada correctamente.")

        # 2. Navegar a la página de registro
        print("Navegando a Registro de Documentos...")
        driver.get("https://repositorio-municipal.legisver.gob.mx/documentos/registrar")
        wait.until(EC.presence_of_element_located((By.XPATH, "//select[contains(., 'Seleccione un periodo')]")))
        time.sleep(2) # Esperar a que Livewire inicialice

        # 3. Iterar Periodos
        periodo_select = Select(driver.find_element(By.XPATH, "//select[contains(., 'Seleccione un periodo')]"))
        # Obtener valores válidos (excluyendo el placeholder vacío o deshabilitados)
        periodos_options = [opt.get_attribute("value") for opt in periodo_select.options if opt.get_attribute("value") and not opt.get_attribute("disabled")]

        for p_val in periodos_options:
            print(f"=== Procesando Periodo ID: {p_val} ===")
            # Refrescar elemento para evitar StaleElementReferenceException
            periodo_select = Select(driver.find_element(By.XPATH, "//select[contains(., 'Seleccione un periodo')]"))
            periodo_select.select_by_value(p_val)
            time.sleep(2) # Wait Livewire
            
            # 4. Iterar Categorías
            try:
                cat_select_element = wait.until(EC.presence_of_element_located((By.XPATH, "//select[contains(., 'Seleccione una categor')]")))
                cat_select = Select(cat_select_element)
                categorias_options = [opt.get_attribute("value") for opt in cat_select.options if opt.get_attribute("value") and not opt.get_attribute("disabled")]
            except:
                print("No se encontraron categorías para este periodo.")
                continue

            for c_val in categorias_options:
                print(f"  --- Procesando Categoría ID: {c_val} ---")
                cat_select = Select(driver.find_element(By.XPATH, "//select[contains(., 'Seleccione una categor')]"))
                cat_select.select_by_value(c_val)
                time.sleep(2)

                # 5. Iterar Subcategorías
                try:
                    sub_select_element = short_wait.until(EC.presence_of_element_located((By.XPATH, "//select[contains(., 'Seleccione una subcategor')]")))
                    sub_select = Select(sub_select_element)
                    subcategorias_options = [opt.get_attribute("value") for opt in sub_select.options if opt.get_attribute("value") and not opt.get_attribute("disabled")]
                except:
                    print("  No se encontraron subcategorías.")
                    continue

                for s_val in subcategorias_options:
                    print(f"    >>> Procesando Subcategoría ID: {s_val}")
                    sub_select = Select(driver.find_element(By.XPATH, "//select[contains(., 'Seleccione una subcategor')]"))
                    sub_select.select_by_value(s_val)
                    time.sleep(2)

                    # 6. Procesar todos los botones habilitados
                    selector_botones = "button:not([disabled]).bg-vino-900, button:not([disabled]).bg-green-700"
                    
                    while True:
                        botones = driver.find_elements(By.CSS_SELECTOR, selector_botones)
                        if not botones:
                            print("      Sin más botones habilitados en esta subcategoría.")
                            break
                        
                        boton = botones[0]
                        es_excel = "bg-green-700" in boton.get_attribute("class")
                        tipo_str = "Excel" if es_excel else "PDF"
                        print(f"      Subiendo {tipo_str}...")
                        
                        # Hacer scroll al botón y clic
                        driver.execute_script("arguments[0].scrollIntoView(true);", boton)
                        time.sleep(0.5)
                        boton.click()
                        
                        # Esperar modal
                        wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, ".fixed.inset-0.z-50")))
                        time.sleep(1)
                        
                        # Adjuntar archivo correspondiente
                        archivo_input = driver.find_element(By.CSS_SELECTOR, "input[type='file']")
                        if es_excel:
                            archivo_input.send_keys(EXCEL_PATH)
                        else:
                            archivo_input.send_keys(PDF_PATH)
                        
                        time.sleep(1)
                        
                        # Clic en botón Subir del modal
                        boton_subir_modal = driver.find_element(By.CSS_SELECTOR, "button.bg-emerald-900")
                        boton_subir_modal.click()
                        
                        # Esperar a que se cierre el modal
                        wait.until(EC.invisibility_of_element_located((By.CSS_SELECTOR, ".fixed.inset-0.z-50")))
                        time.sleep(2) # Esperar a que la página se acomode/refresque
                        
    except Exception as e:
        print(f"Ocurrió un error: {e}")
    finally:
        print("Automatización finalizada.")
        driver.quit()

if __name__ == "__main__":
    main()
