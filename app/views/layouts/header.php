<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo isset($title) ? $title . ' - ' : ''; ?>Sistema de Cotizaciones</title>
    
    <!-- PWA Meta Tags -->
    <meta name="description" content="Sistema integral de gestión de cotizaciones empresariales">
    <meta name="theme-color" content="#007bff">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Cotizaciones">
    <meta name="msapplication-TileColor" content="#007bff">
    <meta name="msapplication-TileImage" content="public/images/icons/ms-icon-144x144.png">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="public/manifest.json">
    
    <!-- PWA Icons -->
    <link rel="apple-touch-icon" sizes="57x57" href="public/images/icons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="public/images/icons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="public/images/icons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="public/images/icons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="public/images/icons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="public/images/icons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="public/images/icons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="public/images/icons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="public/images/icons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="public/images/icons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="public/images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="public/images/icons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/images/icons/favicon-16x16.png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="public/css/style.css" rel="stylesheet">
    
    <!-- Preload critical resources -->
    <link rel="preload" href="public/js/main.js" as="script">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" as="script">
    
    <!-- Estilos ya están en style.css -->
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <!-- Botón toggle para sidebar en móviles -->
            <button class="btn btn-outline-light sidebar-toggle d-md-none me-2" type="button" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-calculator me-2"></i>
                Sistema de Cotizaciones
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>
                            <?php echo isset($_SESSION['nombre_completo']) ? $_SESSION['nombre_completo'] : 'Usuario'; ?>
                            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                                <span class="badge bg-warning text-dark ms-1">Admin</span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <h6 class="dropdown-header">
                                    <i class="fas fa-user-circle me-2"></i>
                                    <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>
                                </h6>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="index.php?controller=auth&action=profile">
                                <i class="fas fa-user-edit me-2"></i>Mi Perfil
                            </a></li>
                            <li><a class="dropdown-item" href="index.php?controller=auth&action=changePassword">
                                <i class="fas fa-key me-2"></i>Cambiar Contraseña
                            </a></li>
                            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="index.php?controller=user&action=index">
                                    <i class="fas fa-users-cog me-2"></i>Gestión de Usuarios
                                </a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="index.php?controller=auth&action=logout">
                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar" id="sidebar">
                <div class="position-sticky pt-3">
                    <!-- Botón cerrar para móviles -->
                    <div class="d-md-none text-end p-2">
                        <button class="btn btn-outline-light btn-sm" onclick="toggleSidebar()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo (!isset($_GET['controller']) || $_GET['controller'] == 'home') ? 'active' : ''; ?>" 
                               href="index.php">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'cliente') ? 'active' : ''; ?>" 
                               href="index.php?controller=cliente&action=index">
                                <i class="fas fa-users me-2"></i>
                                Clientes
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'articulo') ? 'active' : ''; ?>" 
                               href="index.php?controller=articulo&action=index">
                                <i class="fas fa-box me-2"></i>
                                Artículos
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'paquete') ? 'active' : ''; ?>" 
                               href="index.php?controller=paquete&action=index">
                                <i class="fas fa-boxes me-2"></i>
                                Paquetes
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'cotizacion') ? 'active' : ''; ?>" 
                               href="index.php?controller=cotizacion&action=index">
                                <i class="fas fa-file-invoice-dollar me-2"></i>
                                Cotizaciones
                            </a>
                        </li>
                        
                        <hr class="text-white">
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'reporte') ? 'active' : ''; ?>" 
                               href="index.php?controller=reporte&action=index">
                                <i class="fas fa-chart-line me-2"></i>
                                Reportes e Informes
                            </a>
                        </li>
                        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'user') ? 'active' : ''; ?>" 
                               href="index.php?controller=user&action=index">
                                <i class="fas fa-users-cog me-2"></i>
                                Gestión de Usuarios
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <?php
                // Mostrar alertas si existen
                $alert = Helper::getAlert();
                if ($alert): ?>
                    <div class="alert alert-<?php echo $alert['type'] == 'error' ? 'danger' : $alert['type']; ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-<?php echo $alert['type'] == 'success' ? 'check-circle' : ($alert['type'] == 'error' ? 'exclamation-triangle' : 'info-circle'); ?> me-2"></i>
                        <?php echo $alert['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>