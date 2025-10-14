<?php
/**
 * Controlador principal del sistema (Dashboard)
 */

require_once 'Controller.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Articulo.php';
require_once __DIR__ . '/../models/Cotizacion.php';

class HomeController extends Controller {
    
    /**
     * Dashboard principal
     */
    public function index() {
        // Obtener estadísticas generales
        $clienteModel = new Cliente();
        $articuloModel = new Articulo();
        $cotizacionModel = new Cotizacion();
        
        $totalClientes = count($clienteModel->getAll());
        $totalArticulos = count($articuloModel->getAll());
        $estadisticasCotizaciones = $cotizacionModel->getEstadisticas(30);
        
        // Artículos más cotizados
        $articulosMasCotizados = $articuloModel->getArticulosMasCotizados(5);
        
        // Cotizaciones recientes
        $cotizacionesRecientes = $cotizacionModel->getCotizacionesConFiltros([]);
        $cotizacionesRecientes = array_slice($cotizacionesRecientes, 0, 5);
        
        $this->loadView('home/dashboard', [
            'totalClientes' => $totalClientes,
            'totalArticulos' => $totalArticulos,
            'estadisticasCotizaciones' => $estadisticasCotizaciones,
            'articulosMasCotizados' => $articulosMasCotizados,
            'cotizacionesRecientes' => $cotizacionesRecientes
        ]);
    }
}
?>