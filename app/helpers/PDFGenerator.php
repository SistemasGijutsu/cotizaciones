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
        
        // Nombre del archivo
        $filename = 'cotizacion_' . str_pad($cotizacion['id'], 6, '0', STR_PAD_LEFT) . '_' . date('Y-m-d') . '.pdf';
        
        // Por ahora, generar HTML para imprimir
        // En el futuro se puede implementar TCPDF u otra librería
        return $this->generarHTMLImprimible($html, $filename);
    }
    
    /**
     * Generar HTML de cotización
     */
    private function generarHTMLCotizacion($cotizacion, $cliente, $detalles) {
        $subtotal = 0;
        $totalCosto = 0;
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Cotización #<?php echo $cotizacion['numero']; ?></title>
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
                    padding: 20px;
                }
                
                .header {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                    border-bottom: 3px solid #007bff;
                }
                
                .empresa-info {
                    flex: 1;
                }
                
                .empresa-info h1 {
                    color: #007bff;
                    font-size: 18px;
                    margin-bottom: 10px;
                    font-weight: bold;
                }
                
                .empresa-info p {
                    margin-bottom: 3px;
                    font-size: 11px;
                }
                
                .cotizacion-info {
                    text-align: right;
                    flex: 1;
                    background: #f8f9fa;
                    padding: 15px;
                    border-radius: 8px;
                }
                
                .cotizacion-info h2 {
                    color: #007bff;
                    font-size: 16px;
                    margin-bottom: 10px;
                }
                
                .info-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                    margin-bottom: 30px;
                }
                
                .info-section {
                    background: #f8f9fa;
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
                <!-- Header -->
                <div class="header">
                    <div class="empresa-info">
                        <h1><?php echo $this->empresa['nombre']; ?></h1>
                        <p><strong>NIT:</strong> <?php echo $this->empresa['nit']; ?></p>
                        <p><strong>Dirección:</strong> <?php echo $this->empresa['direccion']; ?></p>
                        <p><strong>Ciudad:</strong> <?php echo $this->empresa['ciudad']; ?></p>
                        <p><strong>Teléfono:</strong> <?php echo $this->empresa['telefono']; ?></p>
                        <p><strong>Email:</strong> <?php echo $this->empresa['email']; ?></p>
                        <p><strong>Web:</strong> <?php echo $this->empresa['web']; ?></p>
                    </div>
                    
                    <div class="cotizacion-info">
                        <h2>COTIZACIÓN</h2>
                        <p><strong>Número:</strong> <?php echo $cotizacion['numero']; ?></p>
                        <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($cotizacion['fecha'])); ?></p>
                        <p><strong>Válida hasta:</strong> <?php echo date('d/m/Y', strtotime($cotizacion['fecha_vencimiento'])); ?></p>
                        <p><strong>Estado:</strong> <span style="color: <?php echo $cotizacion['estado'] == 'activa' ? '#28a745' : '#6c757d'; ?>;"><?php echo ucfirst($cotizacion['estado']); ?></span></p>
                    </div>
                </div>
                
                <!-- Información del Cliente -->
                <div class="info-grid">
                    <div class="info-section">
                        <h3>INFORMACIÓN DEL CLIENTE</h3>
                        <p><strong>Cliente:</strong> <?php echo $cliente['nombre']; ?></p>
                        <p><strong>NIT/CC:</strong> <?php echo $cliente['nit']; ?></p>
                        <p><strong>Dirección:</strong> <?php echo $cliente['direccion']; ?></p>
                        <p><strong>Ciudad:</strong> <?php echo $cliente['ciudad']; ?></p>
                        <p><strong>Teléfono:</strong> <?php echo $cliente['telefono']; ?></p>
                        <p><strong>Email:</strong> <?php echo $cliente['email']; ?></p>
                        <?php if (!empty($cliente['contacto'])): ?>
                        <p><strong>Contacto:</strong> <?php echo $cliente['contacto']; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="info-section">
                        <h3>DETALLES DE LA COTIZACIÓN</h3>
                        <p><strong>Vendedor:</strong> <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Sistema'; ?></p>
                        <p><strong>Fecha de elaboración:</strong> <?php echo date('d/m/Y H:i'); ?></p>
                        <p><strong>Moneda:</strong> Pesos Colombianos (COP)</p>
                        <p><strong>Forma de pago:</strong> Según acuerdo comercial</p>
                        <p><strong>Tiempo de entrega:</strong> <?php echo $cotizacion['tiempo_entrega'] ?? '15 días hábiles'; ?></p>
                    </div>
                </div>
                
                <!-- Tabla de Items -->
                <div class="table-container">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width: 8%;">#</th>
                                <th style="width: 35%;">Descripción</th>
                                <th style="width: 10%;" class="text-center">Cant.</th>
                                <th style="width: 15%;" class="text-right">Precio Unit.</th>
                                <th style="width: 15%;" class="text-right">Costo Unit.</th>
                                <th style="width: 17%;" class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $item_num = 1;
                            foreach ($detalles as $detalle): 
                                $total_item = $detalle['cantidad'] * $detalle['precio_unitario'];
                                $costo_item = $detalle['cantidad'] * ($detalle['precio_costo'] ?? 0);
                                $subtotal += $total_item;
                                $totalCosto += $costo_item;
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $item_num++; ?></td>
                                <td>
                                    <strong><?php echo $detalle['nombre_articulo']; ?></strong>
                                    <?php if (!empty($detalle['descripcion'])): ?>
                                    <br><small style="color: #6c757d;"><?php echo $detalle['descripcion']; ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><?php echo number_format($detalle['cantidad'], 0); ?></td>
                                <td class="text-right"><?php echo '$' . number_format($detalle['precio_unitario'], 0); ?></td>
                                <td class="text-right" style="color: #dc3545;"><?php echo '$' . number_format($detalle['precio_costo'] ?? 0, 0); ?></td>
                                <td class="text-right"><strong><?php echo '$' . number_format($total_item, 0); ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Totales -->
                <div class="totals-section">
                    <table class="totals-table">
                        <tr>
                            <td class="total-label">Subtotal:</td>
                            <td class="text-right"><?php echo '$' . number_format($subtotal, 0); ?></td>
                        </tr>
                        <?php 
                        $iva = $subtotal * 0.19; // IVA del 19%
                        $total = $subtotal + $iva;
                        $utilidad = $subtotal - $totalCosto;
                        $margen = $totalCosto > 0 ? (($utilidad / $totalCosto) * 100) : 0;
                        ?>
                        <tr>
                            <td class="total-label">IVA (19%):</td>
                            <td class="text-right"><?php echo '$' . number_format($iva, 0); ?></td>
                        </tr>
                        <tr class="total-final">
                            <td>TOTAL:</td>
                            <td class="text-right"><?php echo '$' . number_format($total, 0); ?></td>
                        </tr>
                        <tr style="background: #d4edda; color: #155724;">
                            <td class="total-label">Costo Total:</td>
                            <td class="text-right"><?php echo '$' . number_format($totalCosto, 0); ?></td>
                        </tr>
                        <tr style="background: #d1ecf1; color: #0c5460;">
                            <td class="total-label">Utilidad:</td>
                            <td class="text-right"><?php echo '$' . number_format($utilidad, 0) . ' (' . number_format($margen, 1) . '%)'; ?></td>
                        </tr>
                    </table>
                </div>
                
                <!-- Validez y Observaciones -->
                <div class="validez">
                    <h4>⏰ VALIDEZ DE LA COTIZACIÓN</h4>
                    <p>Esta cotización tiene una validez de <?php echo $cotizacion['dias_validez'] ?? 30; ?> días calendario a partir de la fecha de emisión.</p>
                </div>
                
                <?php if (!empty($cotizacion['observaciones'])): ?>
                <div class="observaciones">
                    <h4>📝 OBSERVACIONES</h4>
                    <p><?php echo nl2br($cotizacion['observaciones']); ?></p>
                </div>
                <?php endif; ?>
                
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
            </div>
            
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
        $filepath = __DIR__ . '/../../public/temp/' . $filename . '.html';
        
        // Crear directorio temporal si no existe
        $tempDir = dirname($filepath);
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        file_put_contents($filepath, $html);
        
        return [
            'success' => true,
            'type' => 'html',
            'filepath' => $filepath,
            'url' => 'public/temp/' . $filename . '.html?print=1',
            'filename' => $filename . '.html'
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