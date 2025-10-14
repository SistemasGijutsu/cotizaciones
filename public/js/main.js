/**
 * JavaScript principal para el Sistema de Cotizaciones Empresariales
 * Funcionalidades interactivas y validaciones del lado cliente
 */

// Configuración global
const SistemaCotizaciones = {
    baseUrl: window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, ''),
    
    // Configuración de AJAX
    ajax: {
        timeout: 30000,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    },
    
    // Mensajes del sistema
    messages: {
        confirmDelete: '¿Está seguro de que desea eliminar este elemento?',
        loading: 'Cargando...',
        error: 'Ha ocurrido un error. Por favor, inténtelo nuevamente.',
        success: 'Operación completada exitosamente.',
        noResults: 'No se encontraron resultados.',
        required: 'Este campo es obligatorio.',
        invalidEmail: 'Por favor, ingrese un email válido.',
        invalidNumber: 'Por favor, ingrese un número válido.'
    }
};

// Utilidades generales
const Utils = {
    
    /**
     * Formatear número como moneda
     */
    formatCurrency: function(amount) {
        return new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    },
    
    /**
     * Formatear fecha
     */
    formatDate: function(date, format = 'dd/mm/yyyy') {
        const d = new Date(date);
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();
        
        switch(format) {
            case 'dd/mm/yyyy':
                return `${day}/${month}/${year}`;
            case 'yyyy-mm-dd':
                return `${year}-${month}-${day}`;
            default:
                return `${day}/${month}/${year}`;
        }
    },
    
    /**
     * Validar email
     */
    validateEmail: function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },
    
    /**
     * Validar número
     */
    validateNumber: function(number) {
        return !isNaN(number) && isFinite(number);
    },
    
    /**
     * Mostrar notificación toast
     */
    showToast: function(message, type = 'info') {
        // Crear elemento toast si no existe
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '1055';
            document.body.appendChild(toastContainer);
        }
        
        // Crear toast
        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${type === 'error' ? 'danger' : type}" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${this.getToastIcon(type)} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        // Mostrar toast
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 5000
        });
        toast.show();
        
        // Limpiar después de ocultar
        toastElement.addEventListener('hidden.bs.toast', function() {
            toastElement.remove();
        });
    },
    
    /**
     * Obtener icono para toast según el tipo
     */
    getToastIcon: function(type) {
        const icons = {
            'success': 'check-circle',
            'error': 'exclamation-triangle',
            'warning': 'exclamation-triangle',
            'info': 'info-circle'
        };
        return icons[type] || 'info-circle';
    },
    
    /**
     * Mostrar/ocultar spinner de carga
     */
    toggleLoading: function(element, show = true) {
        if (show) {
            element.classList.add('loading');
            const originalText = element.innerHTML;
            element.setAttribute('data-original-text', originalText);
            element.innerHTML = '<span class="spinner me-2"></span>' + SistemaCotizaciones.messages.loading;
            element.disabled = true;
        } else {
            element.classList.remove('loading');
            const originalText = element.getAttribute('data-original-text');
            if (originalText) {
                element.innerHTML = originalText;
                element.removeAttribute('data-original-text');
            }
            element.disabled = false;
        }
    }
};

