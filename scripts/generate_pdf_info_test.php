<?php
require __DIR__ . '/../app/helpers/PDFGenerator.php';
$g = new PDFGenerator();
$info = $g->generarCotizacionInfo(1);
print_r($info);
