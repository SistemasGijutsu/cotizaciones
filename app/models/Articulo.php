<?php
/**
 * Modelo Articulo para el Sistema de Cotizaciones
 * Maneja operaciones CRUD para la tabla articulos
 */

require_once 'Model.php';

class Articulo extends Model {
    protected $table = 'articulos';
    
    /**
     * Buscar artículos por nombre o descripción
     */
    public function searchArticulos($term) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE nombre LIKE :term OR descripcion LIKE :term 
                ORDER BY nombre ASC";
        $stmt = $this->query($sql, [':term' => "%$term%"]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener artículos con stock disponible
     */
    public function getArticulosDisponibles() {
        $sql = "SELECT * FROM {$this->table} WHERE stock > 0 ORDER BY nombre ASC";
        $stmt = $this->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Validar datos del artículo
     */
    public function validateArticuloData($data) {
        $errors = [];
        
        if (empty($data['nombre'])) {
            $errors[] = "El nombre del artículo es obligatorio";
        }
        
        if (!isset($data['precio_costo']) || $data['precio_costo'] < 0) {
            $errors[] = "El precio de costo debe ser mayor o igual a 0";
        }
        
        if (!isset($data['stock']) || $data['stock'] < 0) {
            $errors[] = "El stock debe ser mayor o igual a 0";
        }
        
        return $errors;
    }
    
    /**
     * Actualizar stock del artículo
     */
    public function updateStock($id, $cantidad) {
        $sql = "UPDATE {$this->table} SET stock = stock - :cantidad WHERE id = :id";
        $stmt = $this->query($sql, [':cantidad' => $cantidad, ':id' => $id]);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Verificar disponibilidad de stock
     */
    public function checkStock($id, $cantidad) {
        $articulo = $this->getById($id);
        return $articulo && $articulo['stock'] >= $cantidad;
    }
    
    /**
     * Calcular costo total de un artículo por cantidad
     */
    public function calcularCostoTotal($id, $cantidad = 1) {
        $articulo = $this->getById($id);
        if (!$articulo) return 0;
        
        return $articulo['precio_costo'] * $cantidad;
    }

    /**
     * Calcular utilidad porcentual del artículo (compatibilidad)
     * Si no existe precio_venta o es 0, devuelve 0
     */
    public function calcularUtilidad($id) {
        $articulo = $this->getById($id);
        if (!$articulo) return 0;

        // Si no existe precio_venta (nuevo flujo), devolver 0
        if (!isset($articulo['precio_venta']) || floatval($articulo['precio_venta']) == 0) {
            return 0;
        }

        $costo = floatval($articulo['precio_costo']);
        $venta = floatval($articulo['precio_venta']);

        if ($costo == 0) return 0;
        return (($venta - $costo) / $costo) * 100;
    }
    
    /**
     * Obtener artículos más cotizados
     */
    public function getArticulosMasCotizados($limite = 10) {
        $sql = "SELECT a.*, COUNT(cd.id_articulo) as total_cotizaciones
                FROM {$this->table} a
                LEFT JOIN cotizacion_detalle cd ON a.id = cd.id_articulo
                GROUP BY a.id
                ORDER BY total_cotizaciones DESC
                LIMIT :limite";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>