// Funcionalidades específicas del sistema
const CotizacionesApp = {
    
    /**
     * Inicializar aplicación
     */
    init: function() {
        this.bindEvents();
        this.initializePlugins();
        this.initResponsive();
        this.checkConnection();
    },
    
    /**
     * Inicializar funcionalidades responsive
     */
    initResponsive: function() {
        this.setupSidebarToggle();
        this.setupResponsiveTables();
        this.setupTouchGestures();
        this.handleOrientationChange();
    },
    
    /**
     * Configurar toggle del sidebar para móviles
     */
    setupSidebarToggle: function() {
        // Crear botón toggle si no existe
        const navbar = document.querySelector('.navbar .container-fluid');
        if (navbar && !document.querySelector('.sidebar-toggle')) {
            const toggleButton = document.createElement('button');
            toggleButton.className = 'sidebar-toggle';
            toggleButton.innerHTML = '<i class="fas fa-bars"></i>';
            toggleButton.setAttribute('aria-label', 'Toggle sidebar');
            navbar.insertBefore(toggleButton, navbar.firstChild);
        }
        
        // Crear overlay si no existe
        if (!document.querySelector('.sidebar-overlay')) {
            const overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            document.body.appendChild(overlay);
        }
        
        // Event listeners
        document.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            if (e.target.matches('.sidebar-toggle') || e.target.closest('.sidebar-toggle')) {
                e.preventDefault();
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
                document.body.classList.toggle('sidebar-open');
            }
            
            if (e.target.matches('.sidebar-overlay')) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.classList.remove('sidebar-open');
            }
        });
        
        // Cerrar sidebar al hacer clic en enlaces (en móviles)
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768 && e.target.closest('.sidebar .nav-link')) {
                const sidebar = document.querySelector('.sidebar');
                const overlay = document.querySelector('.sidebar-overlay');
                
                setTimeout(function() {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                    document.body.classList.remove('sidebar-open');
                }, 250);
            }
        });
    },
    
    /**
     * Configurar tablas responsive
     */
    setupResponsiveTables: function() {
        const tables = document.querySelectorAll('table:not(.table-responsive table)');
        tables.forEach(function(table) {
            if (!table.closest('.table-responsive')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'table-responsive';
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            }
        });
        
        // Agregar atributos de datos para tablas en móviles
        this.enhanceTablesForMobile();
    },
    
    /**
     * Mejorar tablas para móviles
     */
    enhanceTablesForMobile: function() {
        const tables = document.querySelectorAll('.table-responsive table');
        tables.forEach(function(table) {
            const headers = table.querySelectorAll('thead th');
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(function(row) {
                const cells = row.querySelectorAll('td');
                cells.forEach(function(cell, index) {
                    if (headers[index]) {
                        cell.setAttribute('data-label', headers[index].textContent.trim());
                    }
                });
            });
        });
    },
    
    /**
     * Configurar gestos táctiles
     */
    setupTouchGestures: function() {
        let startX = 0;
        let startY = 0;
        
        document.addEventListener('touchstart', function(e) {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        });
        
        document.addEventListener('touchmove', function(e) {
            if (!startX || !startY) return;
            
            const diffX = startX - e.touches[0].clientX;
            const diffY = startY - e.touches[0].clientY;
            
            // Swipe horizontal
            if (Math.abs(diffX) > Math.abs(diffY)) {
                const sidebar = document.querySelector('.sidebar');
                const overlay = document.querySelector('.sidebar-overlay');
                
                if (diffX > 50 && sidebar.classList.contains('show')) {
                    // Swipe left - cerrar sidebar
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                    document.body.classList.remove('sidebar-open');
                } else if (diffX < -50 && !sidebar.classList.contains('show') && startX < 50) {
                    // Swipe right desde el borde - abrir sidebar
                    sidebar.classList.add('show');
                    overlay.classList.add('show');
                    document.body.classList.add('sidebar-open');
                }
            }
            
            startX = 0;
            startY = 0;
        });
    },
    
    /**
     * Manejar cambios de orientación
     */
    handleOrientationChange: function() {
        window.addEventListener('orientationchange', function() {
            setTimeout(function() {
                // Reajustar elementos después del cambio de orientación
                const sidebar = document.querySelector('.sidebar');
                const overlay = document.querySelector('.sidebar-overlay');
                
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                    document.body.classList.remove('sidebar-open');
                }
                
                // Recalcular alturas si es necesario
                CotizacionesApp.adjustViewport();
            }, 100);
        });
        
        window.addEventListener('resize', function() {
            CotizacionesApp.adjustViewport();
        });
    },
    
    /**
     * Ajustar viewport
     */
    adjustViewport: function() {
        // Ajustar altura del viewport en móviles
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', vh + 'px');
        
        // Cerrar sidebar en desktop
        if (window.innerWidth > 768) {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            if (sidebar && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.classList.remove('sidebar-open');
            }
        }
    },
    
    /**
     * Vincular eventos
     */
    bindEvents: function() {
        document.addEventListener('DOMContentLoaded', function() {
            CotizacionesApp.onDOMReady();
        });
        
        // Confirmación de eliminación
        document.addEventListener('click', function(e) {
            if (e.target.matches('[data-confirm-delete]')) {
                e.preventDefault();
                const message = e.target.getAttribute('data-confirm-delete') || 
                               SistemaCotizaciones.messages.confirmDelete;
                
                if (confirm(message)) {
                    window.location.href = e.target.href;
                }
            }
        });
        
        // Auto-ocultar alertas
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    setTimeout(function() {
                        try {
                            bsAlert.close();
                        } catch (e) {
                            alert.style.display = 'none';
                        }
                    }, 5000);
                });
            }, 1000);
        });
    },
    
    /**
     * Acciones cuando el DOM está listo
     */
    onDOMReady: function() {
        // Inicializar tooltips
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(function(tooltip) {
            new bootstrap.Tooltip(tooltip);
        });
        
        // Inicializar popovers
        const popovers = document.querySelectorAll('[data-bs-toggle="popover"]');
        popovers.forEach(function(popover) {
            new bootstrap.Popover(popover);
        });
        
        // Animación de entrada para elementos
        const elementsToAnimate = document.querySelectorAll('.card, .stats-card');
        elementsToAnimate.forEach(function(element, index) {
            setTimeout(function() {
                element.classList.add('fade-in');
            }, index * 100);
        });
    },
    
    /**
     * Inicializar plugins adicionales
     */
    initializePlugins: function() {
        // Configurar Select2 si está disponible
        if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
            jQuery('.select2').select2({
                theme: 'bootstrap-5',
                language: 'es'
            });
        }
        
        // Configurar DataTables si está disponible
        if (typeof jQuery !== 'undefined' && jQuery.fn.DataTable) {
            jQuery('.datatable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                responsive: true,
                pageLength: 25,
                dom: 'Bfrtip',
                buttons: ['excel', 'pdf', 'print']
            });
        }
    },
    
    /**
     * Verificar conexión con el servidor
     */
    checkConnection: function() {
        // Verificación simple de conectividad
        const img = new Image();
        img.onload = function() {
            document.body.classList.add('connected');
        };
        img.onerror = function() {
            document.body.classList.add('disconnected');
            Utils.showToast('Problemas de conectividad detectados', 'warning');
        };
        img.src = SistemaCotizaciones.baseUrl + '/public/images/pixel.gif?' + Date.now();
    }
};

