<?php
/**
 * Diagnóstico de Conectividad - Sistema de Cotizaciones
 * Este archivo prueba todas las conexiones del sistema
 */

echo "<h2>🔍 Diagnóstico de Conectividad del Sistema</h2>\n";
echo "<hr>\n";

// Test 1: Verificar extensiones PHP necesarias
echo "<h3>1. Extensiones PHP</h3>\n";
$extensions = ['pdo', 'pdo_mysql', 'mysqli', 'mbstring', 'json'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ $ext: DISPONIBLE<br>\n";
    } else {
        echo "❌ $ext: NO DISPONIBLE<br>\n";
    }
}

// Test 2: Verificar archivo de configuración
echo "<h3>2. Configuración de Base de Datos</h3>\n";
if (file_exists(__DIR__ . '/config/database.php')) {
    echo "✅ Archivo config/database.php: EXISTE<br>\n";
    require_once __DIR__ . '/config/database.php';
    
    echo "📋 Configuración actual:<br>\n";
    echo "&nbsp;&nbsp;🏠 Host: " . DB_HOST . "<br>\n";
    echo "&nbsp;&nbsp;🗄️ Base de Datos: " . DB_NAME . "<br>\n";
    echo "&nbsp;&nbsp;👤 Usuario: " . DB_USER . "<br>\n";
    echo "&nbsp;&nbsp;🔒 Contraseña: " . (empty(DB_PASS) ? 'VACÍA' : 'CONFIGURADA') . "<br>\n";
} else {
    echo "❌ Archivo config/database.php: NO EXISTE<br>\n";
}

// Test 3: Probar conexión a MySQL
echo "<h3>3. Conexión a MySQL</h3>\n";
try {
    $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✅ Conexión a MySQL: EXITOSA<br>\n";
    
    // Verificar si la base de datos existe
    $stmt = $pdo->prepare("SHOW DATABASES LIKE ?");
    $stmt->execute([DB_NAME]);
    $database_exists = $stmt->fetch();
    
    if ($database_exists) {
        echo "✅ Base de datos '" . DB_NAME . "': EXISTE<br>\n";
    } else {
        echo "❌ Base de datos '" . DB_NAME . "': NO EXISTE<br>\n";
        echo "💡 <strong>SOLUCIÓN:</strong> Crear la base de datos 'cotizaciones'<br>\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error de conexión a MySQL: " . $e->getMessage() . "<br>\n";
    
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "💡 <strong>POSIBLE SOLUCIÓN:</strong> Verificar usuario y contraseña de MySQL<br>\n";
    } elseif (strpos($e->getMessage(), 'Connection refused') !== false) {
        echo "💡 <strong>POSIBLE SOLUCIÓN:</strong> Iniciar el servicio MySQL en XAMPP<br>\n";
    }
}

// Test 4: Probar conexión a la base de datos específica
echo "<h3>4. Conexión a Base de Datos Específica</h3>\n";
try {
    $db = Database::getInstance();
    echo "✅ Singleton Database: EXITOSO<br>\n";
    
    $connection = $db->getConnection();
    echo "✅ Conexión PDO: EXITOSA<br>\n";
    
    // Verificar tablas principales
    $tables = ['users', 'clientes', 'articulos', 'paquetes', 'cotizaciones'];
    foreach ($tables as $table) {
        try {
            $stmt = $connection->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$table]);
            $table_exists = $stmt->fetch();
            
            if ($table_exists) {
                echo "✅ Tabla '$table': EXISTE<br>\n";
            } else {
                echo "⚠️ Tabla '$table': NO EXISTE<br>\n";
            }
        } catch (Exception $e) {
            echo "❌ Error verificando tabla '$table': " . $e->getMessage() . "<br>\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error con Database class: " . $e->getMessage() . "<br>\n";
}

// Test 5: Verificar permisos de archivos
echo "<h3>5. Permisos de Archivos</h3>\n";
$critical_files = [
    'index.php',
    'config/database.php',
    'app/models/User.php',
    'app/controllers/AuthController.php'
];

foreach ($critical_files as $file) {
    $full_path = __DIR__ . '/' . $file;
    if (file_exists($full_path)) {
        if (is_readable($full_path)) {
            echo "✅ $file: LEGIBLE<br>\n";
        } else {
            echo "❌ $file: NO LEGIBLE<br>\n";
        }
    } else {
        echo "❌ $file: NO EXISTE<br>\n";
    }
}

// Test 6: Verificar sesiones PHP
echo "<h3>6. Configuración de Sesiones</h3>\n";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    echo "✅ Sesiones PHP: INICIADAS<br>\n";
} else {
    echo "✅ Sesiones PHP: YA ACTIVAS<br>\n";
}

echo "📁 Directorio de sesiones: " . session_save_path() . "<br>\n";
echo "🆔 ID de sesión actual: " . session_id() . "<br>\n";

echo "<hr>\n";
echo "<h3>📊 Resumen del Diagnóstico</h3>\n";
echo "Si hay errores marcados con ❌, revisa las soluciones sugeridas.<br>\n";
echo "Si todo está marcado con ✅, el problema puede estar en la lógica de la aplicación.<br>\n";
?>