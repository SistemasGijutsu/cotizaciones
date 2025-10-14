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
        $sql = "INSERT INTO {$this->table} (nombre, descripcion) VALUES (:nombre, :descripcion)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion']
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
        $sql = "SELECT pa.*, a.nombre, a.descripcion, a.precio_costo, a.precio_venta, a.stock
                FROM paquete_articulos pa
                INNER JOIN articulos a ON pa.id_articulo = a.id
                WHERE pa.id_paquete = :paquete_id";
        
        $stmt = $this->query($sql, [':paquete_id' => $paqueteId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Calcular precio total del paquete (costo y venta)
     */
    public function calcularPreciosPaquete($paqueteId) {
        $articulos = $this->getArticulosPaquete($paqueteId);
        
        $totalCosto = 0;
        $totalVenta = 0;
        
        foreach ($articulos as $articulo) {
            $totalCosto += $articulo['precio_costo'] * $articulo['cantidad'];
            $totalVenta += $articulo['precio_venta'] * $articulo['cantidad'];
        }
        
        return [
            'total_costo' => $totalCosto,
            'total_venta' => $totalVenta,
            'utilidad' => $totalVenta - $totalCosto,
            'utilidad_porcentaje' => $totalCosto > 0 ? (($totalVenta - $totalCosto) / $totalCosto) * 100 : 0
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
     * Obtener paquetes con información de precios
     */
    public function getPaquetesWithPrices() {
        $paquetes = $this->getAll();
        
        foreach ($paquetes as &$paquete) {
            $precios = $this->calcularPreciosPaquete($paquete['id']);
            $paquete = array_merge($paquete, $precios);
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