<?php
/**
 * ============================================================================
 * CONTROLADOR DE AUTENTICACIÓN
 * ============================================================================
 * 
 * Este archivo implementa el CONTROLADOR del patrón MVC.
 * 
 * ¿Qué hace un Controlador?
 * - Recibe las peticiones del usuario (GET, POST)
 * - Decide qué acción ejecutar
 * - Usa los Modelos para obtener/guardar datos
 * - Selecciona qué Vista mostrar
 * - Es el "intermediario" entre Vista y Modelo
 * 
 * Este controlador gestiona:
 * - Login de usuarios (HU-01)
 * - Logout (cerrar sesión)
 * - Verificación de autenticación
 * - Control de acceso por roles
 */

/**
 * ============================================================================
 * INICIO DE SESIÓN
 * ============================================================================
 * 
 * session_status(): Devuelve el estado de la sesión
 * - PHP_SESSION_NONE (0): No hay sesión iniciada
 * - PHP_SESSION_ACTIVE (2): Sesión activa
 * 
 * ¿Por qué esta comprobación?
 * - Evita el error "session already started"
 * - Permite incluir este archivo varias veces sin problemas
 */
if (session_status() == PHP_SESSION_NONE) {
    session_start();  // Inicia la sesión si no está iniciada
}

// Cargar configuración y modelo de Usuario
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../modelos/Usuario.php';

/**
 * ============================================================================
 * CLASE AuthController
 * ============================================================================
 * 
 * Agrupa todos los métodos relacionados con autenticación.
 * Usar una clase permite:
 * - Organizar mejor el código
 * - Encapsular la lógica relacionada
 * - Reutilizar métodos comunes
 */
class AuthController {
    
    /**
     * $usuarioModel: Instancia del modelo Usuario
     * Lo usamos para interactuar con la base de datos
     */
    private $usuarioModel;

    /**
     * Constructor: Se ejecuta al crear el controlador
     * Inicializa el modelo de Usuario
     */
    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    /**
     * ========================================================================
     * mostrarLogin(): Muestra el formulario de login
     * ========================================================================
     * 
     * Este método se llama cuando el usuario accede a la página de login.
     * 
     * Lógica:
     * 1. Si ya está autenticado → redirigir a su dashboard
     * 2. Si no está autenticado → mostrar formulario
     */
    public function mostrarLogin() {
        /**
         * Verificar si ya hay sesión activa
         * 
         * isset(): Comprueba si una variable existe y no es null
         * $_SESSION: Array superglobal que almacena datos de sesión
         * 
         * Si 'usuario_id' existe en sesión, el usuario ya hizo login
         */
        if (isset($_SESSION['usuario_id'])) {
            // Ya está autenticado, redirigir según su rol
            $this->redirigirSegunRol($_SESSION['rol']);
            exit;  // Importante: detener ejecución después de redirigir
        }

        /**
         * require_once: Incluye y ejecuta el archivo de la vista
         * - Muestra el formulario HTML de login
         * - La vista tiene acceso a las variables de este contexto
         */
        require_once __DIR__ . '/../vistas/auth/login.php';
    }

