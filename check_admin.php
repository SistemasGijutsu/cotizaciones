<?php
/**
 * Verificación detallada del usuario admin
 */
require_once 'config/database.php';

try {
    $db = Database::getInstance();
    $connection = $db->getConnection();
    
    echo "=== VERIFICACIÓN DEL USUARIO ADMIN ===\n";
    
    $stmt = $connection->prepare("SELECT * FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "✅ Usuario 'admin' encontrado\n";
        echo "📋 Detalles del usuario:\n";
        echo "   - ID: {$admin['id']}\n";
        echo "   - Username: {$admin['username']}\n";
        echo "   - Email: {$admin['email']}\n";
        echo "   - Activo: " . ($admin['is_active'] ? 'SÍ' : 'NO') . "\n";
        echo "   - Creado: {$admin['created_at']}\n";
        
        // Verificar si la contraseña funciona
        $test_password = 'admin123';
        if (password_verify($test_password, $admin['password'])) {
            echo "✅ Contraseña 'admin123' es CORRECTA\n";
        } else {
            echo "❌ Contraseña 'admin123' es INCORRECTA\n";
            echo "🔧 Actualizando contraseña...\n";
            
            $new_hash = password_hash($test_password, PASSWORD_BCRYPT);
            $update_stmt = $connection->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
            $update_stmt->execute([$new_hash]);
            
            echo "✅ Contraseña actualizada correctamente\n";
        }
    } else {
        echo "❌ Usuario 'admin' NO ENCONTRADO\n";
        echo "🔧 Creando usuario admin...\n";
        
        $password_hash = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt = $connection->prepare("
            INSERT INTO users (username, email, password, is_active) 
            VALUES ('admin', 'admin@sistema.com', ?, 1)
        ");
        $stmt->execute([$password_hash]);
        
        echo "✅ Usuario 'admin' creado con contraseña 'admin123'\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
?>