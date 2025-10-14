            </main>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="public/js/main.js"></script>
    
    <!-- PWA Installation Script -->
    <script>
        // Registro del Service Worker para PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/mod_cotizacion/public/sw.js')
                    .then(function(registration) {
                        console.log('[PWA] Service Worker registrado correctamente:', registration.scope);
                        
                        // Verificar actualizaciones
                        registration.addEventListener('updatefound', function() {
                            const newWorker = registration.installing;
                            newWorker.addEventListener('statechange', function() {
                                if (newWorker.state === 'installed') {
                                    if (navigator.serviceWorker.controller) {
                                        // Nueva versión disponible
                                        showUpdateNotification();
                                    }
                                }
                            });
                        });
                    })
                    .catch(function(error) {
                        console.log('[PWA] Error al registrar Service Worker:', error);
                    });
            });
        }
        
        // Manejar instalación de PWA
        let deferredPrompt;
        
        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            deferredPrompt = e;
            showInstallButton();
        });
        
        function showInstallButton() {
            const installButton = document.createElement('button');
            installButton.className = 'btn btn-outline-primary btn-sm position-fixed';
            installButton.style.cssText = 'bottom: 20px; right: 20px; z-index: 1000; border-radius: 50px; padding: 8px 16px;';
            installButton.innerHTML = '<i class="fas fa-download me-1"></i> Instalar App';
            installButton.addEventListener('click', function() {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then(function(choiceResult) {
                        if (choiceResult.outcome === 'accepted') {
                            console.log('[PWA] Usuario aceptó instalar la app');
                        }
                        deferredPrompt = null;
                        installButton.remove();
                    });
                }
            });
            document.body.appendChild(installButton);
            
            // Auto-ocultar después de 10 segundos
            setTimeout(function() {
                if (installButton.parentNode) {
                    installButton.remove();
                }
            }, 10000);
        }
        
        function showUpdateNotification() {
            const notification = document.createElement('div');
            notification.className = 'alert alert-info alert-dismissible fade show position-fixed';
            notification.style.cssText = 'top: 80px; right: 20px; z-index: 1050; min-width: 300px;';
            notification.innerHTML = `
                <i class="fas fa-sync-alt me-2"></i>
                Nueva versión disponible
                <button type="button" class="btn btn-sm btn-outline-info ms-2" onclick="updateApp()">
                    Actualizar
                </button>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(notification);
        }
        
        function updateApp() {
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.getRegistration().then(function(registration) {
                    if (registration && registration.waiting) {
                        registration.waiting.postMessage({type: 'SKIP_WAITING'});
                        window.location.reload();
                    }
                });
            }
        }
        
        // Detectar instalación exitosa
        window.addEventListener('appinstalled', function(evt) {
            console.log('[PWA] App instalada correctamente');
            if (typeof Utils !== 'undefined' && Utils.showToast) {
                Utils.showToast('App instalada correctamente', 'success');
            }
        });
        
        // Detectar modo standalone (PWA instalada)
        if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone) {
            document.body.classList.add('pwa-mode');
            console.log('[PWA] Ejecutándose en modo PWA');
        }
    </script>
    
    <script>
        // Confirmar eliminación
        function confirmarEliminacion(mensaje = '¿Está seguro de que desea eliminar este elemento?') {
            return confirm(mensaje);
        }
        
        // Formatear números como moneda
        function formatCurrency(amount) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP'
            }).format(amount);
        }
        
        // Auto-ocultar alertas después de 5 segundos
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert:not(.alert-permanent)').fadeOut('slow');
            }, 5000);
        });
        
        // Mejorar accesibilidad en PWA
        if ('ontouchstart' in window) {
            document.body.classList.add('touch-device');
        }
        
        // Función para toggle de sidebar en móviles
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (sidebar) {
                sidebar.classList.toggle('show');
                
                // Crear o remover overlay para móviles
                if (sidebar.classList.contains('show')) {
                    if (!overlay) {
                        const newOverlay = document.createElement('div');
                        newOverlay.id = 'sidebar-overlay';
                        newOverlay.className = 'sidebar-overlay d-md-none';
                        newOverlay.onclick = toggleSidebar;
                        document.body.appendChild(newOverlay);
                    }
                    document.body.style.overflow = 'hidden';
                } else {
                    if (overlay) {
                        overlay.remove();
                    }
                    document.body.style.overflow = '';
                }
            }
        }
        
        // Cerrar sidebar al hacer clic fuera en móviles
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            
            if (window.innerWidth <= 768 && sidebar && sidebar.classList.contains('show')) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    toggleSidebar();
                }
            }
        });
        
        // Cerrar sidebar al redimensionar ventana
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (window.innerWidth > 768 && sidebar && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                if (overlay) {
                    overlay.remove();
                }
                document.body.style.overflow = '';
            }
        });
    </script>
</body>
</html>