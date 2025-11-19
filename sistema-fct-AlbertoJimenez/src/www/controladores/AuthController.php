<?php
/**
 * Sistema de Gestión de Prácticas FCT
 * Controlador de Autenticación
 * 
 * @author Alberto Jiménez Hernández
 */

session_start();

require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../includes/config.php';

class AuthController {
    private $usuario;
    
    public function __construct() {
        $this->usuario = new Usuario();
    }
    
    /**
     * Procesar registro de usuario
     */
    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../vistas/registro.php');
            exit;
        }
        
        // Recoger datos del formulario
        $datos = [
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'password' => $_POST['password'] ?? '',
            'confirmar_password' => $_POST['confirmar_password'] ?? '',
            'rol' => filter_input(INPUT_POST, 'rol', FILTER_SANITIZE_SPECIAL_CHARS),
            'nombre' => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS),
            'telefono' => filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_SPECIAL_CHARS)
        ];
        
        // Validaciones básicas
        $errores = $this->validarDatosRegistro($datos);
        
        // Datos específicos según rol
        if ($datos['rol'] === 'ESTUDIANTE') {
            $datos['dni'] = filter_input(INPUT_POST, 'dni', FILTER_SANITIZE_SPECIAL_CHARS);
            $datos['ciclo_formativo'] = filter_input(INPUT_POST, 'ciclo_formativo', FILTER_SANITIZE_SPECIAL_CHARS);
            $datos['grupo'] = filter_input(INPUT_POST, 'grupo', FILTER_SANITIZE_SPECIAL_CHARS);
            $datos['año_academico'] = filter_input(INPUT_POST, 'año_academico', FILTER_SANITIZE_SPECIAL_CHARS);
            
            if (empty($datos['dni'])) {
                $errores[] = 'El DNI es obligatorio';
            }
            if (empty($datos['ciclo_formativo'])) {
                $errores[] = 'El ciclo formativo es obligatorio';
            }
            if (empty($datos['año_academico'])) {
                $errores[] = 'El año académico es obligatorio';
            }
        } elseif ($datos['rol'] === 'TUTOR_EMPRESA') {
            $datos['empresa_id'] = filter_input(INPUT_POST, 'empresa_id', FILTER_VALIDATE_INT);
            $datos['cargo'] = filter_input(INPUT_POST, 'cargo', FILTER_SANITIZE_SPECIAL_CHARS);
            $datos['departamento'] = filter_input(INPUT_POST, 'departamento', FILTER_SANITIZE_SPECIAL_CHARS);
        } elseif ($datos['rol'] === 'COORDINADOR') {
            $datos['centro_educativo'] = filter_input(INPUT_POST, 'centro_educativo', FILTER_SANITIZE_SPECIAL_CHARS);
            $datos['departamento'] = filter_input(INPUT_POST, 'departamento', FILTER_SANITIZE_SPECIAL_CHARS);
            
            if (empty($datos['centro_educativo'])) {
                $errores[] = 'El centro educativo es obligatorio';
            }
        }
        
        // Si hay errores, volver al formulario
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_form'] = $datos;
            header('Location: ../vistas/registro.php');
            exit;
        }
        
        // Intentar registrar
        $resultado = $this->usuario->registrar($datos);
        
        if ($resultado['exito']) {
            $_SESSION['mensaje'] = '¡Registro exitoso! Ya puedes iniciar sesión.';
            header('Location: ../vistas/login.php');
            exit;
        } else {
            $_SESSION['errores'] = [$resultado['error']];
            $_SESSION['datos_form'] = $datos;
            header('Location: ../vistas/registro.php');
            exit;
        }
    }
    
    /**
     * Procesar inicio de sesión
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../vistas/login.php');
            exit;
        }
        
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        
        // Validaciones básicas
        if (empty($email) || empty($password)) {
            $_SESSION['errores'] = ['Todos los campos son obligatorios'];
            header('Location: ../vistas/login.php');
            exit;
        }
        
        // Intentar login
        $resultado = $this->usuario->login($email, $password);
        
        if ($resultado['exito']) {
            // Guardar datos en sesión
            $_SESSION['usuario_id'] = $resultado['usuario']['id'];
            $_SESSION['usuario_email'] = $resultado['usuario']['email'];
            $_SESSION['usuario_nombre'] = $resultado['usuario']['nombre'];
            $_SESSION['usuario_rol'] = $resultado['usuario']['rol'];
            $_SESSION['logueado'] = true;
            $_SESSION['mensaje'] = '¡Sesión iniciada correctamente!';
            
            // Redirigir según rol
            header('Location: ../vistas/dashboard.php');
            exit;
        } else {
            $_SESSION['errores'] = [$resultado['error']];
            header('Location: ../vistas/login.php');
            exit;
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        session_unset();
        session_destroy();
        header('Location: ../vistas/login.php');
        exit;
    }
    
    /**
     * Validar datos de registro
     */
    private function validarDatosRegistro($datos) {
        $errores = [];
        
        if (empty($datos['email'])) {
            $errores[] = 'El correo electrónico es obligatorio';
        } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El formato del correo electrónico no es válido';
        }
        
        if (empty($datos['password'])) {
            $errores[] = 'La contraseña es obligatoria';
        }
        
        if ($datos['password'] !== $datos['confirmar_password']) {
            $errores[] = 'Las contraseñas no coinciden';
        }
        
        if (empty($datos['nombre'])) {
            $errores[] = 'El nombre es obligatorio';
        }
        
        if (empty($datos['rol'])) {
            $errores[] = 'Debes seleccionar un rol';
        }
        
        return $errores;
    }
}

// Procesar acción
$accion = $_GET['accion'] ?? '';
$controller = new AuthController();

switch ($accion) {
    case 'registrar':
        $controller->registrar();
        break;
    case 'login':
        $controller->login();
        break;
    case 'logout':
        $controller->logout();
        break;
    default:
        header('Location: ../vistas/login.php');
        exit;
}
?>
