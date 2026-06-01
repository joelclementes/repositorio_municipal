## Bot de Automatización para Subida de Documentos

Este repositorio incluye un script de Python (`bot_produccion.py`) diseñado para automatizar la subida de documentos (PDFs y Excel) al sistema de producción. 

### ¿Qué hace el bot?
El bot simula a un usuario humano interactuando con el sistema:
1. Inicia sesión en el portal.
2. Navega a la sección de registro de documentos.
3. De forma inteligente y automática, lee todos los **Periodos, Categorías y Subcategorías** disponibles en la pantalla.
4. Identifica qué botones de "Subir PDF" o "Subir Excel" están **habilitados** (es decir, aquellos documentos que aún no se han subido).
5. Adjunta archivos temporales (`dummy.pdf` y `dummy.xlsx`) y los sube uno por uno hasta completar todos los requerimientos pendientes del usuario con el que se inició sesión.

### Requisitos Previos

1. Necesitas tener **Python 3** y **Google Chrome** instalados en tu computadora.
2. Debes **solicitar el archivo `.env.bot`** al equipo de desarrollo. Ese archivo contiene las variables de entorno necesarias para el bot. No está incluido en el repositorio por razones de seguridad.

### Configuración de Credenciales

Dentro del archivo `.env.bot` encontrarás algo como esto:
```env
BOT_URL_LOGIN=https://repositorio-municipal.legisver.gob.mx/login
BOT_USERNAME=tu_usuario
BOT_PASSWORD=tu_contraseña
```
**Importante:** Debes cambiar los valores de `BOT_USERNAME` y `BOT_PASSWORD` en ese archivo dependiendo del usuario con el que desees ingresar al sistema para realizar la subida masiva.

### ¿Cómo ejecutarlo?

1. Entra a la carpeta del bot e instala las dependencias de Python (solo se hace la primera vez):
```bash
cd pruebas_selenium
pip install -r requirements.txt
```

2. Ejecuta el bot:
```bash
python3 bot_produccion.py
```

Al ejecutarlo, verás que se abre una ventana de Chrome. No la cierres ni interactúes con ella; el bot hará el trabajo por ti y se cerrará automáticamente al finalizar.
