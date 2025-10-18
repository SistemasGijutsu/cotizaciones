<?php
// Script de prueba para diagnosticar errores en Reporte.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../app/models/Model.php';
require_once __DIR__ . '/../app/models/Reporte.php';

$reporte = new Reporte();

$fechaInicio = date('Y-m-01');
$fechaFin = date('Y-m-t');

$resultados = [];

try {
    $resultados['estadisticasGenerales'] = $reporte->getEstadisticasGenerales($fechaInicio, $fechaFin);
} catch (Exception $e) {
    $resultados['estadisticasGenerales_error'] = $e->getMessage();
}

try {
    $resultados['cotizacionesPorPeriodo'] = $reporte->getCotizacionesPorPeriodo($fechaInicio, $fechaFin, 'mes');
} catch (Exception $e) {
    $resultados['cotizacionesPorPeriodo_error'] = $e->getMessage();
}

try {
    $resultados['clientesNuevos'] = $reporte->getClientesNuevos($fechaInicio, $fechaFin);
} catch (Exception $e) {
    $resultados['clientesNuevos_error'] = $e->getMessage();
}

try {
    $resultados['topClientes'] = $reporte->getTopClientes($fechaInicio, $fechaFin, 5);
} catch (Exception $e) {
    $resultados['topClientes_error'] = $e->getMessage();
}

echo json_encode($resultados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>