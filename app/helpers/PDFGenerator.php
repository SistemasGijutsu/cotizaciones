<?php
/**
 * Generador de PDF para Cotizaciones
 * Utiliza HTML/CSS para generar contenido imprimible
 */

class PDFGenerator {
    
    private $empresa;
    private $templatePath;
    
    public function __construct() {
        $this->empresa = [
            'nombre' => 'Sistema de Cotizaciones Empresariales',
            'direccion' => 'Calle Principal #123',
            'ciudad' => 'Bogotá, Colombia',
            'telefono' => '+57 (1) 234-5678',
            'email' => 'info@cotizaciones.com',
            'nit' => '900.123.456-7',
            'web' => 'www.cotizaciones.com'
        ];
        
        $this->templatePath = __DIR__ . '/../views/pdf/';
    }
    
    /**
     * Generar cotización por ID (obtiene datos de la BD)
     */
    public function generarCotizacion($cotizacionId) {
        // Cargar modelos necesarios
        require_once __DIR__ . '/../models/Cotizacion.php';
        require_once __DIR__ . '/../models/Cliente.php';
        
        $cotizacionModel = new Cotizacion();
        $clienteModel = new Cliente();
        
        // Obtener datos
        $cotizacion = $cotizacionModel->getCotizacionCompleta($cotizacionId);
        if (!$cotizacion) {
            return false;
        }
        
        $cliente = $clienteModel->getById($cotizacion['id_cliente']);
        $detalles = $cotizacionModel->getDetallesCotizacion($cotizacionId);
        
        // Generar PDF
        return $this->generarCotizacionPDF($cotizacion, $cliente, $detalles);
    }
    
    /**
     * Generar PDF de cotización
     */
    public function generarCotizacionPDF($cotizacion, $cliente, $detalles) {
        $html = $this->generarHTMLCotizacion($cotizacion, $cliente, $detalles);
        
    // Nombre base del archivo (sin extensión)
    $filename = 'cotizacion_' . str_pad($cotizacion['id'], 6, '0', STR_PAD_LEFT) . '_' . date('Y-m-d');
        
        // Por ahora, generar HTML para imprimir
        // En el futuro se puede implementar TCPDF u otra librería
        return $this->generarHTMLImprimible($html, $filename);
    }
    
    /**
     * Generar HTML de cotización
     */
    private function generarHTMLCotizacion($cotizacion, $cliente, $detalles) {
        $subtotal = 0;
        // Buscar membrete en public/images/membrete.png y preparar data URI si existe
        $membreteFile = __DIR__ . '/../../public/images/membrete.png';
        $membreteData = null;
        if (file_exists($membreteFile)) {
            $membreteData = 'data:image/png;base64,' . base64_encode(file_get_contents($membreteFile));
        }
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Cotización #<?php echo str_pad($cotizacion['id'] ?? 0, 6, '0', STR_PAD_LEFT); ?></title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: 'Arial', sans-serif;
                    font-size: 12px;
                    line-height: 1.4;
                    color: #333;
                    background: white;
                }
                
                .container {
                    max-width: 800px;
                    margin: 0 auto;
                    padding: 0;
                    background: transparent;
                    position: relative;
                    min-height: 297mm; /* altura A4 */
                }

                /* Estilos para el membrete de página completa */
                .membrete-img {
                    position: fixed; /* fijo para que se vea en todas las páginas al imprimir */
                    top: 0;
                    left: 50%;
                    transform: translateX(-50%);
                    width: 100%;
                    max-width: 800px;
                    height: auto;
                    z-index: 0;
                    pointer-events: none;
                    opacity: 1; /* visible completamente */
                }
                
                /* Contenedor interno con padding para respetar el diseño del membrete */
                .content-wrapper {
                    position: relative;
                    z-index: 1;
                    padding: 140px 30px 100px 30px; /* top, right, bottom, left - ajustar según tu membrete */
                }
                
