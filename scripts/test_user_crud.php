<?php
require_once __DIR__ . '/../app/models/User.php';

$userModel = new User();

// Crear usuario de prueba
$data = [
    'username' => 'testuser_' . rand(1000,9999),
    'nombre_completo' => 'Usuario Test',
    'email' => 'testuser' . rand(1000,9999) . '@example.com',
    'password' => 'secret123'
];

try {
    $created = $userModel->createUser($data);
    echo "Created: " . ($created ? 'yes' : 'no') . PHP_EOL;

    $users = $userModel->getActiveUsers();
    echo "Total users: " . count($users) . PHP_EOL;
    $last = end($users);
    print_r($last);

    // Eliminar el usuario creado (por id)
    if ($created && $last && $last['username'] === $data['username']) {
        $deleted = $userModel->delete($last['id']);
        echo "Deleted: " . ($deleted ? 'yes' : 'no') . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

?>