<?php
/**
 * Modelo Cotizacion para el Sistema de Cotizaciones
 * Maneja operaciones CRUD para las tablas cotizaciones y cotizacion_detalle
 */

require_once 'Model.php';
require_once 'Cliente.php';
require_once 'Articulo.php';

class Cotizacion extends Model {
    protected $table = 'cotizaciones';
    
    private $clienteModel;
    private $articuloModel;
    
    public function __construct() {
        parent::__construct();
        $this->clienteModel = new Cliente();
        $this->articuloModel = new Articulo();
    }
    
    /**
     * Crear una cotización completa con detalles
     */
    public function createCotizacionCompleta($clienteId, $items, $codigo = null) {
        try {
            $this->db->beginTransaction();
            
            // Calcular totales
            $totales = $this->calculateTotales($items);
            
            // Crear la cotización principal
            $cotizacionData = [
                'id_cliente' => $clienteId,
                'fecha' => date('Y-m-d H:i:s'),
                'total_costo' => $totales['total_costo'],
                'total_venta' => $totales['total_venta'],
                'utilidad' => $totales['utilidad']
            ];
            
            $cotizacionId = $this->createCotizacion($cotizacionData);
            
            // Agregar detalles de la cotización
            foreach ($items as $item) {
                $this->addDetalleCotizacion($cotizacionId, $item);
            }
            
            $this->db->commit();
            return $cotizacionId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    /**
     * Crear solo la cotización principal
     */
    private function createCotizacion($data) {
        $sql = "INSERT INTO {$this->table} 
                (id_cliente, fecha, total_costo, total_venta, utilidad) 
                VALUES (:id_cliente, :fecha, :total_costo, :total_venta, :utilidad)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }
    
    /**
     * Agregar detalle a la cotización
     */
    private function addDetalleCotizacion($cotizacionId, $item) {
        $articulo = $this->articuloModel->getById($item['id_articulo']);
        
        $sql = "INSERT INTO cotizacion_detalle 
                (id_cotizacion, id_articulo, cantidad, precio_costo, precio_venta) 
                VALUES (:id_cotizacion, :id_articulo, :cantidad, :precio_costo, :precio_venta)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_cotizacion' => $cotizacionId,
            ':id_articulo' => $item['id_articulo'],
            ':cantidad' => $item['cantidad'],
            ':precio_costo' => $articulo['precio_costo'],
            ':precio_venta' => $articulo['precio_venta']
        ]);
    }
    
    /**
     * Calcular totales de una cotización
     */
    private function calculateTotales($items) {
        $totalCosto = 0;
        $totalVenta = 0;
        
        foreach ($items as $item) {
            $articulo = $this->articuloModel->getById($item['id_articulo']);
            $totalCosto += $articulo['precio_costo'] * $item['cantidad'];
            $totalVenta += $articulo['precio_venta'] * $item['cantidad'];
        }
        
        return [
            'total_costo' => $totalCosto,
            'total_venta' => $totalVenta,
            'utilidad' => $totalVenta - $totalCosto
        ];
    }
    
    /**
     * Obtener cotización completa con detalles
     */
    public function getCotizacionCompleta($id) {
        // Obtener cotización principal
        $cotizacion = $this->getById($id);
        if (!$cotizacion) return null;
        
        // Obtener cliente
        $cotizacion['cliente'] = $this->clienteModel->getById($cotizacion['id_cliente']);
        
        // Obtener detalles
        $cotizacion['detalles'] = $this->getDetallesCotizacion($id);
        
        return $cotizacion;
    }
    
    /**
     * Obtener detalles de una cotización
     */
    public function getDetallesCotizacion($cotizacionId) {
        $sql = "SELECT cd.*, a.nombre, a.descripcion, a.stock
                FROM cotizacion_detalle cd
                INNER JOIN articulos a ON cd.id_articulo = a.id
                WHERE cd.id_cotizacion = :cotizacion_id";
        
        $stmt = $this->query($sql, [':cotizacion_id' => $cotizacionId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Generar código único para cotización
     */
    private function generateCotizacionCode() {
        $year = date('Y');
        $month = date('m');
        
        // Obtener el siguiente número secuencial
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE YEAR(fecha) = :year AND MONTH(fecha) = :month";
        $stmt = $this->query($sql, [':year' => $year, ':month' => $month]);
        $result = $stmt->fetch();
        
        $numero = str_pad($result['total'] + 1, 4, '0', STR_PAD_LEFT);
        return "COT-{$year}{$month}-{$numero}";
    }
    
    /**
     * Obtener cotizaciones con filtros
     */
    public function getCotizacionesConFiltros($filtros = []) {
        $sql = "SELECT c.*, cl.nombre as cliente_nombre, cl.correo as cliente_correo,
                       DATE_FORMAT(c.fecha, '%d/%m/%Y %H:%i') as fecha_formato
                FROM {$this->table} c
                INNER JOIN clientes cl ON c.id_cliente = cl.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filtros['cliente_id'])) {
            $sql .= " AND c.id_cliente = :cliente_id";
            $params[':cliente_id'] = $filtros['cliente_id'];
        }
        
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(c.fecha) >= :fecha_desde";
            $params[':fecha_desde'] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(c.fecha) <= :fecha_hasta";
            $params[':fecha_hasta'] = $filtros['fecha_hasta'];
        }
        
        $sql .= " ORDER BY c.fecha DESC";
        
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener estadísticas de cotizaciones
     */
    public function getEstadisticas($periodo = '30') {
        $sql = "SELECT 
                    COUNT(*) as total_cotizaciones,
                    SUM(total_venta) as total_ventas,
                    SUM(utilidad) as total_utilidad,
                    AVG(utilidad) as utilidad_promedio
                FROM {$this->table} 
                WHERE fecha >= DATE_SUB(NOW(), INTERVAL :periodo DAY)";
        
        $stmt = $this->query($sql, [':periodo' => $periodo]);
        return $stmt->fetch();
    }
    
    /**
     * Validar datos de cotización
     */
    public function validateCotizacionData($clienteId, $items) {
        $errors = [];
        
        if (empty($clienteId)) {
            $errors[] = "Debe seleccionar un cliente";
        }
        
        if (empty($items)) {
            $errors[] = "Debe agregar al menos un artículo a la cotización";
        }
        
        // Validar stock disponible
        foreach ($items as $item) {
            if (!$this->articuloModel->checkStock($item['id_articulo'], $item['cantidad'])) {
                $articulo = $this->articuloModel->getById($item['id_articulo']);
                $errors[] = "Stock insuficiente para el artículo: " . $articulo['nombre'];
            }
        }
        
        return $errors;
    }
}
?>