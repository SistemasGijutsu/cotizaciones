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
            $articulos = $this->articuloModel->getAll(); // lista de artículos
            // Obtener paquetes ya con los cálculos de precio/costo para evitar campos indefinidos en la vista
            $paquetes = $this->paqueteModel->getPaquetesWithPrices();
            
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
        $clienteId = $this->getPostData(['cliente_id'])['cliente_id'];
        
        // Obtener items de la cotización
        $items = [];
        if (isset($_POST['items']) && is_array($_POST['items'])) {
            foreach ($_POST['items'] as $item) {
                // Verificar que tenga los campos necesarios
                if (isset($item['id']) && isset($item['tipo']) && isset($item['cantidad']) && $item['cantidad'] > 0) {
                    // Procesar artículos y paquetes
                    if ($item['tipo'] === 'articulo') {
                        $items[] = [
                            'id_articulo' => $item['id'],
                            'cantidad' => intval($item['cantidad']),
                            'precio' => floatval($item['precio'] ?? 0),
                            'utilidad' => floatval($item['utilidad'] ?? 0)
                        ];
                    } elseif ($item['tipo'] === 'paquete') {
                        // Expandir paquete a sus artículos
                        $paqueteId = intval($item['id']);
                        $cantidadPaquete = intval($item['cantidad']);
                        // El precio enviado desde la UI se encuentra en items[][precio]
                        $precioPaquete = floatval($item['precio'] ?? 0);

                        $articulosPaquete = $this->paqueteModel->getArticulosPaquete($paqueteId);
                        if (!empty($articulosPaquete)) {
                            // Calcular costo total del paquete para distribuir el precio proporcionalmente
                            $totalCosto = 0;
                            foreach ($articulosPaquete as $ap) {
                                $totalCosto += floatval($ap['precio_costo']) * intval($ap['cantidad']);
                            }

                            // Si no hay precioPaquete, usar suma de costos como precio (fallback)
                            if ($precioPaquete <= 0) {
                                $precioPaquete = $totalCosto;
                            }

                            // Distribuir precio entre los artículos según su aporte al costo
                            foreach ($articulosPaquete as $ap) {
                                $artCostoTotal = floatval($ap['precio_costo']) * intval($ap['cantidad']);
                                $proporcion = $totalCosto > 0 ? ($artCostoTotal / $totalCosto) : (1 / count($articulosPaquete));
                                $precioUnitarioArticulo = ($precioPaquete * $proporcion) / intval($ap['cantidad']);

                                $items[] = [
                                    'id_articulo' => $ap['id_articulo'],
                                    'cantidad' => intval($ap['cantidad']) * $cantidadPaquete,
                                    'precio' => floatval($precioUnitarioArticulo),
                                    'utilidad' => 0
                                ];
                            }
                        }
                    }
                    // TODO: Implementar soporte para paquetes
                }
            }
        }
        
        // Validar que haya cliente y items
        if (empty($clienteId)) {
            $this->setAlert('Debe seleccionar un cliente', 'error');
            $this->redirect('index.php?controller=cotizacion&action=create');
            return;
        }
        
        if (empty($items)) {
            $this->setAlert('Debe agregar al menos un artículo a la cotización', 'error');
            $this->redirect('index.php?controller=cotizacion&action=create');
            return;
        }
        
        try {
            $cotizacionId = $this->cotizacionModel->createCotizacionCompleta($clienteId, $items);
            $this->setAlert('Cotización creada exitosamente', 'success');
            $this->redirect('index.php?controller=cotizacion&action=show&id=' . $cotizacionId);
        } catch (Exception $e) {
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
     * Mostrar formulario para editar cotización
     */
    public function edit() {
        $id = $this->getGetData('id');
        
        if ($this->isPost()) {
            $this->update();
            return;
        }
        
        try {
            $cotizacion = $this->cotizacionModel->getCotizacionCompleta($id);
            
            if (!$cotizacion) {
                $this->setAlert('Cotización no encontrada', 'error');
                $this->redirect('index.php?controller=cotizacion&action=index');
                return;
            }
            
            $clientes = $this->clienteModel->getAll();
            $articulos = $this->articuloModel->getAll();
            $paquetes = $this->paqueteModel->getPaquetesWithPrices();
            
            $this->loadView('cotizaciones/edit', [
                'cotizacion' => $cotizacion,
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
     * Actualizar cotización existente
     */
    public function update() {
        $id = $this->getPostData(['id'])['id'];
        $clienteId = $this->getPostData(['cliente_id'])['cliente_id'];
        $motivo = $this->getPostData(['motivo_modificacion'])['motivo_modificacion'] ?? null;
        
        // Obtener items de la cotización
        $items = [];
        if (isset($_POST['items']) && is_array($_POST['items'])) {
            foreach ($_POST['items'] as $item) {
                if (isset($item['id']) && isset($item['tipo']) && isset($item['cantidad']) && $item['cantidad'] > 0) {
                    if ($item['tipo'] === 'articulo') {
                        $items[] = [
                            'id_articulo' => $item['id'],
                            'cantidad' => intval($item['cantidad']),
                            'precio' => floatval($item['precio'] ?? 0),
                            'utilidad' => floatval($item['utilidad'] ?? 0)
                        ];
                    } elseif ($item['tipo'] === 'paquete') {
                        // Expandir paquete a sus artículos (igual que en store)
                        $paqueteId = intval($item['id']);
                        $cantidadPaquete = intval($item['cantidad']);
                        $precioPaquete = floatval($item['precio'] ?? 0);

                        $articulosPaquete = $this->paqueteModel->getArticulosPaquete($paqueteId);
                        if (!empty($articulosPaquete)) {
                            $totalCosto = 0;
                            foreach ($articulosPaquete as $ap) {
                                $totalCosto += floatval($ap['precio_costo']) * intval($ap['cantidad']);
                            }

                            if ($precioPaquete <= 0) {
                                $precioPaquete = $totalCosto;
                            }

                            foreach ($articulosPaquete as $ap) {
                                $artCostoTotal = floatval($ap['precio_costo']) * intval($ap['cantidad']);
                                $proporcion = $totalCosto > 0 ? ($artCostoTotal / $totalCosto) : (1 / count($articulosPaquete));
                                $precioUnitarioArticulo = ($precioPaquete * $proporcion) / intval($ap['cantidad']);

                                $items[] = [
                                    'id_articulo' => $ap['id_articulo'],
                                    'cantidad' => intval($ap['cantidad']) * $cantidadPaquete,
                                    'precio' => floatval($precioUnitarioArticulo),
                                    'utilidad' => 0
                                ];
                            }
                        }
                    }
                }
            }
        }
        
        // Validar
        if (empty($clienteId)) {
            $this->setAlert('Debe seleccionar un cliente', 'error');
            $this->redirect('index.php?controller=cotizacion&action=edit&id=' . $id);
            return;
        }
        
        if (empty($items)) {
            $this->setAlert('Debe agregar al menos un artículo a la cotización', 'error');
            $this->redirect('index.php?controller=cotizacion&action=edit&id=' . $id);
            return;
        }
        
        try {
            // Obtener usuario actual de la sesión
            $usuarioId = $_SESSION['user_id'] ?? null;
            
            $this->cotizacionModel->updateCotizacion($id, $clienteId, $items, $usuarioId, $motivo);
            $this->setAlert('Cotización actualizada exitosamente', 'success');
            $this->redirect('index.php?controller=cotizacion&action=show&id=' . $id);
        } catch (Exception $e) {
            $this->setAlert('Error al actualizar la cotización: ' . $e->getMessage(), 'error');
            $this->redirect('index.php?controller=cotizacion&action=edit&id=' . $id);
        }
    }
    
    /**
     * Ver historial de versiones de una cotización
     */
    public function historial() {
        $id = $this->getGetData('id');
        
        try {
            $cotizacion = $this->cotizacionModel->getById($id);
            
            if (!$cotizacion) {
                $this->setAlert('Cotización no encontrada', 'error');
                $this->redirect('index.php?controller=cotizacion&action=index');
                return;
            }
            
            $historial = $this->cotizacionModel->getHistorial($id);
            
            $this->loadView('cotizaciones/historial', [
                'cotizacion' => $cotizacion,
                'historial' => $historial
            ]);
        } catch (Exception $e) {
            $this->setAlert('Error al cargar el historial: ' . $e->getMessage(), 'error');
            $this->redirect('index.php?controller=cotizacion&action=show&id=' . $id);
        }
    }
    
    /**
     * Ver detalles de una versión específica del historial
     */
    public function verVersion() {
        $id = $this->getGetData('id');
        $version = $this->getGetData('version');
        
        try {
            $cotizacion = $this->cotizacionModel->getById($id);
            $versionHistorial = $this->cotizacionModel->getVersionHistorial($id, $version);
            
            if (!$versionHistorial) {
                $this->setAlert('Versión no encontrada', 'error');
                $this->redirect('index.php?controller=cotizacion&action=historial&id=' . $id);
                return;
            }
            
            // Obtener cliente de la versión
            $cliente = $this->clienteModel->getById($versionHistorial['id_cliente']);
            $versionHistorial['cliente'] = $cliente;
            
            // Calcular utilidad porcentual
            $versionHistorial['utilidad_porcentaje'] = Helper::calculateProfitPercentage(
                $versionHistorial['total_costo'], 
                $versionHistorial['total_venta']
            );
            
            $this->loadView('cotizaciones/ver_version', [
                'cotizacion' => $cotizacion,
                'version' => $versionHistorial
            ]);
        } catch (Exception $e) {
            $this->setAlert('Error al cargar la versión: ' . $e->getMessage(), 'error');
            $this->redirect('index.php?controller=cotizacion&action=historial&id=' . $id);
        }
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
            
            // Generar cotización (retorna info completa)
            $resultado = $pdfGenerator->generarCotizacionInfo($id);
            
            if ($resultado && $resultado['success']) {
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
                // Enviar email (el PDF se genera automáticamente dentro del EmailSender)
                require_once __DIR__ . '/../helpers/EmailSender.php';
                $emailSender = new EmailSender();
                
                $enviado = $emailSender->enviarCotizacion([
                    'destinatario' => $email,
                    'asunto' => $asunto,
                    'mensaje' => $mensaje,
                    'cotizacion' => $cotizacion,
                    'cliente' => $cliente
                ]);
                
                if ($enviado) {
                    $this->setAlert('Cotización enviada por email correctamente', 'success');
                } else {
                    // En desarrollo local, la función mail() no funciona sin configurar SMTP
                    $this->setAlert('NOTA: En desarrollo local, el email no se envía realmente. Para enviar emails reales, configure SMTP en php.ini o use una librería como PHPMailer.', 'warning');
                }
                
            } catch (Exception $e) {
                $this->setAlert('Error al enviar email: ' . $e->getMessage(), 'error');
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