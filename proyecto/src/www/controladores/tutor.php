<?php
/**
 * Controlador del Tutor de Empresa
 * HU-03: Validación de Fichas de Seguimiento
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../modelos/Asignacion.php';
require_once __DIR__ . '/../modelos/FichaSeguimiento.php';
require_once __DIR__ . '/../modelos/Competencia.php';

// Verificar que sea un tutor de empresa
AuthController::verificarRol('TUTOR_EMPRESA');

class TutorController {
    private $asignacionModel;
    private $fichaModel;
    private $competenciaModel;

    public function __construct() {
        $this->asignacionModel = new Asignacion();
        $this->fichaModel = new FichaSeguimiento();
        $this->competenciaModel = new Competencia();
    }

    /**
     * Muestra el dashboard del tutor
     */
    public function dashboard() {
        $tutor_id = $_SESSION['perfil_id'];
        
        // Obtener estudiantes asignados
        $estudiantes = $this->asignacionModel->obtenerAsignacionesPorTutor($tutor_id);
        
        // Obtener fichas pendientes de validación
        $fichas_pendientes = $this->fichaModel->obtenerPendientesPorTutor($tutor_id);

        require_once __DIR__ . '/../vistas/tutor/dashboard.php';
    }

    /**
     * Muestra la lista de estudiantes asignados
     * HU-04: Visualización de Estudiantes Asignados (aunque no está en Sprint 1, ya está lista la función)
     */
    public function misEstudiantes() {
        $tutor_id = $_SESSION['perfil_id'];
        
        // Obtener estudiantes asignados
        $estudiantes = $this->asignacionModel->obtenerAsignacionesPorTutor($tutor_id);

        require_once __DIR__ . '/../vistas/tutor/mis_estudiantes.php';
    }

    /**
     * Muestra las fichas pendientes de validación
     * HU-03: Validación de Fichas de Seguimiento
     */
    public function fichasPendientes() {
        $tutor_id = $_SESSION['perfil_id'];
        
        // Obtener fichas pendientes
        $fichas = $this->fichaModel->obtenerPendientesPorTutor($tutor_id);

        require_once __DIR__ . '/../vistas/tutor/fichas_pendientes.php';
    }

    /**
     * Muestra el detalle de una ficha para validar
     * HU-03: Validación de Fichas de Seguimiento
     */
    public function validarFicha() {
        $ficha_id = $_GET['id'] ?? null;
        
        if (!$ficha_id) {
            $_SESSION['error'] = 'Ficha no encontrada';
            header('Location: ' . BASE_URL . '/controladores/tutor.php?action=fichas_pendientes');
            exit;
        }

        $tutor_id = $_SESSION['perfil_id'];
        $ficha = $this->fichaModel->obtenerPorId($ficha_id);
        
        if (!$ficha) {
            $_SESSION['error'] = 'Ficha no encontrada';
            header('Location: ' . BASE_URL . '/controladores/tutor.php?action=fichas_pendientes');
            exit;
        }

        // Verificar que la ficha pertenece a un estudiante del tutor
        if (!$this->asignacionModel->perteneceATutor($ficha['asignacion_id'], $tutor_id)) {
            $_SESSION['error'] = 'No tienes permiso para validar esta ficha';
            header('Location: ' . BASE_URL . '/controladores/tutor.php?action=fichas_pendientes');
            exit;
        }

        // Obtener competencias de la ficha
        $competencias_ids = !empty($ficha['competencias']) ? explode(',', $ficha['competencias']) : [];
        $competencias = [];
        if (!empty($competencias_ids)) {
            $competencias = $this->competenciaModel->obtenerPorIds($competencias_ids);
        }

        require_once __DIR__ . '/../vistas/tutor/validar_ficha.php';
    }

    /**
     * Procesa la validación (aprobación o rechazo) de una ficha
     * HU-03: Validación de Fichas de Seguimiento
     */
    public function procesarValidacion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/controladores/tutor.php?action=fichas_pendientes');
            exit;
        }

        $ficha_id = $_POST['ficha_id'] ?? null;
        $accion = $_POST['accion'] ?? ''; // 'aprobar' o 'rechazar'
        $comentarios = trim($_POST['comentarios'] ?? '');

        if (!$ficha_id || !in_array($accion, ['aprobar', 'rechazar'])) {
            $_SESSION['error'] = 'Datos inválidos';
            header('Location: ' . BASE_URL . '/controladores/tutor.php?action=fichas_pendientes');
            exit;
        }

        // Validar que si rechaza, debe dar motivo
        if ($accion === 'rechazar' && empty($comentarios)) {
            $_SESSION['error'] = 'Debes indicar el motivo del rechazo';
            header('Location: ' . BASE_URL . '/controladores/tutor.php?action=validar_ficha&id=' . $ficha_id);
            exit;
        }

        $tutor_id = $_SESSION['perfil_id'];
        $ficha = $this->fichaModel->obtenerPorId($ficha_id);
        
        if (!$ficha) {
            $_SESSION['error'] = 'Ficha no encontrada';
            header('Location: ' . BASE_URL . '/controladores/tutor.php?action=fichas_pendientes');
            exit;
        }

        // Verificar que la ficha pertenece a un estudiante del tutor
        if (!$this->asignacionModel->perteneceATutor($ficha['asignacion_id'], $tutor_id)) {
            $_SESSION['error'] = 'No tienes permiso para validar esta ficha';
            header('Location: ' . BASE_URL . '/controladores/tutor.php?action=fichas_pendientes');
            exit;
        }

        // Procesar validación
        $resultado = $this->fichaModel->validar($ficha_id, $tutor_id, $accion, $comentarios);

        if ($resultado['success']) {
            $_SESSION['success'] = $resultado['message'];
        } else {
            $_SESSION['error'] = $resultado['message'];
        }

        header('Location: ' . BASE_URL . '/controladores/tutor.php?action=fichas_pendientes');
        exit;
    }

    /**
     * Muestra el detalle de un estudiante
     */
    public function verEstudiante() {
        $asignacion_id = $_GET['id'] ?? null;
        
        if (!$asignacion_id) {
            $_SESSION['error'] = 'Estudiante no encontrado';
            header('Location: ' . BASE_URL . '/controladores/tutor.php?action=mis_estudiantes');
            exit;
        }

        $tutor_id = $_SESSION['perfil_id'];
        
        // Verificar que la asignación pertenece al tutor
        if (!$this->asignacionModel->perteneceATutor($asignacion_id, $tutor_id)) {
            $_SESSION['error'] = 'No tienes permiso para ver este estudiante';
            header('Location: ' . BASE_URL . '/controladores/tutor.php?action=mis_estudiantes');
            exit;
        }

        $asignacion = $this->asignacionModel->obtenerPorId($asignacion_id);
        $fichas = $this->fichaModel->obtenerPorAsignacion($asignacion_id);

        require_once __DIR__ . '/../vistas/tutor/ver_estudiante.php';
    }
}

// Procesar la acción solicitada
$action = $_GET['action'] ?? 'dashboard';
$controller = new TutorController();

switch ($action) {
    case 'dashboard':
        $controller->dashboard();
        break;
    case 'mis_estudiantes':
        $controller->misEstudiantes();
        break;
    case 'fichas_pendientes':
        $controller->fichasPendientes();
        break;
    case 'validar_ficha':
        $controller->validarFicha();
        break;
    case 'procesar_validacion':
        $controller->procesarValidacion();
        break;
    case 'ver_estudiante':
        $controller->verEstudiante();
        break;
    default:
        $controller->dashboard();
        break;
}