                .header {
                    position: relative; /* necesario para el posicionamiento absoluto del recuadro */
                    margin-bottom: 10px;
                    padding-bottom: 10px;
                }

                /* Dejamos espacio a la derecha para que el recuadro no tape el contenido */
                .empresa-info {
                    margin-right: 320px; /* espacio reservado para .cotizacion-info (ajustable) */
                }

                .empresa-info h1 {
                    color: #007bff;
                    font-size: 20px;
                    margin-bottom: 6px;
                    font-weight: bold;
                }

                .empresa-info p {
                    margin-bottom: 3px;
                    font-size: 11px;
                }

                .cotizacion-info {
                    position: absolute;
                    right: 30px;
                    top: 20px; /* posición desde el inicio del content-wrapper */
                    width: 260px;
                    text-align: right;
                    background: rgba(255,255,255,0.95); /* fondo blanco semi-transparente */
                    padding: 10px 12px;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }

                /* Reducir tamaño del título y mejorar jerarquía visual */
                .cotizacion-info h2 {
                    color: #007bff;
                    font-size: 13px;
                    margin-bottom: 6px;
                    letter-spacing: 1px;
                    font-weight: 700;
                }
                
                .info-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                    margin-bottom: 30px;
                    margin-top: 60px; /* espacio debajo del recuadro COTIZACIÓN */
                }
                
                .info-section {
                    background: transparent; /* fondo transparente */
                    padding: 15px;
                    border-radius: 8px;
                    border-left: 4px solid #007bff;
                }
                
                .info-section h3 {
                    color: #007bff;
                    font-size: 14px;
                    margin-bottom: 10px;
                    font-weight: bold;
                }
                
                .info-section p {
                    margin-bottom: 3px;
                    font-size: 11px;
                }
                
                .table-container {
                    margin: 30px 0;
                }
                
                .items-table {
                    width: 100%;
                    border-collapse: collapse;
                    background: white;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    border-radius: 8px;
                    overflow: hidden;
                }
                
                .items-table th {
                    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
                    color: white;
                    padding: 12px 8px;
                    text-align: left;
                    font-weight: bold;
                    font-size: 11px;
                }
                
                .items-table td {
                    padding: 10px 8px;
                    border-bottom: 1px solid #dee2e6;
                    font-size: 11px;
                }
                
                .items-table tbody tr:nth-child(even) {
                    background: #f8f9fa;
                }
                
                .items-table tbody tr:hover {
                    background: #e3f2fd;
                }
                
                .text-right {
                    text-align: right;
                }
                
                .text-center {
                    text-align: center;
                }
                
                .totals-section {
                    margin-top: 20px;
                    display: flex;
                    justify-content: flex-end;
                }
                
                .totals-table {
                    width: 300px;
                    border-collapse: collapse;
                }
                
                .totals-table td {
                    padding: 8px 12px;
                    border: 1px solid #dee2e6;
                    font-size: 12px;
                }
                
                .totals-table .total-label {
                    background: #f8f9fa;
                    font-weight: bold;
                    text-align: right;
                }
                
                .totals-table .total-final {
                    background: #007bff;
                    color: white;
                    font-weight: bold;
                    font-size: 14px;
                }
                
                .footer {
                    margin-top: 40px;
                    padding-top: 20px;
                    border-top: 2px solid #dee2e6;
                    font-size: 10px;
                    color: #6c757d;
                }
                
                .footer-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 30px;
                }
                
                .validez {
                    background: #fff3cd;
                    border: 1px solid #ffeaa7;
                    border-radius: 5px;
                    padding: 10px;
                    margin: 20px 0;
                }
                
                .validez h4 {
                    color: #856404;
                    font-size: 12px;
                    margin-bottom: 5px;
                }
                
                .observaciones {
                    background: #e7f3ff;
                    border: 1px solid #b8daff;
                    border-radius: 5px;
                    padding: 15px;
                    margin: 20px 0;
                }
                
                .observaciones h4 {
                    color: #004085;
                    font-size: 12px;
                    margin-bottom: 8px;
                }
                
                @media print {
                    body {
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    
                    .container {
                        padding: 0;
                        max-width: none;
                    }
                    
                    .no-print {
                        display: none !important;
                    }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <?php if ($membreteData): ?>
                    <img class="membrete-img" src="<?php echo $membreteData; ?>" alt="Membrete">
                <?php endif; ?>
                
                <!-- Contenedor interno para el contenido -->
                <div class="content-wrapper">
                <!-- Header -->
                <div class="header">
                    <?php if (!$membreteData): ?>
                    <div class="empresa-info">
                        <h1><?php echo $this->empresa['nombre']; ?></h1>
                        <p><strong>NIT:</strong> <?php echo $this->empresa['nit']; ?></p>
                        <p><strong>Dirección:</strong> <?php echo $this->empresa['direccion']; ?></p>
                        <p><strong>Ciudad:</strong> <?php echo $this->empresa['ciudad']; ?></p>
                        <p><strong>Teléfono:</strong> <?php echo $this->empresa['telefono']; ?></p>
                        <p><strong>Email:</strong> <?php echo $this->empresa['email']; ?></p>
                        <p><strong>Web:</strong> <?php echo $this->empresa['web']; ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="cotizacion-info">
                        <h2>COTIZACIÓN</h2>
                        <p><strong>Número:</strong> <?php echo str_pad($cotizacion['id'] ?? 0, 6, '0', STR_PAD_LEFT); ?></p>
                        <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($cotizacion['fecha'] ?? 'now')); ?></p>
                    </div>
                </div>
                
                <!-- Información del Cliente -->
                <div class="info-grid">
                    <div class="info-section">
                        <h3>INFORMACIÓN DEL CLIENTE</h3>
                        <p><strong>Cliente:</strong> <?php echo $cliente['nombre'] ?? 'N/A'; ?></p>
                        <?php if (!empty($cliente['documento'])): ?>
                        <p><strong>NIT/CC:</strong> <?php echo $cliente['documento']; ?></p>
                        <?php endif; ?>
                        <p><strong>Dirección:</strong> <?php echo $cliente['direccion'] ?? ''; ?></p>
                        <?php if (!empty($cliente['ciudad'])): ?>
                        <p><strong>Ciudad:</strong> <?php echo $cliente['ciudad']; ?></p>
                        <?php endif; ?>
                        <p><strong>Teléfono:</strong> <?php echo $cliente['telefono'] ?? ''; ?></p>
                        <?php if (!empty($cliente['correo'])): ?>
                        <p><strong>Email:</strong> <?php echo $cliente['correo']; ?></p>
                        <?php endif; ?>
                        <?php if (!empty($cliente['contacto'])): ?>
                        <p><strong>Contacto:</strong> <?php echo $cliente['contacto']; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="info-section">
                        <h3>DETALLES DE LA COTIZACIÓN</h3>
                        <p><strong>Vendedor:</strong> <?php echo $_SESSION['username'] ?? 'Sistema'; ?></p>
                        <p><strong>Fecha de elaboración:</strong> <?php echo date('d/m/Y H:i'); ?></p>
                        <p><strong>Moneda:</strong> Pesos Colombianos (COP)</p>
                        <p><strong>Forma de pago:</strong> Según acuerdo comercial</p>
                        <p><strong>Tiempo de entrega:</strong> 15 días hábiles</p>
                    </div>
                </div>
                
                <!-- Tabla de Items -->
                <div class="table-container">
                    <table class="items-table">
                        <thead>
                                    <tr>
                                        <th style="width: 8%;">#</th>
                                        <th style="width: 62%;">Descripción</th>
                                        <th style="width: 10%;" class="text-center">Cantidad</th>
                                        <th style="width: 20%;" class="text-right">Precio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Preprocesar detalles: agrupar paquetes para mostrarlos como una sola fila
                                    $processedDetalles = [];
                                    foreach ($detalles as $d) {
                                        $isPaquete = (isset($d['tipo_item']) && $d['tipo_item'] === 'paquete') || !empty($d['id_paquete']) || !empty($d['nombre_item']);
                                        if ($isPaquete) {
                                            $key = 'paquete_' . ($d['id_paquete'] ?? md5($d['nombre_item'] ?? ($d['nombre'] ?? '')));
                                            if (!isset($processedDetalles[$key])) {
                                                $processedDetalles[$key] = [
                                                    'tipo' => 'paquete',
                                                    'id_paquete' => $d['id_paquete'] ?? null,
                                                    'nombre' => $d['nombre_item'] ?? $d['nombre'] ?? 'Paquete',
                                                    'descripcion' => $d['descripcion_item'] ?? $d['descripcion'] ?? '',
                                                    'precio_unitario' => floatval($d['precio_venta'] ?? $d['precio'] ?? 0),
                                                    'cantidad' => floatval($d['cantidad'] ?? 0)
                                                ];
                                            } else {
                                                // Acumular cantidades si hay múltiples líneas del mismo paquete
                                                $processedDetalles[$key]['cantidad'] += floatval($d['cantidad'] ?? 0);
                                                // Si el precio unitario está presente y es mayor a 0, mantenlo (no sobrescribir si ya existe)
                                                if (empty($processedDetalles[$key]['precio_unitario']) && !empty($d['precio_venta'])) {
                                                    $processedDetalles[$key]['precio_unitario'] = floatval($d['precio_venta']);
                                                }
                                            }
                                        } else {
                                            // Artículo independiente
                                            $processedDetalles[] = [
                                                'tipo' => 'articulo',
                                                'nombre' => $d['nombre'] ?? 'Artículo',
                                                'descripcion' => $d['descripcion'] ?? '',
                                                'precio_unitario' => floatval($d['precio_venta'] ?? $d['precio'] ?? 0),
                                                'cantidad' => floatval($d['cantidad'] ?? 0)
                                            ];
                                        }
                                    }

                                    // Renderizar detalles procesados
                                    $item_num = 1;
                                    foreach ($processedDetalles as $detalle):
                                        $nombre = $detalle['nombre'] ?? 'Artículo';
                                        $descripcion = $detalle['descripcion'] ?? '';
                                        $precio_unitario = floatval($detalle['precio_unitario'] ?? 0);
                                        $cantidad = floatval($detalle['cantidad'] ?? 0);
                                        $total_item = $cantidad * $precio_unitario;
                                        $subtotal += $total_item;
                                    ?>
                                    <tr>
                                        <td class="text-center" style="vertical-align: top; padding-top: 12px;"><?php echo $item_num++; ?></td>
                                        <td style="vertical-align: top; padding-top: 12px;">
                                            <strong style="font-size: 13px;"><?php echo htmlspecialchars($nombre); ?></strong>
                                            <?php if (!empty($descripcion)): ?>
                                            <br><br>
                                            <strong style="font-size: 11px;">Descripción:</strong><br>
                                            <div style="margin-top: 4px; font-size: 11px; color: #333; line-height: 1.5;">
                                                <?php echo nl2br(htmlspecialchars($descripcion)); ?>
                                            </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center" style="vertical-align: top; padding-top: 12px;"><strong><?php echo number_format($cantidad, 0, ',', '.'); ?></strong></td>
                                        <td class="text-right" style="vertical-align: top; padding-top: 12px;"><strong><?php echo number_format($precio_unitario, 0, ',', '.'); ?></strong></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                    </table>
                </div>
                
                <!-- Totales removidos - solo para uso interno -->
                
                <!-- Validez y Observaciones -->
                <div class="validez">
                    <h4>⏰ VALIDEZ DE LA COTIZACIÓN</h4>
                    <p>Esta cotización tiene una validez de 30 días calendario a partir de la fecha de emisión.</p>
                </div>
                
                <!-- Footer -->
                <div class="footer">
                    <div class="footer-grid">
                        <div>
                            <h4>TÉRMINOS Y CONDICIONES</h4>
                            <ul style="margin-left: 15px; margin-top: 5px;">
                                <li>Los precios están sujetos a cambio sin previo aviso</li>
                                <li>La validez de la cotización es limitada</li>
                                <li>Se requiere confirmación por escrito para procesar pedidos</li>
                                <li>Los tiempos de entrega pueden variar según disponibilidad</li>
                            </ul>
                        </div>
                        <div>
                            <h4>INFORMACIÓN DE CONTACTO</h4>
                            <p><strong>Generado:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
                            <p><strong>Sistema:</strong> Cotizaciones Empresariales v1.0</p>
                            <p style="margin-top: 10px; color: #007bff; font-weight: bold;">
                                ¡Gracias por confiar en nosotros!
                            </p>
                        </div>
                    </div>
                </div>
                </div> <!-- Cierre de content-wrapper -->
            </div> <!-- Cierre de container -->
            
            <script>
                // Auto-imprimir si se accede directamente
                if (window.location.search.includes('print=1')) {
                    window.onload = function() {
                        setTimeout(function() {
                            window.print();
                        }, 500);
                    };
                }
            </script>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Generar HTML imprimible
     */
    private function generarHTMLImprimible($html, $filename) {
        $tempDir = __DIR__ . '/../../public/temp/';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Guardar HTML temporal
        $htmlPath = $tempDir . $filename . '.html';
        file_put_contents($htmlPath, $html);

        // Generar PDF usando Dompdf
        require_once __DIR__ . '/../../vendor/autoload.php';
        try {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $pdfPath = $tempDir . $filename . '.pdf';
            file_put_contents($pdfPath, $dompdf->output());

            // Retornar la ruta del PDF generado
            return $pdfPath;
        } catch (Exception $e) {
            // Si falla Dompdf, retornar el HTML temporal como fallback
            error_log('PDFGenerator: Error generando PDF con Dompdf: ' . $e->getMessage());
            return $htmlPath;
        }
    }
    
    /**
     * Generar cotización y retornar información completa
     */
    public function generarCotizacionInfo($cotizacionId) {
        // Cargar modelos necesarios
        require_once __DIR__ . '/../models/Cotizacion.php';
        require_once __DIR__ . '/../models/Cliente.php';
        
        $cotizacionModel = new Cotizacion();
        $clienteModel = new Cliente();
        
        // Obtener datos
        $cotizacion = $cotizacionModel->getCotizacionCompleta($cotizacionId);
        if (!$cotizacion) {
            return false;
        }
        
        $cliente = $clienteModel->getById($cotizacion['id_cliente']);
        $detalles = $cotizacionModel->getDetallesCotizacion($cotizacionId);
        
        // Generar PDF (o HTML si Dompdf falla)
        $pdfPath = $this->generarCotizacionPDF($cotizacion, $cliente, $detalles);

        $ext = pathinfo($pdfPath, PATHINFO_EXTENSION);
        $filename = basename($pdfPath);

        return [
            'success' => true,
            'type' => $ext === 'pdf' ? 'pdf' : 'html',
            'filepath' => $pdfPath,
            'url' => 'public/temp/' . $filename . ($ext === 'html' ? '?print=1' : ''),
            'filename' => $filename
        ];
    }
    
    /**
     * Configurar información de la empresa
     */
    public function setEmpresaInfo($info) {
        $this->empresa = array_merge($this->empresa, $info);
    }
    
    /**
     * Obtener información de la empresa
     */
    public function getEmpresaInfo() {
        return $this->empresa;
    }
}
?>