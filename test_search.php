<?php
// Script simple para probar la búsqueda de clientes
header('Content-Type: application/json');

// Verificar si es una petición AJAX
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

echo json_encode([
    'test' => 'Script de prueba funcionando',
    'is_ajax' => $isAjax,
    'method' => $_SERVER['REQUEST_METHOD'],
    'get_data' => $_GET,
    'timestamp' => date('Y-m-d H:i:s')
]);
?>