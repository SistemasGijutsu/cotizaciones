<?php
/**
 * Controlador Cotizacion para el Sistema de Cotizaciones
 * Maneja todas las operaciones relacionadas con cotizaciones
 */

require_once 'Controller.php';
require_once __DIR__ . '/../models/Cotizacion.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Articulo.php';
require_once __DIR__ . '/../models/Paquete.php';

class CotizacionController extends Controller {
    private $cotizacionModel;
    private $clienteModel;
    private $articuloModel;
    private $paqueteModel;
    
    public function __construct() {
        $this->cotizacionModel = new Cotizacion();
        $this->clienteModel = new Cliente();
        $this->articuloModel = new Articulo();
        $this->paqueteModel = new Paquete();
    }
    
    /**
     * Mostrar lista de cotizaciones
     */
    public function index() {
        $filtros = [
            'cliente_id' => $this->getGetData('cliente_id'),
            'fecha_desde' => $this->getGetData('fecha_desde'),
            'fecha_hasta' => $this->getGetData('fecha_hasta')
        ];
        
        $cotizaciones = $this->cotizacionModel->getCotizacionesConFiltros($filtros);
        $clientes = $this->clienteModel->getAll();
        
        $this->loadView('cotizaciones/index', [
            'cotizaciones' => $cotizaciones,
            'clientes' => $clientes,
            'filtros' => $filtros
        ]);
    }
    
    /**
     * Mostrar formulario para crear cotización
     */
    public function create() {
        if ($this->isPost()) {
            $this->store();
            return;
        }
        
        try {
            $clientes = $this->clienteModel->getAll();
            $articulos = $this->articuloModel->getAll(); // Cambiar a getAll() para evitar problemas
            $paquetes = $this->paqueteModel->getAll();
            
            $this->loadView('cotizaciones/create', [
                'clientes' => $clientes,
                'articulos' => $articulos,
                'paquetes' => $paquetes,
                'clienteModel' => $this->clienteModel,
                'articuloModel' => $this->articuloModel,
                'paqueteModel' => $this->paqueteModel
            ]);
        } catch (Exception $e) {
            $this->setAlert('Error al cargar los datos: ' . $e->getMessage(), 'error');
            $this->redirect('index.php?controller=cotizacion&action=index');
        }
    }
    
