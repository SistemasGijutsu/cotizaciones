<?php
/**
 * Script de verificación del sistema
 * Verifica que todo esté funcionando correctamente
 */

echo "<h2>🔍 Verificación del Sistema de Cotizaciones</h2>";

// Verificar conexión a la base de datos
try {
    require_once 'config/database.php';
    $db = Database::getInstance()->getConnection();
    echo "✅ Conexión a base de datos: <strong>OK</strong><br>";
    
    // Verificar que las tablas existen
    $tables = ['users', 'clientes', 'articulos', 'paquetes', 'paquete_articulos', 'cotizaciones', 'cotizacion_detalle'];
    $existingTables = [];
    
    foreach ($tables as $table) {
        $stmt = $db->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        if ($stmt->rowCount() > 0) {
            $existingTables[] = $table;
            echo "✅ Tabla <strong>$table</strong>: Existe<br>";
        } else {
            echo "❌ Tabla <strong>$table</strong>: No existe<br>";
        }
    }
    
    // Verificar usuario administrador
    $stmt = $db->prepare("SELECT * FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "✅ Usuario administrador: <strong>Existe</strong><br>";
        echo "&nbsp;&nbsp;&nbsp;• Username: {$admin['username']}<br>";
        echo "&nbsp;&nbsp;&nbsp;• Email: {$admin['email']}<br>";
        echo "&nbsp;&nbsp;&nbsp;• Creado: {$admin['created_at']}<br>";
    } else {
        echo "❌ Usuario administrador: <strong>No existe</strong><br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h3>📋 Información del Sistema</h3>";
echo "• <strong>URL de acceso:</strong> <a href='http://localhost:8080/mod_cotizacion/' target='_blank'>http://localhost:8080/mod_cotizacion/</a><br>";
echo "• <strong>Usuario:</strong> admin<br>";
echo "• <strong>Contraseña:</strong> admin123<br>";
echo "• <strong>Base de datos:</strong> cotizaciones<br>";
echo "• <strong>Puerto:</strong> 8080<br>";

echo "<hr>";
echo "<h3>🚀 Próximos pasos</h3>";
echo "1. Accede al sistema usando las credenciales arriba<br>";
echo "2. Crea algunos artículos de prueba<br>";
echo "3. Registra clientes<br>";
echo "4. Genera tu primera cotización<br>";

echo "<hr>";
echo "<p><small>Sistema de Cotizaciones Empresariales v1.0</small></p>";
?>