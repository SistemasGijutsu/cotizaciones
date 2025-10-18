<?php
// Prueba de actualización de perfil (directo al modelo)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../app/models/Model.php';
require_once __DIR__ . '/../app/models/User.php';

$userModel = new User();

// Asumimos que existe usuario id=1 en la BD de pruebas
$userId = 1;

$datos = [
    'nombre_completo' => 'Usuario Prueba Mod',
    'email' => 'prueba_mod@example.com'
];

try {
    $ok = $userModel->update($userId, $datos);
    echo "Update result: " . ($ok ? 'success' : 'no rows changed') . PHP_EOL;
    $user = $userModel->getById($userId);
    print_r($user);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

?>