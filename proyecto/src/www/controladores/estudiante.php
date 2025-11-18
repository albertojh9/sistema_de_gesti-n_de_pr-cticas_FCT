<?php
/**
 * Controlador del Estudiante
 * HU-02: Registro de Ficha de Seguimiento Diaria
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../modelos/Asignacion.php';
require_once __DIR__ . '/../modelos/FichaSeguimiento.php';
require_once __DIR__ . '/../modelos/Competencia.php';

// Verificar que sea un estudiante
AuthController::verificarRol('ESTUDIANTE');

class EstudianteController {
    private $asignacionModel;
    private $fichaModel;
    private $competenciaModel;

    public function __construct() {
        $this->asignacionModel = new Asignacion();
        $this->fichaModel = new FichaSeguimiento();
        $this->competenciaModel = new Competencia();
    }

    /**
     * Muestra el dashboard del estudiante
     */
    public function dashboard() {
        $estudiante_id = $_SESSION['perfil_id'];
        
        // Obtener asignación activa
        $asignacion = $this->asignacionModel->obtenerAsignacionActivaEstudiante($estudiante_id);
        
        if (!$asignacion) {
            $_SESSION['error'] = 'No tienes una asignación activa';
        }

        // Obtener fichas del estudiante
        $fichas = [];
        if ($asignacion) {
            $fichas = $this->fichaModel->obtenerPorAsignacion($asignacion['id']);
        }

        require_once __DIR__ . '/../vistas/estudiante/dashboard.php';
    }

    /**
     * Muestra el formulario para crear una nueva ficha
     */
    public function nuevaFicha() {
        $estudiante_id = $_SESSION['perfil_id'];
        
        // Obtener asignación activa
        $asignacion = $this->asignacionModel->obtenerAsignacionActivaEstudiante($estudiante_id);
        
        if (!$asignacion) {
            $_SESSION['error'] = 'No tienes una asignación activa';
            header('Location: ' . BASE_URL . '/controladores/estudiante.php?action=dashboard');
            exit;
        }

        // Obtener competencias del ciclo formativo
        $competencias = $this->competenciaModel->obtenerPorCategoria($_SESSION['ciclo_formativo']);

        require_once __DIR__ . '/../vistas/estudiante/nueva_ficha.php';
    }

    /**
     * Guarda una nueva ficha de seguimiento
     */
    public function guardarFicha() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/controladores/estudiante.php?action=dashboard');
            exit;
        }

        $estudiante_id = $_SESSION['perfil_id'];
        $asignacion = $this->asignacionModel->obtenerAsignacionActivaEstudiante($estudiante_id);

        if (!$asignacion) {
            $_SESSION['error'] = 'No tienes una asignación activa';
            header('Location: ' . BASE_URL . '/controladores/estudiante.php?action=dashboard');
            exit;
        }

        // Recoger datos del formulario
        $datos = [
            'asignacion_id' => $asignacion['id'],
            'fecha' => $_POST['fecha'] ?? '',
            'hora_entrada' => $_POST['hora_entrada'] ?? '',
            'hora_salida' => $_POST['hora_salida'] ?? '',
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'competencias' => isset($_POST['competencias']) ? implode(',', $_POST['competencias']) : '',
            'dificultades' => trim($_POST['dificultades'] ?? ''),
            'valoracion' => !empty($_POST['valoracion']) ? intval($_POST['valoracion']) : null
        ];

        // Validar datos
        $errores = $this->fichaModel->validarDatos($datos);
        
        if (!empty($errores)) {
            $_SESSION['error'] = implode(', ', $errores);
            $_SESSION['form_data'] = $datos;
            header('Location: ' . BASE_URL . '/controladores/estudiante.php?action=nueva_ficha');
            exit;
        }

        // Guardar ficha
        $resultado = $this->fichaModel->crear($datos);

        if ($resultado['success']) {
            $_SESSION['success'] = $resultado['message'];
        } else {
            $_SESSION['error'] = $resultado['message'];
        }

        header('Location: ' . BASE_URL . '/controladores/estudiante.php?action=dashboard');
        exit;
    }

    /**
     * Muestra el formulario para editar una ficha
     */
    public function editarFicha() {
        $ficha_id = $_GET['id'] ?? null;
        
        if (!$ficha_id) {
            $_SESSION['error'] = 'Ficha no encontrada';
            header('Location: ' . BASE_URL . '/controladores/estudiante.php?action=dashboard');
            exit;
        }

        $estudiante_id = $_SESSION['perfil_id'];
        $asignacion = $this->asignacionModel->obtenerAsignacionActivaEstudiante($estudiante_id);
        
        if (!$asignacion) {
            $_SESSION['error'] = 'No tienes una asignación activa';
            header('Location: ' . BASE_URL . '/controladores/estudiante.php?action=dashboard');
            exit;
        }

        $ficha = $this->fichaModel->obtenerPorId($ficha_id);
        
        if (!$ficha || $ficha['asignacion_id'] != $asignacion['id']) {
            $_SESSION['error'] = 'No tienes permiso para editar esta ficha';
            header('Location: ' . BASE_URL . '/controladores/estudiante.php?action=dashboard');
            exit;
        }

        if ($ficha['estado'] !== 'PENDIENTE') {
            $_SESSION['error'] = 'Solo puedes editar fichas pendientes';
            header('Location: ' . BASE_URL . '/controladores/estudiante.php?action=dashboard');
            exit;
        }

        // Obtener competencias
        $competencias = $this->competenciaModel->obtenerPorCategoria($_SESSION['ciclo_formativo']);

        require_once __DIR__ . '/../vistas/estudiante/editar_ficha.php';
    }

    /**
     * Actualiza una ficha de seguimiento
     */
    public function actualizarFicha() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/controladores/estudiante.php?action=dashboard');
            exit;
        }

        $ficha_id = $_POST['ficha_id'] ?? null;
        
        if (!$ficha_id) {
            $_SESSION['error'] = 'Ficha no encontrada';
            header('Location: ' . BASE_URL . '/controladores/estudiante.php?action=dashboard');
            exit;
        }

        // Recoger datos del formulario
        $datos = [
            'fecha' => $_POST['fecha'] ?? '',
            'hora_entrada' => $_POST['hora_entrada'] ?? '',
            'hora_salida' => $_POST['hora_salida'] ?? '',
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'competencias' => isset($_POST['competencias']) ? implode(',', $_POST['competencias']) : '',
            'dificultades' => trim($_POST['dificultades'] ?? ''),
            'valoracion' => !empty($_POST['valoracion']) ? intval($_POST['valoracion']) : null
        ];

        // Validar datos
        $errores = $this->fichaModel->validarDatos($datos);
        
        if (!empty($errores)) {
            $_SESSION['error'] = implode(', ', $errores);
            header('Location: ' . BASE_URL . '/controladores/estudiante.php?action=editar_ficha&id=' . $ficha_id);
            exit;
        }

        // Actualizar ficha
        $resultado = $this->fichaModel->actualizar($ficha_id, $datos);

        if ($resultado['success']) {
            $_SESSION['success'] = $resultado['message'];
        } else {
            $_SESSION['error'] = $resultado['message'];
        }

        header('Location: ' . BASE_URL . '/controladores/estudiante.php?action=dashboard');
        exit;
    }

    /**
     * Muestra el detalle de una ficha
     */
    public function verFicha() {
        $ficha_id = $_GET['id'] ?? null;
        
        if (!$ficha_id) {
            $_SESSION['error'] = 'Ficha no encontrada';
            header('Location: ' . BASE_URL . '/controladores/estudiante.php?action=dashboard');
            exit;
        }

        $ficha = $this->fichaModel->obtenerPorId($ficha_id);
        
        if (!$ficha) {
            $_SESSION['error'] = 'Ficha no encontrada';
            header('Location: ' . BASE_URL . '/controladores/estudiante.php?action=dashboard');
            exit;
        }

        // Obtener competencias de la ficha
        $competencias_ids = !empty($ficha['competencias']) ? explode(',', $ficha['competencias']) : [];
        $competencias = [];
        if (!empty($competencias_ids)) {
            $competencias = $this->competenciaModel->obtenerPorIds($competencias_ids);
        }

        require_once __DIR__ . '/../vistas/estudiante/ver_ficha.php';
    }
}

// Procesar la acción solicitada
$action = $_GET['action'] ?? 'dashboard';
$controller = new EstudianteController();

switch ($action) {
    case 'dashboard':
        $controller->dashboard();
        break;
    case 'nueva_ficha':
        $controller->nuevaFicha();
        break;
    case 'guardar_ficha':
        $controller->guardarFicha();
        break;
    case 'editar_ficha':
        $controller->editarFicha();
        break;
    case 'actualizar_ficha':
        $controller->actualizarFicha();
        break;
    case 'ver_ficha':
        $controller->verFicha();
        break;
    default:
        $controller->dashboard();
        break;
}