// Funcionalidades específicas de formularios
const FormHandler = {
    
    /**
     * Validar formulario
     */
    validate: function(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(function(field) {
            if (!field.value.trim()) {
                FormHandler.showFieldError(field, SistemaCotizaciones.messages.required);
                isValid = false;
            } else {
                FormHandler.hideFieldError(field);
                
                // Validaciones específicas
                if (field.type === 'email' && !Utils.validateEmail(field.value)) {
                    FormHandler.showFieldError(field, SistemaCotizaciones.messages.invalidEmail);
                    isValid = false;
                }
                
                if (field.type === 'number' && !Utils.validateNumber(field.value)) {
                    FormHandler.showFieldError(field, SistemaCotizaciones.messages.invalidNumber);
                    isValid = false;
                }
            }
        });
        
        return isValid;
    },
    
    /**
     * Mostrar error en campo
     */
    showFieldError: function(field, message) {
        field.classList.add('is-invalid');
        
        let errorElement = field.parentNode.querySelector('.invalid-feedback');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'invalid-feedback';
            field.parentNode.appendChild(errorElement);
        }
        errorElement.textContent = message;
    },
    
    /**
     * Ocultar error en campo
     */
    hideFieldError: function(field) {
        field.classList.remove('is-invalid');
        const errorElement = field.parentNode.querySelector('.invalid-feedback');
        if (errorElement) {
            errorElement.remove();
        }
    },
    
    /**
     * Enviar formulario via AJAX
     */
    submitAjax: function(form, callback) {
        if (!this.validate(form)) {
            return false;
        }
        
        const formData = new FormData(form);
        const submitButton = form.querySelector('[type="submit"]');
        
        Utils.toggleLoading(submitButton, true);
        
        fetch(form.action, {
            method: form.method || 'POST',
            body: formData,
            headers: SistemaCotizaciones.ajax.headers
        })
        .then(response => response.json())
        .then(data => {
            Utils.toggleLoading(submitButton, false);
            if (callback) callback(data);
        })
        .catch(error => {
            Utils.toggleLoading(submitButton, false);
            Utils.showToast(SistemaCotizaciones.messages.error, 'error');
            console.error('Error:', error);
        });
        
        return false;
    }
};

