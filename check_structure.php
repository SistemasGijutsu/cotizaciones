<?php
/**
 * Verificar estructura de la tabla users
 */
require_once 'config/database.php';

try {
    $db = Database::getInstance();
    $connection = $db->getConnection();
    
    echo "=== ESTRUCTURA DE LA TABLA USERS ===\n";
    
    $stmt = $connection->prepare("DESCRIBE users");
    $stmt->execute();
    $columns = $stmt->fetchAll();
    
    foreach ($columns as $column) {
        echo "- {$column['Field']}: {$column['Type']} " . 
             ($column['Null'] == 'YES' ? '(NULL)' : '(NOT NULL)') . 
             ($column['Key'] ? " [{$column['Key']}]" : '') . "\n";
    }
    
    echo "\n=== CONTENIDO ACTUAL DE LA TABLA ===\n";
    $stmt = $connection->prepare("SELECT * FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    foreach ($users as $user) {
        echo "Usuario: {$user['username']}\n";
        foreach ($user as $key => $value) {
            echo "  $key: $value\n";
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
?>