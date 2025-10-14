# Sistema de Cotizaciones Empresariales

## 📋 Descripción

El Sistema de Cotizaciones Empresariales es una aplicación web desarrollada en **PHP** con base de datos **MySQL** (entorno XAMPP). Su propósito es automatizar la creación, gestión y envío de cotizaciones, permitiendo comparar automáticamente precios de costo y precios de venta para calcular la utilidad de cada operación comercial.

## 🎯 Objetivos

### Objetivo General
Diseñar e implementar un sistema que permita generar y enviar cotizaciones automáticas, optimizando la gestión comercial.

### Objetivos Específicos
- ✅ Registrar artículos con precio costo y precio venta
- ✅ Crear paquetes a partir de artículos existentes
- ✅ Generar automáticamente cotizaciones a precio costo y precio venta
- ⏳ Enviar cotizaciones por correo electrónico al cliente
- ✅ Calcular la utilidad obtenida en cada cotización
- ✅ Generar reportes y estadísticas de ventas y utilidades

## 🏗️ Arquitectura del Sistema

El sistema sigue una arquitectura **cliente-servidor** basada en el patrón **MVC (Modelo-Vista-Controlador)**:

- **Cliente**: Interfaz web responsiva construida con HTML5, CSS3, Bootstrap 5 y JavaScript
- **Servidor PHP**: Controla la lógica del negocio y comunicación con la base de datos
- **Base de Datos MySQL**: Almacena información de clientes, artículos, paquetes y cotizaciones
- **Servidor SMTP**: Gestiona el envío automático de cotizaciones (en desarrollo)

## 🧩 Módulos del Sistema

### 1. 📊 Dashboard
- Estadísticas generales del sistema
- Cotizaciones recientes
- Artículos más cotizados
- Accesos rápidos a funcionalidades principales

### 2. 👥 Gestión de Clientes
- Registro y gestión completa de clientes
- Campos: nombre, correo, teléfono, dirección
- Historial de cotizaciones por cliente
- Búsqueda avanzada

### 3. 📦 Inventario de Artículos
- Registro de artículos con precios de costo y venta
- Control de stock disponible
- Cálculo automático de utilidad por artículo
- Estadísticas de artículos más cotizados

### 4. 📋 Gestión de Paquetes
- Creación de paquetes con múltiples artículos
- Cálculo automático de precios del paquete
- Verificación de disponibilidad de stock
- Gestión dinámica de artículos en paquetes

### 5. 💰 Sistema de Cotizaciones
- Generación automática de cotizaciones
- Cálculo de utilidad en tiempo real
- Dos versiones: precio costo y precio venta
- Gestión completa del proceso comercial

### 6. 📈 Reportes y Estadísticas
- Reportes de ventas y márgenes
- Filtros por fecha, cliente o categoría
- Estadísticas de rentabilidad
- Artículos más cotizados

## 📁 Estructura del Proyecto

```
sistema_cotizaciones/
├── 📁 app/
│   ├── 📁 controllers/          # Controladores MVC
│   │   ├── Controller.php       # Controlador base
│   │   ├── HomeController.php   # Dashboard principal
│   │   ├── ClienteController.php
│   │   ├── ArticuloController.php
│   │   ├── PaqueteController.php
│   │   └── CotizacionController.php
│   ├── 📁 models/              # Modelos de datos
│   │   ├── Model.php           # Modelo base
│   │   ├── Cliente.php
│   │   ├── Articulo.php
│   │   ├── Paquete.php
│   │   └── Cotizacion.php
│   ├── 📁 views/               # Vistas de la aplicación
│   │   ├── 📁 layouts/         # Plantillas base
│   │   ├── 📁 home/           # Dashboard
│   │   ├── 📁 clientes/       # Gestión de clientes
│   │   ├── 📁 articulos/      # Gestión de artículos
│   │   ├── 📁 paquetes/       # Gestión de paquetes
│   │   └── 📁 cotizaciones/   # Sistema de cotizaciones
│   └── 📁 helpers/            # Utilidades y helpers
│       └── Helper.php         # Funciones auxiliares
├── 📁 public/                 # Archivos públicos
│   ├── 📁 css/               # Estilos CSS
│   ├── 📁 js/                # Scripts JavaScript
│   └── 📁 images/            # Imágenes del sistema
├── 📁 config/                # Configuración
│   └── database.php          # Configuración de BD
├── 📁 vendor/                # Dependencias externas
├── index.php                 # Punto de entrada principal
└── README.md                 # Documentación
```

