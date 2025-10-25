<?php
/**
 * Helpers y utilidades generales para el Sistema de Cotizaciones
 */

if (!class_exists('Helper')) {
    class Helper {
    
    /**
     * Formatear un número como moneda
     */
    public static function formatCurrency($amount) {
        // Aceptar null o valores no numéricos: normalizar a 0
        if ($amount === null || $amount === '' || !is_numeric($amount)) {
            $amount = 0;
        }
        return '$' . number_format((float)$amount, 2, '.', ',');
    }
    
    /**
     * Formatear fecha
     */
    public static function formatDate($date, $format = 'd/m/Y') {
        return date($format, strtotime($date));
    }
    
    /**
     * Validar email
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    /**
     * Generar código único para cotizaciones
     */
    public static function generateQuoteCode() {
        return 'COT-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Calcular utilidad porcentual
     */
    public static function calculateProfitPercentage($costoTotal, $ventaTotal) {
        if ($costoTotal == 0) return 0;
        return (($ventaTotal - $costoTotal) / $costoTotal) * 100;
    }
    
    /**
     * Sanitizar entrada de datos
     */
    public static function sanitizeInput($input) {
        return htmlspecialchars(strip_tags(trim($input)));
    }
    
    /**
     * Redireccionar
     */
    public static function redirect($url) {
        header("Location: $url");
        exit();
    }
    
    /**
     * Mostrar alerta
     */
    public static function showAlert($message, $type = 'info') {
        $_SESSION['alert'] = [
            'message' => $message,
            'type' => $type
        ];
    }
    
    /**
     * Obtener y limpiar alerta
     */
    public static function getAlert() {
        if (isset($_SESSION['alert'])) {
            $alert = $_SESSION['alert'];
            unset($_SESSION['alert']);
            return $alert;
        }
        return null;
    }
}

/**
 * Función para incluir archivos de forma segura
 */
if (!function_exists('secure_include')) {
    function secure_include($path) {
        $fullPath = __DIR__ . '/../' . $path;
        if (file_exists($fullPath)) {
            include $fullPath;
        } else {
            die("Error: El archivo $path no existe");
        }
    }
}

/**
 * Función para obtener la URL base
 */
if (!function_exists('getBaseUrl')) {
    function getBaseUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        
        // Si no tiene puerto en el host, agregar el puerto por defecto
        if (strpos($host, ':') === false) {
            $port = $_SERVER['SERVER_PORT'] ?? '80';
            if ($port !== '80' && $port !== '443') {
                $host .= ':' . $port;
            }
        }
        
        $path = dirname($_SERVER['SCRIPT_NAME']);
        return $protocol . $host . $path;
    }
    }
}
?>