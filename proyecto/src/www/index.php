<?php
/**
 * ============================================================================
 * PUNTO DE ENTRADA PRINCIPAL - INDEX.PHP
 * ============================================================================
 * 
 * Este es el archivo que se ejecuta cuando accedes a:
 * http://localhost/dwes/proyecto/src/www/
 * 
 * Su función es muy simple:
 * 1. Si el usuario ya está logueado → redirigir a su dashboard
 * 2. Si no está logueado → mostrar formulario de login
 * 
 * ¿Por qué no incluir directamente auth.php?
 * - Causaría bucles de redirección
 * - Es mejor tener control explícito del flujo
 * - Separación de responsabilidades
 */

/**
 * session_start(): Inicia o reanuda la sesión
 * 
 * IMPORTANTE: Debe llamarse ANTES de cualquier output HTML
 * Si hay texto o espacios antes de <?php, dará error
 * 
 * Las sesiones permiten mantener datos entre páginas:
 * - Página 1: $_SESSION['nombre'] = 'Juan';
 * - Página 2: echo $_SESSION['nombre']; // 'Juan'
 */
session_start();

/**
 * Cargar configuración
 * 
 * __DIR__: Constante mágica con la ruta del directorio actual
 * Ejemplo: /var/www/html/dwes/proyecto/src/www
 * 
 * Usar __DIR__ evita problemas de rutas relativas
 */
require_once __DIR__ . '/config/config.php';

/**
 * ============================================================================
 * VERIFICAR SI YA ESTÁ AUTENTICADO
 * ============================================================================
 * 
 * isset(): Comprueba si una variable está definida y no es null
 * 
 * $_SESSION['usuario_id'] se crea cuando el login es exitoso.
 * Si existe, el usuario ya hizo login y no necesita ver el formulario.
 */
if (isset($_SESSION['usuario_id'])) {
    /**
     * Redirigir según el rol del usuario
     * 
     * Cada tipo de usuario tiene diferente dashboard:
     * - ESTUDIANTE: Ver y crear fichas de seguimiento
     * - TUTOR_EMPRESA: Validar fichas de sus estudiantes
     * - COORDINADOR: Gestionar todo el sistema
     */
    switch ($_SESSION['rol']) {
        case 'ESTUDIANTE':
            /**
             * header(): Envía cabecera HTTP
             * Location: Indica al navegador que redirija
             * 
             * BASE_URL está definida en config.php
             * ?action=dashboard indica qué método ejecutar
             */
            header('Location: ' . BASE_URL . '/controladores/estudiante.php?action=dashboard');
            break;
            
        case 'TUTOR_EMPRESA':
            header('Location: ' . BASE_URL . '/controladores/tutor.php?action=dashboard');
            break;
            
        case 'COORDINADOR':
            header('Location: ' . BASE_URL . '/controladores/coordinador.php?action=dashboard');
            break;
            
        default:
            /**
             * Rol desconocido - mostrar login
             * Esto no debería pasar, pero es buena práctica
             * tener un caso por defecto
             */
            header('Location: ' . BASE_URL . '/vistas/auth/login.php');
            break;
    }
    
    /**
     * exit: Termina la ejecución del script
     * 
     * IMPORTANTE después de header('Location: ...')
     * Sin exit, el código seguiría ejecutándose
     * aunque el navegador vaya a redirigir
     */
    exit;
}

/**
 * ============================================================================
 * USUARIO NO AUTENTICADO - MOSTRAR LOGIN
 * ============================================================================
 * 
 * Si llegamos aquí, el usuario no tiene sesión activa.
 * Incluimos la vista del formulario de login.
 * 
 * require_once: Incluye el archivo una sola vez
 * - require: Incluye el archivo (error fatal si no existe)
 * - include: Incluye el archivo (warning si no existe)
 * - _once: Solo lo incluye si no se ha incluido antes
 */
require_once __DIR__ . '/vistas/auth/login.php';
