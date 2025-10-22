# Cómo habilitar la extensión GD en XAMPP

Para que Dompdf pueda generar PDFs con imágenes (incluyendo el membrete), necesitas habilitar la extensión GD de PHP.

## Pasos para Windows/XAMPP:

### 1. Localizar el archivo php.ini
- Abre el Panel de Control de XAMPP
- Haz clic en el botón **"Config"** junto a Apache
- Selecciona **"PHP (php.ini)"**

**O también puedes editar directamente:**
- Ruta típica: `C:\xampp\php\php.ini`

### 2. Editar php.ini
1. Abre el archivo `php.ini` con un editor de texto (Notepad++, VSCode, etc.)
2. Busca la línea que dice (aproximadamente línea 900-950):
   ```
   ;extension=gd
   ```
   **O también puede estar como:**
   ```
   ;extension=php_gd.dll
   ```

3. **Elimina el punto y coma** (`;`) al inicio de la línea para descomentarla:
   ```
   extension=gd
   ```
   **O:**
   ```
   extension=php_gd.dll
   ```

### 3. Guardar y reiniciar Apache
1. Guarda el archivo `php.ini`
2. En el Panel de Control de XAMPP, haz clic en **"Stop"** en Apache
3. Luego haz clic en **"Start"** para reiniciar Apache

### 4. Verificar que GD está habilitado
Puedes verificar de dos formas:

**Opción A - Desde terminal:**
```bash
/c/xampp/php/php.exe -m | grep -i gd
```
Debería mostrar: `gd`

**Opción B - Crear un archivo PHP de prueba:**
Crea un archivo `phpinfo.php` en `C:\xampp\htdocs\` con:
```php
<?php phpinfo(); ?>
```
Luego abre en el navegador: `http://localhost/phpinfo.php`
Busca la sección "gd" - debería aparecer con información de la extensión.

### 5. Probar la generación de PDF
Una vez habilitado GD y reiniciado Apache:
```bash
cd /c/xampp/htdocs/mod_cotizacion
/c/xampp/php/php.exe scripts/generate_pdf_info_test.php
```

Ahora debería generar un archivo PDF en lugar de solo HTML.

---

## Notas importantes:

- **El DLL ya existe** en tu instalación: `C:\xampp\php\ext\php_gd.dll` ✓
- Solo necesitas **descomentar** la línea en php.ini
- Es necesario **reiniciar Apache** para que los cambios surtan efecto
- Si usas PHP desde línea de comandos (CLI), asegúrate de editar el php.ini correcto (el que usa tu CLI puede ser diferente al de Apache)

## Ajustes del membrete

Una vez que GD esté habilitado y puedas generar PDFs:

1. Asegúrate de que tu archivo `public/images/membrete.png` sea:
   - Tamaño recomendado: **210mm × 297mm** (A4) a 300 DPI = aprox. 2480 × 3508 px
   - O al menos **1240 × 1754 px** (150 DPI)
   - Formato: PNG con transparencia si es necesario

2. El diseño del membrete debe incluir:
   - **Cabecera** (logo, información empresa) en los primeros ~140px
   - **Espacio central** en blanco para el contenido (tablas, detalles)
   - **Pie de página** en los últimos ~100px

3. Puedes ajustar el padding en el CSS si es necesario:
   ```css
   .content-wrapper {
       padding: 140px 30px 100px 30px; /* top, right, bottom, left */
   }
   ```
   - `top: 140px` = espacio para el header del membrete
   - `bottom: 100px` = espacio para el footer del membrete

4. Regenera el PDF después de ajustar:
   ```bash
   /c/xampp/php/php.exe scripts/generate_pdf_info_test.php
   ```

El PDF generado estará en: `public/temp/cotizacion_XXXXXX_YYYY-MM-DD.pdf`