## 💾 Modelo de Base de Datos

### Tablas Principales

```sql
-- Clientes
CREATE TABLE clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    correo VARCHAR(255) UNIQUE NOT NULL,
    telefono VARCHAR(50) NOT NULL,
    direccion TEXT
);

-- Artículos
CREATE TABLE articulos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio_costo DECIMAL(10,2) NOT NULL,
    precio_venta DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0
);

-- Paquetes
CREATE TABLE paquetes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT
);

-- Relación paquete-artículos
CREATE TABLE paquete_articulos (
    id_paquete INT,
    id_articulo INT,
    cantidad INT NOT NULL,
    FOREIGN KEY (id_paquete) REFERENCES paquetes(id),
    FOREIGN KEY (id_articulo) REFERENCES articulos(id)
);

-- Cotizaciones
CREATE TABLE cotizaciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    id_cliente INT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_costo DECIMAL(10,2) NOT NULL,
    total_venta DECIMAL(10,2) NOT NULL,
    utilidad DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id)
);

-- Detalle de cotizaciones
CREATE TABLE cotizacion_detalle (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_cotizacion INT NOT NULL,
    id_articulo INT NOT NULL,
    cantidad INT NOT NULL,
    precio_costo DECIMAL(10,2) NOT NULL,
    precio_venta DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_cotizacion) REFERENCES cotizaciones(id),
    FOREIGN KEY (id_articulo) REFERENCES articulos(id)
);
```

## ⚙️ Requerimientos del Sistema

### Requerimientos Funcionales
- ✅ Registrar, modificar y eliminar artículos
- ✅ Crear paquetes de productos
- ✅ Generar cotizaciones automáticas
- ✅ Calcular utilidad entre precio costo y precio venta
- ✅ Generar informes filtrados por criterios definidos
- ⏳ Envío automático de cotizaciones por email

### Requerimientos No Funcionales
- ✅ Uso del patrón MVC
- ✅ Interfaz web responsiva con Bootstrap 5
- ✅ Seguridad mediante validación de datos
- ✅ Compatibilidad con PHP 8+ y MySQL 8+
- ✅ Alto rendimiento en consultas

### Requerimientos Técnicos
- **PHP**: 7.4 o superior
- **MySQL**: 5.7 o superior
- **Servidor Web**: Apache (XAMPP)
- **Navegadores**: Chrome, Firefox, Safari, Edge (últimas versiones)

## 🚀 Instalación y Configuración

### 1. Prerrequisitos
- XAMPP instalado y funcionando
- PHP 7.4 o superior
- MySQL 5.7 o superior

### 2. Instalación de la Base de Datos
```sql
-- 1. Crear la base de datos
CREATE DATABASE mod_cotizacion CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Usar la base de datos
USE mod_cotizacion;

-- 3. Ejecutar los scripts de creación de tablas (ver sección Modelo de BD)
```

### 3. Configuración del Sistema
1. Copiar el proyecto a `C:\xampp\htdocs\mod_cotizacion\`
2. Verificar configuración en `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'mod_cotizacion');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```
3. Asegurar permisos de escritura en carpetas temporales

### 4. Acceso al Sistema
- URL: `http://localhost/mod_cotizacion/`
- El sistema verificará automáticamente la conexión a la base de datos

