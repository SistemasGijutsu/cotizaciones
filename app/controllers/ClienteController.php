<?php
/**
 * Controlador Cliente para el Sistema de Cotizaciones
 * Maneja todas las operaciones relacionadas con clientes
 */

require_once 'Controller.php';
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController extends Controller {
    private $clienteModel;
    
    public function __construct() {
        $this->clienteModel = new Cliente();
    }
    
    /**
     * Mostrar lista de clientes
     */
    public function index() {
        $search = $this->getGetData('search');
        
        if (!empty($search)) {
            $clientes = $this->clienteModel->searchClientes($search);
        } else {
            $clientes = $this->clienteModel->getAll();
        }
        
        $this->loadView('clientes/index', [
            'clientes' => $clientes,
            'search' => $search
        ]);
    }
    
    /**
     * Mostrar formulario para crear cliente
     */
    public function create() {
        if ($this->isPost()) {
            $this->store();
            return;
        }
        
        $this->loadView('clientes/create');
    }
    
    /**
     * Guardar nuevo cliente
     */
    public function store() {
        $data = $this->getPostData(['nombre', 'correo', 'telefono', 'direccion']);
        
        // Validar datos
        $errors = $this->clienteModel->validateClienteData($data);
        
        if (empty($errors)) {
            if ($this->clienteModel->create($data)) {
                $this->setAlert('Cliente creado exitosamente', 'success');
                $this->redirect('index.php?controller=cliente&action=index');
            } else {
                $this->setAlert('Error al crear el cliente', 'error');
            }
        } else {
            $this->loadView('clientes/create', [
                'data' => $data,
                'errors' => $errors
            ]);
        }
    }
    
    /**
     * Mostrar formulario para editar cliente
     */
    public function edit() {
        $id = $this->getGetData('id');
        $cliente = $this->clienteModel->getById($id);
        
        if (!$cliente) {
            $this->setAlert('Cliente no encontrado', 'error');
            $this->redirect('index.php?controller=cliente&action=index');
        }
        
        if ($this->isPost()) {
            $this->update($id);
            return;
        }
        
        $this->loadView('clientes/edit', ['cliente' => $cliente]);
    }
    
    /**
     * Actualizar cliente
     */
    public function update($id) {
        $data = $this->getPostData(['nombre', 'correo', 'telefono', 'direccion']);
        $data['id'] = $id; // Para validación de email único
        
        // Validar datos
        $errors = $this->clienteModel->validateClienteData($data);
        
        if (empty($errors)) {
            unset($data['id']); // Remover ID de los datos a actualizar
            
            if ($this->clienteModel->update($id, $data)) {
                $this->setAlert('Cliente actualizado exitosamente', 'success');
                $this->redirect('index.php?controller=cliente&action=index');
            } else {
                $this->setAlert('Error al actualizar el cliente', 'error');
            }
        } else {
            $cliente = array_merge(['id' => $id], $data);
            $this->loadView('clientes/edit', [
                'cliente' => $cliente,
                'errors' => $errors
            ]);
        }
    }
    
    /**
     * Ver detalles de un cliente
     */
    public function show() {
        $id = $this->getGetData('id');
        $cliente = $this->clienteModel->getById($id);
        
        if (!$cliente) {
            $this->setAlert('Cliente no encontrado', 'error');
            $this->redirect('index.php?controller=cliente&action=index');
        }
        
        // Obtener historial de cotizaciones
        $historial = $this->clienteModel->getHistorialCotizaciones($id);
        
        $this->loadView('clientes/show', [
            'cliente' => $cliente,
            'historial' => $historial
        ]);
    }
    
    /**
     * Eliminar cliente
     */
    public function delete() {
        $id = $this->getGetData('id');
        
        if ($this->clienteModel->delete($id)) {
            $this->setAlert('Cliente eliminado exitosamente', 'success');
        } else {
            $this->setAlert('Error al eliminar el cliente', 'error');
        }
        
        $this->redirect('index.php?controller=cliente&action=index');
    }
    
    /**
     * Búsqueda AJAX de clientes
     */
    public function search() {
        if (!$this->isAjax()) {
            $this->redirect('index.php');
        }
        
        $term = $this->getGetData('term');
        $clientes = $this->clienteModel->searchClientes($term);
        
        $this->jsonResponse($clientes);
    }
    
    /**
     * Crear cliente rápido via AJAX
     */
    public function createRapido() {
        if (!$this->isAjax() || !$this->isPost()) {
            $this->redirect('index.php');
        }
        
        $data = [
            'nombre' => $this->getPostData('nombre'),
            'documento' => $this->getPostData('documento'),
            'tipo_documento' => $this->getPostData('tipo_documento', 'cedula'),
            'empresa' => $this->getPostData('empresa'),
            'correo' => $this->getPostData('correo'),
            'telefono' => $this->getPostData('telefono', ''),
            'direccion' => $this->getPostData('direccion', '')
        ];
        
        try {
            $clienteId = $this->clienteModel->createRapido($data);
            $cliente = $this->clienteModel->getById($clienteId);
            
            $this->jsonResponse([
                'success' => true,
                'cliente' => $cliente,
                'message' => 'Cliente creado exitosamente'
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
?>