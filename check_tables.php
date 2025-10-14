<?php
/**
 * Verificación simple de tablas en la base de datos
 */
require_once 'config/database.php';

try {
    $db = Database::getInstance();
    $connection = $db->getConnection();
    
    echo "=== TABLAS EN LA BASE DE DATOS 'cotizaciones' ===\n";
    
    $stmt = $connection->prepare("SHOW TABLES");
    $stmt->execute();
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "❌ NO HAY TABLAS EN LA BASE DE DATOS\n";
        echo "💡 NECESITAS CREAR LAS TABLAS DEL SISTEMA\n";
    } else {
        echo "✅ TABLAS ENCONTRADAS:\n";
        foreach ($tables as $table) {
            echo "   - $table\n";
        }
    }
    
    echo "\n=== VERIFICACIÓN DE TABLA USERS ===\n";
    
    $stmt = $connection->prepare("SELECT COUNT(*) as total FROM users");
    $stmt->execute();
    $result = $stmt->fetch();
    
    echo "✅ Tabla 'users' tiene {$result['total']} registros\n";
    
    if ($result['total'] > 0) {
        $stmt = $connection->prepare("SELECT username, email FROM users LIMIT 3");
        $stmt->execute();
        $users = $stmt->fetchAll();
        
        echo "📋 Usuarios en el sistema:\n";
        foreach ($users as $user) {
            echo "   - {$user['username']} ({$user['email']})\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
?>