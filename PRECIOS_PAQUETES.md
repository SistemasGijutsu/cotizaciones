# Sistema de Precios para Paquetes

## Descripción

Ahora los paquetes tienen precios de venta configurables, lo que permite crear diferentes categorías de paquetes como **Premium** y **Básico** con precios distintos para el mismo conjunto de artículos.

## Características

### 1. Precio de Venta Obligatorio
- Cada paquete ahora debe tener un precio de venta definido
- El precio se establece al crear o editar el paquete
- Permite diferenciar entre paquetes Premium y Básico

### 2. Cálculo de Utilidad
El sistema calcula automáticamente:
- **Costo Total**: Suma del costo de todos los artículos incluidos
- **Precio de Venta**: Precio definido para el paquete
- **Utilidad**: Diferencia entre precio de venta y costo total
- **Margen (%)**: Porcentaje de ganancia sobre el costo

### 3. Ejemplos de Uso

#### Paquete Básico
- **Nombre**: Paquete Oficina Básico
- **Artículos**: Escritorio básico, silla estándar, lámpara
- **Costo Total**: $15,000
- **Precio Venta**: $19,500 (30% de margen)
- **Utilidad**: $4,500

#### Paquete Premium
- **Nombre**: Paquete Oficina Premium
- **Artículos**: Escritorio ejecutivo, silla ergonómica, lámpara LED
- **Costo Total**: $35,000
- **Precio Venta**: $52,500 (50% de margen)
- **Utilidad**: $17,500

## Instalación/Actualización

### Para Instalaciones Nuevas
No requiere configuración adicional. La columna `precio_venta` ya está incluida en la estructura de la base de datos.

### Para Instalaciones Existentes

1. **Opción 1: Ejecutar archivo batch (Recomendado)**
   ```
   cd install
   actualizar_precios_paquetes.bat
   ```
   
   Este script:
   - Actualiza la estructura de la tabla
   - Calcula precios automáticos para paquetes existentes (costo + 30%)
   - Muestra un reporte de los resultados

2. **Opción 2: Ejecución manual**
   ```sql
   -- Ejecutar el archivo SQL directamente en MySQL
   SOURCE c:/xampp/htdocs/mod_cotizacion/install/update_precio_venta_paquetes.sql
   ```

3. **Opción 3: phpMyAdmin**
   - Abrir phpMyAdmin
   - Seleccionar la base de datos `cotizaciones`
   - Ir a la pestaña SQL
   - Copiar y ejecutar el contenido de `update_precio_venta_paquetes.sql`

## Uso del Sistema

### Crear un Nuevo Paquete

1. **Acceder al módulo de Paquetes**
   - Menú principal → Paquetes → Nuevo Paquete

2. **Completar información básica**
   - Nombre del paquete (ej: "Paquete Premium Oficina")
   - Descripción
   - **Precio de Venta** (campo obligatorio)
   - Imagen (opcional)

3. **Agregar artículos**
   - Clic en "Agregar Artículo"
   - Seleccionar artículos del catálogo
   - Definir cantidades
   - El sistema calcula automáticamente el costo total

4. **Verificar utilidad**
   - El panel "Resumen del Paquete" muestra:
     * Costo Total (suma de artículos)
     * Precio de Venta (lo que definiste)
     * Utilidad (ganancia)
     * Margen (%)

5. **Guardar**
   - El sistema valida que el precio de venta esté definido
   - Si todo es correcto, crea el paquete

### Editar un Paquete Existente

1. **Ubicar el paquete**
   - Lista de paquetes → Botón "Editar"

2. **Modificar precio**
   - Cambiar el campo "Precio de Venta"
   - El sistema recalcula automáticamente la utilidad

3. **Actualizar artículos** (si es necesario)
   - Modificar cantidades
   - El costo total se actualiza automáticamente

### Crear Cotizaciones

1. **Desde el paquete**
   - Ver paquete → Botón "Crear Cotización"
   - El precio del paquete se usa automáticamente

2. **Ventajas**
   - Precio consistente para todos los clientes
   - Facilita comparaciones entre paquetes
   - Simplifica el proceso de cotización

## Estrategias de Precios

### Paquetes Básicos
- **Margen sugerido**: 20-30%
- **Público objetivo**: Clientes que buscan funcionalidad
- **Ejemplo**: Oficina estándar, equipamiento básico

### Paquetes Premium
- **Margen sugerido**: 40-60%
- **Público objetivo**: Clientes que buscan calidad superior
- **Ejemplo**: Oficina ejecutiva, equipamiento de alta gama

### Paquetes Personalizados
- **Margen flexible**: Según negociación
- **Público objetivo**: Clientes con necesidades específicas
- **Ejemplo**: Soluciones a medida

## Mejores Prácticas

### 1. Nomenclatura Clara
- Incluir categoría en el nombre: "Premium", "Básico", "Estándar"
- Ejemplo: "Paquete Premium Oficina Ejecutiva"

### 2. Descripciones Detalladas
- Explicar qué incluye cada paquete
- Destacar beneficios de paquetes premium
- Mencionar casos de uso recomendados

### 3. Análisis de Precios
- Revisar periódicamente los márgenes
- Comparar con la competencia
- Ajustar según temporada o promociones

### 4. Control de Costos
- Mantener actualizados los precios de costo de artículos
- El sistema recalcula automáticamente al ver el paquete
- Verificar que la utilidad se mantenga positiva

## Reportes y Análisis

### Vista de Lista de Paquetes
Muestra para cada paquete:
- **Precio Costo**: Total del costo de artículos
- **Precio Venta**: Precio definido del paquete
- **Utilidad**: Ganancia en pesos
- **Margen %**: Porcentaje de ganancia

### Estadísticas Globales
- Total de paquetes
- Valor total de inventario en paquetes
- Promedio de artículos por paquete
- Utilidad promedio

### Exportación
Los datos se pueden usar para:
- Reportes financieros
- Análisis de rentabilidad
- Planificación de precios

## Solución de Problemas

### El campo precio de venta aparece en $0.00
**Causa**: Paquete creado antes de la actualización
**Solución**: 
1. Ejecutar `actualizar_precios_paquetes.bat`
2. O editar el paquete y establecer el precio manualmente

### Utilidad negativa
**Causa**: Precio de venta menor al costo total
**Solución**:
1. Revisar el precio de venta del paquete
2. Verificar los costos de los artículos
3. Ajustar el precio de venta para obtener margen positivo

### No puedo guardar sin precio de venta
**Causa**: El sistema ahora requiere precio de venta obligatorio
**Solución**: Definir un precio de venta antes de guardar

## Notas Técnicas

### Estructura de Base de Datos
```sql
ALTER TABLE paquetes 
MODIFY COLUMN precio_venta DECIMAL(12,2) NOT NULL DEFAULT 0.00;
```

### Validaciones
- Precio de venta >= 0
- Precio de venta es obligatorio
- Al menos un artículo en el paquete

### Cálculos
```
Utilidad = Precio_Venta - Costo_Total
Margen% = (Utilidad / Costo_Total) × 100
```

## Soporte

Si tienes dudas o problemas:
1. Revisa este documento
2. Verifica que la base de datos esté actualizada
3. Consulta los logs del sistema
4. Contacta al administrador del sistema

---

**Fecha de actualización**: 27 de octubre de 2025
**Versión**: 2.0
