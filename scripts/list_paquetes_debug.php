<?php
require_once __DIR__ . '/../config/database.php';
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query('SELECT id,nombre,descripcion,precio_venta,imagen FROM paquetes');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Paquetes encontrados: " . count($rows) . PHP_EOL;
    echo "-- DEBUG DATA --" . PHP_EOL;
    var_export($rows);
    echo PHP_EOL . "-- END DEBUG --" . PHP_EOL;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
