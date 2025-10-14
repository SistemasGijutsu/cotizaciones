/**
 * Service Worker para Sistema de Cotizaciones
 * Implementa cache estratégico y funcionalidad offline
 */

const CACHE_NAME = 'cotizaciones-v1.0.0';
const CACHE_VERSION = 1;

// Recursos críticos para cachear
const CRITICAL_RESOURCES = [
    '/mod_cotizacion/',
    '/mod_cotizacion/index.php',
    '/mod_cotizacion/public/css/style.css',
    '/mod_cotizacion/public/js/main.js',
    '/mod_cotizacion/public/manifest.json',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'
];

// Recursos de la aplicación para cachear
const APP_RESOURCES = [
    '/mod_cotizacion/index.php?controller=home',
    '/mod_cotizacion/index.php?controller=cliente&action=index',
    '/mod_cotizacion/index.php?controller=articulo&action=index',
    '/mod_cotizacion/index.php?controller=paquete&action=index',
    '/mod_cotizacion/index.php?controller=cotizacion&action=index'
];

// Recursos que no deben cachearse
const EXCLUDED_RESOURCES = [
    '/mod_cotizacion/index.php?controller=auth&action=logout',
    'chrome-extension://',
    'analytics',
    'gtag'
];

/**
 * Evento de instalación del Service Worker
 */
self.addEventListener('install', function(event) {
    console.log('[SW] Instalando Service Worker...');
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                console.log('[SW] Cacheando recursos críticos...');
                return cache.addAll(CRITICAL_RESOURCES);
            })
            .then(function() {
                console.log('[SW] Service Worker instalado correctamente');
                return self.skipWaiting();
            })
            .catch(function(error) {
                console.error('[SW] Error durante la instalación:', error);
            })
    );
});

/**
 * Evento de activación del Service Worker
 */
