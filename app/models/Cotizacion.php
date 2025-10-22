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
        // Determinar precio de venta a guardar: prioridad al precio enviado en el item
        $precioVenta = isset($item['precio']) && $item['precio'] !== '' ? floatval($item['precio']) : null;
        if ($precioVenta === null) {
            // Fallback al campo del artículo si existiera, sino al costo
            if (isset($articulo['precio_venta']) && $articulo['precio_venta'] !== null) {
                $precioVenta = floatval($articulo['precio_venta']);
            } else {
                $precioVenta = floatval($articulo['precio_costo']);
            }
        }

        return $stmt->execute([
            ':id_cotizacion' => $cotizacionId,
            ':id_articulo' => $item['id_articulo'],
            ':cantidad' => $item['cantidad'],
            ':precio_costo' => $articulo['precio_costo'],
            ':precio_venta' => $precioVenta
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
            $precioCosto = floatval($articulo['precio_costo']);
            $precioUnitarioVenta = null;

            // Priorizar precio enviado en el item
            if (isset($item['precio']) && $item['precio'] !== '') {
                $precioUnitarioVenta = floatval($item['precio']);
            } elseif (isset($articulo['precio_venta']) && $articulo['precio_venta'] !== null) {
                $precioUnitarioVenta = floatval($articulo['precio_venta']);
            } else {
                $precioUnitarioVenta = $precioCosto;
            }

            $totalCosto += $precioCosto * $item['cantidad'];
            $totalVenta += $precioUnitarioVenta * $item['cantidad'];
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
    
    /**
     * Guardar versión actual en el historial antes de editar
     */
    private function guardarEnHistorial($cotizacionId, $usuarioId, $motivo = null) {
        try {
            // Obtener cotización actual
            $cotizacion = $this->getById($cotizacionId);
            if (!$cotizacion) {
                throw new Exception("Cotización no encontrada");
            }
            
            // Obtener detalles actuales
            $detalles = $this->getDetallesCotizacion($cotizacionId);
            
            // Guardar en historial
            $sql = "INSERT INTO cotizaciones_historial 
                    (id_cotizacion, version, id_cliente, fecha_version, 
                     total_costo, total_venta, utilidad, 
                     id_usuario_modifico, fecha_modificacion, motivo_modificacion)
                    VALUES (:id_cotizacion, :version, :id_cliente, :fecha_version,
                            :total_costo, :total_venta, :utilidad,
                            :id_usuario_modifico, NOW(), :motivo)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_cotizacion' => $cotizacionId,
                ':version' => $cotizacion['version'] ?? 1,
                ':id_cliente' => $cotizacion['id_cliente'],
                ':fecha_version' => $cotizacion['fecha'],
                ':total_costo' => $cotizacion['total_costo'],
                ':total_venta' => $cotizacion['total_venta'],
                ':utilidad' => $cotizacion['utilidad'],
                ':id_usuario_modifico' => $usuarioId,
                ':motivo' => $motivo
            ]);
            
            $historialId = $this->db->lastInsertId();
            
            // Guardar detalles en historial
            foreach ($detalles as $detalle) {
                $sqlDetalle = "INSERT INTO cotizaciones_historial_detalle 
                               (id_historial, id_articulo, cantidad, precio_costo, precio_venta)
                               VALUES (:id_historial, :id_articulo, :cantidad, :precio_costo, :precio_venta)";
                
                $stmtDetalle = $this->db->prepare($sqlDetalle);
                $stmtDetalle->execute([
                    ':id_historial' => $historialId,
                    ':id_articulo' => $detalle['id_articulo'],
                    ':cantidad' => $detalle['cantidad'],
                    ':precio_costo' => $detalle['precio_costo'],
                    ':precio_venta' => $detalle['precio_venta']
                ]);
            }
            
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Actualizar cotización existente
     */
    public function updateCotizacion($cotizacionId, $clienteId, $items, $usuarioId, $motivo = null) {
        try {
            $this->db->beginTransaction();
            
            // Guardar versión actual en historial
            $this->guardarEnHistorial($cotizacionId, $usuarioId, $motivo);
            
            // Calcular nuevos totales
            $totales = $this->calculateTotales($items);
            
            // Obtener versión actual
            $cotizacion = $this->getById($cotizacionId);
            $nuevaVersion = ($cotizacion['version'] ?? 1) + 1;
            
            // Actualizar cotización
            $sql = "UPDATE {$this->table} 
                    SET id_cliente = :id_cliente,
                        total_costo = :total_costo,
                        total_venta = :total_venta,
                        utilidad = :utilidad,
                        version = :version,
                        id_usuario_modifico = :id_usuario_modifico,
                        fecha_modificacion = NOW()
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id' => $cotizacionId,
                ':id_cliente' => $clienteId,
                ':total_costo' => $totales['total_costo'],
                ':total_venta' => $totales['total_venta'],
                ':utilidad' => $totales['utilidad'],
                ':version' => $nuevaVersion,
                ':id_usuario_modifico' => $usuarioId
            ]);
            
            // Eliminar detalles antiguos
            $sqlDelete = "DELETE FROM cotizacion_detalle WHERE id_cotizacion = :id_cotizacion";
            $stmtDelete = $this->db->prepare($sqlDelete);
            $stmtDelete->execute([':id_cotizacion' => $cotizacionId]);
            
            // Agregar nuevos detalles
            foreach ($items as $item) {
                $this->addDetalleCotizacion($cotizacionId, $item);
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    /**
     * Obtener historial de versiones de una cotización
     */
    public function getHistorial($cotizacionId) {
        $sql = "SELECT h.*, u.username as usuario_modifico,
                       DATE_FORMAT(h.fecha_modificacion, '%d/%m/%Y %H:%i') as fecha_modificacion_formato,
                       DATE_FORMAT(h.fecha_version, '%d/%m/%Y %H:%i') as fecha_version_formato
                FROM cotizaciones_historial h
                LEFT JOIN users u ON h.id_usuario_modifico = u.id
                WHERE h.id_cotizacion = :id_cotizacion
                ORDER BY h.version DESC";
        
        $stmt = $this->query($sql, [':id_cotizacion' => $cotizacionId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener detalles de una versión histórica
     */
    public function getDetallesHistorial($historialId) {
        $sql = "SELECT hd.*, a.nombre, a.descripcion
                FROM cotizaciones_historial_detalle hd
                INNER JOIN articulos a ON hd.id_articulo = a.id
                WHERE hd.id_historial = :historial_id";
        
        $stmt = $this->query($sql, [':historial_id' => $historialId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener versión específica del historial
     */
    public function getVersionHistorial($cotizacionId, $version) {
        $sql = "SELECT h.*, u.username as usuario_modifico
                FROM cotizaciones_historial h
                LEFT JOIN users u ON h.id_usuario_modifico = u.id
                WHERE h.id_cotizacion = :id_cotizacion AND h.version = :version";
        
        $stmt = $this->query($sql, [
            ':id_cotizacion' => $cotizacionId,
            ':version' => $version
        ]);
        
        $historial = $stmt->fetch();
        
        if ($historial) {
            $historial['detalles'] = $this->getDetallesHistorial($historial['id']);
        }
        
        return $historial;
    }
}
?>