## 🔁 Flujo de Trabajo

1. **Configuración Inicial**
   - Registrar artículos en el inventario
   - Crear paquetes si es necesario
   - Registrar clientes

2. **Proceso de Cotización**
   - Seleccionar cliente
   - Agregar artículos o paquetes
   - Revisar cálculos automáticos
   - Generar cotización

3. **Gestión Comercial**
   - Revisar utilidades calculadas
   - Generar reportes
   - Realizar seguimiento de clientes

## 🎨 Características de la Interfaz

### Diseño Responsivo
- Adaptable a dispositivos móviles, tablets y desktop
- Navegación intuitiva con sidebar colapsible
- Iconografía clara con Font Awesome

### Experiencia de Usuario
- Feedback visual inmediato
- Validación de formularios en tiempo real
- Confirmaciones para acciones críticas
- Notificaciones toast para operaciones

### Elementos Visuales
- Tarjetas de estadísticas con gradientes
- Tablas responsivas con hover effects
- Botones con animaciones suaves
- Alertas contextuales

## 📊 Funcionalidades Principales

### Dashboard Interactivo
- **Estadísticas en tiempo real**: Clientes, artículos, cotizaciones
- **Gráficos visuales**: Utilidades, tendencias, comparativas
- **Accesos rápidos**: Creación rápida de elementos
- **Actividad reciente**: Últimas cotizaciones y operaciones

### Gestión de Clientes
- **CRUD completo**: Crear, leer, actualizar, eliminar
- **Búsqueda avanzada**: Por nombre, email, teléfono
- **Historial comercial**: Todas las cotizaciones del cliente
- **Validaciones**: Email único, campos obligatorios

### Sistema de Artículos
- **Gestión de inventario**: Stock, precios, descripciones
- **Cálculo de utilidad**: Automático por artículo
- **Búsqueda y filtros**: Múltiples criterios
- **Estadísticas**: Artículos más cotizados

### Módulo de Paquetes
- **Composición flexible**: Múltiples artículos por paquete
- **Precios automáticos**: Suma de componentes
- **Verificación de stock**: Disponibilidad total
- **Gestión dinámica**: Agregar/quitar artículos

### Sistema de Cotizaciones
- **Generación automática**: Códigos únicos por cotización
- **Doble cálculo**: Precios de costo y venta
- **Utilidad en tiempo real**: Cálculos automáticos
- **Gestión completa**: Desde creación hasta envío

## 🔧 Arquitectura Técnica

### Patrón MVC Implementado
- **Modelos**: Interacción con base de datos, lógica de negocio
- **Vistas**: Presentación HTML/PHP con Bootstrap
- **Controladores**: Lógica de aplicación, enrutamiento

### Características de Seguridad
- **Validación entrada**: Sanitización de datos
- **Prepared Statements**: Prevención de SQL Injection
- **Escape de salida**: Prevención de XSS
- **Validación servidor**: Doble validación cliente/servidor

### Optimizaciones
- **Consultas eficientes**: Uso de índices, joins optimizados
- **Carga asíncrona**: AJAX para operaciones dinámicas
- **Cache de session**: Gestión eficiente de estado
- **Compresión CSS/JS**: Archivos optimizados

## 🔄 APIs y Endpoints

### Endpoints AJAX Disponibles
```php
// Búsqueda de clientes
GET /index.php?controller=cliente&action=search&term={término}

// Búsqueda de artículos
GET /index.php?controller=articulo&action=search&term={término}

// Artículos disponibles
GET /index.php?controller=articulo&action=disponibles

// Cálculo de totales
POST /index.php?controller=cotizacion&action=calcularTotales

// Artículos de paquete
GET /index.php?controller=cotizacion&action=getArticulosPaquete&id={id}
```

## 📈 Métricas y Reportes