    /**
     * ========================================================================
     * login(): Procesa el formulario de login
     * ========================================================================
     * 
     * Este método se llama cuando el usuario envía el formulario (POST).
     * 
     * Pasos:
     * 1. Verificar que sea método POST
     * 2. Obtener y limpiar datos del formulario
     * 3. Validar formato de email
     * 4. Validar longitud de contraseña
     * 5. Intentar autenticar
     * 6. Si OK → crear sesión y redirigir
     * 7. Si error → mostrar mensaje y volver al login
     */
    public function login() {
        /**
         * Verificar método HTTP
         * 
         * $_SERVER['REQUEST_METHOD']: Método de la petición (GET, POST, etc.)
         * 
         * Solo procesamos si es POST porque:
         * - GET es para obtener datos (mostrar página)
         * - POST es para enviar datos (enviar formulario)
         */
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        /**
         * Obtener datos del formulario
         * 
         * $_POST: Array superglobal con datos enviados por POST
         * trim(): Elimina espacios al inicio y final
         * ?? '': Operador null coalescing - si no existe, usa ''
         * 
         * Ejemplo: Si el usuario escribe " email@test.com  "
         * trim() lo convierte a "email@test.com"
         */
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        /**
         * Validación 1: Formato de email
         * 
         * Usamos el método del modelo para validar
         * Si no es válido, mostramos error y volvemos al login
         */
        if (!$this->usuarioModel->validarEmail($email)) {
            // Guardar mensaje de error en sesión
            $_SESSION['error'] = 'Formato de email inválido';
            // Redirigir al login
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        /**
         * Validación 2: Longitud de contraseña
         * 
         * Mínimo 8 caracteres para evitar contraseñas débiles
         */
        if (!$this->usuarioModel->validarPassword($password)) {
            $_SESSION['error'] = 'La contraseña debe tener mínimo 8 caracteres';
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        /**
         * Intentar autenticar
         * 
         * El modelo se encarga de:
         * - Buscar usuario en BD
         * - Verificar contraseña con hash
         * - Devolver datos o false
         */
        $usuario = $this->usuarioModel->autenticar($email, $password);

        if ($usuario) {
            /**
             * ============================================================
             * LOGIN EXITOSO - Crear sesión
             * ============================================================
             * 
             * Guardamos en $_SESSION los datos que necesitaremos:
             * - Se mantienen mientras el navegador esté abierto
             * - Disponibles en todas las páginas
             * - Se guardan en el servidor (seguro)
             */
            
            // ID único del usuario (para consultas a BD)
            $_SESSION['usuario_id'] = $usuario['id'];
            
            // Email (para mostrar o verificar)
            $_SESSION['email'] = $usuario['email'];
            
            // Nombre completo (para mostrar en la interfaz)
            $_SESSION['nombre'] = $usuario['nombre'];
            
            // Rol (para control de acceso)
            $_SESSION['rol'] = $usuario['rol'];
            
            // ID del perfil específico (estudiante, tutor o coordinador)
            $_SESSION['perfil_id'] = $usuario['perfil_id'];
            
            // Datos adicionales para estudiantes
            if ($usuario['rol'] === 'ESTUDIANTE') {
                $_SESSION['ciclo_formativo'] = $usuario['ciclo_formativo'];
            }

            /**
             * Timestamp del login
             * 
             * time(): Devuelve timestamp Unix actual (segundos desde 1970)
             * Se usa para controlar el timeout de sesión
             */
            $_SESSION['login_time'] = time();

            // Redirigir al dashboard correspondiente al rol
            $this->redirigirSegunRol($usuario['rol']);
            
        } else {
            /**
             * ============================================================
             * LOGIN FALLIDO
             * ============================================================
             * 
             * El email no existe o la contraseña es incorrecta.
             * Por seguridad, NO indicamos cuál de los dos falló.
             */
            
            // Registrar intento fallido (para seguridad)
            $this->usuarioModel->registrarIntentoFallido($email);
            
            // Mostrar mensaje genérico
            $_SESSION['error'] = 'Credenciales inválidas';
            
            // Volver al login
            header('Location: ' . BASE_URL . '/index.php');
        }
        
        // Siempre terminar después de redirigir
        exit;
    }

    /**
     * ========================================================================
     * logout(): Cerrar sesión
     * ========================================================================
     * 
     * Destruye la sesión actual y redirige al login.
     * 
     * Pasos:
     * 1. Asegurar que hay sesión iniciada
     * 2. Limpiar todas las variables de sesión
     * 3. Destruir la sesión
     * 4. Redirigir al login
     */
    public function logout() {
        // Iniciar sesión si no está iniciada (para poder destruirla)
        session_start();
        
        /**
         * session_unset(): Elimina todas las variables de sesión
         * - Limpia $_SESSION pero mantiene la sesión activa
         */
        session_unset();
        
        /**
         * session_destroy(): Destruye la sesión
         * - Elimina el archivo de sesión del servidor
         * - La sesión deja de existir
         */
        session_destroy();
        
        // Redirigir al login
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }

    /**
     * ========================================================================
     * verificarAutenticacion(): Comprobar si hay sesión activa
     * ========================================================================
     * 
     * Método ESTÁTICO - se puede llamar sin instanciar la clase:
     * AuthController::verificarAutenticacion();
     * 
     * Se usa al inicio de páginas protegidas para:
     * - Verificar que el usuario está logueado
     * - Verificar que la sesión no ha expirado
     * 
     * Si no hay sesión válida, redirige al login.
     */
    public static function verificarAutenticacion() {
        // Si no hay usuario_id en sesión, no está autenticado
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        /**
         * Verificar timeout de sesión
         * 
         * Si pasó más tiempo del permitido desde el login,
         * cerrar sesión automáticamente.
         * 
         * Cálculo: tiempo_actual - tiempo_login > tiempo_máximo
         */
        if (isset($_SESSION['login_time']) && 
            (time() - $_SESSION['login_time']) > SESSION_TIMEOUT) {
            
            // Sesión expirada - limpiar y destruir
            session_unset();
            session_destroy();
            
            // Redirigir con parámetro para mostrar mensaje
            header('Location: ' . BASE_URL . '/index.php?timeout=1');
            exit;
        }
    }

    /**
     * ========================================================================
     * verificarRol(): Comprobar que el usuario tiene permiso
     * ========================================================================
     * 
     * Método ESTÁTICO para control de acceso basado en roles.
     * 
     * Uso: AuthController::verificarRol(['ESTUDIANTE', 'COORDINADOR']);
     * 
     * Primero verifica autenticación, luego verifica rol.
     * Si el rol no está permitido, redirige con error.
     * 
     * @param array|string $rolesPermitidos Rol o array de roles permitidos
     */
    public static function verificarRol($rolesPermitidos) {
        // Primero verificar que está autenticado
        self::verificarAutenticacion();
        
        // Convertir a array si es string
        if (!is_array($rolesPermitidos)) {
            $rolesPermitidos = [$rolesPermitidos];
        }

        /**
         * in_array(): Busca un valor en un array
         * - Devuelve true si lo encuentra
         * - Devuelve false si no está
         * 
         * Si el rol del usuario no está en la lista permitida,
         * no tiene acceso a esta página.
         */
        if (!in_array($_SESSION['rol'], $rolesPermitidos)) {
            header('Location: ' . BASE_URL . '/index.php?error=acceso_denegado');
            exit;
        }
    }

    /**
     * ========================================================================
     * redirigirSegunRol(): Enviar al dashboard correcto
     * ========================================================================
     * 
     * Cada tipo de usuario tiene un dashboard diferente:
     * - ESTUDIANTE: Ver sus fichas, crear nuevas
     * - TUTOR_EMPRESA: Validar fichas de sus estudiantes
     * - COORDINADOR: Gestionar todo el sistema
     * 
     * @param string $rol Rol del usuario
     */
    private function redirigirSegunRol($rol) {
        /**
         * switch: Estructura de control para múltiples opciones
         * Es más limpio que múltiples if-elseif
         */
        switch ($rol) {
            case 'ESTUDIANTE':
                // Dashboard del estudiante
                header('Location: ' . BASE_URL . '/controladores/estudiante.php?action=dashboard');
                break;
                
            case 'TUTOR_EMPRESA':
                // Dashboard del tutor
                header('Location: ' . BASE_URL . '/controladores/tutor.php?action=dashboard');
                break;
                
            case 'COORDINADOR':
                // Dashboard del coordinador
                header('Location: ' . BASE_URL . '/controladores/coordinador.php?action=dashboard');
                break;
                
            default:
                // Rol desconocido - volver al login
                header('Location: ' . BASE_URL . '/index.php');
                break;
        }
    }
}

/**
 * ============================================================================
 * PROCESAMIENTO DE LA PETICIÓN
 * ============================================================================
 * 
 * Esta sección se ejecuta cuando se accede al archivo.
 * Lee el parámetro 'action' de la URL y ejecuta el método correspondiente.
 * 
 * Ejemplos de URLs:
 * - auth.php?action=login (GET)  → Muestra formulario
 * - auth.php?action=login (POST) → Procesa login
 * - auth.php?action=logout       → Cierra sesión
 */

// Obtener acción de la URL (por defecto: 'login')
$action = $_GET['action'] ?? 'login';

// Crear instancia del controlador
$controller = new AuthController();

// Ejecutar acción correspondiente
switch ($action) {
    case 'login':
        /**
         * Si es POST, procesar el formulario
         * Si es GET, mostrar el formulario
         */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
            $controller->mostrarLogin();
        }
        break;
        
    case 'logout':
        $controller->logout();
        break;
        
    default:
        // Acción desconocida - mostrar login
        $controller->mostrarLogin();
        break;
}
