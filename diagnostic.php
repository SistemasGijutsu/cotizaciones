<?php
/**
 * Script de prueba y diagnóstico del sistema
 * Ejecutar para verificar que todo funcione correctamente
 */

echo "<h1>🔧 Diagnóstico del Sistema de Cotizaciones</h1>";
echo "<hr>";

// Test 1: Verificar archivos principales
echo "<h2>📁 Verificación de Archivos</h2>";

$requiredFiles = [
    'config/database.php' => 'Configuración de base de datos',
    'app/helpers/Helper.php' => 'Funciones auxiliares',
    'app/controllers/AuthController.php' => 'Controlador de autenticación',
    'app/models/User.php' => 'Modelo de usuario',
    'app/views/auth/login.php' => 'Vista de login',
    'index.php' => 'Punto de entrada principal'
];

foreach ($requiredFiles as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $description: <strong>OK</strong><br>";
    } else {
        echo "❌ $description: <strong>Falta</strong><br>";
    }
}

echo "<hr>";

// Test 2: Verificar inclusión de archivos
echo "<h2>📦 Test de Inclusiones</h2>";

try {
    require_once 'config/database.php';
    echo "✅ Database config incluido correctamente<br>";
    
    require_once 'app/helpers/Helper.php';
    echo "✅ Helper incluido correctamente<br>";
    
    // Test de la clase Helper
    if (class_exists('Helper')) {
        echo "✅ Clase Helper disponible<br>";
        
        // Test método formatCurrency
        $testAmount = Helper::formatCurrency(1500000);
        echo "✅ Formato de moneda: $testAmount<br>";
        
        // Test método validateEmail
        $isValid = Helper::validateEmail('test@example.com');
        echo "✅ Validación de email: " . ($isValid ? 'Funciona' : 'Error') . "<br>";
        
    } else {
        echo "❌ Clase Helper no disponible<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error en inclusiones: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 3: Verificar base de datos
echo "<h2>🗄️ Test de Base de Datos</h2>";

try {
    $db = Database::getInstance()->getConnection();
    echo "✅ Conexión a base de datos: <strong>OK</strong><br>";
    
    // Verificar tabla users
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "✅ Tabla users: {$result['count']} usuarios registrados<br>";
    
    // Verificar usuario admin
    $stmt = $db->prepare("SELECT username, email FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "✅ Usuario admin encontrado: {$admin['username']} ({$admin['email']})<br>";
    } else {
        echo "⚠️ Usuario admin no encontrado<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error de base de datos: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 4: Información del servidor
echo "<h2>🖥️ Información del Servidor</h2>";
echo "• PHP Version: " . phpversion() . "<br>";
echo "• Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'No disponible') . "<br>";
echo "• Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "• Current Directory: " . __DIR__ . "<br>";

echo "<hr>";

// Test 5: URLs de acceso
echo "<h2>🌐 URLs de Acceso</h2>";
$baseUrl = "http://localhost:8080/mod_cotizacion";
echo "• <strong>Sistema principal:</strong> <a href='$baseUrl/' target='_blank'>$baseUrl/</a><br>";
echo "• <strong>Login directo:</strong> <a href='$baseUrl/index.php?controller=auth&action=login' target='_blank'>$baseUrl/index.php?controller=auth&action=login</a><br>";

echo "<hr>";

// Instrucciones finales
echo "<h2>📋 Instrucciones</h2>";
echo "<ol>";
echo "<li>Si todos los tests están en ✅, el sistema está listo</li>";
echo "<li>Accede al sistema usando: <strong>admin / admin123</strong></li>";
echo "<li>Si hay errores ❌, revisa la configuración correspondiente</li>";
echo "</ol>";

echo "<hr>";
echo "<p><small>Diagnóstico ejecutado el " . date('d/m/Y H:i:s') . "</small></p>";
?>