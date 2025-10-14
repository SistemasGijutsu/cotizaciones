<?php
/**
 * Modelo User para el Sistema de Cotizaciones
 * Maneja autenticación y gestión de usuarios
 */

require_once 'Model.php';

class User extends Model {
    protected $table = 'users';
    
    /**
     * Autenticar usuario
     */
    public function authenticate($username, $password) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE username = :username OR email = :email";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':email' => $username
        ]);
        
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Actualizar último acceso
            $this->updateLastAccess($user['id']);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Actualizar último acceso
     */
    private function updateLastAccess($userId) {
        $sql = "UPDATE {$this->table} SET updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $userId]);
    }
    
    /**
     * Crear nuevo usuario
     */
    public function createUser($data) {
        // Encriptar contraseña
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        return $this->create($data);
    }
    
    /**
     * Verificar si el username ya existe
     */
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT id FROM {$this->table} WHERE username = :username";
        $params = [':username' => $username];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Verificar si el email ya existe
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT id FROM {$this->table} WHERE email = :email";
        $params = [':email' => $email];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Cambiar contraseña
     */
    public function changePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $sql = "UPDATE {$this->table} SET password = :password WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':password' => $hashedPassword,
            ':id' => $userId
        ]);
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Activar/desactivar usuario
     */
    public function toggleActive($userId) {
        $sql = "UPDATE {$this->table} SET updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $userId]);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Obtener usuarios activos
     */
    public function getActiveUsers() {
        $sql = "SELECT id, username, email, created_at, updated_at 
                FROM {$this->table} 
                ORDER BY username ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Validar datos de usuario
     */
    public function validateUserData($data, $isEdit = false, $userId = null) {
        $errors = [];
        
        // Validar username
        if (empty($data['username'])) {
            $errors[] = "El nombre de usuario es obligatorio";
        } elseif (strlen($data['username']) < 3) {
            $errors[] = "El nombre de usuario debe tener al menos 3 caracteres";
        } elseif ($this->usernameExists($data['username'], $userId)) {
            $errors[] = "El nombre de usuario ya está en uso";
        }
        
        // Validar email
        if (empty($data['email'])) {
            $errors[] = "El email es obligatorio";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El email no es válido";
        } elseif ($this->emailExists($data['email'], $userId)) {
            $errors[] = "El email ya está registrado";
        }
        
        // Validar contraseña (solo para nuevos usuarios o cambio de contraseña)
        if (!$isEdit && empty($data['password'])) {
            $errors[] = "La contraseña es obligatoria";
        } elseif (!empty($data['password']) && strlen($data['password']) < 6) {
            $errors[] = "La contraseña debe tener al menos 6 caracteres";
        }
        
        // Los campos nombre_completo y rol no existen en la tabla actual
        // Se pueden agregar en futuras versiones
        
        return $errors;
    }
}
?>