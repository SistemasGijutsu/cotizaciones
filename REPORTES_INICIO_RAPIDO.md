# 🚀 Inicio Rápido - Sistema de Reportes

## ¿Qué hace este sistema?

Proporciona **estadísticas e informes completos** del negocio, mostrando:
- Cantidad de clientes (totales, nuevos, recurrentes)
- Cotizaciones creadas por período
- Ventas y utilidades
- Productos más vendidos
- Ranking de mejores clientes

## ⚡ Acceso Rápido (30 segundos)

### Paso 1: Ir a Reportes
```
http://localhost:8080/mod_cotizacion/index.php?controller=reporte&action=index
```

O desde el menú lateral: **Reportes e Informes**

### Paso 2: Ver Dashboard
Inmediatamente verás:
- 📊 **4 Tarjetas principales**: Clientes, Cotizaciones, Nuevos, Recurrentes
- 📈 **Gráfico de ventas**: Evolución en el tiempo
- 🥧 **Gráfico circular**: Distribución de clientes
- 🏆 **Top 10 clientes**: Ranking por ventas
- 📦 **Artículos populares**: Más cotizados

## 🎯 Casos de Uso Comunes

### 📅 Ver estadísticas del mes actual
1. Clic en botón **"Este Mes"**
2. Listo! Ves todo el mes

### 📆 Ver solo hoy
1. Clic en botón **"Hoy"**
2. Ver estadísticas del día

### 📊 Ver el año completo
1. Clic en botón **"Este Año"**
2. Análisis anual completo

### 🔍 Período personalizado
1. Seleccionar tipo: Día/Mes/Año
2. Elegir fecha inicio
3. Elegir fecha fin
4. Clic en **"Filtrar"**

### 💾 Exportar a Excel
1. Configurar filtros
2. Clic en **"Exportar CSV"**
3. Abrir con Excel

## 📊 ¿Qué Significa Cada Métrica?

### Total Clientes
```
┌──────────────────────┐
│ Total Clientes       │
│ ────────────────     │
│      125             │  ← Todos los clientes registrados
│ 45 activos en período│  ← Con cotizaciones en el rango
└──────────────────────┘
```

### Cotizaciones Creadas
```
┌──────────────────────┐
│ Cotizaciones Creadas │
│ ────────────────     │
│       48             │  ← Total en el período
│ Promedio: $2,500,000 │  ← Ticket promedio
└──────────────────────┘
```

### Clientes Nuevos
```
┌──────────────────────┐
│ Clientes Nuevos      │
│ ────────────────     │
│       12             │  ← Primera cotización en período
└──────────────────────┘
```

### Clientes Recurrentes
```
┌──────────────────────┐
│ Clientes Recurrentes │
│ ────────────────     │
│       15             │  ← 2 o más cotizaciones
└──────────────────────┘
```

## 🎨 Interpretación de Gráficos

### Gráfico de Ventas (Línea)
```
Ventas ($)
    ^
    |     /\
    |    /  \    /\
    |   /    \  /  \
    | /       \/    \
    +─────────────────> Tiempo
```
- **Hacia arriba** 📈: Crecimiento
- **Hacia abajo** 📉: Decrecimiento
- **Plano** ➡️: Estable

### Gráfico de Clientes (Circular)
```
    Inactivos
      ___
    /     \
   |  🟦   |  ← Activos (azul)
   | 🟩🟨  |  ← Nuevos (verde), Recurrentes (amarillo)
    \_____/
```

## 📥 Exportaciones Disponibles

### Desde el dashboard principal
```
┌─────────────────────────────────┐
│ [Exportar CSV ▼]                │
├─────────────────────────────────┤
│ • Reporte General               │
│ • Clientes Nuevos               │
│ • Clientes Recurrentes          │
│ • Top Clientes                  │
│ • Artículos Más Cotizados       │
└─────────────────────────────────┘
```

### Desde cada sección
- Cada tabla tiene su botón **[Exportar]**
- Descarga CSV listo para Excel
- Incluye todos los datos de esa sección

## 🔥 Trucos y Tips

### 💡 Tip 1: Comparar períodos
```
Mes actual vs Mes anterior:
1. Ver "Este Mes"
2. Anotar totales
3. Cambiar a mes anterior (manual)
4. Comparar cifras
```

### 💡 Tip 2: Identificar tendencias
```
Ver año completo:
1. Clic en "Este Año"
2. Observar gráfico de línea
3. Identificar picos y valles
```

### 💡 Tip 3: Análisis de clientes
```
Mejores clientes del año:
1. Filtrar "Este Año"
2. Ver "Top 10 Clientes"
3. Identificar patrones de compra
```

### 💡 Tip 4: Productos populares
```
Qué vender más:
1. Ver "Artículos Más Cotizados"
2. Identificar los top 3
3. Asegurar stock de estos items
```

## 📱 Acceso desde Móvil

1. Abre el navegador del móvil
2. Ve a: `http://tu-servidor/mod_cotizacion`
3. Menú → Reportes e Informes
4. Interfaz optimizada para móvil

## ⚠️ Notas Importantes

### ✅ Requerimientos
- Usuario autenticado
- Al menos 1 cotización en BD
- Navegador moderno (Chrome, Firefox, Edge)

### ❌ Limitaciones
- No hay reportes en tiempo real
- Debes recargar para ver nuevos datos
- Exportación limitada a 50 registros por defecto

## 🎯 Checklist de Uso

Antes de analizar reportes:

- [ ] ✅ Tengo cotizaciones creadas
- [ ] ✅ Sé qué período quiero analizar
- [ ] ✅ Tengo claro qué métricas necesito
- [ ] ✅ He configurado los filtros correctamente

## 🆘 Solución Rápida de Problemas

### No veo datos
**Causa**: No hay cotizaciones en ese período
**Solución**: Cambia el rango de fechas o verifica que tengas datos

### Gráficos no se muestran
**Causa**: Chart.js no cargó
**Solución**: Verifica conexión a internet, recarga la página

### CSV descarga vacío
**Causa**: No hay datos para exportar
**Solución**: Verifica filtros y que existan datos

### Números no coinciden
**Causa**: Filtros de fecha incorrectos
**Solución**: Verifica fecha inicio y fin

## 📚 Más Información

- **Documentación completa**: Ver `SISTEMA_REPORTES.md`
- **Métricas detalladas**: Consultar sección de fórmulas
- **Casos de uso**: Ver ejemplos en documentación

## ✨ Resumen Visual

```
┌─────────────────────────────────────────┐
│         DASHBOARD DE REPORTES           │
├─────────────────────────────────────────┤
│                                         │
│  [📊] [📈] [👥] [🔄]                    │
│  Clientes Cotiz Nuevos Recur            │
│    125     48    12     15              │
│                                         │
│  ┌─────────────────┐  ┌──────────────┐ │
│  │  Gráfico de     │  │  Distribución│ │
│  │  Ventas         │  │  Clientes    │ │
│  │   /\    /\      │  │    🥧        │ │
│  │  /  \  /  \     │  │              │ │
│  └─────────────────┘  └──────────────┘ │
│                                         │
│  Top Clientes        Artículos Top     │
│  1. Juan - $5M       1. Laptop (25x)   │
│  2. María - $4M      2. Mouse (20x)    │
│  3. Pedro - $3M      3. Teclado (18x)  │
│                                         │
└─────────────────────────────────────────┘
```

---

**¿Listo? Ve a Reportes y empieza a analizar! 📊**

URL directa: http://localhost:8080/mod_cotizacion/index.php?controller=reporte&action=index
