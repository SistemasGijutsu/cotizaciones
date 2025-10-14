<?php
/**
 * Generador de iconos placeholder para PWA
 * Este script genera iconos básicos usando GD
 */

// Configuración de iconos necesarios
$icons = [
    'android-icon-36x36.png' => 36,
    'android-icon-48x48.png' => 48,
    'android-icon-72x72.png' => 72,
    'android-icon-96x96.png' => 96,
    'android-icon-144x144.png' => 144,
    'android-icon-192x192.png' => 192,
    'apple-icon-57x57.png' => 57,
    'apple-icon-60x60.png' => 60,
    'apple-icon-72x72.png' => 72,
    'apple-icon-76x76.png' => 76,
    'apple-icon-114x114.png' => 114,
    'apple-icon-120x120.png' => 120,
    'apple-icon-144x144.png' => 144,
    'apple-icon-152x152.png' => 152,
    'apple-icon-180x180.png' => 180,
    'favicon-16x16.png' => 16,
    'favicon-32x32.png' => 32,
    'favicon-96x96.png' => 96,
    'ms-icon-144x144.png' => 144,
    'icon-512x512.png' => 512
];

// Función para generar icono
function generateIcon($filename, $size) {
    $image = imagecreatetruecolor($size, $size);
    
    // Colores
    $bg_color = imagecolorallocate($image, 0, 123, 255); // #007bff
    $text_color = imagecolorallocate($image, 255, 255, 255);
    $border_color = imagecolorallocate($image, 0, 86, 179); // Más oscuro
    
    // Fondo
    imagefill($image, 0, 0, $bg_color);
    
    // Borde redondeado simulado
    $border_width = max(1, $size / 50);
    for ($i = 0; $i < $border_width; $i++) {
        imagerectangle($image, $i, $i, $size - 1 - $i, $size - 1 - $i, $border_color);
    }
    
    // Texto/Símbolo
    if ($size >= 32) {
        $font_size = max(8, $size / 8);
        $symbol = '₵'; // Símbolo de cotización
        
        // Calcular posición del texto
        $text_box = imagettfbbox($font_size, 0, __DIR__ . '/../../public/fonts/arial.ttf', $symbol);
        if (!$text_box) {
            // Si no hay fuente TTF, usar fuente built-in
            $x = ($size - imagefontwidth(5) * strlen($symbol)) / 2;
            $y = ($size - imagefontheight(5)) / 2;
            imagestring($image, 5, $x, $y, $symbol, $text_color);
        } else {
            $x = ($size - $text_box[4]) / 2;
            $y = ($size - $text_box[5]) / 2 + $text_box[5];
            imagettftext($image, $font_size, 0, $x, $y, $text_color, __DIR__ . '/../../public/fonts/arial.ttf', $symbol);
        }
    } else {
        // Para iconos muy pequeños, solo un círculo
        $center = $size / 2;
        $radius = $size / 4;
        imagefilledellipse($image, $center, $center, $radius * 2, $radius * 2, $text_color);
    }
    
    // Guardar imagen
    $filepath = __DIR__ . '/../../public/images/icons/' . $filename;
    imagepng($image, $filepath);
    imagedestroy($image);
    
    echo "✅ Generado: $filename ($size x $size)\n";
}

// Generar todos los iconos
echo "🎨 Generando iconos para PWA...\n\n";

foreach ($icons as $filename => $size) {
    generateIcon($filename, $size);
}

// Generar pixel.gif para verificación de conectividad
$pixel = imagecreate(1, 1);
$transparent = imagecolorallocate($pixel, 0, 0, 0);
imagecolortransparent($pixel, $transparent);
imagegif($pixel, __DIR__ . '/../../public/images/pixel.gif');
imagedestroy($pixel);
echo "✅ Generado: pixel.gif (1x1)\n";

echo "\n🎉 ¡Todos los iconos han sido generados correctamente!\n";
echo "\n📝 Nota: Estos son iconos placeholder. Para producción, reemplázalos con iconos profesionales.\n";
?>