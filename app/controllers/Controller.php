<?php
/**
 * Controlador base para el Sistema de Cotizaciones
 * Proporciona funcionalidades comunes para todos los controladores
 */

require_once __DIR__ . '/../helpers/Helper.php';

abstract class Controller {
    
    /**
     * Cargar una vista
     */
    protected function loadView($view, $data = []) {
        // Extraer variables para la vista
        if (!empty($data)) {
            extract($data);
        }
        
        // Cargar header
        include __DIR__ . '/../views/layouts/header.php';
        
        // Cargar vista específica
        $viewPath = __DIR__ . "/../views/{$view}.php";
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            die("Error: La vista {$view} no existe");
        }
        
        // Cargar footer
        include __DIR__ . '/../views/layouts/footer.php';
    }
    
    /**
     * Cargar una vista parcial (sin header/footer)
     */
    protected function loadPartialView($view, $data = []) {
        if (!empty($data)) {
            extract($data);
        }
        
        $viewPath = __DIR__ . "/../views/{$view}.php";
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            die("Error: La vista {$view} no existe");
        }
    }
    
    /**
     * Redireccionar
     */
    protected function redirect($url) {
        Helper::redirect($url);
    }
    
    /**
     * Mostrar alerta
     */
    protected function setAlert($message, $type = 'info') {
        Helper::showAlert($message, $type);
    }
    
    /**
     * Obtener datos POST de forma segura
     */
    protected function getPostData($fields) {
        $data = [];
        foreach ($fields as $field) {
            $data[$field] = isset($_POST[$field]) ? Helper::sanitizeInput($_POST[$field]) : '';
        }
        return $data;
    }
    
    /**
     * Obtener datos GET de forma segura
     */
    protected function getGetData($field, $default = '') {
        return isset($_GET[$field]) ? Helper::sanitizeInput($_GET[$field]) : $default;
    }
    
    /**
     * Verificar si es una petición POST
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Verificar si es una petición AJAX
     */
    protected function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Responder con JSON
     */
    protected function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>