    /**
     * Guardar nueva cotización
     */
    public function store() {
        // DEBUG: Log para verificar qué se está recibiendo
        error_log("=== DEBUG COTIZACIÓN ===");
        error_log("POST data: " . print_r($_POST, true));
        
        $clienteId = $this->getPostData(['cliente_id'])['cliente_id'];
        error_log("Cliente ID: " . $clienteId);
        
        // Obtener items de la cotización
        $items = [];
        if (isset($_POST['items']) && is_array($_POST['items'])) {
            error_log("Items recibidos: " . print_r($_POST['items'], true));
            
            foreach ($_POST['items'] as $item) {
                // Verificar que tenga los campos necesarios
                if (isset($item['id']) && isset($item['tipo']) && isset($item['cantidad']) && $item['cantidad'] > 0) {
                    // Por ahora solo procesamos artículos
                    if ($item['tipo'] === 'articulo') {
                        $items[] = [
                            'id_articulo' => $item['id'],
                            'cantidad' => intval($item['cantidad']),
                            'precio' => floatval($item['precio'] ?? 0),
                            'utilidad' => floatval($item['utilidad'] ?? 0)
                        ];
                    }
                    // TODO: Implementar soporte para paquetes
                }
            }
        }
        
        error_log("Items procesados: " . print_r($items, true));
        
        // Validar que haya cliente y items
        if (empty($clienteId)) {
            error_log("ERROR: No hay cliente seleccionado");
            $this->setAlert('Debe seleccionar un cliente', 'error');
            $this->redirect('index.php?controller=cotizacion&action=create');
            return;
        }
        
        if (empty($items)) {
            error_log("ERROR: No hay items");
            $this->setAlert('Debe agregar al menos un artículo a la cotización', 'error');
            $this->redirect('index.php?controller=cotizacion&action=create');
            return;
        }
        
        try {
            error_log("Intentando crear cotización...");
            $cotizacionId = $this->cotizacionModel->createCotizacionCompleta($clienteId, $items);
            error_log("Cotización creada con ID: " . $cotizacionId);
            $this->setAlert('Cotización creada exitosamente', 'success');
            $this->redirect('index.php?controller=cotizacion&action=show&id=' . $cotizacionId);
        } catch (Exception $e) {
            error_log("ERROR al crear cotización: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->setAlert('Error al crear la cotización: ' . $e->getMessage(), 'error');
            $this->redirect('index.php?controller=cotizacion&action=create');
        }
    }
    
    /**
     * Ver detalles de una cotización
     */
    public function show() {
        $id = $this->getGetData('id');
        $cotizacion = $this->cotizacionModel->getCotizacionCompleta($id);
        
        if (!$cotizacion) {
            $this->setAlert('Cotización no encontrada', 'error');
            $this->redirect('index.php?controller=cotizacion&action=index');
        }
        
        // Calcular utilidad porcentual
        $cotizacion['utilidad_porcentaje'] = Helper::calculateProfitPercentage(
            $cotizacion['total_costo'], 
            $cotizacion['total_venta']
        );
        
        $this->loadView('cotizaciones/show', [
            'cotizacion' => $cotizacion
        ]);
    }
    
    /**
     * Generar PDF de cotización
     */
    public function pdf() {
        $id = $this->getGetData('id');
        $tipo = $this->getGetData('tipo', 'venta'); // 'costo' o 'venta'
        
        $cotizacion = $this->cotizacionModel->getCotizacionCompleta($id);
        
        if (!$cotizacion) {
            $this->setAlert('Cotización no encontrada', 'error');
            $this->redirect('index.php?controller=cotizacion&action=index');
        }
        
        // Aquí se implementaría la generación de PDF
        // Por ahora, solo mostraremos la vista de impresión
        $this->loadPartialView('cotizaciones/pdf', [
            'cotizacion' => $cotizacion,
            'tipo' => $tipo
        ]);
    }
    
    /**
     * Enviar cotización por email
     */
    public function enviar() {
        $id = $this->getGetData('id');
        $cotizacion = $this->cotizacionModel->getCotizacionCompleta($id);
        
        if (!$cotizacion) {
            $this->setAlert('Cotización no encontrada', 'error');
            $this->redirect('index.php?controller=cotizacion&action=index');
        }
        
        // Aquí se implementaría el envío por email
        // Por ahora, solo simularemos el envío
        $this->setAlert('Cotización enviada por email a ' . $cotizacion['cliente']['correo'], 'success');
        $this->redirect('index.php?controller=cotizacion&action=show&id=' . $id);
    }
    
    /**
     * Eliminar cotización
     */
    public function delete() {
        $id = $this->getGetData('id');
        
        if ($this->cotizacionModel->delete($id)) {
            $this->setAlert('Cotización eliminada exitosamente', 'success');
        } else {
            $this->setAlert('Error al eliminar la cotización', 'error');
        }
        
        $this->redirect('index.php?controller=cotizacion&action=index');
    }
    
    /**
     * Dashboard con estadísticas
     */
    public function dashboard() {
        $estadisticas = $this->cotizacionModel->getEstadisticas(30);
        $cotizacionesRecientes = $this->cotizacionModel->getCotizacionesConFiltros([]);
        $cotizacionesRecientes = array_slice($cotizacionesRecientes, 0, 5); // Solo las 5 más recientes
        
        $this->loadView('cotizaciones/dashboard', [
            'estadisticas' => $estadisticas,
            'cotizacionesRecientes' => $cotizacionesRecientes
        ]);
    }
    
    /**
     * Obtener artículos de un paquete (AJAX)
     */
    public function getArticulosPaquete() {
        if (!$this->isAjax()) {
            $this->redirect('index.php');
        }
        
        $paqueteId = $this->getGetData('id');
        $articulos = $this->paqueteModel->getArticulosPaquete($paqueteId);
        
        $this->jsonResponse($articulos);
    }
    
    /**
     * Calcular totales de cotización (AJAX)
     */
    public function calcularTotales() {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->redirect('index.php');
        }
        
        $items = json_decode($_POST['items'], true);
        
        $totalCosto = 0;
        $totalVenta = 0;
        
        foreach ($items as $item) {
            $articulo = $this->articuloModel->getById($item['id_articulo']);
            if ($articulo) {
                $totalCosto += $articulo['precio_costo'] * $item['cantidad'];
                $totalVenta += $articulo['precio_venta'] * $item['cantidad'];
            }
        }
        
        $utilidad = $totalVenta - $totalCosto;
        $utilidadPorcentaje = $totalCosto > 0 ? ($utilidad / $totalCosto) * 100 : 0;
        
        $this->jsonResponse([
            'total_costo' => $totalCosto,
            'total_venta' => $totalVenta,
            'utilidad' => $utilidad,
            'utilidad_porcentaje' => $utilidadPorcentaje
        ]);
    }
    
