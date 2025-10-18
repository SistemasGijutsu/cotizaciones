<?php
/**
 * Controlador Reporte para el Sistema de Cotizaciones
 * Maneja la visualización de reportes y estadísticas
 */

require_once 'Controller.php';
require_once __DIR__ . '/../models/Reporte.php';

class ReporteController extends Controller {
    private $reporteModel;
    
    public function __construct() {
        $this->reporteModel = new Reporte();
    }
    
    /**
     * Dashboard principal de reportes
     */
    public function index() {
        // Obtener filtros de fecha
        $tipoFiltro = $this->getGetData('tipo') ?? 'mes';
        $fechaInicio = $this->getGetData('fecha_inicio');
        $fechaFin = $this->getGetData('fecha_fin');
        
        // Establecer fechas por defecto según el tipo
        if (!$fechaInicio || !$fechaFin) {
            switch ($tipoFiltro) {
                case 'dia':
                    $fechaInicio = $fechaFin = date('Y-m-d');
                    break;
                case 'mes':
                    $fechaInicio = date('Y-m-01');
                    $fechaFin = date('Y-m-t');
                    break;
                case 'año':
                    $fechaInicio = date('Y-01-01');
                    $fechaFin = date('Y-12-31');
                    break;
                default:
                    $fechaInicio = date('Y-m-01');
                    $fechaFin = date('Y-m-t');
            }
        }
        
        // Obtener estadísticas generales
        $estadisticasGenerales = $this->reporteModel->getEstadisticasGenerales($fechaInicio, $fechaFin);
        
        // Obtener resumen de clientes
        $resumenClientes = $this->reporteModel->getResumenClientes($fechaInicio, $fechaFin);
        
        // Obtener cotizaciones por período
        $cotizacionesPorPeriodo = $this->reporteModel->getCotizacionesPorPeriodo($fechaInicio, $fechaFin, $tipoFiltro);
        
        // Obtener top clientes
        $topClientes = $this->reporteModel->getTopClientes($fechaInicio, $fechaFin, 10);
        
        // Obtener artículos más cotizados
        $articulosMasCotizados = $this->reporteModel->getArticulosMasCotizados($fechaInicio, $fechaFin, 10);
        
        // Obtener clientes nuevos
        $clientesNuevos = $this->reporteModel->getClientesNuevos($fechaInicio, $fechaFin);
        
        // Obtener clientes recurrentes
        $clientesRecurrentes = $this->reporteModel->getClientesRecurrentes($fechaInicio, $fechaFin);
        
        // Datos para gráficos
        $datosGrafico = $this->reporteModel->getDatosGraficoVentas($fechaInicio, $fechaFin, $tipoFiltro);
        
        $this->loadView('reportes/index', [
            'tipoFiltro' => $tipoFiltro,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'estadisticasGenerales' => $estadisticasGenerales,
            'resumenClientes' => $resumenClientes,
            'cotizacionesPorPeriodo' => $cotizacionesPorPeriodo,
            'topClientes' => $topClientes,
            'articulosMasCotizados' => $articulosMasCotizados,
            'clientesNuevos' => $clientesNuevos,
            'clientesRecurrentes' => $clientesRecurrentes,
            'datosGrafico' => $datosGrafico
        ]);
    }
    
    /**
     * Ver estadísticas por día
     */
    public function porDia() {
        $fecha = $this->getGetData('fecha') ?? date('Y-m-d');
        
        $estadisticas = $this->reporteModel->getEstadisticasPorDia($fecha);
        
        // Obtener cotizaciones del día
        $fechaInicio = $fechaFin = $fecha;
        $cotizaciones = $this->reporteModel->getCotizacionesPorPeriodo($fechaInicio, $fechaFin, 'dia');
        
        $resumenClientes = $this->reporteModel->getResumenClientes($fechaInicio, $fechaFin);
        
        $this->loadView('reportes/por_dia', [
            'fecha' => $fecha,
            'estadisticas' => $estadisticas,
            'cotizaciones' => $cotizaciones,
            'resumenClientes' => $resumenClientes
        ]);
    }
    
    /**
     * Ver estadísticas por mes
     */
    public function porMes() {
        $año = $this->getGetData('año') ?? date('Y');
        $mes = $this->getGetData('mes') ?? date('m');
        
        $estadisticas = $this->reporteModel->getEstadisticasPorMes($año, $mes);
        
        // Obtener cotizaciones por día del mes
        $cotizacionesPorDia = $this->reporteModel->getCotizacionesPorDiaMes($año, $mes);
        
        $fechaInicio = "$año-$mes-01";
        $fechaFin = date('Y-m-t', strtotime($fechaInicio));
        
        $resumenClientes = $this->reporteModel->getResumenClientes($fechaInicio, $fechaFin);
        $topClientes = $this->reporteModel->getTopClientes($fechaInicio, $fechaFin, 5);
        
        $this->loadView('reportes/por_mes', [
            'año' => $año,
            'mes' => $mes,
            'estadisticas' => $estadisticas,
            'cotizacionesPorDia' => $cotizacionesPorDia,
            'resumenClientes' => $resumenClientes,
            'topClientes' => $topClientes
        ]);
    }
    
