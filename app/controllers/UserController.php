<?php
require_once 'Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/AuthController.php';

class UserController extends Controller {
    private $userModel;
    private $auth;

    public function __construct() {
        $this->userModel = new User();
        $this->auth = new AuthController();
    }

    // Listar usuarios
    public function index() {
        $this->auth->requireAdmin();

        // Paginación
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = 10;
        $total = $this->userModel->countUsers();
        $offset = ($page - 1) * $perPage;
        $users = $this->userModel->getUsersPaginated($perPage, $offset);

        $this->loadView('users/index', [
            'users' => $users,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total
        ]);
    }

    /**
     * Endpoint AJAX para validar unicidad
     */
    public function checkUnique() {
        $this->auth->requireAdmin();
        $field = $_GET['field'] ?? null;
        $value = $_GET['value'] ?? null;
        $excludeId = isset($_GET['exclude_id']) ? intval($_GET['exclude_id']) : null;

        $result = ['unique' => true];
        if ($field && $value) {
            if ($field === 'username') {
                $result['unique'] = !$this->userModel->usernameExists($value, $excludeId);
            } elseif ($field === 'email') {
                $result['unique'] = !$this->userModel->emailExists($value, $excludeId);
            }
        }

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    // Formulario crear
    public function create() {
        $this->auth->requireAdmin();
        $this->loadView('users/create');
    }

    // Guardar nuevo usuario
    public function store() {
        $this->auth->requireAdmin();
        if (!$this->isPost()) {
            $this->redirect('index.php?controller=user&action=create');
        }

        $data = $this->getPostData(['username','email','password']);
        $errors = $this->userModel->validateUserData($data, false);
        if (!empty($errors)) {
            $this->loadView('users/create', ['errors' => $errors, 'old' => $data]);
            return;
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        if ($this->userModel->createUser($data)) {
            $this->setAlert('Usuario creado correctamente', 'success');
            $this->redirect('index.php?controller=user&action=index');
        } else {
            $this->setAlert('Error al crear el usuario', 'error');
            $this->redirect('index.php?controller=user&action=create');
        }
    }

    // Formulario editar
    public function edit() {
        $this->auth->requireAdmin();
        $id = $this->getGetData('id');
        $user = $this->userModel->getById($id);
        if (!$user) {
            $this->setAlert('Usuario no encontrado', 'error');
            $this->redirect('index.php?controller=user&action=index');
        }
        $this->loadView('users/edit', ['user' => $user]);
    }

    // Actualizar usuario
    public function update() {
        $this->auth->requireAdmin();
        if (!$this->isPost()) {
            $this->redirect('index.php?controller=user&action=index');
        }

        $id = $_POST['id'] ?? null;
        $data = $this->getPostData(['username','email']);
        // Si se envía password, validarlo y usar changePassword
        $newPassword = $_POST['password'] ?? '';

        $errors = $this->userModel->validateUserData(array_merge($data, ['password' => $newPassword]), true, $id);
        if (!empty($errors)) {
            $user = $this->userModel->getById($id);
            $this->loadView('users/edit', ['errors' => $errors, 'user' => $user]);
            return;
        }

        // Actualizar campos
        $data['updated_at'] = date('Y-m-d H:i:s');
        $ok = $this->userModel->update($id, $data);

        // Cambiar contraseña si fue enviada
        if (!empty($newPassword)) {
            $this->userModel->changePassword($id, $newPassword);
        }

        if ($ok) {
            $this->setAlert('Usuario actualizado correctamente', 'success');
        } else {
            $this->setAlert('No hubo cambios o error al actualizar', 'info');
        }

        $this->redirect('index.php?controller=user&action=index');
    }

    // Eliminar usuario (soft delete no implementado, hacemos delete físico)
    public function delete() {
        $this->auth->requireAdmin();
        $id = $this->getGetData('id');
        if ($this->userModel->delete($id)) {
            $this->setAlert('Usuario eliminado', 'success');
        } else {
            $this->setAlert('Error al eliminar usuario', 'error');
        }
        $this->redirect('index.php?controller=user&action=index');
    }

    // Ver usuario
    public function show() {
        $this->auth->requireAdmin();
        $id = $this->getGetData('id');
        $user = $this->userModel->getById($id);
        if (!$user) {
            $this->setAlert('Usuario no encontrado', 'error');
            $this->redirect('index.php?controller=user&action=index');
        }
        $this->loadView('users/show', ['user' => $user]);
    }
}

?>
