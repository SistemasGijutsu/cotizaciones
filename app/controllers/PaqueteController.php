<?php
/**
 * Controlador Paquete para el Sistema de Cotizaciones
 * Maneja todas las operaciones relacionadas con paquetes de artículos
 */

require_once 'Controller.php';
require_once __DIR__ . '/../models/Paquete.php';
require_once __DIR__ . '/../models/Articulo.php';

class PaqueteController extends Controller {
    private $paqueteModel;
    private $articuloModel;
    
    public function __construct() {
        $this->paqueteModel = new Paquete();
        $this->articuloModel = new Articulo();
    }
    
    /**
     * Mostrar lista de paquetes
     */
    public function index() {
        $paquetes = $this->paqueteModel->getPaquetesWithPrices();
        
        $this->loadView('paquetes/index', [
            'paquetes' => $paquetes
        ]);
    }
    
    /**
     * Mostrar formulario para crear paquete
     */
    public function create() {
        if ($this->isPost()) {
            $this->store();
            return;
        }
        
        $articulos = $this->articuloModel->getArticulosDisponibles();
        
        $this->loadView('paquetes/create', [
            'articulos' => $articulos
        ]);
    }
    
    /**
     * Guardar nuevo paquete
     */
    public function store() {
        $paqueteData = $this->getPostData(['nombre', 'descripcion']);
        
        // Obtener artículos del paquete
        $articulos = [];
        if (isset($_POST['articulos']) && is_array($_POST['articulos'])) {
            foreach ($_POST['articulos'] as $articuloId) {
                $cantidad = isset($_POST['cantidad'][$articuloId]) ? intval($_POST['cantidad'][$articuloId]) : 0;
                if ($cantidad > 0) {
                    $articulos[] = [
                        'id_articulo' => $articuloId,
                        'cantidad' => $cantidad
                    ];
                }
            }
        }
        
        // Validar datos
        $errors = $this->paqueteModel->validatePaqueteData($paqueteData, $articulos);
        
        if (empty($errors)) {
            try {
                $paqueteId = $this->paqueteModel->createPaqueteWithArticulos($paqueteData, $articulos);
                $this->setAlert('Paquete creado exitosamente', 'success');
                $this->redirect('index.php?controller=paquete&action=show&id=' . $paqueteId);
            } catch (Exception $e) {
                $this->setAlert('Error al crear el paquete: ' . $e->getMessage(), 'error');
            }
        } else {
            $articulosDisponibles = $this->articuloModel->getArticulosDisponibles();
            $this->loadView('paquetes/create', [
                'data' => $paqueteData,
                'articulos' => $articulosDisponibles,
                'articulosSeleccionados' => $articulos,
                'errors' => $errors
            ]);
        }
    }
    
    /**
     * Ver detalles de un paquete
     */
    public function show() {
        $id = $this->getGetData('id');
        $paquete = $this->paqueteModel->getById($id);
        
        if (!$paquete) {
            $this->setAlert('Paquete no encontrado', 'error');
            $this->redirect('index.php?controller=paquete&action=index');
        }
        
        // Obtener artículos del paquete
        $articulos = $this->paqueteModel->getArticulosPaquete($id);
        
        // Calcular precios
        $precios = $this->paqueteModel->calcularPreciosPaquete($id);
        
        // Verificar stock
        $stockDisponible = $this->paqueteModel->checkStockPaquete($id);
        
        $this->loadView('paquetes/show', [
            'paquete' => $paquete,
            'articulos' => $articulos,
            'precios' => $precios,
            'stockDisponible' => $stockDisponible
        ]);
    }
    
    /**
     * Mostrar formulario para editar paquete
     */
    public function edit() {
        $id = $this->getGetData('id');
        $paquete = $this->paqueteModel->getById($id);
        
        if (!$paquete) {
            $this->setAlert('Paquete no encontrado', 'error');
            $this->redirect('index.php?controller=paquete&action=index');
        }
        
        if ($this->isPost()) {
            $this->update($id);
            return;
        }
        
        $articulos = $this->articuloModel->getAll();
        $articulosPaquete = $this->paqueteModel->getArticulosPaquete($id);
        
        $this->loadView('paquetes/edit', [
            'paquete' => $paquete,
            'articulos' => $articulos,
            'articulosPaquete' => $articulosPaquete
        ]);
    }
    
    /**
     * Actualizar paquete
     */
    public function update($id) {
        $data = $this->getPostData(['nombre', 'descripcion']);
        
        if ($this->paqueteModel->update($id, $data)) {
            $this->setAlert('Paquete actualizado exitosamente', 'success');
            $this->redirect('index.php?controller=paquete&action=show&id=' . $id);
        } else {
            $this->setAlert('Error al actualizar el paquete', 'error');
            $this->redirect('index.php?controller=paquete&action=edit&id=' . $id);
        }
    }
    
    /**
     * Eliminar paquete
     */
    public function delete() {
        $id = $this->getGetData('id');
        
        try {
            // Primero eliminar artículos del paquete
            $articulos = $this->paqueteModel->getArticulosPaquete($id);
            foreach ($articulos as $articulo) {
                $this->paqueteModel->removeArticuloFromPaquete($id, $articulo['id_articulo']);
            }
            
            // Luego eliminar el paquete
            if ($this->paqueteModel->delete($id)) {
                $this->setAlert('Paquete eliminado exitosamente', 'success');
            } else {
                $this->setAlert('Error al eliminar el paquete', 'error');
            }
        } catch (Exception $e) {
            $this->setAlert('Error al eliminar el paquete: ' . $e->getMessage(), 'error');
        }
        
        $this->redirect('index.php?controller=paquete&action=index');
    }
    
    /**
     * Agregar artículo a un paquete (AJAX)
     */
    public function addArticulo() {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->redirect('index.php');
        }
        
        $paqueteId = $_POST['paquete_id'];
        $articuloId = $_POST['articulo_id'];
        $cantidad = intval($_POST['cantidad']);
        
        if ($this->paqueteModel->addArticuloToPaquete($paqueteId, $articuloId, $cantidad)) {
            $this->jsonResponse(['success' => true, 'message' => 'Artículo agregado al paquete']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Error al agregar artículo']);
        }
    }
    
    /**
     * Remover artículo de un paquete (AJAX)
     */
    public function removeArticulo() {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->redirect('index.php');
        }
        
        $paqueteId = $_POST['paquete_id'];
        $articuloId = $_POST['articulo_id'];
        
        if ($this->paqueteModel->removeArticuloFromPaquete($paqueteId, $articuloId)) {
            $this->jsonResponse(['success' => true, 'message' => 'Artículo removido del paquete']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Error al remover artículo']);
        }
    }
    
    /**
     * Obtener precios de un paquete (AJAX)
     */
    public function getPrecios() {
        if (!$this->isAjax()) {
            $this->redirect('index.php');
        }
        
        $id = $this->getGetData('id');
        $precios = $this->paqueteModel->calcularPreciosPaquete($id);
        
        $this->jsonResponse($precios);
    }
}
?>