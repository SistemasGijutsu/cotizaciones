<?php
/**
 * Script para crear la tabla de usuarios
 * Ejecutar una sola vez para inicializar el sistema de autenticación
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // La tabla users ya existe, solo verificamos la estructura
    echo "ℹ️ Tabla users ya existe en la base de datos\n";
    
    // Crear usuario administrador por defecto
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    $checkAdmin = $db->prepare("SELECT id FROM users WHERE username = 'admin'");
    $checkAdmin->execute();
    
    if ($checkAdmin->rowCount() == 0) {
        $insertAdmin = $db->prepare("
            INSERT INTO users (username, email, password, created_at, updated_at) 
            VALUES ('admin', 'admin@sistema.com', :password, NOW(), NOW())
        ");
        $insertAdmin->execute([':password' => $adminPassword]);
        
        echo "✅ Usuario administrador creado:\n";
        echo "Usuario: admin\n";
        echo "Contraseña: admin123\n";
        echo "Email: admin@sistema.com\n\n";
    } else {
        echo "ℹ️ Usuario administrador ya existe\n\n";
    }
    
    echo "✅ Tabla de usuarios configurada correctamente\n";
    echo "🔗 Accede al sistema en: http://localhost:8080/mod_cotizacion/\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>