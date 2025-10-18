<?php
/**
 * Modelo Reporte para el Sistema de Cotizaciones
 * Maneja estadísticas e informes del sistema
 */

require_once 'Model.php';

class Reporte extends Model {
    
    /**
     * Obtener estadísticas generales por período
     */
    public function getEstadisticasGenerales($fechaInicio = null, $fechaFin = null) {
        // Si no se especifican fechas, usar el mes actual
        if (!$fechaInicio) {
            $fechaInicio = date('Y-m-01');
        }
        if (!$fechaFin) {
            $fechaFin = date('Y-m-t');
        }
        
        $sql = "SELECT 
                    COUNT(DISTINCT c.id_cliente) as total_clientes,
                    COUNT(c.id) as total_cotizaciones,
                    SUM(c.total_venta) as total_ventas,
                    SUM(c.total_costo) as total_costos,
                    SUM(c.utilidad) as total_utilidad,
                    AVG(c.total_venta) as promedio_venta,
                    AVG(c.utilidad) as promedio_utilidad
                FROM cotizaciones c
                WHERE DATE(c.fecha) BETWEEN :fecha_inicio AND :fecha_fin";
        
        $stmt = $this->query($sql, [
            ':fecha_inicio' => $fechaInicio,
            ':fecha_fin' => $fechaFin
        ]);
        
        return $stmt->fetch();
    }
    
