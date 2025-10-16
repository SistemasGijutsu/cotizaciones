# 🚀 Guía Rápida - Sistema de Edición con Historial

## ¿Qué hace este sistema?

Permite **editar cotizaciones** y mantiene un **historial completo** de todas las versiones anteriores, registrando quién las editó, cuándo y por qué.

## ⚡ Instalación Rápida (5 minutos)

### Paso 1: Ejecutar el SQL

Abre **phpMyAdmin** y ejecuta este SQL:

```sql
-- Ve a: http://localhost/phpmyadmin
-- Selecciona la base de datos "cotizaciones"
-- Pega el contenido del archivo: install/create_historial_table.sql
-- Haz clic en "Ejecutar"
```

✅ Listo! Ya tienes las tablas creadas.

### Paso 2: Probar

1. Abre una cotización existente:
   ```
   http://localhost:8080/mod_cotizacion/index.php?controller=cotizacion&action=show&id=1
   ```

2. Verás **dos botones nuevos**:
   - **Editar** (amarillo) → Para modificar la cotización
   - **Historial** (azul) → Para ver versiones anteriores

3. Haz clic en **Editar**:
   - Modifica algún item o cantidad
   - Escribe un motivo: "Prueba del sistema"
   - Guarda

4. Haz clic en **Historial**:
   - Verás la versión anterior guardada
   - Con el motivo que escribiste
   - Y quién/cuándo la modificó

## 📸 Capturas de Pantalla

### Vista de Cotización con botones nuevos:
```
┌────────────────────────────────────────────────┐
│ Cotización #000001                    [v2]     │
├────────────────────────────────────────────────┤
│ [Editar] [Historial] [PDF] [Email] [Volver]   │
└────────────────────────────────────────────────┘
```

### Formulario de Edición:
```
┌────────────────────────────────────────────────┐
│ ⚠️ Al guardar, la versión actual se guardará  │
│    en el historial. Agregue un motivo.        │
├────────────────────────────────────────────────┤
│ Cliente: [Seleccionar...]                      │
│                                                │
│ Items:                                         │
│ [+Artículo] [+Paquete]                        │
│                                                │
│ Motivo de modificación: *                      │
│ [___________________________________]          │
│                                                │
│ [Guardar Cambios] [Cancelar]                  │
└────────────────────────────────────────────────┘
```

### Vista de Historial:
```
┌────────────────────────────────────────────────┐
│ Historial de Versiones - Cotización #000001   │
├────────────────────────────────────────────────┤
│ Versión Actual: v2                             │
│ Total: $150,000                                │
│ Última modificación: 16/10/2025 14:30         │
├────────────────────────────────────────────────┤
│ Versiones Anteriores:                          │
│                                                │
│ v1 | 15/10/2025 | $120,000 | Admin | [Ver]    │
│    └─ Motivo: "Prueba del sistema"            │
└────────────────────────────────────────────────┘
```

## 🎯 Casos de Uso

### Caso 1: Cliente solicita cambio de cantidad
1. Abrir cotización
2. Clic en "Editar"
3. Cambiar cantidad de items
4. Motivo: "Cliente solicitó 10 unidades más"
5. Guardar
✅ La versión anterior queda guardada con el motivo

### Caso 2: Actualización de precios
1. Abrir cotización
2. Clic en "Editar"
3. Actualizar precios de items
4. Motivo: "Actualización de precios de proveedor"
5. Guardar
✅ Puedes ver los precios anteriores en el historial

### Caso 3: Revisar cambios anteriores
1. Abrir cotización
2. Clic en "Historial"
3. Ver lista de versiones
4. Clic en "Ver" en cualquier versión
✅ Ves exactamente cómo estaba la cotización en ese momento

## ❓ Preguntas Frecuentes

**P: ¿Se puede editar una cotización ya enviada?**
R: Sí, pero la versión enviada queda guardada en el historial.

**P: ¿Puedo recuperar una versión anterior?**
R: Actualmente solo puedes verla. En una futura versión se podrá restaurar.

**P: ¿Quién puede editar cotizaciones?**
R: Cualquier usuario autenticado. Se recomienda agregar permisos.

**P: ¿Se puede editar el historial?**
R: No, el historial es de solo lectura para mantener integridad.

**P: ¿Qué pasa si elimino una cotización?**
R: Se elimina también todo su historial (CASCADE).

## 🐛 Solución de Problemas

### Error: "Tabla no existe"
**Solución**: Ejecuta el SQL de instalación
```sql
-- Verifica en phpMyAdmin que existen las tablas:
SHOW TABLES LIKE 'cotizaciones_historial%';
```

### Error: "Motivo es requerido"
**Solución**: El campo "Motivo de modificación" es obligatorio. Escribe algo antes de guardar.

### No veo el botón "Editar"
**Solución**: 
1. Limpia caché del navegador (Ctrl+F5)
2. Verifica que estás en la vista `show.php` de una cotización

### El historial está vacío
**Solución**: Es normal si nunca has editado la cotización. El historial se crea al hacer la primera edición.

## 📚 Más Información

- **Documentación completa**: Ver `HISTORIAL_COTIZACIONES.md`
- **Resumen técnico**: Ver `RESUMEN_IMPLEMENTACION.md`
- **Script SQL**: Ver `install/create_historial_table.sql`

## ✅ Checklist de Verificación

Antes de usar en producción:

- [ ] ✅ SQL ejecutado correctamente
- [ ] ✅ Tablas creadas (cotizaciones_historial, cotizaciones_historial_detalle)
- [ ] ✅ Campos agregados a cotizaciones (version, id_usuario_modifico, fecha_modificacion)
- [ ] ✅ Botones "Editar" e "Historial" visibles
- [ ] ✅ Edición funciona correctamente
- [ ] ✅ Historial se muestra correctamente
- [ ] ✅ Usuario autenticado en sesión
- [ ] ⚠️ Backup de base de datos realizado

---

**¿Listo para empezar? Ve al Paso 1! 🚀**
