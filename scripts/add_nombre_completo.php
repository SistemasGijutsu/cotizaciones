<?php
require_once __DIR__ . '/../config/database.php';
try {
    $db = Database::getInstance()->getConnection();
    // Agregar columna nombre_completo si no existe
    $check = $db->query("SHOW COLUMNS FROM users LIKE 'nombre_completo'")->fetch();
    if ($check) {
        echo "La columna nombre_completo ya existe." . PHP_EOL;
        exit;
    }

    $sql = "ALTER TABLE users ADD COLUMN nombre_completo VARCHAR(150) NULL AFTER username";
    $db->exec($sql);
    echo "Columna nombre_completo agregada correctamente." . PHP_EOL;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
?>