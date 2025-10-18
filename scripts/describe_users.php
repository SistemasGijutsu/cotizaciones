<?php
require_once __DIR__ . '/../app/models/Model.php';
require_once __DIR__ . '/../app/models/User.php';

require_once __DIR__ . '/../config/database.php';
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query('SHOW COLUMNS FROM users');
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($cols, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>