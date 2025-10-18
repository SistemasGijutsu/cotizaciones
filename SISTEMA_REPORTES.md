# 📊 Sistema de Reportes e Informes

## Descripción

Sistema completo de reportes y estadísticas que permite analizar el desempeño del negocio con filtros por día, mes y año. Incluye análisis de clientes, ventas, cotizaciones y productos más vendidos.

## ✨ Características Principales

### 1. **Dashboard Principal**
- Resumen visual con tarjetas informativas
- Gráficos interactivos de ventas
- Filtros por período (día, mes, año)
- Botones de filtros rápidos

### 2. **Métricas de Clientes**
- **Total de clientes**: Cantidad total registrados
- **Clientes activos**: Con cotizaciones en el período
- **Clientes nuevos**: Primera cotización en el período
- **Clientes recurrentes**: 2 o más cotizaciones
- **Clientes inactivos**: Sin actividad en el período

### 3. **Métricas de Ventas**
- **Total ventas**: Suma de todas las cotizaciones
- **Total costos**: Suma de costos de producción
- **Utilidad neta**: Diferencia entre ventas y costos
- **Margen de utilidad**: Porcentaje de ganancia
- **Promedio de venta**: Ticket promedio

### 4. **Métricas de Cotizaciones**
- **Cantidad creadas**: Total en el período
- **Cotizaciones por día/mes/año**: Distribución temporal
- **Promedio por período**: Análisis de tendencias

### 5. **Análisis de Productos**
- **Artículos más cotizados**: Top 10
- **Cantidad total vendida**: Por artículo
- **Veces cotizado**: Frecuencia de solicitud

### 6. **Top Clientes**
- **Ranking por ventas**: 10 mejores clientes
- **Total comprado**: Monto acumulado
- **Número de cotizaciones**: Por cliente

## 🎨 Componentes Visuales

### Tarjetas de Resumen (Cards)
```
┌──────────────────────┐  ┌──────────────────────┐
│ Total Clientes       │  │ Cotizaciones Creadas │
│ ─────────────────────│  │ ─────────────────────│
│      125             │  │        48            │
│ 45 activos en período│  │ Promedio: $2,500,000 │
└──────────────────────┘  └──────────────────────┘
```

### Gráficos
- **Gráfico de líneas**: Evolución de ventas en el tiempo
- **Gráfico circular (donut)**: Distribución de clientes

### Tablas
- **Top 10 Clientes**: Ranking por ventas
- **Artículos Más Cotizados**: Frecuencia de productos
- **Clientes Nuevos**: Listado cronológico
- **Clientes Recurrentes**: Ordenado por cantidad de compras

## 📅 Filtros Disponibles

### Por Tipo de Período
1. **Por Día**: Análisis diario detallado
2. **Por Mes**: Agregación mensual
3. **Por Año**: Vista anual completa

### Filtros Rápidos
- **Hoy**: Estadísticas del día actual
- **Esta Semana**: Últimos 7 días
- **Este Mes**: Mes calendario actual
- **Este Año**: Año calendario actual

### Filtro Personalizado
- **Fecha Inicio**: Cualquier fecha
- **Fecha Fin**: Cualquier fecha
- Permite rangos personalizados

## 📥 Exportación de Datos

### Formatos Disponibles
- **CSV**: Para análisis en Excel
- Codificación UTF-8 con BOM
- Incluye encabezados de columna

### Tipos de Reportes Exportables
1. **Reporte General**: Cotizaciones por período
2. **Clientes Nuevos**: Listado completo
3. **Clientes Recurrentes**: Con totales
4. **Top Clientes**: Ranking extendido (50)
5. **Artículos Más Cotizados**: Top 50 productos

## 🔗 URLs del Sistema

### Dashboard Principal
```
http://localhost:8080/mod_cotizacion/index.php?controller=reporte&action=index
```

### Con Filtros
```
# Por día
?controller=reporte&action=index&tipo=dia&fecha_inicio=2025-10-16&fecha_fin=2025-10-16

# Por mes
?controller=reporte&action=index&tipo=mes&fecha_inicio=2025-10-01&fecha_fin=2025-10-31

# Por año
?controller=reporte&action=index&tipo=año&fecha_inicio=2025-01-01&fecha_fin=2025-12-31
```

### Exportar CSV
```
# Reporte general
?controller=reporte&action=exportarCSV&tipo=general&fecha_inicio=2025-10-01&fecha_fin=2025-10-31

# Clientes nuevos
?controller=reporte&action=exportarCSV&tipo=clientes_nuevos&fecha_inicio=2025-10-01&fecha_fin=2025-10-31

# Top clientes
?controller=reporte&action=exportarCSV&tipo=top_clientes&fecha_inicio=2025-10-01&fecha_fin=2025-10-31
```

## 📊 Ejemplos de Uso

### Caso 1: Ver reportes del mes actual
1. Ir a **Reportes e Informes** en el menú
2. Seleccionar "Por Mes"
3. Hacer clic en "Este Mes"
4. Ver tarjetas de resumen y gráficos

### Caso 2: Identificar clientes nuevos
1. Ir a Reportes
2. Filtrar por período deseado
3. Scroll hasta "Clientes Nuevos"
4. Ver listado con fecha de primera cotización
5. Exportar CSV si es necesario

### Caso 3: Análisis de productos populares
1. Ir a Reportes
2. Filtrar por período (ej: último trimestre)
3. Ver tabla "Artículos Más Cotizados"
4. Identificar los 10 productos más solicitados
5. Exportar datos para análisis