// Funcionalidades específicas de cotizaciones
const CotizacionManager = {
    
    selectedItems: [],
    
    /**
     * Agregar artículo a la cotización
     */
    addItem: function(articulo) {
        const existingIndex = this.selectedItems.findIndex(item => item.id === articulo.id);
        
        if (existingIndex >= 0) {
            this.selectedItems[existingIndex].cantidad++;
        } else {
            this.selectedItems.push({
                id: articulo.id,
                nombre: articulo.nombre,
                precio_costo: parseFloat(articulo.precio_costo),
                precio_venta: parseFloat(articulo.precio_venta),
                cantidad: 1
            });
        }
        
        this.updateDisplay();
        this.calculateTotals();
    },
    
    /**
     * Remover artículo de la cotización
     */
    removeItem: function(index) {
        this.selectedItems.splice(index, 1);
        this.updateDisplay();
        this.calculateTotals();
    },
    
    /**
     * Actualizar cantidad de artículo
     */
    updateQuantity: function(index, cantidad) {
        if (cantidad <= 0) {
            this.removeItem(index);
        } else {
            this.selectedItems[index].cantidad = parseInt(cantidad);
            this.calculateTotals();
        }
    },
    
    /**
     * Actualizar visualización de items
     */
    updateDisplay: function() {
        const container = document.getElementById('selected-items');
        if (!container) return;
        
        if (this.selectedItems.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>No hay artículos seleccionados</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        this.selectedItems.forEach((item, index) => {
            html += `
                <div class="card mb-2">
                    <div class="card-body py-2">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <strong>${item.nombre}</strong>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control form-control-sm" 
                                       value="${item.cantidad}" min="1"
                                       onchange="CotizacionManager.updateQuantity(${index}, this.value)">
                            </div>
                            <div class="col-md-2">
                                ${Utils.formatCurrency(item.precio_venta)}
                            </div>
                            <div class="col-md-2">
                                ${Utils.formatCurrency(item.precio_venta * item.cantidad)}
                            </div>
                            <div class="col-md-2 text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="CotizacionManager.removeItem(${index})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    },
    
    /**
     * Calcular totales
     */
    calculateTotals: function() {
        let totalCosto = 0;
        let totalVenta = 0;
        
        this.selectedItems.forEach(item => {
            totalCosto += item.precio_costo * item.cantidad;
            totalVenta += item.precio_venta * item.cantidad;
        });
        
        const utilidad = totalVenta - totalCosto;
        const utilidadPorcentaje = totalCosto > 0 ? (utilidad / totalCosto) * 100 : 0;
        
        // Actualizar elementos en la interfaz
        const elements = {
            'total-costo': Utils.formatCurrency(totalCosto),
            'total-venta': Utils.formatCurrency(totalVenta),
            'utilidad': Utils.formatCurrency(utilidad),
            'utilidad-porcentaje': utilidadPorcentaje.toFixed(2) + '%'
        };
        
        Object.keys(elements).forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = elements[id];
            }
        });
    }
};

// Inicializar aplicación
CotizacionesApp.init();

// Funciones globales para compatibilidad
function confirmarEliminacion(mensaje) {
    return confirm(mensaje || SistemaCotizaciones.messages.confirmDelete);
}

function formatCurrency(amount) {
    return Utils.formatCurrency(amount);
}

function showToast(message, type) {
    Utils.showToast(message, type);
}