### Estadísticas Disponibles
- **Clientes**: Total registrados, más activos
- **Artículos**: Inventario, más cotizados, utilidades
- **Cotizaciones**: Volumen, utilidades, tendencias
- **Rentabilidad**: Márgenes, comparativas, proyecciones

### Filtros y Períodos
- Por fechas (desde/hasta)
- Por cliente específico
- Por categoría de producto
- Períodos predefinidos (30, 60, 90 días)

## 🔮 Funcionalidades Futuras

### En Desarrollo
- [ ] Generación de PDF para cotizaciones
- [ ] Sistema de envío de emails SMTP
- [ ] Autenticación de usuarios
- [ ] Módulo de seguimiento de ventas

### Planeadas
- [ ] API REST completa
- [ ] Integración con sistemas contables
- [ ] Notificaciones push
- [ ] Módulo de inventario avanzado
- [ ] Sistema de roles y permisos
- [ ] Backup automático de datos

## 🐛 Troubleshooting

### Problemas Comunes

#### Error de Conexión a Base de Datos
1. Verificar que XAMPP esté ejecutándose
2. Confirmar que MySQL esté activo
3. Revisar configuración en `config/database.php`
4. Verificar que la base de datos existe

#### Problemas de Permisos
1. Verificar permisos de carpeta del proyecto
2. Asegurar que Apache tenga acceso de lectura
3. Verificar configuración de PHP

#### Errores de JavaScript
1. Verificar consola del navegador
2. Confirmar que jQuery y Bootstrap estén cargados
3. Verificar conectividad a CDNs externos

#### ✅ **SOLUCIONADO**: Error "La vista articulos/index no existe"
**Problema**: Error mostrado al acceder al módulo de artículos
**Solución**: Se crearon todas las vistas faltantes:
- ✅ `app/views/articulos/index.php` - Lista completa de artículos con filtros
- ✅ `app/views/articulos/create.php` - Formulario de creación con calculadora
- ✅ `app/views/clientes/index.php` - Gestión completa de clientes  
- ✅ `app/views/paquetes/index.php` - Vista de paquetes con tarjetas
- ✅ `app/views/cotizaciones/index.php` - Sistema completo de cotizaciones

#### ✅ **SOLUCIONADO**: Sistema Responsive, PWA y PDF Completo
**Problema**: El sistema necesitaba ser completamente responsive, funcionar como PWA y generar PDF
**Solución Implementada**:

**📱 Responsive Design:**
- CSS adaptativo con breakpoints para móviles (≤576px), tablets (≤768px) y desktop
- Sidebar colapsable con overlay en móviles  
- Tablas responsive con scroll horizontal
- Formularios optimizados para dispositivos táctiles
- Gestos táctiles (swipe) para navegación
- Botones y elementos UI redimensionados automáticamente

**🚀 Progressive Web App (PWA):**
- ✅ `manifest.json` completo con iconos y shortcuts
- ✅ Service Worker con cache estratégico y funcionalidad offline
- ✅ Instalación como app nativa con botón automático
- ✅ Notificaciones push preparadas
- ✅ Funcionalidad offline para uso sin internet

**📄 Generación de PDF:**
- ✅ Clase `PDFGenerator` con plantillas HTML profesionales
- ✅ Cotizaciones en PDF con diseño corporativo
- ✅ Incluye precios de costo y venta con cálculos de utilidad
- ✅ Diseño responsive para impresión perfecta
- ✅ Auto-impresión opcional

**📧 Sistema de Email SMTP:**
- ✅ Clase `EmailSender` con soporte SMTP completo
- ✅ Plantillas HTML profesionales para emails
- ✅ Envío de cotizaciones por correo con archivos adjuntos
- ✅ Mensajes personalizables y configuración empresarial
- ✅ Log completo de envíos para seguimiento

## 📝 Registro de Cambios