    /**
     * Generar PDF de cotización
     */
    public function generarPDF() {
        $id = $this->getGetData('id');
        
        if (!$id) {
            Helper::showAlert('ID de cotización no especificado', 'error');
            $this->redirect('index.php?controller=cotizacion');
        }
        
        try {
            // Obtener datos de la cotización
            $cotizacion = $this->cotizacionModel->getById($id);
            if (!$cotizacion) {
                Helper::showAlert('Cotización no encontrada', 'error');
                $this->redirect('index.php?controller=cotizacion');
            }
            
            // Obtener cliente
            $cliente = $this->clienteModel->getById($cotizacion['id_cliente']);
            if (!$cliente) {
                Helper::showAlert('Cliente no encontrado', 'error');
                $this->redirect('index.php?controller=cotizacion');
            }
            
            // Obtener detalles de la cotización
            $detalles = $this->cotizacionModel->getDetallesCotizacion($id);
            
            // Cargar generador de PDF
            require_once __DIR__ . '/../helpers/PDFGenerator.php';
            $pdfGenerator = new PDFGenerator();
            
            // Generar PDF
            $resultado = $pdfGenerator->generarCotizacionPDF($cotizacion, $cliente, $detalles);
            
            if ($resultado['success']) {
                if ($resultado['type'] === 'html') {
                    // Redirigir a la página HTML para imprimir
                    $this->redirect($resultado['url']);
                } else {
                    // Descargar archivo PDF
                    $this->downloadFile($resultado['filepath'], $resultado['filename']);
                }
            } else {
                Helper::showAlert('Error al generar el PDF', 'error');
                $this->redirect('index.php?controller=cotizacion');
            }
            
        } catch (Exception $e) {
            Helper::showAlert('Error al generar PDF: ' . $e->getMessage(), 'error');
            $this->redirect('index.php?controller=cotizacion');
        }
    }
    
    /**
     * Enviar cotización por email
     */
    public function enviarEmail() {
        $id = $this->getGetData('id');
        
        if (!$id) {
            Helper::showAlert('ID de cotización no especificado', 'error');
            $this->redirect('index.php?controller=cotizacion');
        }
        
        if ($this->isPost()) {
            $postData = $this->getPostData(['email', 'asunto', 'mensaje']);
            $email = $postData['email'];
            $asunto = $postData['asunto'];
            $mensaje = $postData['mensaje'];
            
            try {
                // Obtener datos de la cotización
                $cotizacion = $this->cotizacionModel->getById($id);
                $cliente = $this->clienteModel->getById($cotizacion['id_cliente']);
                $detalles = $this->cotizacionModel->getDetallesCotizacion($id);
                
                // Generar PDF
                require_once __DIR__ . '/../helpers/PDFGenerator.php';
                $pdfGenerator = new PDFGenerator();
                $resultado = $pdfGenerator->generarCotizacionPDF($cotizacion, $cliente, $detalles);
                
                // Enviar email
                require_once __DIR__ . '/../helpers/EmailSender.php';
                $emailSender = new EmailSender();
                
                $enviado = $emailSender->enviarCotizacion([
                    'destinatario' => $email,
                    'asunto' => $asunto,
                    'mensaje' => $mensaje,
                    'cotizacion' => $cotizacion,
                    'cliente' => $cliente,
                    'archivo_adjunto' => $resultado['type'] === 'html' ? $resultado['filepath'] : null
                ]);
                
                if ($enviado) {
                    Helper::showAlert('Cotización enviada por email correctamente', 'success');
                } else {
                    Helper::showAlert('Error al enviar la cotización por email', 'error');
                }
                
            } catch (Exception $e) {
                Helper::showAlert('Error al enviar email: ' . $e->getMessage(), 'error');
            }
            
            $this->redirect('index.php?controller=cotizacion&action=show&id=' . $id);
        }
        
        // Mostrar formulario de envío
        $cotizacion = $this->cotizacionModel->getById($id);
        $cliente = $this->clienteModel->getById($cotizacion['id_cliente']);
        
        $this->loadView('cotizaciones/enviar_email', [
            'cotizacion' => $cotizacion,
            'cliente' => $cliente
        ]);
    }
    
    /**
     * Descargar archivo
     */
    private function downloadFile($filepath, $filename) {
        if (file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;
        }
    }
}
?>