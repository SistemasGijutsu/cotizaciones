<?php
require_once 'Controller.php';
require_once __DIR__ . '/../models/Paquete.php';
require_once __DIR__ . '/../models/Articulo.php';

class PaqueteController extends Controller
{
    private $paqueteModel;
    private $articuloModel;

    public function __construct()
    {
        $this->paqueteModel = new Paquete();
        $this->articuloModel = new Articulo();
    }

    public function index()
    {
        $paquetes = $this->paqueteModel->getPaquetesWithPrices();
        $this->loadView('paquetes/index', ['paquetes' => $paquetes]);
    }

    public function create()
    {
        if ($this->isPost()) {
            $this->store();
            return;
        }

        $articulos = $this->articuloModel->getArticulosDisponibles();
        $this->loadView('paquetes/create', ['articulos' => $articulos]);
    }

    public function store()
    {
        $paqueteData = $this->getPostData(['nombre', 'descripcion']);
        
        // precio_venta es opcional ahora - se define al agregar a cotización
        if (isset($_POST['precio_venta']) && !empty($_POST['precio_venta'])) {
            $paqueteData['precio_venta'] = floatval($_POST['precio_venta']);
        }

        // Manejar subida de imagen
        $imagenNombre = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $imagenNombre = $this->uploadImagen($_FILES['imagen']);
            if ($imagenNombre) {
                $paqueteData['imagen'] = $imagenNombre;
            }
        }

        $articulos = [];
        if (isset($_POST['articulos']) && is_array($_POST['articulos'])) {
            foreach ($_POST['articulos'] as $item) {
                if (is_array($item) && isset($item['id'])) {
                    $id = intval($item['id']);
                    $cantidad = isset($item['cantidad']) ? intval($item['cantidad']) : 0;
                    if ($cantidad > 0) {
                        $articulos[] = ['id_articulo' => $id, 'cantidad' => $cantidad];
                    }
                }
            }
        }

        $errors = $this->paqueteModel->validatePaqueteData($paqueteData, $articulos);

        $stockErrors = [];
        foreach ($articulos as $a) {
            if (!$this->articuloModel->checkStock($a['id_articulo'], $a['cantidad'])) {
                $art = $this->articuloModel->getById($a['id_articulo']);
                $nombre = $art ? $art['nombre'] : "#{$a['id_articulo']}";
                $stockErrors[] = "Stock insuficiente para el artículo '{$nombre}' (cantidad solicitada: {$a['cantidad']})";
            }
        }

        if (!empty($stockErrors)) {
            $errors = array_merge($errors, $stockErrors);
        }

        if (empty($errors)) {
            try {
                $paqueteId = $this->paqueteModel->createPaqueteWithArticulos($paqueteData, $articulos);
                $this->setAlert('Paquete creado exitosamente', 'success');
                $this->redirect('index.php?controller=paquete&action=show&id=' . $paqueteId);
            } catch (Exception $e) {
                $this->setAlert('Error al crear el paquete: ' . $e->getMessage(), 'error');
                $this->redirect('index.php?controller=paquete&action=create');
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

    public function show()
    {
        $id = $this->getGetData('id');
        $paquete = $this->paqueteModel->getById($id);
        if (!$paquete) {
            $this->setAlert('Paquete no encontrado', 'error');
            $this->redirect('index.php?controller=paquete&action=index');
        }

        $articulos = $this->paqueteModel->getArticulosPaquete($id);
        $precios = $this->paqueteModel->calcularPreciosPaquete($id);
        $stockDisponible = $this->paqueteModel->checkStockPaquete($id);

        $this->loadView('paquetes/show', [
            'paquete' => $paquete,
            'articulos' => $articulos,
            'precios' => $precios,
            'stockDisponible' => $stockDisponible
        ]);
    }

    public function edit()
    {
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

    public function update($id)
    {
        $data = $this->getPostData(['nombre', 'descripcion']);
        
        // precio_venta es opcional
        if (isset($_POST['precio_venta']) && !empty($_POST['precio_venta'])) {
            $data['precio_venta'] = floatval($_POST['precio_venta']);
        }
        
        // Manejar subida de imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $imagenNombre = $this->uploadImagen($_FILES['imagen']);
            if ($imagenNombre) {
                // Eliminar imagen anterior si existe
                $paqueteActual = $this->paqueteModel->getById($id);
                if (!empty($paqueteActual['imagen'])) {
                    $imagenAnterior = __DIR__ . '/../../public/images/paquetes/' . $paqueteActual['imagen'];
                    if (file_exists($imagenAnterior)) {
                        unlink($imagenAnterior);
                    }
                }
                $data['imagen'] = $imagenNombre;
            }
        }
        
        if ($this->paqueteModel->update($id, $data)) {
            $this->setAlert('Paquete actualizado exitosamente', 'success');
            $this->redirect('index.php?controller=paquete&action=show&id=' . $id);
        } else {
            $this->setAlert('Error al actualizar el paquete', 'error');
            $this->redirect('index.php?controller=paquete&action=edit&id=' . $id);
        }
    }

    public function delete()
    {
        $id = $this->getGetData('id');
        try {
            $articulos = $this->paqueteModel->getArticulosPaquete($id);
            foreach ($articulos as $articulo) {
                $this->paqueteModel->removeArticuloFromPaquete($id, $articulo['id_articulo']);
            }

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

    public function addArticulo()
    {
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

    public function removeArticulo()
    {
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

    public function getPrecios()
    {
        if (!$this->isAjax()) {
            $this->redirect('index.php');
        }

        $id = $this->getGetData('id');
        $precios = $this->paqueteModel->calcularPreciosPaquete($id);

        $this->jsonResponse($precios);
    }

    /**
     * Subir imagen de paquete
     */
    private function uploadImagen($file)
    {
        $uploadDir = __DIR__ . '/../../public/images/paquetes/';
        
        // Crear directorio si no existe
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Validar tipo de archivo
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            $this->setAlert('Tipo de archivo no permitido. Use JPG, PNG, GIF o WEBP', 'error');
            return null;
        }

        // Validar tamaño (máx 5MB)
        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            $this->setAlert('El archivo es demasiado grande. Tamaño máximo: 5MB', 'error');
            return null;
        }

        // Generar nombre único
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'paquete_' . time() . '_' . uniqid() . '.' . $extension;
        $rutaDestino = $uploadDir . $nombreArchivo;

        // Mover archivo
        if (move_uploaded_file($file['tmp_name'], $rutaDestino)) {
            return $nombreArchivo;
        }

        $this->setAlert('Error al subir la imagen', 'error');
        return null;
    }
}
