# Funcionalidad: Imágenes en Paquetes

## Descripción
Los paquetes ahora pueden tener una imagen asociada que se mostrará en los PDFs de cotización, mejorando la presentación visual de los documentos.

## Cambios Implementados

### 1. Base de Datos
- **Tabla modificada**: `paquetes`
- **Campo agregado**: `imagen VARCHAR(255) DEFAULT NULL`
- **Script de migración**: `install/add_imagen_paquetes.sql`

### 2. Estructura de Archivos
- **Directorio de imágenes**: `public/images/paquetes/`
- **Formatos permitidos**: JPG, PNG, GIF, WEBP
- **Tamaño máximo**: 5MB
- **Nomenclatura**: `paquete_{timestamp}_{uniqid}.{extension}`

### 3. Modelo (Paquete.php)
- **createPaquete()**: Modificado para aceptar el campo `imagen`
- Los métodos de actualización ahora manejan el campo imagen

### 4. Controlador (PaqueteController.php)
- **store()**: 
  - Procesa la subida de imagen mediante `$_FILES['imagen']`
  - Valida tipo y tamaño de archivo
  - Genera nombre único para evitar conflictos
  
- **update()**: 
  - Permite reemplazar la imagen existente
  - Elimina la imagen anterior al subir una nueva
  
- **uploadImagen()**: Nuevo método privado que maneja:
  - Validación de tipo de archivo
  - Validación de tamaño (máx 5MB)
  - Generación de nombre único
  - Movimiento del archivo al directorio correcto

### 5. Vistas

#### create.php (Crear Paquete)
- Formulario actualizado con `enctype="multipart/form-data"`
- Nuevo campo de carga de imagen con preview en tiempo real
- JavaScript para mostrar preview antes de subir
- Botón para eliminar preview

#### edit.php (Editar Paquete)
- Muestra la imagen actual si existe
- Permite subir una nueva imagen (reemplaza la anterior)
- Preview de la nueva imagen antes de guardar
- Funciones JavaScript para manejo de preview

### 6. PDFGenerator.php
- **generarHTMLCotizacion()**: Modificado para incluir imágenes
- Por cada artículo en la cotización:
  1. Busca si pertenece a un paquete con imagen
  2. Si encuentra imagen, la muestra junto al nombre del artículo
  3. Muestra el nombre del paquete debajo del artículo
  4. La imagen se muestra a 60x60px con bordes redondeados

## Uso

### Crear Paquete con Imagen
1. Ir a "Paquetes" → "Nuevo Paquete"
2. Llenar los datos básicos del paquete
3. En el campo "Imagen del Paquete", hacer clic en "Examinar"
4. Seleccionar una imagen (JPG, PNG, GIF o WEBP)
5. Ver el preview de la imagen
6. Agregar artículos al paquete
7. Guardar

### Editar Imagen de Paquete
1. Ir a "Paquetes" → Seleccionar paquete → "Editar"
2. Se mostrará la imagen actual (si existe)
3. Para cambiarla, seleccionar nueva imagen
4. Ver preview de la nueva imagen
5. Guardar cambios (la imagen anterior se eliminará automáticamente)

### Ver en PDF de Cotización
1. Crear una cotización que incluya artículos de un paquete con imagen
2. Generar PDF de la cotización
3. En el PDF, cada artículo del paquete mostrará:
   - Miniatura de la imagen del paquete (60x60px)
   - Nombre del artículo
   - Nombre del paquete al que pertenece
   - Descripción del artículo

## Validaciones Implementadas

### Tipo de Archivo
- Solo se permiten: image/jpeg, image/jpg, image/png, image/gif, image/webp
- Si se intenta subir otro tipo, se muestra error

### Tamaño de Archivo
- Máximo: 5MB
- Si el archivo es más grande, se muestra error

### Nombres Únicos
- Cada imagen recibe un nombre único: `paquete_{timestamp}_{uniqid}.{extension}`
- Previene conflictos entre archivos con el mismo nombre

## Notas Técnicas

### Eliminación Automática
- Al reemplazar una imagen, la anterior se elimina del servidor automáticamente
- Al eliminar un paquete, la imagen asociada permanece (puede implementarse limpieza manual)

### Compatibilidad con Dompdf
- Las imágenes se embeben en el PDF usando rutas absolutas del servidor
- Dompdf las convierte automáticamente a formato compatible con PDF
- Si la imagen no existe, el PDF se genera sin ella (no causa error)

### Rendimiento
- La consulta para buscar imágenes de paquetes se ejecuta por cada artículo en el PDF
- Para cotizaciones con muchos artículos, podría optimizarse con un JOIN en la consulta principal

## Próximas Mejoras Sugeridas
1. Redimensionar automáticamente las imágenes al subirlas (thumbnails)
2. Comprimir imágenes grandes para optimizar tamaño de PDF
3. Permitir múltiples imágenes por paquete (galería)
4. Implementar eliminación de imágenes huérfanas (sin paquete asociado)
5. Añadir watermark a las imágenes en el PDF
6. Optimizar consulta de imágenes en PDFs con un solo JOIN

## Fecha de Implementación
21 de octubre de 2025