### Caso 4: Evaluar clientes recurrentes
1. Ir a Reportes
2. Filtrar por año
3. Ver sección "Clientes Recurrentes"
4. Identificar clientes con más de 2 cotizaciones
5. Ver total comprado por cada uno

### Caso 5: Exportar datos para análisis externo
1. Ir a Reportes
2. Configurar filtros según necesidad
3. Hacer clic en "Exportar CSV"
4. Seleccionar tipo de reporte
5. Abrir en Excel para análisis detallado

## 🎯 Métricas Clave a Monitorear

### Diariamente
- ✅ Cotizaciones creadas hoy
- ✅ Clientes atendidos
- ✅ Total de ventas del día

### Semanalmente
- ✅ Tendencia de ventas vs semana anterior
- ✅ Clientes nuevos de la semana
- ✅ Productos más solicitados

### Mensualmente
- ✅ Total de ventas del mes
- ✅ Comparación con mes anterior
- ✅ Clientes recurrentes
- ✅ Margen de utilidad
- ✅ Ticket promedio

### Anualmente
- ✅ Crecimiento anual
- ✅ Total de clientes nuevos
- ✅ Evolución de ventas por mes
- ✅ Productos estrella del año

## 🔢 Fórmulas de Cálculo

### Utilidad Neta
```
Utilidad = Total Ventas - Total Costos
```

### Margen de Utilidad (%)
```
Margen = (Utilidad / Total Costos) × 100
```

### Promedio de Venta
```
Promedio = Total Ventas / Cantidad de Cotizaciones
```

### Cliente Recurrente
```
Cliente con 2 o más cotizaciones en el período
```

### Cliente Nuevo
```
Primera cotización registrada está dentro del período filtrado
```

## 📱 Interfaz Responsive

El sistema es completamente responsive y funciona en:
- ✅ Desktop (1920x1080 y superiores)
- ✅ Laptop (1366x768)
- ✅ Tablet (768x1024)
- ✅ Móvil (375x667 y superiores)

## 🎨 Colores y Estados

### Tarjetas
- **Azul** (Primary): Totales generales
- **Verde** (Success): Ventas y utilidades
- **Cyan** (Info): Clientes nuevos
- **Amarillo** (Warning): Clientes recurrentes
- **Rojo** (Danger): Costos

### Estados de Datos
- **Verde**: Valores positivos, crecimiento
- **Rojo**: Costos, decrementos
- **Azul**: Información neutral
- **Gris**: Datos inactivos

## ⚡ Rendimiento

### Optimizaciones
- Índices en base de datos para consultas rápidas
- Caché de consultas frecuentes
- Paginación en tablas grandes
- Lazy loading de gráficos

### Tiempos Esperados
- **Carga inicial**: < 2 segundos
- **Filtrado**: < 1 segundo
- **Exportación CSV**: < 3 segundos

## 🔒 Seguridad

### Validaciones
- ✅ Usuario debe estar autenticado
- ✅ Validación de rangos de fechas
- ✅ Escape de datos en HTML
- ✅ Preparación de consultas SQL (PDO)
- ✅ Control de acceso por sesión

## 📚 Estructura de Archivos

```
mod_cotizacion/
├── app/
│   ├── controllers/
│   │   └── ReporteController.php      ← Lógica de reportes
│   ├── models/
│   │   └── Reporte.php                ← Consultas a BD
│   └── views/
│       └── reportes/
│           └── index.php              ← Vista principal
├── public/
│   └── css/
│       └── style.css                  ← Estilos personalizados
└── SISTEMA_REPORTES.md                ← Esta documentación
```

## 🔧 Mantenimiento

### Agregar Nuevas Métricas
1. Crear método en `app/models/Reporte.php`
2. Llamar desde `app/controllers/ReporteController.php`
3. Mostrar en `app/views/reportes/index.php`

### Agregar Nuevo Tipo de Exportación
1. Agregar caso en `exportarCSV()` del controlador
2. Crear método específico en el modelo
3. Agregar botón de exportación en la vista

## ❓ Preguntas Frecuentes

**P: ¿Por qué no veo datos en los reportes?**
R: Verifica que tengas cotizaciones en el rango de fechas seleccionado.

**P: ¿Cómo cambio el período de análisis?**
R: Usa los filtros en la parte superior o los botones rápidos.

**P: ¿Los reportes se actualizan en tiempo real?**
R: Se actualizan cada vez que recargas la página o cambias filtros.

**P: ¿Puedo comparar dos períodos?**
R: Esta funcionalidad está disponible en `action=comparar`.

**P: ¿El CSV incluye todos los datos?**
R: Sí, incluye hasta 50 registros por defecto (configurable).

## 🚀 Futuras Mejoras

- [ ] Exportación a PDF
- [ ] Gráficos adicionales (barras, áreas)
- [ ] Comparación de períodos lado a lado
- [ ] Predicciones y tendencias
- [ ] Alertas automáticas
- [ ] Reportes programados por email
- [ ] Dashboard personalizable
- [ ] Filtros avanzados (por usuario, por sucursal)

## 📞 Soporte

Para dudas o problemas:
1. Verifica que las tablas de cotizaciones tengan datos
2. Revisa la consola del navegador para errores
3. Verifica que Chart.js se cargue correctamente
4. Comprueba permisos de usuario

---

**Sistema de Reportes v1.0 - Listo para usar! 📊**
