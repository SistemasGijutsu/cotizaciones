<?php
/**
 * Clase para envío de emails
 * Maneja el envío de cotizaciones y notificaciones por correo
 */

class EmailSender {
    
    private $smtpConfig;
    private $empresa;
    
    public function __construct() {
        $this->smtpConfig = [
            'host' => 'smtp.gmail.com', // Cambiar por tu servidor SMTP
            'port' => 587,
            'username' => '', // Configurar con tu email
            'password' => '', // Configurar con tu contraseña
            'from_email' => 'noreply@cotizaciones.com',
            'from_name' => 'Sistema de Cotizaciones'
        ];
        
        $this->empresa = [
            'nombre' => 'Sistema de Cotizaciones Empresariales',
            'telefono' => '+57 (1) 234-5678',
            'email' => 'info@cotizaciones.com',
            'web' => 'www.cotizaciones.com'
        ];
    }
    
    /**
     * Enviar cotización por email
     */
    public function enviarCotizacion($datos) {
        try {
            $destinatario = $datos['destinatario'];
            $asunto = $datos['asunto'];
            $mensaje = $datos['mensaje'];
            $cotizacion = $datos['cotizacion'];
            $cliente = $datos['cliente'];
            $archivoAdjunto = $datos['archivo_adjunto'] ?? null;
            
            // Generar contenido HTML del email
            $htmlContent = $this->generarHTMLEmail($cotizacion, $cliente, $mensaje);
            
            // Configurar headers
            $headers = $this->configurarHeaders($archivoAdjunto);
            
            // Enviar email
            $enviado = mail($destinatario, $asunto, $htmlContent, $headers);
            
            if ($enviado) {
                // Log del envío exitoso
                $this->logEnvio($destinatario, $asunto, 'success');
                return true;
            } else {
                // Log del error
                $this->logEnvio($destinatario, $asunto, 'error');
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Error enviando email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generar HTML para el email
     */
    private function generarHTMLEmail($cotizacion, $cliente, $mensaje) {
        $html = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Cotización ' . $cotizacion['numero'] . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    background-color: #f4f4f4;
                }
                
                .email-container {
                    background: white;
                    padding: 30px;
                    border-radius: 10px;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                }
                
                .header {
                    text-align: center;
                    border-bottom: 3px solid #007bff;
                    padding-bottom: 20px;
                    margin-bottom: 30px;
                }
                
                .header h1 {
                    color: #007bff;
                    margin: 0;
                    font-size: 24px;
                }
                
                .header p {
                    color: #666;
                    margin: 5px 0;
                }
                
                .cotizacion-info {
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 5px;
                    margin: 20px 0;
                }
                
                .cotizacion-info h2 {
                    color: #007bff;
                    margin-top: 0;
                }
                
                .info-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 10px;
                    margin: 15px 0;
                }
                
                .info-item {
                    padding: 5px 0;
                }
                
                .info-item strong {
                    color: #333;
                }
                
                .mensaje-personalizado {
                    background: #e7f3ff;
                    border-left: 4px solid #007bff;
                    padding: 15px;
                    margin: 20px 0;
                }
                
                .footer {
                    text-align: center;
                    border-top: 1px solid #ddd;
                    padding-top: 20px;
                    margin-top: 30px;
                    color: #666;
                    font-size: 12px;
                }
                
                .cta-button {
                    display: inline-block;
                    background: #007bff;
                    color: white;
                    padding: 12px 25px;
                    text-decoration: none;
                    border-radius: 5px;
                    margin: 15px 0;
                    font-weight: bold;
                }
                
                .cta-button:hover {
                    background: #0056b3;
                }
                
                @media (max-width: 600px) {
                    .info-grid {
                        grid-template-columns: 1fr;
                    }
                    
                    .email-container {
                        padding: 20px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="email-container">
                <div class="header">
                    <h1>' . $this->empresa['nombre'] . '</h1>
                    <p>' . $this->empresa['telefono'] . ' | ' . $this->empresa['email'] . '</p>
                    <p>' . $this->empresa['web'] . '</p>
                </div>
                
                <div class="cotizacion-info">
                    <h2>Cotización #' . $cotizacion['numero'] . '</h2>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Cliente:</strong><br>
                            ' . $cliente['nombre'] . '
                        </div>
                        <div class="info-item">
                            <strong>Fecha:</strong><br>
                            ' . date('d/m/Y', strtotime($cotizacion['fecha'])) . '
                        </div>
                        <div class="info-item">
                            <strong>Válida hasta:</strong><br>
                            ' . date('d/m/Y', strtotime($cotizacion['fecha_vencimiento'])) . '
                        </div>
                        <div class="info-item">
                            <strong>Estado:</strong><br>
                            <span style="color: ' . ($cotizacion['estado'] == 'activa' ? '#28a745' : '#6c757d') . ';">
                                ' . ucfirst($cotizacion['estado']) . '
                            </span>
                        </div>
                    </div>
                </div>';
        
        if (!empty($mensaje)) {
            $html .= '
                <div class="mensaje-personalizado">
                    <h3>Mensaje:</h3>
                    <p>' . nl2br(htmlspecialchars($mensaje)) . '</p>
                </div>';
        }
        
        $html .= '
                <div style="text-align: center; margin: 30px 0;">
                    <p><strong>Para ver el detalle completo de la cotización, revise el archivo adjunto.</strong></p>
                </div>
                
                <div class="footer">
                    <p>Este email fue generado automáticamente por el Sistema de Cotizaciones.</p>
                    <p>Si tiene alguna pregunta, no dude en contactarnos.</p>
                    <p style="margin-top: 15px;">
                        <strong>' . $this->empresa['nombre'] . '</strong><br>
                        ' . $this->empresa['telefono'] . ' | ' . $this->empresa['email'] . '<br>
                        ' . $this->empresa['web'] . '
                    </p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Configurar headers del email
     */
    private function configurarHeaders($archivoAdjunto = null) {
        $boundary = md5(time());
        
        $headers = "From: " . $this->smtpConfig['from_name'] . " <" . $this->smtpConfig['from_email'] . ">\r\n";
        $headers .= "Reply-To: " . $this->smtpConfig['from_email'] . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        
        if ($archivoAdjunto && file_exists($archivoAdjunto)) {
            $headers .= "Content-Type: multipart/mixed; boundary=\"" . $boundary . "\"\r\n";
        } else {
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        }
        
        return $headers;
    }
    
    /**
     * Log de envíos de email
     */
    private function logEnvio($destinatario, $asunto, $estado) {
        $logFile = __DIR__ . '/../../logs/email_log.txt';
        $logDir = dirname($logFile);
        
        // Crear directorio de logs si no existe
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$estado] To: $destinatario | Subject: $asunto\n";
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Configurar SMTP
     */
    public function configurarSMTP($config) {
        $this->smtpConfig = array_merge($this->smtpConfig, $config);
    }
    
    /**
     * Configurar información de la empresa
     */
    public function configurarEmpresa($info) {
        $this->empresa = array_merge($this->empresa, $info);
    }
    
    /**
     * Enviar email simple
     */
    public function enviarSimple($destinatario, $asunto, $mensaje) {
        $headers = "From: " . $this->smtpConfig['from_name'] . " <" . $this->smtpConfig['from_email'] . ">\r\n";
        $headers .= "Reply-To: " . $this->smtpConfig['from_email'] . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        $htmlMensaje = "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #007bff;'>" . $this->empresa['nombre'] . "</h2>
                <p>" . nl2br(htmlspecialchars($mensaje)) . "</p>
                <hr>
                <p style='font-size: 12px; color: #666;'>
                    Este mensaje fue enviado desde el Sistema de Cotizaciones<br>
                    " . $this->empresa['telefono'] . " | " . $this->empresa['email'] . "
                </p>
            </div>
        </body>
        </html>";
        
        $enviado = mail($destinatario, $asunto, $htmlMensaje, $headers);
        $this->logEnvio($destinatario, $asunto, $enviado ? 'success' : 'error');
        
        return $enviado;
    }
}
?>