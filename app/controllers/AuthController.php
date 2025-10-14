<?php
/**
 * Controlador de Autenticación para el Sistema de Cotizaciones
 * Maneja login, logout y gestión de sesiones
 */

require_once 'Controller.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends Controller {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Mostrar formulario de login
     */
    public function login() {
        // Si ya está autenticado, redirigir al dashboard
        if ($this->isAuthenticated()) {
            $this->redirect('index.php');
        }
        
        if ($this->isPost()) {
            $this->processLogin();
            return;
        }
        
        $this->loadLoginView('auth/login');
    }
    
    /**
     * Procesar login
     */
    private function processLogin() {
        $username = $this->getPostData(['username'])['username'];
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        if (empty($username) || empty($password)) {
            $this->loadLoginView('auth/login', [
                'error' => 'Por favor, ingrese usuario y contraseña',
                'username' => $username
            ]);
            return;
        }
        
        $user = $this->userModel->authenticate($username, $password);
        
        if ($user) {
            $this->startUserSession($user, $remember);
            $this->redirect('index.php');
        } else {
            $this->loadLoginView('auth/login', [
                'error' => 'Usuario o contraseña incorrectos',
                'username' => $username
            ]);
        }
    }
    
    /**
     * Iniciar sesión de usuario
     */
    private function startUserSession($user, $remember = false) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nombre_completo'] = $user['username']; // Usar username como nombre por ahora
        $_SESSION['email'] = $user['email'];
        $_SESSION['rol'] = 'admin'; // Por defecto admin
        $_SESSION['authenticated'] = true;
        
        // Cookie para recordar sesión (opcional)
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/'); // 30 días
            // Aquí podrías guardar el token en la base de datos para mayor seguridad
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        // Limpiar sesión
        session_unset();
        session_destroy();
        
        // Limpiar cookies
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        // Iniciar nueva sesión para mensajes
        session_start();
        $this->setAlert('Sesión cerrada correctamente', 'success');
        
        $this->redirect('index.php?controller=auth&action=login');
    }
    
    /**
     * Verificar si el usuario está autenticado
     */
    public function isAuthenticated() {
        return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
    }
    
    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin() {
        return $this->isAuthenticated() && $_SESSION['rol'] === 'admin';
    }
    
    /**
     * Middleware para verificar autenticación
     */
    public function requireAuth() {
        if (!$this->isAuthenticated()) {
            $this->setAlert('Debe iniciar sesión para acceder', 'warning');
            $this->redirect('index.php?controller=auth&action=login');
        }
    }
    
    /**
     * Middleware para verificar permisos de administrador
     */
    public function requireAdmin() {
        $this->requireAuth();
        
        if (!$this->isAdmin()) {
            $this->setAlert('No tiene permisos para acceder a esta sección', 'error');
            $this->redirect('index.php');
        }
    }
    
    /**
     * Cargar vista de login (sin header/footer)
     */
    private function loadLoginView($view, $data = []) {
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
     * Cambiar contraseña
     */
    public function changePassword() {
        $this->requireAuth();
        
        if ($this->isPost()) {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Validaciones
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                $this->setAlert('Todos los campos son obligatorios', 'error');
            } elseif ($newPassword !== $confirmPassword) {
                $this->setAlert('Las contraseñas nuevas no coinciden', 'error');
            } elseif (strlen($newPassword) < 6) {
                $this->setAlert('La nueva contraseña debe tener al menos 6 caracteres', 'error');
            } else {
                // Verificar contraseña actual
                $user = $this->userModel->getById($_SESSION['user_id']);
                if (password_verify($currentPassword, $user['password'])) {
                    if ($this->userModel->changePassword($_SESSION['user_id'], $newPassword)) {
                        $this->setAlert('Contraseña cambiada exitosamente', 'success');
                        $this->redirect('index.php');
                    } else {
                        $this->setAlert('Error al cambiar la contraseña', 'error');
                    }
                } else {
                    $this->setAlert('La contraseña actual es incorrecta', 'error');
                }
            }
        }
        
        $this->loadView('auth/change_password');
    }
    
    /**
     * Perfil del usuario
     */
    public function profile() {
        $this->requireAuth();
        
        $user = $this->userModel->getById($_SESSION['user_id']);
        
        if ($this->isPost()) {
            $data = $this->getPostData(['nombre_completo', 'email']);
            
            if ($this->userModel->update($_SESSION['user_id'], $data)) {
                $_SESSION['nombre_completo'] = $data['nombre_completo'];
                $_SESSION['email'] = $data['email'];
                $this->setAlert('Perfil actualizado exitosamente', 'success');
                $this->redirect('index.php?controller=auth&action=profile');
            } else {
                $this->setAlert('Error al actualizar el perfil', 'error');
            }
        }
        
        $this->loadView('auth/profile', ['user' => $user]);
    }
}
?>