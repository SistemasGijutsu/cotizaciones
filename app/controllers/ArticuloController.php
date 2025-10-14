<?php
/**
 * Controlador Articulo para el Sistema de Cotizaciones
 * Maneja todas las operaciones relacionadas con artículos
 */

require_once 'Controller.php';
require_once __DIR__ . '/../models/Articulo.php';

class ArticuloController extends Controller {
    private $articuloModel;
    
    public function __construct() {
        $this->articuloModel = new Articulo();
    }
    
    /**
     * Mostrar lista de artículos
     */
    public function index() {
        $search = $this->getGetData('search');
        
        if (!empty($search)) {
            $articulos = $this->articuloModel->searchArticulos($search);
        } else {
            $articulos = $this->articuloModel->getAll();
        }
        
        // Calcular utilidad para cada artículo
        foreach ($articulos as &$articulo) {
            $articulo['utilidad_porcentaje'] = $this->articuloModel->calcularUtilidad($articulo['id']);
        }
        
        $this->loadView('articulos/index', [
            'articulos' => $articulos,
            'search' => $search
        ]);
    }
    
    /**
     * Mostrar formulario para crear artículo
     */
    public function create() {
        if ($this->isPost()) {
            $this->store();
            return;
        }
        
        $this->loadView('articulos/create');
    }
    
    /**
     * Guardar nuevo artículo
     */
    public function store() {
        $data = $this->getPostData(['nombre', 'descripcion', 'precio_costo', 'precio_venta', 'stock']);
        
        // Convertir precios a float
        $data['precio_costo'] = floatval($data['precio_costo']);
        $data['precio_venta'] = floatval($data['precio_venta']);
        $data['stock'] = intval($data['stock']);
        
        // Validar datos
        $errors = $this->articuloModel->validateArticuloData($data);
        
        if (empty($errors)) {
            if ($this->articuloModel->create($data)) {
                $this->setAlert('Artículo creado exitosamente', 'success');
                $this->redirect('index.php?controller=articulo&action=index');
            } else {
                $this->setAlert('Error al crear el artículo', 'error');
            }
        } else {
            $this->loadView('articulos/create', [
                'data' => $data,
                'errors' => $errors
            ]);
        }
    }
    
    /**
     * Mostrar formulario para editar artículo
     */
    public function edit() {
        $id = $this->getGetData('id');
        $articulo = $this->articuloModel->getById($id);
        
        if (!$articulo) {
            $this->setAlert('Artículo no encontrado', 'error');
            $this->redirect('index.php?controller=articulo&action=index');
        }
        
        if ($this->isPost()) {
            $this->update($id);
            return;
        }
        
        $this->loadView('articulos/edit', ['articulo' => $articulo]);
    }
    
    /**
     * Actualizar artículo
     */
    public function update($id) {
        $data = $this->getPostData(['nombre', 'descripcion', 'precio_costo', 'precio_venta', 'stock']);
        
        // Convertir precios a float
        $data['precio_costo'] = floatval($data['precio_costo']);
        $data['precio_venta'] = floatval($data['precio_venta']);
        $data['stock'] = intval($data['stock']);
        
        // Validar datos
        $errors = $this->articuloModel->validateArticuloData($data);
        
        if (empty($errors)) {
            if ($this->articuloModel->update($id, $data)) {
                $this->setAlert('Artículo actualizado exitosamente', 'success');
                $this->redirect('index.php?controller=articulo&action=index');
            } else {
                $this->setAlert('Error al actualizar el artículo', 'error');
            }
        } else {
            $articulo = array_merge(['id' => $id], $data);
            $this->loadView('articulos/edit', [
                'articulo' => $articulo,
                'errors' => $errors
            ]);
        }
    }
    
    /**
     * Ver detalles de un artículo
     */
    public function show() {
        $id = $this->getGetData('id');
        $articulo = $this->articuloModel->getById($id);
        
        if (!$articulo) {
            $this->setAlert('Artículo no encontrado', 'error');
            $this->redirect('index.php?controller=articulo&action=index');
        }
        
        // Calcular utilidad
        $articulo['utilidad_porcentaje'] = $this->articuloModel->calcularUtilidad($id);
        $articulo['utilidad_monto'] = $articulo['precio_venta'] - $articulo['precio_costo'];
        
        $this->loadView('articulos/show', ['articulo' => $articulo]);
    }
    
    /**
     * Eliminar artículo
     */
    public function delete() {
        $id = $this->getGetData('id');
        
        if ($this->articuloModel->delete($id)) {
            $this->setAlert('Artículo eliminado exitosamente', 'success');
        } else {
            $this->setAlert('Error al eliminar el artículo', 'error');
        }
        
        $this->redirect('index.php?controller=articulo&action=index');
    }
    
    /**
     * Búsqueda AJAX de artículos
     */
    public function search() {
        if (!$this->isAjax()) {
            $this->redirect('index.php');
        }
        
        $term = $this->getGetData('term');
        $articulos = $this->articuloModel->searchArticulos($term);
        
        $this->jsonResponse($articulos);
    }
    
    /**
     * Obtener artículos disponibles para cotización
     */
    public function disponibles() {
        if (!$this->isAjax()) {
            $this->redirect('index.php');
        }
        
        $articulos = $this->articuloModel->getArticulosDisponibles();
        $this->jsonResponse($articulos);
    }
    
    /**
     * Ver estadísticas de artículos más cotizados
     */
    public function estadisticas() {
        $articulos = $this->articuloModel->getArticulosMasCotizados(10);
        
        $this->loadView('articulos/estadisticas', [
            'articulos' => $articulos
        ]);
    }
}
?>