    /**
     * Ver estadísticas por año
     */
    public function porAño() {
        $año = $this->getGetData('año') ?? date('Y');
        
        $estadisticas = $this->reporteModel->getEstadisticasPorAño($año);
        
        // Obtener cotizaciones por mes del año
        $cotizacionesPorMes = $this->reporteModel->getCotizacionesPorMesAño($año);
        
        $fechaInicio = "$año-01-01";
        $fechaFin = "$año-12-31";
        
        $resumenClientes = $this->reporteModel->getResumenClientes($fechaInicio, $fechaFin);
        $topClientes = $this->reporteModel->getTopClientes($fechaInicio, $fechaFin, 10);
        $articulosMasCotizados = $this->reporteModel->getArticulosMasCotizados($fechaInicio, $fechaFin, 10);
        
        $this->loadView('reportes/por_año', [
            'año' => $año,
            'estadisticas' => $estadisticas,
            'cotizacionesPorMes' => $cotizacionesPorMes,
            'resumenClientes' => $resumenClientes,
            'topClientes' => $topClientes,
            'articulosMasCotizados' => $articulosMasCotizados
        ]);
    }
    
    /**
     * Exportar reporte a CSV
     */
    public function exportarCSV() {
        $tipoReporte = $this->getGetData('tipo') ?? 'general';
        $fechaInicio = $this->getGetData('fecha_inicio') ?? date('Y-m-01');
        $fechaFin = $this->getGetData('fecha_fin') ?? date('Y-m-t');
        
        // Preparar datos según el tipo de reporte
        $datos = [];
        $nombreArchivo = '';
        
        switch ($tipoReporte) {
            case 'general':
                $datos = $this->reporteModel->getCotizacionesPorPeriodo($fechaInicio, $fechaFin, 'dia');
                $nombreArchivo = 'reporte_general_' . $fechaInicio . '_' . $fechaFin . '.csv';
                break;
                
            case 'clientes_nuevos':
                $datos = $this->reporteModel->getClientesNuevos($fechaInicio, $fechaFin);
                $nombreArchivo = 'clientes_nuevos_' . $fechaInicio . '_' . $fechaFin . '.csv';
                break;
                
            case 'clientes_recurrentes':
                $datos = $this->reporteModel->getClientesRecurrentes($fechaInicio, $fechaFin);
                $nombreArchivo = 'clientes_recurrentes_' . $fechaInicio . '_' . $fechaFin . '.csv';
                break;
                
            case 'top_clientes':
                $datos = $this->reporteModel->getTopClientes($fechaInicio, $fechaFin, 50);
                $nombreArchivo = 'top_clientes_' . $fechaInicio . '_' . $fechaFin . '.csv';
                break;
                
            case 'articulos':
                $datos = $this->reporteModel->getArticulosMasCotizados($fechaInicio, $fechaFin, 50);
                $nombreArchivo = 'articulos_mas_cotizados_' . $fechaInicio . '_' . $fechaFin . '.csv';
                break;
        }
        
        // Generar CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
        
        $output = fopen('php://output', 'w');
        
        // Escribir BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Escribir encabezados
        if (!empty($datos)) {
            fputcsv($output, array_keys($datos[0]));
            
            // Escribir datos
            foreach ($datos as $fila) {
                fputcsv($output, $fila);
            }
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Comparar dos períodos
     */
    public function comparar() {
        if ($this->isPost()) {
            $periodo1_inicio = $this->getPostData(['periodo1_inicio'])['periodo1_inicio'];
            $periodo1_fin = $this->getPostData(['periodo1_fin'])['periodo1_fin'];
            $periodo2_inicio = $this->getPostData(['periodo2_inicio'])['periodo2_inicio'];
            $periodo2_fin = $this->getPostData(['periodo2_fin'])['periodo2_fin'];
            
            $comparacion = $this->reporteModel->compararPeriodos(
                $periodo1_inicio, $periodo1_fin,
                $periodo2_inicio, $periodo2_fin
            );
            
            $this->loadView('reportes/comparacion', [
                'periodo1_inicio' => $periodo1_inicio,
                'periodo1_fin' => $periodo1_fin,
                'periodo2_inicio' => $periodo2_inicio,
                'periodo2_fin' => $periodo2_fin,
                'comparacion' => $comparacion
            ]);
            return;
        }
        
        $this->loadView('reportes/comparar');
    }
}
?>