self.addEventListener('activate', function(event) {
    console.log('[SW] Activando Service Worker...');
    
    event.waitUntil(
        caches.keys()
            .then(function(cacheNames) {
                return Promise.all(
                    cacheNames.map(function(cacheName) {
                        if (cacheName !== CACHE_NAME) {
                            console.log('[SW] Eliminando cache obsoleto:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(function() {
                console.log('[SW] Service Worker activado');
                return self.clients.claim();
            })
    );
});

/**
 * Estrategia de cache para peticiones
 */
self.addEventListener('fetch', function(event) {
    const request = event.request;
    const url = new URL(request.url);
    
    // Excluir recursos específicos
    if (EXCLUDED_RESOURCES.some(excluded => request.url.includes(excluded))) {
        return;
    }
    
    // Solo cachear GET requests
    if (request.method !== 'GET') {
        return;
    }
    
    event.respondWith(
        caches.match(request)
            .then(function(cachedResponse) {
                // Si está en cache, devolverlo
                if (cachedResponse) {
                    // Para recursos de la app, hacer fetch en background para actualizar cache
                    if (isAppResource(request.url)) {
                        fetchAndCache(request);
                    }
                    return cachedResponse;
                }
                
                // Si no está en cache, hacer fetch y cachear si es apropiado
                return fetchAndCache(request);
            })
            .catch(function() {
                // En caso de error, mostrar página offline si es una navegación
                if (request.mode === 'navigate') {
                    return getOfflinePage();
                }
                
                // Para otros recursos, devolver respuesta vacía
                return new Response('', {
                    status: 408,
                    statusText: 'Request timeout'
                });
            })
    );
});

/**
 * Verificar si un recurso es de la aplicación
 */
function isAppResource(url) {
    return url.includes('/mod_cotizacion/') && 
           (url.includes('.php') || url.includes('.css') || url.includes('.js'));
}

/**
 * Hacer fetch y cachear respuesta
 */
function fetchAndCache(request) {
    return fetch(request)
        .then(function(response) {
            // Solo cachear respuestas exitosas
            if (!response || response.status !== 200 || response.type !== 'basic') {
                return response;
            }
            
            // Clonar respuesta para cachear
            const responseToCache = response.clone();
            
            caches.open(CACHE_NAME)
                .then(function(cache) {
                    cache.put(request, responseToCache);
                });
            
            return response;
        });
}

/**
 * Obtener página offline
 */
function getOfflinePage() {
    return caches.match('/mod_cotizacion/public/offline.html')
        .then(function(cachedResponse) {
            if (cachedResponse) {
                return cachedResponse;
            }
            
            // Crear página offline básica
            return new Response(`
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Sin conexión - Sistema de Cotizaciones</title>
                    <style>
                        body {
                            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            margin: 0;
                            padding: 0;
                            min-height: 100vh;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: white;
                        }
                        .offline-container {
                            text-align: center;
                            padding: 2rem;
                            max-width: 400px;
                        }
                        .offline-icon {
                            font-size: 4rem;
                            margin-bottom: 1rem;
                            opacity: 0.8;
                        }
                        .offline-title {
                            font-size: 1.5rem;
                            font-weight: bold;
                            margin-bottom: 1rem;
                        }
                        .offline-message {
                            font-size: 1rem;
                            line-height: 1.5;
                            opacity: 0.9;
                            margin-bottom: 2rem;
                        }
                        .retry-btn {
                            background: rgba(255, 255, 255, 0.2);
                            border: 2px solid rgba(255, 255, 255, 0.3);
                            color: white;
                            padding: 0.75rem 1.5rem;
                            border-radius: 25px;
                            cursor: pointer;
                            font-size: 1rem;
                            transition: all 0.3s ease;
                        }
                        .retry-btn:hover {
                            background: rgba(255, 255, 255, 0.3);
                            border-color: rgba(255, 255, 255, 0.5);
                        }
                    </style>
                </head>
                <body>
                    <div class="offline-container">
                        <div class="offline-icon">📱</div>
                        <div class="offline-title">Sin conexión a internet</div>
                        <div class="offline-message">
                            No se puede conectar al servidor. Por favor, verifica tu conexión a internet e inténtalo nuevamente.
                        </div>
                        <button class="retry-btn" onclick="window.location.reload()">
                            Reintentar
                        </button>
                    </div>
                </body>
                </html>
            `, {
                headers: {
                    'Content-Type': 'text/html'
                }
            });
        });
}

/**
 * Manejar mensajes del cliente
 */
self.addEventListener('message', function(event) {
    const data = event.data;
    
    switch (data.type) {
        case 'SKIP_WAITING':
            self.skipWaiting();
            break;
            
        case 'CACHE_URLS':
            if (data.urls && Array.isArray(data.urls)) {
                caches.open(CACHE_NAME)
                    .then(function(cache) {
                        return cache.addAll(data.urls);
                    })
                    .then(function() {
                        event.ports[0].postMessage({
                            type: 'CACHE_COMPLETE',
                            success: true
                        });
                    })
                    .catch(function(error) {
                        event.ports[0].postMessage({
                            type: 'CACHE_COMPLETE',
                            success: false,
                            error: error.message
                        });
                    });
            }
            break;
            
        case 'CLEAR_CACHE':
            caches.delete(CACHE_NAME)
                .then(function() {
                    event.ports[0].postMessage({
                        type: 'CACHE_CLEARED',
                        success: true
                    });
                })
                .catch(function(error) {
                    event.ports[0].postMessage({
                        type: 'CACHE_CLEARED',
                        success: false,
                        error: error.message
                    });
                });
            break;
    }
});

/**
 * Manejar sincronización en background
 */
self.addEventListener('sync', function(event) {
    if (event.tag === 'sync-cotizaciones') {
        event.waitUntil(syncCotizaciones());
    }
});

/**
 * Sincronizar cotizaciones pendientes
 */
function syncCotizaciones() {
    return new Promise(function(resolve) {
        // Aquí implementarías la lógica para sincronizar datos pendientes
        console.log('[SW] Sincronizando cotizaciones...');
        
        // Por ahora solo resolvemos la promesa
        setTimeout(resolve, 1000);
    });
}

/**
 * Manejar notificaciones push
 */
self.addEventListener('push', function(event) {
    if (!event.data) {
        return;
    }
    
    const data = event.data.json();
    const options = {
        body: data.body || 'Nueva notificación del sistema',
        icon: '/mod_cotizacion/public/images/icons/android-icon-192x192.png',
        badge: '/mod_cotizacion/public/images/icons/badge-72x72.png',
        vibrate: [200, 100, 200],
        data: data,
        actions: [
            {
                action: 'view',
                title: 'Ver',
                icon: '/mod_cotizacion/public/images/icons/action-view.png'
            },
            {
                action: 'dismiss',
                title: 'Descartar',
                icon: '/mod_cotizacion/public/images/icons/action-dismiss.png'
            }
        ]
    };
    
    event.waitUntil(
        self.registration.showNotification(data.title || 'Sistema de Cotizaciones', options)
    );
});

/**
 * Manejar clicks en notificaciones
 */
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    
    if (event.action === 'view') {
        event.waitUntil(
            clients.openWindow('/mod_cotizacion/')
        );
    }
});

console.log('[SW] Service Worker registrado correctamente');