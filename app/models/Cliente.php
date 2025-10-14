<?php
/**
 * Modelo Cliente para el Sistema de Cotizaciones
 * Maneja operaciones CRUD para la tabla clientes
 */

require_once 'Model.php';

class Cliente extends Model {
    protected $table = 'clientes';
    
    /**
     * Buscar clientes por nombre, correo, documento o empresa
     */
    public function searchClientes($term) {
        $sql = "SELECT id, nombre, documento, correo FROM {$this->table} 
                WHERE nombre LIKE :term 
                ORDER BY nombre ASC LIMIT 10";
        $stmt = $this->query($sql, [':term' => "%$term%"]);
        return $stmt->fetchAll();
    }
    
    /**
     * Buscar cliente por documento
     */
    public function getByDocumento($documento) {
        $sql = "SELECT * FROM {$this->table} WHERE documento = :documento";
        $stmt = $this->query($sql, [':documento' => $documento]);
        return $stmt->fetch();
    }
    
    /**
     * Crear cliente rápido con validación
     */
    public function createRapido($data) {
        $errors = $this->validateClienteRapido($data);
        if (!empty($errors)) {
            throw new Exception(implode(', ', $errors));
        }
        
        // Verificar si ya existe por documento
        if (!empty($data['documento']) && $this->getByDocumento($data['documento'])) {
            throw new Exception('Ya existe un cliente con este documento');
        }
        
        $result = $this->create($data);
        if ($result) {
            return $this->db->lastInsertId();
        }
        throw new Exception('Error al crear el cliente');
    }
    
    /**
     * Validación simplificada para creación rápida
     */
    public function validateClienteRapido($data) {
        $errors = [];
        
        if (empty($data['nombre'])) {
            $errors[] = "El nombre es obligatorio";
        }
        
        if (empty($data['documento'])) {
            $errors[] = "El documento es obligatorio";
        }
        
        if (!empty($data['correo']) && !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El correo electrónico debe ser válido";
        }
        
        return $errors;
    }
    
    /**
     * Obtener cliente por correo electrónico
     */
    public function getByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE correo = :email";
        $stmt = $this->query($sql, [':email' => $email]);
        return $stmt->fetch();
    }
    
    /**
     * Validar datos del cliente
     */
    public function validateClienteData($data) {
        $errors = [];
        
        if (empty($data['nombre'])) {
            $errors[] = "El nombre es obligatorio";
        }
        
        if (empty($data['correo']) || !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El correo electrónico es obligatorio y debe ser válido";
        }
        
        if (empty($data['telefono'])) {
            $errors[] = "El teléfono es obligatorio";
        }
        
        // Verificar si el correo ya existe (para nuevos clientes)
        if (!isset($data['id']) && $this->getByEmail($data['correo'])) {
            $errors[] = "Ya existe un cliente con este correo electrónico";
        }
        
        return $errors;
    }
    
    /**
     * Obtener historial de cotizaciones de un cliente
     */
    public function getHistorialCotizaciones($clienteId) {
        $sql = "SELECT c.*, 
                       COUNT(cd.id) as total_items,
                       DATE_FORMAT(c.fecha, '%d/%m/%Y') as fecha_formato
                FROM cotizaciones c 
                LEFT JOIN cotizacion_detalle cd ON c.id = cd.id_cotizacion 
                WHERE c.id_cliente = :cliente_id 
                GROUP BY c.id 
                ORDER BY c.fecha DESC";
        
        $stmt = $this->query($sql, [':cliente_id' => $clienteId]);
        return $stmt->fetchAll();
    }
}
?>