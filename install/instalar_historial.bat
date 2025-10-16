@echo off
echo ================================================
echo   INSTALACION DE TABLAS DE HISTORIAL
echo   Sistema de Cotizaciones
echo ================================================
echo.

REM Verificar si MySQL está en PATH
where mysql >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] MySQL no encontrado en PATH
    echo Por favor, usa phpMyAdmin o agrega MySQL al PATH
    echo.
    echo Presiona una tecla para abrir el archivo SQL...
    pause >nul
    start notepad install\create_historial_table.sql
    exit /b 1
)

echo Configuracion de la base de datos:
echo.
set /p DB_USER="Usuario MySQL (default: root): "
if "%DB_USER%"=="" set DB_USER=root

set /p DB_PASS="Contraseña (dejar vacio si no tiene): "

set DB_NAME=cotizaciones

echo.
echo Instalando tablas de historial...
echo.

if "%DB_PASS%"=="" (
    mysql -u %DB_USER% %DB_NAME% < install\create_historial_table.sql
) else (
    mysql -u %DB_USER% -p%DB_PASS% %DB_NAME% < install\create_historial_table.sql
)

if %ERRORLEVEL% EQU 0 (
    echo.
    echo [OK] Tablas de historial instaladas correctamente
    echo.
    echo Las siguientes tablas fueron creadas:
    echo   - cotizaciones_historial
    echo   - cotizaciones_historial_detalle
    echo.
    echo Y se agregaron los siguientes campos a la tabla cotizaciones:
    echo   - version
    echo   - id_usuario_modifico
    echo   - fecha_modificacion
    echo.
    echo Ya puedes usar la funcionalidad de edicion y historial!
) else (
    echo.
    echo [ERROR] Hubo un problema al instalar las tablas
    echo Por favor, revisa la configuracion de MySQL
    echo o ejecuta el SQL manualmente desde phpMyAdmin
)

echo.
pause
