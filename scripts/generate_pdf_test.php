<?php
require __DIR__ . '/../app/helpers/PDFGenerator.php';
$g = new PDFGenerator();
$path = $g->generarCotizacion(1);
echo "Generado: " . $path . PHP_EOL;
