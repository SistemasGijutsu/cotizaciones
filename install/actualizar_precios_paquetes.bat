@echo off
echo ========================================
echo Actualizando Precios de Paquetes
echo ========================================
echo.
echo Este script actualizara los precios de venta de los paquetes existentes.
echo.
pause

REM Configuracion de la base de datos
set DB_HOST=localhost
set DB_USER=root
set DB_PASS=
set DB_NAME=cotizaciones

echo.
echo Ejecutando script SQL...
echo.

REM Ejecutar el script SQL
mysql -h %DB_HOST% -u %DB_USER% %DB_NAME% < update_precio_venta_paquetes.sql

if %errorlevel% equ 0 (
    echo.
    echo ========================================
    echo Precios actualizados exitosamente
    echo ========================================
    echo.
    echo Los paquetes ahora tienen precios de venta definidos.
    echo Puedes crear paquetes Premium y Basico con precios diferentes.
    echo.
) else (
    echo.
    echo ========================================
    echo ERROR: No se pudo actualizar los precios
    echo ========================================
    echo.
    echo Verifica que:
    echo - MySQL este ejecutandose
    echo - Las credenciales sean correctas
    echo - La base de datos 'cotizaciones' exista
    echo.
)

pause
