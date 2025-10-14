<?php
/**
 * Sistema de Cotizaciones Empresariales
 * Punto de entrada principal de la aplicación
 * 
 * Este archivo maneja el enrutamiento y carga los controladores apropiados
 * según los parámetros de la URL
 */

// Iniciar sesión
session_start();

// Configurar reporte de errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir dependencias principales
require_once 'config/database.php';
require_once 'app/helpers/Helper.php';

/**
 * Autoloader simple para cargar clases automáticamente
 */
spl_autoload_register(function ($className) {
    $directories = [
        'app/controllers/',
        'app/models/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

/**
 * Router principal del sistema
 */
class Router {
    private $controller = 'home';
    private $action = 'index';
    private $params = [];
    
    public function __construct() {
        $this->parseUrl();
        $this->loadController();
    }
    
    /**
     * Parsear la URL para obtener controlador, acción y parámetros
     */
    private function parseUrl() {
        // Obtener controlador
        if (isset($_GET['controller']) && !empty($_GET['controller'])) {
            $this->controller = $this->sanitize($_GET['controller']);
        }
        
        // Obtener acción
        if (isset($_GET['action']) && !empty($_GET['action'])) {
            $this->action = $this->sanitize($_GET['action']);
        }
        
        // Obtener parámetros adicionales
        $this->params = $_GET;
        unset($this->params['controller']);
        unset($this->params['action']);
    }
    
    /**
     * Cargar y ejecutar el controlador apropiado
     */
    private function loadController() {
        // Crear nombre de la clase del controlador
        $controllerName = ucfirst($this->controller) . 'Controller';
        $controllerFile = "app/controllers/{$controllerName}.php";
        
        // Verificar si el archivo del controlador existe
        if (!file_exists($controllerFile)) {
            $this->show404("Controlador '{$controllerName}' no encontrado");
            return;
        }
        
        // Incluir el archivo del controlador
        require_once $controllerFile;
        
        // Verificar si la clase existe
        if (!class_exists($controllerName)) {
            $this->show404("Clase '{$controllerName}' no encontrada");
            return;
        }
        
        // Instanciar el controlador
        $controller = new $controllerName();
        
        // Verificar si el método existe
        if (!method_exists($controller, $this->action)) {
            $this->show404("Método '{$this->action}' no encontrado en '{$controllerName}'");
            return;
        }
        
        // Ejecutar la acción
        try {
            call_user_func([$controller, $this->action]);
        } catch (Exception $e) {
            $this->showError("Error al ejecutar la acción: " . $e->getMessage());
        }
    }
    
    /**
     * Sanitizar entrada de datos
     */
    private function sanitize($input) {
        return preg_replace('/[^a-zA-Z0-9_-]/', '', $input);
    }
    
    /**
     * Mostrar página de error 404
     */
    private function show404($message = "Página no encontrada") {
        http_response_code(404);
        $this->showErrorPage("Error 404", $message);
    }
    
    /**
     * Mostrar página de error general
     */
    private function showError($message) {
        http_response_code(500);
        $this->showErrorPage("Error del Sistema", $message);
    }
    
    /**
     * Mostrar página de error personalizada
     */
    private function showErrorPage($title, $message) {
        include 'app/helpers/Helper.php';
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $title; ?> - Sistema de Cotizaciones</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center p-5">
                                <i class="fas fa-exclamation-triangle fa-4x text-warning mb-4"></i>
                                <h1 class="h3 mb-3"><?php echo $title; ?></h1>
                                <p class="text-muted mb-4"><?php echo $message; ?></p>
                                <a href="index.php" class="btn btn-primary">
                                    <i class="fas fa-home me-2"></i>
                                    Ir al Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Función para verificar conexión a la base de datos
function checkDatabaseConnection() {
    try {
        $db = Database::getInstance();
        $connection = $db->getConnection();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Verificar conexión a la base de datos
if (!checkDatabaseConnection()) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error de Conexión - Sistema de Cotizaciones</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body text-center p-5">
                            <i class="fas fa-database fa-4x text-danger mb-4"></i>
                            <h1 class="h3 mb-3">Error de Conexión a la Base de Datos</h1>
                            <p class="text-muted mb-4">
                                No se pudo conectar a la base de datos. Verifique la configuración en el archivo 
                                <code>config/database.php</code> y asegúrese de que:
                            </p>
                            <div class="text-start">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>XAMPP esté ejecutándose</li>
                                    <li><i class="fas fa-check text-success me-2"></i>MySQL esté activo</li>
                                    <li><i class="fas fa-check text-success me-2"></i>La base de datos 'mod_cotizacion' exista</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Las tablas estén creadas correctamente</li>
                                </ul>
                            </div>
                            <div class="alert alert-info mt-4">
                                <h6><i class="fas fa-info-circle me-2"></i>Configuración actual:</h6>
                                <p class="mb-0 small">
                                    <strong>Host:</strong> <?php echo DB_HOST; ?><br>
                                    <strong>Base de datos:</strong> <?php echo DB_NAME; ?><br>
                                    <strong>Usuario:</strong> <?php echo DB_USER; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Verificar autenticación para rutas protegidas
$authController = new AuthController();

// Rutas públicas (no requieren autenticación)
$publicRoutes = ['auth'];
$currentController = isset($_GET['controller']) ? $_GET['controller'] : 'home';

// Si no es una ruta pública y no está autenticado, redirigir al login
if (!in_array($currentController, $publicRoutes) && !$authController->isAuthenticated()) {
    header('Location: index.php?controller=auth&action=login');
    exit;
}

// Si está en login y ya está autenticado, redirigir al dashboard
if ($currentController === 'auth' && isset($_GET['action']) && $_GET['action'] === 'login' && $authController->isAuthenticated()) {
    header('Location: index.php');
    exit;
}

// Inicializar el router
new Router();
?>