    /**
     * Obtener cantidad de cotizaciones creadas por período
     */
    public function getCotizacionesPorPeriodo($fechaInicio, $fechaFin, $agrupacion = 'dia') {
        $formatoFecha = match($agrupacion) {
            'dia' => '%Y-%m-%d',
            'mes' => '%Y-%m',
            'año' => '%Y',
            default => '%Y-%m-%d'
        };
        
        // No se puede usar placeholder para DATE_FORMAT, se construye directamente
        $sql = "SELECT 
                    DATE_FORMAT(fecha, '$formatoFecha') as periodo,
                    COUNT(*) as cantidad,
                    SUM(total_venta) as total_venta,
                    SUM(utilidad) as utilidad
                FROM cotizaciones
                WHERE DATE(fecha) BETWEEN :fecha_inicio AND :fecha_fin
                GROUP BY periodo
                ORDER BY periodo ASC";
        
        $stmt = $this->query($sql, [
            ':fecha_inicio' => $fechaInicio,
            ':fecha_fin' => $fechaFin
        ]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener clientes nuevos en el período
     */
    public function getClientesNuevos($fechaInicio, $fechaFin) {
        $sql = "SELECT 
                    cl.id,
                    cl.nombre,
                    cl.documento,
                    cl.correo,
                    cl.telefono,
                    MIN(c.fecha) as primera_cotizacion,
                    COUNT(c.id) as total_cotizaciones
                FROM clientes cl
                INNER JOIN cotizaciones c ON cl.id = c.id_cliente
                WHERE DATE(c.fecha) BETWEEN :fecha_inicio AND :fecha_fin
                GROUP BY cl.id
                HAVING MIN(DATE(c.fecha)) BETWEEN :fecha_inicio_having AND :fecha_fin_having
                ORDER BY primera_cotizacion DESC";

        // Algunos drivers no aceptan reutilizar el mismo named placeholder varias veces
        // por eso usamos nombres únicos para la cláusula HAVING y pasamos ambos valores.
        $stmt = $this->query($sql, [
            ':fecha_inicio' => $fechaInicio,
            ':fecha_fin' => $fechaFin,
            ':fecha_inicio_having' => $fechaInicio,
            ':fecha_fin_having' => $fechaFin
        ]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener clientes recurrentes en el período
     */
    public function getClientesRecurrentes($fechaInicio, $fechaFin) {
        $sql = "SELECT 
                    cl.id,
                    cl.nombre,
                    cl.documento,
                    cl.correo,
                    cl.telefono,
                    COUNT(c.id) as total_cotizaciones,
                    SUM(c.total_venta) as total_comprado,
                    MIN(c.fecha) as primera_cotizacion,
                    MAX(c.fecha) as ultima_cotizacion
                FROM clientes cl
                INNER JOIN cotizaciones c ON cl.id = c.id_cliente
                WHERE DATE(c.fecha) BETWEEN :fecha_inicio AND :fecha_fin
                GROUP BY cl.id
                HAVING COUNT(c.id) > 1
                ORDER BY total_cotizaciones DESC, total_comprado DESC";
        
        $stmt = $this->query($sql, [
            ':fecha_inicio' => $fechaInicio,
            ':fecha_fin' => $fechaFin
        ]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener top 10 clientes por ventas
     */
    public function getTopClientes($fechaInicio, $fechaFin, $limite = 10) {
        $sql = "SELECT 
                    cl.id,
                    cl.nombre,
                    cl.documento,
                    COUNT(c.id) as total_cotizaciones,
                    SUM(c.total_venta) as total_comprado,
                    SUM(c.utilidad) as utilidad_generada
                FROM clientes cl
                INNER JOIN cotizaciones c ON cl.id = c.id_cliente
                WHERE DATE(c.fecha) BETWEEN :fecha_inicio AND :fecha_fin
                GROUP BY cl.id
                ORDER BY total_comprado DESC
                LIMIT :limite";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':fecha_inicio', $fechaInicio);
        $stmt->bindValue(':fecha_fin', $fechaFin);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener artículos más cotizados
     */
    public function getArticulosMasCotizados($fechaInicio, $fechaFin, $limite = 10) {
        $sql = "SELECT 
                    a.id,
                    a.nombre,
                    a.descripcion,
                    COUNT(DISTINCT cd.id_cotizacion) as veces_cotizado,
                    SUM(cd.cantidad) as cantidad_total,
                    SUM(cd.cantidad * cd.precio_venta) as total_vendido
                FROM articulos a
                INNER JOIN cotizacion_detalle cd ON a.id = cd.id_articulo
                INNER JOIN cotizaciones c ON cd.id_cotizacion = c.id
                WHERE DATE(c.fecha) BETWEEN :fecha_inicio AND :fecha_fin
                GROUP BY a.id
                ORDER BY veces_cotizado DESC, cantidad_total DESC
                LIMIT :limite";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':fecha_inicio', $fechaInicio);
        $stmt->bindValue(':fecha_fin', $fechaFin);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener estadísticas por día
     */
    public function getEstadisticasPorDia($fecha) {
        $sql = "SELECT 
                    COUNT(DISTINCT id_cliente) as clientes_atendidos,
                    COUNT(id) as cotizaciones_creadas,
                    SUM(total_venta) as total_ventas,
                    SUM(utilidad) as total_utilidad,
                    AVG(total_venta) as promedio_venta
                FROM cotizaciones
                WHERE DATE(fecha) = :fecha";
        
        $stmt = $this->query($sql, [':fecha' => $fecha]);
        return $stmt->fetch();
    }
    
    /**
     * Obtener estadísticas por mes
     */
    public function getEstadisticasPorMes($año, $mes) {
        $sql = "SELECT 
                    COUNT(DISTINCT id_cliente) as clientes_atendidos,
                    COUNT(id) as cotizaciones_creadas,
                    SUM(total_venta) as total_ventas,
                    SUM(utilidad) as total_utilidad,
                    AVG(total_venta) as promedio_venta,
                    COUNT(DISTINCT DATE(fecha)) as dias_activos
                FROM cotizaciones
                WHERE YEAR(fecha) = :año AND MONTH(fecha) = :mes";
        
        $stmt = $this->query($sql, [
            ':año' => $año,
            ':mes' => $mes
        ]);
        
        return $stmt->fetch();
    }
    
    /**
     * Obtener estadísticas por año
     */
    public function getEstadisticasPorAño($año) {
        $sql = "SELECT 
                    COUNT(DISTINCT id_cliente) as clientes_atendidos,
                    COUNT(id) as cotizaciones_creadas,
                    SUM(total_venta) as total_ventas,
                    SUM(utilidad) as total_utilidad,
                    AVG(total_venta) as promedio_venta
                FROM cotizaciones
                WHERE YEAR(fecha) = :año";
        
        $stmt = $this->query($sql, [':año' => $año]);
        return $stmt->fetch();
    }
    
    /**
     * Obtener cotizaciones por día del mes
     */
    public function getCotizacionesPorDiaMes($año, $mes) {
        $sql = "SELECT 
                    DAY(fecha) as dia,
                    COUNT(*) as cantidad,
                    SUM(total_venta) as total
                FROM cotizaciones
                WHERE YEAR(fecha) = :año AND MONTH(fecha) = :mes
                GROUP BY DAY(fecha)
                ORDER BY dia ASC";
        
        $stmt = $this->query($sql, [
            ':año' => $año,
            ':mes' => $mes
        ]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener cotizaciones por mes del año
     */
    public function getCotizacionesPorMesAño($año) {
        $sql = "SELECT 
                    MONTH(fecha) as mes,
                    COUNT(*) as cantidad,
                    SUM(total_venta) as total,
                    COUNT(DISTINCT id_cliente) as clientes
                FROM cotizaciones
                WHERE YEAR(fecha) = :año
                GROUP BY MONTH(fecha)
                ORDER BY mes ASC";
        
        $stmt = $this->query($sql, [':año' => $año]);
        return $stmt->fetchAll();
    }
    
    /**
     * Comparar períodos
     */
    public function compararPeriodos($periodo1_inicio, $periodo1_fin, $periodo2_inicio, $periodo2_fin) {
        $sql = "SELECT 
                    'Período 1' as periodo,
                    COUNT(*) as cotizaciones,
                    COUNT(DISTINCT id_cliente) as clientes,
                    SUM(total_venta) as ventas,
                    SUM(utilidad) as utilidad
                FROM cotizaciones
                WHERE DATE(fecha) BETWEEN :p1_inicio AND :p1_fin
                UNION ALL
                SELECT 
                    'Período 2' as periodo,
                    COUNT(*) as cotizaciones,
                    COUNT(DISTINCT id_cliente) as clientes,
                    SUM(total_venta) as ventas,
                    SUM(utilidad) as utilidad
                FROM cotizaciones
                WHERE DATE(fecha) BETWEEN :p2_inicio AND :p2_fin";
        
        $stmt = $this->query($sql, [
            ':p1_inicio' => $periodo1_inicio,
            ':p1_fin' => $periodo1_fin,
            ':p2_inicio' => $periodo2_inicio,
            ':p2_fin' => $periodo2_fin
        ]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener resumen de clientes
     */
    public function getResumenClientes($fechaInicio, $fechaFin) {
        // Total de clientes registrados
        $sqlTotal = "SELECT COUNT(*) as total FROM clientes";
        $stmtTotal = $this->query($sqlTotal);
        $total = $stmtTotal->fetch()['total'];
        
        // Clientes con cotizaciones en el período
        $sqlActivos = "SELECT COUNT(DISTINCT id_cliente) as activos 
                       FROM cotizaciones 
                       WHERE DATE(fecha) BETWEEN :fecha_inicio AND :fecha_fin";
        $stmtActivos = $this->query($sqlActivos, [
            ':fecha_inicio' => $fechaInicio,
            ':fecha_fin' => $fechaFin
        ]);
        $activos = $stmtActivos->fetch()['activos'];
        
        // Clientes nuevos
        $nuevos = count($this->getClientesNuevos($fechaInicio, $fechaFin));
        
        // Clientes recurrentes
        $recurrentes = count($this->getClientesRecurrentes($fechaInicio, $fechaFin));
        
        return [
            'total' => $total,
            'activos' => $activos,
            'nuevos' => $nuevos,
            'recurrentes' => $recurrentes,
            'inactivos' => $total - $activos
        ];
    }
    
    /**
     * Obtener datos para gráfico de ventas
     */
    public function getDatosGraficoVentas($fechaInicio, $fechaFin, $tipo = 'dia') {
        $datos = $this->getCotizacionesPorPeriodo($fechaInicio, $fechaFin, $tipo);
        
        $labels = [];
        $valores = [];
        
        foreach ($datos as $dato) {
            $labels[] = $dato['periodo'];
            $valores[] = floatval($dato['total_venta']);
        }
        
        return [
            'labels' => $labels,
            'valores' => $valores
        ];
    }
}
?>
