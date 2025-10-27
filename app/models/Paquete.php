<?php
/**
 * Modelo Paquete para el Sistema de Cotizaciones
 * Maneja operaciones CRUD para las tablas paquetes y paquete_articulos
 */

require_once 'Model.php';

class Paquete extends Model {
    protected $table = 'paquetes';
    
    /**
     * Crear un paquete con sus artículos
     */
    public function createPaqueteWithArticulos($paqueteData, $articulos) {
        try {
            $this->db->beginTransaction();
            
            // Crear el paquete
            $paqueteId = $this->createPaquete($paqueteData);
            
            // Agregar artículos al paquete
            foreach ($articulos as $articulo) {
                $this->addArticuloToPaquete($paqueteId, $articulo['id_articulo'], $articulo['cantidad']);
            }
            
            $this->db->commit();
            return $paqueteId;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    /**
     * Crear solo el paquete
     */
    private function createPaquete($data) {
        // precio_venta ahora es obligatorio para paquetes con categorías (premium, básico)
        $sql = "INSERT INTO {$this->table} (nombre, descripcion, precio_venta, imagen) VALUES (:nombre, :descripcion, :precio_venta, :imagen)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'],
            ':precio_venta' => isset($data['precio_venta']) ? floatval($data['precio_venta']) : 0.00,
            ':imagen' => $data['imagen'] ?? null
        ]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Agregar artículo a un paquete
     */
    public function addArticuloToPaquete($paqueteId, $articuloId, $cantidad) {
        $sql = "INSERT INTO paquete_articulos (id_paquete, id_articulo, cantidad) 
                VALUES (:id_paquete, :id_articulo, :cantidad)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_paquete' => $paqueteId,
            ':id_articulo' => $articuloId,
            ':cantidad' => $cantidad
        ]);
    }
    
    /**
     * Obtener artículos de un paquete
     */
    public function getArticulosPaquete($paqueteId) {
    // Los artículos ya no tienen campo precio_venta (precio de venta solo aplica a paquetes o a la cotización)
    $sql = "SELECT pa.*, a.nombre, a.descripcion, a.precio_costo, a.stock
        FROM paquete_articulos pa
        INNER JOIN articulos a ON pa.id_articulo = a.id
        WHERE pa.id_paquete = :paquete_id";
        
        $stmt = $this->query($sql, [':paquete_id' => $paqueteId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Calcular precio total del paquete basado en precio_venta del paquete
     */
    public function calcularPreciosPaquete($paqueteId) {
        $articulos = $this->getArticulosPaquete($paqueteId);
        $paquete = $this->getById($paqueteId);
        
        $totalCosto = 0;
        
        foreach ($articulos as $articulo) {
            $totalCosto += $articulo['precio_costo'] * $articulo['cantidad'];
        }
        
        // Usar precio_venta del paquete
        $precioVenta = isset($paquete['precio_venta']) ? floatval($paquete['precio_venta']) : 0;
        $utilidad = $precioVenta - $totalCosto;
        $utilidadPorcentaje = $totalCosto > 0 ? ($utilidad / $totalCosto) * 100 : 0;
        
        return [
            'total_costo' => $totalCosto,
            'precio_venta' => $precioVenta,
            'utilidad' => $utilidad,
            'utilidad_porcentaje' => $utilidadPorcentaje
        ];
    }
    
    /**
     * Verificar disponibilidad de stock para un paquete
     */
    public function checkStockPaquete($paqueteId) {
        $articulos = $this->getArticulosPaquete($paqueteId);
        
        foreach ($articulos as $articulo) {
            if ($articulo['stock'] < $articulo['cantidad']) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Obtener todos los paquetes (override del método base)
     */
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} ORDER BY nombre";
        $stmt = $this->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener paquetes con información de precios
     */
    public function getPaquetesWithPrices() {
        $paquetes = $this->getAll();
        
        foreach ($paquetes as &$paquete) {
            $precios = $this->calcularPreciosPaquete($paquete['id']);
            $articulos = $this->getArticulosPaquete($paquete['id']);
            
            // Agregar información de precios y totales
            $paquete['total_costo'] = $precios['total_costo'];
            $paquete['precio_costo_total'] = $precios['total_costo'];
            $paquete['precio_venta_total'] = $precios['precio_venta'];
            $paquete['utilidad'] = $precios['utilidad'];
            $paquete['utilidad_porcentaje'] = $precios['utilidad_porcentaje'];
            $paquete['total_articulos'] = count($articulos);
        }
        
        return $paquetes;
    }
    
    /**
     * Eliminar artículo de un paquete
     */
    public function removeArticuloFromPaquete($paqueteId, $articuloId) {
        $sql = "DELETE FROM paquete_articulos WHERE id_paquete = :paquete_id AND id_articulo = :articulo_id";
        $stmt = $this->query($sql, [
            ':paquete_id' => $paqueteId,
            ':articulo_id' => $articuloId
        ]);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Validar datos del paquete
     */
    public function validatePaqueteData($data, $articulos) {
        $errors = [];
        
        if (empty($data['nombre'])) {
            $errors[] = "El nombre del paquete es obligatorio";
        }
        
        // precio_venta ahora es obligatorio
        if (!isset($data['precio_venta']) || $data['precio_venta'] === '' || $data['precio_venta'] < 0) {
            $errors[] = "El precio de venta es obligatorio y no puede ser negativo";
        }
        
        if (empty($articulos)) {
            $errors[] = "Debe agregar al menos un artículo al paquete";
        }
        
        foreach ($articulos as $articulo) {
            if (!isset($articulo['cantidad']) || $articulo['cantidad'] <= 0) {
                $errors[] = "La cantidad debe ser mayor a 0 para todos los artículos";
                break;
            }
        }
        
        return $errors;
    }
}
?>