# Sistema de Historial de Cotizaciones

## Descripción

Este sistema permite editar cotizaciones mientras mantiene un historial completo de todas las versiones anteriores. Cada vez que se edita una cotización, la versión actual se guarda en el historial junto con información sobre quién la modificó, cuándo y por qué.

## Instalación

### 1. Ejecutar el script SQL

Primero, debes ejecutar el script SQL para crear las tablas necesarias en tu base de datos:

```bash
# En MySQL o phpMyAdmin, ejecuta:
mysql -u tu_usuario -p cotizaciones < install/create_historial_table.sql
```

O desde phpMyAdmin:
1. Abre phpMyAdmin
2. Selecciona la base de datos `cotizaciones`
3. Ve a la pestaña "SQL"
4. Copia y pega el contenido del archivo `install/create_historial_table.sql`
5. Haz clic en "Ejecutar"

### 2. Verificar la instalación

El script creará:
- Tabla `cotizaciones_historial`: Almacena las versiones anteriores de las cotizaciones
- Tabla `cotizaciones_historial_detalle`: Almacena los detalles de cada versión
- Campos adicionales en la tabla `cotizaciones`:
  - `version`: Número de versión actual
  - `id_usuario_modifico`: Usuario que hizo la última modificación
  - `fecha_modificacion`: Fecha de la última modificación

## Características

### 1. Editar Cotizaciones

- **URL**: `index.php?controller=cotizacion&action=edit&id={id}`
- **Acceso**: Desde el botón "Editar" en la vista de detalle de la cotización
- **Funcionalidad**:
  - Permite modificar el cliente
  - Permite agregar, editar o eliminar items
  - Requiere un motivo de modificación (obligatorio)
  - Al guardar, crea automáticamente una nueva versión

### 2. Ver Historial

- **URL**: `index.php?controller=cotizacion&action=historial&id={id}`
- **Acceso**: Desde el botón "Historial" en la vista de detalle
- **Funcionalidad**:
  - Muestra todas las versiones anteriores de la cotización
  - Muestra quién modificó cada versión y cuándo
  - Muestra el motivo de cada modificación
  - Permite ver los detalles de cualquier versión anterior

### 3. Ver Versión Específica

- **URL**: `index.php?controller=cotizacion&action=verVersion&id={id}&version={version}`
- **Acceso**: Desde el botón "Ver" en la lista del historial
- **Funcionalidad**:
  - Muestra el estado completo de la cotización en esa versión
  - Muestra todos los items que tenía en ese momento
  - Compara los totales con la versión actual
  - Solo lectura (no se puede editar)

## Flujo de Trabajo

### Escenario típico:

1. **Crear cotización original** (v1)
   - Usuario crea una cotización nueva
   - Se guarda con versión 1
   - No hay historial aún

2. **Primera edición** (v1 → v2)
   - Usuario hace clic en "Editar"
   - Modifica items o cliente
   - Agrega motivo: "Cliente solicitó cambio de cantidad"
   - Al guardar:
     - La versión 1 se guarda en el historial
     - La cotización actual pasa a versión 2
     - Se registra quién y cuándo la modificó

3. **Segunda edición** (v2 → v3)
   - Usuario hace clic en "Editar" nuevamente
   - Modifica precios
   - Agrega motivo: "Actualización de precios de proveedor"
   - Al guardar:
     - La versión 2 se guarda en el historial
     - La cotización actual pasa a versión 3
     - Se registra la información de la modificación

4. **Consultar historial**
   - Usuario hace clic en "Historial"
   - Ve la lista de versiones: v2, v1
   - Puede hacer clic en cualquiera para ver sus detalles
   - Ve el motivo de cada cambio

## Información Guardada en el Historial

### Por cada versión se guarda:

1. **Datos principales**:
   - Número de versión
   - ID del cliente
   - Fecha de esa versión
   - Total costo
   - Total venta
   - Utilidad

2. **Datos de auditoría**:
   - Usuario que modificó
   - Fecha de modificación
   - Motivo de la modificación

3. **Detalles**:
   - Todos los items (artículos)
   - Cantidades de cada item
   - Precios de costo y venta

## Beneficios

✅ **Trazabilidad completa**: Sabes quién, cuándo y por qué se modificó cada cotización

✅ **Auditoría**: Puedes revisar cualquier versión anterior en cualquier momento

✅ **Transparencia**: Los cambios quedan documentados con motivos

✅ **Recuperación**: Si se comete un error, puedes ver la versión anterior

✅ **Histórico de precios**: Puedes ver cómo cambiaron los precios a lo largo del tiempo

## Consideraciones

### Permisos
- Actualmente, cualquier usuario autenticado puede editar cotizaciones
- Se recomienda implementar roles para restringir quién puede editar

### Base de datos
- El historial crece con cada edición
- Las tablas de historial tienen foreign keys con CASCADE para mantener integridad
- Si se elimina una cotización, se elimina también todo su historial

### Motivo obligatorio
- Al editar, es obligatorio proporcionar un motivo
- Esto asegura que cada cambio esté documentado
- El formulario no se puede enviar sin el motivo

## Archivos Modificados/Creados

### Nuevos archivos:
- `install/create_historial_table.sql` - Script de instalación
- `app/views/cotizaciones/edit.php` - Vista de edición
- `app/views/cotizaciones/historial.php` - Vista de historial
- `app/views/cotizaciones/ver_version.php` - Vista de versión específica
- `HISTORIAL_COTIZACIONES.md` - Este archivo

### Archivos modificados:
- `app/models/Cotizacion.php` - Métodos para manejar historial
- `app/controllers/CotizacionController.php` - Acciones de edición y historial
- `app/views/cotizaciones/show.php` - Botones de editar e historial

## Soporte

Si encuentras algún problema o tienes preguntas sobre el sistema de historial, por favor documenta:
1. El error específico que ves
2. Los pasos para reproducirlo
3. La versión de PHP y MySQL que usas
4. Los logs del navegador (consola de JavaScript)
5. Los logs del servidor (errores PHP)

## Próximas Mejoras

- [ ] Sistema de permisos por roles
- [ ] Restaurar una versión anterior
- [ ] Comparar dos versiones lado a lado
- [ ] Exportar historial a Excel/CSV
- [ ] Notificaciones por email cuando se edita
- [ ] Dashboard de cambios recientes