### ✅ Versión 1.0.0 (COMPLETA - Octubre 2025)
- ✅ **Arquitectura MVC**: Implementación completa del patrón MVC
- ✅ **Módulos Completos**: Clientes, artículos, paquetes, cotizaciones
- ✅ **Sistema de Cotizaciones**: Funcional con cálculos automáticos
- ✅ **Dashboard Avanzado**: Estadísticas en tiempo real y métricas
- ✅ **Responsive Design**: Completamente adaptativo para todos los dispositivos
- ✅ **Progressive Web App**: PWA completa con offline, instalación y cache
- ✅ **Generación PDF**: Sistema profesional de generación de cotizaciones
- ✅ **Sistema Email**: SMTP completo para envío de cotizaciones
- ✅ **Seguridad**: Validaciones, sanitización y protección contra vulnerabilidades
- ✅ **Autenticación**: Sistema de login/logout con sesiones seguras
- ✅ **UI/UX Moderna**: Bootstrap 5, animaciones, iconos y diseño profesional

### 🚀 Funcionalidades Implementadas al 100%
**📊 Dashboard Interactivo**
- Estadísticas en tiempo real de clientes, artículos y cotizaciones
- Gráficos visuales de utilidades y tendencias
- Accesos rápidos a todas las funcionalidades
- Actividad reciente y notificaciones

**👥 Gestión de Clientes**
- CRUD completo con validaciones
- Búsqueda avanzada y filtros múltiples  
- Historial comercial completo por cliente
- Contacto directo (email, teléfono, WhatsApp)

**📦 Sistema de Artículos**
- Gestión completa de inventario con stock
- Cálculo automático de utilidades por artículo
- Control de stock bajo con alertas
- Búsqueda inteligente y filtros avanzados

**📋 Módulo de Paquetes**
- Creación de paquetes con múltiples artículos
- Cálculo automático de precios totales
- Verificación de disponibilidad de stock
- Vista de tarjetas moderna y responsive

**💰 Sistema de Cotizaciones**
- Generación automática con códigos únicos
- Cálculo dual: precios de costo y venta
- Utilidades calculadas en tiempo real
- Estados de cotización (activa, vencida, aprobada)
- Exportación a PDF profesional
- Envío por email con plantillas personalizadas

**📱 Tecnologías Implementadas**
- **Backend**: PHP 8+ con patrón MVC
- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript ES6+
- **Base de Datos**: MySQL 8+ con consultas optimizadas
- **PWA**: Service Worker, Cache API, Web App Manifest
- **Responsive**: CSS Grid, Flexbox, Media Queries
- **Seguridad**: Prepared Statements, Input Sanitization, Session Management

### Próximas Mejoras (v1.1.0)
- 🔄 Integración con APIs de facturación electrónica
- 📊 Reportes avanzados con gráficos interactivos
- 👤 Sistema de roles y permisos multi-usuario
- 🔄 Sincronización con sistemas ERP externos
- 📲 Notificaciones push en tiempo real
- 🔐 Autenticación de dos factores (2FA)

## 👥 Contribución

### Estructura de Desarrollo
1. **Fork** el proyecto
2. **Crear rama** para nueva funcionalidad
3. **Implementar** cambios con estándares de código
4. **Documentar** cambios realizados
5. **Pull Request** con descripción detallada

### Estándares de Código
- **PSR-12**: Estándar de codificación PHP
- **Comentarios**: Documentación en español
- **Nomenclatura**: camelCase para métodos, snake_case para BD
- **Validación**: Siempre validar entrada de datos

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver archivo `LICENSE` para más detalles.

## 📞 Contacto y Soporte

Para soporte técnico o consultas sobre el sistema:
- **Documentación**: Revisar este README completo
- **Issues**: Utilizar el sistema de issues del repositorio
- **Email**: [Configurar email de contacto]

---

**Sistema de Cotizaciones Empresariales** - Automatizando la gestión comercial con tecnología moderna y eficiente.

*Desarrollado con ❤️ usando PHP, MySQL, Bootstrap y las mejores prácticas de desarrollo web.*